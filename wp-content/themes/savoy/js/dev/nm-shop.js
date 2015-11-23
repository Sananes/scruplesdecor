(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize scripts
		 */
		shop_init: function() {
			var self = this;
			
			
			// Shop select config
			self.shopSelectConfig = {
				onOpen: function() {
					var $this = $(this);
					
					$this.closest('li').addClass('open');
					
					// Trigger "focusin" event on original select element to make sure WooCommerce updates the options
					$this.children('select').trigger('focusin');
				},
				onChange: function() {
					$(this).closest('li').removeClass('open');
				},
				onClose: function() {
					$(this).closest('li').removeClass('open');
				}
			};
			
			
			/* Shop */
			if (self.isShop) {
				// Shop vars and elements
				self.shopAjax = false;
				self.shopScrollOffset = parseInt(nm_wp_vars.shopScrollOffset);
				self.infloadScroll = false;
				self.categoryClicked = false;
				self.shopLoaderSpeed = 300;
				self.shopScrollSpeed = 410;
				self.$shopBrowseWrap = $('#nm-shop-browse-wrap');
				self.imageLazyLoading = (nm_wp_vars.shopImageLazyLoad != '0') ? true : false;
				if (nm_wp_vars.shopFiltersAjax != '0') {
					// Check if AJAX should be disabled on mobile devices
					self.filtersEnableAjax = (self.isTouch && nm_wp_vars.shopFiltersAjax != '1') ? false : true;
				} else {
					self.filtersEnableAjax = false;
				}
				self.isProductTagUrl = (self.$body.hasClass('tax-product_tag')) ? true : false; // Is this a product-tag page?
				self.searchAndTagsResetURL = null;
				
				
				// Set shop min-height
				self.shopSetMinHeight();
				
				
				/* Bind: Window resize */
				var timer = null;
				self.$window.resize(function() {
					if (timer) { clearTimeout(timer); }
					timer = setTimeout(function() {
						// Set shop min-height
						self.shopSetMinHeight();
					}, 250);
				});
				
				
				// Load product images (init Unveil)
				if (self.$pageIncludes.hasClass('banner-slider')) {
					// Wait for "banner-slider-loaded" event (banner slider changes height)
					self.$document.on('banner-slider-loaded', function() {
						self.shopUrlHashScroll();
						self.shopLoadImages();
					});
				} else {
					self.shopUrlHashScroll();
					self.shopLoadImages();
				}
				
				
				/* Bind: Back button "popstate" event */
				self.$window.on('popstate.nmshop', function(e) {
					// Return if no "popstate" tag/id is set
					if (!e.originalEvent.state) { return; }
					
					// Make sure the "popstate" event is ours (nmShop)
					if (e.originalEvent.state.nmShop) {
						// Load full page from saved "pushState" url
						self.shopGetPage(window.location.href, true);
					}
				});
				
				
				/* 
				 * Bind: Header main menu shop link 
				 * Note: "shop-link" class is added manually in WP admin
				 */
				$('#nm-main-menu-ul').children('.shop-link').find('> a').bind('click', function(e) {
					e.preventDefault();
					self.shopMaybeScrollToTop(); // Smooth-scroll to shop
				});
				
				
				/* Bind: Results bar reset link "click" event */
				self.$shopWrap.on('click', '#nm-shop-results-reset', function(e) {
					e.preventDefault();
					
					var isCategoryReset = $(this).closest('.nm-shop-results-bar').hasClass('is-category'), // Check if the reset button is for a category (if so, reset to "all", not last active shop page)
						searchAndTagsResetURL = (!isCategoryReset && self.searchAndTagsResetURL) ? self.searchAndTagsResetURL : $(this).data('shop-url');
					
					self.shopGetPage(searchAndTagsResetURL);
				});
			}
			
			
			/* 
			 * Variation selects - Bind: Product variations updated event - Triggered from "add-to-cart-variation.js"
			 * Note: See "self.shopSelectConfig" for related "focusin" event
			 */
			self.$document.on('woocommerce_update_variation_values', '#nm-variations-form', function() {
				// Update select(s) in case options have been added/removed by WooCommerce
				$('#nm-variations-form').find('select').each(function() {
					$(this).selectOrDie('update');
				});
			});
			
			
			/* Products */
			if (self.$pageIncludes.hasClass('products')) {
				/* Bind: Product hover image swap */
				if (self.isShop) {
					$('#nm-shop-products').on('hover', '.nm-products li.hover-image-load', function() { 
						self.productLoadHoverImage($(this));
					});
				} else {
					$('.nm-products').on('hover', '.hover-image-load', function() {
						self.productLoadHoverImage($(this));
					});
				}
			}
			
			
			// Only bind if add-to-cart redirect is disabled
			if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.cart_redirect_after_add !== 'yes') {
				/* Add-to-cart event: Show mini cart with loader overlay */
				self.$body.on('adding_to_cart', function(event, $button, data, quickviewOpen) {
					if (!self.quickviewIsOpen(quickviewOpen)) {
						self.widgetPanelShowCart(true); // Args: (showLoader)
					}
				});
				/* Add-to-cart event: Hide mini cart loader overlay */
				self.$body.on('added_to_cart', function(event, fragments, cartHash, quickviewOpen) {
					if (!self.quickviewIsOpen(quickviewOpen)) {
						self.widgetPanelHideCartLoader();
					}
				});
			} else {
				// Disable default WooCommerce AJAX add-to-cart event if redirect is enabled
				self.$document.off( 'click', '.add_to_cart_button' );
			}
			
			
			/* Bind: Breadcrumbs (add query arg) */
			$('#nm-breadcrumb').find('a').bind('click', function(e) {
				e.preventDefault();
				
				var url = $(this).attr('href');
				
				// Redirect to shop with url #hash (scrolls the page to the shop section)
				window.location.href = url + '#shop';
			});
			
			
			// Load extension scripts
			self.shopLoadExtension();
		},
		
		
		/**
		 *	Shop: Load extension scripts
		 */		
		shopLoadExtension: function() {
			var self = this;
			
			/* Extension: Add to cart */
			if (nm_wp_vars.shopAjaxAddToCart !== '0' && $.nmThemeExtensions.add_to_cart) {
				$.nmThemeExtensions.add_to_cart.call(self);
			}
			
			
			if (self.isShop) {
				/* Extension: Infinite load */
				if ($.nmThemeExtensions.infload) {
					$.nmThemeExtensions.infload.call(self);
				}
					
				
				/* Extension: Filters */
				if ($.nmThemeExtensions.filters) {
					$.nmThemeExtensions.filters.call(self);
				
					/* Extension: Filters scrollbar */
					if ($.nmThemeExtensions.filters_scrollbar) {
						$.nmThemeExtensions.filters_scrollbar.call(self);
					}
				}
				
					
				/* Extension: Search */
				if (self.searchEnabled && $.nmThemeExtensions.search) {
					$.nmThemeExtensions.search.call(self);
				}
			}
			
			
			/* Extension: Quickview */
			if (self.$pageIncludes.hasClass('quickview')) {
				if ($.nmThemeExtensions.quickview) {
					$.nmThemeExtensions.quickview.call(self);
				}
			}
		},
		
		
		/**
		 *	Shop: Check for URL #hash and scroll/jump to shop if added
		 */
		shopUrlHashScroll: function() {
			var self = this;
			
			if (window.location.hash === '#shop') {
				self.shopMaybeScrollToTop(true); // Arg: (noAnim)
			}
		},
		
		
		/**
		 *	Shop/Single-product: Toggle variation details
		 */
		shopToggleVariationDetails: function() {
			var $variationDetails = $('#nm-single-variation');
			// Show variation details container (if it has content)
			if ($variationDetails.children().length) {
				$variationDetails.addClass('show');
			} else {
				$variationDetails.removeClass('show');
			}
		},
		
		
		/**
		 *	Shop: Set current URL (used to reset search and product-tag AJAX results)
		 */
		shopSetCurrentUrl: function(isProductTag) {
			var self = this;
			
			// Exclude product-tag page URL's
			if (!self.isProductTagUrl) {
				// Set current page URL
				self.searchAndTagsResetURL = window.location.href;
			}
			
			// Is the current URL a product-tag URL?
			self.isProductTagUrl = (isProductTag) ? true : false;
		},
		
		
		/**
		 *	Shop/Single-product: Add quantity input buttons
		 */
		shopInitQuantity: function($productSummaryWrap) {
			var self = this;
			
			/* Add buttons */
			$productSummaryWrap.find('.quantity').append('<div class="nm-qty-plus nm-font nm-font-media-play rotate-270"></div>').prepend('<div class="nm-qty-minus nm-font nm-font-media-play rotate-90"></div>');
			
			/* 
			 *	Bind buttons click event
			 *	Note: Modified code from WooCommerce core (v2.2.6)
			 */
			$productSummaryWrap.on('click', '.nm-qty-plus, .nm-qty-minus', function() {
				// Get elements and values
				var $this		= $(this),
					$qty		= $this.closest('.quantity').find('.qty'),
					currentVal	= parseFloat($qty.val()),
					max			= parseFloat($qty.attr( 'max')),
					min			= parseFloat($qty.attr('min')),
					step		= $qty.attr('step');
				
				// Format values
				if (!currentVal || currentVal === '' || currentVal === 'NaN') currentVal = 0;
				if (max === '' || max === 'NaN') max = '';
				if (min === '' || min === 'NaN') min = 0;
				if (step === 'any' || step === '' || step === undefined || parseFloat(step) === 'NaN') step = 1;
		
				// Change the value
				if ($this.hasClass('nm-qty-plus')) {
					if (max && (max == currentVal || currentVal > max)) {
						$qty.val(max);
					} else {
						$qty.val(currentVal + parseFloat(step));
					}
				} else {
					if (min && (min == currentVal || currentVal < min)) {
						$qty.val(min);
					} else if (currentVal > 0) {
						$qty.val(currentVal - parseFloat(step));
					}
				}
		
				// Trigger change event
				//$qty.trigger('change');
			});
		},
		
		
		/**
		 *	Shop: Initialize Unveil (load images)
		 */
		shopLoadImages: function() {
			var self = this;
			
			// Make sure image lazy-loading is enabled
			if (self.imageLazyLoading) {
				// Remove any previous Unveil events
				self.$window.off('scroll.unveil resize.unveil lookup.unveil');
								
				var $images = self.$shopBrowseWrap.find('.nm-products li:not(.image-loaded) .nm-shop-loop-thumbnail .unveil-image'); // Get un-loaded images only
				
				if ($images.length) {
					var scrollTolerance = 1;
					$images.unveil(scrollTolerance, function() {
						$(this).parents('li').first().addClass('image-loaded');
					});
				}
			}
		},
		
		
		/**
		 *	Shop: Set shop min-height
		 */
		shopSetMinHeight: function() {
			var self = this,
				footerHeight = $('#nm-footer').outerHeight(true);
											
			self.$shopWrap.css('min-height', (self.$window.height() - (footerHeight + self.shopScrollOffset))+'px');
		},
		
		
		/**
		 *	Shop: Set shop container "min-height" and (if shop-top is -below- tolerance) smooth-scroll shop directly below header 
		 * 		  - Returns variable "to" with the smooth-scroll animation speed so "setTimeout()" can be used
		 */
		shopMaybeScrollToTop: function(noAnim) {
			var self = this,
				to = 0;
			
			// Set shop min-height
			self.shopSetMinHeight();
			
			var shopPosition = Math.round(self.$shopWrap.offset().top - self.shopScrollOffset),
				tolerance = 100 // 100px tolerance;
				
			if ((self.$document.scrollTop()+tolerance) < shopPosition) {
				to = self.shopScrollSpeed;
				
				if (noAnim) {
					self.$window.scrollTop(shopPosition);
				} else {
					$('html, body').animate({'scrollTop': shopPosition}, self.shopScrollSpeed);
				}
			}
			
			return to;
		},
		
		
		/**
		 *	Shop: Smooth-scroll to shop-top
		 */
		shopScrollToTop: function(jumpTo) {
			var self = this,
				shopPosition = Math.round(self.$shopWrap.offset().top - self.shopScrollOffset);
			
			if (jumpTo) {
				$('html, body').scrollTop(shopPosition);
			} else {
				$('html, body').animate({'scrollTop': shopPosition}, self.shopScrollSpeed);
			}
		},
		
		
		/**
		 *	Shop: Remove any visible shop notices
		 */
		shopRemoveNotices: function() {
			$('#nm-shop-notices-wrap').empty();
		},
		
		
		/**
		 *	Shop: Show "loader" overlay
		 */
		shopShowLoader: function(disableAnimation) {
			var $shopLoader = $('#nm-shop-products-overlay');
			
			if (disableAnimation) {
				$shopLoader.addClass('no-anim');
			}
							
			$shopLoader.addClass('show');
		},
		
		
		/**
		 *	Shop: Hide "loader" overlay
		 */
		shopHideLoader: function(disableAnimation) {
			var self = this,
				$shopLoader = $('#nm-shop-products-overlay');
			
			if (!disableAnimation) {
				$shopLoader.removeClass('no-anim');
			}
			
			$shopLoader.removeClass('nm-loader').addClass('fade-out');
			setTimeout(function() {
				$shopLoader.removeClass('show fade-out').addClass('nm-loader'); 
			}, self.shopLoaderSpeed);
			
			if (self.infloadScroll) {
				self.infscrollLock = false; // "Unlock" infinite scroll
				self.$window.trigger('scroll'); // Load next page (if correct scroll position)
			}
		},
		
		
		/**
		 *	Quick view: Check if quick view modal is open
		 */
		quickviewIsOpen: function(isOpen) {
			// if "isOpen" is undefined/not 'true', check if the quick view is open
			if (isOpen && isOpen == true) {
				return true;
			} else {
				return $('#nm-quickview').is(':visible');
			}
				
		},
		
		
		/**
		 *	Product: Load hover image
		 */
		productLoadHoverImage: function($productWrap) {
			var self = this;
				
			if ($productWrap.hasClass('hover-image-loading')) {
				return;
			}
			
			var $image = $productWrap.find('.nm-shop-loop-thumbnail .hover-image'), // Note: Don't create this variable outside the function first (it will overwrite '.load' below)
				imageSrc = $image.attr('data-src');
			
			if (!imageSrc) {
				console.log('NM: No image src found - productLoadHoverImage()');
				$productWrap.removeClass('hover-image-load');
				return;
			}
								
			$productWrap.addClass('hover-image-loading');
			
			// Bind image 'load' event
			$image.load(function() {
				var $this = $(this),
					$imageWrap = $this.closest('li');
				
				$this.unbind('load');
				$imageWrap.addClass('hover-image-loaded').removeClass('hover-image-load hover-image-loading');
			});
			
			// Load image
			$image.attr('src', imageSrc);
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.shop = $.nmTheme.shop_init;
	
})(jQuery);
