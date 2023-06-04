jQuery( document ).ready( function() {
	EndiGuardWP_IP_Protection.init();
});

var EndiGuardWP_IP_Protection = ( function( $ ) {

	var init = function() {
		notification = new EndiGuardWP_Notification( endiguardwp_js_object.lang );
		var elements = $( ".endiguardwp-ip-protection-ip" );
		var droppers = $( ".endiguardwp-ip-protection-list" );

		// the couple (hover, toggle) brings a bug when refreshing the page with the mouse on an element
		droppers.on( "mouseenter", ".endiguardwp-ip-protection-ip", function() {
			$( this ).find( ".endiguardwp-ip-protection-buttons" ).show();
		} );
		droppers.on( "mouseleave", ".endiguardwp-ip-protection-ip", function() {
			$( this ).find( ".endiguardwp-ip-protection-buttons" ).hide();
		} );
		
		droppers.on( "click", ".endiguardwp-ip-protection-remove", function() {
			remove( this );
		} );
		droppers.on( "click", ".endiguardwp-ip-protection-moveToWhitelist", function() {
			move( this, "whitelist" );
		} );
		droppers.on( "click", ".endiguardwp-ip-protection-moveToBlacklist", function() {
			move( this, "blacklist" );
		} );

		$( ".endiguardwp-ip-protection-search" ).on( "keyup", function() {
			filter( this );
		} );
		$( ".endiguardwp-ip-protection-add" ).on( "click", function() {
			add( this );
		} );
		$( ".endiguardwp-ip-protection-add-input" ).on( "keyup", function() {
			check_format( this );
		} );
		$( ".endiguardwp-ip-protection-confirm" ).on( "click", function() {
			confirm( this );
		} );
		$( ".endiguardwp-ip-protection-cancel" ).on( "click", function() {
			toggleControls( this );
		} );
		$( ".endiguardwp-ip-protection-flush" ).on( "click", function() {
			flush( this );
		} );
	};

	var getOrigin = function( element ) {
		var list = $( element ).closest( ".endiguardwp-ip-protection-list" );
		var id = list.attr( "class" ).split( " " )[2];

		return id;		
	};

	var remove = function( element ) {
		var list = getOrigin( element );
		var ip = $( element ).closest( ".endiguardwp-ip-protection-ip" );
		var id = ip.attr( "class" ).split( " " )[1];

		res = submit( "delete_ip", { "list_id" : list, "ip_id" : id }, function() {
			ip.remove();
		} );
	};

	var move = function( element, dest ) {
		var list_old = getOrigin( element );
		var list_new = $( ".endiguardwp-ip-protection-"+dest ).attr( "class" ).split( " " )[2];
		var ip = $( element ).closest( ".endiguardwp-ip-protection-ip" );
		var id = ip.attr( "class" ).split( " " )[1];

		submit( "update_ip", { "list_id_from" : list_old, "list_id_to" : list_new, "ip_id" : id }, function() {
			$( ".endiguardwp-ip-protection-"+dest ).append( ip );
			ip.find( ".endiguardwp-ip-protection-buttons" ).hide();
			ip.find( ".endiguardwp-ip-protection-moveToWhitelist" ).remove();
			ip.find( ".endiguardwp-ip-protection-moveToBlacklist" ).remove();
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
	};

	var filter = function( input ) {
		var value = $( input ).val();
		var elems = $( input ).next( "ul" ).children();

		elems.filter( function() {
			$( this ).toggle( $( this ).text().indexOf( value ) > -1 );
		} );
	};

	var flush = function( element ) {
		var listElem = $( element ).closest( ".endiguardwp-container" ).find( ".endiguardwp-ip-protection-list" );
		var list = getOrigin( listElem );

		submit( "clean_list", { "list_id" : list }, function() {
			listElem.empty();
		} );
	};
	
	var add = function( element ) {
		var input = $( element ).siblings( ".endiguardwp-ip-protection-add-input" );
		input.val( "" );
		toggleControls( element );
	};

	var confirm = function( element ) {
		var listElem = $( element ).closest( ".endiguardwp-container" ).find( ".endiguardwp-ip-protection-list" );
		var list = getOrigin( listElem );
		var ip = $( element ).siblings( ".endiguardwp-ip-protection-add-input" );

		submit( "add_ip", { "list_id" : list, "ip" : ip.val() }, function() {
			addElement( ip.val(), list, listElem );
			toggleControls( element );
		} );
	};

	var toggleControls = function( element ) {
		var controls = $( element ).closest( ".endiguardwp-controls" ).children();
		for ( var i = 0 ; i < controls.length ; i++ ) {
			$( controls[i] ).toggle();
		}
	};

	var check_format = function( element ) {
		var value = $( element ).val();
		var regex = /^(?:(?:25[0-5]|2[0-4]?\d?|1\d?\d?|[3-9]\d?|0)\.){3}(?:25[0-5]|2[0-4]?\d?|1\d?\d?|[3-9]\d?|0)$/;

		if ( regex.test( value ) )
			$( ".endiguardwp-ip-protection-confirm" ).prop( "disabled", false );
		else
			$( ".endiguardwp-ip-protection-confirm" ).prop( "disabled", true );
	};

	var addElement = function( ip, list, listElem ) {
		if ( list === "2" )
			var buttons = "<span class=\"endiguardwp-ip-protection-buttons\"><i class=\"fa fa-check-circle endiguardwp-ip-protection-moveToWhitelist\" title=\"" + endiguardwp_js_object.lang.moveToWhitelist + "\"></i><i class=\"fa fa-minus-circle endiguardwp-ip-protection-moveToBlacklist\" title=\"" + endiguardwp_js_object.lang.moveToBlacklist + "\"></i><i class=\"fa fa-trash-alt endiguardwp-ip-protection-remove\" title=\"" + endiguardwp_js_object.lang.remove + "\"></i></span>";
		else
			var buttons = "<span class=\"endiguardwp-ip-protection-buttons\"><i class=\"fa fa-trash-alt endiguardwp-ip-protection-remove\" title=\"" + endiguardwp_js_object.lang.remove + "\"></i></span>";

		var newElem = "<li class=\"endiguardwp-ip-protection-ip\">" + ip + buttons + "</li>";
		
		listElem.append( newElem );
	};

	return {
		init: init
	};

} )( jQuery );
