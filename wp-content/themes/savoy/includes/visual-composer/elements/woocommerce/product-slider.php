<?php
	
	// !TODO!
	// VC element: nm_product_slider
	vc_map( array(
		'name' 			=> __( 'Product Slider', 'nm-framework-admin' ),
		'category' 		=> __( 'WooCommerce', 'js_composer' ),
		'description'	=> __( 'Display product slider', 'nm-framework-admin' ),
		'base' 			=> 'nm_product_slider',
		'icon' 			=> 'nm_product_slider',
		'params' 		=> array(
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Type', 'nm-framework-admin' ),
				'param_name' 	=> 'shortcode',
				'description' 	=> __( 'Select type of products to display.', 'nm-framework-admin' ),
				'value' 		=> array(
					__( 'Recent', 'nm-framework-admin' )				=> 'recent_products',
					__( 'Featured Products', 'nm-framework-admin' )		=> 'featured_products',
					__( 'Sale Products', 'nm-framework-admin' )			=> 'sale_products',
					__( 'Best Selling Products', 'nm-framework-admin' )	=> 'best_selling_products',
					__( 'Top Rated Products', 'nm-framework-admin' )	=> 'top_rated_products'
				),
				'save_always' 	=> true
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Per page', 'js_composer' ),
				'value' 		=> 12,
				'param_name' 	=> 'per_page',
				'description' 	=> __( 'The "per_page" shortcode determines how many products to show on the page', 'js_composer' ),
				'save_always'	=> true
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Columns', 'js_composer' ),
				'value' 		=> 4,
				'param_name' 	=> 'columns',
				'description'	=> __( 'The columns attribute controls how many columns wide the products should be before wrapping.', 'js_composer' ),
				'save_always'	=> true
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Order by', 'js_composer' ),
				'param_name' 	=> 'orderby',
				'description' 	=> sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				'value' 		=> array(
					__( 'Date', 'js_composer' )				=> 'date',
					__( 'ID', 'js_composer' )				=> 'ID',
					__( 'Author', 'js_composer' )			=> 'author',
					__( 'Title', 'js_composer' )			=> 'title',
					__( 'Modified', 'js_composer' )			=> 'modified',
					__( 'Random', 'js_composer' )			=> 'rand',
					__( 'Comment count', 'js_composer' )	=> 'comment_count',
					__( 'Menu order', 'js_composer' )		=> 'menu_order'
				),
				'save_always' 	=> true
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Sort order', 'js_composer' ),
				'param_name' 	=> 'order',
				'description' 	=> sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				'value' 		=> array(
					__( 'Descending', 'js_composer' )	=> 'DESC',
					__( 'Ascending', 'js_composer' )	=> 'ASC'
				),
				'save_always' 	=> true
			)
		)
	) );