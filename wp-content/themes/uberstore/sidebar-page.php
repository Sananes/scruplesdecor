<?php $id = $wp_query->get_queried_object_id();
			$sidebar = get_post_meta($id, 'sidebar_set', true);
			$sidebar_pos = get_post_meta($id, 'sidebar_position', true); ?>
<aside class="sidebar three columns<?php if ($sidebar_pos == 'left') { echo ' pull-nine'; }?><?php if (is_page_template('template-portfolio.php') || is_page_template('template-portfolio-paginated.php')) { echo ' no-border'; } ?>">
	<?php 
	
		##############################################################################
		# Display the asigned sidebar
		##############################################################################

	?>
	<?php 
   	if (is_page()) {
   		$sidebar = get_post_meta($post->ID, 'sidebar_set', true);
   		if(is_active_sidebar($sidebar)) {
   			dynamic_sidebar($sidebar);
   		}
   	} else {
   		dynamic_sidebar('blog');
   	}
   	?>
</aside>