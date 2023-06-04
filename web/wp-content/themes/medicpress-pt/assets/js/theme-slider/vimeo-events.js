/**
 * Vimeo events for Slick Carousel - Theme Slider and person profile widget.
 */

define( [ 'jquery', 'underscore' ], function( $, _ ) {
	'use strict';

	var playerOrigin        = '*',
			vimeoVideoClass     = '.js-carousel-item-vimeo-video',
			vimeoVideoLinkClass = '.js-carousel-item-vimeo-video-link';

	var VimeoEvents = function( slickCarouselInstance ) {
		this.sc                   = slickCarouselInstance;
		this.personProfileSliders = $( '.js-person-profile-initialize-carousel' );

		this.pauseOnHover = this.sc.$slider.slick( 'slickGetOption', 'pauseOnHover');
		this.autoplay     = this.sc.$slider.slick( 'slickGetOption', 'autoplay');

		// Register the slick carousel events.
		this.registerSliderEvents();

		// Register the Twitter Bootstrap modal events.
		this.registerModalEvents();

		return this;
	};

	_.extend( VimeoEvents.prototype, {
		/**
		 * Helper function for sending a message to the vimeo player.
		 */
		post: function( action, value, data ) {
			var sendData = {
				method: action
			};

			if ( value ) {
				sendData.value = value;
			}

			var message = JSON.stringify( sendData );

			// Send messages to current vimeo player.
			$( '#' + data.player_id )[0].contentWindow.postMessage( message, playerOrigin );
		},

		/**
		 * Hook into the slider events.
		 * Pause the vimeo video, when the slide is changed.
		 */
		registerSliderEvents: function() {
			// Theme slider.
			this.sc.$slider.on( 'beforeChange', _.bind( function( ev, slick, currentSlide, nextSlide ) {
				var $currentSlideVideoLink = $( ev.target ).find( '.slick-slide[data-slick-index="' + currentSlide + '"] ' + vimeoVideoLinkClass );

				// Pause the video, if the currentSlide was a Vimeo video.
				if( $currentSlideVideoLink.length ) {
					this.post( 'pause', '', { player_id: $currentSlideVideoLink.data( 'video-id' ) } );
				}
			}, this ) );

			// Person profile widget slider.
			this.personProfileSliders.on( 'beforeChange', _.bind( function( ev, slick, currentSlide, nextSlide ) {
				var $currentSlideDiv = $( ev.target ).find( '.slick-slide[data-slick-index="' + currentSlide + '"] ' + vimeoVideoClass );

				// Pause the video, if the currentSlide was a Vimeo video.
				if( $currentSlideDiv.length ) {
					this.post( 'pause', '', { player_id: $currentSlideDiv.children( 'iframe' ).attr( 'id' ) } );
				}
			}, this ) );
		},

		/**
		 * Register the Bootstrap events for each Vimeo modal used in the theme slider.
		 */
		registerModalEvents: function() {
			$( '.js-pt-slick-carousel-video-modal' ).on( 'shown.bs.modal', _.bind( function ( e ) {
				var vimeoVideoId = $( e.currentTarget ).find( vimeoVideoClass + ' > iframe' ).attr( 'id' );

				if ( vimeoVideoId !== undefined ) {
					this.post( 'play', '', { player_id: vimeoVideoId } );
				}
			}, this ) );

			$( '.js-pt-slick-carousel-video-modal' ).on( 'hide.bs.modal', _.bind( function ( e ) {
				var vimeoVideoId = $( e.currentTarget ).find( vimeoVideoClass + ' > iframe' ).attr( 'id' );

				if ( vimeoVideoId !== undefined ) {
					this.post( 'pause', '', { player_id: vimeoVideoId } );
				}
			}, this ) );
		}
	} );

	return VimeoEvents;
} );
