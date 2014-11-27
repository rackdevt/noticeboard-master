=== Buddypress Friends ===
Contributors: Adam J Nowak
Donate link: http://hyperspatial.com/donate
Tags: buddypress,buddypress friends,friends,avatars,social networking
Requires at least: 2.9
Tested up to: 3.2.1
Stable tag: 1.2

This plugin adds a widget to Buddypress that displays the friends for the current user that is logged in.

== Description ==

This plugin adds a widget to Buddypress that displays the friends for the current user that is logged in.  They are displayed as Avatar images or as a list of your friends.  You can easily resize the avatar images and control how many of your friends display in the widget.  This plugin will be expanding to include a lot more features including sorting friends in different manners.

== Installation ==

1. Upload `buddypress-friends.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Instructions: 

Note: This plugin is for extending the functionality of Buddypress.  Buddypress must be installed.

1. Install the plugin
2. Drag an instance of the widget into the desired sidebar
3. Type in a title for your widget
4. Select the maximum number of friend to be shown in the list
5. Select a width and a height for the avatar images, a trial and error approach can be used to get them to fit the way you want
6. Check the box for 'current member mode' if you want to show the friends of the member who's profile you are looking at
7. If you want to just display a list of links with no image check the ‘Display as List’ checkbox

== Screenshots ==

1. Widget in action
2. The Admin backend


== Changelog ==

= 1.0 =
* Hello Buddypress World! Wahoo
= 1.1 =
* Fixed issue with pagination limiting friends to 20
* Added the 'current member mode' feature
= 1.11 =
* Fixed current member mode problem when you are not on a member page
* Changed file structure
= 1.12 =
* Bug patrol, hopfully got the little suckers
= 1.13 =
* Broke the customizable title function when bug hunting, fixed it
= 1.2 =
* Removed loader.php file due to issue with BP 1.5 and WP 3.2