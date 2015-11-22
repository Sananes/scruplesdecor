<?php
/**
 * Variable Product Add to Cart
 */
global $woocommerce, $product, $post;

$variation_params = woocommerce_swatches_get_variation_form_args();

do_action( 'woocommerce_before_add_to_cart_form' );
?>
<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" 
      class="variations_form cart swatches" 
      method="post" 
      enctype='multipart/form-data' 
      data-product_id="<?php echo $post->ID; ?>" 
      data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>"
      data-product_attributes="<?php echo esc_attr( json_encode( $variation_params['attributes_renamed'] ) ); ?>"
      data-product_variations_flat="<?php echo esc_attr( json_encode( $variation_params['available_variations_flat'] ) ); ?>"
      data-variations_map="<?php echo esc_attr( json_encode( $variation_params['variations_map'] ) ); ?>"
      >

	<?php if ( !empty( $available_variations ) ) : ?>

		<div class="variation_form_section">
			<?php
			$woocommerce_variation_control_output = new WC_Swatch_Picker( $product->id, $attributes, $variation_params['selected_attributes'] );
			$woocommerce_variation_control_output->picker();
			?>

			<div class="clear"></div><a id="variations_clear" href="#reset" style="display:none;"><?php _e( 'Reset selection', 'woocommerce' ); ?></a>

		</div>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap" style="display:none;">
			<div class="single_variation"></div>
			<div class="variations_button">
				<?php if ( WC_Swatches_Compatibility::is_wc_version_gte_2_1() ) : ?>
					<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
				<?php endif; ?>

				<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
				<input type="hidden" name="variation_id" value="" />

				<?php woocommerce_quantity_input(); ?>
				<button type="submit" class="single_add_to_cart_button button alt"><?php echo apply_filters( 'single_add_to_cart_text', __( 'Add to cart', 'woocommerce' ), $product->product_type ); ?></button>
			</div>
		</div>
		<div>

		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	<?php else : ?>

		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>

	<?php endif; ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
