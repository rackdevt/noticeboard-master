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
?>
<form id="mapo_create_route" class="standard-form" action="" method="post">

    <?php wp_nonce_field( 'mapo_add_route' ) ?>

    <label for="name"><?php _e( '* Name', 'mapo' ) ?></label>
    <input type="text" name="name" id="name" value="" />

    <label for="description"><?php _e( 'Description', 'mapo' ) ?></label>
    <textarea name="description" id="description"></textarea>

    <div class="date-wrapper">
        <label for="start_date"><?php _e( 'Start Date', 'mapo' ) ?></label>
        <input type="text" class="date-input" name="start_date" id="start_date" value="" />
    </div>
        
    <div class="date-wrapper">
        <label for="end_date"><?php _e( 'End Date', 'mapo' ) ?></label>
        <input type="text" class="date-input" name="end_date" id="end_date" value="" />
    </div>

    <div class="clear"></div>
    
    <div id="mapology-create-map"></div>
    <p><?php _e( 'The type, zoom level and center of the saved map will reflect the settings in the map above.<br />Note: Waypoint IDs use internal Google Maps IDs.', 'mapo' ) ?></p>

    <a class="button" id="wpshow" href="#"><?php _e( 'Show Waypoints', 'mapo' ) ?></a>
    <a class="button" id="wphide" href="#"><?php _e( 'Hide Waypoints', 'mapo' ) ?></a>

	<div id="mapology-waypoints"></div>
    
    <label for="public"><?php _e( '* Privacy', 'mapo' ) ?></label>
    <select id="public" name="public">
        <option value="">----</option>
        <option value="0"><?php _e( 'Only me', 'mapo' ) ?></option>
        <option value="1"><?php _e( 'Friends', 'mapo' ) ?></option>
        <option value="2"><?php _e( 'Logged-in members', 'mapo' ) ?></option>
        <option value="3"><?php _e( 'Everybody', 'mapo' ) ?></option>
    </select>

	<?php if( mapo_is_group_enabled() ) : ?>
    <label for="group_id"><?php _e( 'Group', 'mapo' ) ?></label>
    <select id="group_id" name="group_id">
        <option value="">----</option>
        <?php foreach( mapo_get_user_groups() as $group ) : ?>
        <option value="<?php echo $group->group_id ?>"><?php echo $group->name ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    
    <input type="hidden" id="zoom" name="zoom" value="2" />
    <input type="hidden" id="type" name="type" value="hybrid" />
    <input type="hidden" id="default_coords" name="default_coords" value="(5, 30)" />
    <input type="hidden" id="order_coords" name="order_coords" value="" />

    <div class="submit">
        <input type="submit" value="<?php _e( 'Save Route', 'mapo' ) ?>" id="save-route" name="save-route" />
    </div>
</form>