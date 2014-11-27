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
<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php mapo_group_routes_feed_link() ?>" title="<?php _e( 'RSS Feed', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
		<li><a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/' ?>"><?php _e( 'Routes', 'mapo' ) ?></a></li>
		<li class="current"><a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ) . $bp->mapology->slug .'/overview/' ?>"><?php _e( 'Overview', 'mapo' ) ?></a></li>
		<?php if( is_user_logged_in() ) : ?>
			<li><a href="<?php echo bp_loggedin_user_domain() . $bp->mapology->slug. '/create/' ?>"><?php _e( 'Create', 'mapo' ) ?></a></li>
		<?php endif; ?>
	</ul>
</div>
<div id="group-overview-map"></div>
<div id="group-overview-legend">
	<?php mapo_overview_legend() ?>
</div>
<?php mapo_overview_js( 'group-overview-map', true ) ?>