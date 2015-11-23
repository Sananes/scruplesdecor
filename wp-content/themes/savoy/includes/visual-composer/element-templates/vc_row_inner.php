<?php
	extract( shortcode_atts( array(
		'el_id'        		=> '',
		'el_class'        	=> '',
		'css' 				=> '',
		// Custom params
		'type' 				=> 'full'
	), $atts ) );
	
	$output = '';
	$wrapper_atts = array();
	
	// Custom ID
	if ( ! empty( $el_id ) ) {
		$wrapper_atts[] = 'id="' . esc_attr( $el_id ) . '"';
	}
	
	// Custom class
	$el_class = $this->getExtraClass( $el_class );
	
	// Row class
	$row_class = 'nm-row nm-row-' . $type . ' inner ' . $el_class . vc_shortcode_custom_css_class( $css, ' ' );
	$wrapper_atts[] = 'class="' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $row_class, $this->settings['base'], $atts ) . '"';
	
	// Output
	$output .= '<div ' . implode( ' ', $wrapper_atts ) . '>';
	$output .= wpb_js_remove_wpautop( $content );
	$output .= '</div>';
	
	echo $output;