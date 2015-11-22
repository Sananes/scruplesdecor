<?php
/**
 * Variable Bundled Product Template.
 *
 * @version 4.11.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $bundled_product_variations ) {

	echo __( 'Sorry, this item is not available at the moment.', 'woocommerce-product-bundles' );

} else {

	?><div class="cart bundled_item_cart_content" data-title="<?php echo esc_attr( $bundled_item->get_raw_title() ); ?>" style="<?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'display:none;' : ''; ?>" data-optional="<?php echo $bundled_item->is_optional() ? true : false; ?>" data-type="<?php echo $bundled_product->product_type; ?>" data-product_variations="<?php echo esc_attr( json_encode( $bundled_product_variations ) ); ?>" data-bundled_item_id="<?php echo $bundled_item->item_id; ?>" data-product_id="<?php echo $bundle->id . str_replace( '_', '', $bundled_item->item_id ); ?>" data-bundle_id="<?php echo $bundle->id; ?>">
		<table class="variations" cellspacing="0">
			<tbody><?php

				$attribute_keys = array_keys( $bundled_product_attributes );

				foreach ( $bundled_product_attributes as $attribute_name => $options ) {

					?><tr class="attribute-options" data-attribute_label="<?php echo wc_attribute_label( $attribute_name ); ?>">
						<td class="label">
							<label for="<?php echo sanitize_title( $attribute_name ) . '_' . $bundled_item->item_id; ?>"><?php echo wc_attribute_label( $attribute_name ); ?> <abbr class="required" title="required">*</abbr></label>
						</td>
						<td class="value"><?php
							$selected = isset( $_REQUEST[ $bundle_fields_prefix . 'bundle_attribute_' . sanitize_title( $attribute_name ) . '_' . $bundled_item->item_id ] ) ? wc_clean( $_REQUEST[ $bundle_fields_prefix . 'bundle_attribute_' . sanitize_title( $attribute_name ) . '_' . $bundled_item->item_id ] ) : $bundled_item->get_selected_product_variation_attribute( $attribute_name );
							wc_bundles_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $bundled_product, 'selected' => $selected ) );
							echo end( $attribute_keys ) === $attribute_name ? '<a class="reset_variations" href="#">' . __( 'Clear selection', 'woocommerce' ) . '</a>' : '';
						?></td>
					</tr><?php
				}

			?></tbody>
		</table><?php

		// Compatibility with plugins that normally hook to woocommerce_before_add_to_cart_button
		do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_product->id, $bundled_item );

		?><div class="single_variation_wrap bundled_item_wrap" style="display:none;">
			<div class="single_variation bundled_item_cart_details"></div>
			<div class="variations_button bundled_item_button">
				<input type="hidden" name="variation_id" value="" /><?php

				wc_get_template( 'single-product/bundled-item-quantity.php', array(
						'bundled_item'         => $bundled_item,
						'bundle_fields_prefix' => $bundle_fields_prefix
					), false, WC_PB()->woo_bundles_plugin_path() . '/templates/'
				);

			?></div>
		</div>
	</div><?php
}
