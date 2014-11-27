<?php
/**
 * The sidebar containing the widget area for WordPress blog posts and pages.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */
?>
	
<!-- Humanity Fund - Poundamonth Right Sidebar -->
<div id="secondary" class="widget-area" role="complementary">
	<?php if ( is_active_sidebar('pam-sidebar') ) : ?>
		<?php dynamic_sidebar( 'pam-sidebar' ); ?>
	<?php endif; ?>
</div><!-- #secondary -->