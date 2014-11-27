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
 * Add map menu (plus all sub menues) to an enabled WP admin bar
 *
 * @package Core
 * @since 	1.3.1
 */
function mapo_add_wp_admin_bar_menus()
{
	global $wp_admin_bar, $bp, $mapo;
	
	if( ! bp_use_wp_admin_bar() || ! is_user_logged_in() || $mapo->options->enable_routes == false )
		return false;

	$map_link = bp_loggedin_user_domain() . $bp->mapology->slug. '/';

	$wp_admin_bar->add_menu( array(
		'id'	 => 'maps-main',
		'parent' => $bp->my_account_menu_id,
		'title'  => __( 'Maps', 'events' ),
		'href'   => $map_link
	) );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'map-routes',
		'parent' => 'maps-main',
		'title'  => __( 'Routes', 'mapo' ),
		'href'   => $map_link .'routes'
	) );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'map-overview',
		'parent' => 'maps-main',
		'title'  => __( 'Overview', 'mapo' ),
		'href'   => $map_link .'overview'
	) );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'map-edit',
		'parent' => 'maps-main',
		'title'  => __( 'Edit', 'mapo' ),
		'href'   => $map_link .'edit'
	) );

	$wp_admin_bar->add_menu( array(
		'id'	 => 'map-create',
		'parent' => 'maps-main',
		'title'  => __( 'Create', 'mapo' ),
		'href'   => $map_link .'create'
	) );
}
add_action( 'bp_setup_admin_bar', 'mapo_add_wp_admin_bar_menus' );
?>