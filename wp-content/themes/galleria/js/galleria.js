/**
 * galleria.js
 *
 * Javascript used by the Galleria theme.
 */
( function() {
	jQuery( window ).load( function() {
		jQuery( 'body' ).addClass( 'loaded' );
		jQuery( '.site' ).addClass( 'animated fadeIn' );
		jQuery( '.single_add_to_cart_button, .checkout-button' ).addClass( 'animated bounce' );

		// The star rating on single product pages
		var value = jQuery( '.star-rating > span' ).width();
		jQuery( '.woocommerce-product-rating .star-rating > span' ).css( 'width', 0 );
		jQuery( '.woocommerce-product-rating .star-rating > span' ).animate({
			width: value,
		}, 1500, function() {
		// Animation complete.
		});

		// Animate tabs
		jQuery( '.woocommerce-tabs ul.tabs li a' ).click( function () {
        	var destination = jQuery( this ).attr( 'href' );
        	jQuery( '.woocommerce-tabs' ).find( destination ).addClass( 'animated bounceInUp' );
    	});

		if ( jQuery( window ).width() > 767 ) {
	    	jQuery( 'ul.products' ).masonry({
				columnWidth: 'li.product',
				itemSelector: 'li.product',
				percentPosition: true
			});

			// Animate product headings
			jQuery( 'ul.products li.product:not(.product-category)' ).hover( function () {
	        	jQuery( this ).find( '.g-product-title' ).addClass( 'animated fadeIn' );
	        	jQuery( this ).find( '.button' ).addClass( 'animated bounceIn' );
	    	}, function() {
	    		jQuery( this ).find( '.g-product-title' ).removeClass( 'fadeIn' );
	    		jQuery( this ).find( '.button' ).removeClass( 'bounceIn' );
	    	});

	    	// Product button positioning
			jQuery( 'ul.products li.product .button' ).each(function() {
				var button 	= jQuery( this );
				var height 	= button.outerHeight();
				var width 	= button.outerWidth();

				button.css( 'margin-top', -height/2 ).css( 'margin-left', -width/2 );
			});

			// Product category title
			jQuery( 'ul.products li.product-category .g-product-title' ).each(function() {
				var title 	= jQuery( this );
				var height 	= title.outerHeight();
				var width 	= title.outerWidth();

				title.css( 'margin-top', -height/2 ).css( 'margin-left', -width/2 );
			});
	    }
	});

} )();
