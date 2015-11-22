<?php
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = $css = '';
extract(shortcode_atts(array(
		'el_class'				=> '',
    'bg_image'        => '',
    'bg_color'        => '',
    'bg_image_repeat' => '',
    'font_color'      => '',
    'padding'         => '',
    'margin_bottom'   => '',
    'full_width'			=> '',
    'enable_parallax'	=> '',
    'parallax_speed'	=> '',
    'bg_video_src_mp4' => '',
    'bg_video_src_ogv' => '',
    'bg_video_src_webm' => '',
    'bg_video_overlay_color' => '',
    'css' => ''
), $atts));


wp_enqueue_script( 'wpb_composer_front_js' );
wp_enqueue_style('js_composer_custom_css');
$el_class = $this->getExtraClass($el_class);

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'row '.get_row_css_class(), $this->settings['base']);
$style = $this->buildStyle($bg_image, $bg_color, $bg_image_repeat, $font_color, $padding, $margin_bottom);

$data = '';
$parallax = '';
$video = '';

// video bg
$bg_video = '';
$bg_video_args = array();

if ( $bg_video_src_mp4 ) {
	$bg_video_args['mp4'] = $bg_video_src_mp4;
}

if ( $bg_video_src_ogv ) {
	$bg_video_args['ogv'] = $bg_video_src_ogv;
}

if ( $bg_video_src_webm ) {
	$bg_video_args['webm'] = $bg_video_src_webm;
}


if ( !empty($bg_video_args) ) {
	$attr_strings = array(
		'loop="1"',
		'preload="1"'
	);

	if ( $bg_image && !in_array( $bg_image, array('none') ) ) {

		$attr_strings[] = 'poster="' . esc_url($bg_image) . '"';
	}
	
	
	$bg_video .= sprintf( '<video %s controls="controls" class="row-video-bg" autoplay>', join( ' ', $attr_strings ) );

	$source = '<source type="%s" src="%s" />';
	foreach ( $bg_video_args as $video_type=>$video_src ) {

		$video_type = wp_check_filetype( $video_src, wp_get_mime_types() );
		$bg_video .= sprintf( $source, $video_type['type'], esc_url( $video_src ) );

	}

	$bg_video .= '</video>';
	
	if ( $bg_video_overlay_color != '' ) {
		$bg_video .= '<div class="video_overlay" style="background-color: '.$bg_video_overlay_color .';"></div>';
	}
	
	$video = ' video_bg';
}

// Parallax
if ( $enable_parallax ) {
	if ( $parallax_speed == '' ) {
		$parallax_speed = 1;
	}

	$parallax = ' parallax_bg';
	$data = ' data-parallax-speed="' . $parallax_speed . '"';
}

if ( $full_width ) {
	$output .= '<div class="full-width-section'.$parallax.''.$video.''.$el_class.vc_shortcode_custom_css_class($css, ' ').'" '.$style.''.$data.'>';
	$style = '';
}

$output .= $bg_video;
$output .= '<div class="'.$css_class.' '. ($full_width ? '' : $parallax.$el_class.vc_shortcode_custom_css_class($css, ' ')) .'" '. ($full_width ? '' : $data) .' '.$style.'>';
$output .= wpb_js_remove_wpautop($content);
$output .= '</div>'.$this->endBlockComment('row');

if ( $full_width ) {
	$output .= '</div>';
}
echo $output;