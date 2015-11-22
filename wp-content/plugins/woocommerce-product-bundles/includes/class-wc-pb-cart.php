<?php
/**
 * Product Bundle cart functions and filters.
 *
 * @class   WC_PB_Cart
 * @version 4.11.4
 * @since   4.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Cart {

	/**
	 * Setup cart class
	 */
	function __construct() {

		// Validate bundle add-to-cart
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woo_bundles_validation' ), 10, 6 );

		// Add bundle-specific cart item data
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woo_bundles_add_cart_item_data' ), 10, 2 );

		// Add bundled items to the cart
		add_action( 'woocommerce_add_to_cart', array( $this, 'woo_bundles_add_bundle_to_cart' ), 10, 6 );

		// Modify cart items for bundled pricing strategy
		add_filter( 'woocommerce_add_cart_item', array( $this, 'woo_bundles_add_cart_item_filter' ), 10, 2 );

		// Load bundle data from session into the cart
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woo_bundles_get_cart_data_from_session' ), 10, 3 );

		// Sync quantities of bundled items with bundle quantity
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woo_bundles_cart_item_quantity' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'woo_bundles_cart_item_remove_link' ), 10, 2 );

		// Sync quantities of bundled items with bundle quantity
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'woo_bundles_update_quantity_in_cart' ), 1, 2 );
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'woo_bundles_update_quantity_in_cart' ), 1 );

		// Put back cart item data to allow re-ordering of bundles
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'woo_bundles_order_again' ), 10, 3 );

		// Filter cart item price
		add_filter( 'woocommerce_cart_item_price', array( $this, 'woo_bundles_cart_item_price_html' ), 10, 3 );

		// Modify cart items subtotals according to the pricing strategy used (static / per-product)
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'woo_bundles_item_subtotal' ), 10, 3 );
		add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'woo_bundles_item_subtotal' ), 10, 3 );

		add_action( 'woocommerce_cart_item_removed', array( $this, 'woo_bundles_cart_item_removed' ), 10, 2 );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'woo_bundles_cart_item_restored' ), 10, 2 );

		// Shipping fix - ensure that non-virtual containers/children, which are shipped, have a valid price that can be used for insurance calculations.
		// Additionally, bundled item weights may have to be added in the container.
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woo_bundles_shipping_packages_fix' ) );
	}

	/**
	 * Validates add-to-cart for bundles.
	 * Basically ensures that stock for all bundled products exists before attempting to add them to cart.
	 *
	 * @param  boolean  $add                core validation add to cart flag
	 * @param  int      $product_id         the product id
	 * @param  int      $product_quantity   quantity
	 * @param  mixed    $variation_id       variation id
	 * @param  array    $variations         variation data
	 * @param  array    $cart_item_data     cart item data
	 * @return boolean                      modified add to cart validation flag
	 */
	function woo_bundles_validation( $add, $product_id, $product_quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		// Get product type
		$terms        = get_the_terms( $product_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

		// Ordering again?
		$order_again  = isset( $_GET[ 'order_again' ] ) && isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], 'woocommerce-order_again' );

		// prevent bundled items from getting validated - they will be added by the container item
		if ( isset( $cart_item_data[ 'is_bundled' ] ) && $order_again )
			return false;

		if ( $product_type === 'bundle' ) {

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return false;
			}

			if ( ! apply_filters( 'woocommerce_bundle_before_validation', true, $product ) ) {
				return false;
			}

			// If a stock-managed product / variation exists in the bundle multiple times, its stock will be checked only once for the sum of all bundled quantities.
			// The stock manager class keeps a record of stock-managed product / variation ids
			$bundled_stock = new WC_PB_Stock_Manager();

			// Grab bundled items
			$bundled_items = $product->get_bundled_items();

			if ( ! $bundled_items ) {
				return $add;
			}

			foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

				$id                   = $bundled_item->product_id;
				$bundled_product_type = $bundled_item->product->product_type;

				// Optional
				$is_optional = $bundled_item->is_optional();

				if ( $is_optional ) {

					if ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'optional_selected' ] ) && $order_again ) {

						$is_optional_selected = 'yes' === $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'optional_selected' ] ? true : false;

					} elseif ( isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_selected_optional_' . $bundled_item_id ] ) ) {

						$is_optional_selected = isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_selected_optional_' . $bundled_item_id ] );

					} else {

						$is_optional_selected = false;
					}
				}

				if ( $is_optional && ! $is_optional_selected ) {
					continue;
				}

				// Check quantity
				$item_quantity_min = $bundled_item->get_quantity();
				$item_quantity_max = $bundled_item->get_quantity( 'max' );

				if ( isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_quantity_' . $bundled_item_id ] ) && is_numeric( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_quantity_' . $bundled_item_id ] ) ) {

					$item_quantity = absint( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_quantity_' . $bundled_item_id ] );

				} elseif ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'quantity' ] ) && $order_again ) {

					$item_quantity = $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'quantity' ];

				} else {

					$item_quantity = $item_quantity_min;
				}

				// Sold individually?
				$item_sold_individually = get_post_meta( $id, '_sold_individually', true );

				if ( $item_sold_individually === 'yes' && $item_quantity > 1 ) {
					$item_quantity = $item_quantity_min;
				}


				if ( $item_quantity < $item_quantity_min && $item_sold_individually !== 'yes' ) {

					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. The quantity of &quot;%2$s&quot; cannot be lower than %3$d.', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->get_raw_title(), $item_quantity_min ), 'error' );
					return false;

				} elseif ( $item_quantity > $item_quantity_max ) {

					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. The quantity of &quot;%2$s&quot; cannot be higher than %3$d.', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->get_raw_title(), $item_quantity_max ), 'error' );
					return false;
				}

				$quantity = $item_quantity * $product_quantity;

				// If quantity is zero, continue
				if ( $quantity == 0 ) {
					continue;
				}

				// Purchasable
				if ( ! $bundled_item->is_purchasable() ) {
					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because &quot;%2$s&quot; cannot be purchased at the moment.', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->get_raw_title() ), 'error' );
					return false;
				}

				// Validate variation id
				if ( $bundled_product_type === 'variable' ) {

					$variation_id = '';

					if ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'variation_id' ] ) && $order_again ) {

						$variation_id = $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'variation_id' ];

					} elseif ( isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_variation_id_' . $bundled_item_id ] ) ) {

						$variation_id = $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_variation_id_' . $bundled_item_id ];
					}

					if ( $variation_id && is_numeric( $variation_id ) && $variation_id > 1 ) {

						if ( get_post_meta( $variation_id, '_price', true ) === '' ) {

							wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. The selected variation of &quot;%2$s&quot; cannot be purchased.', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );
							return false;
						}

						// Add item for validation
						$bundled_stock->add_item( $id, $variation_id, $quantity );

					}

					// Verify all attributes for the variable product were set
					$bundled_variation  = wc_get_product( $variation_id );
					$attributes         = ( array ) maybe_unserialize( get_post_meta( $id, '_product_attributes', true ) );
					$variation_data     = array();
					$missing_attributes = array();
					$all_set            = true;

					if ( $bundled_variation ) {
						$variation_data = $bundled_variation->variation_data;
					}

					// Verify all attributes
					foreach ( $attributes as $attribute ) {

					    if ( ! $attribute[ 'is_variation' ] ) {
					    	continue;
					    }

					    $taxonomy = 'attribute_' . sanitize_title( $attribute[ 'name' ] );

						if ( ! empty( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) ) {

							if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {

								 // Get value from post data
								if ( $attribute[ 'is_taxonomy' ] ) {
									$value = sanitize_title( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) );
								} else {
									$value = wc_clean( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) );
								}

							} else {

								// Get value from post data
								$value = sanitize_title( trim( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) ) );
							}

							// Get valid value from variation
							$valid_value = $variation_data[ $taxonomy ];

							// Allow if valid
							if ( $valid_value === '' || $valid_value === $value ) {
								continue;
							}

						} elseif ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ][ $taxonomy ] ) && isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'variation_id' ] ) && $order_again ) {

							if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {

								 // Get value from post data
								if ( $attribute[ 'is_taxonomy' ] ) {
									$value = sanitize_title( stripslashes( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ][ $taxonomy ] ) );
								} else {
									$value = wc_clean( stripslashes( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ][ $taxonomy ] ) );
								}

							} else {

								// Get value from post data
								$value = sanitize_title( trim( stripslashes( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ][ $taxonomy ] ) ) );
							}

							$valid_value = $variation_data[ $taxonomy ];

							if ( $valid_value === '' || $valid_value === $value ) {
								continue;
							}

						} else {

							$missing_attributes[] = wc_attribute_label( $attribute[ 'name' ] );
						}

						$all_set = false;
					}

					if ( ! $all_set ) {

						if ( $missing_attributes && WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ) {

							$required_fields_notice = sprintf( _n( '%1$s is a required &quot;%2$s&quot; field', '%1$s are required &quot;%2$s&quot; fields', sizeof( $missing_attributes ), 'woocommerce-product-bundles' ), wc_format_list_of_items( $missing_attributes ), $bundled_item->product->get_title() );
    						wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. %2$s.', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $required_fields_notice ), 'error' );
    						return false;

						} else {

							wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart. Please choose &quot;%2$s&quot; options&hellip;', 'woocommerce-product-bundles' ), get_the_title( $product_id ), $bundled_item->product->get_title() ), 'error' );
							return false;
						}
					}

				} elseif ( $bundled_product_type === 'simple' || $bundled_product_type === 'subscription' ) {

					// Add item for validation
					$bundled_stock->add_item( $id, false, $quantity );
				}

				if ( ! apply_filters( 'woocommerce_bundled_item_add_to_cart_validation', true, $product, $bundled_item, $quantity, $variation_id ) ) {
					return false;
				}

			}

			// Check stock for stock-managed bundled items
			// If out of stock, don't proceed
			if ( false === apply_filters( 'woocommerce_add_to_cart_bundle_validation', $bundled_stock->validate_stock( $product_id ), $product_id, $bundled_stock ) ) {
				return false;
			}

			// Composite Products compatibility
			WC_PB_Compatibility::$stock_data = $bundled_stock;
		}

		return $add;
	}

	/**
	 * Adds bundle specific cart-item data.
	 * The 'stamp' var is a unique identifier for that particular bundle configuration.
	 *
	 * @param  array    $cart_item_data    the cart item data
	 * @param  int      $product_id	       the product id
	 * @return array                       modified cart item data
	 */
	function woo_bundles_add_cart_item_data( $cart_item_data, $product_id ) {

		// Get product type
		$terms        = get_the_terms( $product_id, 'product_type' );
		$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

		if ( $product_type == 'bundle' ) {

			if ( isset( $cart_item_data[ 'stamp' ] ) && isset( $cart_item_data[ 'bundled_items' ] ) ) {
				return $cart_item_data;
			}

			$product = wc_get_product( $product_id );

			// grab bundled items
			$bundled_items = $product->get_bundled_items();

			if ( empty( $bundled_items ) ) {
				return $cart_item_data;
			}

			// Create a unique stamp id with the bundled items' configuration
			$stamp = array();

			foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

				$id                   = $bundled_item->product_id;
				$bundled_product_type = $bundled_item->product->product_type;

				$bundled_product_qty  = isset ( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_quantity_' . $bundled_item_id ] ) ? absint( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_quantity_' . $bundled_item_id ] ) : $bundled_item->get_quantity();

				$stamp[ $bundled_item_id ][ 'product_id' ] = $id;
				$stamp[ $bundled_item_id ][ 'type' ]       = $bundled_product_type;
				$stamp[ $bundled_item_id ][ 'quantity' ]   = $bundled_product_qty;
				$stamp[ $bundled_item_id ][ 'discount' ]   = $bundled_item->get_discount();
				$stamp[ $bundled_item_id ][ 'secret' ]     = $bundled_item->is_secret() ? 'yes' : 'no';

				if ( $bundled_item->has_title_override() ) {
					$stamp[ $bundled_item_id ][ 'title' ] = $bundled_item->get_raw_title();
				}

				// If quantity is zero, continue
				if ( $bundled_product_qty == 0 ) {
					continue;
				}

				// Optional
				$is_optional = $bundled_item->is_optional();

				if ( $is_optional ) {

					if ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'optional_selected' ] ) && isset( $_GET[ 'order_again' ] ) ) {

						$stamp[ $bundled_item_id ][ 'optional_selected' ] = 'yes' == $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'optional_selected' ] ? 'yes' : 'no';

					} elseif ( isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_selected_optional_' . $bundled_item_id ] ) ) {

						$stamp[ $bundled_item_id ][ 'optional_selected' ] = isset( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_selected_optional_' . $bundled_item_id ] ) ? 'yes' : 'no';

					} else {

						$stamp[ $bundled_item_id ][ 'optional_selected' ] = 'no';
					}
				}


				// Store variable product options in stamp to avoid generating the same bundle cart id
				if ( $bundled_product_type === 'variable' ) {

					if ( isset( $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ] ) && isset( $_GET[ 'order_again' ] ) ) {

						$stamp[ $bundled_item_id ][ 'attributes' ]   = $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'attributes' ];
						$stamp[ $bundled_item_id ][ 'variation_id' ] = $cart_item_data[ 'stamp' ][ $bundled_item_id ][ 'variation_id' ];

						continue;
					}

					$attr_stamp = array();
					$attributes = ( array ) maybe_unserialize( get_post_meta( $id, '_product_attributes', true ) );

					foreach ( $attributes as $attribute ) {

						if ( ! $attribute[ 'is_variation' ] ) {
							continue;
						}

						$taxonomy = 'attribute_' . sanitize_title( $attribute[ 'name' ] );

						// Value has already been checked for validity in function 'woo_bundles_validation'
						if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {

							// Get value from post data
							if ( $attribute[ 'is_taxonomy' ] ) {
								$value = sanitize_title( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) );
							} else {
								$value = wc_clean( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) );
							}

							$attr_stamp[ $taxonomy ] = $value;

						} else {

							// Get value from post data
							$value = sanitize_title( trim( stripslashes( $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_' . $taxonomy . '_' . $bundled_item_id ] ) ) );

							if ( $attribute[ 'is_taxonomy' ] ) {

								$attr_stamp[ $taxonomy ] = $value;

							} else {

								// For custom attributes, get the name from the slug
								$options = array_map( 'trim', explode( WC_DELIMITER, $attribute[ 'value' ] ) );

								foreach ( $options as $option ) {
									if ( sanitize_title( $option ) == $value ) {
										$value = $option;
										break;
									}
								}

								$attr_stamp[ $taxonomy ] = $value;
							}
						}
					}

					$stamp[ $bundled_item_id ][ 'attributes' ] 		= $attr_stamp;
					$stamp[ $bundled_item_id ][ 'variation_id' ] 	= $_REQUEST[ apply_filters( 'woocommerce_product_bundle_field_prefix', '', $product_id ) . 'bundle_variation_id_' . $bundled_item_id ];
				}

				$stamp[ $bundled_item_id ] = apply_filters( 'woocommerce_bundled_item_cart_item_identifier', $stamp[ $bundled_item_id ], $bundled_item_id );

			}

			$cart_item_data[ 'stamp' ] = $stamp;

			// Prepare additional data for later use
			$cart_item_data[ 'bundled_items' ] = array();

			return $cart_item_data;

		} else {

			return $cart_item_data;
		}

	}

	/**
	 * Adds bundled items to the cart.
	 * The 'bundled by' var is added to each item to identify between bundled and non-bundled instances of products.
	 * Important: Recursively calling the core add_to_cart function can lead to issus with the contained action hook: https://core.trac.wordpress.org/ticket/17817.
	 *
	 * @param  string   $item_cart_key      the cart item key
	 * @param  int      $bundle_id          the product id
	 * @param  int      $bundle_quantity    the product quantity
	 * @param  int      $variation_id       the variation id
	 * @param  array    $variation          variation data array
	 * @param  array    $cart_item_data     cart item data array
	 * @return void
	 */
	function woo_bundles_add_bundle_to_cart( $bundle_cart_key, $bundle_id, $bundle_quantity, $variation_id, $variation, $cart_item_data ) {

		if ( isset( $cart_item_data[ 'stamp' ] ) && ! isset( $cart_item_data[ 'bundled_by' ] ) ) {

			// this id is unique, so that bundled and non-bundled versions of the same product will be added separately to the cart.
			$bundled_items_cart_data = array( 'bundled_by' => $bundle_cart_key, 'stamp' => $cart_item_data[ 'stamp' ] );

			// the bundle
			$bundle = WC()->cart->cart_contents[ $bundle_cart_key ][ 'data' ];

			// Now add all items - yay
			foreach ( $cart_item_data[ 'stamp' ] as $bundled_item_id => $bundled_item_stamp ) {

				$bundled_item_cart_data = $bundled_items_cart_data;

				if ( isset( $bundled_item_stamp[ 'optional_selected' ] ) && $bundled_item_stamp[ 'optional_selected' ] === 'no' ) {
					continue;
				}

				if ( absint( $bundled_item_stamp[ 'quantity' ] ) === 0 ) {
					continue;
				}

				// identifier needed for fetching post meta
				$bundled_item_cart_data[ 'bundled_item_id' ] = $bundled_item_id;

				$item_quantity = $bundled_item_stamp[ 'quantity' ];
				$quantity      = $item_quantity * $bundle_quantity ;

				$product_id = $bundled_item_stamp[ 'product_id' ];

				$bundled_product_type = $bundled_item_stamp[ 'type' ];

				if ( $bundled_product_type === 'simple' || $bundled_product_type === 'subscription' ) {

					$variation_id = '';
					$variations   = array();

				} elseif ( $bundled_product_type === 'variable' ) {

					$variation_id = $bundled_item_stamp[ 'variation_id' ];
					$variations   = $bundled_item_stamp[ 'attributes' ];
				}

				// Load child cart item data from the parent cart item data array
				$bundled_item_cart_data = apply_filters( 'woocommerce_bundled_item_cart_data', $bundled_item_cart_data, $cart_item_data );

				// Prepare for adding children to cart
				do_action( 'woocommerce_bundled_item_before_add_to_cart', $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data );

				// Add to cart
				$bundled_item_cart_key = $this->bundled_add_to_cart( $bundle_id, $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data );

				if ( $bundled_item_cart_key && ! in_array( $bundled_item_cart_key, WC()->cart->cart_contents[ $bundle_cart_key ][ 'bundled_items' ] ) ) {
					WC()->cart->cart_contents[ $bundle_cart_key ][ 'bundled_items' ][] = $bundled_item_cart_key;
				}

				// Finish
				do_action( 'woocommerce_bundled_item_after_add_to_cart', $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data );
			}
		}
	}

	/**
	 * Add a bundled product to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
	 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
	 *
	 * @param int          $bundle_id
	 * @param int          $product_id
	 * @param int          $quantity
	 * @param int          $variation_id
	 * @param array        $variation
	 * @param array        $cart_item_data
	 * @return bool
	 */
	public function bundled_add_to_cart( $bundle_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data ) {

		if ( $quantity <= 0 ) {
			return false;
		}

		// Load cart item data when adding to cart
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// Ensure we don't add a variation to the cart directly by variation ID
		if ( 'product_variation' == get_post_type( $product_id ) ) {
			$variation_id = $product_id;
			$product_id   = wp_get_post_parent_id( $variation_id );
		}

		// Get the product
		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by woo_bundles_update_quantity_in_cart.
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			// Add item after merging with $cart_item_data - allow plugins and woo_bundles_add_cart_item_filter to modify cart item
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product_data
			) ), $cart_item_key );

		}

		return $cart_item_key;
	}

	/**
	 * When a bundle is static-priced, the price of all bundled items is set to 0.
	 * When the shipping mode is set to "bundled", all bundled items are marked as virtual when they are added to the cart.
	 * Otherwise, the container itself is a virtual product in the first place.
	 *
	 * @param  array    $cart_item   cart item data
	 * @param  string   $cart_key    cart item key
	 * @return array                 modified cart item data
	 */
	function woo_bundles_add_cart_item_filter( $cart_item, $cart_key ) {

		$cart_contents = WC()->cart->cart_contents;

		if ( isset( $cart_item[ 'bundled_by' ] ) ) {

			$bundle_cart_key = $cart_item[ 'bundled_by' ];

			if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {

				$parent          = $cart_contents[ $bundle_cart_key ][ 'data' ];
				$bundled_item_id = $cart_item[ 'bundled_item_id' ];

				$per_product_pricing  = $parent->is_priced_per_product();
				$per_product_shipping = $parent->is_shipped_per_product();

				if ( $per_product_pricing == false ) {

					$cart_item[ 'data' ]->price = 0;

					if ( isset( $cart_item[ 'data' ]->subscription_sign_up_fee ) ) {
						$cart_item[ 'data' ]->subscription_sign_up_fee = 0;
					}

				} else {

					$discount = $cart_item[ 'stamp' ][ $cart_item[ 'bundled_item_id' ] ][ 'discount' ];

					if ( ! empty( $discount ) || has_filter( 'woocommerce_bundle_is_composited' ) ) {

						$bundled_item = $parent->get_bundled_item( $bundled_item_id );

						$bundled_item->add_price_filters();

						$cart_item[ 'data' ]->price = $cart_item[ 'data' ]->get_price();

						$bundled_item->remove_price_filters();
					}
				}

				if ( $cart_item[ 'data' ]->needs_shipping() ) {

					if ( false === apply_filters( 'woocommerce_bundled_item_shipped_individually', $per_product_shipping, $cart_item[ 'data' ], $bundled_item_id, $parent ) ) {

						if ( apply_filters( 'woocommerce_bundled_item_has_bundled_weight', false, $cart_item[ 'data' ], $bundled_item_id, $parent ) ) {
							$cart_item[ 'data' ]->bundled_weight = $cart_item[ 'data' ]->get_weight();
						}

						$cart_item[ 'data' ]->bundled_value = $cart_item[ 'data' ]->price;
						$cart_item[ 'data' ]->virtual       = 'yes';
					}
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Reload all bundle-related session data in the cart.
	 *
	 * @param  array    $cart_item              cart item data
	 * @param  array    $item_session_values    item session data
	 * @param  array    $cart_item_key          item cart key
	 * @return array                            modified cart item data
	 */
	function woo_bundles_get_cart_data_from_session( $cart_item, $item_session_values, $cart_item_key ) {

		$cart_contents = ! empty( WC()->cart ) ? WC()->cart->cart_contents : '';

		if ( ! isset( $cart_item[ 'bundled_items' ] ) && ! empty( $item_session_values[ 'bundled_items' ] ) ) {
			$cart_item[ 'bundled_items' ] = $item_session_values[ 'bundled_items' ];
		}

		if ( ! isset( $cart_item[ 'stamp' ] ) && isset( $item_session_values[ 'stamp' ] ) ) {
			$cart_item[ 'stamp' ] = $item_session_values[ 'stamp' ];
		}

		if ( isset( $item_session_values[ 'bundled_by' ] ) ) {

			// load 'bundled_by' field
			if ( ! isset( $cart_item[ 'bundled_by' ] ) ) {
				$cart_item[ 'bundled_by' ] = $item_session_values[ 'bundled_by' ];
			}

			// load product bundle post meta identifier
			if ( ! isset( $cart_item[ 'bundled_item_id' ] ) && isset( $item_session_values[ 'bundled_item_id' ] ) ) {
				$cart_item[ 'bundled_item_id' ] = $item_session_values[ 'bundled_item_id' ];
			}

			// now modify item depending on bundle pricing & shipping options
			$bundle_cart_key = $cart_item[ 'bundled_by' ];

			if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {

				$parent          = $cart_contents[ $bundle_cart_key ][ 'data' ];
				$bundled_item_id = $cart_item[ 'bundled_item_id' ];

				if ( $parent->has_bundled_item( $bundled_item_id ) ) {

					$per_product_pricing  = $parent->is_priced_per_product();
					$per_product_shipping = $parent->is_shipped_per_product();

					if ( $per_product_pricing == false ) {

						$cart_item[ 'data' ]->price = 0;

						if ( isset( $cart_item[ 'data' ]->subscription_sign_up_fee ) ) {
							$cart_item[ 'data' ]->subscription_sign_up_fee = 0;
						}

					} else {

						$discount = $cart_item[ 'stamp' ][ $cart_item[ 'bundled_item_id' ] ][ 'discount' ];

						if ( ! empty( $discount ) || has_filter( 'woocommerce_bundle_is_composited' ) ) {

							$bundled_item = $parent->get_bundled_item( $bundled_item_id );

							$bundled_item->add_price_filters();

							$cart_item[ 'data' ]->price = $cart_item[ 'data' ]->get_price();

							$bundled_item->remove_price_filters();

						}
					}

					if ( $cart_item[ 'data' ]->needs_shipping() ) {

						if ( false === apply_filters( 'woocommerce_bundled_item_shipped_individually', $per_product_shipping, $cart_item[ 'data' ], $bundled_item_id, $parent ) ) {

							if ( apply_filters( 'woocommerce_bundled_item_has_bundled_weight', false, $cart_item[ 'data' ], $bundled_item_id, $parent ) ) {
								$cart_item[ 'data' ]->bundled_weight = $cart_item[ 'data' ]->get_weight();
							}

							$cart_item[ 'data' ]->bundled_value = $cart_item[ 'data' ]->price;
							$cart_item[ 'data' ]->virtual       = 'yes';
						}
					}

				} else {

					$cart_item[ 'quantity' ] = 0;
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Bundled items can't be removed individually from the cart - this hides the remove buttons.
	 *
	 * @param  string    $link           remove URL
	 * @param  string    $cart_item_key  the cart item key
	 * @return string                    modified remove link
	 */
	function woo_bundles_cart_item_remove_link( $link, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {

			$bundle_cart_key = WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ];

			if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {
				return '';
			}
		}

		return $link;
	}

	/**
	 * Bundled item quantities can't be changed individually. When adjusting quantity for the container item, the bundled products must follow.
	 *
	 * @param  int      $quantity       quantity of cart item
	 * @param  string   $cart_item_key  cart item key
	 * @return int                      modified quantity
	 */
	function woo_bundles_cart_item_quantity( $quantity, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'stamp' ] ) ) {

			if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {
				return WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ];
			}
		}

		return $quantity;
	}

	/**
	 * Keep quantities between bundled products and container items in sync.
	 *
	 * @param  string   $cart_item_key  the cart item key
	 * @param  integer  $quantity       the item quantity
	 * @return void
	 */
	function woo_bundles_update_quantity_in_cart( $cart_item_key, $quantity = 0 ) {

		if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			if ( $quantity == 0 || $quantity < 0 ) {
				$quantity = 0;
			} else {
				$quantity = WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ];
			}

			if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ][ 'stamp' ] ) && ! isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {

				// unique bundle stamp added to all bundled items & the grouping item
				$stamp = WC()->cart->cart_contents[ $cart_item_key ][ 'stamp' ];

				// change the quantity of all bundled items that belong to the same bundle config
				foreach ( WC()->cart->cart_contents as $key => $value ) {

					if ( isset( $value[ 'bundled_by' ] ) && isset( $value[ 'stamp' ] ) && $cart_item_key == $value[ 'bundled_by' ] && $stamp == $value[ 'stamp' ] ) {

						if ( $value[ 'data' ]->is_sold_individually() && $quantity > 0 ) {
							WC()->cart->set_quantity( $key, 1, false );
						} else {
							$bundle_quantity = $value[ 'stamp' ][ $value[ 'bundled_item_id' ] ][ 'quantity' ];
							WC()->cart->set_quantity( $key, $quantity * $bundle_quantity, false );
						}
					}
				}
			}
		}
	}

	/**
	 * Reinialize cart item data for re-ordering purchased orders.
	 *
	 * @param  array    $cart_item_data     cart item data
	 * @param  array    $order_item         order item data
	 * @param  WC_Order $order              the order
	 * @return array                        modified cart item data
	 */
	function woo_bundles_order_again( $cart_item_data, $order_item, $order ) {

		if ( isset( $order_item[ 'bundled_by' ] ) && isset( $order_item[ 'stamp' ] ) ) {
			$cart_item_data[ 'is_bundled' ] = 'yes';
		}

		if ( isset( $order_item[ 'bundled_items' ] ) && isset( $order_item[ 'stamp' ] ) && ! isset( $order_item[ 'composite_parent' ] ) ) {

			$cart_item_data[ 'stamp' ]         = maybe_unserialize( $order_item[ 'stamp' ] );
			$cart_item_data[ 'bundled_items' ] = array();
		}

		return $cart_item_data;
	}

	/**
	 * Modify the front-end price of bundled items and container items depending on the bundles's pricing strategy.
	 * @param  double   $price          the item price
	 * @param  array    $values         the cart item data
	 * @param  string   $cart_item_key  the cart item key
	 * @return string                   modified subtotal string.
	 */
	function woo_bundles_cart_item_price_html( $price, $values, $cart_item_key ) {

		if ( isset( $values[ 'bundled_by' ] ) ) {

			$bundle_cart_key = $values[ 'bundled_by' ];

			if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {

				if ( ( WC()->cart->cart_contents[ $bundle_cart_key ][ 'data' ]->is_priced_per_product() == false && $values[ 'data' ]->price == 0 ) || isset( WC()->cart->cart_contents[ $bundle_cart_key ][ 'composite_parent' ] ) && $values[ 'data' ]->price == 0 ) {
					return '';
				}
			}
		}

		if ( isset( $values[ 'bundled_items' ] ) ) {

			if ( $values[ 'data' ]->is_priced_per_product() == true && $values[ 'data' ]->get_price() == 0 ) {
				return '';
			}

		}

		return $price;
	}

	/**
	 * Modify the front-end subtotal of bundled items and container items depending on the bundles's pricing strategy.
	 *
	 * @param  string   $subtotal       the item subtotal
	 * @param  array    $values         the item data
	 * @param  string   $cart_item_key  the cart item key
	 * @return string                   modified subtotal string.
	 */
	function woo_bundles_item_subtotal( $subtotal, $values, $cart_item_key ) {

		if ( isset( $values[ 'bundled_by' ] ) ) {

			$bundle_cart_key = $values[ 'bundled_by' ];

			if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {

				if ( ( WC()->cart->cart_contents[ $bundle_cart_key ][ 'data' ]->is_priced_per_product() == false ) || isset( WC()->cart->cart_contents[ $bundle_cart_key ][ 'composite_parent' ] ) ) {
					return '';
				} else {
					return __( 'Subtotal', 'woocommerce-product-bundles' ) . ': ' . $subtotal;
				}
			}
		}

		if ( isset( $values[ 'bundled_items' ] ) ) {

			$bundled_items_price     = 0;
			$contains_recurring_fees = false;
			$bundle_price            = get_option( 'woocommerce_tax_display_cart' ) === 'excl' ? $values[ 'data' ]->get_price_excluding_tax( $values[ 'quantity' ] ) : $values[ 'data' ]->get_price_including_tax( $values[ 'quantity' ] );

			foreach ( $values[ 'bundled_items' ] as $bundled_item_key ) {

				if ( ! isset( WC()->cart->cart_contents[ $bundled_item_key ] ) ) {
					continue;
				}

				$item_values        = WC()->cart->cart_contents[ $bundled_item_key ];
				$item_id            = $item_values[ 'bundled_item_id' ];
				$product            = $item_values[ 'data' ];

				$bundled_item_price = get_option( 'woocommerce_tax_display_cart' ) === 'excl' ? $product->get_price_excluding_tax( $item_values[ 'quantity' ] ) : $product->get_price_including_tax( $item_values[ 'quantity' ] );

				/*------------------------------------------------------------------------------------------------------------------------------------------*/
				/*	If a bundled item is a sub, then add sign up fee
				/*------------------------------------------------------------------------------------------------------------------------------------------*/

				$bundled_item = $values[ 'data' ]->get_bundled_item( $item_id );

				if ( isset( $bundled_item ) && $bundled_item && $bundled_item->is_sub() ) {

					$bundled_items_recurring_price = $bundled_item_price;

					if ( $bundled_items_recurring_price > 0 ) {
						$contains_recurring_fees = true;
					}

					$bundled_item_sign_up_fee  = get_option( 'woocommerce_tax_display_cart' ) === 'excl' ? $product->get_sign_up_fee_excluding_tax( $item_values[ 'quantity' ] ) : $product->get_sign_up_fee_including_tax( $item_values[ 'quantity' ] );
					$bundled_item_prorated_fee = get_option( 'woocommerce_tax_display_cart' ) === 'excl' ? $product->get_price_excluding_tax( $item_values[ 'quantity' ], $bundled_item->get_prorated_price_for_subscription( $product->get_price(), $product ) ) : $product->get_price_including_tax( $item_values[ 'quantity' ], $bundled_item->get_prorated_price_for_subscription( $product->get_price(), $product ) );

					$bundled_item_price = $bundled_item_sign_up_fee + $bundled_item_prorated_fee;
				}

				$bundled_items_price += $bundled_item_price;
			}

			$subtotal = $this->format_product_subtotal( $values[ 'data' ], $bundle_price + $bundled_items_price );

			if ( $contains_recurring_fees && $values[ 'data' ]->is_priced_per_product() ) {
				$subtotal .= __( ' now with recurring fees <small>(see below)</small>', 'woocommerce-product-bundles' );
			}

		}

		return $subtotal;
	}

	/**
	 * Outputs a formatted subtotal ( @see woo_bundles_item_subtotal() ).
	 *
	 * @param  WC_Product   $product    the product
	 * @param  string       $subtotal   formatted subtotal
	 * @return string                   modified formatted subtotal
	 */
	function format_product_subtotal( $product, $subtotal ) {

		$cart = WC()->cart;

		$taxable = $product->is_taxable();

		// Taxable
		if ( $taxable ) {

			if ( $cart->tax_display_cart == 'excl' ) {

				$product_subtotal = wc_price( $subtotal );

				if ( $cart->prices_include_tax && $cart->tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}

			} else {

				$product_subtotal = wc_price( $subtotal );

				if ( ! $cart->prices_include_tax && $cart->tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			}

		// Non-taxable
		} else {
			$product_subtotal = wc_price( $subtotal );
		}

		return $product_subtotal;
	}

	/**
	 * Remove bundled cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  WC_Cart $cart
	 * @return void
	 */
	function woo_bundles_cart_item_removed( $cart_item_key, $cart ) {

		if ( ! empty( $cart->removed_cart_contents[ $cart_item_key ][ 'bundled_items' ] ) ) {

			$bundled_item_cart_keys = $cart->removed_cart_contents[ $cart_item_key ][ 'bundled_items' ];

			foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {

				if ( ! empty( $cart->cart_contents[ $bundled_item_cart_key ] ) ) {

					$remove = $cart->cart_contents[ $bundled_item_cart_key ];
					$cart->removed_cart_contents[ $bundled_item_cart_key ] = $remove;

					unset( $cart->cart_contents[ $bundled_item_cart_key ] );

					do_action( 'woocommerce_cart_item_removed', $bundled_item_cart_key, $cart );
				}
			}
		}
	}

	/**
	 * Restore bundled cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  WC_Cart $cart
	 * @return void
	 */
	function woo_bundles_cart_item_restored( $cart_item_key, $cart ) {

		if ( ! empty( $cart->cart_contents[ $cart_item_key ][ 'bundled_items' ] ) ) {

			$bundled_item_cart_keys = $cart->cart_contents[ $cart_item_key ][ 'bundled_items' ];

			foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {

				if ( ! empty( $cart->removed_cart_contents[ $bundled_item_cart_key ] ) ) {

					$remove = $cart->removed_cart_contents[ $bundled_item_cart_key ];
					$cart->cart_contents[ $bundled_item_cart_key ] = $remove;

					unset( $cart->removed_cart_contents[ $bundled_item_cart_key ] );

					do_action( 'woocommerce_cart_item_restored', $bundled_item_cart_key, $cart );
				}
			}
		}
	}

	/**
	 * Shipping fix - ensure that non-virtual containers/children, which are shipped, have a valid price that can be used for insurance calculations.
	 * Additionally, bundled item weights may have to be added in the container.
	 *
	 * Note: If you charge a static price for the bundle but ship bundled items individually, the only working solution is to spread the total value among the bundled items.
	 *
	 * @param  array  $packages
	 * @return array
	 */
	function woo_bundles_shipping_packages_fix( $packages ) {

		if ( ! empty( $packages ) ) {

			foreach ( $packages as $package_key => $package ) {

				if ( ! empty( $package[ 'contents' ] ) ) {

					foreach ( $package[ 'contents' ] as $cart_item => $cart_item_data ) {

						if ( isset( $cart_item_data[ 'bundled_items' ] ) ) {

							$bundle     = clone $cart_item_data[ 'data' ];
							$bundle_qty = $cart_item_data[ 'quantity' ];

							// Physical container (bundled shipping):
							// - if the container is priced per-item, sum the prices of the children into the parent
							// - optionally, append the weight of the children into the parent

							if ( ! $bundle->is_shipped_per_product() ) {

								$bundled_value  = $bundle->get_price();
								$bundled_weight = 0;

								foreach ( $cart_item_data[ 'bundled_items' ] as $child_item_key ) {

									if ( isset( $package[ 'contents' ][ $child_item_key ] ) ) {

										$bundled_product     = clone $package[ 'contents' ][ $child_item_key ][ 'data' ];
										$bundled_product_qty = $package[ 'contents' ][ $child_item_key ][ 'quantity' ];

										if ( isset( $bundled_product->bundled_value ) ) {
											$bundled_value += $bundled_product->bundled_value * $bundled_product_qty;
											$bundled_product->price = 0;
											$packages[ $package_key ][ 'contents' ][ $child_item_key ][ 'data' ] = $bundled_product;
										}

										if ( isset( $bundled_product->bundled_weight ) ) {
											$bundled_weight += $bundled_product->bundled_weight * $bundled_product_qty;
										}
									}
								}

								$bundle->price  = $bundled_value / $bundle_qty;
								$bundle->weight += $bundled_weight / $bundle_qty;

								if ( isset( $bundle->bundled_weight ) ) {
									$bundle->bundled_weight += $bundled_weight / $bundle_qty;
								}

								if ( isset( $bundle->bundled_value ) ) {
									$bundle->bundled_value += $bundled_value / $bundle_qty;
								}

								$packages[ $package_key ][ 'contents' ][ $cart_item ][ 'data' ] = $bundle;

							// Virtual container (non-bundled shipping enabled) that is priced statically:
							// Distribute the price of the parent uniformly among the children

							} elseif ( $bundle->is_shipped_per_product() && ! $bundle->is_priced_per_product() ) {

								$total_value   = $bundle->get_price() * $bundle_qty;
								$child_count   = 0;
								$bundled_items = array();

								foreach ( $cart_item_data[ 'bundled_items' ] as $child_item_key ) {

									if ( isset( $package[ 'contents' ][ $child_item_key ] ) ) {

										$bundled_product     = $package[ 'contents' ][ $child_item_key ][ 'data' ];
										$bundled_product_qty = $package[ 'contents' ][ $child_item_key ][ 'quantity' ];

										if ( $bundled_product->needs_shipping() ) {
											$child_count += $bundled_product_qty;
											$total_value += $bundled_product->get_price() * $bundled_product_qty;
											$bundled_items[] = $child_item_key;
										}
									}
								}

								foreach ( $bundled_items as $child_item_key ) {

									$bundled_product        = clone $package[ 'contents' ][ $child_item_key ][ 'data' ];
									$bundled_product->price = round( $total_value / $child_count, wc_bundles_get_price_decimals() );

									$packages[ $package_key ][ 'contents' ][ $child_item_key ][ 'data' ] = $bundled_product;
								}

								$bundle->price = 0;
								$packages[ $package_key ][ 'contents' ][ $cart_item ][ 'data' ] = $bundle;
							}
						}
					}
				}
			}
		}

		return $packages;
	}
}
