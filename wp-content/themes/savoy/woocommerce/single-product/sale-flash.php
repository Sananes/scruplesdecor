<?php
/**
 * Single Product Sale Flash
 *
 * @author 	WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $nm_theme_options;

?>
<?php if ( $nm_theme_options['single_product_sale_flash'] && $product->is_on_sale() ) : ?>

	<?php
		// Output percentage or text "sale flash"
		if ( $nm_theme_options['product_sale_flash'] !== 'txt' ) {
			$sale_percent = nm_product_get_sale_percent( $product );
			
			if ( $sale_percent > 0 ) {
				echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale"><span class="nm-onsale-before">-</span>' . $sale_percent . '<span class="nm-onsale-after">%</span></span>', $post, $product );
			}
		} else {
			$sale_text = __( 'Sale!', 'woocommerce' );
			
			echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . $sale_text . '</span>', $post, $product );
		}
	?>

<?php endif; ?>