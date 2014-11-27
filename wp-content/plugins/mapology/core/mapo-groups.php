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

require( MAPO_ABSPATH .'core/mapo-groupinfo.php' );

/**
 * Get coordinates from all groups
 * @since 1.0
 */
function mapo_get_all_groups_coordinates()
{
	global $bp, $wpdb, $mapo, $shabuCounter;

	if( ! $shabuCounter )
		$shabuCounter = 1;

	$result = $wpdb->get_results( "
		SELECT c.lat, c.lng, c.group_id, g.id, g.name, g.description, g.status, g.slug, gm.meta_value as address 
		FROM {$mapo->tables->coords} c 
		RIGHT JOIN {$bp->groups->table_name} g 
		ON c.group_id = g.id 
		RIGHT JOIN {$bp->groups->table_name_groupmeta} gm 
		ON gm.group_id = c.group_id 
		AND gm.meta_key = 'group_address'
		WHERE c.group_id > 0
	" );

	?>
    <div class="map-wrapper"><div id="groupoverviewmap<?php echo $shabuCounter ?>" class="map-holder"></div></div>
	<script type="text/javascript">
	var infoWindow<?php echo $shabuCounter ?>; 
	var map<?php echo $shabuCounter ?>;
	var markers<?php echo $shabuCounter ?> = [];
	var locations<?php echo $shabuCounter ?> = [<?php foreach( $result as $key => $val ) {
			if( $val->status == 'hidden' )
				continue;
			
			$avatar = bp_core_fetch_avatar( array( 'item_id' => $val->id, 'object' => 'group', 'type' => 'full', 'avatar_dir' => 'group-avatars', 'alt' => '', 'css_id' => 'avatar-'. $val->id, 'class' => 'avatar', 'width' => 100, 'height' => 100 ) );
			$avatar = str_replace( "'", '"', $avatar );
			$address = (object) unserialize( $val->address );
			
			$locations[] = "['". esc_js( $val->name ) ."', ". $val->lat .", ". $val->lng .", '<div class=\"map-content\">". $avatar ."<p style=\"float:left;padding-left:15px;\"><strong><a href=\"". bp_get_root_domain() ."/". $bp->groups->root_slug ."/". $val->slug ."/\">". esc_js( $val->name ) ."</a></strong><br />". esc_js( $address->street ) ."<br />". esc_js( $address->postcode ) ."<br />". esc_js( $address->city ) ."<br />". esc_js( $address->country ) ."</p><div style=\"clear:both;\">". esc_js( $val->description ) ."</div></div>']";
		}
		echo implode( ',', (array)$locations );
		?>];

	function groupMapInitialize<?php echo $shabuCounter ?>() {
		var mapOptions<?php echo $shabuCounter ?> = {
			zoom: <?php echo $mapo->options->group_overview_zoom ?>,
			center: new google.maps.LatLng(<?php echo $mapo->options->map_location['lat'] ?>, <?php echo $mapo->options->map_location['lng'] ?>),
			mapTypeId: google.maps.MapTypeId.<?php echo $mapo->options->group_overview_type ?>
		}
		map<?php echo $shabuCounter ?> = new google.maps.Map(document.getElementById("groupoverviewmap<?php echo $shabuCounter ?>"), mapOptions<?php echo $shabuCounter ?>);
		infoWindow<?php echo $shabuCounter ?> = new google.maps.InfoWindow(); 
		
		for (var i = 0; i < locations<?php echo $shabuCounter ?>.length; i++) {
			
			var loc<?php echo $shabuCounter ?> = locations<?php echo $shabuCounter ?>[i];
			var myLatLng<?php echo $shabuCounter ?> = new google.maps.LatLng(loc<?php echo $shabuCounter ?>[1], loc<?php echo $shabuCounter ?>[2]);

			var marker<?php echo $shabuCounter ?> = new google.maps.Marker({
				position: myLatLng<?php echo $shabuCounter ?>,
				icon: '<?php echo apply_filters( 'mapo_members_group_marker', MAPO_URLPATH .'css/images/group.png' ) ?>',
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
		groupMapInitialize<?php echo $shabuCounter ?>();
	});
	</script>
	<?php
	
	$shabuCounter++;
}
add_action( 'bp_before_directory_groups_content', 'mapo_get_all_groups_coordinates' );

/**
 * Add group js vars to the page
 * @since 1.0
 */
function mapo_add_group_js_vars()
{
	global $bp, $mapo;
	
	if( empty( $bp->groups->current_group->id ) )
		return false;

	$coords = new MAPO_Coords( null, null, $bp->groups->current_group->id );
	if( ! empty( $coords ) )
	{
		?>
        <div id="gmap"></div>
		<script type="text/javascript">
		function gmap_initialize() {
			var gcoords = new google.maps.LatLng(<?php echo $coords->lat ?>, <?php echo $coords->lng ?>);
			var gmapOptions = {
				zoom: <?php echo $mapo->options->group_map_zoom ?>,
				center: gcoords,
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.<?php echo $mapo->options->group_map_type ?>
			};
			var gmap = new google.maps.Map(document.getElementById("gmap"), gmapOptions);
		
			var gmarker = new google.maps.Marker({
				position: gcoords,
				title: '<?php echo $bp->groups->current_group->name ?>'
			});
		  
			gmarker.setMap(gmap);
		}
		
		jQuery(document).ready( function() {
			gmap_initialize();
		});
		</script>
		<?php
	}
}
add_action( 'mapo_group_contact_page', 'mapo_add_group_js_vars' );

/**
 * Save a groups coordinates
 * @since 1.0
 */
function mapo_save_group_coordinates( $address, $group_id )
{
	global $bp, $wpdb;
	
	$addr = urlencode( $address['city'] .','. $address['country'] .','. $address['postcode'] .','. $address['street'] );
	$xml = file_get_contents( 'http://maps.google.com/maps/api/geocode/xml?address='. $addr .'&sensor=false' );
	
	$data = new SimpleXMLElement( $xml );

	if( $data->status == 'ZERO_RESULTS' )
	{
		$addr = mapo_format_address( $address['city'] .','. $address['country'] .','. $address['street'] );
		$xml = file_get_contents( 'http://maps.google.com/maps/api/geocode/xml?address='. $addr .'&sensor=false' );
		$data = new SimpleXMLElement( $xml );
	}
	
	if( $data->status == 'ZERO_RESULTS' )
	{
		$addr = mapo_format_address( $address['city'] .','. $address['country'] );
		$xml = file_get_contents( 'http://maps.google.com/maps/api/geocode/xml?address='. $addr .'&sensor=false' );
		$data = new SimpleXMLElement( $xml );
	}
	
	if( $data->status == 'OK' )
	{
		$lat = (array)$data->result->geometry->location->lat;
		$lng = (array)$data->result->geometry->location->lng;

		$latitude = $original_lat = $lat[0];
		$longitude = $original_lng = $lng[0];
		
		$result = $wpdb->get_row( "SELECT * FROM {$mapo->tables->coords} WHERE lat = {$latitude} AND lng = {$longitude} AND group_id > 0" );
		
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
				
				$result = $wpdb->get_row( "SELECT * FROM {$mapo->tables->coords} WHERE lat = {$latitude} AND lng = {$longitude} AND group_id > 0" );
	
				$angle = $angle + 36;
			} while ( $result );
		}
		
		$id = mapo_get_id_by_group( $group_id );
		
		if( ! $id ) $id = null;

		mapo_add_coords( $id, 0, $group_id, $latitude, $longitude );
	}
}
add_action( 'bpe_save_extra_group_details', 'mapo_save_group_coordinates', 10, 2 );

/**
 * Add the map/contact page
 * @since 1.0
 */
class MAPO_Contact extends BP_Group_Extension
{	
	function __construct()
	{
		$this->name = __( 'Contact', 'mapo' );
		$this->slug = 'contact';
		
		// position it at the end
		$this->nav_item_position = 90;
		// we don't need a create or edit screen
		$this->enable_create_step  = false;
		$this->enable_edit_item = false;
	}

	function display()
	{
		do_action( 'mapo_group_contact_page' );
	}
}
bp_register_group_extension( 'MAPO_Contact' );

/**
* Remove all group references upon group deletion
* @since 1.3.3
*/
function mapo_delete_group_coords( $group_id )
{
	$coords = new MAPO_Coords( false, false, $group_id );
	$coords->delete();
}
add_action( 'groups_delete_group', 'mapo_delete_group_coords' );
?>