<?php
	
	// Shortcode: nm_banner
	function nm_shortcode_banner( $atts, $content = NULL ) {
		nm_add_page_include( 'banner' );
		
		extract( shortcode_atts( array(
			'layout'			=> 'full',
			'title' 			=> '',
			'subtitle' 			=> '',
			'title_size'		=> 'small',
			'link_source'		=> 'custom',
			'custom_link'		=> '',
			'shop_link_title'	=> '',
			'shop_link'			=> '',
			'link_type'			=> 'banner_link',
			'text_color_scheme'	=> 'dark',
			'text_position'		=> 'h_center-v_center',
			'text_alignment'	=> 'align_left',
			'text_width'		=> '',
			'text_padding'		=> '',
			'text_animation'	=> '',
			'image_id'			=> '',
			'alt_image_id'		=> '',
			'image_type'		=> 'fluid',
			'height'			=> '',
			'background_color'	=> ''
		), $atts ) );
		
		// Enqueue CSS animation styles
		wp_enqueue_style( 'animate', NM_THEME_URI . '/css/third-party/animate.css', array(), '1.0', 'all' );
		
		// Centered content class
		$banner_class = ( $layout == 'boxed-full-parent' ) ? 'content-boxed full-width-parent ' : 'content-' . esc_attr( $layout ) . ' ';
		
		// Background color
		$background_color_style = ( strlen( $background_color ) > 0 ) ? 'background-color: ' . esc_attr( $background_color ) . ';' : '';
		
		// Image
		$image_output = '';
		if ( strlen( $image_id ) > 0 ) {
			$image = wp_get_attachment_image_src( $image_id, 'full' );
			
			if ( $image_type == 'fluid' ) {
				$height_style = ''; // Remove the banner height if a regular image is used
				$banner_class .= 'image-type-fluid';
				$image_output .= '<img src="' . esc_url( $image[0] ) . '" />';
				
				if ( strlen( $alt_image_id ) > 0 ) {
					$banner_class .= ' has-alt-image';
					$alt_image = wp_get_attachment_image_src( $alt_image_id, 'full' );
					$image_output .= '<img src="' . esc_url( NM_THEME_URI . '/img/transparent.gif' ) . '" class="nm-banner-alt-image img" data-src="' . esc_url( $alt_image[0] ) . '" />';
				}
			} else {
				// Image height style
				$height = intval( $height );
				$height_style = ( $height > 0 ) ? 'height: ' . $height . 'px; ' : '';
				
				$banner_class .= 'image-type-css';
				$image_output .= '<div class="nm-banner-image" style="' . $height_style . 'background-image: url(' . esc_url( $image[0] ) . ');"></div>';
				
				if ( strlen( $alt_image_id ) > 0 ) {
					$banner_class .= ' has-alt-image';
					$alt_image = wp_get_attachment_image_src( $alt_image_id, 'full' );
					$image_output .= '<div class="nm-banner-image nm-banner-alt-image" data-src="' . esc_url( $alt_image[0] ) . '" style="' . $height_style . '"></div>';
				}
			}
			
			$banner_height_style = '';
		} else {
			// No image class
			$banner_class .= 'image-type-none';
			
			// Banner height style
			$height = intval( $height );
			$banner_height_style = ( $height > 0 ) ? 'height: ' . $height . 'px; ' : '';
		}
		
		// CSS animation
		if ( strlen( $text_animation ) > 0 ) {
			$animation_class = ' animated';
			$animation_data = ' data-animate="' . esc_attr( $text_animation ) . '"';
		} else {
			$animation_class = '';
			$animation_data = '';
		}
		
		// Text-color class
		$banner_class .= ' text-color-' . $text_color_scheme;
		
		// Text
		$content_output = '';
		$content_output .= ( strlen( $title ) > 0 ) ? '<h2 class="' . esc_attr( $title_size ) . '">' . $title . '</h2>' : '';
		$content_output .= ( strlen( $subtitle ) > 0 ) ? '<h3 class="nm-alt-font">' . $subtitle . '</h3>' : '';
		
		// Link
		$link_is_custom = ( $link_source == 'custom' ) ? true : false;
		$link = ( $link_is_custom ) ? $custom_link : $shop_link;
		$banner_link_open_output = $banner_link_close_output = '';
		$link_class = '';
		if ( strlen( $link ) > 0 ) {
			if ( $link_is_custom ) {
				$banner_link = vc_build_link( $link );
			} else {
				$link_class = ' nm-banner-shop-link';
				$banner_link = array( 'title' => $shop_link_title, 'url' => htmlspecialchars( $link ) );
			}
			
			if ( $link_type === 'banner_link' ) {
				$banner_link_open_output = '<a href="' . esc_url( $banner_link['url'] ) . '" class="nm-banner-link nm-banner-link-full' . $link_class . '">';
				$banner_link_close_output = '</a>';
			} else {
				$content_output .= '<a href="' . esc_url( $banner_link['url'] ) . '" class="nm-banner-link' . $link_class . '">' . $banner_link['title'] . '</a>';
			}
		}
		
		// Display banner content?
		if ( strlen( $content_output ) > 0 ) {
			// Text position array
			$text_position = explode( '-', $text_position );
					
			// Text width
			$text_styles = '';
			$text_width = intval( $text_width );
			if ( $text_width > 0 ) {
				$text_styles = 'width: ' . $text_width . '%; ';
			}
			
			// Text padding
			if ( strlen( $text_padding ) > 0 ) {
				$padding = intval( $text_padding ) . '% ';
				$padding_top = '0 ';
				$padding_bottom = '0 ';
				
				if ( $text_position[1] === 'v_top' ) {
					$padding_top = $padding;
				} else if ( $text_position[1] === 'v_bottom' ) {
					$padding_bottom = $padding;
				}
							
				$text_styles .= 'padding: ' . $padding_top . $padding . $padding_bottom . $padding . ';';
			}
			
			// Content markup
			$content_output = '
				<div class="nm-banner-content">
					<div class="nm-banner-content-inner">
						<div class="nm-banner-text ' . $text_position[0] . ' ' . $text_position[1] . ' ' . $text_alignment . '" style="' . $text_styles . '">
							<div class="nm-banner-text-inner' . $animation_class . '"' . $animation_data . '>' . $content_output . '</div>
						</div>
					</div>
				</div>';
		}
		
		// Banner markup
		$banner_output = '
			<div class="nm-banner ' . $banner_class . '" style="' . $banner_height_style . $background_color_style . '">' .
				$banner_link_open_output .
					$image_output .
					$content_output .
				$banner_link_close_output . '
			</div>';
		
		return $banner_output;
	}
	
	add_shortcode( 'nm_banner', 'nm_shortcode_banner' );
	