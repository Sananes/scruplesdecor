<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}

$info_message = '<a href="#" class="showcoupon"><span class="title"><em>' . apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'woocommerce' ) . '</em>' . esc_html__( 'Enter your code &rarr;', 'nm-framework' ) . '</span></a>' );
?>

	<div class="nm-checkout-notice-coupon nm-checkout-notice">
        <?php echo $info_message; ?>
    </div>
	
    <div class="nm-checkout-form-coupon">
        <form class="checkout_coupon" method="post" style="display:none">
            <p class="form-row">
                <input type="text" name="coupon_code" class="input-text" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
            </p>
        
            <p class="form-row">
                <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
            </p>
        </form>
    </div>