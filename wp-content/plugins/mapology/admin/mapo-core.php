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
 
class MAPO_Admin_Core
{
	/**
	 * Head
	 * @since 1.1.2
	 */
	function head( $title = false )
	{
		?>
        <div id="mapo-page" class="wrap">
			<h2><?php echo $title ?></h2>
            <div id="mapo-content">
        <?php
	}

	/**
	 * Footer
	 * @since 1.1.2
	 */
	function footer()
	{
			?>
        	</div>
        </div>
        <?php
	}
}
?>