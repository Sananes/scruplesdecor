<?php
	
	// VC element: nm_banner_slider
	vc_map( array(
		'name' 						=> __( 'Banner Slider', 'nm-framework-admin' ),
		'category'					=> __( 'Content', 'nm-framework-admin' ),
	   	'description'				=> __( 'Create a banner slider', 'nm-framework-admin' ),
		'base' 						=> 'nm_banner_slider',
		'icon'						=> 'nm_banner_slider',
		'as_parent' 				=> array( 'only' => 'nm_banner' ),
		'controls' 					=> 'full',
		'content_element' 			=> true,
		'show_settings_on_create'	=> false,
		'js_view' 					=> 'VcColumnView',
		'params' 					=> array(
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Type', 'nm-framework-admin' ),
				'param_name' 	=> 'slider_type',
				'description'	=> __( 'Select slider type.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Boxed'			=> 'boxed',
					'Full Width'	=> 'full'
				),
				'std' 			=> 'boxed'
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Adaptive Height', 'nm-framework-admin' ),
				'param_name' 	=> 'adaptive_height',
				'description'	=> __( 'Enable adaptive height for each slide.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Arrows', 'nm-framework-admin' ),
				'param_name' 	=> 'arrows',
				'description'	=> __( 'Display "prev" and "next" arrows.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Pagination', 'nm-framework-admin' ),
				'param_name' 	=> 'pagination',
				'description'	=> __( 'Display pagination.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Pagination Color', 'nm-framework-admin' ),
				'param_name' 	=> 'pagination_color',
				'description'	=> __( 'Select pagination color.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Light'	=> 'light',
					'Gray'	=> 'gray',
					'Dark' 	=> 'dark'
				),
				'std' 			=> 'gray'
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Infinite Loop', 'nm-framework-admin' ),
				'param_name' 	=> 'infinite',
				'description'	=> __( 'Infinite loop sliding.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Autoplay', 'nm-framework-admin' ),
				'param_name' 	=> 'autoplay',
				'description'	=> __( 'Enter autoplay interval in milliseconds (1 second = 1000 milliseconds).', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'colorpicker',
				'heading' 		=> __( 'Background Color', 'nm-framework-admin' ),
				'param_name' 	=> 'background_color',
				'description'	=> __( 'Set a background color.', 'nm-framework-admin' )
			)
		)
	) );
	
	
	// Extend element with the WPBakeryShortCodesContainer class to inherit all required functionality
	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_NM_Banner_Slider extends WPBakeryShortCodesContainer {}
	}
