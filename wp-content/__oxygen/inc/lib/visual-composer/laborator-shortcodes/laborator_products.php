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
		global $parsed_from_vc, $quickview_enabled, $row_clear, $quickview_wp_query;

		if( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		}
		
		extract(shortcode_atts(array(
			'products_query' => '',
			'product_types_to_show' => '',
			'row_clear' => '4',
			'el_class' => '',
			'css' => '',
		), $atts));


		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_products laborator-woocommerce shop wpb_content_element '.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);

		list($args, $products_query) = vc_build_loop_query($products_query);



		# Show Featured Products Only
		if($product_types_to_show == 'only_featured')
		{
			$args['meta_key'] = '_featured';
			$args['meta_value'] = 'yes';

			$products_query = new WP_Query($args);
		}
		else
		# Show Products on Sale Only
		if($product_types_to_show == 'only_on_sale')
		{
			$args['meta_query']= array(
				'relation' => 'OR',
				array(
					'key'           => '_sale_price',
					'value'         => 0,
					'compare'       => '>',
					'type'          => 'numeric'
				),
				array(
					'key'           => '_min_variation_sale_price',
					'value'         => 0,
					'compare'       => '>',
					'type'          => 'numeric'
				)
			);

			$products_query = new WP_Query($args);
		}

		if(get_data('shop_quickview')):

			wp_enqueue_script('cbp-grid-gallery');
			wp_enqueue_style('cbp-grid-gallery');

		endif;

		$parsed_from_vc = true;
		$quickview_enabled = $args;
		$quickview_wp_query = clone $products_query;

		ob_start();


		?>
		<div class="<?php echo $css_class; ?>">

			<?php if ( $products_query->have_posts() ) : ?>


				<?php woocommerce_product_loop_start(); ?>

					<?php $i = 1; while ( $products_query->have_posts() ) : $products_query->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

						<?php
						# start: modified by Arlind Nushi
						echo $i % $row_clear == 0 ? '<div class="clear"></div>' : '';
						# end: modified by Arlind Nushi
						?>

					<?php $i++; endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>

			<?php endif; ?>

		</div>
		<?php


		$output = ob_get_contents();
		ob_end_clean();

		$parsed_from_vc = false;
		$quickview_enabled = null;
		$row_clear = null;

		return $output;
	}
}

// Shortcode Options
$opts = array(
	"name"		=> __("Products", TD),
	"description" => __('Display shop products on custom query.', TD),
	"base"		=> "laborator_products",
	"class"		=> "vc_laborator_products",
	"icon"		=> "icon-lab-products",
	"controls"	=> "full",
	"category"  => __('Laborator', TD),
	"params"	=> array(


		array(
			"type" => "loop",
			"heading" => __("Products Query", TD),
			"param_name" => "products_query",
			'settings' => array(
				'size' => array('hidden' => false, 'value' => SHOPCOLUMNS * 2),
				'order_by' => array('value' => 'date'),
				'post_type' => array('value' => 'product', 'hidden' => false)
			),
			"description" => __("Create WordPress loop, to populate products from your site.", TD)
		),

		array(
			"type" => "dropdown",
			"heading" => __("Filter Products by Type", TD),
			"param_name" => "product_types_to_show",
			"value" => array(
				"Show all types of products from the above query"  => '',
				"Show only featured products from the above query."  => 'only_featured',
				"Show only products on sale from the above query."  => 'only_on_sale',
			),
			"description" => __("Based on layout columns you use, select number of columns to wrap the product.", TD)
		),

		array(
			"type" => "dropdown",
			"heading" => __("Row Clearing", TD),
			"param_name" => "row_clear",
			"value" => array(
				"After fourth product"  => 4,
				"After sixth product"  => 6,
				"After third product" 	=> 3,
				"After second product"  => 2,
				"After first product"   => 1,
			),
			"description" => __("Based on layout columns you use, select when the product items will be cleared to new row.", TD)
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
#new Laborator_VC_Products($opts);