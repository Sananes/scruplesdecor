<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $parsed_from_vc;

if(SHOPSIDEBAR && ! is_product() && ! $parsed_from_vc):
?>

<div class="row<?php echo SHOPSIDEBARALIGN == 'left' ? ' shop-left-sidebar' : ''; ?>">

	<div class="col-md-9 shop-product-env">

		<div class="shop-grid with-sidebar">

			<section class="items-env">

				<div class="items-env">

					<div class="items">

						<div class="row no-margin">
<?php else: ?>

<div class="row">

	<div class="shop-grid">

		<section class="items-env">

			<div class="items">
<?php endif; ?>

