(function($) {
	
	'use strict';
	
	// Extend core script
	$.extend($.nmTheme, {
		
		/**
		 *	Initialize single product scripts
		 */
		singleProduct_init: function() {
			var self = this;
			
			self.zoomEnabled = false;
			
			self.singleProductVariationsInit();
			self.shopInitQuantity($('#nm-product-summary'));
			self.singleProductFeaturedVideoInit();
			self.singleProductGalleryInit();
			
			/* Star-rating: bind click event */
			var $ratingWrap = $('#nm-comment-form-rating');
			$ratingWrap.on('click.nmAddParentClass', '.stars a', function() {
				$ratingWrap.children('.stars').addClass('has-active');
            });
			
			/* Load related product images (init Unveil) */
			var $upsellsImages = $('#nm-upsells').find('.nm-shop-loop-thumbnail .unveil-image'),
				$relatedImages = $('#nm-related').find('.nm-shop-loop-thumbnail .unveil-image'),
				$images = $.merge($upsellsImages, $relatedImages);
			
			self.$window.load(function() {
				if ($images.length) {
					$images.unveil(1, function() {
						$(this).parents('li').first().addClass('image-loaded');
					});
				}
			});
		},
		
		
		/**
		 *	Single product: Variations
		 */
		singleProductVariationsInit: function() {
			var self = this;
			
			
			/* Variations: Elements */
			self.$variationsForm = $('#nm-variations-form');
			self.$variationsWrap = self.$variationsForm.children('.variations');
			
				
			/* Variations: Select boxes */
			self.$variationsWrap.find('select').selectOrDie(self.shopSelectConfig);
			
			
			/* Variation details: Init */
			self.shopToggleVariationDetails(); // Init
			self.$variationsForm.on('found_variation', function() { // Bind: WooCommerce "found_variation" event
				self.shopToggleVariationDetails();
			});
			
			
			/* Variations/Slider: Go to first slide when variation select changes */
			self.$variationsForm.on('woocommerce_variation_select_change', function() {
				if (self.$productImageSlider && self.$productImageSlider.length) {
					self.$productImageSlider.slick('slickGoTo', 0, false); // Args: (event, slideIndex, skipAnimation)
				}
				
				// Update zoom image (in case a variation image is used)
				if (self.zoomEnabled) {
					self.singleProductZoomUpdateImage();
				}
			});
		},
		
		
		/**
		 *	Single product: Update hover-zoom image
		 */
		singleProductZoomUpdateImage: function() {
			var self = this,
				$firstGalleryImage = self.$productImageSlider.find('.slick-slide').first(),
				firstGalleryImageUrl = $firstGalleryImage.children('a').attr('href'),
				zoomApi = $firstGalleryImage.data('easyZoom'); // Get the zoom plugin API for the first gallery image
			
			// Swap/update zoom image url
			zoomApi.swap(firstGalleryImageUrl);
		},
		
		
		/**
		 *	Single product: Featured video
		 */
		singleProductFeaturedVideoInit: function() {
			var self = this;
			
			self.hasFeaturedVideo = false;
			self.$featuredVideoBtn = $('#nm-featured-video-link');
			
			if (self.$featuredVideoBtn.length) {
				self.hasFeaturedVideo = true;
				
				// Bind: Featured video button */
				self.$featuredVideoBtn.bind('click', function(e) {
					e.preventDefault();
					
					// Open video modal
					self.$featuredVideoBtn.magnificPopup({
						mainClass: 'nm-featured-video-popup nm-mfp-fade-in',
						closeMarkup: '<a class="mfp-close nm-font nm-font-close2"></a>',
						removalDelay: 180,
						type: 'iframe',
						closeOnContentClick: true,
						closeBtnInside: false
					}).magnificPopup('open');
				});
			}
		},
		
		
		/**
		 *	Single product: Initialize gallery
		 */
		singleProductGalleryInit: function() {
			var self = this;
			
			/* Product gallery/slider */
			if ($('#nm-page-includes').hasClass('product-gallery')) {
				
				/* Slider: Elements */
				self.$productImageSlider = $('#nm-product-images-slider');
				
				var $productImagesColumn = $('#nm-product-images-col'),
					$productImages = self.$productImageSlider.children('div'),
					$productThumbSlider = $('#nm-product-thumbnails-slider'),
					$productThumbs = $productThumbSlider.children('div'),
					$activeThumb = $productThumbs.first(),
					numThumbs = $productThumbs.length,
					maxThumbs = 6,
					thumbsToShow = (numThumbs > maxThumbs) ? maxThumbs : numThumbs,
					animSpeed = 300,
					isThumbClick = false,
					modalEnabled = $productImagesColumn.hasClass('modal-enabled');
				
				self.zoomEnabled = (!self.isTouch && $productImagesColumn.hasClass('zoom-enabled'));
				
				// Is image hover-zoom enabled?
				if (self.zoomEnabled) {
					/* Slider: "init" event */
					self.$productImageSlider.on('init', function() {
						// Init hover zoom (EasyZoom plugin)
						$productImages.easyZoom();
					});
				}
				
				// Is modal gallery enabled?
				if (modalEnabled) {
					/* Slider: "init" event */
					self.$productImageSlider.on('init', function() {
						/* Bind: Product image wraps click event */
						$productImages.bind('click', function(e) {
							if (self.$productImageSlider.hasClass('animating')) { return; }
							e.preventDefault();
							
							var index = $(this).index(); // Clicked image-container index
							
							// Is there a featured video?
							if (self.hasFeaturedVideo) {
								// Should the first gallery item open the featured video modal?
								if (index == 0 && self.$featuredVideoBtn.hasClass('modal-override')) {
									// Open featured video modal
									self.$featuredVideoBtn.trigger('click');
								} else {
									// Open fullscreen gallery
									_openPhotoSwipe(this, index);
								}
							} else {
								// Open fullscreen gallery
								_openPhotoSwipe(this, index);
							}
						});
					});
				} else if (self.hasFeaturedVideo) {
					/* Slider: "init" event */
					self.$productImageSlider.on('init', function() {
						/* Bind: Product image wraps click event */
						$productImages.bind('click', function(e) {
							if (self.$productImageSlider.hasClass('animating')) { return; }
							e.preventDefault();
							
							// Open featured video modal
							self.$featuredVideoBtn.trigger('click');
						});
					});	
				}
				
				/* Slider: "beforeChange" event */
				self.$productImageSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
					// Only trigger thumbnail click if navigating the slider directly
					if (!isThumbClick) {
						//console.log('NM: Trigger - Thumb click');
						$productThumbSlider.find('.slick-slide').eq(nextSlide).trigger('click');
					}
					
					isThumbClick = false;
					
					self.$productImageSlider.addClass('animating');
				});
				
				/* Slider: "afterChange" event */
				self.$productImageSlider.on('afterChange', function() {
					self.$productImageSlider.removeClass('animating');
				});
				
				/* Slider: Init */
				self.$productImageSlider.slick({
					adaptiveHeight: true,
					slidesToShow: 1,
					slidesToScroll: 1,
					prevArrow: '<a class="slick-prev"><i class="nm-font nm-font-play flip"></i></a>',
					nextArrow: '<a class="slick-next"><i class="nm-font nm-font-play"></i></a>',
					dots: true,
					fade: true,
					cssEase: 'linear',
					infinite: false,
					speed: animSpeed
				});
				
				
				
				/* Thumbnails slider: "init" event */
				$productThumbSlider.on('init', function() {
					$productThumbs.bind('click', function() {
						var $this = $(this);
						
						if (self.$productImageSlider.hasClass('animating') || $this.hasClass('current')) {
							return;
						}
						
						//console.log('NM: Thumb click');
						
						isThumbClick = true;
						
						// Set active class
						$activeThumb.removeClass('current');
						$this.addClass('current');
						$activeThumb = $this;
						
						// Show prev/next thumbnail
						if (!$this.next().hasClass('slick-active')) {
							$productThumbSlider.slick('slickNext');
						} else if (!$this.prev().hasClass('slick-active')) {
							$productThumbSlider.slick('slickPrev');
						}
						
						// Change main image
						self.$productImageSlider.slick('slickGoTo', $this.index(), false); // (event, slideIndex, skipAnimation)
					});
				});
				
				/* Thumbnails slider: Init */
				$productThumbSlider.slick({
					slidesToShow: thumbsToShow,
					slidesToScroll: 1,
					arrows: false,
					infinite: false,
					focusOnSelect: false,
					vertical: true,
					draggable: false,
					speed: animSpeed,
					swipe: false,
					touchMove: false
				});
				
				
							
				/* Product fullscreen gallery (PhotoSwipe) */
				var _openPhotoSwipe = function(imageWrap, index) {
					// Create gallery images array
					var $this, $a, $img, items = [], size, item;
					$productImages.each(function() {
						$this = $(this);
						$a = $this.children('a');
						$img = $a.children('img');
						size = $a.data('size').split('x');
						
						// Create slide object
						item = {
							src: $a.attr('href'),
							w: parseInt(size[0], 10),
							h: parseInt(size[1], 10),
							msrc: $img.attr('src'),
							el: $a[0] // Save image link for use in 'getThumbBoundsFn()' below
						};
						
						items.push(item);
					});
					
					// Gallery options
					var options = {
						index: index,
						showHideOpacity: true,
						bgOpacity: 1, // Note: Setting this below "1" makes slide transition slow in Chrome (using "rgba" background instead)
						loop: false,
						mainClass: 'pswp--minimal--dark',
						// PhotoSwipeUI_Default:
						barsSize: { top: 0, bottom: 0 },
						captionEl: false,
						fullscreenEl: false,
						zoomEl: false,
						shareE1: false,
						counterEl: false,
						tapToClose: true,
						tapToToggleControls: false
					};
					
					var pswpElement = $('#pswp')[0];
					
					// Initialize and open gallery (PhotoSwipe)
					var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
					gallery.init();
					
					// Event: Opening zoom animation
					gallery.listen('initialZoomIn', function() {
						$productThumbSlider.slick('slickSetOption', 'speed', 0);
					});
					// Event: Before slides change
					var slide = index;
					gallery.listen('beforeChange', function(dirVal) {
						slide = slide + dirVal;
						self.$productImageSlider.slick('slickGoTo', slide, true); // Change active image in slider (event, slideIndex, skipAnimation)
					});
					// Event: Gallery starts closing
					gallery.listen('close', function() {
						$productThumbSlider.slick('slickSetOption', 'speed', animSpeed);
					});
				};
				
			}
		}
		
	});
	
	// Add extension so it can be called from $.nmThemeExtensions
	$.nmThemeExtensions.singleProduct = $.nmTheme.singleProduct_init;
	
})(jQuery);
