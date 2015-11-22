<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product, $shown_id;

# start: modified by Arlind Nushi
wp_enqueue_script(array('nivo-lightbox'));
wp_enqueue_style(array('nivo-lightbox', 'nivo-lightbox-default'));

$attachment_ids = $product->get_gallery_attachment_ids();
# end: modified by Arlind Nushi

?>
<div class="images hidden">
	<div class="thumbnails"></div>
</div>

<div class="main-images product-images">

	<?php
	if(is_wishlist_supported()):

		if($product->is_type('external') == false && $product->is_in_stock()):

			global $add_to_wishlist_args;

			$lists = WC_Wishlists_User::get_wishlists();
			$wishlisted = woocommerce_wishlists_get_wishlists_for_product($product->id);
			?>
			<div class="wish-list<?php echo $wishlisted ? ' wishlisted' : ''; ?>">

				<a href="#" class="glyphicon glyphicon-heart wl-add-to<?php echo ! $lists ? ' wl-add-to-single' : ''; ?>" data-listid="" data-toggle="tooltip" data-placement="left" title="<?php echo esc_attr(apply_filters('woocommerce_wishlist_add_to_wishlist_text', WC_Wishlists_Settings::get_setting('wc_wishlist_button_text', 'Add to wishlist'), $product->product_type)); ?>"></a>

			</div>
			<?php

		endif;


	elseif(is_yith_wishlist_supported()):

		oxygen_yith_wcwl_add_to_wishlist();

	endif;
	?>


	<div id="main-image-slider">
	<?php
		if ( has_post_thumbnail() || count($attachment_ids) ) {

			# start: modified by Arlind Nushi
			if(has_post_thumbnail())
				$shown_id = get_post_thumbnail_id();
			# end: modified by Arlind Nushi

			if(has_post_thumbnail())
			{
				$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
			}
			else
			{
				$first_img = reset($attachment_ids);

				$image_title = esc_attr( get_the_title( $first_img ) );
				$image_link  = wp_get_attachment_url( $first_img );
			}

			#$image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop-thumb-4' ));

			# start: modified by Arlind Nushi
				#$image = laborator_show_thumbnail($post->ID, apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4'));
			if(has_post_thumbnail())
				$image = remove_wh(wp_get_attachment_image(get_post_thumbnail_id(), apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4')));
			else
				$image = remove_wh(wp_get_attachment_image($first_img, apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4')));
				#$image = laborator_show_img($image_link, 'shop-thumb-4');

			$zoom = get_data('shop_single_fullscreen') ? '<span class="zoom-image"><i class="glyphicon glyphicon-fullscreen"></i></span>' : '';
			# end: modified by Arlind Nushi

			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" data-lightbox-gallery="main-images">%s %s</a>', $image_link, $image, $zoom ), $post->ID );


			if ( $attachment_ids )
			{
				if( ! has_post_thumbnail())
					array_shift($attachment_ids);

				$attachment_ids = array_diff($attachment_ids, array(get_post_thumbnail_id()));

				foreach ( $attachment_ids as $attachment_id )
				{
					$image_title = esc_attr( get_the_title( $attachment_id ) );
					$image_link = wp_get_attachment_url( $attachment_id );

					if ( ! $image_link )
						continue;

					#$image = laborator_show_img($image_link, apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4'));
					$image = remove_wh(wp_get_attachment_image($attachment_id, apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4')));
					$image = preg_replace('/alt="'.apply_filters('oxygen_shop_single_thumb', 'shop-thumb-4').'"/i', 'alt="'.esc_attr($image_title).'"', $image);

					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom hidden" data-lightbox-gallery="main-images">%s %s</a>', $image_link, $image, $zoom ), $post->ID );
				}
			}

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );

		}
	?>
	</div>

	<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div>
