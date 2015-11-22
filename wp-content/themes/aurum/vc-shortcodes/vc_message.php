<?php

/* Note: This file has been altered by Laborator */

$output = $color = $el_class = $css_animation = '';
extract(shortcode_atts(array(
    'color' => 'alert-info',
    'el_class' => '',
    'style' => '',
    'css_animation' => ''
), $atts));
$el_class = $this->getExtraClass($el_class);

$class = "";
//$style = "square_outlined";
#$class .= ( $color != '' && $color != "alert-block") ? ' wpb_'.$color : '';

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_content_element alert ' . $color . $class . $el_class, $this->settings['base'], $atts );

$css_class .= $this->getCSSAnimation($css_animation);
?>
<div class="<?php echo $css_class; ?>" role="alert">
	<div class="messagebox_text"><?php echo wpb_js_remove_wpautop($content, true); ?></div>
</div>
<?php echo $this->endBlockComment('alert box')."\n";