<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

?>

<div class="nm-cart-empty">
    <div class="nm-row">
        <div class="col-xs-12">
        
            <?php do_action( 'woocommerce_cart_is_empty' ); ?>
            
            <p class="icon"><i class="nm-font nm-font-close2"></i></p>
            <h1 class="cart-empty"><?php esc_html_e( 'Your cart is currently empty.', 'woocommerce' ); ?></h1>
            <p class="return-to-shop"><a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>"><?php _e( 'Return To Shop', 'woocommerce' ); ?></a></p>
        
        </div>
    </div>
</div>
