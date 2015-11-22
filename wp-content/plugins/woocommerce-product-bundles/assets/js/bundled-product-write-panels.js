jQuery( function($) {

	function wc_bundles_getEnhancedSelectFormatString() {
		var formatString = {
			formatMatches: function( matches ) {
				if ( 1 === matches ) {
					return wc_bundles_admin_params.i18n_matches_1;
				}

				return wc_bundles_admin_params.i18n_matches_n.replace( '%qty%', matches );
			},
			formatNoMatches: function() {
				return wc_bundles_admin_params.i18n_no_matches;
			},
			formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
				return wc_bundles_admin_params.i18n_ajax_error;
			},
			formatInputTooShort: function( input, min ) {
				var number = min - input.length;

				if ( 1 === number ) {
					return wc_bundles_admin_params.i18n_input_too_short_1;
				}

				return wc_bundles_admin_params.i18n_input_too_short_n.replace( '%qty%', number );
			},
			formatInputTooLong: function( input, max ) {
				var number = input.length - max;

				if ( 1 === number ) {
					return wc_bundles_admin_params.i18n_input_too_long_1;
				}

				return wc_bundles_admin_params.i18n_input_too_long_n.replace( '%qty%', number );
			},
			formatSelectionTooBig: function( limit ) {
				if ( 1 === limit ) {
					return wc_bundles_admin_params.i18n_selection_too_long_1;
				}

				return wc_bundles_admin_params.i18n_selection_too_long_n.replace( '%qty%', limit );
			},
			formatLoadMore: function( pageNumber ) {
				return wc_bundles_admin_params.i18n_load_more;
			},
			formatSearching: function() {
				return wc_bundles_admin_params.i18n_searching;
			}
		};

		return formatString;
	}

	$.fn.wc_bundles_select2 = function() {

		$(this).find( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = $.extend({
				minimumResultsForSearch: 10,
				allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
				placeholder: $( this ).data( 'placeholder' )
			}, wc_bundles_getEnhancedSelectFormatString() );

			$(this).select2( select2_args ).addClass( 'enhanced' );
		} );
	};

	// bundle type move stock msg up
	$( '.bundle_stock_msg' ).insertBefore( '._manage_stock_field' );

	// bundle type specific options
	$( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val, select ) {

		if ( select_val === 'bundle' ) {

			$( 'input#_downloadable' ).prop( 'checked', false );
			$( 'input#_virtual' ).removeAttr( 'checked' );

			$( '.show_if_simple' ).show();
			$( '.show_if_external' ).hide();

			$( 'input#_downloadable' ).closest( '.show_if_simple' ).hide();
			$( 'input#_virtual').closest('.show_if_simple' ).hide();

			$( 'input#_manage_stock' ).change();
			$( 'input#_per_product_pricing_active' ).change();
			$( 'input#_per_product_shipping_active' ).change();

			$( '#_nyp' ).change();
		}

	} );

	$( 'select#product-type' ).change();

	// non-bundled shipping
	$( 'input#_per_product_shipping_active' ).change( function() {

		if ( $( 'select#product-type' ).val() === 'bundle' ) {

			if ( $( 'input#_per_product_shipping_active' ).is( ':checked' ) ) {
				$( '.show_if_virtual' ).show();
				$( '.hide_if_virtual' ).hide();
				if ( $( '.shipping_tab' ).hasClass( 'active' ) )
					$( 'ul.product_data_tabs li:visible' ).eq(0).find('a').click();
			} else {
				$( '.show_if_virtual' ).hide();
				$( '.hide_if_virtual' ).show();
			}
		}

	} ).change();

	// show options if pricing is static
	$( 'input#_per_product_pricing_active' ).change( function() {

		if ( $( 'select#product-type' ).val() === 'bundle' ) {

			if ( $(this).is( ':checked' ) ) {

		        $( '#_regular_price' ).val('');
		        $( '#_sale_price' ).val('');

				$( '._tax_class_field' ).closest( '.options_group' ).hide();
				$('.pricing').hide();

				$( '#bundled_product_data .wc-bundled-item .item-data .discount input.bundle_discount' ).each( function() {
					$(this).attr( 'disabled', false );
				} );

			} else {

				$( '._tax_class_field' ).closest( '.options_group' ).show();

				if ( ! $( '#_nyp' ).is( ':checked' ) )
					$( '.pricing' ).show();

				$( '#bundled_product_data .wc-bundled-item .item-data .discount input.bundle_discount' ).each( function() {
					$(this).attr( 'disabled', 'disabled' );
				} );
			}
		}

	} ).change();

	// nyp support
	$( '#_nyp' ).change( function() {

		if ( $( 'select#product-type' ).val() === 'bundle' ) {

			if ( $( '#_nyp' ).is( ':checked' ) ) {
				$( 'input#_per_product_pricing_active' ).prop( 'checked', false );
				$( '.bundle_pricing' ).hide();
			} else {
				$( '.bundle_pricing' ).show();
			}

			$( 'input#_per_product_pricing_active' ).change();
		}

	} ).change();

	init_wc_bundle_metaboxes();

	function bundle_row_indexes() {
		$( '.wc-bundled-items .wc-bundled-item' ).each( function( index, el ) {
			$( '.bundled_item_position', el ).val( parseInt( $(el).index( '.wc-bundled-items .wc-bundled-item' ) ) );
		} );
	}

	function init_wc_bundle_metaboxes() {

		$( '.wc-bundled-items' )

		// variation filtering options
		.on( 'change', '.filter_variations input', function() {
			if ( $(this).is( ':checked' ) )
				$(this).closest( 'div.item-data' ).find( 'div.bundle_variation_filters' ).show();
			else
				$(this).closest( 'div.item-data' ).find( 'div.bundle_variation_filters' ).hide();
		} )

		// selection defaults options
		.on( 'change', '.override_defaults input', function() {
			if ( $(this).is( ':checked' ) )
				$(this).closest( 'div.item-data' ).find( 'div.bundle_selection_defaults' ).show();
			else
				$(this).closest( 'div.item-data' ).find( 'div.bundle_selection_defaults' ).hide();
		} )

		// custom title options
		.on( 'change', '.override_title input', function() {
			if ( $(this).is( ':checked' ) )
				$(this).closest( 'div.item-data' ).find( 'div.custom_title' ).show();
			else
				$(this).closest( 'div.item-data' ).find( 'div.custom_title' ).hide();
		} )

		// custom description options
		.on( 'change', '.override_description input', function() {
			if ( $(this).is( ':checked' ) )
				$(this).closest( 'div.item-data' ).find( 'div.custom_description' ).show();
			else
				$(this).closest( 'div.item-data' ).find( 'div.custom_description' ).hide();
		} )

		// visibility
		.on( 'change', '.item_visibility select', function() {

			if ( $(this).val() == 'visible' ) {
				$(this).closest( 'div.item-data' ).find( '.override_title, .override_description, .images' ).show();
				$(this).closest( 'div.item-data' ).find( '.override_title input' ).change();
				$(this).closest( 'div.item-data' ).find( '.override_description input' ).change();
			} else {
				$(this).closest( 'div.item-data' ).find( '.override_title, .custom_title, .override_description, .custom_description, .images' ).hide();
			}

		} );

		$( '.wc-bundled-items .filter_variations input' ).change();
		$( '.wc-bundled-items .override_defaults input' ).change();
		$( '.wc-bundled-items .override_title input' ).change();
		$( '.wc-bundled-items .override_description input' ).change();
		$( '.wc-bundled-items .item_visibility select' ).change();

		// Initial order
		var bundled_items = $( '.wc-bundled-items' ).find( '.wc-bundled-item' ).get();

		bundled_items.sort( function( a, b ) {
		   var compA = parseInt( $(a).attr( 'rel' ) );
		   var compB = parseInt( $(b).attr( 'rel' ) );
		   return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
		} );

		$(bundled_items).each( function( idx, itm ) {
			$( '.wc-bundled-items' ).append( itm );
		} );

		// Item ordering
		$( '.wc-bundled-items' ).sortable( {
			items:'.wc-bundled-item',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop:function(event,ui){
				ui.item.removeAttr( 'style' );
				bundle_row_indexes();
			}
		} );

		// Remove
		$( '#bundled_product_data .wc-bundle-metaboxes-wrapper' ).on( 'click', 'button.remove_row', function() {

			var $parent = $(this).closest( '.wc-bundled-item' );

			$parent.find('*').off();
			$parent.remove();
			bundle_row_indexes();

		} );

		// Expand & Close
		$( '#bundled_product_data .expand_all' ).click( function() {
			$(this).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .item-data' ).show();
			return false;
		} );

		$( '#bundled_product_data .close_all' ).click( function() {
			$(this).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .item-data').hide();
			return false;
		} );

	}

	// Add Product
	var bundle_metabox_count = $( '#bundled_product_data .wc-bundled-items .wc-bundled-item' ).size();
	var block_params         = {};

	if ( wc_bundles_admin_params.is_wc_version_gte_2_3 == 'yes' ) {
		block_params = {
			message: 	null,
			overlayCSS: {
				background: '#fff',
				opacity: 	0.6
			}
		};
	} else {
		block_params = {
			message: 	null,
			overlayCSS: {
				background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
				opacity: 	0.6
			}
		};
	}

	$( '#bundled_product_data' ).on( 'click', 'button.add_bundled_product', function() {

		var bundled_product_id = $( '#bundled_product_data #bundled_product' ).val();

		if ( ! bundled_product_id > 0 ) {

			if ( wc_bundles_admin_params.is_wc_version_gte_2_3 === 'yes' ) {
				$( '#bundled_product_data .bundled_product_selector .wc-product-search' ).select2( 'open' );
			} else {
				$( '#bundled_product_data .bundled_product_selector .ajax_chosen_select_products' ).trigger( 'chosen:open.chosen' );
			}

			return false;

		} else {
			if ( wc_bundles_admin_params.is_wc_version_gte_2_3 === 'yes' ) {
				$( '#bundled_product_data .bundled_product_selector .wc-product-search' ).select2( 'val', '' );
			}
		}

		$( '#bundled_product_data' ).block( block_params );

		bundle_metabox_count++;

		var data = {
			action: 	'woocommerce_add_bundled_product',
			post_id: 	woocommerce_admin_meta_boxes.post_id,
			id: 		bundle_metabox_count,
			product_id: bundled_product_id,
			security: 	wc_bundles_admin_params.add_bundled_product_nonce
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function ( response ) {

			if ( response.markup !== '' ) {

				$( '#bundled_product_data .wc-bundled-items' ).append( response.markup );

				var added = $( '#bundled_product_data .wc-bundled-items .wc-bundled-item' ).last();

				if ( wc_bundles_admin_params.is_wc_version_gte_2_3 == 'yes' ) {

					added.wc_bundles_select2();

				} else {

					added.find( '.chosen_select' ).chosen();
				}

				added.find( '.filter_variations input' ).change();
				added.find( '.override_defaults input' ).change();
				added.find( '.override_title input' ).change();
				added.find( '.override_description input' ).change();
				added.find( '.item_visibility select' ).change();

				added.find( '.help_tip' ).tipTip( {
					'attribute' : 'data-tip',
					'fadeIn' : 50,
					'fadeOut' : 50,
					'delay' : 200
				} );

				$( 'input#_per_product_pricing_active' ).change();

				$( '#bundled_product_data' ).trigger( 'wc-bundles-added-bundled-product' );

			} else if ( response.message !== '' ) {
				alert( response.message );
			}

			$( '#bundled_product_data' ).unblock();

		} );

		return false;

	} );

} );
