<?php
/* Small Title Shortcodes */
function small_title($atts, $content = null ) {
    extract(shortcode_atts(array(
    	'title'      => 'Title'
    ), $atts));

	$out = '<div class="title">' .$title. '</div>';
	
  return $out;
}
add_shortcode('small_title', 'small_title');

/* Large Title Shortcodes */
function large_title($atts, $content = null ) {
    extract(shortcode_atts(array(
    	'title'      => 'Title',
    	'center'		 => ''
    ), $atts));

	
	if ($center) {
		$center = 'center';
	}
	
	$out = '<div class="large_title '.$center.'">' .$title. '</div>';
	
  return $out;
}
add_shortcode('large_title', 'large_title');

/* Inline Label Shortcodes */
function tags($atts, $content = null ) {
    extract(shortcode_atts(array(
    	'color'      => 'black'
    ), $atts));

	$out = '<span class="label '.$color.'">' .$content. '</span>';
	
    return $out;
}
add_shortcode('tags', 'tags');

/* Blockquote */
function blockquotes( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'pull'      => '',
       	'author'    => ''
    ), $atts));
	$content = remove_invalid_tags($content, array('p'));
	$content = remove_invalid_tags($content, array('br'));
	$authorhtml = '';
	if ($author) {
		$authorhtml = '<cite>'. $author. '</cite>';
	}
	$out = '<blockquote class="styled '.$pull.'"><p>' .$content. $authorhtml. '</p></blockquote>';
    return $out;
}
add_shortcode('blockquote', 'blockquotes');

/* Icons */
function icons( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'type'      => '',
       	'url'				=> '',
       	'box'				=> '',
       	'size'			=> 'icon-1x'
    ), $atts));
 
		$out = '<i class="fa '.$type.'"></i>';
  
  	if ($box) {
  		$class = '';
  		if ($type == 'fa-facebook' || $type == 'fa-twitter' || $type == 'fa-google-plus' || $type == 'fa-pinterest' || $type == 'fa-linkedin') {
  			$class = substr($type, 3);
  		}
  		$out = '<a href="'.$url.'" class="boxed-icon '.$class.' '. $size.'">'.$out.'</a>';
  	}	else {
  		$out = '<figure class="boxed-icon '. $size.' no-link"><i class="fa '.$type.' '. $size.'"></i></figure>';
  	}
  	
  	return $out;
}
add_shortcode('icon', 'icons');

/* Dropcap */
function dropcap( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'boxed'      => 'false'
    ), $atts));
 		
 		if ($boxed == "true") {
 			$type = 'boxed';
 		} else {
 			$type = '';
 		}
		$out = '<span class="dropcap '.$type.'">'.$content.'</span>';
  	
  	return $out;
}
add_shortcode('dropcap', 'dropcap');


/* Icon Styled Lists */
function icon_list($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'icon'      => 'ok'
	), $atts));
	$content = remove_invalid_tags($content, array('p'));
	$content = remove_invalid_tags($content, array('br'));
	$output = '';
	if (!preg_match_all("/(.?)\[(item)\b(.*?)(?:(\/))?\](?:(.+?)\[\/item\])?(.?)/s", $content, $matches)) {
		return do_shortcode($content);
	} else {
		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
		}
		
		$output .='<ul class="iconlist">';
		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= '<li><i class="icon-'.$icon.'"></i>' . do_shortcode(trim($matches[5][$i])) .'</li>';
		}
		$output .='</ul>';
		return $output;
	}
}
add_shortcode('icon-list', 'icon_list');

?>