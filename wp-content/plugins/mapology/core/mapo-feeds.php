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
* Auto discover event feeds
* @since 1.0
*/
function mapo_add_feed_to_head()
{
	if( bp_is_group() && mapo_is_group_enabled() ) : ?>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Latest Group Routes', 'mapo' ) ?>" href="<?php mapo_group_routes_feed_link() ?>" />
    <?php endif;
	
	if( ! empty( $bp->displayed_user->id ) ) : ?>
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php  echo bp_core_get_user_displayname( $bp->displayed_user->id ) ?> | <?php _e( 'Latest User Routes', 'mapo' ) ?>" href="<?php mapo_user_routes_feed_link() ?>" />
    <?php endif; ?>

    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e( 'Latest Sitewide Events', 'events' ) ?>" href="<?php mapo_sitewide_routes_feed_link() ?>" />
    <?php	
}
add_action( 'wp_head', 'mapo_add_feed_to_head' );

/**
* Setup the global routes feed
* @since 1.0
*/
function mapo_global_routes_feed()
{
	global $bp, $wp_query;

	if( ! bp_is_current_component( $bp->mapology->slug ) || ! bp_is_current_action( 'feed' ) || $bp->displayed_user->id || $bp->groups->current_group )
		return false;

	$wp_query->is_404 = false;
	status_header( 200 );

	include_once( MAPO_ABSPATH .'core/feeds/mapo-global.php' );
	die;
}
add_action( 'wp', 'mapo_global_routes_feed', 0 );

/**
* Setup the group routes feed
* @since 1.0
*/
function mapo_group_routes_feed()
{
	global $bp, $wp_query;
	
	if( ! mapo_is_group_enabled() )
		return false;
	
	if( ! bp_is_action_variable( 'feed', 0 ) )
		return false;

	if( $bp->current_component == $bp->groups->slug && bp_is_current_action( $bp->mapology->slug ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		include_once( MAPO_ABSPATH .'core/feeds/mapo-group.php' );
		die;
	}
}
add_action( 'wp', 'mapo_group_routes_feed', 0 );

/**
* Setup the user events feed
* @since 1.0
*/
function mapo_user_routes_feed()
{
	global $bp, $wp_query;
	
	if( ! bp_is_current_action( 'feed' ) )
		return false;

	if( bp_is_current_component( $bp->mapology->slug ) && ! empty( $bp->displayed_user->id ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
	
		include_once( MAPO_ABSPATH .'core/feeds/mapo-user.php' );
		die;
	}
}
add_action( 'wp', 'mapo_user_routes_feed', 0 );

/**
 * Sitewide feed link
 * @since 1.0
 */
function mapo_sitewide_routes_feed_link()
{
	echo mapo_get_sitewide_routes_feed_link();
}
	function mapo_get_sitewide_routes_feed_link()
	{
		global $bp;

		return apply_filters( 'mapo_get_sitewide_routes_feed_link', site_url( $bp->mapology->slug . '/feed/' ) );
	}

/**
 * USer feed link
 * @since 1.0
 */
function mapo_user_routes_feed_link()
{
	echo mapo_get_user_routes_feed_link();
}
	function mapo_get_user_routes_feed_link()
	{
		global $bp;

		return apply_filters( 'mapo_get_user_routes_feed_link', $bp->displayed_user->domain . $bp->mapology->slug .'/feed/' );
	}

/**
 * Group feed link
 * @since 1.0
 */
function mapo_group_routes_feed_link()
{
	echo mapo_get_group_routes_feed_link();
}
	function mapo_get_group_routes_feed_link()
	{
		global $bp;

		return apply_filters( 'mapo_get_group_routes_feed_link', bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/feed/' );
	}
?>