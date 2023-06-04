jQuery( function ( $ ) {
	'use strict';

	/**
	 * Purchase code activation AJAX call.
	 */
	$( '.js-pc-activate-button' ).on( 'click', function () {
		// Prepare data for AJAX call.
		var $button = $( this ),
				$pcField = $( '#pt-purchase-code' ),
				$emailField = $( '#pt-activation-email' ),
				$emailCheckbox = $( '#pt-subscribe-newsletter' );

		// Check if the purchase code field is not empty.
		if ( 0 === $pcField.val().length ) {
			$pcField.addClass( 'is-input-error' );
			return false;
		}

		// Check if the email field is not empty.
		if ( $emailField.length && 0 === $emailField.val().length ) {
			$emailField.addClass( 'is-input-error' );
			return false;
		}

		// Remove error class if it was set before.
		$pcField.removeClass( 'is-input-error' );
		$emailField.removeClass( 'is-input-error' );

		// Fields values.
		var fields = {
			purchase_code: $pcField.val(),
			email: $emailField.val() || null,
			envato_token: null,
			subscribe: $emailCheckbox.prop( 'checked' ) || null
		};

		// AJAX call to verify the purchase code.
		ajaxCall( 'pt_tr_activate_purchase_code', fields, $button );
	});


	/**
	 * Purchase code deactivation AJAX call.
	 */
	$( '.js-pc-deactivate-button' ).on( 'click', function () {
		// Prepare data for AJAX call.
		var $button = $( this ),
				$pcField = $( '#pt-purchase-code' );

		// Check if the purchase code field is not empty.
		if ( 0 === $pcField.val().length ) {
			$pcField.addClass( 'is-input-error' );
			return false;
		}

		// Remove error class if it was set before.
		$pcField.removeClass( 'is-input-error' );

		// Fields values.
		var fields = {
			purchase_code: $pcField.val(),
			email: null,
			envato_token: null,
			subscribe: null
		};

		// AJAX call to verify the purchase code.
		ajaxCall( 'pt_tr_deactivate_purchase_code', fields, $button );
	});


	/**
	 * ThemeForest/Envato token verification AJAX call.
	 */
	$( '.js-et-verify-button' ).on( 'click', function () {
		// Prepare data for AJAX call.
		var $button = $( this ),
				$etField = $( '#pt-envato-token' );

		// Check if the purchase code field is not empty.
		if ( $etField.length && 0 === $etField.val().length ) {
			$etField.addClass( 'is-input-error' );
			return false;
		}

		// Remove error class if it was set before.
		$etField.removeClass( 'is-input-error' );

		// Fields values.
		var fields = {
			purchase_code: null,
			email: null,
			envato_token: $etField.val() || null,
			subscribe: null
		};

		// AJAX call to verify the purchase code.
		ajaxCall( 'pt_tr_envato_token_verification', fields, $button );
	});


	/**
	 * The purchase code expiration update AJAX call.
	 */
	$( '.js-expiration-button' ).on( 'click', function () {
		// Prepare data for AJAX call.
		var $button = $( this ),
				$pcField = $( '#pt-purchase-code' );

		// Check if the purchase code field is not empty.
		if ( 0 === $pcField.val().length ) {
			$pcField.addClass( 'is-input-error' );
			return false;
		}

		// Remove error class if it was set before.
		$pcField.removeClass( 'is-input-error' );

		// Fields values.
		var fields = {
			purchase_code: $pcField.val(),
			email: null,
			envato_token: null,
			subscribe: null
		};

		// AJAX call to verify the purchase code.
		ajaxCall( 'pt_tr_purchase_code_expiration', fields, $button );
	});


	/**
	 * The main AJAX call, which executes the POST requests to our server with the data provided.
	 *
	 * @param string action The data to be passed to the AJAX call.
	 * @param obj    fields The input fields passed to the AJAX call.
	 * @param obj    $button The button that was clicked to trigger this AJAX call.
	 */
	function ajaxCall( action, fields, $button ) {
		$.ajax({
			method:      'POST',
			url:         PTTR.ajaxUrl,
			data:        {
				'action':   action,
				'fields':   fields,
				'security': PTTR.ajaxNonce
			},
			beforeSend:  function() {
				$button.after( '<span class="pt-tr__loader"><span class="spinner"></span><span class="screen-reader-text">Loading...</span></span>' );
				$button.prop( 'disabled', true );
			},
			complete:    function() {
				$button.siblings( '.pt-tr__loader' ).remove();
				$button.prop( 'disabled', false );
			}
		})
		.done( function( response ) {
			handleAjaxResponse( response, action, $button );
		})
		.fail( function( error ) {
			$( '.js-pc-message' ).html( PTTR.text.request_error + error.responseText );
		});
	}


	/**
	 * Handle the AJAX response, depending on other AJAX call parameters
	 *
	 * @param  json   response The JSON object response
	 * @param  string action   The AJAX action.
	 * @param  obj    $button  The jQuery object of the clicked button.
	 */
	function handleAjaxResponse( response, action, $button ) {
		if ( 'pt_tr_activate_purchase_code' === action ) {
			if ( response.data.status ) {
				$button.hide();
				$button.siblings('.js-pc-deactivate-button').show();
				$button.siblings('.js-expiration-button').show();
				$( '.js-pc-message' ).addClass( 'pt-success-message' );

				// Disable the purchase code input field.
				$( '#pt-purchase-code' ).prop( 'disabled', true );

				// Display the TF token block, if the purchase code was a ThemeForest purchase code.
				if ( 'tf' === response.data.purchase_code_type ) {
					$( '.js-envato-token-container' ).show();
					$( '#pt-activation-email' ).prop( 'disabled', true );
				}

				// Display the expiration date.
				if ( response.data.expires_formated ) {
					$( '.js-pc-expiration' ).html( response.data.expires_formated );
				}
			}

			$( '.js-pc-message' ).html( response.data.message );
		}
		else if ( 'pt_tr_deactivate_purchase_code' === action ) {
			if ( response.data.status ) {
				$button.hide();
				$button.siblings('.js-expiration-button').hide();
				$button.siblings('.js-pc-activate-button').show();
				$( '.js-pc-expiration' ).text( '' );
				$( '.js-envato-token-container' ).hide();
				$( '#pt-purchase-code' ).val( '' );

				// Enable the purchase code input field.
				$( '#pt-purchase-code' ).prop( 'disabled', false );

				if ( 'tf' === response.data.purchase_code_type ) {
					$( '#pt-activation-email' ).prop( 'disabled', false );
				}

			}
			$( '.js-pc-message' ).removeClass( 'pt-success-message' );
			$( '.js-pc-message' ).text( response.data.message );
		}
		else if ( 'pt_tr_envato_token_verification' === action ) {
			$( '.js-et-message' ).text( response.data.message );
		}
		else if ( 'pt_tr_purchase_code_expiration' === action ) {
			if ( response.data.status && response.data.expires_formated ) {
				$( '.js-pc-expiration' ).html( response.data.expires_formated );
			}

			$( '.js-pc-message' ).removeClass( 'pt-success-message' );
			$( '.js-pc-message' ).text( response.data.message );
		}
	}

} );
