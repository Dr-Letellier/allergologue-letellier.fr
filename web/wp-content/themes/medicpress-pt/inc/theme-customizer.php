<?php
/**
 * Load the Customizer with some custom extended addons
 *
 * @package medicpress-pt
 * @link http://codex.wordpress.org/Theme_Customization_API
 */

/**
 * This funtion is only called when the user is actually on the customizer page
 *
 * @param  WP_Customize_Manager $wp_customize
 */
if ( ! function_exists( 'medicpress_customizer' ) ) {
	function medicpress_customizer( $wp_customize ) {
		// Add required files.
		MedicPressHelpers::load_file( '/inc/customizer/class-customize-base.php' );

		new MedicPress_Customizer_Base( $wp_customize );
	}
	add_action( 'customize_register', 'medicpress_customizer' );
}


/**
 * Takes care for the frontend output from the customizer and nothing else
 */
if ( ! function_exists( 'medicpress_customizer_frontend' ) && ! class_exists( 'MedicPress_Customize_Frontent' ) ) {
	function medicpress_customizer_frontend() {
		MedicPressHelpers::load_file( '/inc/customizer/class-customize-frontend.php' );
		$medicpress_customize_frontent = new MedicPress_Customize_Frontent();
	}
	add_action( 'init', 'medicpress_customizer_frontend' );
}
