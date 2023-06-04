# Theme Registration and automatic updates

## Description

This is a composer package for theme registration and automatic updates.

It blocks the use of Theme Options panel in the WP customizer and it also blocks the predefined demo imports for the OCDI plugin, if the theme is not registered. Once the theme is registered, the theme is fully functional and it also enables automatic updates, just like any other wp.org theme.

## Theme Registration

This composer package creates an registration page in the Appearance menu. On that page, the user can register his/her copy of the theme with the EDD license key. If the license key is valid, the theme is registered and it also enables theme updates.

Almost the same process is used for ThemeForest verification, except the email field is also added to the submit form and the ThemeForest purchase code is used instead of EDD license key. On our server the *TF subscriber* user is created and the purchase code together with some other data is saved to this new user, so purchase code verification can be used, if they would want to register multiple sites.

## Automatic Theme Updates

By default we disable the wp.org updates for this theme (so that there are no issues with theme slugs being the same). Then, if the theme is registered and the license key is still valid, the theme updates data will be retrieved. The updates data is stored in a transient for 24 hours.

For ThemeForest automtaic updates, additional step of Envato token verification is needed. It is displayed in the registration form, after the valid pruchase code is provided.

## Temporary theme access

If there is something wrong with our API Server, or with the TF/Envato API server, a temporary access to the theme is granted (for 3 hours). Automatic updates are not working in this phase.

## Example of theme integration

Require this composer package via the theme composer.json file.

Instantiate the main registration class (`inc/theme-registration.php`) and require it in functions.php file. Example:

```
<?php
/**
 * Theme Registration for this theme.
 */

use ProteusThemes\ThemeRegistration\ThemeRegistration;

class AdrenalineThemeRegistration {
	function __construct() {
		$this->enable_theme_registration();
	}

	/**
	 * Load theme registration and automatic updates.
	 */
	private function enable_theme_registration() {
		$config = array(
			'item_name'        => 'Adrenaline', // The name of the theme in EDD
			'theme_slug'       => 'adrenaline-pt', // The slug of the theme
			'item_id'          => 4099, // The EDD item ID
			'tf_item_id'       => 19081216, // The ThemeForest item ID
			'customizer_panel' => 'panel_adrenaline', // The name of the PT options panel in customizer (to be disabled)
			'build'            => 'pt', // Which shop registration will be used (pt or tf) - a different registration page is displayed depending on this setting
		);
		$pt_theme_registration = ThemeRegistration::get_instance( $config );
	}
}

$adrenaline_theme_registration = new AdrenalineThemeRegistration();
```
