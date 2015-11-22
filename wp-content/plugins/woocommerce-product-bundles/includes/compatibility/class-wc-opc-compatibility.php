<?php
/**
 * One Page Checkout Compatibility.
 *
 * @since  4.11.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_OPC_Compatibility {

	public static function init() {

		// OPC support
		add_action( 'wcopc_bundle_add_to_cart', array( __CLASS__, 'opc_single_add_to_cart_bundle' ) );
		add_filter( 'wcopc_allow_cart_item_modification', array( __CLASS__, 'opc_disallow_bundled_cart_item_modification' ), 10, 4 );
	}

	/**
	 * OPC Single-product bundle-type add-to-cart template
	 *
	 * @param  int  $opc_post_id
	 * @return void
	 */
	public static function opc_single_add_to_cart_bundle( $opc_post_id ) {

		global $product;

		// Enqueue script
		wp_enqueue_script( 'wc-add-to-cart-bundle' );
		wp_enqueue_style( 'wc-bundle-css' );

		if ( $product->is_purchasable() ) {

			ob_start();

			wc_get_template( 'single-product/add-to-cart/bundle.php', array(
				'available_variations' 		=> $product->get_available_bundle_variations(),
				'attributes'   				=> $product->get_bundle_variation_attributes(),
				'selected_attributes' 		=> $product->get_selected_bundle_variation_attributes(),
				'bundle_price_data' 		=> $product->get_bundle_price_data(),
				'bundled_items' 			=> $product->get_bundled_items()
			), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );

			echo str_replace( array( '<form method="post" enctype="multipart/form-data"', '</form>' ), array( '<div', '</div>' ), ob_get_clean() );
		}
	}

	/**
	 * Prevent OPC from managing bundled items.
	 *
	 * @param  bool   $allow
	 * @param  array  $cart_item
	 * @param  string $cart_item_key
	 * @param  string $opc_id
	 * @return bool
	 */
	public static function opc_disallow_bundled_cart_item_modification( $allow, $cart_item, $cart_item_key, $opc_id ) {

		if ( ! empty( $cart_item[ 'bundled_by' ] ) ) {
			return false;
		}

		return $allow;
	}
}

WC_PB_OPC_Compatibility::init();
