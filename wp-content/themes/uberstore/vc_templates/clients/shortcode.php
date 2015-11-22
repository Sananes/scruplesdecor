<?php function thb_clients( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'images'     => ''
    ), $atts));
	$all_images = explode(',', $images);
	$output = '';
	$output .= '<div class="carousel-container"><div class="owl carousel clients row" data-columns="6" data-navigation="true">';
	foreach($all_images as $img_id) {
			$img = wp_get_attachment_image_src($img_id, 'full');
			$resized = aq_resize( $img[0], 185, 130, true, false);
	    $output .= '<div class="client two columns">';
	    $output .= '<img src="'. $resized[0].'" />';
	    $output .= '</div>';
	}
	$output .= '</div></div>';
	return $output;

}
add_shortcode('thb_clients', 'thb_clients');
