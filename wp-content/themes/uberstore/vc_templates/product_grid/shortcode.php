<?php function thb_productgrid( $atts, $content = null ) {
	extract(shortcode_atts(array(
	  	'item_count' => '8',
	  	'cat' => ''
	), $atts));
	
	global $woocommerce, $woocommerce_loop;
	
	$args = array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'product_cat' => $cat,
		'posts_per_page' => $item_count
	);	    
	$products = new WP_Query( $args );
	
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', '3' ); 
 	
 	ob_start();
 	$i = 1;

	if ( $products->have_posts() ) { ?>
	   
		<div class="products row packery">
		
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<?php
				global $product, $post;
				$font;
				switch($i) {
					case 1:
					case 13:
						$imagesize=array("570","600");
						$font = 'large';
						$articlesize = 'six';
						break;
					case 2:
					case 4:
					case 5:
					case 6:
					case 9:
					case 8:
					case 10:
					case 11:
					case 14:
					case 15:
						$imagesize=array("270","285");
						$font = 'small';
						$articlesize = 'three';
						break;
					case 3:
					case 7:
					case 12:
						$imagesize=array("270","600");
						$articlesize = 'three';
						$font = 'medium';
						break;
				} ?>
				<article itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class($articlesize . ' columns'); ?>>
					<?php if ( has_post_thumbnail() ) {
					
							$image_id = get_post_thumbnail_id();
							$image_link = wp_get_attachment_image_src($image_id,'full');
							$image = aq_resize( $image_link[0], $imagesize[0], $imagesize[1], true, false);
						
						}
					?>
					<figure>
						<img  src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
						<div class="overlay">
							<div class="post-title<?php if($font) { echo ' '.$font; } ?>">	
							<?php
								$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
								echo $product->get_categories( ', ', '<aside class="post_categories">' . _n( '', '', $size, THB_THEME_NAME ) . ' ', '</aside>' );
							?>
							<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
							</div>
						</div>
					</figure>
				</article>
				
			<?php $i++; endwhile; // end of the loop. ?>
		 
		</div>
		
	   
	<?php }
	     
   $out = ob_get_contents();
   if (ob_get_contents()) ob_end_clean();
   
   wp_reset_query();
   wp_reset_postdata();
	   
  return $out;
}
add_shortcode('thb_productgrid', 'thb_productgrid');
