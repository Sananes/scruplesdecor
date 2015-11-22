<?php
/**
 * Bundle quantity input template.
 *
 * @version 4.7.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_sold_individually() ) {
	woocommerce_quantity_input( array ( 'min_value' => 1 ) );
} else {
	?><input class="qty" type="hidden" name="quantity" value="1" /><?php
}
