=== Admin Log ===
Contributors: dgwyer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4051383
Tags: output, report, demo, code, test, admin, log
Requires at least: 2.7
Tested up to: 3.8
Stable tag: 1.44

Need to see who is accessing what admin pages? This Plugin logs admin activity, and shows: admin page accessed, user information, and time of access.

== Description ==

Displays a list of all the admin pages accessed in your sites admin area. This is updated every time a page in the admin area is accessed. Information displayed includes: admin page accessed, user, and time of access.

This is very useful if more than one person maintains your Blog, so you can see exactly who is accessing the admin pages, what they are doing, and when!

The Plugin has been completely re-written to be fully compliant with WordPress 3.x and address some security issues. In particular the Plugin doesn't store admin log information in a plain text file any more. It now uses the WordPress options database. There is also a 200 line log limit to prevent the log getting too big.

Other new options allow you clear the admin log, and also prevent the admin log Plugin page from being logged. This helps to prevent the log from being inflated everytime you visit the Plugin options page.

See our <a href="http://wordpress.org/extend/plugins/profile/dgwyer">other Plugins here</a>.

== Installation ==

Instructions for installing the Admin Log Plugin.

1. Download and extract the Plugin zip file.
2. Upload the folder containing the Plugin files to your WordPress Plugins folder (usually '../wp-content/plugins/').
3. Activate the Plugin via the 'Plugins' menu in WordPress.
4. Once activated, go to the Plugin options under the 'Settings' menu.

== Changelog ==

*1.44 update*

* Removed redundant code.

*1.43 update*

* Addressed security issue.

*1.42 update*

* Added 'admin-ajax.php' to a list of uri's to ignore which reduces the admin log 'bloat'.

*1.41 update*

* Fixed bug: Admin log date now reflects WordPress date settings (i.e. timezone) rather than just getting date via PHP.

*1.4 update*

* Changed Plugin structure to use PHP class.
* Added user IP address to admin log.
* Fixed bug: Ignore admin log option not working properly.
* User display name now shown with user ID number in brackets. This is instead of user first name and last name.

*1.3 update*

* Added a 200 line limit to the admin log. If it goes over the limit the first line is deleted before a new log entry is added.

*1.2.1 update*

* Added missing currURL() function.

*1.2 update*

* Plugin now uses the Settings API.
* Log now stored in WordPress options table, not plain text file.
* Added options to clear log, and prevent Admin Log Plugin page from being logged.