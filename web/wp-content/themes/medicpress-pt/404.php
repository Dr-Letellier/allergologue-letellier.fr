<?php
/**
 * 404 page
 *
 * @package medicpress-pt
 */

get_header();

get_template_part( 'template-parts/page-header' );

?>

<div class="content-area  error-404">
	<img class="error-404__image" src="<?php echo esc_attr( get_template_directory_uri() ) . '/assets/images/404.png'; ?>" alt="<?php esc_html_e( '404 Picture' , 'medicpress-pt' ); ?>">
	<div class="container">
		<p class="h2  error-404__subtitle"><?php esc_html_e( 'You landed on the wrong side of the page' , 'medicpress-pt' ); ?></p>
		<p class="error-404__text">
		<?php
			printf(
				/* translators: the first %s for line break, the second and third %s for link to home page wrap */
				esc_html__( 'Page you are looking for is not here. %1$s Go %2$sHome%3$s or try to search:' , 'medicpress-pt' ),
				'<br>',
				'<b><a href="' . esc_url( home_url( '/' ) ) . '">',
				'</a></b>'
			);
		?>
		</p>
		<div class="widget_search">
			<?php get_search_form(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
