<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce-billing-fields nm-row">
	<div class="col-md-4 col-xs-12">
		<?php if ( WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>
    
            <h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>
    
        <?php else : ?>
    
            <h3><?php _e( 'Billing Details', 'woocommerce' ); ?></h3>
    
        <?php endif; ?>
	</div>
    
    <div class="nm-checkout-form col-md-8 col-xs-12">
		<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
    
        <?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>
    
            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
    
        <?php endforeach; ?>
    
        <?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>
    
        <?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>
    
            <?php if ( $checkout->enable_guest_checkout ) : ?>
    
                <p class="create-account">
                    <input class="nm-custom-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" />
                    <label for="createaccount" class="nm-custom-checkbox-label checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
                </p>
    
            <?php endif; ?>
    
            <?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>
    
            <?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>
    
                <div class="create-account">
    
                    <span><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></span>
    
                    <?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>
    
                        <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
    
                    <?php endforeach; ?>
    
                </div>
    
            <?php endif; ?>
    
            <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
    
        <?php endif; ?>
    </div>
</div>
