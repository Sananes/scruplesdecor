<?php
/**
 * Single Product Sale Flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;

	# start: modified by Arlind Nushi
	if($product->is_featured() && get_data('shop_featured_product_ribbon_show')):

		?>
		<div class="ribbon product-featured">
			<div class="ribbon-content">
				<?php echo apply_filters( 'woocommerce_featured_flash', '<span class="featured">' . __( 'Featured', 'oxygen' ) . '</span>', $post, $product ); ?>
			</div>
		</div>
		<?php

	elseif($product->is_in_stock() == false):

		?>
		<div class="ribbon out-of-stock">
			<div class="ribbon-content">
				<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="outofstock">' . __( 'Out of Stock', 'woocommerce' ) . '</span>', $post, $product ); ?>
			</div>
		</div>
		<?php


	elseif ( get_data('shop_single_sale_ribbon_show') && $product->is_on_sale() ) : ?>

	<div class="ribbon">
		<div class="ribbon-content">
			<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . __( 'Sale', 'oxygen' ) . '</span>', $post, $product ); ?>
		</div>
	</div>

	<?php

# end: modified by Arlind Nushi
?>
<?php endif; ?>