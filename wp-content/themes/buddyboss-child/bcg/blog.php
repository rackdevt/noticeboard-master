	<?php
	/*
	This page is used for group blog home page/categories archives*/
	?>


	<?php $q = new WP_Query( bcg_get_query() );?>
	<?php if ($q->have_posts() ) : ?>
	<?php do_action( 'bp_before_group_blog_content' ) ?>


	<div class="pagination no-ajax gnb-blog-pagination" >
	
		<div id="posts-pagination" class="pagination-links">
			<?php bcg_pagination( $q ) ?>
		</div>
	</div>	

	<?php do_action( 'bp_before_group_blog_list' ) ?>
	 <div class="grid js-isotope isotope gnb-group-classifieds" id="container"  >
<?php
	global $post;
	bcg_loop_start();//please do not remove it

	$i = 1;
	
	while( $q->have_posts() ):$q->the_post();
 
$adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 

$post_id = get_the_ID();

$post_title = get_the_title();

$post_link = get_permalink($post->ID);

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

$chars = array('/',' ','&');

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

  <form method="post" action="<?php the_permalink(); ?>#respond" >
	<div class="gnb-item <?php echo $gnb_item_class; ?>" id="post-<?php the_ID(); ?>">
        <div class="author-box">
        <?php 
echo do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]');

// $adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 
// if ( $adtype = "Wanted" ) { // check if the Type is Wanted.
//		echo '<span><img src="http://gnb2.championsclubcommunity.com/wp-content/uploads/2014/01/MAD-wanted-v2s2.png" alt="Wanted" title="Wanted" /></a></span>';
// } 
// elseif ( $adtype = "Offer" ) {
//		echo '<span><img src="http://gnb2.championsclubcommunity.com/wp-content/uploads/2014/01/MAD-offered-v2s2.png" alt="Offered" title="Offered" /></a></span>';
//	}
?>
        </div>
        <div class="post-content">

<div>
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
           		<h2 class="posttitle"><a href="<?php echo $post_link; ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php echo $post_title; ?>"><?php echo $post_title; ?></a></h2>

							<p class="date" ><?php printf( __( '%1$s <span>at %2$s</span>', 'buddypress' ), get_the_date('d-m-Y'), get_the_time() ); ?></p>
           
                 <p><strong>Category:</strong> <?php echo $gnb_category; ?></p>

               <p><strong>Location:</strong> <?php echo do_shortcode('[ct id="ct_Location_C_text_3b50" property="value"]'); ?>,&nbsp;
					<?php echo do_shortcode('[ct id="ct_Item_Count_text_f58b" property="value"]'); ?></p>

				<p><strong>Description:</strong>
				<?php echo substr(do_shortcode('[ct id="ct_Item_Descr_textarea_b138"]'), 0, 75) . '...'; ?></p>
            </div>
<div>

		
<!-- Addthis share -->

<div class="addthis_toolbox addthis_default_style addthis_32x32_style" data-url="<?php echo "http://" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] . "#activity-"; bp_activity_id(); ?>" data-title="We need to think of something" >
  <?php 
if ( !is_user_logged_in() ) {
   printf(__(''), wp_login_url( get_permalink() ));
}
 else { ?>


    <input type="submit" name="contact_user" value="Respond"  />
 

 <?php
 }
?>         
  <a class="addthis_button_facebook"></a>
          <a class="addthis_button_email"></a>
   

 <!-- end this -->	
 </div>	

</div>	


        </div>


    </div>
</form>
	<?php 

	$i++;

	endwhile;

	?>
	    </div>
	<?php 
        do_action( 'bp_after_group_blog_content' ) ;
        bcg_loop_end();//please do not remove it
	?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This group doesn\'t have any offered or wanted items at the moment, please check again later.', 'bcg' ); ?></p>
	</div>

<?php endif; ?>


