<?php
	
	// Shortcode: nm_testimonial
	function nm_shortcode_nm_testimonial( $atts, $content = NULL ) {
		shortcode_atts( array(
			'image_id'		=> '',
			'signature'		=> '',
			'company'		=> '',
			'description'	=> ''
		), $atts );
		
		// Image
		$image_class = $image_output = '';
		if ( strlen( $atts['image_id'] ) > 0 ) {
			$image_class = ' has-image';
			
			$image_src = wp_get_attachment_image_src( $atts['image_id'], 'full' );
			$image_output = '<div class="nm-testimonial-image"><img src="' . esc_url( $image_src[0] ) . '" /></div>';
		}
		
		// Company signature
		$company_output = ( isset( $atts['company'] ) ) ? ', <em>' . $atts['company'] . '</em>' : '';
		
		return '
			<div class="nm-testimonial' . $image_class . '">' .
				$image_output . '
				<div class="nm-testimonial-content">
					<div class="nm-testimonial-description">' . $atts['description'] . '</div>
					<div class="nm-testimonial-author">
						<span>' . $atts['signature'] . '</span>' .
						$company_output . '
					</div>
				</div>
			</div>';
	}
	
	add_shortcode( 'nm_testimonial', 'nm_shortcode_nm_testimonial' );