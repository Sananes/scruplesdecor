<?php
/**
 * Composited Product Bundle Template.
 *
 * @version  4.11.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce, $woocommerce_composite_products;

?><div class="details component_data" data-component_set="" data-price="0" data-regular_price="0" data-product_type="bundle" data-custom="<?php echo esc_attr( json_encode( $custom_data ) ); ?>"><?php

	/**
	 * Composited product details template
	 *
	 * @hooked wc_cp_composited_product_excerpt - 10
	 */
	do_action( 'woocommerce_composited_product_details', $product, $component_id, $composite_product );

	foreach ( $bundled_items as $bundled_item ) {

		?><div class="bundled_product bundled_product_summary product <?php echo $bundled_item->get_classes(); ?>" style="<?php echo ( ! $bundled_item->is_visible() ? 'display:none;' : '' ); ?>" ><?php

			/**
			 * wc_bundles_bundled_item_details hook
			 *
			 * @hooked wc_bundles_bundled_item_thumbnail - 5
			 * @hooked wc_bundles_bundled_item_details_open - 10
			 * @hooked wc_bundles_bundled_item_title - 15
			 * @hooked wc_bundles_bundled_item_description - 20
			 * @hooked wc_bundles_bundled_item_product_details - 25
			 * @hooked wc_bundles_bundled_item_details_close - 100
			 */
			do_action( 'wc_bundles_bundled_item_details', $bundled_item, $product );

		?></div><?php
	}

	?><div class="cart bundle_data bundle_data_<?php echo $product->id; ?>" data-button_behaviour="<?php echo esc_attr( apply_filters( 'woocommerce_bundles_button_behaviour', 'old', $product ) ); ?>" data-bundle_price_data="<?php echo esc_attr( json_encode( $bundle_price_data ) ); ?>" data-bundle_id="<?php echo $product->id; ?>"><?php

		do_action( 'woocommerce_composited_product_add_to_cart', $product, $component_id, $composite_product );

		?><div class="bundle_wrap component_wrap" style="<?php echo apply_filters( 'woocommerce_bundles_button_behaviour', 'old', $product ) == 'new' ? '' : 'display:none'; ?>">
			<div class="bundle_price"></div>
			<div class="bundle_error" style="display:none"><div class="msg woocommerce-info"></div></div><?php

			// Bundle Availability
			$availability = $product->get_availability();

			if ( $availability[ 'availability' ] ) {
				echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . $availability[ 'class' ] . '">' . $availability[ 'availability' ] . '</p>', $availability[ 'availability' ] );
			}

			?><div class="bundle_button"><?php

				foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

					$bundled_item_id = $bundled_item->item_id;
					$bundled_product = $bundled_item->product;

					if ( $bundled_product->product_type === 'variable' ) {

						?><input type="hidden" name="component_<?php echo $component_id; ?>_bundle_variation_id_<?php echo $bundled_item_id; ?>" class="bundle_variation_id_<?php echo $bundled_item_id; ?>" value="" /><?php

						foreach ( $attributes[ $bundled_item_id ] as $name => $options ) { ?>
							<input type="hidden" name="component_<?php echo $component_id; ?>_bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" class="bundle_attribute_<?php echo $bundled_item_id; ?> bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" value=""><?php
						}
					}
				}

				wc_composite_get_template( 'composited-product/quantity.php', array(
					'quantity_min'      => $quantity_min,
					'quantity_max'      => $quantity_max,
					'component_id'      => $component_id,
					'product'           => $product,
					'composite_product' => $composite_product
				), '', $woocommerce_composite_products->plugin_path() . '/templates/' );

			?></div>
		</div>
	</div>
</div>
