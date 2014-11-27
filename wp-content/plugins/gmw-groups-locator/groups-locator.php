<?php
/*
Plugin Name: GMW Add-on - Groups Locator
Plugin URI: http://www.geomywp.com 
Description: Add locations to groups and create an advance proximity search.
Author: Eyal Fitoussi
Version: 1.1.9
Author URI: http://www.geomywp.com 
Text Domain: GMW-GL
Domain Path: /languages/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
* Activate add-on when activating plugin
* 
* @since 1.2
*/
function gmw_gl_activate_addon() {
        
        $gmw_addons = get_option( 'gmw_addons' );
        
        if ( !isset( $gmw_addons ) ) $gmw_addons = array();
        
        $gmw_addons['groups_locator'] = 'active';		
        update_option( 'gmw_addons', $gmw_addons );
        
}
register_activation_hook( __FILE__, 'gmw_gl_activate_addon' );
              
/**
* Deactivate add-on when Deactivating plugin
* 
* @since 1.2
*/
function gmw_gl_deactivate_addon() {
        
        $gmw_addons = get_option( 'gmw_addons' );
        
        if ( !isset( $gmw_addons ) ) return;
        
        unset( $gmw_addons['groups_locator'] );	
        update_option( 'gmw_addons', $gmw_addons );
}
register_deactivation_hook( __FILE__, 'gmw_gl_deactivate_addon' );

/**
 * GMW Groups Locator Addon class.
 */
class GMW_Groups_Locator {
        
        /**
	 * @var instant
	 * @since 1.2
	 */
        private static $instance;
        
        /**
	 * Main Instance
	 *
	 * @since 1.2
	 * @static
	 * @staticvar array $instance
	 * @return GMW_Groups_Locator
	 */
	public static function instance() {
            
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof GMW_Groups_Locator ) ) {
                                         
			self::$instance = new GMW_Groups_Locator;
			self::$instance->constants();
			self::$instance->includes();
                        self::$instance->actions();
			self::$instance->load_textdomain();
                        
                        if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
                            self::$instance->check_updates();
                        }
                        
		}
           
		return self::$instance;
	}
        
	/**
	 * __construct function.
	 */
	public function __construct() { }
           
        /**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.2
	 * @return void
	 */
	private function constants() {
  
                define( 'GMW_GL_VERSION', '1.1.9');
                define( 'GMW_GL_DB_VERSION', '1.4');
		define( 'GMW_GL_LICENSE_NAME', 'groups_locator' );
                define( 'GMW_GL_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define(	'GMW_GL_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
                
        }
        
        /**
         * Include files
         * 
         * @since 1.2
         * 
         */
        private function includes() {
            
                if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
                    include_once GMW_GL_PATH . '/includes/admin/gmw-gl-admin.php';
                    include_once GMW_GL_PATH . '/includes/admin/gmw-gl-db.php';
                }
                include_once GMW_GL_PATH . '/includes/gmw-gl-component-extension.php';
                
                //include these info window files only when Wordpress doing ajax call
		if ( defined('DOING_AJAX') ) {
			
			include_once GMW_GL_PATH .'/gmaps/includes/gmw-gl-gmaps-info-window-functions.php';
		
		}
        }
        
        /**
         * Do action hooks
         * 
         * @since 1.2
         */
        private function actions() {
            
                // init add-on
		add_filter( 'gmw_plugin_action_links', array( $this, 'addons_action_link' ) );
		add_filter( 'gmw_admin_addons_page', array( $this , 'addon_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_register_scripts' ) );
		add_action( 'gmw_groups_shortcode', array( $this, 'search_functions' ), 10, 2 );
                 
                //global maps functions
                add_action( 'gmw_gmaps_groups_shortcode', array( $this, 'gmaps_search_functions' ), 10, 2 );
        }
        
	/**
	 * add-ons action link
	 *
	 * @access public
	 * @return $addons
	 */
	public function addons_action_link( $links ) {
	
		$links[GMW_GL_LICENSE_NAME] = 'gmw-groups-locator/groups-locator.php';
	
		return $links;
	}

	/**
	 * Include addon details.
	 *
	 * @access public
	 * @return $addons
	 */
	public function addon_init( $addons ) {

		$addons[6] = array(
				'name' 	  => GMW_GL_LICENSE_NAME,
				'title'   => __( 'Groups Locator', 'GMW-GL' ),
				'version' => GMW_GL_VERSION,
				'desc'    => __( 'Add location to groups, display groups members on the map and create Buddypress\'s groups proximity search forms. ', 'GMW-GL'),
				'license' => true,
				'image'	  => false,
				'require' => array(
                                    'GEO my WP'   => array( 'plugin_file' => 'geo-my-wp/geo-my-wp.php', 'link' => 'http://geomywp.com' )
                                ),
		);
		
		return $addons;
	}
       
	/**
	 * Admin functions
	 */
	protected function check_updates() {

		//check license key
		$gmw_license_keys = get_option( 'gmw_license_keys' );
				
		if ( isset( $gmw_license_keys[GMW_GL_LICENSE_NAME] ) && class_exists( 'GMW_Premium_Plugin_Updater' ) ) :

			$license = trim( $gmw_license_keys[GMW_GL_LICENSE_NAME] );
			
			$gmw_updater = new GMW_Premium_Plugin_Updater( GMW_REMOTE_SITE_URL, __FILE__, array(
					'version' 	=> GMW_GL_VERSION,
					'license' 	=> $license, 	// license key (used get_option above to retrieve from DB)
					'item_name'     => 'Groups locator', 	// name of this plugin
					'author' 	=> 'Eyal Fitoussi'  // author of this plugin
			));
					
		endif;
		
	}
        
	/**
	 * Localization
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'GMW-GL', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_register_scripts() {
		
		wp_enqueue_style( 'gmw-gl-style', GMW_GL_URL. '/assets/css/style.css', array(),false,false);
                wp_register_script( 'gmw-gl-map', GMW_GL_URL . '/assets/js/map.js',array(),false,true );
                wp_register_script( 'gmw-gl-group-members-map', GMW_GL_URL . '/assets/js/gl-group-members-map.js',array(),false,true );
                wp_register_script( 'gmw-gl-autocomplete', GMW_GL_URL . '/assets/js/autocomplete.js',array(),false,true );	
	}

        /**
	 * Search functions
	 * @param $form
	 * @param $results
	 */
	public function search_functions( $form, $results ) {
		
		include_once GMW_GL_PATH. '/includes/gmw-gl-component-search-functions.php';
		new GMW_GL_Search_Query( $form, $results );
		
	}
        
        /**
         * incude Gmaps groups Search functions
         * @param $form
         * @param $results
         */
        public function gmaps_search_functions( $form, $results ) {

                include_once GMW_GL_PATH. '/gmaps/includes/gmw-gl-gmaps-query-functions.php';
                include_once GMAPS_PATH. '/includes/gmaps-query-class.php';
                new GMW_Gmaps_Locations_Query( $form, $results );
        }
	
}

//make sure GEO my WP is activated as well as Buddypress's plugin
if ( !class_exists( 'GEO_my_WP') || !class_exists( 'BuddyPress' ) ) {

    if ( !class_exists( 'GEO_my_WP') ) {
        function gmw_gl_gmw_deactivated_admin_notice() {
         ?>
                <div class="error">
                    <p><?php _e( 'Groups Locator requires GEO my WP plugin version 2.4 and up in order to work. Please activate GEO my WP or deactivate Groups Locator', 'GMW-GL' ); ?></p>
                </div>  
        <?php       
        }
    } elseif ( !class_exists( 'BuddyPress' ) ) {
        function gmw_gl_gmw_deactivated_admin_notice() {
         ?>
                <div class="error">
                    <p><?php _e( 'Groups Locator add-on requires Buddypress plugin to be activate in order to work.', 'GMW-GL' ); ?></p>
                </div>  
        <?php       
        } 
    }
    return add_action( 'admin_notices', 'gmw_gl_gmw_deactivated_admin_notice' );
}
            
/**
 *  GMW_Groups_Locator Instance
 *
 * @since 1.2
 * @return GMW_Groups_Locator Instance
 */
function GMW_GL() {
         
        //check for groups component
        if ( !bp_is_active( 'groups' ) ) {
            function gmw_gl_gmw_deactivated_admin_notice() {
             ?>
                    <div class="error">
                        <p><?php _e( 'Groups Locator requires Buddypress\'s Groups component to be active in order to work.', 'GMW-GL' ); ?></p>
                    </div>  
            <?php       
            } 
            return add_action( 'admin_notices', 'gmw_gl_gmw_deactivated_admin_notice' );
        }
        
	return GMW_Groups_Locator::instance();
        
}
add_action( 'bp_loaded', 'GMW_GL' );


