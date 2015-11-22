<?php
/**
 *	Metro Element Type
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $wp_registered_sidebars, $wp_registered_widgets;

$url_to_script = THEMEURL . "inc/lib/visual-composer/param-types/metroelementoptions/metroelementoptions_param_type.js";

add_shortcode_param('metroelementoptions', 'metroelementoptions_param_type', $url_to_script);

function metroelementoptions_param_type($settings, $value)
{
	global $wp_registered_sidebars, $wp_registered_widgets;
	
	# WPB Var
	$dependency = vc_generate_dependencies_attributes($settings);
	
	$sidebars_widgets = wp_get_sidebars_widgets();
	$metroelements_widgets = $sidebars_widgets['metroelements_widgets'];
	
	# Setup Widget as Select Box Options
	$valid_widgets 	= array(
							'WP_Widget_Text', 
							'LaboratorME_InstaSlideshow', 
							'LaboratorME_LatestPosts', 
							'LaboratorME_PortfolioItems', 
							'LaboratorME_PostsSlider', 
							'LaboratorME_ShopProducts', 
							'LaboratorME_Video', 
							'LaboratorME_Weather',
							'SimpleAdsWidget',
							'Laborator_Twitter_V2',
							'LaboratorSubscribe',
							'LaboratorBlogStats'
							);
							
	$widgets_list	= array();
	$widget_titles	= array();
	
	foreach($metroelements_widgets as $widget_id)
	{
		$widget			= $wp_registered_widgets[$widget_id];
		$widget_cb		= reset($widget['callback']);
		$classname		= $widget['classname'];
		$number			= $widget['params'][0]['number'];
		
		
		$widget_class 	= get_class($widget_cb);
		
		if( ! $valid_widgets || in_array($widget_class, $valid_widgets))
		{
			# Widget Title
			$widget_arr		= get_option($classname);
			$wiget_info		= $widget_arr[$number];
			$widget_titles[$widget_id] = isset($wiget_info['title']) ? $wiget_info['title'] : '';
			
			# Widget Instance
			$widgets_list[] = $widget;
		}
	}
	
	$widgets_list_option = '<option value="">Select Widget</option>';
	
	if( ! count($widgets_list))
	{
		$widgets_list_option = '<optgroup label="No supported widgets"></optgroup>';
	}
	
	foreach($widgets_list as $i => $widget)
	{
		$id = $i + 1;
		
		$title = $widget_titles[$widget['id']];
		$id_or_title = $title ? $title : "Order no. {$id}";
		
		$widget['name'] = trim(str_replace(array('[Laborator]', '[Laborator-ME]'), '', $widget['name']));
		
		$widgets_list_option .= <<<EOD
<option value="{$widget['id']}">{$id_or_title} ({$widget['name']})</option>
EOD;
	}
	
	# Box Type Select (1x1)
	$box_type_1x1 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_1x1">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	# Box Type Select (1x2)
	$box_type_1x2 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_1x2">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 2</div>
	<select name="w2" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	# Box Type Select (1x3)
	$box_type_1x3 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_1x3">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 2</div>
	<select name="w2" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 3</div>
	<select name="w3" class="me_field">
		{$widgets_list_option}
	</select>
	
	
	<div class="me_box_title">Reverse Order</div>
	<label>
		<input type="checkbox" name="reverse" value="1" class="me_field" /> Flip the position vertically
	</label>
</div>
EOD;
	
	# Box Type Select (1x4)
	$box_type_1x4 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_1x4">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 2</div>
	<select name="w2" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 3</div>
	<select name="w3" class="me_field">
		{$widgets_list_option}
	</select>
	
	<div class="me_box_title">Widget 4</div>
	<select name="w4" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	# Box Type Select (2x1)
	$box_type_2x1 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_2x1">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	# Box Type Select (3x1)
	$box_type_3x1 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_3x1">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	# Box Type Select (1_2)
	$box_type_1_2 = <<<EOD
<div class="me_box_widgets" id="me_box_widgets_1_2">
	<div class="me_box_title">Widget 1</div>
	<select name="w1" class="me_field">
		{$widgets_list_option}
	</select>
</div>
EOD;
	
	return	'<div class="metro_element_field">' . 
				'<input name="'.$settings['param_name'] .'" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$value.'" ' . $dependency . '/> ' .
			'</div>
			<span></span>' .
			'<div id="metroelement_box_type_options">' . 
				'<span class="loading_options">Loading Options...</span>' . 
				$box_type_1x1 .
				$box_type_1x2 .
				$box_type_1x3 .
				$box_type_1x4 .
				$box_type_2x1 .
				$box_type_3x1 .
				$box_type_1_2 .
				'<br /><span class="description clear">Select Widget (or Widgets) that will appear in this Metro Element Box. <br />All active widgets within <strong>Metro Elements (Visual Composer)</strong> sidebar will be available to select.</span>' . 
			'</div>' . 
			'<div id="metroelement_box_model">' . 
				'<div class="wpb_element_label">Box Model</div>' .
				'<img src="' . THEMEURL . 'images/admin/zinc-me-layoutmodel-box.png" />' .
			'</div>' . 
			'<div class="clear"></div>';
}