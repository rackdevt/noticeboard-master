=== Plugin Name ===
Contributors: baldgoat, nuprn1, etivite
Tags: buddypress, registration, activations, activation key
Requires at least: PHP 5.2, WordPress 3.2.1, BuddyPress 1.5.1
Tested up to: PHP 5.3.x, WordPress 3.6, BuddyPress 1.6.4
Stable tag: 1.0.2

This plugin allows bulk editing (manual activate, resend key, delete user) of pending BuddyPress user registrations on a (non-multisite) WordPress install. (Original code by Rich @etivite)

== Description ==

** IMPORTANT **
This plugin has been updated for BuddyPress 1.5.1


This plugin allows bulk editing (manual activate, resend activation key, delete user) of pending user registrations on a single (non-multisite) WordPress installation of BuddyPress

etivite is the orginal developer of this plugin. He handed off ownership to me and I've completely rebuilt it, but kept some of the core work he did. Thans for the great work you did, etivite! 

This plugin will NOT work for a multisite/network WordPress install - intended for single WordPress ONLY - for the moment.

== Installation ==

1. Upload the full directory into your wp-content/plugins directory
2. Activate the plugin at the plugin administration page

== Frequently Asked Questions ==

= My question isn't answered here =

Please check support forums http://wordpress.org/support/plugin/buddypress-pending-activations


== Changelog ==
= 1.0.2 =

* Removed 'Testing' message

= 1.0.1 =

* Accidentally left the 2 on the Plugin Name after my rebuild. I hope this doesn't cause any confusion.

= 1.0 =

* Completely rebuilt the plugin from the ground up.
* Moved plugin from Top-level BuddyPress Menu (Used in earlier BuddyPress versions) to a tab on Settings>BuddyPress
* Revamped Pending Activations screen
* Added search
* Added paging
* Added column sorting
* Added row actions - Activate, resend, or delete a single user without using bulk actions menu.
* Username links to user edit screen
* Click email address to send an email to the user

= 0.5.4 =

* BUG: Fixed bulk resend only sending first email

= 0.5.3 =

* BUG: Fixed 'prepare' errors in admin

= 0.5.2 =

* BUG: tidy up php notices

= 0.5.1 =

* FEATURE: support for locale mo files

= 0.5.0 =

*BUG: updated for BuddyPress 1.5.1

= 0.2.1 =

* Bug: Fixed display of resent key date

= 0.2.0 =

* display count on wp-admin menu link (wp_cached)

= 0.1.0 =

* First [BETA] version


== Upgrade Notice ==

= 0.5.0 =
* BuddyPress 1.5.1 and higher - required.


== Extra Configuration ==

