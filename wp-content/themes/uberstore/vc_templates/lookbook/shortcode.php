<?php function thb_lookbook( $atts, $content = null ) {
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
 	?>
 	<?php
	if ( $products->have_posts() ) { ?>
		<div class="carousel-container">
			<div class="carousel lookbook products row">				
				<?php if($content) { ?>
					<article <?php post_class('columns'); ?>>
						<?php echo wpautop(do_shortcode($content)); ?>
					</article>
				<?php } ?>
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
			
					<?php woocommerce_get_template_part( 'content', 'product-lookbook' ); ?>
			
				<?php endwhile; // end of the loop. ?>	 
									
			</div>
		</div>
		
		<div class="carousel-container row">
			<div class="carousel lookbook-thumbnails row">
				<?php if($content) { ?>
					<div class="one columns">
						<figure></figure>
					</div>
				<?php } ?>
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
			
					<?php $image_id = get_post_thumbnail_id();
					$image_link = wp_get_attachment_image_src($image_id,'full');
					$image = aq_resize( $image_link[0], 100, 100, true, false);?>
					<div class="one columns"><img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" /></div>
				<?php endwhile; // end of the loop. ?>	 			
			</div>
		</div>
	<?php } else { ?>
	No products found.
	
	<?php }
   $out = ob_get_contents();
   if (ob_get_contents()) ob_end_clean();
   
   wp_reset_query();
   wp_reset_postdata();
   remove_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
	   
  return $out;
}
add_shortcode('thb_lookbook', 'thb_lookbook');
