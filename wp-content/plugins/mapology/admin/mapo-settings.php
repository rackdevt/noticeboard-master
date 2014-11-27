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
 
class MAPO_Admin_Settings extends MAPO_Admin_Core
{
	/**
	 * Constructor
	 * @since 1.1.2
	 */
    function __construct()
	{
		$this->head( __( 'Settings', 'mapo' ) );
		$this->content();
		$this->footer();
    }

	/**
	 * Return all the options
	 * @since 1.1.2
	 */
	function page_options()
	{
		return array(
			'enable_address', 'enable_routes', 'field_id', 'user_map_type', 'group_map_type',
			'group_map_zoom', 'user_map_zoom', 'system', 'slug', 'enable_group', 'enable_post_type',
			'enable_post', 'enable_oembed', 'map_location', 'map_lang', 'user_overview_type',
			'user_overview_zoom', 'group_overview_type', 'group_overview_zoom', 'extra_field_ids',
			'page_id', 'enhanced_map', 'public_location', 'def_within', 'enable_no_privacy'
		);	
	}

	/**
	 * Content of the General Options tab
	 * @since 1.1.2
	 */
    function content()
	{
        global $mapo, $bp_oembed, $wpdb, $bp;
		
		$field_ids = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->profile->table_name_fields} WHERE parent_id = %d", 0 ) );

		$lat = ( empty( $mapo->options->map_location['lat'] ) ) ? 5 : $mapo->options->map_location['lat'];
		$lng = ( empty( $mapo->options->map_location['lng'] ) ) ? 30 : $mapo->options->map_location['lng'];
    	?>
        <form name="general" method="post" action="" >
        
            <?php wp_nonce_field( 'mapo_settings' ) ?>
            <input type="hidden" name="page_options" value="<?php echo implode( ',', $this->page_options() ) ?>" />
            <table id="mapo-tos" class="form-table">
            <tr>
                <th><label><?php _e( 'Replot Coordinates', 'mapo' ); ?></label></th>
                <td>
                	<?php _e( 'Start', 'surf' ); ?> <input type="text" name="start_limit" id="start_limit" value="" />
                	<?php _e( 'End', 'surf' ); ?> <input type="text" name="end_limit" id="end_limit" value="" />
                    <input type="checkbox" name="nocoords_yet" id="nocoords_yet" value="yes" /> <?php _e( 'Only entries without coordinates', 'surf' ); ?>
                	<input type="submit" class="button-secondary" name="replot_coords" id="replot_coords" value="<?php _e( 'Update coordinates', 'mapo' ) ?> &raquo;" /><br /><?php _e( 'Start is the database entry to start with and end is how many entries should get replotted. Google restricts the daily requests to 2.500/day.', 'mapo' ) ;?>
                </td>
            </tr>
           <tr>
                <th><label for="page_id"><?php _e( 'Maps Page', 'mapo' ); ?></label></th>
                <td>
					<?php wp_dropdown_pages( array(
                        'name' => 'page_id',
                        'show_option_none' => __( '- None -', 'mapo' ),
                        'selected' => ( ! empty( $mapo->options->page_id ) ? $mapo->options->page_id : false )
                    ) ); ?>
 					<span class="description"><?php printf( __( 'Pick the main map page. Or <a href="%s" class="button">create it</a> if it does not exist yet.', 'mapo' ), admin_url( '/post-new.php?post_type=page' ) ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="slug"><?php _e( 'Main Slug', 'mapo' ); ?></label></th>
                <td>
 					<input type="text" id="slug" name="slug" value="<?php echo $mapo->options->slug ?>" />
                    <small><?php _e( 'Only lowercase, a-z,0-9, plus some special characters like - (gets sanitized automatically).', 'mapo' ); ?></small>
                </td>
            </tr>
            <tr>
                <th><label for="map_lang"><?php _e( 'Map Language', 'mapo' ); ?></label></th>
                <td>
                    <select id="map_lang" name="map_lang">
                    	<option value="">----</option>
                        <option value="&language=ar" <?php selected( '&language=ar', $mapo->options->map_lang ) ?>><?php _e( 'ARABIC', 'mapo' ); ?></option>	
                        <option value="&language=eu" <?php selected( '&language=eu', $mapo->options->map_lang ) ?>><?php _e( 'BASQUE', 'mapo' ); ?></option>
                        <option value="&language=bg" <?php selected( '&language=bg', $mapo->options->map_lang ) ?>><?php _e( 'BULGARIAN', 'mapo' ); ?></option>
                        <option value="&language=bn" <?php selected( '&language=bn', $mapo->options->map_lang ) ?>><?php _e( 'BENGALI', 'mapo' ); ?></option>
                        <option value="&language=ca" <?php selected( '&language=ca', $mapo->options->map_lang ) ?>><?php _e( 'CATALAN', 'mapo' ); ?></option>
                        <option value="&language=cs" <?php selected( '&language=cs', $mapo->options->map_lang ) ?>><?php _e( 'CZECH', 'mapo' ); ?></option>
                        <option value="&language=da" <?php selected( '&language=da', $mapo->options->map_lang ) ?>><?php _e( 'DANISH', 'mapo' ); ?></option>
                        <option value="&language=de" <?php selected( '&language=de', $mapo->options->map_lang ) ?>><?php _e( 'GERMAN', 'mapo' ); ?></option>
                        <option value="&language=el" <?php selected( '&language=el', $mapo->options->map_lang ) ?>><?php _e( 'GREEK', 'mapo' ); ?></option>
                        <option value="&language=en" <?php selected( '&language=en', $mapo->options->map_lang ) ?>><?php _e( 'ENGLISH', 'mapo' ); ?></option>
                        <option value="&language=en-AU" <?php selected( '&language=en-AU', $mapo->options->map_lang ) ?>><?php _e( 'ENGLISH (AUSTRALIAN)', 'mapo' ); ?></option>
                        <option value="&language=en-GB" <?php selected( '&language=en-GB', $mapo->options->map_lang ) ?>><?php _e( 'ENGLISH (GREAT BRITAIN)', 'mapo' ); ?></option>
                        <option value="&language=es" <?php selected( '&language=es', $mapo->options->map_lang ) ?>><?php _e( 'SPANISH', 'mapo' ); ?></option>
                        <option value="&language=eu" <?php selected( '&language=eu', $mapo->options->map_lang ) ?>><?php _e( 'BASQUE', 'mapo' ); ?></option>
                        <option value="&language=fi" <?php selected( '&language=fi', $mapo->options->map_lang ) ?>><?php _e( 'FINNISH', 'mapo' ); ?></option>
                        <option value="&language=fil" <?php selected( '&language=fil', $mapo->options->map_lang ) ?>><?php _e( 'FILIPINO', 'mapo' ); ?></option>
                        <option value="&language=fr" <?php selected( '&language=fr', $mapo->options->map_lang ) ?>><?php _e( 'FRENCH', 'mapo' ); ?></option>
                        <option value="&language=gl" <?php selected( '&language=gl', $mapo->options->map_lang ) ?>><?php _e( 'GALICIAN', 'mapo' ); ?></option>
                        <option value="&language=gu" <?php selected( '&language=gu', $mapo->options->map_lang ) ?>><?php _e( 'GUJARATI', 'mapo' ); ?></option>
                        <option value="&language=hi" <?php selected( '&language=hi', $mapo->options->map_lang ) ?>><?php _e( 'HINDI', 'mapo' ); ?></option>
                        <option value="&language=hr" <?php selected( '&language=hr', $mapo->options->map_lang ) ?>><?php _e( 'CROATIAN', 'mapo' ); ?></option>
                        <option value="&language=hu" <?php selected( '&language=hu', $mapo->options->map_lang ) ?>><?php _e( 'HUNGARIAN', 'mapo' ); ?></option>
                        <option value="&language=id" <?php selected( '&language=id', $mapo->options->map_lang ) ?>><?php _e( 'INDONESIAN', 'mapo' ); ?></option>
                        <option value="&language=it" <?php selected( '&language=it', $mapo->options->map_lang ) ?>><?php _e( 'ITALIAN', 'mapo' ); ?></option>
                        <option value="&language=iw" <?php selected( '&language=iw', $mapo->options->map_lang ) ?>><?php _e( 'HEBREW', 'mapo' ); ?></option>
                        <option value="&language=ja" <?php selected( '&language=ja', $mapo->options->map_lang ) ?>><?php _e( 'JAPANESE', 'mapo' ); ?></option>
                        <option value="&language=kn" <?php selected( '&language=kn', $mapo->options->map_lang ) ?>><?php _e( 'KANNADA', 'mapo' ); ?></option>
                        <option value="&language=ko" <?php selected( '&language=ko', $mapo->options->map_lang ) ?>><?php _e( 'KOREAN', 'mapo' ); ?></option>
                        <option value="&language=lt" <?php selected( '&language=lt', $mapo->options->map_lang ) ?>><?php _e( 'LITHUANIAN', 'mapo' ); ?></option>
                        <option value="&language=lv" <?php selected( '&language=lv', $mapo->options->map_lang ) ?>><?php _e( 'LATVIAN', 'mapo' ); ?></option>
                        <option value="&language=ml" <?php selected( '&language=ml', $mapo->options->map_lang ) ?>><?php _e( 'MALAYALAM', 'mapo' ); ?></option>
                        <option value="&language=mr" <?php selected( '&language=mr', $mapo->options->map_lang ) ?>><?php _e( 'MARATHI', 'mapo' ); ?></option>
                        <option value="&language=nl" <?php selected( '&language=nl', $mapo->options->map_lang ) ?>><?php _e( 'DUTCH', 'mapo' ); ?></option>
                        <option value="&language=no" <?php selected( '&language=no', $mapo->options->map_lang ) ?>><?php _e( 'NORWEGIAN', 'mapo' ); ?></option>
                        <option value="&language=pl" <?php selected( '&language=pl', $mapo->options->map_lang ) ?>><?php _e( 'POLISH', 'mapo' ); ?></option>
                        <option value="&language=pt" <?php selected( '&language=pt', $mapo->options->map_lang ) ?>><?php _e( 'PORTUGUESE', 'mapo' ); ?></option>
                        <option value="&language=pt-BR" <?php selected( '&language=pt-BR', $mapo->options->map_lang ) ?>><?php _e( 'PORTUGUESE (BRAZIL)', 'mapo' ); ?></option>
                        <option value="&language=pt-PT" <?php selected( '&language=pt-PT', $mapo->options->map_lang ) ?>><?php _e( 'PORTUGUESE (PORTUGAL)', 'mapo' ); ?></option>
                        <option value="&language=ro" <?php selected( '&language=ro', $mapo->options->map_lang ) ?>><?php _e( 'ROMANIAN', 'mapo' ); ?></option>
                        <option value="&language=ru" <?php selected( '&language=ru', $mapo->options->map_lang ) ?>><?php _e( 'RUSSIAN', 'mapo' ); ?></option>
                        <option value="&language=sk" <?php selected( '&language=sk', $mapo->options->map_lang ) ?>><?php _e( 'SLOVAK', 'mapo' ); ?></option>
                        <option value="&language=sl" <?php selected( '&language=sl', $mapo->options->map_lang ) ?>><?php _e( 'SLOVENIAN', 'mapo' ); ?></option>
                        <option value="&language=sr" <?php selected( '&language=sr', $mapo->options->map_lang ) ?>><?php _e( 'SERBIAN', 'mapo' ); ?></option>
                        <option value="&language=sv" <?php selected( '&language=sv', $mapo->options->map_lang ) ?>><?php _e( 'SWEDISH', 'mapo' ); ?></option>
                        <option value="&language=tl" <?php selected( '&language=tl', $mapo->options->map_lang ) ?>><?php _e( 'TAGALOG', 'mapo' ); ?></option>
                        <option value="&language=ta" <?php selected( '&language=ta', $mapo->options->map_lang ) ?>><?php _e( 'TAMIL', 'mapo' ); ?></option>
                        <option value="&language=te" <?php selected( '&language=te', $mapo->options->map_lang ) ?>><?php _e( 'TELUGU', 'mapo' ); ?></option>
                        <option value="&language=th" <?php selected( '&language=th', $mapo->options->map_lang ) ?>><?php _e( 'THAI', 'mapo' ); ?></option>
                        <option value="&language=tr" <?php selected( '&language=tr', $mapo->options->map_lang ) ?>><?php _e( 'TURKISH', 'mapo' ); ?></option>
                        <option value="&language=uk" <?php selected( '&language=uk', $mapo->options->map_lang ) ?>><?php _e( 'UKRAINIAN', 'mapo' ); ?></option>
                        <option value="&language=vi" <?php selected( '&language=vi', $mapo->options->map_lang ) ?>><?php _e( 'VIETNAMESE', 'mapo' ); ?></option>
                        <option value="&language=zh-CN" <?php selected( '&language=zh-CN', $mapo->options->map_lang ) ?>><?php _e( 'CHINESE (SIMPLIFIED)', 'mapo' ); ?></option>
                        <option value="&language=zh-TW" <?php selected( '&language=zh-TW', $mapo->options->map_lang ) ?>><?php _e( 'CHINESE (TRADITIONAL)', 'mapo' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="enable_address"><?php _e( 'Group Contact Details', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_address" name="enable_address"<?php if( $mapo->options->enable_address === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable group contact details.', 'mapo' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_routes"><?php _e( 'Routes', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_routes" name="enable_routes"<?php if( $mapo->options->enable_routes === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable routes.', 'mapo' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_group"><?php _e( 'Group Attachment', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_group" name="enable_group"<?php if( $mapo->options->enable_group === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable route attachments for groups.', 'mapo' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="enable_post_type"><?php _e( 'Custom Post Type', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_post_type" name="enable_post_type"<?php if( $mapo->options->enable_post_type === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable the location post type.', 'mapo' ); ?>
                </td>
            </tr>
            <?php if( isset( $bp_oembed ) ) : ?>
            <tr>
                <th><label for="enable_oembed"><?php _e( 'Enable oEmbed', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_oembed" name="enable_oembed"<?php if( $mapo->options->enable_oembed === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable oembed support for route descriptions.', 'mapo' ); ?>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><label for="enable_post"><?php _e( 'Post Coordinates', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_post" name="enable_post"<?php if( $mapo->options->enable_post === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable post coordinates.', 'mapo' ); ?>
                </td>
            </tr>
             <tr>
                <th><label for="enhanced_map"><?php _e( 'Use enhanced user map?', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enhanced_map" name="enhanced_map"<?php if( $mapo->options->enhanced_map === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to enable the enhanced user map.', 'mapo' ); ?>
                </td>
            </tr>
           <tr>
                <th><label for="public_location"><?php _e( 'Public Location', 'mapo' ); ?></label></th>
                <td>
                    <select id="public_location" name="public_location">
                    	<option value="">----</option>
                    	<?php foreach( $field_ids as $key => $val ) { ?>
                        <option value="<?php echo $val->id ?>" <?php selected( $val->id, $mapo->options->public_location ) ?>><?php echo $val->name ?></option>
                        <?php } ?>
                    </select>
                    <small><?php _e( 'Leave empty to disable a public location (used on enhanced members map).', 'mapo' ); ?></small>
                </td>
            </tr>
             <tr>
                <th><label for="enable_no_privacy"><?php _e( 'Disable map privacy', 'mapo' ); ?></label></th>
                <td>
 					<input type="checkbox" id="enable_no_privacy" name="enable_no_privacy"<?php if( $mapo->options->enable_no_privacy === true ) echo ' checked="checked"'; ?> value="true" /> <?php _e( 'Check to disable map privacy.', 'mapo' ); ?>
                </td>
            </tr>
            <tr>
                <th><label for="def_within"><?php _e( 'Default Distance for Search', 'mapo' ); ?></label></th>
                <td>
                    <select id="def_within" name="def_within">
                    	<option value="">----</option>
                    	<?php foreach( $mapo->config->prox_options as $val ) { ?>
                        <option value="<?php echo esc_attr( $val ) ?>" <?php selected( $val, $mapo->options->def_within ) ?>><?php echo $val ?></option>
                        <?php } ?>
                    </select>
                    <small><?php _e( 'Default map view radius for enhanced members map.', 'mapo' ); ?></small>
                </td>
            </tr>
            <tr>
                <th><label for="field_id"><?php _e( 'Location Field ID', 'mapo' ); ?></label></th>
                <td>
                    <select id="field_id" name="field_id">
                    	<option value="">----</option>
                    	<?php foreach( $field_ids as $key => $val ) { ?>
                        <option value="<?php echo $val->id ?>" <?php selected( $val->id, $mapo->options->field_id ) ?>><?php echo $val->name ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="extra_field_ids"><?php _e( 'Extra Location Field IDs', 'mapo' ); ?></label></th>
                <td>
                   	<?php foreach( $field_ids as $key => $val ) { ?>
                        <input type="checkbox" value="<?php echo $val->id ?>" name="extra_field_ids[]"<?php if( in_array( $val->id, (array)$mapo->options->extra_field_ids ) ) echo ' checked="checked"'; ?> /> <?php echo $val->name ?><br />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th><label for="system"><?php _e( 'System', 'mapo' ); ?></label></th>
                <td>
                    <select id="system" name="system">
                    	<option value="">----</option>
                        <option value="km" <?php selected( 'km', $mapo->options->system ) ?>><?php _e( 'Kilometers', 'mapo' ); ?></option>
                        <option value="m" <?php selected( 'm', $mapo->options->system ) ?>><?php _e( 'Miles', 'mapo' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="group_map_type"><?php _e( 'Group Map Types', 'mapo' ); ?></label></th>
                <td>
                    <select id="group_map_type" name="group_map_type">
                    	<option value="">----</option>
                        <option value="ROADMAP" <?php selected( 'ROADMAP', $mapo->options->group_map_type ) ?>>ROADMAP</option>
                        <option value="SATELLITE" <?php selected( 'SATELLITE', $mapo->options->group_map_type ) ?>>SATELLITE</option>
                        <option value="HYBRID" <?php selected( 'HYBRID', $mapo->options->group_map_type ) ?>>HYBRID</option>
                        <option value="TERRAIN" <?php selected( 'TERRAIN', $mapo->options->group_map_type ) ?>>TERRAIN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="group_map_zoom"><?php _e( 'Group Map Zoom', 'mapo' ); ?></label></th>
                <td>
 					<input type="text" id="group_map_zoom" name="group_map_zoom" value="<?php echo $mapo->options->group_map_zoom ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="user_map_type"><?php _e( 'User Map Types', 'mapo' ); ?></label></th>
                <td>
                    <select id="user_map_type" name="user_map_type">
                    	<option value="">----</option>
                        <option value="ROADMAP" <?php selected( 'ROADMAP', $mapo->options->user_map_type ) ?>>ROADMAP</option>
                        <option value="SATELLITE" <?php selected( 'SATELLITE', $mapo->options->user_map_type ) ?>>SATELLITE</option>
                        <option value="HYBRID" <?php selected( 'HYBRID', $mapo->options->user_map_type ) ?>>HYBRID</option>
                        <option value="TERRAIN" <?php selected( 'TERRAIN', $mapo->options->user_map_type ) ?>>TERRAIN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="user_map_zoom"><?php _e( 'User Map Zoom', 'mapo' ); ?></label></th>
                <td>
 					<input type="text" id="user_map_zoom" name="user_map_zoom" value="<?php echo $mapo->options->user_map_zoom ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="user_overview_type"><?php _e( 'User Overview Type', 'mapo' ); ?></label></th>
                <td>
                    <select id="user_overview_type" name="user_overview_type">
                    	<option value="">----</option>
                        <option value="ROADMAP" <?php selected( 'ROADMAP', $mapo->options->user_overview_type ) ?>>ROADMAP</option>
                        <option value="SATELLITE" <?php selected( 'SATELLITE', $mapo->options->user_overview_type ) ?>>SATELLITE</option>
                        <option value="HYBRID" <?php selected( 'HYBRID', $mapo->options->user_overview_type ) ?>>HYBRID</option>
                        <option value="TERRAIN" <?php selected( 'TERRAIN', $mapo->options->user_overview_type ) ?>>TERRAIN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="user_overview_zoom"><?php _e( 'User Overview Zoom', 'mapo' ); ?></label></th>
                <td>
 					<input type="text" id="user_overview_zoom" name="user_overview_zoom" value="<?php echo $mapo->options->user_overview_zoom ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="group_overview_type"><?php _e( 'Group Overview Type', 'mapo' ); ?></label></th>
                <td>
                    <select id="group_overview_type" name="group_overview_type">
                    	<option value="">----</option>
                        <option value="ROADMAP" <?php selected( 'ROADMAP', $mapo->options->group_overview_type ) ?>>ROADMAP</option>
                        <option value="SATELLITE" <?php selected( 'SATELLITE', $mapo->options->group_overview_type ) ?>>SATELLITE</option>
                        <option value="HYBRID" <?php selected( 'HYBRID', $mapo->options->group_overview_type ) ?>>HYBRID</option>
                        <option value="TERRAIN" <?php selected( 'TERRAIN', $mapo->options->group_overview_type ) ?>>TERRAIN</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="group_overview_zoom"><?php _e( 'Group Overview Zoom', 'mapo' ); ?></label></th>
                <td>
 					<input type="text" id="group_overview_zoom" name="group_overview_zoom" value="<?php echo $mapo->options->group_overview_zoom ?>" />
                </td>
            </tr>
            <tr>
                <th><label for="map_location"><?php _e( 'Default Map Location', 'mapo' ); ?></label></th>
                <td>
 					<input type="hidden" id="map_location_lat" name="map_location[lat]" value="<?php echo $lat ?>" />
                    <input type="hidden" id="map_location_lng" name="map_location[lng]" value="<?php echo $lng ?>" />
                    <div id="default-loc-map" style="width:600px;height:400px;"></div>
                </td>
            </tr>
			</table>
            <div class="submit"><input type="submit" name="mapooption" value="<?php _e( 'Update' ) ;?> &raquo;"/></div>
        </form>
		<script type="text/javascript">
		var map;
		var mark;
		var markersArray = [];
		
		function def_mapo_initialize() {
			var latlng = new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>);
			
			var mapOptions = {
				zoom: 2,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.HYBRID
			}
			map = new google.maps.Map(document.getElementById("default-loc-map"), mapOptions);
		
			mark = new google.maps.Marker({
				position: latlng, 
				map: map
			});
		
			google.maps.event.addListener(map, 'click', function(event) {
				var coords = event.latLng;
				mark.setMap(null);
				placeMarker(coords);
				
				coords = String(coords);
				coords = coords.split(', ');
				
				jQuery('#map_location_lat').val(coords[0].substr( 1, coords[0].length ));
				jQuery('#map_location_lng').val(coords[1].substr( 0, coords[1].length - 1 ));
			});
		}
		
		function placeMarker(location) {
			deleteOverlays();
			var marker = new google.maps.Marker({
				position: location, 
				map: map
			});
			
			markersArray.push(marker);
			map.setCenter(location);
		}
		
		function deleteOverlays() {
			if (markersArray) {
				for (i in markersArray) {
					markersArray[i].setMap(null);
				}
				markersArray.length = 0;
			}
		}
		
		jQuery(document).ready(function(){
			def_mapo_initialize();
		});
        </script>
<?php
	}
}
?>