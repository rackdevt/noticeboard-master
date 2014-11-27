<?php
/**
 * Activations List Table
 *
 * Extends WP_List_Table used to manage pending activations
 *
 *
 */

class Activations_List_Table extends WP_List_Table {
    //variables

    public function __construct() {
		parent::__construct(array(
			'singular' => 'account',
			'plural' => 'accounts',
			'ajax' => false
		));
    }
	
	public function get_bulk_actions()
	{
		$actions = array(
			'delete' => 'Delete User',
			'resend' => 'Resend Key',
			'activate' => 'Activate'
		);
		
		return $actions;
	}
	
	public function column_cb($item)
	{
		return sprintf('<input type="checkbox" name="users[]" value="%s"', $item['id']);
	}
	
	public function column_default($item, $col_name)
	{
		return $item[$col_name];
	}
	
	public function column_date_resent($item)
	{
		$date_resent = get_user_meta($item['id'], 'activation_key_resent', true);
		if ($date_resent) {
			return bp_core_time_since($date_resent);
		}
		
		return '';
	}
	
	public function column_email($item)
	{
		return '<a href="mailto:'.$item['email'].'">'.$item['email'].'</a>';
	}
	
	public function column_date_registered($item)
	{
		return bp_core_time_since($item['date_registered']);
	}
	
	public function column_username($item)
	{
		$actions = array();
		$activate_url = add_query_arg(array("action" => 'activate', 'users[]' => $item['id']));
		$actions['activate'] = sprintf('<a href="%s">%s</a>', $activate_url, 'Activate');
		$resend_url = add_query_arg(array("action" => 'resend', 'users[]' => $item['id']));
		$actions['resend'] = sprintf('<a href="%s">%s</a>', $resend_url, 'Resend Email');
		$delete_url = add_query_arg(array("action" => 'delete', 'users[]' => $item['id']));
		$actions['delete'] = sprintf('<a href="%s">%s</a>', $delete_url, 'Delete'); 
		
		$user_edit = admin_url('user-edit.php?user_id='.$item['id']);
		$username = get_avatar($item['email'], 50).'<a href="'.$user_edit.'">'.$item['username'].'</a>';
		return sprintf('%1$s %2$s', $username, $this->row_actions($actions) );
	}

	public function get_columns()
	{
		return array(
			'cb' => '<input type="checkbox" />',
			'username' => 'Username',
			'name' => 'Name',
			'email' => 'E-mail',
			'date_registered' => 'Date Registered',
			'date_resent' => 'Date Resent'
		);
	}
	
	public function get_sortable_columns()
	{
		return array(
			'username' => array('username', false),
			'name' => array('name', false),
			'email' => array('email', false),
			'date_registered' => array('date_registered', true)
		);
	}
	
	public function prepare_items()
	{
		global $wpdb;
		$cols = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden = array();
		$this->_column_headers = array($cols, $hidden, $sortable);
		
		$order_by = "date_registered";
		$order = "ASC";
		if (isset($_GET['orderby'])) {
			$order_by = $_GET['orderby'];
		}
		
		if (isset($_GET['order'])) {
			$order = strtoupper($_GET['order']);
		}
		
		$search_where = '';
		if (isset($_POST['s']) && !empty($_POST['s'])) {
			if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-'.$this->_args['plural'])) {
				wp_die('Security check failed');
			}
			
			$search_where = " AND (
					u.user_login LIKE '%".trim($_POST['s'])."%' OR 
					u.user_email LIKE '%".trim($_POST['s'])."%'
				) ";
		}
		
		$per_page = 25;
		$page = (isset($_GET['paged'])) ? $_GET['paged'] : 1;
		$offset = ($page - 1) * $per_page;
		$strQry = "SELECT u.ID as id,
						u.user_email as email,
						u.user_registered as date_registered,
						u.user_login as username,
						u.user_nicename as name,
						m.meta_value as activation_key 
					FROM $wpdb->usermeta m, 
						 $wpdb->users u 
					WHERE u.ID = m.user_id 
						AND u.user_status = 2 
						AND m.meta_key = 'activation_key'{$search_where}
					ORDER BY {$order_by} {$order} LIMIT {$offset},{$per_page}";
		
		$this->items = $wpdb->get_results($strQry, ARRAY_A);
		
		$total_items = $wpdb->num_rows;
		
		$this->set_pagination_args(array(
			'per_page' => $per_page,
			'total_items' => bppa_get_pending_count()
		));
	}
	
}
?>
