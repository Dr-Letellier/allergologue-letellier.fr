<?php
/**
 * Template Name: Front Page With Layer/Revolution Slider
 *
 * @package medicpress-pt
 */

get_header(); ?>

<div class="alternative-slider">
<?php

// Slider.
$medicpress_type = get_field( 'slider_type' );

if ( 'layer' === $medicpress_type && function_exists( 'layerslider' ) ) { // Layer slider.
	layerslider( (int) get_field( 'layerslider_id' ) );
}
elseif ( 'revolution' === $medicpress_type && function_exists( 'putRevSlider' ) ) { // Revolution slider.
	putRevSlider( get_field( 'revolution_slider_alias' ) );
}

?>
</div>

<div id="primary" class="content-area  container" role="main">
	<div class="article__content">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}
		?>
	</div>
</div>

<?php get_footer(); ?>
