<?php
	
	// !TODO!
	// Shortcode: nm_product_slider
	function nm_shortcode_product_slider( $atts, $content = NULL ) {
		global $nm_globals;
		$nm_globals['product_slider_loop'] = true;
		
		nm_add_page_include( 'product-slider' );
		
		extract( shortcode_atts( array(
			'shortcode'	=> 'recent_products',
			'per_page'	=> '12',
			'columns'	=> '4',
			'orderby'	=> 'date',
			'order'		=> 'desc'
		), $atts ) );
		
		$shortcode_string = '[' . $shortcode . ' per_page="' . intval( $per_page ) . '" columns="' . intval( $columns ) . '" orderby="' . $orderby . '" order="' . $order . '"]';
		
		return do_shortcode( $shortcode_string );
		
		$nm_globals['product_slider_loop'] = false;
	}
	
	add_shortcode( 'nm_product_slider', 'nm_shortcode_product_slider' );
	