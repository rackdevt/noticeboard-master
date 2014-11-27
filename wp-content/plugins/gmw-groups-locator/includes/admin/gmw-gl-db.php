<?php
//create or update database
if ( get_option( "gmw_gl_db_version" ) == '' || get_option( "gmw_gl_db_version" ) != GMW_GL_DB_VERSION ) {
    
    if ( get_option( "gmw_gl_db_version" ) < 1.4 ) {
        
        global $wpdb;
        
        $glTable = $wpdb->get_results( "SHOW TABLES LIKE 'gmw_groups_locator'", ARRAY_A );

        if ( count($glTable) == 0 ) {
                gmw_gl_db_installation();

        } elseif ( count($glTable) == 1 ) {
                gmw_gl_rename_db();
        }
        update_option( "gmw_gl_db_version", GMW_GL_DB_VERSION );
        
    } else {
        
        global $wpdb;
        
        $glTable = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}gmw_groups_locator'", ARRAY_A );

        if ( count($glTable) == 0 ) {
                gmw_gl_db_installation();

        } elseif ( count($glTable) == 1 ) {
                gmw_gl_update_db();
        }
        update_option( "gmw_gl_db_version", GMW_GL_DB_VERSION );
        
    }
    

}
                
if ( !defined( 'ABSPATH' ) ) exit;

function gmw_gl_db_installation() {
    echo 'create';
	global $wpdb;
	$gmw_groups_sql = "CREATE TABLE {$wpdb->prefix}gmw_groups_locator (
  		`id` 				BIGINT(30) NOT NULL,
  		`lat` 				FLOAT(10,6) NOT NULL,
  		`lng` 				FLOAT(10,6) NOT NULL,
  		`street`			VARCHAR(128) NOT NULL,
  		`apt`				VARCHAR(50) NOT NULL,
  		`city` 				VARCHAR(128) NOT NULL,
  		`state` 			VARCHAR(5) NOT NULL,
  		`state_long`                    VARCHAR(128) NOT NULL,
  		`zipcode` 			VARCHAR(40) NOT NULL,
  		`country` 			VARCHAR(10) NOT NULL,
  		`country_long`                  VARCHAR(128) NOT NULL,
  		`address` 			VARCHAR(255) NOT NULL,
  		`formatted_address`             VARCHAR(255) NOT NULL,
  		`map_icon` 			VARCHAR(50) NOT NULL,
  		UNIQUE KEY id (id)
  		
	)	DEFAULT CHARSET=utf8;";

   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	dbDelta($gmw_groups_sql);
}

function gmw_gl_rename_db() {
    echo 'rename';
    global $wpdb;
    $wpdb->get_results("RENAME TABLE `gmw_groups_locator` TO `{$wpdb->prefix}gmw_groups_locator`");
}

function gmw_gl_update_db() {
    echo 'update';
   return;
}