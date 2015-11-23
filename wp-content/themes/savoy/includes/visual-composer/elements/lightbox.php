<?php
	
	// VC element: nm_lightbox
	vc_map( array(
	   'name'			=> __( 'Lightbox', 'nm-framework-admin' ),
	   'category'		=> __( 'Content', 'nm-framework-admin' ),
	   'description'	=> __( 'Lightbox modal with custom content', 'nm-framework-admin' ),
	   'base'			=> 'nm_lightbox',
	   'icon'			=> 'nm_lightbox',
	   'params'			=> array(
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __('Link Type', 'nm-framework-admin' ),
				'param_name' 	=> 'link_type',
				'description'	=> __( 'Select lightbox link type.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Link'		=> 'link',
					'Button'	=> 'btn', //  Note: Using "button" causes a CSS bug in WP
					'Image'		=> 'image'
				),
				'std' 			=> 'link'
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Title', 'nm-framework-admin' ),
				'param_name' 	=> 'title',
				'description'	=> __( 'Enter a lightbox link/button title.', 'nm-framework-admin' )
			),
			// Dependency: link_type - btn
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Button Style', 'nm-framework-admin' ),
				'param_name'	=> 'button_style',
				'description'	=> __( 'Select button style.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Filled'			=> 'filled',
					'Filled Rounded'	=> 'filled_rounded',
					'Border'			=> 'border',
					'Border Rounded'	=> 'border_rounded',
					'Link'				=> 'link'
				),
				'std' 			=> 'filled',
				'dependency'	=> array(
					'element'	=> 'link_type',
					'value'		=> array( 'btn' )
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Button Align', 'nm-framework-admin' ),
				'param_name'	=> 'button_align',
				'value'			=> array(
					'Left' 		=> 'left',
					'Center'	=> 'center',
					'Right' 	=> 'right'
				),
				'std' 			=> 'center',
				'dependency'	=> array(
					'element'	=> 'link_type',
					'value'		=> array( 'btn' )
				)
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Button Size', 'js_composer' ),
				'param_name' 	=> 'button_size',
				'description'	=> __( 'Select button size.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Mini'		=> 'xs',
					'Small'		=> 'sm',
					'Normal'	=> 'md',
					'Large'		=> 'lg'
				),
				'std' 			=> 'lg',
				'dependency'	=> array(
					'element'	=> 'link_type',
					'value'		=> array( 'btn' )
				)
			),
			array(
				'type' 			=> 'colorpicker',
				'heading' 		=> __( 'Button Color', 'js_composer' ),
				'param_name' 	=> 'button_color',
				'description'	=> __( 'Select button color.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'link_type',
					'value'		=> array( 'btn' )
				)
			),
			// /Dependency
			// Dependency: link_type - image
			array(
				'type' 			=> 'attach_image',
				'heading' 		=> __( 'Link Image', 'nm-framework-admin' ),
				'param_name' 	=> 'link_image_id',
				'description'	=> __( 'Select image from the media library.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'link_type',
					'value' 	=> array( 'image' )
				)
			),
			// /Dependency
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __('Lightbox Type', 'nm-framework-admin' ),
				'param_name' 	=> 'content_type',
				'description'	=> __( 'Select content type.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Image'	=> 'image',
					'Video'	=> 'iframe',
					'HTML'	=> 'inline'
				),
				'std' 			=> 'image'
			),
			// Dependency: content_type - image
			array(
				'type' 			=> 'attach_image',
				'heading' 		=> __( 'Lightbox Image', 'nm-framework-admin' ),
				'param_name' 	=> 'content_image_id',
				'description'	=> __( 'Select image from the media library.', 'nm-framework-admin' ),
				'dependency'	=> array(
					'element'	=> 'content_type',
					'value' 	=> array( 'image' )
				)
			),
			// /Dependency
			// Dependency: content_type - iframe, inline
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Lightbox Source', 'nm-framework-admin' ),
				'param_name' 	=> 'content_url',
				'description'	=> '
					Insert a Video URL or CSS selector for HTML content:
					<br /><br />
					<strong>YouTube video:</strong> http://www.youtube.com/watch?v=XXXXXXXXXXX
					<br />
					<strong>CSS selector:</strong> #contact-form
				',
				'dependency'	=> array(
					'element'	=> 'content_type',
					'value' 	=> array( 'iframe', 'inline' )
				)
			)
			// /Dependency
	   )
	) );
	