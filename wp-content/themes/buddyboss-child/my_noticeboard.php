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

get_header();

global $current_user;
      get_currentuserinfo();
 $author_id = $current_user->ID;

$author_query_id = array('author' => $author_id, 'showposts' => '-1', 'post_type'=> 'classifieds', 'post_status' => 'any');

$author = new BP_Core_User( $author_id );

?>



<div id="buddypress">

	<?php do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php bp_get_template_part( 'members/single/member-header') ?>

	</div><!-- #item-header -->

	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
			<ul>

				<?php bp_get_displayed_user_nav(); ?>

				<?php do_action( 'bp_member_options_nav' ); ?>

			</ul>
		</div>
	</div><!-- #item-nav -->

	<div id="item-body" role="main">
	<div>
		<h1>My NoticeBoard</h1>
 	</div>
 <div class="js-isotope isotope" id="container"  data-isotope-options='{ "layoutMode": "packery", "itemSelector": ".gnb-item"}'>


<?php

$i = 1;

$author_posts_id = new WP_Query($author_query_id);
while($author_posts_id->have_posts()) : $author_posts_id->the_post();

$adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 

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
	<div class="gnb-item <?php echo $gnb_item_class; ?>" id="post-<?php the_ID(); ?>">
        <div class="author-box">
        <?php echo $adtype; ?>

          </div>
 

<div class="post-content">
<?php echo get_the_post_thumbnail( $post_id, $size, $attr ); ?> 
</div>
<div class="entry">
           		<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<p class="date" ><?php printf( __( '%1$s <span>at %2$s</span>', 'buddypress' ), get_the_date('d-m-Y'), get_the_time() ); ?></p>
           
                 <p><strong>Category:</strong> <?php echo $gnb_category; ?></p>

               <p><strong>Location:</strong> <?php echo do_shortcode('[ct id="ct_Location_C_text_3b50" property="value"]'); ?>,&nbsp;
					<?php echo do_shortcode('[ct id="ct_Item_Count_text_f58b" property="value"]'); ?></p>

				<p><strong>Description:</strong>
				<?php echo substr(do_shortcode('[ct id="ct_Item_Descr_textarea_b138"]'), 0, 75) . '...'; ?></p>
            </div>
</div>
<?php


 endwhile; 



?>
</div></div>
</div><!-- #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

