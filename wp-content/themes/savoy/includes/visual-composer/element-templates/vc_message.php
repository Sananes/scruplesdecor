<?php

	extract( shortcode_atts( array(
		'color' 	=> '',
		'el_class'	=> ''
	), $atts ) );
	
	$elementClass = 'nm-message-box ' . $color . $this->getExtraClass( $el_class );
	
	$iconClass = 'nm-font nm-font-textsms flip';
	
	switch ( $color ) {
		case 'warning':
			$iconClass = 'nm-font nm-font-textsms flip';
			break;
		case 'success':
			$iconClass = 'nm-font nm-font-thumb-up';
			break;
		case 'danger':
			$iconClass = 'nm-font nm-font-thumb-down';
			break;
		default:
			break;
	}
	
	$output = '
		<div class="' . $elementClass . '">
			<div class="nm-message-box-icon"><i class="' . $iconClass . '"></i></div>
			<div class="nm-message-box-text">' . $content . '</div>
		</div>';
		
	echo $output;
