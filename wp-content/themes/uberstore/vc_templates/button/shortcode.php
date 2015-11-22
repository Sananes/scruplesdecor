<?php function thb_button( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'color'      => '',
       	'target_blank' => false,
       	'link'       => '#',
       	'size'			 => 'small',
       	'icon'			 => false,
       	'animation'	 => false
    ), $atts));
	
	if($icon) { $content = $content. ' <i class="fa '.$icon.'"></i>'; }
	
	$out = '<a class="btn '.$color.' '.$size.' '.$animation.'" href="'.$link.'" ' . ($target_blank ? ' target="_blank"' : '') .'>' .$content. '</a>';
  
  return $out;
}
add_shortcode('thb_button', 'thb_button');
