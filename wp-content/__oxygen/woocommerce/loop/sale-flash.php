<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;

	# start: modified by Arlind Nushi
	if($product->is_featured() && get_data('shop_featured_product_ribbon_show')):

		echo apply_filters(
			'woocommerce_featured_flash',
			'<div class="sale_tag product-featured">
				<div class="ribbon">
					<strong class="ribbon-content">
						<span>' . __( 'Featured', 'oxygen' ) . '</span>
					</strong>
				</div>
			</div>', $post, $product
		);

	elseif(get_data('shop_sale_ribbon_show') && $product->is_in_stock() == false):

		?>
		<div class="sale_tag stock-out">
			<div class="ribbon">
				<strong class="ribbon-content">
					<span><?php _e( 'Out of Stock', 'woocommerce' ) ; ?></span>
				</strong>
			</div>
		</div>
		<?php

	elseif (get_data('shop_sale_ribbon_show') && $product->is_on_sale()) : ?>

	<?php echo apply_filters(
		'woocommerce_sale_flash',
		'<div class="sale_tag">
			<div class="ribbon">
				<strong class="ribbon-content">
					<span>' . __( 'Sale', 'woocommerce' ) . '</span>
				</strong>
			</div>
		</div>', $post, $product
	); ?>

<?php

# end: modified by Arlind Nushi
?>
<?php endif; ?>