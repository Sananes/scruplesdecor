<?php
 
	global $post, $product, $woocommerce;
	$attachment_ids = $product->get_gallery_attachment_ids();
?>

<article itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class('post product-page'); ?>>
	<div class="product_nav">
		<?php be_previous_post_link( '%link', '<i class="fa fa-angle-left"></i>', true,'', 'product_cat' ); ?>
		<?php be_next_post_link( '%link', '<i class="fa fa-angle-right"></i>', true,'', 'product_cat' ); ?>
	</div>
	<div class="row">      
	  <div class="six columns">        
	  	<div class="product-images">
	  		<div id="lightbox-images" class="flexslider">
					<?php if (thb_out_of_stock()) {
						echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
					} else if ( $product->is_on_sale() ) {
						echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale', THB_THEME_NAME ).'</span>', $post, $product);
					} ?>
					<ul class="slides">
						<?php if ( has_post_thumbnail() ) : ?>
				        	
							<?php
								$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), false, '' );
								$src_small = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'shop_single');
								$image_title = esc_attr( get_the_title( $post->ID ) );
							?>
	         
	            <li itemprop="image" class="easyzoom">
	            	<img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" />
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
											<img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" />
										</li>
									
									<?php
								}
							}
						?>
					</ul>
				</div>
	  	</div><!-- end product images -->
	  </div>
	  <div class="six columns product-information">
	  	<header class="post-title">
	  		<h1 itemprop="name" class="entry-title"><?php the_title(); ?></h1>
	  	</header>
	  	<?php woocommerce_template_single_price(); ?>
	  	<?php woocommerce_template_single_excerpt(); ?>
	  	<?php woocommerce_template_single_add_to_cart(); ?>
	  </div>
	</div><!-- end row -->
</article><!-- #product-<?php the_ID(); ?> -->