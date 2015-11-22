<?php get_header(); ?>
<?php $home = get_home_url(); ?>
<div class="row">
<figure class="six columns notfoundimage">
	<img src="<?php echo THB_THEME_ROOT; ?>/assets/img/404.jpg" alt="404" />
</figure>
<section class="six columns notfound">
	<div class="content404">
	  <h4><?php _e( "We are sorry, <br> but the page you're <br> looking for cannot<br> be found.", THB_THEME_NAME ); ?></h4>
	  
	  <p><?php _e( 'You might try searching our site or visit the <strong><a href="'.$home.'">homepage</a></strong>.', THB_THEME_NAME ); ?></p>
	  <?php get_search_form(); ?> 
	  <div class="title"><?php _e( "Follow UberStore", THB_THEME_NAME ); ?></div>
	  <?php if (ot_get_option('fb_link')) { ?>
	  <a href="<?php echo ot_get_option('fb_link'); ?>" class="facebook boxed-icon icon-1x"><i class="fa fa-facebook"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('pinterest_link')) { ?>
	  <a href="<?php echo ot_get_option('pinterest_link'); ?>" class="pinterest boxed-icon icon-1x"><i class="fa fa-pinterest"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('twitter_link')) { ?>
	  <a href="<?php echo ot_get_option('twitter_link'); ?>" class="twitter boxed-icon icon-1x"><i class="fa fa-twitter"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('googleplus_link')) { ?>
	  <a href="<?php echo ot_get_option('googleplus_link'); ?>" class="google-plus boxed-icon icon-1x"><i class="fa fa-google-plus"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('linkedin_link')) { ?>
	  <a href="<?php echo ot_get_option('linkedin_link'); ?>" class="linkedin boxed-icon icon-1x"><i class="fa fa-linkedin"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('instragram_link')) { ?>
	  <a href="<?php echo ot_get_option('instragram_link'); ?>" class="instagram boxed-icon icon-1x"><i class="fa fa-instagram"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('xing_link')) { ?>
	  <a href="<?php echo ot_get_option('xing_link'); ?>" class="xing boxed-icon icon-1x"><i class="fa fa-xing"></i></a>
	  <?php } ?>
	  <?php if (ot_get_option('tumblr_link')) { ?>
	  <a href="<?php echo ot_get_option('tumblr_link'); ?>" class="tumblr boxed-icon icon-1x"><i class="fa fa-tumblr"></i></a>
	  <?php } ?>
  </div>
</section>
</div>
<?php get_footer(); ?>