<?php
/*
Plugin Name: Gravity Forms Date Limiter
Plugin URI: http://www.smartredfox.com
Description: Create date fields with limited date ranges.
Version: 0.5
Author: James Botham
Author Email: bothamj@gmail.com
License:

  Copyright 2013 James Botham (bothamj@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class GravityFormsDateLimiter {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	public $name = 'Gravity Forms Date Limiter';
	public $slug = 'gravity_forms_date_limiter';	
    
	/**
	 * Constructor
	 */
	function __construct() {

        //Hook up defines
        $this->plugin_defines();
        
		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_gravity_forms_date_limiter' ) );
	}
  
	/**
	 * Runs when the plugin is initialized
	 */
	function init_gravity_forms_date_limiter() {
		// Setup localization
        // Not needed as using gravityforms
		// load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );        
        
        //Actions
        add_action( "gform_field_input" ,array($this, "wps_limited_date_field_input"), 10, 5 );
            //Attach the settings menu
        add_action('admin_menu', array($this,'wps_limited_date_admin_menu'));
        add_action( "gform_editor_js",array($this,  "wps_gform_editor_js" )); 
        add_action( "gform_field_advanced_settings" ,array($this,  "wps_tos_settings") , 10, 2 );
        add_action( 'gform_enqueue_scripts' , array($this, 'wps_gform_enqueue_scripts') , 10 , 2 );
        add_action("gform_field_css_class", array($this, "custom_class"), 10, 3);
        
        //Filters
        add_filter( 'gform_add_field_buttons', array($this, 'wps_add_tos_field' ),11);
            //Attach save event for Settings page
            //add_filter( 'attachment_fields_to_save', array($this,'wps_limited_date_prettylist_save'), 10, 2 );                
        add_filter("gform_field_type_title", array($this, "wps_limited_date_title"),10,2);
        //add_filter("gform_field_type_title", array($this, "wps_redi_address_title"), 10, 2);
        add_filter('gform_validation', array($this, 'validate_date_limit'),11);  
        
	}

    /** 
     * Defines to be used anywhere in WordPress after the plugin has been initiated. 
     */  
    function plugin_defines()
    {
        define( 'GRAVITY_FORMS_DATE_LIMITER_PATH', trailingslashit( WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__ ),"",plugin_basename( __FILE__ ) ) ) );  
        define( 'GRAVITY_FORMS_DATE_LIMITER_URL' , trailingslashit( WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__ ),"",plugin_basename( __FILE__ ) ) ) );    		
    }
    
    // Add Settings page
	public function wps_limited_date_admin_menu()
	{
        // this is where we add our plugin to the admin menu
        $page = add_options_page($this->slug, 'Gravity Forms - Date Limiter', 'manage_options', dirname(__FILE__), array($this,'gravity_forms_date_limiter_settings_page'));
	}
    
    //Get the options page from an include file
	function gravity_forms_date_limiter_settings_page()
	{
        include(GRAVITY_FORMS_DATE_LIMITER_PATH . 'includes/gforms_date_limiter_settings.php');
	}    
    
    
    // Add a custom field button to the advanced to the field editor
    function wps_add_tos_field( $field_groups ) {
        foreach( $field_groups as &$group ){
            //TODO:Make this an option
            if( $group["name"] == "advanced_fields" ){ // to add to the Advanced Fields
                //if( $group["name"] == "standard_fields" ){ // to add to the Standard Fields
                //if( $group["name"] == "post_fields" ){ // to add to the Standard Fields
                $group["fields"][] = array(
				    "class"=>"button",
				    "value" => __("Date in range", "gravityforms"),
				    "onclick" => "StartAddField('tos');"
			    );
                break;
            }
        }
        return $field_groups;
    }

    // Adds title to GF custom field
    function wps_limited_date_title($title, $field_type ) {
        //var_dump($type);
	    if ( $field_type == "tos" ){        
		    return __( 'Limited date picker' , 'gravityforms' );
        }
        else{
            return $title;
        }
    }

    // Adds the input area to the external side
    function wps_limited_date_field_input ( $input, $field, $value, $lead_id, $form_id ){

        //Add datepicker
        wp_enqueue_script('jquery-ui-datepicker');

        if ( $field["type"] == "tos" ) {
               
		    $max_chars = "";
		    if(!IS_ADMIN && !empty($field["maxLength"]) && is_numeric($field["maxLength"])){
			    $max_chars = self::get_counter_script($form_id, $field_id, $field["maxLength"]);
            }

		    $input_name = $form_id .'_' . $field["id"];
		    $tabindex = GFCommon::get_tabindex();
		    $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';    

            return sprintf("<div class='ginput_container'><input name='input_%s' id='%s' class='datepicker daterange %s' $tabindex rows='10' cols='50' value='%s' data-mindate='%s' data-maxdate='%s'/></div>{$max_chars}", $field["id"], 'daterange-'.$field['id'] , $field["type"] . ' ' . esc_attr( $css ) . ' ' . $field['size'] , esc_html($value), @$field["field_date_range_min"],  @$field["field_date_range_max"]);

        }

        return $input;
    }

    // Now we execute some javascript technicalities for the field to load correctly
    function wps_gform_editor_js(){
    
    //Get date format
    $dateFormat = get_option('gforms_date_limiter_date_format') == '' ? 'dd/mm/yy' : get_option('gforms_date_limiter_date_format');

    ?>

    <script type='text/javascript'>

	    jQuery(document).ready(function($) {
		    //Add field in alongside others that are needed
		    fieldSettings["tos"] = ".label_setting, .description_setting, .admin_label_setting, .rules_setting, .default_value_setting, .error_message_setting, .css_class_setting, .visibility_setting, .date_range_setting";

            //Fire datepicker
            $('.datepickerAdmin').datepicker({dateFormat:<?php echo "'" . $dateFormat . "'" ?>});
        
		    //binding to the load field settings event to initialize the checkbox
		    $(document).bind("gform_load_field_settings", function(event, field, form){
			    jQuery("#field_date_range_min").val(field["field_date_range_min"]);
                jQuery("#field_date_range_max").val(field["field_date_range_max"]);
		    });
	    });

    </script>
    <?php
    }

    // Add a custom setting to the tos advanced field    
    function wps_tos_settings( $position, $form_id ){
        //TODO:Add a before/after than today option
        //Checkboxes for this?
        
	    // Create settings on position 50 (right after Field Label)
	    if( $position == 50 ){
	    ?>
        <li class="date_range_setting field_setting" style="display: list-item;">
	        <div style="clear:both;"><?php _e("Date range", "gravityforms"); ?> <a href="#" onclick="return false;" class="tooltip tooltip_form_field_number_range" tooltip="&lt;h6&gt;<?php _e("Date Range", "gravityforms"); ?>&lt;/h6&gt;<?php _e("Enter the minimum and maximum dates for this form field.  This will require that the date entered by the user must fall within this range.", "gravityforms"); ?>">(?)</a></div>
	        <div style="width:105px; float:left;">
		        <input type="text" id="field_date_range_min" style="width:100px" class="datepickerAdmin" value="" onchange="SetFieldProperty('field_date_range_min', this.value);" />
		        <label for="field_date_range_min"><?php _e("Start date", "gravityforms"); ?></label>
	        </div>
	        <div style="width:105px; float:left;">
		        <input type="text" id="field_date_range_max" style="width:100px" class="datepickerAdmin" value="" onchange="SetFieldProperty('field_date_range_max', this.value);" />
		        <label for="field_date_range_max"><?php _e("End date", "gravityforms"); ?></label>
	        </div>
	        <br class="clear">
        </li>
	    <?php
	    }
    }

    // Add a script to the display of the particular form only if tos field is being used    
    function wps_gform_enqueue_scripts( $form, $ajax ) {    
	    // cycle through fields to see if tos is being used
	    foreach ( $form['fields'] as $field ) {
		    if ( $field['type'] == 'tos' ) {
                //Get date format
                $dateFormat = get_option('gforms_date_limiter_date_format');
                //Parameters to be passed to main script
                $params = array('dateFormat' => $dateFormat = '' ? 'dd/mm/yyyy' : $dateFormat);
                 
			    $url = GRAVITY_FORMS_DATE_LIMITER_URL . 'js/gform_daterange.js';
			    wp_enqueue_script( "gform_tos_script", $url , array("jquery"), '1.0' );
                wp_localize_script( 'gform_tos_script', 'GformsDateLimiterParams', $params );
                
                wp_enqueue_script('jquery-ui-datepicker');
			    break;
		    }
	    }
    }

    // Add a custom class to the field li    
    function custom_class($classes, $field, $form){

        if( $field["type"] == "tos" ){
            $classes .= " gform_limited_date";
        }

        return $classes;
    }

    //Validation
    // 1 - Tie our validation function to the 'gform_validation' hook    
    function validate_date_limit($validation_result) {

        // 2 - Get the form object from the validation result
        $form = $validation_result["form"];

        // 3 - Get the current page being validated
        $current_page = rgpost('gform_source_page_number_' . $form['id']) ? rgpost('gform_source_page_number_' . $form['id']) : 1;

        // 4 - Loop through the form fields
        foreach($form['fields'] as &$field){
    
            // 5 - If the field does not have our designated CSS class, skip it
            if($field['type'] !== 'tos'){
                continue;
            }

            // 6 - Get the field's page number
            $field_page = $field['pageNumber'];

            // 7 - Check if the field is hidden by GF conditional logic
            $is_hidden = RGFormsModel::is_field_hidden($form, $field, array());

            // 8 - If the field is not on the current page OR if the field is hidden, skip it
            if($field_page != $current_page || $is_hidden){
                continue;
            }

            // 9 - Get the submitted value from the $_POST
            $field_value = rgpost("input_{$field['id']}");

            // 10 - Make a call to your validation function to validate the value
            $is_valid = $this->isDateInRange($field_value,@$field['field_date_range_min'], @$field['field_date_range_max']);

            // 11 - If the field is valid we don't need to do anything, skip it
            if($is_valid){
                continue;
            }

            // 12 - The field field validation, so first we'll need to fail the validation for the entire form
            $validation_result['is_valid'] = false;

            // 13 - Next we'll mark the specific field that failed and add a custom validation message
            $field['failed_validation'] = true;
        
            
            //Create error message
            if($field['errorMessage'] == ''){
                if(@$field['field_date_range_min'] && @$field['field_date_range_max']){
                    $field['validation_message'] = 'This date must be between ' . @$field['field_date_range_min'] . ' and ' . @$field['field_date_range_max'] ;
                }
                elseif(@$field['field_date_range_min']){
                    $field['validation_message'] = 'This date must be after ' . @$field['field_date_range_min'];
                }
                elseif(@$field['field_date_range_max']){
                    $field['validation_message'] = 'This date must be before ' . @$field['field_date_range_max'];
                }
            }
            else{
                //Replace %%startdate%% %%enddate%% for custom messages    
                $field['errorMessage'] = str_replace ('%%startdate%%' , $field['field_date_range_min'] , $field['errorMessage']);
                $field['validation_message'] = str_replace ('%%enddate%%' , $field['field_date_range_max'] , $field['errorMessage']);
            }           
        }

        // 14 - Assign our modified $form object back to the validation result
        $validation_result['form'] = $form;

        // 15 - Return the validation result
        return $validation_result;
    }

    function isDateInRange($date_to_compare,$start_date,$end_date){
       
        //Get date format
        $dateFormat = get_option('gforms_date_limiter_date_format') == '' ? 'dd/mm/yy' : get_option('gforms_date_limiter_date_format');
    
        //If blank
        if($date_to_compare == ''){return false;}
        
        //if european date
        if('dd/mm/yy' == $dateFormat){
            $date_to_compare = str_replace('/','.',$date_to_compare);
            $start_date = str_replace('/','.',$start_date);
            $end_date = str_replace('/','.',$end_date);
        }
    
        //Both dates specified
        if($start_date != '' && $end_date != ''){
            $isValid = date_parse($date_to_compare) > date_parse($start_date) && date_parse($date_to_compare) < date_parse($end_date);
            return $isValid;
        }
        
        //Just start
        if($start_date != ''){
            $isValid = date_parse($date_to_compare) > date_parse($start_date);
            return $isValid;
        }        
        
        //Just end
        if($end_date != ''){                    
            $isValid = date_parse($date_to_compare) < date_parse($end_date);            
            return $isValid;
        }     
        
        return false;
            
    }        
  
} // end class
new GravityFormsDateLimiter();

?>