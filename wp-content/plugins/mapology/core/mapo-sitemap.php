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

class MAPO_KML_Sitemap
{
	var $output;
	var $xml_file;
	var $gz_file;
	var $ping_url;
	
	function __construct()
	{
		$this->xml_file = '/kml-sitemap.xml';
		$this->gz_file = '/kml-sitemap.xml.gz';
		$this->ping_url = 'http://www.google.com/webmasters/sitemaps/ping?sitemap=';
		
		$this->build();
		$this->create_xml();
		$this->create_gz();
		$this->ping();
	}
	
	function ping()
	{
		global $mapo, $bp;
		
		if( file_exists( ABSPATH . $this->gz_file ) )
			$link = urlencode( bp_get_root_domain() . $this->gz_file );
		elseif( file_exists( ABSPATH . $this->xml_file ) )
			$link = urlencode( bp_get_root_domain() . $this->xml_file );
		else
			return false;
			
		$link = $this->ping_url . $link;
		
		$options = array();
		$options['headers'] = array(
			'User-Agent' => 'Mapology v'. $mapo->version,
			'Referer' => get_bloginfo( 'url' )
		);
			
		wp_remote_request( $link, $options );
	}
	
	function create_xml()
	{
		$xml = fopen( ABSPATH . $this->xml_file, 'wb' );  
		fwrite( $xml, $this->output );  
		fclose( $xml );
  	}
	
	function create_gz()
	{
		if( function_exists( 'gzwrite' ) )
		{
			$gz = gzopen( ABSPATH . $this->gz_file, 'w9' );
			gzwrite( $gz, $this->output );
			gzclose( $gz );
		}
	}
	
	function build()
	{
		$this->output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$this->output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:geo="http://www.google.com/geo/schemas/sitemap/1.0">';
		$this->output .= $this->loop();
		$this->output .= '</urlset>';
	}
	
	function loop()
	{
		$str = '';
		if( mapo_has_routes( array( 'per_page' => 50000, 'public' => 3, 'user_id' => false, 'group_id' => false ) ) ) :
			while ( mapo_routes() ) : mapo_the_route();
				$str .= $this->url();
			endwhile;
		endif;
		
		return $str;
	}
    
    function url()
    {
        $str   = '<url>';
			$str  .= '<loc>'. mapo_get_routes_kml_link( false, true ) .'</loc>';
			$str  .= '<geo:geo>';
				$str  .= '<geo:format>kml</geo:format>';
			$str  .= '</geo:geo>';
        $str  .= '</url>';
		
		return $str;
    }
}
?>