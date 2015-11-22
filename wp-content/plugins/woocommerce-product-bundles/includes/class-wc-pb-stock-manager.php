<?php
/**
 * Used to create and store a product_id / variation_id representation of a product collection based on the included items' inventory requirements.
 *
 * @class    WC_PB_Stock_Manager
 * @version  4.8.7
 * @since    4.8.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Stock_Manager {

	private $items;

	public function __construct() {

		$this->items = array();
	}

	/**
	 * Add a product to the collection.
	 *
	 * @param int          $product_id
	 * @param false|int    $variation_id
	 * @param integer      $quantity
	 */
	public function add_item( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->items[] = new WC_PB_Stock_Manager_Item( $product_id, $variation_id, $quantity );
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return array
	 */
	public function get_items() {

		if ( ! empty( $this->items ) ) {
			return $this->items;
		}

		return array();
	}

	/**
	 * Merge another collection with this one.
	 *
	 * @param WC_PB_Stock_Manager  $stock
	 */
	public function add_stock( $stock ) {

		if ( ! is_object( $stock ) ) {
			return false;
		}

		$items_to_add = $stock->get_items();

		if ( ! empty( $items_to_add ) ) {
			foreach ( $items_to_add as $item ) {
				$this->items[] = $item;
			}
			return true;
		}

		return false;
	}

	/**
	 * Return the stock requirements of the items in this collection.
	 * To validate stock accurately, this method is used to add quantities and build a list of product/variation ids to check.
	 * Note that in some cases, stock for a variation might be managed by the parent - this is tracked by the managed_by_id property in WC_PB_Stock_Manager_Item.
	 *
	 * @return array
	 */
	public function get_managed_items() {

		$managed_items = array();

		if ( ! empty( $this->items ) ) {

			foreach ( $this->items as $purchased_item ) {

				$managed_by_id = $purchased_item->managed_by_id;

				if ( isset( $managed_items[ $managed_by_id ] ) ) {

					$managed_items[ $managed_by_id ][ 'quantity' ] += $purchased_item->quantity;

				} else {

					$managed_items[ $managed_by_id ][ 'quantity' ] = $purchased_item->quantity;

					if ( $purchased_item->variation_id && $purchased_item->variation_id == $managed_by_id ) {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = true;
						$managed_items[ $managed_by_id ][ 'product_id' ]   = $purchased_item->product_id;
					} else {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = false;
					}
				}
			}
		}

		return $managed_items;
	}

	/**
	 * Validate that all managed items in the collection are in stock.
	 *
	 * @param  int    $bundle_id
	 * @return boolean
	 */
	public function validate_stock( $bundle_id ) {

		$managed_items = $this->get_managed_items();

		if ( empty( $managed_items ) ) {
			return true;
		}

		// Stock Validation
		foreach ( $managed_items as $managed_item_id => $managed_item ) {

			$quantity = $managed_item[ 'quantity' ];

			// Get the product
			$product_data = wc_get_product( $managed_item_id );

			if ( ! $product_data ) {
				return false;
			}

			// is_sold_individually
			if ( $product_data->sold_individually === 'yes' && $quantity > 1 ) {
				wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart &mdash; only 1 &quot;%2$s&quot; may be purchased.', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title() ), 'error' );
				return false;
			}

			// Stock check - only check if we're managing stock and backorders are not allowed
			if ( ! $product_data->is_in_stock() ) {

				if ( $product_data->product_type === 'variable' ) {
					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because your &quot;%2$s&quot; selection is out of stock.', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title() ), 'error' );
				} else {
					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because &quot;%2$s&quot; is out of stock.', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title() ), 'error' );
				}

				return false;

			} elseif ( ! $product_data->has_enough_stock( $quantity ) ) {

				if ( $product_data->product_type === 'variable' ) {
					wc_add_notice( sprintf(__( '&quot;%1$s&quot; cannot be added to the cart because your &quot;%2$s&quot; selection does not have enough stock (%3$s remaining).', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title(), $product_data->get_stock_quantity() ), 'error' );
				} else {
					wc_add_notice( sprintf(__( '&quot;%1$s&quot; cannot be added to the cart because there is not enough stock of &quot;%2$s&quot; (%3$s remaining).', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title(), $product_data->get_stock_quantity() ), 'error' );
				}

				return false;
			}

			// Stock check - this time accounting for whats already in-cart
			$product_qty_in_cart = WC()->cart->get_cart_item_quantities();

			if ( $product_data->managing_stock() ) {

				// Variations
				if ( $managed_item[ 'is_variation' ] && $product_data->variation_has_stock ) {

					if ( isset( $product_qty_in_cart[ $managed_item_id ] ) && ! $product_data->has_enough_stock( $product_qty_in_cart[ $managed_item_id ] + $quantity ) ) {

						wc_add_notice( sprintf(
							'<a href="%s" class="button wc-forward">%s</a> %s',
							WC()->cart->get_cart_url(),
							__( 'View Cart', 'woocommerce' ),
							sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because the option selected for &quot;%2$s&quot; does not have enough stock &mdash; we have %3$s in stock and you already have %4$s in your cart.', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title(), $product_data->get_stock_quantity(), $product_qty_in_cart[ $managed_item_id ] )
						), 'error' );

						return false;
					}

				// Products
				} else {

					if ( isset( $product_qty_in_cart[ $managed_item_id ] ) && ! $product_data->has_enough_stock( $product_qty_in_cart[ $managed_item_id ] + $quantity ) ) {
						wc_add_notice( sprintf(
							'<a href="%s" class="button wc-forward">%s</a> %s',
							WC()->cart->get_cart_url(),
							__( 'View Cart', 'woocommerce' ),
							sprintf( __( '&quot;%1$s&quot; cannot be added to the cart because there is not enough stock of &quot;%2$s&quot; &mdash; we have %3$s in stock and you already have %4$s in your cart.', 'woocommerce-product-bundles' ), get_the_title( $bundle_id ), $product_data->get_title(), $product_data->get_stock_quantity(), $product_qty_in_cart[ $managed_item_id ] )
						), 'error' );

						return false;
					}
				}
			}
		}

		return true;
	}
}

/**
 * Maps a product/variation in the collection to the item managing stock for it.
 * These 2 will differ only if stock for a variation is managed by its parent.
 *
 * @class    WC_PB_Stock_Manager_Item
 * @version  4.8.7
 * @since    4.8.7
 */
class WC_PB_Stock_Manager_Item {

	public $product_id;
	public $variation_id;
	public $quantity;

	public $managed_by_id;

	public function __construct( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->product_id   = $product_id;
		$this->variation_id = $variation_id;
		$this->quantity     = $quantity;

		if ( $variation_id ) {

			$variation_stock = get_post_meta( $variation_id, '_stock', true );

			// If stock is managed at variation level
			if ( isset( $variation_stock ) && $variation_stock !== '' ) {
				$this->managed_by_id = $variation_id;
			// Otherwise stock is managed by the parent
			} else {
				$this->managed_by_id = $product_id;
			}

		} else {
			$this->managed_by_id = $product_id;
		}
	}
}
