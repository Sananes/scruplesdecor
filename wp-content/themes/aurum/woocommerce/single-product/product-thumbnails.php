<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.3
 */

/* Note: This file has been altered by Laborator */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

# start: modified by Arlind Nushi
$thumbnails_to_show = get_data('shop_single_aside_thumbnails_count');

if(has_post_thumbnail() && count($attachment_ids))
{
	array_unshift($attachment_ids, get_post_thumbnail_id());
}
# end: modified by Arlind Nushi

if ( $attachment_ids ) {
	?>
	<div class="product-thumbnails" data-show="<?php echo $thumbnails_to_show; ?>"><?php

		$loop = 0;
		$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );

			if ( $loop == 0 || $loop % $columns == 0 )
				$classes[] = 'first';

			if ( ( $loop + 1 ) % $columns == 0 )
				$classes[] = 'last';

			$image_link = wp_get_attachment_url( $attachment_id );

			if ( ! $image_link )
				continue;

			# start: modified by Arlind Nushi
			$classes[] = 'item-image';

			if($loop == 0)
				$classes[] = 'active';

			if($loop > $thumbnails_to_show - 1)
				$classes[] = 'hidden';
			# end: modified by Arlind Nushi

			$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop-thumb-2' ) );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s">%s</a>', $image_link, $image_class, $image_title, $image ), $attachment_id, $post->ID, $image_class );

			$loop++;
		}

	?></div>
	<?php
}