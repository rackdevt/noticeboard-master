<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @sub-subpackage Mapology
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

/**
* Add a members coordinates
* @since 1.0
*/
if( ! function_exists( 'mapo_add_coords' ) )
{
	function mapo_add_coords( $id = null, $user_id, $group_id, $lat, $lng )
	{
		$ucoords = new MAPO_Coords( $id );
		
		$ucoords->user_id = $user_id;
		$ucoords->group_id = $group_id;
		$ucoords->lat = $lat;
		$ucoords->lng = $lng;
	
		if( $new_id = $ucoords->save() )
			return $new_id;
			
		return false;
	}
}

/**
* Add a route
* @since 1.0
*/
function mapo_add_route( $id = null, $user_id, $group_id, $name, $slug, $description, $type, $default_lat, $default_lng, $zoom, $start_date, $end_date, $date_created, $public )
{
	$route = new MAPO_Routes( $id );
	
	$route->user_id = $user_id;
	$route->group_id = $group_id;
	$route->name = $name;
	$route->slug = $slug;
	$route->description = $description;
	$route->type = $type;
	$route->default_lat = $default_lat;
	$route->default_lng = $default_lng;
	$route->zoom = $zoom;
	$route->start_date = $start_date;
	$route->end_date = $end_date;
	$route->date_created = $date_created;
	$route->public = $public;

	if( $new_id = $route->save() )
		return $new_id;
		
	return false;
}

/**
* Add a route point
* @since 1.0
*/
function mapo_add_route_point( $id = null, $route_id, $title, $description, $image, $lat, $lng, $coord_order, $type )
{
	$point = new MAPO_Routes_Coords( $id );
	
	$point->route_id = $route_id;
	$point->title = $title;
	$point->description = $description;
	$point->image = $image;
	$point->lat = $lat;
	$point->lng = $lng;
	$point->coord_order = $coord_order;
	$point->type = $type;

	if( $new_id = $point->save() )
		return $new_id;
		
	return false;
}

/**
* Get the routes from the database
* @since 1.0
*/
function mapo_get_routes( $args = '' )
{
	global $bp;
	
	$defaults =  array(
		'user_id' => false,
		'group_id' => false,
		'ids' => false,
		'name' => false,
		'slug' => false,
		'type' => false,
		'default_lat' => false,
		'default_lng' => false,
		'zoom' => false,
		'start_date' => false,
		'end_date' => false,
		'date_created' => false,
		'per_page' => 20,
		'page' => 1,
		'search_terms' => false,
		'populate_extras' => true,
		'asc_desc' => 'DESC',
		'sort' => false,
		'public' => false
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$routes = MAPO_Routes::get( (int)$user_id, (int)$group_id, $ids, $name, $slug, $type, $default_lat, $default_lng, (int)$zoom, $start_date, $end_date, $date_created, (int)$page, (int)$per_page, $search_terms, (bool)$populate_extras, $asc_desc, $sort, $public );

	return apply_filters( 'mapo_get_routes', $routes, $params );
}

/**
* Get an id
* @since 1.0
*/
if( ! function_exists( 'mapo_get_id_by_user' ) )
{
	function mapo_get_id_by_user( $user_id )
	{
		return MAPO_Coords::get_id_by_user( $user_id );
	}
} 

/**
* Get an id
* @since 1.0
*/
if( ! function_exists( 'mapo_get_id_by_group' ) )
{
	function mapo_get_id_by_group( $group_id )
	{
		return MAPO_Coords::get_id_by_group( $group_id );
	}
}

/**
* Make sure we have a unique slug
* @since 1.0
*/
function mapo_check_unique_slug( $slug )
{
	global $wpdb, $mapo, $bp;
	
	$sql = "SELECT slug FROM {$mapo->tables->routes} WHERE slug = %s AND user_id = %d LIMIT 1";

	$check = $wpdb->get_var( $wpdb->prepare( $sql, $slug, $bp->displayed_user->id ) );

	if( $check )
	{
		$suffix = 2;
		do {
			$alt_title = substr( $slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
			$check = $wpdb->get_var( $wpdb->prepare( $sql, $alt_title ) );
			$suffix++;
		} while ( $check );
		$slug = $alt_title;
	}
	
	return $slug;	
}

/*
* Get the adjacent route
* Takes into account access rights to a route
* @since 1.0
*/
function mapo_get_adjacent_route( $previous = true )
{
	global $wpdb, $mapo, $route_template;
	
	$current_route_date = $route_template->route->date_created;
	$current_user_id 	= $route_template->route->user_id;
	
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';
	
	$query = $wpdb->prepare( "
		SELECT * 
		FROM {$mapo->tables->routes} 
		WHERE date_created {$op} %s 
		AND user_id = %d 
		ORDER BY date_created {$order} 
		LIMIT 1
	", $current_route_date, (int)$current_user_id );
		
	$query_key = 'adjacent_route_'. md5( $query );
	
	$result = wp_cache_get( $query_key, 'routes' );
	
	if( $result === false )
	{
		$result = $wpdb->get_row( $query );
		if ( $result === null )
			$result = '';
	
		wp_cache_set( $query_key, $result, 'routes' );
	}

	$check = mapo_restrict_route_access( $result );
	
	if( $check && $result )
	{
		do {
			$current_route_date = $result->date_created;
			$current_user_id = $result->user_id;

			$query = $wpdb->prepare( "
				SELECT *
				FROM {$mapo->tables->routes} 
				WHERE date_created {$op} %s 
				AND user_id = %d 
				ORDER BY date_created {$order}
				LIMIT 1
			", $current_route_date, (int)$current_user_id );
			
			$query_key = 'adjacent_route_'. md5( $query );

			$result = wp_cache_get( $query_key, 'routes' );

			if( $result === false )
			{
				$result = $wpdb->get_row( $query );
				if( $result === null )
					$result = '';
			
				wp_cache_set( $query_key, $result, 'routes' );
			}
			
			$check = mapo_restrict_route_access( $result );
		} while ( $check && $result );
	}
	
	return $result;
}

/**
* Get all user groups
* @since 1.0
*/
function mapo_get_user_groups()
{
	global $wpdb, $bp;

	return  $wpdb->get_results( $wpdb->prepare( "SELECT m.group_id, g.name FROM {$bp->groups->table_name_members} m LEFT JOIN {$bp->groups->table_name} g ON m.group_id = g.id WHERE m.user_id = %d AND m.is_banned = 0", $bp->loggedin_user->id ) );
}

/**
* Get a row by slug
* @since 1.0
*/
function mapo_get_by_slug( $slug, $user_id )
{
	return MAPO_Routes::get_by_slug( $slug, $user_id );	
}

/**
* Delete all waypoints for a route
* @since 1.0
*/
function mapo_waypoints_delete_by_route_id( $route_id, $type = 'route' )
{
	return MAPO_Routes_Coords::delete_by_route_id( $route_id, $type );
}

/**
* Remove group_ids
* @since 1.0
*/
function mapo_remove_group_reference( $group_id )
{
	return MAPO_Routes::remove_group_reference( $group_id );	
}

/**
* Remove for user
* @since 1.0
*/
function mapo_remove_for_user( $user_id )
{
	return MAPO_Routes::remove_for_user( $user_id );	
}

/**
* Remove from group
* @since 1.0
*/
function mapo_remove_group_id( $route_id )
{
	return MAPO_Routes::remove_group_id( $route_id );	
}

/**
* Last published route date
* @since 1.0
*/
function mapo_get_last_published()
{
	return MAPO_Routes::get_last_published();	
}

/**
* Last published route date for a group
* @since 1.0
*/
function mapo_group_get_last_published()
{
	return MAPO_Routes::group_get_last_published();	
}

/**
* Last published route date for a user
* @since 1.0
*/
function mapo_user_get_last_published()
{
	return MAPO_Routes::user_get_last_published();	
}

/**
* Remove waypoints for user
* @since 1.0
*/
function mapo_remove_wp_for_user( $user_id )
{
	return MAPO_Routes::remove_wp_for_user( $user_id );	
}
?>