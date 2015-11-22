<?php function thb_teammember( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'image' 			=> '',
       	'name'			=> '',
       	'position'	=> '',
       	'facebook'	=> '',
       	'twitter'	=> '',
       	'googleplus'	=> '',
       	'linkedin'	=> ''
    ), $atts));
	
	$out = '';
	
	$img_id = preg_replace('/[^\d]/', '', $image);
	$img = wp_get_attachment_image_src($img_id, 'full');
	$resized = aq_resize( $img[0], 270, 270, true, false);
  $out .= '<aside class="team_member">';
  $out .= '<img src="'.$resized[0].'" width="'.$resized[1].'" height="'.$resized[2].'" alt="'.$name.'" />';
  $out .= ($name ? '<strong>'.$name.'</strong>' : '');
  $out .= ($position ? '<small>'.$position.'</small>' : '');
  
  if ($facebook || $googleplus || $twitter || $linkedin) {
  	$out .= '<div class="social_links">';
  		if ($facebook) {
  			$out .= '<a href="'.$facebook.'" class="facebook icon-2x"><i class="fa fa-facebook"></i></a>';
  		}
  		if ($twitter) {
  			$out .= '<a href="'.$twitter.'" class="twitter icon-2x"><i class="fa fa-twitter"></i></a>';
  		}
  		if ($googleplus) {
  			$out .= '<a href="'.$googleplus.'" class="google-plus icon-2x"><i class="fa fa-google-plus"></i></a>';
  		}
  		if ($linkedin) {
  			$out .= '<a href="'.$linkedin.'" class="linkedin icon-2x"><i class="fa fa-linkedin"></i></a>';
  		}
  	$out .= '</div>';
  }
  $out .= '</aside>';
  
  
  return $out;
}
add_shortcode('thb_teammember', 'thb_teammember');
