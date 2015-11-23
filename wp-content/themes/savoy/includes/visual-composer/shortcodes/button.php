<?php
	
	// Shortcode: nm_button
	function nm_shortcode_button( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'title'	=> __( 'Button with Text', 'nm-framework-admin' ),
			'link' 	=> '',
			'style'	=> 'filled',
			'color'	=> '',
			'size' 	=> 'lg',
			'align'	=> 'left'
		), $atts ) );
		
		// Parse link
		$link = ( $link == '||' ) ? '' : $link;
		$link = vc_build_link( $link );
		$a_href = $link['url'];
		$a_title = $link['title'];
		$a_target = $link['target'];
		
		// Class
		$button_class = 'nm_btn nm_btn_' . esc_attr( $size ) . ' nm_btn_' . esc_attr( $style );
		
		// Background style
		$button_style = $bg_style = '';
		if ( strlen( $color ) > 0 ) {
			if ( strpos( $style, 'border' ) !== false ) {
				$button_style = ' style="color:' . $color . ';"';
			} else {
				$bg_style = ' style="background-color:' . $color . ';"';
			}
		}
		
		$output = '
			<div class="nm_btn_align_' . $align . '">
				<a href="' . esc_url( $a_href ) . '" class="' . $button_class . '" title="' . esc_attr( $a_title ) . '" target="' . esc_attr( $a_target ) . '"' . $button_style . '>
					<span class="nm_btn_title">' . esc_attr( $title ) . '</span>
					<span class="nm_btn_bg"' . $bg_style . '></span>
				</a>
			</div>';
		
		return $output;
	}
	
	add_shortcode( 'nm_button', 'nm_shortcode_button' );
