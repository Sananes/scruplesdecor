<?php
/**
 * Bundled Product Quantity Template.
 *
 * @version 4.8.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$quantity_min = $bundled_item->get_quantity();
$quantity_max = $bundled_item->get_quantity( 'max' );

if ( $quantity_min == $quantity_max || $bundled_item->is_out_of_stock() ) {

	?><div class="quantity quantity_hidden" style="display:none;"><input class="qty bundled_qty" type="hidden" name="<?php echo $bundle_fields_prefix; ?>bundle_quantity_<?php echo $bundled_item->item_id; ?>" value="<?php echo $quantity_min; ?>" /></div><?php

} else {

	$input_name = $bundle_fields_prefix . 'bundle_quantity_' . $bundled_item->item_id;

	ob_start();

 	woocommerce_quantity_input( array(
 		'input_name'  => $input_name,
 		'min_value'   => $quantity_min,
		'max_value'   => $quantity_max,
 		'input_value' => isset( $_POST[ $input_name ] ) ? $_POST[ $input_name ] : apply_filters( 'woocommerce_bundled_product_quantity', $quantity_min, $quantity_min, $quantity_max, $bundled_item )
 	), $bundled_item->product );

 	echo str_replace( 'qty text', 'qty text bundled_qty', ob_get_clean() );
}
