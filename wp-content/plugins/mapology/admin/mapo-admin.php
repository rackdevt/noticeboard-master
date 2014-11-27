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
 
class MAPO_Admin_Loader
{
	var $update_available;
	var $db_upgrade;

	/**
	 * Constructor
	 * @since 1.0
	 */
	function __construct()
	{
		global $mapo;

		require( MAPO_ABSPATH .'admin/mapo-core.php' );
		require( MAPO_ABSPATH .'admin/mapo-process.php' );
		
		$this->update_available = ( get_blog_option( BP_ROOT_BLOG, 'mapology_update_exists' ) == 'yes' ) ? true : false;
		$this->db_upgrade = get_blog_option( BP_ROOT_BLOG, 'mapo_dbversion' );
		$this->admin_notice = get_blog_option( BP_ROOT_BLOG, 'mapo_admin_notice' );
		
		if( $this->admin_notice )
			add_action( 'admin_notices', array( $this, 'render_message' ) );
		
		add_action( 'admin_menu', array( $this, 'add_menu' ), 20 );
		
		add_action( 'admin_print_scripts', array( $this, 'load_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'load_styles' ) );
		add_action( 'after_plugin_row_'. MAPO_PLUGIN, array( $this, 'add_row' ) );
		add_action( 'admin_init', array( $this, 'remove_upgrade_nag' ), 2 );
		add_action( 'admin_init', 'mapo_settings_processor', 2 );

		add_filter( 'plugin_row_meta', array( $this, 'add_links' ), 10, 2 );
		add_filter( 'contextual_help', array( $this, 'show_help' ), 10, 2 );
		
		$this->check_upgrade();
	}

	/**
	 * Check for a db table upgrade
	 * @since 1.1.2
	 */
	function check_upgrade()
	{
		// no need to show on Buddyvents pages
		if( strpos( MAPO_FOLDER, $_GET['page'] ) === false )
		{
			if( MAPO_DBVERSION != $this->db_upgrade )
				add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __( \'The Mapology database tables need to get upgraded to v%s. Please follow <a href="%s">this link</a>.\', "events" ) . \'</strong></p></div>\', MAPO_DBVERSION, admin_url().\'admin.php?page=\'. MAPO_FOLDER );' ), 20 );
		}
	}

	/**
	 * Add the options page
	 * @since 1.0
	 */
	function add_menu()
	{
		global $mapo, $wpdb;
		
		$update = ( $this->update_available == true ) ? '<span title="' . esc_attr(__( 'Update Available', 'mapo' ) ) .'" class="update-plugins count-1"><span class="update-count">1</span></span>' : '';

		add_menu_page( __( 'Maps', 'mapo' ), __( 'Maps', 'mapo' ) . $update, 'manage_options', MAPO_FOLDER, array( $this, 'show_menu' ), MAPO_URLPATH .'admin/images/shabu-logo-small.png', 9 );
	    add_submenu_page( MAPO_FOLDER , __( 'Settings', 'mapo' ), __( 'Settings', 'mapo' ), 'manage_options', MAPO_FOLDER, array( $this, 'show_menu' ) );
	    add_submenu_page( MAPO_FOLDER , __( 'Hooks', 'mapo' ), __( 'Hooks', 'mapo' ), 'manage_options', MAPO_FOLDER .'-hooks', array( $this, 'show_menu' ) );
	    add_submenu_page( MAPO_FOLDER , __( 'Readme', 'mapo' ), __( 'Readme', 'mapo' ), 'manage_options', MAPO_FOLDER .'-readme', array( $this, 'show_menu' ) );
	    add_submenu_page( MAPO_FOLDER , __( 'Changelog', 'mapo' ), __( 'Changelog', 'mapo' ), 'manage_options', MAPO_FOLDER .'-changelog', array( $this, 'show_menu' ) );
	}

	/**
	 * Display the options page
	 * @since 1.0
	 */
	function show_menu()
	{
		global $mapo;

		if( $this->db_upgrade != MAPO_DBVERSION )
		{
			include_once ( MAPO_ABSPATH .'admin/mapo-upgrade.php' );
			mapo_upgrade();
			return;			
		}

        if( current_user_can( 'activate_plugins' ) )
		{
    		if( $this->check_new_version() )
    			echo $this->render_message( sprintf( __( 'A new version of Mapology is available! Please download the latest version <a href="http://shabushabu.eu/downloads/?category=15">here</a>. <a style="float:right" href="%s">Remove</a>', 'events' ), admin_url( '?remove_nag=right-this-second-young-lady' ) ), 'error' );
    	}
		
		switch( $_GET['page'] )
		{
			case MAPO_FOLDER:  default:
				include_once( MAPO_ABSPATH .'admin/mapo-settings.php' );
				$settings = new MAPO_Admin_Settings();
				break;

			case MAPO_FOLDER .'-hooks':
				include_once( MAPO_ABSPATH .'admin/mapo-hooks.php' );
				$hooks = new MAPO_Admin_Hooks();
				break;
				
			case MAPO_FOLDER .'-readme':
				include_once( MAPO_ABSPATH .'admin/mapo-readme.php' );
				$readme = new MAPO_Admin_Readme();
				break;

			case MAPO_FOLDER .'-changelog':
				include_once( MAPO_ABSPATH .'admin/mapo-changelog.php' );
				$changelog = new MAPO_Admin_Changelog();
				break;
		}
	}

	/**
	 * Load necessary scripts
	 * @since 1.0
	 */
	function load_scripts()
	{
		// no need to go on if it's not a plugin page
		if( ! isset( $_GET['page'] ) )
			return;

		switch( $_GET['page'] )
		{
			case MAPO_FOLDER:
				wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false' );
				break;
		}
	}		
	
	/**
	 * Load necessary styles
	 * @since 1.0
	 */
	function load_styles()
	{
		// no need to go on if it's not a plugin page
		if( ! isset( $_GET['page'] ) )
			return;

		switch( $_GET['page'] )
		{
			case MAPO_FOLDER:
			case MAPO_FOLDER .'-hooks':
			case MAPO_FOLDER .'-readme':
			case MAPO_FOLDER .'-changelog':
				wp_enqueue_style( 'mapoadmin', MAPO_URLPATH .'admin/css/mapo-admin.css', false, '1.0', 'screen' );
				break;
		}
	}
	
	/**
	 * Add some helpful links
	 * @since 1.0
	 */
	function show_help( $help, $screen_id )
	{
		global $mapo;
		
		if( in_array( $screen_id, array(
			'toplevel_page_'. MAPO_FOLDER,
			'maps_page_'. MAPO_FOLDER .'-hooks',
			'maps_page_'. MAPO_FOLDER .'-changelog',
			'maps_page_'. MAPO_FOLDER .'-readme'
		) ) )
		{
			$help  = '<h5>' . __( 'Get help for Mapology', 'events' ) . '</h5>';
			$help .= '<div class="metabox-prefs">';
			$help .= '<a href="'. $mapo->home_url .'forums/">' . __( 'Support Forums', 'events' ) . '</a><br />';
			$help .= '<a href="'. $mapo->home_url .'donation/">' . __( 'Donate', 'events' ) . '</a><br />';
			$help .= '</div>';
		}
			
		return $help;
	}

	/**
	 * Maybe show the upgrade message
	 * @since 1.0
	 */
	function add_row()
	{
		//$upgrade_link = '<a href="'. wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin='. urlencode( MAPO_PLUGIN ), 'upgrade-plugin_'. MAPO_PLUGIN ) .'">'. __( 'Upgrade Automatically', 'mapo' ) .'</a>';
		
		if( $this->update_available == true )
			echo '</tr><tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">'. __( 'A new version of Mapology is available! Please download the latest version <a href="http://shabushabu.eu/downloads/?category=15">here</a>.', 'mapo' ) .'</div></td>';
	}

	/**
	 * Check for an update
	 * @since 1.0
	 */
	function check_new_version()
	{
		global $mapo;
		
		if( $this->update_available == true )
			return true;

		$interval = get_blog_option( BP_ROOT_BLOG, 'mapology_next_update' );

		if( $interval < time() || empty( $interval ) )
		{
			// check twice a day
			$interval = time() + 43200;
			
			update_blog_option( BP_ROOT_BLOG, 'mapology_next_update', $interval );
			
			$options = array();
			$options['headers'] = array(
                'User-Agent' => 'Mapology v'. $mapo->version,
                'Referer' => get_bloginfo( 'url' )
			);
			
			$response = wp_remote_request( $mapo->home_url .'versions.php', $options );
			
			if( is_wp_error( $response ) )
				return false;
		
			if( 200 != $response['response']['code'] )
				return false;
				
			$version = unserialize( $response['body'] );

			if( is_array( $version ) )
			{
				if( version_compare( $version['mapology'], $mapo->version, '>' ) )
				{
					update_blog_option( BP_ROOT_BLOG, 'mapology_update_exists', 'yes' );
					return true;
				}
			} 
				
			delete_blog_option( BP_ROOT_BLOG, 'mapology_update_exists' );
			return false;
		}
	}

	/**
	 * Remove the upgrade nag
	 * @since 1.0
	 */
	function remove_upgrade_nag()
	{
		if( isset( $_GET['remove_nag'] ) && $_GET['remove_nag'] = 'right-this-second-young-lady' )
		{
			delete_option( 'mapology_update_exists' );
			wp_redirect( admin_url() );
			exit();
		}
	}

	/**
	 * Add some links to plugin setup page
	 * @since 1.0
	 */
	function add_links( $links, $file )
	{
		global $mapo;
		
		if( $file == $mapo->plugin_name )
		{
			$links[] = '<a href="'. $mapo->home_url .'forums/">' . __( 'Support Forums', 'events' ) . '</a>';
			$links[] = '<a href="'. $mapo->home_url .'donation/">' . __( 'Donate', 'events' ) . '</a>';
		}
		
		return $links;
	}

	/**
	 * Add an admin notice
	 * @since 1.5
	 */
	function add_message( $message, $type = false )
	{
		if( ! $type )
			$type = 'updated';
	
		update_blog_option( BP_ROOT_BLOG, 'mapo_admin_notice', array( 'message' => $message, 'type' => $type ) );
	}

	/**
	 * Render an admin notice
	 * @since 1.1.2
	 */
	function render_message( $message = false, $type = false)
	{
		if( ! $message )
			$message = $this->admin_notice['message'];
			
		if( ! $type )
			$type = $this->admin_notice['type'];
		
		if( $message )
		{
			?>
			<div id="message" class="<?php echo $type ?> inline">
				<p><strong><?php echo stripslashes( $message ); ?></strong></p>
			</div>
			<?php
			
			delete_blog_option( BP_ROOT_BLOG, 'mapo_admin_notice' );
		}
	}
}
?>