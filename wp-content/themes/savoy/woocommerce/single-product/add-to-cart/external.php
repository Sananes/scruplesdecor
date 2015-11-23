<?php
/**
 * External product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<div class="cart">
	<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="button alt"><?php echo $button_text; ?></a>
</div>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>