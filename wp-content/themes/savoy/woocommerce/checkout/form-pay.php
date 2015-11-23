<?php
/**
 * Pay for order form
 *
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form id="order_review" method="post">
	<div class="nm-myaccount-checkout nm-checkout clearfix">
        <div class="nm-row">
            <div class="col-md-4 col-xs-12">
                <h3 id="order_review_heading"><?php esc_html_e( 'Unpaid Order', 'nm-framework' ); ?></h3>
            </div>
            
            <div class="col-md-8 col-xs-12">
                <ul class="shop_table">
                    <?php
                    if ( sizeof( $order->get_items() ) > 0 ) :
                        foreach ( $order->get_items() as $item ) :
                        	
							// Product thumbnail
                            if ( has_post_thumbnail( $item['product_id'] ) ) {
                                $thumbnail = get_the_post_thumbnail( $item['product_id'], 'shop_thumbnail', array() );
							} else {
                                $thumbnail = '';
							}
                            
                            echo '
                                <li>
                                    <div class="product-thumbnail">' . $thumbnail . '</div>
                                    <div class="product-details">
                                        <div class="col-xs-8 nopad">
                                            <div class="product-name">' . $item['name'] . ' <strong class="product-quantity">&times; ' . $item['qty'] . '</strong></div>
                                        </div>
                                        <div class="col-xs-4 nopad">
                                            <div class="product-subtotal">' . $order->get_formatted_line_subtotal( $item ) . '</div>
                                        </div>
                                    </div<
                                </li>';
                        endforeach;
                    endif;
                    ?>
                </ul>
                
                <div class="cart_totals">
                    <ul>
                    <?php
                        if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
                            ?>
                            <li>
                                <div class="col-th col-xs-6"><?php echo $total['label']; ?></div>
                                <div class="product-total col-td col-xs-6"><?php echo $total['value']; ?></div>
                            </li>
                            <?php
                        endforeach;
                    ?>
                    </ul>
                </div>
                
            </div>
		</div>
		    
        <div id="payment" class="nm-row">
        	<div class="col-md-4 col-xs-12">
                <h3><?php _e( 'Payment', 'woocommerce' ); ?></h3>
            </div>
            
			<div class="col-md-8 col-xs-12">
            	<?php if ( $order->needs_payment() ) : ?>
                <ul class="payment_methods methods">
                    <?php
                        if ( $available_gateways = WC()->payment_gateways->get_available_payment_gateways() ) {
                            // Chosen Method
                            if ( sizeof( $available_gateways ) ) {
                                current( $available_gateways )->set_current();
							}
        
                            foreach ( $available_gateways as $gateway ) {
                                ?>
                                <li class="payment_method_<?php echo $gateway->id; ?>">
                                    <div class="nm-payment-title">
                                        <input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="nm-custom-radio input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
                                        <label for="payment_method_<?php echo $gateway->id; ?>" class="nm-custom-radio-label"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
                                    </div>
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
        
                <div class="place-order">
                    <?php wp_nonce_field( 'woocommerce-pay' ); ?>
                    <?php
                        $pay_order_button_text = apply_filters( 'woocommerce_pay_order_button_text', __( 'Pay for order', 'woocommerce' ) );
                        
                        echo apply_filters( 'woocommerce_pay_order_button_html', '<input type="submit" class="button alt" id="place_order" value="' . esc_attr( $pay_order_button_text ) . '" data-value="' . esc_attr( $pay_order_button_text ) . '" />' );
                    ?>			
                    <input type="hidden" name="woocommerce_pay" value="1" />
                </div>
		
			</div>
		</div>
	</div>
</form>
