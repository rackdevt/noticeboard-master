<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @sub-subpackage Mapology
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

/**
 * Setup more MAPO globals
 * Needs to be done here. bp_setup_globals happens too early in the game.
 * @since 1.0
 */
function mapo_setup_additional_globals()
{
	global $bp, $mapo;

	// set up the address and email messages of the current group
	$group_id = empty( $bp->groups->current_group->id ) ? $_COOKIE['bp_new_group_id'] : $bp->groups->current_group->id;
	
	if( $address = groups_get_groupmeta( $group_id, 'group_address' ) )
	{
		$mapo->displayed_group = new stdClass;
		foreach( $address as $key => $val )
			$mapo->displayed_group->{$key} = $val;
	}
}
add_action( 'wp', 'mapo_setup_additional_globals', 2 );

/**
* Add group contact details to group creation and edit screen
* @since 1.0
*/
function mapo_group_creation_details()
{
	?>
    <label for="group_street"><?php _e( '* Street', 'mapo' ) ?></label>
    <input type="text" name="group_street" id="group_street" value="<?php mapo_groupmeta( 'street' ) ?>" />

    <label for="group_postcode"><?php _e( '* Postcode', 'mapo' ) ?></label>
    <input type="text" name="group_postcode" id="group_postcode" value="<?php mapo_groupmeta( 'postcode' ) ?>" />

    <label for="group_city"><?php _e( '* City', 'mapo' ) ?></label>
    <input type="text" name="group_city" id="group_city" value="<?php mapo_groupmeta( 'city' ) ?>" />

    <label for="group_country"><?php _e( '* Country', 'mapo' ) ?></label>
    <select name="group_country" id="group_country"><?php mapo_country_select( mapo_groupmeta( 'country', false ) ) ?></select>

    <label for="group_telephone"><?php _e( 'Telephone', 'mapo' ) ?></label>
    <input type="text" name="group_telephone" id="group_telephone" value="<?php mapo_groupmeta( 'telephone' ) ?>" />

    <label for="group_mobile"><?php _e( 'Mobile', 'mapo' ) ?></label>
    <input type="text" name="group_mobile" id="group_mobile" value="<?php mapo_groupmeta( 'mobile' ) ?>" />

    <label for="group_fax"><?php _e( 'Fax', 'mapo' ) ?></label>
    <input type="text" name="group_fax" id="group_fax" value="<?php mapo_groupmeta( 'fax' ) ?>" />

    <label for="group_website"><?php _e( 'Website', 'mapo' ) ?></label>
    <input type="text" name="group_website" id="group_website" value="<?php mapo_groupmeta( 'website' ) ?>" />
    
	<?php		
    do_action( 'mapo_group_creation_details' );
}
add_action( 'groups_custom_group_fields_editable', 'mapo_group_creation_details' );

/**
* Save group contact details
* @since 1.0
*/
function mapo_save_group_creation_details( $id = false )
{
	global $bp;

	if( ! $id )
		$id = $bp->groups->new_group_id;

	$address = array(
		'street'	=> $_POST['group_street'],
		'postcode'	=> $_POST['group_postcode'],
		'city'		=> $_POST['group_city'],
		'country'	=> $_POST['group_country'],
		'telephone' => $_POST['group_telephone'],
		'mobile'	=> $_POST['group_mobile'],
		'fax'		=> $_POST['group_fax'],
		'website'	=> $_POST['group_website']
	);

	foreach( $address as $key => $val )
		$address_safe[$key] = wp_filter_kses( $val );

	$address_safe = apply_filters( 'bpe_save_group_creation_details', $address_safe );

	do_action( 'bpe_save_extra_group_details', $address_safe, $id );

	groups_update_groupmeta( $id, 'group_address', $address_safe );
}
add_action( 'groups_create_group_step_save_group-details', 'mapo_save_group_creation_details' );
add_action( 'groups_group_details_edited', 'mapo_save_group_creation_details' );

/**
* Validate the extra input
* @since 1.0
*/
function mapo_check_group_save_details( $group_id )
{
	if( empty( $_POST['group_street'] ) || empty( $_POST['group_postcode'] ) || empty( $_POST['group_city'] ) || empty( $_POST['group_country'] ) )
	{
		$group = new BP_Groups_Group( $group_id );
		
		bp_core_add_message( __( 'Please fill in all required fields.', 'mapo' ), 'error' );
		bp_core_redirect( bp_get_group_permalink( $group ) . 'admin/edit-details/' );
	}
}
add_action( 'groups_details_updated', 'mapo_check_group_save_details' );

/**
* Validate the extra input
* @since 1.0
*/
function mapo_check_group_creation_details( $group_id )
{
	global $bp;

	if( $bp->groups->current_create_step == 'group-details' )
	{
		if( empty( $_POST['group_street'] ) || empty( $_POST['group_postcode'] ) || empty( $_POST['group_city'] ) || empty( $_POST['group_country'] ) )
		{
			bp_core_add_message( __( 'Please fill in all required fields.', 'mapo' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . '/' . $bp->pages->groups->slug . '/create/step/' . $bp->groups->current_create_step . '/' );
		}
	}
}
add_action( 'groups_created_group', 'mapo_check_group_creation_details' );

/**
* Display group contact details
* @since 1.0
*/
function mapo_display_group_address()
{
	global $mapo;

	$out = '';

	if( mapo_check_empty_object( $mapo->displayed_group ) )
	{
		$out .= '<h3>'. __( 'Contact Details', 'mapo' ) .'</h3>';
		$out .= '<p class="address">';
		$out .= mapo_check_empty( $mapo->displayed_group->street, '<br />' );
		$out .= mapo_check_empty( $mapo->displayed_group->postcode, '<br />' );
		$out .= mapo_check_empty( $mapo->displayed_group->city, '<br />' );
		$out .= mapo_check_empty( $mapo->displayed_group->country );
		$out .= '</p>';
	
		$out .= '<p class="contact">';
		$out .= mapo_check_empty( $mapo->displayed_group->telephone, '<br />', __( 'Telephone: ', 'mapo' ) );
		$out .= mapo_check_empty( $mapo->displayed_group->mobile, '<br />', __( 'Mobile: ', 'mapo' ) );
		$out .= mapo_check_empty( $mapo->displayed_group->fax, '<br />', __( 'Fax: ', 'mapo' ) );
		$out .= mapo_check_empty( $mapo->displayed_group->website, '', __( 'Website: ', 'mapo' ), true );
		$out .= '</p>';
		$out .= '<div class="clear"></div>';
	}
	
	echo apply_filters( 'mapo_display_group_contact_details', $out, $mapo->displayed_group );
}
add_action( 'mapo_group_contact_page', 'mapo_display_group_address', 9 );

/**
* Helper function to check for empty values
* @since 1.0
*/
function mapo_check_empty( $val, $suffix = '', $prefix = '', $clickable = false )
{
	if( ! empty( $val ) )
	{
		if( $clickable )
			$val = make_clickable( $val );
			
		return $prefix . $val . $suffix;
	}
}

/**
* Get group contact details
* @since 1.0
*/
function mapo_groupmeta( $key, $echo = true )
{
	global $mapo;
	
	if( ! empty( $mapo->displayed_group->{$key} ) )
		$r = $mapo->displayed_group->{$key};
	
	if( $echo )
		echo $r;
	else
		return $r;
}

/**
* Check an array or object for being empty
* @since 1.0
*/
function mapo_check_empty_object( $object )
{
	if( is_object( $object ) )
		$object = get_object_vars( $object );
	
	foreach( (array)$object as $k => $v )
			$empty[] = ( empty( $v ) ) ? false : true;

	if( in_array( true,(array)$empty ) )
		return true;
	
	return false;
}

/**
* Get all countries
* @since 1.0
*/
function mapo_countries()
{
	return array(
		__( 'Afghanistan', 'mapo' ), __( 'Albania', 'mapo' ), __( 'Algeria', 'mapo' ), __('American Samoa', 'mapo' ), __('Andorra', 'mapo' ),
		__( 'Angola', 'mapo' ), __( 'Antigua and Barbuda', 'mapo' ), __( 'Argentina', 'mapo' ), __( 'Armenia', 'mapo' ), __( 'Australia', 'mapo' ),
		__( 'Austria', 'mapo' ), __( 'Azerbaijan', 'mapo' ), __( 'Bahamas', 'mapo' ), __( 'Bahrain', 'mapo' ), __( 'Bangladesh', 'mapo' ),
		__( 'Barbados', 'mapo' ), __( 'Belarus', 'mapo' ), __( 'Belgium', 'mapo' ), __( 'Belize', 'mapo' ), __( 'Benin', 'mapo' ), __( 'Bermuda', 'mapo' ),
		__( 'Bhutan', 'mapo' ), __( 'Bolivia', 'mapo' ), __( 'Bosnia and Herzegovina', 'mapo' ), __( 'Botswana', 'mapo' ), __( 'Brazil', 'mapo' ),
		__( 'Brunei', 'mapo' ), __( 'Bulgaria', 'mapo' ), __( 'Burkina Faso', 'mapo' ), __( 'Burundi', 'mapo' ), __( 'Cambodia', 'mapo' ),
		__( 'Cameroon', 'mapo' ), __('Canada', 'mapo' ), __( 'Cape Verde', 'mapo' ), __( 'Central African Republic', 'mapo' ), __( 'Chad', 'mapo' ),
		__( 'Chile', 'mapo' ), __( 'China', 'mapo' ), __( 'Colombia', 'mapo' ), __( 'Comoros', 'mapo' ), __( 'Congo', 'mapo' ), __( 'Costa Rica', 'mapo' ),
		__( 'C&ocirc;te d\'Ivoire', 'mapo' ), __( 'Croatia', 'mapo' ), __( 'Cuba', 'mapo' ), __( 'Cyprus', 'mapo' ), __( 'Czech Republic', 'mapo' ),
		__( 'Denmark', 'mapo' ), __( 'Djibouti', 'mapo' ), __( 'Dominica', 'mapo' ), __( 'Dominican Republic', 'mapo' ), __( 'East Timor', 'mapo' ),
		__( 'Ecuador', 'mapo' ), __( 'Egypt', 'mapo' ), __( 'El Salvador', 'mapo' ), __( 'Equatorial Guinea', 'mapo' ), __( 'Eritrea', 'mapo' ),
		__( 'Estonia', 'mapo' ), __( 'Ethiopia', 'mapo' ), __( 'Fiji', 'mapo' ), __( 'Finland', 'mapo' ), __( 'France', 'mapo' ), __( 'Gabon', 'mapo' ),
		__( 'Gambia', 'mapo' ), __( 'Georgia', 'mapo' ), __( 'Germany', 'mapo' ), __( 'Ghana', 'mapo' ), __( 'Greece', 'mapo' ), __( 'Grenada', 'mapo' ),
		__( 'Guam', 'mapo' ), __( 'Guatemala', 'mapo' ), __( 'Guinea', 'mapo' ), __( 'Guinea-Bissau', 'mapo' ), __( 'Guyana', 'mapo' ), __( 'Haiti', 'mapo' ),
		__( 'Honduras', 'mapo' ), __( 'Hong Kong', 'mapo' ), __( 'Hungary', 'mapo' ), __( 'Iceland', 'mapo' ), __( 'India', 'mapo' ),
		__( 'Indonesia', 'mapo' ), __( 'Iran', 'mapo' ), __( 'Iraq', 'mapo' ), __( 'Ireland', 'mapo' ), __( 'Israel', 'mapo' ), __( 'Italy', 'mapo' ),
		__( 'Jamaica', 'mapo' ), __( 'Japan', 'mapo' ), __( 'Jordan', 'mapo' ), __( 'Kazakhstan', 'mapo' ), __( 'Kenya', 'mapo' ), __( 'Kiribati', 'mapo' ),
		__( 'North Korea', 'mapo' ), __( 'South Korea', 'mapo' ), __( 'Kuwait', 'mapo' ), __( 'Kyrgyzstan', 'mapo' ), __( 'Laos', 'mapo' ),
		__( 'Latvia', 'mapo' ), __( 'Lebanon', 'mapo' ), __( 'Lesotho', 'mapo' ), __( 'Liberia', 'mapo' ), __( 'Libya', 'mapo' ),
		__( 'Liechtenstein', 'mapo' ), __( 'Lithuania', 'mapo' ), __( 'Luxembourg', 'mapo' ), __( 'Macedonia', 'mapo' ), __( 'Madagascar', 'mapo' ),
		__( 'Malawi', 'mapo' ), __( 'Malaysia', 'mapo' ), __( 'Maldives', 'mapo' ), __( 'Mali', 'mapo' ), __( 'Malta', 'mapo' ),
		__( 'Marshall Islands', 'mapo' ), __( 'Mauritania', 'mapo' ), __( 'Mauritius', 'mapo' ), __( 'Mexico', 'mapo' ), __( 'Micronesia', 'mapo' ),
		__( 'Moldova', 'mapo' ), __( 'Monaco', 'mapo' ), __( 'Mongolia', 'mapo' ), __( 'Montenegro', 'mapo' ), __( 'Morocco', 'mapo' ),
		__( 'Mozambique', 'mapo' ), __( 'Myanmar', 'mapo' ), __( 'Namibia', 'mapo' ), __( 'Nauru', 'mapo' ), __( 'Nepal', 'mapo' ),
		__( 'Netherlands', 'mapo' ), __( 'New Zealand', 'mapo' ), __( 'Nicaragua', 'mapo' ), __( 'Niger', 'mapo' ), __( 'Nigeria', 'mapo' ),
		__( 'Norway', 'mapo' ), __('Northern Mariana Islands', 'mapo' ), __('Oman', 'mapo' ), __( 'Pakistan', 'mapo' ), __( 'Palau', 'mapo' ),
		__( 'Palestine', 'mapo' ), __( 'Panama', 'mapo' ), __( 'Papua New Guinea', 'mapo' ), __( 'Paraguay', 'mapo' ), __( 'Peru', 'mapo' ),
		__( 'Philippines', 'mapo' ), __( 'Poland', 'mapo' ), __( 'Portugal', 'mapo' ), __( 'Puerto Rico', 'mapo' ), __( 'Qatar', 'mapo' ),
		__( 'Romania', 'mapo' ), __( 'Russia', 'mapo' ), __( 'Rwanda', 'mapo' ), __( 'Saint Kitts and Nevis', 'mapo' ), __( 'Saint Lucia', 'mapo' ),
		__( 'Saint Vincent and the Grenadines', 'mapo' ), __( 'Samoa', 'mapo' ), __( 'San Marino', 'mapo' ), __( 'Sao Tome and Principe', 'mapo' ),
		__( 'Saudi Arabia', 'mapo' ), __( 'Senegal', 'mapo' ), __( 'Serbia and Montenegro', 'mapo' ), __( 'Seychelles', 'mapo' ),
		__( 'Sierra Leone', 'mapo' ), __( 'Singapore', 'mapo' ), __( 'Slovakia', 'mapo' ), __( 'Slovenia', 'mapo' ), __( 'Solomon Islands', 'mapo' ),
		__( 'Somalia', 'mapo' ), __( 'South Africa', 'mapo' ), __( 'Spain', 'mapo' ), __( 'Sri Lanka', 'mapo' ), __( 'Sudan', 'mapo' ),
		__( 'Suriname', 'mapo' ), __( 'Swaziland', 'mapo' ), __( 'Sweden', 'mapo' ), __( 'Switzerland', 'mapo' ), __( 'Syria', 'mapo' ),
		__( 'Taiwan', 'mapo' ), __( 'Tajikistan', 'mapo' ), __( 'Tanzania', 'mapo' ), __( 'Thailand', 'mapo' ), __( 'Togo', 'mapo' ),
		__( 'Tonga', 'mapo' ), __( 'Trinidad and Tobago', 'mapo' ), __( 'Tunisia', 'mapo' ), __( 'Turkey', 'mapo' ), __( 'Turkmenistan', 'mapo' ),
		__( 'Tuvalu', 'mapo' ), __( 'Uganda', 'mapo' ), __( 'Ukraine', 'mapo' ), __( 'United Arab Emirates', 'mapo' ), __( 'United Kingdom', 'mapo' ),
		__('United States', 'mapo' ), __( 'Uruguay', 'mapo' ), __( 'Uzbekistan', 'mapo' ), __( 'Vanuatu', 'mapo' ), __( 'Vatican City', 'mapo' ),
		__( 'Venezuela', 'mapo' ), __( 'Vietnam', 'mapo' ), __('Virgin Islands, British', 'mapo' ), __('Virgin Islands, U.S.', 'mapo' ),
		__( 'Yemen', 'mapo' ), __( 'Zambia', 'mapo' ), __( 'Zimbabwe', 'mapo' )
	);
}

/**
* Dropdown of all countries
* Props to Gravity Forms
* @since 1.0
*/
function mapo_country_select( $selected_country = '' )
{
	$countries = array_merge( array(''), mapo_countries() );
	foreach( $countries as $country )
	{
		$selected = ( $country == $selected_country ) ? ' selected="selected"' : '';
		$options .= '<option value="'. esc_attr( $country ) .'"'. $selected .'>'. $country .'</option>';
	}
	
	echo $options;
}
?>