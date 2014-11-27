<?php
/**
 * Template Name: Right Sidebar - PaM
 *
 * Description: Use this page template for a page with a right sidebar.
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */
get_header(pam); ?>

	<div id="primary" class="site-content">
	
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar("pam-sidebar"); ?>
<?php get_footer(pam); ?>