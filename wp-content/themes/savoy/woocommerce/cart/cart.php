<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Action: woocommerce_cart_collaterals
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

?>

<div class="nm-cart">

<?php 
	wc_print_notices();
	
	do_action( 'woocommerce_before_cart' );
?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

    <div class="nm-row">
        <div class="col-md-4 col-xs-12">
            <h2><?php esc_html_e( 'Shopping Cart', 'nm-framework' ); ?></h2>
        </div>
        
        <div id="nm-cart-product-summary" class="product-summary col-md-8 col-xs-12">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>
            
            <ul class="shop_table cart">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>
        
                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
        
                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        ?>
                        <li class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
        
                            <div class="product-thumbnail">
                                <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
        
                                    if ( ! $_product->is_visible() ) {
                                        echo $thumbnail;
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
									}
                                ?>
                            </div>
        					
                            <div class="product-details">
                            	<?php
									echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove invert-color" title="%s" data-product_id="%s" data-product_sku="%s" data-cart-item-key="%s"><i class="nm-font nm-font-close2"></i></a>',
										esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
										__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() ),
										$cart_item_key
									), $cart_item_key );
								?>
                                
                                <div class="product-name">
									<?php
										if ( ! $_product->is_visible() ) {
											echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
										} else {
											echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key );
										}
                                    ?>
                                </div>                                
                                 
                                <div class="product-quantity-pricing">
									<div class="product-quantity">
										<?php
                                            if ( $_product->is_sold_individually() ) {
                                                $product_quantity = sprintf( '<span>%s:</span> 1 <input type="hidden" name="cart[%s][qty]" value="1" />', esc_html__( 'Qty', 'nm-framework' ), $cart_item_key );
                                            } else {
                                                $product_quantity = woocommerce_quantity_input( array(
                                                    'input_name'  => "cart[{$cart_item_key}][qty]",
                                                    'input_value' => $cart_item['quantity'],
                                                    'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                                    'min_value'   => '0'
                                                ), $_product, false );
                                            }
                                            
                                            echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
                                        ?>
                                    </div>
                                   	
                                    <div class="product-price">
                                    	<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                                    </div>
                                </div>
                                
								<?php
                                    // Meta data
                                    echo WC()->cart->get_item_data( $cart_item );
                                    
                                    // Backorder notification
									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                        echo '<div class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</div>';
									}
                                ?>
                            </div>
                            
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            
            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </div>
    
    </div>
    
    <div class="nm-cart-summary-wrap nm-row">
        <div class="col-md-4 col-xs-12">
            <h2><?php esc_html_e( 'Summary', 'nm-framework' ); ?></h2>
        </div>
        
        <div class="col-md-8 col-xs-12">
            <ul class="nm-cart-summary">
            	<?php if ( WC()->cart->coupons_enabled() ) : ?>
                <li class="coupon-wrap">
                    <div class="nm-coupon">
                        <label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?></label>
                        <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Enter coupon code', 'nm-framework' ); ?>" />
                        <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply', 'nm-framework' ); ?>" />
                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                    </div>
                </li>
                <?php endif; ?>
                <li class="totals-wrap">
                    <?php do_action( 'woocommerce_cart_collaterals' ); ?>
                </li>
                <li class="actions-wrap">
                    <input type="submit" class="button" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" />
                    <?php do_action( 'woocommerce_cart_actions' ); ?>
                    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
                    
					<?php wp_nonce_field( 'woocommerce-cart' ); ?>
                </li>
            </ul>
    
            <?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </div>
    </div>
    
    <div class="nm-row">
    	<div class="col-xs-12">
            <?php woocommerce_cross_sell_display(); ?>
        </div>
    </div>

</form>

<?php do_action( 'woocommerce_after_cart' ); ?>

</div>
