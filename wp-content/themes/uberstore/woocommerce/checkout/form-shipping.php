<?php
/**
 * Checkout shipping information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php  if ( WC()->cart->needs_shipping_address() === true ) : ?>

	<?php
		if ( empty( $_POST ) ) {

			$ship_to_different_address = get_option( 'woocommerce_ship_to_billing' ) == 'no' ? 1 : 0;
			$ship_to_different_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_different_address );

		} else {

			$ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );

		}
	?>

	<div class="title"><?php _e('Shipping Address', THB_THEME_NAME); ?></div>

	  <div id="shiptobilling">
	  		<input id="ship-to-different-address-checkbox" class="custom_check" <?php checked( $ship_to_different_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
	      <label for="ship-to-different-address-checkbox" class="custom_label"><?php _e( 'Ship to a different address?', THB_THEME_NAME ); ?></label>
	  </div>
	
	  <div class="shipping_address">
	
	      <?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>
	
	      <?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>
	
	          <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
	
	      <?php endforeach; ?>
	
	      <?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>
	
	  </div>
        
    
<?php endif; ?>

<?php do_action('woocommerce_before_order_notes', $checkout); ?>

<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>

	<?php if ( ! WC()->cart->needs_shipping() || WC()->cart->ship_to_billing_address_only() ) : ?>

		<div class="title"><?php _e('Additional Information', THB_THEME_NAME); ?></div>

	<?php endif; ?>
    

	<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>
	
		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>
        

            
	<input type="button" class="btn small black" name="button_address_continue" value="<?php _e('Continue &raquo;', THB_THEME_NAME); ?>" />
        


<?php endif; ?>

<?php do_action('woocommerce_after_order_notes', $checkout); ?>