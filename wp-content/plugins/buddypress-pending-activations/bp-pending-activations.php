<?php
/*
Plugin Name: BuddyPress Pending Activations
Plugin URI: http://wordpress.org/extend/plugins/buddypress-pending-activations/
Description: Manage pending member activations (keys) for main BuddyPress installation - (not for multisite blogs signups - yet.)
Author: Patrick McCoy
Author URI: http://wordpress.org/extend/plugins/buddypress-pending-activations/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 1.0.2
Text Domain: bp-pending-activations
Network: true
*/

define ('BPPA_BASE_PATH', dirname(__FILE__));
define ('BPPA_BASE_URL', plugins_url('', __FILE__));
define ('BPPA_CSS_URL', BPPA_BASE_URL.'/includes/css');
define ('BPPA_INC_PATH', BPPA_BASE_PATH."/includes");
define ('BPPA_LIB_PATH', BPPA_INC_PATH."/lib");

class BP_Pending_Activations {
    //variables

	private $settings_page;
	public $resend_updated = false;
	public $activate_updated = false;
	public $delete_updated = false;
	public $message = false;
	
    public function __construct() {
		add_action('bp_admin_tabs', array(&$this, 'add_admin_tab'));
		add_action(bp_core_admin_hook(), array(&$this, 'init'), 10);
		add_action('bp_admin_head', array(&$this, 'admin_head'));
		add_action('admin_print_styles', array(&$this, 'add_styles'));
		add_action('bp_core_activated_user', array(&$this, 'activated_user'), 3, 3);
		add_action('bp_core_signup_user', array(&$this, 'clear_cache'));
		
		$this->settings_page = bp_core_do_network_admin() ? 'settings.php' : 'options-general.php';
		
    }
	
	public function init()
	{
		$add_tab = add_submenu_page(
				$this->settings_page, 
				'Pending Activations', 
				'Pending Activations', 
				'manage_options', 
				'bp-pending-activations',
				array(&$this, 'activations_mgr')
				);
		
		add_action("admin_head-$add_tab", 'bp_core_modify_admin_menu_highlight');
	}
	
	public function activations_mgr()
	{
		include "bp-activations-manager.php";
	}
	
	public function activated_user($user_id, $key, $user)
	{
		delete_user_meta($user_id, 'activation_key_resent');
		wp_cache_delete('bppa_activations_count');
	}
	
	public function add_admin_tab()
	{
		$class = "nav-tab";
		if (isset($_GET['page']) && $_GET['page'] == 'bp-pending-activations') {
			$class = "nav-tab nav-tab-active";
		}
		
		$href = bp_get_admin_url( add_query_arg( array( 'page' => 'bp-pending-activations' ), 'admin.php' ) );
		
		echo '<a href="'.$href.'" class="'.$class.'">Pending Activations ('.  number_format(bppa_get_pending_count()).')</a>';
	}
	
	public function add_styles()
	{
		if (isset($_GET['page']) && $_GET['page'] == 'bp-pending-activations') {
			wp_enqueue_style('bppa-styles', BPPA_CSS_URL.'/bppa_styles.css');
		}
	}


	/**
	 * Removes the submenu under "Settings" so the page only displays as a tab under Settings>BuddyPress
	 */
	public function admin_head()
	{
		remove_submenu_page($this->settings_page, 'bp-pending-activations');
	}
	
	public function clear_cache()
	{
		wp_cache_delete('bppa_activations_count');
	}
}

add_action('bp_include', 'bppa_load');

function bppa_load()
{
	$objPending = new BP_Pending_Activations();
}

function bppa_get_pending_count()
{
	global $wpdb;
	
	$count = wp_cache_get('bppa_activations_count');
	if (!$count) {
		$count = $wpdb->get_var(
			"SELECT COUNT(u.ID) FROM {$wpdb->usermeta} m, {$wpdb->users} u 
				WHERE u.ID = m.user_id
				AND u.user_status = 2 
				AND m.meta_key = 'activation_key' 
				ORDER BY u.user_registered ASC"
		);
		
		$count = (!empty($count)) ? $count : 0;
		
		wp_cache_set('bppa_activations_count', $count, 'bp');
	}

	return $count;
}
?>
