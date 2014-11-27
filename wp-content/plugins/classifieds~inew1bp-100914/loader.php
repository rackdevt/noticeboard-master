<?php
/*
Plugin Name: Classifieds
Plugin URI: http://premium.wpmudev.org/project/classifieds
Description: Add Classifieds to your blog, network or BuddyPress site. Create and manage ads, upload images, send emails, enable the credit system and charge your users for placing ads on your network or BuddyPress site.
Version: 2.3.3.9
Author: Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)
Author URI: http://premium.wpmudev.org
License: GNU General Public License (Version 2 - GPLv2)
Text Domain: classifieds
Domain Path: /languages
Network: false
WDP ID: 158
*/

$plugin_header_translate = array(
__('Classifieds - Add Classifieds to your blog, network or BuddyPress site. Create and manage ads, upload images, send emails, enable the credit system and charge your users for placing ads on your network or BuddyPress site.', 'classifieds'),
__('Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)', 'classifieds'),
__('http://premium.wpmudev.org', 'classifieds'),
__('Classifieds', 'classifieds'),
);

/*
Copyright 2007-2012 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/* Define plugin version */
define ( 'CF_VERSION', '2.3.3.9' );
define ( 'CF_DB_VERSION', '2.0' );

/* define the plugin folder url */
define ( 'CF_PLUGIN_URL', plugin_dir_url(__FILE__));
/* define the plugin folder dir */
define ( 'CF_PLUGIN_DIR', plugin_dir_path(__FILE__));
// The key for the options array
define( 'CF_TEXT_DOMAIN', 'classifieds' );
// The key for the options array
define( 'CF_OPTIONS_NAME', 'classifieds_options' );

// include core files
include_once 'core/wpmudev-dash-notification.php';
//If another version of CustomPress not loaded, load ours.
if(!class_exists('CustomPress_Core')) include_once 'core/custompress/loader.php';

/* Load plugin files */
include_once 'core/core.php';
include_once 'core/payments.php';
include_once 'core/paypal-express-gateway.php';
include_once 'core/functions.php';