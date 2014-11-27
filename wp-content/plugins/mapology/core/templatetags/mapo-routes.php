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

class MAPO_Routes_Template
{
	var $current_route = -1;
	var $route_count;
	var $routes;
	var $route;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_route_count;

	function mapo_routes_template( $user_id, $group_id, $ids, $name, $slug, $type, $default_lat, $default_lng, $zoom, $start_date, $end_date, $date_created, $page, $per_page, $max, $search_terms, $populate_extras, $asc_desc, $sort, $public )
	{
		$this->pag_page = isset( $_REQUEST['rpage'] ) ? intval( $_REQUEST['rpage'] ) : $page;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		$this->routes = mapo_get_routes( array( 'user_id' => $user_id, 'group_id' => $group_id, 'ids' => $ids, 'name' => $name, 'slug' => $slug, 'type' => $type, 'default_lat' => $default_lat, 'default_lng' => $default_lng, 'zoom' => $zoom, 'start_date' => $start_date, 'end_date' => $end_date, 'date_created' => $date_created, 'per_page' => $this->pag_num, 'page' => $this->pag_page, 'search_terms' => $search_terms, 'populate_extras' => $populate_extras, 'asc_desc' => $asc_desc, 'sort' => $sort, 'public' => $public ) );
		
		if( $this->pag_num < 1 ) $this->pag_num = 1;

		if( ! $max || $max >= (int)$this->routes['total'] )
			$this->total_route_count = (int)$this->routes['total'];
		else
			$this->total_route_count = (int)$max;

		$this->routes = $this->routes['routes'];

		if( $max )
		{
			if( $max >= count( $this->routes ) )
				$this->route_count = count( $this->routes );
			else
				$this->route_count = (int)$max;
		}
		else
			$this->route_count = count( $this->routes );
		
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'rpage' => '%#%' ) ),
			'format' => '',
			'total' => ceil( $this->total_route_count / $this->pag_num ),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 15
		));
	}

	function has_routes()
	{
		if ( $this->route_count )
			return true;

		return false;
	}

	function next_route()
	{
		$this->current_route++;
		$this->route = $this->routes[$this->current_route];

		return $this->route;
	}

	function rewind_routes()
	{
		$this->current_route = -1;
		
		if ( $this->route_count > 0 )
		{
			$this->route = $this->routes[0];
		}
	}

	function routes()
	{
		if ( $this->current_route + 1 < $this->route_count )
		{
			return true;
		}
		elseif( $this->current_route + 1 == $this->route_count )
		{
			do_action('loop_end');
			$this->rewind_routes();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_route()
	{
		$this->in_the_loop = true;
		$this->route = $this->next_route();

		if ( 0 == $this->current_route )
			do_action('loop_start');
	}

}

function mapo_has_routes( $args = '' )
{
	global $route_template, $bp;
	
	$search_terms = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	$user_id = ( ! empty( $bp->displayed_user->id ) ) ? $bp->displayed_user->id : 0;
	$group_id = ( ! empty( $bp->groups->current_group->id ) ) ? $bp->groups->current_group->id : false;
	
	$defaults = array(
		'user_id' => $user_id,
		'group_id' => $group_id,
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
		'page' => 1,
		'per_page' => 10,
		'max' => false,
		'search_terms' => $search_terms,
		'populate_extras' => true,
		'asc_desc' => 'DESC',
		'sort' => false,
		'public' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );
	
	$route_template = new MAPO_Routes_Template( (int)$user_id, (int)$group_id, $ids, $name, $slug, $type, $default_lat, $default_lng, $zoom, $start_date, $end_date, $date_created, (int)$page, (int)$per_page, (int)$max, $search_terms, (bool)$populate_extras, $asc_desc, $sort, $public );
	return apply_filters( 'mapo_has_routes', $route_template->has_routes(), $route_template );
}

function mapo_routes()
{
	global $route_template;

	return $route_template->routes();
}

function mapo_the_route()
{
	global $route_template;

	return $route_template->the_route();
}

function mapo_get_routes_count()
{
	global $route_template;

	return $route_template->route_count;
}

function mapo_get_total_routes_count()
{
	global $route_template;

	return $route_template->total_route_count;
}

/**
 * Pagination links
 * @since 1.0
 */
function mapo_routes_pagination_links()
{
	echo mapo_get_routes_pagination_links();
}
	function mapo_get_routes_pagination_links()
	{
		global $route_template;
	
		if( ! empty( $route_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'mapo' ), $route_template->pag_links );
	}

/**
 * Pagination count
 * @since 1.0
 */
function mapo_routes_pagination_count()
{
	echo mapo_get_routes_pagination_count();
}
	function mapo_get_routes_pagination_count()
	{
		global $bp, $route_template;
	
		$from_num = bp_core_number_format( intval( ( $route_template->pag_page - 1 ) * $route_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $route_template->pag_num - 1 ) > $route_template->total_route_count ) ? $route_template->total_route_count : $from_num + ( $route_template->pag_num - 1 ) );
		$total = bp_core_number_format( $route_template->total_route_count );
	
		return apply_filters( 'mapo_get_routes_pagination_count', sprintf( __( 'Viewing route %1$s to %2$s (of %3$s routes)', 'mapo' ), $from_num, $to_num, $total ) );
	}
	
/**
 * Routes id
 * @since 1.0
  */
function mapo_routes_id( $r = false )
{
	echo mapo_get_routes_id( $r );
}
	function mapo_get_routes_id( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->id;
	}

/**
 * Routes user_id
 * @since 1.0
  */
function mapo_routes_user_id( $r = false )
{
	echo mapo_get_routes_user_id( $r );
}
	function mapo_get_routes_user_id( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->user_id;
	}

/**
 * Route user avatar
 * @since 1.0
 */
function mapo_routes_user_avatar( $args = '' )
{
	echo apply_filters( 'mapo_routes_user_avatar', mapo_get_routes_user_avatar( $args ) );
}
	function mapo_get_routes_user_avatar( $args = '' )
	{
		global $bp, $route_template;

		$defaults = array(
			'r' => false,
			'type' => 'thumb',
			'width' => false,
			'height' => false,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Event Admin Avatar', 'events' )
		);

		$a = wp_parse_args( $args, $defaults );
		extract( $a, EXTR_SKIP );
		
		$route = ( ! $r ) ? $route_template->route : $e;
		
		$email = bp_core_get_user_email( $route->user_id );

		return '<a href="'. bp_core_get_user_domain( $route->user_id ) .'">'. apply_filters( 'mapo_get_routes_user_avatar', bp_core_fetch_avatar( array( 'item_id' => $route->user_id, 'type' => $type, 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'email' => $email ) ) ) .'</a>';
	}

/**
 * Routes group_id
 * @since 1.0
  */
function mapo_routes_group_id( $r = false )
{
	echo mapo_get_routes_group_id( $r );
}
	function mapo_get_routes_group_id( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->group_id;
	}

/**
 * Routes name
 * @since 1.0
  */
function mapo_routes_name( $r = false )
{
	echo mapo_get_routes_name( $r );
}
	function mapo_get_routes_name( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return apply_filters( 'mapo_routes_get_route_name', $route->name );
	}

/**
 * Routes raw name
 * @since 1.0
  */
function mapo_routes_name_raw( $r = false )
{
	echo mapo_get_routes_name_raw( $r );
}
	function mapo_get_routes_name_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return stripslashes( $route->name );
	}

/**
 * Routes slug
 * @since 1.0
  */
function mapo_routes_slug( $r = false )
{
	echo mapo_get_routes_slug( $r );
}
	function mapo_get_routes_slug( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->slug;
	}

/**
 * Route link
 * @since 1.0
 */
function mapo_routes_link( $r = false, $page = 'routes', $dir = false )
{
	echo mapo_get_routes_link( $r, $page, $dir );
}
	function mapo_get_routes_link( $r = false, $page = 'routes', $dir = false )
	{
		global $route_template, $bp;

		$route = ( ! $r ) ? $route_template->route : $r;
		
		if( $dir )
			$link = bp_core_get_user_domain( $route->user_id );
		else
			$link = $bp->displayed_user->domain;
		
		return $link . $bp->mapology->slug .'/'. $page .'/'. $route->slug .'/';
	}
	
/**
 * Routes kml link
 * @since 1.0
 */
function mapo_routes_kml_link( $r = false, $dir = false )
{
	echo mapo_get_routes_kml_link( $r, $dir );
}
	function mapo_get_routes_kml_link( $r = false, $dir = false )
	{
		global $route_template, $bp;

		$route = ( ! $r ) ? $route_template->route : $r;

		if( $dir )
			$link = bp_core_get_user_domain( $route->user_id );
		else
			$link = $bp->displayed_user->domain;
		
		return $link . $bp->mapology->slug .'/routes/'. $route->slug .'/kml/'. $route->slug .'-'. $route->user_id .'.kml';
	}

/**
 * Routes description
 * @since 1.0
 */
function mapo_routes_description( $r = false )
{
	echo mapo_get_routes_description( $r );
}
	function mapo_get_routes_description( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return apply_filters( 'mapo_routes_get_route_description', $route->description );
	}

/**
 * Routes raw description
 * @since 1.0
  */
function mapo_routes_description_raw( $r = false )
{
	echo mapo_get_routes_description_raw( $r );
}
	function mapo_get_routes_description_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return stripslashes( $route->description );
	}

/**
 * Routes raw description excerpt
 * @since 1.0
  */
function mapo_routes_description_excerpt_raw( $r = false )
{
	echo mapo_get_routes_description_excerpt_raw( $r );
}
	function mapo_get_routes_description_excerpt_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return bp_create_excerpt( $route->description );
	}

/**
 * Routes raw description excerpt
 * @since 1.0
  */
function mapo_routes_description_excerpt( $r = false )
{
	echo mapo_get_routes_description_excerpt( $r );
}
	function mapo_get_routes_description_excerpt( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return apply_filters( 'mapo_routes_get_route_description_excerpt', bp_create_excerpt( $route->description ) );
	}

/**
 * Routes type
 * @since 1.0
  */
function mapo_routes_type( $r = false )
{
	echo mapo_get_routes_type( $r );
}
	function mapo_get_routes_type( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->type;
	}
	
/**
 * Routes default_lat
 * @since 1.0
  */
function mapo_routes_default_lat( $r = false )
{
	echo mapo_get_routes_default_lat( $r );
}
	function mapo_get_routes_default_lat( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->default_lat;
	}

/**
 * Routes default_lng
 * @since 1.0
  */
function mapo_routes_default_lng( $r = false )
{
	echo mapo_get_routes_default_lng( $r );
}
	function mapo_get_routes_default_lng( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->default_lng;
	}

/**
 * Routes zoom
 * @since 1.0
  */
function mapo_routes_zoom( $r = false )
{
	echo mapo_get_routes_zoom( $r );
}
	function mapo_get_routes_zoom( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->zoom;
	}

/**
 * Routes public
 * @since 1.0
  */
function mapo_routes_public( $r = false )
{
	echo mapo_get_routes_public( $r );
}
	function mapo_get_routes_public( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->public;
	}

/**
 * Routes avatar
 * @since 1.0
  */
function mapo_routes_avatar( $r = false, $w = 150, $h = 150 )
{
	echo mapo_get_routes_avatar( $r, $w, $h );
}
	function mapo_get_routes_avatar( $r = false, $w = 150, $h = 150  )
	{
		global $route_template, $mapo;
		
		$route = ( ! $r ) ? $route_template->route : $r;
		
		foreach( (array)$route->waypoints as $wp )
			$points[] = $wp->lat .','. $wp->lng;

		return '<img class="avatar" src="http://maps.google.com/maps/api/staticmap?size='. $w .'x'. $h .'&path=color:0x0000ff|weight:5|'. mapo_get_static_waypoints( $points ) .'&maptype='. strtolower( $route->type ) .'&sensor=false" width="'. $w .'"  height="'. $h .'" alt="" />';
	}

/**
 * Routes start_date
 * @since 1.0
  */
function mapo_routes_start_date( $r = false )
{
	echo mapo_get_routes_start_date( $r );
}
	function mapo_get_routes_start_date( $r = false )
	{
		global $route_template, $mapo;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return ( $route->start_date == '0000-00-00' ) ? '' : mysql2date( $mapo->date_format, $route->start_date, true );
	}

/**
 * Routes raw start_date
 * @since 1.0
  */
function mapo_routes_start_date_raw( $r = false )
{
	echo mapo_get_routes_start_date_raw( $r );
}
	function mapo_get_routes_start_date_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return ( $route->start_date == '0000-00-00' ) ? '' : $route->start_date;
	}

/**
 * Routes end_date
 * @since 1.0
  */
function mapo_routes_end_date( $r = false )
{
	echo mapo_get_routes_end_date( $r );
}
	function mapo_get_routes_end_date( $r = false )
	{
		global $route_template, $mapo;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return ( $route->end_date == '0000-00-00' ) ? '' : mysql2date( $mapo->date_format, $route->end_date, true );
	}

/**
 * Routes raw end_date
 * @since 1.0
  */
function mapo_routes_end_date_raw( $r = false )
{
	echo mapo_get_routes_end_date_raw( $r );
}
	function mapo_get_routes_end_date_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return ( $route->end_date == '0000-00-00' ) ? '' : $route->end_date;
	}

/**
 * Routes date_created
 * @since 1.0
  */
function mapo_routes_date_created( $r = false )
{
	echo mapo_get_routes_date_created( $r );
}
	function mapo_get_routes_date_created( $r = false )
	{
		global $route_template, $mapo;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return mysql2date( $mapo->date_format, $route->date_created, true );
	}

/**
 * Routes raw date_created
 * @since 1.0
  */
function mapo_routes_date_created_raw( $r = false )
{
	echo mapo_get_routes_date_created_raw( $r );
}
	function mapo_get_routes_date_created_raw( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->date_created;
	}

/**
 * Routes waypoints
 * @since 1.0
  */
function mapo_routes_waypoints( $r = false )
{
	echo mapo_get_routes_waypoints( $r );
}
	function mapo_get_routes_waypoints( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints;
	}
	
/**
 * Routes waypoints id
 * @since 1.0
  */
function mapo_routes_waypoints_id( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_id( $key, $r );
}
	function mapo_get_routes_waypoints_id( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints[$key]->id;
	}

/**
 * Routes waypoints route_id
 * @since 1.0
  */
function mapo_routes_waypoints_route_id( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_route_id( $key, $r );
}
	function mapo_get_routes_waypoints_route_id( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints[$key]->route_id;
	}

/**
 * Routes waypoints title
 * @since 1.0
  */
function mapo_routes_waypoints_title( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_title( $key, $r );
}
	function mapo_get_routes_waypoints_title( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return apply_filters( 'mapo_routes_get_waypoint_title', $route->waypoints[$key]->title );
	}

/**
 * Routes waypoints title raw
 * @since 1.0
  */
function mapo_routes_waypoints_title_raw( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_title_raw( $key, $r );
}
	function mapo_get_routes_waypoints_title_raw( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return stripslashes( $route->waypoints[$key]->title );
	}

/**
 * Routes waypoints description
 * @since 1.0
  */
function mapo_routes_waypoints_description( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_description( $key, $r );
}
	function mapo_get_routes_waypoints_description( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return apply_filters( 'mapo_routes_get_waypoint_description', $route->waypoints[$key]->description );
	}

/**
 * Routes waypoints description raw
 * @since 1.0
  */
function mapo_routes_waypoints_description_raw( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_description_raw( $key, $r );
}
	function mapo_get_routes_waypoints_description_raw( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return stripslashes( $route->waypoints[$key]->description );
	}

/**
 * Routes waypoints image
 * @since 1.0
  */
function mapo_routes_waypoints_image( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_image( $key, $r );
}
	function mapo_get_routes_waypoints_image( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return stripslashes( $route->waypoints[$key]->image );
	}

/**
 * Routes waypoints lat
 * @since 1.0
  */
function mapo_routes_waypoints_lat( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_lat( $key, $r );
}
	function mapo_get_routes_waypoints_lat( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints[$key]->lat;
	}

/**
 * Routes waypoints lng
 * @since 1.0
  */
function mapo_routes_waypoints_lng( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_lng( $key, $r );
}
	function mapo_get_routes_waypoints_lng( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints[$key]->lng;
	}

/**
 * Routes waypoints coord_order
 * @since 1.0
  */
function mapo_routes_waypoints_coord_order( $key = false, $r = false )
{
	echo mapo_get_routes_waypoints_coord_order( $key, $r );
}
	function mapo_get_routes_waypoints_coord_order( $key = false, $r = false )
	{
		global $route_template;
		
		if( ! $key )
			return false;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return $route->waypoints[$key]->coord_order;
	}

/**
 * Routes remove from group
 * @since 1.0
  */
function mapo_routes_remove_from_group( $r = false )
{
	echo mapo_get_routes_remove_from_group( $r );
}
	function mapo_get_routes_remove_from_group( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		return wp_nonce_url( bp_get_group_permalink( $bp->groups->current_group ) .'routes/remove/'. $route->id .'/', 'mapo_remove_group' );
	}

/**
 * Routes waypoints js
 * @since 1.0
  */
function mapo_routes_waypoints_js( $r = false, $js_var = 'google.maps' )
{
	echo mapo_get_routes_waypoints_js( $r, $js_var );
}
	function mapo_get_routes_waypoints_js( $r = false, $js_var = 'google.maps' )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		foreach( (array)$route->waypoints as $wp )
			$points[] = 'new '. $js_var .'.LatLng('. $wp->lat .', '. $wp->lng .')';
		
		return join( ',', (array)$points );
	}

/**
 * Routes waypoints infowindows
 * @since 1.0
  */
function mapo_routes_waypoints_infowindow( $r = false )
{
	echo mapo_get_routes_waypoints_infowindow( $r );
}
	function mapo_get_routes_waypoints_infowindow( $r = false )
	{
		global $route_template;
		
		$route = ( ! $r ) ? $route_template->route : $r;

		foreach( (array)$route->waypoints as $wp )
			$points[] = "['". esc_js( $wp->title ) ."','". esc_js( $wp->description ) ."']";
		
		return join( ',', (array)$points );
	}
	
/*
* Get the activity comments
* @since 1.0
*/
function mapo_routes_has_activity( $r = false )
{
	global $bp, $route_template;

	$route = ( ! $r ) ? $route_template->route : $r;

	return bp_has_activities( array( 'object' => 'routes', 'primary_id' => $route->id , 'show_hidden' => true, 'display_comments' => 'threaded' ) );
}

/*
* The next event
* @since 1.0
*/
function mapo_next_route_link( $suffix = '&rarr;' )
{
	echo mapo_get_next_route_link( $suffix );
}
	/*
	* Get the next event
	* @since 1.0
	*/
	function mapo_get_next_route_link( $suffix = '&rarr;' )
	{
		global $bp;
		
		$route = mapo_get_adjacent_route( false );
		
		$link = '';
		if( ! empty( $route ) )
			$link .= '<a href="'. mapo_get_routes_link( $route ) .'" title="'. __( 'View the next route', 'mapo' ) .'">'. $route->name .' '. $suffix .'</a>';
		
		return $link;
	}
	
/*
* The previous route
* @since 1.0
*/
function mapo_previous_route_link( $prefix = '&larr;' )
{
	echo mapo_get_previous_route_link( $prefix );
}
	/*
	* Get the previous route
	* @since 1.0
	*/
	function mapo_get_previous_route_link( $prefix = '&larr;' )
	{
		global $bp;
		
		$route = mapo_get_adjacent_route( true );

		$link = '';
		if( ! empty( $route ) )
			$link .= '<a href="'. mapo_get_routes_link( $route ) .'" title="'. __( 'View the previous route', 'mapo' ) .'">'. $prefix .' '. $route->name .'</a>';
		
		return $link;
	}

/*
* JS for single routes
* @since 1.0
*/
function mapo_single_route_js( $route = false, $type = false, $zoom = false, $nav = false, $typenav = false, $scale = false )
{
	echo  mapo_get_single_route_js( $route, $type, $zoom, $nav, $typenav, $scale );
}

	function mapo_get_single_route_js( $route = false, $type = false, $zoom = false, $nav = false, $typenav = false, $scale = false, $rid = false )
	{
		global $route_template;
		
		if( ! $route )
			$route = $route_template->route;
			
		if( ! $route )
			return false;
			
		if( ! $rid )
			$rid = $route->id;
			
		$t = ( empty( $type ) ) ? mapo_get_routes_type( $route ) : $type;
		$z = ( empty( $zoom ) ) ? mapo_get_routes_zoom( $route ) : $zoom;
		$n = ( empty( $nav ) ) ? '' : 'navigationControl: '. $nav .",\n";
		$tn = ( empty( $typenav ) ) ? '' : 'mapTypeControl: '. $typenav .",\n";
		$s = ( empty( $scale ) ) ? '' : 'scaleControl: '. $scale .",\n";
	
		$out = '<script type="text/javascript">'."\n";
		$out .= 'var infowindow'. $rid .'; '."\n";
		$out .= 'function singleInitialize'. $rid .'() {'."\n";
			$out .= 'var mapLatLng'. $rid .' = new google.maps.LatLng('. mapo_get_routes_default_lat( $route ) .', '. mapo_get_routes_default_lng( $route ) .');'."\n";
			$out .= 'var mapOptions'. $rid .' = {'."\n";
				$out .= 'zoom: '. $z .','."\n";
				$out .= 'center: mapLatLng'. $rid .','."\n";
				$out .= $n . $tn . $s;
				$out .= 'mapTypeId: google.maps.MapTypeId.'. strtoupper( $t ) ."\n";
			$out .= '};'."\n";
		
			$out .= 'var map'. $rid .' = new google.maps.Map(document.getElementById("route-map-'. $rid .'"), mapOptions'. $rid .');'."\n";
			$out .= 'infowindow'. $rid .' = new google.maps.InfoWindow();'."\n";
			$out .= 'var pathCoordinates'. $rid .' = ['. mapo_get_routes_waypoints_js( $route ) .'];'."\n";
			$out .= 'var waypoints'. $rid .' = ['. mapo_get_routes_waypoints_infowindow( $route ) .'];'."\n";
			$out .= 'var image'. $rid .' = new google.maps.MarkerImage('."\n";
				$out .= '"'. MAPO_URLPATH .'css/images/circle.png",'."\n";
				$out .= 'new google.maps.Size(16, 16),'."\n";
				$out .= 'new google.maps.Point(0, 0),'."\n";
				$out .= 'new google.maps.Point(8, 8)'."\n";
			$out .= ');'."\n";
			$out .= 'for (var i = 0; i < waypoints'. $rid .'.length; i++) {'."\n";
				$out .= 'var wp'. $rid .' = waypoints'. $rid .'[i];'."\n";
				$out .= 'var marker'. $rid .' = new google.maps.Marker({'."\n";
					$out .= 'position: pathCoordinates'. $rid .'[i],'."\n";
					$out .= 'icon: image'. $rid .','."\n";
					$out .= 'map: map'. $rid .','."\n";
					$out .= 'title: wp'. $rid .'[0]'."\n";
				$out .= '});'."\n";
				$out .= 'google.maps.event.addListener(marker'. $rid .', \'click\', (function(m, c) {'."\n";
					$out .= 'return function() {'."\n";
						$out .= 'infowindow'. $rid .'.setContent(c);'."\n";
						$out .= 'infowindow'. $rid .'.open(map'. $rid .', m);'."\n";
					$out .= '};'."\n";
				$out .= '})(marker'. $rid .', wp'. $rid .'[1]));'."\n";
			$out .= '}'."\n";
			$out .= 'var pathRoute'. $rid .' = new google.maps.Polyline({'."\n";
				$out .= 'path: pathCoordinates'. $rid .','."\n";
				$out .= 'strokeColor: "#3355FF",'."\n";
				$out .= 'strokeOpacity: 0.8,'."\n";
				$out .= 'strokeWeight: 4'."\n";
			$out .= '});'."\n";
			$out .= 'pathRoute'. $rid .'.setMap(map'. $rid .');'."\n";
		$out .= '}'."\n";
		$out .= 'jQuery(document).ready(function() {'."\n";
			$out .= 'singleInitialize'. $rid .'();'."\n";
		$out .= '});'."\n";
		$out .= '</script>'."\n";
        
        return apply_filters( 'mapo_get_single_route_js', $out );
	}
	
/*
* JS for member routes overview
* @since 1.0
*/
function mapo_overview_js( $div_id = 'routes-overview-map', $user = false, $lat = false, $lng = false, $zoom = 2, $type = 'HYBRID' )
{
	echo mapo_get_overview_js( $div_id, $user, $lat, $lng, $zoom, $type );
}
	function mapo_get_overview_js( $div_id = 'routes-overview-map', $user = false, $lat = false, $lng = false, $zoom = 2, $type = 'HYBRID' )
	{
		global $mapo;
		
		if( ! $lat )
			$lat = $mapo->options->map_location['lat'];

		if( ! $lng )
			$lng = $mapo->options->map_location['lng'];
		
		$out  = '<script type="text/javascript">'."\n";
		$out .= 'function overviewInitialize() {'."\n";
			$out .= 'var mapLatLng = new google.maps.LatLng('. $lat .', '. $lng .');'."\n";
			$out .= 'var mapOptions = {'."\n";
				$out .= 'zoom: '. $zoom .','."\n";
				$out .= 'center: mapLatLng,'."\n";
				$out .= 'mapTypeId: google.maps.MapTypeId.'. $type ."\n";
			$out .= '};'."\n";
		
			$out .= 'var map = new google.maps.Map(document.getElementById("'. $div_id .'"), mapOptions);'."\n";

			if( mapo_has_routes() ) :
				while ( mapo_routes() ) : mapo_the_route();
				
				$desc = mapo_get_routes_description_raw();
				
				if( $user )
				{
					$avatar = mapo_get_routes_user_avatar();
					$style = ' style="padding-left:60px;"';
				}
				
				if( $desc )
					$description = '<p>'. esc_js( $desc ) .'</p>';
				
				$out .= 'var infowindow'. mapo_get_routes_id() .' = new google.maps.InfoWindow();'."\n";
				$out .= 'var content'. mapo_get_routes_id() .' = \''. $avatar .'<div'. $style .'><a href="'. esc_js( mapo_get_routes_link( false, 'routes', true ) ) .'">'. esc_js( mapo_get_routes_name() ) .'</a>'. $description .'</div>\';'."\n";
				$out .= 'var pathCoordinates'. mapo_get_routes_id() .' = ['. mapo_get_routes_waypoints_js() .'];'."\n";
				$out .= 'var pathRoute'. mapo_get_routes_id() .' = new google.maps.Polyline({'."\n";
					$out .= 'path: pathCoordinates'. mapo_get_routes_id() .','."\n";
					$out .= 'strokeColor: "'. mapo_generate_color_code( mapo_get_routes_id() . mapo_get_routes_name_raw() ) .'",'."\n";
					$out .= 'strokeOpacity: 0.8,'."\n";
					$out .= 'strokeWeight: 4'."\n";
				$out .= '});'."\n";
				$out .= 'pathRoute'. mapo_get_routes_id() .'.setMap(map);'."\n";
				$out .= 'google.maps.event.addListener(pathRoute'. mapo_get_routes_id() .', \'click\', function(event) {'."\n";
					$out .= 'infowindow'. mapo_get_routes_id() .'.setContent(content'. mapo_get_routes_id() .');'."\n";
					$out .= 'infowindow'. mapo_get_routes_id() .'.setPosition(event.latLng);'."\n";
					$out .= 'infowindow'. mapo_get_routes_id() .'.open(map);'."\n";
				$out .= '});'."\n";
				endwhile;
			endif;
			
		$out .= '}'."\n";

		$out .= 'jQuery(document).ready(function() {'."\n";
			$out .= 'overviewInitialize();'."\n";
		$out .= '});'."\n";
		$out .= '</script>'."\n";
        
        return apply_filters( 'mapo_get_overview_js', $out );
	}

/*
* Legend for routes overview
* @since 1.0
*/
function mapo_overview_legend()
{
	echo mapo_get_overview_legend();
}
	function mapo_get_overview_legend()
	{
		$out = '';
		
		if( mapo_has_routes() ) :
			while ( mapo_routes() ) : mapo_the_route();
			
			$out .= '<div class="legend-item">';
			$out .= '<span class="legend-color" style="background:'. mapo_generate_color_code(  mapo_get_routes_id() . mapo_get_routes_name_raw() ) .'"></span>';
			$out .= '<span class="legend-title"><a href="'. mapo_get_routes_link( false, 'routes', true ) .'">'. mapo_get_routes_name() .'</a></span>';
			$out .= '</div>';
						
			endwhile;
		endif;
        
        return apply_filters( 'mapo_get_overview_legend', $out );
	}
?>