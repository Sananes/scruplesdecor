<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

# start: modified by Arlind Nushi
$cart_contents_count = WC()->cart->cart_contents_count;

do_action('laborator_woocommerce_before_wrapper');

?>
<div class="view-cart">

	<div class="row">
		<div class="col-lg-12">
			<div class="white-block block-pad">
				<h1><?php _e('Shopping Cart', 'oxygen'); ?></h1>
				<span><?php echo sprintf(_n('%d item', '%d items', $cart_contents_count, 'oxygen'), $cart_contents_count); ?></span>
			</div>
		</div>
	</div>
	
</div>
<?php
# end: modified by Arlind Nushi

wc_print_notices();

?>

<div class="row">
	<div class="col-lg-12">
		<p class="cart-empty"><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></p>
		
		<?php do_action( 'woocommerce_cart_is_empty' ); ?>
		
		<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php _e( 'Return To Shop', 'woocommerce' ) ?></a></p>
	</div>
</div>

<?php
# start: modified by Arlind Nushi
do_action('laborator_woocommerce_after_wrapper');
# end: modified by Arlind Nushi