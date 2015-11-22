<?php
/**
 * Shipstation Integration.
 *
 * @since  4.11.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Shipstation_Compatibility {

	public static function init() {

		// Shipstation compatibility
		add_filter( 'woocommerce_bundles_filter_product_from_item', array( __CLASS__, 'filter_order_data' ), 10, 2 );
		add_filter( 'woocommerce_bundles_filter_order_items_part_of_meta', array( __CLASS__, 'filter_order_data' ), 10, 2 );
	}

	/**
	 * Use the Order API Modifications in WC_PB_Order to return the correct items/weights/values for shipping.
	 *
	 * @param  boolean   $filter
	 * @param  WC_Order  $order
	 * @return boolean
	 */
	public static function filter_order_data( $filter, $order ) {

		global $wp;

		if ( isset( $wp->query_vars[ 'wc-api' ] ) && $wp->query_vars[ 'wc-api' ] === 'wc_shipstation' ) {
			$filter = true;
		}

		return $filter;
	}
}

WC_PB_Shipstation_Compatibility::init();
