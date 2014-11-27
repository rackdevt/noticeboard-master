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

MAPO_Moderation::bootstrap();

class MAPO_Moderation
{
	function bootstrap()
	{
		global $mapo;
		
		if( $mapo->options->enable_routes === false )
			return false;
		
		$slug = 'mapo_routes';
		$label = __( 'Routes', 'mapo' );

		$callbacks = array(
			'info'   => array( __CLASS__, 'info' ),
			'init'   => array( __CLASS__, 'init' ),
			'edit'   => array( __CLASS__, 'edit' ),
			'delete' => array( __CLASS__, 'delete' )
		);

		$activity_types = array( 'new_route', 'edit_route', 'route_comment', 'updated_location' );

		return bpModeration::register_content_type ( $slug, $label, $callbacks, $activity_types );
	}

	function init()
	{
		add_action( 'mapo_end_single_route_action', array( __CLASS__, 'append_link' ), 10, 2 );
	}

	function append_link( $route_id, $user_id )
	{
		$link = bpModFrontend::get_link( array(
			'type' => 'mapo_routes',
			'id' => $route_id,
			'id2' => 0,
			'author_id' => $user_id,
			'unflagged_text' => __( 'Flag this route as inappropriate', 'mapo' )
		));

		echo '<p class="mapo-mod">'. $link .'</p>';
	}

	function info( $id, $id2 )
	{
		global $bp;
		
		$route = new MAPO_Routes( $id );
		
		return array(
			'author' => $route->user_id,
			'url'    => bp_core_get_user_domain( $route->user_id ) . $bp->mapology->slug .'/routes/'. $route->slug .'/',
			'date'   => $route->date_created,
		);
	}

	function edit( $id, $id2 )
	{
		global $bp;
		
		$route = new MAPO_Routes( $id );

		$url = bp_core_get_user_domain( $route->user_id ) . $bp->mapology->slug .'/edit/'. $route->slug .'/';

		return $url;
	}

	function delete( $id, $id2 )
	{
		$route = new MAPO_Routes( $id );
		
		mapo_waypoints_delete_by_route_id( $route->id );
		
		$route->delete();
	}
}
?>