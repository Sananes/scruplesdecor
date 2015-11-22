<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

?>
<?php if(function_exists('WC') && get_data('cart_ribbon_show')): ?>

<?php
$ribbons = array(
	'cart-icon-1' => THEMEASSETS . 'images/cart-icon-1.png',
	'cart-icon-2' => THEMEASSETS . 'images/cart-icon-2.png',
	'cart-icon-3' => THEMEASSETS . 'images/cart-icon-3.png',
	'cart-icon-4' => THEMEASSETS . 'images/cart-icon-4.png',
);

$cart_ribbon_image = str_replace('-black.png', '', basename(get_data('cart_ribbon_image')));

$cart_counter_ajax = get_data('shop_cart_counter_ajax');

if( isset($ribbons[$cart_ribbon_image]))
	$cart_ribbon_image = $ribbons[$cart_ribbon_image];
else
	$cart_ribbon_image = $ribbons['cart-icon-1'];
?>

<div class="cart-ribbon"<?php echo $cart_counter_ajax ? ' data-ajax-counter="1"' : ''; ?>>
	<a href="<?php echo WC()->cart->get_cart_url(); ?>">
		<span class="cart_content">
			<span class="bucket" style="background-image: url(<?php echo $cart_ribbon_image; ?>);"></span>
			<span class="number"><?php echo $cart_counter_ajax ? '...' : WC()->cart->cart_contents_count; ?></span>
		</span>

		<span class="bucket_bottom"></span>
	</a>
</div>
<?php endif; ?>