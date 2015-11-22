<?php
/**
 * Product Bundles < 4.8.0 Compatibility Functions
 * @version 4.10.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Deprecated WC 2.0 back-compat functions.
 *
 */
function wc_bundles_attribute_label( $arg ) {
	_deprecated_function( 'wc_bundles_attribute_label', '4.8.0', 'wc_attribute_label' );
	return wc_attribute_label( $arg );
}

function wc_bundles_attribute_order_by( $arg ) {
	_deprecated_function( 'wc_bundles_attribute_order_by', '4.8.0', 'wc_attribute_orderby' );
	return wc_attribute_orderby( $arg );
}

function wc_bundles_get_template( $file, $data, $empty, $path ) {
	_deprecated_function( 'wc_bundles_get_template', '4.8.0', 'wc_get_template' );
	return wc_get_template( $file, $data, $empty, $path );
}

/**
 * 'wc_get_product_terms()' back-compat wrapper.
 *
 * @return array
 */
function wc_bundles_get_product_terms( $product_id, $attribute_name, $args ) {

	if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ) {

		return wc_get_product_terms( $product_id, $attribute_name, $args );

	} else {

		$orderby = wc_attribute_orderby( sanitize_title( $attribute_name ) );

		switch ( $orderby ) {
			case 'name' :
				$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
			break;
			case 'id' :
				$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false );
			break;
			case 'menu_order' :
				$args = array( 'menu_order' => 'ASC' );
			break;
		}

		$terms = get_terms( sanitize_title( $attribute_name ), $args );

		return $terms;
	}
}

/**
 * 'wc_get_price_decimals()' back-compat wrapper.
 *
 * @return array
 */
function wc_bundles_get_price_decimals() {

	if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_3() ) {
		return wc_get_price_decimals();
	} else {
		return absint( get_option( 'woocommerce_price_num_decimals', 2 ) );
	}
}

/**
 * 'wc_dropdown_variation_attribute_options()' back-compat wrapper.
 */
function wc_bundles_dropdown_variation_attribute_options( $args = array() ) {

	if ( WC_PB_Core_Compatibility::is_wc_version_gte_2_4() ) {

		return wc_dropdown_variation_attribute_options( $args );

	} else {

		$args = wp_parse_args( $args, array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected' 	       => false,
			'name'             => '',
			'id'               => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' )
		) );

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">';

		if ( $args['show_option_none'] ) {
			echo '<option value="">' . esc_html( $args['show_option_none'] ) . '</option>';
		}

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {

				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_bundles_get_product_terms( $product->id, $attribute, array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					echo '<option value="' . esc_attr( sanitize_title( $option ) ) . '" ' . selected( $args['selected'], sanitize_title( $option ), false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
				}
			}
		}

		echo '</select>';
	}
}
