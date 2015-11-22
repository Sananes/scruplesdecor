<?php
/**
 * Galleria structural functions
 *
 * @package galleria
 */

/**
 * Layout adjustments
 * @return rearrange markup through add_action and remove_action
 */
function g_layout_adjustments() {

	if ( is_woocommerce_activated() ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'g_product_loop_title_price_wrap', 11 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 2 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 1 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'g_product_loop_title_price_wrap_close', 2 );

		add_action( 'woocommerce_before_subcategory_title', 'g_product_loop_title_price_wrap', 11 );
		add_action( 'woocommerce_after_subcategory_title', 'g_product_loop_title_price_wrap_close', 2 );

		remove_action( 'storefront_header', 'storefront_header_cart', 60 );
		add_action( 'storefront_header', 'storefront_header_cart', 4 );

		remove_action( 'storefront_header', 'storefront_product_search', 40 );
		add_action( 'storefront_header', 'storefront_product_search', 3 );
	}

	remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
	add_action( 'storefront_header', 'storefront_secondary_navigation', 6 );

	remove_action( 'storefront_header', 'storefront_site_branding', 20 );
	add_action( 'storefront_header', 'storefront_site_branding', 5 );

	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
	add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 30 );

	add_action( 'storefront_header', 'g_primary_navigation_wrapper', 49 );
	add_action( 'storefront_header', 'g_primary_navigation_wrapper_close', 61 );

	add_action( 'storefront_header', 'g_top_bar_wrapper', 1 );
	add_action( 'storefront_header', 'g_top_bar_wrapper_close', 6 );
}

/**
 * Product title wrapper
 * @return void
 */
function g_product_loop_title_price_wrap() {
	echo '<section class="g-product-title">';
}

/**
 * Product title wrapper close
 * @return void
 */
function g_product_loop_title_price_wrap_close() {
	echo '</section>';
}

/**
 * Primary navigation wrapper
 * @return void
 */
function g_primary_navigation_wrapper() {
	echo '<section class="g-primary-navigation">';
}

/**
 * Primary navigation wrapper close
 * @return void
 */
function g_primary_navigation_wrapper_close() {
	echo '</section>';
}

/**
 * Top bar wrapper
 * @return void
 */
function g_top_bar_wrapper() {
	echo '<section class="g-top-bar">';
}

/**
 * Top bar wrapper close
 * @return void
 */
function g_top_bar_wrapper_close() {
	echo '</section>';
}

/**
 * Products per page
 * @return int products to display per page
 */
function g_products_per_page( $per_page ) {
	$per_page = 19;
	return intval( $per_page );
}

function g_change_breadcrumb_delimiter( $defaults ) {
	$defaults['delimiter'] = ' <span>/</span> ';
	return $defaults;
}
