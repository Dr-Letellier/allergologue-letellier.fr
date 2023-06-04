<?php
/**
 * Load here all the individual widgets
 *
 * @package medicpress-pt
 */

// ProteusWidgets init.
new ProteusWidgets;

// Require the individual widgets.
add_action( 'widgets_init', function () {
	// Custom widgets in the theme.
	$medicpress_custom_widgets = array(
		'widget-call-to-action',
		'widget-notice',
		'widget-opening-time',
		'widget-icon-list',
	);

	foreach ( $medicpress_custom_widgets as $file ) {
		MedicPressHelpers::load_file( sprintf( '/inc/widgets/%s.php', $file ) );
	}

	// Relying on composer's autoloader, just provide classes from ProteusWidgets.
	register_widget( 'PW_Brochure_Box' );
	register_widget( 'PW_Facebook' );
	register_widget( 'PW_Featured_Page' );
	register_widget( 'PW_Icon_Box' );
	register_widget( 'PW_Skype' );
	register_widget( 'PW_Social_Icons' );
	register_widget( 'PW_Testimonials' );
	register_widget( 'PW_Person_Profile' );
	register_widget( 'PW_Accordion' );
	register_widget( 'PW_Latest_News' );
	register_widget( 'PW_Pricing_List' );
} );
