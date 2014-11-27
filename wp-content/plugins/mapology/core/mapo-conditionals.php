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
 * Is the directory
 * @since 1.0
 */
function mapo_is_dir_routes()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && empty( $bp->current_action ) )
		return true;
		
	return false;
}

/**
 * Is the directory map
 * @since 1.0
 */
function mapo_is_dir_routes_map()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'overview' ) )
		return true;
		
	return false;
}

/**
 * Is the member directory
 * @since 1.0
 */
function mapo_is_member_routes()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'routes' ) && empty( $bp->action_variables[0] ) )
		return true;
		
	return false;
}

/**
 * Is the member overview
 * @since 1.0
 */
function mapo_is_member_overview()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'overview' ) && empty( $bp->action_variables[0] ) )
		return true;
		
	return false;
}

/**
 * Is single view
 * @since 1.0
 */
function mapo_is_member_routes_single()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'routes' ) && ! empty( $bp->action_variables[0] ) )
		return true;
		
	return false;
}

/**
 * Check if the current user has a valid location
 * @since 1.0
 */
function mapo_has_user_location()
{
	global $bp;
	
	if( mapo_get_id_by_user( $bp->loggedin_user->id ) )
		return true;
	
	return false;	
}

/**
 * Is a member single edit page
 * @since 1.0
 */
function mapo_is_member_edit_single()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'edit' ) && ! empty( $bp->action_variables[0] ) )
		return true;
		
	return false;
}

/**
 * Is the member edit page
 * @since 1.0
 */
function mapo_is_member_edit()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'edit' ) && empty( $bp->action_variables[0] ) )
		return true;
		
	return false;
}

/**
 * Is the member create page
 * @since 1.0
 */
function mapo_is_member_create()
{
	global $bp;
	
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'create' ) )
		return true;
		
	return false;
}

/**
 * Is group attachment enabled
 * @since 1.0
 */
function mapo_is_group_enabled()
{
	global $mapo;
	
	if( $mapo->options->enable_group === true )
		return true;
		
	return false;
}

/**
 * Is group attachment enabled
 * @since 1.0
 */
function mapo_is_address_enabled()
{
	global $mapo;
	
	if( ! bp_is_active( 'groups' ) )
		return false;
	
	if( $mapo->options->enable_address === true )
		return true;
		
	return false;
}

/**
 * Are routes enabled
 * @since 1.1.2
 */
function mapo_are_routes_enabled()
{
	global $mapo;
	
	if( $mapo->options->enable_routes === true )
		return true;
		
	return false;
}

/**
 * Is custom post type enabled
 * @since 1.0
 */
function mapo_is_post_type_enabled()
{
	global $mapo;
	
	if( $mapo->options->enable_post_type === true )
		return true;
		
	return false;
}

/**
 * Is custom post type enabled
 * @since 1.0
 */
function mapo_is_post_coords_enabled()
{
	global $mapo;
	
	if( $mapo->options->enable_post === true )
		return true;
		
	return false;
}

/**
 * Is oembed enabled
 * @since 1.0
 */
function mapo_is_oembed_enabled()
{
	global $mapo;
	
	if( $mapo->options->enable_oembed === true )
		return true;
		
	return false;
}

/**
 * Is there a date for a route
 * @since 1.0
 */
function mapo_has_date( $r = false )
{
	global $route_template;
	
	$route = ( ! $r ) ? $route_template->route : $r;
	
	if( $route->start_date != '0000-00-00' || $route->end_date != '0000-00-00' )
		return true;
		
	return false;
}

/**
 * Make sure that the current user can view a location
 * @since 1.0
 */
function mapo_location_is_viewable( $user_id = false, $privacy = false )
{
	global $bp, $mapo;
	
	if( $mapo->options->enable_no_privacy == true )
		return true;
	
	if( ! $user_id )
		$user_id = $bp->displayed_user->id;
	
	if( $bp->loggedin_user->id == $user_id )
		return true;
	
	if( ! $privacy )
		$privacy = get_user_meta( $user_id, 'map_privacy', true );
	
	if( $privacy == 'everyone' ) :
		return true;
	
	elseif( $privacy == 'nobody' ) :
		return false;
		
	elseif( $privacy == 'members' ) :
		if( ! is_user_logged_in() )
			return false;
		else 
			return true;
	
	elseif( $privacy == 'friends' ) :
		$friends = friends_get_friend_user_ids( $user_id );
		
		if( in_array( $bp->loggedin_user->id, $friends ) )
			return true;
		else 
			return false;
		
	else :
		return false;
	
	endif;
}
?>