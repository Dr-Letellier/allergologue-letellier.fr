<?php
/**
 * Add the link to documentation under Appearance in the wp-admin
 *
 * @package medicpress-pt
 */

if ( ! function_exists( 'medicpress_add_docs_page' ) ) {

	/**
	 * Creates the Documentation page under the Appearance menu in wp-admin.
	 */
	function medicpress_add_docs_page() {
		add_theme_page(
			esc_html__( 'Documentation', 'medicpress-pt' ),
			esc_html__( 'Documentation', 'medicpress-pt' ),
			'',
			'proteusthemes-theme-docs',
			'medicpress_docs_page_output'
		);
	}
	add_action( 'admin_menu', 'medicpress_add_docs_page' );

	/**
	 * This is the callback function for the Documentation page above.
	 * This is the output of the Documentation page.
	 */
	function medicpress_docs_page_output() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Documentation', 'medicpress-pt' ); ?></h2>

			<p>
				<strong><a href="https://www.proteusthemes.com/docs/medicpress/" class="button button-primary " target="_blank"><?php esc_html_e( 'Click here to see online documentation of the theme!', 'medicpress-pt' ); ?></a></strong>
			</p>
		</div>
		<?php
	}
}
