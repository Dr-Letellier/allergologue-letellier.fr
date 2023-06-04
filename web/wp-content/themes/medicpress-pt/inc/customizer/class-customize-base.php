<?php
/**
 * Customizer
 *
 * @package medicpress-pt
 */

use ProteusThemes\CustomizerUtils\Setting;
use ProteusThemes\CustomizerUtils\Control;
use ProteusThemes\CustomizerUtils\CacheManager;
use ProteusThemes\CustomizerUtils\Helpers as WpUtilsHelpers;

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 */
class MedicPress_Customizer_Base {
	/**
	 * The singleton manager instance
	 *
	 * @see wp-includes/class-wp-customize-manager.php
	 * @var WP_Customize_Manager
	 */
	protected $wp_customize;

	/**
	 * Instance of the DynamicCSS cache manager
	 *
	 * @var ProteusThemes\CustomizerUtils\CacheManager
	 */
	private $dynamic_css_cache_manager;

	/**
	 * Holds the array for the DynamiCSS.
	 *
	 * @var array
	 */
	private $dynamic_css = array();

	/**
	 * Constructor method for this class.
	 *
	 * @param WP_Customize_Manager $wp_customize The WP customizer manager instance.
	 */
	public function __construct( WP_Customize_Manager $wp_customize ) {
		// Set the private propery to instance of wp_customize.
		$this->wp_customize = $wp_customize;

		// Set the private propery to instance of DynamicCSS CacheManager.
		$this->dynamic_css_cache_manager = new CacheManager( $this->wp_customize );

		// Init the dynamic_css property.
		$this->dynamic_css = $this->dynamic_css_init();

		// Register the settings/panels/sections/controls.
		$this->register_settings();
		$this->register_panels();
		$this->register_sections();
		$this->register_partials();
		$this->register_controls();

		/**
		 * Action and filters
		 */

		// Render the CSS and cache it to the theme_mod when the setting is saved.
		add_action( 'wp_head', array( 'ProteusThemes\CustomizerUtils\Helpers', 'add_dynamic_css_style_tag' ), 50, 0 );
		add_action( 'customize_save_after', function() {
			$medicpress_woocommerce_selectors_filter_callable = false;

			if ( ! MedicPressHelpers::is_woocommerce_active() ) {
				$medicpress_woocommerce_selectors_filter_callable = function ( $css_selector ) {
					return false === strpos( $css_selector, '.woocommerce' ) && false === strpos( $css_selector, '.wc-appointments' );
				};
			}

			$this->dynamic_css_cache_manager->cache_rendered_css( $medicpress_woocommerce_selectors_filter_callable );
		}, 10, 0 );

		// Save logo width/height dimensions.
		add_action( 'customize_save_logo_img', array( 'ProteusThemes\CustomizerUtils\Helpers', 'save_logo_dimensions' ), 10, 1 );
	}


	/**
	 * Initialization of the dynamic CSS settings with config arrays
	 *
	 * @return array
	 */
	private function dynamic_css_init() {
		$darken5   = new Setting\DynamicCSS\ModDarken( 5 );
		$darken6   = new Setting\DynamicCSS\ModDarken( 6 );
		$darken12  = new Setting\DynamicCSS\ModDarken( 12 );
		$lighten5  = new Setting\DynamicCSS\ModLighten( 5 );
		$lighten7  = new Setting\DynamicCSS\ModLighten( 7 );

		return array(
			'top_bar_bg' => array(
				'default'    => '#e9edf0',
				'css_props' => array( // List of all css properties this setting controls.
					array( // Each property in it's own array.
						'name'      => 'background-color',
						'selectors' => array(
							'noop' => array( // Regular selectors.
								'.top__container',
							),
						),
					),
				),
			),

			'top_bar_color' => array(
				'default'    => '#72858a',
				'css_props' => array(
					array(
						'name'      => 'color',
						'selectors' => array(
							'noop' => array(
								'.top__container',
								'.top .widget_nav_menu .menu a',
								'.top .social-icons__link',
								'.top .icon-box__title',
								'.top .icon-box',
							),
						),
					),
					array(
						'name'      => 'color',
						'selectors' => array(
							'noop' => array(
								'.top .icon-box .fa',
							),
						),
						'modifier'  => $lighten5,
					),
				),
			),

			'header_bg' => array(
				'default'    => '#ffffff',
				'css_props' => array(
					array(
						'name'      => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.header',
							),
						),
					),
					array(
						'name'  => 'background-color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.header__container::before',
								'.header__container::after',
								'.header::before',
								'.header::after',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.header__container',
							),
						),
					),
				),
			),

			'main_navigation_mobile_background' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'background',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation',
							),
						),
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation a',
							),
						),
						'modifier'  => $lighten5,
					),
				),
			),

			'main_navigation_mobile_color' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation a',
							),
						),
					),
				),
			),

			'main_navigation_mobile_color_hover' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation .menu-item:focus > a',
								'.main-navigation .menu-item:hover > a',
							),
						),
					),
				),
			),

			'main_navigation_mobile_sub_bgcolor' => array(
				'default' => '#52b9d0',
				'css_props' => array(
					array(
						'name' => 'background-color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation .sub-menu a',
							),
						),
					),
				),
			),

			'main_navigation_mobile_sub_color' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation .sub-menu .menu-item > a',
							),
						),
					),
				),
			),

			'main_navigation_mobile_sub_color_hover' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (max-width: 991px)' => array(
								'.main-navigation .sub-menu .menu-item:hover > a',
								'.main-navigation .sub-menu .menu-item:focus > a',
							),
						),
					),
				),
			),

			'main_navigation_color' => array(
				'default' => '#333333',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation a',
							),
						),
					),
				),
			),

			'main_navigation_color_hover' => array(
				'default' => '#333333',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation > .menu-item:focus > a',
								'.main-navigation > .menu-item:hover > a',
							),
						),
					),
				),
			),

			'main_navigation_color_current' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation > .current-menu-item > a',
								'.main-navigation > .current-menu-ancestor > a',
								'.main-navigation a::after',
								'.main-navigation > .current-menu-item:focus > a',
								'.main-navigation > .current-menu-item:hover > a',
								'.main-navigation > .current-menu-ancestor:focus > a',
								'.main-navigation > .current-menu-ancestor:hover > a',
							),
						),
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation > .current-menu-item > a',
								'.main-navigation > .current-menu-ancestor > a',
								'.main-navigation .menu-item:focus > a',
								'.main-navigation .menu-item:hover > a',
								'.main-navigation .menu-item.is-hover > a',
							),
						),
					),
				),
			),

			'main_navigation_sub_bg' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'background-color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation .sub-menu a',
								'.main-navigation .pt-special-dropdown .sub-menu',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation .sub-menu .menu-item > a:hover',
							),
						),
						'modifier'  => $lighten7,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation .sub-menu a',
								'.main-navigation .sub-menu .sub-menu a',
								'.main-navigation .sub-menu .menu-item:hover > a',
								'.main-navigation .pt-special-dropdown .sub-menu .menu-item:not(:last-of-type)',
							),
						),
						'modifier'  => $lighten7,
					),
				),
			),

			'main_navigation_sub_color' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.main-navigation .sub-menu .menu-item > a',
								'.main-navigation .sub-menu .menu-item > a:hover',
							),
						),
					),
				),
			),

			'page_header_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.page-header__title',
							),
						),
					),
				),
			),

			'page_header_bg_color' => array(
				'default' => '',
				'css_props' => array(
					array(
						'name' => 'background',
						'selectors' => array(
							'noop' => array(
								'.page-header',
							),
						),
					),
				),
			),

			'breadcrumbs_color' => array(
				'default'    => '#56676b',
				'css_props' => array(
					array(
						'name'      => 'color',
						'selectors' => array(
							'noop' => array(
								'.breadcrumbs a',
								'.breadcrumbs a::after',
							),
						),
					),
				),
			),

			'breadcrumbs_color_hover' => array(
				'default'    => '#333333',
				'css_props' => array(
					array(
						'name'      => 'color',
						'selectors' => array(
							'noop' => array(
								'.breadcrumbs a:focus',
								'.breadcrumbs a:hover',
							),
						),
					),
				),
			),

			'breadcrumbs_color_active' => array(
				'default'    => '#56676b',
				'css_props' => array(
					array(
						'name'      => 'color',
						'selectors' => array(
							'noop' => array(
								'.breadcrumbs .current-item',
							),
						),
					),
				),
			),

			'text_color_content_area' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.content-area',
								'.content-area .icon-box__subtitle',
								'.person-profile .icon-list__text',
							),
						),
					),
				),
			),

			'headings_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'h1',
								'h2',
								'h3',
								'h4',
								'h5',
								'h6',
								'.person-profile__location',
								'.person-profile .icon-list__item--featured .icon-list__description',
								'.header__logo-text',
								'.content-area .icon-box__title',
								'.latest-news__title a',
								'.latest-news__title a:focus',
								'.latest-news__title a:hover',
								'.latest-news__title a:active:hover',
								'.latest-news--more-news',
								'.accordion__panel .panel-title a.collapsed',
								'.accordion__panel .panel-title a',
								'.testimonial__author',
								'.page-box__title a',
								'.page-box__title a:focus',
								'.page-box__title a:hover',
								'.page-box__title a:active:hover',
								'.article__title a',
								'.article__title a:focus',
								'.article__title a:hover',
								'.article__title a:active:hover',
								'.person-profile__name a',
								'.person-profile__name a:focus',
								'.person-profile__name a:hover',
								'.person-profile__name a:active:hover',
								'.sidebar__headings',
								'.comment__author',
								'.comment__author a',
								'.comment__author a:focus',
								'.comment__author a:hover',
								'.comment__author a:active:hover',
								'.widget_archive a',
								'.widget_pages a',
								'.widget_categories a',
								'.widget_meta a',
								'.widget_recent_comments a',
								'.widget_recent_entries a',
								'.widget_rss a',
								'body.woocommerce-page ul.products li.product .woocommerce-loop-product__title',
								'body.woocommerce-page ul.products li.product h3',
								'body.woocommerce-page .entry-summary .entry-title',
								'.woocommerce ul.products li.product .woocommerce-loop-product__title',
								'.woocommerce ul.products li.product h3',
							),
						),
					),
				),
			),

			'primary_color' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.person-profile__specific-location',
								'.person-profile .icon-list__item .fa',
								'.person-profile .icon-list__item--featured .icon-list__text',
								'.pricing-list__title',
								'.pricing-list__badge',
								'.pricing-list__price',
								'.accordion__panel .panel-title a::after',
								'.accordion__panel .panel-title a:hover',
								'.accordion .more-link::after',
								'.latest-news:focus .latest-news__title',
								'.latest-news:hover .latest-news__title',
								'.latest-news:focus .latest-news__title a',
								'.latest-news:hover .latest-news__title a',
								'.latest-news--more-news:focus',
								'.latest-news--more-news:hover',
								'.latest-news__tag',
								'.content-area .icon-box .fa',
								'.content-area a.icon-box:focus .icon-box__title',
								'.content-area a.icon-box:hover .icon-box__title',
								'.widget_tag_cloud a',
								'.widget_archive a:focus',
								'.widget_archive a:hover',
								'.widget_archive a:hover:active',
								'.widget_pages a:focus',
								'.widget_pages a:hover',
								'.widget_pages a:hover:active',
								'.widget_categories a:focus',
								'.widget_categories a:hover',
								'.widget_categories a:hover:active',
								'.widget_meta a:focus',
								'.widget_meta a:hover',
								'.widget_meta a:hover:active',
								'.widget_recent_comments a:focus',
								'.widget_recent_comments a:hover',
								'.widget_recent_comments a:hover:active',
								'.widget_recent_entries a:focus',
								'.widget_recent_entries a:hover',
								'.widget_recent_entries a:hover:active',
								'.widget_rss a:focus',
								'.widget_rss a:hover',
								'.widget_rss a:hover:active',
								'.article__tags a',
								'.footer-top .widget_tag_cloud a',
								'.footer-bottom .icon-container:hover',
								'body.woocommerce-page ul.products li.product a:hover img',
								'.woocommerce ul.products li.product a:hover img',
								'body.woocommerce-page ul.product_list_widget .amount',
							),
						),
					),
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.icon-list a.icon-list__item:focus .fa',
								'.icon-list a.icon-list__item:hover .fa',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.testimonials .slick-current + .slick-active .testimonial',
								'.btn-primary',
								'.widget_calendar caption',
								'.brochure-box',
								'.latest-news__tag:focus',
								'.latest-news__tag:hover',
								'.widget_tag_cloud a:focus',
								'.widget_tag_cloud a:hover',
								'.article__tags a:focus',
								'.article__tags a:hover',
								'.footer-top__back-to-top',
								'.footer-top__back-to-top:focus',
								'.sidebar .opening-time',
								'.footer .opening-time',
								'body.woocommerce-page .widget_price_filter .ui-slider .ui-slider-handle',
								'body.woocommerce-page .widget_price_filter .ui-slider .ui-slider-range',
								'body.woocommerce-page #review_form #respond input#submit',
								'body.woocommerce-page div.product form.cart .button.single_add_to_cart_button',
								'body.woocommerce-page .woocommerce-error a.button',
								'body.woocommerce-page .woocommerce-info a.button',
								'body.woocommerce-page .woocommerce-message a.button',
								'.woocommerce button.button.alt:disabled',
								'.woocommerce button.button.alt:disabled:hover',
								'.woocommerce button.button.alt:disabled[disabled]',
								'.woocommerce button.button.alt:disabled[disabled]:hover',
								'.woocommerce-cart .wc-proceed-to-checkout a.checkout-button',
								'body.woocommerce-page #payment #place_order',
								'body.woocommerce-page a.button',
								'body.woocommerce-page input.button',
								'body.woocommerce-page input.button.alt',
								'body.woocommerce-page button.button',
								'body.woocommerce-page .widget_product_search .search-field + input',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-primary:focus',
								'.btn-primary:hover',
								'.brochure-box:focus',
								'.brochure-box:hover',
								'.latest-news__tag:active:hover',
								'.widget_tag_cloud a:active:hover',
								'.article__tags a:active:hover',
								'.footer-top__back-to-top:hover',
								'.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover',
								'body.woocommerce-page #payment #place_order:hover',
								'body.woocommerce-page .woocommerce-error a.button:hover',
								'body.woocommerce-page .woocommerce-info a.button:hover',
								'body.woocommerce-page .woocommerce-message a.button:hover',
								'body.woocommerce-page #review_form #respond input#submit:hover',
								'.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover',
								'body.woocommerce-page div.product form.cart .button.single_add_to_cart_button:focus',
								'body.woocommerce-page div.product form.cart .button.single_add_to_cart_button:hover',
								'body.woocommerce-page .widget_product_search .search-field + input:hover',
								'body.woocommerce-page .widget_product_search .search-field + input:focus',
								'body.woocommerce-page a.add_to_cart_button:hover',
								'.woocommerce a.add_to_cart_button:hover',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout:hover',
								'body.woocommerce-page a.button:hover',
								'body.woocommerce-page input.button:hover',
								'body.woocommerce-page input.button.alt:hover',
								'body.woocommerce-page button.button:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-primary:active:hover',
								'.brochure-box:active:hover',
								'.footer-top__back-to-top:active:hover',
								'body.woocommerce-page div.product form.cart .button.single_add_to_cart_button:active:hover',
								'body.woocommerce-page #payment #place_order:active:hover',
								'body.woocommerce-page .woocommerce-error a.button:active:hover',
								'body.woocommerce-page .woocommerce-info a.button:active:hover',
								'body.woocommerce-page .woocommerce-message a.button:active:hover',
								'body.woocommerce-page #review_form #respond input#submit:active:hover',
								'.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:active:hover',
								'body.woocommerce-page a.add_to_cart_button:active:hover',
								'.woocommerce a.add_to_cart_button:active:hover',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.testimonials .slick-current + .slick-active .testimonial',
								'.btn-primary',
								'.person-profile__specific-location',
								'.pricing-list__badge',
								'.latest-news__tag',
								'.latest-news__tag:focus',
								'.latest-news__tag:hover',
								'.widget_tag_cloud a',
								'.widget_tag_cloud a:focus',
								'.widget_tag_cloud a:hover',
								'.article__tags a',
								'.article__tags a:focus',
								'.article__tags a:hover',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout',
							),
						),
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-primary:focus',
								'.btn-primary:hover',
								'.latest-news__tag:active:hover',
								'.widget_tag_cloud a:active:hover',
								'.article__tags a:active:hover',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout:focus',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-primary:active:hover',
								'body.woocommerce-page .widget_shopping_cart_content .buttons .checkout:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),

			'secondary_color' => array(
				'default' => '#66d0cc',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'body.woocommerce-page .star-rating, .woocommerce .star-rating',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary',
								'.sidebar .icon-list',
								'.footer .icon-list',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary:focus',
								'.btn-secondary:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary',
							),
						),
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary:focus',
								'.btn-secondary:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-secondary:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),

			'link_color' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'a',
								'a:focus',
								'.page-box__more-link',
								'.page-box__more-link:focus',
								'.article__content .more-link',
								'.article__content .more-link:focus',
							),
						),
					),
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'a:hover',
								'.page-box__more-link:hover',
								'.article__content .more-link:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'a:active:hover',
								'.page-box__more-link:active:hover',
								'.article__content .more-link:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),

			'slider_text_color' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'@media (min-width: 992px)' => array(
								'.pt-slick-carousel__content-title',
								'.pt-slick-carousel__content-description',
							),
						),
					),
				),
			),

			'light_button_color' => array(
				'default' => '#ffffff',
				'css_props' => array(
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light',
							),
						),
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light:focus',
								'.btn-light:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light',
							),
						),
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light:focus',
								'.btn-light:hover',
							),
						),
						'modifier'  => $darken6,
					),
					array(
						'name' => 'border-color',
						'selectors' => array(
							'noop' => array(
								'.btn-light:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),

			'body_bg' => array(
				'default'   => '#ffffff',
				'css_props' => array(
					array(
						'name'      => 'background-color',
						'selectors' => array(
							'noop' => array(
								'body .boxed-container',
							),
						),
					),
				),
			),

			'footer_bg_color' => array(
				'default' => '#e9edf0',
				'css_props' => array(
					array(
						'name' => 'background-color',
						'selectors' => array(
							'noop' => array(
								'.footer-top',
							),
						),
					),
				),
			),

			'footer_title_color' => array(
				'default' => '#079bbb',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-top__heading',
							),
						),
					),
				),
			),

			'footer_text_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-top',
							),
						),
					),
				),
			),

			'footer_link_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-top a',
								'.footer-top .widget_nav_menu .menu a',
							),
						),
					),
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-top a:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),

			'footer_bottom_background_color' => array(
				'default' => '#dbdee0',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-bottom__container',
							),
						),
					),
				),
			),

			'footer_bottom_text_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-bottom',
							),
						),
					),
				),
			),

			'footer_bottom_link_color' => array(
				'default' => '#56676b',
				'css_props' => array(
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-bottom a',
							),
						),
					),
					array(
						'name' => 'color',
						'selectors' => array(
							'noop' => array(
								'.footer-bottom a:active:hover',
							),
						),
						'modifier'  => $darken12,
					),
				),
			),
		);
	}

	/**
	 * Register customizer settings
	 *
	 * @return void
	 */
	public function register_settings() {
		// Branding.
		$this->wp_customize->add_setting( 'logo_img', array( 'sanitize_callback' => 'esc_url' ) );
		$this->wp_customize->add_setting( 'logo2x_img', array( 'sanitize_callback' => 'esc_url' ) );

		// Header.
		$this->wp_customize->add_setting( 'top_bar_visibility', array( 'default' => 'yes', 'sanitize_callback' => 'sanitize_key' ) );

		// Page title area.
		$this->wp_customize->add_setting( 'page_header_bg_img', array( 'sanitize_callback' => 'esc_url' ) );
		$this->wp_customize->add_setting( 'page_header_bg_img_repeat', array( 'default' => 'repeat', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'page_header_bg_img_position_x', array( 'default' => 'left', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'page_header_bg_img_attachment', array( 'default' => 'scroll', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'show_page_title_area', array( 'default' => 'yes', 'sanitize_callback' => 'sanitize_key' ) );

		// Featured page.
		$this->wp_customize->add_setting( 'featured_page_select', array( 'default' => 'none', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'featured_page_custom_text', array( 'sanitize_callback' => 'wp_kses_post' ) );
		$this->wp_customize->add_setting( 'featured_page_custom_url', array( 'sanitize_callback' => 'esc_url' ) );
		$this->wp_customize->add_setting( 'featured_page_open_in_new_window', array( 'sanitize_callback' => 'sanitize_key' ) );

		// Typography.
		$this->wp_customize->add_setting( 'charset_setting', array( 'default' => 'latin', 'sanitize_callback' => 'sanitize_key' ) );

		// Theme layout & color.
		$this->wp_customize->add_setting( 'layout_mode', array( 'default' => 'wide', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'body_bg_img', array( 'sanitize_callback' => 'esc_url' ) );
		$this->wp_customize->add_setting( 'body_bg_img_repeat', array( 'default' => 'repeat', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'body_bg_img_position_x', array( 'default' => 'left', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'body_bg_img_attachment', array( 'default' => 'scroll', 'sanitize_callback' => 'sanitize_key' ) );

		// Shop.
		if ( MedicPressHelpers::is_woocommerce_active() ) {
			$this->wp_customize->add_setting( 'products_per_page', array( 'default' => 9, 'sanitize_callback' => 'absint' ) );
			$this->wp_customize->add_setting( 'single_product_sidebar', array( 'default' => 'left', 'sanitize_callback' => 'sanitize_key' ) );
		}

		// Footer.
		$this->wp_customize->add_setting( 'footer_widgets_layout', array( 'default' => '[4,6,8]', 'sanitize_callback' => 'wp_kses_post' ) );

		$this->wp_customize->add_setting( 'footer_bottom_left_txt', array( 'default' => '&copy; ' . date( 'Y' ) . ' <strong><a href="https://www.proteusthemes.com/wordpress-themes/medicpress/">MedicPress</a></strong> All Rights Reserved.', 'sanitize_callback' => 'wp_kses_post' ) );
		$this->wp_customize->add_setting( 'footer_bottom_right_txt', array( 'default' => '[fa icon="fa-twitter" href="https://twitter.com/ProteusThemes"] [fa icon="fa-facebook" href="https://www.facebook.com/ProteusThemes/"] [fa icon="fa-youtube" href="https://www.youtube.com/user/ProteusNetCompany"]', 'sanitize_callback' => 'wp_kses_post' ) );

		// Custom code (css/js).
		$this->wp_customize->add_setting( 'custom_js_head', array( 'sanitize_callback' => array( 'ProteusThemes\CustomizerUtils\Helpers', 'wp_kses_script' ) ) );
		$this->wp_customize->add_setting( 'custom_js_footer', array( 'sanitize_callback' => array( 'ProteusThemes\CustomizerUtils\Helpers', 'wp_kses_script' ) ) );

		// ACF.
		$this->wp_customize->add_setting( 'show_acf', array( 'default' => 'no', 'sanitize_callback' => 'sanitize_key' ) );
		$this->wp_customize->add_setting( 'use_minified_css', array( 'default' => 'no', 'sanitize_callback' => 'sanitize_key' ) );

		// All the DynamicCSS settings.
		foreach ( $this->dynamic_css as $setting_id => $args ) {
			$this->wp_customize->add_setting(
				new Setting\DynamicCSS( $this->wp_customize, $setting_id, $args )
			);
		}
	}


	/**
	 * Panels
	 *
	 * @return void
	 */
	public function register_panels() {
		// One ProteusThemes panel to rule them all.
		$this->wp_customize->add_panel( 'panel_medicpress', array(
			'title'       => esc_html__( '[PT] Theme Options', 'medicpress-pt' ),
			'description' => esc_html__( 'All MedicPress theme specific settings.', 'medicpress-pt' ),
			'priority'    => 10,
		) );
	}


	/**
	 * Sections
	 *
	 * @return void
	 */
	public function register_sections() {
		$this->wp_customize->add_section( 'medicpress_section_logos', array(
			'title'       => esc_html__( 'Logo', 'medicpress-pt' ),
			'description' => sprintf( esc_html__( 'Logo for the MedicPress theme.', 'medicpress-pt' ) , '<i>', '</i>' ),
			'priority'    => 10,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'medicpress_section_header', array(
			'title'       => esc_html__( 'Header', 'medicpress-pt' ),
			'description' => esc_html__( 'All layout and appearance settings for the header.', 'medicpress-pt' ),
			'priority'    => 20,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'medicpress_section_navigation', array(
			'title'       => esc_html__( 'Navigation', 'medicpress-pt' ),
			'description' => esc_html__( 'Navigation for the MedicPress theme.', 'medicpress-pt' ),
			'priority'    => 30,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'medicpress_section_page_header', array(
			'title'       => esc_html__( 'Page Header Area', 'medicpress-pt' ),
			'description' => esc_html__( 'All layout and appearance settings for the page header area (regular pages).', 'medicpress-pt' ),
			'priority'    => 33,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'medicpress_section_theme_colors', array(
			'title'       => esc_html__( 'Theme Layout &amp; Colors', 'medicpress-pt' ),
			'priority'    => 40,
			'panel'       => 'panel_medicpress',
		) );

		if ( MedicPressHelpers::is_woocommerce_active() ) {
			$this->wp_customize->add_section( 'medicpress_section_shop', array(
				'title'       => esc_html__( 'Shop', 'medicpress-pt' ),
				'priority'    => 80,
				'panel'       => 'panel_medicpress',
			) );
		}

		$this->wp_customize->add_section( 'section_footer', array(
			'title'       => esc_html__( 'Footer', 'medicpress-pt' ),
			'description' => esc_html__( 'All layout and appearance settings for the footer.', 'medicpress-pt' ),
			'priority'    => 90,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'section_custom_code', array(
			'title'       => esc_html__( 'Custom Code' , 'medicpress-pt' ),
			'priority'    => 100,
			'panel'       => 'panel_medicpress',
		) );

		$this->wp_customize->add_section( 'section_other', array(
			'title'       => esc_html__( 'Other' , 'medicpress-pt' ),
			'priority'    => 150,
			'panel'       => 'panel_medicpress',
		) );
	}


	/**
	 * Partials for selective refresh
	 *
	 * @return void
	 */
	public function register_partials() {
		$this->wp_customize->selective_refresh->add_partial( 'dynamic_css', array(
			'selector' => 'head > #wp-utils-dynamic-css-style-tag',
			'settings' => array_keys( $this->dynamic_css ),
			'render_callback' => function() {
				return $this->dynamic_css_cache_manager->render_css();
			},
		) );
	}


	/**
	 * Controls
	 *
	 * @return void
	 */
	public function register_controls() {
		// Section: medicpress_section_logos.
		$this->wp_customize->add_control( new WP_Customize_Image_Control(
			$this->wp_customize,
			'logo_img',
			array(
				'label'       => esc_html__( 'Logo Image', 'medicpress-pt' ),
				'description' => esc_html__( 'Max recommended height for the logo image is 95px.', 'medicpress-pt' ),
				'section'     => 'medicpress_section_logos',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Image_Control(
			$this->wp_customize,
			'logo2x_img',
			array(
				'label'       => esc_html__( 'Retina Logo Image', 'medicpress-pt' ),
				'description' => esc_html__( '2x logo size, for screens with high DPI.', 'medicpress-pt' ),
				'section'     => 'medicpress_section_logos',
			)
		) );

		// Section: header.
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'header_bg',
			array(
				'priority' => 14,
				'label'    => esc_html__( 'Header background color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_header',
			)
		) );

		$this->wp_customize->add_control( 'top_bar_visibility', array(
			'type'        => 'select',
			'priority'    => 20,
			'label'       => esc_html__( 'Top bar visibility', 'medicpress-pt' ),
			'description' => esc_html__( 'Show or hide?', 'medicpress-pt' ),
			'section'     => 'medicpress_section_header',
			'choices'     => array(
				'yes'         => esc_html__( 'Show', 'medicpress-pt' ),
				'no'          => esc_html__( 'Hide', 'medicpress-pt' ),
				'hide_mobile' => esc_html__( 'Hide on Mobile', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'top_bar_bg',
			array(
				'priority' => 21,
				'label'    => esc_html__( 'Top bar background color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_header',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'top_bar_color',
			array(
				'priority' => 22,
				'label'    => esc_html__( 'Top bar text color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_header',
			)
		) );

		// Section: medicpress_section_navigation.
		$this->wp_customize->add_control( 'featured_page_select', array(
			'type'        => 'select',
			'priority'    => 113,
			'label'       => esc_html__( 'Featured page', 'medicpress-pt' ),
			'description' => esc_html__( 'To which page should the Featured Page button link to?', 'medicpress-pt' ),
			'section'     => 'medicpress_section_navigation',
			'choices'     => WpUtilsHelpers::get_all_pages_id_title(),
		) );
		$this->wp_customize->add_control( 'featured_page_custom_text', array(
			'priority'        => 115,
			'label'           => esc_html__( 'Custom Button Text', 'medicpress-pt' ),
			'section'         => 'medicpress_section_navigation',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_specific_value( 'featured_page_select', 'custom-url' );
			},
		) );

		$this->wp_customize->add_control( 'featured_page_custom_url', array(
			'priority'        => 117,
			'label'           => esc_html__( 'Custom URL', 'medicpress-pt' ),
			'section'         => 'medicpress_section_navigation',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_specific_value( 'featured_page_select', 'custom-url' );
			},
		) );

		$this->wp_customize->add_control( 'featured_page_open_in_new_window', array(
			'type'            => 'checkbox',
			'priority'        => 120,
			'label'           => esc_html__( 'Open link in a new window/tab.', 'medicpress-pt' ),
			'section'         => 'medicpress_section_navigation',
			'active_callback' => function() {
				return ( ! WpUtilsHelpers::is_theme_mod_specific_value( 'featured_page_select', 'none' ) );
			},
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_color',
			array(
				'priority' => 130,
				'label'    => esc_html__( 'Main navigation link color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_color_hover',
			array(
				'priority' => 132,
				'label'    => esc_html__( 'Main navigation link hover color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_color_current',
			array(
				'priority' => 135,
				'label'    => esc_html__( 'Main navigation current link color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_sub_bg',
			array(
				'priority' => 160,
				'label'    => esc_html__( 'Main navigation submenu background', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_sub_color',
			array(
				'priority' => 170,
				'label'    => esc_html__( 'Main navigation submenu link color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_color',
			array(
				'priority' => 190,
				'label'    => esc_html__( 'Main navigation link color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_color_hover',
			array(
				'priority' => 192,
				'label'    => esc_html__( 'Main navigation link hover color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_sub_bgcolor',
			array(
				'priority' => 193,
				'label'    => esc_html__( 'Main navigation submenu background color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_sub_color',
			array(
				'priority' => 194,
				'label'    => esc_html__( 'Main navigation submenu link color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_sub_color_hover',
			array(
				'priority' => 195,
				'label'    => esc_html__( 'Main navigation submenu link hover color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'main_navigation_mobile_background',
			array(
				'priority' => 188,
				'label'    => esc_html__( 'Main navigation background color (mobile)', 'medicpress-pt' ),
				'section'  => 'medicpress_section_navigation',
			)
		) );

		// Section: medicpress_section_page_header.
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'page_header_color',
			array(
				'priority' => 30,
				'label'    => esc_html__( 'Page title color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_page_header',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'breadcrumbs_color',
			array(
				'priority' => 45,
				'label'    => esc_html__( 'Breadcrumbs text color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_page_header',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'breadcrumbs_color_hover',
			array(
				'priority' => 50,
				'label'    => esc_html__( 'Breadcrumbs hover text color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_page_header',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'breadcrumbs_color_active',
			array(
				'priority' => 55,
				'label'    => esc_html__( 'Breadcrumbs active text color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_page_header',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'page_header_bg_color',
			array(
				'priority'    => 60,
				'label'       => esc_html__( 'Page title background color', 'medicpress-pt' ),
				'section'     => 'medicpress_section_page_header',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Image_Control(
			$this->wp_customize,
			'page_header_bg_img',
			array(
				'priority' => 65,
				'label'    => esc_html__( 'Page title area background image', 'medicpress-pt' ),
				'section'  => 'medicpress_section_page_header',
			)
		) );
		$this->wp_customize->add_control( 'page_header_bg_img_repeat', array(
			'priority'        => 67,
			'label'           => esc_html__( 'Page title area background repeat', 'medicpress-pt' ),
			'section'         => 'medicpress_section_page_header',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'page_header_bg_img' );
			},
			'choices'         => array(
				'no-repeat'  => esc_html__( 'No Repeat', 'medicpress-pt' ),
				'repeat'     => esc_html__( 'Tile', 'medicpress-pt' ),
				'repeat-x'   => esc_html__( 'Tile Horizontally', 'medicpress-pt' ),
				'repeat-y'   => esc_html__( 'Tile Vertically', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'page_header_bg_img_position_x', array(
			'priority'        => 69,
			'label'           => esc_html__( 'Page title area background position', 'medicpress-pt' ),
			'section'         => 'medicpress_section_page_header',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'page_header_bg_img' );
			},
			'choices'         => array(
				'left'       => esc_html__( 'Left', 'medicpress-pt' ),
				'center'     => esc_html__( 'Center', 'medicpress-pt' ),
				'right'      => esc_html__( 'Right', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'page_header_bg_img_attachment', array(
			'priority'        => 71,
			'label'           => esc_html__( 'Page title area background attachment', 'medicpress-pt' ),
			'section'         => 'medicpress_section_page_header',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'page_header_bg_img' );
			},
			'choices'         => array(
				'scroll'     => esc_html__( 'Scroll', 'medicpress-pt' ),
				'fixed'      => esc_html__( 'Fixed', 'medicpress-pt' ),
			),
		) );

		$this->wp_customize->add_control( 'show_page_title_area', array(
			'type'        => 'select',
			'priority'    => 75,
			'label'       => esc_html__( 'Show page title area', 'medicpress-pt' ),
			'description' => esc_html__( 'This will hide the page title area on all pages. You can also hide individual page headers in page settings. To remove breadcrumbs from all pages, please deactivate the Breadcrumb NavXT plugin.', 'medicpress-pt' ),
			'section'     => 'medicpress_section_page_header',
			'choices'     => array(
				'yes' => esc_html__( 'Show', 'medicpress-pt' ),
				'no'  => esc_html__( 'Hide', 'medicpress-pt' ),
			),
		) );

		// Section: medicpress_section_theme_colors.
		$this->wp_customize->add_control( 'layout_mode', array(
			'type'     => 'select',
			'priority' => 10,
			'label'    => esc_html__( 'Layout', 'medicpress-pt' ),
			'section'  => 'medicpress_section_theme_colors',
			'choices'  => array(
				'wide'  => esc_html__( 'Wide', 'medicpress-pt' ),
				'boxed' => esc_html__( 'Boxed', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'text_color_content_area',
			array(
				'priority' => 30,
				'label'    => esc_html__( 'Text color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'headings_color',
			array(
				'priority' => 33,
				'label'    => esc_html__( 'Headings color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'primary_color',
			array(
				'priority' => 34,
				'label'    => esc_html__( 'Primary color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'secondary_color',
			array(
				'priority' => 35,
				'label'    => esc_html__( 'Secondary color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'link_color',
			array(
				'priority' => 36,
				'label'    => esc_html__( 'Link color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'slider_text_color',
			array(
				'priority'        => 37,
				'label'           => esc_html__( 'Slider text color', 'medicpress-pt' ),
				'description'     => esc_html__( 'Only for desktop slider view.', 'medicpress-pt' ),
				'section'         => 'medicpress_section_theme_colors',
				'active_callback' => function () {
					return is_page_template( 'template-front-page-slider.php' );
				}
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'light_button_color',
			array(
				'priority' => 38,
				'label'    => esc_html__( 'Light button background color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Image_Control(
			$this->wp_customize,
			'body_bg_img',
			array(
				'priority' => 40,
				'label'    => esc_html__( 'Body background image', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );
		$this->wp_customize->add_control( 'body_bg_img_repeat', array(
			'priority'        => 41,
			'label'           => esc_html__( 'Body background repeat', 'medicpress-pt' ),
			'section'         => 'medicpress_section_theme_colors',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'body_bg_img' );
			},
			'choices'         => array(
				'no-repeat' => esc_html__( 'No Repeat', 'medicpress-pt' ),
				'repeat'    => esc_html__( 'Tile', 'medicpress-pt' ),
				'repeat-x'  => esc_html__( 'Tile Horizontally', 'medicpress-pt' ),
				'repeat-y'  => esc_html__( 'Tile Vertically', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'body_bg_img_position_x', array(
			'priority'        => 42,
			'label'           => esc_html__( 'Body background position', 'medicpress-pt' ),
			'section'         => 'medicpress_section_theme_colors',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'body_bg_img' );
			},
			'choices'         => array(
				'left'   => esc_html__( 'Left', 'medicpress-pt' ),
				'center' => esc_html__( 'Center', 'medicpress-pt' ),
				'right'  => esc_html__( 'Right', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'body_bg_img_attachment', array(
			'priority'        => 43,
			'label'           => esc_html__( 'Body background attachment', 'medicpress-pt' ),
			'section'         => 'medicpress_section_theme_colors',
			'type'            => 'radio',
			'active_callback' => function() {
				return WpUtilsHelpers::is_theme_mod_not_empty( 'body_bg_img' );
			},
			'choices'         => array(
				'scroll' => esc_html__( 'Scroll', 'medicpress-pt' ),
				'fixed'  => esc_html__( 'Fixed', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'body_bg',
			array(
				'priority' => 45,
				'label'    => esc_html__( 'Body background color', 'medicpress-pt' ),
				'section'  => 'medicpress_section_theme_colors',
			)
		) );

		// Section: medicpress_section_shop.
		if ( MedicPressHelpers::is_woocommerce_active() ) {
			$this->wp_customize->add_control( 'products_per_page', array(
					'label'   => esc_html__( 'Number of products per page', 'medicpress-pt' ),
					'section' => 'medicpress_section_shop',
				)
			);
			$this->wp_customize->add_control( 'single_product_sidebar', array(
					'label'   => esc_html__( 'Sidebar on single product page', 'medicpress-pt' ),
					'section' => 'medicpress_section_shop',
					'type'    => 'select',
					'choices' => array(
						'none'  => esc_html__( 'No sidebar', 'medicpress-pt' ),
						'left'  => esc_html__( 'Left', 'medicpress-pt' ),
						'right' => esc_html__( 'Right', 'medicpress-pt' ),
					),
				)
			);
		}

		// Section: section_footer.
		$this->wp_customize->add_control( new Control\LayoutBuilder(
			$this->wp_customize,
			'footer_widgets_layout',
			array(
				'priority'    => 5,
				'label'       => esc_html__( 'Footer widgets layout', 'medicpress-pt' ),
				'description' => esc_html__( 'Select number of widget you want in the footer and then with the slider rearrange the layout', 'medicpress-pt' ),
				'section'     => 'section_footer',
				'input_attrs' => array(
					'min'     => 0,
					'max'     => 12,
					'step'    => 1,
					'maxCols' => 6,
				),
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_bg_color',
			array(
				'priority' => 10,
				'label'    => esc_html__( 'Footer background color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );
		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_title_color',
			array(
				'priority' => 30,
				'label'    => esc_html__( 'Footer widget title color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_text_color',
			array(
				'priority' => 31,
				'label'    => esc_html__( 'Footer text color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_link_color',
			array(
				'priority' => 32,
				'label'    => esc_html__( 'Footer link color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_bottom_background_color',
			array(
				'priority' => 34,
				'label'    => esc_html__( 'Footer bottom background color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_bottom_text_color',
			array(
				'priority' => 36,
				'label'    => esc_html__( 'Footer bottom text color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'footer_bottom_link_color',
			array(
				'priority' => 37,
				'label'    => esc_html__( 'Footer bottom link color', 'medicpress-pt' ),
				'section'  => 'section_footer',
			)
		) );

		$this->wp_customize->add_control( 'footer_bottom_left_txt', array(
			'type'        => 'text',
			'priority'    => 110,
			'label'       => esc_html__( 'Footer bottom left text', 'medicpress-pt' ),
			'description' => esc_html__( 'You can use HTML: a, span, i, em, strong, img.', 'medicpress-pt' ),
			'section'     => 'section_footer',
		) );

		$this->wp_customize->add_control( 'footer_bottom_right_txt', array(
			'type'        => 'text',
			'priority'    => 120,
			'label'       => esc_html__( 'Footer bottom right text', 'medicpress-pt' ),
			'description' => esc_html__( 'You can use HTML: a, span, i, em, strong, img.', 'medicpress-pt' ),
			'section'     => 'section_footer',
		) );

		// Section: section_custom_code.
		$this->wp_customize->add_control( 'custom_js_head', array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Custom JavaScript (head)', 'medicpress-pt' ),
			'description' => esc_html__( 'You have to include the &lt;script&gt;&lt;/script&gt; tags as well. Paste your Google Analytics tracking code here.', 'medicpress-pt' ),
			'section'     => 'section_custom_code',
		) );

		$this->wp_customize->add_control( 'custom_js_footer', array(
			'type'        => 'textarea',
			'label'       => esc_html__( 'Custom JavaScript (footer)', 'medicpress-pt' ),
			'description' => esc_html__( 'You have to include the &lt;script&gt;&lt;/script&gt; tags as well.', 'medicpress-pt' ),
			'section'     => 'section_custom_code',
		) );

		// Section: section_other.
		$this->wp_customize->add_control( 'show_acf', array(
			'type'        => 'select',
			'label'       => esc_html__( 'Show ACF admin panel?', 'medicpress-pt' ),
			'description' => esc_html__( 'If you want to use ACF and need the ACF admin panel set this to <strong>Yes</strong>. Do not change if you do not know what you are doing.', 'medicpress-pt' ),
			'section'     => 'section_other',
			'choices'     => array(
				'no'  => esc_html__( 'No', 'medicpress-pt' ),
				'yes' => esc_html__( 'Yes', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'use_minified_css', array(
			'type'    => 'select',
			'label'   => esc_html__( 'Use minified theme CSS', 'medicpress-pt' ),
			'section' => 'section_other',
			'choices' => array(
				'no'  => esc_html__( 'No', 'medicpress-pt' ),
				'yes' => esc_html__( 'Yes', 'medicpress-pt' ),
			),
		) );
		$this->wp_customize->add_control( 'charset_setting', array(
			'type'     => 'select',
			'label'    => esc_html__( 'Character set for Google Fonts', 'medicpress-pt' ),
			'section'  => 'section_other',
			'choices'  => array(
				'latin'        => 'Latin',
				'latin-ext'    => 'Latin Extended',
				'cyrillic'     => 'Cyrillic',
				'cyrillic-ext' => 'Cyrillic Extended',
				'vietnamese'   => 'Vietnamese',
				'greek'        => 'Greek',
				'greek-ext'    => 'Greek Extended',
			),
		) );
	}
}
