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
?>
<div id="member-overview-map"></div>
<div id="member-overview-legend">
	<?php mapo_overview_legend() ?>
</div>
<?php mapo_overview_js( 'member-overview-map' ) ?>