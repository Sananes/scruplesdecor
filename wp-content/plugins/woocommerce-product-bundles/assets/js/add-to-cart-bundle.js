/* jshint -W069 */
/* jshint -W041 */

/**
 * Bundle scripts, accessible to the outside world
 */

var wc_pb_bundle_scripts = {};

jQuery( document ).ready( function($) {

	$( 'body' ).on( 'quick-view-displayed', function() {

		$( '.bundle_form .bundle_data' ).each( function() {
			$(this).wc_pb_bundle_form();
		} );
	} );

	$.fn.wc_pb_bundle_form = function() {

		if ( ! $(this).hasClass( 'bundle_data' ) ) {
			return true;
		}

		var bundle_data  = $(this);
		var container_id = $(this).data( 'bundle_id' );

		if ( typeof( container_id ) === 'undefined' ) {
			container_id = $(this).attr( 'data-bundle-id' );

			if ( container_id ) {
				$(this).data( 'bundle_id', container_id );
			} else {
				return false;
			}
		}

		var bundle_form = bundle_data.closest( '.bundle_form' );

		if ( typeof( wc_pb_bundle_scripts[ container_id ] ) !== 'undefined' ) {
			bundle_form.find( '*' ).off();
		}

		wc_pb_bundle_scripts[ container_id ] = {

			$bundle_data:                    bundle_data,
			$bundle_form:                    bundle_form,
			$bundle_wrap:                    bundle_data.find( '.bundle_wrap' ),
			$bundled_items:                  bundle_form.find( '.bundled_product' ),

			$bundle_price:                   bundle_data.find( '.bundle_price' ),
			$bundle_error:                   bundle_data.find( '.bundle_error' ),
			$bundle_error_content:           bundle_data.find( '.bundle_error .msg' ),
			$bundle_button:                  bundle_data.find( '.bundle_button' ),

			bundled_items:                   {},

			$initial_stock_status:           false,

			update_bundle_lock:              false,

			init: function() {

				/**
				 * Bind bundle event handlers
				 */

				this.bind_event_handlers();

				/**
				 * Init bundled items
				 */

				this.init_bundled_items();


				/**
				 * Initial states and loading
				 */

				var bundle        = this;
				var bundle_data   = bundle.$bundle_data;

				// Ensure error div exists (template back-compat)
				if ( bundle.$bundle_error_content.length == 0 ) {
					if ( bundle.$bundle_error.length > 0 ) {
						bundle.$bundle_error.remove();
					}
					bundle.$bundle_price.after( '<div class="bundle_error" style="display:none"><div class="msg woocommerce-info></div></div>' );
					bundle.$bundle_error         = bundle_data.find( '.bundle_error' );
					bundle.$bundle_error_content = bundle.$bundle_error.find( '.msg' );
				}

				// Add-ons support - move totals container
				var addons_totals = bundle_data.find( '#product-addons-total' );
				bundle.$bundle_price.after( addons_totals );

				if ( bundle.$bundle_wrap.find( 'p.stock' ).length > 0 ) {
					bundle.$initial_stock_status = bundle.$bundle_wrap.find( 'p.stock' ).clone();
				}

				// Init variations and addons scripts
				$.each( bundle.bundled_items, function( index, bundled_item ) {
					bundled_item.init_scripts();
				} );

				bundle.update_bundle();

			},

			/**
			 * Attach bundle-level event handlers
			 */

			bind_event_handlers: function() {

				var bundle = this;

				this.$bundle_data

					.on( 'woocommerce-nyp-updated-item', function( event ) {

						var item = $(this);
						var nyp  = item.find( '.nyp' );

						if ( nyp.is( ':visible' ) ) {

							var bundle_price_data = bundle.$bundle_data.data( 'bundle_price_data' );

							bundle_price_data[ 'total' ] = nyp.data( 'price' );

							bundle.update_bundle();
						}

						event.stopPropagation();
					} )

					.on( 'change', '.bundle_button input.qty', function( event ) {

						bundle.update_bundle();
					} );
			},

			/**
			 * Initialize bundled item objects
			 */

			init_bundled_items: function() {

				var bundle = this;

				bundle.$bundled_items.each( function( index ) {

					bundle.bundled_items[ index ] = new WC_PB_Bundled_Item( bundle, $(this), index );

					bundle.bind_bundled_item_event_handlers( bundle.bundled_items[ index ] );

				} );

			},

			/**
			 * Attach bundled-item-level event handlers
			 */

			bind_bundled_item_event_handlers: function( bundled_item ) {

				var bundle = this;

				bundled_item.$self

					/**
					 * Update totals upon changing quantities
					 */
					.on( 'change', 'input.bundled_qty', function( event ) {

						var min  = parseFloat( $(this).attr( 'min' ) );
						var max  = parseFloat( $(this).attr( 'max' ) );

						if ( min >= 0 && parseFloat( $(this).val() ) < min ) {
							$(this).val( min );
						}

						if ( max > 0 && parseFloat( $(this).val() ) > max ) {
							$(this).val( max );
						}

						bundle.update_bundle();
					} )

					.on( 'change', '.bundled_product_optional_checkbox input', function( event ) {

						if ( $(this).is( ':checked' ) ) {

							bundled_item.$bundled_item_content.slideDown( 200 );
							bundled_item.set_selected( true );

							// Allow variations script to flip images in bundled_product_images div
							bundled_item.$self.find( '.variations_form .variations select:eq(0)' ).trigger( 'change' );

						} else {

							bundled_item.$bundled_item_content.slideUp( 200 );
							bundled_item.set_selected( false );

							// Reset image in bundled_product_images div
							bundled_item.$bundled_item_image.addClass( 'images' );
							bundled_item.$self.find( '.variations_form' ).trigger( 'reset_image' );
							bundled_item.$bundled_item_image.removeClass( 'images' );
						}

						bundle.update_bundle();

						event.stopPropagation();
					} )

					.on( 'found_variation', function( event, variation ) {

						var variations        = $(this).find( '.variations_form' );
						var bundled_item_id   = bundled_item.bundled_item_id;
						var bundle_price_data = bundle.$bundle_data.data( 'bundle_price_data' );

						if ( bundle_price_data[ 'per_product_pricing' ] == true ) {
							// put variation price in price table
							bundle_price_data[ 'prices' ][ bundled_item_id ]         = variation.price;
							bundle_price_data[ 'regular_prices' ][ bundled_item_id ] = variation.regular_price;
						}

						bundle.$bundle_wrap.find( 'input.bundle_variation_id_' + bundled_item_id ).val( variation.variation_id ).change();

						for ( var attribute in variation.attributes ) {
							var escaped_attr = attribute.replace( /%/g,'\\%' );
							bundle.$bundle_wrap.find( 'input.bundle_' + escaped_attr + '_' + bundled_item_id ).val( variations.find( 'select[name="' + escaped_attr + '"]' ).val() );
						}

						// Remove .images class from bundled_product_images div in order to avoid styling issues
						bundled_item.$bundled_item_image.removeClass( 'images' );

						bundle.update_bundle();

						event.stopPropagation();
					} )

					.on( 'reset_image', function( event ) {

						// Remove .images class from bundled_product_images div in order to avoid styling issues
						bundled_item.$bundled_item_image.removeClass( 'images' );

					} )

					.on( 'woocommerce-product-addons-update', function( event ) {

						event.stopPropagation();
					} )

					.on( 'woocommerce_variation_select_focusin', function( event ) {

						event.stopPropagation();
					} )

					.on( 'woocommerce_variation_select_change', function( event ) {

						var variations = $(this).find( '.variations_form' );

						// Add .images class to bundled_product_images div ( required by the variations script to flip images )
						if ( bundled_item.is_selected() ) {
							bundled_item.$bundled_item_image.addClass( 'images' );
						}

						$(this).find( '.variations .attribute-options select' ).each( function() {

							if ( $(this).val() === '' ) {
								variations.find( '.bundled_item_wrap .stock' ).addClass( 'disabled' );
								bundle.update_bundle();
								return false;
							}
						} );

						event.stopPropagation();
					} );


				bundled_item.$bundled_item_cart

					.on( 'woocommerce-product-addons-update', function( event ) {

						bundle.update_bundle( true );

						event.stopPropagation();
					} )

					.on( 'woocommerce-nyp-updated-item', function( event ) {

						var item_id = bundled_item.bundled_item_id;
						var nyp     = $(this).find( '.nyp' );

						if ( nyp.is( ':visible' ) ) {

							var bundle_price_data = bundle.$bundle_data.data( 'bundle_price_data' );

							bundle_price_data[ 'prices' ][ item_id ] = nyp.data( 'price' );

							bundle.update_bundle();
						}

						event.stopPropagation();
					} );

			},

			/**
			 * Schedules an update of the bundle totals
			 * Uses a dumb scheduler to avoid queueing multiple calls of wc_pb_update_bundle_task - the "scheduler" simply introduces a 50msec execution delay during which all update requests are dropped
			 */
			update_bundle: function( update_only ) {

				var bundle = this;

				// Dumb task scheduler
				if ( bundle.update_bundle_lock === true ) {
					return false;
				}

				bundle.update_bundle_lock = true;

				window.setTimeout( function() {

					bundle.update_bundle_task( update_only );
					bundle.update_bundle_lock = false;

				}, 20 );
			},

			update_bundle_task: function( update_only ) {

				var bundle                   = this;
				var form                     = bundle.$bundle_form;

				var out_of_stock_found       = false;
				var $overridden_stock_status = false;

				if ( typeof( update_only ) === 'undefined' ) {
					update_only = false;
				}

				var all_set = true;

				var addons_prices           = {};
				var bundled_item_quantities = {};

				var bundle_price_data = bundle.$bundle_data.data( 'bundle_price_data' );

				// Validate bundle

				$.each( bundle.bundled_items, function( index, bundled_item ) {

					var cart            = bundled_item.$bundled_item_cart;
					var bundled_item_id = bundled_item.bundled_item_id;
					var item_quantity   = cart.find( 'input.qty' ).val();

					// Set quantity based on optional flag
					if ( ! bundled_item.is_selected() ) {
						bundled_item_quantities[ bundled_item_id ] = 0;
					} else {
						bundled_item_quantities[ bundled_item_id ] = item_quantity;
					}

					// Check variable products
					if ( bundled_item.product_type === 'variable' && cart.find( '.bundled_item_wrap input[name="variation_id"]' ).val() == '' ) {

						bundle.$bundle_wrap.find( 'input.bundle_variation_id_' + bundled_item_id ).val( '' );
						bundle.$bundle_wrap.find( 'input.bundle_attribute_' + bundled_item_id ).val( '' );

						bundle_price_data[ 'prices' ][ bundled_item_id ]         = 0;
						bundle_price_data[ 'regular_prices' ][ bundled_item_id ] = 0;

						if ( bundled_item.is_selected() ) {
							all_set = false;
							return false;
						}
					}

				} );

				if ( all_set ) {

					var bundle_quantity = parseInt( bundle.$bundle_button.find( 'input.qty' ).val() );

					$.each( bundle.bundled_items, function( index, bundled_item ) {

						var item    = bundled_item.$bundled_item_cart;
						var item_id = bundled_item.bundled_item_id;

						// Save addons prices
						addons_prices[ item_id ] = 0;

						item.find( '.addon' ).each(function() {
							var addon_cost = 0;

							if ( $(this).is('.addon-custom-price') ) {
								addon_cost = $(this).val();
							} else if ( $(this).is('.addon-input_multiplier') ) {
								if( isNaN( $(this).val() ) || $(this).val() == '' ) { // Number inputs return blank when invalid
									$(this).val('');
									$(this).closest('p').find('.addon-alert').show();
								} else {
									if( $(this).val() != '' ){
										$(this).val( Math.ceil( $(this).val() ) );
									}
									$(this).closest('p').find('.addon-alert').hide();
								}
								addon_cost = $(this).data('price') * $(this).val();
							} else if ( $(this).is('.addon-checkbox, .addon-radio') ) {
								if ( $(this).is(':checked') )
									addon_cost = $(this).data('price');
							} else if ( $(this).is('.addon-select') ) {
								if ( $(this).val() )
									addon_cost = $(this).find('option:selected').data('price');
							} else {
								if ( $(this).val() )
									addon_cost = $(this).data('price');
							}

							if ( ! addon_cost )
								addon_cost = 0;

							addons_prices[ item_id ] = parseFloat( addons_prices[ item_id ] ) + parseFloat( addon_cost );

						} );

						// Store quantity for easy access by 3rd parties
						item.data( 'quantity', bundled_item_quantities[ item_id ] );
					} );

					// Unavailable when priced statically and price is undefined
					if ( ( bundle_price_data[ 'per_product_pricing' ] == false ) && ( bundle_price_data[ 'total' ] === '' ) ) {

						bundle.hide_bundle( wc_bundle_params.i18n_unavailable_text );
						return;
					}

					if ( bundle_price_data[ 'per_product_pricing' ] == true ) {

						bundle_price_data[ 'total' ]         = 0;
						bundle_price_data[ 'regular_total' ] = 0;

						for ( var item_id_ppp in bundle_price_data[ 'prices' ] ) {

							bundle_price_data[ 'total' ]         += ( parseFloat( bundle_price_data[ 'prices' ][ item_id_ppp ] ) + parseFloat( addons_prices[ item_id_ppp ] ) ) * bundled_item_quantities[ item_id_ppp ];
							bundle_price_data[ 'regular_total' ] += ( parseFloat( bundle_price_data[ 'regular_prices' ][ item_id_ppp ] ) + parseFloat( addons_prices[ item_id_ppp ] ) ) * bundled_item_quantities[ item_id_ppp ];
						}

					} else {

						bundle_price_data[ 'total_backup' ]         = parseFloat( bundle_price_data[ 'total' ] );
						bundle_price_data[ 'regular_total_backup' ] = parseFloat( bundle_price_data[ 'regular_total' ] );

						for ( var item_id_sp in addons_prices ) {

							bundle_price_data[ 'total' ]         += parseFloat( addons_prices[ item_id_sp ] ) * bundled_item_quantities[ item_id_sp ];
							bundle_price_data[ 'regular_total' ] += parseFloat( addons_prices[ item_id_sp ] ) * bundled_item_quantities[ item_id_sp ];
						}
					}

					var bundle_addon = bundle.$bundle_data.find( '#product-addons-total' );

					if ( bundle_addon.length > 0 ) {
						bundle_addon.data( 'price', bundle_price_data[ 'total' ] );
						bundle.$bundle_data.trigger( 'woocommerce-product-addons-update' );
					}

					var per_product_priced_composite = false;

					if ( bundle_price_data[ 'bundle_is_composited' ] === true ) {

						var composite_price_data     = bundle.$bundle_form.closest( '.composite_form' ).find( '.composite_data' ).data( 'price_data' );
						per_product_priced_composite = composite_price_data[ 'per_product_pricing' ];

						wc_bundle_params.i18n_total = wc_bundle_params.i18n_subtotal;
					}


					if ( bundle_price_data[ 'per_product_pricing' ] == true || ( bundle_price_data[ 'bundle_is_composited' ] == true && per_product_priced_composite == true ) ) {

						if ( bundle_price_data[ 'total' ] == 0 && bundle_price_data[ 'show_free_string' ] == true ) {

							bundle.$bundle_price.html( '<p class="price"><span class="total">' + wc_bundle_params.i18n_total + '</span>' + wc_bundle_params.i18n_free + '</p>' );

						} else {

							var sales_price_format   = wc_pb_woocommerce_number_format( wc_pb_number_format( bundle_quantity * bundle_price_data[ 'total' ] ) );
							var regular_price_format = wc_pb_woocommerce_number_format( wc_pb_number_format( bundle_quantity * bundle_price_data[ 'regular_total' ] ) );

							if ( bundle_price_data[ 'regular_total' ] > bundle_price_data[ 'total' ] ) {
								bundle.$bundle_price.html( '<p class="price">' + bundle_price_data[ 'price_string' ].replace( '%s', '<span class="total">' + wc_bundle_params.i18n_total + '</span><del>' + regular_price_format + '</del> <ins>' + sales_price_format + '</ins>' ) + '</p>' );
							} else {
								bundle.$bundle_price.html( '<p class="price">' + bundle_price_data[ 'price_string' ].replace( '%s', '<span class="total">' + wc_bundle_params.i18n_total + '</span>' + sales_price_format ) + '</p>' );
							}

							// Display recurring price html data for optional bundled subs
							var bundled_subs = bundle.get_bundled_subscriptions();

							if ( bundled_subs ) {
								$.each( bundled_subs, function( index, bundled_sub ) {

									var bundled_item_id = bundled_sub.bundled_item_id;

									if ( bundled_item_quantities[ bundled_item_id ] > 0 ) {
										bundle.$bundle_price.find( '.bundled_subscriptions_price_html' ).show();
										bundle.$bundle_price.find( '.bundled_sub_price_html_' + bundled_item_id ).show().addClass( 'visible' );
									}
								} );

								bundle.$bundle_price.find( '.bundled_subscriptions_price_html .bundled_sub_price_html.visible' ).first().find( '.plus' ).hide();
							}
						}

					} else {

						bundle.$bundle_price.html( '' );
					}

					// Check if any item is out of stock
					$.each( bundle.bundled_items, function( index, bundled_item ) {

						if ( ! bundled_item.is_selected() ) {
							return true;
						}

						var $item_stock_p = bundled_item.$bundled_item_cart.find( 'p.stock:not(.disabled)' );

						if ( $item_stock_p.hasClass( 'out-of-stock' ) ) {
							out_of_stock_found = true;
						}

					} );

					if ( out_of_stock_found ) {
						bundle.$bundle_button.find( 'button' ).prop( 'disabled', true ).addClass( 'disabled' );
					} else {
						bundle.$bundle_button.find( 'button' ).prop( 'disabled', false ).removeClass( 'disabled' );
					}

					// Show price and add-to-cart button

					var button_behaviour = bundle.$bundle_data.data( 'button_behaviour' );

					if ( button_behaviour !== 'new' ) {
						bundle.$bundle_wrap.slideDown( 200 );
					} else {
						bundle.$bundle_price.slideDown( 200 );
						bundle.$bundle_error.slideUp( 200 );
					}

					bundle.$bundle_wrap.trigger( 'woocommerce-product-bundle-show' );

					// Composite product compatibility - Save price
					form.find( '.component_data' ).data( 'price', bundle_price_data[ 'total' ] );
					form.find( '.component_data' ).data( 'regular_price', bundle_price_data[ 'regular_total' ] );

					// Composite product compatibility - Save state
					form.find( '.component_data' ).data( 'component_set', true );

					// Restore initial values
					bundle_price_data[ 'total' ]         = bundle_price_data[ 'total_backup' ];
					bundle_price_data[ 'regular_total' ] = bundle_price_data[ 'regular_total_backup' ];

				} else {

					if ( ! update_only ) {
						bundle.hide_bundle();
					}
				}

				// Override bundle availability

				$.each( bundle.bundled_items, function( index, bundled_item ) {

					if ( ! bundled_item.is_selected() ) {
						return true;
					}

					var $item_stock_p = bundled_item.$bundled_item_cart.find( 'p.stock:not(.disabled)' );

					if ( $item_stock_p.hasClass( 'out-of-stock' ) ) {
						$overridden_stock_status = $item_stock_p.clone().html( wc_bundle_params.i18n_partially_out_of_stock );
					}

					if ( $item_stock_p.hasClass( 'available-on-backorder' ) && ! out_of_stock_found ) {
						$overridden_stock_status = $item_stock_p.clone().html( wc_bundle_params.i18n_partially_on_backorder );
					}

				} );

				var $current_stock_status = bundle.$bundle_wrap.find( 'p.stock' );

				if ( $overridden_stock_status ) {
					if ( $current_stock_status.length > 0 ) {
						if ( $current_stock_status.hasClass( 'inactive' ) ) {
							$current_stock_status.replaceWith( $overridden_stock_status.hide() );
							$overridden_stock_status.slideDown( 200 );
						} else {
							$current_stock_status.replaceWith( $overridden_stock_status );
						}
					} else {
						bundle.$bundle_button.before( $overridden_stock_status.hide() );
						$overridden_stock_status.slideDown( 200 );
					}
				} else {
					if ( bundle.$initial_stock_status ) {
						$current_stock_status.replaceWith( bundle.$initial_stock_status );
					} else {
						$current_stock_status.addClass( 'inactive' ).slideUp( 200 );
					}
				}

				bundle.$bundle_wrap.trigger( 'woocommerce-composited-product-update' );

			},

			hide_bundle: function( hide_message ) {

				var bundle = this;
				var form   = bundle.$bundle_form;

				if ( typeof( hide_message ) === 'undefined' ) {
					hide_message = wc_bundle_params.i18n_select_options;
				}

				// Composite products compatibility
				form.find( '.component_data' ).data( 'component_set', false );

				var button_behaviour = bundle.$bundle_data.data( 'button_behaviour' );

				if ( button_behaviour === 'new' ) {

					bundle.$bundle_price.slideUp( 200 );
					bundle.$bundle_error_content.html( hide_message );
					bundle.$bundle_error.slideDown( 200 );
					bundle.$bundle_button.find( 'button' ).prop( 'disabled', true ).addClass( 'disabled' );

				} else {

					bundle.$bundle_wrap.slideUp( 200 );
				}

				bundle.$bundle_wrap.trigger( 'woocommerce-product-bundle-hide' );

			},

			get_bundled_subscriptions: function() {

				var bundle       = this;

				var bundled_subs = {};
				var has_sub      = false;

				$.each( bundle.bundled_items, function( index, bundled_item ) {

					if ( bundled_item.product_type === 'subscription' ) {

						bundled_subs[ index ] = bundled_item;
						has_sub        = true;
					}

				} );

				if ( has_sub ) {
					return bundled_subs;
				}

				return false;
			}

		};

		wc_pb_bundle_scripts[ container_id ].init();
	};

	 /*
     * Bundled item class
     */

	function WC_PB_Bundled_Item( bundle, $bundled_item, index ) {

		this.$self                     = $bundled_item;
		this.$bundled_item_cart        = $bundled_item.find( '.cart' );
		this.$bundled_item_content     = $bundled_item.find( '.bundled_item_optional_content, .bundled_item_cart_content' );
		this.$bundled_item_image       = $bundled_item.find( '.bundled_product_images' );

		this.bundled_item_index        = index;
		this.bundled_item_id           = this.$bundled_item_cart.data( 'bundled_item_id' );
		this.bundled_item_title        = this.$bundled_item_cart.data( 'title' );

		this.product_type              = this.$bundled_item_cart.data( 'type' );

		if ( typeof( this.bundled_item_id ) === 'undefined' ) {
			this.bundled_item_id = this.$bundled_item_cart.attr( 'data-bundled-item-id' );
		}

		this.is_optional = function() {

			return this.$bundled_item_cart.data( 'optional' );
		};

		this.is_selected = function() {

			var selected = true;

			if ( this.$bundled_item_cart.data( 'optional' ) == true ) {
				if ( this.$bundled_item_cart.data( 'optional_status' ) == false ) {
					selected = false;
				}
			}

			return selected;
		};

		this.set_selected = function( status ) {

			if ( this.is_optional() ) {
				this.$bundled_item_cart.data( 'optional_status', status );
			}
		};

		this.init_scripts = function() {

			this.$self.find( '.bundled_product_optional_checkbox input' ).change();

			if ( this.product_type === 'variable' && ! this.$bundled_item_cart.hasClass( 'variations_form' ) ) {

				// Initialize variations script
				this.$bundled_item_cart.addClass( 'variations_form' ).wc_variation_form();
				this.$bundled_item_cart.find( '.variations select:eq(0)' ).change();
			}

		};
	}

	/**
	 * Helper functions
	 */

	function wc_pb_update_bundle( form, update_only ) {

		var bundle_data  = form.find( '.bundle_data' );
		var container_id = bundle_data.data( 'bundle_id' );

		if ( typeof( wc_pb_bundle_scripts[ container_id ] ) !== 'undefined' ) {
			var bundle = wc_pb_bundle_scripts[ container_id ];

			return bundle.update_bundle( update_only );
		}

		return false;
	}

	function wc_pb_update_bundle_task( form, update_only ) {

		var bundle_data  = form.find( '.bundle_data' );
		var container_id = bundle_data.data( 'bundle_id' );

		if ( typeof( wc_pb_bundle_scripts[ container_id ] ) !== 'undefined' ) {
			var bundle = wc_pb_bundle_scripts[ container_id ];

			return bundle.update_bundle_task( update_only );
		}

		return false;
	}

	function wc_pb_hide_bundle( form, update_only ) {

		var bundle_data  = form.find( '.bundle_data' );
		var container_id = bundle_data.data( 'bundle_id' );

		if ( typeof( wc_pb_bundle_scripts[ container_id ] ) !== 'undefined' ) {
			var bundle = wc_pb_bundle_scripts[ container_id ];

			return bundle.hide_bundle( update_only );
		}

		return false;
	}

	function wc_pb_attempt_show_bundle( form, update_only ) {

		wc_pb_update_bundle( form, update_only );
	}

	function wc_pb_woocommerce_number_format( price ) {

		var remove     = wc_bundle_params.currency_format_decimal_sep;
		var position   = wc_bundle_params.currency_position;
		var symbol     = wc_bundle_params.currency_symbol;
		var trim_zeros = wc_bundle_params.currency_format_trim_zeros;
		var decimals   = wc_bundle_params.currency_format_num_decimals;

		if ( trim_zeros == 'yes' && decimals > 0 ) {
			for (var i = 0; i < decimals; i++) { remove = remove + '0'; }
			price = price.replace( remove, '' );
		}

		var price_format = '';

		if ( position == 'left' )
			price_format = '<span class="amount">' + symbol + price + '</span>';
		else if ( position == 'right' )
			price_format = '<span class="amount">' + price + symbol +  '</span>';
		else if ( position == 'left_space' )
			price_format = '<span class="amount">' + symbol + ' ' + price + '</span>';
		else if ( position == 'right_space' )
			price_format = '<span class="amount">' + price + ' ' + symbol +  '</span>';

		return price_format;
	}

	function wc_pb_number_format( number ) {

		var decimals      = wc_bundle_params.currency_format_num_decimals;
		var decimal_sep   = wc_bundle_params.currency_format_decimal_sep;
		var thousands_sep = wc_bundle_params.currency_format_thousand_sep;

	    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
	    var d = decimal_sep == undefined ? ',' : decimal_sep;
	    var t = thousands_sep == undefined ? '.' : thousands_sep, s = n < 0 ? '-' : '';
	    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + '', j = (j = i.length) > 3 ? j % 3 : 0;

	    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
	}


	$( '.bundle_form .bundle_data' ).each( function() {

		$(this).wc_pb_bundle_form();

	} );

} );
