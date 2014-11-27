=== Mapology ===
Contributors: travel-junkie
Donate link: http://shabushabu.eu/donation/
Tags: buddypress, google, maps
Requires at least: WP 3.2.1, BP 1.5
Tested up to: WP 3.4.1, BP 1.6.1
Stable tag: 1.3.6

Add various Google Maps (v3) for groups, members and posts

== Description ==

Adds Google Maps for groups, locations post type, blog posts and members

Features:
* Overview map for members and (optionally) groups
* Activity stream integration for users
* Profile integration
* Uses MarkerClusterer for basically unlimited numbers of markers
* Search radius for users, activity and groups (available from BP 1.3)
* Blog integration (shortcodes)
* User routes
* Routes can be attached to a group
* Activity integration
* Attach coordinates to blog posts
* Export as KML file
* Custom location post type
* Geo sitemap of all kml files
* Adds geoRSS info to normal feeds
* Feeds for global, member and group routes
* Group admins can remove routes from their group
* Routes directory, groups and member overview maps
* Integration with BP Moderation
* oEmbed support for route descriptions
* Grid view for routes
* Locations privacy settings
* Enhanced members map (members, groups, events (Buddyvents)), incl. map search
* Widget

Future features:
= v2.x =
* AJAX pagination
* AJAX route creation
* Import KML files
* Route categories
* Route tags
* Directions
* Frontend locations submission
* iPhone app to update locations
* Admin definable fields (like BP profile fields)

= v1.x =
* Custom infowindows
* Configurable distance search (e.g. 10 km from a chosen city)
* Proximity filtering for profiles
* Blog posts/location post type overview map
* Add super admin menu (edit/delete)
* Sort routes by continent/date/anything else
* Set default directory map locations (for user/groups and directories)
* Highlight locations tab on custom post type and custom taxonomies
* Refactor to use http://code.google.com/p/phpgooglemapapiv3/
* Combine KML files together for download (by group/user)
* When creating/editing routes be able to stop waypoint connections
* Create a map where all pins (routes/members/groups/events/custom components) can be seen
* Random route
* Finish hook reference
* Map ratings
* Achievements integration

Translations:
* German

All other translations are most welcome :)


== Installation ==

1. Upload all files to wp-content/plugins/mapology
2. Create the files kml-sitemap.xml and kml-sitemap.xml.gz in the root of WP and set permissions to 777
3. Activate the plugin in your admin backend
4. Go to BuddyPress->Mapology and adjust the options

That's it ... enjoy!


== Upgrade ==

1. Deactivate Mapology
2. Backup any custom image, stylesheet and language files
3. Upload all files via FTP to wp-content/plugins/mapology
4. Restore custom language, stylesheets and image files
5. Activate Mapology


== Frequently Asked Questions ==

= What about feature requests? =

You can register on our [support site](http://shabushabu.eu/membership-options/) and leave a comment in our support forums.

= Sometimes the static map images don't show up anymore. Why? =

Google allows 1000 views per image per day. This will be enough for most sites. If you have a large (very) active user base with lots of routes,
then some users could reach that limit.

= What are the minimum requirements to run Mapology? =

That would be:
WordPress 3.0.1
BuddyPress 1.2.6
PHP 5

Mapology might run on earlier versions of WP and BP, but they are not supported.


= Support =

Upon purchase of this plugin you receive support for v1.x of the plugin. You will need to purchase the plugin again when the version reaches 2.0