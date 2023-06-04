<?php
/**
 * Filters for MedicPress WP theme
 *
 * @package medicpress-pt
 */

/**
 * MedicPressFilters class with filter hooks
 */
class MedicPressFilters {

	/**
	 * Runs on class initialization. Adds filters and actions.
	 */
	function __construct() {

		// ProteusWidgets.
		add_filter( 'pw/widget_views_path', array( $this, 'set_widgets_view_path' ) );
		add_filter( 'pw/testimonial_widget', array( $this, 'set_testimonial_settings' ) );
		add_filter( 'pw/featured_page_widget_page_box_image_size', array( $this, 'set_page_box_image_size' ) );
		add_filter( 'pw/featured_page_widget_inline_image_size', array( $this, 'set_inline_image_size' ) );
		add_filter( 'pw/latest_news_widget_image_size', array( $this, 'set_latest_news_image_size' ) );
		add_filter( 'pw/featured_page_excerpt_lengths', array( $this, 'set_featured_page_excerpt_lengths' ) );
		add_filter( 'pw/social_icons_fa_icons_list', array( $this, 'social_icons_fa_icons_list' ) );
		add_filter( 'pw/default_social_icon', array( $this, 'default_social_icon' ) );
		add_filter( 'pw/featured_page_fields', array( $this, 'set_featured_page_fields' ) );
		add_filter( 'pw/latest_news_texts', array( $this, 'set_latest_news_texts' ) );
		add_filter( 'pw/person_profile_widget_settings', array( $this, 'set_person_profile_widget_settings' ) );
		add_filter( 'pw/icons_input_field_notice', array( $this, 'set_icons_input_field_notice' ) );
		add_filter( 'pw/latest_news_fields', array( $this, 'set_latest_news_fields' ) );

		// Custom tag font size.
		add_filter( 'widget_tag_cloud_args', array( $this, 'set_tag_cloud_sizes' ) );

		// Custom text after excerpt.
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

		// Footer widgets with dynamic layouts.
		add_filter( 'dynamic_sidebar_params', array( $this, 'footer_widgets_params' ), 9, 1 );

		// Google fonts.
		add_filter( 'medicpress_pre_google_web_fonts', array( $this, 'additional_fonts' ) );
		add_filter( 'medicpress_subsets_google_web_fonts', array( $this, 'subsets_google_web_fonts' ) );

		// Page builder.
		add_filter( 'siteorigin_panels_settings_defaults', array( $this, 'siteorigin_panels_settings_defaults' ) );
		add_filter( 'siteorigin_panels_widgets', array( $this, 'add_icons_to_page_builder_for_pw_widgets' ), 15 );
		add_filter( 'siteorigin_panels_widget_dialog_tabs', array( $this, 'siteorigin_panels_add_widgets_dialog_tabs' ), 15 );

		// Embeds.
		add_filter( 'embed_oembed_html', array( $this, 'embed_oembed_html' ), 10, 1 );
		add_filter( 'oembed_result', array( $this, 'oembed_result' ), 10, 3 );
		add_filter( 'oembed_fetch_url', array( $this, 'oembed_fetch_url' ), 10, 3 );

		// Protocols.
		add_filter( 'kses_allowed_protocols', array( $this, 'kses_allowed_protocols' ) );

		// <body> and post class
		add_filter( 'body_class', array( $this, 'body_class' ), 10, 1 );
		add_filter( 'post_class', array( $this, 'post_class' ), 10, 1 );

		// One Click Demo Import plugin.
		add_filter( 'pt-ocdi/import_files', array( $this, 'ocdi_import_files' ) );
		add_action( 'pt-ocdi/after_import', array( $this, 'ocdi_after_import_setup' ) );
		add_filter( 'pt-ocdi/message_after_file_fetching_error', array( $this, 'ocdi_message_after_file_fetching_error' ) );

		// Shortcodes plugin.
		add_filter( 'pt/convert_widget_text', '__return_true' );

		// Sticky menu.
		add_filter( 'pt-sticky-menu/theme_panel', array( $this, 'pt_sticky_menu_theme_panel' ) );
		add_filter( 'pt-sticky-menu/cta_button_class', array( $this, 'pt_sticky_menu_cta_button_class' ) );

		// Remove references to SiteOrigin Premium.
		add_filter( 'siteorigin_premium_upgrade_teaser', '__return_false' );

		// Special dropdown menu.
		add_filter( 'wp_nav_menu_objects', array( $this, 'add_images_to_special_submenu' ) );
	}


	/**
	 * Filter the Testimonial widget fields that the MedicPress theme will need from ProteusWidgets - Tesimonial widget.
	 *
	 * @param array $attr default attributes.
	 * @return array
	 */
	function set_testimonial_settings( $attr ) {
		$attr['number_of_testimonial_per_slide'] = 1;
		$attr['rating']                          = false;
		$attr['author_description']              = true;
		$attr['author_avatar']                   = false;
		$attr['bootstrap_version']               = 4;
		return $attr;
	}

	/**
	 * Custom tag font size.
	 *
	 * @param array $args default arguments.
	 * @return array
	 */
	function set_tag_cloud_sizes( $args ) {
		$args['smallest'] = 12;
		$args['largest']  = 12;
		$args['unit'] = 'px';
		return $args;
	}


	/**
	 * Custom text after excerpt.
	 *
	 * @param array $more default more value.
	 * @return array
	 */
	function excerpt_more( $more ) {
		return ' &hellip;';
	}


	/**
	 * Filter the dynamic sidebars and alter the BS col classes for the footer widgets.
	 *
	 * @param  array $params parameters of the sidebar.
	 * @return array
	 */
	function footer_widgets_params( $params ) {
		static $counter              = 0;
		static $first_row            = true;
		$footer_widgets_layout_array = MedicPressHelpers::footer_widgets_layout_array();

		if ( 'footer-widgets' === $params[0]['id'] ) {
			// 'before_widget' contains __col-num__, see inc/theme-sidebars.php.
			$params[0]['before_widget'] = str_replace( '__col-num__', $footer_widgets_layout_array[ $counter ], $params[0]['before_widget'] );

			// First widget in the any non-first row.
			if ( false === $first_row && 0 === $counter ) {
				$params[0]['before_widget'] = '</div><div class="row">' . $params[0]['before_widget'];
			}

			$counter++;
		}

		end( $footer_widgets_layout_array );
		if ( $counter > key( $footer_widgets_layout_array ) ) {
			$counter   = 0;
			$first_row = false;
		}

		return $params;
	}


	/**
	 * Filter setting ProteusWidgets mustache widget views path for MedicPress.
	 */
	function set_widgets_view_path() {
		return get_template_directory() . '/inc/widgets-views';
	}


	/**
	 * Filter the Featured page widget pw-page-box image size for MedicPress (ProteusWidgets).
	 *
	 * @param array $image default image parameters.
	 * @return array
	 */
	function set_page_box_image_size( $image ) {
		$image['width']  = 350;
		$image['height'] = 175;
		return $image;
	}


	/**
	 * Filter the pw-latest-news image size for MedicPress (ProteusWidgets).
	 *
	 * @param array $image default image parameters.
	 * @return array
	 */
	function set_latest_news_image_size( $image ) {
		$image['width']  = 350;
		$image['height'] = 175;
		return $image;
	}


	/**
	 * Set the default FA social icon for MedicPress (ProteusWidgets).
	 *
	 * @param array $icon default FA icon string.
	 * @return string
	 */
	function default_social_icon( $icon ) {
		return 'fa-facebook';
	}


	/**
	 * Filter the Featured page widget pw-inline image size for MedicPress (ProteusWidgets).
	 *
	 * @param array $image default image parameters.
	 * @return array
	 */
	function set_inline_image_size( $image ) {
		$image['width']  = 100;
		$image['height'] = 70;
		return $image;
	}

	/**
	 * Filter the Featured page widget excerpt lengths for MedicPress (ProteusWidgets).
	 *
	 * @param array $excerpt_lengths default excerpt lengths.
	 * @return array
	 */
	function set_featured_page_excerpt_lengths( $excerpt_lengths ) {
		$excerpt_lengths['inline_excerpt'] = 55;
		$excerpt_lengths['block_excerpt']  = 140;
		return $excerpt_lengths;
	}

	/**
	 * Set Latest News widget texts for MedicPress (ProteusWidgets).
	 *
	 * @param array $defaults default Latest news widget texts.
	 * @return array
	 */
	function set_latest_news_texts( $defaults ) {
		$defaults['read_more'] = 'Read more';
		return $defaults;
	}

	/**
	 * Set Person Profile widget settings for MedicPress (ProteusWidgets).
	 *
	 * @param array $defaults default Person Profile settings.
	 * @return array
	 */
	function set_person_profile_widget_settings( $defaults ) {
		$defaults['label_instead_of_tag']      = true;
		$defaults['carousel_instead_of_image'] = true;
		$defaults['skills']                    = false;
		$defaults['tags']                      = false;
		$defaults['description']               = false;
		$defaults['social_icons']              = false;
		$defaults['location']                  = true;
		$defaults['specific_location']         = true;
		$defaults['cta']                       = true;
		$defaults['icon_list_items']           = true;
		$defaults['name_link_url']             = true;

		return $defaults;
	}

	/**
	 * Filter for the list of Font-Awesome icons in social icons widget in MedicPress (ProteusWidgets).
	 */
	function social_icons_fa_icons_list() {
		return array(
			'fa-facebook',
			'fa-twitter',
			'fa-youtube',
			'fa-google-plus',
			'fa-pinterest',
			'fa-tumblr',
			'fa-xing',
			'fa-vimeo',
			'fa-linkedin',
			'fa-facebook-square',
			'fa-twitter-square',
			'fa-youtube-square',
			'fa-google-plus-square',
			'fa-pinterest-square',
			'fa-tumblr-square',
			'fa-xing-square',
			'fa-vimeo-square',
			'fa-linkedin-square',
		);
	}

	/**
	 * Return Google fonts and sizes.
	 *
	 * @see https://github.com/grappler/wp-standard-handles/blob/master/functions.php
	 * @param array $fonts google fonts used in the theme.
	 * @return array Google fonts and sizes.
	 */
	function additional_fonts( $fonts ) {

		/* translators: If there are characters in your language that are not supported by Open Sans or Roboto Slab, translate this to 'off'. Do not translate into your own language. */
		if ( 'off' !== esc_html_x( 'on', 'Open Sans and Roboto Slab: on or off', 'medicpress-pt' ) ) {
			$fonts['Open Sans'] = array(
				'400' => '400',
				'700' => '700',
			);
			$fonts['Roboto Slab'] = array(
				'700' => '700',
			);
		}

		return $fonts;
	}


	/**
	 * Add subsets from customizer, if needed.
	 *
	 * @param array $subsets google font subsets used in the theme.
	 * @return array
	 */
	function subsets_google_web_fonts( $subsets ) {
		$additional_subset = get_theme_mod( 'charset_setting', 'latin' );

		array_push( $subsets, $additional_subset );

		return $subsets;
	}


	/**
	 * Embedded videos and video container around them.
	 *
	 * @param string $html html to be enclosed with responsive HTML.
	 * @return string
	 */
	function embed_oembed_html( $html ) {
		if (
			false !== strstr( $html, 'youtube.com' ) ||
			false !== strstr( $html, 'wordpress.tv' ) ||
			false !== strstr( $html, 'wordpress.com' ) ||
			false !== strstr( $html, 'vimeo.com' )
		) {
			$out = '<div class="embed-responsive  embed-responsive-16by9">' . $html . '</div>';
		} else {
			$out = $html;
		}
		return $out;
	}


	/**
	 * Add more allowed protocols.
	 *
	 * @param array $protocols default protocols.
	 * @return array
	 * @link https://developer.wordpress.org/reference/functions/wp_allowed_protocols/
	 */
	static function kses_allowed_protocols( $protocols ) {
		return array_merge( $protocols, array( 'skype' ) );
	}


	/**
	 * Append the right body classes to the <body>.
	 *
	 * @param  array $classes The default array of classes.
	 * @return array
	 */
	public static function body_class( $classes ) {
		$classes[] = 'medicpress-pt';

		if ( 'boxed' === get_theme_mod( 'layout_mode', 'wide' ) ) {
			$classes[] = 'boxed';
		}

		return $classes;
	}


	/**
	 * Append the right post classes.
	 *
	 * @param  array $classes The default array of classes.
	 * @return array
	 */
	public static function post_class( $classes ) {
		$classes[] = 'clearfix';
		$classes[] = 'article';

		// Remove the hentry class.
		$classes = array_diff( $classes , array( 'hentry' ) );

		return $classes;
	}


	/**
	 * Change the default settings for SO.
	 *
	 * @param  array $settings default Page Builder settings.
	 * @return array
	 */
	function siteorigin_panels_settings_defaults( $settings ) {
		$settings['title-html']           = '<h3 class="widget-title"><span class="widget-title__inline">{{title}}</span></h3>';
		$settings['full-width-container'] = '.boxed-container';
		$settings['mobile-width']         = '991';

		return $settings;
	}


	/**
	 * Set Featured Page widget fields (ProteusWidgets)
	 *
	 * @param array $fields default settings for Featured page widget.
	 * @return array
	 */
	function set_featured_page_fields( $fields ) {
		$fields['show_read_more_link'] = true;
		return $fields;
	}


	/**
	 * Define demo import files for One Click Demo Import plugin.
	 */
	function ocdi_import_files() {
		return array(
			array(
				'import_file_name'       => 'MedicPress Import',
				'import_file_url'        => 'http://artifacts.proteusthemes.com/xml-exports/medicpress-latest.xml',
				'import_widget_file_url' => 'http://artifacts.proteusthemes.com/json-widgets/medicpress.json',
			),
		);
	}


	/**
	 * After import theme setup for One Click Demo Import plugin.
	 */
	function ocdi_after_import_setup() {

		// Menus to Import and assign.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'main-menu'   => $main_menu->term_id,
			)
		);

		// Set options for front page and blog page.
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'News' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );

		// Set options for Breadcrumbs NavXT.
		$breadcrumbs_settings                         = get_option( 'bcn_options', array() );
		$breadcrumbs_settings['hseparator']           = '';
		$breadcrumbs_settings['bcurrent_item_linked'] = true;
		$breadcrumbs_settings['bmainsite_display']    = false;
		update_option( 'bcn_options', $breadcrumbs_settings );

		// Set logo in customizer.
		set_theme_mod( 'logo_img', get_template_directory_uri() . '/assets/images/logo.png' );
		set_theme_mod( 'logo2x_img', get_template_directory_uri() . '/assets/images/logox2.png' );

		esc_html_e( 'After import setup ended!', 'medicpress-pt' );
	}


	/**
	 * Message for manual demo import for One Click Demo Import plugin.
	 */
	function ocdi_message_after_file_fetching_error() {
		return sprintf( esc_html__( 'Please try to manually import the demo data. Here are instructions on how to do that: %1$sDocumentation: Import XML File%2$s', 'medicpress-pt' ), '<a href="https://www.proteusthemes.com/docs/medicpress/#import-xml-file" target="_blank">', '</a>' );
	}


	/**
	 * Add arguments to oembed iframes.
	 *
	 * @param string $html the output.
	 * @param string $url the url used for the embed.
	 * @param array  $args arguments.
	 */
	function oembed_result( $html, $url, $args ) {

		// Modify youtube parameters.
		if ( strstr( $html, 'youtube.com/' ) ) {
			if ( isset( $args['player_id'] ) ) {
				$html = str_replace( '<iframe ', '<iframe id="' . $args['player_id'] . '"', $html );
			}
			if ( isset( $args['api'] ) ) {
				$html = str_replace( '?feature=oembed', '?feature=oembed&enablejsapi=1', $html );
			}
		}

		// Modify vimeo parameters.
		if ( strstr( $html, 'vimeo.com/' ) ) {
			if ( isset( $args['player_id'] ) ) {
				$html = str_replace( '<iframe ', '<iframe id="' . $args['player_id'] . '" ', $html );
			}
		}

		return $html;
	}


	/**
	 * Modify the oembed urls.
	 * Check the full list of args here: https://developer.vimeo.com/apis/oembed.
	 * You can find the list of defaults providers in WP_oEmbed::__construct().
	 *
	 * @param  string $provider URL of the oEmbed provider.
	 * @param  string $url      URL of the content to be embedded.
	 * @param  array  $args     Arguments, usually passed from a shortcode.
	 * @return string           Modified $provider.
	 */
	function oembed_fetch_url( $provider, $url, $args ) {
		if ( false !== strpos( $provider, 'vimeo.com' ) ) {
			if ( isset( $args['api'] ) ) {
				$provider = add_query_arg( 'api', absint( $args['api'] ), $provider );
			}
			if ( isset( $args['player_id'] ) ) {
				$provider = add_query_arg( 'player_id', esc_attr( $args['player_id'] ), $provider );
			}
		}

		return $provider;
	}


	/**
	 * Add PW widgets to Page Builder group and add icon class.
	 *
	 * @param array $widgets All widgets in page builder list of widgets.
	 *
	 * @return array
	 */
	function add_icons_to_page_builder_for_pw_widgets( $widgets ) {
		foreach ( $widgets as $class => $widget ) {
			if ( strstr( $widget['title'], 'ProteusThemes:' ) ) {
				$widgets[ $class ]['icon']   = 'pw-pb-widget-icon';
				$widgets[ $class ]['groups'] = array( 'pw-widgets' );
			}
		}

		return $widgets;
	}


	/**
	 * Add another tab section in the Page Builder "add new widget" dialog.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @return array
	 */
	function siteorigin_panels_add_widgets_dialog_tabs( $tabs ) {
		$tabs['pw_widgets'] = array(
			'title' => esc_html__( 'ProteusThemes Widgets', 'medicpress-pt' ),
			'filter' => array(
				'groups' => array( 'pw-widgets' ),
			),
		);

		return $tabs;
	}


	/**
	 * Set to which customizer panel the sticky menu should be appended.
	 *
	 * @param array $defaults Default settings for this filter.
	 *
	 * @return array
	 */
	public function pt_sticky_menu_theme_panel( $defaults ) {
		$defaults['panel'] = 'panel_medicpress';

		return $defaults;
	}


	/**
	 * Change the default cta button class for the sticky menu.
	 *
	 * @return string
	 */
	public function pt_sticky_menu_cta_button_class() {
		return 'btn-secondary';
	}


	/**
	 * Change the default icons field notice in our widgets.
	 *
	 * @param string $default_text The default notice string.
	 */
	public function set_icons_input_field_notice( $default_text ) {
		return $default_text . ' ' . sprintf( esc_html__( 'You can also use one of our medic icons, like so: "mp mp-ambulance-car". %1$sView all medic icons and how to use them%2$s.', 'medicpress-pt' ), '<a href="https://www.proteusthemes.com/docs/medicpress/#medic-icons" target="_blank">', '</a>' );
	}


	/**
	 * Set the Latest news widget settings.
	 *
	 * @param array $defaults Array of the default widget settings.
	 * @return array          Modified settings.
	 */
	public function set_latest_news_fields( $defaults ) {
		$defaults['by_author']   = true;
		$defaults['by_category'] = true;

		return $defaults;
	}


	/**
	 * Add the images to the special submenu -> the submenu items with the parent with 'pt-special-dropdown' class.
	 *
	 * @param array $items List of menu objects (WP_Post).
	 * @param array $args  Array of menu settings.
	 * @return array
	 */
	public function add_images_to_special_submenu( $items ) {
		$special_menu_parent_ids = array();

		foreach ( $items as $item ) {
			if ( in_array( 'pt-special-dropdown', $item->classes, true ) && isset( $item->ID ) ) {
				$special_menu_parent_ids[] = $item->ID;
			}

			if ( in_array( $item->menu_item_parent, $special_menu_parent_ids ) && has_post_thumbnail( $item->object_id ) ) {
				$item->title = sprintf(
					'%1$s %2$s',
					get_the_post_thumbnail( $item->object_id, 'thumbnail', array( 'alt' => esc_attr( $item->title ) ) ),
					$item->title
				);
			}
		}

		return $items;
	}
}

// Single instance.
$medicpress_filters = new MedicPressFilters();
