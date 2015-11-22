<?php
/**
 * General setup hooks and filters
 *
 * @package galleria
 */

/**
 * Styles / scripts
 */
add_action( 'wp_enqueue_scripts', 'g_enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'g_enqueue_child_styles', 999 );

/**
 * Layout tweaks
 */
add_action( 'storefront_loop_columns', 			'g_loop_columns' );
add_action( 'swc_product_columns_default', 		'g_loop_columns' );
add_filter( 'storefront_related_products_args', 'g_related_products_args' );

/**
 * Extension integrations / tweaks
 */
add_action( 'customize_register', 'g_customize_storefront_extensions', 99 );
add_action( 'after_switch_theme', 'g_set_theme_mods' );