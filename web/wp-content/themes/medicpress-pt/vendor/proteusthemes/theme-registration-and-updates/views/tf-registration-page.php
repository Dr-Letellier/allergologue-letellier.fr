<?php
/**
 * The theme registration view for TF.
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
		<?php printf( esc_html__( 'To use the full potential of this theme, you will have to register it with your purchase code. After successful registration, you will be able to access %1$sTheme Options%2$s, %1$sImport the Demo Data%2$s and receive %1$sAutomatic Updates%2$s.', 'pt-tr' ), '<b>', '</b>' ); ?>
	</div>

	<div class="pt-tr__settings-panel">

		<div class="pt-tr__purchase-code-container">
			<label for="pt-purchase-code"><?php esc_html_e( 'Purchase code:', 'pt-tr' ); ?></label>
			<a href="#pt-instructions" class="pt-purchase-code-explanation-icon"><span class="dashicons dashicons-warning"></span></a>
			<input type="text" id="pt-purchase-code" name="pt-purchase-code" class="widefat" value="<?php echo esc_attr( $activation_pc ); ?>" placeholder="<?php esc_html_e( 'Purchase code, ie. 34813r73-838c-466d-bbbc-aa3aaaaaa273', 'pt-tr' ); ?>"<?php echo empty( $activation_status ) ? '' : ' disabled'; ?>>
			<label for="pt-activation-email"><?php esc_html_e( 'Email:', 'pt-tr' ); ?></label>
			<input type="email" id="pt-activation-email" name="pt-activation-email" class="widefat" value="<?php echo esc_attr( $activation_email ); ?>"<?php echo empty( $activation_status ) ? '' : ' disabled'; ?>>
			<?php if ( ! $activation_status ) : ?>
				<input type="checkbox" id="pt-subscribe-newsletter" name="pt-subscribe-newsletter" checked>
				<label for="pt-subscribe-newsletter">
					<?php
					printf(
						esc_html__( 'I want to be notified about theme updates and new theme releases by ProteusThemes! (%1$shere is an example%2$s)', 'pt-tr' ),
						'<a href="http://us4.campaign-archive2.com/?u=ea0786485977f5ec8c9283d5c&id=d7a1291f56" target="_blank">',
						'</a>'
					);
					?>
				</label>
			<?php endif; ?>
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

		<div class="pt-tr__envato-token-container  js-envato-token-container"<?php echo 'tf' !== $activation_pc_type ? ' style="display:none;"' : ''; ?>>

			<hr>
			<h3><?php esc_html_e( 'Automatic theme updates', 'pt-tr' ); ?></h3>
			<p>
				<?php esc_html_e( 'If you want to enable automatic theme updates, you have to generate an Envato token and verify it. More details in the instructions section below.', 'pt-tr' ); ?>
			</p>

			<label for="pt-envato-token"><?php esc_html_e( 'Please enter your Envato token and click on the "Save" button to enable automatic updates:', 'pt-tr' ); ?></label>
			<input type="text" id="pt-envato-token" name="pt-envato-token" class="widefat" value="<?php echo esc_attr( $envato_token_data['token'] ); ?>" placeholder="<?php esc_html_e( 'Envato token', 'pt-tr' ); ?>">
			<p class="pt-tr__envato-token-message  js-et-message">
				<?php echo esc_html( $envato_token_data['message'] ); ?>
			</p>
			<button class="button  button-hero  button-primary  pt-tr__activate-button  js-et-verify-button"><?php esc_html_e( 'Save', 'pt-tr' ); ?></button>
		</div>

	</div>

	<hr>

	<div id="pt-instructions" class="pt-tr__instructions-container">
		<h3><?php esc_html_e( 'Instructions', 'pt-tr' ); ?></h3>

		<h4><?php esc_html_e( 'Where can I find the purchase code?', 'pt-tr' ); ?></h4>
		<ol>
			<li><?php printf( esc_html__( 'Go to your %1$sThemeForest download page%2$s and login,', 'pt-tr' ), '<a href="https://themeforest.net/downloads" target="_blank">', '</a>' ); ?></li>
			<li><?php printf( esc_html__( 'You will see a list of all purchased items, that you made on ThemeForest. Find our %s item and click on the "Download" button, then click on the "License certificates & purchase code (text)",', 'pt-tr' ), '<b>' . esc_html( $this->theme_args['item_name'] ) . '</b>' ); ?></li>
			<li><?php esc_html_e( 'That will download a small text file, which you should open. In this text file, you will find the purchase code under the "Item Purchase Code:" title,', 'pt-tr' ); ?></li>
		</ol>
		<p>
			<?php printf( esc_html__( 'If you have trouble finding this purchase code, please take a look at this detailed support article on %1$sWhere to find the ThemeForest purchase code%2$s.', 'pt-tr' ), '<a href="https://support.proteusthemes.com/hc/en-us/articles/213593725-Where-can-I-find-the-theme-purchase-code-" target="_blank">', '</a>' ); ?>
		</p>

		<div class="pt-tr-notice  pt-tr-notice-info">
			<p>
				<?php printf( esc_html__( 'Don\'t have a purchase code yet? %1$sBuy our %2$s theme here%3$s!', 'pt-tr' ), '<b><a href="https://www.proteusthemes.com/wordpress-themes/' . sanitize_title( $this->theme_args['item_name'] ) . '" target="_blank">',esc_html( $this->theme_args['item_name'] ), '</a></b>' ); ?>
			</p>
		</div>

		<h4><?php esc_html_e( 'How do I generate the Envato token for automatic updates?', 'pt-tr' ); ?></h4>
		<ol>
			<li><?php printf( esc_html__( 'Go to %1$sCreate an Envato token%2$s and login with your themeforest details,', 'pt-tr' ), '<a href="https://build.envato.com/create-token/?purchase:download=t" target="_blank">', '</a>' ); ?></li>
			<li><?php printf( esc_html__( 'Input the "Token name", for example: %s - automatic updates,', 'pt-tr' ), esc_html( $this->theme_args['item_name'] ) ); ?></li>
			<li><?php esc_html_e( 'The checked permissions should be "View and search Envato sites" and "Download your purchased items"', 'pt-tr' ); ?></li>
			<li><?php esc_html_e( 'Finally click on the "Create Token" button', 'pt-tr' ); ?></li>
			<li><?php esc_html_e( 'An Envato token will be generated for you, that you should use in the field above.', 'pt-tr' ); ?></li>
		</ol>
	</div>

</div>
