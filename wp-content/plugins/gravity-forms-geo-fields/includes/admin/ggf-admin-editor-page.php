<?php

/**
 * GGF_Admin_Edit_Form_Page
 */
class GGF_Admin_Edit_Form_Page {
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		//get form
		$this->form 		= RGFormsModel::get_form_meta_by_id( $_GET['id'] );
		$this->ggf_settings = ( isset( $this->form[0]['ggf_settings'] ) ) ? $this->form[0]['ggf_settings'] : '';

		if ( !isset( $this->ggf_settings['address_fields']['use'] ) || $this->ggf_settings['address_fields']['use'] == 0 ) return;
				
		add_filter( 'gform_add_field_buttons', array( $this, 'field_groups' ), 10, 1 );
		add_filter( 'gform_field_type_title' , array( $this, 'fields_title' ), 10, 1 );
		add_action( 'gform_field_standard_settings' , array( $this, 'fields_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_action( "gform_editor_js", array( $this, 'js_editor' ) );
		
		add_action( 'gform_admin_pre_render', array( $this, 'render_form' ) );
		
	}

	/**
	 * Add GGF group buttons
	 */
	function field_groups( $field_groups ) {
		
		//add ggf fields button
		$ggf_fields[] = array( "class"=>"button ggf-fields-button", "value" => __( "Location Field", "GGF" ), "onclick" => "StartAddField('post_custom_field');" );
		
		//only if single address field add the map button
		if ( $this->ggf_settings['address_fields']['use'] == 1 ) $ggf_fields[] = array("class"=>"button ggf-map-button", "value" => __( "Map", "GGF" ), "onclick" => "StartAddField('ggfMap');");
		
		//create ggf locator icon button
		$ggf_fields[] = array( 'class' => 'button ggf-locator-button', 'value' => __( "Auto-Locator", "GGF" ), 'onclick' => "StartAddField('ggfLocator');");
		
		//create ggf address field button
		$field_groups[] = array( "name" => "ggf_fields", "label"=> __( "GGF Fields" , "GGF"), "fields" => apply_filters('ggf_field_buttons', $ggf_fields, $field_groups));
	
		return $field_groups;
	}
	
	/**
	 * Change title name for fields
	 */
	function fields_title( $type ) {
	
		if ( $type == 'mapIcons' )
			return __( 'GGF map icons' , 'GGF' );
		if ( $type == 'ggfMap' )
			return __( 'GGF Map' , 'GGF' );
		if ( $type == 'ggfLocator' )
			return __( 'GGF Locator' , 'GGF' );
	}
	
	/**
	 * GGF function - Add ggf fields to the input fields
	 */
	function fields_settings( $position, $form_id ) {
	
		if ( $position == 50 ) {
	
			?>
				<!-- 
				<li class="ggf-autocomplete-settings field_setting">
					<label for="ggf-autocomplete-field">
						<?php _e( "Custom Field", "GGF" ); ?>
						<?php gform_tooltip("ggf_acfield_tt"); ?>
					</label>
					<input type="text" id="field-ggf-autocomplete-field" class="" size="15" onkeyup="SetFieldProperty('ggf-autocomplete-field', this.value);">
				</li>
				-->
				
                        <li class="ggf-locator-title field_setting ggf-locator-settings">

                                <label for="ggf-locator-title">
                                        <?php _e( "Button Title", "GGF"); ?>
                                        <?php gform_tooltip("ggf_locator_title_tt"); ?>
                                </label>
                                <input type="text" id="field-ggf-locator-title" class="" onkeyup="SetFieldProperty('ggf-locator-title', this.value);">

                        </li>
				
                        <li class="ggf-locator-auto-submit field_setting ggf-locator-settings">
                                <input type="checkbox" id="field-ggf-locator-autosubmit" onclick="SetFieldProperty('ggf-locator-autosubmit', this.checked);" />
                                <label for="ggf-locator-auto-submit" class="inline">
                                    <?php _e( "Auto-submit Form", "GGF" ); ?>
                                    <?php gform_tooltip("ggf_locator_autosubmit_tt"); ?>
                                </label>
		       </li>
		       
		       <li class="ggf-locator-hide-submit field_setting ggf-locator-settings">
		       		<input type="checkbox" id="field-ggf-locator-hide-submit" onclick="SetFieldProperty('ggf-locator-hide-submit', this.checked);" />
                                <label for="ggf-locator-hide-submit" class="inline">
                                    <?php _e( "Hide form's submit button", "GGF" ); ?>
                                    <?php gform_tooltip("ggf_locator_hide_submit_tt"); ?>
                                </label>
		       </li>
		       
                        <li class="ggf-map-width field_setting ggf-map-settings ">

                                <label for="ggf-map-width">
                                        <?php _e( "Map Width", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_width_tt"); ?>
                                </label>
                                <input type="text" id="field-ggf-map-width" class="" size="15" onkeyup="SetFieldProperty('ggf-map-width', this.value);">

                        </li>
                        
                        <li class="ggf-map-height field_setting ggf-map-settings ">
                                <label for="ggf-map-height">
                                        <?php _e( "Map Height", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_height_tt"); ?>
                                </label>
                                <input type="text" id="field-ggf-map-height" class="" size="15" onkeyup="SetFieldProperty('ggf-map-height', this.value);">
                        </li>
                        
                        <li class="ggf-map-latitude field_setting ggf-map-settings ">
                                <label for="ggf-map-latitude">
                                        <?php _e( "Latitude", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_lat_tt"); ?>
                                </label>
                                <input type="text" id="field-ggf-map-latitude" class="" size="25" onkeyup="SetFieldProperty('ggf-map-latitude', this.value);">
                        </li>
                        
                        <li class="ggf-map-longitude field_setting ggf-map-settings ">
                                <label for="ggf-map-longitude">
                                        <?php _e( "longitude", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_long_tt"); ?>
                                </label>
                                <input type="text" id="field-ggf-map-longitude" class="" size="25" onkeyup="SetFieldProperty('ggf-map-longitude', this.value);">
                        </li>
                        
                        <li class="ggf-map-type field_setting ggf-map-settings ">
                                <label for="ggf-map-type">
                                        <?php _e( "Map Type", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_map_type_tt"); ?>
                                </label>
                                <select name="ggf_map_type" id="field-ggf-map-type" onchange="SetFieldProperty('ggf_map_type', jQuery(this).val());">          
                                        <option value="ROADMAP"><?php _e( 'ROADMAP','GGF' ); ?></option>
                                        <option value="SATELLITE"><?php _e( 'SATELLITE','GGF' ); ?></option>
                                        <option value="HYBRID"><?php _e( 'HYBRID','GGF' ); ?></option>
                                        <option value="TERRAIN"><?php _e( 'TERRAIN','GGF' ); ?></option>          
                                </select>
                        </li>
                        
                        <li class="ggf-zoom-level field_setting ggf-map-settings ">
                                <label for="ggf-zoom-level">
                                        <?php _e( "Zoom Level", "GGF" ); ?>
                                        <?php gform_tooltip("ggf_zoom_level_tt"); ?>
                                </label>
                                <select name="ggf_zoom_level" id="field-ggf-zoom-level" onchange="SetFieldProperty('ggf_zoom_level', jQuery(this).val());">          
                                        <?php $count = 18; ?>
                                        <?php
                                            for ( $x=1; $x<=18; $x++ ) {
                                                echo '<option value="'.$x.'">'. $x .'</option>';
                                            } 
                                        ?>     
                                </select>
                        </li>

                        <li class="post_custom_field_type_setting field_setting" style="display: list-item;">
                                <label for="post_custom_field_type">
                                    <?php _e( 'GEO fields - address field type','GGF' ); ?>
                                    <?php gform_tooltip("ggf_address_fields_tt"); ?>
                                </label>
                                <select name="ggf_fields" id="ggf-additional-fields" class="ggf-address-fields" onchange="SetFieldProperty('ggf_fields', jQuery(this).val());">
                                    <option value=""><?php _e('N/A','GGF'); ?></option>
                                    <?php if ( $this->ggf_settings['address_fields']['use'] == 1 ) : ?>
                                            <option value="address"><?php _e('Full Address','GGF'); ?></option>
                                    <?php elseif ( $this->ggf_settings['address_fields']['use'] == 2 ) : ?>
                                            <option value="street"><?php _e('Street','GGF'); ?></option>
                                            <option value="apt"><?php _e('Apt','GGF'); ?></option>
                                            <option value="city"><?php _e('City','GGF'); ?></option>
                                            <option value="state"><?php _e('State','GGF'); ?></option>
                                            <option value="zipcode"><?php _e('Zipcode','GGF'); ?></option>
                                            <option value="country"><?php _e('Country','GGF'); ?></option>
                                        <?php endif; ?>
                                    <?php if ( function_exists('gmw_loaded') ) : ?>
                                            <option value="phone"><?php _e('Phone Number','GGF'); ?></option>
                                            <option value="fax"><?php _e('Fax number','GGF'); ?></option>
                                            <option value="email"><?php _e('Email Address','GGF'); ?></option>
                                            <option value="website"><?php _e('Website','GGF'); ?></option>
                                    <?php endif; ?>
                                </select>
		       	</li>
                        <li class="post_custom_field_type_setting field_setting" style="display: list-item;">
		       		<input type="checkbox" id="field-ggf-locator-fill" onclick="SetFieldProperty( 'ggf-locator-fill', this.checked );" />
                                <label for="field_address_hide_state_<?php echo $key; ?>" class="inline">
                                    <?php _e("Disable locator autofill", "GGF"); ?>
                                    <?php gform_tooltip("ggf_locator_fill_tt"); ?>
                                </label>
		       </li>
		       	<li class="post_custom_field_type_setting field_setting" style="display: list-item;">
		       		<input type="checkbox" id="field-ggf-autocomplete" onclick="SetFieldProperty( 'ggf-autocomplete', this.checked );" />
                                <label for="field_address_hide_state_<?php echo $key; ?>" class="inline">
                                    <?php _e("Autocomplete", "GGF"); ?>
                                    <?php gform_tooltip("ggf_autocomplete_tt"); ?>
                                </label>
		       </li>
                       <li class="post_custom_field_type_setting field_setting" style="display: list-item;">
		       		<input type="checkbox" id="field-ggf-update-map" onclick="SetFieldProperty( 'ggf-update-map', this.checked );" />
                                <label for="field_address_hide_update_map_<?php echo $key; ?>" class="inline">
                                    <?php _e("Update Map", "GGF"); ?>
                                    <?php gform_tooltip("ggf_update_map_tt"); ?>
                                </label>
		       </li>
                       
                       
			<?php
		}//get form details
	}

	/**
	 * GGF buttons tooltips
	 */
	function tooltips($tooltips){
                
                $tooltips["ggf_update_map_tt"]          = __("<h6>Update Marker's location on the map when autocomplete is triggered on this field.</h6>","GGF");
                $tooltips["ggf_address_fields_tt"]      = __("<h6>Address Fields</h6>Select the type of field from the available form fields</h6>","GGF");
		$tooltips["ggf_width_tt"]               = __("<h6>Map Width</h6>Enter the map width in pixels or percentage.</h6>","GGF");
		$tooltips["ggf_height_tt"] 		= __("<h6>Map Height</h6>Enter the map height in pixels or percentage.</h6>","GGF");
		$tooltips["ggf_lat_tt"]    		= __("<h6>Latitude</h6>Enter the latitude of the initial point that will be displayed on the map.</h6>","GGF");
		$tooltips["ggf_long_tt"]   		= __("<h6>Longitude</h6>Enter the longitude of the initial point that will be displayed on the map.</h6>","GGF");
		$tooltips["ggf_autocomplete_tt"] 	= __("<h6>Google's Autocomplete</h6>Add address autocomplete to the address field.</h6>","GGF");
                $tooltips["ggf_locator_fill_tt"] 	= __("<h6>Disable locator button adddress autofill on this field.</h6>","GGF");
		$tooltips["ggf_locator_title_tt"]       = __("<h6>lable for the locator button.</h6>","GGF");
                $tooltips["ggf_map_type_tt"]            = __("<h6>Select the map type.</h6>","GGF");
                $tooltips["ggf_zoom_level_tt"]          = __("<h6>Select the zoom level of the map.</h6>","GGF");
                $tooltips["ggf_locator_title_tt"]       = __("<h6>lable for the locator button.</h6>","GGF");
		$tooltips["ggf_locator_autosubmit_tt"]  = __("<h6>Submit form automatically after location found.</h6>","GGF");
		$tooltips["ggf_locator_hide_submit_tt"] = __("<h6>Hide form's submit button. This can be useful when using the locator button to auto-submit the form after location found.</h6>","GGF");
		return $tooltips;
	}
	
	/**
	 * execute some javascript technicalitites for the field to load correctly
	 */
	function js_editor(){ 
	?>
	
		<script type='text/javascript'>
		
			jQuery(document).ready(function($) {
                                
				$('#ggf-additional-fields').change(function() {
					if ( jQuery(this).val() == 'address' ) { 
						jQuery('#field-ggf-locator-fill').closest('li').show();
						jQuery('#field-ggf-locator-fill').removeAttr('disabled');
					} else { 
						jQuery('#field-ggf-locator-fill').closest('li').hide();
						jQuery('#field-ggf-locator-fill').attr('disabled','disabled');
					}
				});
                                
                                $('#field-ggf-autocomplete').change(function() {
					if ( jQuery(this).is(":checked") ) { 
						jQuery('#field-ggf-update-map').closest('li').show();
						jQuery('#field-ggf-update-map').removeAttr('disabled');
					} else { 
						jQuery('#field-ggf-update-map').closest('li').hide();
						jQuery('#field-ggf-update-map').attr('disabled','disabled');
					}
				});
	
				fieldSettings["mapIcons"] 	= ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting"; 
				fieldSettings["ggfMap"]   	= ".ggf-map-settings , .label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting";
				fieldSettings["ggfLocator"] = ".ggf-locator-settings, .description_setting, .css_class_setting";
                                                        
				jQuery(document).bind("gform_load_field_settings", function(event, field, form){
                                                                    
                                        jQuery("#field-ggf-autocomplete").attr("checked", field["ggf-autocomplete"] == true);
                                        jQuery("#field-ggf-update-map").attr("checked", field["ggf-update-map"] == true);
                                        jQuery("#field-ggf-locator-fill").attr("checked", field["ggf-locator-fill"] == true);
                                        jQuery("#field-ggf-locator-autosubmit").attr("checked", field["ggf-locator-autosubmit"] == true);
                                        jQuery("#field-ggf-locator-hide-submit").attr("checked", field["ggf-locator-hide-submit"] == true);

					jQuery("#field-ggf-map-width").val(field["ggf-map-width"]);
					jQuery("#field-ggf-map-height").val(field["ggf-map-height"]);
					jQuery("#field-ggf-map-latitude").val(field["ggf-map-latitude"]);
					jQuery("#field-ggf-map-longitude").val(field["ggf-map-longitude"]);
                                        jQuery("#field-ggf-map-type").val(field["ggf_map_type"]);
                                        jQuery("#field-ggf-zoom-level").val(field["ggf_zoom_level"]);
					jQuery("#field-ggf-locator-title").val(field["ggf-locator-title"]);
					jQuery("#ggf-additional-fields").val(field["ggf_fields"]);

					if ( jQuery("#ggf-additional-fields").val() == 'address' ) {
						jQuery('#field-ggf-locator-fill').closest('li').show();
						jQuery('#field-ggf-locator-fill').removeAttr('disabled');
					} else {
						jQuery('#field-ggf-locator-fill').closest('li').hide();
						jQuery('#field-ggf-locator-fill').attr('disabled','disabled');
					}
                                        
                                        if ( $('#field-ggf-autocomplete').is(":checked") ) { 
						jQuery('#field-ggf-update-map').closest('li').show();
						jQuery('#field-ggf-update-map').removeAttr('disabled');
					} else { 
						jQuery('#field-ggf-update-map').closest('li').hide();
						jQuery('#field-ggf-update-map').attr('disabled','disabled');
					}
					
				});
			});
		</script>
	<?php 
	}
	
	function render_form( $form ) {
		
		if ( !class_exists('GFUpdatePost') ) return $form;
		
		?>
		<script type="text/javascript">
		
			gform.addFilter("gform_pre_form_editor_save", "ggf_save_form");
		
			function ggf_save_form(form){
	
				var customFields = form.ggf_settings['address_fields']['fields'];
				var i;
				
				for ( i = 0; i < form.fields.length; i++ ) { 
					var field = form.fields[i];
						
					if ( field.ggf_fields == 'address' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['address'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['address'];
						}
					}
					
					if ( field.ggf_fields == 'street' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['street'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['street'];
						}
					} 
					if ( field.ggf_fields == 'apt' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['apt'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['apt'];
						}
					} 
					if ( field.ggf_fields == 'city' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['city'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['state'];
						}
					} 
					if ( field.ggf_fields == 'state' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['state'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['state'];
						}
					}
					if ( field.ggf_fields == 'zipcode' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['zipcode'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['zpicode'];
						}
					} 
					if ( field.ggf_fields == 'country' ) {
						if ( form.ggf_settings['address_fields']['update_post']['autocheck'] == 1 ) form.fields[i].postCustomFieldUnique = true;
						if ( customFields['country'] != '' ) {
							if ( form.ggf_settings['address_fields']['update_post']['use'] == 1 ) form.fields[i].postCustomFieldName = customFields['country'];
						}
					}
				}
						
				return form;
			}
		</script>
		<?php
		//return the form object from the php hook	
		return $form;
	}	
	
}
new GGF_Admin_Edit_Form_Page;