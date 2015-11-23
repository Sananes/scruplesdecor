<?php
/**
 * 	NM: The template for including AJAX add-to-cart replacement elements/fragments
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
// Cart contents count
echo nm_get_cart_contents_count();

// Shop notices
nm_print_shop_notices();

// Mini cart
global $nm_globals;
if ( $nm_globals['cart_panel'] ) {
	woocommerce_mini_cart();
}
?>
