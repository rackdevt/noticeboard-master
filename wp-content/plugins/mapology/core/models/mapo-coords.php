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

if( ! class_exists( 'MAPO_Coords' ) )
{
	class MAPO_Coords
	{
		var $id;
		var $user_id;
		var $group_id;
		var $lat;
		var $lng;
		
		/**
		 * PHP5 Constructor
		 * @since 1.0
		 */
		function __construct( $id = null, $user_id = null, $group_id = null )
		{
			global $mapo, $wpdb;
	
			if( $id )
			{
				$this->id = $id;
				$this->populate();
			}
			elseif( $user_id )
			{
				$this->user_id = $user_id;
				$this->populate_by_user_id();
			}
			elseif( $group_id )
			{
				$this->group_id = $group_id;
				$this->populate_by_group_id();
			}
		}
	
		/**
		 * Get a row from the database
		 * @since 1.0
		 */
		function populate()
		{
			global $mapo, $wpdb;
			
			$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->coords} WHERE id = %d", $this->id ) );
	
			$this->user_id	= $table->user_id;
			$this->group_id	= $table->group_id;
			$this->lat	 	= $table->lat;
			$this->lng		= $table->lng;
		}
	
		/**
		 * Get a row from the database
		 * @since 1.0
		 */
		function populate_by_user_id()
		{
			global $mapo, $wpdb;
			
			$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->coords} WHERE user_id = %d", $this->user_id ) );
	
			$this->id		= $table->id;
			$this->group_id	= $table->group_id;
			$this->lat		= $table->lat;
			$this->lng		= $table->lng;
		}
	
		/**
		 * Get a row from the database
		 * @since 1.0
		 */
		function populate_by_group_id()
		{
			global $mapo, $wpdb;
			
			$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->coords} WHERE group_id = %d", $this->group_id ) );
	
			$this->id		= $table->id;
			$this->user_id	= $table->user_id;
			$this->lat		= $table->lat;
			$this->lng		= $table->lng;
		}
	
		/**
		 * Save or uptimestamp a row
		 * @since 1.0
		 */
		function save()
		{
			global $wpdb, $mapo;
			
			$this->user_id	= apply_filters( 'mapo_before_save_coords_user_id', $this->user_id, $this->id );
			$this->group_id	= apply_filters( 'mapo_before_save_coords_group_id', $this->group_id, $this->id );
			$this->lat	 	= apply_filters( 'mapo_before_save_coords_lat', $this->lat, $this->id );
			$this->lng		= apply_filters( 'mapo_before_save_coords_lng', $this->lng, $this->id );
			
			/* Call a before save action here */
			do_action( 'mapo_coords_before_save', $this );
							
			if( $this->id )
			{
				$result = $wpdb->query( $wpdb->prepare( "UPDATE {$mapo->tables->coords} SET
														user_id = %d,
														group_id = %d,
														lat = %s,
														lng = %s
												WHERE id = %d",
														$this->user_id,
														$this->group_id,
														$this->lat,
														$this->lng,
														$this->id ) );
			}
			else
			{
				$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$mapo->tables->coords} (
														user_id,
														group_id,
														lat,
														lng
												) VALUES ( 
														%d, %d, %s, %s
												)",
														$this->user_id,
														$this->group_id,
														$this->lat,
														$this->lng ) );
			}
					
			if( ! $result )
				return false;
			
			if( ! $this->id )
				$this->id = $wpdb->insert_id;
			
			/* Add an after save action here */
			do_action( 'mapo_coords_after_save', $this ); 
			
			return $this->id;
		}
		
		/**
		 * Delete a row
		 * @since 1.0
		 */
		function delete()
		{
			global $wpdb, $mapo;
			
			return $wpdb->query( $wpdb->prepare( "DELETE FROM {$mapo->tables->coords} WHERE id = %d", $this->id ) );
		}
	
		/**
		 * Get an id
		 * @since 1.0
		 */
		function get_id_by_user( $user_id )
		{
			global $wpdb, $mapo;
			
			return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$mapo->tables->coords} WHERE user_id = %d", $user_id ) );
		}
	
		/**
		 * Get an id
		 * @since 1.0
		 */
		function get_id_by_group( $group_id )
		{
			global $wpdb, $mapo;
			
			return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$mapo->tables->coords} WHERE group_id = %d", $group_id ) );
		}
	}
}
?>