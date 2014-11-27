<?php
/**
 * Template Name: My noticeboard pinboard
 *
 * Description: Use this page template for a page with a right sidebar.
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */


global $current_user;
      
get_currentuserinfo();
 $author_id = $current_user->ID;

$author_query_id = array('author' => $author_id, 'showposts' => '-1', 'post_type'=> 'classifieds', 'post_status' => 'any');

$author = new BP_Core_User( $author_id );



?>



	
 <div class="js-isotope isotope" id="container"  data-isotope-options='{ "layoutMode": "packery", "itemSelector": ".gnb-item"}'>


<?php

$i = 1;

$author_posts_id = new WP_Query($author_query_id);
while($author_posts_id->have_posts()) : $author_posts_id->the_post();

$adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 

$post_id = get_the_ID();

$post_title = get_the_title();

$post_link = get_permalink($post->ID);

$location = do_shortcode('[ct id="ct_Location_C_text_3b50" property="value"]');

$country = do_shortcode('[ct id="ct_Item_Count_text_f58b" property="value"]');

$post_date = get_the_date('d-m-Y');

$post_time = get_the_time();

$description = substr(do_shortcode('[ct id="ct_Item_Descr_textarea_b138"]'), 0, 75) . '...';



  if ( $adtype == "Wanted" && $post_id % 2 == 0)
  		{
  		$gnb_item_class = "gnb-item-3 wanted";	
  		}
  else if ( $adtype == "Wanted" ) { 
  		$gnb_item_class = "gnb-item-1 wanted";
  		} 		
  else {
  		$gnb_item_class = "gnb-item-2 offered";	
 	 	}

$gnb_category = do_shortcode('[ct id="ct_Item_Categ_text_9be6" property="value"]');

$chars = array(' – ','/',' ','&');

$gnb_category_slug = str_replace ($chars , '-', strtolower($gnb_category));

$gnb_category_slug = $gnb_category_slug . "-offered";

$args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'tax_query' => array(
        array(
            'taxonomy' => 'offered_categories',
            'field' => 'slug',
            'terms' => $gnb_category_slug
        )
    )
);



$new_query = new WP_Query($args);
if($new_query->have_posts()) :
    while ($new_query->have_posts()) : $new_query->the_post();
        //...

$default_image = "/wp-content/uploads/" . get_the_title() . ".jpg";


    endwhile;
endif;

wp_reset_query();


?>
<form method="post" action="/members/<?php echo $current_user->user_login; ?>/classifieds/create-new/?post_id=<?php echo $post_id; ?>" >
	<div class="gnb-item <?php echo $gnb_item_class; ?>" id="post-<?php the_ID(); ?>">
        <div class="author-box">
        <?php echo $adtype; ?>

          </div>
 

<div class="post-content">
<?php 

    $post_image = get_the_post_thumbnail();

    if (!empty($post_image))
    { 
    echo get_the_post_thumbnail( $post_id, $size, $attr );
    }
    else {
     echo "<img src='" . $default_image . "' alt='' class='gnb-image-responsive"; if ($i % 2 == 0) { echo " gnb-greyscale'" . " >";} else {echo "' />";} 
    }
    ?>
</div>
<div class="entry">
           		<h2 class="posttitle"><a href="/members/<?php echo $current_user->user_login; ?>/classifieds/create-new/?post_id=<?php echo $post_id; ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><?php echo $post_title; ?></a></h2>

							<p class="date" ><?php printf( __( '%1$s <span>at %2$s</span>', 'buddypress' ), $post_date, $post_time ); ?></p>
           
                 <p><strong>Category:</strong> <br /><?php echo $gnb_category; ?></p>

               <p><strong>Location:</strong> <?php echo $location ?>,&nbsp;
					<?php echo $country; ?></p>

				<p><strong>Description:</strong>
				<?php echo $description ?></p>
            </div>
<div>

    <input type="submit" name="contact_user" value="Amend"  />
</div> 
     

</div>
</form>
<?php


 endwhile; 



?>

</div><!-- #content -->



