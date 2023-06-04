jQuery( document ).ready( function() {
	EndiGuardWP_Settings.init();
} );

var EndiGuardWP_Settings = ( function( $ ) {

	notification = new EndiGuardWP_Notification();

	var init = function() {
		$( ".endiguardwp-container button" ).on( "click", function( e ) {
			submit( this );
		} );
	};

	var submit = function( element ) {
		var id = $( element ).attr( "id" );
		var data = {
			_ajax_nonce: endiguardwp_js_object.ajax.nonce,
			action: endiguardwp_js_object.ajax.action
		};

		switch ( id ) {
			case "endiguardwp-license-button":
				data["request"] = "add_license";
				data["datas"] = {
					license: $( element ).siblings( "input" ).val()
				};
				break;
			case "endiguardwp-clear-cache-button":
				data["request"] = "clear_cache";
				data["datas"] = {
					clear_cache: true
				};
				break;
			default:
				break;
		}

		$.ajax( {
			url: endiguardwp_js_object.ajax.url,
			type: "POST",
			dataType: "json",
			data: data,
			success: function( response ) {
				notification.show( response );
			}
		} );
	};

	return {
		init: init
	};

} )( jQuery );
