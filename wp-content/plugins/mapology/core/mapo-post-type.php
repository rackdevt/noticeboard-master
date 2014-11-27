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
 * Register the listings post type
 * @since 1.0
 */
function mapo_custom__types_init()
{
	$loc = array(
		'labels' => array(
			'name' => _x( __( 'Locations', 'mapo' ), 'post type general name' ),
			'singular_name' => _x( __( 'Location', 'mapo' ), 'post type singular name' ),
			'add_new' => _x( __( 'Add New', 'mapo' ), 'location' ),
			'add_new_item' => __( 'Add New Location' ),
			'edit_item' => __( 'Edit Location', 'mapo' ),
			'new_item' => __( 'New Location', 'mapo' ),
			'view_item' => __( 'View Location', 'mapo' ),
			'search_items' => __( 'Search Locations', 'mapo' ),
			'not_found' =>  __( 'No Locations found', 'mapo' ),
			'not_found_in_trash' => __( 'No locations found in trash', 'mapo' ),
			'parent_item_colon' => ''
		),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => 'location',
		'rewrite' => array( 'slug' => 'location' ),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' )
	); 

	register_post_type( 'location', $loc );
}
add_action( 'init', 'mapo_custom__types_init' );

/**
 * Create categories and tags for the listings post type
 */
function mapo_create_location_taxonomies()
{
	register_taxonomy( 'continents', array( 'location' ), array(
		'hierarchical' => true,
		'labels' => array(
			'name' => _x( __( 'Continents', 'mapo' ), 'taxonomy general name' ),
			'singular_name' => _x( __( 'Continent', 'mapo' ), 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Continents', 'mapo' ),
			'popular_items' => __( 'Popular Continents', 'mapo' ),
			'all_items' => __( 'All Continents', 'mapo' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Continent', 'mapo' ),
			'update_item' => __( 'Update Continent', 'mapo' ),
			'add_new_item' => __( 'Add New Continent', 'mapo' ),
			'new_item_name' => __( 'New Continent', 'mapo' ),
			'separate_items_with_commas' => __( 'Separate continents with commas', 'mapo' ),
			'add_or_remove_items' => __( 'Add or remove continent', 'mapo' ),
			'choose_from_most_used' => __( 'Choose from the most used continents', 'mapo' )
		),
		'show_ui' => true,
		'query_var' => true
	));

	register_taxonomy( 'countries', array( 'location' ), array(
		'hierarchical' => false,
		'labels' => array(
			'name' => _x( __( 'Countries', 'mapo' ), 'taxonomy general name' ),
			'singular_name' => _x( __( 'Country', 'mapo' ), 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Countries', 'mapo' ),
			'popular_items' => __( 'Popular Countries', 'mapo' ),
			'all_items' => __( 'All Countries', 'mapo' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Country', 'mapo' ),
			'update_item' => __( 'Update Country', 'mapo' ),
			'add_new_item' => __( 'Add New Country', 'mapo' ),
			'new_item_name' => __( 'New Country', 'mapo' ),
			'separate_items_with_commas' => __( 'Separate countries with commas', 'mapo' ),
			'add_or_remove_items' => __( 'Add or remove country', 'mapo' ),
			'choose_from_most_used' => __( 'Choose from the most used countries', 'mapo' )
		),
		'show_ui' => true,
		'query_var' => true
	));

	register_taxonomy( 'areas', array( 'location' ), array(
		'hierarchical' => false,
		'labels' => array(
			'name' => _x( __( 'Areas', 'mapo' ), 'taxonomy general name' ),
			'singular_name' => _x( __( 'Area', 'mapo' ), 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Areas', 'mapo' ),
			'popular_items' => __( 'Popular areas', 'mapo' ),
			'all_items' => __( 'All Areas', 'mapo' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Area', 'mapo' ),
			'update_item' => __( 'Update Area', 'mapo' ),
			'add_new_item' => __( 'Add New Area', 'mapo' ),
			'new_item_name' => __( 'New Area', 'mapo' ),
			'separate_items_with_commas' => __( 'Separate areas with commas', 'mapo' ),
			'add_or_remove_items' => __( 'Add or remove area', 'mapo' ),
			'choose_from_most_used' => __( 'Choose from the most used areas', 'mapo' )
		),
		'show_ui' => true,
		'query_var' => true
	));
}
add_action( 'init', 'mapo_create_location_taxonomies', 0 );

/**
 * Remove custom fields on location backend screen
 * @since 1.0
 */
function mapo_remove_custom()
{
	remove_meta_box( 'postcustom', 'location', 'advanced' );
}
add_action( 'admin_menu', 'mapo_remove_custom' );

/**
 * Listing search form
 * @since 1.0
 */
function mapo_directory_locations_search_form()
{
	global $bp;

	$search_value = __( 'Search anything...', 'mapo' );
	if ( !empty( $_REQUEST['r'] ) )
	 	$search_value = $_REQUEST['r'];
	?>
	<form action="<?php echo bp_get_root_domain() .'/location/' ?>" method="get" id="search-location-form">
		<label><input type="text" name="r" id="location_search" value="<?php echo esc_attr( $search_value ) ?>"  onfocus="if (this.value == '<?php _e( 'Search anything...', 'mapo' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search anything...', 'mapo' ) ?>';}" /></label>
		<input type="submit" id="location_search_submit" value="<?php _e( 'Search', 'mapo' ) ?>" />
	</form>
	<?php
}

/**
 * Show the location avatar
 * @since 1.0
 */
function mapo_location_avatar( $w = 150, $h = 150 )
{
	echo mapo_get_location_avatar( $w, $h );
}
	function mapo_get_location_avatar( $w = 150, $h = 150 )
	{
		$coords = mapo_get_post_coords();
		
		if( empty( $coords['lat'] ) || empty( $coords['lng'] ) )
			return false;
		
		return '<img class="avatar" src="http://maps.google.com/maps/api/staticmap?zoom=4&size='. $w .'x'. $h .'&maptype=hybrid&markers=color:red|'. $coords['lat'] .','. $coords['lng'] .'&sensor=false" weight="'. $w .'"  height="'. $h .'" alt="" />';
	}
?>