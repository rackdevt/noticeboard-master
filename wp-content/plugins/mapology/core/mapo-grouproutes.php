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

class MAPO_Group_Routes extends BP_Group_Extension
{	
	function __construct()
	{
		global $bp;
		
		$this->name = __( 'Maps', 'mapo' );
		$this->slug = $bp->mapology->slug;
		
		$this->nav_item_position = 50;
		$this->enable_create_step  = false;
		$this->enable_edit_item = false;
	}

	function display()
	{
		global $bp;
		
		if( bp_is_action_variable( 'overview', 0 ) )
			mapo_load_template( 'maps/group/overview' );
		else
			mapo_load_template( 'maps/group/index' );
	}
}
bp_register_group_extension( 'MAPO_Group_Routes' );

/*
* Remove a route from a group
* @since 1.0
*/
function mapo_remove_route_from_group()
{
	global $bp;
	
	if( bp_is_current_action( 'routes' ) && bp_is_action_variable( 'remove', 0 ) && is_numeric( $bp->action_variables[1] ) )
	{
		check_admin_referer( 'mapo_remove_group' );
		
		mapo_remove_group_id( (int)$bp->action_variables[1] );

		bp_core_add_message( __( 'Route has been successfully removed.', 'mapo' ) );
		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/' );
	}
}
add_action( 'wp', 'mapo_remove_route_from_group', 2 );
?>