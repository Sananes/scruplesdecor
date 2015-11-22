<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Descriptions.
 *
 * Display a description icon + tooltip on hover.
 *
 * @since 1.0.0
 *
 * @param string $condition Current condition to display the description for.
 */
function was_condition_description( $condition ) {

	$descriptions = array(
		'state' 					=> __( 'States must be installed in WC', 'woocommerce-advanced-shipping' ),
		'weight' 					=> __( 'Weight calculated on all the cart contents', 'woocommerce-advanced-shipping' ),
		'length' 					=> __( 'Compared to lengthiest product in cart', 'woocommerce-advanced-shipping' ),
		'width' 					=> __( 'Compared to widest product in cart', 'woocommerce-advanced-shipping' ),
		'height'					=> __( 'Compared to highest product in cart', 'woocommerce-advanced-shipping' ),
		'stock_status' 				=> __( 'All products in cart must match stock status', 'woocommerce-advanced-shipping' ),
		'category' 					=> __( 'All products in cart must match category', 'woocommerce-advanced-shipping' ),
		'contains_product' 			=> __( 'Cart must contain one of this product, other products are allowed', 'woocommerce-advanced-shipping' ),
		'contains_shipping_class' 	=> __( 'Cart must contain at least one product with the selected shipping class', 'woocommerce-advanced-shipping' ),
	);
	$descriptions = apply_filters( 'was_descriptions', $descriptions );

	// Display description
	if ( ! isset( $descriptions[ $condition ] ) ) :
		?><span class='was-description no-description'></span><?php
		return;
	endif;

	?><span class='was-description <?php echo $condition; ?>-description'>

		<div class='description'>

			<img class='was_tip' src='<?php echo WC()->plugin_url(); ?>/assets/images/help.png' height='24' width='24' />

			<div class='was_desc'><?php
				echo wp_kses_post( $descriptions[ $condition ] );
			?></div>

		</div>

	</span><?php

}
