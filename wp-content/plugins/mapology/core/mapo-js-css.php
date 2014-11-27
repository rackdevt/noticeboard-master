<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @sub-subpackage Mapology
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://scubavagabonds.com
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

class MAPO_JS_CSS
{
	function __construct()
	{
		if( ! is_admin() )
		{
			add_action( 'wp_print_scripts', array( $this, 'load_scripts' ) );
			add_action( 'wp_print_styles', array( $this, 'load_styles' ) );
		}
	}

	function load_styles()
	{
		global $bp;
		
		if( file_exists( STYLESHEETPATH .'/maps/mapo.css' ) )
			wp_enqueue_style( 'mapo-css', get_bloginfo('stylesheet_directory') .'/maps/mapo.css' );
		else
			wp_enqueue_style( 'mapo-css', MAPO_URLPATH .'css/mapo.css' );

		if( bp_is_current_component( $bp->groups->slug ) && bp_is_current_component( $bp->mapology->slug ) || bp_is_current_component( $bp->mapology->slug ) )
		{
			if( file_exists( STYLESHEETPATH .'/maps/colorbox.css' ) )
				wp_enqueue_style( 'colorbox-css', get_bloginfo('stylesheet_directory') .'/maps/colorbox.css' );
			else
				wp_enqueue_style( 'colorbox-css', MAPO_URLPATH .'css/colorbox.css' );
		}

		if( bp_is_current_component( $bp->mapology->slug ) )
		{
			if( bp_is_current_action( 'create' ) || bp_is_current_action( 'edit' ) && ! empty( $bp->action_variables[0] ) )
				wp_enqueue_style( 'bpe-datepicker-css', MAPO_URLPATH .'css/datepicker.css' );
		}
	}
	
	function load_scripts()
	{
		global $bp, $mapo;

		wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false'. $mapo->options->map_lang, array( 'jquery' ) );

		if( bp_is_current_component( $bp->groups->slug ) && bp_is_current_action( $bp->mapology->slug ) )
		{
			wp_enqueue_script( 'colorbox', MAPO_URLPATH .'js/jquery.colorbox-min.js', array( 'jquery' ), '1.3.9' );
			wp_enqueue_script( 'mapo-general', MAPO_URLPATH .'js/general.js', array( 'jquery' ), '1.0', true );
		}
		
		if( bp_is_current_component( $bp->groups->slug ) && ! $bp->displayed_user->id || bp_is_current_component( $bp->members->slug ) && ! $bp->displayed_user->id )
			wp_enqueue_script( 'markerclusterer', MAPO_URLPATH .'js/markerclusterer.js', array( 'bpe-maps-js' ), '1.0', true );

		if( bp_is_current_component( $bp->mapology->slug ) )
		{
			wp_enqueue_script( 'colorbox', MAPO_URLPATH .'js/jquery.colorbox-min.js', array( 'jquery' ), '1.3.9' );
			wp_enqueue_script( 'mapo-general', MAPO_URLPATH .'js/general.js', array( 'jquery' ), '1.0', true );

			if( bp_is_current_action( 'create' ) )
				wp_enqueue_script( 'mapo-create-js', MAPO_URLPATH .'js/create.js', array( 'bpe-maps-js', 'jquery' ), '1.0', true );

			if( bp_is_current_action( 'create' ) || bp_is_current_action( 'edit' ) && ! empty( $bp->action_variables[0] ) )
				wp_enqueue_script( 'bpe-datepicker-js', MAPO_URLPATH .'js/jquery.datepicker.min.js', array( 'jquery', 'jquery-ui-core' ), '1.0', true );
		}
	}
}
$mapo_js_css = new MAPO_JS_CSS();
?>