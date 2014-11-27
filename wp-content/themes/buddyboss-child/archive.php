<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, BuddyBoss already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */

get_header(); ?>

	

		<?php if ( have_posts() ) : ?>
			<header class="archive-header">
				<h1 class="archive-title">

<?php 

$adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 
echo $adtype;

?>
		</h1>

<h2>

<?php

$gnb_category = do_shortcode('[ct id="ct_Item_Categ_text_9be6" property="value"]');

echo $gnb_category

?>
</h2>

<?php

$url = $_SERVER['REQUEST_URI'];
$cat = explode("/", $url);


$args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'tax_query' => array(
        array(
            'taxonomy' => $cat[1],
            'field' => 'slug',
            'terms' => $cat[2]
        )
    )
);
$query = new WP_Query($args);
if($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        //...

$default_image = "/wp-content/uploads/" . get_the_title() . ".jpg";


    endwhile;
endif;

	?>
			</header><!-- .archive-header -->

<div>
<?php
	buddyboss_pagination();		

?>	
 <div class="js-isotope isotope" id="container"  data-isotope-options='{ "layoutMode": "packery", "itemSelector": ".gnb-item"}'>
			<?php
			$i = 1;
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/* Include the post format-specific template for the content. If you want to
				 * this in a child theme then include a file called called content-___.php
				 * (where ___ is the post format) and that will be used instead.
				 */

$post_id = get_the_ID();


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


?>
  <form method="post" action="<?php the_permalink(); ?>#respond" >
		<div class="gnb-item <?php echo $gnb_item_class; ?>" id="post-<?php the_ID(); ?>">
        <div class="author-box">
        <?php 
echo do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]');

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
     echo "<img src='" . $default_image . "' alt='' class='gnb-image-responsive"; if ($i % 2 == 0) { echo " gnb-greyscale'" . " >";} else {echo "' >";} 
    }
    ?>
</div>
 <div class="entry">
           		<h2 class="posttitle gnb-sub-cat-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<p class="date" ><?php printf( __( '%1$s <span>at %2$s</span>', 'buddypress' ), get_the_date('d-m-Y'), get_the_time() ); ?></p>
       
                 <p><strong>Category:</strong> <?php echo $gnb_category; ?></p>

               <p><strong>Location:</strong> <?php echo do_shortcode('[ct id="ct_Location_C_text_3b50" property="value"]'); ?>,&nbsp;
					<?php echo do_shortcode('[ct id="ct_Item_Count_text_f58b" property="value"]'); ?></p>

				<p><strong>Description:</strong>
				<?php echo substr(do_shortcode('[ct id="ct_Item_Descr_textarea_b138"]'), 0, 75) . '...'; ?></p>
            </div>
</div>

<div class="gnb-ad-buttons" >		
<!-- Addthis share -->

<div class="addthis_toolbox addthis_default_style addthis_16x16_style" data-url="<?php echo "http://" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] . "#activity-"; bp_activity_id(); ?>" data-title="We need to think of something" >
  

  <a class="addthis_button_facebook"></a>
          <a class="addthis_button_email"></a>

 <?php 
if ( !is_user_logged_in() ) {
   printf(__(''), wp_login_url( get_permalink() ));
}
 else { ?>


    <input type="submit" name="contact_user" value="Respond" class="gnb-respond" />
 

 <?php
 }
?>           
    </div>	

 <!-- end Addthis -->	
</div>

</div>
 </form>
<?php

$i++;

			endwhile;
			?>
</div>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

</div>



<?php get_sidebar(); ?>
<?php get_footer(); ?>