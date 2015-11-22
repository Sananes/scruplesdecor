(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize scripts
		 */
		infload_init: function() {
			var self = this,			
				$shopPagination = self.$shopBrowseWrap.children('.nm-pagination');
		
			// Init
			if ($shopPagination.length && $shopPagination.hasClass('nm-infload')) {
				// Bind
				self.shopInfLoadBind();
			}
		},
		
		
		/**
		 *	Shop: Initialize infinite load
		 */
		shopInfLoadBind: function() {
			var self = this,
				$infloadControls = self.$shopBrowseWrap.children('.nm-infload-controls'),					
				nextPageUrl;
			
			
			// Used to check if "infload" needs to be initialized after Ajax page load
			self.shopInfLoadBound = true;
			
			
			self.infloadScroll = ($infloadControls.hasClass('scroll-mode')) ? true : false;
			
			if (self.infloadScroll) {
				self.infscrollLock = false;
				
				var pxFromWindowBottomToBottom,
					pxFromMenuToBottom = Math.round(self.$document.height() - $infloadControls.offset().top);
					//bufferPx = 0;
				
				/* Bind: Window resize event to re-calculate the 'pxFromMenuToBottom' value (so the items load at the correct scroll-position) */
				var to = null;
				self.$window.resize(function() {
					if (to) { clearTimeout(to); }
					to = setTimeout(function() {
						var $infloadControls = self.$shopBrowseWrap.children('.nm-infload-controls'); // Note: Don't cache, element is dynamic
						pxFromMenuToBottom = Math.round(self.$document.height() - $infloadControls.offset().top);
					}, 100);
				});
				
				/* Bind: Window scroll event */
				self.$window.bind('smartscroll.infscroll', function() {
					if (self.infscrollLock) {
						return;
					}
					
					pxFromWindowBottomToBottom = 0 + self.$document.height() - (self.$window.scrollTop()) - self.$window.height();
					
					// If distance remaining in the scroll (including buffer) is less than the pagination element to bottom:
					if ((pxFromWindowBottomToBottom/* - bufferPx*/) < pxFromMenuToBottom) {
						self.shopInfLoadGetPage();
					}
				});
			} else {
				var $productsWrap = $('#nm-shop-products');
				
				/* Bind: "Load" button */
				$productsWrap.on('click', '.nm-infload-btn', function(e) {
					e.preventDefault();
					self.shopInfLoadGetPage();
				});
				
				/* Bind: Up-arrow button (visible after all products are loaded) */
				$productsWrap.on('click', '.nm-infload-to-top', function(e) {
					e.preventDefault();
					// Smooth-scroll to shop-top
					self.shopScrollToTop();
				});
			}
			
			
			if (self.infloadScroll) {
				self.$window.trigger('scroll'); // Trigger scroll in case the pagination element (+buffer) is above the window bottom
			}
		},
		
		
		/**
		 *	Shop: AJAX load next page
		 */
		shopInfLoadGetPage: function() {
			var self = this;
			
			if (self.shopAjax) return false;
			
			// Remove any visible shop notices
			self.shopRemoveNotices();
			
			// Get elements (these can be replaced with AJAX, don't pre-cache)
			var $nextPageLink = self.$shopBrowseWrap.children('.nm-infload-link').find('a'),
				$infloadControls = self.$shopBrowseWrap.children('.nm-infload-controls'),
				nextPageUrl = $nextPageLink.attr('href');
			
			if (nextPageUrl) {
				// Add/update the 'shop_load=products' query parameter to the page URL
				// Note: Don't use the 'data' setting in the '$.ajax' function or the query will be appended, not updated (if 'shop_load' is added to the URL)
				nextPageUrl = self.updateUrlParameter(nextPageUrl, 'shop_load', 'products');
				
				// Show 'loader'
				$infloadControls.addClass('nm-loader');
				
				self.shopAjax = $.ajax({
					url: nextPageUrl,
					dataType: 'html',
					cache: false,
					headers: {'cache-control': 'no-cache'},
					method: 'GET',
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log('NM: AJAX error - shopInfLoadGetPage() - ' + errorThrown);
					},
					complete: function() {
						// Hide 'loader'
						$infloadControls.removeClass('nm-loader');
					},
					success: function(response) {
						var $response = $('<div>' + response + '</div>'), // Wrap the returned HTML string in a dummy 'div' element we can get the elements
							$newElements = $response.children('.nm-products').children('li');
						
						// Append the new elements
						self.$shopBrowseWrap.find('.nm-products').append($newElements);
						
						// Load images (init Unveil)
						self.shopLoadImages();
						
						// Get the 'next page' URL
						nextPageUrl = $response.find('.nm-infload-link').children('a').attr('href');
						
						if (nextPageUrl) {
							$nextPageLink.attr('href', nextPageUrl);
						} else {
							self.$shopBrowseWrap.addClass('all-products-loaded');
							
							if (self.infloadScroll) {
								self.infscrollLock = true; // "Lock" scroll (no more products/pages)
							} else {
								$infloadControls.addClass('hide-btn'); // Hide "load" button (no more products/pages)
							}
							
							$nextPageLink.removeAttr('href');
						}
						
						self.shopAjax = false;
						
						if (self.infloadScroll) {
							self.$window.trigger('scroll'); // Trigger 'scroll' in case the pagination element (+buffer) is still above the window bottom
						}
					}
				});
			} else {
				if (self.infloadScroll) {
					self.infscrollLock = true; // "Lock" scroll (no more products/pages)
				}
			}
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.infload = $.nmTheme.infload_init;
	
})(jQuery);
