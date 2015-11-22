(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize Quickview
		 */
		quickview_init: function() {
			var self = this,
				$qvContainer = $('#nm-quickview'),
				$qvOverlay = $('<div id="nm-quickview-overlay" class="mfp-bg nm-mfp-fade-in"></div>'),
				productId;
			
			
			/* Bind: Quick view links */
			self.$body.on('click', '.nm-quickview-btn', function(e) {
				e.preventDefault();
				
				productId = $(this).data('product_id');
				
				if (productId) {
					self.$html.css('width', 'auto');
					
					$qvOverlay.appendTo(self.$body);
					$qvOverlay.addClass('show mfp-ready nm-loader');
					
					//$qvContainer.addClass('hide');
					
					_qvLoadProduct();
				}
			});
			
			
			/* Function: Load product with AJAX */
			var _qvLoadProduct = function() {
				var ajaxUrl,
					data = {
						product_id: productId
					};
				
				// Use new WooCommerce endpoint URL if available
				if (typeof wc_add_to_cart_params !== 'undefined') {
					ajaxUrl = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'nm_ajax_load_product'); // WooCommerce Ajax endpoint URL (available since 2.4)
				} else {
					ajaxUrl = nm_wp_vars.ajaxUrl;
					data['action'] = 'nm_ajax_load_product';
				}
				
				window.nm_quickview_get_product = $.ajax({
					type: 'POST',
					url: ajaxUrl,
					data: data,
					dataType: 'html',
					cache: false,
					headers: {'cache-control': 'no-cache'},
					beforeSend: function() {
						// Check previous requests
						if (typeof window.nm_quickview_get_product === 'object') {
							window.nm_quickview_get_product.abort();
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log('NM: AJAX error - _qvLoadProduct() - ' + errorThrown);
						
						// Remove 'auto' width
						self.$html.css('width', '');
														
						// Remove overlay
						$qvOverlay.removeClass('mfp-ready mfp-removing').remove();
					},
					success: function(data) {
						$qvContainer.html(data);
						
						// Get added elements
						var $currentContainer = $qvContainer.children('#product-'+productId),
							$productForm = $currentContainer.find('form.cart'),
							$lastImg = $('#nm-quickview-slider').find('img').last();
						
						$lastImg.one('load', function() { // Note: Using ".one()" to make sure the event is only fired once (buggy otherwise)
							// Variable product
							if ($currentContainer.hasClass('product-variable')) {
								// Bind WooCommerce variation-form events
								// Source: "../plugins/woocommerce/assets/js/frontend/add-to-cart-variation.js" (line 538)
								$productForm.wc_variation_form().find('.variations select:eq(0)').change();
								
								// Init custom select boxes
								$productForm.find('select').selectOrDie(self.shopSelectConfig);
								
								// Variation details
								self.shopToggleVariationDetails(); // Show if not empty
								$productForm.on('found_variation', function() { // Bind: WooCommerce "found_variation" event
									self.shopToggleVariationDetails();
								});
								
								// WooCommerce event: Go to first slide when variation select changes
								$productForm.on('woocommerce_variation_select_change', function() {
									if (self.qvHasSlider) {
										self.$qvSlider.slick('slickGoTo', 0, false); // Args: (event, slideIndex, skipAnimation)
									}
								});
							}
							
							// Add quantity buttons
							self.shopInitQuantity($('#nm-qv-product-summary'));
																					
							// Product form: Prevent double submission
							// Note: This is now done in "nm-shop-add-to-cart.js" instead
							/*$productForm.submit(function() {
								$productForm.find(':submit').attr('disabled', 'disabled');
							});*/
							
							_qvShowModal();
						});
					}
				});
			},
			
			
			/* Function: Show quick view modal */
			_qvShowModal = function() {
				// Open via API
				$.magnificPopup.open({
					mainClass: 'nm-mfp-quickview nm-mfp-fade-in',
					closeMarkup: '<a class="mfp-close nm-font nm-font-close2"></a>',
					removalDelay: 180,
					items: {
						src: $qvContainer,
						type: 'inline'
					},
					callbacks: {
						open: function() {
							$qvOverlay.removeClass('nm-loader'); // Hide 'loader'
							
							_qvInitSlider();
							
							// Bind quick view overlay "touchstart" event
							$qvOverlay.one('touchstart.qv', function() {
								// Close quick view
								$.magnificPopup.close();
							});
						},
						beforeClose: function() {
							$qvOverlay.addClass('mfp-removing'); // Fade-out overlay
						},
						close: function() {
							self.$html.css('width', '');
														
							$qvOverlay.removeClass('mfp-ready mfp-removing').remove(); // Remove overlay
						}
					}
				});
			},
			
			
			/* Function: Initialize quick view slider */
			_qvInitSlider = function() {
				self.qvHasSlider = false;
				self.$qvSlider = $('#nm-quickview-slider');
				
				if (self.$qvSlider.children().length > 1) {
					self.qvHasSlider = true;
					
					self.$qvSlider.slick({
						//adaptiveHeight: true,
						prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-play flip"></i></a>',
						nextArrow: '<a class="slick-next"><i class="nm-font nm-font-play"></i></a>',
						dots: false,
						infinite: false,
						speed: 350
					});
					
					// Event: Slick slide - Init
					/*self.$qvSlider.on('init', function() {
						$qvContainer.removeClass('hide');
					});*/
				}/* else {
					$qvContainer.removeClass('hide');
				}*/
			};
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.quickview = $.nmTheme.quickview_init;
	
})(jQuery);
