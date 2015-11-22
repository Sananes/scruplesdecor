<?php
/**
 * Galleria structural hooks and filters
 *
 * @package galleria
 */

/**
 * Layout
 */
add_action( 'init', 'g_layout_adjustments' );
add_filter( 'storefront_products_per_page', 'g_products_per_page' );
add_filter( 'woocommerce_breadcrumb_defaults', 'g_change_breadcrumb_delimiter' );
