<?php
/**
 * The template for displaying product archives, including the main shop page which is a post type archive
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// If "shop_load" is set, make sure request is via AJAX
if ( isset( $_REQUEST['shop_load'] ) && nm_is_ajax_request() ) {
	
	if ( 'products' !== $_REQUEST['shop_load'] ) {
		// AJAX filter or search:
		include( NM_THEME_DIR . '/woocommerce/archive-product_nm_ajax_full.php' );
	} else {
		// AJAX page load:
		include( NM_THEME_DIR . '/woocommerce/archive-product_nm_ajax_products.php' );
	}
	
} else {
	
	include( NM_THEME_DIR . '/woocommerce/archive-product_nm.php' );
	
}
