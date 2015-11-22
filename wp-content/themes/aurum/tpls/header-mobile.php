<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $top_menu_class;

$nav_id = 'main-menu';
$top_menu_class = 'light';

if(has_nav_menu('mobile-menu'))
	$nav_id = 'mobile-menu';

$menu = wp_nav_menu(
	array(
		'theme_location'    => $nav_id,
		'container'         => '',
		'menu_class'        => 'mobile-menu',
		'echo'				=> false
	)
);
?>
<header class="mobile-menu">

	<section class="mobile-logo">

		<?php get_template_part('tpls/header-logo'); ?>

		<div class="mobile-toggles">
			<a class="toggle-menu" href="#">
				<?php echo lab_get_svg('images/toggle-menu.svg'); ?>
				<span class="sr-only"><?php _e('Toggle Menu', TD); ?></span>
			</a>
		</div>

	</section>

	<section class="search-site<?php echo get('s') ? ' is-visible' : ''; ?>">

		<?php get_template_part('tpls/header-search-form'); ?>

	</section>

	<?php echo $menu; ?>

	<?php
	if(get_data('header_cart_info') && function_exists('WC')):

		$cart_items_count = WC()->cart->get_cart_contents_count();
		$cart_icon = get_data('header_cart_info_icon');

		if( ! $cart_icon)
			$cart_icon = 1;
	?>
	<section class="cart-info">
		<a class="cart-counter<?php echo $cart_items_count ? ' has-notifications' : ''; ?>" href="<?php echo WC()->cart->get_cart_url(); ?>">
			<i class="cart-icon"><?php echo lab_get_svg("images/cart_{$cart_icon}.svg"); ?></i>
			<strong><?php _e('Cart', 'woocommerce'); ?></strong>
			<span class="badge items-count"><?php echo $cart_items_count; ?></span>
		</a>
	</section>
	<?php
	endif;
	?>

	<header class="site-header">
		<?php get_template_part('tpls/header-top-bar'); ?>
	</header>

</header>