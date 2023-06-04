/* global YT */

/**
 * Youtube events for Slick Carousel - Theme Slider and person profile widget.
 */

define( [ 'jquery' ], function( $ ) {
	'use strict';

	// Everything has to be global, so that it can be used in the "onYouTubeIframeAPIReady" callback.
	var themeSlider,
		themeSliderOptions         = {},
		themeSliderVideoModalClass = '.js-pt-slick-carousel-video-modal',
		youtubeVideoClass          = '.js-carousel-item-yt-video',
		youtubeVideoLinkClass      = '.js-carousel-item-yt-video-link',
		$personProfileSliders      = $( '.js-person-profile-initialize-carousel' );

	var YoutubeEvents = function( themeSliderSlickCarouselInstance ) {
		// Get slider instance and get it's default settings.
		themeSlider                     = themeSliderSlickCarouselInstance;
		themeSliderOptions.pauseOnHover = themeSlider.$slider.slick( 'slickGetOption', 'pauseOnHover');
		themeSliderOptions.autoplay     = themeSlider.$slider.slick( 'slickGetOption', 'autoplay');

		/**
		 * Global function (it must be), which is triggered, when Youtube API loads.
		 * Creates Youtube objects of existing iframes and registers player event listeners.
		 */
		window.onYouTubeIframeAPIReady = function() {
			// Register event listeners only if the theme slider has slides.
			if ( themeSlider.$slider.length ) {
				$( themeSliderVideoModalClass ).find( youtubeVideoClass + ' iframe' ).each( function( index, element ) {
					new YT.Player( $( element ).attr( 'id' ) );
				});
			}

			// Register YT players for the Person Profile widget sliders.
			if ( $personProfileSliders.length ) {
				$personProfileSliders.find( youtubeVideoClass + ' iframe' ).each( function( index, element ) {
					new YT.Player( $( element ).attr( 'id' ) );
				});
			}
		};

		// Trigger the YouTube API.
		this.initializeYoutubeIframeApi();

		// Register the slick carousel events.
		this.registerSliderEvents();

		// Register the Twitter Bootstrap modal events.
		this.registerModalEvents();
	};

	_.extend( YoutubeEvents.prototype, {
		/**
		 * Hook into the slider events.
		 * Pause the youtube video, when the slide is changed.
		 */
		registerSliderEvents: function() {
			// Theme slider.
			themeSlider.$slider.on( 'beforeChange', _.bind( function( ev, slick, currentSlide, nextSlide ) {
				var $currentSlideVideoLink = $( ev.target ).find( '.slick-slide[data-slick-index="' + currentSlide + '"] ' + youtubeVideoLinkClass );

				if( $currentSlideVideoLink.length ) {
					YT.get( $currentSlideVideoLink.data( 'video-id' ) ).pauseVideo();
				}
			}, this ) );

			// Person profile slider.
			$personProfileSliders.on( 'beforeChange', _.bind( function( ev, slick, currentSlide, nextSlide ) {
				var $currentSlideDiv = $( ev.target ).find( '.slick-slide[data-slick-index="' + currentSlide + '"] ' + youtubeVideoClass);

				if( $currentSlideDiv.length ) {
					YT.get( $currentSlideDiv.children( 'iframe' ).attr( 'id' ) ).pauseVideo();
				}
			}, this ) );
		},

		/**
		 * Loads the Youtube iframe API code asynchronously and fires the onYouTubeIframeAPIReady function.
		 */
		initializeYoutubeIframeApi: function() {
			var tag = document.createElement( 'script' ),
				firstScriptTag = document.getElementsByTagName( 'script' )[0];

			tag.src = 'https://www.youtube.com/iframe_api';
			firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
		},

		/**
		 * Register the Bootstrap events for each YouTube modal used in the theme slider.
		 */
		registerModalEvents: function() {
			$( '.js-pt-slick-carousel-video-modal' ).on( 'shown.bs.modal', function ( e ) {
				var ytVideoId = $( e.currentTarget ).find( youtubeVideoClass + ' > iframe' ).attr( 'id' );

				if ( ytVideoId !== undefined ) {
					YT.get( ytVideoId ).playVideo();
				}
			} );

			$( '.js-pt-slick-carousel-video-modal' ).on( 'hide.bs.modal', function ( e ) {
				var ytVideoId = $( e.currentTarget ).find( youtubeVideoClass + ' > iframe' ).attr( 'id' );

				if ( ytVideoId !== undefined ) {
					YT.get( ytVideoId ).pauseVideo();
				}
			} );
		}
	} );

	return YoutubeEvents;
});
