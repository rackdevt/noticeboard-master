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
 * Display the upgrade page
 * @since 1.1.2
 */
function mapo_upgrade()
{
	global $mapo, $wpdb, $bp;
	
	$version = get_blog_option( BP_ROOT_BLOG, 'mapo_dbversion' );
	
	if( ! $version ) $version = '1.0';
	
	$updated = false;

	if( ! empty( $wpdb->charset ) )
		$charset_collate =  "DEFAULT CHARACTER SET $wpdb->charset";
		
	echo '<div id="mapo-page" class="wrap"><h2>'. __( 'Upgrade', 'mapo' ) .'</h2><div id="mapo-content">';
	
	// Upgrade to database version 1.1
	if( version_compare( $version, '1.1', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.1' );
		$wpdb->show_errors();
		
		// update the options
		$mapo->options->map_location['lat'] = 5;
		$mapo->options->map_location['lng'] = 30;
		$mapo->options->map_lang = '';
		
		// save to the database
		update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );
		
		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.1

	// Upgrade to database version 1.2
	if( version_compare( $version, '1.2', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.2' );
		$wpdb->show_errors();
		
		// update the options
		$mapo->options->user_overview_type = 'HYBRID';
		$mapo->options->user_overview_zoom = 2;
		$mapo->options->group_overview_type = 'HYBRID';
		$mapo->options->group_overview_zoom = 2;
		
		// save to the database
		update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );
		
		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.2

	// Upgrade to database version 1.2.1
	if( version_compare( $version, '1.2.1', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.2.1' );
		$wpdb->show_errors();
		
		mapo_maybe_add_column( $mapo->tables->routes_coords, 'type', "varchar(255) NOT NULL DEFAULT 'route' AFTER coord_order" );
		
		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.2.1

	// Upgrade to database version 1.3
	if( version_compare( $version, '1.3', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.3' );
		$wpdb->show_errors();

		$mapo->options->page_id = false;
		
		// write to the database
		update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );

		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.2.1

	// Upgrade to database version 1.3.1
	if( version_compare( $version, '1.3.1', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.3.1' );
		$wpdb->show_errors();

		$mapo->options->enhanced_map = false;
		$mapo->options->public_location = '';
		$mapo->options->def_within = 10;
		
		// write to the database
		update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );

		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.3.1
	
	// Upgrade to database version 1.3.2
	if( version_compare( $version, '1.3.2', '<' ) )
	{
		printf( __( 'Upgrading database structure for DB %s...', 'mapo' ), 'v1.3.2' );
		$wpdb->show_errors();

		$mapo->options->enable_no_privacy = false;
		
		// write to the database
		update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );

		// Update the db version
		update_blog_option( BP_ROOT_BLOG, 'mapo_dbversion', MAPO_DBVERSION );
		
		echo __( 'Done! Please refresh this page.', 'mapo' ) . "<br />\n";
		$wpdb->hide_errors();
		
		$updated = true;
	} // END UPGRADE TO 1.3.3

	if( ! $updated )
    	echo __( 'Upgrade failed!', 'mapo' );
		
	echo '</div></div>';
    return;
}

/**
 * Check for a db column
 * THX to NextGEN Gallery
 * @since 1.1
 */
function mapo_maybe_add_column( $table_name, $column_name, $create_ddl )
{
	global $wpdb;
	
	foreach( $wpdb->get_col( "SHOW COLUMNS FROM {$table_name}" ) as $column )
	{
		if( $column == $column_name )
			return true;
	}
	
	$wpdb->query( "ALTER TABLE {$table_name} ADD {$column_name} " . $create_ddl );
	
	foreach( $wpdb->get_col( "SHOW COLUMNS FROM {$table_name}" ) as $column )
	{
		if( $column == $column_name )
			return true;
	}
	
	echo sprintf( __( 'Could not add column %s in table %s<br />', 'mapo' ), $column_name, $table_name ) ."\n";
	return false;
}

?>