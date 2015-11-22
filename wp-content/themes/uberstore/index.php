<?php get_header(); ?>
<?php 
 		$id = $wp_query->get_queried_object_id();
 		$sidebar_pos = get_post_meta($id, 'sidebar_position', true);
?>
<div class="row">
<section class="eight columns blog-section<?php if ($sidebar_pos == 'left')  { echo ' push-four'; } ?>">
  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
	  <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
	  	
	    <?php
	      // The following determines what the post format is and shows the correct file accordingly
	      $format = get_post_format();
	      if ($format) {
	      get_template_part( 'inc/postformats/'.$format );
	      } else {
	      get_template_part( 'inc/postformats/standard' );
	      }
	    ?>
	    <?php if( !in_array($format, array('quote', 'link'))) { ?>
	    	<?php get_template_part( 'inc/postformats/post-meta' ); ?>
		    <div class="post-title">
		    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		    </div>
		    <div class="post-content">
		    	<?php the_content('Read More'); ?>
		    </div>
	  	<?php } ?>
	  </article>
  <?php endwhile; ?>
      <?php theme_pagination(); ?>
  <?php else : ?>
    <p><?php _e( 'Please add posts from your WordPress admin page.', THB_THEME_NAME ); ?></p>
  <?php endif; ?>
</section>
  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>