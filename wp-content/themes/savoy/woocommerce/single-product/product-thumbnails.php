<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

if ( $attachment_ids ) {
	?>
	<div class="thumbnails">
        <div id="nm-product-thumbnails-slider"><?php
            
            // Featured image
            if ( has_post_thumbnail() ) {
                $loop = 1;
                $image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
    
                echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="current">%s</div>', $image ), $post->ID );
            } else {
                $loop = 0;
            }
            
            // Gallery images
            foreach ( $attachment_ids as $attachment_id ) {
    		
                $loop++;
                
                $image_link = wp_get_attachment_url( $attachment_id );
                
                if ( ! $image_link ) {
                    continue;
				}
                
                $active_class = ( $loop == 1 ) ? ' class="current"' : '';
                $image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
                    
                echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div%s>%s</div>', $active_class, $image ), $attachment_id, $post->ID );
                
            }

		?></div>
    </div>
	<?php
}
