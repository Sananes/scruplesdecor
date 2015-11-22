<?php
/**
 * Bundle add-to-cart button template.
 *
 * @version 4.7.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

?><button type="submit" class="single_add_to_cart_button bundle_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>
