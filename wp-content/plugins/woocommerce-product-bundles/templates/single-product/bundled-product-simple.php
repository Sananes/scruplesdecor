<?php
/**
 * Simple Bundled Product Template.
 *
 * @version 4.9.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="cart" data-title="<?php echo esc_attr( $bundled_item->get_raw_title() ); ?>" data-optional="<?php echo $bundled_item->is_optional() ? true : false; ?>" data-type="<?php echo $bundled_product->product_type; ?>" data-bundled_item_id="<?php echo $bundled_item->item_id; ?>" data-product_id="<?php echo $bundle->id . str_replace( '_', '', $bundled_item->item_id ); ?>" data-bundle_id="<?php echo $bundle->id; ?>">
	<div class="bundled_item_wrap">
		<div class="bundled_item_cart_content" style="<?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'display:none;' : ''; ?>">
			<div class="bundled_item_cart_details"><?php

				if ( ! $bundled_item->is_optional() ) {
					wc_get_template( 'single-product/bundled-item-price.php', array(
						'bundled_item' => $bundled_item ), false, WC_PB()->woo_bundles_plugin_path() . '/templates/'
					);
				}

				if ( $availability[ 'availability' ] ) {
					echo apply_filters( 'woocommerce_stock_html', '<p class="stock '. $availability[ 'class' ] .'">' . $availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );
				}

				// Compatibility with plugins that normally hook to woocommerce_before_add_to_cart_button
				do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_product->id, $bundled_item );

			?></div>
			<div class="bundled_item_button"><?php

				wc_get_template( 'single-product/bundled-item-quantity.php', array(
						'bundled_item'         => $bundled_item,
						'bundle_fields_prefix' => $bundle_fields_prefix
					), false, WC_PB()->woo_bundles_plugin_path() . '/templates/'
				);

			?></div>
		</div>
	</div>
</div>
