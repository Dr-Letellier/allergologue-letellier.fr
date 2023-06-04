<?php
/**
 * The Header for MedicPress Theme
 *
 * @package medicpress-pt
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php endif; ?>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?>>
	<div class="boxed-container<?php echo ( is_single() && 'post' === get_post_type() ) ? '  h-entry' : ''; ?>">

	<?php get_template_part( 'template-parts/top-bar' ); ?>

	<header class="header__container">
		<div class="container">
			<div class="header">
				<!-- Logo -->
				<?php
					$medicpress_logo   = get_theme_mod( 'logo_img', false );
					$medicpress_logo2x = get_theme_mod( 'logo2x_img', false );
				?>
				<a class="header__logo<?php echo empty( $medicpress_logo ) ? '  header__logo--text' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( ! empty( $medicpress_logo ) ) : ?>
						<img src="<?php echo esc_url( $medicpress_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" srcset="<?php echo esc_attr( $medicpress_logo ); ?><?php echo empty( $medicpress_logo2x ) ? '' : ', ' . esc_url( $medicpress_logo2x ) . ' 2x'; ?>" class="img-fluid" <?php echo MedicPressHelpers::get_logo_dimensions(); ?> />
					<?php
					else :
						printf( '<%1$s class="h1  header__logo-text">%2$s</%1$s>', MedicPressHelpers::is_slider_template() ? 'h1' : 'p', esc_html( get_bloginfo( 'name' ) ) );
					endif;
					?>
				</a>
				<!-- Toggle button for Main Navigation on mobile -->
				<button class="btn  btn-primary  header__navbar-toggler  hidden-lg-up  js-sticky-mobile-option" type="button" data-toggle="collapse" data-target="#medicpress-main-navigation"><i class="fa  fa-bars  hamburger"></i> <span><?php esc_html_e( 'MENU' , 'medicpress-pt' ); ?></span></button>
				<!-- Main Navigation -->
				<nav class="header__main-navigation  collapse  navbar-toggleable-md  js-sticky-desktop-option" id="medicpress-main-navigation" aria-label="<?php esc_html_e( 'Main Menu', 'medicpress-pt' ); ?>">
					<?php
					if ( has_nav_menu( 'main-menu' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'main-menu',
							'container'      => false,
							'menu_class'     => 'main-navigation  js-main-nav  js-dropdown',
							'walker'         => new Aria_Walker_Nav_Menu(),
							'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
						) );
					}
					?>
					<!-- Featured Button -->
					<?php
						$featured_page_data = MedicPressHelpers::get_featured_page_data();

						if ( ! empty( $featured_page_data ) ) :
					?>
						<a class="btn  btn-secondary  btn-featured" href="<?php echo esc_url( $featured_page_data['url'] ); ?>" target="<?php echo esc_attr( $featured_page_data['target'] ); ?>"><?php echo esc_html( $featured_page_data['title'] ); ?></a>
					<?php endif; ?>
				</nav>
			</div>
		</div>
	</header>
