<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( sizeof( WC()->cart->get_cart() ) == 0 ) {
	$empty_class = ' class="show"';
	$has_products = false;
} else {
	$empty_class = '';
	$has_products = true;
}
?>
<div id="nm-mini-cart" class="nm-mini-cart">
    <div id="nm-mini-cart-loader"><h5 class="nm-loader"><?php esc_html_e( 'Updating ..', 'nm-framework' ); ?></h5></div>
    
    <div class="widget_shopping_cart_content">
		<?php do_action( 'woocommerce_before_mini_cart' ); ?>
        
        <div id="nm-mini-cart-inner-empty"<?php echo $empty_class; ?>>
            <div class="nm-mini-cart-empty-notice"><?php esc_html_e( 'Your cart is currently empty.', 'woocommerce' ); ?></div>
            
            <div class="nm-mini-cart-summary">
                <div class="buttons">
                    <a href="#" id="nm-mini-cart-empty-continue" class="button view-cart"><?php esc_html_e( 'Continue Shopping', 'nm-framework' ); ?></a>
                </div>
            </div>
        </div>
        
        <?php if ( $has_products ) : ?>
        
        <div id="nm-mini-cart-inner">
            <ul id="nm-mini-cart-list" class="cart_list <?php echo $args['list_class']; ?>">
                <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
            
                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
            
                            $product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
                            $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                            $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                            
                            // NM
                            $thumbnail = str_replace( array( 'http:', 'https:' ), '', $thumbnail );
                            if ( $_product->is_visible() ) {
                                $esc_permalink = esc_url( get_permalink( $product_id ) );
                                $thumbnail = '<a href="' . $esc_permalink . '">' . $thumbnail . '</a>';
                                $product_name = '<a href="' . $esc_permalink . '" class="product-title">' . $product_name . '</a>';
                            } else {
								$product_name = '<span class="product-title">' . esc_html( $product_name ) . '</span>';
							}
                            
                            ?>
                            <li>
                                <div class="product-image-wrap">
                                    <?php echo $thumbnail; ?>
                                </div>
                                <div class="product-details-wrap">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s" data-cart-item-key="%s"><i class="nm-font nm-font-close2"></i></a>', 
                                            esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
											__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() ),
											$cart_item_key
                                        ), $cart_item_key );
                                    ?>
                                    
                                    <?php echo $product_name; ?>
                                    
                                    <?php
                                        // Variations/meta: 
                                        echo WC()->cart->get_item_data( $cart_item );
                                    ?>
                                    
                                    <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . esc_html__( 'Qty:', 'nm-framework' ) . ' ' . $cart_item['quantity'] . '</span>', $cart_item, $cart_item_key ); ?>
                                    
                                    <?php echo $product_price; ?>
                                </div>
                            </li>
                            <?php
                        }
                    }
                ?>
            </ul><!-- end product list -->
        
            <div class="nm-mini-cart-summary">
                <div class="total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?></strong> <span id="nm-mini-cart-subtotal" class="nm-mini-cart-subtotal"><?php echo WC()->cart->get_cart_subtotal(); ?></span></div>
            
                <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>
            
                <div class="buttons">
                    <a href="<?php echo WC()->cart->get_cart_url(); ?>" class="button view-cart wc-forward"><?php _e( 'Edit Cart', 'nm-framework' ); ?></a>
                    <a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="button checkout wc-forward"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
                </div>
            </div>
        </div>
    
        <?php endif; ?>
    
        <?php do_action( 'woocommerce_after_mini_cart' ); ?>
    </div> <!-- WooCommerce AJAX div -->
</div>
