<?php
/**
 *	The template for displaying quickview product content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

/* Function: Product summary - Opening tags */
function nm_qv_product_summary_open() {
	echo '<div class="nm-qv-summary-top">';
}

/* Function: Product summary - Divider tags */
function nm_qv_product_summary_divider() {
	global $nm_theme_options;
	
	echo '
		</div>
		<div class="nm-qv-summary-content ' . esc_attr( $nm_theme_options['product_quickview_summary_layout'] ) . '">';
}

/* Function: Product summary - Closing tags */
function nm_qv_product_summary_close() {
	echo '</div>';
}

/* Function: Product summary - Actions */
function nm_qv_product_summary_actions() {
	global $product, $nm_theme_options;
	
	$details_button_class = '';
					
	// Add-to-cart button
	if ( $nm_theme_options['product_quickview_atc'] ) {
		$details_button_class = ' border';
		
		woocommerce_template_single_add_to_cart();
	}
	
	// Details button
	if ( $nm_theme_options['product_quickview_details_button'] ) {
		echo '<a href="' . esc_url( get_permalink( $product->id ) ) . '" class="nm-qv-details-button button' . esc_attr( $details_button_class ) . '">' . esc_html__( 'Details', 'nm-framework' ) . '</a>';
	}
}

// Action: woocommerce_single_product_summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'nm_qv_product_summary_open', 3 );
add_action( 'woocommerce_single_product_summary', 'nm_qv_product_summary_divider', 15 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 21 );
add_action( 'woocommerce_single_product_summary', 'nm_qv_product_summary_actions', 30 );
add_action( 'woocommerce_single_product_summary', 'nm_qv_product_summary_close', 55 );

// Main wrapper class
$class = 'product' . ' product-' . $product->product_type;

?>

<div id="product-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
	<div class="nm-qv-product-image">
		<?php wc_get_template( 'quickview/product-image.php' ); ?>
	</div>
    
    <div class="nm-qv-summary">
        <div id="nm-qv-product-summary" class="product-summary">
        	<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked nm_qv_product_summary_open - 3
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked nm_qv_product_summary_divider - 15
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_rating - 21
				 * @hooked nm_qv_product_summary_actions - 30
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked nm_qv_product_summary_close - 55
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
        </div>
    </div>
</div>
