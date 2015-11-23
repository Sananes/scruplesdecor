<?php
	
	// Shortcode: nm_feature
	function nm_shortcode_feature( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'title'					=> '',
			'subtitle'				=> '',
			'icon_type'				=> 'icon',
			'icon'					=> '',
			'icon_style'			=> 'simple',
			'icon_background_color'	=> '',
			'icon_color'			=> '',
			'image_id'				=> '',
			'image_style'			=> 'default',
			'layout'				=> 'default',
			'top_offset'			=> '',
			'bottom_spacing'		=> 'none',
			'link' 					=> ''
		), $atts ) );
		
		// Prepare icon/image
		if ( $icon_type === 'icon' ) {
			if ( strlen( $icon ) > 0 ) {
				// Enqueue font icon styles
				wp_enqueue_style( 'pe-icons-filled', NM_THEME_URI . '/css/font-icons/pe-icon-7-filled/css/pe-icon-7-filled.css' );
				wp_enqueue_style( 'pe-icons-stroke', NM_THEME_URI . '/css/font-icons/pe-icon-7-stroke/css/pe-icon-7-stroke.css' );
				
				// Background/border color
				$icon_background_color_style = '';
				if ( strlen( $icon_background_color ) > 0 ) {
					if ( $icon_style == 'background' ) {
						$icon_background_color_style = ' style="background-color: ' . $icon_background_color . '"';
					} else if ( $icon_style == 'border' ) {
						$icon_background_color_style = ' style="border-color: ' . $icon_background_color . '"';
					}
				}
				
				// Icon color
				$icon_color_style = '';
				if ( strlen( $icon_color ) > 0 ) {
					$icon_color_style = ' style="color: ' . $icon_color . ';"';
				}
				
				$icon = '<div class="nm-feature-icon"' . $icon_background_color_style . '><i class="' . $icon . '"' . $icon_color_style . '></i></div>';
			}
		} else {
			$icon_style = 'image-' . $image_style;
			
			if ( strlen( $image_id ) > 0 ) {
				$image_src = wp_get_attachment_image_src( $image_id, 'full' );
				$icon = '<div class="nm-feature-icon"><img src="' . esc_url( $image_src[0] ) . '" alt="' . esc_attr( $title ) . '" /></div>';
			}
		}
		
		$title = ( strlen( $title ) > 0 ) ? '<h2>' . esc_attr( $title ) . '</h2>' : '';
		$subtitle = ( strlen( $subtitle ) > 0 ) ? '<h3>' . esc_attr( $subtitle ) . '</h3>' : '';
		
		// Button
		if ( strlen( $link ) > 0 ) {
			$link = vc_build_link( $link );
			$button = '<a href="' . esc_url( $link['url'] ) . '" title="' . esc_attr( $link['title'] ) . '" class="vc_btn vc_btn_md vc_btn_link"><span class="vc_btn_title">' . esc_attr( $link['title'] ) . '</span></a>';
		} else {
			$button = '';
		}
		
		$top_offset = ( strlen( $top_offset ) > 0 ) ? ' style="padding-top: ' . intval( $top_offset ) . 'px";' : '';
				
		return '
			<div class="nm-feature layout-' . esc_attr( $layout ) . ' icon-style-' . esc_attr( $icon_style ) . ' bottom-spacing-' . esc_attr( $bottom_spacing ) . '">' .
				$icon . '
				<div class="nm-feature-content"' . $top_offset . '>' . 
					$title .
					$subtitle . '
					<div class="wpb_text_column">' . wpb_js_remove_wpautop( $content, true ) . '</div>' .
					$button . '
				</div>
			</div>';
	}
	
	add_shortcode( 'nm_feature', 'nm_shortcode_feature' );
	