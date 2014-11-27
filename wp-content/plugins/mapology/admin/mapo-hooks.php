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

class MAPO_Admin_Hooks extends MAPO_Admin_Core
{
	/**
	 * Constructor
	 * @since 1.4
	 */
    function __construct()
	{
		$this->head( __( 'Hooks', 'mapo' ) );
		$this->content();
		$this->footer();
    }

	/**
	 * Content of the hooks tab
	 * @since 1.4
	 */
	function content()
	{
		global $bpe;
		?>
		<p><?php _e( 'In WordPress there are 2 kinds of hooks: <i>actions</i> and <i>filters</i>.<br />Actions let you add content and filters let you manipulate content. Below you can find a reference of all hooks used in Mapology.','mapo' ); ?></p>

        <h3><?php _e( 'Actions','mapo' ); ?></h3>

        <dl>
            <dt><code>mapo_update_options_page</code></dt><dd></dd>	
            <dt><code>mapo_tab_content_</code></dt><dd></dd>
            <dt><code>mapo_distance_drop_down</code></dt><dd></dd>
            <dt><code>mapo_group_creation_details</code></dt><dd></dd>
            <dt><code>bpe_save_extra_group_details</code></dt><dd></dd>
            <dt><code>mapo_group_contact_page</code></dt><dd></dd>
            <dt><code>mapo_updated_location</code></dt><dd></dd>
            <dt><code>mapo_directory_setup</code></dt><dd></dd>
            <dt><code>mapo_deleted_new_route</code></dt><dd></dd>
            <dt><code>mapo_edited_new_route</code></dt><dd></dd>
            <dt><code>mapo_saved_new_route</code></dt><dd></dd>
            <dt><code>mapo_global_routes_feed</code></dt><dd></dd>
            <dt><code>mapo_feed_head</code></dt><dd></dd>
            <dt><code>mapo_global_feed_geo_entry</code></dt><dd></dd>
            <dt><code>mapo_global_feed_item</code></dt><dd></dd>
            <dt><code>mapo_group_events_feed</code></dt><dd></dd>
            <dt><code>mapo_group_feed_head</code></dt><dd></dd>
            <dt><code>mapo_group_feed_item</code></dt><dd></dd>
            <dt><code>mapo_user_events_feed</code></dt><dd></dd>
            <dt><code>mapo_user_feed_head</code></dt><dd></dd>
            <dt><code>mapo_user_feed_item</code></dt><dd></dd>
            <dt><code>mapo_coords_before_save</code></dt><dd></dd>
            <dt><code>mapo_coords_after_save</code></dt><dd></dd>
            <dt><code>mapo_routes_coords_before_save</code></dt><dd></dd>
            <dt><code>mapo_routes_coords_after_save</code></dt><dd></dd>
            <dt><code>mapo_routes_before_save</code></dt><dd></dd>
            <dt><code>mapo_routes_after_save</code></dt><dd></dd>
            <dt><code>loop_end</code></dt><dd></dd>
            <dt><code>loop_start</code></dt><dd></dd>
            <dt><code>template_notices</code></dt><dd></dd>
            <dt><code>mapo_member_route_before_loop</code></dt><dd></dd>
            <dt><code>mapo_member_routes_actions</code></dt><dd></dd>
            <dt><code>mapo_member_route_after_loop</code></dt><dd></dd>
            <dt><code>bp_activity_entry_content</code></dt><dd></dd>
            <dt><code>bp_activity_entry_meta</code></dt><dd></dd>
            <dt><code>mapo_edit_single_js</code></dt><dd></dd>
            <dt><code>mapo_before_member_home_content</code></dt><dd></dd>
            <dt><code>bp_member_options_nav</code></dt><dd></dd>
            <dt><code>mapo_before_member_body</code></dt><dd></dd>
            <dt><code>mapo_before_member_content</code></dt><dd></dd>
            <dt><code>mapo_inside_member_content</code></dt><dd></dd>
            <dt><code>mapo_after_member_body</code></dt><dd></dd>
            <dt><code>mapo_after_member_home_content</code></dt><dd></dd>
            <dt><code>mapo_end_single_route_action</code></dt><dd></dd>
            <dt><code>mapo_dir_route_before_loop</code></dt><dd></dd>
            <dt><code>mapo_dir_route_after_loop</code></dt><dd></dd>
        </dl>
                
        <h3><?php _e( 'Filters','mapo' ); ?></h3>

        <dl>
            <dt><code>mapo_settings_tabs</code></dt><dd></dd>
            <dt><code>mapo_activity_action_new_route</code></dt><dd></dd>
            <dt><code>mapo_activity_action_edited_route</code></dt><dd></dd>
            <dt><code>mapo_activity_action_updated_location</code></dt><dd></dd>
            <dt><code>mapo_get_routes</code></dt><dd></dd>
            <dt><code>mapo_get_sitewide_routes_feed_link</code></dt><dd></dd>		
            <dt><code>mapo_get_user_routes_feed_link</code></dt><dd></dd>
            <dt><code>mapo_get_group_routes_feed_link</code></dt><dd></dd>
            <dt><code>bpe_save_group_creation_details</code></dt><dd></dd>
            <dt><code>mapo_display_group_contact_details</code></dt><dd></dd>
            <dt><code>mapo_upload_path</code></dt><dd></dd>
            <dt><code>mapo_upload_url</code></dt><dd></dd>
            <dt><code>mapo_load_template_filter</code></dt><dd></dd>
            <dt><code>mapo_get_display_route_map</code></dt><dd></dd>
            <dt><code>mapo_display_post_map</code></dt><dd></dd>
            <dt><code>mapo_template_directory</code></dt><dd></dd>
            <dt><code>mapo_template_directory_user</code></dt><dd></dd>
            <dt><code>mapo_before_save_coords_user_id</code></dt><dd></dd>
            <dt><code>mapo_before_save_coords_group_id</code></dt><dd></dd>
            <dt><code>mapo_before_save_coords_lat</code></dt><dd></dd>
            <dt><code>mapo_before_save_coords_lng</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_route_id</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_title</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_description</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_lat</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_lng</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_coords_coord_order</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_user_id</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_group_id</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_name</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_slug</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_description</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_type</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_default_lat</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_default_lng</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_zoom</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_start_date</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_end_date</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_date_created</code></dt><dd></dd>
            <dt><code>mapo_before_save_routes_public</code></dt><dd></dd>
            <dt><code>mapo_has_routes</code></dt><dd></dd>
            <dt><code>mapo_get_routes_pagination_count</code></dt><dd></dd>
            <dt><code>mapo_routes_user_avatar</code></dt><dd></dd>
            <dt><code>mapo_get_routes_user_avatar</code></dt><dd></dd>
            <dt><code>mapo_routes_get_route_name</code></dt><dd></dd>
            <dt><code>mapo_routes_get_route_description</code></dt><dd></dd>
            <dt><code>mapo_routes_get_route_description_excerpt</code></dt><dd></dd>
            <dt><code>mapo_routes_get_waypoint_title</code></dt><dd></dd>
            <dt><code>mapo_routes_get_waypoint_description</code></dt><dd></dd>
            <dt><code>mapo_get_single_route_js</code></dt><dd></dd>
            <dt><code>mapo_get_overview_js</code></dt><dd></dd>
            <dt><code>mapo_get_overview_legend</code></dt><dd></dd>
        </dl>
		<?php	
	}
}
?>