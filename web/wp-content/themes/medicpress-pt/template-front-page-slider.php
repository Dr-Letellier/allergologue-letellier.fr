<?php
/**
 * Template Name: Front Page With Slider
 *
 * @package medicpress-pt
 */

get_header();

// Only include the slick carousel if we defined some slides.
if ( have_rows( 'slides' ) ) {
	get_template_part( 'template-parts/slick-carousel' );
}

?>

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
