<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) return;

$info_message  = '<i class="entypo-lock"></i> ' . apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'woocommerce' ) );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'woocommerce' ) . '</a>';

?>

<div class="col-md-6 checkout-login-form">
	
	<div class="white-block checkout-block-padd">
		<?php echo $info_message; ?>
	</div>

	<?php
		woocommerce_login_form (
			array(
				'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'woocommerce' ),
				'redirect' => get_permalink( wc_get_page_id( 'checkout' ) ),
				'hidden'   => true
			)
		);
	?>
	
</div>