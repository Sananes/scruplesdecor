<?php
/**
 * Product Bundle front-end functions and filters.
 *
 * @class   WC_PB_Display
 * @version 4.11.4
 * @since   4.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Display {

	private $enqueued_bundled_table_item_js = false;

	/**
	 * Setup class
	 */
	public function __construct() {

		// Single product template functions and hooks
		require_once( 'wc-pb-template-functions.php' );
		require_once( 'wc-pb-template-hooks.php' );

		// Filter add_to_cart_url & add_to_cart_text when product type is 'bundle'
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'woo_bundles_loop_add_to_cart_link' ), 10, 2 );

		// Add preamble info to bundled products
		add_filter( 'woocommerce_cart_item_name', array( $this, 'woo_bundles_in_cart_item_title' ), 10, 3 );
		add_filter( 'woocommerce_order_item_name', array( $this, 'woo_bundles_order_table_item_title' ), 10, 2 );

		// Change the tr class attributes when displaying bundled items in templates
		add_filter( 'woocommerce_cart_item_class', array( $this, 'woo_bundles_table_item_class' ), 10, 2 );
		add_filter( 'woocommerce_order_item_class', array( $this, 'woo_bundles_table_item_class' ), 10, 2 );

		// Front end variation select box jquery for multiple variable products
		add_action( 'wp_enqueue_scripts', array( $this, 'woo_bundles_frontend_scripts' ), 100 );

		// QuickView support
		add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'woo_bundles_qv' ) );

		// Filter cart item count
		add_filter( 'woocommerce_cart_contents_count',  array( $this, 'woo_bundles_cart_contents_count' ) );

		// Filter cart widget items
		add_filter( 'woocommerce_before_mini_cart', array( $this, 'woo_bundles_add_cart_widget_filters' ) );
		add_filter( 'woocommerce_after_mini_cart', array( $this, 'woo_bundles_remove_cart_widget_filters' ) );

		// Wishlists compatibility
		add_filter( 'woocommerce_wishlist_list_item_price', array( $this, 'woo_bundles_wishlist_list_item_price' ), 10, 3 );
		add_action( 'woocommerce_wishlist_after_list_item_name', array( $this, 'woo_bundles_wishlist_after_list_item_name' ), 10, 2 );

		// Fix microdata price in per product pricing mode
		add_action( 'woocommerce_single_product_summary', array( $this, 'woo_bundles_loop_price_9' ), 9 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'woo_bundles_loop_price_11' ), 11 );

		// Visibility of bundled items
		add_filter( 'woocommerce_order_item_visible', array( $this, 'woo_bundles_order_item_visible' ), 10, 2 );
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'woo_bundles_cart_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_visible', array( $this, 'woo_bundles_cart_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'woo_bundles_cart_item_visible' ), 10, 3 );

		// Indent bundled items in emails
		add_action( 'woocommerce_email_styles', array( $this, 'woo_bundles_email_styles' ) );
	}

	/**
	 * Frontend scripts.
	 *
	 * @return void
	 */
	public function woo_bundles_frontend_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-add-to-cart-bundle', WC_PB()->woo_bundles_plugin_url() . '/assets/js/add-to-cart-bundle' . $suffix . '.js', array( 'jquery', 'wc-add-to-cart-variation' ), WC_PB()->version, true );
		wp_register_style( 'wc-bundle-css', WC_PB()->woo_bundles_plugin_url() . '/assets/css/bundles-frontend.css', false, WC_PB()->version );
		wp_register_style( 'wc-bundle-style', WC_PB()->woo_bundles_plugin_url() . '/assets/css/bundles-style.css', false, WC_PB()->version );
		wp_enqueue_style( 'wc-bundle-style' );

		$params = array(
			'i18n_free'                     => __( 'Free!', 'woocommerce' ),
			'i18n_total'                    => __( 'Total', 'woocommerce-product-bundles' ) . ': ',
			'i18n_subtotal'                 => __( 'Subtotal', 'woocommerce-product-bundles' ) . ': ',
			'i18n_partially_out_of_stock'   => __( 'Insufficient stock', 'woocommerce-product-bundles' ),
			'i18n_partially_on_backorder'   => __( 'Available on backorder', 'woocommerce-product-bundles' ),
			'i18n_select_options'           => __( 'To continue, please choose product options&hellip;', 'woocommerce-product-bundles' ),
			'i18n_unavailable_text'         => __( 'Sorry, this product cannot be purchased at the moment.', 'woocommerce-product-bundles' ),
			'currency_symbol'               => get_woocommerce_currency_symbol(),
			'currency_position'             => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
			'currency_format_num_decimals'  => wc_bundles_get_price_decimals(),
			'currency_format_decimal_sep'   => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format_trim_zeros'    => false == apply_filters( 'woocommerce_price_trim_zeros', false ) ? 'no' : 'yes'
		);

		wp_localize_script( 'wc-add-to-cart-bundle', 'wc_bundle_params', $params );
	}

	/**
	 * Adds QuickView support
	 */
	public function woo_bundles_loop_add_to_cart_link( $link, $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			if ( $product->is_in_stock() && ! $product->has_variables() ) {
				return str_replace( 'product_type_bundle', 'product_type_bundle product_type_simple', $link );
			} else {
				return str_replace( 'add_to_cart_button', '', $link );
			}
		}

		return $link;
	}

	/**
	 * Override bundled item title in cart/checkout templates.
	 *
	 * @param  string   $content
	 * @param  array    $cart_item_values
	 * @param  string   $cart_item_key
	 * @return string
	 */
	public function woo_bundles_in_cart_item_title( $content, $cart_item_values, $cart_item_key ) {

		if ( ! empty( $cart_item_values[ 'bundled_by' ] ) ) {

			// Display overridden title
			if ( ! empty( $cart_item_values[ 'bundled_item_id' ] ) && ! empty( $cart_item_values[ 'stamp' ][ $cart_item_values[ 'bundled_item_id' ] ][ 'title' ] ) ) {

				$content = $cart_item_values[ 'stamp' ][ $cart_item_values[ 'bundled_item_id' ] ][ 'title' ];

				if ( $cart_item_values[ 'data' ]->is_visible() && ! ( is_checkout() || ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'woocommerce_update_order_review' ) || did_action( 'woocommerce_before_mini_cart' ) ) ) {
					$content = sprintf( '<a href="%s">%s</a>', $cart_item_values[ 'data' ]->get_permalink(), $content );
				}
			}

			$this->woo_bundles_enqueue_bundled_table_item_js();
		}

		return $content;
	}

	/**
	 * Override bundled item title in order-details template.
	 *
	 * @param  string 	$content
	 * @param  array 	$order_item
	 * @return string
	 */
	public function woo_bundles_order_table_item_title( $content, $order_item ) {

		if ( ! empty( $order_item[ 'bundled_by' ] ) ) {

			// Display overridden title
			if ( ! empty( $order_item[ 'bundled_item_title' ] ) ) {

				$content = $order_item[ 'bundled_item_title' ];

				if ( did_action( 'woocommerce_view_order' ) || did_action( 'woocommerce_thankyou' ) ) {
					$product = wc_get_product( ! empty( $order_item[ 'variation_id' ] ) ? $order_item[ 'variation_id' ] : $order_item[ 'product_id' ] );

					if ( $product && $product->is_visible() ) {
						$content = sprintf( '<a href="%s">%s</a>', $product->get_permalink(), $content );
					}
				}
			}

			if ( did_action( 'woocommerce_view_order' ) || did_action( 'woocommerce_thankyou' ) ) {
				$this->woo_bundles_enqueue_bundled_table_item_js();

			// Use of > ensures that if multiple e-mails are sent in one go, then the filter is being called between the two actions
			} elseif ( did_action( 'woocommerce_email_before_order_table' ) > did_action( 'woocommerce_email_after_order_table' ) ) {
				$content = '<small>' . $content . '</small>';
			}
		}

		return $content;
	}

	/**
	 * Enqeue js that wraps bundled table items in a div in order to apply indentation reliably.
	 *
	 * @return void
	 */
	private function woo_bundles_enqueue_bundled_table_item_js() {

		if ( ! $this->enqueued_bundled_table_item_js ) {
			wc_enqueue_js( "
				var wc_pb_wrap_bundled_table_item = function() {
					jQuery( '.bundled_table_item td.product-name' ).wrapInner( '<div class=\"bundled_table_item_indent\"></div>' );
				}

				jQuery( 'body' ).on( 'updated_checkout', function() {
					wc_pb_wrap_bundled_table_item();
				} );

				wc_pb_wrap_bundled_table_item();
			" );

			$this->enqueued_bundled_table_item_js = true;
		}
	}

	/**
	 * Change the tr class of bundled items in all templates to allow their styling.
	 *
	 * @param  string   $classname      original classname
	 * @param  array    $values         cart item data
	 * @return string                   modified class string
	 */
	public function woo_bundles_table_item_class( $classname, $values ) {

		if ( isset( $values[ 'bundled_by' ] ) )
			return $classname . ' bundled_table_item';
		elseif ( isset( $values[ 'stamp' ] ) )
			return $classname . ' bundle_table_item';

		return $classname;
	}

	/**
	 * Load quickview script.
	 */
	public function woo_bundles_qv() {

		if ( ! is_product() ) {

			$this->woo_bundles_frontend_scripts();

			wp_enqueue_script( 'wc-add-to-cart-bundle' );
			wp_enqueue_style( 'wc-bundle-css' );

		}

	}

	/**
	 * Filters the reported number of cart items depending on pricing strategy: per-item price: container is subtracted, static price: items are subtracted.
	 *
	 * @param  int  $count  item counnt
	 * @return int          modified item count
	 */
	public function woo_bundles_cart_contents_count( $count ) {

		$cart = WC()->cart->get_cart();

		$subtract = 0;

		foreach ( $cart as $key => $value ) {

			if ( isset( $value[ 'bundled_by' ] ) ) {
				$subtract += $value[ 'quantity' ];
			}
		}

		return $count - $subtract;
	}

	/**
	 * Add cart widget filters.
	 *
	 * @return void
	 */
	public function woo_bundles_add_cart_widget_filters() {

		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'woo_bundles_cart_widget_item_visible' ), 10, 3 );
		add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'woo_bundles_cart_widget_item_qty' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'woo_bundles_cart_widget_container_item_name' ), 10, 3 );
	}

	/**
	 * Remove cart widget filters.
	 *
	 * @return void
	 */
	public function woo_bundles_remove_cart_widget_filters() {

		remove_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'woo_bundles_cart_widget_item_visible' ), 10, 3 );
		remove_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'woo_bundles_cart_widget_item_qty' ), 10, 3 );
		remove_filter( 'woocommerce_cart_item_name', array( $this, 'woo_bundles_cart_widget_container_item_name' ), 10, 3 );
	}

	/**
	 * Do not show bundled items in mini cart.
	 *
	 * @param  boolean  $show           show/hide flag
	 * @param  array    $cart_item      cart item data
	 * @param  string   $cart_item_key  cart item key
	 * @return boolean                  modified show/hide flag
	 */
	public function woo_bundles_cart_widget_item_visible( $show, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item[ 'bundled_by' ] ) ) {
			$show = false;
		}

		return $show;
	}

	/**
	 * Tweak bundle container qty.
	 *
	 * @param  bool 	$qty
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return bool
	 */
	public function woo_bundles_cart_widget_item_qty( $qty, $cart_item, $cart_item_key ) {

		global $woocommerce_composite_products;

		if ( isset( $cart_item[ 'bundled_items' ] ) ) {
			$qty = '<span class="quantity">' . apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $cart_item[ 'data' ], $cart_item[ 'quantity' ] ), $cart_item, $cart_item_key ) . '</span>';
		}

		return $qty;
	}

	/**
	 * Tweak bundle container name.
	 *
	 * @param  bool 	$show
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return bool
	 */
	public function woo_bundles_cart_widget_container_item_name( $name, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item[ 'bundled_items' ] ) ) {
			$name = WC_PB_Helpers::format_product_shop_title( $name, $cart_item[ 'quantity' ] );
		}

		return $name;
	}

	/**
	 * Inserts bundle contents after main wishlist bundle item is displayed.
	 *
	 * @param  array    $item       Wishlist item
	 * @param  array    $wishlist   Wishlist
	 * @return void
	 */
	public function woo_bundles_wishlist_after_list_item_name( $item, $wishlist ) {

		if ( $item[ 'data' ]->is_type( 'bundle' ) && ! empty( $item[ 'stamp' ] ) ) {

			echo '<dl>';

			foreach ( $item[ 'stamp' ] as $bundled_item_id => $bundled_item_data ) {

				echo '<dt class="bundled_title_meta wishlist_bundled_title_meta">' . get_the_title( $bundled_item_data[ 'product_id' ] ) . ' <strong class="bundled_quantity_meta wishlist_bundled_quantity_meta product-quantity">&times; ' . $bundled_item_data[ 'quantity' ] . '</strong></dt>';

				if ( ! empty ( $bundled_item_data[ 'attributes' ] ) ) {

					$attributes = '';

					foreach ( $bundled_item_data[ 'attributes' ] as $attribute_name => $attribute_value ) {

						$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attribute_name ) ) );

						// If this is a term slug, get the term's nice name
			            if ( taxonomy_exists( $taxonomy ) ) {

			            	$term = get_term_by( 'slug', $attribute_value, $taxonomy );

			            	if ( ! is_wp_error( $term ) && $term && $term->name ) {
			            		$attribute_value = $term->name;
			            	}

			            	$label = wc_attribute_label( $taxonomy );

			            // If this is a custom option slug, get the options name
			            } else {

							$attribute_value    = apply_filters( 'woocommerce_variation_option_name', $attribute_value );
							$bundled_product    = wc_get_product( $bundled_item_data[ 'product_id' ] );
							$product_attributes = $bundled_product->get_attributes();

							if ( isset( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ] ) ) {
								$label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $attribute_name ) ][ 'name' ] );
							} else {
								$label = $attribute_name;
							}
						}

						$attributes = $attributes . $label . ': ' . $attribute_value . ', ';
					}
					echo '<dd class="bundled_attribute_meta wishlist_bundled_attribute_meta">' . rtrim( $attributes, ', ' ) . '</dd>';
				}
			}
			echo '</dl>';
			echo '<p class="bundled_notice wishlist_component_notice">' . __( '*', 'woocommerce-product-bundles' ) . '&nbsp;&nbsp;<em>' . __( 'For accurate pricing details, please add the product in your cart.', 'woocommerce-product-bundles' ) . '</em></p>';
		}
	}

	/**
	 * Modifies wishlist bundle item price - the precise sum cannot be displayed reliably unless the item is added to the cart.
	 *
	 * @param  double   $price      Item price
	 * @param  array    $item       Wishlist item
	 * @param  array    $wishlist   Wishlist
	 * @return string   $price
	 */
	public function woo_bundles_wishlist_list_item_price( $price, $item, $wishlist ) {

		if ( $item[ 'data' ]->is_type( 'bundle' ) && ! empty( $item[ 'stamp' ] ) )
			return __( '*', 'woocommerce-product-bundles' );

		return $price;
	}

	/**
	 * Modify microdata get_price call: get_price() will return the base price in per-product pricing mode, or the product price in static pricing mode.
	 * Here we modify the output of get_price, which is overridden in the bundle product class.
	 *
	 * @return void
	 */
	public function woo_bundles_loop_price_9() {

		global $product;

		if ( $product->is_type( 'bundle' ) ) {

			if ( ! $product->is_synced() )
				$product->sync_bundle();

			add_filter( 'woocommerce_bundle_get_price', array( $this, 'get_microdata_bundle_price' ), 10, 2 );
		}
	}

	/**
	 * Modify microdata get_price call.
	 *
	 * @return void
	 */
	public function get_microdata_bundle_price( $price, $bundle ) {

		return $bundle->min_price;
	}

	/**
	 * Remove filter.
	 *
	 * @return void
	 */
	public function woo_bundles_loop_price_11() {

		remove_filter( 'woocommerce_bundle_get_price', array( $this, 'get_microdata_bundle_price' ), 10, 2 );
	}

	/**
	 * Visibility of bundled item in orders.
	 *
	 * @param  boolean $visible
	 * @param  array   $order_item
	 * @return boolean
	 */
	public function woo_bundles_order_item_visible( $visible, $order_item ) {

		if ( ! empty( $order_item[ 'bundled_by' ] ) && ! empty( $order_item[ 'bundled_item_hidden' ] ) ) {
			return false;
		}

		return $visible;
	}

	/**
	 * Visibility of bundled item in cart.
	 *
	 * @param  boolean $visible
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return boolean
	 */
	public function woo_bundles_cart_item_visible( $visible, $cart_item, $cart_item_key ) {

		if ( ! empty( $cart_item[ 'bundled_by' ] ) && ! empty( $cart_item[ 'stamp' ] ) ) {

			if ( ! empty( $cart_item[ 'bundled_item_id' ] ) ) {

				$bundled_item_id = $cart_item[ 'bundled_item_id' ];
				$hidden          = isset( $cart_item[ 'stamp' ][ $bundled_item_id ][ 'secret' ] ) ? $cart_item[ 'stamp' ][ $bundled_item_id ][ 'secret' ] : 'no';

				if ( $hidden === 'yes' ) {
					$visible = false;
				}
			}
		}

		return $visible;
	}

	/**
	 * Indent bundled items in emails
	 *
	 * @param  string 	$css
	 * @return string
	 */
	public function woo_bundles_email_styles( $css ) {
		$css = $css . ".bundled_table_item td:nth-child(1) { padding-left: 35px !important; } .bundled_table_item td { border-top: none; }";
		return $css;
	}
}
