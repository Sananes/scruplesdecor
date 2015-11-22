<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php if ( WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

	<div class="title"><?php _e('Billing &amp; Shipping', THB_THEME_NAME); ?></div>

<?php else : ?>

	<div class="title"><?php _e('Billing Address', THB_THEME_NAME); ?></div>

<?php endif; ?>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>
  
<?php foreach ($checkout->checkout_fields['billing'] as $key => $field) : ?>

    <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

<?php endforeach; ?>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
  

          
<input type="button" class="btn small black" name="button_address_continue" value="<?php _e('Continue &raquo;', THB_THEME_NAME); ?>" />