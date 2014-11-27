<?php 
/**
 * Template for info window. 
 * 
 * The information on this file will be displayed on either the infobox or the popup window.
 * There are 3 args for you to use:
 * $gmw - the form being used
 * $group - the member being displayed with member and location information
 * 
 */
?>
<div class="gmgl-iw-template-inner">
	
	<div class="gmgl-iw-close-button">x</div>
	
	<div class="gmgl-iw-title">
		<a href="<?php bp_group_permalink( $group ); ?>" ><?php bp_group_name( $group ); ?></a>
		<span class="gmgl-iw-distance-wrapper"><?php gmgl_distance( $group, $gmw ); ?></span>
	</div>
	
        <?php if ( isset( $gmw['info_window']['avatar'] ) ) { ?>
                <div class="gmgl-iw-avatar-wrapper">
                        <a href="<?php bp_group_permalink( $group ); ?>"><?php echo bp_core_fetch_avatar ( array( 'item_id' => $group->ID, 'type' => 'full' ) ); ?></a>
                </div>
        <?php } ?>
        
	<div class="gmgl-iw-address-wrapper">
		<span><?php _e( 'Address: ', 'GMW-GL' ); ?></span><?php echo $group->formatted_address; ?>
	</div>
    <?php echo gmgl_get_directions( $group, $gmw, __( 'Get Directions' ) ); ?>
 
</div>  
 		    
