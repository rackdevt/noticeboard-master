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
?>
<li id="route-<?php mapo_routes_id() ?>" class="group-list-item list-view">

    <div class="item-avatar">
        <a href="<?php mapo_routes_link( false, 'routes', true ) ?>"><?php mapo_routes_avatar(); ?></a>
    </div>
    
    <div class="item">
        <div class="item-title"><a href="<?php mapo_routes_link( false, 'routes', true ) ?>"><?php mapo_routes_name() ?></a></div>
        
        <div class="item-desc"><?php mapo_routes_description_excerpt() ?></div>
    </div>

    <div class="action">
        <?php do_action( 'mapo_member_routes_actions' ); ?>
        <?php if( mapo_has_date() ) : ?>
        <span class="highlight"><?php mapo_routes_start_date() ?> - <?php mapo_routes_end_date() ?></span>
        <?php endif; ?>
        <span class="route-creator"><?php _e( 'Creator:', 'mapo' ) ?><br /><?php mapo_routes_user_avatar() ?></span>
        <?php if( ! empty( $bp->groups->current_group->id ) && $bp->groups->current_group->admins[0]->is_admin == 1 ) : ?>
        <span class="remove-route"><a class="button confirm" href="<?php mapo_routes_remove_from_group() ?>"><?php _e( 'Remove', 'mapo' ) ?></a></span>
        <?php endif; ?>
    </div>
    
    <div class="clear"></div>
</li>