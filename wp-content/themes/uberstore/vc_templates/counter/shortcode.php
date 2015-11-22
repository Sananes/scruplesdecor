<?php function thb_counter( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'image'      => '',
       	'color'      => '',
       	'icon'			 => 'fa-camera',
       	'speed'			 => '1500',
       	'heading'		 => ''
    ), $atts));
	$btn = '';
	wp_enqueue_script('countto');
	// Image & Icon
	if ($image) {
		$img_id = preg_replace('/[^\d]/', '', $image);
		$img = wp_get_attachment_image($img_id, 'full', false, array(
			'alt'   => trim(strip_tags( get_post_meta($img_id, '_wp_attachment_image_alt', true) )),
		));
  } else {
  	$icon = '<i class="fa '.$icon.'"></i>';
  }


	// Content
	
	$out = '<div class="counter">';
	
	$out .= '<span' . ($color ? ' style="color:'.$color.';"' : '') . ($image ? ' class="img"' : '') .' >' . ($image ? $img : $icon) .'</span>';
	
	$out .= '<figure class="timer" data-countto="'.$content.'" data-speed="'.$speed.'"></figure>
			'.($heading ? '<div class="timertitle animation bottom-to-top">'.$heading.'</div>' : '').'
	</div>';
	
  return $out;
}
add_shortcode('thb_counter', 'thb_counter');
