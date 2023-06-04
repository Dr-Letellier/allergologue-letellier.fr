jQuery( document ).ready( function() {
	EndiGuardWP_Automatic_Updates.init();
} );

var EndiGuardWP_Automatic_Updates = ( function( $ ) {

	var init = function() {
		notification = new EndiGuardWP_Notification( endiguardwp_js_object.lang );

		$( "#endiguardwp-checkpoint-new" ).off();
		$( "#endiguardwp-checkpoint-new" ).on( "click", function() {
			$( "#endiguardwp-checkpoint-new-element" ).show();
		} );

		$( "#endiguardwp-checkpoint-add" ).off();
		$( "#endiguardwp-checkpoint-add" ).on( "click", function() {
			add( this );
		} );

		$( ".endiguardwp-checkpoint-modification" ).off();
		$( "#endiguardwp-checkpoint-list" ).on( "click", ".endiguardwp-checkpoint-modification", function() {
			modification( this );
		} );

		$( ".endiguardwp-checkpoint-remove" ).off();
		$( "#endiguardwp-checkpoint-list" ).on( "click", ".endiguardwp-checkpoint-remove", function() {
			remove( this );
		} );
	};

	var add = function( button ) {
		var value = $( button ).closest( ".endiguardwp-checkpoint-element" ).find( ".endiguardwp-checkpoint-new-value" ).val();
		var html = "<li class=\"endiguardwp-checkpoint-element\"><span class=\"endiguardwp-checkpoint-value\">" + value + "</span><button type=\"button\" class=\"endiguardwp-checkpoint-modification\">" + endiguardwp_js_object.lang.modification + "</button><button type=\"button\" class=\"endiguardwp-checkpoint-remove\">" + endiguardwp_js_object.lang.remove + "</button></li>";

		$( html ).insertBefore( "#endiguardwp-checkpoint-new-element" );
		$( "#endiguardwp-checkpoint-new-element" ).hide();
		$( "#endiguardwp-checkpoint-new-element .endiguardwp-checkpoint-new-value" ).val( "" );

		submit( "add_checkpoint", { "url" : value }, function() {} );
	};

	var modification = function( button ) {
		var element = $( button ).closest( ".endiguardwp-checkpoint-element" );
		var value = $( element ).find( ".endiguardwp-checkpoint-value" ).text();
		$( element ).find( ".endiguardwp-checkpoint-value, .endiguardwp-checkpoint-modification, .endiguardwp-checkpoint-remove" ).hide();

		var html = "<input type=\"url\" class=\"endiguardwp-checkpoint-new-value\" value=\"" + value + "\"><button type=\"button\" class=\"endiguardwp-checkpoint-edit\">" + endiguardwp_js_object.lang.edit + "</button><button type=\"button\" class=\"endiguardwp-checkpoint-cancel\">" + endiguardwp_js_object.lang.cancel + "</button>";

		$( element ).append( html );

		$( element ).find( ".endiguardwp-checkpoint-edit" ).on( "click", function() {
			edit( element );	
		} );

		$( element ).find( ".endiguardwp-checkpoint-cancel" ).on( "click", function() {
			cancel( element );
		} );
	};

	var hideEditCancelButtons = function( element, edit ) {
		if ( edit ) {
			var value = $( element ).find( ".endiguardwp-checkpoint-new-value" ).val();
			$( element ).find( ".endiguardwp-checkpoint-value" ).text( value );
		}
		$( element ).find( ".endiguardwp-checkpoint-value, .endiguardwp-checkpoint-modification, .endiguardwp-checkpoint-remove" ).show();
		$( element ).find( ".endiguardwp-checkpoint-new-value, .endiguardwp-checkpoint-edit, .endiguardwp-checkpoint-cancel" ).remove();
	};

	var edit = function( element ) {
		var id = $( element ).attr( "class" ).split( " " )[1];
		var value = $( element ).find( ".endiguardwp-checkpoint-new-value" ).val();

		submit( "update_checkpoint", { "checkpoint_id" : id, "url" : value }, function() {
			hideEditCancelButtons( element, true );
		} );
	};

	var cancel = function( element ) {
		hideEditCancelButtons( element, false );
	};

	var remove = function( button ) {
		var element = $( button ).closest( ".endiguardwp-checkpoint-element" );
		var id = $( element ).attr( "class" ).split( " " )[1];

		submit( "delete_checkpoint", { "checkpoint_id" : id }, function() {
			$( element ).remove();
		} );
	};

	var submit = function( request, args, successCallback ) {
		var data = {
			_ajax_nonce: endiguardwp_js_object.ajax.nonce,
			action: endiguardwp_js_object.ajax.action,
			request: request,
			datas: args
		};

		$.ajax( {
			url: endiguardwp_js_object.ajax.url,
			type: "POST",
			dataType: "json",
			data: data,
			success: function( response ) {
				notification.show( response );
				if ( response.success )
					successCallback();
			}
		} );
	}

	return{
		init: init
	};

} )( jQuery );
