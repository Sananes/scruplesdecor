<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $post, $product;

$item_preview_type = get_data('shop_item_preview_type');

$post_cloned    = $post;
$product_images = $product->get_gallery_attachment_ids();
$post           = $post_cloned;


# Primary Thumbnail
echo woocommerce_get_product_thumbnail('shop-thumb');

# Remove Duplicate Images
if(has_post_thumbnail())
{
	$post_thumb_id = get_post_thumbnail_id();

	foreach($product_images as $i => $attachment_id)
	{
		if($post_thumb_id == $attachment_id  || ! wp_get_attachment_url($attachment_id))
		{
			unset($product_images[$i]);
		}
	}
}

# Other Thumbnails
if(count($product_images) && $item_preview_type != 'none'):

	if(in_array($item_preview_type, array('fade', 'slide'))):

		$attachment_id = reset($product_images);

		if($attachment = wp_get_attachment_image_src($attachment_id, 'shop-thumb'))
		{
			$image_url = $attachment[0];

			?>
			<img class="shop-image lazy-load-shop-image" data-src="<?php echo $image_url; ?>" />
			<?php
		}

	endif;


	if($item_preview_type == 'gallery'):

		foreach($product_images as $attachment_id)
		{
			if($attachment = wp_get_attachment_image_src($attachment_id, 'shop-thumb'))
			{
				$image_url = $attachment[0];

				?>
				<img class="shop-image lazy-load-shop-image" data-src="<?php echo $image_url; ?>" />
				<?php
			}
		}

	endif;

endif;