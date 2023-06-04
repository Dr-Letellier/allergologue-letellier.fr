<?php
/**
 * Theme updates class.
 *
 * @package pt-theme-registration
 */

namespace ProteusThemes\ThemeRegistration;

class ThemeUpdates {
	private $registration_options;
	private $purchase_code;
	private $purchase_code_type;
	private $theme;
	private $envato_token;
	private $response_key;

	/**
	 * Initiate the Theme updater
	 */
	public function __construct( $args = array() ) {

		$defaults = array(
			'item_id'    => false,
			'item_name'  => '',
			'theme_slug' => get_template(),
			'tf_item_id' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->registration_options = ThemeRegistration::get_theme_activation_data();

		// Only enable automatic theme updates, if the theme is registered.
		if ( $this->are_theme_udpates_available( $this->registration_options ) ) {
			$this->purchase_code      = isset( $this->registration_options['purchase_code'] ) ? $this->registration_options['purchase_code'] : '';
			$this->purchase_code_type = isset( $this->registration_options['purchase_code_type'] ) ? $this->registration_options['purchase_code_type'] : 'xx';

			$theme_slug                = sanitize_key( $args['theme_slug'] );
			$theme                     = wp_get_theme( $theme_slug );
			$envato_token_data         = ThemeRegistration::get_envato_token_data( $this->registration_options );

			$this->theme               = array();
			$this->theme['version']    = $theme->get( 'Version' );
			$this->theme['slug']       = $theme_slug;
			$this->theme['item_id']    = absint( $args['item_id'] );
			$this->theme['item_name']  = sanitize_text_field( $args['item_name'] );
			$this->response_key        = $this->theme['slug'] . '-theme-update-response';
			$this->theme['tf_item_id'] = absint( $args['tf_item_id'] );
			$this->envato_token        = $envato_token_data['token'];

			add_filter( 'site_transient_update_themes',        array( $this, 'theme_update_transient' ) );
			add_filter( 'delete_site_transient_update_themes', array( $this, 'delete_theme_update_transient' ) );
			add_action( 'load-update-core.php',                array( $this, 'delete_theme_update_transient_if_forced' ) );
			add_action( 'admin_notices',                       array( $this, 'update_notice' ) );
		}

		// Disable WordPress.org theme updates of this theme.
		add_filter( 'http_request_args', array( $this, 'disable_wp_org_theme_update' ), 5, 2 );
	}


	/**
	 * Display the theme update notifications.
	 *
	 * @return void
	 */
	public function update_notice() {
		$api_response = get_transient( $this->response_key );

		if ( false === $api_response || ! isset( $api_response['data'] ) ) {
			return;
		}

		// Get the proper theme updates data.
		$api_response = $api_response['data'];

		$update_notice_string = esc_html__( "Updating this theme will lose any customizations you have made to the original theme files (customizer settings will not be lost). 'Cancel' to stop, 'OK' to update.", 'pt-tr' );

		$update_url     = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . rawurlencode( $this->theme['slug'] ), 'upgrade-theme_' . $this->theme['slug'] );

		$update_onclick = ' onclick="if ( confirm(\'' . esc_js( $update_notice_string ) . '\') ) {return true;}return false;"';

		if ( version_compare( $this->theme['version'] , $api_response['new_version'], '<' ) ) :
		?>
			<div id="update-nag">
			<?php
				printf(
					esc_html__( 'Theme update is available: %5$s%1$s %2$s%6$s! %5$s%3$sUpdate now%4$s%6$s (check %7$s%1$s changelog%4$s).', 'pt-tr' ),
					esc_html( $this->theme['item_name'] ),
					esc_html( $api_response['new_version'] ),
					'<a href="' . esc_url( $update_url ) . '"' . $update_onclick . '>',
					'</a>',
					'<strong>',
					'</strong>',
					'<a href="' . esc_url( $api_response['homepage'] ) . '#go_changelog" target="_blank">'
				);
			?>
			</div>
		<?php
		endif;
	}


	/**
	 * Update the theme update transient with the response from the theme update check.
	 *
	 * @param  array $value The default WP theme update transient object (WP core).
	 * @return obj          The updated WP theme update transient object.
	 */
	public function theme_update_transient( $value ) {
		// Get theme update data.
		$update_data = $this->check_for_update();

		// If valid update data (not false), insert it into the value response array.
		if ( ! empty( $update_data ) ) {
			$value->response[ $this->theme['slug']  ] = $update_data['data'];
		}

		return $value;
	}


	/**
	 * Get the latest theme data from either EDD or TF.
	 *
	 * @return array|boolean If an update is available, returns the update parameters,
	 *                       if no update is needed returns false,if the request fails returns false.
	 */
	private function check_for_update() {
		$update_data = get_transient( $this->response_key );

		// Make a new theme version request.
		if ( false === $update_data ) {
			$failed = false;

			// Prepare data for the POST request to our server.
			$body_data = array(
				'purchase_code'      => $this->purchase_code,
				'item_id'            => $this->theme['item_id'],
				'item_name'          => $this->theme['item_name'],
				'tf_item_id'         => $this->theme['tf_item_id'],
				'theme_slug'         => $this->theme['slug'],
				'envato_token'       => $this->envato_token,
			);

			// Send POST request to our server.
			$request_data = array(
				'timeout' => 15,
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => json_encode( $body_data ),
			);

			$request = wp_remote_post( ThemeRegistration::ENDPOINT_ROOT . 'theme-update-data', $request_data );

			// Make sure the request was successful.
			if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
				$failed = true;
			}

			$update_data = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( ! is_array( $update_data ) ) {
				$failed = true;
			}

			if ( isset( $update_data['status'] ) && ! $update_data['status'] ) {
				$failed = true;
			}

			if ( ! isset( $update_data['data'] ) ) {
				$failed = true;
			}

			// If the request failed, try again in 3 hours.
			if ( $failed ) {
				$data = array();
				$data['new_version']   = $this->theme['version'] ;
				$data['error_message'] = isset( $update_data['message'] ) ? $update_data['message'] : '';
				set_transient( $this->response_key, $data, 3 * HOUR_IN_SECONDS );
				return false;
			}

			// If the status is 'ok', return the update arguments.
			if ( ! $failed ) {
				if ( isset( $update_data['data']['sections'] ) ) {
					$update_data['data']['sections'] = maybe_unserialize( $update_data['data']['sections'] );
				}

				set_transient( $this->response_key, $update_data, 24 * HOUR_IN_SECONDS );
			}
		}

		$update_version = isset( $update_data['data']['new_version'] ) ? $update_data['data']['new_version'] : $this->theme['version'] ;

		if ( version_compare( $this->theme['version'] , $update_version, '>=' ) ) {
			return false;
		}

		return $update_data;
	}


	/**
	 * Are theme updates available to the user?
	 * PT shop: Is the theme registered?
	 * TF: Is the theme registered and envato token valid?
	 *
	 * @param  array $registration_options The theme registration options.
	 * @return boolean
	 */
	private function are_theme_udpates_available( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = $this->registration_options;
		}

		// If theme is temporary registered, do not allow automatic updates.
		if ( ThemeRegistration::is_theme_temporary_registered() ) {
			return false;
		}

		// Check if it's not registered.
		if ( ! ThemeRegistration::is_theme_registered( $pt_theme_activation ) ) {
			return false;
		}

		// Check if it's not registered.
		if ( ThemeRegistration::has_purchase_code_expired( $pt_theme_activation ) ) {
			return false;
		}

		// Check if it's a TF registration and the envato token is not set.
		$envato_token_data = ThemeRegistration::get_envato_token_data( $pt_theme_activation );

		if ( 'tf' === ThemeRegistration::get_purchase_code_type( $pt_theme_activation ) && empty( $envato_token_data['token'] ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Remove the update data for the theme.
	 *
	 * @return void
	 */
	public function delete_theme_update_transient() {
		delete_transient( $this->response_key );
	}


	/**
	 * Remove the update data for the theme, if the force-check get parameter is set.
	 *
	 * @return void
	 */
	public function delete_theme_update_transient_if_forced() {
		if ( isset( $_GET['force-check'] ) && 1 == $_GET['force-check'] ) {
			delete_transient( $this->response_key );
		}
	}


	/**
	 * Disable WordPress.org theme updates of this theme.
	 *
	 * @param array  $r   An array of HTTP request arguments.
	 * @param string $url The request URL.
	 */
	public function disable_wp_org_theme_update( $r, $url ) {
		// Array of theme slug (folder) to exclude.
		$themes_to_disable = array(
			get_template(),
			get_stylesheet(),
		);

		// Bail, if this request is not a theme update check.
		if ( false === strpos( $url, 'api.wordpress.org/themes/update-check' ) ) {
			return $r;
		}

		// Remove themes from the request.
		if ( isset( $r['body']['themes'] ) ) {
			$r_themes = json_decode( $r['body']['themes'], true );

			foreach ( $themes_to_disable as $theme ) {
				unset( $r_themes['themes'][ $theme ] );
			}

			$r['body']['themes'] = json_encode( $r_themes );
		}

		return $r;
	}
}
