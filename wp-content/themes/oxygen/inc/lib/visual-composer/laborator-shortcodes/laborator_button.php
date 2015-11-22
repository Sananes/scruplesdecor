<?php
/**
 *	Text Banner Shortcode for Visual Composer
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

class WPBakeryShortCode_Laborator_Button extends WPBakeryShortCode {
	
	public function content($atts, $content = null)
	{
		
		global $lab_button_ids;

		# Atts
		$defaults = array(
			'title'                    => '',
			'link'                     => '',
			'type'                     => '',
			'size'                     => '',
			'button_bg'                => '',
			'button_bg_custom'         => '',
			'button_txt_custom'        => '',
			'button_bg_hover_custom'   => '',
			'button_txt_hover_custom'  => '',
			'el_class'                 => '',
			'css'                      => ''
		);
		
		#$atts = vc_shortcode_attribute_parse($defaults, $atts);
		if( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		}
		
		extract( $atts );
		
		
				
		if( ! isset($lab_button_ids) || ! $lab_button_ids)
			$lab_button_ids = 0;
		
		$lab_button_ids++;
		$btn_index = "btn-index-{$lab_button_ids}";
		
		# Link 
		$link = vc_build_link($link);
		
		# Element Class
		$class = $this->getExtraClass( $el_class );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );
		
		$css_class = "laborator-btn btn {$btn_index} btn-type-{$type} {$css_class}";
		
		if($type == 'outlined-bg' || $type == 'fliphover')
		{
			$css_class .= ' btn-type-outlined';
		}
		
		if($button_bg != 'custom')
		{
			$css_class .= " {$button_bg}";
		}
		
		$css_class .= " {$size}";
		
		# Custom Button Color
		$button_css_normal = $button_css_hover = '';
		
		if($button_bg == 'custom')
		{
			switch($type)
			{	
				case "outlined":
				
					if($button_bg_custom)
					{
						$button_css_normal .= 'border-color:' . $button_bg_custom . ';';
						
						if( ! $button_txt_custom)
						{
							$button_css_normal .= 'color:' . $button_bg_custom . ';';
						}
					}
				
					if($button_txt_custom)
					{
						$button_css_normal .= 'color:' . $button_txt_custom . ';';
					}
					
					if($button_bg_hover_custom)
					{
						$button_css_hover .= 'border-color:' . $button_bg_hover_custom . ';';
					}
					
					if($button_txt_hover_custom)
					{
						$button_css_hover .= 'color:' . $button_txt_hover_custom . ';';
					}
					break;
				
				default:
				
					if($button_bg_custom)
					{
						$button_css_normal .= 'background-color:' . $button_bg_custom . ';';
					}
					
					if($button_txt_custom)
					{
						$button_css_normal .= 'color:' . $button_txt_custom . ';';
					}
					
					if($button_bg_hover_custom)
					{
						$button_css_hover .= 'background-color:' . $button_bg_hover_custom . ';';
					}
					
					if($button_txt_hover_custom)
					{
						$button_css_hover .= 'color:' . $button_txt_hover_custom . ';';
					}
			}
		}
		
		ob_start();
		?>
		<a id="<?php echo $btn_index; ?>" href="<?php echo esc_url($link['url']); ?>" title="<?php echo esc_attr($link['title']); ?>" target="<?php echo esc_attr(trim($link['target'])); ?>" class="<?php echo esc_attr($css_class) . vc_shortcode_custom_css_class($css, ' '); ?>"><?php echo esc_html($title); ?></a>
		<?php
		
		if($button_css_normal || $button_css_hover):
		
			?>
			<style>
			<?php if($button_css_normal): ?>
			#<?php echo $btn_index; ?>.btn { <?php echo $button_css_normal; ?> }
			<?php endif; ?>
			
			<?php if($button_css_hover): ?>
			#<?php echo $btn_index; ?>.btn:hover { <?php echo $button_css_hover; ?> }
			<?php endif; ?>
			</style>
			<?php
		
		endif;
			
		$btn = ob_get_clean();
		
		return $btn;	

	}
}


# Element Information
$lab_vc_element_path    = dirname( __FILE__ ) . '/';
$lab_vc_element_url     = site_url(str_replace(ABSPATH, '', $lab_vc_element_path));
$lab_vc_element_icon    = $lab_vc_element_url . '../assets/images/laborator.png';

$colors_arr = array(
	__( 'Primary', 'lab_composer' )    => 'btn-primary',
	__( 'Black', 'lab_composer' )      => 'btn-black',
	__( 'Blue', 'lab_composer' )       => 'btn-blue',
	__( 'Dark Red', 'lab_composer' )   => 'btn-dark-red',
	__( 'Green', 'lab_composer' )      => 'btn-green',
	__( 'Yellow', 'lab_composer' )     => 'btn-warning',
	__( 'White', 'lab_composer' )      => 'btn-white',
	__( 'Gray', 'lab_composer' )       => 'btn-gray',
);

vc_map( array(
	'base'             => 'laborator_button',
	'name'             => __('Button', 'lab_composer'),
	"description"      => __("Insert button link", "lab_composer"),
	'category'         => __('Laborator', 'lab_composer'),
	'icon'             => $lab_vc_element_icon,
	'params' => array(
		array(
			'type'           => 'textfield',
			'heading'        => __( 'Button Title', 'lab_composer' ),
			'param_name'     => 'title',
			'admin_label'    => true,
			'value'          => ''
		),
		array(
			'type'           => 'vc_link',
			'heading'        => __( 'Button Link', 'lab_composer' ),
			'param_name'     => 'link',
		),
		array(
			'type'           => 'dropdown',
			'heading'        => __( 'Button Type', 'lab_composer' ),
			'param_name'     => 'type',
			'std'            => 'default',
			'admin_label'    => true,
			'value'          => array(
				__('Standard', 'lab_composer')                          => 'standard',
				__('Outlined', 'lab_composer')                          => 'outlined',
			),
			'description' => __( 'Select between two button types.', 'lab_composer' )
		),
		array(
			'type'           => 'dropdown',
			'heading'        => __( 'Button Size', 'lab_composer' ),
			'param_name'     => 'size',
			'std'            => 'btn-md',
			'value'          => array(
				__('Small', 'lab_composer')     => 'btn-sm',
				__('Medium', 'lab_composer')    => 'btn-md',
				__('Large', 'lab_composer')     => 'btn-lg',
			),
			'description' => __( 'Select button size: S, M, L.', 'lab_composer' )
		),
		array(
			'type'           => 'dropdown',
			'heading'        => __( 'Background Color', 'lab_composer' ),
			'param_name'     => 'button_bg',
			'admin_label'    => true,
			'value'          => array_merge( $colors_arr, array( __( 'Custom color', 'lab_composer' ) => 'custom' ) ),
			'std'            => 'btn-primary',
			'description'    => __( 'Select button background (and/or border) color.', 'lab_composer' )
		),
		array(
			'type'           => 'colorpicker',
			'heading'        => __( 'Custom Background Color', 'lab_composer' ),
			'param_name'     => 'button_bg_custom',
			'description'    => __( 'Custom background color for button.', 'lab_composer' ),
			'dependency'     => array(
				'element'   => 'button_bg',
				'value'     => array( 'custom' )
			),
		),
		array(
			'type'           => 'colorpicker',
			'heading'        => __( 'Custom Text Color', 'lab_composer' ),
			'param_name'     => 'button_txt_custom',
			'description'    => __( 'Custom text color for button.', 'lab_composer' ),
			'dependency'     => array(
				'element'   => 'button_bg',
				'value'     => array( 'custom' )
			),
		),
		array(
			'type'           => 'colorpicker',
			'heading'        => __( 'Custom Background Hover Color', 'lab_composer' ),
			'param_name'     => 'button_bg_hover_custom',
			'description'    => __( 'Custom background/border hover color for button (where applied).', 'lab_composer' ),
			'dependency'     => array(
				'element'   => 'button_bg',
				'value'     => array( 'custom' )
			),
		),
		array(
			'type'           => 'colorpicker',
			'heading'        => __( 'Custom Text Hover Color', 'lab_composer' ),
			'param_name'     => 'button_txt_hover_custom',
			'description'    => __( 'Custom text hover color for button (where applied).', 'lab_composer' ),
			'dependency'     => array(
				'element'   => 'button_bg',
				'value'     => array( 'custom' )
			),
		),
		array(
			'type'           => 'textfield',
			'heading'        => __( 'Extra class name', 'lab_composer' ),
			'param_name'     => 'el_class',
			'description'    => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'lab_composer' )
		),
		array(
			'type'       => 'css_editor',
			'heading'    => __( 'Css', 'lab_composer' ),
			'param_name' => 'css',
			'group'      => __( 'Design options', 'lab_composer' )
		)
	)
) );