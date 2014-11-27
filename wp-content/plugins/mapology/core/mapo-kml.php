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

class MAPO_KML_Export
{
	var $route;
	var $output;
	var $style;
	
	function __construct( $route = false, $style = array() )
	{
		if( ! $route )
			return false;
			
		if( empty( $style ) )
		{
			$style = array(
				'id' => 'mapoPoly',
				'linecolor' => '7f00ffff',
				'linewidth' => '4',
				'polycolor' => '7f00ff00'
			);
		}
		
		$this->route = $route;
		$this->style = $style;

		$this->process();
	}

   function process()
   {
		$this->output = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$this->output .= '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
		$this->output .= "\t".'<Document>'."\n";
		$this->output .= $this->head();
		$this->output .= $this->style();
		$this->output .= $this->points();
		$this->output .= $this->polyline();
		$this->output .= "\t".'</Document>'."\n";
		$this->output .= '</kml>';
   }
   
   function head()
   {
		$str  = "\t\t".'<atom:author>'."\n";           
		$str .= "\t\t\t".'<atom:name>'. bp_core_get_username( $this->route->user_id ) .'</atom:name>'."\n";        
		$str .= "\t\t".'</atom:author>'."\n";
		$str .= "\t\t".'<atom:link href="'. mapo_get_routes_link( $this->route, 'routes', true ) .'" />'."\n";

		if( ! empty( $this->route->name ) )
		{
			$str .= "\t\t".'<name>'."\n";
			$str .= "\t\t\t".'<![CDATA['."\n";
			$str .= "\t\t\t\t". mapo_get_routes_name( $this->route ) ."\n";
			$str .= "\t\t\t".']]>'."\n";
			$str .= "\t\t".'</name>'."\n";
		}

		if( ! empty( $this->route->description ) )
		{
			$str .= "\t\t".'<description>'."\n";
			$str .= "\t\t\t".'<![CDATA['."\n";
			$str .= "\t\t\t\t". mapo_get_routes_description( $this->route );
			$str .= "\t\t\t".']]>'."\n";
			$str .= "\t\t".'</description>'."\n";
		}
	  
	  return $str;
   }
   
   function style()
   {
		$str  = "\t\t".'<Style id="'. $this->style['id'] .'">'."\n";
		$str .= "\t\t\t".'<LineStyle>'."\n";
		$str .= "\t\t\t\t".'<color>'. $this->style['linecolor'] .'</color>'."\n";
		$str .= "\t\t\t\t".'<width>'. $this->style['linewidth'] .'</width>'."\n";
		$str .= "\t\t\t".'</LineStyle>'."\n";
		$str .= "\t\t\t".'<PolyStyle>'."\n";
		$str .= "\t\t\t\t".'<color>'. $this->style['polycolor'] .'</color>'."\n";
		$str .= "\t\t\t".'</PolyStyle>'."\n";
		$str .= "\t\t".'</Style>'."\n";
		
		return $str;
   }
   
   function points()
   {
	   $str = "\t\t".'<Folder>'."\n";
	   foreach( (array)$this->route->waypoints as $waypoint )
	   {
			$str  .= "\t\t\t".'<Placemark>'."\n";
			if( ! empty( $waypoint->title ) )
			{
				$str .= "\t\t\t\t".'<name>'."\n";
				$str .= "\t\t\t\t\t".'<![CDATA['."\n";
				$str .= "\t\t\t\t\t\t". $waypoint->title ."\n";
				$str .= "\t\t\t\t\t".']]>'."\n";
				$str .= "\t\t\t\t".'</name>'."\n";
			}
			if( ! empty( $waypoint->description ) )
			{
				$str .= "\t\t\t\t".'<description>'."\n";
				$str .= "\t\t\t\t\t".'<![CDATA['."\n";
				$str .= "\t\t\t\t\t\t". $waypoint->description ."\n";
				$str .= "\t\t\t\t\t".']]>'."\n";
				$str .= "\t\t\t\t".'</description>'."\n";
			}
			$str .= "\t\t\t\t".'<Point>'."\n";
			$str .= "\t\t\t\t\t".'<coordinates>'. $waypoint->lng .','. $waypoint->lat .'</coordinates>'."\n";
			$str .= "\t\t\t\t".'</Point>'."\n";
			$str .= "\t\t\t".'</Placemark>'."\n";
	   }
	   $str .= "\t\t".'</Folder>'."\n";
	   
	   return $str;
   }
   
   function polyline()
   {
		$str = "\t\t".'<Folder>'."\n";
		$str .= "\t\t\t".'<Placemark>'."\n";
		if( ! empty( $this->route->name ) )
		{
			$str .= "\t\t\t\t".'<name>'."\n";
			$str .= "\t\t\t\t\t".'<![CDATA['."\n";
			$str .= "\t\t\t\t\t\t". mapo_get_routes_name( $this->route ) ."\n";
			$str .= "\t\t\t\t\t".']]>'."\n";
			$str .= "\t\t\t\t".'</name>'."\n";
		}
		if( ! empty( $this->route->description ) )
		{
			$str .= "\t\t\t\t".'<description>'."\n";
			$str .= "\t\t\t\t\t".'<![CDATA['."\n";
			$str .= "\t\t\t\t\t\t". mapo_get_routes_description( $this->route );
			$str .= "\t\t\t\t\t".']]>'."\n";
			$str .= "\t\t\t\t".'</description>'."\n";
		}
		$str .= "\t\t\t\t".'<styleUrl>#'. $this->style['id'] .'</styleUrl>'."\n";
		$str .= "\t\t\t\t".'<LineString>'."\n";
		$str .= "\t\t\t\t\t".'<tessellate>1</tessellate>'."\n";
		$str .= "\t\t\t\t\t".'<coordinates>'."\n";
		foreach( (array)$this->route->waypoints as $waypoint )
			$str .= "\t\t\t\t\t\t". $waypoint->lng .','. $waypoint->lat ."\n";
		$str .= "\t\t\t\t\t".'</coordinates>'."\n";
		$str .= "\t\t\t\t".'</LineString>'."\n";
		$str .= "\t\t\t".'</Placemark>'."\n";
		$str .= "\t\t".'</Folder>'."\n";
		
		return $str;
   }
   
   function __destruct()
   {
		echo $this->output;  
   }
}
?>