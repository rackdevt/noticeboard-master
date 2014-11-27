<?php
/*
Plugin Name: SP FAQ
Plugin URL: http://sptechnolab.com
Description: A simple FAQ plugin
Version: 2.2
Author: SP Technolab
Author URI: http://sptechnolab.com
Contributors: SP Technolab
*/
/*
 * Register CPT sp_faq
 *
 */
function sp_faq_setup_post_types() {

	$festivals_labels =  apply_filters( 'sp_faq_labels', array(
		'name'                => 'FAQs',
		'singular_name'       => 'FAQ',
		'add_new'             => __('Add New', 'sp_faq'),
		'add_new_item'        => __('Add New FAQ', 'sp_faq'),
		'edit_item'           => __('Edit FAQ', 'sp_faq'),
		'new_item'            => __('New FAQ', 'sp_faq'),
		'all_items'           => __('All FAQ', 'sp_faq'),
		'view_item'           => __('View FAQ', 'sp_faq'),
		'search_items'        => __('Search FAQ', 'sp_faq'),
		'not_found'           => __('No FAQ found', 'sp_faq'),
		'not_found_in_trash'  => __('No FAQ found in Trash', 'sp_faq'),
		'parent_item_colon'   => '',
		'menu_name'           => __('FAQ', 'sp_faq'),
		'exclude_from_search' => true
	) );


	$faq_args = array(
		'labels' 			=> $festivals_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'query_var' 		=> true,
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'hierarchical' 		=> false,
		'supports' => array('title','editor','thumbnail','excerpt'),
		'taxonomies' => array('category', 'post_tag')
	);
	register_post_type( 'sp_faq', apply_filters( 'sp_faq_post_type_args', $faq_args ) );

}

add_action('init', 'sp_faq_setup_post_types');
/*
 * Add [sp_faq limit="-1"] shortcode
 *
 */
function sp_faq_shortcode( $atts, $content = null ) {
	
	extract(shortcode_atts(array(
		"limit" => '',
		"category" => '',
	), $atts));
	
	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit; 
	} else {
		$posts_per_page = '-1';
	}
	// Define limit
	if( $category ) { 
		$cat = $category; 
	} else {
		$cat = '';
	}
	
	ob_start();

	// Create the Query
	$post_type 		= 'sp_faq';
	$orderby 		= 'post_date';
	$order 			= 'DESC';
				
	$query = new WP_Query( array ( 
								'post_type'      => $post_type,
								'posts_per_page' => $posts_per_page,
								'orderby'        => $orderby, 
								'order'          => $order,
								'cat'          =>   $cat,
								'no_found_rows'  => 1
								) 
						);
	
	//Get post type count
	$post_count = $query->post_count;
	$i = 1;
	
	// Displays Custom post info
	
	
	
	if( $post_count > 0) :
	?>
	
	 <div class="accordion">
	<?php
		// Loop 
		while ($query->have_posts()) : $query->the_post();
		?>
		
		
		 <h3> <?php the_title(); ?></h3>
		 <div>
<div><?php
                  if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
                    the_post_thumbnail('thumbnail'); 
                  }
                  ?>
				  </div>
				  <?php echo get_the_content(); ?></p></div>
		
	
		<?php
		$i++;
		endwhile; ?>
		</div>
		
<?php	endif;
	
	// Reset query to prevent conflicts
	wp_reset_query();
	
	?>
	
	<?php
	
	return ob_get_clean();

}

	add_shortcode("sp_faq", "sp_faq_shortcode");

	wp_register_style( 'accordioncss', plugin_dir_url( __FILE__ ) . 'css/jqueryuicss.css' );
	wp_register_script( 'accordionjs', plugin_dir_url( __FILE__ ) . 'js/jqueryuijs.js', array( 'jquery' ) );	

	wp_enqueue_style( 'accordioncss' );
	wp_enqueue_script( 'accordionjs' );
	function myfaqscript() {
	?>
	<script type="text/javascript">
	
	 //jQuery( "#accordion" ).accordion(); 
	 
	   jQuery( ".accordion" ).accordion({
heightStyle: "content"
});
	</script>
	<?php
	}
add_action('wp_footer', 'myfaqscript'); 