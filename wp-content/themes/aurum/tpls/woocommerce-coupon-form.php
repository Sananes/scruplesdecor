<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

?>
<div class="coupon-env">
	<a class="coupon-enter pull-right-md" href="#">
		<?php _e('Enter Coupon <span>To get discounts</span>', TD); ?>
	</a>

	<div class="coupon">

		<?php if( ! is_cart()): ?>
		<form method="post">
		<?php endif; ?>

		<a href="#" class="close-coupon">&times;</a>

		<input type="text" name="coupon_code" class="input-text form-control" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" />
		<input type="submit" class="button btn btn-primary" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />

		<?php do_action('woocommerce_cart_coupon'); ?>

		<?php if( ! is_cart()): ?>
		</form>
		<?php endif; ?>
	</div>
</div>
