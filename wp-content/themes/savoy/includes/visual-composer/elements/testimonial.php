<?php
	
	// VC element: nm_social_profiles
	vc_map( array(
	   'name'			=> __( 'Testimonial', 'nm-framework-admin' ),
	   'category'		=> __( 'Content', 'nm-framework-admin' ),
	   'description'	=> __( 'User testimonial', 'nm-framework-admin' ),
	   'base'			=> 'nm_testimonial',
	   'icon'			=> 'nm_testimonial',
	   'params'			=> array(
			array(
				'type' 			=> 'attach_image',
				'heading' 		=> __( 'Image', 'nm-framework-admin' ),
				'param_name' 	=> 'image_id',
				'description'	=> __( 'Author image.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Signature', 'nm-framework-admin' ),
				'param_name' 	=> 'signature',
				'description'	=> __( 'Author signature.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Company', 'nm-framework-admin' ),
				'param_name' 	=> 'company',
				'description'	=> __( 'Company signature.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textarea',
				'heading' 		=> __( 'Description', 'nm-framework-admin' ),
				'param_name' 	=> 'description',
				'description'	=> __( 'Testimonial description.', 'nm-framework-admin' )
			)
	   )
	) );
