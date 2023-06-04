/* global define */
define(['stampit', 'jquery'], function objectFitFallback (stampit, $) {
	return stampit({
		init: function () {
			var imgUrl = this.$container.find('img').prop('currentSrc');

			if (! imgUrl) {
				imgUrl = this.$container.find('img').prop('src');
			}

			if (imgUrl) {
				this.$container
					.css('backgroundImage', 'url(' + imgUrl + ')')
					.addClass('compat-object-fit');
			}
		},
		props: {
			$container: null
		},
	});
});
