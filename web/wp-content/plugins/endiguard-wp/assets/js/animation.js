class EndiGuardWP_Animation {

	constructor( ms = 1000 ) {
		this.ms = ms;
	}

	fadeIn( element ) {
		element.style.opacity = 0;

		let opacity = 0;
		let timer = setInterval( () => {
			opacity += 50 / this.ms;
			if ( opacity >= 1 ) {
				clearInterval( timer );
				opacity = 1;
			}
			element.style.opacity = opacity;
		}, 50 );
	}

	fadeOut( element, callback = false ) {
		element.style.opacity = 1;

		let opacity = 1;
		let timer = setInterval( () => {
			opacity -= 50 / this.ms;
			if ( opacity <= 0 ) {
				clearInterval( timer );
				opacity = 0;
			}
			element.style.opacity = opacity;
			if ( opacity == 0 && callback )
				callback( element );
		}, 50 );
	}
}
