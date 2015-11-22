<?php
/**
 *	Look Book Products Shortcode for Visual Composer
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


#
class WPBakeryShortCode_laborator_lookbook_carousel extends WPBakeryShortCode
{
	public function content($atts, $content = null)
	{
		global $parsed_from_vc, $quickview_enabled, $row_clear, $is_lookbook_carousel, $quickview_wp_query;
		
		if( function_exists( 'vc_map_get_attributes' ) ) {
			$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		}
		
		extract(shortcode_atts(array(
			'title' => '',
			'full_width' => '',
			'products_query' => '',
			'row_clear' => '',
			'auto_rotate' => '',
			'size' => '',
			'overlay_bg' => '',
			'spacing' => 0,
			'pager_pagination' => '',
			'el_class' => '',
			'css' => '',
		), $atts));
		
		
		$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,'lab_wpb_lookbook_carousel laborator-woocommerce shop wpb_content_element products-hidden '.$el_class.vc_shortcode_custom_css_class($css, ' '), $this->settings['base']);
		
		if($row_clear == 1)
			$css_class .= ' single-column';
		
		if($full_width)
			$css_class .= ' is-fullwidth';
		else
			$css_class .= ' normal-width';
		
		list($args, $products_query) = vc_build_loop_query($products_query);
		$quickview_wp_query = clone $products_query;
		
		
		$rand_id = "el_" . time() . mt_rand(10000,99999);
		
		$size = explode("x", trim($size));
		$pager_pagination = explode(',', $pager_pagination);
		
		ob_start();
		
		
		?>
		<style>
			#<?php echo $rand_id; ?>.lab_wpb_lookbook_carousel .lookbook-carousel .product-item:hover .lookbook-hover-info {
				background-color: <?php echo $overlay_bg; ?>;
			}
			
			#<?php echo $rand_id; ?>.lab_wpb_lookbook_carousel .lookbook-carousel .product-item {
				padding-left: <?php echo absint($spacing)/2; ?>px;
				padding-right: <?php echo absint($spacing)/2; ?>px;
			}
			
			#<?php echo $rand_id; ?>.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info {
				left: <?php echo absint($spacing)/2; ?>px;
				right: <?php echo absint($spacing)/2; ?>px;
			}
		</style>
		<div class="<?php echo $css_class; ?>" id="<?php echo $rand_id; ?>">
			
			<?php if(in_array('pager', $pager_pagination) || $title): ?>
			<div class="lookbook-header">
				<h2><?php echo $title; ?></h2>
				
				<div class="pager"></div>
			</div>
			<?php endif; ?>
			
			<div class="products-loading">
				<div class="loader">
					<strong><?php _e('Loading products...', TD); ?></strong>
					<span></span>
					<span></span>
					<span></span>
				</div>
			</div>
		
			<?php if ( $products_query->have_posts() ) : ?>
			
				<div class="lookbook-carousel">
				<?php $i = 1; while ( $products_query->have_posts() ) : $products_query->the_post(); ?>
					
					<div class="product-item product cols-<?php echo $row_clear; ?>">
						
						<?php
						
						global $post;
						
						$id = get_the_id();
						$thumb_id = 0;
						$thumb_size = apply_filters('oxygen_shop_loop_thumb_large', 'shop-thumb-1-large');
						
						$product = get_product($post);
						
						if(has_post_thumbnail())
						{
							$thumb_id = get_post_thumbnail_id();
							$image_link = wp_get_attachment_url($thumb_id);
						}
						else
						{
							$product_images = $product->get_gallery_attachment_ids();
							
							if(count($product_images))
							{
								$thumb_id = $product_images[0];
								$image_link = wp_get_attachment_url($thumb_id);
							}
							else
							{
								$image_link = wc_placeholder_img_src();
							}
						}
						
						?>
						
						<?php if(count($size) == 2): ?>
							
							<?php echo laborator_show_img($image_link, $size[0], $size[1], 4); ?>
							<?php #echo remove_wh(wp_get_attachment_image($thumb_id, array($size[0], $size[1], true))); ?>
						
						<?php else: ?>
						
							<?php echo laborator_show_img($image_link, 0, $size[0]); ?>
							<?php #echo remove_wh(wp_get_attachment_image($thumb_id, array(null, $size[0]))); ?>
							
						<?php endif; ?>
						
						<div class="lookbook-hover-info">
							
							<div class="lookbook-inner-content">
								<?php echo $product->get_categories( ', ', '<span class="posted_in">', '</span>' ); ?>
								
								<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
								
								<?php if( ! is_catalog_mode() && ! catalog_mode_hide_prices() ): ?>
								<div class="divider"></div>
								
								<div class="price-and-add-to-cart">
								
									<?php if ( $price_html = $product->get_price_html() ) : ?>
										<span class="price"><?php echo $price_html; ?></span>
									<?php endif; ?>
									
									
									<?php if( ! is_catalog_mode()): ?>
									
										<?php if($product->is_type('variable')): ?>
										<a class="add-to-cart-btn" href="<?php echo $product->get_permalink(); ?>">
											<?php _e('Select Options', TD); ?>
											<i class="entypo-list-add"></i>
										</a>
										
										<?php elseif($product->is_type('grouped')): ?>
										<a class="add-to-cart-btn" href="<?php echo $product->get_permalink(); ?>">
											<?php _e('Select Products', TD); ?>
											<i class="entypo-list-add"></i>
										</a>
										
										<?php elseif($product->is_type('external')): ?>
										<a class="add-to-cart-btn" href="<?php echo $product->get_product_url(); ?>" target="_blank">
											<?php echo $product->single_add_to_cart_text(); ?>
											<i class="entypo-export"></i>
										</a>
										
										<?php else: ?>
										<a class="add-to-cart-btn add-to-cart" data-id="<?php echo $product->post->ID; ?>" href="#">
											<?php _e('Add to Cart', TD); ?>
											<i class="entypo-basket"></i>
										</a>
										<?php endif; ?>
									
									<?php endif; ?>
									
								</div>
								<?php endif; ?>
								
							</div>
							
							<div class="loading-disabled">
								<div class="loader">
									<strong><?php _e('Adding to cart...', TD); ?></strong>
									<span></span>
									<span></span>
									<span></span>
								</div>
							</div>
							
						</div>
						
					</div>
					
				<?php $i++; endwhile; // end of the loop. ?>
				</div>
				
			<?php endif; ?>
			
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var $container = $("#<?php echo $rand_id; ?>"),
					$carousel = $container.find('.lookbook-carousel');

				if($container.hasClass('is-fullwidth'))
					forceFullWidth( $carousel );
				
				imagesLoaded($carousel, function()
				{
					$container.removeClass('products-hidden');
					
					if($container.hasClass('is-fullwidth'))
						forceFullWidth( $carousel ); // Recall the fullwidth
					
					$carousel.owlCarousel({
						items: <?php echo $row_clear; ?>,
						navigation: <?php echo in_array('pager', $pager_pagination) ? 'true' : 'false'; ?>,
						pagination: <?php echo in_array('pagination', $pager_pagination) ? 'true' : 'false'; ?>,
						autoPlay: <?php echo absint($auto_rotate) <= 0 ? 'false' : $auto_rotate * 1000; ?>,
						stopOnHover: true,
						singleItem: <?php echo $row_clear == 1 ? 'true' : 'false'; ?>
					});
					
					// Pager change place
					$container.find('.pager').append( $container.find('.owl-buttons') );
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
	"name"		=> __("Lookbook Carousel", TD),
	"description" => __('Display shop products on full width.', TD),
	"base"		=> "laborator_lookbook_carousel",
	"class"		=> "vc_laborator_lookbook_carousel",
	"icon"		=> "icon-lab-lookbook-carousel",
	"controls"	=> "full",
	"category"  => __('Laborator', TD),
	"params"	=> array(
	
	
		array(
			"type" => "loop",
			"heading" => __("Products Query", TD),
			"param_name" => "products_query",
			'settings' => array(
				'size' => array('hidden' => false, 'value' => SHOPCOLUMNS * 4),
				'order_by' => array('value' => 'date'),
				'post_type' => array('value' => 'product', 'hidden' => false)
			),
			"description" => __("Create WordPress loop, to populate products from your site.", TD)
		),
		
		array(
			"type" => "textfield",
			"heading" => __("Widget title", TD),
			"param_name" => "title",
			"value" => "Lookbook",
			"description" => __("What text use as widget title. Leave blank if no title is needed.", TD)
		),
		
		array(
			"type" => "checkbox",
			"heading" => __("Full Width", TD),
			"param_name" => "full_width",
			"value" => array( __( "Yes, please", TD ) => true ),
			"description" => __("Allow Lookbook container to occopy full width of the browser.", TD)
		),
		
		array(
			"type" => "dropdown",
			"heading" => __("Columns count", TD),
			"param_name" => "row_clear",
			"std" => 4,
			"value" => array(
				"6 Columns"  => 6,
				"5 Columns"  => 5,
				"4 Columns"  => 4,
				"3 Columns"  => 3,
				"2 Columns"  => 2,
				"1 Column"   => 1,
			),
			"description" => __("Select number of columns you want to paginate products.", TD)
		),
		
		array(
			"type" => "textfield",
			"heading" => __("Auto Rotate", TD),
			"param_name" => "auto_rotate",
			"value" => "5",
			"description" => __("You can set automatical rotation of carousel, unit is seconds. Enter 0 to disable.", TD)
		),
		
		array(
			"type" => "textfield",
			"heading" => __("Image size", TD),
			"param_name" => "size",
			"value" => "500x596",
			"description" => __("Set the product image size to show. Type: {width}x{height}, if you enter just a number it will resize the image by height.", TD)
		),
		
		array(
			"type" => "colorpicker",
			"heading" => __("Overlay Color", TD),
			"param_name" => "overlay_bg",
			"value" => "rgba(130,2,2,0.85)",
			"description" => __("Background color of the layer when product is hovered.", TD)
		),
		
		array(
			"type" => "textfield",
			"heading" => __("Spacing", TD),
			"param_name" => "spacing",
			"value" => "30",
			"description" => __("Set elements margin in pixels unit.", TD)
		),
		
		array(
			"type" => "checkbox",
			"heading" => __("Pager and Pagination", TD),
			"param_name" => "pager_pagination",
			"std" => '',
			"value" => array( 
				__( "Show pager (next, prev)<br />", TD ) => 'pager',
				__( "Show pagination (dots)", TD ) => 'pagination',
			),
			"description" => __("Set the visibility of pager and pagination.", TD)
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
//new Laborator_VC_Lookbook_Carousel($opts);
