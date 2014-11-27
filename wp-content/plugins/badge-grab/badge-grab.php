<?php
/*
Plugin Name: Badge Grab
Version: 1.1
Description: Add's an image and html in a text area for visitors to copy/paste to show how much they love your site. Uses shortcode.
Author: Daisy Olsen and Lisa Boyd
Author URI: http://daisyolsen.com/
Plugin URI: http://daisyolsen.com/plugins/badge-grab
*/

/*  Copyright 2009  Daisy Olsen and Lisa Boyd  (email : daisy@daisyolsen.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Future Feature: Add a Widget to include Badge Grab code in a widget area. */

/* Check version for compatibility with WP ShortCode */
global $wp_version;

$exit_msg = 'Badge Grab requires WordPress 2.5 or newer. Please Upgrade your WordPress site!';

if (version_compare($wp_version,"2.5","<"))	{
		exit ($exit_msg);
	}
	
/* Add the shortcode options to insert the badge based on the target URL, a Title, and the image */

add_shortcode('badge', 'badge_grab_shortcode');

function badge_grab_shortcode($atts, $content = null) {
	
	$url = get_option('badge_grab_url');
	$title = get_option('badge_grab_title');
	$image = get_option('badge_grab_image');

	extract(shortcode_atts( array(
		'url' => esc_url($url),
		'title' => wp_kses($title, ''),
		'image' => esc_url($image),
	), $atts));
	return '
	<img src="' . esc_url($image) . '" alt="' . wp_kses($title, '') . '" title="' . wp_kses($title, '') . '" style="border:none;" />
	<textarea onclick="select()" style="max-width: 100%;" cols="60" rows="5" name="' . wp_kses($title, '') . '" readonly="readonly">&lt;a href="' . esc_url($url) . '" title="' . wp_kses($title, '') . '"&gt;&lt;img src="' . esc_url($image) . '" alt="' . wp_kses($title, '') . '" title="' . wp_kses($title, '') . '" style="border:none;" /&gt;&lt;/a&gt;</textarea>';
}


/* Adds an options page to set defaults for the shortcode */
function badge_grab_config_page() {
	if(!empty($_POST)) {
	//update options
        update_option('badge_grab_url', $_POST['badge_grab_url']);
        update_option('badge_grab_title', $_POST['badge_grab_title']);
        update_option('badge_grab_image', $_POST['badge_grab_image']);
	}

    echo '<div class="wrap">
            '. screen_icon() .'
            <h2>'.__('Badge Grab Default Options Page', 'badge_grab').'</h2>';

    if(!empty($_POST)) {
        echo '<div id="message" class="updated fade"><p><strong>'.__('Settings saved', 'badge_grab').'</strong></p></div>';
    }

    echo   '<p>
			Shortcode inserted using in a post or page will return badge grab code using the defaults as entered here.<br />
			Each default value can be over-ridden in the shortcode. <br />
			<strong>Example:</strong><br />
			[badge title="Custom Title" url="http://mysite.com image="http://mysite.com/customimage.jpg"]
			</p>
			<form method="POST">
            <table class="form-table">
            <tr>
                <th><label for="badge_grab_url">'.__('Default Link URL(Must begin with http://):', 'badge_grab').'</label></th>
                <td>
                    <input type="text" size=80 name="badge_grab_url" id="badge_grab_url" value="' . esc_url(get_option('badge_grab_url')) .'" />
                </td>
            </tr>
            <tr>
                <th><label for="badge_grab_title">'.__('Default Link Title/ Image Alt:', 'badge_grab').'</label></th>
                <td>
                    <input type="text" size=80 name="badge_grab_title" id="badge_grab_title" value="' . wp_kses(get_option('badge_grab_title'), '') .'" />
                </td>
            </tr>
            <tr>
                <th><label for="badge_grab_image">'.__('Default Image URL(Must begin with http://):', 'badge_grab').'</label></th>
                <td>
                    <input type="text" size=80 name="badge_grab_image" id="badge_grab_imagel" value="' . esc_url(get_option('badge_grab_image')) .'" />
                </td>
            </tr>
            </table>
            <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="'.__('Save Changes', 'badge_grab').'" />
            </p>
            </form>';
}
	
function badge_grab_configpagelink() {
	add_options_page('badge_grab', 'Badge Grab', 6, basename(__FILE__), 'badge_grab_config_page');
}
	
add_action('admin_menu', 'badge_grab_configpagelink');
?>