<?php
/**
 * groups Locator GMAPS function - get information of the displayed group
 * @param unknown_type $gmw
 * @param unknown_type $member_info
 */
function gmaps_gmgl_load_template( $gmw, $group_info ) {

	do_action( 'gmaps_groups_before_info_window_query', $gmw, $group_info );
		
	/**
	 * get the post being displayed from the database
	 */
	global $wpdb;
	$group = $wpdb->get_row( $wpdb->prepare("
			SELECT bpGroup.id as ID, bpGroup.*, gmwGroup.*
                        FROM `{$wpdb->prefix}bp_groups` bpGroup INNER JOIN `{$wpdb->prefix}gmw_groups_locator` gmwGroup ON bpGroup.id = gmwGroup.id
			WHERE bpGroup.id = %d",
			$group_info['id'] ) );
	
	$group->distance = ( isset( $group_info['distance'] ) && !empty( $group_info['distance'] ) ) ? $group_info['distance'] : false;
	
	do_action( 'gmaps_groups_after_info_window_query', $gmw, $group );
	$group = apply_filters( 'gmaps_groups_member_after_info_window_query', $group, $gmw );
	
	$iwType = ( $gmw['info_window']['iw_type']  == 'infobox' ) ? 'infobox' :'popup';
	
	/**
	 * load info window template file
	 * @var unknown_type
	 */
	if( strpos( $gmw['info_window'][$iwType.'_template'], 'custom_' ) !== false ) :
		include( STYLESHEETPATH. '/geo-my-wp/groups/info-window-templates/'.$iwType.'/'.str_replace( 'custom_','',$gmw['info_window'][$iwType.'_template'] ).'/content.php' );
	//get stylesheet and results template from plugin's folder
	else :
		include GMW_GL_PATH . '/gmaps/templates/'.$iwType.'/'.$gmw['info_window'][$iwType.'_template'].'/content.php';
	endif;
	
}
add_action( 'gmaps_gmaps_groups_info_window_display', 'gmaps_gmgl_load_template', 10, 2 );

/**
 * GMAPS fucntion - get member distance
 * @param unknown_type $member
 * @param unknown_type $gmw
 */
function gmgl_distance( $group, $gmw ) {
	
	if ( !isset( $group->distance ) || $group->distance == false ) return;

	echo apply_filters( 'gmaps_groups_group_distance', $group->distance . ' ' .$gmw['units_array']['name'], $group, $gmw );
}

/**
 * GMAPS function - get directions link
 * @param unknown_type $member
 * @param unknown_type $gmw
 * @param unknown_type $title
 */
function gmgl_get_directions( $group, $gmw, $title ) {

	if ( !isset( $gmw['info_window']['get_directions'] ) ) return;
	return apply_filters( 'gmaps_groups_iw_get_directions_link', '<a href="http://maps.google.com/maps?f=d&hl='.$gmw['region'][0].'&region='.$gmw['region'][1].'&doflg='.$gmw['units_array']['map_units'].'&geocode=&saddr=&daddr='.str_replace( ' ', '+', $group->formatted_address ).'&ie=UTF8&z=12" target="_blank">'.$title.'</a>', $group, $gmw );
}
