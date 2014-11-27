<?php

/**
 * BP Activations Manager
 *
 * Shell for Activations manager
 *
 */

if (!defined('ABSPATH')) {
	exit('Direct access not allowed');
}

global $wpdb, $current_user;

$activate_updated = false;
$resend_updated = false;
$delete_updated = false;
$message = false;
if (isset($_REQUEST['action'])) {
	if (isset($_REQUEST['users']) && is_array($_REQUEST['users']) && count($_REQUEST['users'])) {
		$users = $_REQUEST['users'];
	} else {
		$message = "No users selected";
	}

	switch ($_REQUEST['action']) {
		case 'delete':
			if (!current_user_can('delete_users')) {
				$message = "You can&#8217;t delete users.";
			}

			foreach ($users as $id) {
				if (!current_user_can('delete_user', $id) || is_super_admin($id) || $id == $current_user->ID) {
					$this->message = 'You can&#8217;t delete that user.';
					return;
				}

				wp_delete_user($id);
			}
			wp_cache_delete('bppa_activations_count');
			$delete_updated = true;
			break;
		case 'resend':
			foreach ($users as $id) {
				$user = get_user_by('id', $id);
				$activation_key = get_user_meta($id, 'activation_key', true);
				bp_core_signup_send_validation_email($id, $user->user_email, $activation_key);
				update_user_meta( $user->ID, 'activation_key_resent', current_time('mysql', TRUE));
			}

			$resend_updated = true;
			break;
		case 'activate':
			$userids = implode(',', $users);

			$activate_users = $wpdb->get_results( $wpdb->prepare(
				"SELECT u.ID, u.user_login, m.meta_value 
					FROM 
						$wpdb->usermeta m, 
						$wpdb->users u 
					WHERE u.ID = m.user_id 
					AND m.meta_key = 'activation_key' 
					AND u.ID IN (%s)", $userids)
			);

			foreach ($users as $id) {
				$user = get_user_by('id', $id);
				$activation_key = get_user_meta($id, 'activation_key', true);
				$activate = apply_filters('bp_core_activate_account', bp_core_activate_signup($activation_key));

				if (!empty($activate->errors)) {
					$message = "There was an error activating this account, please try again: {$user->user_login}";
				} else {
					/* Check if the avatar folder exists. If it does, move rename it, move it and delete the signup avatar dir */
					if ( file_exists( bp_core_avatar_upload_path() . '/avatars/signups/' . wp_hash( $activate ) ) ) {
						@rename( bp_core_avatar_upload_path() . '/avatars/signups/' . wp_hash( $activate ), bp_core_avatar_upload_path() . '/avatars/' . $activate );
					}
				}
			}
			wp_cache_delete('bppa_activations_count');
			$activate_updated = true;
			break;
		default:
			break;
	}
}



include 'includes/lib/Activations_List_Table.php';
?>
<div id="bp-pending-activations" class="wrap">
	<h2 class="nav-tab-wrapper"><?php bp_core_admin_tabs('Pending Activations'); ?></h2>
	<div class="wrap">
		<?php
		if ($activate_updated) {
			echo "<div id='message' class='updated fade'><p>Users Activated</p></div>";
		}
		
		if ($resend_updated) {
			echo "<div id='message' class='updated fade'><p>Resent Activation Keys</p></div>";
		}
		
		if ($delete_updated) {
			echo "<div id='message' class='updated fade'><p>Users Deleted</p></div>";
		}
		
		if ($message) {
			echo "<div id='message' class='updated fade'><p>{$message}</p></div>";
		}
		?>
		<form method="post">
			<?php 
			$objListTable = new Activations_List_Table();
			$objListTable->prepare_items();
			$objListTable->search_box("Search by username/email", "pending_search");
			$objListTable->display();
			?>	
		</form>
	</div>
</div>