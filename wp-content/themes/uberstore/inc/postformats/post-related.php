<!-- Start Related Posts -->
<?php global $post; 
      $postId = $post->ID;
      $format = get_post_meta($postId, 'portfolio_type', true); 
      $layout = get_post_meta($postId, 'portfolio_layout', true); 
      $type = get_post_meta($postId, 'portfolio_type', true);
      
      if (is_singular('post')) {
      	$query = get_blog_posts_related_by_category($postId); 
      } elseif (is_singular('portfolio')) {
      	$query = get_posts_related_by_taxonomy($postId, 'project-category');
      }
      if ($layout == 'layout2') {
      	$column = "four";
      } else {
       	$column = "twelve";
      } 
?>
<?php if ($query->have_posts()) : ?>
<aside class="related">
	<h2><?php _e( 'Other Works', THB_THEME_NAME ); ?></h2>
	<div class="row relatedposts hide-on-print">
	  <?php while ($query->have_posts()) : $query->the_post(); ?>             
	    <div class="<?php echo $column; ?> columns">
	      <article class="post" id="post-<?php the_ID(); ?>">
	        <figure class="post-gallery fresco">
	        			<?php
	        					$type = get_post_meta($post->ID, 'portfolio_type', true);
	        			    $image_id = get_post_thumbnail_id();
	        			    $image_url = wp_get_attachment_image_src($image_id,'full'); $image_url = $image_url[0];
	        			    $image_title = esc_attr( get_the_title($post->ID) );
	        			?>
	        			<?php $image = aq_resize( $image_url, 270, 190, true, false); ?>
	        			<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" />
	            	<?php switch($type) {
	            	
	            		case "link": ?>
	            			<?php $link = get_post_meta($post->ID, 'portfolio_link', TRUE); ?>
	              		<div class="overlay">
	              			<div class="buttons"><a href="<?php echo $link; ?>" class="details" target="blank"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
	              		</div>
	            		<?php break;
	            		
	            		case "image": ?>
	            			<div class="overlay">
	            				<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
	            				<a href="<?php echo $image_url; ?>" class="zoom" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
	            			</div>
	            		<?php break;
	            		
	            		case "gallery": ?>
	            			<div class="overlay">
	            				<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a></div>
	            			</div>
	            		<?php break;
	            		
	            		case "video": ?>
	            			<?php $video_url = get_post_meta($post->ID, 'portfolio_video', TRUE); ?>
	            			<div class="overlay">
	            				<div class="buttons"><a href="<?php the_permalink(); ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
	            				<a href="<?php echo $video_url; ?>" class="zoom video" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
	            			</div>
	            		<?php break;
	            	}?>
	            	
	        </figure>    
	      </article>
	    </div>
	    <?php endwhile; ?>
	</div>
</aside>
<?php endif; ?>
<?php wp_reset_query(); ?>
<!-- End Related Posts -->