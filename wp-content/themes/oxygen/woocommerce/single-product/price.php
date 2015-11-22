<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;


$hide_main_offer = false;

# start: modified by Arlind Nushi
/*
if($product->is_type('variable'))
{
	ob_start();
	echo $product->get_price_html();

	$price_html = ob_get_clean();

	if(preg_match("/\"amount\".*?\"amount\"/", $price_html))
	{
		$hide_main_offer = true;
	}
}
*/
# end: modified by Arlind Nushi

?>
<div class="<?php echo $hide_main_offer ? 'price-hidden' : ''; ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

	<p class="price"><?php echo $product->get_price_html(); ?></p>

	<meta itemprop="price" content="<?php echo $product->get_price(); ?>" />
	<meta itemprop="priceCurrency" content="<?php echo get_woocommerce_currency(); ?>" />
	<link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>