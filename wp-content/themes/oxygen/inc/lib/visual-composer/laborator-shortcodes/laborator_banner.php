<?php
/**
 *	Text Banner Shortcode for Visual Composer
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


class WPBakeryShortCode_laborator_banner extends  WPBakeryShortCode
{
	public function content($atts, $content = null)
	{
		if( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		}
		
		extract(shortcode_atts(array(
			'title' => '',
			'description' => '',
			'href' => '',
			'color' => '',
			'type' => '',
			'el_class' => '',
			'css' => '',
		), $atts));
		
		$link     = vc_build_link($href);
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = trim($link['target']);
		
		switch($color)
		{
			case 'black':
				$el_class .= ' banner-black';
				break;
				
			case 'red':
				$el_class .= ' banner-default';
				break;
			
			case 'dark-red':
				$el_class .= ' banner-dark-red';
				break;
				
			default:
				$el_class .= ' banner-white';
		}
		
		if($type == 'button-left-text-right')
			$el_class .= ' button-right';
		else
		if($type == 'text-button-center')
			$el_class .= ' text-button-center';
		
		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_banner wpb_content_element banner '.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);
		
		ob_start();
		
		?>
		<div class="<?php echo $css_class; ?>">
			<div class="button_outer">
				<div class="button_middle">
					<div class="button_inner">
						
						<?php if($type == 'button-left-text-right'): ?>
							<?php if($a_title): ?>
							<div class="banner-call-button">
								<a href="<?php echo $a_href; ?>" class="btn" target="<?php echo $a_target; ?>"><?php echo $a_title; ?></a>
							</div>
							<?php endif; ?>
						<?php endif; ?>
						
						<div class="banner-content">
							<strong><?php echo $title; ?></strong>
							
							<?php if($description): ?>
							<span><?php echo $description; ?></span>
							<?php endif; ?>
						</div>
						
						<?php if( ! in_array($type, array('button-left-text-right'))): ?>
							<?php if($a_title): ?>
							<div class="banner-call-button">
								<a href="<?php echo $a_href; ?>" class="btn" target="<?php echo $a_target; ?>"><?php echo $a_title; ?></a>
							</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
}

// Shortcode Options
$opts = array(
	"name"		=> __("Text Banner", TD),
	"description" => __('Include a Call to Action banner.', TD),
	"base"		=> "laborator_banner",
	"class"		=> "vc_laborator_banner",
	"icon"		=> "icon-lab-banner",
	"controls"	=> "full",
	"category"  => __('Laborator', TD),
	"params"	=> array(
		
		array(
			"type" => "textfield",
			"heading" => __("Widget title", TD),
			"param_name" => "title",
			"value" => "",
			"description" => __("What text use as widget title. Leave blank if no title is needed.", TD)
		),
		
		array(
			"type" => "textfield",
			'admin_label' => true,
			"heading" => __("Text", TD),
			"param_name" => "description",
			"value" => __("Free shipping over $125 for international orders", TD),
			"description" => __("Banner content.", TD)
		),
		
		array(
			"type" => "vc_link",
			"heading" => __("URL (Link)", TD),
			"param_name" => "href",
			"description" => __("Button link.", TD)
		),
		
		array(
			"type" => "dropdown",
			"heading" => __("Banner Color", TD),
			"param_name" => "color",
			"value" => array(
				"White"     => 'white',
				"Black"     => 'black',
				"Red"       => 'red',
				"Dark Red"  => 'dark-red',
			),
			"description" => __("Select the type of banner.", TD)
		),
		
		array(
			"type" => "dropdown",
			"heading" => __("Banner Type", TD),
			"param_name" => "type",
			"value" => array(
				"Text (left) + Button (right)" => 'text-left-button-right',
				"Button (left) + Text (right)" => 'button-left-text-right',
				"Text + Button (Center)" => 'text-button-center',
			),
			"description" => __("Select the type of banner.", TD)
		),
		
		array(
			"type" => "textfield",
			"heading" => __("Extra class name", TD),
			"param_name" => "el_class",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", TD)
		),
		
		array(
			"type" => "css_editor",
			"heading" => __('Css', TD),
			"param_name" => "css",
			"group" => __('Design options', TD)
		)
	)
);

// Add & init the shortcode
wpb_map($opts);
#new Laborator_VC_Banner($opts);