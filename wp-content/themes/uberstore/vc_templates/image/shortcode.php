<?php function thb_image( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'image'      => '',
       	'target_blank' => false,
       	'img_size'	 => 'thumbnail',
       	'img_link'       => '',
       	'img_link_target'       => '',
       	'alignment'	 => '',
       	'lightbox'	 => '',
       	'size'			 => 'small',
       	'animation'	 => false
    ), $atts));
	
	$img_id = preg_replace('/[^\d]/', '', $image);
	//$img = wpb_getImageBySize(array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => $animation ));
	$img = wp_get_attachment_image($img_id, $img_size, false, array(
		'class'	=> $animation,
		'alt'   => trim(strip_tags( get_post_meta($img_id, '_wp_attachment_image_alt', true) )),
	));
	if ( $img == NULL ) $img = '<img src="http://placekitten.com/g/400/300" />';
  
  $link_to = $c_lightbox = '';
  if ($lightbox==true) {
      $link_to = wp_get_attachment_image_src( $img_id, 'large');
      $link_to = $link_to[0];
      $c_lightbox = ' rel="magnific"';
  } else if (!empty($img_link)) {
      $link_to = $img_link;
  }
  $css_class = ' class="align'.$alignment.'"';
  
  if(!empty($link_to) && !preg_match('/^(https?\:\/\/|\/\/)/', $link_to)) $link_to = 'http://'.$link_to;
  $out = !empty($link_to) ? '<a'.$css_class.' '.$c_lightbox.' href="'.$link_to.'"'. ($target_blank ? ' target="_blank"' : '') .'>'.$img.'</a>' : $img;

  return $out;
}
add_shortcode('thb_image', 'thb_image');
