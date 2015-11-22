<?php
	/*
	 *	NM - WooCommerce Product Quick View Module
	 */
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	/*
	 *	AJAX: Load product
	 */
	function nm_ajax_load_product() {
		global $woocommerce, $product, $post;
		
		//$post = $product = get_post( $_POST['product_id'] );
		$product = get_product( $_POST['product_id'] );
		$post = $product->post;
		$output = '';
		
		setup_postdata( $post );
			
		ob_start();
			wc_get_template_part( 'quickview/content', 'quickview' );
		$output = ob_get_clean();
		
		wp_reset_postdata();
				
		echo $output;
				
		exit;
	}
	// Note: Keep default AJAX actions in case WooCommerce endpoint URL is unavailable
	add_action( 'wp_ajax_nm_ajax_load_product' , 'nm_ajax_load_product' );
	add_action( 'wp_ajax_nopriv_nm_ajax_load_product', 'nm_ajax_load_product' );
	// Register WooCommerce Ajax endpoint (available since 2.4)
	add_action( 'wc_ajax_nm_ajax_load_product', 'nm_ajax_load_product' );