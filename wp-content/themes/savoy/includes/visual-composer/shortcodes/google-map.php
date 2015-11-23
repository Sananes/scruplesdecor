<?php
	
	// Shortcode: nm_gmap
	function nm_shortcode_gmap( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'address'		=> 'Amsterdam, The Netherlands',
			'map_type'		=> 'roadmap_custom',
			'map_style'		=> 'paper',
			'height'		=> '',
			'zoom'			=> '18',
			'zoom_controls'	=> '0',
			'scroll_zoom'	=> '0',
			'touch_drag'	=> '0',
			'marker_icon'	=> ''
		), $atts ) );
		
		// Enqueue Google Map scripts
		wp_enqueue_script( 'google-maps-api', '//maps.google.com/maps/api/js?sensor=false', '', '', true );
		wp_enqueue_script( 'nm-google-maps', NM_THEME_URI . '/js/plugins/google-maps.min.js', array( 'jquery', 'google-maps-api' ), NM_THEME_VERSION, true );
		
		static $i = 0;
		$i++;
		
		$marker_icon_url = ( strlen( $marker_icon ) > 0 ) ? wp_get_attachment_url( $marker_icon ) : '';
		
		$map_data = '
			data-address="' . esc_attr( $address ) . '" 
			data-map-type="' . esc_attr( $map_type ) . '" 
			data-map-style="' . esc_attr( $map_style ) . '" 
			data-zoom="' . esc_attr( $zoom ) . '" 
			data-zoom-controls="' . esc_attr( $zoom_controls ) . '" 
			data-scroll-zoom="' . esc_attr( $scroll_zoom ) . '" 
			data-touch-drag="' . esc_attr( $touch_drag ) . '" 
			data-marker-icon="' . esc_url( $marker_icon_url ) . '"
		';
		
		$map_classes = 'nm-gmap';
		$map_styles = '';
		
		if ( strlen( $height > 0 ) ) {
			$map_styles = ' style="height:' . intval( $height ) . 'px;"';
		} else {
			$map_classes .= ' aspect-ratio';
		}
		
		return '<div id="nm-gmap-' . $i . '" ' . $map_data . ' class="' . $map_classes . '"' . $map_styles . '></div>';
	}
	
	add_shortcode( 'nm_gmap', 'nm_shortcode_gmap' );
	