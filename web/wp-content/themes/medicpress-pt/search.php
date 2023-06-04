<?php
/**
 * Search results page
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

						<?php get_template_part( 'template-parts/content', 'search' ); ?>

					<?php endwhile; ?>

					<?php
						the_posts_pagination( array(
							'prev_text' => '<i class="fa  fa-arrow-left"></i>',
							'next_text' => '<i class="fa  fa-arrow-right"></i>',
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
