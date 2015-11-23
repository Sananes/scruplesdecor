<?php
/**
 *	Quickview Product Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$attachment_ids = $product->get_gallery_attachment_ids();

$slider_disabled_class = ( count( $attachment_ids ) == 0 ) ? ' nm-carousel-disabled' : ' slick-slider slick-arrows-small';
?>

<div class="images">
    <?php //echo woocommerce_show_product_sale_flash(); ?>    
    <div id="nm-quickview-slider" class="product-images <?php echo $slider_disabled_class; ?>">
        <?php
            // Featured image
			if ( has_post_thumbnail() ) {
        		//$image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'nm_quick_view' ) );
				$image = get_the_post_thumbnail( $post->ID, 'nm_quick_view' );
				
				echo apply_filters( 'woocommerce_quickview_single_product_image_html', '<div>' . $image . '</div>', $post->ID );
            } else {
				echo apply_filters( 'woocommerce_quickview_single_product_image_html', sprintf( '<div><img src="%s" alt="%s" /></div>', wc_placeholder_img_src(), esc_attr__( 'Placeholder', 'woocommerce' ) ), $post->ID );
            }
			
			// Gallery images        
            if ( $attachment_ids ) {
                foreach ( $attachment_ids as $attachment_id ) {
					$image_link = wp_get_attachment_url( $attachment_id );
                    
					if ( ! $image_link ) {
						continue;
					}
					
					$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'nm_quick_view' ) );
        
					echo apply_filters( 'woocommerce_quickview_single_product_image_html', '<div>' . $image .'</div>', $post->ID );
                }
            }
        ?>
    </div>
</div>
