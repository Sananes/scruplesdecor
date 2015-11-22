<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;
$attachment_ids = $product->get_gallery_attachment_ids();
?>
<?php $product_zoom = get_post_meta($post->ID, 'product_zoom', true);  
			$product_zoom = ($product_zoom == 'on' ? 'true' : 'false'); 
			if( $product->has_child() && $product->is_type( 'variable' )) { 
				$available_variations = $product->get_available_variations();
			}
?>
<div class="product-images">
	<div id="product-images" class="flexslider" data-zoom="<?php echo $product_zoom; ?>">
				<?php if (thb_out_of_stock()) {
					echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
				} else if ( $product->is_on_sale() ) {
					echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale', THB_THEME_NAME ).'</span>', $post, $product);
				} ?>
				<ul class="slides" rel="gallery">
					<?php if ( has_post_thumbnail() ) : ?>
			        	
						<?php
							$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), false, '' );
							$src_small = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'shop_single');
							$image_title = esc_attr( get_the_title( $post->ID ) );
						?>
         
            <li itemprop="image" class="easyzoom">
            	<a href="<?php echo $src[0]; ?>" itemprop="image"><img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" /></a>
            </li>
					
					<?php endif; ?>	
			            
					<?php if ( $attachment_ids ) {						
							
							foreach ( $attachment_ids as $attachment_id ) {
					
								$image_link = wp_get_attachment_url( $attachment_id );
								
								$src = wp_get_attachment_image_src( $attachment_id, false, '' );
								$src_small = wp_get_attachment_image_src( $attachment_id,  apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
								
								$image_title = esc_attr( get_the_title( $attachment_id ) );
								?>
									<li itemprop="image" class="easyzoom">
										<a href="<?php echo $src[0]; ?>" itemprop="image"><img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" /></a>
									</li>
								
								<?php
							}
						}
					?>
				</ul>
			</div>
	<div id="product-nav" class="flexslider">
		<ul class="slides">
        <?php if ( has_post_thumbnail() ) : 
        	$var_img = false;
        	$image_id = get_post_thumbnail_id();
        	$image_link = wp_get_attachment_image_src($image_id,'full');
        	$image = aq_resize( $image_link[0], 106, 117, true, false);
        	$image_title = esc_attr( get_the_title($image_id) );
        	if (isset($available_variations)) {
        		foreach($available_variations as $prod_variation) {
        		  if ($image_link[0] == $prod_variation['image_link']) {
        		  	$var_img = $prod_variation['attributes']['attribute_pa_color'];
        		  }
        		}
        	}
        ?>
        	<li><img  src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" title="<?php echo $image_title; ?>" data-variation-color="<?php echo $var_img; ?>" /></li>
        <?php endif; ?>
        
        <?php if ( $attachment_ids ) { foreach ( $attachment_ids as $attachment_id ) {
        	$var_img = false;
        	$image_link = wp_get_attachment_image_src($attachment_id,'full');
        	
        	$image = aq_resize( $image_link[0], 106, 117, true, false);
        	$image_title = esc_attr( get_the_title($attachment_id) );
        	
        	if (isset($available_variations)) {
        		foreach($available_variations as $prod_variation) {
        		  if ($image_link[0] == $prod_variation['image_link']) {
        		  	$var_img = $prod_variation['attributes']['attribute_pa_color'] ? $prod_variation['attributes']['attribute_pa_color'] : '';
        		  }
        		}
        	}
        ?>
					<li><img  src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" title="<?php echo $image_title; ?>" data-variation-color="<?php echo $var_img; ?>" /></li>
				<?php } } ?>
		</ul>
	</div>
</div><!-- end product images -->