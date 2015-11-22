(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize checkout scripts
		 */
		checkout_init: function() {
			var self = this,
				$showLoginButton = $('#nm-show-login'),
				$checkoutOverlay = $('#nm-checkout-login-overlay');
			
			
			/* Bind: "Show login" button */
			$showLoginButton.bind('click', function() {
				if ($checkoutOverlay.hasClass('show')) {
					$checkoutOverlay.addClass('fade-out');
					setTimeout(function() {
						$checkoutOverlay.removeClass('show fade-out');
						$showLoginButton.removeClass('active');
					}, self.panelsAnimSpeed);
				} else {
					$showLoginButton.addClass('active');
					$checkoutOverlay.addClass('show');
				}
			});
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.checkout = $.nmTheme.checkout_init;
	
})(jQuery);
