<?php
/**
 * Pay for order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

# start: modified by Arlind Nushi
wp_enqueue_script(array('icheck'));
wp_enqueue_style(array('icheck'));

do_action('laborator_woocommerce_before_wrapper');
# end: modified by Arlind Nushi

?>
<form id="order_review" method="post" class="cart-env checkout-cart-env">

<div class="row">
	<div class="col-md-6">
	
		<div class="white-block block-pad">		
			
			<h4 id="order_review_heading" class="with-divider"><?php _e( 'Your order', 'woocommerce' ); ?></h4>
		
			<ul class="cart-totals">
				
				<?php
				if ( sizeof( $order->get_items() ) > 0 ) :
					foreach ( $order->get_items() as $cart_item_key => $item ) :
						
						# start: modified by Arlind Nushi
						$quantity = apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf(__('Quantity: %d', 'oxygen'), $item['qty']) . '</strong>', $item, $cart_item_key );
						# end: modified by Arlind Nushi
						echo '
							<li>
								<div class="name product-name">' . $item['name']. $quantity .'</div>
								<div class="value product-subtotal">' . $order->get_formatted_line_subtotal( $item ) . '</div>
							</li>';
					endforeach;
				endif;
				?>
				
				<?php
					if ( $totals = $order->get_order_item_totals() ) $i = 0; $l = count($totals) - 1; foreach ( $totals as $total ) :
						?>
						<li class="<?php echo $i == 0 ? 'subtotal' : ($i == $l ? 'order-total' : ''); ?>">
							<div class="name"><?php echo $total['label']; ?></div>
							<div class="value product-total"><?php echo $total['value']; ?></div>
						</li>
						<?php
						$i++;
					endforeach;
				?>
			</ul>
			
		</div>
	
	</div>
	
	<div class="col-md-6">
	
		<div class="white-block block-pad">
		
			<div id="payment">
				<?php if ( $order->needs_payment() ) : ?>
				<h4 class="with-divider"><?php _e( 'Payment', 'woocommerce' ); ?></h4>
				<ul class="payment_methods methods">
					<?php
						if ( $available_gateways = WC()->payment_gateways->get_available_payment_gateways() ) {
							// Chosen Method
							if ( sizeof( $available_gateways ) )
								current( $available_gateways )->set_current();
		
							foreach ( $available_gateways as $gateway ) {
								?>
								<li class="payment_method_<?php echo $gateway->id; ?>">
									<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
									<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
									<?php
										if ( $gateway->has_fields() || $gateway->get_description() ) {
											echo '<div class="payment_box payment_method_' . $gateway->id . '" style="display:none;">';
											$gateway->payment_fields();
											echo '</div>';
										}
									?>
								</li>
								<?php
							}
						} else {
		
							echo '<p>' . __( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';
		
						}
					?>
				</ul>
				<?php endif; ?>
		
				
			</div>
		
			
		</div>
	
		<div class="form-row">
			<?php wp_nonce_field( 'woocommerce-pay' ); ?>
			<?php
				$pay_order_button_text = apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'woocommerce' ) );
				
				echo apply_filters( 'woocommerce_pay_order_button_html', '<input type="submit" class="button alt fluid-dark-button" id="place_order" value="' . esc_attr( $pay_order_button_text ) . '" data-value="' . esc_attr( $pay_order_button_text ) . '" />' );
			?>			
			<input type="hidden" name="woocommerce_pay" value="1" />
		</div>
	</div>

</div>
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		var $payments = $(".payment_methods.methods input");
		
		$payments.on('ifChecked', function()
		{
			var $this = $(this);
			
			$payments.not($this).each(function(i, el)
			{
				var $pb = $(el).closest('li').find('.payment_box');
				
				$pb.slideUp(350);
			});
			
			$this.closest('li').find('.payment_box').slideDown(350);
		});
	});
</script>
</form>
<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi
?>