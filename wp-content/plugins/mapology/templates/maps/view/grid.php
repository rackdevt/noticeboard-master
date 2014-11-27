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
<li id="route-<?php mapo_routes_id() ?>" class="grid-view">

    <div class="item-avatar">
        <a href="<?php mapo_routes_link( false, 'routes', true ) ?>"><?php mapo_routes_avatar(); ?></a>
    </div>
    
    <div class="grid-item">
    	<a id="colorbox<?php mapo_routes_id() ?>" rel="route" class="zoomin" href="#calroute-<?php mapo_routes_id() ?>"></a>
		<a href="<?php mapo_routes_link( false, 'routes', true) ?>"><?php mapo_routes_name() ?></a>
        <?php if( mapo_has_date() ) : ?><span class="grid-date"><?php mapo_routes_start_date() ?> - <?php mapo_routes_end_date() ?></span><?php endif; ?>
    </div>
    
    <div style="display:none">
        <div id="calroute-<?php mapo_routes_id() ?>" class="item-list">
            <div class="item-avatar">
                <a href="<?php mapo_routes_link() ?>"><?php mapo_routes_avatar(); ?></a>
            </div>
            
            <div class="item">
                <div class="item-title"><a href="<?php mapo_routes_link( false, 'routes', true) ?>"><?php mapo_routes_name() ?></a></div>
                
                <div class="item-desc"><?php mapo_routes_description() ?></div>
            </div>
        
            <div class="action">
                <?php do_action( 'mapo_member_routes_actions' ); ?>
                <?php if( mapo_has_date() ) : ?>
                <span class="highlight"><?php mapo_routes_start_date() ?> - <?php mapo_routes_end_date() ?></span>
                <?php endif; ?>
                <span class="route-creator"><?php _e( 'Creator:', 'mapo' ) ?><br /><?php mapo_routes_user_avatar() ?></span>
            </div>
        </div>
    </div>

	<script type="text/javascript">
    jQuery(document).ready( function() {
        jQuery("a#colorbox<?php mapo_routes_id() ?>").colorbox({
            width: "70%",
            inline: true,
            opacity: 0.6,		
            current: "<?php _e( 'Route {current} of {total}', 'mapo' ) ?>",
            previous: "<?php _e( 'Previous', 'mapo' ) ?>",
            next: "<?php _e( 'Next', 'mapo' ) ?>",
            close: "<?php _e( 'Close', 'mapo' ) ?>"
        });
    });
    </script>
</li>