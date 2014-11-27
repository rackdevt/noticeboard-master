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
* Determine the upload folder
* @since 1.0
*/
function mapo_upload_path()
{
	if( bp_core_is_multisite() )
	{
		$path = ABSPATH . get_blog_option( BP_ROOT_BLOG, 'upload_path' );
	}
	else
	{
		$upload_path = get_blog_option( BP_ROOT_BLOG, 'upload_path' );
		$upload_path = trim( $upload_path );
		
		if( empty( $upload_path ) || $upload_path == 'wp-content/uploads' ) 
		{
			$path = WP_CONTENT_DIR . '/uploads';
		}
		else
		{
			$path = $upload_path;
			if( 0 !== strpos( $path, ABSPATH ) )
				$path = path_join( ABSPATH, $path );
		}
	}
	
	$path .= '/maps/';

	return apply_filters( 'mapo_upload_path', $path );
}

/**
* Determine the upload url
* @since 1.0
*/
function mapo_upload_url( $root = false )
{
	global $bp;
	
	if( bp_core_is_multisite() )
	{
		$url = get_site_url() .'/'. get_blog_option( BP_ROOT_BLOG, 'upload_path' );
	}
	else
	{
		$upload_url = get_blog_option( BP_ROOT_BLOG, 'upload_path' );
		$upload_url = trim( $upload_url );
		
		if( empty( $upload_url ) || $upload_url == 'wp-content/uploads' ) 
		{
			$url = WP_CONTENT_URL . '/uploads';
		}
		else
		{
			$url = $upload_url;
			if( 0 !== strpos( $url, get_site_url() ) )
				$url = path_join( get_site_url(), $url );
		}
	}
	
	$url .= '/maps/';
	
	if( $root )
		$url = str_replace( bp_get_root_domain(), '', $url );

	return apply_filters( 'mapo_upload_url', $url );
}

/**
* Look for the templates in the proper places
* @since 1.0
*/
function mapo_load_template_filter( $found_template, $templates )
{
	global $bp;

	if( bp_is_current_component( $bp->mapology->slug ) )
	{
		foreach( (array)$templates as $template )
		{
			if( file_exists( STYLESHEETPATH . '/' . $template ) )
				$filtered_templates[] = STYLESHEETPATH . '/' . $template;
				
			else
				$filtered_templates[] = MAPO_ABSPATH . 'templates/' . $template;
		}
	
		return apply_filters( 'mapo_load_template_filter', $filtered_templates[0] );
	}
	else
		return $found_template;
}
add_filter( 'bp_located_template', 'mapo_load_template_filter', 10, 2 );

/**
* Load a template in the correct order
* @since 1.0
*/
function mapo_load_template( $template_name )
{
	if( file_exists( STYLESHEETPATH .'/'. $template_name . '.php' ) )
		$located = STYLESHEETPATH .'/'. $template_name . '.php';
		
	elseif( file_exists( TEMPLATEPATH .'/'. $template_name . '.php' ) )
		$located = TEMPLATEPATH .'/'. $template_name . '.php';
	
	else
		$located = MAPO_ABSPATH . 'templates/' . $template_name . '.php';

	include( $located );
}

/**
 * Manual coordinate lookup
 * @since 1.0
 */
function mapo_add_manual_coords_update()
{
	global $mapo, $field, $bp;
	
	if( $mapo->options->field_id == $field->id )
	{
		$coords = new MAPO_Coords( null, $bp->loggedin_user->id );
		
		$manual = get_user_meta( $bp->loggedin_user->id, 'has_manual_coordinates', true );
		
		$lat = ( empty( $coords->lat ) ) ? 5 : $coords->lat;
		$lng = ( empty( $coords->lng ) ) ? 5 : $coords->lng;
		?>
        <p class="cchange">
        	<a class="button" id="coords-change" href="#"><?php _e( 'Change location manually', 'mapo' ) ?></a>
			<?php if( $manual == 'yes' ) : ?>
                <label><input type="checkbox" id="keep_manual_coords" name="keep_manual_coords" checked="checked" value="yes" /> <?php _e( 'Keep my custom location!', 'mapo' ) ?></label>
            <?php endif; ?>
        </p>
        
        <div id="change-coords">
        	<div id="default-loc-map" style="width:450px;height:300px;"></div>
        </div>
        <input type="hidden" id="map_location_lat" name="map_location[lat]" value="<?php echo $lat ?>" />
        <input type="hidden" id="map_location_lng" name="map_location[lng]" value="<?php echo $lng ?>" />
        <input type="hidden" id="manual_coords" name="manual_coords" value="false" />
        
		<script type="text/javascript">
		var map;
		var mark;
		var markersArray = [];
		
		function def_mapo_initialize() {
			var latlng = new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>);
			
			var mapOptions = {
				zoom: <?php echo ( $lat == 5 ) ? 2 : 12; ?>,
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
				jQuery('#manual_coords').val('true');
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

			jQuery('#change-coords').hide();
			jQuery('#coords-change').click(function() {
				jQuery('#change-coords').toggle('slow', function() {
					google.maps.event.trigger(map, 'resize');
					var pos = new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lng ?>);
					map.setCenter(pos);
				});
				return false;
			});

			def_mapo_initialize();
		});
        </script>
		<?php		
	}
}
add_action( 'bp_custom_profile_edit_fields', 'mapo_add_manual_coords_update' );

/**
 * Save a users coordinates
 * @since 1.0
 */
if( ! function_exists( 'mapo_save_user_coordinates' ) )
{
	function mapo_save_user_coordinates( $user_id, $field = false )
	{
		global $mapo, $bp;

		$position = new MAPO_Coords( null, $user_id );
		
		// manual update of coordinates takes precedence
		if( $_POST['manual_coords'] == 'true' )
		{
				$id = mapo_get_id_by_user( $user_id );
				
				if( ! $id ) $id = null;
				
				$coords['lat'] = $_POST['map_location']['lat'];
				$coords['lng'] = $_POST['map_location']['lng'];
				
				update_user_meta( $user_id, 'has_manual_coordinates', 'yes' );
		
				mapo_add_coords( $id, $user_id, 0, $coords['lat'], $coords['lng'] );
				do_action( 'mapo_updated_location', $user_id, $coords );
				
				return;
		}
		
		// Don't change the coordinates
		if( $_POST['keep_manual_coords'] == 'yes' )
			return;
		else
			delete_user_meta( $user_id, 'has_manual_coordinates' );

		if( ! $field )
		{
			$field = $_POST['field_'. $mapo->options->field_id];
			foreach( (array)$mapo->options->extra_field_ids as $id )
			{
				if( ! empty( $_POST['field_'. $id] ) )
					$field .= ','. $_POST['field_'. $id];
			}
		}

		if( ! empty( $field ) )
		{			
			$coords = mapo_get_coords( $field );

			if( $coords == 'OVER_QUERY_LIMIT' )
				return 'OVER_QUERY_LIMIT';
			
			if( $coords )
			{
				$id = mapo_get_id_by_user( $user_id );
				
				if( ! $id ) $id = null;
				
				mapo_add_coords( $id, $user_id, 0, $coords['lat'], $coords['lng'] );
				do_action( 'mapo_updated_location', $user_id, $coords );
			}
		}
	}
	add_action( 'xprofile_updated_profile', 'mapo_save_user_coordinates' );
}

/**
 * Save a users public coordinates
 * @since 1.0
 */
function mapo_save_public_user_location( $user_id, $field = false )
{
	global $mapo;
	
	if( empty( $mapo->options->public_location ) )
		return false;

	if( ! $field )
		$field = $_POST['field_'. $mapo->options->public_location];
		
	if( ! empty( $field ) )
	{
		$coords = mapo_get_coords( $field );
			
		if( $coords == 'OVER_QUERY_LIMIT' )
			return 'OVER_QUERY_LIMIT';
			
		if( $coords )
		{
			update_user_meta( $user_id, 'latitude', $coords['lat'] );
			update_user_meta( $user_id, 'longitude', $coords['lng'] );
		}
	}
}
add_action( 'xprofile_updated_profile', 'mapo_save_public_user_location' );

/**
 * Get a users coordinates
 * @since 1.0
 */
if( ! function_exists( 'mapo_get_coords' ) )
{
	function mapo_get_coords( $field )
	{
		global $mapo, $wpdb;
	
		$xml = file_get_contents( 'http://maps.google.com/maps/api/geocode/xml?address='. urlencode( $field ) .'&sensor=false' );
		
		if( $xml == 'OVER_QUERY_LIMIT' )
			return 'OVER_QUERY_LIMIT';
		
		$data = new SimpleXMLElement( $xml );
		
		if( $data->status == 'OK' )
		{
			$lat = (array)$data->result->geometry->location->lat;
			$lng = (array)$data->result->geometry->location->lng;
	
			$latitude = $original_lat = $lat[0];
			$longitude = $original_lng = $lng[0];
			
			$result = $wpdb->get_row( "SELECT * FROM {$mapo->tables->coords} WHERE lat = {$latitude} AND lng = {$longitude} AND user_id > 0" );
	
			if( $result )
			{
				$angle = 36;
				$radius = 0.0001;
				do {
					if( $angle % 360 == 0 )
						$radius = $radius + 0.0001;
						
					$coords = mapo_next_coords( $original_lat, $original_lng, $radius, $angle );
					
					$latitude = $coords['lat'];
					$longitude = $coords['lng'];
					
					$result = $wpdb->get_row( "SELECT * FROM {$mapo->tables->coords} WHERE lat = {$latitude} AND lng = {$longitude} AND user_id > 0" );
		
					$angle = $angle + 36;
				} while ( $result );
			}
			
			return array( 'lat' => $latitude, 'lng' => $longitude );
		}
		
		return false;
	}
}

/**
* Arrange coordinates in a circle
* @since 1.1.6
*/
function mapo_next_coords( $lat, $lng, $radius, $angle )
{
	$new_lat = $lat + ( $radius * cos( $angle * pi() / 180 ) );	
	$new_lng = $lng + ( $radius * sin( $angle * pi() / 180 ) );
	
	return array( 'lat' => $new_lat, 'lng' => $new_lng );
}

/**
 * Add some usermeta
 * @since 1.0
 */
if( ! function_exists( 'mapo_add_to_signup' ) )
{
	function mapo_add_to_signup( $usermeta )
	{
		global $mapo;
		
		if( ! empty( $_POST['field_'. $mapo->options->field_id] ) )
		{
			$field = $_POST['field_'. $mapo->options->field_id];
			foreach( (array)$mapo->options->extra_field_ids as $id )
			{
				if( ! empty( $_POST['field_'. $id] ) )
					$field .= ','.$_POST['field_'. $id];
			}
			
			$usermeta['mapo_location'] = $field;
		}
		
		if( ! empty( $mapo->options->public_location ) )
		{
			$pub_field = $_POST['field_'. $mapo->options->public_location];
			$usermeta['mapo_public_location'] = $pub_field;
		}

		return $usermeta;
	}
	add_filter( 'bp_signup_usermeta', 'mapo_add_to_signup' );
}

/**
 * Save coordinates on registration
 * @since 1.0
 */
if( ! function_exists( 'mapo_user_activate_fields' ) )
{
	function mapo_user_activate_fields( $user_id, $user_login, $user_password, $user_email, $usermeta )
	{
		if( ! empty( $usermeta['mapo_location'] ) )
		{
			$coords = mapo_get_coords( $usermeta['mapo_location'] );
			
			if( $coords )
				mapo_add_coords( null, $user_id, 0, $coords['lat'], $coords['lng'] );
		}
		
		if( ! empty( $usermeta['mapo_public_location'] ) )
		{
			$coords = mapo_get_coords( $usermeta['mapo_public_location'] );

			if( $coords )
			{
				update_user_meta( $user_id, 'latitude', $coords['lat'] );
				update_user_meta( $user_id, 'longitude', $coords['lng'] );
			}
		}
		
		return $user_id;
	}
	add_action( 'bp_core_signup_user', 'mapo_user_activate_fields', 5, 5 );
}

/**
 * Add user js vars to the page
 * @since 1.0
 */
function mapo_add_user_js_vars()
{
	global $bp, $mapo;

	if( ! mapo_location_is_viewable() )
		return false;
	
	if( ! empty( $bp->displayed_user->id ) && $bp->current_component == 'profile' )
	{	
		$coords = new MAPO_Coords( null, $bp->displayed_user->id );
		if( ! empty( $coords ) )
		{
			?>
			<script type="text/javascript">
			function umap_initialize() {
				var ucoords = new google.maps.LatLng(<?php echo $coords->lat ?>, <?php echo $coords->lng ?>);
				var umapOptions = {
					zoom: <?php echo $mapo->options->user_map_zoom ?>,
					center: ucoords,
					navigationControl: true,
					mapTypeControl: false,
					scaleControl: false,
					mapTypeId: google.maps.MapTypeId.<?php echo $mapo->options->user_map_type ?>
				};
				var umap = new google.maps.Map(document.getElementById("umap"), umapOptions);
			
				var umarker = new google.maps.Marker({
					position: ucoords,
					title: '<?php echo $bp->displayed_user->fullname ?>'
				});
			  
				umarker.setMap(umap);
			}
			
			jQuery(document).ready( function() {
				jQuery( 'tr.field_<?php echo $mapo->options->field_id ?> td.data' ).prepend( '<div id="umap"></div>' );
				umap_initialize();
			});
			</script>
			<?php
		}
	}
}
add_action( 'wp_footer', 'mapo_add_user_js_vars' );

/**
 * Maybe hide the location
 * @since 1.0
 */
function mapo_maybe_hide_location( $value, $type, $id )
{
	global $mapo;
	
	if( $mapo->options->field_id != $id )
		return $value;
	
	if( ! mapo_location_is_viewable() )
		return __( 'You are not allowed to view this location', 'mapo' );

	return $value;
}
add_filter( 'bp_get_the_profile_field_value', 'mapo_maybe_hide_location', 10, 3 );

/**
 * Get coordinates from all users
 * @since 1.0
 */
function mapo_get_all_user_coordinates()
{
	global $wpdb, $mapo, $shabuCounter;
	
	if( $mapo->options->enhanced_map === true )
		return false;
	
	if( ! $shabuCounter )
		$shabuCounter = 1;
	
	$result = $wpdb->get_results( $wpdb->prepare( "
		SELECT c.lat, c.lng, c.user_id, u.*, um.meta_value as privacy
		FROM {$mapo->tables->coords} c
		RIGHT JOIN {$wpdb->users} u 
		ON u.ID = c.user_id
		LEFT JOIN {$wpdb->usermeta} um 
		ON um.user_id = c.user_id
		AND um.meta_key = %s
		WHERE c.user_id > 0"
	, 'map_privacy' ) );

	?>
    <div class="map-wrapper"><div id="useroverviewmap<?php echo $shabuCounter ?>" class="map-holder"></div></div>
	<script type="text/javascript">
	var infoWindow<?php echo $shabuCounter ?>; 
	var markers<?php echo $shabuCounter ?> = [];
	var map<?php echo $shabuCounter ?>;
	var locations<?php echo $shabuCounter ?> = [<?php foreach( $result as $key => $val ) {
			
			if( $val->user_status == 2 || ! mapo_location_is_viewable( $val->user_id, $val->privacy ) )
				continue;
			
			$avatar = bp_core_fetch_avatar( array( 'item_id' => $val->user_id, 'object' => 'user', 'type' => 'full', 'alt' => '', 'css_id' => 'avatar-'. $val->user_id, 'class' => 'avatar', 'width' => 100, 'height' => 100 ) );
			$avatar = str_replace( "'", '"', $avatar );
			
			$map_content = $avatar ."<div class=\"clear\"></div><strong><a href=\"". bp_core_get_user_domain( $val->user_id ) ."/\">". esc_js( $val->user_nicename ) ."</a></strong>";
			
			$locations[] = "['". esc_js( $val->user_nicename ) ."', ". $val->lat .", ". $val->lng .", '<div class=\"map-content\">". apply_filters( 'mapo_member_map_content', $map_content, $val ) ."</div>']";
			
		}
		echo implode( ',', (array)$locations );
		?>];

	function userMapInitialize<?php echo $shabuCounter ?>() {
		var mapOptions<?php echo $shabuCounter ?> = {
			zoom: <?php echo $mapo->options->user_overview_zoom ?>,
			center: new google.maps.LatLng(<?php echo $mapo->options->map_location['lat'] ?>, <?php echo $mapo->options->map_location['lng'] ?>),
			mapTypeId: google.maps.MapTypeId.<?php echo $mapo->options->user_overview_type ?>
		}
		map<?php echo $shabuCounter ?> = new google.maps.Map(document.getElementById("useroverviewmap<?php echo $shabuCounter ?>"), mapOptions<?php echo $shabuCounter ?>);
		infoWindow<?php echo $shabuCounter ?> = new google.maps.InfoWindow(); 
		
		for (var i = 0; i < locations<?php echo $shabuCounter ?>.length; i++) {
			var loc<?php echo $shabuCounter ?> = locations<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				icon: '<?php echo apply_filters( 'mapo_members_map_marker', MAPO_URLPATH .'css/images/member.png' ) ?>',
				map: map<?php echo $shabuCounter ?>,
				title: loc<?php echo $shabuCounter ?>[0]
			});

			google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(markerArg, cont) {
			  return function() {
				infoWindow<?php echo $shabuCounter ?>.setContent(cont);
				infoWindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>, markerArg);
			  };
			})(marker<?php echo $shabuCounter ?>, loc<?php echo $shabuCounter ?>[3]));

			markers<?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>);
		}
		i = null;
		
		var markerCluster<?php echo $shabuCounter ?> = new MarkerClusterer(map<?php echo $shabuCounter ?>, markers<?php echo $shabuCounter ?>, { maxZoom: 12, gridSize: 50 });
	}

	jQuery(document).ready( function() {
		userMapInitialize<?php echo $shabuCounter ?>();
	});
	</script>
	<?php
	
	$shabuCounter++;
}
add_action( 'bp_before_directory_members_content', 'mapo_get_all_user_coordinates' );

/**
 * Show the enhanced map
 * @since 1.0
 */
function mapo_enhanced_members_map()
{
	global $wpdb, $mapo, $shabuCounter, $bp;

	if( $mapo->options->enhanced_map === false )
		return false;

	if( ! $shabuCounter )
		$shabuCounter = 1;

	$gr = ( isset( $_GET['show_groups'] ) ) ? absint( $_GET['show_groups'] ) : false;
	$me = ( isset( $_GET['show_members'] ) ) ? absint( $_GET['show_members'] ) : false;
	$ev = ( isset( $_GET['show_events'] ) ) ? absint( $_GET['show_events'] ) : false;
	$within = ( isset( $_GET['within'] ) ) ? absint( $_GET['within'] ) : false;
	$check  = ( isset( $_GET['check'] ) ) ? absint( $_GET['check'] ) : false;
	$of = ( isset( $_GET['of'] ) ) ? urldecode( $_GET['of'] ) : false;
	$all = ( isset( $_GET['all'] ) ) ? true : false;

	$show_members = $show_groups = $show_events = false;
	$users = $groups = $events = array();
	
	$dist = ( $mapo->options->system  == 'm' ) ? 3959 : 6371;
	$dist_sql = '';
	$order_by = '';
	
	if( $check && absint( $within ) > 0 )
	{
		if( $of ) :
			$c = mapo_get_coords( $of );
			
			$coords = new stdClass();
			$coords->lat = $c['lat'];
			$coords->lng = $c['lng'];
		else :
			$coords = new MAPO_Coords( null, bp_loggedin_user_id() );
		endif;
	}
	elseif( ! $check && $bp->loggedin_user->has_location )
	{
		$coords = new MAPO_Coords( null, bp_loggedin_user_id() );
		$within = ( ! empty( $mapo->options->def_within ) ) ? $mapo->options->def_within : 10;
	}

	if( ! empty( $coords->lat ) && ! empty( $coords->lng ) && ! empty( $within ) ) :
		if( empty( $mapo->options->public_location ) ) :
			$dist_sql = ", ( {$dist} * acos( cos( radians( {$coords->lat} ) ) * cos( radians( c.lat ) ) * cos( radians( c.lng ) - radians( {$coords->lng} ) ) + sin( radians( {$coords->lat} ) ) * sin( radians( c.lat ) ) ) ) as distance";
		else :
			$dist_sql = ", ( {$dist} * acos( cos( radians( {$coords->lat} ) ) * cos( radians( um1.meta_value ) ) * cos( radians( um2.meta_value ) - radians( {$coords->lng} ) ) + sin( radians( {$coords->lat} ) ) * sin( radians( um1.meta_value ) ) ) ) as distance";
		endif;
		
		$order_by = "HAVING distance < {$within} ORDER BY distance";
	endif;
	
	$date = new DateTime();
	$date->modify( '-7 days' );
	
	$days_before = $date->format( 'Y-m-d H:i:s' );

	if( defined( 'MAPO_SHOW_USERS' ) && MAPO_SHOW_USERS === true ) :
		if( ! $check || $check && $me ) :
			$all__member_query = $all ? '' :"AND u.user_registered >= '{$days_before}'";
			
			if( empty( $mapo->options->public_location ) ) :
				$user_query = $wpdb->prepare( "
					SELECT c.lat, c.lng, c.user_id, u.*, um.meta_value as privacy{$dist_sql}
					FROM {$mapo->tables->coords} c
					RIGHT JOIN {$wpdb->users} u 
					ON u.ID = c.user_id
					LEFT JOIN {$wpdb->usermeta} um 
					ON um.user_id = c.user_id
					AND um.meta_key = %s
					WHERE c.user_id > 0
					{$all_member_query}
					{$order_by}"
				, 'map_privacy' );
			else :
				$user_query = $wpdb->prepare( "
					SELECT um1.user_id, um1.meta_value as lat, um2.meta_value as lng, u.*{$dist_sql}
					FROM {$wpdb->usermeta} um1
					RIGHT JOIN {$wpdb->usermeta} um2 
					ON um2.user_id = um1.user_id
					AND um2.meta_key = 'longitude'
					RIGHT JOIN {$wpdb->users} u 
					ON u.ID = um1.user_id
					WHERE um1.meta_key = %s
					{$all_member_query}
					{$order_by}"
				, 'latitude' );
			endif;

			$users = $wpdb->get_results( apply_filters( 'mapo_advanced_map_member_query', $user_query ) );
		endif;
			
		$show_members = true;
	endif;
	
	if( defined( 'MAPO_SHOW_GROUPS' ) && MAPO_SHOW_GROUPS === true && mapo_is_address_enabled() ) :
		if( ! $check || $check && $gr ) :
			$all_group_query = $all ? '' : " 'g.date_created >= {$days_before}'";
		
			$group_query = "
				SELECT c.lat, c.lng, c.group_id, g.id, g.name, g.description, g.status, g.slug, gm.meta_value as address{$dist_sql}
				FROM {$mapo->tables->coords} c
				LEFT JOIN {$bp->groups->table_name} g
				ON c.group_id = g.id
				LEFT JOIN {$bp->groups->table_name_groupmeta} gm
				ON gm.group_id = c.group_id
				AND gm.meta_key = 'group_address'
				WHERE c.group_id > 0
				{$all_group_query}
				{$order_by}";
			$groups = $wpdb->get_results( apply_filters( 'mapo_advanced_map_group_query', $group_query ) );
		endif;

		$show_groups = true;
	endif;

	if( defined( 'MAPO_SHOW_EVENTS' ) && MAPO_SHOW_EVENTS === true && function_exists( 'bpe_get_events' ) ) :
		if( ! $check || $check && $ev ) :
			$event_args = array( 
				'map' 		=> true,
				'per_page' 	=> 9999
			);

			if( $all )
				$event_args['plus_days'] = 7;
			
			if( absint( $within ) > 0 ) :
				$event_args['radius'] = $within;
				$event_args['latitude'] = $coords->lat;
				$event_args['longitude'] = $coords->lng;
			endif;
			
			$events = bpe_get_events( apply_filters( 'mapo_advanced_map_event_args', $event_args ) );
		endif;
			
		$show_events = true;
	endif;
	
	?>
	<div id="usermap-nav">
		<form id="membermap-nav-form" method="get" action="" class="standard-form">
			<input type="hidden" name="check" value="1" />
			
			<div class="mapnav-section mapnav-move">
				<span class="mapnav-title"><?php _e( 'Show:', 'mapo' ) ?></span>
				<label><input type="checkbox"<?php if( $all == 1 ) echo ' checked="checked"' ?> id="all" name="all" value="1" /> <?php _e( 'All', 'mapo' ) ?></label>

				<?php if( $show_groups ) : ?>
					<label><input type="checkbox"<?php if( $gr == 1 || ! $check ) echo ' checked="checked"' ?> id="show_groups" name="show_groups" value="1" /> <?php _e( 'Groups', 'mapo' ) ?></label>
				<?php endif; ?>
				
				<?php if( $show_members ) : ?>
					<label><input type="checkbox"<?php if( $me == 1 || ! $check ) echo ' checked="checked"' ?> id="show_members" name="show_members" value="1" /> <?php _e( 'Members', 'mapo' ) ?></label>
				<?php endif; ?>
				
				<?php if( $show_events ) : ?>
					<label><input type="checkbox"<?php if( $ev == 1 || ! $check ) echo ' checked="checked"' ?> id="show_events" name="show_events" value="1" /> <?php _e( 'Events', 'mapo' ) ?></label>
				<?php endif; ?>

				<?php do_action( 'mapo_show_extra_map_nav ', $dist_sql, $order_by, $check ) ?>
			</div>
						
			<div class="mapnav-section">
				<span class="mapnav-title"><?php _e( 'Within:', 'mapo' ) ?></span>
				<select id="within" name="within">
					<option value=""></option>
					<?php foreach( $mapo->config->prox_options as $prox ) : ?>
			            <option<?php if( $within == $prox || ! $within && $prox == $mapo->options->def_within ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $prox ) ?>"><?php echo $prox .' '. $mapo->options->system ?></option>
					<?php endforeach; ?>
	
	           		<?php do_action( 'mapo_distance_drop_down', $dist, $cookie, $context ) // deprecated, use mapo_prox_options filter instead ?>
				</select>
			</div>			
			
			<div class="mapnav-section<?php if( $bp->loggedin_user->has_location && ! $of ) : ?> mapnav-move<?php endif; ?>">
				<span class="mapnav-title"><?php _e( 'Of:', 'mapo' ) ?></span>
				<span id="of_wrapper">
					<?php if( $bp->loggedin_user->has_location && ! $of ) : ?>
						<span id="of_location">
							<?php _e( 'My Location', 'mapo' ) ?>
						</span>
					<?php else : ?>
						<input type="text" id="of" name="of" value="<?php echo esc_attr( $of ) ?>" />
					<?php endif; ?>
				</span>
				
				<?php if( $bp->loggedin_user->has_location && ! $of ) : ?>
					<span id="change-wrapper">(<a id="change-loc" href="#"><?php _e( 'Change', 'mapo' ) ?></a>)</span>
				<?php endif; ?>
			</div>			
			
			<div class="mapnav-section last-section">
				<input type="submit" value="<?php _e( 'Submit', 'mapo' ) ?>" />
			</div>
		</form>
	</div>
	
	<div class="map-wrapper"><div id="useroverviewmap<?php echo $shabuCounter ?>" class="map-holder enhanced-map"></div></div>
	<script type="text/javascript">
	var infoWindow<?php echo $shabuCounter ?>; 
	var markers<?php echo $shabuCounter ?> = [];
	var map<?php echo $shabuCounter ?>;
	
	<?php do_action( 'mapo_enhanced_map_global_js_vars', $shabuCounter ); ?>
	
	<?php if( $show_members ) : ?>
	var users<?php echo $shabuCounter ?> = [<?php
	$locations = array();
	foreach( $users as $key => $val )
	{
		if( $val->user_status == 2 )
			continue;
		
		if( empty( $mapo->options->public_location ) )
			if( ! mapo_location_is_viewable( $val->user_id, $val->privacy ) )
				continue;
			
		$avatar = bp_core_fetch_avatar( array( 'item_id' => $val->user_id, 'object' => 'user', 'type' => 'full', 'alt' => '', 'css_id' => 'avatar-'. $val->user_id, 'class' => 'avatar', 'width' => 100, 'height' => 100 ) );
		$avatar = str_replace( "'", '"', $avatar );
		
		$map_content = $avatar ."<div class=\"clear\"></div><strong><a href=\"". bp_core_get_user_domain( $val->user_id ) ."/\">". esc_js( $val->user_nicename ) ."</a></strong>";
			
		$locations[] = "['". esc_js( $val->user_nicename ) ."', ". $val->lat .", ". $val->lng .", '<div class=\"map-content\">". apply_filters( 'mapo_member_map_content', $map_content, $val ) ."</div>']";
		
	}
	echo implode( ',', $locations );
	?>];
	<?php endif; ?>
	
	<?php if( $show_groups ) : ?>
	var groups<?php echo $shabuCounter ?> = [<?php
	$locations = array();
	foreach( $groups as $key => $val )
	{
		if( $val->status == 'hidden' )
			continue;
			
		$avatar = bp_core_fetch_avatar( array( 'item_id' => $val->id, 'object' => 'group', 'type' => 'full', 'avatar_dir' => 'group-avatars', 'alt' => '', 'css_id' => 'avatar-'. $val->id, 'class' => 'avatar', 'width' => 100, 'height' => 100 ) );
		$avatar = str_replace( "'", '"', $avatar );
		$address = (object) unserialize( $val->address );
			
		$locations[] = "['". esc_js( $val->name ) ."', ". $val->lat .", ". $val->lng .", '<div class=\"map-content\">". $avatar ."<p style=\"float:left;padding-left:15px;\"><strong><a href=\"". bp_get_root_domain() ."/". $bp->groups->root_slug ."/". $val->slug ."/\">". esc_js( $val->name ) ."</a></strong><br />". esc_js( $address->street ) ."<br />". esc_js( $address->postcode ) ."<br />". esc_js( $address->city ) ."<br />". esc_js( $address->country ) ."</p><div style=\"clear:both;\">". esc_js( strip_tags( $val->description ) ) ."</div></div>']";
	}
	echo implode( ',', $locations );
	?>];
	<?php endif; ?>

	<?php if( $show_events ) : ?>
	var events<?php echo $shabuCounter ?> = [<?php 
	$locations = array();
	foreach( (array)$events['events'] as $key => $event )
	{
		$group_data = '';
		if( ! empty( $event->group_name ) )
			$group_data = '<br /><strong>'. __( 'Group', 'events' ) .':</strong> <a href="'. bpe_event_get_group_permalink( $event ) .'">'. bpe_event_get_group_name( $event ) .'</a>';

		$start_end = '';
        if( bpe_is_all_day_event( $event ) ) :
			$start_end .= sprintf( __( '<strong>Start:</strong> <span class="dtstart">%s</span> (all day event)<br />', 'events' ), bpe_get_event_start_date( $event ) );
            if( bpe_get_event_start_date( $event ) != bpe_get_event_end_date( $event ) ) :
				$start_end .= sprintf( __( '<strong>End:</strong> <span class="dtend">%s</span> (all day event)<br />', 'events' ), bpe_get_event_end_date( $event ) );
            endif;
        else :
			$start_end .= sprintf( __( '<strong>Start:</strong> <span class="dtstart">%s</span> at %s<br />', 'events' ), bpe_get_event_start_date( $event ), bpe_get_event_start_time( $event ) );
            $start_end .= sprintf( __( '<strong>End:</strong> <span class="dtend">%s</span> at %s<br />', 'events' ), bpe_get_event_end_date( $event ), bpe_get_event_end_time( $event ) );
        endif;

        if( bpe_has_event_timezone( $event ) )
			$timezone = sprintf( __( '<strong>Timezone:</strong> <span class="timezone">%s</span><br />', 'events' ), bpe_get_event_timezone( $event ) );

		$category = sprintf( __( '<strong>Category:</strong> <span class="category">%s</span><br />', 'events' ), bpe_get_event_category( $event ) );

        if( bpe_has_url( $event ) )
			$event_url = sprintf( __( '<br /><strong>Website:</strong> <span class="url">%s</span>', 'events' ), bpe_get_event_url( $event ) );
			
		$avatar = bpe_get_event_image_thumb( $event );
		$avatar = str_replace( "'", '"', $avatar );
		
		$desc = sprintf( __( '<div class="map-content">%s<p style="float:left;padding-left:15px;"><strong><a href="%s">%s</a></strong><br />%s %s %s<strong>Venue:</strong> %s %s %s</p><div style="padding-left:70px;float;left;clear:both;"><strong>Description:</strong> %s</div></div>', 'events' ), $avatar, bpe_get_event_link( $event ), esc_js( bpe_get_event_name( $event ) ), $start_end, $timezone, $category, bpe_get_event_location_link( $event ), $event_url, $group_data, esc_js( bpe_get_event_description_excerpt_raw( $event ) ) );
		$desc = apply_filters( 'bpe_event_coordinates_description', $desc, $event, $avatar );

		$image = ( $bpe->options->use_event_images === true ) ? bpe_get_event_image( array( 'event' => $event, 'type' => 'thumb', 'width' => 50, 'height' => 50, 'html' => false ) ) : EVENT_URLPATH .'css/images/event.png';
		
		$locations[] = "['". esc_js( bpe_get_event_name( $event ) ) ."', ". bpe_get_event_latitude( $event ) .", ". bpe_get_event_longitude( $event ) .", '". $desc ."', '". bpe_get_event_category_id( $event ) ."', '". $image ."']";
	}
		
	echo implode( ',', (array)$locations );
	?>];
	<?php endif; ?>
	
	<?php do_action( 'mapo_add_global_jsvar_enhanced_map', $shabuCounter ) ?>
	
	function userMapInitialize<?php echo $shabuCounter ?>() {
		var mapOptions<?php echo $shabuCounter ?> = {
			zoom: <?php echo $mapo->options->user_overview_zoom ?>,
			center: new google.maps.LatLng(<?php echo $mapo->options->map_location['lat'] ?>, <?php echo $mapo->options->map_location['lng'] ?>),
			mapTypeId: google.maps.MapTypeId.<?php echo $mapo->options->user_overview_type ?>
		}
		var bounds<?php echo $shabuCounter ?> = new google.maps.LatLngBounds();
		map<?php echo $shabuCounter ?> = new google.maps.Map(document.getElementById("useroverviewmap<?php echo $shabuCounter ?>"), mapOptions<?php echo $shabuCounter ?>);
		infoWindow<?php echo $shabuCounter ?> = new google.maps.InfoWindow(); 

		<?php if( $show_members ) : ?>
		for (var i = 0; i < users<?php echo $shabuCounter ?>.length; i++) {
			var loc<?php echo $shabuCounter ?> = users<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				map: map<?php echo $shabuCounter ?>,
				icon: '<?php echo apply_filters( 'mapo_members_map_marker', MAPO_URLPATH .'css/images/member.png' ) ?>',
				title: loc<?php echo $shabuCounter ?>[0]
			});

			google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(markerArg, cont) {
			  return function() {
				infoWindow<?php echo $shabuCounter ?>.setContent(cont);
				infoWindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>, markerArg);
			  };
			})(marker<?php echo $shabuCounter ?>, loc<?php echo $shabuCounter ?>[3]));

			markers<?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>);

			bounds<?php echo $shabuCounter ?>.extend(myLatLng<?php echo $shabuCounter ?>);
			map<?php echo $shabuCounter ?>.fitBounds(bounds<?php echo $shabuCounter ?>);
		}
		i = null;
		<?php endif; ?>
		<?php if( $show_groups ) : ?>
		for (var i = 0; i < groups<?php echo $shabuCounter ?>.length; i++) {
			var loc<?php echo $shabuCounter ?> = groups<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				map: map<?php echo $shabuCounter ?>,
				icon: '<?php echo apply_filters( 'mapo_group_map_marker', MAPO_URLPATH .'css/images/group.png' ) ?>',
				title: loc<?php echo $shabuCounter ?>[0]
			});

			google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(markerArg, cont) {
			  return function() {
				infoWindow<?php echo $shabuCounter ?>.setContent(cont);
				infoWindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>, markerArg);
			  };
			})(marker<?php echo $shabuCounter ?>, loc<?php echo $shabuCounter ?>[3]));

			markers<?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>);

			bounds<?php echo $shabuCounter ?>.extend(myLatLng<?php echo $shabuCounter ?>);
			map<?php echo $shabuCounter ?>.fitBounds(bounds<?php echo $shabuCounter ?>);
		}
		i = null;
		<?php endif; ?>
		<?php if( $show_events ) : ?>
		for (var i = 0; i < events<?php echo $shabuCounter ?>.length; i++) {
			var loc<?php echo $shabuCounter ?> = events<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				map: map<?php echo $shabuCounter ?>,
				icon: '<?php echo apply_filters( 'mapo_event_map_marker', MAPO_URLPATH .'css/images/event.png' ) ?>',
				title: loc<?php echo $shabuCounter ?>[0]
			});

			google.maps.event.addListener(marker<?php echo $shabuCounter ?>, 'click', (function(markerArg, cont) {
			  return function() {
				infoWindow<?php echo $shabuCounter ?>.setContent(cont);
				infoWindow<?php echo $shabuCounter ?>.open(map<?php echo $shabuCounter ?>, markerArg);
			  };
			})(marker<?php echo $shabuCounter ?>, loc<?php echo $shabuCounter ?>[3]));

			markers<?php echo $shabuCounter ?>.push(marker<?php echo $shabuCounter ?>);

			bounds<?php echo $shabuCounter ?>.extend(myLatLng<?php echo $shabuCounter ?>);
			map<?php echo $shabuCounter ?>.fitBounds(bounds<?php echo $shabuCounter ?>);
		}
		i = null;
		<?php endif; ?>
		
		<?php do_action( 'mapo_add_enhanced_map_loop', $shabuCounter ) ?>
		
		var markerCluster<?php echo $shabuCounter ?> = new MarkerClusterer(map<?php echo $shabuCounter ?>, markers<?php echo $shabuCounter ?>, { maxZoom: 12, gridSize: 50 });
	}
	
	jQuery(document).ready( function() {
		jQuery('#change-loc').click( function() {
			jQuery('#of_location').fadeOut( 700, function(){
				jQuery(this).parent().parent('.mapnav-section').removeClass('mapnav-move');
				jQuery('#of_location').remove();
				jQuery('#change-wrapper').hide();
				jQuery('#of_wrapper').append('<input type="text" id="of" name="of" value="" />');
			});
			return false;
		});
		
		userMapInitialize<?php echo $shabuCounter ?>();
	});
	</script>
	<?php
	
	$shabuCounter++;
}
add_action( 'bp_before_directory_members', 'mapo_enhanced_members_map' );

/**
 * Check access
 * @since 1.0
 *
 * public = 0 | only self
 * public = 1 | friends
 * public = 2 | logged in users
 * public = 3 | everybody
 */
function mapo_restrict_route_access( $route )
{
	global $bp, $mapo;
	
	if( is_super_admin() )
		return false;

	if( $bp->loggedin_user->id == $route->user_id )
		return false;
	
	switch( $route->public ) {
		case 0 :
			if( $bp->loggedin_user->id != $route->user_id )
				return true;
			break;

		case 1 :
			if( ! in_array( $bp->loggedin_user->id, (array) friends_get_friend_user_ids( $route->user_id ) ) )
				return true;
			break;

		case 2 :
			if( ! is_user_logged_in() )
				return true;
			break;

		case 3 :
			break;
			
		default :
			return true;
			break;
	}
	
	return false;
}

/**
 * Get the coordinates from a Google map location
 * (lat, lng)
 * @since 1.0
 */
function mapo_return_coords( $coords )
{
	$coords = trim( $coords, '()' );
	$coords = explode( ', ', $coords );
	
	return array( 'lat' => $coords[0], 'lng' => $coords[1] );
}

/**
 * Add some translatable js vars
 * @since 1.0
 */
function mapo_add_js_vars()
{
	?>
	<script type="text/javascript">
    var mapoLink = '<?php echo MAPO_URLPATH .'css/images/' ?>';
    var mapoWaypoint = '<?php _e( 'Waypoint', 'mapo' ) ?>';
    var mapoWpTitle = '<?php _e( 'Waypoint Title', 'mapo' ) ?>';
    var mapoWpDesc = '<?php _e( 'Waypoint Description', 'mapo' ) ?>';
    </script>
    <?php
}
add_action( 'wp_footer', 'mapo_add_js_vars' );

/**
* Display the navigation on single view
* @since 1.0
*/
function mapo_single_navigation()
{
	$prev_route = mapo_get_previous_route_link();
	$next_route = mapo_get_next_route_link();

	if( ! empty( $prev_route ) || ! empty( $next_route ) ) : ?>
	
		<div class="single-nav">
			<div class="previous-route">
				<?php echo $prev_route ?>
			</div>
	
			<div class="next-route">
				<?php echo $next_route ?>
			</div>
		</div>
	
	<?php endif;	
}

/**
* Add the edit js
* @since 1.0
*/
function mapo_add_edit_js()
{
	?>
	<script type="text/javascript">
    var map = null;
    var polyLine;
    var tmpPolyLine;
    var order = [];
    var markers = [];
    var vmarkers = [];
	var idarray = [];
    var g = google.maps;
    
    var initMap = function(mapHolder) {
        markers = [];
        vmarkers = [];
        var mapOptions = {
            zoom: <?php mapo_routes_zoom() ?>,
            center: new g.LatLng(<?php mapo_routes_default_lat() ?>, <?php mapo_routes_default_lng() ?>), 
            mapTypeId: g.MapTypeId.<?php mapo_routes_type() ?>,
            draggableCursor: 'auto',
            draggingCursor: 'move',
            disableDoubleClickZoom: true
        };
        map = new g.Map(document.getElementById(mapHolder), mapOptions);
        
        g.event.addListener(map, "click", mapLeftClick);
        g.event.addListener(map, 'zoom_changed', function() {
            var zoom = map.getZoom(); 
            jQuery('input#zoom').empty().val(zoom);
        });
        g.event.addListener(map, 'maptypeid_changed', function() {
            var type = map.getMapTypeId(); ;
            jQuery('input#type').empty().val(type);
        });
        g.event.addListener(map, 'center_changed', function() {
            var center = map.getCenter();
            jQuery('input#default_coords').empty().val(center);
        });
        mapHolder = null;
        mapOptions = null;
    };

    var initPolyline = function() {
        var polyOptions = {
            strokeColor: "#3355FF",
            strokeOpacity: 0.8,
            strokeWeight: 4
        };
        var tmpPolyOptions = {
            strokeColor: "#3355FF",
            strokeOpacity: 0.4,
            strokeWeight: 4
        };
        polyLine = new g.Polyline(polyOptions);
        polyLine.setMap(map);
        tmpPolyLine = new g.Polyline(tmpPolyOptions);
        tmpPolyLine.setMap(map);
        
        var pathCoordinates = [<?php mapo_routes_waypoints_js( false, 'g' ) ?>];
        var pathInfo = [<?php mapo_routes_waypoints_infowindow() ?>];
        for(var t = 0; t < pathCoordinates.length; t++) {
            var info = pathInfo[t];
            var marker = mapManualLeftClick(pathCoordinates[t],info[0],info[1]);
        }
        marker = null;t = null;info = null;
    };
    
    var mapDefAddWaypoint = function(id,pos,title,desc) {
        var wp = '<div id="waypoint-'+ id +'" class="waypoint-hide">';
        wp += '<input type="hidden" id="wplatlng-'+ id +'" name="waypoint['+ id +'][latlng]" value="'+ pos +'" />';
        wp += '<label for="wptitle-'+ id +'">'+ mapoWpTitle +' #'+ id +'</label>';
        wp += '<input type="text" id="wptitle-'+ id +'" name="waypoint['+ id +'][title]" id="" value="'+ title +'" />';
        wp += '<label for="wpdesc-'+ id +'">'+ mapoWpDesc +' #'+ id +'</label>';
        wp += '<textarea id="wpdesc-'+ id +'" name="waypoint['+ id +'][description]">'+ desc +'</textarea>';
        wp += '</div>';
        return wp;
    };

    var mapAddWaypoint = function(id,pos) {
        var wp = '<div id="waypoint-'+ id +'">';
        wp += '<input type="hidden" id="wplatlng-'+ id +'" name="waypoint['+ id +'][latlng]" value="'+ pos +'" />';
        wp += '<label for="wptitle-'+ id +'">'+ mapoWpTitle +' #'+ id +'</label>';
        wp += '<input type="text" id="wptitle-'+ id +'" name="waypoint['+ id +'][title]" id="" value="" />';
        wp += '<label for="wpdesc-'+ id +'">'+ mapoWpDesc +' #'+ id +'</label>';
        wp += '<textarea id="wpdesc-'+ id +'" name="waypoint['+ id +'][description]"></textarea>';
        wp += '</div>';
        return wp;
    };

	var setWaypontsHidden = function() {
		for (var w = 0; w < idarray.length; w++) {
			jQuery('#waypoint-'+ idarray[w] ).addClass('waypoint-hide');
		};
		w = null;
	};

    var mapManualLeftClick = function(mapoManuLatLng,title,desc) {
        if (mapoManuLatLng) {
            var marker = createMarker(mapoManuLatLng);
            markers.push(marker);
            var mid = marker.__gm_id;
            var p = marker.getPosition();
            var wayp = mapDefAddWaypoint(mid,p,title,desc);
            var orderCoords = jQuery('input#order_coords').val();
            orderCoords = String(orderCoords);
            orderCoords += String(mid +',');
            jQuery('input#order_coords').val(orderCoords);
            marker.setTitle(mapoWaypoint +' #'+ mid);
            jQuery('#mapology-waypoints').prepend(wayp)
            if (markers.length != 1) {
                var vmarker = createVMarker(mapoManuLatLng);
                vmarkers.push(vmarker);
                vmarker = null;
            }
            var path = polyLine.getPath();
            path.push(mapoManuLatLng);
            marker = null; mid = null; l = null; p = null; wayp = null; orderCoords = null;
        }
        mapoManuLatLng = null;title = null;desc = null;
    };
    
    var mapLeftClick = function(event) {
        if (event.latLng) {
            var marker = createMarker(event.latLng);
            markers.push(marker);
            var mid = marker.__gm_id;
            var p = marker.getPosition();
            var wayp = mapAddWaypoint(mid,p);
			setWaypontsHidden();
			idarray.push(mid);
            var orderCoords = jQuery('input#order_coords').val();
            orderCoords = String(orderCoords);
            orderCoords += String(mid +',');
            jQuery('input#order_coords').val(orderCoords);
            marker.setTitle(mapoWaypoint +' #'+ mid);
            jQuery('#mapology-waypoints').prepend(wayp)
            if (markers.length != 1) {
                var vmarker = createVMarker(event.latLng);
                vmarkers.push(vmarker);
                vmarker = null;
            }
            var path = polyLine.getPath();
            path.push(event.latLng);
            marker = null; mid = null; l = null; p = null; wayp = null; orderCoords = null;
        }
        event = null;
    };
    
    var createMarker = function(point) {
        var imageNormal = new g.MarkerImage(
            mapoLink +"circle.png",
            new g.Size(16, 16),
            new g.Point(0, 0),
            new g.Point(8, 8)
        );
        var imageHover = new g.MarkerImage(
            mapoLink +"circle_over.png",
            new g.Size(16, 16),
            new g.Point(0, 0),
            new g.Point(8, 8)
        );
        var marker = new g.Marker({
            position: point,
            map: map,
            icon: imageNormal,
            draggable: true
        });
        g.event.addListener(marker, "mouseover", function() {
            marker.setIcon(imageHover);
        });
        g.event.addListener(marker, "mouseout", function() {
            marker.setIcon(imageNormal);
        });
        g.event.addListener(marker, "drag", function() {
            for (var m = 0; m < markers.length; m++) {
                if (markers[m] == marker) {
                    var pos = marker.getPosition();
                    var mid = marker.__gm_id;
                    polyLine.getPath().setAt(m, pos);
                    jQuery('input#wplatlng-'+ mid ).empty().val(pos);
                    moveVMarker(m);
                    break;
                }
            }
            m = null;
        });
        g.event.addListener(marker, "click", function() {
            for (var m = 0; m < markers.length; m++) {
                if (markers[m] == marker) {
                    var mid = marker.__gm_id;
                    jQuery('#waypoint-'+ mid ).remove();
                    var c = jQuery('input#order_coords').val();
                    c = c.replace(mid +',','');
                    jQuery('input#order_coords').val(c);
                    marker.setMap(null);
                    markers.splice(m, 1);
                    polyLine.getPath().removeAt(m);
                    removeVMarkers(m);
                    break;
                }
            }
            m = null;
        });
        return marker;
    };
    
    var createVMarker = function(point) {
        var prevpoint = markers[markers.length-2].getPosition();
        var imageNormal = new g.MarkerImage(
            mapoLink +"circle_transparent.png",
            new g.Size(16, 16),
            new g.Point(0, 0),
            new g.Point(8, 8)
        );
        var imageHover = new g.MarkerImage(
            mapoLink +"circle_transparent_over.png",
            new g.Size(16, 16),
            new g.Point(0, 0),
            new g.Point(8, 8)
        );
        var marker = new g.Marker({
            position: new g.LatLng(
                point.lat() - (0.5 * (point.lat() - prevpoint.lat())),
                point.lng() - (0.5 * (point.lng() - prevpoint.lng()))
            ),
            map: map,
            icon: imageNormal,
            draggable: true
        });
        g.event.addListener(marker, "mouseover", function() {
            marker.setIcon(imageHover);
        });
        g.event.addListener(marker, "mouseout", function() {
            marker.setIcon(imageNormal);
        });
        g.event.addListener(marker, "dragstart", function() {
            for (var m = 0; m < vmarkers.length; m++) {
                if (vmarkers[m] == marker) {
                    var tmpPath = tmpPolyLine.getPath();
                    tmpPath.push(markers[m].getPosition());
                    tmpPath.push(vmarkers[m].getPosition());
                    tmpPath.push(markers[m+1].getPosition());
                    break;
                }
            }
            m = null;
        });
        g.event.addListener(marker, "drag", function() {
            for (var m = 0; m < vmarkers.length; m++) {
                if (vmarkers[m] == marker) {
                    tmpPolyLine.getPath().setAt(1, marker.getPosition());
                    break;
                }
            }
            m = null;
        });
        g.event.addListener(marker, "dragend", function() {
            for (var m = 0; m < vmarkers.length; m++) {
                if (vmarkers[m] == marker) {
                    var newpos = marker.getPosition();
                    var startMarkerPos = markers[m].getPosition();
                    var firstVPos = new g.LatLng(
                        newpos.lat() - (0.5 * (newpos.lat() - startMarkerPos.lat())),
                        newpos.lng() - (0.5 * (newpos.lng() - startMarkerPos.lng()))
                    );
                    var endMarkerPos = markers[m+1].getPosition();
                    var secondVPos = new g.LatLng(
                        newpos.lat() - (0.5 * (newpos.lat() - endMarkerPos.lat())),
                        newpos.lng() - (0.5 * (newpos.lng() - endMarkerPos.lng()))
                    );
                    var newVMarker = createVMarker(secondVPos);
                    newVMarker.setPosition(secondVPos);
                    var newMarker = createMarker(newpos);
                    markers.splice(m+1, 0, newMarker);
                    var mid = newMarker.__gm_id;
                    var wayp = mapAddWaypoint(mid,newpos);
					setWaypontsHidden();
					idarray.push(mid);
                    var wp = String();
                    for (var s = 0; s < markers.length; s++) {
                        wp += String(markers[s].__gm_id +',');
                    }
                    jQuery('input#order_coords').empty().val(wp);
                    newMarker.setTitle(mapoWaypoint +' #'+ mid);
                    jQuery('#mapology-waypoints').prepend(wayp)
                    polyLine.getPath().insertAt(m+1, newpos);
                    marker.setPosition(firstVPos);
                    vmarkers.splice(m+1, 0, newVMarker);
                    tmpPolyLine.getPath().removeAt(2);
                    tmpPolyLine.getPath().removeAt(1);
                    tmpPolyLine.getPath().removeAt(0);
                    newpos = null; startMarkerPos = null; firstVPos = null;
                    endMarkerPos = null; secondVPos = null;	newVMarker = null;
                    newMarker = null; mid = null; l = null; wayp = null;
                    break;
                }
            }
        });
        return marker;
    };
    
    var moveVMarker = function(index) {
        var newpos = markers[index].getPosition();
        if (index != 0) {
            var prevpos = markers[index-1].getPosition();
            vmarkers[index-1].setPosition(new g.LatLng(
                newpos.lat() - (0.5 * (newpos.lat() - prevpos.lat())),
                newpos.lng() - (0.5 * (newpos.lng() - prevpos.lng()))
            ));
            prevpos = null;
        }
        if (index != markers.length - 1) {
            var nextpos = markers[index+1].getPosition();
            vmarkers[index].setPosition(new g.LatLng(
                newpos.lat() - (0.5 * (newpos.lat() - nextpos.lat())), 
                newpos.lng() - (0.5 * (newpos.lng() - nextpos.lng()))
            ));
            nextpos = null;
        }
        newpos = null;
        index = null;
    };
    
    var removeVMarkers = function(index) {
        if (markers.length > 0) {
            if (index != markers.length) {
                vmarkers[index].setMap(null);
                vmarkers.splice(index, 1);
            } else {
                vmarkers[index-1].setMap(null);
                vmarkers.splice(index-1, 1);
            }
        }
        if (index != 0 && index != markers.length) {
            var prevpos = markers[index-1].getPosition();
            var newpos = markers[index].getPosition();
            vmarkers[index-1].setPosition(new g.LatLng(
                newpos.lat() - (0.5 * (newpos.lat() - prevpos.lat())),
                newpos.lng() - (0.5 * (newpos.lng() - prevpos.lng()))
            ));
            prevpos = null;
            newpos = null;
        }
        index = null;
    };
    
    jQuery(document).ready(function() {
        initMap('mapology-create-map');
        initPolyline();
		jQuery('#wpshow').click(function() {
			jQuery('.waypoint-hide').addClass('wp-show');
			return false;
		});
		jQuery('#wphide').click(function() {
			jQuery('.waypoint-hide').removeClass('wp-show');
			return false;
		});
        jQuery(function() {
            var dates = jQuery( "#start_date,#end_date" ).datepicker({
                firstDay: 1,
                changeMonth: false,
                changeYear: false,
                dateFormat: "yy-mm-dd",
                onSelect: function( selectedDate ) {
                    var option = this.id == "start_date" ? "minDate" : "maxDate", instance = jQuery(this).data("datepicker");
                    date = jQuery.datepicker.parseDate( instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings );
                    dates.not(this).datepicker( "option", option, date );
                }
            });
        });
    });
    </script>
    <?php
}
add_action( 'mapo_edit_single_js', 'mapo_add_edit_js' );

/**
* Create color codes
* @since 1.0
*/
function mapo_generate_color_code( $id )
{
  $cc = dechex( crc32( $id ) );
  $cc = substr( $cc, 0, 6 );
  
  return '#'. $cc;
}

/**
* Change per_page value based on view
* @since 1.0
*/
function mapo_get_view_per_page()
{
	return apply_filters( 'mapo_get_view_per_page', ( ( $_COOKIE['bpe_view_style'] == 'grid'  ) ? 25 : 10 ), $_COOKIE['bpe_view_style'] );
}

/**
* Show the view style links
* @since 1.0
*/
function mapo_view_link( $type )
{
	global $bp;
	
	echo bp_get_root_domain() .'/'. $bp->mapology->root_slug .'/view/'. $type .'/';
}

/**
* Checks for grid style
* @since 1.0
*/
function mapo_grid_style()
{
	if( $_COOKIE['bpe_view_style'] == 'grid' )
		return true;
		
	return false;
}

/**
* Adds a view style class
* @since 1.0
*/
function mapo_view_class( $type )
{
	if( $_COOKIE['bpe_view_style'] == $type || ! $_COOKIE['bpe_view_style'] && $type == 'list' )
		echo ' active-view';
	else
		echo ' inactive-view';
}

/**
* Remove danish characters
* @since 1.1.1
*/
function mapo_remove_accents( $string ) 
{
	$chars = array( '%c3%b8', '%c3%a6' );
	$repl = array( 'o', 'ae' );
	
	$string = str_replace( $chars, $repl, $string );

	return $string;
}

/**
* Return only a certain number of waypoints
* max waypoints ~ 40 (-2 for $first and $last)
* @since 1.1.2
*/
function mapo_get_static_waypoints( $points = array() )
{
	// get the first and the last
	if( ! is_array( $points ) )
		$points = array( $points );
	
	$first = array_shift( $points );
	$last = array_pop( $points );

	$count = count( $points );
	
	if( $count <= 38 )
		$wp = join( '|', (array)$points );
	
	elseif( $count >= 39 )
	{
		$skip = ceil( $count / 38 );
		
		for( $i = 0; $i <= $count; $i = $i + $skip )
		{
			if( ! empty( $points[$i] ) )
				$wp[] = $points[$i];
		}
		
		$wp = join( '|', (array)$wp );
	}
	
	if( $wp )
		$wp = '|'. $wp;
	
	return  $first . $wp .'|'. $last;
}

/**
* Return false if forums are disabled
* @since 1.0
*/
if( ! function_exists('bp_forums_is_installed_correctly') ) :
	function bp_forums_is_installed_correctly()	{ return false;	}
endif;

/**
* Return false if activity stream is disabled
* @since 1.0
*/
if( ! function_exists('bp_get_activity_id') ) :
	function bp_get_activity_id() {	return false; }
endif;

/**
* Return false if activity stream is disabled
* @since 1.0
*/
if( ! function_exists('bp_activity_get_meta') ) :
	function bp_activity_get_meta() { return false;	}
endif;

/**
* Return false if activity stream is disabled
* @since 1.0
*/
if( ! function_exists('bp_activity_update_meta') ) :
	function bp_activity_update_meta() { return false; }
endif;

/**
* Single WP compatibility
* @since 1.1.2
*/
if ( !function_exists( 'delete_blog_option' ) ) :
	function delete_blog_option( $blog_id, $option_name, $option_value = false ) {
		return delete_option( $option_name, $option_value );
	}
endif
?>