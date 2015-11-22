<?php
/**
 * Product Bundles Compatibility.
 *
 * @since  4.11.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_CP_Compatibility {

	public static function init() {

		/**
		 * Prices
		 */

		// Price calculations
		add_filter( 'woocommerce_composited_product_get_price', array( __CLASS__, 'composited_bundle_price' ), 10, 4 );
		add_filter( 'woocommerce_composited_product_get_regular_price', array( __CLASS__, 'composited_bundle_reg_price' ), 10, 4 );
		add_filter( 'woocommerce_composited_product_get_price_including_tax', array( __CLASS__, 'composited_bundle_price_incl_tax' ), 10, 3 );
		add_filter( 'woocommerce_composited_product_get_price_excluding_tax', array( __CLASS__, 'composited_bundle_price_excl_tax' ), 10, 3 );
		add_filter( 'woocommerce_composited_product_is_nyp', array( __CLASS__, 'composited_bundle_is_nyp' ), 10, 2 );

		/**
		 * Templates
		 */

		// Show bundle type products using the bundle-product.php composited product template
		add_action( 'woocommerce_composite_show_composited_product_bundle', array( __CLASS__, 'composite_show_product_bundle' ), 10, 3 );

		/**
		 * Cart and Orders
		 */

		// Validate bundle type component selections
		add_filter( 'woocommerce_composite_component_add_to_cart_validation', array( __CLASS__, 'composite_validate_bundle_data' ), 10, 6 );

		// Add bundle identifier to composited item stamp
		add_filter( 'woocommerce_composite_component_cart_item_identifier', array( __CLASS__, 'composite_bundle_cart_item_stamp' ), 10, 2 );

		// Apply component prefix to bundle input fields
		add_filter( 'woocommerce_product_bundle_field_prefix', array( __CLASS__, 'bundle_field_prefix' ), 10, 2 );

		// Hook into composited product add-to-cart action to add bundled items since 'woocommerce-add-to-cart' action cannot be used recursively
		add_action( 'woocommerce_composited_add_to_cart', array( __CLASS__, 'add_bundle_to_cart' ), 10, 6 );

		// Link bundled cart/order items with composite
		add_filter( 'woocommerce_cart_item_is_child_of_composite', array( __CLASS__, 'bundled_cart_item_is_child_of_composite' ), 10, 5 );
		add_filter( 'woocommerce_order_item_is_child_of_composite', array( __CLASS__, 'bundled_order_item_is_child_of_composite' ), 10, 4 );

		// Tweak bundle container items appearance in various templates
		add_filter( 'woocommerce_cart_item_name', array( __CLASS__, 'composited_bundle_in_cart_item_title' ), 9, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'composited_bundle_in_cart_item_quantity' ), 11, 2 );
		add_filter( 'woocommerce_composited_cart_item_quantity_html', array( __CLASS__, 'composited_bundle_checkout_item_quantity' ), 10, 2 );
		add_filter( 'woocommerce_order_item_visible', array( __CLASS__, 'composited_bundle_order_item_visible' ), 10, 2 );
		add_filter( 'woocommerce_order_item_name', array( __CLASS__, 'composited_bundle_order_table_item_title' ), 9, 2 );
		add_filter( 'woocommerce_composited_order_item_quantity_html', array( __CLASS__, 'composited_bundle_order_table_item_quantity' ), 11, 2 );
	}

	/**
	 * Composited bundle price.
	 *
	 * @param  double        $price
	 * @param  string        $min_or_max
	 * @param  boolean       $display
	 * @param  WC_CP_Product $composited_product
	 * @return double
	 */
	public static function composited_bundle_price( $price, $min_or_max, $display, $composited_product ) {

		$product = $composited_product->get_product();

		if ( $product->product_type === 'bundle' ) {
			$price = $product->get_bundle_price( $min_or_max, $display );
		}

		return $price;
	}

	/**
	 * Composited bundle regular price.
	 *
	 * @param  double        $price
	 * @param  string        $min_or_max
	 * @param  boolean       $display
	 * @param  WC_CP_Product $composited_product
	 * @return double
	 */
	public static function composited_bundle_reg_price( $price, $min_or_max, $display, $composited_product ) {

		$product = $composited_product->get_product();

		if ( $product->product_type === 'bundle' ) {
			$price = $product->get_bundle_regular_price( $min_or_max, $display );
		}

		return $price;
	}

	/**
	 * Composited bundle price including tax.
	 *
	 * @param  double        $price
	 * @param  string        $min_or_max
	 * @param  WC_CP_Product $composited_product
	 * @return double
	 */
	public static function composited_bundle_price_incl_tax( $price, $min_or_max, $composited_product ) {

		$product = $composited_product->get_product();

		if ( $product->product_type === 'bundle' ) {
			$price = $product->get_bundle_price_including_tax( $min_or_max );
		}

		return $price;
	}

	/**
	 * Composited bundle price excluding tax.
	 *
	 * @param  double        $price
	 * @param  string        $min_or_max
	 * @param  WC_CP_Product $composited_product
	 * @return double
	 */
	public static function composited_bundle_price_excl_tax( $price, $min_or_max, $composited_product ) {

		$product = $composited_product->get_product();

		if ( $product->product_type === 'bundle' ) {
			$price = $product->get_bundle_price_excluding_tax( $min_or_max );
		}

		return $price;
	}

	/**
	 * True if a composited bundle is seen as a NYP product.
	 *
	 * @param  boolean       $is_nyp
	 * @param  WC_CP_Product $composited_product
	 * @return double
	 */
	public static function composited_bundle_is_nyp( $is_nyp, $composited_product ) {

		$product = $composited_product->get_product();

		if ( $product->product_type === 'bundle' ) {
			if ( $product->is_nyp() || $product->contains_nyp() ) {
				$is_nyp = true;
			}
		}

		return $is_nyp;
	}

	/**
	 * Hook into 'woocommerce_composite_show_composited_product_bundle' to show bundle type product content.
	 *
	 * @param  WC_Product  $product
	 * @param  string      $component_id
	 * @param  WC_Product  $composite
	 * @return void
	 */
	public static function composite_show_product_bundle( $product, $component_id, $composite ) {

		if ( $product->contains_sub() ) {

			?><div class="woocommerce-error"><?php
				echo __( 'This item cannot be purchased at the moment.', 'woocommerce-product-bundles' );
			?></div><?php

			return false;
		}

		WC_PB_Compatibility::$compat_product = $product;
		WC_PB_Compatibility::$bundle_prefix  = $component_id;

		$component_data = $composite->get_component_data( $component_id );

		$quantity_min = $component_data[ 'quantity_min' ];
		$quantity_max = $component_data[ 'quantity_max' ];

		if ( $product->sold_individually == 'yes' ) {
 			$quantity_max = 1;
 			$quantity_min = min( $quantity_min, 1 );
 		}

 		$custom_data = apply_filters( 'woocommerce_composited_product_custom_data', array(), $product, $component_id, $component_data, $composite );

 		wc_get_template( 'composited-product/bundle-product.php', array(
			'product'              => $product,
			'composite_id'         => $composite->id,
			'quantity_min'         => $quantity_min,
			'quantity_max'         => $quantity_max,
			'available_variations' => $product->get_available_bundle_variations(),
			'attributes'           => $product->get_bundle_variation_attributes(),
			'selected_attributes'  => $product->get_selected_bundle_variation_attributes(),
			'custom_data'          => $custom_data,
			'bundle_price_data'    => $product->get_bundle_price_data(),
			'bundled_items'        => $product->get_bundled_items(),
			'component_id'         => $component_id,
			'composite_product'    => $composite
		), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
	}

	/**
	 * Hook into 'woocommerce_composite_component_add_to_cart_validation' to validate composited bundles.
	 *
	 * @param  boolean  $result
	 * @param  int      $composite_id
	 * @param  string   $component_id
	 * @param  int      $bundle_id
	 * @param  int      $quantity
	 * @return boolean
	 */
	public static function composite_validate_bundle_data( $result, $composite_id, $component_id, $bundle_id, $quantity, $cart_item_data ) {

		// Get product type
		$terms 			= get_the_terms( $bundle_id, 'product_type' );
		$product_type 	= ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

		if ( $product_type == 'bundle' ) {

			// Present only when re-ordering
			if ( isset( $cart_item_data[ 'composite_data' ][ $component_id ][ 'stamp' ] ) )
				$cart_item_data [ 'stamp' ] = $cart_item_data[ 'composite_data' ][ $component_id ][ 'stamp' ];

			WC_PB_Compatibility::$bundle_prefix = $component_id;

			add_filter( 'woocommerce_bundle_before_validation', array( __CLASS__, 'disallow_bundled_item_subs' ), 10, 2 );

			$result = WC_PB()->cart->woo_bundles_validation( true, $bundle_id, $quantity, '', array(), $cart_item_data );

			WC_PB_Compatibility::$bundle_prefix = '';

			remove_filter( 'woocommerce_bundle_before_validation', array( __CLASS__, 'disallow_bundled_item_subs' ), 10, 2 );

			// Add filter to return stock manager items from bundle
			if ( class_exists( 'WC_CP_Stock_Manager' ) ) {
				add_filter( 'woocommerce_composite_component_associated_stock', array( __CLASS__, 'associated_bundle_stock' ), 10, 5 );
			}
		}

		return $result;
	}

	/**
	 * Bundles with subscriptions can't be composited.
	 *
	 * @param  boolean     $passed
	 * @param  WC_Product  $bundle
	 * @return boolean
	 */
	public static function disallow_bundled_item_subs( $passed, $bundle ) {

		if ( $bundle->contains_sub() ) {

			wc_add_notice( sprintf( __( 'The configuration you have selected cannot be added to the cart. &quot;%s&quot; cannot be purchased.', 'woocommerce-product-bundles' ), $bundle->get_title() ), 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Hook into 'woocommerce_composite_component_associated_stock' to append bundled items to the composite stock data object.
	 *
	 * @param  WC_PB_Stock_Manager   $items
	 * @param  int                   $composite_id
	 * @param  string                $component_id
	 * @param  int                   $bundled_product_id
	 * @param  int                   $quantity
	 * @return WC_PB_Stock_Manager
	 */
	public static function associated_bundle_stock( $items, $composite_id, $component_id, $bundled_product_id, $quantity ) {

		if ( ! empty( WC_PB_Compatibility::$stock_data ) ) {

			$items = WC_PB_Compatibility::$stock_data;

			WC_PB_Compatibility::$stock_data = '';
			remove_filter( 'woocommerce_composite_component_associated_stock', array( __CLASS__, 'associated_bundle_stock' ), 10, 5 );
		}

		return $items;
	}

	/**
	 * Hook into 'woocommerce_composite_component_cart_item_identifier' to add stamp data for bundles.
	 *
	 * @param  array  $composited_item_identifier
	 * @param  string $composited_item_id
	 * @return array
	 */
	public static function composite_bundle_cart_item_stamp( $composited_item_identifier, $composited_item_id ) {

		if ( isset( $composited_item_identifier[ 'type' ] ) && $composited_item_identifier[ 'type' ] === 'bundle' ) {

			WC_PB_Compatibility::$bundle_prefix = $composited_item_id;

			$bundle_cart_data = WC_PB()->cart->woo_bundles_add_cart_item_data( array(), $composited_item_identifier[ 'product_id' ] );

			$composited_item_identifier[ 'stamp' ] = $bundle_cart_data[ 'stamp' ];

			WC_PB_Compatibility::$bundle_prefix = '';
		}

		return $composited_item_identifier;
	}

	/**
	 * Sets a prefix for unique bundles.
	 *
	 * @param  string 	$prefix
	 * @param  int 		$product_id
	 * @return string
	 */
	public static function bundle_field_prefix( $prefix, $product_id ) {

		if ( ! empty( WC_PB_Compatibility::$bundle_prefix ) ) {
			return 'component_' . WC_PB_Compatibility::$bundle_prefix . '_';
		}

		return $prefix;
	}

	/**
	 * Hook into 'woocommerce_composited_add_to_cart' to trigger 'woo_bundles_add_bundle_to_cart'.
	 *
	 * @param string  $cart_item_key
	 * @param int     $product_id
	 * @param int     $quantity
	 * @param int     $variation_id
	 * @param array   $variation
	 * @param array   $cart_item_data
	 */
	public static function add_bundle_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		WC_PB()->cart->woo_bundles_add_bundle_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );
	}

	/**
	 * Used to link bundled order items with the composite container product.
	 *
	 * @param  boolean  $is_child
	 * @param  array    $order_item
	 * @param  array    $composite_item
	 * @param  WC_Order $order
	 * @return boolean
	 */
	public static function bundled_order_item_is_child_of_composite( $is_child, $order_item, $composite_item, $order ) {

		if ( ! empty( $order_item[ 'bundled_by' ] ) ) {

			$parent = WC_PB()->order->get_bundled_order_item_container( $order_item, $order );

			if ( $parent && isset( $parent[ 'composite_parent' ] ) && $parent[ 'composite_parent' ] === $composite_item[ 'composite_cart_key' ] ) {
				return true;
			}
		}

		return $is_child;
	}

	/**
	 * Used to link bundled cart items with the composite container product.
	 *
	 * @param  boolean  $is_child
	 * @param  string   $cart_item_key
	 * @param  array    $cart_item_data
	 * @param  string   $composite_key
	 * @param  array    $composite_data
	 * @return boolean
	 */
	public static function bundled_cart_item_is_child_of_composite( $is_child, $cart_item_key, $cart_item_data, $composite_key, $composite_data ) {

		if ( ! empty( $cart_item_data[ 'bundled_by' ] ) ) {

			$parent_key = $cart_item_data[ 'bundled_by' ];

			if ( isset( WC()->cart->cart_contents[ $parent_key ] ) ) {

				$parent = WC()->cart->cart_contents[ $parent_key ];

				if ( isset( $parent[ 'composite_parent' ] ) && $parent[ 'composite_parent' ] == $composite_key ) {
					return true;
				}
			}
		}

		return $is_child;
	}

	/**
	 * Edit composited bundle container cart title.
	 *
	 * @param  string   $content
	 * @param  array    $cart_item_values
	 * @param  string   $cart_item_key
	 * @return string
	 */
	public static function composited_bundle_in_cart_item_title( $content, $cart_item_values, $cart_item_key ) {

		if ( isset( $cart_item_values[ 'bundled_items' ] ) && ! empty( $cart_item_values[ 'composite_parent' ] ) ) {

			if ( ! empty( $cart_item_values[ 'stamp' ] ) ) {

				if ( empty( $cart_item_values[ 'bundled_items' ] ) && $cart_item_values[ 'data' ]->get_price() == 0  ) {
					$content = __( 'None', 'woocommerce-product-bundles' );
				} elseif ( apply_filters( 'woocommerce_composited_bundle_container_cart_item_hide_title', false, $cart_item_values, $cart_item_key ) ) {
					$content = '';
				}
			}
		}

		return $content;
	}

	/**
	 * Edit composited bundle container cart qty.
	 *
	 * @param  int      $quantity
	 * @param  string   $cart_item_key
	 * @return int
	 */
	public static function composited_bundle_in_cart_item_quantity( $quantity, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
			 $cart_item_values = WC()->cart->cart_contents[ $cart_item_key ];

			if ( isset( $cart_item_values[ 'bundled_items' ] ) && ! empty( $cart_item_values[ 'composite_parent' ] ) ) {

				if ( ! empty( $cart_item_values[ 'stamp' ] ) ) {

					if ( empty( $cart_item_values[ 'bundled_items' ] ) && $cart_item_values[ 'data' ]->get_price() == 0  ) {
						$quantity = '';
					} elseif ( apply_filters( 'woocommerce_composited_bundle_container_cart_item_hide_title', false, $cart_item_values, $cart_item_key ) ) {
						$quantity = '';
					}
				}
			}
		}

		return $quantity;
	}

	/**
	 * Edit composited bundle container cart qty.
	 *
	 * @param  int      $quantity
	 * @param  string   $cart_item_values
	 * @param  string   $cart_item_key
	 * @return int
	 */
	public static function composited_bundle_checkout_item_quantity( $quantity, $cart_item_values, $cart_item_key = false ) {

		if ( isset( $cart_item_values[ 'bundled_items' ] ) && ! empty( $cart_item_values[ 'composite_parent' ] ) ) {

			if ( ! empty( $cart_item_values[ 'stamp' ] ) ) {

				if ( empty( $cart_item_values[ 'bundled_items' ] ) && $cart_item_values[ 'data' ]->get_price() == 0  ) {
					$quantity = '';
				} elseif ( apply_filters( 'woocommerce_composited_bundle_container_cart_item_hide_title', false, $cart_item_values, $cart_item_key ) ) {
					$quantity = '';
				}
			}
		}

		return $quantity;
	}

	/**
	 * Visibility of composited bundle container in orders.
	 * Hide containers without children and a zero price (all optional).
	 *
	 * @param  boolean $visible
	 * @param  array   $order_item
	 * @return boolean
	 */
	public static function composited_bundle_order_item_visible( $visible, $order_item ) {

		if ( isset( $order_item[ 'bundled_items' ] ) && ! empty( $order_item[ 'composite_parent' ] ) ) {

			$bundled_items = maybe_unserialize( $order_item[ 'bundled_items' ] );

			if ( empty( $bundled_items ) && $order_item[ 'line_subtotal' ] == 0  ) {
				$visible = false;
			}
		}

		return $visible;
	}

	/**
	 * Edit composited bundle container order item title.
	 *
	 * @param  string   $content
	 * @param  array 	$order_item
	 * @return string
	 */
	public static function composited_bundle_order_table_item_title( $content, $order_item ) {

		if ( isset( $order_item[ 'bundled_items' ] ) && ! empty( $order_item[ 'composite_parent' ] ) ) {

			if ( ! empty( $order_item[ 'stamp' ] ) ) {

				$bundled_items = maybe_unserialize( $order_item[ 'bundled_items' ] );

				if ( empty( $bundled_items ) && $order_item[ 'line_subtotal' ] == 0  ) {
					$content = __( 'None', 'woocommerce-product-bundles' );
				} elseif ( apply_filters( 'woocommerce_composited_bundle_container_order_item_hide_title', false, $order_item ) ) {
					$content = '';
				}
			}
		}

		return $content;
	}

	/**
	 * Edit composited bundle container order item qty.
	 *
	 * @param  string   $content
	 * @param  array 	$order_item
	 * @return string
	 */
	public static function composited_bundle_order_table_item_quantity( $quantity, $order_item ) {

		if ( isset( $order_item[ 'bundled_items' ] ) && ! empty( $order_item[ 'composite_parent' ] ) ) {

			if ( ! empty( $order_item[ 'stamp' ] ) ) {

				$bundled_items = maybe_unserialize( $order_item[ 'bundled_items' ] );

				if ( empty( $bundled_items ) && $order_item[ 'line_subtotal' ] == 0  ) {
					$quantity = '';
				} elseif ( apply_filters( 'woocommerce_composited_bundle_container_order_item_hide_title', false, $order_item ) ) {
					$quantity = '';
				}
			}
		}

		return $quantity;
	}

}

WC_PB_CP_Compatibility::init();
