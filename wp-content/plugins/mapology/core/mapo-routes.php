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

require( MAPO_ABSPATH .'core/models/mapo-routes.php' );
require( MAPO_ABSPATH .'core/models/mapo-routes-coords.php' );
require( MAPO_ABSPATH .'core/templatetags/mapo-routes.php' );
require( MAPO_ABSPATH .'core/mapo-feeds.php' );
require( MAPO_ABSPATH .'core/mapo-widget.php' );
require( MAPO_ABSPATH .'core/mapo-screen.php' );

if( mapo_is_group_enabled() )
	require( MAPO_ABSPATH .'core/mapo-grouproutes.php' );

/**
* Setup the user navigation
* @since 1.0
*/
function mapo_setup_nav()
{
	global $bp;

	bp_core_new_nav_item( array( 
		'name' => __( 'Maps', 'mapo' ),
		'slug' => $bp->mapology->slug,
		'position' => 30,
		'screen_function' => 'mapo_screen_routes',
		'default_subnav_slug' => 'routes',
		'show_for_displayed_user' => true,
		'item_css_id' => $bp->mapology->id
		)
	);

	$map_link = ( ! empty( $bp->displayed_user->domain ) ) ? $bp->displayed_user->domain : $bp->loggedin_user->domain;
	$map_link = $map_link . $bp->mapology->slug. '/';

	bp_core_new_subnav_item( array(
		'name' => __( 'Routes', 'mapo' ),
		'slug' => 'routes',
		'parent_url' => $map_link,
		'parent_slug' => $bp->mapology->slug,
		'screen_function' => 'mapo_screen_routes',
		'position' => 10,
		'item_css_id' => 'routes',
		'user_has_access' => true
		)
	);

	bp_core_new_subnav_item( array(
		'name' => __( 'Overview', 'mapo' ),
		'slug' => 'overview',
		'parent_url' => $map_link,
		'parent_slug' => $bp->mapology->slug,
		'screen_function' => 'mapo_screen_overview',
		'position' => 10,
		'item_css_id' => 'overview',
		'user_has_access' => true
		)
	);

	bp_core_new_subnav_item( array(
		'name' => __( 'Edit', 'mapo' ),
		'slug' => 'edit',
		'parent_url' => $map_link,
		'parent_slug' => $bp->mapology->slug,
		'screen_function' => 'mapo_screen_edit',
		'position' => 20,
		'item_css_id' => 'routes-edit',
		'user_has_access' => bp_is_my_profile()
		)
	);

	bp_core_new_subnav_item( array(
		'name' => __( 'Create', 'mapo' ),
		'slug' => 'create',
		'parent_url' => $map_link,
		'parent_slug' => $bp->mapology->slug,
		'screen_function' => 'mapo_screen_create',
		'position' => 30,
		'item_css_id' => 'routes-create',
		'user_has_access' => bp_is_my_profile()
		)
	);
}
add_action( 'bp_setup_nav', 'mapo_setup_nav' );

/**
* Add KML output for every route
* @since 1.0
*/
function mapo_setup_kml()
{
	global $bp, $wp_query;
	
	if( ! bp_is_action_variable( 'kml', 1 ) )
		return false;
		
	if( bp_is_current_component( $bp->mapology->slug ) && bp_is_current_action( 'routes' ) && ! empty( $bp->action_variables[0] ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
		
		require_once( MAPO_ABSPATH .'core/mapo-kml.php' );

		$route = mapo_get_routes( array( 'slug' => $bp->action_variables[0], 'user_id' => $bp->displayed_user->id ) );

		header( "Content-type: application/vnd.google-earth.kml+xml" );
		$kml = new MAPO_KML_Export( $route['routes'][0] );
		die;
	}
}
add_action( 'wp', 'mapo_setup_kml', 0 );

/*
* Rebuild the sitemap
* @since 1.0
*/
function mapo_rebuild_geo_sitemap()
{
	require( MAPO_ABSPATH .'core/mapo-sitemap.php' );
	$sm = new MAPO_KML_Sitemap();
}

/*
* Display the search form
* @since 1.0
*/
function mapo_directory_routes_search_form()
{
	global $bp;

 	$search_value = ( ! empty( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : __( 'Search anything...', 'mapo' );

	?>
	<form action="" method="get" id="search-routes-form">
		<label><input type="text" name="s" id="route_search" value="<?php echo attribute_escape( $search_value ) ?>"  onfocus="if (this.value == '<?php _e( 'Search anything...', 'mapo' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search anything...', 'mapo' ) ?>';}" /></label>
		<input type="submit" id="routes_search_submit" value="<?php _e( 'Search', 'mapo' ) ?>" />
	</form>
	<?php
}

/**
* Remove all group references upon group deletion
* @since 1.0
*/
function mapo_delete_group_data( $group_id )
{
	mapo_remove_group_reference( $group_id );
}
add_action( 'groups_delete_group', 'mapo_delete_group_data' );

/**
* Delete all user data upon user deletion
* @since 1.0
*/
function mapo_delete_user_data( $user_id )
{
	mapo_remove_for_user( $user_id );
	mapo_remove_wp_for_user( $user_id );
}
add_action( 'delete_user', 'mapo_delete_user_data' );
?>