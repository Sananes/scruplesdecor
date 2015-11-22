<?php
/**
 * Product Bundles Template Functions.
 *
 * @version  4.11.4
 * @since    4.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------- */
/* Product Bundles single product template functions
/* -------------------------------------------------------- */

/**
 * Add-to-cart template for Product Bundles.
 *
 * @return void
 */
function wc_bundles_add_to_cart() {

	global $product, $post;

	// Enqueue variation scripts
	wp_enqueue_script( 'wc-add-to-cart-bundle' );

	wp_enqueue_style( 'wc-bundle-css' );

	$bundled_items = $product->get_bundled_items();

	if ( $bundled_items ) {
		wc_get_template( 'single-product/add-to-cart/bundle.php', array(
			'available_variations' 		=> $product->get_available_bundle_variations(),
			'attributes'   				=> $product->get_bundle_variation_attributes(),
			'selected_attributes' 		=> $product->get_selected_bundle_variation_attributes(),
			'bundle_price_data' 		=> $product->get_bundle_price_data(),
			'bundled_items' 			=> $bundled_items
		), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
	}
}

/**
 * Add-to-cart button and quantity template for Product Bundles.
 *
 * @return void
 */
function wc_bundles_add_to_cart_button() {

	wc_get_template( 'single-product/add-to-cart/bundle-quantity-input.php', array(), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
	wc_get_template( 'single-product/add-to-cart/bundle-button.php', array(), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
}

/**
 * Load the bundled item title template.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_title( $bundled_item, $bundle ) {

	wc_get_template( 'single-product/bundled-item-title.php', array(
		'quantity'     => $bundled_item->get_quantity(),
		'title'        => $bundled_item->get_title(),
		'optional'     => $bundled_item->is_optional(),
		'bundled_item' => $bundled_item,
	), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
}

/**
 * Load the bundled item thumbnail template.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_thumbnail( $bundled_item, $bundle ) {

	if ( $bundled_item->is_visible() ) {
		if ( $bundled_item->is_thumbnail_visible() ) {
			$bundled_product = $bundled_item->product;
			wc_get_template( 'single-product/bundled-item-image.php', array( 'post_id' => $bundled_product->id ), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
		}
	}
}

/**
 * Load the bundled item short description template.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_description( $bundled_item, $bundle ) {

	wc_get_template( 'single-product/bundled-item-description.php', array(
		'description' => $bundled_item->get_description()
	), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
}

/**
 * Add a 'details' container div.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_details_open( $bundled_item, $bundle ) {
	echo '<div class="details">';
}

/**
 * Close the 'details' container div.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_details_close( $bundled_item, $bundle ) {
	echo '</div>';
}

/**
 * Display bundled product details templates.
 *
 * @param  WC_Bundled_Item   $bundled_item
 * @param  WC_Product_Bundle $bundle
 * @return void
 */
function wc_bundles_bundled_item_product_details( $bundled_item, $bundle ) {

	if ( $bundled_item->is_purchasable() ) {

		$bundled_product = $bundled_item->product;
		$availability    = $bundled_item->get_availability();

		$bundled_item->add_price_filters();

		if ( $bundled_item->is_optional() ) {

			// Optional checkbox template
			wc_get_template( 'single-product/bundled-item-optional.php', array(
				'quantity'             => $bundled_item->get_quantity(),
				'bundled_item'         => $bundled_item,
				'bundle_fields_prefix' => apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle->id )
			), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
		}

		if ( $bundled_product->product_type === 'simple' || $bundled_product->product_type === 'subscription' ) {

			// Simple Product template
			wc_get_template( 'single-product/bundled-product-simple.php', array(
				'bundled_product'      => $bundled_product,
				'bundled_item'         => $bundled_item,
				'bundle'               => $bundle,
				'bundle_fields_prefix' => apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle->id ),
				'availability'         => $availability
			), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );

		} elseif ( $bundled_product->product_type === 'variable' ) {

			// Variable Product template
			wc_get_template( 'single-product/bundled-product-variable.php', array(
				'bundled_product'                     => $bundled_product,
				'bundled_item'                        => $bundled_item,
				'bundle'                              => $bundle,
				'bundle_fields_prefix'                => apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle->id ),
				'availability'                        => $availability,
				'bundled_product_attributes'          => $bundled_item->get_product_variation_attributes(),
				'bundled_product_variations'          => $bundled_item->get_product_variations(),
				'bundled_product_selected_attributes' => $bundled_item->get_selected_product_variation_attributes()
			), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
		}

		$bundled_item->remove_price_filters();

	} else {
		echo __( 'Sorry, this item is not available at the moment.', 'woocommerce-product-bundles' );
	}

}
