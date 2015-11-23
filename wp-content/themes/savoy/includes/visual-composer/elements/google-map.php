<?php
	
	// VC element: nm_gmap
	vc_map( array(
	   'name'			=> __( 'Google Map', 'nm-framework-admin' ),
	   'category'		=> __( 'Content', 'nm-framework-admin' ),
	   'description'	=> __( 'Embed a Google map', 'nm-framework-admin' ),
	   'base'			=> 'nm_gmap',
	   'icon'			=> 'nm_gmap',
	   'params'			=> array(
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Address', 'nm-framework-admin' ),
				'param_name' 	=> 'address',
				'description'	=> __( 'Address for the map marker (you can type it in any language).', 'nm-framework-admin' ),
				'value' 		=> 'Amsterdam, The Netherlands'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Map Type', 'nm-framework-admin' ),
				'param_name' 	=> 'map_type',
				'description'	=> __( 'Select a map type.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Custom Roadmap'						=> 'roadmap_custom',
					'Default Roadmap (no custom styles)'	=> 'roadmap',
					'Satellite'								=> 'satellite',
					'Terrain'								=> 'terrain'
				),
				'std' 			=> 'roadmap_custom'
			),
			array(
				'type' 			=> 'dropdown',
				'heading' 		=> __( 'Map Style', 'nm-framework-admin' ),
				'param_name' 	=> 'map_style',
				'description'	=> __( 'Select a map style.', 'nm-framework-admin' ),
				'value' 		=> array(
					'Clean Flat'			=> 'clean_flat',
					'Grayscale'				=> 'grayscale',
					'Cooltone Grayscale'	=> 'cooltone_grayscale',
					'Light Monochrome'		=> 'light_monochrome',
					'Dark Monochrome'		=> 'dark_monochrome',
					'Paper'					=> 'paper',
					'Countries'				=> 'countries'
				),
				'std' 			=> 'paper'
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Map Height', 'nm-framework-admin' ),
				'param_name' 	=> 'height',
				'description'	=> __( 'Enter a map height.', 'nm-framework-admin' )
			),
			array(
				'type' 			=> 'textfield',
				'heading' 		=> __( 'Zoom Level', 'nm-framework-admin' ),
				'param_name' 	=> 'zoom',
				'description' 	=> __( 'Default map zoom level (1 - 20).', 'nm-framework-admin' ),
				'value' 		=> '18',
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Zoom Controls', 'nm-framework-admin' ),
				'param_name' 	=> 'zoom_controls',
				'description' 	=> __( 'Display map zoom controls.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Scroll Zoom', 'nm-framework-admin' ),
				'param_name' 	=> 'scroll_zoom',
				'description' 	=> __( 'Enable mouse-wheel zoom.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'checkbox',
				'heading' 		=> __( 'Touch Drag', 'nm-framework-admin' ),
				'param_name' 	=> 'touch_drag',
				'description' 	=> __( 'Enable touch-drag on mobile devices.', 'nm-framework-admin' ),
				'value'			=> array(
					__( 'Enable', 'nm-framework-admin' )	=> '1'
				)
			),
			array(
				'type' 			=> 'attach_image',
				'heading' 		=> __( 'Marker Icon', 'nm-framework-admin' ),
				'param_name' 	=> 'marker_icon',
				'description' 	=> __( 'Custom marker icon.', 'nm-framework-admin' )
			)
	   )
	) );
	