<?php function thb_banner( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'type'     				=> 'type1',
       	'banner_color'		=> 'transparent',
       	'banner_border_color'		=> '',
       	'banner_bg' 			=> false,
       	'banner_height' 	=> '300',
       	'banner_padding' 	=> '30',
       	'enable_parallax' => false,
       	'parallax_speed'	=> '',
       	'alignment'				=> '',
       	'title'						=> '',
       	'subtitle'				=> '',
       	'button_text'			=> '',
       	'button_link'			=> '',
       	'overlay_link'		=> ''
    ), $atts));
	
	$out = $parallax = $data = '';
	if ( $enable_parallax ) {
		if ( $parallax_speed == '' ) {
			$parallax_speed = 1;
		}
	
		$parallax = 'parallax_bg';
		$data = ' data-parallax-speed="' . $parallax_speed . '"';
	}
	
	$img_id = preg_replace('/[^\d]/', '', $banner_bg);
	$img = wp_get_attachment_image_src($img_id, 'full');
	$content = remove_invalid_tags($content, array('p'));
	
  $out .= '<aside class="banner '.$alignment.' '.$type.' '.$parallax.'" style="border-color:'.$banner_border_color.'; min-height:'.$banner_height.'px; background: '.$banner_color.' url('.$img[0].'); padding:'.$banner_padding.'px;" '.$data.'><div class="divcontent">'.($type != 'type3' ? do_shortcode($content) : '').'</div>';
  	$out .= '<div class="divstyle" style="border-color:'.$banner_border_color.';">';
  	
  if( $type == 'type3') {
  	$out .= '<a href="'.$button_link.'" class="btn large white" title="'.$button_text.'">'.$button_text.'</a>';
  	$out .= '<h3>'.$title.'</h3>';
  	$out .= '<p>'.$subtitle.'</p>';
  }

  	$out .= '</div>';
  	if( $type == 'type5') {
  		$out .= '<a href="'.$overlay_link.'" class="overlay_link"></a>';
  	}
  $out .= '</aside>';
  return $out;
}
add_shortcode('thb_banner', 'thb_banner');
