<?php
	
	// VC element: product_categories
	vc_map(array(
	   'name'			=> __( 'Product Categories', 'nm-framework-admin' ),
	   'category'		=> __( 'WooCommerce', 'nm-framework-admin' ),
	   'description'	=> __( 'Product Categories', 'nm-framework-admin' ),
	   'base'			=> 'nm_product_categories',
	   'icon'			=> 'icon-wpb-woocommerce',
	   'params'			=> array(
	   		array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Categories to Display', 'nm-framework-admin' ),
				'param_name' 	=> 'number',
				'description'	=> __( 'Enter the number of product categories to display.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Columns', 'nm-framework-admin' ),
				'param_name' 	=> 'columns',
				'description'	=> __( 'Select number of columns.', 'nm-framework-admin' ),
				'value' 		=> array(
					'1'	=> '1',
					'2'	=> '2',
					'3'	=> '3',
					'4'	=> '4',
					'5'	=> '5'
				),
				'std'			=> '4'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Order By', 'nm-framework-admin' ),
				'param_name' 	=> 'orderby',
				'description'	=> __( 'Select categories order-by.', 'nm-framework-admin' ),
				'value'			=> array(
					'None'			=> 'none',
					'ID'			=> 'ID',
					'Name'			=> 'name',
					'Date'			=> 'date',
					'Menu Order'	=> 'menu_order',
					'Random'		=> 'rand'
				),
				'std'			=> 'name'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Order', 'nm-framework-admin' ),
				'param_name' 	=> 'order',
				'description'	=> __( 'Select categories order.', 'nm-framework-admin' ),
				'value'			=> array(
					'Descending'	=> 'desc',
					'Ascending'		=> 'asc'
				),
				'std'			=> 'asc'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Hide Empty', 'nm-framework-admin' ),
				'param_name' 	=> 'hide_empty',
				'description'	=> __( 'Hide empty categories.', 'nm-framework-admin' ),
				'value'			=> array(
					'Yes'	=> '1',
					'No'	=> '0'
				),
				'std'			=> '1'
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Parent', 'nm-framework-admin' ),
				'param_name' 	=> 'parent',
				'description'	=> __( 'Enter 0 to only display top level categories.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( "ID's", 'nm-framework-admin' ),
				'param_name' 	=> 'ids',
				'description'	=> __( "Filter categories by entering a comma separated list of ID's.", 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Packery Grid', 'nm-framework-admin' ),
				'param_name' 	=> 'packery',
				'description'	=> __( 'Enable Packery grid layout.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' ) => '1'
				)
			)
	   )
	));
	