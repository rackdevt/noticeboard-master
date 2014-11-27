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

global $bp; ?>

<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php mapo_group_routes_feed_link() ?>" title="<?php _e( 'RSS Feed', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
		<li class="selected"><a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/' ?>"><?php _e( 'Routes', 'mapo' ) ?></a></li>
		<li><a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/overview/' ?>"><?php _e( 'Overview', 'mapo' ) ?></a></li>
		<?php if( is_user_logged_in() ) : ?>
			<li><a href="<?php echo bp_loggedin_user_domain() . $bp->mapology->slug. '/create/' ?>"><?php _e( 'Create', 'mapo' ) ?></a></li>
		<?php endif; ?>
        <li class="last">
            <div id="list-choices-group">
                <a class="grid-style<?php mapo_view_class( 'grid' ) ?>" title="<?php _e( 'Change to grid style', 'mapo' ) ?>" href="<?php mapo_view_link( 'grid' ) ?>group/<?php echo $bp->groups->current_group->id ?>/"></a>
                <a class="list-style<?php mapo_view_class( 'list' ) ?>" title="<?php _e( 'Change to list style', 'mapo' ) ?>" href="<?php mapo_view_link( 'list' )?>group/<?php echo $bp->groups->current_group->id ?>/"></a>
            </div>
        </li>
	</ul>
</div>

<?php if( mapo_has_routes( array( 'per_page' => mapo_get_view_per_page() ) ) ) : ?>

	<div id="pag-top" class="pagination no-ajax group-pagination">
    
		<div class="pag-count" id="routes-count">
			<?php mapo_routes_pagination_count() ?>
		</div>

		<div class="pagination-links" id="routes-pag">
			<?php mapo_routes_pagination_links() ?>
		</div>

	</div>
    
    <?php do_action( 'mapo_member_route_before_loop' ); ?>

	<ul id="routes-list" class="item-list group-routes<?php if( mapo_grid_style() ) : ?> item-list-grid<?php endif; ?>">
	
	<?php while ( mapo_routes() ) : mapo_the_route(); ?>

		<?php if( mapo_grid_style() ) : ?>
        
            <?php mapo_load_template( 'maps/view/grid' ); ?>                
        
        <?php else : ?>
    
            <?php mapo_load_template( 'maps/view/list' ); ?>                
       
        <?php endif; ?>

	<?php endwhile; ?>
    <?php if( $bp->groups->current_group->admins[0]->is_admin == 1 ) : ?>
	<script type="text/javascript">
    jQuery(document).ready(function() {
		jQuery('.remove-route').hide();
		jQuery('.group-list-item').hover(
			function() {
				jQuery(this).children().children('.remove-route').show();
			}, 
			function() {
				jQuery(this).children().children('.remove-route').hide();
			}
		);		
	});
    </script>
    <?php endif; ?>
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