<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $s;

if( ! get_data('header_links'))
	return;
?>
<div class="header-links">

	<ul class="header-widgets">
		<?php if(get_data('header_links_search_form')): ?>
		<li>

			<form action="<?php echo home_url(); ?>" method="get" class="search-form<?php echo $s ? ' input-visible' : ''; ?>" enctype="application/x-www-form-urlencoded">

				<div class="search-input-env<?php echo trim(get('s')) ? ' visible' : ''; ?>">
					<input type="text" class="form-control search-input" name="s" placeholder="<?php _e('Search...', TD); ?>" value="<?php echo esc_attr($s); ?>">
				</div>

				<a href="#" class="search-btn">
					<?php echo lab_get_svg('images/search.svg'); ?>
					<span class="sr-only"><?php _e('Search', TD); ?></span>
				</a>

			</form>

		</li>
		<?php endif; ?>

		<?php if(get_data('header_cart_info') && function_exists('WC')): ?>
		<?php
			$cart_items_count = WC()->cart->get_cart_contents_count();
			$cart_icon = get_data('header_cart_info_icon');

			if( ! $cart_icon)
				$cart_icon = 1;

		?>
		<li>
			<a class="cart-counter<?php echo $cart_items_count ? ' has-notifications' : ''; ?>" href="<?php echo WC()->cart->get_cart_url(); ?>">
				<span class="badge items-count"><?php echo $cart_items_count; ?></span>
				<?php echo lab_get_svg("images/cart_{$cart_icon}.svg"); ?>
			</a>

			<div class="lab-mini-cart">
				<?php get_template_part('tpls/woocommerce-mini-cart'); ?>
			</div>
		</li>
		<?php endif; ?>
	</ul>

</div>