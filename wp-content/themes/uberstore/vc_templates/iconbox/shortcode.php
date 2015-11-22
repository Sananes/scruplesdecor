<?php function thb_iconbox( $atts, $content = null ) {
    extract(shortcode_atts(array(
    		'type'			 => 'top type1',
       	'image'      => '',
       	'color'      => '',
       	'icon'			 => 'fa-camera',
       	'heading'		 => '',
       	'use_btn'				 => false,
       	'btn_color'      => '',
       	'btn_target_blank' => false,
       	'btn_link'       => '#',
       	'btn_size'			 => 'small',
       	'btn_icon'			 => false,
       	'btn_content'		 => false,
       	'animation'	 => false
    ), $atts));
	$btn = '';
	
	// Image & Icon
	if ($image) {
		$img_id = preg_replace('/[^\d]/', '', $image);
		$img = wp_get_attachment_image($img_id, 'full', false, array(
			'alt'   => trim(strip_tags( get_post_meta($img_id, '_wp_attachment_image_alt', true) )),
		));
  } else {
  	$icon = '<i class="fa '.$icon.'"></i>';
  }
  
  // Button
  if ($use_btn) {
	  if($btn_icon) { $btn_content = $btn_content. ' <i class="fa '.$btn_icon.'"></i>'; }
	  
	  $btn = '<a class="btn '.$btn_color.' '.$btn_size.'" href="'.$btn_link.'" ' . ($btn_target_blank ? ' target="_blank"' : '') .'>' .$btn_content. '</a>';
	}

	// Content
	
	$out = '<div class="iconbox '.$type.' '.$animation.'">';
	switch($type) {
		case 'top type1':
		case 'left type1':
		case 'left type2':
		case 'right type1':
		case 'right type2':
			$out .= '<span' . ($color ? ' style="background-color:'.$color.'"' : '') . ($image ? ' class="img"' : '') .' >' . ($image ? $img : $icon) .'</span>';
			break;
		case 'top type2':
		case 'left type3':
		case 'right type3':
			$out .= '<span' . ($color ? ' style="border-color:'.$color.'; color:'.$color.'"' : '') . ($image ? ' class="img"' : '') .' >' . ($image ? $img : $icon) .'</span>';
			break;
		case 'top type3':
			$out .= '<span' . ($color ? ' style="background-color:'.$color.';"' : '') . ($image ? ' class="img"' : '') .' >' . ($image ? $img : $icon) .'</span>';
			break;
	}
		
		
	$out .= '<div class="content">
			<div class="title">'.$heading.'</div>
			<div>'.$content.'</div>
			'.$btn.'
		</div>
	</div>';
  return $out;
}
add_shortcode('thb_iconbox', 'thb_iconbox');
