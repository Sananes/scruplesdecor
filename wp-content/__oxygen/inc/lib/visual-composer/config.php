<?php	
/**
 * Laborator Visual Composer Settings
 *
 * Developed by: Arlind Nushi (www.arlindnushi.com)
 *
 * www.laborator.co
 *
 * File Date: 22/04/2014
 *
 */

global $composer_settings;


/* ! Layout Elements */

$curr_dir = dirname(__FILE__);

/* Register Own Param Types */
#get_template_part('inc/lib/visual-composer/param-types/skillsbars/skillsbars_param_type');
#get_template_part('inc/lib/visual-composer/param-types/listrows/listrows_param_type');
#get_template_part('inc/lib/visual-composer/param-types/metroelementoptions/metroelementoptions_param_type');
include_once($curr_dir . '/param-types/fontelloicon/fontelloicon_param_type.php');


/* Shortcodes */
add_action('init', 'laborator_vc_shortcodes');

function laborator_vc_shortcodes()
{
	global $curr_dir;
	
	include_once($curr_dir . '/laborator-shortcodes/laborator_banner.php');
	include_once($curr_dir . '/laborator-shortcodes/laborator_banner2.php');
	include_once($curr_dir . '/laborator-shortcodes/laborator_featuretab.php');
	include_once($curr_dir . '/laborator-shortcodes/laborator_blog.php');
	include_once($curr_dir . '/laborator-shortcodes/laborator_testimonials.php');
	
	if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option( 'active_plugins'))))
	{
		include_once($curr_dir . '/laborator-shortcodes/laborator_products.php');
		include_once($curr_dir . '/laborator-shortcodes/laborator_products_carousel.php');
		include_once($curr_dir . '/laborator-shortcodes/laborator_lookbook.php');
	}
}

/* Admin Styles */
add_action('admin_enqueue_scripts', 'laborator_vc_styles');

function laborator_vc_styles()
{
	
	$laborator_vc_style = THEMEURL . 'inc/lib/visual-composer/assets/laborator_vc_main.css';
	
	wp_enqueue_style('laborator_vc_main', $laborator_vc_style);
}


// Custom Params
add_action( 'vc_before_mapping', 'lab_vc_before_mapping' );

function lab_vc_before_mapping()
{	
	// VC Row
	vc_add_params( 'vc_row', array(
		array(
			"type" => 'checkbox',
			"heading" => 'Block with Background',
			"param_name" => "block_with_background",
			"description" => "Make this block with background.",
			"value" => array('Yes' => 'yes'),
			"weight" => 1
		),
		
		array(
			"type" => 'checkbox',
			"heading" => 'Add Default Margin',
			"param_name" => "add_default_margin",
			"description" => "Add the default margin for the elements container.",
			"value" => array('Yes' => 'yes'),
			"weight" => 1
		)
	) );
	
	
	
	// VC Column
	vc_add_param( 'vc_column_text', array(
		"type" => 'checkbox',
		"heading" => 'Block with Background',
		"param_name" => "block_with_background",
		"description" => "Make this block with background.",
		"value" => array('Yes' => 'yes')
	) );
	
	
	// VC Message
	vc_remove_param( 'vc_message', 'style' );
	
	
	// VC Button
	vc_remove_param( 'vc_button', 'icon' );
	
	vc_add_param( 'vc_btn', array(
		"type" => 'checkbox',
		"heading" => 'Bordered',
		"param_name" => "bordered",
		"description" => "Remove the background and show only border.",
		"value" => array('Yes' => 'yes')
	) );
	
	
	// VC Text Separator
	vc_add_params( 'vc_text_separator', array(
		array(
			"type" => "dropdown",
			"heading" => __("Separator Style", TD),
			"param_name" => "separator_style",
			"value" => array(
				"Double Bordered Thick"	=> 'double-bordered-thick',
				"Double Bordered Thin"	 => 'double-bordered-thin',
				"Double Bordered"		  => 'double-bordered',
				"One Line Border"		  => 'one-line-border',
			),
			"description" => __("Select separator style", TD),
			"weight" => 1
		),
		array(
			"type" => "textfield",
			"heading" => __("Sub Title", TD),
			"param_name" => "subtitle",
			"description" => __("You can apply subtitle but its optional.", TD),
			"value" => "",
		)
	) );
}




# Filter to Replace default css class for vc_row shortcode and vc_column
add_filter('vc_shortcodes_css_class', 'laborator_css_classes_for_vc', 10, 4);

function laborator_css_classes_for_vc($class_string, $tag, $atts_values = array() )
{
	if($tag == 'vc_row' || $tag == 'vc_row_inner')
	{
		$class_string = str_replace(array('wpb_row vc_row-fluid'), array('row'), $class_string);

		# No Margin Row
		if(isset($atts_values['add_default_margin']) && $atts_values['add_default_margin'] == 'yes')
		{
			$class_string .= ' with-margin';
		}

		# Block background
		if(isset($atts_values['block_with_background']) && $atts_values['block_with_background'] == 'yes')
		{
			$class_string .= ' block-bg';
		}
	}
	elseif($tag == 'vc_column' || $tag == 'vc_column_inner')
	{
		if(preg_match("/vc_span(\d+)/", $class_string, $matches))
		{
			$span_columns = $matches[1];

			$col_type = $tag == 'vc_column' ? 'sm' : 'md';

			$class_string = str_replace($matches[0], "col-{$col_type}-{$span_columns}", $class_string);
		}
	}
	elseif($tag == 'vc_column_text')
	{
		# Block background
		if(isset($atts_values['block_with_background']) && $atts_values['block_with_background'] == 'yes')
		{
			$class_string .= ' block-bg';
		}
	}
	elseif($tag == 'vc_button')
	{
		$class_string = str_replace(array('wpb_button', 'wpb_button', 'wpb_btn'), array('btn', '', 'btn'), $class_string);

		# Bordered Button
		if(isset($atts_values['bordered']) && $atts_values['bordered'] == 'yes')
		{
			$class_string .= ' btn-bordered';
		}
	}
	elseif($tag == 'vc_widget_sidebar')
	{
		$class_string .= ' shop_sidebar';
	}
	elseif($tag == 'vc_text_separator')
	{
		$subtitle = isset($atts_values['subtitle']) ? $atts_values['subtitle'] : '';
		$accent_color = isset($atts_values['accent_color']) && $atts_values['accent_color'] ? $atts_values['accent_color'] : '';

		if(isset($atts_values['separator_style']))
			$class_string .= ' ' . $atts_values['separator_style'] . ($accent_color ? (" custom-color-" . str_replace('#', '', $accent_color)) : '');

		if($subtitle)
		{
			#$class_string .= '" data-subtitle="' . esc_attr($subtitle);
			$class_string .= ' __' . str_replace(' ', '-', $subtitle) . '__';
		}
	}

	return $class_string;
}