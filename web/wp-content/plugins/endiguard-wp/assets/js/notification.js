class EndiGuardWP_Notification {

	constructor() {
		this.animation = new EndiGuardWP_Animation();
	}

	show( response ) {
		let html;
		let rand = Math.random().toString().substr( 2, 9 );
		let type = response.success ? "success" : "error";

		html = `<div class="endiguardwp-notification endiguardwp-notification-${type} ${rand}">`;
		html += `<div class="endiguardwp-notification-content">`;
		html += response.data;
		html += `</div>`;
		html += `</div>`;

		document.getElementById( "endiguardwp-notifications" ).insertAdjacentHTML( "beforeend", html );
		this.animation.fadeIn( document.getElementsByClassName( rand )[0] );
		this.hide( document.getElementsByClassName( rand )[0] );
	}

	hide( element ) {
		setTimeout( () => {
			this.animation.fadeOut( element, this.remove );
		}, 5000 );
	}

	remove( element ) {
		element.parentNode.removeChild( element );
	}
}
