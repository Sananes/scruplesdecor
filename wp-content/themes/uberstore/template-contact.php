<?php
/*
Template Name: Contact
*/
?>
<?php get_header(); ?>

<div class="row">
<section class="twelve columns">
  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
  	<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
  			<div id="contact-map" class="google_map twelve columns" data-map-zoom="<?php echo ot_get_option('contact_zoom', 17); ?>" data-map-center-lat="<?php echo ot_get_option('map_center_lat', '59.93815'); ?>" data-map-center-long="<?php echo ot_get_option('map_center_long', '10.76537'); ?>" data-pin-info="<?php echo ot_get_option('map_pin_info'); ?>" data-pin-image="<?php echo ot_get_option('map_pin_image', THB_THEME_ROOT. '/assets/img/pin.png'); ?>"></div>
  	  <div class="post-content">
  	    <?php the_content(); ?>
  	  </div>
  	</article>
  <?php endwhile; else : endif; ?>
</section>
</div>
<?php get_footer(); ?>