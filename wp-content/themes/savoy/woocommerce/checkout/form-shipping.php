<?php
/**
 * Checkout shipping information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce-shipping-fields nm-row">
	<div class="col-md-4 col-xs-12">
		<?php if ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) : ?>
            <h3><?php esc_html_e( 'Additional Information', 'woocommerce' ); ?></h3>
        <?php else : ?>
            <h3><?php esc_html_e( 'Shipping Details', 'nm-framework' ); ?></h3>
        <?php endif; ?>
    </div>
    
    <div class="nm-checkout-form col-md-8 col-xs-12">
	<?php if ( WC()->cart->needs_shipping_address() === true ) : ?>
    
		<?php
			if ( empty( $_POST ) ) {

				$ship_to_different_address = get_option( 'woocommerce_ship_to_destination' ) === 'shipping' ? 1 : 0;
				$ship_to_different_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_different_address );

			} else {

				$ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );

			}
		?>
			
            <div id="ship-to-different-address">
                <input id="ship-to-different-address-checkbox" class="nm-custom-checkbox input-checkbox" <?php checked( $ship_to_different_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
                <label for="ship-to-different-address-checkbox" class="nm-custom-checkbox-label checkbox"><?php _e( 'Ship to a different address?', 'woocommerce' ); ?></label>
            </div>
            
            <div class="shipping_address clearfix">
                <?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>
                
                <?php foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : ?>
    
                    <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
    
                <?php endforeach; ?>
    
                <?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
            </div>
	
	<?php endif; ?>

	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>
    
        <div class="nm-shipping-form-notes">
		<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

        <?php endforeach; ?>
        </div>
	
	<?php endif; ?>
	
	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
    </div>
</div>
