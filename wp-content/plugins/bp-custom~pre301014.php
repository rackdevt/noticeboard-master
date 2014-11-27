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



$bp->bp_nav['activity']['name'] = 'Wall';
$bp->bp_nav['groups']['name'] = 'My Groups';


//$bp->bp_options_nav['groups']['home']['name'] = 'Wall';
$bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = __('Wall', 'buddypress');

$bp->bp_nav['classifieds']['position'] = 01;
$bp->bp_options_nav['profile']['change-avatar']['name'] = 'Change Profile Picture';
}
add_action( 'bp_setup_nav', 'bbg_amend_profile_tabs', 999 );


// Filter wp_nav_menu() to add profile link
//add_filter( 'wp_nav_menu_items', 'my_nav_menu_profile_link' );
//function my_nav_menu_profile_link($menu) { 	
//	if (!is_user_logged_in())
//		return $menu;
//	else
//		$profilelink = '<li><a href="' . bp_loggedin_user_domain( '/' ) . '">' . __('MyGNB') . '</a></li>';
//		$menu = $menu . $profilelink;
//		return $menu;
//}


//Remove Buddypress search drowpdown for selecting members etc
add_filter('bp_search_form_type_select', 'bpmag_remove_search_dropdown'  );
function bpmag_remove_search_dropdown($select_html){
    return '';
}

//force buddypress to not process the search/redirect
remove_action( 'bp_init', 'bp_core_action_search_site', 7 );

//let us handle the unified page ourself
add_action( 'init', 'bp_buddydev_search', 10 );// custom handler for the search
function bp_buddydev_search(){
global $bp;
    if ( bp_is_current_component(BP_SEARCH_SLUG) )//if thids is search page
        bp_core_load_template( apply_filters( 'bp_core_template_search_template', 'search-single' ) );//load the single searh template
}

add_action('advance-search','bpmag_show_search_results',1);//highest priority
/* we just need to filter the query and change search_term=The search text*/
function bpmag_show_search_results(){
    //filter the ajaxquerystring
     add_filter('bp_ajax_querystring','bpmag_global_search_qs',100,2);
}
 
 //modify the query string with the search term
function bpmag_global_search_qs(){
    return 'search_terms='.$_REQUEST['search-terms'];
}
 
//a utility function
function bpmag_is_advance_search(){
global $bp;
if(bp_is_current_component( BP_SEARCH_SLUG))
    return true;
return false;
}

define ( 'BP_SEARCH_SLUG', 'search' );

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

?>