<?php
/**
 * Loop Price
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
?>

<?php if ( $price_html = $product->get_price_html() ) : ?>
	<span class="price"><?php echo $price_html; ?></span>
<?php endif; ?>

<?php
# start: modified by Arlind Nushi
if( ! get_data('shop_add_to_cart_listing'))
	return;
# end: modified by Arlind Nushi
?>

<?php if(is_catalog_mode()) return; ?>


<?php if($product->is_type('variable')): ?>
<a class="add-to-cart-btn entypo-list-add" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Select Options', 'oxygen'); ?>" href="<?php echo $product->get_permalink(); ?>"></a>

<?php elseif($product->is_type('grouped')): ?>
<a class="add-to-cart-btn entypo-list-add" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Select Products', 'oxygen'); ?>" href="<?php echo $product->get_permalink(); ?>"></a>

<?php elseif($product->is_type('external')): ?>
<a class="add-to-cart-btn entypo-export" data-toggle="tooltip" data-placement="bottom" title="<?php echo $product->single_add_to_cart_text(); ?>" href="<?php echo $product->get_product_url(); ?>" target="_blank"></a>

<?php else: ?>

<a class="add-to-cart-btn add-to-cart glyphicon glyphicon-plus-sign" data-id="<?php echo $product->post->ID; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php _e('Add to Cart', 'oxygen'); ?>" href="#">
	<span class="glyphicon glyphicon-ok-sign"></span>
</a>
<?php endif; ?>