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
 
require( MAPO_ABSPATH .'core/models/mapo-coords.php' );
require( MAPO_ABSPATH .'core/mapo-db.php' );
require( MAPO_ABSPATH .'core/mapo-proximity.php' );
require( MAPO_ABSPATH .'core/mapo-conditionals.php' );
require( MAPO_ABSPATH .'core/mapo-filters.php' );
require( MAPO_ABSPATH .'core/mapo-js-css.php' );
require( MAPO_ABSPATH .'core/mapo-activity.php' );
require( MAPO_ABSPATH .'core/mapo-helpers.php' );

if( mapo_are_routes_enabled() ) :
	require( MAPO_ABSPATH .'core/mapo-menu.php' );
	require( MAPO_ABSPATH .'core/mapo-routes.php' );
endif;

if( mapo_is_post_coords_enabled() )
	require( MAPO_ABSPATH .'core/mapo-posts.php' );

if( mapo_is_post_type_enabled() )
	require( MAPO_ABSPATH .'core/mapo-post-type.php' );

if( is_admin() )
	require( MAPO_ABSPATH .'core/mapo-mediatab.php' );

if( mapo_is_address_enabled() )
	require( MAPO_ABSPATH .'core/mapo-groups.php' );

/**
 * Setup BP globals
 * @since 1.0
 */
function mapo_setup_globals()
{
	global $bp, $mapo;

	$bp->mapology = new stdClass;
	$bp->mapology->id = 'mapology';
	$bp->mapology->slug = MAPOLOGY_SLUG;
	$bp->mapology->root_slug = $bp->pages->{$bp->mapology->slug}->slug;

	$bp->active_components[$bp->mapology->slug] = 1;
	$bp->loaded_components[$bp->mapology->slug] = $bp->mapology->id;

	$bp->loggedin_user->has_location = mapo_has_user_location();
	$bp->mapology->view_styles = apply_filters( 'mapo_view_styles', array( 'list', 'grid' ) );

	$mapo->date_format = get_blog_option( BP_ROOT_BLOG, 'date_format' );
}
add_action( 'bp_setup_globals', 'mapo_setup_globals' );

/**
 * Setup Settings page
 * @since 1.0
 */
function mapo_setup_settings_nav()
{
	global $mapo;
	
	if( $mapo->options->enable_no_privacy == true )
		return false;
	
	bp_core_new_subnav_item( array(
		'name' 				=> __( 'Map', 'mapo' ),
		'slug' 				=> 'map',
		'parent_url' 		=> bp_loggedin_user_domain() . bp_get_settings_slug() . '/',
		'parent_slug' 		=> bp_get_settings_slug(),
		'screen_function' 	=> 'mapo_map_settings',
		'position' 			=> 40,
		'item_css_id' 		=> 'settings-map',
		'user_has_access' 	=> bp_is_my_profile()
		)
	);	
}
add_action( 'bp_setup_nav', 'mapo_setup_settings_nav' );

/**
 * Settings page setup
 * @since 1.0
 */
function mapo_map_settings()
{
	global $bp_settings_updated;

	$bp_settings_updated = false;

	if( isset( $_POST['submit'] ) )
	{
		check_admin_referer( 'mapo_settings_map' );
		
		if( isset( $_POST['privacy'] ) && in_array( $_POST['privacy'], array( 'everyone', 'members', 'friends', 'nobody' ) ) ) :
			update_user_meta( bp_loggedin_user_id(), 'map_privacy', $_POST['privacy'] );

			$bp_settings_updated = true;
		endif;
	}
	
	add_action( 'bp_template_title', 'mapo_map_settings_title' );
	add_action( 'bp_template_content', 'mapo_map_settings_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * Settings page title
 * @since 1.0
 */
function mapo_map_settings_title()
{
	echo '<h3>'. __( 'Map Settings', 'mapo' ) .'</h3>';
}

/**
 * Settings page content
 * @since 1.0
 */
function mapo_map_settings_content()
{
	global $bp_settings_updated;
	
	$privacy = get_user_meta( bp_loggedin_user_id(), 'map_privacy', true );
	
	if ( $bp_settings_updated ) { ?>
		<div id="message" class="updated fade">
			<p><?php _e( 'Changes Saved.', 'mapo' ) ?></p>
		</div>
	<?php } ?>

	<form action="<?php echo bp_loggedin_user_domain() . bp_get_settings_slug() . '/map/' ?>" method="post" id="settings-form" class="standard-form">
		<?php wp_nonce_field( 'mapo_settings_map' ) ?>
		
        <table class="notification-settings zebra" id="mapo-map-settings">
            <tbody>
                <tr>
                	<td class="icon"></td>
                    <th><?php _e( 'Who can see my location?', 'mapo' ) ?></th>
                    <td>
                    	<label><input type="radio" name="privacy" value="everyone"<?php if( $privacy == 'everyone' ) echo ' checked="checked"'; ?> /> <?php _e( 'Everyone', 'mapo' ) ?></label>
                    </td>
                   <td>
                    	<label><input type="radio" name="privacy" value="members"<?php if( $privacy == 'members' ) echo ' checked="checked"'; ?> /> <?php _e( 'Members', 'mapo' ) ?></label>
                    </td>
                    <td>
                    	<label><input type="radio" name="privacy" value="friends"<?php if( $privacy == 'friends' ) echo ' checked="checked"'; ?> /> <?php _e( 'Friends', 'mapo' ) ?></label>
                   </td>
                   <td>
                    	<label><input type="radio" name="privacy" value="nobody"<?php if( $privacy == 'nobody' || ! $privacy ) echo ' checked="checked"'; ?> /> <?php _e( 'Nobody', 'mapo' ) ?></label>
                    </td>
                </tr>
			</tbody>
		</table>
		
        <?php do_action( 'mapo_map_settings_action_end', bp_loggedin_user_id()  ); ?>

		<div class="submit">
			<p><input type="submit" name="submit" value="<?php _e( 'Save Changes', 'mapo' ) ?>" id="submit" class="auto"/></p>
		</div>
	</form>
	<?php
}

/**
* Add Buddyvents to pages without components
* TODO: fix this somehow
* @since 2.0
*/
function mapo_add_to_pages_without_components( $pages )
{
	global $bp;
	
	$pages[] = $bp->mapology->slug;
	
	return $pages;
}
add_filter( 'bp_pages_without_components', 'mapo_add_to_pages_without_components' );

/**
* Delete all user data upon user deletion
* @since 1.0
*/
function mapo_delete_route_activity( $route_id )
{
	global $bp;
	
	bp_activity_delete(  array(
		'component' => $bp->mapology->slug,
		'item_id' => $route_id
	) );
}
add_action( 'mapo_deleted_new_route', 'mapo_delete_route_activity' );
?>