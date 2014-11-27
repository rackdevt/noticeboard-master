<?php
/*
Plugin Name: Mapology
Plugin URI: http://shabushabu.eu/
Description: Add google maps for BuddyPress groups and members 
Author: ShabuShabu
Version: 1.3.6
Author URI: http://shabushabu.eu/
Network: true

Copyright 2010 by ShabuShabu Webdesign

****************************************************************************

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************
*/

class MAPO_Loader
{
	/**
	 * The plugin version
	 */
	public $version = '1.3.6';

	/**
	 * The db version
	 */
	public $dbversion = '1.3.2';
	
	/**
	 * Minimum required WP version
	 */
	public $min_wp = '3.2.1';
	
	/**
	 * Minimum required BP version
	 */
	public $min_bp = '1.5';
	
	/**
	 * Plugin creator link
	 */
	public $home_url = 'http://shabushabu.eu/';
	
	/**
	 * Name of the plugin folder
	 */
	public $plugin_name;
	
	/**
	 * Holds the admin page
	 */
	public $admin;
	
	/**
	 * All our options
	 */
	public $options;

	/**
	 * Holds our DB tables
	 */
	public $tables;

	/**
	 * Needs to be set to true after plugin checks
	 * for the plugin to load everything. If it
	 * stays false, the plugin will not load
	 */
	static $active = false;

	/**
	 * PHP4 constructor
	 * @since 1.0
	 */
	public function mapo_loader()
	{
		$this->__construct();
	}
	
	/**
	 * PHP5 constructor
	 * @since 1.0
	 */
	public function __construct()
	{
		$this->plugin_name = plugin_basename( __FILE__ );

		add_action( 'plugins_loaded', array( $this, 'constants' 		  ),  0 );
		add_action( 'plugins_loaded', array( $this, 'check_requirements'  ),  1	);
		add_action( 'plugins_loaded', array( $this, 'translate' 		  ),  1 );
		add_action( 'plugins_loaded', array( $this, 'globals' 			  ),  2 );
		add_action( 'bp_include',  	  array( $this, 'dependencies' 	  	  ), 10 );
		add_action( 'bp_include', 	  array( $this, 'start' 			  ), 10 );
		add_action( 'bp_init', 		  array( $this, 'start_mod' 		  ), 20 );

		// activate and uninstall hooks
		register_activation_hook( $this->plugin_name, array( __CLASS__, 'activate' ) );
		register_uninstall_hook( $this->plugin_name, array( __CLASS__, 'uninstall' ) );

	}

	/**
	 * Load all BP related files
	 * @since 1.0
	 */
	public function start()
	{
		if( self::$active === false )
			return false;

		if( ! defined( 'MAPOLOGY_SLUG' ) )
			define( 'MAPOLOGY_SLUG', ( empty( $this->options->slug ) ? 'maps' : $this->options->slug ) );
		
		// core files
		require( MAPO_ABSPATH .'core/mapo-core.php');
	}

	/**
	 * Load the moderation file
	 * @since 1.0
	 */
	public function start_mod()
	{
		if( self::$active === false )
			return false;

		if( class_exists( 'bpModeration' ) )
			require( MAPO_ABSPATH .'core/mapo-moderation.php' );
	}

	/**
	 * Check for required wp version
	 * @since 1.0
	 */
	public function check_requirements()
	{		
		global $wp_version, $bp;
		
		$error = false;

		if( ! defined( 'BP_VERSION' ) )
		{
			add_action( 'admin_notices', create_function( '', 'global $mapo; printf(\'<div id="message" class="error"><p><strong>\' . __(\'Mapology needs BuddyPress to be installed. <a href="%s">Install it now!</a>!\', "mapo" ) . \'</strong></p></div>\', admin_url( \'plugin-install.php\' ) );' ) );
			$error = true;
		}
		elseif( ! empty( $bp->maintenance_mode ) )
		{
			add_action( 'admin_notices', create_function( '', 'global $mapo; echo \'<div id="message" class="error"><p><strong>\' . __(\'BuddyPress is in maintenance mode right now. Mapology will be available again soon!\', "mapo" ) . \'</strong></p></div>\';' ) );
			$error = true;
		}
		elseif( version_compare( BP_VERSION, $this->min_bp, '>=' ) == false )
		{
			add_action( 'admin_notices', create_function( '', 'global $mapo; printf(\'<div id="message" class="error"><p><strong>\' . __(\'Mapology works only under BuddyPress %s or higher. <a href="%supdate-core.php">Upgrade now</a>!\', "mapo" ) . \'</strong></p></div>\', $mapo->min_bp, admin_url() );'	) );
			$error = true;
		}
		
		if( version_compare( $wp_version, $this->min_wp, '>=' ) == false )
		{
			add_action( 'admin_notices', create_function( '', 'global $mapo; printf(\'<div id="message" class="error"><p><strong>\' . __(\'Mapology works only under WordPress %s or higher. <a href="%supdate-core.php">Upgrade now</a>!\', "mapo" ) . \'</strong></p></div>\', $mapo->min_wp, admin_url() );' ) );
			$error = true;
		}
		
		self::$active = ( ! $error ) ? true : false;
	}


	/**
	 * Load the languages
	 * @since 1.0
	 */
	public function translate()
	{
		if( file_exists( MAPO_ABSPATH . 'languages/mapo-' . get_locale() . '.mo' ) )
			load_textdomain( 'mapo', MAPO_ABSPATH . 'languages/mapo-' . get_locale() . '.mo' );
	}

	/**
	 * Declare our options
	 * @since 1.0
	 */
	public function globals()
	{
		global $wpdb;
		
		$this->tables = new stdClass;
		$this->tables->coords = $wpdb->base_prefix . 'mapo_coords';
		$this->tables->routes = $wpdb->base_prefix . 'mapo_routes';
		$this->tables->routes_coords = $wpdb->base_prefix . 'mapo_routes_coords';

		if( $options = get_blog_option( BP_ROOT_BLOG, 'mapo_options' ) )
		{
			$this->options = new stdClass;
			foreach( $options as $key => $var )
				$this->options->{$key} = $var;
		}
		
		$this->config = new stdClass;
		$this->config->prox_options = apply_filters( 'mapo_prox_options', array(
			5, 10, 25, 50, 75, 100, 125, 150, 200, 250, 500, 750
		) );
	}
	
	/**
	 * Include all dependent files
	 * @since 1.0
	 */
	public function dependencies()
	{
		if( self::$active === false )
			return false;

		if( is_admin() )
		{
			require_once( dirname( __FILE__ ) . '/admin/mapo-admin.php');
			$this->admin = new MAPO_Admin_Loader();
		}
	}
	
	/**
	 * Declare all constants
	 * @since 1.0
	 */
	public function constants()
	{
		if( ! defined( 'MAPO_SHOW_USERS'  ) ) define( 'MAPO_SHOW_USERS',  true );
		if( ! defined( 'MAPO_SHOW_GROUPS' ) ) define( 'MAPO_SHOW_GROUPS', true );
		if( ! defined( 'MAPO_SHOW_EVENTS' ) ) define( 'MAPO_SHOW_EVENTS', true );
		
		define( 'MAPO_PLUGIN', 	  $this->plugin_name );
		define( 'MAPO_VERSION',   $this->version );
		define( 'MAPO_DBVERSION', $this->dbversion );
		define( 'MAPO_FOLDER', 	  plugin_basename( dirname( __FILE__ ) ) );
		define( 'MAPO_ABSPATH',   trailingslashit( str_replace("\\","/", WP_PLUGIN_DIR . '/' . MAPO_FOLDER ) ) );
		define( 'MAPO_URLPATH',   trailingslashit( plugins_url( $path = '/'. MAPO_FOLDER ) ) );
	}
	
	/**
	 * Activate the plugin
	 * @since 1.0
	 */
	public static function activate()
	{
		include_once( dirname( __FILE__ ) .'/admin/mapo-install.php' );
		mapo_install();

		// delete the upgrade nag
		delete_option( 'mapology_update_exists' );
	}

	/**
	 * Delete all options
	 * @since 1.0
	 */
	public static function uninstall()
	{
		include_once( dirname( __FILE__ ) .'/admin/mapo-install.php' );
		mapo_uninstall();
	}
}
// get the show on the road
$mapo = new MAPO_Loader();
global $mapo;
?>