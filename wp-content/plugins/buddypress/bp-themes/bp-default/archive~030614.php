<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_archive' ); ?>

		<div class="page" id="blog-archives" role="main">


			<h1 class="pagetitle"><?php printf( __( 'Listed Items: %1$s', 'buddypress' ), wp_title( false, false ) ); ?></h1>


			<?php if ( have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>


<div class="author-box">
<?php 
echo do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]');

// $adtype = do_shortcode('[ct id="ct_List_Type_checkbox_537b" property="value"]'); 
// if ( $adtype = "Wanted" ) { // check if the Type is Wanted.
//		echo '<span><img src="http://new2012.championsclubcommunity.com/inew1bp/wp-content/uploads/2014/01/MAD-wanted-v2s2.png" alt="Wanted" title="Wanted" /></a></span>';
// } 
// elseif ( $adtype = "Offer" ) {
//		echo '<span><img src="http://new2012.championsclubcommunity.com/inew1bp/wp-content/uploads/2014/01/MAD-offered-v2s2.png" alt="Offered" title="Offered" /></a></span>';
//	}
?>
</div>



						<div class="post-content">
							<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<p class="date" style="margin-top:-6px;padding-bottom:35px !important;"><?php printf( __( 'Posted on %1$s <span>at %2$s</span>', 'buddypress' ), get_the_date(), get_the_time() ); ?></p>


						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ); ?>

				<?php endwhile; ?>

				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ); ?></h2>
				<?php get_search_form(); ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
