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

/**
 * Setup the default options
 * @since 1.0
 */
function mapo_install()
{
	global $mapo, $wpdb;

	if( ! empty( $wpdb->charset ) )
		$charset_collate =  "DEFAULT CHARACTER SET $wpdb->charset";
	
	$sql = array();

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mapo_coords'" ) ) :
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}mapo_coords (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					group_id int(20) unsigned NOT NULL,
					lat decimal(17, 14) NOT NULL,
					lng decimal(17, 14) NOT NULL,
					KEY group_id (group_id),
					KEY user_id (user_id)
				   ) {$charset_collate};";
	endif;

    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mapo_routes'" ) ) :
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}mapo_routes (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					user_id int(20) unsigned NOT NULL,
					group_id int(20) unsigned NOT NULL,
					name varchar(255) NOT NULL,
					slug varchar(255) NOT NULL,
					description longtext NOT NULL,
					type varchar(255) NOT NULL,
					default_lat decimal(17, 14) NOT NULL,
					default_lng decimal(17, 14) NOT NULL,
					zoom int(20) unsigned NOT NULL,
					start_date date NOT NULL,
					end_date date NOT NULL,
					date_created datetime NOT NULL,
					public tinyint(1) NOT NULL DEFAULT '1',
					is_spam tinyint(1) NOT NULL DEFAULT '0',
					KEY group_id (group_id),
					KEY user_id (user_id)
				   ) {$charset_collate};";
	endif;
			   
    if( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mapo_routes_coords'" ) ) :
		$sql[] = "CREATE TABLE {$wpdb->base_prefix}mapo_routes_coords (
					id int(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
					route_id int(20) unsigned NOT NULL,
					title varchar(255) NOT NULL,
					description longtext NOT NULL,
					image longtext NOT NULL,
					lat decimal(17, 14) NOT NULL,
					lng decimal(17, 14) NOT NULL,
			    	coord_order int(20) NOT NULL DEFAULT '0',
					type varchar(255) NOT NULL DEFAULT 'route'
				   ) {$charset_collate};";
	endif;

	if( count( $sql ) > 0 ) :
		require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );
		
		dbDelta( (array)$sql );
	endif;

	if( ! get_option( 'mapo_options' ) ) :
		$mapo->options = new stdClass;
		$mapo->options->enable_address = false;
		$mapo->options->enable_routes = true;
		$mapo->options->public_location = '';
		$mapo->options->field_id = '';
		$mapo->options->extra_field_ids = '';
		$mapo->options->user_map_type = 'HYBRID';
		$mapo->options->user_map_zoom = 10;
		$mapo->options->group_map_type = 'HYBRID';
		$mapo->options->group_map_zoom = 10;
		$mapo->options->user_overview_type = 'HYBRID';
		$mapo->options->user_overview_zoom = 2;
		$mapo->options->group_overview_type = 'HYBRID';
		$mapo->options->group_overview_zoom = 2;
		$mapo->options->system = 'km';
		$mapo->options->slug = 'maps';
		$mapo->options->enable_no_privacy = false;
		$mapo->options->enable_group = true;
		$mapo->options->enable_post_type = true;
		$mapo->options->enable_post = true;
		$mapo->options->enable_oembed = false;
		$mapo->options->map_location['lat'] = 5;
		$mapo->options->map_location['lng'] = 30;
		$mapo->options->map_lang = '';
		$mapo->options->page_id = false;
		$mapo->options->def_within = 10;
		
		// write to the database
		update_option( 'mapo_options', $mapo->options );
	endif;
	
	// copy theme templates across
	foreach( glob( MAPO_ABSPATH .'templates/theme/*.php' ) as $file )
	{
		$filename = pathinfo( $file, PATHINFO_FILENAME );
		$newfile = STYLESHEETPATH .'/'. $filename .'.php';
		
		if( ! file_exists( $newfile ) )
			copy( $file, $newfile );
	}
	
	// create the location directory
	if( ! get_option( 'mapo_locations_page' ) ) :
		$post_id = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'page',
			'post_author' => 1,
			'post_title' => __( 'Locations', 'mapo' )
		) );
		update_post_meta( $post_id, '_wp_page_template', 'location-directory.php' );
		
		update_option( 'mapo_locations_page', $post_id );
	endif;
	
	// flush the rules for location post type
	flush_rewrite_rules();
	
	update_option( 'mapo_dbversion', $mapo->dbversion );
}

/**
 * Delete all options and database tables
 * @since 1.0
 */
function mapo_uninstall()
{
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}mapo_coords" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}mapo_routes" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->base_prefix}mapo_routes_coords" );

	delete_option( 'mapo_options' );
	
	$post_id  = get_option( 'mapo_locations_page' );
	wp_delete_post( $post_id, true );

	delete_option( 'mapo_locations_page' );
}
?>