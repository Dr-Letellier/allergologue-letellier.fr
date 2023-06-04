/* global MedicPressVars */

// config
require.config( {
	paths: {
		jquery:          'assets/js/fix.jquery',
		underscore:      'assets/js/fix.underscore',
		util:            'bower_components/bootstrap/js/dist/util',
		alert:           'bower_components/bootstrap/js/dist/alert',
		button:          'bower_components/bootstrap/js/dist/button',
		carousel:        'bower_components/bootstrap/js/dist/carousel',
		collapse:        'bower_components/bootstrap/js/dist/collapse',
		dropdown:        'bower_components/bootstrap/js/dist/dropdown',
		modal:           'bower_components/bootstrap/js/dist/modal',
		scrollspy:       'bower_components/bootstrap/js/dist/scrollspy',
		tab:             'bower_components/bootstrap/js/dist/tab',
		tooltip:         'bower_components/bootstrap/js/dist/tooltip',
		popover:         'bower_components/bootstrap/js/dist/popover',
		SlickCarousel:   'bower_components/slick-carousel/slick/slick',
		isElementInView: 'assets/js/utils/isElementInView',
		stampit:         'assets/js/vendor/stampit',
	}
} );

require.config( {
	baseUrl: MedicPressVars.pathToTheme
} );

require( [
		'jquery',
		'underscore',
		'isElementInView',
		'assets/js/utils/objectFitFallback',
		'assets/js/utils/easeInOutQuad',
		'assets/js/theme-slider/slick-carousel',
		'assets/js/theme-slider/vimeo-events',
		'assets/js/theme-slider/youtube-events',
		'vendor/proteusthemes/sticky-menu/assets/js/sticky-menu',
		'assets/js/TouchDropdown',
		'SlickCarousel',
		'util',
		'carousel',
		'collapse',
		'tab',
		'modal',
], function ( $, _, isElementInView, objectFitFallback, easeInOutQuad, ThemeSlider, VimeoEvents, YoutubeEvents ) {
	'use strict';

	/**
	 * Footer widgets fix
	 */
	$( '.col-lg-__col-num__' ).removeClass( 'col-lg-__col-num__' ).addClass( 'col-lg-3' );

	/**
	 * Slick carousel for the Person profile widget (from the PW composer package).
	 */
	$( '.js-person-profile-initialize-carousel' ).slick();

	/**
	 * Slick carousel for the Testimonials widget (from the PW composer package).
	 */
	$( '.js-testimonials-initialize-carousel' ).slick();

	/**
	 * Slick Carousel - Theme Slider
	 */
	(function () {
		var themeSliderInstance = new ThemeSlider( $( '.js-pt-slick-carousel-initialize-slides' ) );
		new VimeoEvents( themeSliderInstance );

		// Load the YT events only, if there are items on the page that need it.
		// (theme slider with YT video or person profile with YT video).
		if ( $( '.js-carousel-item-yt-video-link' ).length || $( '.js-carousel-item-yt-video' ).length ) {
			new YoutubeEvents( themeSliderInstance );
		}
	})();

	/**
	 * Animate the scroll, when back to top is clicked
	 */
	( function () {
		$( '.js-back-to-top' ).click( function ( ev ) {
			ev.preventDefault();

			$( 'body, html' ).animate( {
				scrollTop: 0
			}, 700 );
		});
	} )();

	/**
	 * Object fit - fallback for old browsers
	 * @return {[type]} [description]
	 */
	(function () {
		if ( ! Modernizr.objectfit ) {
			// slider, page header (single, portfoliop)
			$('.js-object-fit-fallback').each(function () {
				objectFitFallback({
					$container: $(this)
				});
			});
		}
	}());

});
