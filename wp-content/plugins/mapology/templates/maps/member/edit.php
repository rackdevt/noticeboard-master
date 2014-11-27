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

if( mapo_has_routes() ) : ?>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="routes-count">
			<?php mapo_routes_pagination_count() ?>
		</div>

		<div class="pagination-links" id="routes-pag">
			<?php mapo_routes_pagination_links() ?>
		</div>

	</div>
    
    <?php do_action( 'mapo_member_route_before_loop' ) ?>

	<ul id="routes-list" class="item-list">
	
	<?php while ( mapo_routes() ) : mapo_the_route();	?>

        <li id="route-<?php mapo_routes_id() ?>">
        
            <div class="item-avatar">
                <a href="<?php mapo_routes_link( false, 'edit' ) ?>"><?php mapo_routes_avatar( false, 70, 70 ) ?></a>
            </div>
            
            <div class="item">
                <div class="item-title"><a href="<?php mapo_routes_link( false, 'edit' ) ?>"><?php mapo_routes_name() ?></a></div>
                
                <div class="item-desc"><?php mapo_routes_description_excerpt() ?></div>
            </div>

			<div class="action">
				<?php do_action( 'mapo_member_routes_actions' ) ?>

				<?php if( mapo_has_date() ) : ?>
                <span class="highlight"><?php mapo_routes_start_date() ?> - <?php mapo_routes_end_date() ?></span>
                <?php endif; ?>
			</div>
            
            <div class="clear"></div>
        </li>

	<?php endwhile; ?>
	
	</ul>
    
    <?php do_action( 'mapo_member_route_after_loop' ) ?>
    
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