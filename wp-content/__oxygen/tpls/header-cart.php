<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

wp_enqueue_script('owl-carousel');
wp_enqueue_style('owl-carousel-theme');

if( ! get_data('cart_ribbon_show') || ! function_exists('WC'))
	return false;

$cart = array_reverse(WC()->cart->get_cart());
$cart_counter_ajax = get_data('shop_cart_counter_ajax');

if($cart_counter_ajax)
{
	$cart = array();
}
?>
<div class="header-cart">
	<div class="col-md-10 col-sm-9">

		<div class="row cart-items">
			<?php

			if( ! count($cart)):

				?>
				<div class="no-items">
					<?php if($cart_counter_ajax): ?>
						<?php _e('Loading cart contents...', 'oxygen'); ?>
					<?php else: ?>
						<?php _e('Your cart is empty!', 'oxygen'); ?>
					<?php endif; ?>
				</div>
				<?php

			endif;

			foreach($cart as $cart_item_key => $cart_item):

				$product_id = $cart_item['product_id'];
				$product = new WC_Product($product_id);
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$permalink = get_permalink($product->post);

				$quantity = $cart_item['quantity'];

			?>
			<div class="col-sm-3">

				<div class="cart-item">

					<a href="<?php echo $permalink; ?>">
						<?php
						$image = '';

						if(has_post_thumbnail($product_id)):

							#echo laborator_show_img($product_id, 'shop-thumb-2');
							#$image = wp_get_attachment_image(get_post_thumbnail_id($product_id), 'shop-thumb-2');

							if($_product && method_exists($_product, 'get_image'))
								$image = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('shop-thumb-2'), $cart_item, $cart_item_key );
							else
								$image = wp_get_attachment_image(get_post_thumbnail_id($product_id), 'shop-thumb-2');

						else:

							$attachment_ids = $product->get_gallery_attachment_ids();

							if(count($attachment_ids))
							{
								$first_img = reset($attachment_ids);
								$first_img_link = wp_get_attachment_url( $first_img );

								#echo laborator_show_img($first_img_link, 'shop-thumb-2');
								$image = wp_get_attachment_image($first_img, 'shop-thumb-2');
							}
							else
							{
								$image = laborator_show_img(wc_placeholder_img_src(), 'shop-thumb-2');
							}
						endif;

						echo apply_filters('woocommerce_cart_item_thumbnail', $image, $cart_item, $cart_item_key);
						?>
					</a>

					<div class="details">
						<a href="<?php echo $permalink; ?>" class="title"><?php echo get_the_title($product->post); ?></a>

						<div class="price-quantity">
							<?php if ( $price_html = $product->get_price_html() ) : ?>
							<span class="price"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?></span>
							<?php endif; ?>

							<?php
							// Meta data
							echo WC()->cart->get_item_data( $cart_item );
							?>

							<span class="quantity"><?php echo sprintf(__("Q: %d", 'oxygen'), $quantity); ?></span>
						</div>
					</div>
				</div>

			</div>
			<?php
			endforeach;
			?>
		</div>

	</div>

	<div class="col-md-2 col-sm-3">

    	<a class="btn btn-block btn-gray" href="<?php echo WC()->cart->get_cart_url(); ?>">
    		<span class="glyphicon bucket-icon"></span>
    		<?php _e('View Cart', 'oxygen'); ?>
    	</a>

   		<a class="btn btn-block btn-default" href="<?php echo CHECKOUTURL; ?>">
   			<span class="glyphicon cart-icon"></span>
   			<?php _e('Checkout', 'oxygen'); ?>
   		</a>

   		<div class="cart-sub-total">
   			<?php _e('Cart subtotal', 'oxygen'); ?>:
   			<span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
   		</div>

	</div>
</div>

<?php if(count($cart)): ?>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	var $hc = $(".header-cart");

	$hc.show();

	$(".header-cart .cart-items").owlCarousel({
		items: 4,
		navigation: true,
		pagination: false
	});

	$hc.data('height', $hc.outerHeight());

	$hc.hide();
});
</script>
<?php endif; ?>