<?php

/* Note: This file has been altered by Laborator */

extract(shortcode_atts(array(
    'title' => '',
    'title_align' => '',
    'el_width' => '',
    'style' => '',
    'color' => '',
    'accent_color' => '',
    # start: modified by Arlind Nushi
    'title_style' => '',
    'title_text_color' => '',
    'use_icon' => '',
    'icon' => '',
    # end: modified by Arlind Nushi
    'el_class' => ''
), $atts));
$class = "vc_separator wpb_content_element";

//$el_width = "90";
//$style = 'double';
//$title = '';
//$color = 'blue';

$class .= ($title_align!='') ? ' vc_'.$title_align : '';
$class .= ($style!='') ? ' vc_sep_'.$style : '';
# start: modified by Arlind Nushi
$class .= " title-style-{$title_style}";

if($use_icon && $icon)
{
	wp_enqueue_style('vc-icons');

	if($title == 'Title')
		$title = '';

	$title = '<i class="vc-icon-'.$icon.'"></i>' . ($title ? ' ' : '') . $title;

	$class .= ' has-icon';
}
# end: modified by Arlind Nushi
if( $color!= '' && 'custom' != $color ) {
	$class .= ' vc_sep_color_' . $color;
}
$inline_css = ( 'custom' == $color && $accent_color!='') ? ' style="'.vc_get_css_color('border-color', $accent_color).'"' : '';


$class .= $this->getExtraClass($el_class);
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class, $this->settings['base'], $atts );

?>
<div class="<?php echo esc_attr(trim($css_class)); ?>">
	<span class="vc_sep_holder vc_sep_holder_l">
		<span<?php echo $inline_css; ?> class="vc_sep_line vc_first_line"></span>

		<?php if(in_array($style, array('double-border', 'double-border-2', 'thick'))): ?>
		<span<?php echo $inline_css; ?> class="vc_sep_line vc_second_line"></span>
		<?php endif; ?>
	</span>
	<?php if($title!=''): ?><h4<?php echo $inline_css; ?>><span class="title-container"<?php echo ($color == 'custom' || in_array($title_style, array('squared-filled', 'rounded-filled')) ) && $title_text_color ? (' style="'.vc_get_css_color('color', $title_text_color).'"') : $inline_css; ?>><?php echo $title; ?></span></h4><?php endif; ?>
	<span class="vc_sep_holder vc_sep_holder_r">
		<span<?php echo $inline_css; ?> class="vc_sep_line vc_first_line"></span>

		<?php if(in_array($style, array('double-border', 'double-border-2', 'thick'))): ?>
		<span<?php echo $inline_css; ?> class="vc_sep_line vc_second_line"></span>
		<?php endif; ?>
	</span>
</div>
<?php echo $this->endBlockComment('separator')."\n";