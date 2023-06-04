<?php
/*
 * Plugin Name: Must Use Plugin EndiGuard.WP
 * Plugin URI: https://www.icodia.com
 * Description: This plugin disables automatic updates of WordPress to ensure proper operation of EndiGuard.WP
 * Author: Icodia
 * Author URI: https://www.icodia.com
 */

add_filter( "automatic_updater_disabled", "__return_true" );
add_filter( "auto_update_theme", "__return_false" );
add_filter( "auto_update_plugin", "__return_false" );
