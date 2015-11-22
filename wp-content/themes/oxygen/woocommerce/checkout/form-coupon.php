<?php
/**
 * Checkout coupon form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! WC()->cart->coupons_enabled() ) {
	return;
}

$info_message = '<i class="entypo-tag"></i> ' . apply_filters( 'woocommerce_checkout_coupon_message', __( 'Have a coupon?', 'oxygen' ) );
$info_message .= ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>';
?>
<div class="col-md-<?php echo is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ? 12 : 6; ?> checkout-page-coupon">
	
	<div class="white-block checkout-block-padd">
		<?php echo $info_message; ?>
	
	</div>
	
	<form class="checkout_coupon checkout_coupon" method="post" style="display:none">
		
		<div class="input-group">
			<input type="text" name="coupon_code" class="input-text form-control" value="" placeholder="<?php _e( 'Coupon code', 'oxygen' ); ?>">
			
			<span class="input-group-btn">
				<button type="submit" name="apply_coupon" class="btn btn-black"><?php _e('Apply', 'oxygen'); ?></button>
			</span>
		</div>
		
		<div class="clear"></div>
	</form>
</div>