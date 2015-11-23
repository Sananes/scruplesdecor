(function($) {
	
	'use strict';
	
	$(document).ready(function() {
		
		var animTimeout = 250;
		
		
		/* Show register form */
		function showRegisterForm() {
			// Header links
			$('#nm-show-register-link').addClass('hide');
			$('#nm-show-login-link').removeClass('hide');
			
			// Login/register form
			$('#nm-login-wrap').removeClass('show');
			setTimeout(function() {
				$('#nm-register-wrap').addClass('show slide');
				$('#nm-login-wrap').removeClass('slide');
			}, animTimeout);
		};
		
		/* Show login form */
		function showLoginForm() {
			// Header links
			$('#nm-show-login-link').addClass('hide');
			$('#nm-show-register-link').removeClass('hide');
			
			// Login/register form
			$('#nm-register-wrap').removeClass('show');
			setTimeout(function() {
				$('#nm-login-wrap').addClass('show slide');
				$('#nm-register-wrap').removeClass('slide');
			}, animTimeout);
		};
		
		
		/* Bind: Show register form header link */
		$('#nm-show-register-link').bind('click', function(e) {
			e.preventDefault();
			showRegisterForm();
		});
		
		
		/* Bind: Show login form header link */
		$('#nm-show-login-link').bind('click', function(e) {
			e.preventDefault();
			showLoginForm();
		});
		
		
		/* Bind: Show register form button */
		$('#nm-show-register-button').bind('click', function(e) {
			e.preventDefault();
			showRegisterForm();
		});
		
		
		/* Bind: Show login form button */
		$('#nm-show-login-button').bind('click', function(e) {
			e.preventDefault();
			showLoginForm();
		});
		
	});
})(jQuery);
