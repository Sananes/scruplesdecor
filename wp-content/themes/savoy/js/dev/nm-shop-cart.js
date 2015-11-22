(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize cart scripts
		 */
		cart_init: function() {
			var self = this;
			
			// Init quantity buttons
			self.shopInitQuantity($('#nm-cart-product-summary'));
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.cart = $.nmTheme.cart_init;
	
})(jQuery);