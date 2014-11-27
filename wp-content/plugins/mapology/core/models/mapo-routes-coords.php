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

class MAPO_Routes_Coords
{
	var $id;
	var $route_id;
	var $title;
	var $description;
	var $image;
	var $lat;
	var $lng;
	var $coord_order;
	var $type;
	
	/**
	 * PHP5 Constructor
	 * @since 1.0
	 */
	function __construct( $id = null, $route_id = null, $coord_order = null )
	{
		global $mapo, $wpdb;

		if( $id )
		{
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Get a row from the database
	 * @since 1.0
	 */
	function populate()
	{
		global $mapo, $wpdb;
		
		$table = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mapo->tables->routes_coords} WHERE id = %d", $this->id ) );

		$this->route_id		= $table->route_id;
		$this->title		= $table->title;
		$this->description	= $table->description;
		$this->image		= $table->image;
		$this->lat	 		= $table->lat;
		$this->lng			= $table->lng;
		$this->coord_order	= $table->coord_order;
		$this->type			= $table->type;
	}

	/**
	 * Save or uptimestamp a row
	 * @since 1.0
	 */
	function save()
	{
		global $wpdb, $mapo;
		
		$this->route_id		= apply_filters( 'mapo_before_save_routes_coords_route_id', $this->route_id, $this->id );
		$this->title		= apply_filters( 'mapo_before_save_routes_coords_title', $this->title, $this->id );
		$this->description	= apply_filters( 'mapo_before_save_routes_coords_description', $this->description, $this->id );
		$this->image		= apply_filters( 'mapo_before_save_routes_coords_route_id', $this->image, $this->id );
		$this->lat	 		= apply_filters( 'mapo_before_save_routes_coords_lat', $this->lat, $this->id );
		$this->lng			= apply_filters( 'mapo_before_save_routes_coords_lng', $this->lng, $this->id );
		$this->coord_order	= apply_filters( 'mapo_before_save_routes_coords_coord_order', $this->coord_order, $this->id );
		$this->type			= apply_filters( 'mapo_before_save_routes_coords_type', $this->type, $this->id );
		
		/* Call a before save action here */
		do_action( 'mapo_routes_coords_before_save', $this );
						
		if( $this->id )
		{
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$mapo->tables->routes_coords} SET
													route_id = %d,
													title = %s,
													description = %s,
													image = %s,
													lat = %s,
													lng = %s,
													coord_order = %d,
													type = %s
											WHERE id = %d",
													$this->route_id,
													$this->title,
													$this->description,
													$this->image,
													$this->lat,
													$this->lng,
													$this->coord_order,
													$this->type,
													$this->id ) );
		}
		else
		{
			$result = $wpdb->query( $wpdb->prepare( "INSERT INTO {$mapo->tables->routes_coords} (
													route_id,
													title,
													description,
													image,
													lat,
													lng,
													coord_order,
													type
											) VALUES ( 
													%d, %s, %s, %s, %s, %s, %d, %s
											)",
													$this->route_id,
													$this->title,
													$this->description,
													$this->image,
													$this->lat,
													$this->lng,
													$this->coord_order,
													$this->type ) );
		}
				
		if( ! $result )
			return false;
		
		if( ! $this->id )
			$this->id = $wpdb->insert_id;
		
		/* Add an after save action here */
		do_action( 'mapo_routes_coords_after_save', $this ); 
		
		return $this->id;
	}
	
	/**
	 * Delete a row
	 * @since 1.0
	 */
	function delete()
	{
		global $wpdb, $mapo;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$mapo->tables->routes_coords} WHERE id = %d", $this->id ) );
	}

	/**
	 * Delete a row
	 * @since 1.0
	 */
	function delete_by_route_id( $route_id, $type = 'route' )
	{
		global $wpdb, $mapo;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$mapo->tables->routes_coords} WHERE route_id = %d AND type = %s", $route_id, $type ) );
	}
}
?>