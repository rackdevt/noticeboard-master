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

global $bp, $post;

wp_enqueue_script( 'bpe-maps-js', 'http://maps.google.com/maps/api/js?sensor=false' );

$categories = get_categories('taxonomy=continents&hide_empty=0');
$terms = get_the_terms( $post->ID, 'continents' );
$terms = array_values( (array)$terms );

get_header() ?>

	<div id="content">
		<div class="padder">

			<h3><?php _e( 'Locations', 'mapo' ); ?></h3>

			<div id="listings-dir-search" class="dir-search">
				<?php mapo_directory_locations_search_form() ?>
			</div>

			<div class="item-list-tabs no-ajax">
				<ul>
                    <li class="feed"><a href="<?php echo bp_get_root_domain() .'/feed/?post_type=location' ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>
                	<li><a href="<?php echo bp_get_root_domain() .'/locations/' ?>"><?php _e( 'All Locations', 'mapo' ) ?></a></li>
                	<?php foreach( (array)$categories as $category ) : ?>
                	<li<?php if( $category->slug == $terms[0]->slug ) echo ' class="selected"'; ?>><a href="<?php echo bp_get_root_domain() .'/continents/'. $category->slug .'/' ?>"><?php echo $category->name ?></a></li>
                    <?php endforeach; ?>
				</ul>
            </div>

			<div id="location-dir-list" class="location dir-list">
                <ul id="location-list" class="item-list">
                    <li id="location-<?php echo $post->ID ?>">
        
                        <div class="item">
							<div class="item-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'mapo' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>

							<div class="item-meta"><em><?php printf( __( 'Posted in %s', 'mapo' ), get_the_term_list( $post->ID, 'continents', '', ', ', '' ) ) ?></em></div>
 
 							<?php echo do_shortcode( '[mapology]' ); ?>
 
                            <div class="entry">
                            	<?php
								$content = apply_filters( 'the_content', $post->post_content );
								$content = str_replace( ']]>', ']]&gt;', $content );
								echo $content;
								?>
                            </div>
       
							<div class="item-tags">
                            	<span class="mapo-countries"><?php echo get_the_term_list( $post->ID, 'countries', __( 'Countries: ', 'mapo' ), ', ', '' ) ?></span>
                             	<span class="mapo-areas"><?php echo get_the_term_list( $post->ID, 'areas', __( 'Areas: ', 'mapo' ), ', ', '' ) ?></span>
                           </div>
                        </div>
                    </li>
                </ul>
			</div>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>