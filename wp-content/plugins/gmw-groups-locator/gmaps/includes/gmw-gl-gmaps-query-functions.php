<?php 
/**
 * GMPAS function - query members based on distance
 * @param unknown_type $form
 */
function gmaps_gl_distance_query( $form ) {

	global $wpdb;
	
	$byDistance = ( !empty( $form['general_settings']['radius'] ) ) ? $wpdb->prepare( 'HAVING distance <= %d OR distance IS NULL ', $form['general_settings']['radius'] ) : '';
		
	$form['results'] = $wpdb->get_results(
			$wpdb->prepare("
					SELECT gmwGroups.*, 
					ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gmwGroups.lat ) ) * cos( radians( gmwGroups.lng ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gmwGroups.lat) ) ),1 ) AS distance
                                        FROM `{$wpdb->prefix}gmw_groups_locator` gmwGroups GROUP BY gmwGroups.id {$byDistance}",
					$form['units_array']['radius'], $form['your_lat'], $form['your_lng'], $form['your_lat']),
			ARRAY_A );
	
	return $form;
	
}
add_filter( 'gmaps_gmaps_groups_distance_query', 'gmaps_gl_distance_query' );

/**
 * GMAPS function - query members no distance
 * @param unknown_type $form
 */
function gmaps_gl_no_distance_query( $form ) {

	global $wpdb;

	$form['results'] = $wpdb->get_results("SELECT gmwGroups.* FROM {$wpdb->prefix}gmw_groups_locator gmwGroups GROUP BY gmwGroups.id", ARRAY_A );

	return $form;

}
add_filter( 'gmaps_gmaps_groups_no_distance_query', 'gmaps_gl_no_distance_query' );

/**
 * GMAPS fucntion - Include stylesheets
 * @param unknown_type $form
 */
function gmaps_gl_styles( $form ) {

	$iwType = ( $form['info_window']['iw_type']  == 'infobox' ) ? 'infobox' :'popup';
	
	if( strpos( $form['info_window'][$iwType.'_template'], 'custom_' ) !== false ) :			
		wp_enqueue_style( 'gmaps-gl-'.$form['ID'].'-'.$iwType.'-style', get_stylesheet_directory_uri(). '/geo-my-wp/groups/info-window-templates/'.$iwType.'/'.str_replace('custom_','', $form['info_window'][$iwType.'_template'] ).'/css/style.css' );		
	//get stylesheet and results template from plugin's folder
	else :
		wp_enqueue_style( 'gmaps-gl-'.$form['ID'].'-'.$iwType.'-style', GMW_GL_URL. '/gmaps/templates/'.$iwType.'/'.$form['info_window'][$iwType.'_template'].'/css/style.css' );
	endif;

}
add_action( 'gmaps_gmaps_groups_after_query' , 'gmaps_gl_styles' );