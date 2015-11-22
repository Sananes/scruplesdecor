(function($) {
	
	
	'use strict';
	
	
	if (!$.nmThemeExtensions)
		$.nmThemeExtensions = {};
	
		
	function NmTheme() {
		var self = this;
		
		// Page width "breakpoints"
		//self.BREAKPOINT_SMALL = 0;
		//self.BREAKPOINT_MEDIUM = 0;
		self.BREAKPOINT_LARGE = 864;
		
		// CSS Classes
		self.classHeaderFixed = 'header-on-scroll';
		self.classSlideMenuOpen = 'slide-menu-open';
		self.classWidgetPanelOpen = 'widget-panel-open';
		
		// Page elements
		self.$window = $(window);
		self.$document = $(document);
		self.$html = $('html');
		self.$body = $('body');
		
		// Page includes element
		self.$pageIncludes = $('#nm-page-includes');
		
		// Page overlays
		self.$pageOverlay = $('#nm-page-overlay');
		self.$widgetPanelOverlay = $('#nm-widget-panel-overlay');
		
		// Header
		self.$topBar = $('#nm-top-bar');
		self.$header = $('#nm-header');
		self.$headerPlaceholder = $('#nm-header-placeholder');
		self.headerScrollTolerance = 0;
		
		// Slide menu
		self.$slideMenuBtn = $('#nm-slide-menu-button');
		self.$slideMenu = $('#nm-slide-menu');
		self.$slideMenuScroller = self.$slideMenu.children('.nm-slide-menu-scroll');
		self.$slideMenuLi = self.$slideMenu.find('ul li.menu-item');
		
		// Widget panel
		self.$widgetPanel = $('#nm-widget-panel');
		
		// Slide panels animation speed
		self.panelsAnimSpeed = 200;
		
		// Shop
		self.$shopWrap = $('#nm-shop');
		self.isShop = (self.$shopWrap.length) ? true : false;
		
		// Search
		self.searchEnabled = false;
		self.searchInHeader = false;
		if (nm_wp_vars.shopSearch !== '0') {
			self.searchEnabled = true;
			
			self.$searchNotice = $('#nm-shop-search-notice');
			
			if (nm_wp_vars.shopSearch === 'header') {
				self.searchInHeader = true;
				self.$searchBtn = $('#nm-menu-search-btn');
			} else {
				// Shop search enabled, only need the button on shop listings
				if (self.isShop) {
					self.$searchBtn = $('#nm-shop-search-btn');
				}
			}
		}
		
		// Initialize scripts
		self.init();
	};
	
	
	NmTheme.prototype = {
	
		/**
		 *	Initialize
		 */
		init: function() {
			var self = this;
			
			// Remove the CSS transition preload class
			self.$body.removeClass('nm-preload');
			
			// Fixed header
			self.headerIsFixed = (self.$body.hasClass('header-fixed')) ? true : false;
			
			// Init history/back-button support (push/pop-state)
			if (self.$html.hasClass('history')) {
				self.hasPushState = true;
				window.history.replaceState({nmShop: true}, '', window.location.href);
			} else {
				self.hasPushState = false;
			}
			
			// Initialize scripts
			self.setScrollbarWidth();
			self.headerCheckPlaceholderHeight(); // Make sure the header and header-placeholder has the same height
			if (self.headerIsFixed) {
				self.headerSetScrollTolerance();
				self.slideMenuPrep();
			}
			self.widgetPanelHideScrollbar();
			
			// Check for old IE browser (IE10 or below)
			var ua = window.navigator.userAgent,
            	msie = ua.indexOf('MSIE ');
			if (msie > 0) {
				self.$html.addClass('nm-old-ie');
			}
			
			// Check for touch device (modernizr)
			self.isTouch = (self.$html.hasClass('touch')) ? true : false;
			
			// Load extension scripts
			self.loadExtension();
			
			self.bind();
			self.initPageIncludes();
			
			
			// "Add to cart" redirect: Show cart panel
			if (self.$body.hasClass('nm-added-to-cart')) {
				self.$body.removeClass('nm-added-to-cart')
				
				self.$window.load(function() {
					// Show cart panel
					self.widgetPanelShowCart(true); // Args: (showLoader)
					// Hide cart panel "loader" overlay
					setTimeout(function() { self.widgetPanelHideCartLoader(); }, 1000);
				});
			}
		},
		
		
		/**
		 *	Extensions: Load scripts
		 */
		loadExtension: function() {
			var self = this;
			
			// Shop scripts
			if ($.nmThemeExtensions.shop) {
				$.nmThemeExtensions.shop.call(self);
			}
				
			// Shop/single-product scripts
			if ($.nmThemeExtensions.singleProduct) {
				$.nmThemeExtensions.singleProduct.call(self);
			}
				
			// Cart scripts
			if ($.nmThemeExtensions.cart) {
				$.nmThemeExtensions.cart.call(self);
			}
			
			// Checkout scripts
			if ($.nmThemeExtensions.checkout) {
				$.nmThemeExtensions.checkout.call(self);
			}
		},
		
		
		/**
		 *	Helper: Calculate scrollbar width
		 */
		setScrollbarWidth: function() {
			// From Magnific Popup v1.0.0
			var self = this,
				scrollDiv = document.createElement('div');
			scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
			document.body.appendChild(scrollDiv);
			self.scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
			document.body.removeChild(scrollDiv);
			// /Magnific Popup
		},
		
		
		/**
		 * Helper: Add/update a key-value pair in the URL query parameters 
		 */
		updateUrlParameter: function(uri, key, value) {
			// Remove #hash before operating on the uri
			var i = uri.indexOf('#'),
				hash = i === -1 ? '' : uri.substr(i);
			uri = (i === -1) ? uri : uri.substr(0, i);
			
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i"),
				separator = (uri.indexOf('?') !== -1) ? "&" : "?";
			
			if (uri.match(re)) {
				uri = uri.replace(re, '$1' + key + "=" + value + '$2');
			} else {
				uri = uri + separator + key + "=" + value;
			}
			
			return uri + hash; // Append #hash
		},
		
		
		/**
		 *	Helper: Set browser history "pushState" (AJAX url)
		 */
		setPushState: function(pageUrl) {
			var self = this;
			
			// Set browser "pushState"
			if (self.hasPushState) {
				window.history.pushState({nmShop: true}, '', pageUrl);
			}
		},
		
		
		/**
		 *	Header: Check/set placeholder height
		 */
		headerCheckPlaceholderHeight: function() {
			var self = this;
			
			// Make sure the header is not fixed/floated
			if (self.$body.hasClass(self.classHeaderFixed)) {
				return;
			}
			
			var headerHeight = self.$header.outerHeight(true),
				headerPlaceholderHeight = parseInt(self.$headerPlaceholder.css('height'));
			
			// Is the header height different than the current placeholder height?
			if (headerHeight !== headerPlaceholderHeight) {
				self.$headerPlaceholder.css('height', headerHeight+'px');
			}
		},
		
		
		/**
		 *	Header: Set scroll tolerance
		 */
		headerSetScrollTolerance: function() {
			var self = this;
			
			self.headerScrollTolerance = (self.$topBar.length && self.$topBar.is(':visible')) ? self.$topBar.outerHeight(true) : 0;
		},
		
		
		/**
		 *	Page scroll: Disable
		 */
		pageScrollDisable: function() {
			var self = this;
			
			// Only disable page scroll on touch devices
			if (!self.isTouch) { return; }
			
			self.bodyScrollTop = self.$document.scrollTop();
			
			// Use a timeout so the panel animation can complete (or it won't work)
			setTimeout(function() {
				// Add "disable-scroll" class and set a top-position to prevent page from "jumping" to the top
				self.$body.addClass('disable-scroll').children('.nm-page-overflow').css('top', '-'+self.bodyScrollTop+'px');
				
				// Force "position:fixed" on header
				self.$header.addClass('force-fix');
			}, self.panelsAnimSpeed);
		},
		
		
		/**
		 *	Page scroll: Enable
		 */
		pageScrollEnable: function() {
			var self = this;
			
			if (!self.isTouch) { return; }
			
			// Use a timeout so the panel animation can complete (or it won't work)
			setTimeout(function() {
				self.$body.removeClass('disable-scroll');
				
				// Re-set original scroll position
				self.$document.scrollTop(self.bodyScrollTop);
				
				// Use a timeout to prevent header "flicker"
				setTimeout(function() {
					// Remove forced "position:fixed" from header
					self.$header.removeClass('force-fix');
				}, 100);
			}, self.panelsAnimSpeed);
		},
		
		
		/**
		 *	Bind scripts
		 */
		bind: function() {
			var self = this;
			
			
			
			/* Bind: Window resize */
			var timer = null, windowWidth;
			self.$window.resize(function() {
				if (timer) { clearTimeout(timer); }
				timer = setTimeout(function() {
					windowWidth = parseInt(self.$html.css('width'));
					
					if (self.$body.hasClass(self.classSlideMenuOpen) && windowWidth > self.BREAKPOINT_LARGE) {
						self.$pageOverlay.trigger('click');
					}
					
					// Make sure the header and header-placeholder has the same height
					self.headerCheckPlaceholderHeight();
																	
					if (self.headerIsFixed) {
						self.headerSetScrollTolerance();
						self.slideMenuPrep();
					}
				}, 250);
			});
			
			
			
			/* Bind: Window scroll (Fixed header) */
			if (self.headerIsFixed) {
				self.$window.bind('scroll', function(e) {
					if (self.$document.scrollTop() > self.headerScrollTolerance) {
						if (!self.$body.hasClass(self.classHeaderFixed))
							self.$body.addClass(self.classHeaderFixed);
					} else {
						if (self.$body.hasClass(self.classHeaderFixed)) {
							self.$body.removeClass(self.classHeaderFixed);
						}
					}
				});
				
				self.$window.trigger('scroll');
			}
			
			
			
			/* Bind: Sub-menu position check */
			var $topMenuItems = $('#nm-top-menu').children('.menu-item'),
				$mainMenuItems = $('#nm-main-menu-ul').children('.menu-item'),
				$menuItems = $.merge($topMenuItems, $mainMenuItems);
				
			$menuItems.hover(function() {
				var $subMenu = $(this).children('.sub-menu');
				if ($subMenu.length) {
					var windowWidth = self.$window.innerWidth(),
						subMenuOffset = $subMenu.offset().left,
						subMenuWidth = $subMenu.width(),
						subMenuGap = windowWidth - (subMenuOffset + subMenuWidth);
					if (subMenuGap < 0) {
						$subMenu.css('left', (subMenuGap-33)+'px');
					} else {
						$subMenu.css('left', '');
					}
				}
			});
			
			
			
			/* Bind: Slide menu button */
			self.$slideMenuBtn.bind('click', function(e) {
				e.preventDefault();
				
				if (!self.$body.hasClass(self.classSlideMenuOpen)) {
					var headerPosition = self.$header.outerHeight(true);
					self.$slideMenuScroller.css('margin-top', headerPosition+'px');
					
					self.$body.addClass(self.classSlideMenuOpen);
					self.$pageOverlay.addClass('show');
					
					// Disable page scroll (if header is fixed/floating)
					if (self.headerIsFixed) {
						self.pageScrollDisable();
					}
				} else {
					self.$body.removeClass(self.classSlideMenuOpen);
					self.$pageOverlay.trigger('click');
					
					// Enable page scroll (if header is fixed/floating)
					if (self.headerIsFixed) {
						self.pageScrollEnable();
					}
				}
			});
			
			/* Function: Slide menu - Toggle sub-menu */
			var _slideMenuToggleSub = function($menu, $subMenu) {
				$menu.toggleClass('active');
				$subMenu.toggleClass('open');
			};
			
			/* Bind: Slide menu list elements */
			self.$slideMenuLi.bind('click', function(e) {
				e.preventDefault();
				e.stopPropagation(); // Prevent click event on parent menu link
				
				var $this = $(this),
					$thisSubMenu = $this.children('ul');
									
				if ($thisSubMenu.length) {
					_slideMenuToggleSub($this, $thisSubMenu);
				}
			});
			
			/* Bind: Slide menu links */
			self.$slideMenuLi.find('a').bind('click', function(e) {
				e.stopPropagation(); // Prevent click event on parent list element
				
				var $this = $(this),
					$thisLi = $this.parent('li'),
					$thisSubMenu = $thisLi.children('ul');
					
				if (($thisSubMenu.length || $this.attr('href').substr(0, 1) == '#') && !$thisLi.hasClass('nm-notoggle')) {
					e.preventDefault();
					_slideMenuToggleSub($thisLi, $thisSubMenu);
				}
			});
			
			
			
			if (self.searchEnabled) {
				/* Bind: Search - Header link */
				if (self.searchInHeader) {
					self.$searchBtn.bind('click', function(e) {
						e.preventDefault();
						$(this).toggleClass('active');
						self.$body.toggleClass('header-search-open');
						self.searchPanelToggle();
					});
				}
				
				/* Bind: Search - Panel "close" button */
				$('#nm-shop-search-close').bind('click', function(e) {
					e.preventDefault();
					self.$searchBtn.trigger('click');
				});
				
				
				/* Bind: Search input "input" event */
				var validSearch;
				$('#nm-shop-search-input').on('input', function() {
					validSearch = self.shopSearchValidateInput($(this).val());
					
					if (validSearch) {
						self.$searchNotice.addClass('show');
					} else {
						self.$searchNotice.removeClass('show');
					}
				}).trigger('input');
			}
			
			
			
			/* Bind: Widget panel */
			if (self.$widgetPanel.length) {
				self.widgetPanelBind();
			}
			
			
			
			/* Bind: Blog categories toggle link */
			$('#nm-blog-categories-toggle-link').bind('click', function(e) {
				e.preventDefault();
				
				var $thisLink = $(this);
				
				$('#nm-blog-categories-list').slideToggle(200, function() {
					var $this = $(this);
					
					$thisLink.toggleClass('active');
					
					if (!$thisLink.hasClass('active')) {
						$this.css('display', '');
					}
				});
			});
			
			
			
			/* Bind: Page overlay */
			$('#nm-page-overlay, #nm-widget-panel-overlay').bind('click', function() {
				var $this = $(this);
				
				if (self.$body.hasClass(self.classSlideMenuOpen)) {
					self.$body.removeClass(self.classSlideMenuOpen);
					
					// Re-enable page scroll (if header is fixed)
					if (self.headerIsFixed) {
						self.pageScrollEnable();
					}
				} else {
					self.$body.removeClass(self.classWidgetPanelOpen);
				}
				
				$this.addClass('fade-out');
				setTimeout(function() {
					$this.removeClass('show fade-out');
				}, self.panelsAnimSpeed);
			});
		},
		
		
		/**
		 *	Slide menu: Set CSS
		 */
		slideMenuPrep: function() {
			var self = this,
				windowHeight = self.$window.height() - self.$header.outerHeight(true);
			
			self.$slideMenuScroller.css({'max-height': windowHeight+'px', 'margin-right': '-'+self.scrollbarWidth+'px'});
		},
		
		
		/**
		 *	Shop search: Toggle panel
		 */
		searchPanelToggle: function() {
			var self = this,
				$searchPanel = $('#nm-shop-search'),
				$searchInput = $('#nm-shop-search-input');
			
			$searchPanel.slideToggle(200, function() {
				$searchPanel.toggleClass('fade-in');
												
				if ($searchPanel.hasClass('fade-in')) {
					// "Focus" search input
					$searchInput.focus();
				} else {
					// Empty input value
					$searchInput.val('');
				}
				
				self.filterPanelSliding = false;
			});
			
			// Hide search notice
			self.shopSearchHideNotice();
		},
		
		
		/**
		 *	Shop search: Validate input string
		 */
		shopSearchValidateInput: function(s) {
			// Make sure the search string has at least one character (not just whitespace) and minimum allowed characters are entered
			if ((/\S/.test(s)) && s.length > (nm_wp_vars.shopSearchMinChar-1)) {
				return true;
			} else {
				return false;
			}
		},
		
		
		/**
		 *	Shop search: Hide notice
		 */
		shopSearchHideNotice: function(s) {
			$('#nm-shop-search-notice').removeClass('show');
		},
		
		
		/**
		 *	Widget panel: Hide scrollbar
		 */
		widgetPanelHideScrollbar: function() {
			var self = this;
			
			if (parseInt(self.scrollbarWidth) > 0) {
				// Hide scrollbar from widget-panel container by adding margin
				self.$widgetPanel.children('.nm-widget-panel-scroll').css('marginRight', '-'+self.scrollbarWidth+'px');
			}
		},
		
		
		/**
		 *	Widget panel: Bind
		 */
		widgetPanelBind: function() {
			var self = this;
			
			
			// Touch event handling
			if (self.isTouch) {
				// Allow page overlay "touchmove" event if header is not fixed/floating
				if (self.headerIsFixed) {
					// Bind: Page overlay "touchmove" event
					self.$pageOverlay.on('touchmove', function(e) {
						e.preventDefault(); // Prevent default touch event
					});
				}
				
				// Bind: Widget panel overlay "touchmove" event
				self.$widgetPanelOverlay.on('touchmove', function(e) {
					e.preventDefault(); // Prevent default touch event
				});
				
				// Bind: Widget panel "touchmove" event
				self.$widgetPanel.on('touchmove', function(e) {				
					e.stopPropagation(); // Prevent event propagation (bubbling)
				});
			}
			
			
			/* Bind: "Cart" buttons */
			$('#nm-menu-cart-btn, #nm-slide-menu-cart-btn').bind('click', function(e) {
				e.preventDefault();										
				
				// Close the slide menu first					
				if (self.$body.hasClass(self.classSlideMenuOpen)) {
					var $this = $(this);
					self.$pageOverlay.trigger('click');
					setTimeout(function() {
						$this.trigger('click');
					}, self.panelsAnimSpeed);
					return;
				}
				
				self.widgetPanelShowCart();
			});
			
			/* Bind: "Close" button */
			$('#nm-widget-panel-close').bind('click', function(e) {
				e.preventDefault();
				$('#nm-widget-panel-overlay').trigger('click');
			});
						
			/* Bind: Mini cart empty "continue" button */
			self.$widgetPanel.on('click', '#nm-mini-cart-empty-continue', function(e) {
				e.preventDefault();
				$('#nm-widget-panel-overlay').trigger('click');
			});
			
			
			/* Bind: Mini cart - Remove product */
			self.$widgetPanel.on('click', '#nm-mini-cart-list .product-details-wrap .remove', function(e) {
				e.preventDefault();
				self.widgetPanelRemoveCartProduct(this);
			});
		},
		
		
		/**
		 *	Widget panel: Show cart
		 */
		widgetPanelShowCart: function(showLoader) {
			var self = this;
			
			if (showLoader) {
				$('#nm-mini-cart-loader').addClass('show');
			}
			
			self.$body.addClass(self.classWidgetPanelOpen);
			self.$widgetPanelOverlay.addClass('show');
		},
		
		
		/**
		 *	Widget panel: Hide cart loader
		 */
		widgetPanelHideCartLoader: function(showLoader) {
			$('#nm-mini-cart-loader').addClass('fade-out');
			setTimeout(function() { $('#nm-mini-cart-loader').removeClass('fade-out show'); }, 300);
		},
		
		
		/**
		 *	Widget panel: Remove cart product
		 */		
		widgetPanelRemoveCartProduct: function(button) {
			var self = this,
				$this = $(button),
				$miniCartLoader = $('#nm-mini-cart-loader'),
				cartItemKey = $this.data('cart-item-key');
			
			// Show "loader" overlay
			$miniCartLoader.addClass('show');
			
			$.ajax({
				type: 'POST',
				url: nm_wp_vars.ajaxUrl,
				data: {
					action: 'nm_mini_cart_remove_product',
					cart_item_key: cartItemKey
				},
				dataType: 'json',
				cache: false,
				headers: {'cache-control': 'no-cache'},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log('NM: AJAX error - widgetPanelRemoveCartProduct() - ' + errorThrown);
					
					// Hide "loader" overlay
					$miniCartLoader.removeClass('show');
				},
				complete: function(response) {
					var json = response.responseJSON;
					
					if (json.status === '1') {
						$this.closest('li').remove();
						
						// Update cart numbers
						$('.nm-menu-cart-count').html(json.cart_count);
						
						// Is the cart empty?
						if ($('#nm-mini-cart-list').children().length == 0) {
							$('#nm-mini-cart-inner-empty').addClass('show'); // Show "cart empty" content
							$('#nm-mini-cart-inner').css('display', 'none'); // Hide cart content
						} else {
							// Update cart subtotal
							$('#nm-mini-cart-subtotal').html(json.cart_subtotal);
						}
					} else {
						console.log("NM: Couldn't remove product from cart");
					}
					
					// Hide "loader" overlay
					$miniCartLoader.addClass('fade-out');
					setTimeout(function() { $miniCartLoader.removeClass('fade-out show'); }, 300);
				}
			});
		},
		
		
		/**
		 *	Initialize "page includes" elements
		 */
		initPageIncludes: function() {
			var self = this;
			
			
			/* VC element: Row - Video (YouTube) background */
			if (!self.isTouch && self.$pageIncludes.hasClass('video-background')) {
				$('.nm-row-video').each(function() {
					var $row = $(this),
						youtubeUrl = $row.data('video-url');
					
					if (youtubeUrl) {
						var youtubeId = vcExtractYoutubeId(youtubeUrl); // Note: function located in: "nm-js_composer_front(.min).js"
						
						if (youtubeId) {
							insertYoutubeVideoAsBackground($row, youtubeId); // Note: function located in: "nm-js_composer_front(.min).js"
						}
					}
				});
			}
			
			
			self.$window.load(function() {
				
				/* Blog grid (Packery) */
				if (self.$pageIncludes.hasClass('blog-grid')) {
					var $blogUl = $('#nm-blog-grid-ul');
						
					// Initialize Packery
					$blogUl.packery({
						itemSelector: '.post',
						gutter: 0,
						isInitLayout: false // // Disable initial layout
					});
					
					// Packery event: "layoutComplete"
					$blogUl.packery('once', 'layoutComplete', function() {
						//setTimeout(function() {
						$blogUl.removeClass('nm-loader').addClass('show');
						//}, 200);
					});
					
					// Manually trigger initial layout
					$blogUl.packery();
				}
				
				
				/* VC element: Banner */
				if (self.$pageIncludes.hasClass('banner')) {
					var $banners = $('.nm-banner'),
						$bannerAltImages = $banners.find('.nm-banner-alt-image');
					
					
					/* Bind: Banner shop links (AJAX) */
					if (self.isShop && self.filtersEnableAjax) {
						$banners.find('.nm-banner-shop-link').bind('click', function(e) {
							e.preventDefault();
							var shopUrl = $(this).attr('href');
							if (shopUrl) {
								self.shopExternalGetPage($(this).attr('href')); // Smooth-scroll to top, then load shop page
							}
						});
					}
					
					
					/* Helper: Load alternative/smaller banner images */
					var _bannersLoadAltImage = function() {
						if (self.$window.width() <= 768) {
							var $image, imageSrc;
								
							for (var i = 0; i < $bannerAltImages.length; i++) {
								$image = $($bannerAltImages[i]);
								imageSrc = $($bannerAltImages[i]).data('src');
								
								if ($image.hasClass('img')) {
									$image.attr('src', imageSrc);
								} else {
									$image.css('background-image', 'url('+imageSrc+')');
								}
							}
							
							// Unbind resize event after images are loaded
							self.$window.unbind('resize.banners');
						}
					};
					
					/* Bind: Window resize event for loading alternative/smaller banner images */
					var timer = null;
					self.$window.bind('resize.banners', function() {
						if (timer) { clearTimeout(timer); }
						timer = setTimeout(function() { _bannersLoadAltImage(); }, 250);
					});
					
					// Run function on page load (keep below the 'resize.bannerslider' event)
					_bannersLoadAltImage();
				}
				
				
				/* VC element: Banner slider */
				if (self.$pageIncludes.hasClass('banner-slider')) {
					var $bannerSliders = $('.nm-banner-slider');
					
					/* Helper: Add banner animation class */
					var _bannerAddAnimClass = function($slider, $slideActive) {
						$slider.$bannerContent = $slideActive.find('.nm-banner-text-inner');
						
						if ($slider.$bannerContent.length) {
							$slider.bannerAnimation = $slider.$bannerContent.data('animate');
							$slider.$bannerContent.addClass($slider.bannerAnimation);
						}
					};
					
					// Initialize banner sliders											
					$bannerSliders.each(function() {
						var $slider = $(this),
							sliderOptions = {
								arrows: false,
								prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-angle-thin-left"></i></a>',
								nextArrow: '<a class="slick-next"><i class="nm-font nm-font-angle-thin-right"></i></a>',
								dots: false,
								edgeFriction: 0,
								infinite: false,
								pauseOnHover: false,
								speed: 350
							};
							
						// Wrap slider banners in a 'div' element (this will be the '.slick-slide' element around each banner)
						$slider.children().wrap('<div></div>');
						
						// Extend default slider settings with data attribute settings
						sliderOptions = $.extend(sliderOptions, $slider.data());
						
						// Event: Slick slide - Init
						$slider.on('init', function() {
							self.$document.trigger('banner-slider-loaded');
							_bannerAddAnimClass($slider, $slider.find('.slick-track .slick-active'));
						});
						
						// Event: Slick slide - Slide change
						$slider.on('afterChange', function(event, slick, currentSlide) {
							// Make sure the slide has changed
							if ($slider.slideIndex != currentSlide) {
								$slider.slideIndex = currentSlide;
								
								// Remove animation class from previous banner
								if ($slider.$bannerContent) {
									$slider.$bannerContent.removeClass($slider.bannerAnimation);
								}
								
								_bannerAddAnimClass($slider, $slider.find('.slick-track .slick-active')); // Note: Don't use the "currentSlide" index to find the active element ("infinite" setting clones slides)
							}
						});
						
						// Event: Slick slide - After position/size changes
						$slider.on('setPosition', function(event, slick) {
							var $currentSlide = $(slick.$slides[slick.currentSlide]),
								$currentBanner = $currentSlide.children('.nm-banner');
							
							// Is there an alt. image?
							if ($currentBanner.hasClass('has-alt-image')) {
								// Is the alt. image currently visible?
								if ($currentBanner.children('.nm-banner-alt-image').is(':visible')) {
									slick.$slider.addClass('alt-image-visible');
								} else {
									slick.$slider.removeClass('alt-image-visible');
								}
							} else {
								slick.$slider.removeClass('alt-image-visible');
							}
						});
						
						// Initialize banner slider
						$slider.slick(sliderOptions);
					});
				}
				
				
				/* VC element: Post slider */
				if (self.$pageIncludes.hasClass('post-slider')) {
					var $sliders = $('.nm-post-slider'),
						sliderOptions = {
							adaptiveHeight: true,
							arrows: false,
							dots: true,
							edgeFriction: 0,
							infinite: false,
							pauseOnHover: false,
							speed: 350,
							slidesToShow: 4,
							slidesToScroll: 4,
							responsive: [
								{
									breakpoint: 1024,
									settings: {
										slidesToShow: 3,
										slidesToScroll: 3
									}
								},
								{
									breakpoint: 768,
									settings: {
										slidesToShow: 2,
										slidesToScroll: 2
									}
								},
								{
									breakpoint: 518,
									settings: {
										slidesToShow: 1,
										slidesToScroll: 1
									}
								}
							]
						};
					
					$sliders.each(function() {
						var $slider = $(this);
						
						// Extend default slider settings with data attribute settings
						sliderOptions = $.extend(sliderOptions, $slider.data());
						
						$slider.slick(sliderOptions);
					});
				}
				
				
				/* VC element: Blog slider */
				if (self.$pageIncludes.hasClass('blog-slider')) {
					var $galleries = $('.nm-blog-slider'),
						sliderOptions = {
							//prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-play flip"></i></a>',
							//nextArrow: '<a class="slick-next"><i class="nm-font nm-font-play"></i></a>',
							prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-angle-left"></i></a>',
							nextArrow: '<a class="slick-next"><i class="nm-font nm-font-angle-right"></i></a>',
							dots: true,
							edgeFriction: 0,
							infinite: false,
							pauseOnHover: false,
							speed: 350,
							responsive: [
								{
									breakpoint: 550,
									settings: {
										slidesToShow: 1
									}
								}
							]
						};
					
					$galleries.each(function() {
						var $gallery = $(this);
						
						// Extend default slider settings with data attribute settings
						sliderOptions = $.extend(sliderOptions, $gallery.data());
						
						$gallery.slick(sliderOptions);
					});
				}
				
				
				/* WP gallery popup */
				if (self.$pageIncludes.hasClass('wp-gallery')) {
					$('.gallery').each(function() {
						$(this).magnificPopup({
							mainClass: 'nm-wp-gallery-popup nm-mfp-fade-in',
							closeMarkup: '<a class="mfp-close nm-font nm-font-close2"></a>',
							removalDelay: 180,
							delegate: '.gallery-icon > a', // Gallery item selector
							type: 'image',
							gallery: {
								enabled: true,
								arrowMarkup: '<a title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir% nm-font nm-font-play"></a>'
							},
							closeBtnInside: false
						});
					});
				}
				
				
				/* VC element: Portfolio */
				if (self.$pageIncludes.hasClass('portfolio')) {
					var $portfolioGrids = $('.nm-portfolio-grid');
					
					for (var i = 0; i < $portfolioGrids.length; i++) {
						var $thisGrid = $($portfolioGrids[i]),
							packeryEnabled = $thisGrid.parent('.nm-portfolio').hasClass('packery-enabled') ? true : false;
						
						if (packeryEnabled) {
							// Packery settings
							var settings = {
								itemSelector: 'li',
								gutter: 0,
								isInitLayout: false // Disable initial layout
							};
							
							// Initialize Packery
							$thisGrid.packery(settings);
							
							// Packery event: "layoutComplete"
							$thisGrid.packery('once', 'layoutComplete', function() {
								$thisGrid.parent('.nm-portfolio').addClass('show');
							});
							
							// Manually trigger initial layout
							$thisGrid.packery();
						}
						
						
						// Filters
						var $filtersMenu = $thisGrid.siblings('.nm-portfolio-filters');
						if ($filtersMenu.length) {
							$filtersMenu.find('a').bind('click', function(e) {
								e.preventDefault();
								
								var $this = $(this),
									$thisWrap = $this.closest('.nm-portfolio');
								
								if ($this.hasClass('current')) {
									return;
								} else {
									// Set "current" link
									$thisWrap.children('.nm-portfolio-filters').children('.current').removeClass('current');
									$this.parent('li').addClass('current');
								}
								
								var $thisGrid = $thisWrap.children('.nm-portfolio-grid'),
									$thisItems = $thisGrid.children('li'),
									filterSlug = $this.data('filter'),
									packeryEnabled = $thisWrap.hasClass('packery-enabled') ? true : false;
								
								// Show/hide elements
								if (filterSlug) {
									var $item;
									$thisItems.each(function() {
										$item = $(this);
										if ($item.hasClass(filterSlug)) {
											if (packeryEnabled) {
												$thisGrid.packery('unignore', $item[0]); // Packery - un-ignore element
											}
											
											$item.removeClass('hide fade-out');
										} else {
											if (packeryEnabled) {
												$thisGrid.packery('ignore', $item[0]); // Packery - ignore element
											}
											
											$item.addClass('hide fade-out');
										}
									});
								} else {
									if (packeryEnabled) {
										$thisItems.each(function() {
											$thisGrid.packery('unignore', $(this)[0]); // Packery - unignore element
										});
									}
									
									$thisItems.removeClass('hide fade-out'); // Show all items
								}
								
								if (packeryEnabled) {
									$thisGrid.packery(); // Re-position grid elements
								}
							});
						}
					}
				}
			
			}); // $window.load()
			
			
			/* VC element: Product categories */
			if (self.$pageIncludes.hasClass('product_categories')) {
				var $categories = $('.nm-product-categories');
				
				/* Bind: Category links */
				if (self.isShop && self.filtersEnableAjax) {
					$categories.find('.product-category a').bind('click', function(e) {
						e.preventDefault();
						
						// Load shop category page
						self.shopExternalGetPage($(this).attr('href'));
					});
				}
				
				self.$window.load(function() {
					for (var i = 0; i < $categories.length; i++) {
						var $categoriesUl = $($categories[i]).children('.woocommerce').children('ul');
						
						// Initialize Packery
						$categoriesUl.packery({
							itemSelector: '.product-category',
							gutter: 0,
							isInitLayout: false // Disable initial layout
						});
						
						// Packery event: "layoutComplete"
						$categoriesUl.packery('once', 'layoutComplete', function() {
							$categoriesUl.closest('.nm-product-categories').removeClass('nm-loader'); // Hide preloader
							$categoriesUl.addClass('show');
						});
						
						// Manually trigger initial layout
						$categoriesUl.packery();
					}
				});
			}
			
			
			/* VC element: Lightbox */
			if (self.$pageIncludes.hasClass('lightbox')) {
				var $this, type;
				
				$('.nm-lightbox').each(function() {
					$(this).bind('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						$this = $(this);
						type = $this.data('mfp-type');
						
						$this.magnificPopup({
							mainClass: 'nm-wp-gallery-popup nm-mfp-zoom-in',
							closeMarkup: '<a class="mfp-close nm-font nm-font-close2"></a>',
							removalDelay: 180,
							type: type,
							closeOnContentClick: true,
							closeBtnInside: false/*,
							image: {
								//titleSrc: 'data-mfp-title',
								verticalFit: false
							}*/
						}).magnificPopup('open');
					});
				});
			}
		}
	
	};
	
	
	// Add core script to $.nmTheme so it can be extended
	$.nmTheme = NmTheme.prototype;
	
	
	$(document).ready(function() {
		// Initialize script
		new NmTheme();
	});
	
	
})(jQuery);
