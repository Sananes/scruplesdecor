<?php	
/**
 * Laborator - Fontello Icon Selector
 *
 * Param Type by Arlind Nushi
 *
 * www.laborator.co
 *
 * File Date: 09/16/13
 */

add_shortcode_param('fontelloicon', 'fontello_icon_wpb_param_type');

add_action('admin_enqueue_scripts', 'lab_vc_fontelloicon');

function lab_vc_fontelloicon()
{
	wp_enqueue_style('entypo');
}

function fontello_icon_wpb_param_type($settings, $value)
{
	$dependency = vc_generate_dependencies_attributes($settings);
	
	$fontello_icon_list = fontello_icon_list();
	
	$icons_list = '';
	
	foreach($fontello_icon_list as $icon)
	{
		$ico = '<span data-icon="'.$icon.'" title="'.$icon.'" class="icon-entry entypo-'.$icon.'"></span>';
		
		$icons_list .= $ico;
	}
	
	$fontello_dir = THEMEURL . 'css/fontello/';
	
	$url_to_script = THEMEURL . "inc/lib/visual-composer/param-types/fontelloicon/fontelloicon_param_type.js";
	
	$fontello_css = <<<EOD
<script type="text/javascript" src="{$url_to_script}"></script>
EOD;
	
	return	$fontello_css . 
			
			'<div class="fontello_icon_wpb_param_type_field">' . 
				'<input name="'.$settings['param_name'] .'" class="wpb_vc_param_value laborator_fontello_select wpb-textinput ' . $settings['param_name'].' '.$settings['type'].'_field" type="text" value="'.$value.'" ' . $dependency . '/> ' .
				'<label class="font-icon-heart"></label>' .
			'</div>' .
			
			'<div id="fontello_font_list" class="fontello-icon-list">'.
				$icons_list.
				'<div class="clear"></div>' . 
			'</div>' . 
			
			'<span></span>';
}