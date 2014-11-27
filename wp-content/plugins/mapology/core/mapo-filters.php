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

add_filter( 'mapo_before_save_coords_user_id', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_coords_group_id', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_coords_lat', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_coords_lng', 'wp_filter_kses', 1 );

add_filter( 'mapo_before_save_routes_coords_route_id', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_title', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_description', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_description', 'force_balance_tags' );
add_filter( 'mapo_before_save_routes_coords_image', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_lat', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_lng', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_coords_coord_order', 'wp_filter_kses', 1 );

add_filter( 'mapo_before_save_routes_user_id', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_name', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_slug', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_description', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_description', 'force_balance_tags' );
add_filter( 'mapo_before_save_routes_type', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_default_lat', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_default_lng', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_zoom', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_start_date', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_end_date', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_date_created', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_image', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_group_id', 'wp_filter_kses', 1 );
add_filter( 'mapo_before_save_routes_public', 'wp_filter_kses', 1 );

add_filter( 'mapo_routes_get_route_description', 'wptexturize' );
add_filter( 'mapo_routes_get_route_description', 'make_clickable' );
add_filter( 'mapo_routes_get_route_description', 'bp_groups_filter_kses', 1 );
add_filter( 'mapo_routes_get_route_description', 'wpautop' );
add_filter( 'mapo_routes_get_route_description', 'convert_chars' );
add_filter( 'mapo_routes_get_route_description', 'stripslashes' );

add_filter( 'mapo_routes_get_route_description_excerpt', 'wptexturize' );
add_filter( 'mapo_routes_get_route_description_excerpt', 'make_clickable' );
add_filter( 'mapo_routes_get_route_description_excerpt', 'wp_filter_kses', 1 );
add_filter( 'mapo_routes_get_route_description_excerpt', 'wpautop' );
add_filter( 'mapo_routes_get_route_description_excerpt', 'convert_chars' );
add_filter( 'mapo_routes_get_route_description_excerpt', 'stripslashes' );

add_filter( 'mapo_routes_get_route_name', 'wptexturize' );
add_filter( 'mapo_routes_get_route_name', 'convert_chars' );
add_filter( 'mapo_routes_get_route_name', 'stripslashes' );
add_filter( 'mapo_routes_get_route_name', 'convert_chars' );

add_filter( 'mapo_routes_get_waypoint_title', 'wptexturize' );
add_filter( 'mapo_routes_get_waypoint_title', 'convert_chars' );
add_filter( 'mapo_routes_get_waypoint_title', 'stripslashes' );
add_filter( 'mapo_routes_get_waypoint_title', 'convert_chars' );

add_filter( 'mapo_routes_get_waypoint_description', 'wptexturize' );
add_filter( 'mapo_routes_get_waypoint_description', 'make_clickable' );
add_filter( 'mapo_routes_get_waypoint_description', 'wp_filter_kses', 1 );
add_filter( 'mapo_routes_get_waypoint_description', 'wpautop' );
add_filter( 'mapo_routes_get_waypoint_description', 'convert_chars' );
add_filter( 'mapo_routes_get_waypoint_description', 'stripslashes' );

if( mapo_is_oembed_enabled() )
{
	add_filter( 'mapo_routes_get_route_description', 'ray_bp_oembed', 9 );
	add_filter( 'mapo_routes_get_route_description_excerpt', 'ray_bp_oembed', 9 );
}
?>