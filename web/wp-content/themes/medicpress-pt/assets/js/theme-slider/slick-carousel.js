/* global MedicPressSliderCaptions */

/**
 * Slick Carousel - Theme Slider
 */

define( [ 'jquery', 'underscore', 'SlickCarousel', 'isElementInView' ], function( $, _, slick, isElementInView ) {
	'use strict';

	var $captions = {
			mainContainer: $( '.js-pt-slick-carousel-captions-container' ),
			container:     $( '.js-pt-slick-carousel-captions' ),
			title:         $( '.js-pt-slick-carousel-captions-title' ),
			text:          $( '.js-pt-slick-carousel-captions-text' ),
		},
		$currentSliderNumber       = $( '.js-pt-slick-carousel-slide-current-number' ),
		transitionClass            = 'is-in-transition',
		youtubeVideoContainerClass = '.js-carousel-item-yt-video';

	var SlickCarousel = function( $slider ) {
		this.$slider          = $slider;
		this.$parentContainer = $slider.parent();

		if ( this.$slider.length ) {
			this.initializeCarousel();
			this.pauseCarouselIfNotVisible();
			this.onSliderChangeEvents();
			this.registerModalEvents();
		}

		return this;
	};

	_.extend( SlickCarousel.prototype, {

		onSliderChangeEvents: function() {
			this.$slider.on( 'beforeChange', _.bind( function( ev, slick, currentSlide, nextSlide ) {
				if ( this.$slider.length && 'object' === typeof MedicPressSliderCaptions ) {
					this.changeCaptions( slick, nextSlide );
				}
			}, this ) );

			this.$slider.on( 'afterChange', _.bind( function( ev, slick, currentSlide ) {
				if ( this.$slider.length && 'object' === typeof MedicPressSliderCaptions ) {
					$captions.container.removeClass( transitionClass );
				}

				this.changeNavigationCount( currentSlide );
			}, this ) );

			return this;
		},

		/**
		 * Change the title and the text for the current (new) slider.
		 * Captions for the theme slider - change them in the out-of-bounds element.
		 */
		changeCaptions: function( slick, nextSlide ) {
			$captions.container.addClass( transitionClass );
			if ( MedicPressSliderCaptions[ nextSlide ].is_video ) {
				if ( Modernizr.mq( '(max-width: 991px)' ) ) {
					$captions.container.slideUp( 300 );
				}
				else {
					$captions.container.hide();

					// Hide the main captions container, so that video slides can link to a video,
					// otherwise this main container blocks the link and it can't be clicked.
					$captions.mainContainer.hide();
				}
			}
			else {
				// Show the main captions container, so that normal image slides can display captions.
				$captions.mainContainer.show();
			}

			setTimeout( function() {
				$captions.title.text( MedicPressSliderCaptions[ nextSlide ].title );
				$captions.text.html( MedicPressSliderCaptions[ nextSlide ].text );
				if ( ! MedicPressSliderCaptions[ nextSlide ].is_video ) {
					if ( Modernizr.mq( '(max-width: 991px)' ) ) {
						$captions.container.slideDown( 300 );
					}
					else {
						$captions.container.show();
					}
				}
			}, slick.options.speed );

			return this;
		},

		/**
		 * Pause carousel, if it's not visible and only if it's set to autoplay.
		 */
		pauseCarouselIfNotVisible: function() {
			$( document ).on( 'scroll', _.bind( _.throttle( function() {
				if ( this.$slider.slick( 'slickGetOption', 'autoplay' ) ) {
					if ( isElementInView( this.$slider ) ) {

						// 'slickPlay' also sets 'autoplay' option to true!
						// https://github.com/kenwheeler/slick#methods
						this.$slider.slick( 'slickPlay' );
					}
					else {
						this.$slider.slick( 'slickPause' );
					}
				}
			}, 1000, { leading: false } ), this ) );

			return this;
		},

		/**
		 * Initialize the Slick Carousel.
		 */
		initializeCarousel: function() {
			// Move the Twitter Bootstrap modal markup for video slides to just before closing body tag.
			this.moveModalsToRoot();

			// Initialize Slick Carousel.
			this.$slider.slick();

			// If first slide is video, hide the captions container, so it can be clicked.
			if ( 'object' === typeof MedicPressSliderCaptions && MedicPressSliderCaptions[0].is_video ) {
				$captions.mainContainer.hide();
			}

			// Show the whole slider (parent container is hidden by default).
			this.$parentContainer.css( 'visibility', 'visible' );

			return this;
		},

		/**
		 * Change the count of the current slide in the navigation container.
		 * Add a leading zero to a single digit. 1 -> 01, 12 -> 12.
		 */
		changeNavigationCount: function( currentSlide ) {
			$currentSliderNumber.html( ( '00' + (currentSlide + 1) ).slice(-2) );

			return this;
		},

		/**
		 * Move all video slide modal markup before the closing body tag.
		 */
		moveModalsToRoot: function() {
			this.$slider.find( '.js-pt-slick-carousel-video-modal-container' ).detach().children().appendTo( document.body );
		},

		/**
		 * Register the Bootstrap events for each modal used in the theme slider.
		 */
		registerModalEvents: function() {
			$( '.js-pt-slick-carousel-video-modal' ).on( 'show.bs.modal', _.bind( function ( e ) {
				if ( this.$slider.slick( 'slickGetOption', 'autoplay' ) ) {
					this.$slider.slick( 'slickPause' );
				}
			}, this ) );
		}

	} );

	return SlickCarousel;
} );
