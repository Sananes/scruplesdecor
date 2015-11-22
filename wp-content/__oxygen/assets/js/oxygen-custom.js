/**
 *	Oxygen Main JavaScript File
 *
 *	Theme by: www.laborator.co
 **/

var public_vars = public_vars || {};

;(function($, window, undefined){

	"use strict";

	$(document).ready(function()
	{
		public_vars.$body         = $('body');
		public_vars.$mainSidebar  = $('.main-sidebar');
		public_vars.$sidebarMenu  = public_vars.$mainSidebar.find('.sidebar-menu');

		public_vars.$cartRibbon   = public_vars.$body.find('.cart-ribbon .cart_content');

		public_vars.$headerCart   = public_vars.$body.find('.header-cart');
		public_vars.$cartSubTotal = public_vars.$headerCart.find('.cart-sub-total .amount');
		public_vars.$cartItems    = public_vars.$headerCart.find('.cart-items');

		public_vars.$topMainMenu  = public_vars.$body.find('.main-menu-env li:has(> ul)');

		public_vars.$mobileMenu   = public_vars.$body.find('.mobile-menu');

		public_vars.$stickyLogo   = public_vars.$body.find('.logo-sticky');


		// NiceScroll
		$(".main-sidebar").niceScroll({
			cursorcolor: '#fafafa',
			cursorborder: '1px solid #fafafa',
			railpadding: {right: 0},
			railalign: 'right',
			cursorborderradius: 1
		});



		// Setup Sidebar Menu
		if(public_vars.$sidebarMenu.hasClass('collapsed-subs'))
		{
			setupCollapsedSidebarMenu();
		}


		// Setup Top Main Menu
		$.extend(public_vars, {hoverIndex: 100, mainTopMenuFall: 15, mainTopMenuDuration: .3});

		if(public_vars.$body.find(".main-menu-env .megaMenuContainer").length == 0)
		{
		public_vars.$topMainMenu.each(function(i, el)
		{
			var $this = $(el),
				$sub = $this.find('> ul');

			if($this.hasClass('has-sub-sub'))
				TweenLite.set($sub, {css: {x: -public_vars.mainTopMenuFall, y: 0}})
			else
				TweenLite.set($sub, {css: {y: public_vars.mainTopMenuFall}})

			$this.hoverIntent({
				over: function()
				{
					public_vars.hoverIndex++;

					$this.css({overflow: 'visible'});
					$sub.css({zIndex: public_vars.hoverIndex});

					if($this.hasClass('has-sub-sub'))
						TweenLite.to($sub, public_vars.mainTopMenuDuration, {css: {x: 0, autoAlpha: 1}});
					else
						TweenLite.to($sub, public_vars.mainTopMenuDuration, {css: {y: 0, autoAlpha: 1}});
				},

				out: function()
				{
					$sub.css({zIndex: public_vars.hoverIndex});

					if($this.hasClass('has-sub-sub'))
						TweenLite.to($sub, public_vars.mainTopMenuDuration, {css: {x: -public_vars.mainTopMenuFall, autoAlpha: 0}, onComplete: function()
						{
							$this.css({overflow: 'hidden'});
						}});
					else
						TweenLite.to($sub, public_vars.mainTopMenuDuration, {css: {y: public_vars.mainTopMenuFall, autoAlpha: 0}, onComplete: function()
						{
							$this.css({overflow: 'hidden'});
						}});
				},
				timeout: 200
			});
		});
		}



		// Autogrow
		$(".autogrow").autoGrow();



		// Lazy Load Images
		var lazy_load = [],
			lazyLoader = function()
			{
				if(lazy_load.length)
				{
					var img = lazy_load.shift(),
						img_loader = new Image();

					img_loader.src = img.src;

					img_loader.onload = function()
					{
						if(img.$el.hasClass('lab-lazy-load'))
							img.$el.attr('src', img.src).removeClass('lab-lazy-load');

						lazyLoader();
					}
				}
			};

		$("img:not(.lab-lazy-load):visible").each(function(i, el)
		{
			var $img = $(el),
				src = $img.attr('src');

			lazy_load.push({i: i, src: src, $el: $img});
		});


		$("img.lab-lazy-load").each(function(i, el)
		{
			var $img = $(el),
				src = $img.data('src');

			lazy_load.push({i: i, src: src, $el: $img});
		});

		$(window).load(lazyLoader);


		// Lightbox
		if($.isFunction($.fn.nivoLightbox))
		{
			$(".nivo a").nivoLightbox({
				effect: 'fade',
				theme: 'default',
			});
		}


		// Post Images Slider
		if($.isFunction($.fn.cycle))
		{
			var $pis = $(".post-imgs-slider");

			$.fn.cycle.log = function(){};

			imagesLoaded($pis.get(0), function()
			{
				$pis.find('.loading').remove();
				$pis.find('> a.hidden').removeClass('hidden');

				$pis.cycle({
					slides: '> a',
					prev: $pis.find('.pager .prev'),
					next: $pis.find('.pager .next'),
					log: function(){}
				});
			});


		}


		// Contact Form
		var $cf = $(".contact-form");

		if($cf.length && $cf.find('form').length)
		{
			var $cf_form = $cf.find('form'),
				$cf_title = $cf.find('h4');

			$cf_form.submit(function(ev)
			{
				ev.preventDefault();

				var fields = $cf_form.serializeArray(),
					$required = $cf_form.find('[data-required="1"]');


				// Check for required fields
				if($required.length)
				{
					var required = $required.serializeArray(),
						required_arr = [];

					for(var i in required)
					{
						required_arr.push(required[i].name);
					}
				}

				// Check For errors
				for(var i in fields.reverse())
				{
					var field = fields[i],
						$field = $cf_form.find('[name="'+field.name+'"]');

					// Required Field
					if($.inArray(field.name, required_arr) != -1)
					{

						if($.trim($field.val()) == '')
						{
							$field.addClass('has-errors').focus();
						}
						else
						{
							$field.removeClass('has-errors');
						}
					}

					// Email Field
					if(field.name == 'email' && $field.val().length)
					{
						if( ! validateEmail($field.val()))
						{
							$field.addClass('has-errors').focus();
						}
						else
						{
							$field.removeClass('has-errors');
						}
					}
				}


				// Send form data
				if( ! $cf_form.find('.has-errors').length && ! $cf.hasClass('is-loading') && ! $cf.data('is-done'))
				{

					fields.push({name: 'action', value: 'cf_process'});
					fields.push({name: 'verify', value: $cf_form.data('check')});
					fields.push({name: 'id', value: $cf_form.data('id')});

					$cf.addClass('is-loading');

					$.post(ajaxurl, fields, function(resp)
					{
						if(resp.success)
						{
							var $msg = $cf.find('.success-message');
							$msg.show();

							$cf.removeClass('is-loading');
							$cf.data('is-done', 1);

							$cf.find('[name]').fadeTo(200, .6).attr('readonly', true);

							packTheContactForm($cf);
						}
						else
						{
							alert("An error occured your message cannot be send!");
						}

					}, 'json');
				}
			});
		}



		// iCheck
		/*
if($.isFunction($.fn.iCheck))
		{
			$('input').iCheck({
				checkboxClass: 'icheckbox_flat',
				radioClass: 'iradio_flat'
			});
		}
*/


		// Toggle Tooltip
		if($.isFunction($.fn.tooltip))
			$('a[data-toggle="tooltip"], span[data-toggle="tooltip"]').tooltip({});


		// Product Gallery (Loop)
		$(".product.has-gallery").each(function(i, el)
		{
			var $this = $(el),
				$images = $this.find('.image.full-gallery .thumb img');

			if($images.length > 1)
			{
				$this.find('.image').append( '<a href="#" class="thumb-prev">Prev</a><a href="#" class="thumb-next">Next</a>' );

				var	$nextprev = $this.find('.thumb-prev, .thumb-next');


				$nextprev.on('click', function(ev)
				{
					ev.preventDefault();

					var dir = $(this).hasClass('thumb-prev') ? -1 : 1,
						$curr = $images.filter(':not(.hidden-slowly)'),
						$next = $curr.next();

					if(dir == 1)
					{
						if($next.length == 0)
							$next = $images.first();
					}
					else
					{
						$next = $curr.prev();

						if($next.length == 0)
							$next = $images.last();
					}

					$next.addClass('enter-in notrans ' + (dir == -1 ? 'ei-left' : '')).removeClass('hidden hidden-slowly hs-left hs-right');
					$curr.addClass('hidden-slowly ' + (dir == -1 ? 'hs-left' : ''));

					setTimeout(function(){ $next.removeClass('enter-in notrans ei-left'); }, 0);
				});
			}
		});


		// Product add to cart
		$(".product .add-to-cart[data-id]").each(function(i, el)
		{
			var $this = $(el),
				$product = $this.closest('.product');

			$this.on('click', function(ev)
			{
				ev.preventDefault();

				if($this.hasClass('added'))
					return;

				$product.addClass('is-loading');
				$this.tooltip('hide');

				// Data
				var data = {
					action: 'lab_add_to_cart',
					product_id: $this.data('id')
				};


				$.post(ajaxurl, data, function(resp)
				{
					$product.removeClass('is-loading');

					if(resp.success)
					{
						$this.addClass('added').tooltip('destroy');
						updateCartItemsNumber(resp.cart_items);

						updateHeaderCart(resp.cart_subtotal, resp.cart_html);

						setTimeout(function()
						{
							$this.removeClass('added');
							$this.tooltip();

						}, 1000 * 1.5);

						$(document).trigger('added_to_cart', [$this, resp.cart_html]);
						
						// Refresh Cart
						$(".widget_shopping_cart").show();
						$(".widget_shopping_cart_content").html( resp.cart_html_frag );
					}
					else
					{
						var $ec = $product.find('.error-container');

						$product.addClass('has-errors');
						$ec.html('');

						$.each(resp.error_msg, function(i, msg)
						{
							$ec.html( $('<span>').html(msg) );
						});

						setTimeout(function()
						{
							$product.removeClass('has-errors');
						}, 1000 * 2);
					}

				}, 'json');
			});
		});


		// Open Header Cart
		public_vars.$cartRibbon.closest('.cart-ribbon').on('click', function(ev)
		{
			ev.preventDefault();

			if( ! public_vars.$body.hasClass('header-cart-open'))
			{
				public_vars.$headerCart.slideDown( function() {
					$(window).on( 'click', automaticallyCloseHeaderCart );
				} );
				
				public_vars.$body.addClass('header-cart-open');
			}
			else
			{
				public_vars.$headerCart.slideUp('normal', function()
				{
					public_vars.$body.removeClass('header-cart-open');
				});
			}
		});
		
		// Close Header Cart when clicking outside the area
		var automaticallyCloseHeaderCart = function( ev ){
			
			if( public_vars.$headerCart.is( ':visible' ) ) {
				
				if( ! $( ev.target ).closest( '.header-cart' ).length ) {
					
					$( window ).off( 'click', automaticallyCloseHeaderCart );
					
					public_vars.$headerCart.slideUp('normal', function()
					{
						public_vars.$body.removeClass('header-cart-open');
					});
				}
			}
		};

		// Open Header Cart (Mobile Menu)
		/*public_vars.$mobileMenu.find('.cart-items').on('click', function(ev)
		{
			ev.preventDefault();

			if( ! public_vars.$body.hasClass('header-cart-open'))
			{
				public_vars.$headerCart.slideDown();
				public_vars.$body.addClass('header-cart-open');
			}
			else
			{
				public_vars.$headerCart.slideUp('normal', function()
				{
					public_vars.$body.removeClass('header-cart-open');
				});
			}
		});*/



		// Shipping Method
		$(".cart-totals #shipping_method input")
		.on('change', function()
		{
			var shipping_methods = $(this).val();

			updateShippingMethods(shipping_methods);
		});

		$(".cart-totals select.shipping_method").on('change', function()
		{
			var shipping_methods = $(this).val();

			updateShippingMethods(shipping_methods);
		});


		$("body").on('updated_checkout', function()
		{
			// iCheck
			if($.isFunction($.fn.iCheck))
			{
				$('input[type="checkbox"], input[type="radio"]').each(function(i, el)
				{
					if( ! $(el).data('iCheck'))
					{
						/*$(el).iCheck({
							checkboxClass: 'icheckbox_flat',
							radioClass: 'iradio_flat'
						});.on('ifToggled', function(ev)
						{
							$(this).trigger('change');
						});*/
					}
				});
			}
		});


		// My Account Tabs
		var $account_tabs = $(".myaccount-env .myaccount-tabs li > a");

		$account_tabs.each(function(i, el)
		{
			var $tab = $(el);

			if($tab.attr('href').match(/^\#/))
			{
				var $tab_content = $(".myaccount-tab" + $tab.attr('href'));

				if(window.location.hash.toString() == $tab.attr('href'))
				{
					setTimeout(function()
					{
						if( ! $tab_content.length)
						{
							$tab_content = $(".myaccount-tab" + $tab.attr('href'));
						}

						var $other_tabs = $(".myaccount-tab").not( $tab_content );

						$other_tabs.hide();
						$tab_content.show();

						$account_tabs.parent().removeClass('active');
						$tab.parent().addClass('active');
					}, 1);
				}

				$tab.on('click', function(ev)
				{
					ev.preventDefault();

					if( ! $tab_content.length)
					{
						$tab_content = $(".myaccount-tab" + $tab.attr('href'));
					}

					var $other_tabs = $(".myaccount-tab").not( $tab_content );

					$other_tabs.hide();
					$tab_content.fadeIn(300);

					$account_tabs.parent().removeClass('active');
					$tab.parent().addClass('active');

					var stop = $(window).scrollTop();
					window.location.hash = $tab.attr('href').replace('#', '');

					$(window).scrollTop(stop);

				});
			}
		});



		// Variations Select Replacment
		/*
var $variations = $(".product-single .variations select");

		if($variations.length)
		{
			var $variations_form = $(".variations_form");

			$variations_form.on('found_variation', function(ev)
			{
				var owl = $("#main-image-slider").data('owlCarousel');

				if(typeof owl != 'undefined')
				{
					owl.jumpTo(1, false);
					owl.goTo(0, false);
				}
			});
		}
*/



		// Select Box Replacement
		var $select_wrapper = $('.product-single .variations select, select.oxy-list');

		$select_wrapper.each(function(i, el)
		{
			var $this = $(el);

			$this.css({width: '100%'}).wrap($('<div class="select-wrapper" />'));

			var $select = $this.parent(),
				$placeholder = $('<span class="select-placeholder" />');

			$select.prepend($placeholder);
			$select.prepend('<div class="select-arrow" />');

			$placeholder.html($this.find('option:selected').html());

			$this.on('change', function(ev)
			{
				$placeholder.html($this.find('option:selected').html());
			});
		});



		// Laborator WooCommerce Rating
		var $lr = $("#laborator-rating");

		if($lr.length)
		{
			var $rating_wrapper = $('<div class="rating" />');
			$lr.hide().after( $rating_wrapper );

			$($lr.find('option').get().reverse()).each(function(i, el)
			{
				var $rate_option = $(el),
					rating = 5-i;

				if($rate_option.attr('value').match(/[0-9]+/))
					$rating_wrapper.prepend( '<a href="#" data-rating="' + rating + '" class="glyphicon glyphicon-star star-' + rating + '"></a>' );
			});

			$rating_wrapper.data('current-rating', 0);

			$rating_wrapper
			.on('click', 'a', function(ev)
			{
				ev.preventDefault();

				var $this = $(this),
					rating = $this.data('rating');

				$lr.find('option').attr('selected', false);

				if($rating_wrapper.data('current-rating') == rating)
				{
					$rating_wrapper.attr('class', 'rating');
					$rating_wrapper.data('current-rating', 0);
				}
				else
				{
					$rating_wrapper.attr('class', 'rating filled-' + rating);
					$rating_wrapper.data('current-rating', rating);
					$lr.find('option[value="' + rating + '"]').attr('selected', true);
				}

			})
			.on('mouseover', 'a', function(ev)
			{
				$rating_wrapper.removeClass('hover-1 hover-2 hover-3 hover-4 hover-5').addClass('hover-' + $(this).data('rating'));
			})
			.on('mouseout', 'a', function(ev)
			{
				$rating_wrapper.removeClass('hover-1 hover-2 hover-3 hover-4 hover-5');
			});
		}



		// Laborator WooCommerce Quickview
		if(typeof CBPGridGallery != 'undefined')
		{
			var $items_env = $(".laborator-woocommerce .shop-grid");

			$items_env.each(function(i, el)
			{
				new CBPGridGallery( el );
			});
		}



		// Add to wishlist
		var $yatw = $( '.yith-add-to-wishlist' );
		
		$( 'body' ).on( 'added_to_wishlist', function( ev ) {
			$yatw.removeClass('is-loading');
			$yatw.parent().addClass('wishlisted');
		} );
		
		$(".yith-add-to-wishlist").each(function(i, el)
		{
			$( el ).on('click', function(ev)
			{
				ev.preventDefault();

				$( el ).addClass('is-loading');
			});
		});



		// Quickview Carousel
		$(window).bind('cbpOpen', function(ev, pos, cbp)
		{
			var $qv_item = $(cbp.currentItem),
				owl = $qv_item.data('owlCarousel');

			if( ! owl)
			{
				$qv_item.find('.product-gallery').owlCarousel({
					items: 1,
					navigation: true,
					pagination: false,
					singleItem: true,
					//autoHeight: true,
					slideSpeed: 400,
					beforeInit: function()
					{
						var $hidden = $qv_item.find('.product-gallery .hidden');

						if($hidden.length > 0)
						{
							$hidden.closest('.product-gallery-env').addClass('has-gallery');
							$hidden.removeClass('hidden');
						}
					}
				});
			}
		});



		// Search Field
		var $menu_top_search = $(".main-menu-top .search-form, .top-menu-centered .search-form");

		if($menu_top_search.length)
		{
			var $mts_input = $menu_top_search.find('.search-input-env'),
				$mts_input_real = $mts_input.find('input');

			$menu_top_search.on('click', 'a', function(ev)
			{
				ev.preventDefault();
				
				if( $mts_input_real.val().length > 0 ) {
					$menu_top_search.submit();
					return;
				}
				
				if($mts_input_real.val().length && ! $mts_input.is(':visible'))
				{
					$menu_top_search.submit();
				}

				$mts_input.toggleClass('visible');

				if($mts_input.hasClass('visible'))
				{
					setTimeout(function(){ $mts_input_real.focus(); }, 100);
				}
			});

			$mts_input_real.on('blur', function()
			{
				$mts_input.removeClass('visible');
			});
		}


		// Scroll Reveal
		if(typeof WOW != 'undefined')
		{
			setTimeout(function(){
				new WOW().init();
			}, 500);
		}



		// VC Separator Custom Settings
		$(".vc_separator.one-line-border, .vc_separator.double-bordered, .vc_separator.double-bordered-thin, .vc_separator.double-bordered-thick").each(function(i, el)
		{
			var $this = $(el),
				el_class = $this.attr('class'),
				matches;

			if(matches = el_class.match(/custom-color-([a-f0-9]+)/))
			{
				$this.find('.vc_sep_line').css({
					borderColor: '#' + matches[1]
				});

				if($this.hasClass('one-line-border'))
				{
					$this.find('h4').css({
						borderColor: '#' + matches[1]
					});
				}
			}

			if(matches = el_class.match(/__(.*?)__/i))
			{
				$this.find('h4').append( '<span>' + matches[1].replace(/-/g, ' ') + '</span>' );
			}
		});


		// Mobile Menu
		var mobile_menu_duration = 300,
			setupMobileMenuHeight = function()
			{
				public_vars.$mobileMenu
				.addClass('visible-xs')
					.removeClass('hidden')
						.attr('style', 'display: block !important')
							.data('height', public_vars.$mobileMenu.outerHeight());

				public_vars.$mobileMenu.find('.menu-item-has-children').each(function(i, el)
				{
					var $this = $(el),
						$sub = $this.find('> ul');

					// Calculate height
					$sub
					.attr('style', 'display: block !important')
						.data('height', $sub.outerHeight())
							.attr('style', 'height: 0px; display: block');
				});

				public_vars.$mobileMenu.attr('style', 'height: 0px');
			};


		public_vars.$body.find('.mobile-menu-link > a').on('click', function(ev)
		{
			ev.preventDefault();

			var $this = $(this);

			if( ! $this.hasClass('opened'))
			{
				$this.addClass('opened');

				setTimeout(function(){ public_vars.$mobileMenu.addClass('visible'); }, mobile_menu_duration/2);

				TweenLite.to(public_vars.$mobileMenu, mobile_menu_duration/1000, {css: {height: public_vars.$mobileMenu.data('height')}, ease: Power2.easeInOut, onComplete: function()
				{
					public_vars.$mobileMenu.height('');
				}});
			}
			else
			{
				$this.removeClass('opened');

				var $visible = public_vars.$mobileMenu.find('li.visible');

				public_vars.$mobileMenu.add( $visible ).removeClass('visible');

				TweenLite.to(public_vars.$mobileMenu, mobile_menu_duration/1000, {css: {height: 0}, delay: (mobile_menu_duration/2)/1000, onComplete: function()
				{
					$visible
					.find('> ul')
						.height(0)
							.removeClass('visible');

					$visible
					.find('.expand')
						.removeClass('expanded');
				}});
			}
		});

		public_vars.$mobileMenu.find('.menu-item-has-children').each(function(i, el)
		{
			var $this = $(el),
				$plus = $('<span class="expand"><i class="entypo-plus"></i></span>'),
				$sub = $this.find('> ul');

			$this.find('> a').prepend( $plus );

			// Calculate height (dep)
			//$sub.addClass('visible-xs').data('height', $sub.outerHeight()).css({height: 0});

			$plus.on('click', function(ev)
			{
				ev.preventDefault();

				if( ! $plus.hasClass('expanded'))
				{
					$plus.addClass('expanded');

					setTimeout(function(){ $this.addClass('visible'); }, mobile_menu_duration/2);

					TweenLite.to($sub, mobile_menu_duration/1000, {css: {height: $sub.data('height')}, onComplete: function()
					{
						$sub.height('');
					}});
				}
				else
				{
					$plus.removeClass('expanded');

					var $subs = $sub.add( $sub.find('ul') );

					$subs.parent().removeClass('visible');

					TweenLite.to($subs, mobile_menu_duration/1000, {css: {height: 0}, delay: (mobile_menu_duration/2)/1000, onComplete: function()
					{
						$subs.find('.expand').removeClass('expanded');
					}});
				}
			});
		});

		setupMobileMenuHeight();



		// Sticky Menu
		if(public_vars.$body.hasClass('sticky-menu'))
			setupStickMenu();



		// Testimonials Switcher
		$(".lab_wpb_testimonials").each(function(i, el)
		{
			var $testimonials    	= $(el),
				$inner              = $testimonials.find('.testimonials-inner'),
				$items              = $testimonials.find('.testimonial-entry'),
				$items_hidden       = $items.filter('.hidden'),
				autoswitch          = $testimonials.data('autoswitch'),
				$nav                = $('<div class="testimonials-nav">'),
				current_slide       = 0;

			$items.eq(current_slide).addClass('current');

			$items_hidden.removeClass('hidden').hide();

			if($items.length > 1)
			{
				for(var i=0; i<$items.length; i++)
				{
					$nav.append('<a href="#"'+(i == current_slide ? ' class="active"' : '')+' data-index="'+i+'">'+(i+1)+'</a>');
				}

				$inner.append( $nav );
			}

			var goToSlide = function(index)
			{
				if(current_slide != index)
				{
					index = index % $items.length;

					var $to_hide = $items.filter('.current'),
						$to_show = $items.eq(index);

					$to_show.show();
					$to_hide.hide();

					var next_height = $to_show.outerHeight(true) + $nav.outerHeight();

					$to_hide.show();
					$to_show.hide();



					$nav.find('a').removeClass('active').eq(index).addClass('active');

					TweenLite.to($to_hide, .15, {css: {autoAlpha: 0}, onComplete: function()
					{
						$to_hide.attr('style', '').removeClass('current').hide();
						$to_show.show().addClass('current');

						TweenLite.set($to_show, {css: {autoAlpha: 0}});

						TweenLite.to($to_show, .35, {css: {autoAlpha: 1}, onComplete: function()
						{
							current_slide = index;
						}});
					}});

					TweenLite.to($inner, .3, {css: {height: next_height}, onComplete: function()
					{
						$inner.attr('style', '');
					}});
				}
			};

			$nav.on('click', 'a', function(ev)
			{
				ev.preventDefault();
				goToSlide( parseInt($(this).data('index'), 10) );
			});


			if(autoswitch > 0)
			{
				var hover_tm = 0,
					setupAutoSwitcher = function(on)
					{
						window.clearTimeout(hover_tm);

						if(on)
						{
							hover_tm = setTimeout(function()
							{
								goToSlide(current_slide+1);
								setupAutoSwitcher(1);

							}, autoswitch * 1000);
						}
					};

				$testimonials
				.on('mouseover', function()
				{
					setupAutoSwitcher();
				}).
				on('mouseleave', function()
				{
					setupAutoSwitcher(true);
				});

				setupAutoSwitcher(true);
			}
		});


		// Ajax Counter
		var $cart_ajax_counter = $(".cart-ribbon[data-ajax-counter]");

		if($cart_ajax_counter.length)
		{
			$.post(ajaxurl, {action: 'lab_get_cart_info'}, function(resp)
			{
				$cart_ajax_counter.find('.number').html(resp.count);
				updateHeaderCart(resp.cart_subtotal, resp.cart_html);

				if(resp.count == 0)
				{
					public_vars.$cartItems.html(resp.cart_html);
				}

			}, 'json');
		}


		// Commision King Plugin
		var $ck_items = $(".commission-rates, .my-commissions, .payment-details");

		if($ck_items.length == 3)
		{
			$ck_items.wrapAll('<div class="myaccount-tab" id="comission-king"></div>');
			$ck_items.find('table').addClass('table');

			$ck_items.find('input[type="text"], textarea').addClass('form-control');
			$ck_items.find('.button').addClass('btn btn-default');

			var $submit = $ck_items.find('.button[onclick]');

			$submit.parent().before($submit);
		}


		// Quantity Buttons for WooCommerce 2.3.x
		var replaceWooCommerceQuantityButtons = function()
		{
			$(".quantity").each(function(i, el)
			{
				var $quantity = $(el),
					$button = $quantity.find('.qty');

				if($quantity.hasClass('buttons_added'))
					return;

				$quantity.addClass('buttons_added');

				$button.before('<input type="button" value="-" class="plusminus minus">');
				$button.after('<input type="button" value="+" class="plusminus plus">');
			});
		};

		replaceWooCommerceQuantityButtons();

		$("body").on('click', 'input[type="button"].plusminus', function()
		{
			var $this = $(this),
				$quantity = $this.prev(),
				add = 1;

			if($this.hasClass('minus'))
			{
				$quantity = $this.next();
				add = -1;
			}
			
			var newVal = parseInt($quantity.val(), 10) + add;
			
			if(newVal < 0)
			{
				newVal = 0;
			}

			$quantity.val(newVal);
		});
		
		
		
		$( "#yith-wcwl-form .show-title-form" ).on( 'click', function( ev ) {
			ev.preventDefault();
			
			$( this ).next().slideToggle( 'fast', function() {
				$( this ).parent().find( '.form-control ').focus();
			} );
		} );
	});



	// Enable/Disable Resizable Event
	var wid = 0;

	$(window).resize(function() {
		clearTimeout(wid);
		wid = setTimeout(trigger_resizable, 200);
	});


})(jQuery, window);


function packTheContactForm($cf)
{
	// Mail is sent - Resize Contact Form
	var base_ratio = 100 / 70,
		ratio = $cf.width() / $cf.height(),
		width = $cf.outerWidth() + 10,
		height = parseInt(($cf.height() * ratio) / base_ratio);

	var $cbe = jQuery(".contact-blocks-env");

	$cbe.height( $cbe.height() );

	TweenLite.to($cf, .5, {css: {height: height}, onComplete: function()
	{
		resizeEmailIcon(width, height, $cf);

		var tm = new TimelineLite();

		tm.append( TweenLite.to($cf, .2, {css: {scale: .8}, delay: 1.5}) );
		tm.append( TweenLite.to($cf, .5, {css: {left: 200, autoAlpha: 0}, ease: Back.easeIn, onComplete: function()
		{
			TweenLite.to($cf, .5, {css: {height: 0}});
			setTimeout(function()
			{
				jQuery('.contact-form-block .success-message').slideDown('normal');
			}, 500);
		}}) );
	}});
}


function resizeEmailIcon(width, height, $cf)
{
	// Mail Sent
	var $mail_sent 	= jQuery(".mail-sent"),
		$ms_left 	= $mail_sent.find('.mail-left'),
		$ms_right 	= $mail_sent.find('.mail-right'),
		$ms_top		= $mail_sent.find('.mail-top'),
		$ms_bottom 	= $mail_sent.find('.mail-bottom'),

		thickness	= 5;

	$mail_sent.css({
		width: width,
		height: height
	});

	$mail_sent.fadeIn(300, function()
	{
		$mail_sent.addClass('visible');
	});

	var h2_th = height/2 - thickness,
		w2_th = width/2 - thickness,
		w2 = width/2;

	$ms_left.css('border-width', h2_th + "px 0 " + h2_th + "px " + h2_th + "px");
	$ms_right.css('border-width', h2_th + "px " + h2_th + "px " + h2_th + "px 0");
	$ms_top.css('border-width', w2_th + "px " + w2_th + "px 0" + w2_th + "px");
	$ms_bottom.css('border-width', "0 " + w2 + "px " + w2 + "px " + w2 + "px");
}


function setupCollapsedSidebarMenu()
{
	var $ = jQuery,
		$nav = public_vars.$sidebarMenu.find('> .nav'),
		$root_items = $nav.find('> li:has(ul)'),

		duration = .35,
		ease = Quad.easeInOut

		odd_len = 100,
		even_len = 200,

		opacity_class = 'opacity-hidden';

	// Calculate Heights
	calculateMenuHeights($root_items);

	// Setup Hover Intent
	$nav.find('li:has(ul):not(.current-menu-ancestor)').each(function(i, el)
	{
		var $this 	 = $(el),
			$sub     = $this.children('ul'),
			height   = $this.data('height'),
			$odd     = $sub.find('> li:odd'),
			$even    = $sub.find('> li:even'),

			odd_tm	 = 0,
			even_tm	 = 0;


		$sub.find('> li').addClass(opacity_class);

		$this.hoverIntent({
			over: function()
			{
				window.clearTimeout(odd_tm);
				window.clearTimeout(even_tm);

				odd_tm = setTimeout(function(){ $odd.removeClass(opacity_class);  }, duration * 1000 - odd_len);
				even_tm = setTimeout(function(){ $even.removeClass(opacity_class); }, duration * 1000 - even_len);

				TweenLite.to($sub, duration, {css: {height: height}, ease: ease, onComplete: function(){
					$sub.css({height: 'auto'});
				}});
			},

			out: function()
			{
				window.clearTimeout(odd_tm);
				window.clearTimeout(even_tm);

				odd_tm = setTimeout(function(){ $odd.addClass(opacity_class);  }, odd_len);
				even_tm = setTimeout(function(){ $even.addClass(opacity_class); }, even_len);

				TweenLite.to($sub, duration, {css: {height: 0}, delay: Math.max(odd_len, even_len)/1000 + duration/2, ease: ease});
			},

			timeout: 100
		});
	});


	// Show Current Item
	$nav.find('.current_page_ancestor:has(ul)').each(function(i, el)
	{
		var $this = $(el),
			$sub = $this.children('ul');

		$sub.find('li').removeClass(opacity_class);
		$sub.css({height: 'auto'});
	})
}


function calculateMenuHeights($lis)
{
	var $ = jQuery;

	$lis.each(function(i, el)
	{
		var $this = $(el),
			$sub = $this.children('ul');

		// Show the sub
		$sub.css({display: 'block', height: 'auto'});

		var height = $sub.outerHeight(true);

		$this.data('height', height);

		// Continue with Sub elements
		if($sub.find('> li:has(ul)').length)
		{
			calculateMenuHeights($sub.find('> li'));
		}

	});

	// Hide Items
	$lis.find('> ul').removeAttr('style').css({display: 'block', height: 0});
}


function validateEmail(email)
{
	var emailPattern = /^[a-zA-Z0-9._]+[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z\.]{2,}$/;
	return emailPattern.test(email);
}


function updateCartItemsNumber(count)
{
	var $bucket = public_vars.$cartRibbon.find('.bucket'),
		$number = public_vars.$cartRibbon.find('.number').add( public_vars.$mobileMenu.find('.cart-items span') ),
		padding_top = parseInt(public_vars.$cartRibbon.css('padding-top'), 10);

	TweenLite.to(public_vars.$cartRibbon, .2, {css: {paddingTop: padding_top * 1.5}});
	TweenLite.to(public_vars.$cartRibbon, .1, {css: {paddingTop: padding_top}, delay: .2});

	TweenLite.to($number, .2, {css: {scale: .4}, onComplete: function()
	{
		$number.html(count);
		TweenLite.to($number, .2, {css: {scale: 1}});


		var t = .2;

		TweenLite.to($bucket, t, {css: {transform: "rotate(5deg)"}});
		TweenLite.to($bucket, t, {css: {transform: "rotate(-5deg)"}, delay: t * 1});
		TweenLite.to($bucket, t, {css: {transform: "rotate(0)"}, delay: t * 2});

	}});
}


function updateHeaderCart(subtotal, cart_contents)
{
	var owl = public_vars.$cartItems.data('owlCarousel');

	if(typeof owl == 'object')
	{
		owl.destroy();
		public_vars.$cartItems.hide();

		TweenLite.to(public_vars.$cartItems, .1, {css: {autoAlpha: 0}, onComplete: function()
		{
			public_vars.$cartItems.html(cart_contents);
			public_vars.$cartItems.show();

			public_vars.$cartItems.owlCarousel({
				items: 4,
				navigation: true,
				pagination: false
			});

			TweenLite.to(public_vars.$cartItems, .2, {css: {autoAlpha: 1}});
		}});
	}
	else
	if(jQuery.isFunction(jQuery.fn.owlCarousel))
	{
		public_vars.$cartItems.html(cart_contents);

		public_vars.$cartItems.owlCarousel({
			items: 4,
			navigation: true,
			pagination: false
		});
	}

	TweenLite.to(public_vars.$cartSubTotal, .3, {css: {autoAlpha: 0}, onComplete: function()
	{
		public_vars.$cartSubTotal.html(subtotal);
		TweenLite.to(public_vars.$cartSubTotal, .3, {css: {autoAlpha: 1}});
	}});
}


function updateShippingMethods(shipping_methods)
{
	if(typeof wc_cart_params != 'undefined')
	{
		var data = {
			action: 'laborator_update_shipping_method',
			security: wc_cart_params.update_shipping_method_nonce,
			shipping_method: [shipping_methods]
		};

		jQuery(".cart-totals").addClass('is-loading');

		jQuery.post(ajaxurl, data, function(response)
		{
			jQuery(".cart-totals").removeClass('is-loading');

			jQuery(".cart-totals .subtotal .value").html(response.subtotal);
			jQuery(".cart-totals .total .value").html(response.total);
			jQuery(".cart-totals .tax-rate .value").html(response.vat_total);

		}, 'json');
	}
	else
	if(typeof wc_checkout_params != 'undefined')
	{
		var $				= jQuery,
			payment_method  = $( '#order_review input[name=payment_method]:checked' ).val(),
			country			= $( '#billing_country' ).val(),
			state			= $( '#billing_state' ).val(),
			postcode		= $( 'input#billing_postcode' ).val(),
			city			= $( '#billing_city' ).val(),
			address			= $( 'input#billing_address_1' ).val(),
			address_2		= $( 'input#billing_address_2' ).val(),
			s_country,
			s_state,
			s_postcode,
			s_city,
			s_address,
			s_address_2;


		if ( $( '#ship-to-different-address input' ).is( ':checked' ) || $( '#ship-to-different-address input' ).size() === 0 ) {
			s_country		= $( '#shipping_country' ).val();
			s_state			= $( '#shipping_state' ).val();
			s_postcode		= $( 'input#shipping_postcode' ).val();
			s_city			= $( '#shipping_city' ).val();
			s_address		= $( 'input#shipping_address_1' ).val();
			s_address_2		= $( 'input#shipping_address_2' ).val();
		} else {
			s_country		= country;
			s_state			= state;
			s_postcode		= postcode;
			s_city			= city;
			s_address		= address;
			s_address_2		= address_2;
		}

		var data = {
			action:						'laborator_update_order_review',
			security:					wc_checkout_params.update_order_review_nonce,
			shipping_method:			[shipping_methods],
			payment_method:				payment_method,
			country:					country,
			state:						state,
			postcode:					postcode,
			city:						city,
			address:					address,
			address_2:					address_2,
			s_country:					s_country,
			s_state:					s_state,
			s_postcode:					s_postcode,
			s_city:						s_city,
			s_address:					s_address,
			s_address_2:				s_address_2,
			post_data:					$( 'form.checkout' ).serialize()
		};

		jQuery(".cart-totals").addClass('is-loading');

		xhr = $.ajax({
			type:		'POST',
			dataType: 	'json',
			url:		wc_checkout_params.ajax_url,
			data:		data,
			success:	function( response ) {

				if ( response )
				{
					jQuery(".cart-totals").removeClass('is-loading');

					jQuery(".cart-totals .cart-subtotal .value").html(response.subtotal);
					jQuery(".cart-totals .order-total .value").html(response.total);
					jQuery(".cart-totals .tax-rate .value").html(response.vat_total);

					$( 'body' ).trigger('updated_checkout' );
				}
			}
		});
	}
}



function launchFullscreen(element)
{
	if(element.requestFullscreen)
	{
		element.requestFullscreen();
	}
	else
	if(element.mozRequestFullScreen)
	{
		element.mozRequestFullScreen();
	}
	else
	if(element.webkitRequestFullscreen)
	{
		element.webkitRequestFullscreen();
	}
	else
	if(element.msRequestFullscreen)
	{
		element.msRequestFullscreen();
	}
}

function exitFullscreen()
{
	if(document.exitFullscreen)
	{
		document.exitFullscreen();
	}
	else
	if(document.mozCancelFullScreen)
	{
		document.mozCancelFullScreen();
	}
	else
	if(document.webkitExitFullscreen)
	{
		document.webkitExitFullscreen();
	}
}



// Fullwidth an element relative to the document width
function forceFullWidth( $el )
{
	// Reset CSS Margin
	$el.css({marginLeft: '', width: ''});

	var left = $el.offset().left;

	//$el.css({marginLeft: -left, marginRight: -right, overflow: 'hidden'});

	$el.css({
		width: jQuery(document).width(),
		marginLeft: -left
	});

	// Reset fullwidth
	jQuery(window).on('lab.resize', function(){ forceFullWidth( $el ); });
}



// Sticky menu
function setupStickMenu()
{
	// Menu type
	var $ = jQuery,
		menu_type = public_vars.$body.hasClass('ht-1') ? 1 : (public_vars.$body.hasClass('ht-2') ? 2 : 3);
		

	// Sticky Menu for Menu Type 2
	if(menu_type == 2)
	{
		var $header = public_vars.$body.find('.top-menu'),
			$main_menu = public_vars.$body.find('.main-menu-top'),
			$spacer = $('<div></div>'),
			extra_top = public_vars.$body.hasClass('admin-bar') ? 32 : 0;

		if( ! $header.length)
			return;

		var watcher = scrollMonitor.create( $header, -extra_top);

		watcher.lock();

		$header.after( $spacer );

		var menu_height = public_vars.$stickyLogo.next().innerHeight();

		public_vars.$stickyLogo.css({display: 'block'}).data('width', public_vars.$stickyLogo.innerWidth()).css({width: 0, lineHeight: menu_height + 'px'});

		if(public_vars.$stickyLogo.hasClass('image-logo'))
		{
			public_vars.$stickyLogo
			.data('width', 0)
				.find('img')
					.css({maxHeight: menu_height - 2 * 15});
		}

		$main_menu.css({
			top: extra_top
		});

		watcher.exitViewport(function()
		{
			if(isxs())
				return;

			$header.hide();
			$spacer.height( $header.height() + $main_menu.outerHeight(true));

			$main_menu.addClass('sticky');

			TweenLite.to(public_vars.$stickyLogo, .3, {css: {width: public_vars.$stickyLogo.data('width'), autoAlpha: 1}});
		});

		watcher.enterViewport(function()
		{
			if(isxs())
				return;

			$header.attr('style', '');
			$spacer.height(0);

			$main_menu.removeClass('sticky');

			TweenLite.to(public_vars.$stickyLogo, .3, {css: {width: 0, autoAlpha: 0}});
		});

		$(window).load(function()
		{
			if(public_vars.$stickyLogo.hasClass('image-logo'))
			{
				public_vars.$stickyLogo.css({width: ''}).data('width', public_vars.$stickyLogo.width()).css({width: 0});
			}
			else
			{
				var old_width = public_vars.$stickyLogo.data('width');

				public_vars.$stickyLogo.css({width: ''}).data('width', public_vars.$stickyLogo.width()).css('width', 0);

				if($main_menu.hasClass('sticky'))
					public_vars.$stickyLogo.css({width: old_width});
			}

			if($main_menu.hasClass('sticky'))
				TweenLite.to(public_vars.$stickyLogo, .3, {css: {width: public_vars.$stickyLogo.data('width'), autoAlpha: 1}});
		});
	}
	// END: Sticky Menu for Menu Type 2



	// Sticky Menu for Menu Type 3
	if(menu_type == 3)
	{
		var $header = public_vars.$body.find('.top-menu'),
			$sec_nav = $header.find('.sec-nav-menu'),
			$spacer = $('<div class="header-menu-spacer"></div>'),
			extra_top = public_vars.$body.hasClass('admin-bar') ? 32 : 0;
			tm_out = 0;

		if($header.length)
		{
			var watcher = scrollMonitor.create( $header, -extra_top);
	
			watcher.lock();
	
			$header.after( $spacer );
	
			var menu_height = public_vars.$stickyLogo.next().innerHeight();
	
			watcher.exitViewport(function()
			{
				if(isxs())
					return;
	
				$spacer.height( $header.outerHeight(true) );
	
				$header.addClass('sticky-header').removeClass('is-hidden show-header');
	
				window.clearTimeout(tm_out);
	
				tm_out = setTimeout(function()
				{
					$header.addClass('visible');
					$sec_nav.addClass('hidden');
	
				}, 100);
			});
	
			watcher.fullyEnterViewport(function(e)
			{
				if(isxs() || ! e)
					return;
	
				$spacer.height(0);
				$header.removeClass('sticky-header visible').addClass('is-hidden');
				$sec_nav.removeClass('hidden');
				$sec_nav.next().removeClass('hidden');
	
				if(tm_out)
				{
					window.clearTimeout(tm_out);
				}
	
				tm_out = setTimeout(function()
				{
					$header.addClass('show-header');
				}, 50);
			});
			
			return;
		}
	}
	// END: Sticky Menu for Menu Type 3



	// Sticky Menu for Menu Type 3 - centered
	if(menu_type == 3)
	{
		$header = public_vars.$body.find('.top-menu-centered');
		
		var $nav_env = $header.find('.navs');
		
		if($header.length && $nav_env.length)
		{
			$nav_env.before($spacer);
			
			var navWatcher = scrollMonitor.create($nav_env, {top: extra_top});
			
			navWatcher.lock();
			
			navWatcher.partiallyExitViewport(function()
			{
				var left = $nav_env.offset().left,
					width = $nav_env.width();
				
				$nav_env.addClass('is-fixed').css({
					width: 'auto'
				});
				$spacer.height($nav_env.outerHeight());
			});
			
			navWatcher.fullyEnterViewport(function()
			{
				$nav_env.removeClass('is-fixed');
				$spacer.removeAttr('style');
			});
		}
	}
	// END: Sticky Menu for Menu Type 3 - centered
}