<?php
	
	// VC element: nm_post_slider
	vc_map( array(
	   'name'			=> __( 'Post Slider', 'nm-framework-admin' ),
	   'category'		=> __( 'Content', 'nm-framework-admin' ),
	   'description'	=> __( 'Display posts from the blog', 'nm-framework-admin' ),
	   'base'			=> 'nm_post_slider',
	   'icon'			=> 'nm_post_slider',
	   'params'			=> array(
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Number of Posts', 'nm-framework-admin' ),
				'param_name' 	=> 'num_posts',
				'description' 	=> __( 'Enter max number of posts to display.', 'nm-framework-admin' ),
				'value' 		=> '8'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Category', 'nm-framework-admin' ),
				'param_name' 	=> 'category',
				'description'	=> __( 'Filter by post category.', 'nm-framework-admin' ),
				'value' 		=> nm_get_post_categories()
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Columns', 'nm-framework-admin' ),
				'param_name' 	=> 'columns',
				'description'	=> __( 'Select slider columns.', 'nm-framework-admin' ),
				'value' 		=> array(
					'3'	=> '3',
					'4'	=> '4',
					'5'	=> '5'
				),
				'std' 			=> '4'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Image Type', 'nm-framework-admin' ),
				'param_name' 	=> 'image_type',
				'description'	=> __( 'Select image-type to display.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Fluid'				=> 'fluid',
					'Background (CSS)'	=> 'background'
				),
				'std' 			=> 'fluid'
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Background Image Height', 'nm-framework-admin' ),
				'param_name' 	=> 'bg_image_height',
				'description' 	=> __( 'Enter a height for the background image.', 'nm-framework-admin' ),
				'value' 		=> '',
				'dependency'	=> array(
					'element'	=> 'image_type',
					'value'		=> 'background'
				)
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Post Excerpt', 'nm-framework-admin' ),
				'param_name' 	=> 'post_excerpt',
				'description'	=> __( 'Display post excerpt.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			)
	   )
	) );
	