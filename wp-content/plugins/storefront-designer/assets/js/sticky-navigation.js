jQuery(document).ready(function() {
	if ( jQuery( 'body' ).hasClass( 'admin-bar' ) ) {
		var topSpacing = 32;
	} else {
		var topSpacing = 0;
	}

	if ( jQuery( window ).width() > 768 ) {
		jQuery( '.sd-sticky-navigation' ).sticky({
			topSpacing: topSpacing,
			responsiveWidth: true,
		});
	}
});