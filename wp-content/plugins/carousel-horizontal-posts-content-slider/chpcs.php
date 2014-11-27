<?php
/*
Plugin Name: Carousel Horizontal Posts Content Slider
Description: This is JQuery CarouFredSel library based WordPress horizontal posts content slider.
Author: subhansanjaya
Version: 3.2.3
Plugin URI: http://wordpress.org/plugins/carousel-horizontal-posts-content-slider/
Author URI: http://www.weaveapps.com
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BXBCGCKDD74UE
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if(! defined( 'ABSPATH' )) exit; // Exit if accessed directly
require('classes/class-chpcs.php');

global $CHPCS;
$CHPCS = new CHPC_Slider();