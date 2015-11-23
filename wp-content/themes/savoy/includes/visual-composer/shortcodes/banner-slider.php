<?php
	
	// Shortcode: nm_banner_slider
	function nm_shortcode_banner_slider( $atts, $content = NULL ) {
		nm_add_page_include( 'banner-slider' );
		
		extract( shortcode_atts( array(
			'slider_type'		=> 'boxed',
			'adaptive_height'	=> '',
			'arrows' 			=> '',
			'pagination'		=> '',
			'pagination_color'	=> 'gray',
			'infinite'			=> '',
			'autoplay'			=> '',
			'background_color'	=> ''
		), $atts ) );
		
		$slider_class = 'nm-banner-slider slider-type-' . esc_attr( $slider_type ) . ' slick-slider slick-controls-' . esc_attr( $pagination_color );
		$slider_settings_data = ' ';
		
		// Adaptive Height
		if ( strlen( $adaptive_height ) > 0 ) $slider_settings_data .= 'data-adaptive-height="true" ';
		
		// Arrows
		if ( strlen( $arrows ) > 0 ) { $slider_settings_data .= 'data-arrows="true" '; }
		
		// Pagination
		if ( strlen( $pagination ) > 0 ) {
			$slider_class .= ' slick-dots-inside';
			$slider_settings_data .= 'data-dots="true" ';
		} else {
			$slider_class .= ' slick-dots-disabled';
		}
		
		// Autoplay
		if ( strlen( $autoplay ) > 0 ) $slider_settings_data .= 'data-autoplay="true" data-autoplay-speed="' . intval( $autoplay ) . '" ';
		
		// Infinite loop
		if ( strlen( $infinite ) > 0 ) $slider_settings_data .= 'data-infinite="true"';
		
		// Background color
		$background_color_style = ( strlen( $background_color ) > 0 ) ? 'style="background-color: ' . esc_attr( $background_color ) . '"' : '';
				
		$output = '<div class="' . $slider_class . '"' . $slider_settings_data . $background_color_style . '>' . do_shortcode( $content ) . '</div>';
						
		return $output;
	}
	
	add_shortcode( 'nm_banner_slider', 'nm_shortcode_banner_slider' );
	