<?php
/*
Template Name: Portfolio - Ajax
*/
?>
<?php get_header(); ?>
<?php 
 	if (is_page()) {
 		$id = $wp_query->get_queried_object_id();
 		$sidebar = get_post_meta($id, 'sidebar_set', true);
 		$sidebar_pos = get_post_meta($id, 'sidebar_position', true);
 	}
?>
<div class="row">
<section class="portfolio-container <?php if($sidebar) { echo 'nine';} else { echo 'twelve'; } ?> columns <?php if ($sidebar && ($sidebar_pos == 'left'))  { echo 'push-three'; } ?>">
	<div class="row">
		<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<?php $cols = get_post_meta($post->ID, 'portfolio_columns', TRUE); ?>
		<?php $catorg = get_post_meta($post->ID, 'portfolio_categories', TRUE); ?>
		<?php $cat = implode(',', $catorg); ?>
		<div class="twelve columns">
			<ul class="filters hide-for-small">
			  <li><a href="#" data-filter="*" class="active"><?php _e( 'show all', THB_THEME_NAME ); ?></a></li>
			  <?php 
				$portfolio_categories = get_categories(array('taxonomy'=>'project-category', 'include' => $cat));
				foreach($portfolio_categories as $portfolio_category) {
					$args = array(
					    'post_type' => 'portfolio',
					    'post_status' => 'published',
					    'project-category' => $portfolio_category->slug,
					    'numberposts' => -1
					);
					$num = count(get_posts($args));
					
					echo '<li><a href="#" data-filter=".' . $portfolio_category->slug . '">' . $portfolio_category->name . ' <span>('.$num.')</span></a></li>';
					
				}
				?>
			</ul>

			<div id="portfolioselect" class="show-for-small">
				<a href="#" id="sortportfolio"><?php _e( 'Sort By:', THB_THEME_NAME ); ?></a>
				<ul>
				   <li><a href="#" data-filter="*" class="active"><?php echo __('All', THB_THEME_NAME); ?></a></li>
			     <?php 
			     $portfolio_categories = get_categories(array('taxonomy'=>'project-category', 'include' => $cat));
			     foreach($portfolio_categories as $portfolio_category) {
			     	$args = array(
			     	    'post_type' => 'portfolio',
			     	    'post_status' => 'published',
			     	    'project-category' => $portfolio_category->slug,
			     	    'numberposts' => -1
			     	);
			     	$num = count(get_posts($args));
			     	
			     	echo '<li><a href="#" data-filter=".'.$portfolio_category->slug.'">' .$portfolio_category->name. ' <span>('.$num.')</span></a></li>';
			     }
			     ?>
				</ul>
			</div>
		</div>
		<?php endwhile; else : endif; ?> 
	</div>
	<?php if ($cols == 'three') {
					$columns = '4';
				} else if ($cols == 'four') {
					$columns = '3';
				} else if ($cols == 'six') {
					$columns = '2';
				} ?>	
	<div class="thbportfolio ajax row" data-columns="<?php echo $columns; ?>">
		<?php $args = array(
  	   'post_type' => 'portfolio',
  	   'orderby'=>'menu_order',
  	   'order'     => 'ASC',
  	   'posts_per_page' => '-1',
  	   'tax_query' => array(
  	   		array(
           'taxonomy' => 'project-category',
           'field' => 'id',
           'terms' => array_values($catorg),
           'operator' => 'IN'
  	      )
  	    ) // end of tax_query
	  	);
		?>
		<?php $query = new WP_Query($args); ?>
            <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
                  $terms = get_the_terms( get_the_ID(), 'project-category' );
                  $type = get_post_meta($post->ID, 'portfolio_type', true);
                  $meta = get_the_term_list( $post->ID, 'project-category', '<span>', '</span>, <span>', '</span>' ); 
                  $meta = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $meta);
                  ?>
            <div class="item <?php echo $cols; ?> columns <?php foreach ($terms as $term) { echo strtolower($term->slug). ' '; } ?>">
	            <article id="post-<?php the_ID(); ?>" class="post cf">
                <figure class="post-gallery fresco">
                	<?php
                	    $image_id = get_post_thumbnail_id();
                	    $image_link = wp_get_attachment_image_src($image_id,'full');
                	    $image_title = esc_attr( get_the_title($post->ID) );
                	?>
                    	<?php if ($cols == 'three') { ?>
                    		<?php $image = aq_resize( $image_link[0], 270, 190, true, false); ?>
                    		<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" />
                    	<?php } else if ($cols == 'four') { ?>
                    		<?php $image = aq_resize( $image_link[0], 370, 260, true, false); ?>
                    		<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" />
                    	<?php } else if ($cols == 'six') { ?>
                    		<?php $image = aq_resize( $image_link[0], 570, 340, true, false); ?>
                    		<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" />
                    	<?php } ?>
                    	<?php 
                    				if ($type == "video") {
	                    				$video_url = get_post_meta($post->ID, 'portfolio_video', TRUE);
                    				}
                    	?>
                    	<?php switch($type) {
                    	
                    		case "link": ?>
                    			<?php $link = get_post_meta($post->ID, 'portfolio_link', TRUE); ?>
	                    		<div class="overlay">
	                    			<div class="buttons"><a href="<?php echo $link; ?>" class="details" target="blank"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
	                    		</div>
                    		<?php break;
                    		
                    		case "image":
                    		case "standard": ?>
                    			<div class="overlay">
                    				<div class="buttons"><a href="<?php the_permalink() ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
                    				<a href="<?php echo $image_link[0]; ?>" class="zoom" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
                    			</div>
                    		<?php break;
                    		
                    		case "gallery": ?>
                    			<div class="overlay">
                    				<div class="buttons"><a href="<?php the_permalink() ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a></div>
                    			</div>
                    		<?php break;
                    		
                    		case "video": ?>
                    			<div class="overlay">
                    				<div class="buttons"><a href="<?php the_permalink() ?>" class="details"><?php _e( 'Details', THB_THEME_NAME ); ?></a>
                    				<a href="<?php echo $video_url; ?>" class="zoom video" rel="magnific" title="<?php the_title(); ?>"><?php _e( 'View', THB_THEME_NAME ); ?></a></div>
                    			</div>
                    		<?php break;
                    	}?>
                    	
                </figure>
                <div class="post-title cf">
                	<aside class="post_categories"><?php echo $meta; ?></aside>
                	<h4><?php if ($type != 'link') { ?><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a><?php } else { the_title(); } ?></h4>
                	
                </div> 
	            </article>
            </div>
            <?php endwhile; else : ?>
            <div class="three columns centered">
            	<p><?php _e('Please select Portfolio Categories for this page', THB_THEME_NAME); ?></p>
            </div>
            <?php endif; ?>
        <?php wp_reset_query(); ?>
	</div>
</section>
<?php if($sidebar) { get_sidebar('page'); } ?>
</div>
<?php get_footer(); ?>