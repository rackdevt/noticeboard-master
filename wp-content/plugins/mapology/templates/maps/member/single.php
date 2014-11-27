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

if( mapo_has_routes( array( 'slug' => $bp->action_variables[0] ) ) ) : ?>

    <?php do_action( 'mapo_member_route_before_loop' ) ?>

	<?php while ( mapo_routes() ) : mapo_the_route();	?>

		<span class="kml"><a class="button" href="<?php  mapo_routes_kml_link() ?>"><?php _e( 'Download as KML', 'mapo' ) ?></a></span>
    
		<?php mapo_single_navigation() ?>
    
		<ul id="routes-list" class="item-list">
			<li id="route-<?php mapo_routes_id() ?>" class="single-route">
                
                <div class="item">
                    <div class="item-title"><a href="<?php mapo_routes_link() ?>"><?php mapo_routes_name() ?></a></div>
                    
                    <div class="item-desc"><?php mapo_routes_description() ?></div>
                </div>
    
                <div id="route-map-<?php mapo_routes_id() ?>" class="route-map"></div>
                
                <div class="action">
                    <?php do_action( 'mapo_member_routes_actions' ) ?>
                    <?php if( mapo_has_date() ) : ?>
                    <span class="highlight"><?php mapo_routes_start_date() ?> - <?php mapo_routes_end_date() ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="clear"></div>
                
                <?php do_action( 'mapo_end_single_route_action', mapo_get_routes_id(), mapo_get_routes_user_id() ) ?>
                <?php mapo_single_route_js() ?>
			</li>
		</ul>
	<?php endwhile; ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No routes were found.', 'mapo' ) ?></p>
	</div>

<?php endif; ?>