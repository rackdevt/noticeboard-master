=== Badge Grab ===
Contributors: daisyo, LeaseahB
Donate link: http://daisyolsen.com/plugins/badge-grab/
Tags: badge, image, shortcode
Requires at least: 2.5
Tested up to: 3.4.1
Stable tag: 1.2

Badge Grab simplifies the process of adding html badge code to encourage visitors to link back to your site with an image link.

== Description ==

This plugin will allow you to use the `[badge]` shortcode to display a helpful and valid html snippet to encourage others to link back to your website by placing a linked image on thier own site.

This plugin does not have a Widget at this time but I recommend trying the [Shortcode For Sidebar Plugin](http://wordpress.org/extend/plugins/shortcode-for-sidebar/) to use the shortcode for plugin in a text widget.

Attributes:
url - This will be the link added to the html to be copied.
title - This will be the alt tag and title tag applied to the image and link.
image - URL to Image to be used to display sample of badge and included in the provided html

Usage examples:
`[badge]` (Will display default code based on options page settings
`[badge url='http://daisyolsen.com' title='DaisyOlsen.com' image='http://daisyolsen.com/images/daisy125.png']`

Default URL, Image and Title settings for the shortcode can be set from the Badge Grab settings page.  If the settings page options are populated then all shortcode attributes are optional.

The [shortcode](http://codex.wordpress.org/Shortcode_API) can be used multiple times on the same page to make multiple variations of your badge available to your visitors.

Bug Reports and Questions can be reported at the [Badge Grab Plugin Page](http://daisyolsen.com/plugins/badge-grab)

== Installation ==

1. Extract Plugin Zip File
2. Upload the text-widget-oembed directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Options Settings Page for Shortcode Defaults
2. Example of usage in post or page.
3. Display of shortcodes on the site in a post or page

== Changelog ==

= 1.0 =
* Initial Release

= 1.1 =
* Removed Visual Editor Button.
* Security Improvements
* Minor UI Improvements

= 1.2 =
* Updated WordPress Version Supported
