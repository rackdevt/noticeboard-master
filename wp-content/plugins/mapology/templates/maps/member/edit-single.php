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

global $bp;

if( mapo_has_routes( array( 'slug' => $bp->action_variables[0] ) ) ) : ?>

	<ul id="routes-list" class="item-list">
	
	<?php while ( mapo_routes() ) : mapo_the_route();	?>

  		<li id="route-<?php mapo_routes_id() ?>" class="single-route">
            <form id="mapo_edit_route" class="standard-form" action="" method="post">
            
                <?php wp_nonce_field( 'mapo_edit_route' ) ?>
            
                <label for="name"><?php _e( '* Name', 'mapo' ) ?></label>
                <input type="text" name="name" id="name" value="<?php mapo_routes_name_raw() ?>" />
            
                <label for="description"><?php _e( 'Description', 'mapo' ) ?></label>
                <textarea name="description" id="description"><?php mapo_routes_description_raw() ?></textarea>
            
                <div class="date-wrapper">
                    <label for="start_date"><?php _e( 'Start Date', 'mapo' ) ?></label>
                    <input type="text" class="date-input" name="start_date" id="start_date" value="<?php mapo_routes_start_date_raw() ?>" />
                </div>
                    
                <div class="date-wrapper">
                    <label for="end_date"><?php _e( 'End Date', 'mapo' ) ?></label>
                    <input type="text" class="date-input" name="end_date" id="end_date" value="<?php mapo_routes_end_date_raw() ?>" />
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
                    <option<?php if( mapo_get_routes_public() == 0 ) echo ' selected="selected"' ?> value="0"><?php _e( 'Only me', 'mapo' ) ?></option>
                    <option<?php if( mapo_get_routes_public() == 1 ) echo ' selected="selected"' ?> value="1"><?php _e( 'Friends', 'mapo' ) ?></option>
                    <option<?php if( mapo_get_routes_public() == 2 ) echo ' selected="selected"' ?> value="2"><?php _e( 'Logged-in members', 'mapo' ) ?></option>
                    <option<?php if( mapo_get_routes_public() == 3 ) echo ' selected="selected"' ?> value="3"><?php _e( 'Everybody', 'mapo' ) ?></option>
                </select>
                
				<?php if( mapo_is_group_enabled() ) : ?>
                <label for="group_id"><?php _e( 'Group', 'mapo' ) ?></label>
                <select id="group_id" name="group_id">
                    <option value="">----</option>
                    <?php foreach( mapo_get_user_groups() as $group ) : ?>
                    <option<?php if( mapo_get_routes_group_id() == $group->group_id ) echo ' selected="selected"' ?> value="<?php echo $group->group_id ?>"><?php echo $group->name ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                
                <input type="hidden" id="zoom" name="zoom" value="<?php mapo_routes_zoom() ?>" />
                <input type="hidden" id="type" name="type" value="<?php echo strtolower( mapo_get_routes_type() ) ?>" />
                <input type="hidden" id="default_coords" name="default_coords" value="(<?php mapo_routes_default_lat() ?>, <?php mapo_routes_default_lng() ?>)" />
                <input type="hidden" id="order_coords" name="order_coords" value="" />
            
                <div class="submit">
                    <input type="submit" value="<?php _e( 'Edit Route', 'mapo' ) ?>" id="edit-route" name="edit-route" />
                    <input type="submit" value="<?php _e( 'Delete Route', 'mapo' ) ?>" id="delete-route" name="delete-route" />
                </div>
            </form>
            <?php do_action( 'mapo_edit_single_js' ) ?>         
        </li>

	<?php endwhile; ?>
	
	</ul>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No routes were found.', 'mapo' ) ?></p>
	</div>

<?php endif; ?>