<?php
/**
 * Class which handles the output of the WP customizer on the frontend.
 * Meaning that this stuff loads always, no matter if the global $wp_cutomize
 * variable is present or not.
 *
 * @package medicpress-pt
 */

/**
 * Customizer frontend related code
 */
class MedicPress_Customize_Frontent {

	/**
	 * Add actions to load the right staff at the right places (header, footer).
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts' , array( $this, 'output_customizer_css' ), 20 );
		add_action( 'wp_head' , array( $this, 'head_output' ) );
		add_action( 'wp_footer' , array( $this, 'footer_output' ) );
	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action( 'wp_head' , array( $this, 'head_output' ) );
	 */
	public static function output_customizer_css() {
		$css_string = self::get_customizer_css();

		if ( $css_string ) {
			wp_add_inline_style( self::get_inline_styles_handler(), $css_string );
		}
	}

	/**
	 * This will get custom WordPress settings to the live theme's WP head.
	 *
	 * Used by hook: 'wp_head'
	 *
	 * @see add_action( 'wp_head' , array( $this, 'head_output' ) );
	 */
	public static function get_customizer_css() {
		$css = array();

		$css[] = self::get_customizer_colors_css();
		$css[] = self::get_page_header_bg_image();
		$css[] = self::get_body_bg_image();

		return join( PHP_EOL, $css );
	}


	/**
	 * Woocommerce css handler if woo is active, main css handler otherwise
	 *
	 * @return string
	 */
	public static function get_inline_styles_handler() {
		if ( MedicPressHelpers::is_woocommerce_active() ) {
			return 'medicpress-woocommerce';
		}

		return 'medicpress-main';
	}


	/**
	 * Branding CSS, generated dynamically and cached stringifyed in db
	 *
	 * @return string CSS
	 */
	public static function get_customizer_colors_css() {
		$out = '';

		$cached_css = get_theme_mod( 'cached_css', '' );

		$out .= '/* WP Customizer start */' . PHP_EOL;
		$out .= strip_tags( apply_filters( 'medicpress_cached_css', $cached_css ) );
		$out .= PHP_EOL . '/* WP Customizer end */';

		return $out;
	}


	/**
	 * Page header background image
	 *
	 * @return string CSS
	 */
	public static function get_page_header_bg_image() {
		// Don't output header bg CSS on Front Page with Slider page template.
		if ( is_page_template( array( 'template-front-page-slider.php', 'template-front-page-slider-alt.php' ) ) ) {
			return '';
		}

		$out                  = '';
		$page_header_bg_img   = get_theme_mod( 'page_header_bg_img', '' );
		$page_header_bg_color = get_theme_mod( 'page_header_bg_color', '' );

		if ( '' !== $page_header_bg_img ) {
			$out = sprintf(
				'.page-header { background-image: url(%1$s); background-repeat: %2$s; background-position: top %3$s; background-attachment: %4$s; }',
				esc_url( $page_header_bg_img ),
				sanitize_key( get_theme_mod( 'page_header_bg_img_repeat', 'repeat' ) ),
				sanitize_key( get_theme_mod( 'page_header_bg_img_position_x', 'left' ) ),
				sanitize_key( get_theme_mod( 'page_header_bg_img_attachment', 'scroll' ) )
			);
		}

		if ( '' !== $page_header_bg_color ) {
			$out .= sprintf(
				'.page-header { background-color: %s }',
				esc_html( $page_header_bg_color )
			);
		}

		return $out;
	}


	/**
	 * Body background image
	 *
	 * @return string CSS
	 */
	public static function get_body_bg_image() {
		$out         = '';
		$body_bg_img = get_theme_mod( 'body_bg_img', '' );

		if ( ! empty( $body_bg_img ) ) {
			$out = sprintf(
				'body .boxed-container { background-image: url(%1$s); background-repeat: %2$s; background-position: top %3$s; background-attachment: %4$s; }',
				esc_url( $body_bg_img ),
				sanitize_key( get_theme_mod( 'body_bg_img_repeat', 'repeat' ) ),
				sanitize_key( get_theme_mod( 'body_bg_img_position_x', 'left' ) ),
				sanitize_key( get_theme_mod( 'body_bg_img_attachment', 'scroll' ) )
			);
		}

		return $out;
	}


	/**
	 * Outputs the code in head of the every page
	 *
	 * Used by hook: add_action( 'wp_head' , array( $this, 'head_output' ) );
	 */
	public static function head_output() {
		// Custom JS from the customizer.
		$script = get_theme_mod( 'custom_js_head', '' );

		if ( ! empty( $script ) ) {
			echo PHP_EOL . $script . PHP_EOL;
		}
	}


	/**
	 * Outputs the code in footer of the every page, right before closing </body>
	 *
	 * Used by hook: add_action( 'wp_footer' , array( $this, 'footer_output' ) );
	 */
	public static function footer_output() {
		$script = get_theme_mod( 'custom_js_footer', '' );

		if ( ! empty( $script ) ) {
			echo PHP_EOL . $script . PHP_EOL;
		}
	}
}
