<?php
/**
 * Theme registration and limitation class.
 *
 * @package pt-theme-registration
 */

namespace ProteusThemes\ThemeRegistration;

class ThemeRegistration {
	/**
	 * The instance *Singleton* of this class
	 *
	 * @var object
	 */
	private static $instance;


	/**
	 * The temporary registration access transient key.
	 *
	 * @var string
	 */
	private static $temp_access_key;


	/**
	 * The expiration email transient key.
	 *
	 * @var string
	 */
	private static $expiration_email_key;


	/**
	 * The license expiration recheck transient key.
	 *
	 * @var string
	 */
	private static $expiration_recheck_key;


	/**
	 * The resulting page's hook_suffix, or false if the user does not have the capability required.
	 *
	 * @var boolean or string
	 */
	private $theme_page;


	/**
	 * The theme options passed into the constructor.
	 *
	 * @var array
	 */
	private $theme_args;


	/**
	 * The instance of the Theme Updater class.
	 *
	 * @var obj
	 */
	private $theme_updater;


	/**
	 * The theme settings page slug.
	 *
	 * @var string
	 */
	const THEME_PAGE_SLUG = 'theme-registration';


	/**
	 * The WP capability needed, to see the theme registration page, and to verify the AJAX calls.
	 *
	 * @var string
	 */
	const CAPABILITY = 'switch_themes';


	/**
	 * The API endpoint for purchase code verification.
	 *
	 * @var string
	 */
	const ENDPOINT_ROOT = 'https://www.proteusthemes.com/wp-json/olm-verification/v1/';


	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @param array $args The options for this theme.
	 * @return PT_Theme_Registration the *Singleton* instance.
	 */
	public static function get_instance( $args = array() ) {
		if ( null === static::$instance ) {
			static::$instance = new static( $args );
		}

		return static::$instance;
	}


	/**
	 * Class construct function, to initiate this class.
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 *
	 * @param array $args The options for this theme.
	 */
	protected function __construct( $args ) {
		$this->theme_args = $args;

		// Define the temporary access transient key for this theme.
		self::$temp_access_key = $this->theme_args['theme_slug'] . '-temp-reg';

		// Define the expiration email transient key for this theme.
		self::$expiration_email_key = $this->theme_args['theme_slug'] . '-dont-send-email';

		// Define the expiration license recheck key for this theme.
		self::$expiration_recheck_key = $this->theme_args['theme_slug'] . '-dont-recheck';

		$registration_options = self::get_theme_activation_data();

		// Load these hooks only in the admin area.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'create_theme_settings_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Execute only, if the theme access is not allowed.
			if ( ! self::is_theme_access_allowed( $registration_options ) ) {
				// Redirect the user to the registration page, after theme activation.
				add_action( 'after_switch_theme', array( $this, 'redirect_to_theme_registration_page' ) );

				// Display the admin notice.
				add_action( 'admin_notices', array( $this, 'theme_is_not_registered_admin_notice' ) );

				// For older version of OCDI (before 2.0).
				add_filter( 'pt-ocdi/import_files', '__return_empty_array', 11 );
				add_filter( 'pt-ocdi/plugin_intro_text', array( $this, 'disable_ocdi_change_intro_text' ), 11 );

				// For OCDI version 2.0 and above.
				add_filter( 'pt-ocdi/plugin_page_display_callback_function', array( $this, 'disable_ocdi_plugin_page_callback' ) );
			}

			// Display an admin notice, if the theme purchase code has expired.
			if ( self::has_purchase_code_expired( $registration_options ) ) {
				add_action( 'admin_notices', array( $this, 'purchase_code_has_expired_admin_notice' ) );
			}
			else {
				// Initialize the theme updater.
				$this->setup_theme_updater();
			}

			// AJAX calls.
			add_action( 'wp_ajax_pt_tr_activate_purchase_code', array( $this, 'ajax_activate_purchase_code' ) );
			add_action( 'wp_ajax_pt_tr_deactivate_purchase_code', array( $this, 'ajax_deactivate_purchase_code' ) );
			add_action( 'wp_ajax_pt_tr_envato_token_verification', array( $this, 'ajax_envato_token_verification' ) );
			add_action( 'wp_ajax_pt_tr_purchase_code_expiration', array( $this, 'ajax_purchase_code_expiration' ) );
		}

		// Load these hooks on frontpage and admin area.
		// The customize_register hook has to be defined on frontpage as well, otherwise the controls do not stick (they disappear).
		if ( ! self::is_theme_access_allowed( $registration_options ) ) {
			add_action( 'customize_register', array( $this, 'disable_customizer_theme_options' ) );
		}

		add_action( 'customize_register', array( $this, 'add_customizer_theme_support_link' ) );

		// Maybe send the expiration email.
		if ( self::is_theme_registered( $registration_options ) ) {
			add_action( 'wp', array( $this, 'maybe_send_expiration_email' ) );
		}
	}


	/**
	 * Private clone method to prevent cloning of the instance of the *Singleton* instance.
	 *
	 * @return void
	 */
	private function __clone() {}


	/**
	 * Private unserialize method to prevent unserializing of the *Singleton* instance.
	 *
	 * @return void
	 */
	private function __wakeup() {}


	/**
	 * Creates the theme settings page and a submenu item in WP Appearance menu.
	 */
	public function create_theme_settings_page() {
		$this->theme_page = add_theme_page( esc_html__( 'Theme Registration' , 'pt-tr' ), esc_html__( 'Theme Registration' , 'pt-tr' ), self::CAPABILITY, self::THEME_PAGE_SLUG, array( $this, 'display_theme_settings_page' ) );
	}


	/**
	 * Theme settings page display (HTML output).
	 */
	public function display_theme_settings_page() {
		// Get theme activation data.
		$pt_theme_activation           = self::get_theme_activation_data();
		$activation_status             = self::is_theme_registered( $pt_theme_activation );
		$activation_pc                 = self::get_purchase_code( $pt_theme_activation );
		$activation_pc_type            = self::get_purchase_code_type( $pt_theme_activation );
		$activation_message            = self::get_theme_activation_message( $pt_theme_activation );
		$activation_expiration         = self::get_formated_purchase_code_expiration( $pt_theme_activation );
		$activation_expiration_message = self::get_purchase_code_expiration_message( $activation_expiration );
		$envato_token_data             = self::get_envato_token_data( $pt_theme_activation );
		$activation_email              = self::get_activation_email( $pt_theme_activation );

		// Set the admin email as the default email.
		$activation_email = ! empty( $activation_email ) ? $activation_email : get_option('admin_email');

		// Include the correct registration page view.
		include( sprintf( '%1$s/vendor/proteusthemes/theme-registration-and-updates/views/%2$s-registration-page.php', get_template_directory(), esc_html( $this->theme_args['build'] ) ) );
	}


	/**
	 * Enqueue admin scripts (JS and CSS)
	 *
	 * @param string $hook holds info on which admin page you are currently loading.
	 */
	public function admin_enqueue_scripts( $hook ) {
		// Enqueue the scripts only on the theme settings page.
		if ( $this->theme_page === $hook ) {
			wp_enqueue_script( 'pt-tr-main-js', get_template_directory_uri() . '/vendor/proteusthemes/theme-registration-and-updates/assets/js/main.js' , array( 'jquery' ) );

			wp_localize_script( 'pt-tr-main-js', 'PTTR',
				array(
					'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
					'ajaxNonce' => wp_create_nonce( 'pt-tr-ajax-verification' ),
					'text'      => array(
						'request_error' => esc_html__( 'Request error: ', 'pt-tr' ),
					),
				)
			);

			wp_enqueue_style( 'pt-tr-main-css', get_template_directory_uri() . '/vendor/proteusthemes/theme-registration-and-updates/assets/css/main.css' );
		}
	}


	/**
	 * Redirect the the user to the registration page, after theme activation.
	 */
	public function redirect_to_theme_registration_page() {
		global $pagenow;

		if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) ) {
			wp_safe_redirect( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) );
			exit();
		}
	}


	/**
	 * AJAX function for activating the purchase code.
	 */
	public function ajax_activate_purchase_code() {
		// Verify if the AJAX call is valid.
		$this->verify_ajax_call();

		$response = $this->get_response( 'purchase-code-activation' );

		// Check if temporary access should be granted, because of our server issues or delete it.
		if ( ! empty( $response['temp_access'] ) ) {
			set_transient( self::$temp_access_key, true, 3 * HOUR_IN_SECONDS );

			$response['message'] .= sprintf( esc_html__( ' However, you have been granted temporary access to all theme functionality for 3 hours. After 3 hours, please try to register the theme again. If you keep getting this error, please %1$scontact our support%2$s.', 'pt-tr' ), '<a href="https://support.proteusthemes.com/" target="_blank">', '</a>' );
		}
		else {
			delete_transient( self::$temp_access_key );
		}

		// Save the appropriate data to the theme mod setting and set the expiration email transient (1 week before expiration).
		if ( $response['status'] ) {
			set_theme_mod( 'pt_theme_activation', $response );

			if ( ! empty( $response['expires'] ) && 'lifetime' !== $response['expires'] ) {
				$time_diff = self::get_time_difference_in_seconds( $response['expires'] );

				if ( ! empty( $time_diff ) && WEEK_IN_SECONDS < $time_diff ) {
					set_transient( self::$expiration_email_key, true, ( $time_diff - WEEK_IN_SECONDS ) );

					// Allow expiration emails to be sent.
					self::set_expiration_email_allowed( true );
					self::set_before_expiration_email_allowed( true );
				}
			}
		}

		// Format the license expiration date.
		if ( ! empty( $response['expires'] ) ) {
			if ( 'lifetime' === $response['expires'] ) {
				$response['expires_formated'] = self::get_purchase_code_expiration_message( esc_html__( 'Never (lifetime license)', 'pt-tr' ) );
			}
			else {
				$response['expires_formated'] = self::get_purchase_code_expiration_message( self::format_expiration_date( $response['expires'] ) );
			}
		}

		// Send the JSON response to the user.
		wp_send_json_success( $response );
	}


	/**
	 * AJAX function for deactivating the purchase code.
	 */
	public function ajax_deactivate_purchase_code() {
		// Verify if the AJAX call is valid.
		$this->verify_ajax_call();

		// Get theme activation data.
		$pt_theme_activation = self::get_theme_activation_data();

		// Abort the deactivation, if the theme is not currently registered/active.
		if ( ! self::is_theme_registered( $pt_theme_activation ) ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Can\'t deactivate the purchase code, if the theme is not registered.', 'pt-tr' ),
				)
			);
		}

		$response = $this->get_response( 'purchase-code-deactivation' );

		// Deactivate the theme mod setting.
		if ( $response['status'] ) {
			set_theme_mod( 'pt_theme_activation', array( 'status' => false ) );
		}

		// Send the JSON response to the user.
		wp_send_json_success( $response );
	}


	/**
	 * AJAX function for verifying the Envato token.
	 */
	public function ajax_envato_token_verification() {
		// Verify if the AJAX call is valid.
		$this->verify_ajax_call();

		// Get theme activation data.
		$pt_theme_activation = self::get_theme_activation_data();

		// Abort the verification, if the theme is not currently registered/active and is not a TF activation.
		if ( ! $pt_theme_activation['status'] || 'tf' !== $pt_theme_activation['purchase_code_type'] || empty( $pt_theme_activation['purchase_code'] ) ) {
			wp_send_json_error(
				array(
					'status'  => false,
					'message' => esc_html__( 'Can\'t verify the Envato token, if the theme is not registered via a ThemeForest purchase code!', 'pt-tr' ),
				)
			);
		}

		$response = $this->get_response( 'envato-token-verification' );

		// Save the Envato token to the theme activation theme_mod.
		if ( $response['status'] ) {
			$pt_theme_activation['envato_token'] = $response;
			set_theme_mod( 'pt_theme_activation', $pt_theme_activation );
		}

		// Send the JSON response to the user.
		wp_send_json_success( $response );
	}


	/**
	 * AJAX function for checking the purchase code expiration.
	 */
	public function ajax_purchase_code_expiration() {
		// Verify if the AJAX call is valid.
		$this->verify_ajax_call();

		$response = $this->get_response( 'check-purchase-code-expiration' );

		// Save the appropriate data to the theme mod setting.
		if ( $response['status'] && ! empty( $response['expires'] ) ) {
			// Update the expiration date.
			$pt_theme_activation            = self::get_theme_activation_data();
			$pt_theme_activation['expires'] = $response['expires'];
			set_theme_mod( 'pt_theme_activation', $pt_theme_activation );

			// Reset the expiration mail transient.
			if ( 'lifetime' === $response['expires'] ) {
				delete_transient( self::$expiration_email_key );
				set_transient( self::$expiration_email_key, true, 0 );
			}
			else {
				$time_diff = self::get_time_difference_in_seconds( $response['expires'] );

				if ( ! empty( $time_diff ) && WEEK_IN_SECONDS < $time_diff ) {
					set_transient( self::$expiration_email_key, true, ( $time_diff - WEEK_IN_SECONDS ) );

					// Allow expiration emails to be sent.
					self::set_expiration_email_allowed( true );
					self::set_before_expiration_email_allowed( true );
				}
			}
		}

		// Format the license expiration date.
		if ( ! empty( $response['expires'] ) ) {
			if ( 'lifetime' === $response['expires'] ) {
				$response['expires_formated'] = self::get_purchase_code_expiration_message( esc_html__( 'Never (lifetime license)', 'pt-tr' ) );
			}
			else {
				$response['expires_formated'] = self::get_purchase_code_expiration_message( self::format_expiration_date( $response['expires'] ) );
			}
		}

		// Send the JSON response to the user.
		wp_send_json_success( $response );
	}


	/**
	 * Show the admin notice, if the theme is not registered.
	 */
	public function theme_is_not_registered_admin_notice() {
	?>
		<div class="notice notice-error">
			<p></p>
			<p>
			<?php
				printf(
					esc_html__( '%1$sThe theme is not registered!%2$s %3$sPlease register your theme%4$s, to access %1$sTheme Settings%2$s, %1$sOne Click Demo Import%2$s and receive %1$sAutomatic Theme Updates%2$s.', 'pt-tr' ),
					'<b>',
					'</b>',
					'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) ) . '">',
					'</a>'
				);
			?>
			</p>
			<p></p>
		</div>
	<?php
	}


	/**
	 * Show the admin notice, if the purchase code has expired.
	 */
	public function purchase_code_has_expired_admin_notice() {
		$pc_type = self::get_purchase_code_type();

		// Default PT (EDD) text.
		$text = sprintf(
			esc_html__( 'Your theme license key has expired! %1$sAutomatic theme updates%2$s and access to our %1$ssupport system%2$s have been %1$sdisabled%2$s. %3$sPlease renew the license key for %6$s on our shop%5$s. Once the license is renewed, please visit the %4$stheme registration page%5$s and click on the %1$sRefresh expiration date%2$s button.', 'pt-tr' ),
			'<b>',
			'</b>',
			'<a href="https://www.proteusthemes.com/account/" target="_blank">',
			'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) ) . '">',
			'</a>',
			esc_html( $this->theme_args['item_name'] )
		);

		// ThemeForest text.
		if ( 'tf' === $pc_type ) {
			$text = sprintf(
				esc_html__( 'Your theme purchase code has expired! %1$sAutomatic theme updates%2$s and access to our %1$ssupport system%2$s have been %1$sdisabled%2$s. %3$sPlease renew support for %6$s on ThemeForest%5$s. Once the support is renewed, please visit the %4$stheme registration page%5$s and click on the %1$sRefresh expiration date%2$s button.', 'pt-tr' ),
				'<b>',
				'</b>',
				'<a href="https://themeforest.net/downloads" target="_blank">',
				'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) ) . '">',
				'</a>',
				esc_html( $this->theme_args['item_name'] )
			);
		}
	?>
		<div class="notice notice-error">
			<p></p>
			<p>
			<?php echo wp_kses_post( $text ); ?>
			</p>
			<p></p>
		</div>
	<?php
	}


	/**
	 * Disable the customizer Theme Options panel, if the theme is not registered.
	 *
	 * @param obj $wp_customize The WP_Customize_Manager instance object.
	 */
	public function disable_customizer_theme_options( $wp_customize ) {
		// Remove the original theme panel.
		$wp_customize->remove_panel( $this->theme_args['customizer_panel'] );

		// Add the new dummy panel with same title, so we can show the notice to register the theme.
		$wp_customize->add_section( 'pt_dummy_section', array(
			'title'       => esc_html__( '[PT] Theme Options', 'pt-tr' ),
			'priority'    => 10,
		) );

		// Add the new dummy setting (control does not display without a setting).
		$wp_customize->add_setting( 'pt_dummy_notice', array( 'sanitize_callback' => 'strip_tags' ) );

		// Add the new dummy control with the notice.
		$wp_customize->add_control( 'pt_dummy_notice', array(
			'type'        => 'hidden',
			'label'       => esc_html__( 'Theme is not registered!', 'pt-tr' ),
			'section'     => 'pt_dummy_section',
			'description' => sprintf(
				esc_html__( '%5$s%3$sPlease register your theme%4$s, to access these %1$sTheme Options%2$s.%6$s %5$sOnce you register your theme, you will also be able to use %1$sOne Click Demo Import%2$s and receive %1$sAutomatic Theme Updates%2$s.%6$s', 'pt-tr' ),
				'<b>',
				'</b>',
				'<a href="' . admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) . '">',
				'</a>',
				'<p>',
				'</p>'
			),
		) );
	}


	/**
	 * Adds the ProteusThemes Support menu item to the WP customizer.
	 *
	 * @param obj $wp_customize The WP_Customize_Manager instance object.
	 */
	public function add_customizer_theme_support_link( $wp_customize ) {
		$notice = esc_html__( '%3$sPlease register your theme%4$s, to access %1$sOur Support Dashboard%2$s!', 'pt-tr' );

		if ( self::is_theme_registered() ) {
			$notice = esc_html__( 'Visit our %6$sHelp center%4$s, where you can find the theme documentation and support articles, or login to our %5$sSupport dashboard%4$s, to open a new support ticket.', 'pt-tr' );
		}

		$wp_customize->add_section( 'pt_support_section', array(
			'title'       => esc_html__( '[PT] ProteusThemes Support', 'pt-tr' ),
			'priority'    => 11,
		) );

		// Add the new support setting (control does not display without a setting).
		$wp_customize->add_setting( 'pt_support_notice', array( 'sanitize_callback' => 'strip_tags' ) );

		// Add the new support control with the support notice.
		$wp_customize->add_control( 'pt_support_notice', array(
			'type'        => 'hidden',
			'label'       => esc_html__( 'ProteusThemes Support', 'pt-tr' ),
			'section'     => 'pt_support_section',
			'description' => sprintf(
				$notice,
				'<b>',
				'</b>',
				'<a href="' . admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) . '">',
				'</a>',
				'<a href="https://www.proteusthemes.com/account/" target="_blank">',
				'<a href="https://www.proteusthemes.com/help/" target="_blank">'
			),
		) );
	}


	/**
	 * Change the OCDI intro text, if the theme is not registered (before OCDI version 2.0).
	 *
	 * @param  array $demo_imports The predefined demo imports.
	 * @return array
	 */
	public function disable_ocdi_change_intro_text( $default_text ) {
		$message = sprintf(
			esc_html__( '%5$sThe One Click Demo Import for this theme is disabled, because the theme is not registered!%7$s%3$sPlease register your theme%4$s, to access the %1$sOne Click Demo Import%2$s.%6$s', 'pt-tr' ),
			'<b>',
			'</b>',
			'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) ) . '">',
			'</a>',
			'<p class="about-description" style="color: red;">',
			'</p>',
			'<br>'
		);

		return $message . '<hr>' . $default_text;
	}


	/**
	 * Disable OCDI plugin page callback.
	 */
	public function disable_ocdi_plugin_page_callback() {
		return array( $this, 'disable_ocdi_plugin_page' );
	}


	/**
	 * Disable the OCDI plugin page - add custom content to force theme registration (after OCDI version 2.0).
	 */
	public function disable_ocdi_plugin_page() {
		?>
			<div class="ocdi  wrap  about-wrap">
				<h1 class="ocdi__title  dashicons-before  dashicons-upload"><?php esc_html_e( 'One Click Demo Import', 'pt-tr' ); ?></h1>
				<p class="about-description" style="color: red;">
					<?php
						printf(
							esc_html__( 'The One Click Demo Import for this theme is disabled, because the theme is not registered!%5$s%1$sPlease register your theme%2$s, to access the %3$sOne Click Demo Import%4$s.', 'pt-tr' ),
							'<a href="' . esc_url( admin_url( 'themes.php?page=' . self::THEME_PAGE_SLUG ) ) . '">',
							'</a>',
							'<b>',
							'</b>',
							'<br>'
						);
					?>
				</p>
				<p>
					<?php esc_html_e( 'Once you register the theme, come back to this page and you will be able to import the demo data!', 'pt-tr' ); ?>
				</p>
			</div>
		<?php
	}


	/**
	 * Maybe send an email to the admin of this site, that the theme license will or has expired.
	 */
	public function maybe_send_expiration_email() {
		// Skip sending email, if the constant is set and is true.
		if ( defined( 'PT_TR_DONT_SEND_EMAILS' ) && PT_TR_DONT_SEND_EMAILS ) {
			return false;
		}

		// Skip everything, if the transient for expiration email is set.
		if ( get_transient( self::$expiration_email_key ) ) {
			return false;
		}

		$expiration_date = self::get_purchase_code_expiration();

		// Skip, if the expiration date is not set.
		if ( empty( $expiration_date ) ) {
			return false;
		}

		// Skip, if a "lifetime" license is being used.
		if ( 'lifetime' === $expiration_date ) {
			return false;
		}

		$email_address = get_option('admin_email');

		// Skip, if the admin email is not available.
		if ( empty( $email_address ) ) {
			return false;
		}

		$has_expired = self::has_purchase_code_expired();

		// Check different scenarios, when not to send the email.
		if ( $has_expired ) {
			// The license has expired, so set the transient with no expiration date.
			delete_transient( self::$expiration_email_key );
			set_transient( self::$expiration_email_key, true, 0 );

			// Skip, if the expiration mail was already sent.
			if ( ! self::is_expiration_email_allowed() ) {
				return false;
			}

			// The expiration email will be sent, so we have to prevent any further emails of this kind.
			self::set_expiration_email_allowed( false );
		}
		else {
			return false;
		}

		// Get email content (subject, message, headers).
		$email_content = $this->get_expiration_email( $has_expired, $email_address );

		// Send the email.
		wp_mail( $email_address, $email_content['subject'], $email_content['message'], $email_content['headers'] );
	}

	/**
	 * Get the contents of the expiration mail, depending on the state of the license.
	 * Is the license about to expire, or did it already expire.
	 *
	 * @param  boolean $has_expired Has the license expired?
	 * @param  string $email        The email address of this site admin user.
	 * @return array                The subject, message and the header strings.
	 */
	private function get_expiration_email( $has_expired, $email ) {
		$site_name = get_bloginfo( 'name' );
		$site_name = ! empty( $site_name ) ? esc_html( $site_name ) : esc_html__( 'From your WordPress site', 'pt-tr' );

		$headers  = "From: {$site_name} <{$email}>\r\n";
		$headers .= "Reply-To: {$email}\r\n";
		$headers .= "Content-Type: text/plain; charset=utf-8\r\n";

		$pc_type = self::get_purchase_code_type();

		// PT (EDD) expiration subject and message one week before expiration.
		$subject = sprintf( esc_html__( 'Your license key for the %s theme is about to expire!', 'pt-tr' ), esc_html( $this->theme_args['item_name'] ) );
		$message = sprintf(
			esc_html__( "Hi,\n\nYour license key for the %1\$s theme on your %2\$s website is about to expire.\n\nOnce the license expires, you will not be able to access our support system and the automatic %1\$s theme updates on your site will be disabled.\n\nTo renew your license, please login to our Shop and renew the appropriate license:\n%4\$s\n\nYour expiring item: %1\$s.\n\nYour expiring license key: %3\$s.\n", 'pt-tr' ),
			esc_html( $this->theme_args['item_name'] ),
			esc_url( get_site_url() ),
			esc_html( self::get_purchase_code() ),
			'https://www.proteusthemes.com/account/'
		);

		// ThemeForest expiration subject and message one week before expiration.
		if ( 'tf' === $pc_type ) {
			$subject = sprintf( esc_html__( 'Your support period for the %s theme is about to expire!', 'pt-tr' ), esc_html( $this->theme_args['item_name'] ) );
			$message = sprintf(
				esc_html__( "Hi,\n\nYour support period for the %1\$s theme on your %2\$s website is about to expire.\n\nOnce support period expires, you will not be able to access our support system and the automatic %1\$s theme updates on your site will be disabled.\n\nTo renew your support, please login to the ThemeForest download section and renew the expired support:\n%4\$s\n\nYour expiring item: %1\$s.\n\nYour expiring purchase code: %3\$s.\n", 'pt-tr' ),
				esc_html( $this->theme_args['item_name'] ),
				esc_url( get_site_url() ),
				esc_html( self::get_purchase_code() ),
				'https://themeforest.net/downloads'
			);
		}

		// Create different subject and message, if the purchase code has already expired.
		if ( $has_expired ) {
			// PT (EDD) expiration subject and message after expiration.
			$subject = sprintf( esc_html__( 'Your license key for the %s theme has expired!', 'pt-tr' ), esc_html( $this->theme_args['item_name'] ) );
			$message = sprintf(
				esc_html__( "Hi,\n\nYour license key for the %1\$s theme on your %2\$s website has expired.\n\nYou will not be able to access our support system and the automatic %1\$s theme updates on your site have been disabled.\n\nTo renew your license, please login to our Shop and renew the appropriate license:\n%4\$s\n\nYour expiring item: %1\$s.\n\nYour expiring license key: %3\$s.\n", 'pt-tr' ),
				esc_html( $this->theme_args['item_name'] ),
				esc_url( get_site_url() ),
				esc_html( self::get_purchase_code() ),
				'https://www.proteusthemes.com/account/'
			);

			// ThemeForest expiration subject and message after expiration.
			if ( 'tf' === $pc_type ) {
				$subject = sprintf( esc_html__( 'Your support period for the %s theme has expired!', 'pt-tr' ), esc_html( $this->theme_args['item_name'] ) );
				$message = sprintf(
					esc_html__( "Hi,\n\nYour support period for the %1\$s theme on your %2\$s website has expired.\n\nYou will not be able to access our support system and the automatic %1\$s theme updates on your site have been disabled.\n\nTo renew your support, please login to the ThemeForest download section and renew the expired support:\n%4\$s\n\nYour expiring item: %1\$s.\n\nYour expiring purchase code: %3\$s.\n", 'pt-tr' ),
					esc_html( $this->theme_args['item_name'] ),
					esc_url( get_site_url() ),
					esc_html( self::get_purchase_code() ),
					'https://themeforest.net/downloads'
				);
			}
		}

		return compact( 'headers', 'subject', 'message' );
	}


	/**
	 * Get the Envato token data (code and message), if set and valid.
	 *
	 * @param  array  $registration_options The saved activation/registration data.
	 * @return array
	 */
	public static function get_envato_token_data( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		if (
			isset( $pt_theme_activation['envato_token'] ) &&
			isset( $pt_theme_activation['envato_token']['status'] ) &&
			! empty( $pt_theme_activation['envato_token']['status'] ) &&
			isset( $pt_theme_activation['envato_token']['token'] ) &&
			! empty( $pt_theme_activation['envato_token']['token'] ) ) {
				return array(
					'token'   => $pt_theme_activation['envato_token']['token'],
					'message' => isset( $pt_theme_activation['envato_token']['message'] ) ? $pt_theme_activation['envato_token']['message'] : '',
				);
		}

		return array(
			'token'   => false,
			'message' => '',
		);
	}


	/**
	 * Get the message from theme activation.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_theme_activation_message( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		return isset( $pt_theme_activation['message'] ) ? $pt_theme_activation['message'] : '';
	}


	/**
	 * Get the purchase code type from the saved activation data.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_purchase_code_type( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		return isset( $pt_theme_activation['purchase_code_type'] ) ? $pt_theme_activation['purchase_code_type'] : 'xx';
	}


	/**
	 * Get the purchase code from the saved activation data.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_purchase_code( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		return isset( $pt_theme_activation['purchase_code'] ) ? $pt_theme_activation['purchase_code'] : '';
	}


	/**
	 * Get the purchase code expiration date from the saved activation data.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_purchase_code_expiration( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		if ( empty( $pt_theme_activation['expires'] ) ) {
			return '';
		}

		return $pt_theme_activation['expires'];
	}


	/**
	 * Get the formated purchase code expiration date from the saved activation data.
	 * Or a text for "lifetime" licenses.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_formated_purchase_code_expiration( $registration_options = array() ) {
		$date = self::get_purchase_code_expiration( $registration_options );

		if ( empty( $date ) ) {
			return '';
		}

		if ( 'lifetime' === $date ) {
			return esc_html__( 'Never (lifetime license)', 'pt-tr' );
		}

		return self::format_expiration_date( $date );
	}


	/**
	 * Format the date to the proper format.
	 *
	 * Example: February 10, 2017
	 *
	 * @param  string $date_string The raw date and time string
	 * @return string              Formated date string.
	 */
	public static function format_expiration_date( $date_string ) {
		$date = new \DateTime( $date_string );

		return $date->format( 'F j, Y' );
	}


	/**
	 * Get the purchase code expiration message.
	 *
	 * @param  string $formated_date Formated date string.
	 * @return string                The full message.
	 */
	public static function get_purchase_code_expiration_message( $formated_date ) {
		return sprintf(
			esc_html__( 'License expires on: %1$s. %2$s Your website will continue to work when the license expires. For continued theme updates and access to our industry-leading support you will have an option to extend the license before it expires. No worries, we will remind you when the time is due. Go create something awesome now!', 'pt-tr' ),
			esc_html( $formated_date ),
			'<br>'
		);
	}


	/**
	 * Check if the license code has expired (the 'expires' date parameter).
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return boolean
	 */
	public static function has_purchase_code_expired( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		// If the theme is not registered, we don't need to check for expiration.
		if ( ! self::is_theme_registered( $pt_theme_activation ) ) {
			return false;
		}

		// If there is no data for license expiration date, then we can say the purchase code has expired.
		if ( empty( $pt_theme_activation['expires'] ) ) {
			return true;
		}

		// If the license is a "lifetime" license, then it never expires.
		if ( 'lifetime' === $pt_theme_activation['expires'] ) {
			return false;
		}

		// Was the expiration date reached/passed?
		$now             = new \DateTime();
		$expiration_date = new \DateTime( $pt_theme_activation['expires'] );

		$is_expired = $now > $expiration_date;

		if ( ! $is_expired ) {
			return false;
		}

		// If the purchase code has expired and this in fact is a ThemeForest purchase code, then skip extra verification.
		if ( $is_expired && 'tf' === self::get_purchase_code_type( $pt_theme_activation ) ) {
			return true;
		}

		// Recheck the expiration date on our PT Shop, if the license has been renewed/extended.
		if ( $is_expired && ! get_transient( self::$expiration_recheck_key ) ) {
			self::recheck_license_expiration();

			set_transient( self::$expiration_recheck_key, true, 12 * HOUR_IN_SECONDS );

			$new_reg_data = self::get_theme_activation_data();

			$new_expiration_date = new \DateTime( $new_reg_data['expires'] );

			$is_expired = $now > $new_expiration_date;
		}

		return $is_expired;
	}


	/**
	 * Get the activation email from the saved activation data.
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return string
	 */
	public static function get_activation_email( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		if ( empty( $pt_theme_activation['email'] ) ) {
			return '';
		}

		return $pt_theme_activation['email'];
	}


	/**
	 * Is the theme registered or is the temporary registration enabled?
	 *
	 * Should we allow access to OCDI and customizer theme options?
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return boolean
	 */
	public static function is_theme_access_allowed( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		// Check if the temporary registration is enabled.
		if ( self::is_theme_temporary_registered() ) {
			return true;
		}

		// Check if it's not registered.
		if ( self::is_theme_registered( $pt_theme_activation ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Is the theme registered?
	 *
	 * @param  array $registration_options The saved activation/registration data.
	 * @return boolean
	 */
	public static function is_theme_registered( $registration_options = array() ) {
		// Get theme activation data.
		$pt_theme_activation = $registration_options;

		if ( empty( $pt_theme_activation ) ) {
			$pt_theme_activation = self::get_theme_activation_data();
		}

		// Check if it's not registered.
		if ( empty( $pt_theme_activation ) || ! isset( $pt_theme_activation['status'] ) || ! $pt_theme_activation['status'] ) {
			return false;
		}

		return true;
	}


	/**
	 * Is the theme temporary registered?
	 *
	 * @return boolean
	 */
	public static function is_theme_temporary_registered() {
		return get_transient( self::$temp_access_key );
	}


	/**
	 * Get the theme activation data.
	 * Registration/activation status and all other related data.
	 *
	 * @return array The registration data.
	 */
	public static function get_theme_activation_data() {
		return get_theme_mod( 'pt_theme_activation', array() );
	}


	/**
	 * Is the expiration email allowed?
	 *
	 * @return boolean If the email is allowed.
	 */
	public static function is_expiration_email_allowed() {
		return get_theme_mod( 'pt_tr_expiration_email', true );
	}


	/**
	 * Is the before expiration email allowed (1 week before expiration)?
	 *
	 * @return boolean If the email is allowed.
	 */
	public static function is_before_expiration_email_allowed() {
		return get_theme_mod( 'pt_tr_before_expiration_email', true );
	}

	/**
	 * Set the expiration email allowed option.
	 *
	 * @param  boolean The value to be set (true or false).
	 */
	public static function set_expiration_email_allowed( $flag ) {
		set_theme_mod( 'pt_tr_expiration_email', $flag );
	}


	/**
	 * Set the before expiration email allowed option (1 week before expiration).
	 *
	 * @param  boolean The value to be set (true or false).
	 */
	public static function set_before_expiration_email_allowed( $flag ) {
		set_theme_mod( 'pt_tr_before_expiration_email', $flag );
	}


	/**
	 * Get the response form the PT sites, depending on which action you pass it.
	 *
	 * @param  string $action The action to fire on the PT site.
	 * @return array          Array with the response data or error message.
	 */
	private function get_response( $action ) {
		$purchase_code = $_POST['fields']['purchase_code'];
		$purchase_code = empty( $purchase_code ) ? self::get_purchase_code() : $purchase_code;

		// Prepare data for the POST request to our server.
		$data = array(
			'purchase_code' => sanitize_key( $purchase_code ),
			'email'         => sanitize_email( $_POST['fields']['email'] ),
			'subscribe'     => ! empty( $_POST['fields']['subscribe'] ),
			'envato_token'  => sanitize_text_field( $_POST['fields']['envato_token'] ),
			'site_url'      => get_site_url(),
			'tf_item_id'    => $this->theme_args['tf_item_id'],
			'item_name'     => $this->theme_args['item_name'],
			'item_id'       => $this->theme_args['item_id'],
		);

		// Send POST request to our server.
		$request_data = array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => json_encode( $data ),
		);

		$request       = wp_remote_post( self::ENDPOINT_ROOT . $action, $request_data );
		$response_code = wp_remote_retrieve_response_code( $request );

		if ( is_wp_error( $request ) || 200 !== $response_code  ) {
			$error = '';

			if ( is_wp_error( $request ) ) {
				$error = $request->get_error_message();
			} else {
				$error = esc_html__( 'There was an error with the request to our server!', 'pt-tr' );
			}

			return array(
				'status'      => false,
				'temp_access' => true,
				'message'     => $error,
				'code'        => $response_code,
			);
		}

		// Decode the JSON response from the server and prepare the final response data.
		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		$response['code'] = $response_code;

		return $response;
	}


	/**
	 * Verify if the AJAX call is valid.
	 * Check for nonce and current_user_can.
	 */
	private function verify_ajax_call() {
		// Check nonce.
		if ( ! check_ajax_referer( 'pt-tr-ajax-verification', 'security', false ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The WP nonce passed via the AJAX call was not valid!', 'pt-tr' ),
				)
			);
		}

		// Check if user has the WP capability needed.
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Your WP user role isn\'t high enough. You don\'t have the capability to switch themes.', 'pt-tr' ),
				)
			);
		}
	}

	/**
	 * Get the difference from now till $time_string in seconds.
	 * We only return a positive number.
	 *
	 * @param  string $time_string The date and time in a string.
	 * @return mixed               Integer of seconds or false, the string is not in the correct format.
	 */
	public static function get_time_difference_in_seconds( $time_string ) {
		$now = time();
		$till = strtotime( $time_string );

		if ( empty( $till ) ) {
			return false;
		}

		return $till - $now;
	}

	/**
	 * Setup the Theme Updates class.
	 */
	private function setup_theme_updater() {
		$this->theme_updater = new ThemeUpdates( $this->theme_args );
	}

	/**
	 * Recheck (and set if new date is available) the expiration date of the current licence.
	 */
	public static function recheck_license_expiration() {
		$purchase_code = self::get_purchase_code();

		if ( empty( $purchase_code ) ) {
			return false;
		}

		// Prepare data for the POST request to our server.
		$data = array(
			'purchase_code' => sanitize_key( $purchase_code ),
			'site_url'      => get_site_url(),
		);

		// Send POST request to our server.
		$request_data = array(
			'timeout' => 10,
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => json_encode( $data ),
		);

		$request       = wp_remote_post( self::ENDPOINT_ROOT . 'check-purchase-code-expiration', $request_data );
		$response_code = wp_remote_retrieve_response_code( $request );

		if ( is_wp_error( $request ) || 200 !== $response_code  ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ), true );

		// Save the appropriate data to the theme mod setting.
		if ( $response['status'] && ! empty( $response['expires'] ) ) {
			// Update the expiration date.
			$pt_theme_activation            = self::get_theme_activation_data();
			$pt_theme_activation['expires'] = $response['expires'];
			set_theme_mod( 'pt_theme_activation', $pt_theme_activation );

			// Reset the expiration mail transient.
			if ( 'lifetime' === $response['expires'] ) {
				delete_transient( self::$expiration_email_key );
				set_transient( self::$expiration_email_key, true, 0 );
			}
			else {
				$time_diff = self::get_time_difference_in_seconds( $response['expires'] );

				if ( ! empty( $time_diff ) && WEEK_IN_SECONDS < $time_diff ) {
					set_transient( self::$expiration_email_key, true, ( $time_diff - WEEK_IN_SECONDS ) );

					// Allow expiration emails to be sent.
					self::set_expiration_email_allowed( true );
					self::set_before_expiration_email_allowed( true );
				}
			}
		}
	}
}
