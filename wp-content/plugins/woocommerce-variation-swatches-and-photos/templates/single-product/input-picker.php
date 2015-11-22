<div 
	data-attribute-name="<?php echo 'attribute_' . $st_name; ?>"
	data-value="<?php echo!empty( $selected_value ) ? md5( $selected_value ) : ''; ?>"
	id="<?php echo esc_attr( $st_name ); ?>" 
	class="select attribute_<?php echo $st_name; ?>_picker">

	<input type="hidden" name="<?php echo 'attribute_' . $st_name; ?>" id="<?php echo 'attribute_' . $st_name; ?>" value="<?php echo $selected_value; ?>" />

	<?php if ( is_array( $options ) ) : ?>
		<?php
		// Get terms if this is a taxonomy - ordered
		if ( taxonomy_exists( $taxonomy_lookup_name ) ) :
			$args = array('menu_order' => 'ASC', 'hide_empty' => false);
			$terms = get_terms( $taxonomy_lookup_name, $args );

			foreach ( $terms as $term ) :

				if ( !in_array( $term->slug, $options ) ) {
					continue;
				}


				if ( $picker->swatch_type_options[$lookup_name]['type'] == 'term_options' ) {
					$size = apply_filters( 'woocommerce_swatches_size_for_product', $picker->size, get_the_ID(), $st_name );
					$swatch_term = new WC_Swatch_Term( 'swatches_id', $term->term_id, $taxonomy_lookup_name, $selected_value == $term->slug, $size );
				} elseif ( $picker->swatch_type_options[$lookup_name]['type'] == 'product_custom' ) {
					$size = apply_filters( 'woocommerce_swatches_size_for_product', $picker->swatch_type_options[$lookup_name]['size'], get_the_ID(), $st_name );
					$swatch_term = new WC_Product_Swatch_Term( $picker->swatch_type_options[$lookup_name], $term->term_id, $taxonomy_lookup_name, $selected_value == $term->slug, $size );
				}


				do_action( 'woocommerce_swatches_before_picker_item', $swatch_term );
				echo $swatch_term->get_output();
				do_action( 'woocommerce_swatches_after_picker_item', $swatch_term );

			endforeach;
		else :
			foreach ( $options as $option ) :

				$size = apply_filters( 'woocommerce_swatches_size_for_product', $picker->swatch_type_options[$lookup_name]['size'], get_the_ID(), $st_name );
				$swatch_term = new WC_Product_Swatch_Term( $picker->swatch_type_options[$lookup_name], $option, $name, $selected_value == sanitize_title( $option ), $size );

				do_action( 'woocommerce_swatches_before_picker_item', $swatch_term );
				echo $swatch_term->get_output();
				do_action( 'woocommerce_swatches_after_picker_item', $swatch_term );
			endforeach;
		endif;
		?>
	<?php endif; ?>
</div>

