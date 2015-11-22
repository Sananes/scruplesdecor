<?php function thb_portfolio( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'carousel' => 'no',
       	'item_count' => '9',
       	'columns' => '4',
       	'categories' => false
    ), $atts));
    
	$args = array(
		'showposts' => $item_count, 
		'nopaging' => 0, 
		'post_type'=>'portfolio', 
		'post_status' => 'publish', 
		'ignore_sticky_posts' => 1,
		'no_found_rows' => true,
		'tax_query' => array(
				array(
		    'taxonomy' => 'project-category',
		    'field' => 'id',
		    'terms' => explode(',',$categories),
		    'operator' => 'IN'
		   )
		 ) 
	);
	
	$posts = new WP_Query( $args );
 	
 	ob_start();
 	
	if ( $posts->have_posts() ) { ?>
	  <?php switch($columns) {
	  	case 2:
	  		$col = 'six';
	  		$w = '570';
	  		break;
	  	case 3:
	  		$col = 'four';
	  		$w = '370';
	  		break;
	  	case 4:
	  		$col = 'three';
	  		$w = '270';
	  		break;
	  } ?>
		<?php if ($carousel == "yes") { ?>
			
			<div class="carousel-container thbportfolio">
				<div class="carousel owl row" data-columns="<?php echo $columns; ?>" data-navigation="true">				
					
					<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
						<?php 
						$id = get_the_ID();
						$image_id = get_post_thumbnail_id();
						$image_link = wp_get_attachment_image_src($image_id,'full');
						$image = aq_resize( $image_link[0], $w, 225, true, false);
						$image_title = esc_attr( get_the_title($id) );
						$type = get_post_meta($id, 'portfolio_type', true);
						$meta = get_the_term_list( $id, 'project-category', '<span>', '</span>, <span>', '</span>' ); 
						$meta = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $meta);
						?>
						<article <?php post_class('post '.$col.' columns'); ?> id="post-<?php the_ID(); ?>">
							<figure class="post-gallery fresco">
								<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" title="<?php echo $image_title; ?>" />
								<?php 
										if ($type == "video") {
											$video_url = get_post_meta($id, 'portfolio_video', TRUE);
										}
								?>
								<?php switch($type) {
								
									case "link": ?>
										<?php $link = get_post_meta($id, 'portfolio_link', TRUE); ?>
										<div class="overlay">
											<div class="buttons"><a href="<?php echo $link; ?>" class="details" target="blank"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
										</div>
									<?php break;
									
									case "image":
									case "standard": ?>
										<div class="overlay">
											<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
											<a href="<?php echo $image_link[0]; ?>" class="zoom" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
										</div>
									<?php break;
									
									case "gallery": ?>
										<div class="overlay">
											<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a></div>
										</div>
									<?php break;
									
									case "video": ?>
										<div class="overlay">
											<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
											<a href="<?php echo $video_url; ?>" class="zoom video" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
										</div>
									<?php break;
								}?>
								
							</figure>
							<div class="post-title">
								<aside class="post_categories"><?php echo $meta; ?></aside>
								<h4><?php if ($type != 'link') { ?><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a><?php } else { the_title(); } ?></h4>
							</div>
						</article>
					<?php endwhile; // end of the loop. ?>	 
										
				</div>
			</div>
			
		<?php } else {  ?> 
		<div class="row thbportfolio" data-equal="article">
		
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<?php 
				$id = get_the_ID();
				$image_id = get_post_thumbnail_id();
				$image_link = wp_get_attachment_image_src($image_id,'full');
				$image = aq_resize( $image_link[0], $w, 225, true, false);
				$image_title = esc_attr( get_the_title($id) );
				$type = get_post_meta($id, 'portfolio_type', true);
				$meta = get_the_term_list( $id, 'project-category', '<span>', '</span>, <span>', '</span>' ); 
				$meta = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $meta);
				?>
				<article <?php post_class('post mobile-two '.$col.' columns'); ?> id="post-<?php the_ID(); ?>">
					<figure class="post-gallery fresco">
						<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" title="<?php echo $image_title; ?>" />
						<?php 
								if ($type == "video") {
									$video_url = get_post_meta($id, 'portfolio_video', TRUE);
								}
						?>
						<?php switch($type) {
						
							case "link": ?>
								<?php $link = get_post_meta($id, 'portfolio_link', TRUE); ?>
								<div class="overlay">
									<div class="buttons"><a href="<?php echo $link; ?>" class="details" target="blank"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
								</div>
							<?php break;
							
							case "image":
							case "standard": ?>
								<div class="overlay">
									<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
									<a href="<?php echo $image_link[0]; ?>" class="zoom" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
								</div>
							<?php break;
							
							case "gallery": ?>
								<div class="overlay">
									<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a></div>
								</div>
							<?php break;
							
							case "video": ?>
								<div class="overlay">
									<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
									<a href="<?php echo $video_url; ?>" class="zoom video" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
								</div>
							<?php break;
						}?>
						
					</figure>
					<div class="post-title">
						<aside class="post_categories"><?php echo $meta; ?></aside>
						<h4><?php if ($type != 'link') { ?><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a><?php } else { the_title(); } ?></h4>
					</div>
				</article>
			<?php endwhile; // end of the loop. ?>
		 
		</div>
		
		<?php } ?>
	   
	<?php }

   $out = ob_get_contents();
   if (ob_get_contents()) ob_end_clean();
   
   wp_reset_query();
   wp_reset_postdata();
     
  return $out;
}
add_shortcode('thb_portfolio', 'thb_portfolio');
