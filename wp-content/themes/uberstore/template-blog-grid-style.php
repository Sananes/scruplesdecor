<?php
/*
Template Name: Blog - Grid Style
*/
?>
<?php get_header(); ?>
<div class="row">
<section class="twelve columns grid-style">
	<?php $i = 0; $counter = range(0, 200, 3); ?>
		
	  <?php 
	  $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	  $args = array('offset'=> 0, 'paged'=>$paged);
	  $all_posts = new WP_Query($args);
	  if (have_posts()) :  while($all_posts->have_posts()) : $all_posts->the_post();?>
	  
	  	<?php if ($i % 3 == 0) { echo '<div class="row" data-equal=".post">'; } ?>
	  	
		  <article <?php post_class('four columns post'); ?> id="post-<?php the_ID(); ?>">
		    <?php get_template_part( 'inc/postformats/grid-style' ); ?>
		    <div class="post-title">
		    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		    </div>
		    <div class="post-content">
		    	<?php echo ShortenText(get_the_content(), 200); ?>
		    </div>
		  	<?php get_template_part( 'inc/postformats/post-meta' ); ?>
		  </article>
		  
		  <?php if (in_array($i + 1, $counter)){ echo '</div>'; }   ?>
		  
	  <?php $i++; endwhile; ?>
	  	
	  	<div class="twelve columns">
	      <?php theme_pagination($all_posts->max_num_pages, 1, true); ?>
	    </div>
	  <?php else : ?>
	    <p><?php _e( 'Please add posts from your WordPress admin page.', THB_THEME_NAME ); ?></p>
	  <?php endif; ?>
</section>
</div>
<?php get_footer(); ?>