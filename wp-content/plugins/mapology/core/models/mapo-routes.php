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

class MAPO_Routes
{
	var $id;
	var $user_id;
	var $group_id;
	var $name;
	var $slug;
	var $description;
	var $type;
	var $default_lat;
	var $default_lng;
	var $zoom;
	var $start_date;
	var $end_date;
	var $date_created;
	var $public;
	
	/**
	 * PHP5 Constructor
	 * @since 1.0
	 */
	function __construct( $id = null, $slug = null, $user_id = null )
	{
		global $mapo, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
		elseif( $slug && $user_id )
		{
			$this->slug = $slug;
			$this->user_id = $user_id;
			$this->populate_by_slug();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	function populate()
	{
		global $mapo, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->routes} WHERE id = %d", $this->id ) );

		$this->user_id		= $table->user_id;
		$this->group_id		= $table->group_id;
		$this->name			= $table->name;
		$this->slug	 		= $table->slug;
		$this->description	= $table->description;
		$this->type			= $table->type;
		$this->default_lat	= $table->default_lat;
		$this->default_lng	= $table->default_lng;
		$this->zoom			= $table->zoom;
		$this->start_date	= $table->start_date;
		$this->end_date		= $table->end_date;
		$this->date_created	= $table->date_created;
		$this->public		= $table->public;
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	function populate_by_slug()
	{
		global $mapo, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->routes} WHERE slug = %s AND user_id = %d", $this->slug, $this->user_id ) );

		$this->id			= $table->id;
		$this->group_id		= $table->group_id;
		$this->name			= $table->name;
		$this->description	= $table->description;
		$this->type			= $table->type;
		$this->default_lat	= $table->default_lat;
		$this->default_lng	= $table->default_lng;
		$this->zoom			= $table->zoom;
		$this->start_date	= $table->start_date;
		$this->end_date		= $table->end_date;
		$this->date_created	= $table->date_created;
		$this->public		= $table->public;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.0
	 */
	function save()
	{
		global $wpdb, $mapo;
		
		$this->user_id		= apply_filters( 'mapo_before_save_routes_user_id', $this->user_id, $this->id );
		$this->group_id		= apply_filters( 'mapo_before_save_routes_group_id', $this->group_id, $this->id );
		$this->name			= apply_filters( 'mapo_before_save_routes_name', $this->name, $this->id );
		$this->slug	 		= apply_filters( 'mapo_before_save_routes_slug', $this->slug, $this->id );
		$this->description	= apply_filters( 'mapo_before_save_routes_description', $this->description, $this->id );
		$this->type			= apply_filters( 'mapo_before_save_routes_type', $this->type, $this->id );
		$this->default_lat	= apply_filters( 'mapo_before_save_routes_default_lat', $this->default_lat, $this->id );
		$this->default_lng	= apply_filters( 'mapo_before_save_routes_default_lng', $this->default_lng, $this->id );
		$this->zoom			= apply_filters( 'mapo_before_save_routes_zoom', $this->zoom, $this->id );
		$this->start_date	= apply_filters( 'mapo_before_save_routes_start_date', $this->start_date, $this->id );
		$this->end_date		= apply_filters( 'mapo_before_save_routes_end_date', $this->end_date, $this->id );
		$this->date_created	= apply_filters( 'mapo_before_save_routes_date_created', $this->date_created, $this->id );
		$this->public		= apply_filters( 'mapo_before_save_routes_public', $this->public, $this->id );
		
		/* Call a before save action here */
		do_action( 'mapo_routes_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$mapo->tables->routes} SET
													user_id = %d,
													group_id = %d,
													name = %s,
													slug = %s,
													description = %s,
													type = %s,
													default_lat = %s,
													default_lng = %s,
													zoom = %d,
													start_date = %s,
													end_date = %s,
													date_created = %s,
													public = %d
											WHERE id = %d",
													$this->user_id,
													$this->group_id,
													$this->name,
													$this->slug,
													$this->description,
													$this->type,
													$this->default_lat,
													$this->default_lng,
													$this->zoom,
													$this->start_date,
													$this->end_date,
													$this->date_created,
													$this->public,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$mapo->tables->routes} (
													user_id,
													group_id,
													name,
													slug,
													description,
													type,
													default_lat,
													default_lng,
													zoom,
													start_date,
													end_date,
													date_created,
													public
											) VALUES ( 
													%d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %d
											)",
													$this->user_id,
													$this->group_id,
													$this->name,
													$this->slug,
													$this->description,
													$this->type,
													$this->default_lat,
													$this->default_lng,
													$this->zoom,
													$this->start_date,
													$this->end_date,
													$this->date_created ,
													$this->public ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'mapo_routes_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.0
	 */
	function delete()
	{
		global $wpdb, $mapo;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$mapo->tables->routes} WHERE id = %d", $this->id ) );
	}
	
	/**
	 * Database workhorse
	 * @since 1.0
	 */
	function get( $user_id = 0, $group_id = false, $ids = false, $name = false, $slug = false, $type = false, $default_lat = false, $default_lng = false, $zoom = false, $start_date = false, $end_date = false, $date_created = false, $page = null, $per_page = null, $search_terms = false, $populate_extras = true, $asc_desc = 'DESC', $sort = false, $public = false )
	{
		global $wpdb, $mapo, $bp;

		$paged_sql = array();

		$paged_sql['select'][] = "SELECT r.* FROM {$mapo->tables->routes} r";

		if( ! empty( $user_id ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.user_id = %d", (int)$user_id );

		if( ! empty( $group_id ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.group_id = %d", (int)$group_id );

		if( ! empty( $ids ) )
			$paged_sql['where'][] = "r.id in ({$ids})";

		if( ! empty( $name ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.name = %s", $name );

		if( ! empty( $slug ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.slug = %s", $slug );

		if( ! empty( $type ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.type = %s", $type );

		if( ! empty( $default_lat ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.default_lat = %s", $default_lat );

		if( ! empty( $default_lng ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.default_lng = %s", $default_lng );

		if( ! empty( $zoom ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.zoom = %d", (int)$zoom );

		if( ! empty( $start_date ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.start_date = %s", $start_date );

		if( ! empty( $end_date ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.end_date = %s", $end_date );

		if( ! empty( $date_created ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.date_created = %s", $date_created );

		if( ! empty( $public ) )
			$paged_sql['where'][] = $wpdb->prepare( "r.public = %s", $public );

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$paged_sql['where'][] = "( r.name LIKE '%%{$search_terms}%%' OR r.slug LIKE '%%{$search_terms}%%' OR r.description LIKE '%%{$search_terms}%%' OR r.type LIKE '%%{$search_terms}%%' )";
		}

		switch( $sort )
		{
			case 'end_date': default:
				$paged_sql['orderby'] = "ORDER BY r.end_date {$asc_desc}";
				break;

			case 'start_date':
				$paged_sql['orderby'] = "ORDER BY r.start_date {$asc_desc}";
				break;
				
			case 'date_created':
				$paged_sql['orderby'] = "ORDER BY r.date_created {$asc_desc}";
				break;

			case 'random':
				$paged_sql['orderby'] = "ORDER BY RAND()";
				break;
		}

		if( $per_page && $page )
			$paged_sql['pagination'] = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $per_page), intval( $per_page ) );

		// put it all together
		$p_sql[] = join( ' ', (array)$paged_sql['select'] );

		if( ! empty( $paged_sql['where'] ) )
			$p_sql[] = "WHERE " . join( ' AND ', (array)$paged_sql['where'] );
		
		$p_sql[] = $paged_sql['orderby'];
		
		if( $per_page && $page )
			$p_sql[] = $paged_sql['pagination'];

		/* Get paginated results */
		$paged_routes = $wpdb->get_results( join( ' ', (array)$p_sql ) );

		$total_sql['select'] = "SELECT COUNT(r.id) FROM {$mapo->tables->routes} r ";

		if( ! empty( $user_id ) )
			$total_sql['where'][] = $wpdb->prepare( "r.user_id = %d", (int)$user_id );

		if( ! empty( $group_id ) )
			$total_sql['where'][] = $wpdb->prepare( "r.group_id = %d", (int)$group_id );

		if( ! empty( $ids ) )
			$total_sql['where'][] = "r.id in ({$ids})";

		if( ! empty( $name ) )
			$total_sql['where'][] = $wpdb->prepare( "r.name = %s", $name );

		if( ! empty( $slug ) )
			$total_sql['where'][] = $wpdb->prepare( "r.slug = %s", $slug );

		if( ! empty( $type ) )
			$total_sql['where'][] = $wpdb->prepare( "r.type = %s", $type );

		if( ! empty( $default_lat ) )
			$total_sql['where'][] = $wpdb->prepare( "r.default_lat = %s", $default_lat );

		if( ! empty( $default_lng ) )
			$total_sql['where'][] = $wpdb->prepare( "r.default_lng = %s", $default_lng );

		if( ! empty( $zoom ) )
			$total_sql['where'][] = $wpdb->prepare( "r.zoom = %d", (int)$zoom );

		if( ! empty( $start_date ) )
			$total_sql['where'][] = $wpdb->prepare( "r.start_date = %s", $start_date );

		if( ! empty( $end_date ) )
			$total_sql['where'][] = $wpdb->prepare( "r.end_date = %s", $end_date );

		if( ! empty( $date_created ) )
			$total_sql['where'][] = $wpdb->prepare( "r.date_created = %s", $date_created );

		if( ! empty( $public ) )
			$total_sql['where'][] = $wpdb->prepare( "r.public = %s", $public );

		if( $search_terms )
		{
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$total_sql['where'][] = "( r.name LIKE '%%{$search_terms}%%' OR r.slug LIKE '%%{$search_terms}%%' OR r.description LIKE '%%{$search_terms}%%' OR r.type LIKE '%%{$search_terms}%%' )";
		}

		$t_sql[] = $total_sql['select'];

		if( ! empty( $total_sql['where'] ) )
			$t_sql[] = " WHERE " . join( ' AND ', (array)$total_sql['where'] );

		/* Get total routes results */
		$total_routes = $wpdb->get_var( join( ' ', (array)$t_sql ) );

		if( ! empty( $populate_extras ) )
		{
			foreach( (array)$paged_routes as $route )
				$route_ids[] = $route->id;
				
			$route_ids = $wpdb->escape( join( ',', (array)$route_ids ) );

			$paged = self::get_route_extras( $paged_routes, $total_routes, $route_ids );
			
			$paged_routes = $paged['routes'];
			$total_routes = $paged['total'];
		}

		unset( $paged_sql, $total_sql );
		
		return array( 'routes' => $paged_routes, 'total' => $total_routes );
	}
	
	/**
	 * Get some extras
	 * @since 1.0
	 */
	function get_route_extras( $paged_routes, $total_routes, $ids )
	{
		global $mapo, $wpdb;
		
		$coords_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$mapo->tables->routes_coords} WHERE route_id IN ( {$ids} ) AND type = %s ORDER BY coord_order ASC", 'route' ) );
		
		for( $i = 0; $i < count( $paged_routes ); $i++ )
		{
			if( $coords_data )
			{
				foreach( (array)$coords_data as $data )
				{
					if( $data->route_id == $paged_routes[$i]->id )
						$paged_routes[$i]->waypoints[] = $data;
				}
			}
		}
		
		// do some access checks after we have all the data
		foreach( $paged_routes as $k => $route )
		{
			if( mapo_restrict_route_access( $route ) )
			{
				// remove from array
				unset( $paged_routes[$k] );
				// adjust the total number
				$total_routes--;
			}
		}
		
		// reset the array keys
		$paged_routes = array_values( $paged_routes );

		return array( 'routes' => $paged_routes, 'total' => $total_routes );
	}
	
	/**
	 * Get some extras
	 * @since 1.0
	 */
	function get_by_slug( $slug, $user_id )
	{
		global $mapo, $wpdb;
		
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->routes} WHERE slug = %s AND user_id = %d", $slug, $user_id ) );
	}
	
	/**
	 * Remove the group_ids
	 * @since 1.0
	 */
	function remove_group_reference( $group_id )
	{
		global $mapo, $wpdb;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$mapo->tables->routes} SET group_id = '' WHERE group_id = %d", $group_id ) );
	}

	/**
	 * Remove from group
	 * @since 1.0
	 */
	function remove_group_id( $route_id )
	{
		global $mapo, $wpdb;
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$mapo->tables->routes} SET group_id = '' WHERE id = %d", $route_id ) );
	}

	/**
	 * Remove routes for a user
	 * @since 1.0
	 */
	function remove_for_user( $user_id )
	{
		global $mapo, $wpdb;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$mapo->tables->routes} WHERE user_id = %d", $user_id ) );
	}

	/**
	 * Remove waypopints for a user
	 * @since 1.0
	 */
	function remove_wp_for_user( $user_id )
	{
		global $mapo, $wpdb;
		
		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT rc.id FROM {$mapo->tables->routes_coords} rc RIGHT JOIN {$mapo->tables->routes} r ON rc.route_id = r.id AND r.user_id = %d", $user_id ) );
		$ids = $wpdb->escape( join( ',', (array)$ids ) );

		return $wpdb->query( "DELETE FROM {$mapo->tables->routes_coords} WHERE id IN ({$ids})" );
	}

	/**
	 * Get global last published date
	 * @since 1.0
	 */
	function get_last_published()
	{
		global $mapo, $wpdb;
	
		return $wpdb->get_var( "SELECT date_created FROM {$mapo->tables->routes} ORDER BY date_created ASC LIMIT 1" );
	}

	/**
	 * Get groups last published date
	 * @since 1.0
	 */
	function group_get_last_published()
	{
		global $bp, $mapo, $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$mapo->tables->routes} WHERE group_id = %d ORDER BY date_created ASC LIMIT 1", $bp->groups->current_group->id ) );
	}

	/**
	 * Get users last published date
	 * @since 1.0
	 */
	function user_get_last_published()
	{
		global $bp, $mapo, $wpdb;
		
		return $wpdb->get_var( $wpdb->prepare( "SELECT date_created FROM {$mapo->tables->routes} WHERE user_id = %d ORDER BY date_created ASC LIMIT 1", $bp->displayed_user->id ) );
	}
}
?>