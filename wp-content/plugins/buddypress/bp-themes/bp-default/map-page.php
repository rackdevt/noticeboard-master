<?php
/*
 * Template Name: Group map, no sidebar
 *
 * A custom page template without sidebar.
 *
 * @package BuddyPress
 * @subpackage BP_Default
 * @since BuddyPress (1.5)
 */

get_header(); ?>

	<div id="content">
		<div class="padder one-column">

		<?php do_action( 'bp_before_blog_page' ); ?>

		<div class="page" id="blog-page" role="main">
		
			<?php mapo_get_all_groups_coordinates(); ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_blog_page' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer(); ?>