<?php

	extract( shortcode_atts( array(
		'title' 		=> '',
		'title_align' 	=> 'separator_align_center',
		'accent_color'	=> '',
		'border_width'	=> '',
		'el_class' 		=> '',
		// Custom params
		'title_size' 	=> 'medium'
	), $atts ) );
	
	$custom_class = ( strlen( $el_class ) > 0 ) ? $this->getExtraClass( $el_class ) : '';
	$class = $title_align . $custom_class;
	
	$title = ( strlen( $title ) > 0 ) ? '<h1 class="' . $title_size . '">' . $title . '</h1>' : '';
	
	$divider_style = ( strlen( $border_width ) > 0 ) ? 'height:' . $border_width . 'px; ' : '';
	$divider_style .= ( strlen( $accent_color ) > 0 ) ? 'background:' . $accent_color . ';' : '';
			
	$output = '
		<div class="nm-divider ' . $class . '">' .
			$title . '
			<div class="nm-divider-line" style="' . $divider_style . '"></div>
		</div>';
					
	echo $output;
