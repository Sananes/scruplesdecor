<?php

class WC_Swatch_Picker {

	public $size;
	public $attributes;
	public $selected_attributes;
	public $swatch_type_options;

	public function __construct( $product_id, $attributes, $selected_attributes ) {
		$this->swatch_type_options = maybe_unserialize( get_post_meta( $product_id, '_swatch_type_options', true ) );

		if ( !$this->swatch_type_options ) {
			$this->swatch_type_options = array();
		}

		$product_configured_size = get_post_meta( $product_id, '_swatch_size', true );
		if ( !$product_configured_size ) {
			$this->size = 'swatches_image_size';
		} else {
			$this->size = $product_configured_size;
		}

		$this->attributes = $attributes;
		$this->selected_attributes = $selected_attributes;
	}

	public function picker() {
		global $woocommerce_swatches;
		woocommerce_swatches_get_template( 'single-product/table.php', array('picker' => $this) );
	}

	public function render_picker( $name, $options, $real_name = '' ) {
		$st_name = sanitize_title( $name );
		$hashed_name = md5( $st_name );

		$lookup_name = '';
		if ( isset( $this->swatch_type_options[$hashed_name] ) ) {
			$lookup_name = $hashed_name;
		} elseif ( isset( $this->swatch_type_options[$st_name] ) ) {
			$lookup_name = $st_name;
		}

		$taxonomy_lookup_name = taxonomy_exists( $st_name ) ? $st_name : (taxonomy_exists( $real_name ) ? $real_name : $st_name);
		$selected_value = (isset( $this->selected_attributes[$lookup_name] )) ? $this->selected_attributes[$lookup_name] : '';

		if ( isset( $_GET['attribute_' . $st_name] ) ) {
			$selected_value = esc_attr( $_GET['attribute_' . $st_name] );
		}

		$layout = apply_filters( 'wc_swatches_and_photos_label_get_layout', (isset( $this->swatch_type_options[$lookup_name]['layout'] ) ? $this->swatch_type_options[$lookup_name]['layout'] : 'default' ), $name, $options, $this );

		if ( $layout == 'label_above' ) :
			$this->render_picker_label_layout( $layout, $name, $options );
		endif;

		do_action( 'wc_swatches_and_photos_label_before', $layout, $name, $options, $this );
		woocommerce_swatches_get_template( 'single-product/input-picker.php', array(
		    'picker' => $this, 
		    'st_name' => $st_name, 
		    'hashed_name' => $hashed_name,
		    'lookup_name' => $lookup_name,
		    'taxonomy_lookup_name' => $taxonomy_lookup_name,
		    'selected_value' => $selected_value,
		    'options' => $options));
	}

	public function render_default( $name, $options ) {
		$st_name = sanitize_title( $name );
		$hashed_name = md5( $st_name );
		$selected_value = '';

		$lookup_name = '';
		if ( isset( $this->swatch_type_options[$hashed_name] ) ) {
			$lookup_name = $hashed_name;
		} elseif ( isset( $this->swatch_type_options[$st_name] ) ) {
			$lookup_name = $st_name;
		}
		?>

		<?php do_action( 'woocommerce_swatches_before_select', $name, $options, $this ); ?>
		
		<?php
		
		woocommerce_swatches_get_template( 'single-product/input-select.php', array(
		    'picker' => $this, 
		    'st_name' => $st_name, 
		    'hashed_name' => $hashed_name,
		    'lookup_name' => $lookup_name,
		    'selected_value' => $selected_value, 
		    'options' => $options));
		
		?>

		<?php do_action( 'woocommerce_swatches_after_select', $name, $options, $this ); ?>
		<input type="hidden" name="<?php echo 'attribute_' . $st_name; ?>" id="<?php echo 'attribute_' . $st_name; ?>" value="<?php echo $selected_value; ?>" />
		<?php
	}

	public function render_picker_label_layout( $layout, $name, $options ) {
		$st_name = sanitize_title( $name );
		?>

		<div 
			id="<?php echo esc_attr( $st_name ); ?>_label" 
			class="attribute_<?php echo $st_name; ?>_picker_label swatch-label">
			&nbsp;
		</div>

		<?php
	}

}
