( function( $ ) {
	jQuery(window).load( function() {
		var headerHeight = jQuery( '.site-header' ).outerHeight();

		if ( jQuery(window).width() > 768 ) {
			jQuery( '.site' ).css( 'margin-top', headerHeight );
		}
	});
} )( jQuery );