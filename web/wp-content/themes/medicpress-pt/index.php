<?php
/**
 * The main template file.
 *
 * Main blog page
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package medicpress-pt
 */

get_header();

$medicpress_sidebar = get_field( 'sidebar', (int) get_option( 'page_for_posts' ) );

if ( ! $medicpress_sidebar ) {
	$medicpress_sidebar = 'left';
}

get_template_part( 'template-parts/page-header' );

?>

	<div id="primary" class="content-area  container">
		<div class="row">
			<main id="main" class="site-main  col-xs-12<?php echo 'left' === $medicpress_sidebar ? '  site-main--left  col-lg-9  push-lg-3' : ''; ?><?php echo 'right' === $medicpress_sidebar ? '  site-main--right  col-lg-9' : ''; ?>">
				<?php if ( have_posts() ) : ?>

					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<?php

							/*
							 * Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'template-parts/content', get_post_format() );
						?>
					<?php endwhile; ?>

					<?php
						the_posts_pagination( array(
							'prev_text' => '<i class="fa fa-arrow-left"></i>',
							'next_text' => '<i class="fa fa-arrow-right"></i>',
						) );
					?>

				<?php else : ?>

					<?php get_template_part( 'template-parts/content', 'none' ); ?>

				<?php endif; ?>
			</main>

			<?php get_template_part( 'template-parts/sidebar', 'blog' ); ?>

		</div>
	</div>

<?php get_footer(); ?>
