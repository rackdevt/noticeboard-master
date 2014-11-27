<div id="gmw-gl-location-tab-wrapper">
	<?php if ( isset( $gmw_gl_address ) && !empty( $gmw_gl_address ) ) : ?>
            <div id="gmw-gl-group-address-wrapper">
                <?php do_action( 'gmw_gl_location_tab_location_exists', $group_id, $gmw_gl_address ); ?>
            </div>
        <?php else : ?>
            <?php do_action( 'gmw_gl_location_tab_location_not_exist', $group_id ); ?>
            <div id="gmw-gl-no-group-location-message"><?php _e('This group does not have a location','GMW-GL'); ?></div>
	<?php endif; ?>
</div>