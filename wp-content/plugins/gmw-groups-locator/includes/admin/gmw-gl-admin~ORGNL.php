<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * GMW_GL_Admin class
 */

class GMW_GL_Admin {

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {

        $this->add_ons  = get_option('gmw_addons');
        $this->settings = get_option('gmw_options');

        //check if we are in new/edit post page
        if (in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'post.php', 'page.php', 'page-new'))) {
            include_once GMW_PT_PATH . 'includes/admin/gmw-pt-metaboxes.php';
        }

        add_filter('gmw_admin_settings', array($this, 'settings_init'), 1);
        add_filter('gmw_admin_new_form_button', array($this, 'new_form_button'), 10, 1);
        add_filter('gmw_groups_form_settings', array($this, 'form_settings_init'), 10, 1);

        //main settings
        add_action('gmw_main_settings_groups_admin_tab_settings', array($this, 'groups_admin_tab_settings'), 10, 4);
        add_action('gmw_main_settings_location_tab_settings', array($this, 'location_tab_settings'), 10, 4);
        add_action('gmw_main_settings_group_members_tab_settings', array($this, 'group_members_tab_settings'), 10, 4);

        //form settings
        //posts locator form settings
        add_action('gmw_groups_form_settings_search_form_template', array($this, 'form_settings_search_form_template'), 10, 4);
        add_action('gmw_groups_form_settings_post_types', array($this, 'form_settings_post_types'), 10, 4);
        add_action('gmw_groups_form_settings_address_field', array($this, 'form_settings_address_field'), 10, 4);
        add_action('gmw_groups_form_settings_results_template', array($this, 'form_settings_results_template'), 10, 4);
        add_action('gmw_groups_form_settings_auto_results', array($this, 'form_settings_auto_results'), 10, 4);
        add_action('gmw_groups_form_settings_group_avatar', array($this, 'group_avatar'), 10, 4);
        add_action('gmw_groups_form_settings_show_excerpt', array($this, 'show_excerpt'), 10, 4);
        add_action('gmw_groups_form_settings_form_taxonomies', array($this, 'form_taxonomies'), 10, 4);

        //global maps settings
        add_filter('gmw_gmaps_groups_form_settings', array($this, 'gmaps_groups_form_settings_init'), 10, 1);
        add_action('gmw_gmaps_groups_form_settings_groups_info_window_theme', array($this, 'groups_info_window_theme'), 1, 4);
    }

    /**
     * New form button function.
     *
     * @access public
     * @return $buttons
     */
    public function new_form_button($buttons) {

        $buttons[15] = array(
            'name'       => 'groups',
            'addon'      => 'groups_locator',
            'title'      => __('Groups Locator', 'GMW-GL'),
            'link_title' => __('Create new Groups Locator form', 'GMW-GL'),
            'prefix'     => 'gl',
            'color'      => 'E6CCC3'
        );

        //check if global maps add-on is install and activated
        if (GEO_my_WP::gmw_check_addon('global_maps')) {

            $buttons[16] = array(
                'name'       => 'gmaps_groups',
                'addon'      => 'global_maps',
                'title'      => __('Groups Global Map', 'GMW-GL'),
                'link_title' => __('Create new Buddypress groups global map', 'GMW-GL'),
                'prefix'     => 'gmgl',
                'color'      => 'E6E1C3'
            );
        }

        return $buttons;
    }

    /**
     * addon settings page function.
     *
     * @access public
     * @return $settings
     */
    public function settings_init($settings) {

        $settings['groups_locator'] = array(
            __('Groups Locator', 'GMW-GL'),
            array(
                array(
                    'name'  => 'groups_admin_tab_settings',
                    'std'   => '',
                    'label' => __('Location tab - group\'s admin', 'GMW-GL'),
                    'desc'  => __("Choose between single address field or multiple address field that will be avalibale when creating or editing location of a group.", 'GMW-GL'),
                    'type'  => 'function'
                ),
                array(
                    'name'  => 'location_tab_settings',
                    'std'   => '',
                    'label' => __('Location tab - displayed group', 'GMW-GL'),
                    'desc'  => __('These are the settings for the "Location" tab of a group that being displayed: <br />1) Choose the height and width of a map in percentage or pixels and choose the map type. <br /> 2)You can also display "Get Directions" link that will open a new window of google map that displays the location of the group.', 'GMW-GL'),
                    'type'  => 'function'
                ),
                array(
                    'name'  => 'group_members_tab_settings',
                    'std'   => '',
                    'label' => __('Group\'s Members tab', 'GMW-GL'),
                    'desc'  => __('These are the settings for the "Members" tab of a group: <br /> 1) You can chosoe to display a map showing the location of the group members. Set the width and height of the map in percentage or pixels and choose the map type. <br /> 2)Display the address of each group member in the results.', 'GMW-GL'),
                    'type'  => 'function'
                ),
            ),
        );

        return $settings;
    }

    /**
     * Post types main settings
     *
     */
    public function groups_admin_tab_settings($gmw_options, $section, $option) {
        ?>	
        <div>
            <div>

                <span><?php _e('Single Address Field', 'GMW-GL'); ?></span>
                <input type="radio" value="single" class="gmw-group-address-use" name="<?php echo 'gmw_options[' . $section . '][location_tab][address_field][use]'; ?>" <?php echo 'checked="checked"'; ?> size="8">
                &nbsp;&nbsp;&#124;&nbsp;&nbsp;
                <span><?php _e('Multiple Address Fields', 'GMW-GL'); ?></span>
                <input type="radio" value="multiple" class="gmw-group-address-use" name="<?php echo 'gmw_options[' . $section . '][location_tab][address_field][use]'; ?>" <?php if (isset($gmw_options['groups_locator']['location_tab']['address_field']['use']) && $gmw_options['groups_locator']['location_tab']['address_field']['use'] == 'multiple') echo 'checked="checked"'; ?> size="8">

            </div>
            <br />
            <div>
                <div id="group-address-use-wrapper" <?php if (!isset($gmw_options['groups_locator']['location_tab']['address_field']['use']) || $gmw_options['groups_locator']['location_tab']['address_field']['use'] == 'single') echo 'style="display:none"'; ?>>
                    <?php $addressFields = array('street', 'apt', 'city', 'state', 'zipcode', 'country'); ?>
                    <table style="width:200px">
                        <tr>
                            <th style="border:1px solid #dedede;background:#fefefe"><?php _e('Field', 'GMW-GL') ?></th>
                            <th style="border:1px solid #dedede;background:#fefefe"><?php _e('Use', 'GMW-GL') ?></th>
                            <th style="border:1px solid #dedede;background:#fefefe"><?php _e('Title', 'GMW-GL') ?></th>
                        </tr>
                        <?php foreach ($addressFields as $field) : ?>
                            <tr>
                                <td style="height:10px;border:1px solid #dedede"><?php echo $field; ?></td>
                                <td style="height:10px;border:1px solid #dedede"><input type="checkbox" value="<?php echo $field; ?>" name="<?php echo 'gmw_options[' . $section . '][location_tab][address_field][fields][use][]'; ?>" <?php if (isset($gmw_options['groups_locator']['location_tab']['address_field']['fields']['use']) && in_array($field, $gmw_options['groups_locator']['location_tab']['address_field']['fields']['use'])) echo 'checked="checked"'; ?> /></td>
                                <td style="height:10px;border:1px solid #dedede"><input type="text" value="<?php if (isset($gmw_options['groups_locator']['location_tab']['address_field']['fields']['title'][$field])) echo $gmw_options['groups_locator']['location_tab']['address_field']['fields']['title'][$field]; ?>" name="<?php echo 'gmw_options[' . $section . '][location_tab][address_field][fields][title][' . $field . ']'; ?>" /></td>
                            </tr>
                        <?php endforeach; ?>	
                    </table>
                </div>
                <script>
                    jQuery(document).ready(function($) {
                        $('.gmw-group-address-use').click(function() {
                            $('#group-address-use-wrapper').slideToggle();
                        });
                    });
                </script>
            </div>

        </div>
        <?php
    }

    /**
     * Group Location tab settings
     *
     */
    public function location_tab_settings($gmw_options, $section, $option) {

        $map_width  = ( isset($gmw_options['groups_locator']['location_tab']['map']['width']) && !empty($gmw_options['groups_locator']['location_tab']['map']['width']) ) ? $gmw_options['groups_locator']['location_tab']['map']['width'] : '100%';
        $map_height = ( isset($gmw_options['groups_locator']['location_tab']['map']['height']) && !empty($gmw_options['groups_locator']['location_tab']['map']['height']) ) ? $gmw_options['groups_locator']['location_tab']['map']['height'] : '250px';
        $map_type   = ( isset($gmw_options['groups_locator']['location_tab']['map']['type']) ) ? $gmw_options['groups_locator']['location_tab']['map']['type'] : 'ROADMAP';
        ?>	
        <div>
            <span><?php _e('Map Width: ', 'GMW-GL'); ?></span>
            <input type="text" name="<?php echo 'gmw_options[' . $section . '][location_tab][map][width]'; ?>" value="<?php echo $map_width; ?>" size="8">
        </div>
        <br />
        <div>
            <span><?php _e('Map Height: ', 'GMW-GL'); ?></span>
            <input type="text" name="<?php echo 'gmw_options[' . $section . '][location_tab][map][height]'; ?>" value="<?php echo $map_height; ?>" size="8">    
        </div>
        <br />
        <div>
            <span><?php _e('Map Type', 'GMW-GL'); ?></span>
            <?php
            echo
            '<select name="gmw_options[' . $section . '][location_tab][map][type]">
                                    <option value="ROADMAP">' . __('ROADMAP', 'GMW-GL') . '</option>
                                    <option value="SATELLITE" ';
            if ($map_type == "SATELLITE")
                echo 'selected="selected"'; echo '>' . __('SATELLITE', 'GMW-GL') . '</option>
                                    <option value="HYBRID" ';
            if ($map_type == "HYBRID")
                echo 'selected="selected"'; echo '>' . __('HYBRID', 'GMW-GL') . '</option>
                                    <option value="TERRAIN" ';
            if ($map_type == "TERRAIN")
                echo 'selected="selected"'; echo '>' . __('TERRAIN', 'GMW-GL') . '</option>
                            </select>'
            ?>
        </div>
        <br />
        <div>
            <input type="checkbox" name="<?php echo 'gmw_options[' . $section . '][location_tab][get_directions]'; ?>" size="15" value="1" <?php if (isset($gmw_options['groups_locator']['location_tab']['get_directions'])) echo 'checked="checked"'; ?>  />
            <label><?php echo _e('"Get Directions" link', 'GMW-GL'); ?></label>
        </div>
        <?php
    }

    /**
     * Group members tab settings
     *
     */
    public function group_members_tab_settings($gmw_options, $section, $option) {

        $map_width  = ( isset($gmw_options['groups_locator']['members_tab']['map']['width']) && !empty($gmw_options['groups_locator']['members_tab']['map']['width']) ) ? $gmw_options['groups_locator']['members_tab']['map']['width'] : '100%';
        $map_height = ( isset($gmw_options['groups_locator']['members_tab']['map']['height']) && !empty($gmw_options['groups_locator']['members_tab']['map']['height']) ) ? $gmw_options['groups_locator']['members_tab']['map']['height'] : '250px';
        $map_type   = ( isset($gmw_options['groups_locator']['members_tab']['map']['type']) ) ? $gmw_options['groups_locator']['members_tab']['map']['type'] : 'ROADMAP';
        ?>	
        <div>
            <input type="checkbox" name="<?php echo 'gmw_options[' . $section . '][members_tab][map][on]'; ?>" size="15" value="1" <?php if (isset($gmw_options['groups_locator']['members_tab']['map']['on'])) echo 'checked="checked"'; ?>  />
            <label><?php echo _e("Display Map", 'GMW-GL'); ?></label>
        </div>
        <br />
        <div>
            <span><?php _e('Map Width: ', 'GMW-GL'); ?></span>
            <input type="text" name="<?php echo 'gmw_options[' . $section . '][members_tab][map][width]'; ?>" value="<?php echo $map_width; ?>" size="8">    
        </div>
        <br />
        <div>
            <span><?php _e('Map Height: ', 'GMW-GL'); ?></span>
            <input type="text" name="<?php echo 'gmw_options[' . $section . '][members_tab][map][height]'; ?>" value="<?php echo $map_height; ?>" size="8">    
        </div>
        <br />
        <div>
            <span><?php _e('Map Type', 'GMW-GL'); ?></span>
            <?php
            echo
            '<select name="gmw_options[' . $section . '][members_tab][map][type]">
                                <option value="ROADMAP">' . __('ROADMAP', 'GMW-GL') . '</option>
                                <option value="SATELLITE" ';
            if ($map_type == "SATELLITE")
                echo 'selected="selected"'; echo '>' . __('SATELLITE', 'GMW-GL') . '</option>
                                <option value="HYBRID" ';
            if ($map_type == "HYBRID")
                echo 'selected="selected"'; echo '>' . __('HYBRID', 'GMW-GL') . '</option>
                                <option value="TERRAIN" ';
            if ($map_type == "TERRAIN")
                echo 'selected="selected"'; echo '>' . __('TERRAIN', 'GMW-GL') . '</option>
                        </select>';
            ?>
        </div>
        <br />
        <div>
            <input type="checkbox" name="<?php echo 'gmw_options[' . $section . '][members_tab][member_address][on]'; ?>" size="15" value="1" <?php if (isset($gmw_options['groups_locator']['members_tab']['member_address']['on'])) echo 'checked="checked"'; ?>  />
            <label><?php echo _e("Display Address", 'GMW-GL'); ?></label>
        </div>
        <?php
    }

    /**
     * search form template
     * @param unknown_type $gmw_forms
     * @param unknown_type $formID
     * @param unknown_type $section
     * @param unknown_type $option
     */
    public function form_settings_search_form_template($gmw_forms, $formID, $section, $option) {
        ?>
        <div>
            <select name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][form_template]'; ?>">
                <?php foreach (glob(GMW_GL_PATH . '/search-forms/*', GLOB_ONLYDIR) as $dir) { ?>
                    <option value="<?php echo basename($dir); ?>" <?php if (isset($gmw_forms[$formID][$section]['form_template']) && $gmw_forms[$formID][$section]['form_template'] == basename($dir)) echo 'selected="selected"'; ?>><?php echo basename($dir); ?></option>
                <?php } ?>

                <?php foreach (glob(STYLESHEETPATH . '/geo-my-wp/groups/search-forms/*', GLOB_ONLYDIR) as $dir) { ?>
                    <?php $cThems = 'custom_' . basename($dir) ?>
                    <option value="<?php echo $cThems; ?>" <?php if (isset($gmw_forms[$formID][$section]['form_template']) && $gmw_forms[$formID][$section]['form_template'] == $cThems) echo 'selected="selected"'; ?>><?php _e('Custom Form: ', 'GMW-GL'); ?><?php echo basename($dir); ?></option>
                <?php } ?>
            </select>
        </div>
        <?php
    }

    /**
     * address field form settings
     *
     */
    public function form_settings_address_field($gmw_forms, $formID, $section, $option) {
        ?>
        <div>
            <div id="gmw-af">
                <table>
                    <tr>
                        <td style="background: #C2D7EF;padding:5px 15px;"><?php _e('Use', 'GMW-GL'); ?></td>
                        <td style="border:1px solid #bbb;padding:5px">
                            <input type="radio" class="gmw-af-buttons" id="gmw-af-single-btn" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][how]'; ?>" value="single" <?php if (!isset($gmw_forms[$formID][$section]['address_fields']['how']) || $gmw_forms[$formID][$section]['address_fields']['how'] == 'single') echo 'checked="checked"'; ?> /><?php _e('Single field', 'GMW-GL'); ?>
                            <span>
                                <input type="radio" style="margin-left:5px;" class="gmw-af-buttons" id="gmw-af-multiple-btn" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][how]'; ?>" value="multiple" <?php if (isset($gmw_forms[$formID][$section]['address_fields']['how']) && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple') echo 'checked="checked"'; ?>   /><?php _e('Multiple fields', 'GMW-GL'); ?>
                            </span>
                        </td>
                    </tr>
                </table>
                <div id="gmw-af-single">
                    <table class="gmw-saf-table">
                        <tr>
                            <th>Field Type</th>
                            <th>Field Actions</th>
                        </tr>
                        <tr>
                            <td style="border:1px solid #bbb;padding:5px">
                                <?php echo _e('Address', 'GMW-GL'); ?>
                            </td>
                            <td style="border:1px solid #bbb;padding:5px">

                                <?php echo _e('Field Title:', 'GMW-GL'); ?>
                                <input type="text" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_field][title]'; ?>" size="40" value="<?php
                                if (isset($gmw_forms[$formID][$section]['address_field']['title']))
                                    echo $gmw_forms[$formID][$section]['address_field']['title'];
                                else
                                    echo '';
                                ?>" />	

                                <br />

                                <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_field][within]'; ?>" <?php echo ( isset($gmw_forms[$formID][$section]['address_field']['within'])) ? " checked=checked " : ""; ?>>	
                                <?php echo _e('Within the input field', 'GMW-GL'); ?>

                                <br />

                                <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_field][mandatory]'; ?>" <?php echo (isset($gmw_forms[$formID][$section]['address_field']['mandatory'])) ? " checked=checked " : ""; ?>>	
                                <label><?php echo _e('Mandatory Field', 'GMW-GL'); ?></label>

                            </td>
                        </tr>
                    </table>
                </div>
                <div id="gmw-af-multiple" style="display:none;">

                    <?php $addressFields = array('street', 'apt', 'city', 'state', 'zipcode', 'country'); ?>

                    <table class="gmw-saf-table">
                        <tr>
                            <th>Field Type</th>
                            <th style="min-width:115px;">Action</th>
                            <th>Field Info</th>
                        </tr>
                        <?php foreach ($addressFields as $field) : $sy = false; ?>
                            <tr class="gmw-saf">
                                <td>
                                    <span style="padding:0px 5px;font-size: 12px;text-transform: capitalize;"><?php echo $field; ?></span>
                                </td>
                                <td style="padding:3px 5px">
                                    <div style="float:left;padding:4px;">
                                        <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][on]'; ?>" value="exclude" <?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['on']) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'exclude' || empty($gmw_forms[$formID][$section]['address_fields'][$field]['on']) || $gmw_forms[$formID][$section]['address_fields']['how'] == 'single') echo " checked=checked "; ?> />	
                                        <span style="text-transform:capitalize"><?php echo _e('Exclude', 'GMW-GL'); ?></span>

                                        <br />

                                        <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][on]'; ?>" value="include" <?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['on']) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'include' && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple') echo " checked=checked "; ?> />	
                                        <span style="text-transform:capitalize"><?php echo _e('Include', 'GMW-GL'); ?></span>

                                        <br />

                                        <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][on]'; ?>" value="default" <?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['on']) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'default' && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple') echo " checked=checked "; ?> />	
                                        <span style="text-transform:capitalize"><?php echo _e('Pre-defined Value', 'GMW-GL'); ?></span>

                                    </div>
                                </td>

                                <td  style="min-width: 300px;padding:3px 5px">
                                    <div class="gmw-saf-settings" style="flaot:left">
                                        <?php echo _e('Field Title:', 'GMW-GL'); ?>
                                        <input type="text" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][title]'; ?>" size="25" value="<?php echo ( isset($gmw_forms[$formID][$section]['address_fields'][$field]['title']) ) ? $gmw_forms[$formID][$section]['address_fields'][$field]['title'] : ''; ?>" />

                                        <br />

                                        <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][within]'; ?>" <?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['within'])) echo " checked=checked "; ?>>	
                                        <label><?php echo _e('Within the address field', 'GMW-GL'); ?></label>

                                        <br />

                                        <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][mandatory]'; ?>" <?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['mandatory'])) echo " checked=checked "; ?>>	
                                        <label><?php echo _e('Mandatory Field', 'GMW-GL'); ?></label>

                                        <?php /* &nbsp;&nbsp; | &nbsp;&nbsp;
                                          <input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_fields]['.$field.'][dropdown]'; ?>" <?php if ( isset($option['address_fields'][$field]['dropdown']) ) echo " checked=checked "; ?>>
                                          <?php echo _e('Dropdown menu','GMW-GL'); ?>
                                          &nbsp;&nbsp; | &nbsp;&nbsp;
                                          <?php echo _e('dropdown Values:','GMW-GL'); ?>
                                          <textarea style="vertical-align:top;" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_fields]['.$field.'][drop_values]'; ?>" ><?php if ( isset($option['address_fields'][$field]['drop_values']) ) echo $option['address_fields'][$field]['drop_values']; ?></textarea>
                                         */ ?>
                                    </div>
                                    <div class="gmw-saf-default" style="flaot:left">
                                        <?php echo _e('Default Value:', 'GMW-GL'); ?>
                                        <input type="text" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][address_fields][' . $field . '][value]'; ?>" size="15" value="<?php if (isset($gmw_forms[$formID][$section]['address_fields'][$field]['value'])) echo $gmw_forms[$formID][$section]['address_fields'][$field]['value']; ?>" />
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * results template form settings
     *
     */
    public function form_settings_results_template($gmw_forms, $formID, $section, $option) {
        ?>
        <div>
            <select name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][results_template]'; ?>">
                <?php foreach (glob(GMW_GL_PATH . '/search-results/*', GLOB_ONLYDIR) as $dir) { ?>
                    <option value="<?php echo basename($dir); ?>" <?php if (isset($gmw_forms[$formID][$section]['results_template']) && $gmw_forms[$formID][$section]['results_template'] == basename($dir)) echo 'selected="selected"'; ?>><?php echo basename($dir); ?></option>
                <?php } ?>
                <?php foreach (glob(STYLESHEETPATH . '/geo-my-wp/groups/search-results/*', GLOB_ONLYDIR) as $dir) { ?>
                    <?php $cThems = 'custom_' . basename($dir) ?>
                    <option value="<?php echo $cThems; ?>" <?php if (isset($gmw_forms[$formID][$section]['results_template']) && $gmw_forms[$formID][$section]['results_template'] == $cThems) echo 'selected="selected"'; ?>><?php _e('Custom Template:'); ?> <?php echo basename($dir); ?></option>
                <?php } ?>
            </select>
        </div>
        <?php
    }

    /**
     * auto results settings
     *
     */
    public function form_settings_auto_results($gmw_forms, $formID, $section, $option) {
        ?>
        <div>
            <p>
                <input name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][auto_search][on]'; ?>" type="checkbox" value="1" <?php if (isset($gmw_forms[$formID][$section]['auto_search']['on'])) echo "checked='checked'"; ?>/>
                <?php _e('Yes', 'GMW-GL'); ?>
            </p>	
            <p>
                <?php _e('Radius', 'GMW-GL'); ?>		
                <input type="text" id="wppl-auto-radius" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][auto_search][radius]'; ?>" size="5" value="<?php echo ( isset($gmw_forms[$formID][$section]['auto_search']['radius']) ) ? $gmw_forms[$formID][$section]['auto_search']['radius'] : "50"; ?>" />	
            </p>
            <p>
                <select id="wppl-auto-units" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][auto_search][units]'; ?>">
                    <option value="imperial" <?php echo 'selected="selected"'; ?>><?php _e('Miles', 'GMW-GL'); ?></option>
                    <option value="metric"   <?php if (isset($gmw_forms[$formID][$section]['auto_search']['units']) && $gmw_forms[$formID][$section]['auto_search']['units'] == "metric") echo 'selected="selected"'; ?>><?php _e('Kilometers', 'GMW-GL'); ?></option>
                </select>
            </p>
        </div>
        <?php
    }

    /**
     * Get pages
     */
    public function get_pages() {
        $pages = array();

        $pages[''] = __(' -- Same Page -- ', 'GMW-GL');
        foreach (get_pages() as $page) {
            $pages[$page->ID] = $page->post_title;
        }

        return $pages;
    }

    /**
     * locator icons
     */
    public function get_locator_icons() {
        $icons         = array();
        $locator_icons = glob(GMW_PATH . '/assets/images/locator-images/*.png');
        $display_icon  = GMW_IMAGES . '/locator-images/';

        $icons['gmw_na'] = __('Do not use', 'GMW-GL');
        foreach ($locator_icons as $locator_icon) {
            $icons[basename($locator_icon)] = '<img src="' . $display_icon . basename($locator_icon) . '" height="30px" width="30px"/>';
        }
        return $icons;
    }

    /**
     * Featured Image
     */
    public function group_avatar($gmw_forms, $formID, $section, $option) {
        ?>
        <div>
            <p>
                <input type="checkbox" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][avatar][use]'; ?>" value="1" <?php echo ( isset($gmw_forms[$formID][$section]['avatar']['use']) ) ? "checked=checked" : ""; ?> />
                <?php _e('Yes', 'GMW-GL'); ?>
            </p>
            <p>
                <?php _e('Height', 'GMW-GL'); ?>:
                &nbsp;<input type="text" size="5" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][avatar][width]'; ?>" value="<?php echo ( isset($gmw_forms[$formID][$section]['avatar']['width']) && !empty($gmw_forms[$formID][$section]['avatar']['width']) ) ? $gmw_forms[$formID][$section]['avatar']['width'] : '200px'; ?>" />
            </p>
            <p>
                <?php _e('Width', 'GMW-GL'); ?>:
                &nbsp;<input type="text" size="5" name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][avatar][height]'; ?>" value="<?php echo ( isset($gmw_forms[$formID][$section]['avatar']['height']) && !empty($gmw_forms[$formID][$section]['avatar']['height']) ) ? $gmw_forms[$formID][$section]['avatar']['height'] : '200px'; ?>" />
            </p>
        </div>
        <?php
    }

    /**
     * form settings function.
     *
     * @access public
     * @return $settings
     */
    function form_settings_init($settings) {

        $settings['search_form'][1] = array(
            'form_template' => array(
                'name'     => 'form_template',
                'std'      => '',
                'label'    => __('Search Form Template', 'GMW-GL'),
                'desc'     => __('Choose the search form template that you want to use.', 'GMW-GL'),
                'type'     => 'function',
                'function' => 'search_form_template'
            ),
            'address_field' => array(
                'name'     => 'address_field',
                'std'      => '',
                'label'    => __('Address Field', 'GMW-GL'),
                'cb_label' => '',
                'desc'     => __('Type the title for the address field of the search form. for example "Enter your address". this title wll be displayed either next to the address input field or within if you check the checkbox for it. You can also choose to have the address field mandatory which will prevent users from submitting the form if no address entered. Otherwise if you allow the field to be empty and user submit a form with no address the plugin will display all results.', 'GMW-GL'),
                'type'     => 'function',
            ),
            'radius'        => array(
                'name'        => 'radius',
                'std'         => '5,10,15,25,50,100',
                'placeholder' => __('Radius values comma separated', 'GMW-GL'),
                'label'       => __('Radius / Distance', 'GMW-GL'),
                'desc'        => __('Enter distance values in the input box comma separated if you want to have a select dropdown menu of multiple radius values in the search form. If only one value entered it will be the default value of the search form which will be hidden.', 'GMW-GL'),
                'attributes'  => array('size' => '30')
            ),
            'unit'          => array(
                'name'    => 'units',
                'std'     => 'both',
                'label'   => __('Units', 'GMW-GL'),
                'desc'    => __('Choose if to show both type of units as a dropdown or a single default type.', 'GMW-GL'),
                'type'    => 'select',
                'options' => array(
                    'both'     => __('Both', 'GMW-GL'),
                    'imperial' => __('Miles', 'GMW-GL'),
                    'metric'   => __('Kilometers', 'GMW-GL')
                ),
            ),
            'locator_icon'  => array(
                'name'    => 'locator_icon',
                'std'     => 'gmw_na',
                'label'   => __('Locator Icon', 'GMW-GL'),
                'desc'    => __('Choose if to display the locator button in the search form. The locator will get the user&#39;s current location and submit the search form based of the location found. you can choose one of the default icons or you can add icon of your own. ', 'GMW-GL'),
                'type'    => 'radio',
                'options' => $this->get_locator_icons()
            )
        );

        $settings['search_results'][1] = array(
            'results_page'     => array(
                'name'    => 'results_page',
                'std'     => '',
                'label'   => __('Results Page', 'GMW-GL'),
                'desc'    => __('The results page will display the search results in the selected page when using the "GMW Search Form" widget or when you want to have the search form in one page and the results showing in a different page. 
											Choose the results page from the dropdown menu and paste the shortcode [gmw form="results"] into that page. To display the search result in the same page as the search form choose "Same Page" from the select box.', 'GMW-GL'),
                'type'    => 'select',
                'options' => $this->get_pages()
            ),
            'results_template' => array(
                'name'  => 'results_template',
                'std'   => '',
                'label' => __('Results Template', 'GMW-GL'),
                'desc'  => __('Choose The resuls template file (results.php). You can find the search results template files in the <code>plugins folder/geo-my-wp/plugin/posts/search-results</code>. You can modify any of the templates or create your own.
											If you do modify or create you own template files you should create/save them in your theme or child theme folder and the plugin will read them from there. This way your changes will not be removed once the plugin is updated.
											You will need to create the folders and save your results template there <code><strong>themes/your-theme-or-child-theme-folder/geo-my-wp/posts/search-results/your-results-theme-folder</strong></code>.
											Your theme folder will contain the results.php file and another folder named "css" and the style.css within it.', 'GMW-GL'),
                'type'  => 'function',
            ),
            'auto_results'     => array(
                'name'  => 'auto_results',
                'std'   => '',
                'label' => __('Auto Results', 'GMW-GL'),
                'desc'  => __('Will automatically run initial search and display results based on the user\'s current location (if exists via cookies) when he/she first goes to a search page. You need to define the radius and the units for this initial search .', 'GMW-GL'),
                'type'  => 'function'
            ),
            'display_groups'   => array(
                'name'     => 'display_groups',
                'std'      => '',
                'label'    => __('Display groups?', 'GMW-GL'),
                'desc'     => __('Display results as list of groups', 'GMW-GL'),
                'type'     => 'checkbox',
                'cb_label' => __('Yes', 'GMW-GL'),
            ),
            'display:map'      => array(
                'name'    => 'display_map',
                'std'     => 'na',
                'label'   => __('Display Map?', 'GMW-GL'),
                'desc'    => __('Display results on map. You can do so automatically above the list of results or manually using the shortcode [gmw map="form ID"].', 'GMW-GL'),
                'type'    => 'radio',
                'options' => array(
                    'na'        => __('No map', 'GMW-GL'),
                    'results'   => __('In results', 'GMW-GL'),
                    'shortcode' => __('Using shortcode', 'GMW-GL'),
                ),
            ),
            'groups_avatar'    => array(
                'name'     => 'group_avatar',
                'std'      => '',
                'label'    => __('Group Avatar', 'GMW-GL'),
                'cb_label' => '',
                'desc'     => __('Display group\'s avatar and define its width and height in pixels or percentage.', 'GMW-GL'),
                'type'     => 'function',
            ),
            'per_page'         => array(
                'name'        => 'per_page',
                'std'         => '5,10,15,25',
                'placeholder' => __('Enter values', 'GMW-GL'),
                'label'       => __('Results Per Page', 'GMW-GL'),
                'desc'        => __('Choose the number of results per page. By setting a single value you set the default number of results per page. By giving multiple values, comma separated, a select box will be created and the users will be able to set the number of results per page.', 'GMW-GL'),
                'attributes'  => array('style' => 'width:170px')
            ),
            'by_driving'       => array(
                'name'       => 'by_driving',
                'std'        => '',
                'label'      => __('Driving Distance', 'GMW-GL'),
                'cb_label'   => __('Yes', 'GMW-GL'),
                'desc'       => __('While the results showing the radius distance from the user to each of the locations, this feature let you display the exact driving distance. Please note that each driving distance request counts with google API when you can have 2500 requests per day.', 'GMW-GL'),
                'type'       => 'checkbox',
                'attributes' => array()
            ),
            'get_directions'   => array(
                'name'       => 'get_directions',
                'std'        => '',
                'label'      => __('Get Directions Link', 'GMW-GL'),
                'cb_label'   => __('Yes', 'GMW-GL'),
                'desc'       => __('Display "get directions" link that will open a new window with google map that shows the exact driving direction from the user to the location.', 'GMW-GL'),
                'type'       => 'checkbox',
                'attributes' => array()
            ),
        );
        $settings['results_map'][1]    = array(
            'map_width'  => array(
                'name'        => 'map_width',
                'std'         => '100%',
                'placeholder' => __('Map width in px or %', 'GMW-GL'),
                'label'       => __('Map Width', 'GMW-GL'),
                'desc'        => __('Enter the map\'s width in pixels or percentage. ex. 100% or 200px', 'GMW-GL'),
                'attributes'  => array('size' => '7')
            ),
            'map_height' => array(
                'name'        => 'map_height',
                'std'         => '300px',
                'placeholder' => __('Map height in px or %', 'GMW-GL'),
                'label'       => __('Map Height', 'GMW-GL'),
                'desc'        => __('Enter the map\'s height in pixels or percentage. ex. 100% or 200px', 'GMW-GL'),
                'attributes'  => array('size' => '7')
            ),
            'map_type'   => array(
                'name'    => 'map_type',
                'std'     => 'ROADMAP',
                'label'   => __('Map Type', 'GMW-GL'),
                'desc'    => __('Choose the map type', 'GMW-GL'),
                'type'    => 'select',
                'options' => array(
                    'ROADMAP'   => __('ROADMAP', 'GMW-GL'),
                    'SATELLITE' => __('SATELLITE', 'GMW-GL'),
                    'HYBRID'    => __('HYBRID', 'GMW-GL'),
                    'TERRAIN'   => __('TERRAIN', 'GMW-GL')
                ),
            ),
            'zoom_level' => array(
                'name'    => 'zoom_level',
                'std'     => 'auto',
                'label'   => __('Zoom Level', 'GMW-GL'),
                'desc'    => __('Map zoom level', 'GMW-GL'),
                'type'    => 'select',
                'options' => array(
                    'auto' => 'Auto Zoom',
                    '1'    => '1',
                    '2'    => '2',
                    '3'    => '3',
                    '4'    => '4',
                    '5'    => '5',
                    '6'    => '6',
                    '7'    => '7',
                    '8'    => '8',
                    '9'    => '9',
                    '10'   => '10',
                    '11'   => '11',
                    '12'   => '12',
                    '13'   => '13',
                    '14'   => '14',
                    '15'   => '15',
                    '16'   => '16',
                    '17'   => '17',
                    '18'   => '18',
                )
            ),
            'map_frame'  => array(
                'name'       => 'map_frame',
                'std'        => '',
                'label'      => __('Map Frame', 'GMW-GL'),
                'cb_label'   => __('Yes', 'GMW-GL'),
                'desc'       => __('show frame around the map?', 'GMW-GL'),
                'type'       => 'checkbox',
                'attributes' => array()
            ),
        );
        return $settings;
    }

    /**
     * results template form settings posts
     *
     */
    public function groups_info_window_theme($gmw_forms, $formID, $section, $option) {
        ?>
        <div id="gmaps-infobox-themes-dropdown" class="gmaps-themes-dropdown" style="display:none;">
            <select name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][infobox_template]'; ?>">

                <?php foreach (glob(GMW_GL_PATH . '/gmaps/templates/infobox/*', GLOB_ONLYDIR) as $dir) { ?>

                    <option value="<?php echo basename($dir); ?>" <?php if (isset($gmw_forms[$formID][$section]['infobox_template']) && $gmw_forms[$formID][$section]['infobox_template'] == basename($dir)) echo 'selected="selected"'; ?>><?php echo basename($dir); ?></option>

                <?php } ?>

                <?php foreach (glob(STYLESHEETPATH . '/geo-my-wp/groups/info-window-templates/infobox/*', GLOB_ONLYDIR) as $dir) { ?>

                    <?php $cThems = 'custom_' . basename($dir) ?>
                    <option value="<?php echo $cThems; ?>" <?php if (isset($gmw_forms[$formID][$section]['infobox_template']) && $gmw_forms[$formID][$section]['infobox_template'] == $cThems) echo 'selected="selected"'; ?>><?php _e('Custom Template:'); ?> <?php echo basename($dir); ?></option>

                <?php } ?>

            </select>
        </div>

        <div id="gmaps-popup-themes-dropdown" class="gmaps-themes-dropdown" style="display:none;">
            <select name="<?php echo 'gmw_forms[' . $_GET['formID'] . '][' . $section . '][popup_template]'; ?>">

                <?php foreach (glob(GMW_GL_PATH . '/gmaps/templates/popup/*', GLOB_ONLYDIR) as $dir) { ?>

                    <option value="<?php echo basename($dir); ?>" <?php if (isset($gmw_forms[$formID][$section]['popup_template']) && $gmw_forms[$formID][$section]['popup_template'] == basename($dir)) echo 'selected="selected"'; ?>><?php echo basename($dir); ?></option>

                <?php } ?>

                <?php foreach (glob(STYLESHEETPATH . '/geo-my-wp/groups/info-window-templates/popup/*', GLOB_ONLYDIR) as $dir) { ?>

                    <?php $cThems = 'custom_' . basename($dir) ?>
                    <option value="<?php echo $cThems; ?>" <?php if (isset($gmw_forms[$formID][$section]['popup_template']) && $gmw_forms[$formID][$section]['popup_template'] == $cThems) echo 'selected="selected"'; ?>><?php _e('Custom Template:'); ?> <?php echo basename($dir); ?></option>

                <?php } ?>

            </select>
        </div>

        <script>
            jQuery(document).ready(function($) {

                if ($('.setting-iw_type').closest('td').find('input[value="popup"]').is(':checked')) {
                    $('#gmaps-popup-themes-dropdown').slideToggle();
                } else {
                    $('#gmaps-infobox-themes-dropdown').slideToggle();
                }

                $('.setting-iw_type').click(function() {
                    $('.gmaps-themes-dropdown').slideToggle();
                });

            });

        </script>
        <?php
    }

    /**
     * Groups global Maps form settings function.
     *
     * @access public
     * @return $settings
     */
    function gmaps_groups_form_settings_init($settings) {

        //remove the settings we donot need
        unset($settings['search_form']);
        unset($settings['search_results']);

        $general_settings = array('general_settings' => array(
                __('General Settings', 'GMW-GL'),
                array(
                    'form_template' => array(
                        'name'  => 'form_template',
                        'label' => '',
                        'std'   => 'default',
                        'type'  => 'hidden',
                    ),
                    'radius'        => array(
                        'name'        => 'radius',
                        'std'         => '',
                        'placeholder' => __('Radius', 'GMW-GL'),
                        'label'       => __('Radius / Distance', 'GMW-GL'),
                        'desc'        => __('Set radius to search from the user\'s current location when exists. Leave the input box empty if you want to display all exisintg locations.', 'GMW-GL'),
                        'attributes'  => array('size' => '30')
                    ),
                    'units'         => array(
                        'name'    => 'units',
                        'std'     => 'both',
                        'label'   => __('Units', 'GMW-GL'),
                        'desc'    => __('Calculate distance in Miles or Kilometers.', 'GMW-GL'),
                        'type'    => 'select',
                        'options' => array(
                            'imperial' => __('Miles', 'GMW-GL'),
                            'metric'   => __('Kilometers', 'GMW-GL')
                        ),
                    ),
                )
        ));

        $settings['results_map'][1] = array(
            'map_width'        => array(
                'name'        => 'map_width',
                'std'         => '100%',
                'placeholder' => __('Map width in px or %', 'GMW-GL'),
                'label'       => __('Map Width', 'GMW-GL'),
                'desc'        => __('Enter the map\'s width in pixels or percentage. ex. 100% or 200px', 'GMW-GL'),
                'attributes'  => array('size' => '7')
            ),
            'map_height'       => array(
                'name'        => 'map_height',
                'std'         => '300px',
                'placeholder' => __('Map height in px or %', 'GMW-GL'),
                'label'       => __('Map Height', 'GMW-GL'),
                'desc'        => __('Enter the map\'s height in pixels or percentage. ex. 100% or 200px', 'GMW-GL'),
                'attributes'  => array('size' => '7')
            ),
            'map_type'         => array(
                'name'    => 'map_type',
                'std'     => 'ROADMAP',
                'label'   => __('Map Type', 'GMW-GL'),
                'desc'    => __('Choose the map type', 'GMW-GL'),
                'type'    => 'select',
                'options' => array(
                    'ROADMAP'   => __('ROADMAP', 'GMW-GL'),
                    'SATELLITE' => __('SATELLITE', 'GMW-GL'),
                    'HYBRID'    => __('HYBRID', 'GMW-GL'),
                    'TERRAIN'   => __('TERRAIN', 'GMW-GL')
                ),
            ),
            'zoom_level'       => array(
                'name'    => 'zoom_level',
                'std'     => 'auto',
                'label'   => __('Zoom Level', 'GMW-GL'),
                'desc'    => __('Map zoom level', 'GMW-GL'),
                'type'    => 'select',
                'options' => array(
                    'auto' => 'Auto Zoom',
                    '1'    => '1',
                    '2'    => '2',
                    '3'    => '3',
                    '4'    => '4',
                    '5'    => '5',
                    '6'    => '6',
                    '7'    => '7',
                    '8'    => '8',
                    '9'    => '9',
                    '10'   => '10',
                    '11'   => '11',
                    '12'   => '12',
                    '13'   => '13',
                    '14'   => '14',
                    '15'   => '15',
                    '16'   => '16',
                    '17'   => '17',
                    '18'   => '18',
                )
            ),
            'map_controls'     => array(
                'name'    => 'map_controls',
                'std'     => 'true',
                'label'   => __('Map Controls', 'GMW-GL'),
                'desc'    => __('Which map controls would you like to use', 'GMW-GL'),
                'type'    => 'multicheckbox',
                'options' => array(
                    'zoomControl'        => __('Zoom', 'GMW-GL'),
                    'panControl'         => __('Pan', 'GMW-GL'),
                    'scaleControl'       => __('Scale', 'GMW-GL'),
                    'mapTypeControl'     => __('Map Type', 'GMW-GL'),
                    'streetViewControl'  => __('Street View', 'GMW-GL'),
                    'overviewMapControl' => __('Overview', 'GMW-GL'),
                    'scrollwheel'        => __('Scroll Wheel', 'GMW-GL'),
                ),
            ),
            'marker_clusterer' => array(
                'name'       => 'marker_clusterer',
                'std'        => '',
                'label'      => __('Marker Clusterer', 'GMW-GL'),
                'cb_label'   => __('Yes', 'GMW-GL'),
                'desc'       => __('Use marker Clusterer to group near locations.', 'GMW-GL'),
                'type'       => 'checkbox',
                'attributes' => array()
            ),
            'map_frame'        => array(
                'name'       => 'map_frame',
                'std'        => '',
                'label'      => __('Map Frame', 'GMW-GL'),
                'cb_label'   => __('Yes', 'GMW-GL'),
                'desc'       => __('show frame around the map?', 'GMW-GL'),
                'type'       => 'checkbox',
                'attributes' => array()
            ),
        );
        $info_window                = array('info_window' => array(
                __('Marker Window', 'GMW-GL'),
                array(
                    'iw_type'        => array(
                        'name'       => 'iw_type',
                        'std'        => '',
                        'label'      => __('Marker Window Type', 'GMW-GL'),
                        'desc'       => __('What type of marker window would you like to use? "Infobox" will open an info window within the map above the marker or Pop-up window will pop-up an HTML window in the middle of the screen.', 'GMW-GL'),
                        'type'       => 'radio',
                        'options'    => array(
                            'infobox' => 'Infobox',
                            'popup'   => 'Pop-up window'
                        ),
                        'attributes' => array()
                    ),
                    'marker_theme'   => array(
                        'name'  => 'groups_info_window_theme',
                        'std'   => '',
                        'label' => __('Marker Window Theme', 'GMW-GL'),
                        'desc'  => __('Choose the theme that will display the marker window.', 'GMW-GL'),
                        'type'  => 'function'
                    ),
                    'avatar'         => array(
                        'name'       => 'avatar',
                        'std'        => '',
                        'label'      => __('Avatar', 'GMW-GL'),
                        'cb_label'   => __('Yes', 'GMW-GL'),
                        'desc'       => __('Display Group\'s Avatar', 'GMW-GL'),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    'get_directions' => array(
                        'name'       => 'get_directions',
                        'std'        => '',
                        'label'      => __('Get Directions Link', 'GMW-GL'),
                        'cb_label'   => __('Yes', 'GMW-GL'),
                        'desc'       => __('Display get directions link that will open a new window with Google map showting the directions to the location.', 'GMW-GL'),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                )
        ));
        $settings['hidden'][1]      = array(
            'form_template' => array(
                'name'  => 'form_template',
                'label' => '',
                'std'   => false,
                'type'  => 'hidden',
            ),
        );

        $settings = array_merge($general_settings, $settings, $info_window);

        return $settings;
    }

}

new GMW_GL_Admin();
?>