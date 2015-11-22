<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Conditions dropdown.
 *
 * Display a list of conditions.
 *
 * @since 1.0.0
 *
 * @param mixed 	$id				ID of the current condition.
 * @param mixed 	$group			Group the condition belongs to.
 * @param string 	$current_value	Current condition value.
 */
function was_condition_conditions( $id, $group = 0, $current_value = 'subtotal' ) {

	$conditions = array(
		__( 'Cart', 'woocommerce-advanced-shipping' ) => array(
			'subtotal' 					=> __( 'Subtotal', 'woocommerce-advanced-shipping' ),
			'subtotal_ex_tax' 			=> __( 'Subtotal ex. taxes', 'woocommerce-advanced-shipping' ),
			'tax' 						=> __( 'Tax', 'woocommerce-advanced-shipping' ),
			'quantity' 					=> __( 'Quantity', 'woocommerce-advanced-shipping' ),
			'contains_product' 			=> __( 'Contains product', 'woocommerce-advanced-shipping' ),
			'coupon' 					=> __( 'Coupon', 'woocommerce-advanced-shipping' ),
			'weight' 					=> __( 'Weight', 'woocommerce-advanced-shipping' ),
			'contains_shipping_class'	=> __( 'Contains shipping class', 'woocommerce-advanced-shipping' ),
		),
		__( 'User Details', 'woocommerce-advanced-shipping' ) => array(
			'zipcode' 					=> __( 'Zipcode', 'woocommerce-advanced-shipping' ),
			'city' 						=> __( 'City', 'woocommerce-advanced-shipping' ),
			'state'	 					=> __( 'State', 'woocommerce-advanced-shipping' ),
			'country' 					=> __( 'Country', 'woocommerce-advanced-shipping' ),
			'role'	 					=> __( 'User role', 'woocommerce-advanced-shipping' ),
		),
		__( 'Product', 'woocommerce-advanced-shipping' ) => array(
			'width' 					=> __( 'Width', 'woocommerce-advanced-shipping' ),
			'height' 					=> __( 'Height', 'woocommerce-advanced-shipping' ),
			'length' 					=> __( 'Length', 'woocommerce-advanced-shipping' ),
			'stock' 					=> __( 'Stock', 'woocommerce-advanced-shipping' ),
			'stock_status'				=> __( 'Stock status', 'woocommerce-advanced-shipping' ),
			'category' 					=> __( 'Category', 'woocommerce-advanced-shipping' ),
		),
	);
	$conditions = apply_filters( 'was_conditions', $conditions );


	?><span class='was-condition-wrap was-condition-wrap-<?php echo absint( $id ); ?>'>

		<select class='was-condition' data-group='<?php echo absint( $group ); ?>' data-id='<?php echo absint( $id ); ?>'
			name='_was_shipping_method_conditions[<?php echo absint( $group ); ?>][<?php echo absint( $id ); ?>][condition]'><?php

			foreach ( $conditions as $option_group => $values ) :

				?><optgroup label='<?php echo esc_attr( $option_group ); ?>'><?php

				foreach ( $values as $key => $value ) :
					?><option value='<?php echo esc_attr( $key ); ?>' <?php selected( $key, $current_value ); ?>><?php echo esc_html( $value ); ?></option><?php
				endforeach;

				?></optgroup><?php

			endforeach;

		?></select>

	</span><?php

}
