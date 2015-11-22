<?php
	global $nm_vcomp_stock;
	
	
	/* Remove default elements
	================================================== */
	
	if ( ! $nm_vcomp_stock ) {
		//vc_remove_element( 'vc_column_text' );
		//vc_remove_element( 'vc_separator' );
		vc_remove_element( 'vc_text_separator' );
		//vc_remove_element( 'vc_message' );
		//vc_remove_element( 'vc_facebook' );
		//vc_remove_element( 'vc_tweetmeme' );
		//vc_remove_element( 'vc_googleplus' );
		//vc_remove_element( 'vc_pinterest' );
		//vc_remove_element( 'vc_toggle' );
		//vc_remove_element( 'vc_single_image' );
		vc_remove_element( 'vc_gallery' );
		vc_remove_element( 'vc_images_carousel' );
		//vc_remove_element( 'vc_tabs' );
		//vc_remove_element( 'vc_tour' );
		//vc_remove_element( 'vc_accordion' );
		vc_remove_element( 'vc_teaser_grid' );
		vc_remove_element( 'vc_posts_grid' );
		vc_remove_element( 'vc_carousel' );
		vc_remove_element( 'vc_posts_slider' );
		//vc_remove_element( 'vc_widget_sidebar' );
		vc_remove_element( 'vc_button' );
		vc_remove_element( 'vc_button2' );
		vc_remove_element( 'vc_cta_button' );
		vc_remove_element( 'vc_cta_button2' );
		//vc_remove_element( 'vc_video' );
		vc_remove_element( 'vc_gmaps' );
		//vc_remove_element( 'vc_raw_html' );
		//vc_remove_element( 'vc_raw_js' );
		vc_remove_element( 'vc_flickr' );
		//vc_remove_element( 'vc_progress_bar' );
		//vc_remove_element( 'vc_pie' );
		//vc_remove_element( 'vc_empty_space' );
		//vc_remove_element( 'vc_custom_heading' );
		vc_remove_element( 'vc_basic_grid' );
		vc_remove_element( 'vc_media_grid' );
		vc_remove_element( 'vc_masonry_grid' );
		vc_remove_element( 'vc_masonry_media_grid' );
		vc_remove_element( 'vc_icon' );
		vc_remove_element( 'vc_btn' );
		vc_remove_element( 'vc_cta' );
		vc_remove_element( 'vc_round_chart' );
		vc_remove_element( 'vc_line_chart' );
		vc_remove_element( 'vc_tta_tabs' );
		vc_remove_element( 'vc_tta_tour' );
		vc_remove_element( 'vc_tta_accordion' );
		vc_remove_element( 'vc_tta_section' );
		vc_remove_element( 'vc_tta_pageable' );
	}
	
	
	/* Remove third-party plugin elements */
	function nm_vc_remove_plugin_elements() {
		vc_remove_element( 'contact-form-7' );
	}
	add_action( 'vc_after_set_mode', 'nm_vc_remove_plugin_elements', 100 );
	
	
	// WordPress default Widgets (Appearance > Widgets)
	if ( ! $nm_vcomp_stock ) {
		vc_remove_element( 'vc_wp_search' );
		vc_remove_element( 'vc_wp_meta' );
		vc_remove_element( 'vc_wp_recentcomments' );
		vc_remove_element( 'vc_wp_calendar' );
		vc_remove_element( 'vc_wp_pages' );
		vc_remove_element( 'vc_wp_tagcloud' );
		vc_remove_element( 'vc_wp_custommenu' );
		vc_remove_element( 'vc_wp_text' );
		vc_remove_element( 'vc_wp_posts' );
		vc_remove_element( 'vc_wp_categories' );
		vc_remove_element( 'vc_wp_archives' );
		vc_remove_element( 'vc_wp_rss' );
	}
		
	
	/* Custom element params
	================================================== */
	
	// Element: vc_row
	vc_remove_param( 'vc_row', 'full_width' );
	vc_remove_param( 'vc_row', 'full_height' );
	vc_remove_param( 'vc_row', 'content_placement' );
	vc_remove_param( 'vc_row', 'video_bg_parallax' );
	vc_add_param( 'vc_row', array(
		'type' 			=> 'dropdown',
		'heading' 		=> __( 'Row Type', 'nm-framework-admin' ),
		'param_name' 	=> 'type',
		'description'	=> __( 'Select row layout.', 'nm-framework-admin' ),
		'weight'		=> 1,
		'value' 		=> array(
			'Full'				=> 'full',
			'Full (no padding)'	=> 'full-nopad',
			'Boxed' 			=> 'boxed'
		)
	) );
	vc_add_param( 'vc_row', array(
		'type' 			=> 'textfield',
		'heading' 		=> __( 'Maximum Width', 'js_composer' ),
		'param_name' 	=> 'max_width',
		'value' 		=> '',
		'description'	=> __( 'Optional: Enter a maximum width (numbers only).', 'js_composer' ),
		'weight'		=> 1
	) );
	vc_add_param( 'vc_row', array(
		'type' 			=> 'textfield',
		'heading' 		=> __( 'Minimum Height', 'js_composer' ),
		'param_name' 	=> 'min_height',
		'value' 		=> '',
		'description'	=> __( 'Optional: Enter a minimum height (numbers only).', 'js_composer' ),
		'weight'		=> 1
	) );
	// Modify "vc_row - parallax" param (instead of removing param and adding new)
	function nm_vc_row_param_parallax() {
		// Get param values
		$param = WPBMap::getParam( 'vc_row', 'parallax' );
		
		// Replace param values
		$param['value'] = array(
			__( 'None', 'js_composer' ) => '',
			__( 'Static (fixed background)', 'nm-framework-admin' ) => 'static'
		);
		
		// Finally "mutate" param with new values
		vc_update_shortcode_param( 'vc_row', $param );
	}
	add_action( 'vc_after_init', 'nm_vc_row_param_parallax' );
	
				
	// Element: vc_row_inner
	vc_add_param( 'vc_row_inner', array(
		'type' 			=> 'dropdown',
		'heading' 		=> __( 'Row Type', 'nm-framework-admin' ),
		'param_name' 	=> 'type',
		'value' 		=> array(
			'Full Width'	=> 'full_width',
			'Boxed' 		=> 'boxed'
		)
	) );
	
	
	// Element: vc_column_text
	vc_remove_param( 'vc_column_text', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_separator
	vc_remove_param( 'vc_separator', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_separator', 'color' );
	vc_remove_param( 'vc_separator', 'align' );
	vc_remove_param( 'vc_separator', 'accent_color' );
	vc_remove_param( 'vc_separator', 'style' );
	vc_remove_param( 'vc_separator', 'el_width' );
	vc_add_param( 'vc_separator', array(
		'type' 			=> 'textfield',
		'heading' 		=> __( 'Title', 'js_composer' ),
		'param_name' 	=> 'title',
		'holder' 		=> 'div',
		'value' 		=> '',
		'description'	=> __( 'Separator title.', 'js_composer' ),
		'weight'		=> 1
	) );
	vc_add_param( 'vc_separator', array(
		'type' 			=> 'dropdown',
		'heading' 		=> __( 'Title Size', 'nm-framework-admin' ),
		'param_name' 	=> 'title_size',
		'description'	=> __( 'Select title size.', 'nm-framework-admin' ),
		'value' 		=> array(
			'Large' 	=> 'large',
			'Medium'	=> 'medium',
			'Small' 	=> 'small',
		),
		'weight'		=> 1
	) );
	vc_add_param( 'vc_separator', array(
		'type' 			=> 'dropdown',
		'heading' 		=> __( 'Title position', 'js_composer' ),
		'param_name'	=> 'title_align',
		'value' 		=> array(
			__( 'Align center', 'js_composer' )	=> 'separator_align_center',
			__( 'Align left', 'js_composer' )	=> 'separator_align_left',
			__( 'Align right', 'js_composer' )	=> "separator_align_right"
		),
		'description'	=> __( 'Select title location.', 'js_composer' ),
		'weight'		=> 1
	) );
	vc_add_param( 'vc_separator', array(
		'type' 			=> 'colorpicker',
		'heading' 		=> __( 'Custom Border Color', 'js_composer' ),
		'param_name' 	=> 'accent_color',
		'description'	=> __( 'Select border color for your element.', 'js_composer' ),
		'weight'		=> 1
	) );
	
	
	// Element: vc_message
	vc_remove_param( 'vc_message', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_message', 'color' );
	vc_remove_param( 'vc_message', 'message_box_style' );
	vc_remove_param( 'vc_message', 'style' );
	vc_remove_param( 'vc_message', 'message_box_color' );
	vc_remove_param( 'vc_message', 'icon_type' );
	vc_remove_param( 'vc_message', 'icon_fontawesome' );
	vc_remove_param( 'vc_message', 'icon_openiconic' );
	vc_remove_param( 'vc_message', 'icon_typicons' );
	vc_remove_param( 'vc_message', 'icon_entypo' );
	vc_remove_param( 'vc_message', 'icon_linecons' );
	vc_remove_param( 'vc_message', 'icon_pixelicons' );
	vc_remove_param( 'vc_message', 'css_animation' );
	vc_add_param( 'vc_message', array(
		'type' 			=> 'dropdown',
		'heading' 		=> __( 'Message Box Presets', 'js_composer' ),
		'param_name'	=> 'color',
		'value' 		=> array(
			'Information'	=> 'info',
			'Warning'		=> 'warning',
			'Success' 		=> 'success',
			'Error' 		=> 'danger'
		),
		'description' => __( 'Select predefined message box design or choose "Custom" for custom styling.', 'js_composer' ),
		'weight'		=> 1
	) );
	
	
	// Element: vc_facebook
	vc_remove_param( 'vc_facebook', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_googleplus
	vc_remove_param( 'vc_googleplus', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_tweetmeme
	vc_remove_param( 'vc_tweetmeme', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_pinterest
	vc_remove_param( 'vc_pinterest', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_toggle
	vc_remove_param( 'vc_toggle', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_toggle', 'style' );
	vc_remove_param( 'vc_toggle', 'color' );
	vc_remove_param( 'vc_toggle', 'size' );
	vc_remove_param( 'vc_toggle', 'css_animation' );
	
	
	// Element: vc_single_image
	vc_remove_param( 'vc_single_image', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_single_image', 'title' );
	// Modify "vc_single_image - onclick" param (instead of removing param and adding new)
	function nm_vc_single_image_param_onclick() {
		// Get param values
		$param = WPBMap::getParam( 'vc_single_image', 'onclick' );
		
		// Replace param values
		$param['value'] = array(
			__( 'None', 'js_composer' ) => '',
			__( 'Link to large image', 'js_composer' ) => 'img_link_large',
			__( 'Open custom link', 'js_composer' ) => 'custom_link'
		);
		
		// Finally "mutate" param with new values
		vc_update_shortcode_param( 'vc_single_image', $param );
	}
	add_action( 'vc_after_init', 'nm_vc_single_image_param_onclick' );
	
	
	// Element: vc_tabs
	vc_remove_param( 'vc_tabs', 'title' );
	
	
	// Element: vc_tour
	vc_remove_param( 'vc_tour', 'title' );
	
	
	// Element: vc_accordion
	vc_remove_param( 'vc_accordion', 'title' );
	
	
	// Element: vc_widget_sidebar
	vc_remove_param( 'vc_widget_sidebar', 'title' );
	
	
	// Element: vc_video
	vc_remove_param( 'vc_video', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_video', 'title' );
	
	
	// Element: vc_progress_bar
	vc_remove_param( 'vc_progress_bar', 'css' ); // Disable "Design Options" tab
	vc_remove_param( 'vc_progress_bar', 'title' );
	vc_remove_param( 'vc_progress_bar', 'options' );
	
	
	// Element: vc_pie
	vc_remove_param( 'vc_pie', 'css' ); // Disable "Design Options" tab
	
		
	// Element: vc_empty_space
	vc_remove_param( 'vc_empty_space', 'css' ); // Disable "Design Options" tab
	
	
	// Element: vc_custom_heading
	vc_remove_param( 'vc_custom_heading', 'css' ); // Disable "Design Options" tab
	