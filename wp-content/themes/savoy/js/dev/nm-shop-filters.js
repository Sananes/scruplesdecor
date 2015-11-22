(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize scripts
		 */
		filters_init: function() {
			var self = this;
			
			/* Shop filters */
			self.$shopFilterMenu = $('#nm-shop-filter-menu');
			self.filterPanelSliding = false;
			self.filterPanelSlideSpeed = 200;
			self.filterPanelHideWidth = 551;
			
			self.filterScrollbars = (nm_wp_vars.shopFilterScrollbars == '1') ? true : false;
			self.filterScrollbarsLoaded = false;
			
			// Shop filters: Toggle function names
			self.shopFilterMenuFnNames = {
				'cat':		'shopFiltersCategoriesToggle',
				'filter':	'shopFiltersSidebarToggle',
				'search':	'shopFiltersSearchToggle'
			};
			
			self.shopFiltersBind();
		},
		
		
		/**
		 *	Shop filters: Bind
		 */
		shopFiltersBind: function() {
			var self = this;
			
			
			/* Bind: Shop filters menu */
			self.$shopFilterMenu.find('a').bind('click', function(e) {
				e.preventDefault();
				
				if (self.filterPanelSliding) { return; }
				
				// Remove any visible shop notices
				self.shopRemoveNotices();
					
				self.filterPanelSliding = true;
				
				var to = 0,
					$this = $(this).parent('li'),
					thisData = $this.data('panel');					
				
				// Hide active panel
				if (!$this.hasClass('active')) {
					to = self.shopFiltersHideActivePanel();
				}
				
				$this.toggleClass('active');
				
				// Use "setTimeout()" to allow the active panel to slide-up first (if open)
				setTimeout(function() {
					var fn = self.shopFilterMenuFnNames[thisData];
					self[fn]();
				}, to);
			});
			
			
			/* Bind: Category menu */
			if (self.filtersEnableAjax && self.$pageIncludes.hasClass('shop_categories')) {
				// Set categories count
				$('#nm-categories-count').text($('#nm-shop-categories').children().length-1);
				
				self.$shopWrap.on('click', '#nm-shop-categories a',  function(e) {
					e.preventDefault();
					
					var $this = $(this),
						$thisLi = $this.parent('li');					
					
					//if ($thisLi.hasClass('current-cat')) { return; }
										
					// Close search panel (if open)
					if (self.searchEnabled && self.$searchBtn.parent('li').hasClass('active')) {
						self.categoryClicked = true; // Adding this to make sure the shop overlay will not be hidden by the search button click event
						self.$searchBtn.trigger('click');
					}
										
					// Set new "current" class
					$('#nm-shop-categories').children('.current-cat').removeClass('current-cat');
					$thisLi.addClass('current-cat');
					
					self.shopGetPage($this.attr('href'));
				});
			}
			
			
			/* Bind: Sidebar widget headings */
			self.$shopWrap.on('click', '#nm-shop-sidebar .nm-widget-title', function(e) {
				$(this).parent().toggleClass('show');
			});
			
			
			/* Bind: Sidebar widgets */
			if (self.filtersEnableAjax && self.$pageIncludes.hasClass('shop_filters')) {
				/* 
				 *	Bind custom widgets:
				 *	- Sorting
				 *	- Price 
				 *	- Color
				 */
				self.$shopWrap.on('click', '#nm-shop-sidebar .nm_widget a', function(e) {
					e.preventDefault();
					self.shopGetPage($(this).attr('href'));
				});
				
				/* Bind: WooCommerce product categories widget */
				self.$shopWrap.on('click', '#nm-shop-sidebar .widget_product_categories a', function(e) {
					e.preventDefault();
					self.shopGetPage($(this).attr('href'));
				});
				
				/* Bind: WooCommerce layered nav widget */
				self.$shopWrap.on('click', '#nm-shop-sidebar .widget_layered_nav a', function(e) {
					e.preventDefault();
					self.shopGetPage($(this).attr('href'));
				});
								
				/* Bind: WooCommerce layered nav (active) filters */
				self.$shopWrap.on('click', '#nm-shop-sidebar .widget_layered_nav_filters a', function(e) {
					e.preventDefault();
					self.shopGetPage($(this).attr('href'));
				});
								
				/* Bind: WooCommerce product tags widget */
				self.$shopWrap.on('click', '#nm-shop-sidebar .widget_product_tag_cloud a', function(e) {
					e.preventDefault();
					self.shopGetPage($(this).attr('href'), false, true); // Args: pageUrl, isBackButton, isProductTag
				});
			}
		},
		
		
		/**
		 *	Shop filters: Toggle categories
		 */
		shopFiltersCategoriesToggle: function() {
			var self = this;
			
			$('#nm-shop-categories').slideToggle(self.filterPanelSlideSpeed, function() {
				var $this = $(this);
				
				$this.toggleClass('fade-in');
				if (!$this.hasClass('fade-in')) {
					$this.removeClass('force-show').css('display', '');
				}
				
				self.filterPanelSliding = false;
			});
		},
		
		
		/**
		 *	Shop filters: Reset categories (remove classes and inline style)
		 */
		shopFiltersCategoriesReset: function() {
			$('#nm-shop-categories').removeClass('fade-in force-show').css('display', '');
		},
		
		
		/**
		 *	Shop filters: Toggle sidebar filters/widgets panel
		 */
		shopFiltersSidebarToggle: function() {
			var self = this,
				$shopSidebar = $('#nm-shop-sidebar'),
				isOpen = $shopSidebar.is(':visible');
			
			// Hide filters before sliding-up if sidebar is visible
			if (isOpen) {
				$shopSidebar.removeClass('fade-in');
			}
			
			$shopSidebar.slideToggle(self.filterPanelSlideSpeed, function() {
				// Show filters after sliding-down if sidebar is hidden
				if (!isOpen) {
					$shopSidebar.addClass('fade-in');
				}
				
				self.filterPanelSliding = false;
				
				// Instantiate filter/widget scrollbars
				if (self.filterScrollbars && !self.filterScrollbarsLoaded) {
					self.filterScrollbarsInit();
				}
			});
		},
		
		
		/**
		 *	Shop filters: Toggle search panel
		 */
		shopFiltersSearchToggle: function() {
			var self = this;
			
			// Toggle panel (code in nm-core)
			self.searchPanelToggle();
			
			// Reset search query
			self.currentSearch = '';
		},
		
		
		/**
		 *	Shop filters: Hide active panel
		 */
		shopFiltersHideActivePanel: function() {
			var self = this,
				to = 0,
				$activeMenu = self.$shopFilterMenu.children('.active');
			
			// Hide active panel
			if ($activeMenu.length) {
				$activeMenu.removeClass('active');
				
				var activeData = $activeMenu.data('panel');
				
				// Categories panel should remain visible, don't "slideToggle"
				if ($activeMenu.is(':hidden') && activeData == 'cat') {
					self.shopFiltersCategoriesReset();
				} else {
					to = 300;
					
					var fn = self.shopFilterMenuFnNames[activeData];
					self[fn]();
				}
			}
			
			// Return timeout
			return to;
		},
		
		
		/**
		 *	Shop: AJAX load shop page from external link
		 */
		shopExternalGetPage: function(pageUrl) {
			var self = this;
			
			//console.log('NM: shopExternalGetPage() URL: '+pageUrl);
						
			if (pageUrl == window.location.href) {
				// Shop page is already loaded, scroll to shop-top
				self.shopScrollToTop();
			} else {
				// Remove current "active" class from categories menu
				$('#nm-shop-categories').children('.current-cat').removeClass('current-cat');
			
				// Smooth-scroll to top
				var to = self.shopMaybeScrollToTop();
				setTimeout(function() {
					self.shopGetPage(pageUrl); // Load shop page
				}, to);
			}
		},
		
		
		/**
		 *	Shop: AJAX load shop page
		 */
		shopGetPage: function(pageUrl, isBackButton, isProductTag) {
			var self = this;
			
			if (self.shopAjax) { return false; }
			
			if (pageUrl) {
				// Remove any visible shop notices
				self.shopRemoveNotices();												
				
				// Set current shop URL (used to reset search and product-tag AJAX results)
				self.shopSetCurrentUrl(isProductTag);
				
				// Smooth-scroll to top
				//var to = self.shopMaybeScrollToTop();
				//setTimeout(function() {
				
				// Hide active filter panel and scroll/jump to shop-top (if browser has "mobile" width)
				if (self.$body.width() < self.filterPanelHideWidth) {
					// Show 'loader' overlay
					self.shopShowLoader(true); // Args: disableAnimation
										
					var orgToggleSpeed = self.filterPanelSlideSpeed; // Save original panel slide speed
					self.filterPanelSlideSpeed = 0; // Disable panel slide speed
					
					self.shopFiltersHideActivePanel(); // Hide active filter panel
					self.shopScrollToTop(/*true*/); // Args: jumpTo
					
					self.filterPanelSlideSpeed = orgToggleSpeed; // Reset panel slide speed
				} else {
					// Show 'loader' overlay
					self.shopShowLoader();
				}
				
				// Make sure the URL has a trailing-slash before query args (301 redirect fix)
				pageUrl = pageUrl.replace(/\/?(\?|#|$)/, '/$1');
				
				// Set browser history "pushState" (if not back button "popstate" event)
				if (!isBackButton) {
					self.setPushState(pageUrl);
				}
				
				self.shopAjax = $.ajax({
					url: pageUrl,
					data: {
						shop_load: 'full'
					},
					dataType: 'html',
					cache: false,
					headers: {'cache-control': 'no-cache'},
					
					method: 'POST', // Note: Using "POST" method for the Ajax request to avoid "shop_load" query-string in pagination links
					
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log('NM: AJAX error - shopGetPage() - ' + errorThrown);
						
						// Hide 'loader' overlay (after scroll animation)
						self.shopHideLoader();
						
						self.shopAjax = false;
					},
					success: function(response) {
						// Update shop content
						self.shopUpdateContent(response);
						
						self.shopAjax = false;
					}
				});
				
				//}, to);
			}
		},
		
		
		/**
		 *	Shop: Update shop content with AJAX HTML
		 */
		shopUpdateContent: function(ajaxHTML) {
			var self = this,
				$ajaxHTML = $('<div>' + ajaxHTML + '</div>'); // Wrap the returned HTML string in a dummy 'div' element we can get the elements
						
			// Extract elements
			var $categories = $ajaxHTML.find('#nm-shop-categories'),
				$sidebar = $ajaxHTML.find('#nm-shop-sidebar'),
				$shop = $ajaxHTML.find('#nm-shop-browse-wrap');
											
			// Prepare/replace categories
			if ($categories.length) { 
				var $shopCategories = $('#nm-shop-categories');
				
				// Is the category menu open? -add 'force-show' class
				if ($shopCategories.hasClass('fade-in')) {
					$categories.addClass('fade-in force-show');
				}
				
				$shopCategories.replaceWith($categories); 
			}
			// Prepare/replace sidebar filters
			if ($sidebar.length) {
				var $shopSidebar = $('#nm-shop-sidebar');
				
				// Is the sidebar open? -add 'force-show' class
				if ($shopSidebar.hasClass('fade-in')) {
					$sidebar.addClass('fade-in force-show');
				
					$shopSidebar.replaceWith($sidebar);
					
					// Instantiate filter/widget scrollbars
					if (self.filterScrollbars) {
						self.filterScrollbarsInit();
					}
				} else {
					$shopSidebar.replaceWith($sidebar);
					
					// Sidebar is hidden, instantiate filter/widget scrollbars when "filter" link is clicked instead
					self.filterScrollbarsLoaded = false;
				}
			}
			// Replace shop
			if ($shop.length) { 
				self.$shopBrowseWrap.replaceWith($shop); 
			}
			
			// Get the new shop browse wrap
			self.$shopBrowseWrap = $('#nm-shop-browse-wrap');
			
			// Load images (init Unveil)
			self.shopLoadImages();
			
			
			if (!self.shopInfLoadBound) {	
				// Bind "infinite load" if enabled (initial shop page didn't have pagination)
				self.infload_init();
			}
			
			
			// Smooth-scroll to top
			var to = self.shopMaybeScrollToTop();
			setTimeout(function() {
				// Hide 'loader' overlay (after scroll animation)
				self.shopHideLoader();
			}, to);
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.filters = $.nmTheme.filters_init;
	
})(jQuery);
