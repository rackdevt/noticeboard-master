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
* Setup the directory
* @since 1.0
*/
function mapo_directory_setup()
{
	global $bp, $mapo;

	if( bp_is_current_component( $bp->mapology->slug ) && empty( $bp->displayed_user->id ) )
	{
		$bp->is_directory = true;

		/***************************************************************************
		//
		// CHANGE THE VIEW STYLE
		//
		***************************************************************************/
		if( bp_is_current_action( 'view' ) && in_array( $bp->action_variables[0], $bp->mapology->view_styles ) )
		{
			@setcookie( 'bpe_view_style', $bp->action_variables[0], time() + 31104000, COOKIEPATH );

			bp_core_add_message( sprintf( __( 'The view has been changed to %s-style', 'mapo' ), $bp->action_variables[0] ) );
			
			if( empty( $bp->action_variables[1] ) )
			{
				bp_core_redirect( bp_get_root_domain() .'/'. $bp->mapology->root_slug .'/' );
			}
			elseif( bp_is_action_variable( 'group', 1 ) )
			{
				$group = new BP_Groups_Group( $bp->action_variables[2] );
				bp_core_redirect( bp_get_group_permalink( $group ) . $bp->mapology->slug .'/' );
			}
			elseif( bp_is_action_variable( 'user', 1 ) )
			{
				bp_core_redirect( bp_core_get_user_domain( $bp->action_variables[2] ) . $bp->mapology->slug .'/' );
			}
		}
		
		do_action( 'mapo_directory_setup' );
		bp_core_load_template( apply_filters( 'mapo_template_directory', 'maps/index' ) );
	}
}
add_action( 'wp', 'mapo_directory_setup', 2 );

/**
 * User directory screen
 * @since 1.0
 */
function mapo_screen_routes()
{
	global $bp;
	
	if( empty( $bp->displayed_user->id ) )
		return false;

	if( isset( $_POST['send_route_comment'] ) )
	{
		check_admin_referer( 'mapo_route_comment' );
		
		if( empty( $_POST['comment_text'] ) )
		{
			bp_core_add_message( __( 'Please enter a comment text', 'mapo' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
		
		if( empty( $_POST['route_id'] ) )
		{
			bp_core_add_message( __( 'There was a problem saving your comment. Please try again.', 'mapo' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
		
		$show = ( in_array( $_POST['privacy'], array( 0, 1 ) ) ) ? true : false;
		bp_activity_add( array(
			'action' => sprintf( __( '%s posted a new route comment:', 'mapo' ), bp_core_get_userlink( $bp->loggedin_user->id ) ),		

			'hide_sitewide' => $show, 
			'component' => 'routes', 
			'type' => 'route_comment', 
			'content' => $_POST['comment_text'], 
			'item_id' => $_POST['route_id']
		) );

		bp_core_add_message( __( 'Your comment has been added.', 'mapo' ) );
		bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
	}

	bp_core_load_template( apply_filters( 'mapo_template_directory_user', 'maps/member/home' ) );
}

/**
 * Edit screen
 * @since 1.0
 */
function mapo_screen_edit()
{
	global $bp;
	
	if( empty( $bp->displayed_user->id ) )
		return false;
	
	if( bp_is_current_component( $bp->mapology->slug ) )
	{
		if( isset( $_POST['delete-route'] ) )
		{
			check_admin_referer( 'mapo_edit_route' );
			
			$route = new MAPO_Routes( null, $bp->action_variables[0], $bp->displayed_user->id );
			$privacy = $route->public;
			$route_id = $route->id;
			
			mapo_waypoints_delete_by_route_id( $route_id );
			
			$route->delete();
			
			if( $privacy == 3 )
				mapo_rebuild_geo_sitemap();

			do_action( 'mapo_deleted_new_route', $route_id );

			bp_core_add_message( __( 'Your route was successfully deleted!', 'mapo' ) );
			bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/edit/' );
		}
		
		if( isset( $_POST['edit-route'] ) )
		{
			check_admin_referer( 'mapo_edit_route' );
			
			/*if( empty( $_POST['name'] ) || empty( $_POST['public'] ) )
			{
				bp_core_add_message( __( 'Please fill in all fields marked by *.', 'mapo' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			}

			if( empty( $_POST['waypoint'] ) )
			{
				bp_core_add_message( __( 'You need to mark at least one position on the map.', 'mapo' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			}*/
			
			$route = mapo_get_by_slug( $bp->action_variables[0], $bp->displayed_user->id );
			
			if( $route->user_id != $bp->loggedin_user->id )
			{
				bp_core_add_message( __( 'You are not allowed to edit this route.', 'mapo' ), 'error' );
				bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
			}
			
			$type = ( empty( $_POST['type'] ) ) ? 'HYBRID' : strtoupper( $_POST['type'] );
			$zoom = ( empty( $_POST['zoom'] ) ) ? 2 : (int)$_POST['zoom'];
			$group_id = ( empty( $_POST['group_id'] ) ) ? '0' : $_POST['group_id'];
			
			$coords = mapo_return_coords( $_POST['default_coords'] );
			
			$changed = false;
			
			if( $route->group_id != $group_id || $route->name != $_POST['name'] || $route->description != $_POST['description'] || $route->type != $type ||
				$route->zoom != $zoom || $route->start_date != $_POST['start_date'] || $route->end_date != $_POST['end_date'] ||
				$route->public != $_POST['public'] || $route->default_lat != $coords['lat'] || $route->default_lng != $coords['lng'] )
				$changed = true;

			if( $changed === true )
			{
				if( ! $route_id = mapo_add_route( $route->id, $route->user_id, $group_id, $_POST['name'], $route->slug, $_POST['description'], $type, $coords['lat'], $coords['lng'], $zoom, $_POST['start_date'], $_POST['end_date'], $route->date_created, $_POST['public'] ) )
				{
					bp_core_add_message( __( 'Your route could not be published. Please try again!', 'mapo' ), 'error' );
					bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
				}
			}
			
			// rebuild the sitemap if the privacy changed
			if( $route->public != $_POST['public'] )
				mapo_rebuild_geo_sitemap();
			
			$order = explode( ',', $_POST['order_coords'] );
			
			// remove the old waypoints
			//mapo_waypoints_delete_by_route_id( $route->id );
			
			// add new waypoints
			foreach( (array)$_POST['waypoint'] as $k => $point )
			{
				$order_num = array_search( $k, (array)$order );
				$point_coords = mapo_return_coords( $point['latlng'] );
				mapo_add_route_point( null, $route->id, $point['title'], $point['description'], '', $point_coords['lat'], $point_coords['lng'], $order_num, 'route' );
			}

			// activity entries are attached to this hook
			do_action( 'mapo_edited_new_route', $route );

			bp_core_add_message( __( 'Your route was successfully edited!', 'mapo' ) );
			bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/edit/'. $route->slug .'/' );
		}
	}
	bp_core_load_template( apply_filters( 'mapo_template_directory_user', 'maps/member/home' ) );
}

/**
 * Create screen
 * @since 1.0
 */
function mapo_screen_create()
{
	global $bp;
	
	if( empty( $bp->displayed_user->id ) )
		return false;
	
	if( bp_is_current_component( $bp->mapology->slug ) )
	{
		if( isset( $_POST['save-route'] ) )
		{
			check_admin_referer( 'mapo_add_route' );
			
			/*if( empty( $_POST['name'] ) || empty( $_POST['public'] ) )
			{
				bp_core_add_message( __( 'Please fill in all fields marked by *.', 'mapo' ), 'error' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/create/' );
			}

			if( empty( $_POST['waypoint'] ) )
			{
				bp_core_add_message( __( 'You need to mark at least one position on the map.', 'mapo' ), 'error' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/create/' );
			}*/
			
			// get the unique slug
			$slug = sanitize_title_with_dashes( $_POST['name'] );
			$slug = mapo_remove_accents( $slug );
			$slug = mapo_check_unique_slug( $slug );
			
			$type = ( empty( $_POST['type'] ) ) ? 'HYBRID' : strtoupper( $_POST['type'] );
			$zoom = ( empty( $_POST['zoom'] ) ) ? 2 : (int)$_POST['zoom'];
			$group_id = ( empty( $_POST['group_id'] ) ) ? 2 : (int)$_POST['group_id'];
			
			$coords = mapo_return_coords( $_POST['default_coords'] );

			if( ! $route_id = mapo_add_route( null, $bp->displayed_user->id, $group_id, $_POST['name'], $slug, $_POST['description'], $type, $coords['lat'], $coords['lng'], $zoom, $_POST['start_date'], $_POST['end_date'], gmdate( 'Y-m-d H:i:s' ), $_POST['public'] ) )
			{
				bp_core_add_message( __( 'Your route could not be published. Please try again!', 'mapo' ), 'error' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/create/' );
			}
			else
			{
				$order = explode( ',', $_POST['order_coords'] );
				
				foreach( (array)$_POST['waypoint'] as $k => $point )
				{
					$order_num = array_search( $k, (array)$order );
					$point_coords = mapo_return_coords( $point['latlng'] );
					mapo_add_route_point( null, $route_id, $point['title'], $point['description'], '', $point_coords['lat'], $point_coords['lng'], $order_num, 'route' );
				}

				// activity entries are attached to this hook
				do_action( 'mapo_saved_new_route', $route_id );

				// rebuild the sitemap
				if( $_POST['public'] == 3 )
					mapo_rebuild_geo_sitemap();
				
				bp_core_add_message( __( 'Your route was successfully published!', 'mapo' ) );
				bp_core_redirect( $bp->displayed_user->domain . $bp->mapology->slug .'/routes/'. $slug .'/' );
			}			
		}
	}
	bp_core_load_template( apply_filters( 'mapo_template_directory_user', 'maps/member/home' ) );
}

/**
 * Overview screen
 * @since 1.0
 */
function mapo_screen_overview()
{
	global $bp;
	
	if( empty( $bp->displayed_user->id ) )
		return false;

	bp_core_load_template( apply_filters( 'mapo_template_directory_user', 'maps/member/home' ) );
}
?>