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
 * Get coordinates from all users
 * @since 1.0
 */
function mapo_distance_dropdown()
{
	global $mapo, $bp;
	
	if( ! apply_filters( 'mapo_show_proximity_filter', is_user_logged_in() ) )
		return false;

	$dist = ( $mapo->options->system == 'm' ) ? __( 'm', 'mapo' ) : __( 'km', 'mapo' );

	$action = current_filter();
	
	$context = false;
	if( $action == 'bp_members_directory_member_types' )
		$context = 'members';
	elseif( $action == 'bp_activity_syndication_options' )
		$context = 'activity';
	elseif( $action == 'bp_groups_directory_group_types' )
		$context = 'groups';
		
	if( ! $context )
		return false;

	$c = $_COOKIE['bp-'. $context .'-extras'];
	$scope = $_COOKIE['bp-'. $context .'-scope'];
	
	if( ! in_array( $scope, array( 'personal', 'all', 'friends', 'mentions', '' ) ) )
		return false;

	$cookie = array();
	parse_str( $c, $cookie );
	?>
	<li id="within-filter-select" class="last">
    	<?php do_action( 'mapo_before_proximity_action', $action ) ?>    
    	<?php _e( 'Within:', 'mapo' ) ?>
        <select class="prox-filter">
            <option value="">----</option>
			<?php foreach( $mapo->config->prox_options as $prox ) : ?>
	            <option<?php if( $cookie['value'] == $prox ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $prox ) ?>"><?php echo $prox .' '. $mapo->options->system ?></option>
			<?php endforeach; ?>

            <?php do_action( 'mapo_distance_drop_down', $dist, $cookie, $context ) // deprecated, use mapo_prox_options filter instead ?>
        </select>
    <li>
	<?php
}
add_action( 'bp_members_directory_member_types', 'mapo_distance_dropdown' );
add_action( 'bp_activity_syndication_options', 'mapo_distance_dropdown' );
add_action( 'bp_groups_directory_group_types', 'mapo_distance_dropdown' );

/**
 * Modify the querystring for members and activity
 * @since 1.2.1
 */
function mapo_filter_add_querystring( $query_string, $object, $filter, $scope, $page, $terms, $extras )
{
	global $bp;
	
	if( ! in_array( $bp->current_component, array( BP_MEMBERS_SLUG, $bp->activity->slug, $bp->groups->slug ) ) )
		return $query_string;
		
	if( ! empty( $bp->current_action ) )
		return $query_string;
		
	if( ! empty( $extras ) ) :
		$cookie = array();
		parse_str( $extras, $cookie );
			
		if( $cookie['context'] != 'mapology' )
			return $query_string;
			
		if( empty( $cookie['value'] ) )
			return $query_string;

		$args = array();
		parse_str( $query_string, $args );

		if( $object == 'members' )
		{
			$user_ids = mapo_get_user_ids( $cookie['value'], $scope );
			if( empty( $user_ids ) )
				$user_ids = '-1';
			
			$args['include'] = $user_ids;
		}
		elseif( $object == 'activity' )
		{
			$act_ids = apply_filters( 'mapo_proximity_activity_ids', mapo_get_activity_ids( $cookie['value'], $args['type'], $scope ), $cookie, $args, $scope );
			if( empty( $act_ids ) )
				$act_ids = '-1';

			$args['include'] = $act_ids;
		}
		elseif( $object == 'groups' )
		{
			$group_ids = mapo_get_group_ids( $cookie['value'], $scope );
			if( empty( $group_ids ) )
				$group_ids = '-1';
			
			$args['include'] = $group_ids;
		}

		foreach( $args as $key => $val )
			$q[] = $key .'='. $val;
		
		$query = join( '&', (array)$q );
			
		return $query;
	endif;

	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'mapo_filter_add_querystring', 10, 7 );

/**
 * Set the member cookies and add the JS
 * @since 1.2.1
 */
function mapo_prox_add_member_js()
{
	?>
    <script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('div.item-list-tabs li a').click( function() {
			var allowed = ['personal', 'all'];
			var parent = jQuery(this).parent('li');
			var scope = parent.attr('id').substr( 8, parent.attr('id').length );
			
			if( jQuery.inArray(scope, allowed) == -1 ) {
				jQuery('#within-filter-select').hide();
				jQuery.cookie('bp-members-extras', '');
			} else {
				jQuery('#within-filter-select').show();
			}
		});
		
		jQuery('.prox-filter').change( function() {
			var value = jQuery(this).val();
			var filter = jQuery('#members-order-select select').val();

			var selected_tab = jQuery( 'div.item-list-tabs li.selected' );
			if ( !selected_tab.length ) {
				var scope = null;
			} else {
				var scope = selected_tab.attr('id').substr( 8, selected_tab.attr('id').length ); }

			if ( value != '' ) {
				jQuery.cookie('bp-members-extras', 'value='+ value +'&context=mapology');
			} else {
				jQuery.cookie('bp-members-extras', '');
			}

			bp_filter_request( 'members', filter, scope, 'div.members', false, 1, jQuery.cookie('bp-members-extras') );
			return false;
		});
	});
	</script>
    <?php
}
add_action( 'bp_after_directory_members_content', 'mapo_prox_add_member_js' );

/**
 * Set the member cookies and add the JS
 * @since 1.2.1
 */
function mapo_prox_add_group_js()
{
	?>
    <script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('div.item-list-tabs li a').click( function() {
			var allowed = ['personal', 'all'];
			var parent = jQuery(this).parent('li');
			var scope = parent.attr('id').substr( 7, parent.attr('id').length );
			
			if( jQuery.inArray(scope, allowed) == -1 ) {
				jQuery('#within-filter-select').hide();
				jQuery.cookie('bp-groups-extras', '');
			} else {
				jQuery('#within-filter-select').show();
			}
		});
		
		jQuery('.prox-filter').change( function() {
			var value = jQuery(this).val();
			var filter = jQuery('#groups-order-select select').val();

			var selected_tab = jQuery( 'div.item-list-tabs li.selected' );
			if ( !selected_tab.length ) {
				var scope = null;
			} else {
				var scope = selected_tab.attr('id').substr( 7, selected_tab.attr('id').length ); }

			if ( value != '' ) {
				jQuery.cookie('bp-groups-extras', 'value='+ value +'&context=mapology');
			} else {
				jQuery.cookie('bp-groups-extras', '');
			}

			bp_filter_request( 'groups', filter, scope, 'div.groups', false, 1, jQuery.cookie('bp-groups-extras') );
			return false;
		});
	});
	</script>
    <?php
}
add_action( 'bp_after_directory_groups_content', 'mapo_prox_add_group_js' );

/**
 * Set the member cookies and add the JS
 * @since 1.2.1
 */
function mapo_prox_add_activity_js()
{
	?>
    <script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('div.activity-type-tabs li a').click( function() {
			var allowed = ['all', 'friends', 'groups', 'mentions'];
			var parent = jQuery(this).parent('li');
			var scope = parent.attr('id').substr( 9, parent.attr('id').length );
			
			if( jQuery.inArray(scope, allowed) == -1 ) {
				jQuery('#within-filter-select').hide();
				jQuery.cookie('bp-activity-extras', '');
			} else {
				jQuery('#within-filter-select').show();
			}
		});

		jQuery('.prox-filter').change( function() {
			var value = jQuery(this).val();
			var act_filter = jQuery('#activity-filter-select select').val();
			var selected_tab = jQuery( 'div.activity-type-tabs li.selected' );
	
			if ( !selected_tab.length ) {
				var scope = null;
			} else {
				var scope = selected_tab.attr('id').substr( 9, selected_tab.attr('id').length ); }
				
			if ( value != '' ) {
				jQuery.cookie('bp-activity-extras', 'value='+ value +'&context=mapology');
			} else {
				jQuery.cookie('bp-activity-extras', '');
			}

			bp_activity_request(scope, act_filter);
		});
	});
	</script>
    <?php
}
add_action( 'bp_after_directory_activity_content', 'mapo_prox_add_activity_js' );

/**
 * Get the user ids
 * http://code.google.com/apis/maps/articles/phpsqlsearch.html#findnearsql
 * @since 1.0
 */
function mapo_get_user_ids( $filter, $scope )
{
	global $wpdb, $mapo, $bp;
	
	if( empty( $filter ) )
		return false;
	
	$dist = ( $mapo->options->system  == 'm' ) ? 3959 : 6371;
	
	$coords = new MAPO_Coords( null, $bp->loggedin_user->id );
	$coords = apply_filters( 'mapo_proximity_user_coords', $coords, $bp->loggedin_user->id, $filter );

	$users = $wpdb->get_results( "
		SELECT c.user_id, ( {$dist} * acos( cos( radians( {$coords->lat} ) ) * cos( radians( c.lat ) ) * cos( radians( c.lng ) - radians( {$coords->lng} ) ) + sin( radians( {$coords->lat} ) ) * sin( radians( c.lat ) ) ) ) as distance
		FROM {$mapo->tables->coords} c
		RIGHT JOIN {$wpdb->users} u
		ON u.ID = c.user_id
		HAVING distance < {$filter}
		ORDER BY distance
	" );

	foreach( (array)$users as $user )
		if( $user->user_id != 0 && $user->user_id != $bp->loggedin_user->id )
			$uids[] = $user->user_id;

	// maybe remove any user_ids to respect scope
	if( $scope == 'personal' || $scope == 'friends' )
	{
		$friends = friends_get_friend_user_ids( $bp->loggedin_user->id );
		$uids = array_intersect( $uids, $friends );
	}
	elseif( $scope == 'groups' )
	{
		$data = groups_get_user_groups( $bp->loggedin_user->id );
		$group_ids = join( ',', (array)$data['groups'] );
		$member_ids = $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$bp->groups->table_name_members} WHERE group_id IN ({$group_ids}) AND is_confirmed = %d", 1 ) );

		$uids = array_intersect( $uids, $member_ids );
	}
			
	if( count( $uids ) < 1 )
		return '-1';
		
	$uids = $wpdb->escape( join( ',', (array)$uids ) );

	return $uids;
}

/**
 * Get the activity ids
 * @since 1.0
 */
function mapo_get_group_ids( $filter, $scope )
{	global $wpdb, $mapo, $bp;
	
	if( empty( $filter ) )
		return false;
	
	$dist = ( $mapo->options->system  == 'm' ) ? 3959 : 6371;
	
	$coords = new MAPO_Coords( null, $bp->loggedin_user->id );
	$coords = apply_filters( 'mapo_proximity_user_coords', $coords, $bp->loggedin_user->id, $filter );

	$groups = $wpdb->get_results( "
		SELECT c.group_id, ( {$dist} * acos( cos( radians( {$coords->lat} ) ) * cos( radians( c.lat ) ) * cos( radians( c.lng ) - radians( {$coords->lng} ) ) + sin( radians( {$coords->lat} ) ) * sin( radians( c.lat ) ) ) ) as distance
		FROM {$mapo->tables->coords} c
		RIGHT JOIN {$bp->groups->table_name} g
		ON g.id = c.group_id
		HAVING distance < {$filter}
		ORDER BY distance
	" );
	
	foreach( (array)$groups as $group )
		if( $group->group_id != 0 )
			$gids[] = $group->group_id;

	// maybe remove any user_ids to respect scope
	if( $scope == 'personal' )
	{
		$groups = groups_get_user_groups( $bp->loggedin_user->id );
		$gids = array_intersect( $gids, $groups );
	}

	if( count( $gids ) < 1 )
		return '-1';

	$gids = $wpdb->escape( join( ',', (array)$gids ) );

	return $gids;
}

/**
 * Get the activity ids
 * @since 1.0
 */
function mapo_get_activity_ids( $filter, $type, $scope )
{
	global $wpdb, $bp;
	
	$uids = mapo_get_user_ids( $filter, $scope );

	if( $uids == '-1' )
		return '-1';
		
	$type_sql = '';
	if( $type )
	{
		$type = explode( ',', $type );
		
		foreach( $type as $t )
			$types[] = "'". $t ."'";
			
		$types = join( ',', (array)$types );
		$type_sql = " AND type IN ({$types})";
	}
	
	$mentions_sql = '';
	if( $scope == 'mentions' )
	{
		$search_term = '@' . bp_core_get_username( $bp->loggedin_user->id, $bp->loggedin_user->userdata->user_nicename, $bp->displayed_user->userdata->user_login ) . '<';
		$search_term = like_escape( $wpdb->escape( $search_term ) );
		
		$mentions_sql = " AND content LIKE '%%{$search_term}%%'";
	}

	$act_ids = $wpdb->get_col( "SELECT id FROM {$bp->activity->table_name} WHERE user_id IN ({$uids}){$type_sql}{$mentions_sql}" );

	return $wpdb->escape( join( ',', (array)$act_ids ) );
}
?>