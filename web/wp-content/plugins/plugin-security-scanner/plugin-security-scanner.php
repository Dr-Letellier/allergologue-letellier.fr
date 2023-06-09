<?php

/**
 * Plugin Name: Plugin Security Scanner
 * Plugin URI: https://yellowsquare.com/plugin-security-scanner/
 * Description: This plugin determines whether any of your plugins have security vulnerabilities.  It does this by looking up details in the WPScan Vulnerability Database.
 * Version: 2.0.2
 * Author: Glen Scott
 * Author URI: https://www.glenscott.co.uk
 * License: GPL2
 * Text Domain: plugin-security-scanner
 */

/*  Copyright 2015  Glen Scott  (email : glen@glenscott.co.uk)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('PSP_GENERAL_ERROR', 1000);

if ( ! class_exists( 'WP_Http' ) ) {
	include_once( ABSPATH . WPINC. '/class-http.php' );
}

// Check if get_plugins() function exists. This is required on the front end of the
// site, since it is in a file that is normally only loaded in the admin.
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action( 'admin_menu', 'plugin_security_scanner_menu' );
add_action( 'admin_menu', 'plugin_security_scanner_options' );
add_action( 'admin_init', 'plugin_security_scanner_register_settings' );

function plugin_security_scanner_menu() {
	add_management_page(
		__( 'Plugin Security Scanner', 'plugin-security-scanner' ),
		__( 'Plugin Security Scanner', 'plugin-security-scanner' ),
		'manage_options',
		'plugin-security-scanner',
		'plugin_security_scanner_tools'
	);
}

function plugin_security_scanner_options() {
	// This page will be under "Settings"
	add_options_page(
		__( 'Plugin Security Scanner', 'plugin-security-scanner' ),
		__( 'Plugin Security Scanner', 'plugin-security-scanner' ),
		'manage_options',
		'plugin-security-scanner-admin',
		'plugin_security_scanner_admin'
	);
}

function plugin_security_scanner_admin() {


	?>
	<div class="wrap">
		<h2>Plugin Security Scanner</h2>           
		<form method="post" action="options.php">
		<?php
			// This prints out all hidden setting fields
			settings_fields( 'plugin-security-scanner-group' );
			do_settings_sections( 'plugin-security-scanner-admin' );
			submit_button();
		?>
		</form>
	</div>
	<?php

}

function plugin_security_scanner_register_settings() {
	register_setting( 'plugin-security-scanner-group', 'plugin-security-scanner', 'plugin_security_scanner_validate' );

	add_settings_section( 'plugin-security-scanner-section', __( 'General Settings', 'plugin-security-scanner' ),
	'plugin_security_scanner_section_text', 'plugin-security-scanner-admin' );

	add_settings_field( 'plugin-security-scanner-api-token', __( 'API Token', 'plugin-security-scanner'),
		'plugin_security_scanner_api_token_field', 'plugin-security-scanner-admin', 'plugin-security-scanner-section' );

	add_settings_field( 'plugin-security-scanner-email-notification', __( 'Email Notification', 'plugin-security-scanner' ),
	'plugin_security_scanner_email_notification_field', 'plugin-security-scanner-admin', 'plugin-security-scanner-section' );

	add_settings_field( 'plugin-security-scanner-webhook-notification', __( 'Webhook Notification', 'plugin-security-scanner' ),
	'plugin_security_scanner_webhook_notification_field', 'plugin-security-scanner-admin', 'plugin-security-scanner-section' );


	add_settings_field( 'plugin-security-scanner-ignore-nofix',
		__( 'Unpatched issues' , 'plugin-security-scanner' ),
		'plugin_security_scanner_ignore_nofix_field',
		'plugin-security-scanner-admin',
		'plugin-security-scanner-section'
		);

	add_settings_field( 'plugin-security-scanner-ignore-8807', __( 'Ignore', 'plugin-security-scanner' ),
	'plugin_security_scanner_ignore_8807_field', 'plugin-security-scanner-admin', 'plugin-security-scanner-section' );

	if ( false === get_option( 'plugin-security-scanner' ) ) {
	    update_option( 'plugin-security-scanner', array(
			 'email_notification' => '1',
			 'webhook_notification' => '0',
			 'webhook_notification_url' => '',
			 'ignore_8807' => '0') );
	} else {
		$options = get_option( 'plugin-security-scanner' );

		if (false === array_key_exists('email_notification', $options)) {
			$options['email_notification'] = '0';
			update_option( 'plugin-security-scanner', $options );
		}

		if (false == array_key_exists('webhook_notification', $options)){
			$options['webhook_notification'] = '0';
			update_option( 'plugin-security-scanner', $options );
		}

		if (false == array_key_exists('webhook_notification_url', $options)){
			$options['webhook_notification_url'] = '';
			update_option( 'plugin-security-scanner', $options );
		}

		if (false == array_key_exists('ignore_8807', $options)){
			$options['ignore_8807'] = '0';
			update_option( 'plugin-security-scanner', $options );
		}

		if (false == array_key_exists('ignore_nofix', $options)){
			$options['ignore_nofix'] = '0';
			update_option( 'plugin-security-scanner', $options );
		}

	}
}

function plugin_security_scanner_section_text() {
}

function plugin_security_scanner_validate($input) {
	if ( ! is_array( $input ) ) {
		$input = array(
			'email_notification' => 0,
			'webhook_notification' => 0,
			'webhook_notification_url' => ''
			);
	}

	$webhook = (isset($input['webhook_notification']) ? $input['webhook_notification'] : '');
	$url = $input['webhook_notification_url'];
	if ($webhook == '1'){
		if ($url == ''){
			add_settings_error( 'plugin-security-scanner', esc_attr( 'setting_updated' ), 'missing required field webhook url', 'error' );
		} else if (false == filter_var($url, FILTER_VALIDATE_URL)){
			add_settings_error( 'plugin-security-scanner', esc_attr( 'setting_updated' ), 'webhook url is not a valid url', 'error' );
		}
	}

	return $input;
}

function plugin_security_scanner_api_token_field() {
	$options = get_option( 'plugin-security-scanner' );

	echo '<label for="plugin-security-scanner-api-token">WPScan Vulnerability Database API Token</label>';
	echo '<p>To use the API you need to <a href="https://wpvulndb.com/users/sign_up">register a user and get the API token from your profile page</a>.</p>';
	echo '<br />';
	echo '<input type="text" id="plugin-security-scanner-api-token" name="plugin-security-scanner[api_token]" placeholder="" value="'. (isset($options['api_token']) ? $options['api_token'] : '') . '"/>';
}

function plugin_security_scanner_email_notification_field() {
	$options = get_option( 'plugin-security-scanner' );

	echo '<input type="checkbox" id="plugin-security-scanner-email-notification" name="plugin-security-scanner[email_notification]" value="1"' . checked( 1, $options['email_notification'], false ) . '/>';
	echo '<label for="plugin-security-scanner-email-notification">Send an e-mail notification when vulnerable plugins are found?</label>';
}

function plugin_security_scanner_webhook_notification_field() {
	$options = get_option( 'plugin-security-scanner' );

	echo '<input type="checkbox" id="plugin-security-scanner-webhook-notification" name="plugin-security-scanner[webhook_notification]" value="1"' . checked( 1, $options['webhook_notification'], false ) . '/>';
	echo '<label for="plugin-security-scanner-webhook-notification">Send a webhook notification when vulnerable plugins are found?</label>';
	echo '<br />';
	echo '<input type="url" id="plugin-security-scanner-webhook-notification-url" name="plugin-security-scanner[webhook_notification_url]" placeholder="webhook url" value="'. $options['webhook_notification_url'] . '"/>';
}

function plugin_security_scanner_ignore_8807_field() {
	$options = get_option( 'plugin-security-scanner' );

	echo '<input type="checkbox" id="plugin-security-scanner-ignore-8807" name="plugin-security-scanner[ignore_8807]" value="1"' . checked( 1, $options['ignore_8807'], false ) . '/>';
	echo '<label for="plugin-security-scanner-ignore-8807">Ignore <em>WordPress 2.3-4.8.3 - Host Header Injection in Password Reset</em> -- <strong>Warning:  please make sure your server is not vulnerable before ticking this box (<a href="https://exploitbox.io/vuln/WordPress-Exploit-4-7-Unauth-Password-Reset-0day-CVE-2017-8295.html">see solution section</a>)</strong></label>';
}

function plugin_security_scanner_ignore_nofix_field() {
	$options = get_option( 'plugin-security-scanner' );

	echo '<input type="checkbox" id="plugin-security-scanner-ignore-nofix" name="plugin-security-scanner[ignore_nofix]" value="1"' .
		checked( 1, $options['ignore_nofix'], false) . '/>';
	echo '<label for="plugin-security-scanner-ignore-nofix">Ignore issues where no known fix currently exists</label>';		
}

function get_vulnerable_plugins() {
	$options = get_option( 'plugin-security-scanner' );

	$vulnerabilities = array();

	$request = new WP_Http;
	$request_args = array('headers' => 'Authorization: Token token=' . $options['api_token']);
	global $wp_version;
	$version_raw = $wp_version;
	$version_trimmed = str_replace(".", "", $wp_version);
	$result = $request->request( 'https://wpvulndb.com/api/v3/wordpresses/' . $version_trimmed, $request_args );

	if ( is_wp_error( $result )) {
		return new WP_Error( PSP_GENERAL_ERROR, $result->get_error_message() );
	}
	else if (is_error_status_code(wp_remote_retrieve_response_code($result)) ){
		$headers = $result['headers'];
		if ($headers->offsetExists('x-ratelimit-remaining') && $headers->offsetGet('x-ratelimit-remaining') === '0') {
			$msg = 'Your API key has a limit of ' . $headers->offsetGet('x-ratelimit-limit') . ' requests per day.  You have 0 requests left until ' . date_i18n( get_option( 'date_format' ), $headers->offsetGet('x-ratelimit-reset') );
		} else {
			$msg = 'Failed to query wpvulndb, status code does not indicate success: ' . wp_remote_retrieve_response_code($result);
		}

		return new WP_Error( PSP_GENERAL_ERROR, $msg );
	}
	else {
		if ( $result['body'] ) {
			$version = json_decode( $result['body'] );
			if ( isset( $version->$version_raw->vulnerabilities ) ) {
				foreach ( $version->$version_raw->vulnerabilities as $vuln ) {
					if ('1' == $options['ignore_8807'] && $vuln->id == 8807) {
						continue;
					}
					if ('1' == $options['ignore_nofix'] && $vuln->fixed_in === null) {
						continue;
					}
					$vulnerabilities[$version_raw][] = $vuln;
				}
			}
		}
	}

	foreach ( get_plugins() as $name => $details ) {
		// get unique name
		if ( preg_match( '|(.+)/|', $name, $matches ) ) {
			$plugin_key = $matches[1];
			$result = $request->request( 'https://wpvulndb.com/api/v3/plugins/' . $plugin_key, $request_args );

			if ( is_wp_error( $result )) {
				return new WP_Error( PSP_GENERAL_ERROR, $result->get_error_message() );
			}
			else if (is_error_status_code(wp_remote_retrieve_response_code($result)) ){
				return new WP_Error( PSP_GENERAL_ERROR, 'Failed to query wpvulndb, status code does not indicate success: ' . wp_remote_retrieve_response_code($result) );
			}
			else {
				if ( $result['body'] ) {
					$plugin = json_decode( $result['body'] );

					if ( isset( $plugin->$plugin_key->vulnerabilities ) ) {
						foreach ( $plugin->$plugin_key->vulnerabilities as $vuln ) {
							if ('1' == $options['ignore_nofix'] && $vuln->fixed_in === null) {
								continue;
							}

							if ( ! isset($vuln->fixed_in) ||
								version_compare( $details['Version'], $vuln->fixed_in, '<' ) ) {
								$vulnerabilities[$name][] = $vuln;
							}
						}
					}
				}
			}
		} 
	}

	foreach ( wp_get_themes() as $details ) {
		$theme_key = strtolower( str_replace( ' ', '', $details->name ) );
		$result = $request->request( 'https://wpvulndb.com/api/v3/themes/' . $theme_key, $request_args );

		if ( is_wp_error( $result )) {
			return new WP_Error( PSP_GENERAL_ERROR, $result->get_error_message() );
		}
		else if (is_error_status_code(wp_remote_retrieve_response_code($result)) ){
			return new WP_Error( PSP_GENERAL_ERROR, 'Failed to query wpvulndb, status code does not indicate success: ' . wp_remote_retrieve_response_code($result) );
		}
		else {
			if ( $result['body'] ) {
				$theme = json_decode( $result['body'] );

				if ( isset( $theme->$theme_key->vulnerabilities ) ) {
					foreach ( $theme->$theme_key->vulnerabilities as $vuln ) {
						if ('1' == $options['ignore_nofix'] && $vuln->fixed_in === null) {
							continue;
						}

						if ( ! isset($vuln->fixed_in) ||
							version_compare( $details['Version'], $vuln->fixed_in, '<' ) ) {
							$vulnerabilities[$theme_key][] = $vuln;
						}
					}
				}
			}
		}
	}

	return $vulnerabilities;
}

function is_error_status_code($statusCode){
	return ($statusCode > 299 || $statusCode < 200) && $statusCode != 404;
}

function plugin_security_scanner_tools() {
	if ( ! current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo '<div class="wrap">';
	echo '<h2>' . esc_html__( 'Plugin Security Scanner', 'plugin-security-scanner' ) . '</h2>';

	$options = get_option( 'plugin-security-scanner' );
	if (!isset($options['api_token']) || ! $options['api_token']) {
		echo '<p>You must enter an API token in order to use the scanner.  <a href="https://wpvulndb.com/users/sign_up">Register a user</a> and then copy the API token into the the Plugin Security Scanner settings.</p>';
		wp_die();
	}

	$vulnerability_count = 0;

	$vulnerabilities = get_vulnerable_plugins();

	if (is_wp_error( $vulnerabilities ) ) {
		echo '<p>Unfortunately a scan could not be performed due to the following reason:</p>';
		echo '<p><strong>' . $vulnerabilities->get_error_message(PSP_GENERAL_ERROR) . '</strong></p>';
	} else {
		foreach ( $vulnerabilities as $plugin_name => $plugin_vulnerabilities ) {
			foreach ( $plugin_vulnerabilities as $vuln ) {
					echo '<p><strong>' . esc_html__( 'Vulnerability found', 'plugin-security-scanner' ) . ':</strong> ' . esc_html( $vuln->title );

					if ($vuln->fixed_in === null) {
						echo ' [* note: no fix currently exists for this issue *] ';
					}

					echo ' -- <a href="' . esc_url( 'https://wpvulndb.com/vulnerabilities/' . $vuln->id ) . '" target="_blank">' . esc_html__( 'View details', 'plugin-security-scanner' ) . '</a></p>';

						$vulnerability_count++;
			}
			flush();
		}

		echo '<p>' .
			sprintf(
				_n(
					'Scan completed: %s vulnerability found.',
				    'Scan completed: %s vulnerabilities found.',
					$vulnerability_count,
					'plugin-security-scanner'
				),
				'<strong>' . esc_html( $vulnerability_count ) . '</strong>'
			)
		.
			'</p>';
	}

	echo '</div>';
}

// scheduled email to admin
register_activation_hook( __FILE__, 'plugin_security_scanner_activation' );
/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
function plugin_security_scanner_activation() {
	wp_schedule_event( time(), 'daily', 'plugin_security_scanner_daily_event_hook' );
}

add_action( 'plugin_security_scanner_daily_event_hook', 'plugin_security_scanner_do_this_daily' );
/**
 * On the scheduled action hook, run the function.
 */
function plugin_security_scanner_do_this_daily() {
	$options = get_option( 'plugin-security-scanner' );
	$admin_email = get_option( 'admin_email' );

	$vulnerabilities = get_vulnerable_plugins();

	if (is_wp_error($vulnerabilities)) {
		if ( $admin_email && '1' === $options['email_notification'] ) {
			wp_mail( $admin_email, get_bloginfo() . ' ' . __( 'Plugin Security Scan', 'plugin-security-scanner' ) . ' ' . date_i18n( get_option( 'date_format' ) ), $vulnerabilities->get_error_message() );
		}

		if ('1' === $options['webhook_notification']) {
			$request = new WP_Http;
			$result = $request->post( $options['webhook_notification_url'], array('body' => apply_filters('pluginsecurityscanner_webhook_message', json_encode($vulnerabilities->get_error_message())), 'headers' => array( "Content-type" => "application/json" )) );
		}
	} else {
		if ( $admin_email && '1' === $options['email_notification'] ) {
			$mail_body = '';

			// run scan
			$vulnerability_count = 0;

			foreach ( $vulnerabilities as $plugin_name => $plugin_vulnerabilities ) {
				foreach ( $plugin_vulnerabilities as $vuln ) {
					$mail_body .= __( 'Vulnerability found', 'plugin-security-scanner' ) . ': ' . $vuln->title . "\n";
					$vulnerability_count++;
				}
			}

			// if vulns, email admin
			if ( $vulnerability_count ) {
				$mail_body .= "\n\n" . sprintf(_n(
					'Scan completed: %s vulnerability found.',
					'Scan completed: %s vulnerabilities found.',
				$vulnerability_count, 'plugin-security-scanner'), $vulnerability_count) . "\n";

				wp_mail( $admin_email, get_bloginfo() . ' ' . __( 'Plugin Security Scan', 'plugin-security-scanner' ) . ' ' . date_i18n( get_option( 'date_format' ) ), $mail_body );
			}
		}

		if ('1' === $options['webhook_notification']){
			$request = new WP_Http;
			$result = $request->post( $options['webhook_notification_url'], array('body' => apply_filters('pluginsecurityscanner_webhook_message', json_encode($vulnerabilities)), 'headers' => array( "Content-type" => "application/json" )) );
		}
	}
}

register_deactivation_hook( __FILE__, 'prefix_deactivation' );

function prefix_deactivation() {
	wp_clear_scheduled_hook( 'plugin_security_scanner_daily_event_hook' );
}
