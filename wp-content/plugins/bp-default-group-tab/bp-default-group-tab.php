<?php

/**
 * Plugin Name: BP Default Group Tab
 * Version: 1.0.1
 * Plugin URI: http://buddydev.com/bp-default-group-tab/
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh/
 * Description: Allows Site Admin/Group admin to select default landing page for each group
 * 
 * License: GPL
 * Last Updated: Aproil 7, 2014
 *
 */
class BP_Grous_Defalt_Tab_Helper {

    private static $instance;

    private function __construct() {

        //show the tab selector
        ////another limitation can not show on creation step
        //
        //add_action('bp_before_group_settings_creation_step', array($this, 'show_settings'));
        add_action('bp_after_group_settings_admin', array( $this, 'show_settings' ), 1000 );
        //update settings
        add_action( 'groups_group_settings_edited', array( $this, 'update_settings' ) );
        add_action( 'groups_create_group', array( $this, 'update_settings' ) );
        add_action( 'groups_update_group', array( $this, 'update_settings' ) );
        //add on wp-admin edit group page
        //add_action('bp_groups_admin_meta_boxes',array($this,'add_metabox'));
        //switch to default tab
        add_filter( 'bp_groups_default_extension', array( $this, 'switch_default_tab' ), 50 );
    }

    /**
     * 
     * @return BP_Grous_Defalt_Tab_Helper
     */
    public static function get_instance() {

        if ( !isset( self::$instance ) )
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Switched default tab of a group based on preference
     * 
     * @param type $default_tab
     * @return type
     */
    function switch_default_tab( $default_tab ) {

        $group = groups_get_current_group(); //get the current group

        if ( empty( $group ) )
            return $default_tab;

        $selected_tab = bpdgt_get_default_tab( $group->id );
        
        if (!empty( $selected_tab ) )
            $default_tab = $selected_tab;



        return $default_tab;
    }

    /**
     * Though It would have been awesome to have it in the admin panel, I am not sure if it will not cause any issue
     * BP does not have a way to know which group extensions are active, so I am unable to fetch the list in admin panel
     * 
     */
    function add_metabox() {

        add_meta_box( 'bp_group_default_tab_settings', _x( 'Default Landing page', 'Single Group admin screen in wp-dashboard', 'bp-default-group-tab' ), array( $this, 'render_metabox' ), get_current_screen()->id, 'side', 'core' );
    }

    //not used in v 1.0
    function render_metabox() {
        $this->show_settings_box_content( $_GET['gid'] );
    }

    function show_settings_box_content( $current_group_id ) {
        
        if ( !$current_group_id )
            return;
        ?>

        <div id="bpdgt-settings" class="select">

        <?php
        global $bp;
        //the current landing tab
        $selected_tab = bpdgt_get_default_tab( $current_group_id );

        //find current group                
        $current_group = new BP_Groups_Group( $current_group_id );

        //we need slug to access the nav items
        $current_group_slug = $current_group->slug;
        //get all nav items
        $tabs = $bp->bp_options_nav[$current_group_slug];
       
        //let us reset the admin option from the tab
        foreach ( $tabs as $key => $tab ) {
            if ( $tab['slug'] == 'admin' ) {
                unset( $tabs[$key] );
                break;
            }
        }
        //reset the empty items
        $tabs = array_filter( $tabs );
        ?>
            <h4 class='defaultlanding-tab-settings-title'><?php _e( 'Group Landing Component', 'bp-default-group-tab' );?> </h4>
        
            <label> <?php _e( 'Select default landing component','bp-default-group-tab' );?>
                <select name="group_default_tab">
                <?php
                    foreach ( $tabs as $tab ):
                        $name = $tab['name'];
                    //we need to strip off the span
                    $span_pos = stripos( $name, '<span>' );
                    if( $span_pos )
                        $name = substr ($name, 0,$span_pos);
                        echo "<option value='" . $tab['slug'] . "'" . selected( $tab['slug'], $selected_tab ) . " >{$name}</option>";
                    endforeach;

                ?>
                </select>
            </label>
        </div>

            <?php
        }

        function show_settings() {
            
            if( is_super_admin() || groups_is_user_admin( get_current_user_id(), bp_get_current_group_id() ) )
                $this->show_settings_box_content( bp_get_current_group_id() );
        }

        public function update_settings( $group_id ) {
            
            $tab = $_POST['group_default_tab'];
            
            if ( !empty( $tab ) )
                bpdgt_update_default_tab( $group_id, $tab );
        }

    }

    BP_Grous_Defalt_Tab_Helper::get_instance();

    
    
    function bpdgt_get_default_tab( $group_id ) {

        return groups_get_groupmeta( $group_id, 'default_landing_tab' );
    }

    function bpdgt_update_default_tab( $group_id, $tab ) {

        return groups_update_groupmeta( $group_id, 'default_landing_tab', $tab );
    }
    