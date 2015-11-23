<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}

$info_message = '
	<a href="#" id="nm-show-login" class="showlogin">
		<span class="title"><em>' . apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'woocommerce' ) ) . '</em> ' . __( 'Login &rarr;', 'nm-framework' ) . '</span>
		<span class="title-close">' . __( '&larr;&nbsp; Return to Checkout', 'nm-framework' ) . '</span>
	</a>';
?>

<div class="nm-checkout-notice-login nm-checkout-notice">
	<?php echo $info_message; ?>
</div>

<div class="nm-checkout-form-login">
	<?php
        woocommerce_login_form(
            array(
                'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
                'redirect' => get_permalink( wc_get_page_id( 'checkout' ) ),
                'hidden'   => true
            )
        );
    ?>
</div>

<div id="nm-checkout-login-overlay" class="nm-page-overlay"></div>
