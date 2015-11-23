<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $woocommerce_loop, $nm_theme_options;
	
// Columns large
if ( ( isset( $woocommerce_loop['columns'] ) && $woocommerce_loop['columns'] != '' ) ) {
	$columns_large = $woocommerce_loop['columns'];
} else {
	$columns_large = ( isset( $_GET['col'] ) ) ? intval( $_GET['col'] ) : $nm_theme_options['shop_columns'];
}

// Columns medium
if ( intval( $columns_large ) < 3 ) {
	$columns_medium = '2'; // Make sure "columns_medium" is lower-than or equal-to "columns"
} else {
	$columns_medium = ( isset( $woocommerce_loop['columns_medium'] ) ) ? $woocommerce_loop['columns_medium'] : '3';
}

// Columns small
$columns_small = ( isset( $woocommerce_loop['columns_small'] ) ) ? $woocommerce_loop['columns_small'] : '2';

// Columns x-small
$columns_xsmall = ( isset( $woocommerce_loop['columns_xsmall'] ) ) ? $woocommerce_loop['columns_xsmall'] : $nm_theme_options['shop_columns_mobile'];

// Class
$columns_class = 'xsmall-block-grid-' . $columns_xsmall . ' small-block-grid-' . $columns_small . ' medium-block-grid-' . $columns_medium . ' large-block-grid-' . $columns_large;
?>

<ul class="nm-products products <?php echo esc_attr( $columns_class ); ?>">
