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

get_header() ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'mapo_before_member_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
						<?php do_action( 'bp_member_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">
				<?php do_action( 'mapo_before_member_body' ) ?>

                <div class="item-list-tabs no-ajax" id="subnav">
                    <ul>
                    	<li class="feed"><a href="<?php mapo_user_routes_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
                        <?php bp_get_options_nav() ?>
                        <?php if( bp_is_current_action( 'routes' ) && empty( $bp->action_variables[0] ) ) : ?>       
                        <li class="last">
                            <div id="list-choices-group">
                                <a class="grid-style<?php mapo_view_class( 'grid' ) ?>" title="<?php _e( 'Change to grid style', 'mapo' ) ?>" href="<?php mapo_view_link( 'grid' ) ?>user/<?php echo $bp->displayed_user->id ?>/"></a>
                                <a class="list-style<?php mapo_view_class( 'list' ) ?>" title="<?php _e( 'Change to list style', 'mapo' ) ?>" href="<?php mapo_view_link( 'list' )?>user/<?php echo $bp->displayed_user->id ?>/"></a>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>

				<?php do_action( 'mapo_before_member_content' ) ?>
                
                <div class="maps">
                
					<?php if( mapo_is_member_routes_single() ) : ?>
                        <?php mapo_load_template( 'maps/member/single' ); ?>

					<?php elseif( mapo_is_member_overview() ) : ?>
                        <?php mapo_load_template( 'maps/member/overview' ); ?>

					<?php elseif( mapo_is_member_routes() ) : ?>
                        <?php mapo_load_template( 'maps/member/routes' ); ?>

                    <?php elseif( mapo_is_member_edit_single() ) : ?>
                        <?php mapo_load_template( 'maps/member/edit-single' ); ?>
                
                    <?php elseif( mapo_is_member_edit() ) : ?>
                        <?php mapo_load_template( 'maps/member/edit' ); ?>
                
                    <?php elseif( mapo_is_member_create() ) : ?>
                        <?php mapo_load_template( 'maps/member/create' ); ?>
                
                    <?php endif; ?>
                
                    <?php do_action( 'mapo_inside_member_content' ) ?>

                </div><!-- .event -->
                
				<?php do_action( 'mapo_after_member_body' ) ?>
			</div><!-- #item-body -->

			<?php do_action( 'mapo_after_member_home_content' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>