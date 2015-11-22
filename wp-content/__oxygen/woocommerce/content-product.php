<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce_loop, $quickview_enabled, $row_clear, $is_products_carousel;

$id = get_the_id();


// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array('col-sm-3');

if(SHOPCOLUMNS == 3 && ! is_product())
	$classes = array('col-sm-4 col-xs-6');

if(SHOPSINGLESIDEBAR && is_product())
	$classes = array('col-sm-4 col-xs-6');

if($row_clear)
{
	$classes = array();

	if($row_clear == 6)
		$classes = array('col-xs-6 col-xs-6 col-sm-2');
	else
	if($row_clear == 4)
		$classes = array('col-xs-6 col-sm-3');
	else
	if($row_clear == 3)
		$classes = array('col-xs-6 col-sm-6');
	else
	if($row_clear == 2)
		$classes = array('col-xs-6 col-sm-6');
}

if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';


# start: modified by Arlind Nushi
$post_cloned = $post;

$product_images = $product->get_gallery_attachment_ids();
$product_images_urls = array();
$product_images_urls_ids = array();

$post = $post_cloned;

if($product_images)
{
	foreach($product_images as $attachment_id):

		$image_link = wp_get_attachment_url( $attachment_id );

		if( ! $image_link)
			continue;

		$product_images_urls[] = $image_link;
		$product_images_urls_ids[$image_link] = $attachment_id;

	endforeach;

	if($product_images_urls)
		$classes[] = 'has-gallery';
}
# end: modified by Arlind Nushi
?>
<div <?php post_class( $classes ); ?>>

	<?php
	# start: modified by Arlind Nushi
	?>
	<div class="item-wrapper">
		<div class="item">
	<?php
	# end: modified by Arlind Nushi
	?>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>



		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>

		<?php
		# start: modified by Arlind Nushi
		$post = $post_cloned;
		$item_preview_type = get_data('shop_item_preview_type');
		?>
		<div class="image<?php echo $item_preview_type == 'Second Image on Hover' && count($product_images_urls) ? ' hover-second-only' : ' full-gallery'; ?>">

			<a href="<?php the_permalink(); ?>" class="thumb">
				<?php
				
				$thumb_size = apply_filters('oxygen_shop_loop_thumb', 'shop-thumb-1');
				
				if(isset($row_clear) && $row_clear < 3)
				{
					$thumb_size = apply_filters('oxygen_shop_loop_thumb_large', 'shop-thumb-1-large');
				}

				if(has_post_thumbnail())
				{
					#echo laborator_show_img($id, $thumb_size);
					the_post_thumbnail($thumb_size);
				}
				else
				{
					if(count($product_images_urls))
					{
						$image_id = $product_images_urls_ids[array_shift($product_images_urls)];
						#echo laborator_show_img($image_id, $thumb_size);
						echo remove_wh( wp_get_attachment_image($image_id, $thumb_size) );
					}
					else
					{
						echo laborator_show_img(wc_placeholder_img_src(), $thumb_size);
					}
				}

				if($product_images_urls && $item_preview_type != 'None'):

					$main_thumbnail_url = $first_attachment_link = wp_get_attachment_url( get_post_thumbnail_id() );

					$product_images_urls = array_diff($product_images_urls, array($main_thumbnail_url));

					if($item_preview_type == 'Second Image on Hover')
					{
						$product_images_urls = array_slice($product_images_urls, 0, 1);
					}

					$product_images_urls = array_diff($product_images_urls, array($main_thumbnail_url));

					foreach($product_images_urls as $attachment_url):

						#echo laborator_show_img_lazy($attachment_url, 'shop-thumb-1', 0, 0, array('class' => 'hidden-slowly'));
						$image_src = wp_get_attachment_image_src($product_images_urls_ids[$attachment_url], apply_filters('oxygen_shop_loop_thumb', 'shop-thumb-1'));
						$image_title = get_the_title($product_images_urls_ids[$attachment_url]);

						if($image_src)
						{
							$image_src = $image_src[0];

							echo '<img src="'.wc_placeholder_img_src().'" data-src="' . $image_src . '" alt="' . esc_attr($image_title) . '" class="hidden-slowly lab-lazy-load" />';
						}

					endforeach;

				endif;
				?>
			</a>

			<?php

			if(is_yith_wishlist_supported()):

				oxygen_yith_wcwl_add_to_wishlist();

			endif;

			/*
			if(is_wishlist_supported()):

				if($product->is_type('external') == false && $product->is_in_stock()):
					$wishlisted = woocommerce_wishlists_get_wishlists_for_product($product->id);

					?>
					<div class="wish-list">
						<a href="#" class="glyphicon glyphicon-heart type-<?php echo $product->product_type; echo $wishlisted ? ' wishlisted' : ''; ?>" data-id="<?php echo $product->id; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo esc_attr(apply_filters('woocommerce_wishlist_add_to_wishlist_text', WC_Wishlists_Settings::get_setting('wc_wishlist_button_text', 'Add to wishlist'), $product->product_type)); ?>"></a>
					</div>
					<?php
				endif;

			endif;
			*/
			?>

			<?php if(get_data('shop_quickview')): ?>

				<?php if(is_shop() || is_product_category() || is_product_tag() || $quickview_enabled): ?>
				<div class="quick-view">
					<a href="#">
						<i class="entypo-popup"></i>
						<?php _e('Quick View', 'oxygen'); ?>
					</a>
				</div>
				<?php endif; ?>

			<?php endif; ?>

			<?php
			# Rating
			if ( get_data('shop_rating_show') && get_option( 'woocommerce_enable_review_rating' ) != 'no' && $product->get_average_rating() > 0):
			?>

			<div class="rating filled-<?php echo intval($product->get_average_rating()); echo $product->get_average_rating() - intval($product->get_average_rating()) > .49 ? ' and-half' : ''; ?>">
				<span class="glyphicon glyphicon-star star-1"></span>
				<span class="glyphicon glyphicon-star star-2"></span>
				<span class="glyphicon glyphicon-star star-3"></span>
				<span class="glyphicon glyphicon-star star-4"></span>
				<span class="glyphicon glyphicon-star star-5"></span>
			</div>
			<?php endif; ?>
		</div>

		<div class="white-block description">
			<h4 class="title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h4>

			<?php if(get_data('shop_product_category_listing')): ?>
			<span class="type">
				<?php the_terms($id, 'product_cat'); ?>
			</span>
			<?php endif; ?>

			<?php if(get_data('shop_product_price_listing')): ?>
			<div class="divider"></div>

			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

			<?php endif; ?>

			<div class="error-container">
			</div>
		</div>
		<?php
		# end: modified by Arlind Nushi
		?>

	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

	<?php
	# start: modified by Arlind Nushi
	?>
			<div class="loading-disabled">
				<div class="loader">
					<strong><?php _e('Adding to cart', 'oxygen'); ?></strong>
					<span></span>
					<span></span>
					<span></span>
				</div>
			</div>

		</div>
	</div>
	<?php
	# end: modified by Arlind Nushi
	?>

</div>