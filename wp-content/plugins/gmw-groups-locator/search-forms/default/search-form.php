<?php 
/**
 * Default search form for Groups Locator.
 * @version 1.0
 * @author Eyal Fitoussi
 */
?>
<div id="gmw-form-wrapper-<? echo $gmw['ID']; ?>" class="gmw-form-wrapper gmw-form-wrapper-<? echo $gmw['ID']; ?> gmw-gl-form-wrapper">
	
	<form id="gmw-form-<? echo $gmw['ID']; ?>" class="standard-form gmw-form gmw-form-<? echo $gmw['ID']; ?> gmw-gl-form " name="gmw_form" action="<?php echo $gmw['search_results']['results_page']; ?>" method="get">

		<?php do_action( 'gmw_search_form_start', $gmw ); ?>
		
		<div class="gmw-address-field-wrapper">
			<!-- Address Field -->
			<?php gmw_search_form_address_field($gmw, $id='', $class=''); ?>
		</div>	
		<!--  locator icon -->
		<?php gmw_search_form_locator_icon($gmw, $class=''); ?>
		
		<div class="clear"></div>
		
                <?php do_action( 'gmw_search_form_before_distance', $gmw ); ?>
                
		<div class="gmw-unit-distance-wrapper">
			<!--distance values -->
			<?php gmw_search_form_radius_values($gmw, $class='', $btitle='', $stitle=''); ?>
			<!--distance units-->
			<?php gmw_search_form_units($gmw, $class='' ); ?>	
		</div>
		
		<?php gmw_form_submit_fields( $gmw, $subValue=__('Submit','GMW-GL') ); ?>
		
		<?php do_action( 'gmw_search_form_end', $gmw ); ?>
                
	</form>
</div>
<!--form wrapper -->	
