<?php
/**
 * Review order table
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce-checkout-review-order-table nm-row">
    <div class="col-md-4 col-xs-12">
        <h3 id="order_review_heading"><?php _e( 'Your Order', 'nm-framework' ); ?></h3>
    </div>
    
    <div class="col-md-8 col-xs-12">
        <ul class="shop_table">
            <?php
                do_action( 'woocommerce_review_order_before_cart_contents' );
        
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        
                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        ?>
                        <li class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                            <div class="product-thumbnail">
                                <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                            
									echo $thumbnail;
                                ?>
                            </div>
                            <div class="product-details">
                                <div class="col-xs-8 nopad">
                                    <div class="product-name">
										<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
                                        <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
                                    </div>
									
									<?php echo WC()->cart->get_item_data( $cart_item ); ?>
                                </div>
                                <div class="col-xs-4 nopad">
                                	<div class="product-total">
                                    	<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                }
        
                do_action( 'woocommerce_review_order_after_cart_contents' );
            ?>
        </ul>
    
        <div class="cart_totals">
            <ul>
                <li class="cart-subtotal">
                    <div class="col-th col-xs-6"><?php _e( 'Subtotal', 'woocommerce' ); ?></div>
                    <div class="col-td col-xs-6"><?php wc_cart_totals_subtotal_html(); ?></div>
                </li>
            
                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                    <li class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
                        <div class="col-th col-xs-6"><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
                        <div class="col-td col-xs-6"><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
                    </li>
                <?php endforeach; ?>
            
                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            
                    <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
            
                    <?php wc_cart_totals_shipping_html(); ?>
            
                    <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
            
                <?php endif; ?>
            
                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <li class="fee">
                        <div class="col-th col-xs-6"><?php echo esc_html( $fee->name ); ?></div>
                        <div class="col-td col-xs-6"><?php wc_cart_totals_fee_html( $fee ); ?></div>
                    </li>
                <?php endforeach; ?>
            
                <?php if ( WC()->cart->tax_display_cart === 'excl' ) : ?>
                    <?php if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) : ?>
                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                            <li class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                                <div class="col-th col-xs-6"><?php echo esc_html( $tax->label ); ?></div>
                                <div class="col-td col-xs-6"><?php echo wp_kses_post( $tax->formatted_amount ); ?></div>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li class="tax-total">
                            <div class="col-th col-xs-6"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></div>
                            <div class="col-td col-xs-6"><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></div>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            
                <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
            
                <li class="order-total">
                    <div class="col-th col-xs-6"><?php esc_html_e( 'Total', 'woocommerce' ); ?></div>
                    <div class="col-td col-xs-6"><?php wc_cart_totals_order_total_html(); ?></div>
                </li>
            
                <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
            </ul>
        </div>
	</div>
</div>
