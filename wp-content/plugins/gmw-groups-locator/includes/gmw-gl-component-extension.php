<?php 
if ( class_exists( 'BP_Group_Extension' ) ) :

    class GMW_Group_Extension extends BP_Group_Extension {
        /**
         * Here you can see more customization of the config options
         */
        function __construct() {
             
            $args = array(
                'slug'              => 'gmw-group-location',
                'name'              => __('Location','GMW-GL'),
                'nav_item_position' => 65,
                'nav_item_name'     => __('Location','GMW-GL'),
                'screens' => array(
                    'create' => array(
                            'position'     => 20,
                            'name'         =>  __('Location','GMW-GL'),
                    ),
                    'edit'                 => array(
                            'name'         => __('Location','GMW-GL'),
                            'submit_text'  => __('Update Location','GMW-GL'),
                    ),
                ),
            );
            parent::init( $args );
              
            //gmw options
            $this->settings    = get_option( 'gmw_options' );
            //location tab options
            $this->lt_settings = isset( $this->settings['groups_locator'] ) ? $this->settings['groups_locator']['location_tab'] : false;
            //memebrs tab options
            $this->mt_settings = isset( $this->settings['groups_locator'] ) ? $this->settings['groups_locator']['members_tab'] : false;
            
            add_action( 'groups_before_delete_group', array( $this, 'delete_location' ), 20, 1 );
            add_action( 'gmw_gl_location_tab_location_exists', array( $this, 'group_address' ), 10, 2 );
            add_action( 'gmw_gl_location_tab_location_exists', array( $this, 'group_map' ), 10, 2 );
            add_action( 'gmw_gl_location_tab_location_exists', array( $this, 'get_directions_link' ), 10, 2 );
            add_action( 'bp_group_members_list_item', array( $this, 'add_elements_to_group_members' ) ); 
            add_action( 'bp_before_group_members_content', array( $this, 'members_map' ), 10 );
            add_action( 'bp_after_group_members_content',array( $this, 'members_map_trigger' ) ); 
            
        }

        function display() {
            global $bp;
            $group_id       = $bp->groups->current_group->id;
            $gmw_options    = $this->lt_settings;
            $gmw_gl_address = groups_get_groupmeta( $group_id, 'gmw_gl_address' );

            include_once GMW_GL_PATH . '/includes/gmw-gl-location-tab.php';
        }

        function settings_screen( $group_id = NULL ) {
       
            $gmw_gl_address = groups_get_groupmeta( $group_id, 'gmw_gl_address' );
            $afSettings     = $this->lt_settings['address_field'];
            
            ?>
            <div id="gmw-edit-group-location">
                <?php if ( !isset( $afSettings['use'] ) || $afSettings['use'] == 'single') : ?>
                    <div class="gmw-gl-autocomplete-field">
                        <label for="gmw-gl-adderss"><?php _e('Address:','GMW-GL'); ?></label>
                        <input name="gmw_gl_address[address]" id="gmw-gl-autocomplete" autocomplete="off" type="text" value="<?php if ( isset( $gmw_gl_address['address'] ) ) echo esc_attr( $gmw_gl_address['address'] ); ?>" />
                    </div>
                <?php else : ?>
                    <?php
                    $useFields   = $afSettings['fields']['use'];
                    $fieldsTitle = $afSettings['fields']['title'];
                    ?>
                    <?php foreach ( $useFields as $field ) : ?>
                        <div class="single-input-fields">
                            <label for="gmw-gl-street"><?php echo ( isset( $fieldsTitle[$field] ) && !empty( $fieldsTitle[$field] ) ) ? _e( $fieldsTitle[$field],'GMW-GL' ) : _e( $field, 'GMW-GL' ); ?></label>
                            <input name="gmw_gl_address[<?php echo $field; ?>]" id="gmw-gl-<?php echo $field; ?>" type="text" value="<?php if ( isset( $gmw_gl_address[$field] ) ) echo esc_attr( $gmw_gl_address[$field] ); ?>" />
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>	
            </div>
            <br />
            <?php
            wp_enqueue_script( 'jquery-ui-autocomplete');
            wp_enqueue_script( 'gmw-gl-autocomplete');
        }

        function settings_screen_save( $group_id = NULL ) {
            $gmw_gl_address = isset( $_POST['gmw_gl_address'] ) ? $_POST['gmw_gl_address'] : '';
            self::update_location( $group_id, $_POST['gmw_gl_address'] );
        }

        //Add location fields to group create screen function
        function create_screen( $group_id = NULL ) {
            
            $useFields   = $this->lt_settings['address_field']['fields']['use'];
            $fieldsTitle = $this->lt_settings['address_field']['fields']['title'];

            ?>
            <div id="gmw-edit-group-location">
                <?php if ( !isset( $this->lt_settings['address_field']['use'] ) || $this->lt_settings['address_field']['use'] == 'single' ) : ?>
                    <div class="gmw-gl-autocomplete-field">
                        <label for="gmw-gl-adderss"><?php _e('Address:','GMW-GL'); ?></label>
                        <input name="gmw_gl_address[address]" id="gmw-gl-autocomplete" type="text" value="" />
                    </div>
                <?php else : ?>
                    <?php foreach ( $useFields as $field ) : ?>
                        <div class="single-input-fields">
                            <label for="gmw-gl-street"><?php echo ( isset( $fieldsTitle[$field] ) && !empty( $fieldsTitle[$field] ) ) ? _e( $fieldsTitle[$field], 'GMW-GL' ) : _e( $field,'GMW-GL' ); ?></label>
                            <input name="gmw_gl_address[<?php echo $field; ?>]" id="gmw-gl-<?php echo $field; ?>" type="text" value="" />
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>	
            </div>

            <?php
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script( 'gmw-gl-autocomplete' );
        }
        
        function update_location( $group_id, $gmw_gl_address ) {
            global $wpdb, $bp;
            $apt = false;

            //include geocoder file
            include_once GMW_PATH .'/includes/geo-my-wp-geocode.php';

            //when using single address field
            if ( !isset( $this->lt_settings['address_field']['use'] ) || $this->lt_settings['address_field']['use'] == 'single' ) {
                $returned_address = GmwConvertToCoords( $gmw_gl_address['address'] );
            //multiple address field
            } else {
                
                $address = $address_apt = $gmw_gl_address;
                if ( isset( $address['apt'] ) && !empty( $address['apt'] ) ) {
                    $apt = $address['apt'];
                    unset( $address['apt'] );
                }
                $returned_address          = GmwConvertToCoords( implode( ' ',$address ) );
                $gmw_gl_address['address'] = implode( ' ', $address_apt );
                
            }
            
            $map_icon = ( isset( $_POST['map_icon'] ) && !empty( $_POST['map_icon'] ) ) ? $_POST['map_icon'] : '_default.png';
	
            //delete location if not exists
            if ( !isset( $returned_address ) || empty( $returned_address ) ) {        
                self::delete_location( $group_id );      
            //save address fields to group meta
            } else {
                
                if ( $this->lt_settings['address_field']['use'] == 'single' ) {
                        $gmw_gl_address['street']    = $returned_address['street'];
                        $gmw_gl_address['city']      = $returned_address['city'];
                        $gmw_gl_address['zipcode']   = $returned_address['zipcode'];
                } 
                $gmw_gl_address['state']             = $returned_address['state_short'];
                $gmw_gl_address['state_long']        = $returned_address['state_long'];
                $gmw_gl_address['country']           = $returned_address['country_short'];
                $gmw_gl_address['country_long']      = $returned_address['country_long'];
                $gmw_gl_address['formatted_address'] = $returned_address['formatted_address'];
                $gmw_gl_address['lat']               = $returned_address['lat'];
                $gmw_gl_address['lng']               = $returned_address['lng'];
                $gmw_gl_address['map_icon']          = $map_icon;

                groups_update_groupmeta( $group_id, 'gmw_gl_address', $gmw_gl_address );

                global $blog_id;
                //update address in database
                $wpdb->replace( $wpdb->prefix.'gmw_groups_locator', 
                        array(
                                'id'                => $group_id,
                                'lat'               => $gmw_gl_address['lat'],
                                'lng'               => $gmw_gl_address['lng'],
                                'street'            => $gmw_gl_address['street'],
                                'apt'               => $apt,
                                'city'              => $gmw_gl_address['city'],
                                'state'             => $gmw_gl_address['state'],
                                'state_long'        => $gmw_gl_address['state_long'],
                                'zipcode'           => $gmw_gl_address['zipcode'],
                                'country'           => $gmw_gl_address['country'],
                                'country_long'      => $gmw_gl_address['country_long'],
                                'address'           => $gmw_gl_address['address'],
                                'formatted_address' => $gmw_gl_address['formatted_address'],
                                'map_icon'          => $map_icon
                        )
                );
            }
        }

        /**
         * delete address from database when deleting group
         */
        function delete_location( $group_id ) {
            global $wpdb;
            $wpdb->query($wpdb->prepare( "DELETE FROM `{$wpdb->prefix}gmw_groups_locator` WHERE id=%d", $group_id ) );
            groups_delete_groupmeta( $group_id, 'gmw_gl_address' );
        }
        
        public function group_address( $group_id, $gmw_gl_address ) {
            ?>
            <span><?php _e( 'Address: ', 'GMW-GL' ); ?></span><span><?php echo apply_filters( 'gmw_gl_location_tab_address', $gmw_gl_address['formatted_address'], $gmw_gl_address, $group_id ); ?></span>
            <?php
        }
        /**
         * GWM GL function - display map in group location tab
         */
       public function group_map( $group_id, $gmw_gl_address ) {
            ?>
            <div class="gmw-map-wrapper gmw-gl-tab-map-wrapper gmw-map-frame" id="gmw-gl-tab-map-wrapper'" style="width:<?php echo $this->lt_settings['map']['width']; ?>;height:<?php echo $this->lt_settings['map']['height']; ?>">
                    <div class="gmw-map-loader-wrapper gmw-gl-tab-loader-wrapper">
                            <img class="gmw-map-loader gmw-gl-tab-map-loader" src="<?php echo GMW_URL; ?>/assets/images/map-loader.gif"/>
                    </div>
                    <div id="gmw-gl-location-tab-map" class="gmw-map gmw-gl-location-tab-map" style="width:100%;height:100%;"></div>
            </div>

            <script>
                    jQuery(window).ready(function($) {

                            var groupArgs = JSON.parse('<?php echo json_encode( $gmw_gl_address ); ?>');
                            var glMap = new google.maps.Map(document.getElementById('gmw-gl-location-tab-map'), {
                                    zoom: 13,
                                    center: new google.maps.LatLng(groupArgs.lat, groupArgs.lng),
                                    mapTypeId: google.maps.MapTypeId['<?php echo $this->lt_settings['map']['type']; ?>'],
                            });

                            marker = new google.maps.Marker({
                                    position: new google.maps.LatLng( groupArgs.lat, groupArgs.lng ),
                                    map: glMap,
                                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                            });
                    });
            </script>
            <?php 
        }
        
        /**
         * display "Get directions" link
         * @version 1.0
         * @author Eyal Fitoussi
         */
       function get_directions_link( $group_id, $gmw_gl_address ) {
            if ( !isset( $gmw_gl_address['address'] ) || empty( $gmw_gl_address['address'] ) || !isset( $this->lt_settings['get_directions'] ) ) return;
            
            echo '<a class="gmw-gl-location-tab-directions-link" href="http://maps.google.com/maps?f=d&hl=en&doflg=&geocode=&saddr= &daddr=' . str_replace(" ", "+", $gmw_gl_address['address']) . '&ie=UTF8&z=12" target="_blank">'.__( 'Get Directions','GMW-GL' ).'</a>';

       }

        /**
         * GMW GL function - add elements to group members to be displyed on the map and in results
         */
        function add_elements_to_group_members() {
            global $members_template, $wpdb;
   
            //If we need to display map in members tab of a group
            if ( isset( $this->mt_settings['map']['on'] ) || isset( $this->mt_settings['member_address']['on'] ) ) {

                //Add members lat/long and address into loop
                $memLocation = $wpdb->get_results("SELECT u.lat, u.long, u.address FROM wppl_friends_locator u WHERE u.member_id = {$members_template->member->ID}", ARRAY_A);

                if ( !empty( $memLocation ) ) {
                        $members_template->show_map       = 1;
                        $members_template->member->latLng = $memLocation;
                }

                if ( isset( $this->mt_settings['map']['on'] ) ) {
                        global $mc;

                        //Add members count into loop
                        if ( !isset( $mc ) || empty( $mc ) ) if ( $members_template->pag_page == 1 ) $mc = 0; else $mc = ( $members_template->pag_num * ( $members_template->pag_page -1 ) );
                        $mc++;
                        $members_template->member->mc = $mc;

                        //add permalink and avatar into loop
                        $members_template->member->permalink = bp_get_member_permalink();
                        $members_template->member->avatar    = bp_get_member_avatar( $args = 'type=thumb' );
                }
                if ( isset( $this->mt_settings['member_address']['on'] ) && !empty( $memLocation ) ) {
                        echo '<div class="clear"></div>';
                        if ( isset( $this->mt_settings['map']['on'] ) ) echo '<span class="gmw-gl-mt-member-count">'. $mc .') </span>';
                        echo '<div class="gmw-gl-mt-address-wrapepr"><span>'. __( 'Address: ','GMW-GL' ).' </span><span>'. $members_template->member->latLng[0]['address'] .'</span></div>';
                }
            }
        }
        
        /**
         * GMW GL funtion - display map before memebrs loop in a group
         */
        function members_map() {

            if ( !isset( $this->mt_settings['map']['on'] ) ) return;
            ?>
            <div class="gmw-map-wrapper gmw-gl-group-members-map-wrapper" id="gmw-gl-group-members-map-wrapper" style="width:<?php echo $this->mt_settings['map']['width']; ?>;height:<?php echo $this->mt_settings['map']['height']; ?>;display:none;">
                <div class="gmw-map-loader-wrapper gmw-gl-loader-wrapper">
                        <img class="gmw-map-loader gmw-gl-map-loader" src="<?php echo GMW_URL; ?>/assets/images/map-loader.gif"/>
                </div>
                <div id="gmw-gl-group-members-map" class="gmw-map gmw-gl-group-members-map" style="width:100%;height:100%;"></div>
            </div>
        <?php 
        }
        
        /**
        * GMW GL function - trigger the javascript to display map with group's members location
        */
        function members_map_trigger() {
            global $members_template;

            //if there are no members with location we do not need to show the map
            if ( !isset( $members_template->show_map ) || $members_template->show_map != 1 || !isset( $this->mt_settings['map']['on'] ) ) return;

            $glgmMapArgs = array();
            $glgmMapArgs['your_lat'] = false;
            $glgmMapArgs['your_lng'] = false;

            if ( isset( $_COOKIE['gmw_lat'] ) && !empty( $_COOKIE['gmw_lng'] ) ) {
                $glgmMapArgs['your_lat']  = urldecode( $_COOKIE['gmw_lat'] );
                $glgmMapArgs['your_lng'] = urldecode( $_COOKIE['gmw_lng'] );
            }
            wp_enqueue_script( 'gmw-gl-group-members-map' );
            wp_localize_script( 'gmw-gl-group-members-map', 'glgmMapArgs', $glgmMapArgs );
            wp_localize_script( 'gmw-gl-group-members-map', 'glgmGroups', $members_template->members );
        }

    }
    
    bp_register_group_extension( 'GMW_Group_Extension' );

endif;
?>