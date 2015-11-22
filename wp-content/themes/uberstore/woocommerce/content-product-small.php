<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $product, $woocommerce_loop;

$attachment_ids = $product->get_gallery_attachment_ids();

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibilty
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

?>


    <article <?php post_class("four mobile-two columns"); ?>>

<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<figure<?php if( ot_get_option('product_hover') == 'fade'){ echo ' class="fade"'; }?>>
	
		<?php
			$image_html = "";
			
			if (thb_out_of_stock()) {
				echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
			} else if ( $product->is_on_sale() ) {
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale', THB_THEME_NAME ).'</span>', $post, $product);
			}
			
			
	
			if ( has_post_thumbnail() ) {
				$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
			}
		?>
		
		<a href="<?php the_permalink(); ?>">
			
			<?php
				$attachment_ids = $product->get_gallery_attachment_ids();
				
				$img_count = 0;
				
				if ($attachment_ids) {
					
					echo '<div class="product-image">'.$image_html.'</div>';	
					
					foreach ( $attachment_ids as $attachment_id ) {
						
						if ( get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) )
							continue;
						
						echo '<div class="product-image">'.wp_get_attachment_image( $attachment_id, 'shop_catalog' ).'</div>';	
						
						$img_count++;
						
						if ($img_count == 1) break;
			
					}
								
				} else {
				
					echo '<div class="product-image">'.$image_html.'</div>';					
					echo '<div class="product-image">'.$image_html.'</div>';
					
				}
			?>			
		</a>
		<div class="quick-view" data-id="<?php echo $post->ID; ?>"><i class="fa fa-search"></i></div>
	</figure>
	
	<div class="post-title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
	</div>
</article><!-- end product -->
