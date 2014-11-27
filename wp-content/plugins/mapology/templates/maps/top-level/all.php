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
                
if( mapo_has_routes( array( 'per_page' => mapo_get_view_per_page() ) ) ) : ?>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="routes-count">
			<?php mapo_routes_pagination_count() ?>
		</div>

		<div class="pagination-links" id="routes-pag">
			<?php mapo_routes_pagination_links() ?>
		</div>

	</div>
	
	<?php do_action( 'mapo_dir_route_before_loop' ) ?>

	<ul id="routes-list" class="item-list<?php if( mapo_grid_style() ) : ?> item-list-grid<?php endif; ?>">
	
	<?php while ( mapo_routes() ) : mapo_the_route();	?>

		<?php if( mapo_grid_style() ) : ?>
        
            <?php mapo_load_template( 'maps/view/grid' ); ?>                
        
        <?php else : ?>
    
            <?php mapo_load_template( 'maps/view/list' ); ?>                
       
        <?php endif; ?>

	<?php endwhile; ?>
	
	</ul>
	
	<?php do_action( 'mapo_dir_route_after_loop' ) ?>
	
	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="routes-count">
			<?php mapo_routes_pagination_count() ?>
		</div>

		<div class="pagination-links" id="routes-pag">
			<?php mapo_routes_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No routes were found.', 'mapo' ) ?></p>
	</div>

<?php endif; ?>