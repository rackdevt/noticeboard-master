<?php
/*
Plugin Name: Buddypress Friends
Plugin URI: http://hyperspatial.com/wordpress-development/plugins/buddypress-friends
Description: This widget displays the avatars of your buddypress friends.
Version: 1.2
Requires at least: 2.9.2 / 1.2.4
Tested up to: 3.2.1 / 1.5.1
Author: Adam J Nowak
Author URI: http://hyperspatial.com
License: GPL2
*/

/*
Copyright 2011  Adam J Nowak  (email : adam@hyperspatial.com)

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

/* Buddypress Friends */
class BuddypressFriends extends WP_Widget {
    function BuddypressFriends() {
        $widget_ops = array('classname' => 'widget_buddypress_friends', 'description' => __( "Display your list of friends") );
		$control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('buddypress_friends', __('Buddypress Friends'), $widget_ops, $control_ops);
    }

    function widget($args, $instance) {		
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
		$av_width = $instance['av_width'];
		$av_height = $instance['av_height'];
		$list_type = $instance['list_type'];
		$max_friends = $instance['max_friends'];
		$default_friends = $instance['default_friends'];
		$current_member_mode = $instance['current_member_mode'];
		
		require('includes/setup.php');
		require('includes/widget.php');
    }

    function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['av_width'] = $new_instance['av_width'];
		$instance['av_height'] = $new_instance['av_height'];
		$instance['list_type'] = $new_instance['list_type'];
		$instance['max_friends'] = $new_instance['max_friends'];
		$instance['default_friends'] = $new_instance['default_friends'];
		$instance['current_member_mode'] = $new_instance['current_member_mode'];
		return $instance;
	}

    function form($instance) {
		//Defaults
		$defaults = array(
			'title'   => 'My Buddypress Friends', 
			'av_width' => '40',
			'av_height' => '40',
			'list_type' => 'avatar',
			'max_friends' => '20',
			'default_friends' => '',
			'current_member_mode' => 'false'
		);
		$instance = wp_parse_args((array)$instance, $defaults); 
		$title = esc_attr($instance['title']);
		$av_width = esc_attr($instance['av_width']);
		$av_height = esc_attr($instance['av_height']);
		$list_type = esc_attr($instance['list_type']);
		$max_friends = esc_attr($instance['max_friends']);
		$default_friends = esc_attr($instance['default_friends']);
		$current_member_mode = esc_attr($instance['current_member_mode']);
		?>
        <p>
          <!-- Title Input Field -->
          <label for="<?php echo $this->get_field_id('title'); ?>">
            <?php _e('Widget Title:'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                    name="<?php echo $this->get_field_name('title'); ?>" 
                    value="<?php echo $title; ?>" 
                    type="text" />
          </label>
          <em style="font-size:10px">Type <strong>user</strong> to display logged in username</em>
        </p>
        <p>
          <!-- Default Friends -->
          <label for="<?php echo $this->get_field_id('default_friends'); ?>">
            <?php _e('Username - Default Friends:'); ?>
            <input class="widefat" id="<?php echo $this->get_field_id('default_friends'); ?>" 
                    name="<?php echo $this->get_field_name('default_friends'); ?>" 
                    value="<?php echo $default_friends; ?>" 
                    type="text" />
          </label>
          <em style="font-size:10px">Friends to show when logged out - leave blank for all</em>
        </p>
        <p>
          <!-- Width -->
          <label for="<?php echo $this->get_field_id('av_width'); ?>">
            <?php _e('Avatar Width:'); ?>
            <input style="width:40px;margin-right:6px;" id="<?php echo $this->get_field_id('av_width'); ?>" 
                    name="<?php echo $this->get_field_name('av_width'); ?>" 
                    value="<?php echo $av_width; ?>" 
                    type="text" />
          </label>
          <!-- Height -->
          <label for="<?php echo $this->get_field_id('av_height'); ?>">
            <?php _e('Avatar Height:'); ?>
            <input style="width:40px;" id="<?php echo $this->get_field_id('av_height'); ?>" 
                    name="<?php echo $this->get_field_name('av_height'); ?>" 
                    value="<?php echo $av_height; ?>" 
                    type="text" />
          </label>
        </p>
        <p>
          <!-- Max Friends -->
          <label for="<?php echo $this->get_field_id('max_friends'); ?>">
            <?php _e('Maximum Friends:'); ?>
            <input style="width:40px;" id="<?php echo $this->get_field_id('max_friends'); ?>" 
                    name="<?php echo $this->get_field_name('max_friends'); ?>" 
                    value="<?php echo $max_friends; ?>" 
                    type="text" />
          </label>
        </p>
        <p>
          <!-- Displayed Member Friends -->
          <label>
            <input id="<?php echo $this->get_field_id('current_member_mode') . '_1'; ?>" 
                      name="<?php echo $this->get_field_name('current_member_mode'); ?>" 
                      value="true"
                      type="checkbox" 
                      <?php if ($instance['current_member_mode'] == 'true') echo 'checked'; ?> />
            Current Member Mode</label><br />
            <em style="font-size:10px;margin-left:20px;">Shows the friends of the member you are inspecting</em>
        </p>
        <p>
          <!-- List Type -->
          <label>
            <input id="<?php echo $this->get_field_id('list_type') . '_1'; ?>" 
                      name="<?php echo $this->get_field_name('list_type'); ?>" 
                      value="list"
                      type="checkbox" 
                      <?php if ($instance['list_type'] == 'list') echo 'checked'; ?> />
            Show list instead of Avatars</label>
        </p>
        
<?php 
    }
} //End class BuddypressFriends
add_action('widgets_init', create_function('', 'return register_widget("BuddypressFriends");'));
?>