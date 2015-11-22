<?php
/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if( ! get_data('shop_add_to_cart_listing') || get_data('shop_catalog_mode'))
	return;

$classes        = array('add-to-cart');

$href           = get_permalink();
$button_text    = __('Add to cart', 'woocommerce');

if($product->is_in_stock() == false)
{
	$classes[]     = 'out-of-stock';
	$button_text   = __('Out of stock', 'woocommerce');
}
else
if($product->is_type('variable'))
{
	$classes[]     = 'select-options';
	$button_text   = __('Select options', 'woocommerce');
}
else
if($product->is_type('external'))
{
	$classes[]     = 'external-product';
	$button_text   = $product->button_text;
	$href          = $product->product_url;
}

# Add To Cart via AJAX
if($product->product_type == 'simple')
{
	$classes[] = 'ajax-add-to-cart';
}

?>
<a class="<?php echo implode(' ', $classes); ?>" target="<?php echo $product->is_type('external') ? '_blank' : '_self'; ?>" data-product-id="<?php echo $product->id; ?>" href="<?php echo $href; ?>" data-toggle="tooltip" data-placement="<?php echo is_rtl() ? 'right' : 'left'; ?>" title="<?php echo $button_text; ?>" data-added-to-cart-title="<?php _e('Product added to cart!', TD); ?>"></a>
<?php

/*
echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( $product->id ),
		esc_attr( $product->get_sku() ),
		esc_attr( isset( $quantity ) ? $quantity : 1 ),
		$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
		esc_attr( $product->product_type ),
		esc_html( $product->add_to_cart_text() )
	),
$product );
*/
