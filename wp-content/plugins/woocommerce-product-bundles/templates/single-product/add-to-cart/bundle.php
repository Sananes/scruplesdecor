<?php
/**
 * Product Bundle add-to-cart template.
 *
 * @version 4.11.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce, $product;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form method="post" enctype="multipart/form-data" class="bundle_form" ><?php

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

	if ( $product->is_purchasable() ) {

		?><div class="cart bundle_data bundle_data_<?php echo $product->id; ?>" data-button_behaviour="<?php echo esc_attr( apply_filters( 'woocommerce_bundles_button_behaviour', 'new', $product ) ); ?>" data-bundle_price_data="<?php echo esc_attr( json_encode( $bundle_price_data ) ); ?>" data-bundle_id="<?php echo $product->id; ?>"><?php

			do_action( 'woocommerce_before_add_to_cart_button' );

			?><div class="bundle_wrap" style="<?php echo apply_filters( 'woocommerce_bundles_button_behaviour', 'new', $product ) == 'new' ? '' : 'display:none'; ?>">
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

							?><input type="hidden" name="bundle_variation_id_<?php echo $bundled_item_id; ?>" class="bundle_variation_id_<?php echo $bundled_item_id; ?>" value="" /><?php

							foreach ( $attributes[ $bundled_item_id ] as $name => $options ) { ?>
								<input type="hidden" name="bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" class="bundle_attribute_<?php echo $bundled_item_id; ?> bundle_attribute_<?php echo sanitize_title( $name ); ?>_<?php echo $bundled_item_id; ?>" value=""><?php
							}
						}
					}

					do_action( 'woocommerce_bundles_add_to_cart_button' );

				?></div>
				<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
			</div>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

		</div><?php

	} else {
		?><div class="bundle_unavailable woocommerce-info"><?php
			echo __( 'This product is currently unavailable.', 'woocommerce-product-bundles' );
		?></div><?php
	}

?></form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
