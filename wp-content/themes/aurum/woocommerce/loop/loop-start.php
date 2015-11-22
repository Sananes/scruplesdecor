<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

/* Note: This file has been altered by Laborator */

global $is_related_products;

$class = 'col-sm-12';

if(SHOP_SIDEBAR && ! $is_related_products)
{
	$class = 'col-md-9 col-sm-8';

	if(get_data('shop_sidebar') == 'left')
		$class .= ' pull-right-md';
}
?>
<div class="<?php echo $class; ?>">

	<div class="row">

		<div class="products">