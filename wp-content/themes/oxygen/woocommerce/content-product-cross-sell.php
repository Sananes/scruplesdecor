<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $product, $post;

?>
<div class="product-entry">
	
	<a href="<?php the_permalink(); ?>" class="thumb">
	<?php
	
		if(has_post_thumbnail()):
			
			echo laborator_show_thumbnail($id, 'shop-thumb-3');
			
		else:
		
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );
			
		endif;
	?>
	</a>
	
	<div class="product-info">
		<h3>
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</h3>
		
		<div class="price">
			<?php echo $product->get_price_html(); ?>
		</div>
	</div>
</div>