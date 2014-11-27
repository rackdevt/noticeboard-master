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

function mapo_settings_processor()
{
	global $mapo, $wpdb, $bp_oembed, $bp;

	// replot the user coordinates
	if( isset( $_POST['replot_coords'] ) )
	{
		check_admin_referer( 'mapo_settings' );
		
		if( ! is_numeric( $_POST['start_limit'] ) || ! is_numeric( $_POST['end_limit'] ) )
		{
			MAPO_Admin_Loader::add_message( __( 'Start and end have to be specified', 'mapo' ), 'error' );
			bp_core_redirect( admin_url( 'admin.php?page='. MAPO_FOLDER ) );
		}
		
		$ids = array();
		if( ! empty( $mapo->options->extra_field_ids ) )
			$ids = $mapo->options->extra_field_ids;
		
		array_unshift( $ids, $mapo->options->field_id );
		
		$fids = $wpdb->escape( join( ',', $ids ) );
		
		$nc_sql = '';
		if( isset( $_POST['nocoords_yet'] ) )
		{
			$has_loc = $wpdb->get_col( "SELECT user_id FROM {$mapo->tables->coords}" );
			$has_loc_string = $wpdb->escape( join( ',', $has_loc ) );
			
			$nc_sql = " WHERE ID NOT IN ({$has_loc_string})";
		}

		$user_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} {$nc_sql} LIMIT %d, %d", (int)$_POST['start_limit'], (int)$_POST['end_limit'] ) );

		$uids = $wpdb->escape( join( ',', (array)$user_ids ) );

		$locations = $wpdb->get_results( "SELECT * FROM {$bp->profile->table_name_data} WHERE user_id IN ({$uids}) AND field_id IN ({$fids})" );

		foreach( (array)$user_ids as $user_id )
		{
			foreach( (array)$locations as $val )
			{
				if( $val->user_id == $user_id )
					$locs[$user_id][] = $val->value;
			}
		}
		
		foreach( (array)$locs as $id => $data )
		{
			$location = implode( ', ', (array)$data );
			
			if( isset( $_POST['nocoords_yet'] ) )
			{
				if( in_array( $id, (array)$has_loc ) )
					continue;
			}

			$result = mapo_save_user_coordinates( $id, $location );

			if( $result == 'OVER_QUERY_LIMIT' )
			{
				MAPO_Admin_Loader::add_message( __( 'Over daily query limit on Google.', 'mapo' ), 'error' );
				bp_core_redirect( admin_url( 'admin.php?page='. MAPO_FOLDER ) );
			}				
		}
		
		MAPO_Admin_Loader::add_message( __( 'Coordinates updated successfully.', 'mapo' ) );
		
		bp_core_redirect( admin_url( 'admin.php?page='. MAPO_FOLDER ) );
	}
	
	// update the options
	if( isset( $_POST['mapooption'] ) )
	{	
		check_admin_referer( 'mapo_settings' );
		
		$error = false;
	
		// proceed if there is no error
		if( ! $error )
		{
			if( $_POST['page_options'] )	
				$options = explode( ',', stripslashes( $_POST['page_options'] ) );
				
			if( $options )
			{
				foreach( $options as $option )
				{
					$option = trim( $option );

					if( is_array( $_POST[$option] ) )
						$value = $_POST[$option];
					else
					{
						$value = trim( $_POST[$option] );
						
						if( in_array( $option, array( 'enable_no_privacy', 'enable_address', 'enable_group', 'enable_post_type', 'enable_post', 'enable_oembed', 'enable_routes', 'enhanced_map' ) ) )
							$value = (bool)$value;
					}
	
					if( $option == 'slug' )
						$value = sanitize_title_with_dashes( $value );							

					if( $option == 'page_id' )
					{
						$blog_page_ids = bp_get_option( 'bp-pages' );
						
						$page = get_post( $value );
						
						$blog_page_ids[$bp->mapology->slug] = $page->ID;

						bp_update_option( 'bp-pages', $blog_page_ids );
					}
						
					// delete the locations page if custom post type gets disabled
					if( $option == 'enable_post_type' && $value == false && $mapo->options->enable_post_type == true )
					{
						$post_id  = get_blog_option( BP_ROOT_BLOG, 'mapo_locations_page' );
						wp_delete_post( $post_id, true );
					
						delete_blog_option( BP_ROOT_BLOG, 'mapo_locations_page' );
					}
					// create the locations page if custom post type gets enabled
					elseif( $option == 'enable_post_type' && $value == true && $mapo->options->enable_post_type == false )
					{
						$post_id = wp_insert_post( array(
							'post_status' => 'publish',
							'post_type' => 'page',
							'post_author' => 1,
							'post_title' => __( 'Locations', 'mapo' )
						) );
						update_post_meta( $post_id, '_wp_page_template', 'location-directory.php' );
						update_blog_option( BP_ROOT_BLOG, 'mapo_locations_page', $post_id );
						
						flush_rewrite_rules();
					}
					
					$mapo->options->{$option} = $value;
	
					// set oembed to false if plugin is not activated
					if( $option == 'enable_oembed' )
					{
						if( ! isset( $bp_oembed ) )
							$mapo->options->enable_oembed = false;
					}
				}
			}
			
			// Save options
			update_blog_option( BP_ROOT_BLOG, 'mapo_options', $mapo->options );

			MAPO_Admin_Loader::add_message( __( 'Update Successfully', 'mapo' ) );

			bp_core_redirect( admin_url( 'admin.php?page='. MAPO_FOLDER ) );
		}
	}
}