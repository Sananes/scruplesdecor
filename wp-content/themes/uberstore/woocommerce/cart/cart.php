<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

wc_print_notices(); 
?>
<?php do_action( 'woocommerce_before_cart' ); ?>
<div class="row">
	<div class="nine columns">
	<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>
		
			<table class="shopping_bag cart" cellspacing="0">
			<thead>
				<tr>
					<th class="product-remove">&nbsp;</th>
					<th class="product-thumbnail">&nbsp;</th>
					<th class="product-name"><?php _e( 'Product', THB_THEME_NAME ); ?></th>
					<th class="product-price"><?php _e( 'Price', THB_THEME_NAME ); ?></th>
					<th class="product-quantity"><?php _e( 'Quantity', THB_THEME_NAME ); ?></th>
					<th class="product-subtotal"><?php _e( 'SubTotal', THB_THEME_NAME ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>
		
				<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				
							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								?>
							<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
								<!-- Remove from cart link -->
								<td class="product-remove">
									<?php
										echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', THB_THEME_NAME ) ), $cart_item_key );
									?>
								</td>
		
								<!-- The thumbnail -->
								<td class="product-thumbnail">
									<?php
										$image_id = get_post_thumbnail_id($product_id);
										$image_link = wp_get_attachment_image_src($image_id,'full');
										$image = aq_resize( $image_link[0], 100, 105, true, false);
										$image_title = esc_attr( get_the_title($product_id) );
										
										$image_src = '<img  src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" title="'.$image_title.'" />';
									
										$thumbnail = apply_filters( 'woocommerce_in_cart_product_thumbnail', $image_src, $cart_item, $cart_item_key );
		
										if ( ! $_product->is_visible() || ( ! empty( $_product->variation_id ) && ! $_product->parent_is_visible() ) )
											echo $thumbnail;
										else
											printf('<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
											
									?>
								</td>
		
								<!-- Product Name -->
								<td class="product-name">
									<?php
										if ( ! $_product->is_visible() )
											echo apply_filters( 'woocommerce_cart_item_name', '<h6>'.$_product->get_title().'</h6>', $cart_item, $cart_item_key );
										else
											echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<h6><a href="%s">%s</a></h6>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );
			
										// Meta data
										echo WC()->cart->get_item_data( $cart_item );
			
			               				// Backorder notification
			               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
			               					echo '<p class="backorder_notification">' . __( 'Available on backorder', THB_THEME_NAME ) . '</p>';
									?>
								</td>
		
								<!-- Product price -->
								<td class="product-price">
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
									?>
								</td>
		
								<!-- Quantity inputs -->
								<td class="product-quantity">
									<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
										} else {
											$product_quantity = woocommerce_quantity_input( array(
												'input_name'  => "cart[{$cart_item_key}][qty]",
												'input_value' => $cart_item['quantity'],
												'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
											), $_product, false );
										}
			
										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
									?>
								</td>
		
								<!-- Product subtotal -->
								<td class="product-subtotal">
									<?php
										echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
									?>
								</td>
							</tr>
							<?php
					}
				}
		
				do_action( 'woocommerce_cart_contents' );
				?>
				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</tbody>
		</table>
			<div class="row actions">
				<div class="six columns coupon">
					<?php if ( WC()->cart->coupons_enabled() ) { ?>
						<div class="row">
							<div class="nine columns">
								<input type="text" name="coupon_code" id="coupon_code" value="" placeholder="<?php _e( 'Enter Coupon', THB_THEME_NAME ); ?>"/>
							</div>
							<div class="three columns">
								<input type="submit" class="btn small black" name="apply_coupon" value="<?php _e( 'Apply Coupon', THB_THEME_NAME ); ?>" />
							</div>
						</div>
						<?php do_action('woocommerce_cart_coupon'); ?>
					<?php } ?>
				</div>
				<div class="six columns shoppingbag-buttons">
					<div class="row">
						<div class="seven mobile-two columns">
							<input type="submit" class="button small grey right" name="update_cart" value="<?php _e( 'Update Cart', THB_THEME_NAME ); ?>" />
						</div>
						<div class="five mobile-two columns">
							<input type="submit" class="checkout-button button right small wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout', THB_THEME_NAME ); ?>" />
							<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
							<?php wp_nonce_field( 'woocommerce-cart' ); ?>
						</div>
					</div>
				</div>
			</div>
			
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>
	<?php do_action('woocommerce_cart_collaterals'); ?>
	</div>
	<aside class="sidebar woo three columns cart-collaterals">
		
		<?php woocommerce_cart_totals(); ?>
		<?php woocommerce_shipping_calculator(); ?>
	</aside>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>