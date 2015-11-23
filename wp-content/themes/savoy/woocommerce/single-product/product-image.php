<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $woocommerce, $product, $nm_page_includes, $nm_theme_options, $nm_globals;

$nm_page_includes['product-gallery'] = true;

// Image column
$image_column_class = ( isset( $nm_theme_options['product_image_column_size'] ) ) ? 'col-lg-' . $nm_theme_options['product_image_column_size'] . ' col-xs-12' : 'col-lg-6 col-xs-12';

// Featured video
$featured_video_url = get_post_meta( $product->id, 'nm_featured_product_video', true );
$has_featured_video = ( empty( $featured_video_url ) ) ? false : true;
if ( $has_featured_video ) {
	$featured_video_link_class = ' modal-override';
	$image_column_class .= ' has-featured-video';
}

// Image modal gallery
if ( $nm_theme_options['product_image_zoom'] === '1' ) {
	$modal_enabled = true;
	$image_column_class .= ' modal-enabled';
} else {
	$modal_enabled = false;
}

// Image zoom
if ( $nm_globals['product_image_hover_zoom'] ) {
	$zoom_enabled = true;
	$zoom_class = 'easyzoom';
	$image_column_class .= ' zoom-enabled';
} else {
	$zoom_enabled = false;
	$zoom_class = '';
}

?>
<div class="nm-product-thumbnails-col col-xs-1">
	<?php do_action( 'woocommerce_product_thumbnails' ); ?>
</div>

<div id="nm-product-images-col" class="nm-product-images-col <?php echo esc_attr( $image_column_class ); ?>">
    <div class="images">
    	<?php woocommerce_show_product_sale_flash(); ?>
        
        <div id="nm-product-images-slider" class="slick-slider slick-arrows-small">
        <?php
			// Featured image
            if ( has_post_thumbnail() ) {
            
                $image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
                
				if ( $modal_enabled || $zoom_enabled ) {
                    $full_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                    $image_icon = ( $has_featured_video ) ? 'nm-font-media-play' : 'nm-font-plus';
					
					$image_wrap_open = sprintf( '<a href="%s" class="nm-product-image-link zoom" data-size="%sx%s" itemprop="image">', esc_url( $full_image[0] ), intval( $full_image[1] ), intval( $full_image[2] ) );
                    $image_wrap_close = '<i class="nm-product-image-icon nm-font ' . $image_icon . '"></i></a>';
                } else {
                    $image_wrap_open = '';
					$image_wrap_close = ( $has_featured_video ) ? '<i class="nm-product-image-icon nm-font nm-font-media-play"></i>' : '';
                }
                
                echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="%s">%s%s%s</div>', $zoom_class, $image_wrap_open, $image, $image_wrap_close ), $post->ID );
                
            } else {
                
                echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div><img src="%s" alt="%s" /></div>', wc_placeholder_img_src(), esc_attr__( 'Placeholder', 'woocommerce' ) ), $post->ID );
            
            }
            
            // Gallery images
            $attachment_ids = $product->get_gallery_attachment_ids();
            
            if ( $attachment_ids ) {
                foreach ( $attachment_ids as $attachment_id ) {
                    $image_link = wp_get_attachment_url( $attachment_id );
        
                    if ( ! $image_link ) {
						continue;
					}
                            
                    $image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
                    
					if ( $modal_enabled || $zoom_enabled ) {	
                        $full_image = wp_get_attachment_image_src( $attachment_id, 'full' );
                        $image_wrap_open = sprintf( '<a href="%s" class="nm-product-image-link zoom" data-size="%sx%s" itemprop="image">', esc_url( $full_image[0] ), intval( $full_image[1] ), intval( $full_image[2] ) );
						$image_wrap_close = '<i class="nm-product-image-icon nm-font nm-font-plus"></i></a>';
                    } else {
                        $image_wrap_open = '';
						$image_wrap_close = ( $has_featured_video ) ? '<i class="nm-product-image-icon nm-font nm-font-media-play"></i>' : '';
                    }
                    
					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="%s">%s%s%s</div>', $zoom_class, $image_wrap_open, $image, $image_wrap_close ), $post->ID );
                }
                
            }
        ?>
        </div>
        
        <?php if ( $has_featured_video ) : ?>
			<a href="#" id="nm-featured-video-link" class="nm-featured-video-link<?php echo esc_attr( $featured_video_link_class ); ?>" data-mfp-src="<?php echo esc_url( $featured_video_url ); ?>">
            	<span class="nm-featured-video-icon nm-font nm-font-media-play"></span>
                <span class="nm-featured-video-label"><?php esc_html_e( 'Watch Video', 'nm-framework' ); ?></span>
			</a>
		<?php endif; ?>
    </div>
</div>
