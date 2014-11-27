<?php
/*
Plugin Name: Admin Log
Plugin URI: http://www.presscoders.com/
Description: Need to see who is accessing what in your admin section? This Plugin logs admin activity, and shows the page, user information, and time of access.
Version: 1.44
Author: David Gwyer
Author URI: http://www.presscoders.com/
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer(at)presscoders.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
To Do:
1. Be able to log other user roles other than admin? If so have these in a different color?
2. Add text box to allow the admin log limit to be altered. At the moment it is fixed at 200 lines.
3. Add setting link on main Plugin page.
4. Change the Plugin settings to be on the 'Tools' menu instead of the 'Settings' menu.
5. Add option to remove 'admin-ajax.php' pages from the log that don't have an additional query string?
6. Add option to show latest log entries at the start of the log rather than at the end (current behavior). Otherwise you have to keep scrolling to the end of the log. Note if this is implemented then the remove_first_line() method needs to remove the last line of the log, not the first.
7. Have an option to show admin log on dashboard too? Or maybe move it to the dashboard and leave the Plugin settings page for just the Plugin options. If so don't need to worry about changing the settings page to 'Tools' menu.
*/

class WPGo_Admin_Log_Plugin {

	/**
	 * WPGo_Admin_Log class constructor.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		register_activation_hook(__FILE__, array( $this, 'add_defaults' ) );
		register_uninstall_hook(__FILE__, array( 'WPGo_Admin_Log_Plugin', 'delete_plugin_options' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'plugins_loaded', array( $this, 'write_text' ) );
   }

	// Define default option settings
	public function add_defaults() {
		$tmp = get_option('wpgo_admin_log_options');
		if( !is_array($tmp) ) {
			delete_option('wpgo_admin_log_options'); // so we don't have to reset all the 'off' checkboxes too!
			$arr = array("chk_ignore_adminlog" => "0", "txtar_log" => "");
			update_option('wpgo_admin_log_options', $arr);
		}
	}

	// Delete options table entries only when plugin deactivated AND deleted
	public static function delete_plugin_options() {
		delete_option('wpgo_admin_log_options');
		delete_option('wpgo_admin_log');
	}

	// Use the Settings API for our Plugin options
	public function admin_init(){
		register_setting( 'wpgo_admin_log_plugin_options', 'wpgo_admin_log_options' );
	}

	// Add menu page
	public function add_options_page() {
		add_options_page('Admin Log Options Page', 'Admin Log', 'manage_options', __FILE__, array( $this, 'render_form' ) );
	}

	// Draw the menu page itself
	public function render_form() {
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2>Admin Log</h2>
			<?php

			// Check to see if user clicked on the clear admin button
			if(isset($_POST['wpgo_admin_log_clear'])) {
				// Clear admin log
				update_option('wpgo_admin_log', null);
				?><div class="error" style="margin-bottom: 0px;"><p>Admin log erased!</p></div><?php
			}
			?>
			<div>
				<table class="form-table">
					<tr>
						<td>
							<textarea style="font-family:courier new;" name="wpgo_admin_log_textarea" rows="25" cols="110" type='textarea' readonly><?php echo get_option('wpgo_admin_log'); ?></textarea>
							<div>Current log count: <?php echo substr_count( get_option('wpgo_admin_log'), "\r\n" ); ?> (max. limit 200)<span style="font-style:italic;color:#666666;margin:0px 0px 30px 2px;"></span></div>
						</td>
					</tr>
					<tr>
						<td Width="554">
							<div>
								<form action="<?php echo $this->currURL(); // current page url ?>" method="post" id="wpgo_admin_log_clear_form" style="display:inline;">
									<span class="submit-admin-log">
										<input type="submit" onclick="return confirm('Are you sure? The admin log will be erased!');" class="button submit-button clear-log-button" value="Clear Admin Log" name="admin_log_clear">
										<input type="hidden" name="wpgo_admin_log_clear" value="true">
									</span>
								</form>
							</div>
						</td>
					</tr>
					<tr>
					</tr>
				</table>
			</div>

			<form method="post" action="options.php">
				<?php settings_fields( 'wpgo_admin_log_plugin_options' ); ?>
				<?php $options = get_option( 'wpgo_admin_log_options' ); ?>
				<table class="form-table">
					<tr valign="top">
						<td>
							<label><input name="wpgo_admin_log_options[chk_ignore_adminlog]" type="checkbox" value="1" <?php if (isset($options['chk_ignore_adminlog'])) { checked('1', $options['chk_ignore_adminlog']); } ?> /> Ignore Admin Log Page?</label>
							<p class="description">The Admin Log page will be included in the log by default. This can inflate the log which may be undesirable.</p>
						</td>
					</tr>
				</table>
				<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>

			<div style="margin:20px 0px 5px 0px;border-top:#dddddd 1px solid;"></div>

			<table width="600">
				<tr>
					<td>
						<p style="margin-top:20px;font-style:italic;">
							<span><a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/admin-log/images/facebook-icon.png" /></a></span>
							&nbsp;&nbsp;<span><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/admin-log/images/twitter-icon.png" /></a></span>
							&nbsp;&nbsp;<span><a href="http://www.presscoders.com" title="PressCoders.com" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/admin-log/images/pc-icon.png" /></a></span>
						</p>
					</td>
					<td>
						<div style="padding:5px;">
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
								<input type="hidden" name="cmd" value="_s-xclick">
								<input type="hidden" name="hosted_button_id" value="4051383">
								<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
								<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
							</form>
						</div>
					</td>
				<tr>
			</table>
		</div>
		<?php
	}

	public function write_text() {

		if(is_admin()) {
			$options = get_option('wpgo_admin_log_options');
			$log = get_option('wpgo_admin_log');
			$user = wp_get_current_user();

			// while log length too big, remove first line
			while( substr_count( $log, "\r\n" ) >= 200 )
				$log = $this->remove_first_line($log);

			// Is user logged in?
			if ($user) {

				$uri = basename($_SERVER["REQUEST_URI"]);

				$remove_uri = array(
					'admin-ajax.php'
				);

				// don't log specific uri's
				if( in_array( $uri, $remove_uri ) )
					return;

				if ( !( isset($options['chk_ignore_adminlog']) && $options['chk_ignore_adminlog']=="1" && ( $uri=="admin-log.php" || $uri=="options-general.php?page=admin-log%2Fadmin-log.php&settings-updated=true" ) ) ) {

					$url = explode("/wp-admin/", $_SERVER["REQUEST_URI"]);
					$date = date_i18n('j/n/y@G:i:s');
					$txt = $date.' '.$user->display_name.'('.$user->ID.') => ['.$_SERVER['REMOTE_ADDR'].'] '.$url[1]."\r\n";
					$txt = $log.$txt;
					update_option('wpgo_admin_log', $txt);
				}
			}
		}
	}

	public function remove_first_line($txt) {

	  return substr( $txt, strpos($txt, "\r\n")+1 );
	}

	// Get URL of current page
	public function currURL() {
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}

/* Create Plugin class instance. */
$wpgo_admin_log_plugin = new WPGo_Admin_Log_Plugin();