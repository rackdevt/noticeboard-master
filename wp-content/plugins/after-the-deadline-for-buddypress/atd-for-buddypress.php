<?php 
/*
Plugin Name: After The Deadline for BuddyPress
Plugin URI: http://slipfire.com
Description: Adds After The Deadline capabilities to BuddyPress.
Author: <a href="http://www.slipfire.com">SlipFire</a>
Version: 0.2
Author URI: http://slipfire.com
License: GPL2
*/


function atd_for_buddypress_init() {
	require( dirname( __FILE__ ) . '/atd-for-buddypress-functions.php' );
}
add_action( 'bp_init', 'atd_for_buddypress_init' );
?>