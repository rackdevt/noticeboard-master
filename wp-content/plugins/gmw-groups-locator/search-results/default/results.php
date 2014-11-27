<?php

/**
 * GEO my WP - Groups Loop default 
 *
 * This page is the same as groups-loop.php page that provided with buddypress plugin with few modifications to make it work with GEO my WP
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( $gmw['query_args'] ) ) : ?>

        <?php gmw_gl_per_page_dropdown($gmw, ''); ?><?php gmw_gl_orderby_dropdown( $gmw, '', '' ); ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-dir-count-top">
			<?php bp_groups_pagination_count(); ?> <?php gmw_gl_within_message($gmw, $class=''); ?>
		</div>
		
		<div class="clear"></div>
			
		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links(); ?>
		</div>

	</div>
	
	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list" role="main">

        <!-- GEO my WP Map -->
	<?php gmw_results_map( $gmw ); ?>
        
	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li <?php bp_group_class(); ?>>
                    
                        <?php do_action( 'gmw_gl_directory_group_start', $gmw ); ?>
                    
			<div class="item-avatar">
				<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
			</div>

			<div class="item">
                            
				<div class="item-title"><div class="gmw-gl-group-count"><?php echo gmw_gl_group_number($gmw); ?>)</div><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a><div class="gmw-gl-radius-wrapper"><?php gmw_gl_by_radius($gmw); ?></div></div>
				<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'GMW-GL' ), bp_get_group_last_active() ); ?></span></div>

				<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>

				<?php do_action( 'bp_directory_groups_item' ); ?>
                                
                                <?php do_action( 'gmw_gl_directory_group_item', $gmw ); ?>

			</div>

			<div class="action">

				<?php do_action( 'bp_directory_groups_actions' ); ?>

				<div class="meta">

					<?php bp_group_type(); ?> / <?php bp_group_member_count(); ?>

				</div>

			</div>

			<div class="clear"></div>
			
			<div class="gmw-gl-address-wrapper">
                            <span>Address: </span><span><?php gmw_gl_group_address($gmw); ?></span>
                        </div>
                        
                        <?php gmw_gl_get_directions( $gmw ); ?> <?php echo gmw_gl_driving_distance($gmw, $class); ?>
                        
                        <?php do_action( 'gmw_gl_directory_group_end', $gmw ); ?>
                        
		</li>
		
	<?php endwhile; ?>
                
	</ul>
	
        <?php do_action( 'gmw_fl_after_groups_loop', $gmw ); ?>
        
	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-dir-count-bottom">

			<?php bp_groups_pagination_count(); ?><?php gmw_gl_within_message($gmw, $class=''); ?>

		</div>
		
		<div class="pagination-links" id="group-dir-pag-bottom">
			<?php bp_groups_pagination_links(); ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'GMW-GL' ); ?></p>
	</div>
        
        <?php do_action( 'gmw_gl_after_no_groups', $gmw ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
