<?php 

/**
 * GMW GL function - adds elements to be display on the map
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_map_elements() {
	
	global $groups_template;
	$groups_template->group->permalink = bp_get_group_permalink();
	$groups_template->group->avatar = bp_get_group_avatar( $args = 'type=thumb' );
}
//add_action('bp_directory_groups_actions', 'gmw_gl_map_elements', 10);

/**
 * GMW GL function - Display group's full address
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_group_address( $gmw ) {
	global $groups_template;
	$address = $groups_template->group->address;
	echo apply_filters( 'gmw_gl_single_group_address', $address, $gmw, $groups_template );
}

/**
 * GMW GL function - Display Radius distance
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_by_radius($gmw) {
	global $groups_template;
	if ( isset( $gmw['your_lat'] ) && !empty( $gmw['your_lat'] ) ) echo  $groups_template->group->distance .' ' . $gmw['units_array']['name'];
}

/**
 * GMW GL function - "Get directions" link
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_get_directions($gmw) {
	global $groups_template;
	if ( !isset( $gmw['search_results']['get_directions'] ) ) return;
	
        if ( !isset($gmw['org_address']) ) $gmw['org_address'] = '';
	echo '<a class="gmw-gl-single-group-get-directions" href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $gmw['units_array']['map_units'] . '&geocode=&saddr=' .$gmw['org_address'] . '&daddr=' . str_replace(" ", "+", $groups_template->group->address) . '&ie=UTF8&z=12" target="_blank">'.__('Get Directions','GMW-GL').'</a>';
	
}

/**
 * GMW GL function - display within distance message
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_within_message($gmw, $class) {
	if ( !isset( $gmw['org_address'] ) || empty( $gmw['org_address'] ) ) return;
	echo ' <div class="gmw-gl-within-message-wrapper '.$class.'">'. __('within','GMW-GL') . ' ' . $gmw['radius'] . ' ' . $gmw['units_array']['name'] . ' ' . __('from','GMW-GL') . ' ' . $gmw['org_address'] .'</div>';
}

/**
 * GMW GL function - calculate driving distance
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_driving_distance($gmw, $class) {
	
	if ( !isset( $gmw['search_results']['by_driving'] ) || $gmw['units_array']['name'] == false )
		return;
	
	global $groups_template;
	
	echo 	'<div id="gmw-gl-driving-distance-'.$groups_template->group->id . '" class="'.$class.'"></div>';
	?>
	<script>   
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
        var directionsDisplay = new google.maps.DirectionsRenderer();	
        
  		var start = new google.maps.LatLng('<?php echo $gmw['your_lat']; ?>','<?php echo $gmw['your_lng']; ?>');
  		var end = new google.maps.LatLng('<?php echo $groups_template->group->lat; ?>','<?php echo $groups_template->group->lng; ?>');
  		var request = {
    		origin:start,
    		destination:end,
    		travelMode: google.maps.TravelMode.DRIVING
 		};
 		
  		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) {
        	      			directionsDisplay.setDirections(result);
      			if ( '<?php echo $gmw['units_array']['name']; ?>' == 'Mi') {
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.000621371192 * 10) / 10)+' Mi';
      			} else { 
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.01) / 10)+' Km';
      			}	
      			jQuery('#<?php echo 'gmw-gl-driving-distance-'. $groups_template->group->id; ?>').text('Driving: ' + totalDistance)
    		}
 		 });	 
	</script>
	<?php	
}

/**
 * GMW FL Search results function - Per page dropdown
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_per_page_dropdown( $gmw, $class ) {
	global $groups_template;
	
	$perPage  = explode( ",", $gmw['search_results']['per_page'] );
	$lastpage = ceil( $groups_template->total_group_count/$gmw['get_per_page'] );
		
	if ( count( $perPage ) > 1 ) :
	
		echo '<select name="gmw_per_page" class="gmw-gl-per-page-dropdown '.$class.'">';
	
			foreach ( $perPage as $pp ) :
			
				if ( isset( $_GET['gmw_per_page']) && $_GET['gmw_per_page'] == $pp ) $pp_s = 'selected="selected"'; else $pp_s = "";
				echo '<option value="'. $pp .'" '.$pp_s.'>'.$pp.' per page</option>';
				
			endforeach;
			
		echo '</select>';

	endif;
	?>
	<script>
		jQuery(document).ready(function($) {
			
		   	$(".gmw-gl-per-page-dropdown").change(function() {
			   	
			   	var totalResults = <?php echo $groups_template->total_group_count; ?>;
			   	var lastPage = Math.ceil(totalResults/$(this).val());
			   	var newPaged = ( <?php echo $groups_template->pag_num; ?> > lastPage ) ? lastPage : false;
		   		//var seperator = (window.location.href.indexOf("?")===-1)?"?":"&";
		   		window.location.href = jQuery.query.set("gmw_per_page", $(this).val()).set('upage', newPaged);
		   		
		    });
		});
    </script>
<?php 
}

/**
 * GMW FL Search results function - Per page dropdown
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_gl_orderby_dropdown( $gmw, $title, $class ) {
	global $groups_template;
	
        $orderby_title = ( isset( $title ) && !empty( $title ) ) ? $title : __( ' -- Order By --', 'GMW-GL' );
        
	$orderby  = array( 
            'distance'      => 'Distance',
            'active'        => 'Last Active',
            'popular'       => 'Most Members', 
            'newest'        => 'Newly Created',
            'alphabetical'  => 'Alphabetical'
        );
        
	$lastpage = ceil( $groups_template->total_group_count/$gmw['get_per_page'] );
		
	if ( count( $orderby ) > 1 ) :
            
		echo '<select name="gmw_orderby" class="gmw-gl-orderby-dropdown '.$class.'">';
                        
                        echo '<option value="">'.$orderby_title.'</option>';
			foreach ( $orderby as $key => $value ) :
			
				if ( isset( $_GET['gmw_orderby'] ) && $_GET['gmw_orderby'] == $key ) $pp_s = 'selected="selected"'; else $pp_s = "";
				echo '<option value="'. $key .'" '.$pp_s.'>'.$value.'</option>';
				
			endforeach;
			
		echo '</select>';

	endif;
	?>
	<script>
		jQuery(document).ready(function($) {
			
		   	$(".gmw-gl-orderby-dropdown").change(function() {
			   	if ( $(this).val() == '' ) return;
			   	
		   		//var seperator = (window.location.href.indexOf("?")===-1)?"?":"&";
		   		window.location.href = jQuery.query.set("gmw_orderby", $(this).val()).set('gmw_orderby', $(this).val());
		   		
		    });
		});
    </script>
<?php 
}

/**
 * GMW GL function - display groups count
 * @para  $gmw
 * @param $gmw_options
 */
function gmw_gl_group_number($gmw) {
	global $groups_template, $gc;
	if ( !isset($gc) || empty($gc) ) if ( $groups_template->pag_page == 1 ) $gc = 0; else $gc = ($groups_template->pag_num * ($groups_template->pag_page -1 ));
	$gc++;
	$groups_template->group->gc = $gc;
	return $gc;
}

/**
 * GMW_FL_Search_Query class
 *
 */
class GMW_GL_Search_Query extends GMW {

	/**
	 * __construct function.
	 */
	function __construct( $form, $results ) {
		
		do_action( 'gmw_gl_search_query_start', $form, $results );
		
                add_filter( 'gmw_search_form_address_field', array( $this, 'address_fields' ), 10, 2 );
		add_action( 'group_loop_end', array( $this, 'localize_groups' ), 10 );		
		add_filter( 'group_loop_start' , array( $this, 'loop_start' ), 10, 2 );               
                add_filter( 'bp_groups_get_total_groups_sql', array( $this, 'gmwGlTotalQuery' ), 10, 2 );
                add_action( 'gmw_gl_directory_group_start', array( $this, 'modify_group' ), 10 );
				
		parent::__construct( $form, $results );
		
	}
        
        /**
	 * Address Fields
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	function address_fields( $address_field, $gmw ) {
		
		if ( !isset( $gmw['search_form']['address_fields'] ) || $gmw['search_form']['address_fields']['how'] == 'single' ) return $address_field;
		
		$address_field = '';
                
		foreach ( $gmw['search_form']['address_fields'] as $key => $field ) :
			
			$am = ( isset( $field['mandatory'] ) ) ? 'mandatory' : '';
			
			$field_on = ( isset( $field['on'] ) ) ? $field['on'] : false;
			
			if ( $field_on == 'default' ) :
				
				$address_field .=  '<input type="hidden" name="gmw_address['.$key.']" value="'.$field['value'].'" />';
			
			elseif ( $field_on == 'include' ) :	
				
				$address_field .=  '<div id="gmw-'.$key.'-wrapper-'.$gmw['ID'].'" class="gmw-saf-wrapper gmw-'.$key.'-wrapper-'.$gmw['ID'].'">';
					
					if ( !isset( $field['dropdown'] ) ) :
					
						if ( !isset( $field['within'] ) ) $address_field .=  '<label for="saf-'.$key.'">' .$field['title']. '</label>';
						$address_field .=  '<input type="text" name="gmw_address['.$key.']" id="gmw-saf-'.$key.'" class="gmw-saf gmw-saf-'.$key.' gmw-address '.$am.'" value="'; if ( isset( $_GET['gmw_address'][$key] ) ) $address_field .=  $_GET['gmw_address'][$key]; $address_field .=  '" size="20" '; if ( isset( $field['within'] ) ) $address_field .=  'placeholder="'. $field['title'] . '"'; $address_field .=  '/>';
					
					else :
					
						if ( !isset($field['within']) ) $address_field .=  '<label for="saf-'.$key.'">' .$field['title']. '</label>';
						$dropArray = explode(',',$field['drop_values']);
						$address_field .=  '<select name="gmw_address['.$key.']" id="gmw-'.$key.'" class="gmw-'.$key.' gmw-address-field">';
							foreach ( $dropArray as $va ) :
								$address_field .=  '<option value="'.$va.'" ';if ( isset($_GET['gmw_address'][$key]) && $_GET['gmw_address'][$key] == $va ) $address_field .=  'selected="selected"' ; $address_field .=  '>'.$va.'</option>';
							endforeach;
						$address_field .=  '</select>';
						
					endif;
					
				$address_field .=  '</div>';
				
			endif;
			
		endforeach;
		
		return $address_field;
	}
        
	/**
	 * Include search form
	 * 
	 */
	public function search_form() {
	
		$gmw = $this->form;
	
		do_action( 'gmw_gl_before_search_form', $this->form, $this->settings );
	
		wp_enqueue_style( 'gmw-'.$this->form['ID'].'-'.$this->form['search_form']['form_template'].'-form-style', GMW_GL_URL. '/search-forms/'.$this->form['search_form']['form_template'].'/css/style.css' );
		include GMW_GL_PATH .'/search-forms/'. $this->form['search_form']['form_template'].'/search-form.php';
	
		do_action( 'gmw_gl_after_search_form', $this->form, $this->settings );
	
	}
	
	
	/**
         * Create filters for Groups query
         * @version 1.0
         * @author Eyal Fitoussi
         */
        public function groups_query_filter() {
                global $wpdb;
                
                $clauses = array();

                $clauses['total_sql']          = '';
                $clauses['total_sql']['where'] = array();

                if ( !empty( $this->form['org_address']) ) :

                        $clauses['sql']['select'] = $wpdb->prepare("SELECT DISTINCT gg.id, gg.lat, gg.lng, gg.address, gg.map_icon, g.*, gm1.meta_value AS total_member_count, gm2.meta_value AS last_activity , ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gg.lat ) ) * cos( radians( gg.lng ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gg.lat) ) ),1 ) AS distance",
                                $this->form['units_array']['radius'], $this->form['your_lat'], $this->form['your_lng'], $this->form['your_lat']);
                        $clauses['sql']['having'] = $wpdb->prepare(" HAVING distance <= %d OR distance IS NULL ", $this->form['radius']);

                        $clauses['total_sql']['where'][] = $wpdb->prepare(" ROUND( %d * acos( cos( radians( %s ) ) * cos( radians( gg.lat ) ) * cos( radians( gg.lng ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( gg.lat) ) ),1 ) <= %d",
                                $this->form['units_array']['radius'], $this->form['your_lat'], $this->form['your_lng'], $this->form['your_lat'], $this->form['radius']);
                else :
                        $clauses['sql']['select'] = "SELECT DISTINCT gg.id, gg.lat, gg.lng, gg.address, gg.map_icon, g.*, gm1.meta_value AS total_member_count, gm2.meta_value AS last_activity";
                endif;

                $clauses['sql']['hidden']        = " AND g.status != 'hidden' ";
                $clauses['sql']['from']          = " {$wpdb->prefix}bp_groups g, ";
                $clauses['sql']['group_from']    = " {$wpdb->prefix}gmw_groups_locator gg WHERE g.id = gg.id AND ";

                $clauses['total_sql']['hidden']  = " g.id = gg.id ";
                $clauses['total_sql']['where'][] = " g.status != 'hidden' ";
                $clauses['total_sql']['select']  = " SELECT COUNT(DISTINCT g.id), g.status FROM {$wpdb->prefix}gmw_groups_locator gg, wp_bp_groups g ";

                return $clauses = apply_filters( 'gmw_gl_groups_sql', $clauses, $this->form );
        }

        /**
         * GMW GL function - Add $this->GmwGroupsSql filters into Buddypress groups "get()" filter
         * @version 1.0
         * @author Eyal Fitoussi
         */
         function gmwGlQuery( $sql, $sql ) {
                
                 $orderby = ( isset( $_GET['gmw_orderby'] ) ) ? $_GET['gmw_orderby'] : 'distance';
                                 
                 $sql['select']      = $this->clauses['sql']['select'];
                 $sql['from']       .= $this->clauses['sql']['from'];
                 $sql['group_from']  = $this->clauses['sql']['group_from'];

                 if ( isset( $sql['hidden'] ) ) $sql['hidden']           = $this->clauses['sql']['hidden'];
                 
                 if ( $orderby == 'distance' ) 
                     if ( isset( $this->clauses['sql']['having'] ) ) $sql[0] = $this->clauses['sql']['having'] . ' ORDER BY distance';
                 else
                     if ( isset( $this->clauses['sql']['having'] ) ) $sql[0] = $this->clauses['sql']['having'] . $sql[0];
                     
                 return join( ' ', (array) $sql);
         }

        /**
         * GMW GL function - Modify the "Total group count" filter
         * @version 1.0
         * @author Eyal Fitoussi
         */
        function gmwGlTotalQuery( $t_sql, $total_sql ){
 
                $t_sql = $this->clauses['total_sql']['select'];
                if ( $total_sql['where'][0] == "g.status != 'hidden'" )  $this->clauses['total_sql']['where'][] = $this->clauses['total_sql']['hidden'];
                $t_sql .= " WHERE " . join( ' AND ', (array) $this->clauses['total_sql']['where'] );

                return $t_sql;
        }
               
	/**
	 * modify members_template 
	 * @version 1.0
	 * @author Eyal Fitoussi
	 */
	public function modify_group() {
		global $groups_template;
		
		$groups_template->group->permalink     = bp_get_group_permalink();
		$groups_template->group->avatar        = bp_get_group_avatar( $args = 'type=full' );
		$groups_template->group->group_count   = $this->form['group_count'];
		$groups_template->group->mapIcon       = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='. $groups_template->group->group_count .'|FF776B|000000';
		
		$this->form['group_count']++;
		
		$groups_template = apply_filters( 'gmw_gl_modify_group', $groups_template, $this->form );
	}
	
	public function localize_groups() {
		global $groups_template;
		
		if ( $this->form['search_results']['display_map'] != 'na' ) 
			wp_localize_script( 'gmw-gl-map', 'glGroups', $groups_template->groups );
	}
	
        public function loop_start() {
		global $groups_template;
		
		//setup member count
		$this->form['paged']       = ( !isset( $_GET['upage'] ) || $_GET['upage'] == 1 ) ? 1 : $_GET['upage'];
		$this->form['group_count'] = ( $this->form['paged'] == 1 ) ? 1 : ( $this->form['get_per_page'] * ( $this->form['paged'] - 1 ) ) + 1;
		$this->form['results']	   = $groups_template->groups;
		
	}
        
	public function results() {

		echo '<div id="buddypress" class="gmw-gl-results-wrapper gmw-gl-results-wrapper-'.$this->form['ID'].'">';
                    
                    $this->form['region']       = ( WPLANG ) ? explode('_', WPLANG) : array('en','US');
                    $this->form['post_loader']  = GMW_URL .'/assets/images/gmw-loader.gif';
                    $this->form['orderby']      = ( isset( $_GET['gmw_orderby'] ) ) ? $_GET['gmw_orderby'] : 'distance';
                    //Filter groups query
                    $this->clauses = self::groups_query_filter();
                    add_filter( 'bp_groups_get_paged_groups_sql', array( $this, 'gmwGlQuery' ), 10, 2 );

                    $this->form['query_args']  = array(
                                    'type'     => $this->form['orderby'],
                                    'per_page' => $this->form['get_per_page'],
                    );
                    
                    $this->form = apply_filters( 'gmw_gl_form_before_search_results', $this->form, $this->settings );
                    do_action( 'gmw_gl_before_search_results', $this->form, $this->settings );
                    
                    //load results template file to display list of groups
                    if ( isset( $this->form['search_results']['display_groups'] ) ) :

                            $gmw = $this->form;

                            // include custom results and stylesheet pages from child/theme 
                            if( strpos( $this->form['search_results']['results_template'], 'custom_') !== false ) :

                                    $sResults = str_replace( 'custom_','',$this->form['search_results']['results_template'] );
                                    wp_register_style( 'gmw-gl-current-style', get_stylesheet_directory_uri() . '/geo-my-wp/groups/search-results/'.$sResults.'/css/style.css' );
                                    wp_enqueue_style( 'gmw-gl-current-style');

                                    include(STYLESHEETPATH. '/geo-my-wp/groups/search-results/'.$sResults.'/results.php');
                            //include results and stylesheet pages from plugin's folder
                            else :

                                    wp_register_style( 'gmw-gl-current-style', GMW_GL_URL. '/search-results/'.$this->form['search_results']['results_template'].'/css/style.css' );
                                    wp_enqueue_style( 'gmw-gl-current-style');
                                    include GMW_GL_PATH . '/search-results/'.$this->form['search_results']['results_template'].'/results.php';

                            endif;

                    /*
                     * if we do not display list of groups we still need to have a loop
                     * and add some information to each group in order to be able to 
                     * display it on the map
                     */
                    else :

                            if ( bp_has_members( $this->form['query_args'] ) ) : 

                                    while ( bp_members() ) : bp_the_member();

                                            self::modify_group();

                                    endwhile;

                            endif;

                    endif;
                    
                    global $groups_template;
				
                    // if we need to display map
                    if ( $this->form['search_results']['display_map'] != 'na' ) :

                            $this->form['iw_labels'] = array(
                                    'distance'	 => __( 'Distance: ','GMW-GL' ),
                                    'address' 	 => __( 'Address: ','GMW-GL' ),
                                    'directions' => __( 'Get Directions: ','GMW-GL' )
                            );

                            $this->form       = apply_filters( 'gmw_gl_form_before_map', $this->form, $groups_template, $this->settings );
                            $members_template = apply_filters( 'gmw_gl_groups_before_map', $groups_template, $this->form, $this->settings );

                            do_action( 'gmw_gl_has_groups_before_map', $this->form, $this->settings, $groups_template );

                            $form = $this->form;
                            $form['results'] = $groups_template->groups;

                            wp_enqueue_script( 'gmw-gl-map' );
                            wp_localize_script( 'gmw-gl-map', 'gmwForm', $form );

                    endif;
                 						
		echo '</div>';
               
	}
        
        /**
         * GMW GL function - no groups found
         */
        function no_groups_found( $gmw, $gmw_options ) {
               $no_groups = __( 'Sorry, No groups found','GMW-GL' );
               echo apply_filters( 'gmw_gl_no_groups_message', $no_groups, $gmw );
        }
}