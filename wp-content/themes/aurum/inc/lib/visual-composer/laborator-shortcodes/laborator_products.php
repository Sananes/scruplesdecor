<?php
/**
 *	Products Shortcode for Visual Composer
 *
 *	Laborator.co
 *	www.laborator.co
 */


class WPBakeryShortCode_laborator_products extends  WPBakeryShortCode
{
	public function content($atts, $content = null)
	{
		global $products_columns;

		extract(shortcode_atts(array(
			'products_query' => '',
			'featured_products' => '',
			'columns' => '',
			'el_class' => '',
			'css' => '',
		), $atts));


		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_products laborator-woocommerce shop wpb_content_element '.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);

		list($args, $products_query) = vc_build_loop_query($products_query);

		# Show Featured Products Only
		if($featured_products)
		{
			$args['meta_key'] = '_featured';
			$args['meta_value'] = 'yes';

			$products_query = new WP_Query($args);
		}

		ob_start();

		$products_columns = $columns;
		?>
		<div class="<?php echo $css_class; ?>">

			<div class="products">
			<?php if ( $products_query->have_posts() ) : ?>


				<?php //woocommerce_product_loop_start(); ?>

					<?php $i = 1; while ( $products_query->have_posts() ) : $products_query->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

						<?php
						# start: modified by Arlind Nushi
						echo $i % $columns == 0 ? '<div class="clear"></div>' : '';
						# end: modified by Arlind Nushi
						?>

					<?php $i++; endwhile; // end of the loop. ?>

				<?php //woocommerce_product_loop_end(); ?>

			<?php endif; ?>
			</div>

		</div>
		<?php

		$products_columns = null;


		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}

// Shortcode Options
$opts = array(
	"name"		=> __("Products", 'lab_composer' ),
	"description" => __('Display shop products on custom query.', 'lab_composer' ),
	"base"		=> "laborator_products",
	"class"		=> "vc_laborator_products",
	"icon"		=> "icon-lab-products",
	"controls"	=> "full",
	"category"  => __('Laborator', 'lab_composer' ),
	"params"	=> array(


		array(
			"type" => "loop",
			"heading" => __("Products Query", 'lab_composer' ),
			"param_name" => "products_query",
			'settings' => array(
				'size' => array('hidden' => false, 'value' => SHOP_COLUMNS * 2),
				'order_by' => array('value' => 'date'),
				'post_type' => array('value' => 'product', 'hidden' => false)
			),
			"description" => __("Create WordPress loop, to populate products from your site.", 'lab_composer' )
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
			"heading" => __("Columns", 'lab_composer' ),
			"param_name" => "columns",
			"value" => array(
				"Six Columns"   => 6,
				"Four Columns"  => 4,
				"Three Columns" => 3,
				"Two Columns"   => 2,
				"One Column"    => 1,
			),
			"description" => __("Based on layout columns you use, select when the product items will be cleared to new row.", 'lab_composer' )
		),

		array(
			"type" => "textfield",
			"heading" => __("Extra class name", 'lab_composer' ),
			"param_name" => "el_class",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'lab_composer' )
		),

		array(
			"type" => "css_editor",
			"heading" => __('Css', 'lab_composer' ),
			"param_name" => "css",
			"group" => __('Design options', 'lab_composer' )
		)
	)
);

// Add & init the shortcode
wpb_map($opts);