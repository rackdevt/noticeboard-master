<?php

//add_filter('show_admin_bar', '__return_false');   --- this to diable BP Admin bar at top of page; reinstate when Going Live!

add_filter( 'bp_do_register_theme_directory', '__return_true' );

add_filter( 'bcg_get_post_type','bcg_custom_post_type' );

function bcg_custom_post_type( $post_type ){
    return 'classifieds';//change to your custom post type
}

add_filter( 'bcg_get_taxonomy', 'bcg_custom_taxonomy' );

function bcg_custom_taxonomy( $tax ){
    
   return 'category';//change to your custom taxonomy
   //return 'group_id_mapped_txnmy';//change to your custom taxonomy
   //return 'group_id_mapped_txnmy';//change to your custom taxonomy
    //return 'group_id_tag';//change to your custom taxonomy
}


function bbg_amend_profile_tabs() {
global $bp;

bp_core_remove_nav_item('blogs');
bp_core_remove_nav_item('friends');
bp_core_remove_nav_item('posts');
bp_core_remove_nav_item('notifications');
bp_core_remove_nav_item('photos');
bp_core_remove_nav_item('settings');

bp_core_remove_subnav_item( $bp->activity->slug, 'favorites' );

$bp->bp_nav['activity']['name'] = 'Wall';
$bp->bp_nav['groups']['name'] = 'Groups';


$bp->bp_nav['wall']['position'] = 0;
$bp->bp_nav['groups']['position'] = 30;
$bp->bp_nav['messages']['position'] = 40;
$bp->bp_nav['following']['position'] = 50;
$bp->bp_nav['followers']['position'] = 60;
$bp->bp_nav['profile']['position'] = 100;


//$bp->bp_options_nav['groups']['home']['name'] = 'Wall';
$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = __('Wall', 'buddypress');

$bp->bp_nav['classifieds']['position'] = 01;
$bp->bp_options_nav['profile']['change-avatar']['name'] = 'Change Profile Picture';

$bp->bp_options_nav['activity']['following']['name'] = 'Friends feed';



}
add_action( 'bp_setup_nav', 'bbg_amend_profile_tabs', 999 );


// Filter wp_nav_menu() to add profile link
add_filter( 'wp_nav_menu_items', 'my_nav_menu_profile_link' );
function my_nav_menu_profile_link($menu) { 	
	if (!is_user_logged_in())
		return $menu;
	else
		$profilelink = '<li><a href="' . bp_loggedin_user_domain( '/' ) . '">' . __('MyGNB') . '</a></li>';
		$menu = $menu . $profilelink;
		return $menu;
}




$editor = get_role( 'editor' );
$editor->add_cap( 'gravityforms_edit_forms' );
$editor->add_cap( 'gravityforms_delete_forms' );
$editor->add_cap( 'gravityforms_create_form' );
$editor->add_cap( 'gravityforms_view_entries' );
$editor->add_cap( 'gravityforms_edit_entries' );
$editor->add_cap( 'gravityforms_delete_entries' );
$editor->add_cap( 'gravityforms_view_settings' );
$editor->add_cap( 'gravityforms_edit_settings' );
$editor->add_cap( 'gravityforms_export_entries' );
$editor->add_cap( 'gravityforms_view_entry_notes' );
$editor->add_cap( 'gravityforms_edit_entry_notes' );

/**
* This custom function is for PLUGIN: Blog Categories for Groups - to implement adding Offered & Wanted items to respective Groups
* It adds a new Category for each public or private group
*/

function createCatAfterGroup() {
  global $wpdb;
  $group = $wpdb->get_row("SELECT * FROM `wp_bp_groups` ORDER BY date_created DESC");
  $checkedgrpstatus = $_POST['group-status'];  
      wp_insert_term($group->name, 'category');
  
	  if ($checkedgrpstatus == "public" || $checkedgrpstatus == "private") {  	   
		   wp_insert_term($group->name, 'group_id_mapped_txnmy');
	  }
}
add_action('groups_created_group', 'createCatAfterGroup');


function remove_xprofile_links() {
    remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
}
add_action('bp_setup_globals', 'remove_xprofile_links');


function my_setup_nav() {
      global $bp;
 
      bp_core_new_nav_item( array( 
      'name' => __( 'Offered & Wanted', 'buddypress' ), 
      'slug' => 'my-noticeboard', 
      'position' => 10,
      'screen_function' => 'my_noticeboard_link',
      'show_for_displayed_user' => true,
      'default_subnav_slug' => 'my-sub',
      'item_css_id' => 'my_noticeboard'
      ) );

      bp_core_new_subnav_item( array( 
        'name' => __( 'My Noticeboard', 'buddypress' ), 
        'slug' => 'my-sub', 
        'parent_url' => $bp->loggedin_user->domain . '/', 
        'parent_slug' => 'my-noticeboard', 
        'screen_function' => 'my_noticeboard_link' ) );

      bp_core_new_subnav_item( array( 
        'name' => __( 'Site noticeboard' ), 
        'slug' => 'site-noticeboard', 
        'parent_url' => '/', 
        'parent_slug' => 'my-noticeboard', 
        'screen_function' => 'my_noticeboard_link' ) );
     
}
 
add_action( 'bp_setup_nav', 'my_setup_nav', 1000 );
 
 
function my_noticeboard_content() {
  ?>
  
    <?php locate_template( array( 'buddypress/members/single/my_noticeboard.php' ), true ) ?>

    <?php
}
 
function my_noticeboard_link () {
  add_action( 'bp_template_content', 'my_noticeboard_content' );
  bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}


define ( 'BP_SEARCH_SLUG', 'search' );
?>
