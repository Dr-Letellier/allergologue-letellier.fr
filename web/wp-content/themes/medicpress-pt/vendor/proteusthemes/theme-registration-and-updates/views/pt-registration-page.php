<?php
/**
 * The theme registration view for EDD.
 */
?>

<div class="wrap  about-wrap  pt-tr">

	<h1>
		<img src="<?php echo get_template_directory_uri() . '/vendor/proteusthemes/theme-registration-and-updates/assets/images/pt.svg' ?>" alt="<?php esc_attr_e( 'PT logo', 'pt-tr' ); ?>" width="50" height="50">
		<?php esc_html_e( 'Theme Registration', 'pt-tr' ); ?>
	</h1>

	<div class="about-text">
		<?php printf( esc_html__( 'Yey, you are using our %s theme, therefore, You are awesome!', 'pt-tr' ), '<b><a href="https://www.proteusthemes.com/wordpress-themes/' . sanitize_title( $this->theme_args['item_name'] ) . '" target="_blank">' . esc_html( $this->theme_args['item_name'] ) . '</a></b>' ); ?>
	</div>

	<div class="about-text">
		<?php printf( esc_html__( 'To use the full potential of this theme, you will have to register it with your license key. After successful registration, you will be able to access %1$sTheme Options%2$s, %1$sImport the Demo Data%2$s and receive %1$sAutomatic Updates%2$s.', 'pt-tr' ), '<b>', '</b>' ); ?>
	</div>

	<div class="pt-tr__settings-panel">

		<div class="pt-tr__purchase-code-container">
			<label for="pt-purchase-code"><?php esc_html_e( 'License key:', 'pt-tr' ); ?></label>
			<a href="#pt-instructions" class="pt-purchase-code-explanation-icon"><span class="dashicons dashicons-warning"></span></a>
			<input type="text" id="pt-purchase-code" name="pt-purchase-code" class="widefat" value="<?php echo esc_attr( $activation_pc ); ?>" placeholder="<?php esc_html_e( 'License key, ie. 3e1fffff58adaaaa3d0ceea2zbaaccg4', 'pt-tr' ); ?>"<?php echo empty( $activation_status ) ? '' : ' disabled'; ?>>
			<p class="pt-tr__purchase-code-message  js-pc-message<?php echo empty( $activation_status ) ? '' : '  pt-success-message'; ?>">
				<?php echo esc_html( $activation_message ); ?>
			</p>
			<p class="pt-tr__purchase-code-expiration  js-pc-expiration">
				<?php
					if ( ! empty( $activation_expiration ) ) {
						echo wp_kses_post( $activation_expiration_message );
					}
				?>
			</p>
			<button class="button  button-hero  button-primary  pt-tr__activate-button  js-pc-activate-button"<?php echo $activation_status ? ' style="display:none;"' : ''; ?>><?php esc_html_e( 'Activate', 'pt-tr' ); ?></button>
			<button class="button  button-hero  pt-tr__deactivate-button  js-pc-deactivate-button"<?php echo ! $activation_status ? ' style="display:none;"' : ''; ?>><?php esc_html_e( 'Deactivate', 'pt-tr' ); ?></button>
			<button class="button  button-hero  pt-tr__expiration-button  js-expiration-button"<?php echo ! $activation_status ? ' style="display:none;"' : ''; ?>><?php esc_html_e( 'Refresh expiration date', 'pt-tr' ); ?></button>
		</div>

	</div>

	<hr>

	<div id="pt-instructions" class="pt-tr__instructions-container">
		<h4><?php esc_html_e( 'Where can I find the license key?', 'pt-tr' ); ?></h4>
		<ol>
			<li><?php printf( esc_html__( 'Go to our %1$sProteusThemes dashboard%2$s and login,', 'pt-tr' ), '<a href="https://www.proteusthemes.com/account/" target="_blank">', '</a>' ); ?></li>
			<li><?php printf( esc_html__( 'You will see a list of all purchased themes. Locate the theme you want to register (%s in your case),', 'pt-tr' ), '<b>' . esc_html( $this->theme_args['item_name'] ) . '</b>' ); ?></li>
			<li><?php printf( esc_html__( 'Copy the %s license key.', 'pt-tr' ), '<b>' . esc_html( $this->theme_args['item_name'] ) . '</b>' ); ?></li>
		</ol>
	</div>

	<div class="pt-tr-notice  pt-tr-notice-info">
		<p>
			<?php printf( esc_html__( 'Don\'t have a license key yet? %1$sBuy our %2$s theme here%3$s!', 'pt-tr' ), '<b><a href="https://www.proteusthemes.com/wordpress-themes/' . sanitize_title( $this->theme_args['item_name'] ) . '" target="_blank">',esc_html( $this->theme_args['item_name'] ), '</a></b>' ); ?>
		</p>
	</div>

</div>
