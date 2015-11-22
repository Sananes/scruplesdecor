<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product, $product_images, $has_gallery;

# start: modified by Arlind Nushi

	# Nivo Lightbox
	wp_enqueue_script('nivo-lightbox');
	wp_enqueue_style('nivo-lightbox-default');

$product_images = $product->get_gallery_attachment_ids();
$has_gallery = count($product_images) > 0;


	# Owl Carousel
	wp_enqueue_script('owl-carousel');
	wp_enqueue_style('owl-carousel');


$autoswitch = get_data('shop_single_auto_rotate_image');

if( ! is_numeric($autoswitch))
	$autoswitch = 5;

# end: modified by Arlind Nushi

?>
<div class="row">

	<?php if($has_gallery): ?>
	<div class="col-lg-2 col-md-2 hidden-sm hidden-xs">

		<?php do_action( 'woocommerce_product_thumbnails' ); ?>

	</div>
	<?php endif; ?>

	<div class="<?php echo $has_gallery ? 'col-lg-10 col-md-10' : 'col-lg-12'; ?>">

		<?php
		# start: modified by Arlind Nushi
		woocommerce_show_product_sale_flash();
		# end: modified by Arlind Nushi
		?>
		<div class="images hidden">
			<div class="thumbnails"></div>
		</div>

		<div class="product-images nivo" data-autoswitch="<?php echo 1000 * absint($autoswitch); ?>">

			<?php
				if ( has_post_thumbnail() ) {

					$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
					$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
					$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop-thumb-main' ), array(
						'title' => $image_title
						) );

					$attachment_count = count( $product->get_gallery_attachment_ids() );

					$gallery = '';

					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom item-image-big" title="%s" data-lightbox-gallery="shop-gallery">%s</a>', $image_link, $image_title, $image ), $post->ID );

					# start: modified by Arlind Nushi
					foreach($product_images as $attachment_id)
					{

						$image_link = wp_get_attachment_url( $attachment_id );

						if ( ! $image_link )
							continue;

						$attachment_url = wp_get_attachment_image_src($attachment_id, 'original');

						echo '<a href="'.$attachment_url[0].'" class="item-image-big hidden" data-lightbox-gallery="shop-gallery">';
							echo wp_get_attachment_image($attachment_id, 'shop-thumb-main');
						echo '</a>';
					}
					# end: modified by Arlind Nushi

				} else {

					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

				}
			?>


		</div>
	</div>
</div>