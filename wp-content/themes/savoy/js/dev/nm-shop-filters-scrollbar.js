(function($) {
		
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
	
		/**
		 *	Initialize scripts
		 */
		filters_scrollbar_init: function() {
			var self = this;
			
			
			// Is this a touch WebKit browser?
			self.isTouchWebKit = (self.$html.hasClass('touch') && 'WebkitAppearance' in document.documentElement.style);
			
			
			/**
			 *	Class: Filter scrollbar
			 */
			var nmFilterScrollbar = function(widget) {
				var scrollbar = this;
				
				// Get widget elemenets
				scrollbar.$widget = $(widget);
				scrollbar.$widgetWrap = scrollbar.$widget.children('.nm-shop-widget-content');
				scrollbar.$widgetScroll = scrollbar.$widgetWrap.children('.nm-shop-widget-scroll');
				
				// Make sure container is scrollable
				if (scrollbar.$widgetScroll[0].scrollHeight > scrollbar.$widgetWrap.height()) {
					
					scrollbar.$widgetScroll.addClass('scrollable');
					
					// Is this a touch WebKit browser?
					if (self.isTouchWebKit) {
						// Add class to hide WebKit touch floatbar via CSS
						scrollbar.$widgetScroll.addClass('touch-webkit');
					} else {
						// Hide default (non-touch) scrollbar
						scrollbar.$widgetScroll.css('margin-right', '-'+self.scrollbarWidth+'px');
					}
					
					// Add/get scrollbar element
					scrollbar.$widgetWrap.prepend('<div class="nm-scrollbar"></div>');
					scrollbar.$scrollbar = scrollbar.$widgetWrap.children('.nm-scrollbar');
					
					scrollbar.$widgetScroll.on('scroll', function() { scrollbar._setPositionHeight(); });
					scrollbar.$scrollbar.on('mousedown', function(e) { scrollbar._startDrag(e); });
					
					// Set initial scroll position
					scrollbar._setPositionHeight();
				
				}
			};
			
			
			/**
			 *	Private - Filter scrollbar: Functions
			 */
			nmFilterScrollbar.prototype = {
				/* Set scrollbar position/height */
				_setPositionHeight: function() {
					var scrollbar 		= this,
						contentHeight	= scrollbar.$widgetScroll[0].scrollHeight,
						scrollOffset    = scrollbar.$widgetScroll.scrollTop(),
						scrollbarHeight	= scrollbar.$widgetWrap.height(),
						scrollbarRatio 	= scrollbarHeight / contentHeight,
						// Calculate scrollbar position/height
						handleOffset	= Math.round(scrollbarRatio * scrollOffset),
						handleHeight	= Math.floor(scrollbarRatio * scrollbarHeight);
						
						scrollbar.$scrollbar.css({'top': handleOffset, 'height': handleHeight});
				},
				
				
				/* Set-up scrollbar drag */
				_startDrag: function(e) {
					// Preventing the event's default action stops text being selectable during the drag
					e.preventDefault();
					
					var scrollbar = this;
					
					scrollbar.$scrollbar.addClass('dragging');
					
					// Measure how far the user's mouse is from the top of the scrollbar drag handle
					scrollbar.dragOffset = e.pageY - scrollbar.$scrollbar.offset().top;
			
					self.$document.on('mousemove.nmScrollbar', function(e) { scrollbar._drag(e); });
					self.$document.on('mouseup.nmScrollbar', function(e) { scrollbar._endDrag(e); });
				},
				
				
				/* Perform scrollbar drag */
				_drag: function(e) {
					e.preventDefault();
			
					var scrollbar = this, dragPos, dragPerc, scrollPos;
					
					// Calculate how far the user's mouse is from the top of the scrollbar (minus the dragOffset)
					dragPos = e.pageY - scrollbar.$widgetScroll.offset().top - scrollbar.dragOffset;
					// Convert the mouse position into a percentage of the scrollbar height
					dragPerc = dragPos / scrollbar.$widgetWrap.height();
					// Scroll the content by the same percentage
					scrollPos = dragPerc * scrollbar.$widgetScroll[0].scrollHeight;
			
					scrollbar.$widgetScroll.scrollTop(scrollPos);
				},
				
				
				/* End scrollbar drag */
				_endDrag: function() {
					this.$scrollbar.removeClass('dragging');
					
					self.$document.off('mousemove.nmScrollbar');
					self.$document.off('mouseup.nmScrollbar');
				}
			};
			
			
			
			/**
			 *	Public - Filter scrollbar: Instantiate widget scrollbars
			 */
			self.filterScrollbarsInit = function() {
				// Make sure the alternative/mobile filter layout is -not- displaying ("#nm-shop-sidebar-layout-indicator" placeholder element is set to "overflow:hidden" when it's displaying)
				if ($('#nm-shop-sidebar-layout-indicator').css('overflow') == 'hidden') {
					return;
				}
				
				//console.log('NM: Filter scrollbars init. (sidebar must be visible).');
				
				self.filterScrollbarsLoaded = true;
								
				// Get widget containers
				var $shopWidgets = $('#nm-shop-widgets-ul').children('li');
				
				$shopWidgets.each(function() {
					new nmFilterScrollbar(this);
				});
			};
			
			
			
			/* Bind: Window "resize" event */
			var to = null, filtersVisible = false;
				
			self.$window.bind('resize.nmScrollbar', function() {
				if (to) { clearTimeout(to); }
				
				to = setTimeout(function() {
					filtersVisible = $('#nm-shop-sidebar').hasClass('fade-in');
					
					// Make sure the filter scrollbars have not loaded and are visible
					if (!self.filterScrollbarsLoaded && filtersVisible) {
						// Instantiate filter/widget scrollbars
						self.filterScrollbarsInit();
					}
				}, 250);
			});
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.filters_scrollbar = $.nmTheme.filters_scrollbar_init;

})(jQuery);
