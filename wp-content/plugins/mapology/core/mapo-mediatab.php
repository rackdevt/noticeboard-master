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

/**
 * Add the Mapology tab
 * @since 1.0
 */
function mapo_wp_upload_tabs( $tabs )
{
	$newtab = array( 'mapo' => __( 'Mapology', 'mapo' ) );
 
    return array_merge( $tabs, $newtab );
}
add_filter( 'media_upload_tabs', 'mapo_wp_upload_tabs');

/**
 * Process the input
 * @since 1.0
 */
function media_upload_mapo()
{
	$errors = false;
	
	if ( isset( $_POST['save'] ) )
	{
		if( $_POST['type'] == 'post_map' )
		{
			$p = $_POST['pm'];
			
			$width = ( $p['width'] == '100%' || empty( $p['width'] ) ) ? '' : '&nbsp;width="'. $p['width'] .'"';
			$height = ( $p['height'] == 300 || empty( $p['height'] ) ) ? '' : '&nbsp;height="'. $p['height'] .'"';
			$type =  ( $p['maptype'] == 'HYBRID' || empty( $p['maptype'] ) ) ? '' : '&nbsp;type="'. $p['maptype'] .'"';
			$zoom = ( $p['zoom'] == 2 || empty( $p['zoom'] ) ) ? '' : '&nbsp;zoom="'. $p['zoom'] .'"';
			$float = ( $p['float'] == 'center' || empty( $p['float'] ) ) ? '' : '&nbsp;float="'. $p['float'] .'"';
			$title = ( $p['title'] == __( 'Post Map', 'mapo' ) || empty( $p['title'] ) ) ? '' : '&nbsp;title="'. $p['title'] .'"';
			$nav = ( $p['nav'] == 'true' || empty( $p['nav'] ) ) ? '' : '&nbsp;nav="'. $p['nav'] .'"';
			$typenav = ( $p['typenav'] == 'false' || empty( $p['typenav'] ) ) ? '' : '&nbsp;typenav="'. $p['typenav'] .'"';
			$scale = ( $p['scale'] == 'false' || empty( $p['scale'] ) ) ? '' : '&nbsp;scale="'. $p['scale'] .'"';
			$desc = ( empty( $p['description'] ) ) ? '' : $p['description'] .'[/mapology]';

			$html = '[mapology'. $width . $height . $type . $zoom . $float . $title . $nav . $typenav . $scale .']'. $desc;
		}
		elseif( $_POST['type'] == 'route_map' )
		{
			$r = $_POST['rm'];
			
			if( empty( $r['id'] ) )
				return false;

			$id = '&nbsp;id="'. $r['id'] .'"';
			$width = ( empty( $r['width'] ) ) ? '' : '&nbsp;width="'. $r['width'] .'"';
			$height = ( empty( $r['height'] ) ) ? '' : '&nbsp;height="'. $r['height'] .'"';
			$type =  ( empty( $r['maptype'] ) ) ? '' : '&nbsp;type="'. $r['maptype'] .'"';
			$zoom = ( empty( $r['zoom'] ) ) ? '' : '&nbsp;zoom="'. $r['zoom'] .'"';
			$float = ( empty( $r['float'] ) ) ? '' : '&nbsp;float="'. $r['float'] .'"';
			$title = ( empty( $r['title'] ) ) ? '' : '&nbsp;title="'. $r['title'] .'"';
			$nav = ( empty( $r['nav'] ) ) ? '' : '&nbsp;nav="'. $r['nav'] .'"';
			$typenav = ( empty( $r['typenav'] ) ) ? '' : '&nbsp;typenav="'. $r['typenav'] .'"';
			$scale = ( empty( $r['scale'] ) ) ? '' : '&nbsp;scale="'. $r['scale'] .'"';
			$desc = ( empty( $r['desc'] ) ) ? '' : '&nbsp;desc="'. $r['desc'] .'"';
			$date = ( empty( $r['date'] ) ) ? '' : '&nbsp;date="'. $r['date'] .'"';

			$html = '[routes '. $id . $width . $height . $type . $zoom . $float . $nav . $typenav . $scale . $title . $desc . $date .']';
		}
		
		return media_send_to_editor( $html );
	}
		
	return wp_iframe( 'media_upload_mapo_form', $errors );
}
add_action( 'media_upload_mapo', 'media_upload_mapo' );

/**
 * Form content
 * @since 1.0
 */
function media_upload_mapo_form( $errors )
{
	global $mapo;
	
	media_upload_header();
	
	$_GET['paged'] = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
	if( $_GET['paged'] < 1 ) $_GET['paged'] = 1;
	
	$start = ( $_GET['paged'] - 1 ) * 10;
	if( $start < 1 ) $start = 0;

	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'total' => ceil( $routes['total'] / 10 ),
		'current' => $_GET['paged']
	));

	?>
    <style type="text/css">
	form.mapology label{display:block}
	form.mapology select,form.mapology input,form.mapology textarea{margin-bottom:10px}
	form.mapology input[type="radio"]{margin-bottom:4px !important}
	form.mapology textarea{width:90%}
	form.mapology .clear{clear:both}
	form.mapology .mapo-left{float:left;width:45%}
	form.mapology img.avatar{margin:5px 0 5px -5px;vertical-align:middle;}
	form.mapology hr{border:none;border-bottom:1px solid #ccc}
	</style>
    
	<form id="file-form" class="media-upload-form type-form validate mapology" action="" method="post">
    	
        <label for="type"><?php _e( 'Shortcode Type', 'mapo' ) ?></label>
        <select id="type" name="type">
        	<option value="0">----</option>
        	<option value="post_map"><?php _e( 'Post Map', 'mapo' ) ?></option>
            <?php if( $mapo->options->enable_routes === true ) : ?>
        	<option value="route_map"><?php _e( 'Route Maps', 'mapo' ) ?></option>
            <?php endif; ?>
        </select>
        
        <hr />

        <div id="post_map_wrap">
        	<h3 class="media-title"><?php _e( 'Post Map Shortcode', 'mapo' ) ?></h3>
        
        	<label for="pm_title"><?php _e( 'Title', 'mapo' ) ?></label>
			<input type="text" id="pm_title" name="pm[title]" value="" />

        	<label for="pm_description"><?php _e( 'InfoWindow Description', 'mapo' ) ?></label>
			<textarea id="pm_description" name="pm[description]"></textarea>

			<div class="mapo-left">
                <label for="pm_width"><?php _e( 'Width', 'mapo' ) ?></label>
                <input type="text" id="pm_width" name="pm[width]" value="" />
    		</div>
			<div class="mapo-left">
                <label for="pm_height"><?php _e( 'Height', 'mapo' ) ?></label>
                <input type="text" id="pm_height" name="pm[height]" value="" />
			</div>
            <div class="clear"></div>

			<div class="mapo-left">
                <label for="pm_maptype"><?php _e( 'Map Type', 'mapo' ) ?></label>
                <select id="pm_maptype" name="pm[maptype]">
                    <option value="">----</option>
                    <option value="HYBRID">HYBRID</option>
                    <option value="ROADMAP">ROADMAP</option>
                    <option value="TERRAIN">TERRAIN</option>
                    <option value="SATELLITE">SATELLITE</option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="pm_float"><?php _e( 'Float', 'mapo' ) ?></label>
                <select id="pm_float" name="pm[float]">
                    <option value="">----</option>
                    <option value="left"><?php _e( 'Left', 'mapo' ) ?></option>
                    <option value="right"><?php _e( 'Right', 'mapo' ) ?></option>
                </select>
			</div>
            <div class="clear"></div>
            
			<div class="mapo-left">
                <label for="pm_nav"><?php _e( 'Enable Navigation Control', 'mapo' ) ?></label>
                <select id="pm_nav" name="pm[nav]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="pm_typenav"><?php _e( 'Enable Map Type Control', 'mapo' ) ?></label>
                <select id="pm_typenav" name="pm[typenav]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
            <div class="clear"></div>

			<div class="mapo-left">
                <label for="pm_scale"><?php _e( 'Enable Scale Control', 'mapo' ) ?></label>
                <select id="pm_scale" name="pm[scale]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="pm_zoom"><?php _e( 'Zoom', 'mapo' ) ?></label>
                <input type="text" id="pm_zoom" name="pm[zoom]" value="" />
    		</div>
            <div class="clear"></div>
        </div>

        <?php if( $mapo->options->enable_routes === true ) : ?>
        <div id="route_map_wrap">
        	<h3 class="media-title"><?php _e( 'Route Shortcode Options', 'mapo' ) ?></h3>

			<div class="mapo-left">
                <label for="rm_title"><?php _e( 'Enable Route Title', 'mapo' ) ?></label>
                <select id="rm_title" name="rm[title]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
			</div>
			<div class="mapo-left">
                <label for="rm_desc"><?php _e( 'Enable Route Description', 'mapo' ) ?></label>
                <select id="rm_desc" name="rm[desc]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
			</div>
            <div class="clear"></div>

			<div class="mapo-left">
                <label for="rm_date"><?php _e( 'Enable Route Date', 'mapo' ) ?></label>
                <select id="rm_date" name="rm[date]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
			</div>
            <div class="clear"></div>
            
			<div class="mapo-left">
                <label for="rm_width"><?php _e( 'Width', 'mapo' ) ?></label>
                <input type="text" id="rm_width" name="rm[width]" value="" />
    		</div>
			<div class="mapo-left">
                <label for="rm_height"><?php _e( 'Height', 'mapo' ) ?></label>
                <input type="text" id="rm_height" name="rm[height]" value="" />
			</div>
            <div class="clear"></div>

			<div class="mapo-left">
                <label for="rm_maptype"><?php _e( 'Map Type', 'mapo' ) ?></label>
                <select id="rm_maptype" name="rm[maptype]">
                    <option value="">----</option>
                    <option value="HYBRID">HYBRID</option>
                    <option value="ROADMAP">ROADMAP</option>
                    <option value="TERRAIN">TERRAIN</option>
                    <option value="SATELLITE">SATELLITE</option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="rm_float"><?php _e( 'Float', 'mapo' ) ?></label>
                <select id="rm_float" name="rm[float]">
                    <option value="">----</option>
                    <option value="left"><?php _e( 'Left', 'mapo' ) ?></option>
                    <option value="right"><?php _e( 'Right', 'mapo' ) ?></option>
                </select>
			</div>
            <div class="clear"></div>
            
			<div class="mapo-left">
                <label for="rm_nav"><?php _e( 'Enable Navigation Control', 'mapo' ) ?></label>
                <select id="rm_nav" name="rm[nav]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="rm_typenav"><?php _e( 'Enable Map Type Control', 'mapo' ) ?></label>
                <select id="rm_typenav" name="rm[typenav]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
            <div class="clear"></div>

			<div class="mapo-left">
                <label for="rm_scale"><?php _e( 'Enable Scale Control', 'mapo' ) ?></label>
                <select id="rm_scale" name="rm[scale]">
                    <option value="">----</option>
                    <option value="true"><?php _e( 'Yes', 'mapo' ) ?></option>
                    <option value="false"><?php _e( 'No', 'mapo' ) ?></option>
                </select>
    		</div>
			<div class="mapo-left">
                <label for="rm_zoom"><?php _e( 'Zoom', 'mapo' ) ?></label>
                <input type="text" id="rm_zoom" name="rm[zoom]" value="" />
    		</div>
            <div class="clear"></div>
            
            <hr />
            
        	<h3 class="media-title"><?php _e( 'Available Routes', 'mapo' ) ?></h3>
			
			<?php if( $page_links ) : ?>
            <div class="tablenav"><div class="tablenav-pages"><?php echo $page_links ?></div></div>
            <?php endif; ?>
            
            <div id="media-items">
            <?php
       		$data = mapo_get_routes( array( 'page' => false, 'per_page' => false ) );
			if( count( $data['routes'] ) > 0 )
			{
				foreach( $data['routes'] as $route ):
				?>
                <div id="media-item-<?php echo $route->id ?>" class="media-item">
                    <div class="filename"><?php mapo_routes_avatar( $route, 70, 70 ) ?> <input type="radio" id="rm_id<?php echo $route->id ?>"  name="rm[id]" value="<?php echo $route->id ?>" /> <?php printf( __( '<a href="%s">%s</a> by %s', 'mapo' ), mapo_get_routes_link( $route, $page = 'routes', true ), $route->name, bp_core_get_username( $route->user_id ) ) ?> </div>
				</div>
				<?php
                endforeach;
			}
			?>
            </div>
            <?php endif; ?>

			<?php if( $page_links ) : ?>
            <div class="tablenav"><div class="tablenav-pages"><?php echo $page_links ?></div></div>
            <?php endif; ?>
        </div>
    
        <p class="ml-submit">
            <input type="submit" class="button savebutton" name="save" value="<?php echo esc_attr( __( 'Insert into editor', 'mapo' ) ); ?>" />
        </p>
	</form>

    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#post_map_wrap,#route_map_wrap,.ml-submit').hide();
		jQuery.viewMap = { '0' : jQuery([]), 'post_map' : jQuery('#post_map_wrap,.ml-submit'),	'route_map' : jQuery('#route_map_wrap,.ml-submit') };
		jQuery('#type').change(function() {
			jQuery.each(jQuery.viewMap, function() { this.hide(); });
			jQuery.viewMap[jQuery(this).val()].show();
		});
	});
	</script>
	<?php
}
?>