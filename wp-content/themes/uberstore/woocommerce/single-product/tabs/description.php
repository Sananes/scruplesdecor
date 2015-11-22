<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $post;

$heading = esc_html( apply_filters('woocommerce_product_description_heading', __( 'Product Description', THB_THEME_NAME ) ) );
?>
<?php $extended_product_page = get_post_meta($post->ID, 'extended_product_page', true);  ?>

<?php if ($extended_product_page == 'on') { 
		echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); 
	} else {
		the_content();
	} ?>