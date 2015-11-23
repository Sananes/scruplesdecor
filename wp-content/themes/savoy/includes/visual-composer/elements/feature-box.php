<?php
	
	// VC element: nm_feature
	vc_map( array(
	   'name'			=> __( 'Feature Box', 'nm-framework-admin' ),
	   'category'		=> __( 'Content', 'nm-framework-admin' ),
	   'description'	=> __( 'Feature box with image or icon.', 'nm-framework-admin' ),
	   'base'			=> 'nm_feature',
	   'icon'			=> 'nm_feature',
	   'params'			=> array(
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Title', 'nm-framework-admin' ),
				'param_name' 	=> 'title',
				'description'	=> __( 'Enter a feature title.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Sub-title', 'nm-framework-admin' ),
				'param_name' 	=> 'subtitle',
				'description'	=> __( 'Enter a sub-title.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __('Icon Type', 'nm-framework-admin' ),
				'param_name' 	=> 'icon_type',
				'description'	=> __( 'Select icon type.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Font Icon'	=> 'icon',
					'Image'		=> 'image_id'
				),
				'std' 			=> 'icon'
			),
			array(
				'type' 			=> 'iconpicker',
				'heading' 		=> __( 'Icon', 'nm-framework-admin' ),
				'param_name' 	=> 'icon',
				'description' 	=> __( 'Select icon from library.', 'nm-framework-admin' ),
				'value' 		=> 'pe-7s-close',  // Default value to backend editor admin_label
				'settings' 		=> array(
					'type' 			=> 'pixeden', // Default type for icons
					'emptyIcon' 	=> false, // Default true, display an "EMPTY" icon?
					'iconsPerPage'	=> 3000 // Default 100, how many icons per/page to display, we use (big number) to display all icons in single page
				),
				'dependency'	=> array(
					'element'	=> 'icon_type',
					'value'		=> 'icon'
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Icon Style', 'nm-framework-admin' ),
				'param_name' 	=> 'icon_style',
				'description'	=> __( 'Select an icon style.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Simple'		=> 'simple',
					'Background'	=> 'background',
					'Border'		=> 'border'
				),
				'std' 			=> 'simple',
				'dependency'	=> array(
					'element'	=> 'icon_type',
					'value' 	=> array( 'icon' )
				)
			),
			array(
				'type' 			=> 'colorpicker',
				'heading' 		=> __( 'Icon Background/Border Color', 'nm-framework-admin' ),
				'param_name' 	=> 'icon_background_color',
				'description' 	=> __( 'Select icon background/border color.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'icon_style',
					'value' 	=> array( 'background', 'border' )
				)
			),
			array(
				'type' 			=> 'colorpicker',
				'heading' 		=> __( 'Icon Color', 'nm-framework-admin' ),
				'param_name' 	=> 'icon_color',
				'description' 	=> __( 'Select icon color.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'icon_type',
					'value' 	=> array( 'icon' )
				)
			),
			array(
				'type' 			=> 'attach_image',
				'heading' 		=> __( 'Image', 'nm-framework-admin' ),
				'param_name' 	=> 'image_id',
				'description'	=> __( 'Select image from the media library.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'icon_type',
					'value' 	=> array( 'image_id' )
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Image Style', 'nm-framework-admin' ),
				'param_name' 	=> 'image_style',
				'description'	=> __( 'Select an image style.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Default'	=> 'default',
					'Rounded'	=> 'rounded'
				),
				'std' 			=> 'default',
				'dependency'	=> array(
					'element'	=> 'icon_type',
					'value' 	=> array( 'image_id' )
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __('Layout', 'nm-framework-admin' ),
				'param_name' 	=> 'layout',
				'description'	=> __( 'Select a layout.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Default'		=> 'default',
					'Centered'		=> 'centered',
					'Icon Right'	=> 'icon_right',
					'Icon Left'		=> 'icon_left'
				),
				'std' 			=> 'default'
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Top Offset', 'nm-framework-admin' ),
				'param_name' 	=> 'top_offset',
				'description'	=> __( 'Offset the feature text (numbers only).', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __('Bottom Spacing', 'nm-framework-admin' ),
				'param_name' 	=> 'bottom_spacing',
				'description'	=> __( 'Select bottom spacing.', 'nm-framework-admin' ),
				'value' 		=> array(
					'(None)'	=> 'none',
					'Small'		=> 'small',
					'Medium'	=> 'medium',
					'Large'		=> 'large'
				),
				'std' 			=> 'none'
			),
			array(
				'type' 			=> 'textarea_html',
				//'holder' 		=> 'div',
				'heading' 		=> __( 'Description', 'nm-framework-admin' ),
				'param_name' 	=> 'content', // Important: Only one textarea_html param per content element allowed and it should have "content" as a "param_name"
				'description'	=> __( 'Enter a feature description.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'vc_link',
				'heading' 		=> __( 'Link', 'nm-framework-admin' ),
				'param_name' 	=> 'link',
				'description' 	=> __( 'Add a link after the description.', 'nm-framework-admin' )
			)
	   )
	) );
	