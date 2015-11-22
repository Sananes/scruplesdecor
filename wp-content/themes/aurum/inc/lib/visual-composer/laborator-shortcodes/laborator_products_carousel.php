<?php
/**
 *	Products Carousel Shortcode for Visual Composer
 *
 *	Laborator.co
 *	www.laborator.co
 */


class WPBakeryShortCode_laborator_products_carousel extends  WPBakeryShortCode
{
	public function content($atts, $content = null)
	{
		global $products_columns;

		extract(shortcode_atts(array(
			'products_query' => '',
			'featured_products' => '',
			'columns' => '',
			'auto_rotate' => '',
			'el_class' => '',
			'css' => '',
		), $atts));


		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_products_carousel laborator-woocommerce shop wpb_content_element products-hidden '.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);

		if($columns == 1)
			$css_class .= ' single-column';

		list($args, $products_query) = vc_build_loop_query($products_query);

		# Show Featured Products Only
		if($featured_products)
		{
			$args['meta_key'] = '_featured';
			$args['meta_value'] = 'yes';

			$products_query = new WP_Query($args);
		}

		$rand_id = "el_" . time() . mt_rand(10000,99999);


		wp_enqueue_script('owl-carousel');
		wp_enqueue_style('owl-carousel');

		ob_start();

		$products_columns = -1;

		?>
		<div class="<?php echo $css_class; ?>" id="<?php echo $rand_id; ?>">

			<div class="products-loading">
				<?php _e('Loading products...', 'lab_composer'); ?>
			</div>

			<?php if ( $products_query->have_posts() ) : ?>

				<div class="products">

				<?php #woocommerce_product_loop_start(); ?>

					<?php $i = 1; while ( $products_query->have_posts() ) : $products_query->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php $i++; endwhile; // end of the loop. ?>

				<?php #woocommerce_product_loop_end(); ?>

				</div>

			<?php endif; ?>

		</div>

		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var $carousel_el = $("#<?php echo $rand_id; ?> .products");

				$("#<?php echo $rand_id; ?>").removeClass('products-hidden');

				$carousel_el.owlCarousel({
					items: <?php echo $columns; ?>,
					navigation: true,
					pagination: false,
					autoPlay: <?php echo absint($auto_rotate) <= 0 ? 'false' : $auto_rotate * 1000; ?>,
					stopOnHover: true,
					singleItem: <?php echo $columns == 1 ? 'true' : 'false'; ?>,
					direction: _rtl()
				});
			});
		</script>
		<?php


		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

// Shortcode Options
$opts = array(
	"name"		=> __("Products Carousel", 'lab_composer'),
	"description" => __('Display shop products with Touch Carousel.', 'lab_composer'),
	"base"		=> "laborator_products_carousel",
	"class"		=> "vc_laborator_products_carousel",
	"icon"		=> "icon-lab-products-carousel",
	"controls"	=> "full",
	"category"  => __('Laborator', 'lab_composer'),
	"params"	=> array(


		array(
			"type" => "loop",
			"heading" => __("Products Query", 'lab_composer'),
			"param_name" => "products_query",
			'settings' => array(
				'size' => array('hidden' => false, 'value' => SHOP_COLUMNS * 4),
				'order_by' => array('value' => 'date'),
				'post_type' => array('value' => 'product', 'hidden' => false)
			),
			"description" => __("Create WordPress loop, to populate products from your site.", 'lab_composer')
		),

		array(
			'type'        => 'checkbox',
			'heading'     => __( 'Show Only Featured Products', 'lab_composer' ),
			'param_name'  => 'featured_products',
			'description' => __( 'Show only featured products from the above query.', 'lab_composer' ),
			'value'       => array( __( 'Yes', 'lab_composer' ) => 'yes' )
		),

		array(
			"type" => "dropdown",
			"heading" => __("Columns count", 'lab_composer'),
			"param_name" => "columns",
			"value" => array(
				"6 Columns"  => 6,
				"5 Columns"  => 5,
				"4 Columns"  => 4,
				"3 Columns"  => 3,
				"2 Columns"  => 2,
				"1 Column"   => 1,
			),
			"description" => __("Based on layout columns you use, select number of columns to wrap the product.", 'lab_composer')
		),

		array(
			"type" => "textfield",
			"heading" => __("Auto Rotate", 'lab_composer'),
			"param_name" => "auto_rotate",
			"value" => "5",
			"description" => __("You can set automatic rotation of carousel, unit is seconds. Enter 0 to disable.", 'lab_composer')
		),

		array(
			"type" => "textfield",
			"heading" => __("Extra class name", 'lab_composer'),
			"param_name" => "el_class",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'lab_composer')
		),

		array(
			"type" => "css_editor",
			"heading" => __('Css', 'lab_composer'),
			"param_name" => "css",
			"group" => __('Design options', 'lab_composer')
		)
	)
);

// Add & init the shortcode
wpb_map($opts);