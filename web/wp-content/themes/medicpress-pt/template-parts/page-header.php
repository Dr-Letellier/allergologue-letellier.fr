<?php
/**
 * The page title part of the header
 *
 * @package medicpress-pt
 */

$medicpress_style_attr = '';

// Regular page id.
$medicpress_bg_id        = get_the_ID();
$medicpress_blog_id      = absint( get_option( 'page_for_posts' ) );
$medicpress_shop_id      = absint( get_option( 'woocommerce_shop_page_id', 0 ) );

// If blog or single post use the ID of the blog.
if ( is_home() || is_singular( 'post' ) ) {
	$medicpress_bg_id = $medicpress_blog_id;
}

// If woocommerce page, use the shop page id.
if ( MedicPressHelpers::is_woocommerce_active() && is_woocommerce() ) {
	$medicpress_bg_id = $medicpress_shop_id;
}

$show_title_area = get_field( 'show_title_area', $medicpress_bg_id );
if ( ! $show_title_area ) {
	$show_title_area = 'yes';
}

$show_breadcrumbs = get_field( 'show_breadcrumbs', $medicpress_bg_id );
if ( ! $show_breadcrumbs ) {
	$show_breadcrumbs = 'yes';
}

// Show/hide page title area (ACF control - single page && customizer control - all pages).
if ( 'yes' === $show_title_area && 'yes' === get_theme_mod( 'show_page_title_area', 'yes' ) ) :

	$medicpress_style_attr = MedicPressHelpers::page_header_background_style( $medicpress_bg_id );

	?>

	<div class="page-header"<?php echo empty( $medicpress_style_attr ) ? '' : ' style="' . esc_attr( $medicpress_style_attr ) . ';"'; ?>>
		<div class="container">
			<?php
			$medicpress_main_tag = 'h1';

			if ( is_home() || ( is_single() && 'post' === get_post_type() ) ) {
				$medicpress_title    = 0 === $medicpress_blog_id ? esc_html__( 'Blog', 'medicpress-pt' ) : get_the_title( $medicpress_blog_id );

				if ( is_single() ) {
					$medicpress_main_tag = 'h2';
				}
			}
			elseif ( MedicPressHelpers::is_woocommerce_active() && is_woocommerce() ) {
				ob_start();
				woocommerce_page_title();
				$medicpress_title    = ob_get_clean();

				if ( is_product() ) {
					$medicpress_main_tag = 'h2';
				}
			}
			elseif ( is_category() || is_tag() || is_author() || is_post_type_archive() || is_tax() || is_day() || is_month() || is_year() ) {
				$medicpress_title = get_the_archive_title();
			}
			elseif ( is_search() ) {
				$medicpress_title = esc_html__( 'Search Results For' , 'medicpress-pt' ) . ' &quot;' . get_search_query() . '&quot;';
			}
			elseif ( is_404() ) {
				$medicpress_title = esc_html__( 'Error 404' , 'medicpress-pt' );
			}
			else {
				$medicpress_title    = get_the_title();
			}

			?>

			<?php printf( '<%1$s class="page-header__title">%2$s</%1$s>', tag_escape( $medicpress_main_tag ), wp_kses_post( $medicpress_title ) ); ?>

				<?php
					if ( 'yes' === $show_breadcrumbs ) {
						get_template_part( 'template-parts/breadcrumbs' );
					}
				?>
		</div>
	</div>

<?php endif; ?>
