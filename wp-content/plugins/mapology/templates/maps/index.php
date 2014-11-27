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

global $bp;

get_header(); ?>

	<div id="content">
		<div class="padder">
        
            <h3 class="pagetitle">
				<?php _e( 'Routes Directory', 'mapo' ) ?>
                <?php if( mapo_is_dir_routes() ) : ?>
                <div id="list-choices">
                    <a class="grid-style<?php mapo_view_class( 'grid' ) ?>" title="<?php _e( 'Change to grid style', 'mapo' ) ?>" href="<?php mapo_view_link( 'grid' ) ?>"></a>
                    <a class="list-style<?php mapo_view_class( 'list' ) ?>" title="<?php _e( 'Change to list style', 'mapo' ) ?>" href="<?php mapo_view_link( 'list' )?>"></a>
                </div>
                <?php endif; ?>
            </h3>
    
            <div id="routes-dir-search" class="dir-search no-ajax">
                <?php mapo_directory_routes_search_form() ?>
            </div>

            <?php do_action( 'template_notices' ) ?>
            
            <div class="item-list-tabs no-ajax">
                <ul>
                    <li class="feed"><a href="<?php mapo_sitewide_routes_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
                    <li id="routes-all"<?php if( mapo_is_dir_routes() ) echo ' class="selected"'; ?>><a href="<?php echo bp_get_root_domain() .'/'. $bp->mapology->root_slug .'/' ?>"><?php _e( 'All Routes', 'mapo' ) ?></a></li>
                    <li id="routes-map"<?php if( mapo_is_dir_routes_map() ) echo ' class="selected"'; ?>><a href="<?php echo bp_get_root_domain() .'/'. $bp->mapology->root_slug .'/overview/' ?>"><?php _e( 'Overview Map', 'mapo' ) ?></a></li>
                </ul>
            </div><!-- .item-list-tabs -->
        
            <div id="routes-dir-list" class="routes dir-list">

			<?php if( mapo_is_dir_routes_map() ) : ?>
            	<?php mapo_load_template( 'maps/top-level/map' ); ?>
            <?php else : ?>
            	<?php mapo_load_template( 'maps/top-level/all' ); ?>
            <?php endif; ?>
            
            </div><!-- #routes-dir-list -->

  		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>