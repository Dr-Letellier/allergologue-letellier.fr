<?php
/*
 * Plugin Name: EndiGuard.WP
 * Plugin URI: https://www.icodia.com/fr/solutions/infogerance/wordpress.html
 * Description: A management and reporting plugin for your WordPress outsourcing by Icodia
 * Version: 1.0.7 Ioncube
 * Author: Icodia
 * Author URI: https://www.icodia.com
 * License: GPLv2
 * Text Domain: endiguardwp
 * Domain Path: /languages
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

define('EGWP_MODE', 'ioncube'); // source, ioncube, sourceguardian

function endiguardwp_load_textdomain() {
	load_plugin_textdomain( 'endiguardwp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
//add_action( 'plugins_loaded', 'endiguardwp_load_textdomain' );
endiguardwp_load_textdomain();

if(EGWP_MODE == "ioncube"){
	if ( extension_loaded( 'ionCube Loader' )) {
		require_once __DIR__ . '/includes/engine/endiguardwp.php';
	} else {
		add_action(
			'admin_notices',
			function() {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php _e( 'EndiGuard.WP', 'endiguardwp' ); ?></strong><?php _e( ' : ionCube Loader is required !', 'endiguardwp' ); ?>
				</p>
			</div>
			<?php
			}
		);
	}
}
else if(EGWP_MODE == "sourceguardian"){
	if ( function_exists('sg_load') ) {
		require_once __DIR__ . '/includes/engine/endiguardwp.php';
	} else {
		add_action(
			'admin_notices',
			function() {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php _e( 'EndiGuard.WP', 'endiguardwp' ); ?></strong><?php _e( ' : sourceguardian Loader is required !', 'endiguardwp' ); ?>
				</p>
			</div>
			<?php
			}
		);
	}
}
else {
	require_once __DIR__ . '/includes/engine/endiguardwp.php';
}

?>
