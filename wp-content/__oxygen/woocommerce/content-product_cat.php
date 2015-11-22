<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop, $category_image, $animation_delay;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Increase loop count
$woocommerce_loop['loop'] ++;

# start: modified by Arlind Nushi
$rand_id = "el_" . time() . mt_rand(10000,99999);

$animation_class = 'wow flipInX';

if( ! isset($animation_delay))
	$animation_delay = 0;

$animation_delay += 100;
# end: modified by Arlind Nushi
?>
<div class="col-sm-3 col-xs-6">

	<div id="<?php echo $rand_id; ?>" <?php wc_product_cat_class( 'product-category product lab_wpb_banner_2 wpb_content_element banner-type-2' ); ?>>

		<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

		<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">

			<?php
				/**
				 * woocommerce_before_subcategory_title hook
				 *
				 * @hooked woocommerce_subcategory_thumbnail - 10
				 */
				do_action( 'woocommerce_before_subcategory_title', $category );
			?>
			<span class="ol"></span>

			<span class="centered">

				<span class="title <?php echo $animation_class; ?>" data-wow-delay="<?php echo $animation_delay; ?>ms">
					<strong><?php echo $category->name; ?></strong>
				</span>

			</span>

			<?php
				/**
				 * woocommerce_after_subcategory_title hook
				 */
				do_action( 'woocommerce_after_subcategory_title', $category );
			?>

		</a>

		<?php do_action( 'woocommerce_after_subcategory', $category ); ?>

	</div>

	<?php

	/*
	$imagesize = getimagesize($category_image);
?>
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
	<?php
*/ ?>

</div>