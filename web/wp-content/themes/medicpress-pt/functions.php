<?php
/**
 * MedicPress functions and definitions
 *
 * @author ProteusThemes <info@proteusthemes.com>
 * @package medicpress-pt
 */

// Display informative message if PHP version is less than 5.4.
if ( version_compare( phpversion(), '5.4', '<' ) ) {
	printf( esc_html_x( 'This theme requires %2$sPHP 5.4+%3$s to run. Please contact your hosting company and ask them to update the PHP version of your site to at least PHP 5.4.%4$s Your current version of PHP: %2$s%1$s%3$s', '%1$s - version ie. 5.4.0. %2$s, %3$s and %4$s  - html tags, must be included around the same words as original', 'medicpress-pt' ), esc_html( phpversion() ), '<strong>', '</strong>', '<br>' );
}


// Composer autoloader.
require_once trailingslashit( get_template_directory() ) . 'vendor/autoload.php';


/**
 * Define the version variable to assign it to all the assets (css and js)
 */
define( 'MEDICPRESS_WP_VERSION', wp_get_theme()->get( 'Version' ) );


/**
 * Define the development constant
 */
if ( ! defined( 'MEDICPRESS_DEVELOPMENT' ) ) {
	define( 'MEDICPRESS_DEVELOPMENT', false );
}


/**
 * Helper functions used in the theme
 */
require_once get_template_directory() . '/inc/helpers.php';


/**
 * Advanced Custom Fields calls to require the plugin within the theme
 */
MedicPressHelpers::load_file( '/inc/acf.php' );

/**
 * Theme support and thumbnail sizes
 */
if ( ! function_exists( 'medicpress_theme_setup' ) ) {
	function medicpress_theme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on MedicPress, use a find and replace
		 * to change 'medicpress-pt' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'medicpress-pt', get_template_directory() . '/languages' );

		/**
		 * Loads separate textdomain for the proteuswidgets which are included with composer.
		 */
		load_theme_textdomain( 'proteuswidgets', get_template_directory() . '/languages/proteuswidgets' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// WooCommerce basic support.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		/**
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'medicpress-jumbotron-slider-l', 1920, 680, true );
		add_image_size( 'medicpress-jumbotron-slider-m', 960, 340, true );
		add_image_size( 'medicpress-jumbotron-slider-s', 480, 170, true );

		// Menus.
		add_theme_support( 'menus' );
		register_nav_menu( 'main-menu', esc_html__( 'Main Menu', 'medicpress-pt' ) );

		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add excerpt support for pages.
		add_post_type_support( 'page', 'excerpt' );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'medicpress_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );
	}
	add_action( 'after_setup_theme', 'medicpress_theme_setup' );
}


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @see https://codex.wordpress.org/Content_Width
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1140; /* pixels */
}


/**
 * Enqueue CSS stylesheets
 */
if ( ! function_exists( 'medicpress_enqueue_styles' ) ) {
	function medicpress_enqueue_styles() {
		$min = '';
		$stylesheet_uri = get_stylesheet_uri();

		if ( 'yes' === get_theme_mod( 'use_minified_css', 'no' ) ) {
			$min = '.min';
			$stylesheet_uri = get_stylesheet_directory_uri() . '/style.min.css';
		}

		wp_enqueue_style( 'medicpress-main', $stylesheet_uri, array(), MEDICPRESS_WP_VERSION );

		// Custom WooCommerce CSS (enqueue it only if the WooCommerce plugin is active).
		if ( MedicPressHelpers::is_woocommerce_active() ) {
			wp_enqueue_style( 'medicpress-woocommerce', get_template_directory_uri() . "/woocommerce{$min}.css" , array( 'medicpress-main' ) , MEDICPRESS_WP_VERSION );
		}
	}
	add_action( 'wp_enqueue_scripts', 'medicpress_enqueue_styles' );
}


/**
 * Enqueue Google Web Fonts.
 */
if ( ! function_exists( 'medicpress_enqueue_google_web_fonts' ) ) {
	function medicpress_enqueue_google_web_fonts() {
		wp_enqueue_style( 'medicpress-google-fonts', MedicPressHelpers::google_web_fonts_url(), array(), null );
	}
	add_action( 'wp_enqueue_scripts', 'medicpress_enqueue_google_web_fonts' );
}


/**
 * Enqueue JS scripts
 */
if ( ! function_exists( 'medicpress_enqueue_scripts' ) ) {
	function medicpress_enqueue_scripts() {
		// Modernizr for the frontend feature detection.
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.custom.20160801.js', array(), null );

		// Requirejs.
		wp_register_script( 'requirejs', get_template_directory_uri() . '/bower_components/requirejs/require.js', array(), null, true );

		// Array for main.js dependencies.
		$main_deps = array( 'jquery', 'underscore' );

		// Main JS file, conditionally.
		if ( true === MEDICPRESS_DEVELOPMENT ) {
			$main_deps[] = 'requirejs';
			wp_enqueue_script( 'medicpress-main', get_template_directory_uri() . '/assets/js/main.js', $main_deps, MEDICPRESS_WP_VERSION, true );
		}
		else {
			wp_enqueue_script( 'medicpress-main', get_template_directory_uri() . '/assets/js/main.min.js', $main_deps, MEDICPRESS_WP_VERSION, true );
		}

		// Pass data to the main script.
		wp_localize_script( 'medicpress-main', 'MedicPressVars', array(
			'pathToTheme'   => get_template_directory_uri(),
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'    => wp_create_nonce( 'pt-medicpress-ajax-verification' ),
		) );

		// For nested comments.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'medicpress_enqueue_scripts' );
}


/**
 * Register admin JS scripts
 */
if ( ! function_exists( 'medicpress_admin_enqueue_scripts' ) ) {
	function medicpress_admin_enqueue_scripts() {
		// Mustache for ProteusWidgets.
		wp_register_script( 'mustache.js', get_template_directory_uri() . '/bower_components/mustache/mustache.min.js' );

		// Add the color picker css file.
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue admin utils js.
		wp_enqueue_script( 'medicpress-admin-utils', get_template_directory_uri() . '/assets/admin/js/admin.js', array( 'jquery', 'underscore', 'backbone', 'mustache.js', 'wp-color-picker' ), MEDICPRESS_WP_VERSION );

		// Register fa CSS.
		wp_register_style( 'font-awesome', get_template_directory_uri() . '/bower_components/font-awesome/css/font-awesome.min.css', array(), '4.7.0' );

		// Enqueue CSS for admin area.
		wp_enqueue_style( 'medicpress-admin-css', get_template_directory_uri() . '/assets/admin/css/admin.css' );

	}
	add_action( 'admin_enqueue_scripts', 'medicpress_admin_enqueue_scripts' );
}


/**
 * Require the files in the inc folder.
 */
MedicPressHelpers::load_file( '/inc/theme-widgets.php' );
MedicPressHelpers::load_file( '/inc/theme-sidebars.php' );
MedicPressHelpers::load_file( '/inc/filters.php' );
MedicPressHelpers::load_file( '/inc/compat.php' );
MedicPressHelpers::load_file( '/inc/woocommerce.php' );
MedicPressHelpers::load_file( '/inc/theme-customizer.php' );
MedicPressHelpers::load_file( '/inc/theme-sticky-menu.php' );
MedicPressHelpers::load_file( '/inc/theme-registration.php' );


/**
 * WIA-ARIA nav walker and accompanying JS file.
 */
if ( ! function_exists( 'medicpress_wai_aria_js' ) ) {
	function medicpress_wai_aria_js() {
		wp_enqueue_script( 'medicpress-wp-wai-aria', get_template_directory_uri() . '/vendor/proteusthemes/wai-aria-walker-nav-menu/wai-aria.js', array( 'jquery' ), null, true );
	}
	add_action( 'wp_enqueue_scripts', 'medicpress_wai_aria_js' );
}


/**
 * Require some files only when in admin.
 */
if ( is_admin() ) {
	MedicPressHelpers::load_file( '/inc/tgm-plugin-activation.php' );
	MedicPressHelpers::load_file( '/inc/documentation-link.php' );
}
