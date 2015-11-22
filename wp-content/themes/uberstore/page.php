<?php get_header(); ?>
<?php 
 	if (is_page()) {
 		$id = $wp_query->get_queried_object_id();
 		$display_breadcrumbs = get_post_meta($id, 'display_breadcrumbs', true); 
 		$sidebar = get_post_meta($id, 'sidebar_set', true);
 		$sidebar_pos = get_post_meta($id, 'sidebar_position', true);
 	}
?>
<?php if($post->post_content != "") { ?>
<div class="row <?php if($sidebar && $display_breadcrumbs == 'off') { echo 'pagepadding';} ?>">
	<section class="<?php if($sidebar) { echo 'content-padding nine';} else { echo 'twelve'; } ?> columns <?php if ($sidebar && ($sidebar_pos == 'left'))  { echo 'push-three'; } ?>">
	  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		  <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
		    <div class="post-content">
		    	<?php the_content('Read More'); ?>
		    </div>
		  </article>
	  <?php endwhile; else : endif; ?>
	</section>
	<?php if($sidebar) { get_sidebar('page'); } ?>
</div>
<?php } ?>
<?php get_footer(); ?>