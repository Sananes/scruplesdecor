<?php get_header(); ?>
<div class="row">
<section class="eight columns blog-section">
  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
	  <article <?php post_class('post blog-post'); ?> id="post-<?php the_ID(); ?>">
	    <?php
	      // The following determines what the post format is and shows the correct file accordingly
	      $format = get_post_format();
	      if ($format) {
	      get_template_part( 'inc/postformats/'.$format );
	      } else {
	      get_template_part( 'inc/postformats/standard' );
	      }
	    ?>
	    <?php get_template_part( 'inc/postformats/post-meta' ); ?>
	    <div class="post-title">
	    	<h2><?php the_title(); ?></h2>
	    </div>
	    <div class="post-content">
	    	<?php the_content(); ?>
	    	<?php if ( is_single()) { wp_link_pages(); } ?>
	    </div>
	  </article>
  <?php endwhile; else : endif; ?>
  <?php get_template_part( 'inc/postformats/post-prevnext' ); ?>
  <!-- Start #comments -->
  <section id="comments" class="cf">
    <?php comments_template('', true ); ?>
  </section>
  <!-- End #comments -->
</section>
  <?php get_sidebar('single'); ?>
</div>
<?php get_footer(); ?>