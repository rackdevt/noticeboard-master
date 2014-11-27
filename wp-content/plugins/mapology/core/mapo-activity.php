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
 
/*
* Add activity comments for single view events
* @since 1.1
*/
function mapo_add_route_activity( $route_id, $user_id )
{
	mapo_load_template( 'maps/member/activity' );
}
add_action( 'mapo_end_single_route_action', 'mapo_add_route_activity', 10, 2 );

/*
* Record an event activity item
* @since 1.0
*/
function mapo_record_activity( $args = '' )
{
	global $bp;

	if( ! function_exists( 'bp_activity_add' ) )
		return false;
		
	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->mapology->slug,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => bp_core_current_time(),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/*
* Add an activity entry for a new event
* @since 1.0
*/
function mapo_add_new_route_activity( $route_id )
{
	global $bp;
	
	$route = new MAPO_Routes( $route_id );
	
	$hide_show = ( in_array( $route->public, array( 0, 1 ) ) ) ? true : false;
	$route_link = mapo_get_routes_link( $route );
	
	mapo_record_activity( array(	
		'action' => apply_filters( 'mapo_activity_action_new_route', sprintf( __( '%1$s published the route <a href="%2$s">%3$s</a>', 'logs' ), bp_core_get_userlink( $route->user_id ), $route_link, $route->name ), $route_link, $route ),
		'content' => bp_create_excerpt( $route->description ),
		'primary_link' => $route_link,
		'type' => 'new_route',
		'item_id' => $route->id,
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'mapo_saved_new_route', 'mapo_add_new_route_activity' );

/*
* Add an activity entry when a route  has been edited
* @since 1.0
*/
function mapo_add_edited_route_activity( $route )
{
	global $bp;
	
	$hide_show = ( in_array( $route->public, array( 0, 1 ) ) ) ? true : false;
	$route_link = mapo_get_routes_link( $route );
	
	mapo_record_activity( array(	
		'action' => apply_filters( 'mapo_activity_action_edited_route', sprintf( __( '%1$s edited the route <a href="%2$s">%3$s</a>', 'logs' ), bp_core_get_userlink( $route->user_id ), $route_link, $route->name ), $route_link, $route ),
		'content' => bp_create_excerpt( $route->description ),
		'primary_link' => $route_link,
		'type' => 'edited_route',
		'item_id' => $route->id,
		'hide_sitewide' => $hide_show
	) );
}
add_action( 'mapo_edited_new_route', 'mapo_add_edited_route_activity' );

/**
 * Activity entry for updated location
 * Uses static google maps
 * @since 1.0
 */
function mapo_updated_location_activity( $user_id, $coords )
{
	global $bp;
	
	// check the privacy setting
	$privacy = get_user_meta( $user_id, 'map_privacy', true );
	
	if( $privacy != 'everyone' )
		return false;
	
	$static = apply_filters( 'mapo_updated_location_args', array(
		'zoom'	  => 12,
		'width'	  => 620,
		'height'  => 200,
		'maptype' => 'hybrid'
	) );

	$act_id =  bp_activity_get_activity_id( array(
		'user_id' 	=> $user_id,
		'component' => $bp->mapology->slug,
		'type' 		=> 'updated_location'
	) );
	
	$id = ( is_numeric( $act_id ) ) ? $act_id : false;

	mapo_record_activity( array(
		'id' => $id,
		'user_id' => $user_id,
		'action' => apply_filters( 'mapo_activity_action_updated_location', sprintf( __( '%1$s updated their location', 'mapo' ), bp_core_get_userlink( $user_id ) ), $user_id ),
		'content' => '<img src="http://maps.google.com/maps/api/staticmap?center='. $coords['lat'] .','. $coords['lng'] .'&zoom='. $static['zoom'] .'&size='. $static['width'] .'x'. $static['height'] .'&maptype='. $static['maptype'] .'&markers=color:red|'. $coords['lat'] .','. $coords['lng'] .'&sensor=false" alt="Location" width="'. $static['width'] .'" height="'. $static['height'] .'" />',
		'type' => 'updated_location',
		'item_id' => str_replace( '.', '', $coords['lat'] . $coords['lng'] ),
		'recorded_time' => gmdate( 'Y-m-d H:i:s' ),
		'hide_sitewide' => false
	) );
}
add_action( 'mapo_updated_location', 'mapo_updated_location_activity', 10, 2 );

/*
* Add new events to activity filters
* @since 1.0
*/
function mapo_add_activity_filter()
{
	global $mapo;
	
	if( $mapo->options->enable_routes === true ) :
	?>
    <option value="new_route"><?php _e( 'Show New Routes', 'mapo' ) ?></option>
    <option value="edit_route"><?php _e( 'Show Edited Routes', 'mapo' ) ?></option>
    <option value="route_comment"><?php _e( 'Show Route Comments', 'mapo' ) ?></option>
    <?php endif; ?>
	
    <option value="updated_location"><?php _e( 'Show New Locations', 'mapo' ) ?></option>
    <?php
}
add_action( 'bp_activity_filter_options', 'mapo_add_activity_filter' );
add_action( 'bp_member_activity_filter_options', 'mapo_add_activity_filter' );
?>