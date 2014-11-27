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
 
class MAPO_Admin_Readme extends MAPO_Admin_Core
{
	/**
	 * Constructor
	 * @since 1.1.2
	 */
    function __construct()
	{
		$this->head( __( 'Readme', 'mapo' ) );
		$this->content();
		$this->footer();
    }

	/**
	 * Content of the readme tab
	 * @since 1.1.2
	 */
	function content()
	{
		echo '<pre>'. file_get_contents( MAPO_ABSPATH .'readme.txt' ) .'</pre>';
	}
}
?>