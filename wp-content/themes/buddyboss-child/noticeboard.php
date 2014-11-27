<?php
/**
 * Template Name: Main noticeboard pinboard
 *
 * Description: Use this page template for a page with a right sidebar.
 *
 * @package WordPress
 * @subpackage BuddyBoss
 * @since BuddyBoss 3.0
 */

get_header();

//list terms in a given taxonomy
$taxonomy = 'offered_categories';
$tax_terms = get_terms($taxonomy);
?>
	<section id="primary" class="site-content gnb-noticeboard-header">
		<div id="content" role="main">
		<header class="archive-header">
				<h1 class="archive-title"><?php the_title(); ?></h1>
                <h2>Offered & Wanted</h2>
					</header><!-- .archive-header -->		
 <div class="js-isotope isotope" id="container"  data-isotope-options='{ "layoutMode": "packery", "itemSelector": ".gnb-item"}'>


<?php

$i = 1;

foreach ($tax_terms as $tax_term) {



$offered_args = array(
    'post_type' => 'classifieds',
   	'post_status' => 'published',
   	'numberposts'       => -1,
    'tax_query' => array(
        array(
            'taxonomy' => 'offered_categories',
            'field' => 'slug',
            'terms' => $tax_term->slug,
            'orderby' => $tax_term->name,
            'order' => 'ASC'
        )
    )
);

$wanted_slug = str_replace("offered", "wanted", $tax_term->slug);

$wanted_args = array(
    'post_type' => 'classifieds',
   	'post_status' => 'published',
   	'numberposts'       => -1,
    'tax_query' => array(
        array(
            'taxonomy' => 'wanted_categories',
            'field' => 'slug',
            'terms' => $wanted_slug,
        )
    )
);

$exclude_terms = array("at-home-offered", "at-home-wanted", "at-work", "at-work-wanted", "reaching-out-offered", "reaching-out-wanted");

if (!in_array($tax_term->slug, $exclude_terms)) {

?>
<div class="gnb-item <?php if ($i % 2 == 0) { echo "gnb-item-2 offered";} else {echo "gnb-item-3 wanted";} ?>" >
        <div class="author-box">
<?php echo $tax_term->name; ?>
          </div>
 

<div class="post-content">

<?php

$img_args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
    'tax_query' => array(
        array(
            'taxonomy' => 'offered_categories',
            'field' => 'slug',
            'terms' => $tax_term->slug,
        )
    )
);
$img_query = new WP_Query($img_args);
if($img_query->have_posts()) :
    while ($img_query->have_posts()) : $img_query->the_post();
        //...

echo '<img src="/wp-content/uploads/' . get_the_title() . '.jpg" class="gnb-image-responsive"  alt="' . $tax_term->slug . '" />';


    endwhile;
endif;

?>

<div class="entry">        
<h2>
<?php
echo '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '" title="' . sprintf( __( "View all offered posts in %s" ), $tax_term->name ) . '" ' . '>Offered';
echo "(" . $num = count( get_posts( $offered_args ) ) . ")";
echo '</a>';
?>
</h2>
<h2>

<a href="/wanted_categories/<?php echo $wanted_slug; ?>" >Wanted 

<?php echo "(" . $num = count( get_posts( $wanted_args ) ) . ")"; ?>

</a>

</h2>
</div>
</div>
</div>
<?php


$i ++;
}
}

unset($tax_term);
?>
</div>
</div><!-- #content -->
</section><!-- #primary -->


<?php get_sidebar(); ?>

<?php get_footer(); ?>

