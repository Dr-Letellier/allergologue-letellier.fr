<?php
/**
 * Template part for displaying blog sidebar
 *
 * @package medicpress-pt
 */

$medicpress_sidebar = get_field( 'sidebar', (int) get_option( 'page_for_posts' ) );

if ( ! $medicpress_sidebar ) {
	$medicpress_sidebar = 'left';
}

if ( 'none' !== $medicpress_sidebar && is_active_sidebar( 'blog-sidebar' ) ) : ?>
	<div class="col-xs-12  col-lg-3<?php echo 'left' === $medicpress_sidebar ? '  pull-lg-9' : ''; ?>">
		<div class="sidebar" role="complementary">
			<?php dynamic_sidebar( apply_filters( 'medicpress_blog_sidebar', 'blog-sidebar', get_the_ID() ) ); ?>
		</div>
	</div>
<?php endif; ?>
