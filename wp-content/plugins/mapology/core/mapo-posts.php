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
 * Add the georss namespace
 * @since 1.0
 */
function mapo_add_xmlns()
{
	echo 'xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml"';
}
add_action( 'rss2_ns', 'mapo_add_xmlns' );
add_action( 'atom_ns', 'mapo_add_xmlns' );
add_action( 'rdf_ns', 'mapo_add_xmlns' );

/**
 * Add post coordinates
 * @since 1.0
 */
function mapo_add_latlng_feeds()
{
	if( $coords = mapo_get_post_coords() )
		echo '<georss:where><gml:Point><gml:pos>'. $coords['lat'] .' '. $coords['lng'] .'</gml:pos></gml:Point></georss:where>';
}
add_action( 'rss2_item', 'mapo_add_latlng_feeds' ); 
add_action( 'rss_item', 'mapo_add_latlng_feeds' ); 
add_action( 'rdf_item', 'mapo_add_latlng_feeds' ); 
add_action( 'atom_entry', 'mapo_add_latlng_feeds' ); 

/**
 * Add custom fields back in
 * @since 1.0
 */
function mapo_add_meta_box()
{
	wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false' );
	add_meta_box( 'mapo-coordinates-post', __( 'Post Coordinates', 'mapo' ), 'mapo_meta_box_content', 'post', 'advanced', 'high' );
	add_meta_box( 'mapo-coordinates-page', __( 'Post Coordinates', 'mapo' ), 'mapo_meta_box_content', 'page', 'advanced', 'high' );
	add_meta_box( 'mapo-coordinates-location', __( 'Post Coordinates', 'mapo' ), 'mapo_meta_box_content', 'location', 'advanced', 'high' );
}
add_action( 'admin_menu', 'mapo_add_meta_box' );

/**
 * Post map shortcode
 * width can be a number or 100%
 * @since 1.0
 */
function mapo_post_map_shortcode( $atts, $desc = null )
{
	extract( shortcode_atts( array(
		'width' => '100%',
		'height' => 300,
		'type' => 'HYBRID',
		'zoom' => 2,
		'float' => '',
		'title' => __( 'Post Map', 'mapo' ),
		'nav' => 'true',
		'typenav' => 'false',
		'scale' => 'false'
	), $atts ) );
	
	$map = mapo_get_display_post_map( false, $width, $height, $type, $zoom, $float, $title, $desc, $nav, $typenav, $scale );

	return $map;
}
add_shortcode( 'mapology', 'mapo_post_map_shortcode' );

/**
 * Post map shortcode
 * width can be a number or 100%
 * @since 1.0
 */
function mapo_route_shortcode( $atts, $desc = null )
{
	extract( shortcode_atts( array(
		'id' => '',
		'width' => '',
		'height' => '',
		'type' => '',
		'zoom' => '',
		'float' => '',
		'nav' => '',
		'typenav' => '',
		'scale' => '',
		'title' => 'true',
		'desc' => 'true',
		'date' => 'true'
	), $atts ) );
	
	$map = mapo_get_display_route_map( $id, $title, $desc, $date, $width, $height, $type, $zoom, $float, $nav, $typenav, $scale );

	return $map;
}
if( mapo_are_routes_enabled() )
	add_shortcode( 'routes', 'mapo_route_shortcode' );

/**
 * Template tag for a route
 * @since 1.0
 */
function mapo_display_route_map( $id = false, $title = 'true', $desc = 'true', $date = 'true', $width = '', $height = '', $type = '', $zoom = '', $float = '', $nav = '', $typenav = '', $scale = ''  )
{
	echo mapo_get_display_route_map( $id, $title, $desc, $date, $width, $height, $type, $zoom, $float, $nav, $typenav, $scale );
}
	function mapo_get_display_route_map( $id = false, $title = 'true', $desc = 'true', $date = 'true', $width = '', $height = '', $type = '', $zoom = '', $float = '', $nav = '', $typenav = '', $scale = ''  )
	{
		global $routeCounter, $mapo;
		
		if( $mapo->options->enable_routes === false )
			return false;
		
		if( ! isset( $routeCounter ) )
			$routeCounter = 1;

		if( ! $id )
			return false;
			
		$routes = mapo_get_routes( array( 'ids' => $id, 'per_page' => false, 'page' => false ) );
		$route = $routes['routes'][0];
		
		$rid = $route->id .'_'. $routeCounter;
		
		if( empty( $route ) )
		{
			return '<div class="private-route"><p>'. __( 'Sorry, but you are not allowed to view this map according to its privacy setting.', 'mapo' ) .'</p></div>';
		}
		else
		{
			if( in_array( $float, array( 'right', 'left' ) ) )
				$class = ' rwwrap-'. $float;
			
			if( ! empty( $width ) )
			{
				$mapwidth = 'width:'. $width .'px;';
				$wrapwidth = 'width:'. $width + 10 .'px;';
			}
	
			if( ! empty( $height ) )
			{
				$mapheight = 'height:'. $height .'px;';
				$wrapheight = 'height:'. $height + 10 .'px;';
			}
			
			if( ! empty( $mapwidth ) || ! empty( $mapheight ) )
			{
				$mapstyle = ' style="'. $mapwidth . $mapheight .'"';
				$wrapstyle = ' style="'. $wrapwidth . $wrapheight .'"';
			}
	
			$map = '<div class="route-map-wrapper'. $class .'"'. $wrapstyle .'>';
			if( $title == 'true' )
				$map .= '<div class="route-map-title"><a href="'. mapo_get_routes_link( $route, 'routes', true ) .'">'. mapo_get_routes_name( $route ) .'</a></div>';
			if( $desc == 'true' )
				$map .= '<div class="route-map-description">'. mapo_get_routes_description( $route ) .'</div>';
			if( $date == 'true' )
				$map .= '<div class="route-map-date">'. mapo_get_routes_start_date( $route ) .' - '. mapo_get_routes_end_date( $route ) .'</div>';
			$map .= '<div id="route-map-'. $rid .'" class="route-map"'. $mapstyle .'></div>';
			$map .= '<div class="route-map-creator">'. sprintf( __( 'Created by %s', 'mapo' ), bp_core_get_userlink( $route->user_id ) ) .'</div>';
			$map .= mapo_get_single_route_js( $route, $type, $zoom, $nav, $typenav, $scale, $rid );
			$map .= '</div>';
			
			$routeCounter++;
			
			return apply_filters( 'mapo_get_display_route_map', $map );
		}
	}

/**
 * Display the post map
 * @since 1.0
 */
function mapo_display_post_map( $post_object = false, $width = '100%', $height = 300, $type = 'HYBRID', $zoom = 2, $float = '', $title = '', $desc = '', $nav = 'true', $typenav = 'false', $scale = 'false' )
{
	echo mapo_get_display_post_map( $post_object, $width, $height, $type, $zoom, $float, $title, $desc, $nav, $typenav, $scale );
}
	function mapo_get_display_post_map( $post_object = false, $width = '100%', $height = 300, $type = 'HYBRID', $zoom = 2, $float = '', $title = '', $desc = '', $nav = 'true', $typenav = 'false', $scale = 'false' )
	{
		global $post, $mapCounter;

		if( ! isset( $mapCounter ) )
			$mapCounter = 1;
		
		if( $post_object ) $post = $post_object;
		
		$coords = mapo_get_post_coords( $post );
		
		if( empty( $coords['lat'] ) || empty( $coords['lng'] ) )
			return false;
			
		$post_id = $post->ID .'_'. $mapCounter;
		
		$pos = ( in_array( $float, array( 'left', 'right' ) ) && $width != '100%' ) ? 'post-map-'. $float : '';
		$w = ( $width == '100%' || empty( $width ) ) ? '' : 'width:'. $width .'px';
		$nav = ( in_array( $nav, array( 'true', 'false' ) ) ) ? $nav : 'true';
		$typenav = ( in_array( $typenav, array( 'true', 'false' ) ) ) ? $typenav : 'false';
		$scale = ( in_array( $scale, array( 'true', 'false' ) ) ) ? $scale : 'false';
		
		$map = '<div id="mapo-post-map'. $post_id .'" class="post-map '. $pos .'" style="'. $w .';height:'. $height .'px;"></div>'."\n";
		$map .= '<script type="text/javascript">'."\n";
		$map .= 'function post_map_initialize'. $post_id .'() {'."\n";
			$map .= 'var coords'. $post_id .' = new google.maps.LatLng('. $coords['lat'] .', '. $coords['lng'] .');'."\n";
			$map .= 'var mapOptions'. $post_id .' = {'."\n";
				$map .= 'zoom: '. $zoom .','."\n";
				$map .= 'center: coords'. $post_id .','."\n";
				$map .= 'navigationControl: '. $nav .','."\n";
				$map .= 'mapTypeControl: '. $typenav .','."\n";
				$map .= 'scaleControl: '. $scale .','."\n";
				$map .= 'mapTypeId: google.maps.MapTypeId.'. strtoupper( $type ) ."\n";
			$map .= '};'."\n";
			$map .= 'var map'. $post_id .' = new google.maps.Map(document.getElementById("mapo-post-map'. $post_id .'"), mapOptions'. $post_id .');'."\n";
			if( ! empty( $desc ) )
			{
				$map .= 'var content'. $post_id .' = \''. esc_js( $desc ) .'\';'."\n";
				$map .= 'var infowindow'. $post_id .' = new google.maps.InfoWindow({content: content'. $post_id .'});'."\n";
			}
			$map .= 'var marker'. $post_id .' = new google.maps.Marker({'."\n";
				$map .= 'position: coords'. $post_id .','."\n";
				$map .= 'map: map'. $post_id .','."\n";
				if( ! empty( $title ) )
					$map .= 'title: \''. esc_js( $title ) .'\','."\n";
				$map .= 'icon: new google.maps.MarkerImage(\''. MAPO_URLPATH .'css/images/circle.png\', new google.maps.Size(16, 16), new google.maps.Point(0, 0), new google.maps.Point(8, 8))'."\n";
			$map .= '});'."\n";
			if( ! empty( $desc ) )
			{
				$map .= 'google.maps.event.addListener(marker'. $post_id .', \'click\', function() {'."\n";
					$map .= 'infowindow'. $post_id .'.open(map'. $post_id .',marker'. $post_id .');'."\n";
				$map .= '});'."\n";
			}
		$map .= '}'."\n";
		$map .= 'jQuery(document).ready( function() {'."\n";
			$map .= 'post_map_initialize'. $post_id .'();'."\n";
		$map .= '});'."\n";
		$map .= '</script>';
		
		$map = apply_filters( 'mapo_display_post_map', $map );
		
		$mapCounter++;
		
		return $map;
	}

/**
 * Get post coordinates
 * @since 1.0
 */
function mapo_get_post_coords( $post_object = false )
{
	global $post;
	
	if( $post_object ) $post = $post_object;
		
	$coords = get_post_meta( $post->ID, 'mapo_coords', true );
	
	return $coords;
}

/**
 * Content of the meta box
 * @since 1.0
 */
function mapo_meta_box_content()
{
	$coords = mapo_get_post_coords();
	?>
    <style type="text/css">
	.mapo_meta_coords .coords{width:45%;float:left;margin-bottom:7px;}
	.mapo_meta_coords .coords input{width:75%;}
	.mapo_meta_coords .lat{margin-right:5%;}
	.mapo_meta_coords .clear{clear:both;}
	.mapo_meta_coords #post_choose_coords{height:400px;}
	</style>
	<div class="mapo_meta_coords">
    	<div class="coords lat">
            <label for="post_lat"><?php _e( 'Latitude', 'mapo' ); ?></label>
            <input type="text" name="mapo_coords[lat]" id="post_lat" value="<?php echo $coords['lat'] ?>" />
    	</div>
        <div class="coords lng">
            <label for="post_lng"><?php _e( 'Longitude', 'mapo' ); ?></label>
            <input type="text" name="mapo_coords[lng]" id="post_lng" value="<?php echo $coords['lng'] ?>" />
        </div>
        <div class="clear"></div>
        <div id="post_choose_coords"></div>
	</div>
	<script type="text/javascript">
	var map;
	var mark;
	var markersArray = [];
	function def_map_initialize() {
		var latlng = new google.maps.LatLng(<?php echo ( empty( $coords['lat'] ) ) ? 5 : $coords['lat']; ?>, <?php echo ( empty( $coords['lng'] ) ) ? 30 : $coords['lng']; ?>);
		var mapOptions = {
			zoom: 2,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.HYBRID
		}
		map = new google.maps.Map(document.getElementById("post_choose_coords"), mapOptions);
		mark = new google.maps.Marker({
			position: latlng, 
			draggable: true,
			map: map
		});
		google.maps.event.addListener(map, 'click', function(event) {
			var coords = event.latLng;
			mark.setMap(null);
			placeMarker(coords);
			coords = String(coords);
			coords = coords.split(', ');
			jQuery('#post_lat').val(coords[0].substr( 1, coords[0].length ));
			jQuery('#post_lng').val(coords[1].substr( 0, coords[1].length - 1 ));
		});
		google.maps.event.addListener(mark, "dragend", function(event) {
			var pos = event.latLng;
			pos = String(pos);
			pos = pos.split(', ');
			jQuery('#post_lat').val(pos[0].substr( 1, pos[0].length ));
			jQuery('#post_lng').val(pos[1].substr( 0, pos[1].length - 1 ));
		});
	}
	function placeMarker(location) {
		deleteOverlays();
		var marker = new google.maps.Marker({
			position: location, 
			draggable: true,
			map: map
		});

		google.maps.event.addListener(marker, "dragend", function(event) {
			var pos = event.latLng;
			pos = String(pos);
			pos = pos.split(', ');
			
			jQuery('#post_lat').val(pos[0].substr( 1, pos[0].length ));
			jQuery('#post_lng').val(pos[1].substr( 0, pos[1].length - 1 ));
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
	jQuery(document).ready(function() {
		def_map_initialize();
	});
	</script>    
	<input type="hidden" name="mapo_meta_noncename" value="<?php echo wp_create_nonce( __FILE__ ) ?>" />
	<?php
}

/**
 * Save the extra information
 * @since 1.0
 */
function mapo_meta_box_save( $post_id )
{
	if( ! wp_verify_nonce( $_POST['mapo_meta_noncename'] , __FILE__ ) )
		return $post_id;
 
	$current_data = get_post_meta( $post_id, 'mapo_coords' );	
 
	$new_data = $_POST['mapo_coords'];

	mapo_meta_clean( $new_data );
 
	if( $current_data ) 
	{
		if( is_null( $new_data ) )
			delete_post_meta( $post_id, 'mapo_coords' );
		else
			update_post_meta( $post_id, 'mapo_coords', $new_data );
	}
	elseif( ! is_null( $new_data ) )
	{
		add_post_meta( $post_id, 'mapo_coords', $new_data, true );
	}
 
	return $post_id;
}
add_action( 'save_post', 'mapo_meta_box_save' );

/**
 * Check the post array
 * @since 1.0
 */
function mapo_meta_clean( &$arr )
{
	if( is_array( $arr ) )
	{
		foreach( $arr as $i => $v )
		{
			if( is_array( $arr[$i] ) ) 
			{
				list_meta_clean( $arr[$i] );
				if( ! count( $arr[$i] ) ) 
					unset( $arr[$i] );
			}
			else 
			{
				if( trim( $arr[$i] ) == '' ) 
					unset( $arr[$i] );
			}
		}
 
		if( ! count( $arr ) ) 
			$arr = null;
	}
}
?>