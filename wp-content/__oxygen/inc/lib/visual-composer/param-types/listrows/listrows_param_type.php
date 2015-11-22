<?php	
/**
 * Silicon Theme
 *
 * Theme by: Laborator.co
 *
 * Designed by: Art Ramadani (www.artramadani.com)
 * Developed by: Arlind Nushi (www.arlindnushi.com)
 *
 * www.laborator.co
 *
 * File Date: 1/21/13
 *
 */

$url_to_script = THEMEURL . "inc/lib/visual-composer/param-types/listrows/listrows_param_type.js";

add_shortcode_param('listrows', 'list_rows_wpb_param_type', $url_to_script);

function list_rows_wpb_param_type($settings, $value)
{
	$dependency = vc_generate_dependencies_attributes($settings);
	
	$list_row_entry = '
	<div class="list_rows_wpb_param_type_entry">
		<table>
			<tr>
				<td class="lbl">' . 'Feature name' . '</td>
			</tr>
			<tr>
				<td>
					<input type="text" class="lr_field field_lbl" />
				</td>
			</tr>
		</table>
	</div>';
	
	return	'<div class="list_rows_wpb_param_type_field">' . 
				'<input name="'.$settings['param_name'] .'" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$value.'" ' . $dependency . '/> ' .
			'</div>
			<span></span>' .
			'<div class="lr_entries">' . $list_row_entry . '</div>' . 
			'<a href="#" class="add_lr_entry">Add Row</a>';
}