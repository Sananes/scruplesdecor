<?php
/**
 *	Image Banner Shortcode for Visual Composer
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $terms_list;

class WPBakeryShortCode_laborator_banner2 extends  WPBakeryShortCode
{
	public function content($atts, $content = null)
	{
		if( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		}
		
		extract(shortcode_atts(array(
			'image' => '',
			'size' => '',
			'is_category_link' => '',
			'product_term_id' => '',
			'title' => '',
			'description' => '',
			'font_color' => '',
			'href' => '',
			'type' => '',
			'overlay_bg' => '#000000',
			'animation' => '',
			'animation_delay' => '0s',
			'el_class' => '',
			'css' => '',
		), $atts));

		$rand_id = "el_" . time() . mt_rand(10000,99999);

		$link	 = vc_build_link($href);
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = trim($link['target']);

		if( ! $a_target)
			$a_target = '_self';

		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_banner_2 wpb_content_element '.$type.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);

		$size = explode("x", trim($size));
		$image_link = wp_get_attachment_url( $image );

		$animation_class = trim($animation) ? " wow {$animation}" : '';

		if($is_category_link == 'yes')
		{
			$term = get_term($product_term_id, 'product_cat');

			if($term && ! is_wp_error($term))
			{
				$a_target = '_self';
				$a_href = get_term_link( $term );

				$title = $term->name;
				$count = lab_total_cat_product_count( $term->term_id );

				//_n('%d items', TD); // Just for translation
				$description = sprintf( _n('%d item', '%d items', $count, 'oxygen'), $count);
			}
		}

		if( ! $animation)
			$animation_delay = '0s';

		ob_start();

		?>

		<?php if($font_color): ?>
		<style>
			#<?php echo $rand_id; ?> .title,
			#<?php echo $rand_id; ?> .line-bottom {
				color: <?php echo $font_color; ?>;
			}

			#<?php echo $rand_id; ?> .dividerx {
				border-bottom-color: <?php echo $font_color; ?>;
			}
		</style>
		<?php endif; ?>
		<div class="<?php echo $css_class; ?>" id="<?php echo $rand_id; ?>">

			<?php if($image_link): ?>

				<a href="<?php echo $a_href; ?>" target="<?php echo $a_target; ?>">

					<?php 
					$img = wpb_getImageBySize( array( 'attach_id' => $image, 'thumb_size' => $atts['size'], 'class' => 'banner-img' ) );
					echo $img['thumbnail'];
					?>
					<?php /*if(count($size) == 2): ?>

						<?php echo laborator_show_img($image_link, $size[0], $size[1], 4); ?>

					<?php else: ?>

						<?php echo laborator_show_img($image_link, 0, $size[0]); ?>

					<?php endif; */ ?>

					<span class="ol" style="background-color: <?php echo $overlay_bg; ?>;"></span>


					<span class="centered">
					<?php
					switch($type):

						case "banner-type-1":
						case "banner-type-3":
							?>
							<span class="title<?php echo $animation_class; ?>" data-wow-delay="<?php echo $animation_delay; ?>">
								<strong><?php echo $title; ?></strong>
							</span>
							<?php
							break;

						case "banner-type-2":
							?>
							<span class="title<?php echo $animation_class; ?>" data-wow-delay="<?php echo $animation_delay; ?>">
								<strong class="line-top"><?php echo $title; ?></strong>
								<span class="divider"></span>
								<strong class="line-bottom"><?php echo $description; ?></strong>
							</span>
							<?php
							break;

					endswitch;
					?>
					</span>

				</a>
				
				<?php /*
				$imagesize = getimagesize(ABSPATH . str_replace(site_url('/'), '', $image_link));
				<script type="text/javascript">
					jQuery(function($)
					{
						var $el = jQuery("#<?php echo $rand_id; ?>"),
							$centered = $el.find('.centered'),
							ratio = <?php echo $imagesize[1]/$imagesize[0]; ?>,
							autocenter = function(){
								$centered.css({top: $el.height()/2 - $centered.height()/2 , left: $el.width()/2 - $centered.width()/2 });

								if($centered.find('.wow').length == 0)
									$centered.addClass('visible');
							};

						$el.css({height: $el.width() * ratio});

						autocenter();

						imagesLoaded($el, function()
						{
							$el.css('height', '');
							autocenter();
						});

						setInterval(autocenter, 800);
					});
				</script>
				*/ ?>

			<?php endif; ?>

		</div>
		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

$animated_transitions_list = array(
	"None"                 => '',
	"bounce"               => "bounce",
	"flash"                => "flash",
	"pulse"                => "pulse",
	"rubberBand"           => "rubberBand",
	"shake"                => "shake",
	"swing"                => "swing",
	"tada"                 => "tada",
	"wobble"               => "wobble",
	"bounceIn"             => "bounceIn",
	"bounceInDown"         => "bounceInDown",
	"bounceInLeft"         => "bounceInLeft",
	"bounceInRight"        => "bounceInRight",
	"bounceInUp"           => "bounceInUp",
	"fadeIn"               => "fadeIn",
	"fadeInDown"           => "fadeInDown",
	"fadeInDownBig"        => "fadeInDownBig",
	"fadeInLeft"           => "fadeInLeft",
	"fadeInLeftBig"        => "fadeInLeftBig",
	"fadeInRight"          => "fadeInRight",
	"fadeInRightBig"       => "fadeInRightBig",
	"fadeInUp"             => "fadeInUp",
	"fadeInUpBig"          => "fadeInUpBig",
	"flip"                 => "flip",
	"flipInX"              => "flipInX",
	"flipInY"              => "flipInY",
	"lightspeedIn"         => "lightspeedIn",
	"rotateIn"             => "rotateIn",
	"rotateInDownLeft"     => "rotateInDownLeft",
	"rotateInDownRight"    => "rotateInDownRight",
	"rotateInUpLeft"       => "rotateInUpLeft",
	"rotateInUpRight"      => "rotateInUpRight",
	"slideInDown"          => "slideInDown",
	"slideInLeft"          => "slideInLeft",
	"slideInRight"         => "slideInRight",
	"slideInUp"            => "slideInUp",
	"hinge"                => "hinge",
	"rollIn"               => "rollIn",
	"zoomIn"               => "zoomIn",
	"zoomInDown"           => "zoomInDown",
	"zoomInLeft"           => "zoomInLeft",
	"zoomInRight"          => "zoomInRight",
	"zoomInUp"             => "zoomInUp"
);

// Shortcode Options
$product_categories = get_categories(array('taxonomy' => 'product_cat', 'pad_counts' => false));
$terms_list = array();

foreach($product_categories as $term)
{
	if(is_object($term))
		$terms_list[$term->name . " ({$term->count})"] = $term->term_id;
}


function lab_total_cat_product_count( $cat_id )
{
	$q = new WP_Query( array(
		'nopaging' => true,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $cat_id,
				'include_children' => true,
			),
		),
		'fields' => 'ids',
	) );

	return $q->post_count;
}

$opts = array(
	"name"		=> __("Image Banner", TD),
	"description" => __('Graphical banner or category with text.', TD),
	"base"		=> "laborator_banner2",
	"class"		=> "vc_laborator_banner2",
	"icon"		=> "icon-lab-banner2",
	"controls"	=> "full",
	"category"  => __('Laborator', TD),
	"params"	=> array(

		array(
			"type" => "attach_image",
			"heading" => __("Image", TD),
			"param_name" => "image",
			"value" => "",
			"description" => __("Set the banner image.", "js_composer")
		),

		array(
			"type" => "textfield",
			"heading" => __("Image size", TD),
			"param_name" => "size",
			"value" => "400x280",
			"description" => __("Enter the banner background size. Type: {width}x{height}, if you enter just a number it will resize the image by height.", TD)
		),


		array(
			"type" => "dropdown",
			"heading" => __("Use as Category Link", TD),
			"param_name" => "is_category_link",
			"std" => '',
			"value" => array(
				__( "No", TD ) => 'no',
				__( "Yes", TD ) => 'yes',
			),
			"description" => __("Instead of setting custom link, you can link this banner to a product category.", TD)
		),


		array(
			"type" => "dropdown",
			"heading" => __("Category", TD),
			"param_name" => "product_term_id",
			"std" => '',
			"value" => $terms_list,
			"description" => __("Select product category. Second Line is category items counter.", TD),
			'dependency' => array( 'element' => 'is_category_link', 'value' => array( 'yes' ) )
		),

		array(
			"type" => "textfield",
			"heading" => __("Widget title", TD),
			"param_name" => "title",
			"value" => "",
			"description" => __("What text use as widget title. Leave blank if no title is needed.", TD),
			'dependency' => array( 'element' => 'is_category_link', 'value' => array( 'no' ) )
		),

		array(
			"type" => "textfield",
			'admin_label' => true,
			"heading" => __("Second Line", TD),
			"param_name" => "description",
			"value" => "",
			"description" => __("Second Line Text.", TD),
			'dependency' => array( 'element' => 'is_category_link', 'value' => array( 'no' ) )
		),

		array(
			'type' => 'colorpicker',
			'heading' => __( 'Font Color', TD),
			'param_name' => 'font_color',
			'description' => __( 'Select font color', TD),
			'value' => '#fff'
		),

		array(
			"type" => "vc_link",
			"heading" => __("URL (Link)", TD),
			"param_name" => "href",
			"description" => __("Banner link.", TD),
			'dependency' => array( 'element' => 'is_category_link', 'value' => array( 'no' ) )
		),

		array(
			"type" => "dropdown",
			"heading" => __("Banner Type", TD),
			"param_name" => "type",
			"value" => array(
				"Bordered with Title Only"			  => 'banner-type-1',
				"Bordered with Title and Second Line"   => 'banner-type-2',
				"Top Bordered Only with Title"		  => 'banner-type-3',
			),
			"description" => __("Select the type of banner.", TD)
		),

		array(
			"type" => "colorpicker",
			"heading" => __("Overlay Color", TD),
			"param_name" => "overlay_bg",
			"value" => "rgba(0,0,0,0.2)",
			"description" => __("Select banner overlay layer color.", TD)
		),

		array(
			"type" => "dropdown",
			"heading" => __("Box Animation", TD),
			"param_name" => "animation",
			"value" => $animated_transitions_list,
			"description" => __("Select transition of the element when it is visible in viewport. <a href='http://daneden.github.io/animate.css/' target='_blank'>View transitions live &raquo;</a>", TD)
		),

		array(
			"type" => "textfield",
			"heading" => __("Animation Delay", TD),
			"param_name" => "animation_delay",
			"value" => "0s",
			"description" => __("When the elements is in viewport set the delay when the animation should start. Example values: <em>1s, 500ms</em>.", TD)
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
#new WPBakeryShortCode_laborator_banner2($opts);