jQuery( document ).ready( function( $ ) {
	'use strict';

	var wcavi_original_gallery_images = $( wc_additional_variation_images_local.gallery_images_class ).html();
	var wcavi_original_main_images = $( wc_additional_variation_images_local.main_images_class ).html();

	// create namespace to avoid any possible conflicts
	$.wc_additional_variation_images_frontend = {
		isCloudZoom: function() {
			var cloudZoomClass = $( 'a.woocommerce-main-image' ).hasClass( 'cloud-zoom' );

			return cloudZoomClass;
		},

		runLightBox: function() {
			// user trigger
			$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_lightbox' );

			// if cloud zoom is active
			if ( $.wc_additional_variation_images_frontend.isCloudZoom() ) {

				$( '.cloud-zoom' ).each( function() {
					$( this ).data( 'zoom' ).destroy();
				});

				$( '.cloud-zoom, .cloud-zoom-gallery' ).CloudZoom();
			} else {

				if ( $.isFunction( $.fn.prettyPhoto ) ) {
					// lightbox
					$( '.product .images a.zoom' ).prettyPhoto({
						hook: 'data-rel',
						social_tools: false,
						theme: 'pp_woocommerce',
						horizontal_padding: 20,
						opacity: 0.8,
						deeplinking: false
					});
				}
			}
		},

		reset: function() {

			if ( wc_additional_variation_images_local.custom_reset_swap == true ) {
				var response = '';

				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_on_reset', [ response, wcavi_original_gallery_images, wcavi_original_main_images ] );
			} else {
				// replace the original gallery images
				$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
					$( this ).html( wcavi_original_gallery_images ).hide().fadeIn( 100, function() {
						$.wc_additional_variation_images_frontend.runLightBox();
					});
				});
			}

		},

		imageSwap: function( response ) {

			if ( wc_additional_variation_images_local.custom_swap == true ) {
				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_image_swap_callback', [ response, wcavi_original_gallery_images, wcavi_original_main_images ] );
			} else {

				$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
					$( this ).html( response.gallery_images ).hide().fadeIn( 100, function() {
						$.wc_additional_variation_images_frontend.runLightBox();
					});
				});
			}
		},

		imageSwapOriginal: function() {

			if ( wc_additional_variation_images_local.custom_original_swap == true ) {
				var response = '';

				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_default_image_swap_callback', [ response, wcavi_original_gallery_images, wcavi_original_main_images ] );
			} else {
				$( wc_additional_variation_images_local.gallery_images_class ).fadeOut( 50, function() {
					$( this ).html( wcavi_original_gallery_images ).hide().fadeIn( 100, function() {
						$.wc_additional_variation_images_frontend.runLightBox();
					});
				});
			}
		},

		neighborhoodTheme: function() {

			$( 'body' ).on( 'wc_additional_variation_images_frontend_image_swap_callback wc_additional_variation_images_frontend_ajax_default_image_swap_callback wc_additional_variation_images_frontend_on_reset', function( e, response, o_gallery_images, o_main_images ) {

				// remove items
				$( '#product-img-slider' ).remove();
				$( '#product-img-nav' ).remove();

				// add items back
				$( '.product .images' ).html( '<div id="product-img-slider" class="flexslider"><ul class="slides"></ul></div><div id="product-img-nav" class="flexslider"><ul class="slides"></ul></div>' );

				switch( e.type ) {
					case 'wc_additional_variation_images_frontend_image_swap_callback':
						$( '#product-img-slider ul.slides' ).html( response.main_images );
						
						$( '#product-img-nav ul.slides' ).html( response.gallery_images );

						var link = [];

						$( '#product-img-slider ul.slides a' ).each( function() {
							// get main image href
							link.push( $( this ).attr( 'href' ) );

							$( this ).find( 'img' ).removeClass().addClass( 'product-slider-image' );
							$( this ).replaceWith( '<li>' + this.innerHTML + '</li>' );
						});

						$( '#product-img-slider ul.slides li' ).each( function( i ) {
							$( this ).find( 'img' ).after( '<a href="' + link[i] + '" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" alt="" title=""><i class="fa-search-plus"></i></a>' );

							$( this ).find( 'img' ).attr( 'data-zoom-image', link[i] );
						});

						$( '#product-img-nav ul.slides a' ).each( function() {
							$( this ).find( 'img' ).removeClass();
							$( this ).replaceWith( '<li>' + this.innerHTML + '</li>' );
						});

						break;						
					case 'wc_additional_variation_images_frontend_ajax_default_image_swap_callback':
						$( '#product-img-slider ul.slides' ).html( o_main_images );
						
						$( '#product-img-nav ul.slides' ).html( o_gallery_images );

						break;	
					case 'wc_additional_variation_images_frontend_on_reset':
						$( '#product-img-slider ul.slides' ).html( o_main_images );
						
						$( '#product-img-nav ul.slides' ).html( o_gallery_images );

						break;	
				}

				SF.flexSlider.init();
				SF.lightbox.init();
				SF.woocommerce.variations();
			});
		},

		init: function() {

			// when variation changes trigger
			$( 'form.variations_form' ).on( 'show_variation', function( event, variation ) {
				$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_before_show_variation' );

				var $data = {
						action: 'wc_additional_variation_images_load_frontend_images_ajax',
						ajaxImageSwapNonce: wc_additional_variation_images_local.ajaxImageSwapNonce,
						variation_id: variation.variation_id,
						post_id: $( 'form.variations_form' ).data( 'product_id' )
					};

				$.post( wc_additional_variation_images_local.ajaxurl, $data, function( response ) {
					if ( response.length ) {
						response = $.parseJSON( response );

						$( 'form.variations_form' ).trigger( 'wc_additional_variation_images_frontend_ajax_response_callback', [ response, wcavi_original_gallery_images, wcavi_original_main_images ] );

						// replace with new image set
						$.wc_additional_variation_images_frontend.imageSwap( response );

					} else {

						// replace with original image set
						$.wc_additional_variation_images_frontend.imageSwapOriginal();
					}
				});	
			});

			// on reset click
			$( 'form.variations_form' ).on( 'click', '.reset_variations', function() {
				$.wc_additional_variation_images_frontend.reset();
			});

			// add support for swatches and photos plugin
			$( '#variations_clear' ).on( 'click', function() {
				$.wc_additional_variation_images_frontend.reset();
			});

			$( '.swatch-anchor' ).on( 'click', function() {
				var option = $( this ).parent( '.select-option' );

				if ( option.hasClass( 'selected' ) ) {
					$.wc_additional_variation_images_frontend.reset();
				}
			});

			// on reset select trigger
			$( 'form.variations_form' ).on( 'reset_image', function() {
				$.wc_additional_variation_images_frontend.reset();
			});
		}
	}; // close namespace

	$.wc_additional_variation_images_frontend.init();

	// maybe load neighborhood theme functions
	if ( typeof SF === 'object' && typeof SF.flexSlider.init === 'function' ) {
		$.wc_additional_variation_images_frontend.neighborhoodTheme();
	}
// end document ready
});	