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

$blog_charset = get_blog_option( BP_ROOT_BLOG, 'blog_charset' );

header( 'Content-Type: text/xml; charset='. $blog_charset, true );
header( 'Status: 200 OK' );

echo '<?xml version="1.0" encoding="'. $blog_charset .'"?>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:georss="http://www.georss.org/georss"
    xmlns:gml="http://www.opengis.net/gml"
	<?php do_action( 'mapo_user_events_feed' ); ?>
>
<channel>
	<title><?php echo bp_site_name() ?> | <?php echo bp_core_get_user_displayname( $bp->displayed_user->id ) ?> | <?php _e( 'Latest Routes', 'mapo' ) ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php mapo_user_routes_feed_link() ?></link>
	<description><?php printf( __( '%s - Latest Routes Feed', 'mapo' ), bp_core_get_user_displayname( $bp->displayed_user->id )  ) ?></description>
	<pubDate><?php echo mysql2date( 'D, d M Y H:i:s O', mapo_user_get_last_published(), false ); ?></pubDate>
	<generator>http://shabushabu.eu/?v=<?php echo MAPO_VERSION ?></generator>
	<language><?php echo get_blog_option( BP_ROOT_BLOG, 'rss_language' ); ?></language>
	<?php do_action( 'mapo_user_feed_head' ); ?>

	<?php if( mapo_has_routes( 'public=3' ) ) : ?>
		<?php while ( mapo_routes() ) : mapo_the_route(); ?>
			<item>
				<guid><?php mapo_routes_link( false, 'routes', true ) ?></guid>
				<title>
                    <![CDATA[
                    <?php mapo_routes_name() ?>
                    ]]>
                </title>
				<link><?php mapo_routes_link( false, 'routes', true ) ?></link>
				<pubDate><?php echo mysql2date( 'D, d M Y H:i:s O', mapo_routes_date_created_raw(), false ); ?></pubDate>
				<description>
					<![CDATA[
					<?php mapo_routes_description() ?>
					]]>
				</description>
                <?php
                $waypoints = mapo_get_routes_waypoints();
				if( count( $waypoints ) >= 2 ) : ?>
                    <georss:where>
                        <gml:LineString>
                            <gml:posList>
                            <?php foreach( $waypoints as $k => $wp ) : ?>
                            <?php mapo_routes_waypoints_lat( $k ) ?> <?php mapo_routes_waypoints_lat( $k ) ?>
                            <?php endforeach; ?>
                            </gml:posList>
                        </gml:LineString>
                    </georss:where>
                    <?php foreach( $waypoints as $k => $wp ) : ?>
                        <entry>
                            <?php if( ! empty( $wp->title ) ) : ?>
                            <title>
                                <![CDATA[
                                <?php mapo_routes_waypoints_title( $k ) ?>
                                ]]>
                            </title>
                            <?php endif; ?>
                            <link href="<?php mapo_routes_link( false, 'routes', true ) ?>"/>
                            <id><?php mapo_routes_link( false, 'routes', true ) ?></id>
                            <updated><?php echo mysql2date( 'D, d M Y H:i:s O', mapo_routes_date_created_raw(), false ); ?></updated>
                            <?php if( ! empty( $wp->description ) ) : ?>
                            <content>
                                <![CDATA[
                                <?php mapo_routes_waypoints_description( $k ) ?>
                                ]]>
                            </content>
                            <?php endif; ?>
                            <georss:where>
                                <gml:Point>
                                    <gml:pos><?php mapo_routes_waypoints_lat( $k ) ?> <?php mapo_routes_waypoints_lat( $k ) ?></gml:pos>
                                </gml:Point>
                            </georss:where>
                            <?php do_action( 'mapo_global_feed_geo_entry' ); ?>
                        </entry>
                    <?php endforeach; ?>
                <?php endif; ?>
				<?php do_action( 'mapo_user_feed_item' ); ?>
			</item>
		<?php endwhile; ?>
	<?php endif; ?>
</channel>
</rss>