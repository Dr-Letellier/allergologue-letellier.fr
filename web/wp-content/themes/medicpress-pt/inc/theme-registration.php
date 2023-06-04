<?php
/**
 * Theme registration for this theme.
 */

use ProteusThemes\ThemeRegistration\ThemeRegistration;

class MedicPressThemeRegistration {
	function __construct() {
		$this->enable_theme_registration();
	}

	/**
	 * Load theme registration and automatic updates.
	 */
	private function enable_theme_registration() {
		$config = array(
			'item_name'        => 'MedicPress',
			'theme_slug'       => 'medicpress-pt',
			'item_id'          => 6282,
			'tf_item_id'       => 19534952,
			'customizer_panel' => 'panel_medicpress',
			'build'            => 'tf',
		);
		$pt_theme_registration = ThemeRegistration::get_instance( $config );
	}
}

if ( ! MEDICPRESS_DEVELOPMENT ) {
	$medicpress_theme_registration = new MedicPressThemeRegistration();
}
