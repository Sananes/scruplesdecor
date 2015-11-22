<?php
/**
 * Bundled Item Title Template.
 * Note: bundled product properties accessible from $bundled_item->product .
 *
 * @version 4.9.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $title === '' ) {
	return;
}

?><h4 class="bundled_product_title product_title"><?php
		$quantity = ( $quantity > 1 && $bundled_item->get_quantity( 'max' ) === $quantity ) ? $quantity : '';
		$optional = $optional ? __( 'optional', 'woocommerce-product-bundles' ) : '';
		echo WC_PB_Helpers::format_product_shop_title( $title, $quantity, '', $optional );
?></h4>
