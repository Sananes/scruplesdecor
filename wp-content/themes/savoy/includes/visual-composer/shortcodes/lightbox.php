<?php
	
	// Shortcode: nm_lightbox
	function nm_shortcode_nm_lightbox( $atts, $content = NULL ) {
		nm_add_page_include( 'lightbox' );
		
		extract( shortcode_atts( array(
			'link_type'			=> 'link',
			
			'title'				=> 'View',
			
			'button_style'		=> 'filled',
			'button_align'		=> 'center',
			'button_size'		=> 'lg',
			'button_color'		=> '',
			
			'link_image_id'		=> '',
			
			'content_type'		=> 'image',
			'content_image_id'	=> '',
			'content_url'		=> ''
		), $atts ) );
		
		// Text/Button/Image
		if ( $link_type == 'btn' ) {
			$shortcode_params = 'link="url:%23" title="' . esc_attr( $title ) . '" align="' . esc_attr( $button_align ) . '" size="' . esc_attr( $button_size ) . '" style="' . esc_attr( $button_style ) . '"';
			$shortcode_params .= ( strlen( $button_color ) > 0 ) ? ' color="' . $button_color . '"' : '';
			
			$link = do_shortcode( '[nm_button ' . $shortcode_params . ']' );
		} else if ( $link_type == 'image' ) {
			$image_src = wp_get_attachment_image_src( $link_image_id, 'full' );
			$link_icon = ( $content_type == 'iframe' ) ? '<i class="nm-font nm-font-play-filled"></i>' : '';
			
			$link = '
				<img src="' . esc_url( $image_src[0] ) . '" />' .
				$link_icon . '
				<div class="nm-image-overlay"></div>
			';
		} else {
			$link = '<a href="#">' . esc_attr( $title ) . '</a>';
		}
		
		// Content
		if ( $content_type != 'image' ) {
			$data_attr = 'data-mfp-src="' . esc_url( $content_url ) . '"';
		} else {
			$image_src = wp_get_attachment_image_src( $content_image_id, 'full' );
			$data_attr = 'data-mfp-src="' . esc_url( $image_src[0] ) . '"';
		}
		
		return '
			<div class="nm-lightbox" data-mfp-type="' . esc_attr( $content_type ) . '" ' . $data_attr . '>' .
				$link . '
			</div>';
	}
	
	add_shortcode( 'nm_lightbox', 'nm_shortcode_nm_lightbox' );
	