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

$url_to_script = THEMEURL . "inc/lib/visual-composer/param-types/skillsbars/skillsbars_param_type.js";

add_shortcode_param('skillsbars', 'progress_bars_skills', $url_to_script);

function progress_bars_skills($settings, $value)
{
	$dependency = vc_generate_dependencies_attributes($settings);
	
	$progress_entry = '
	<div class="progress_bars_skills_entry">
		<table>
			<tr>
				<td class="lbl">' . 'Label' . '</td>
				<td class="pct">' . 'Percentage' . '</td>
			</tr>
			<tr>
				<td>
					<input type="text" class="pbs_field field_lbl" />
				</td>
				<td>
					<input type="number" class="pbs_field field_pct" step="1" min="1" max="100" value="0" />
				</td>
			</tr>
		</table>
	</div>';
	
	return	'<div class="progress_bars_skills_field">' . 
				'<input name="'.$settings['param_name'] .'" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$value.'" ' . $dependency . '/> ' .
			'</div>
			<span></span>' .
			'<div class="pbs_entries">' . $progress_entry . '</div>' . 
			'<a href="#" class="add_pbs_entry">Add Entry</a>';
}