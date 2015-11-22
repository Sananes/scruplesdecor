<?php get_header(); ?>
<div class="row">
<section class="twelve columns">
  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
  	<?php $layout = get_post_meta($post->ID, 'portfolio_layout', true); 
  				$format = get_post_meta($post->ID, 'portfolio_type', true); 
  				$portfolio_main = get_post_meta($post->ID, 'portfolio_main', TRUE);
  				$meta = get_the_term_list( $post->ID, 'project-category', '<span>', '</span>, <span>', '</span>' ); 
  				$meta = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $meta); 
  				$attributes = get_post_meta($post->ID, 'portfolio_attributes', TRUE);
  				?>
  	<?php 
  		if ($portfolio_main) {
  			$portfolio_link = get_permalink($portfolio_main);
  		} else {
  			$portfolio_link = get_portfolio_page_link(get_the_ID()); 
  		}
  	?>
  	<?php if ($layout == 'layout2') { ?>
  		<article <?php post_class('post blog-post portfolio-post'); ?> id="post-<?php the_ID(); ?>">
  			<div class="row">
  				<div class="seven columns">
  					<?php
  					  // The following determines what the post format is and shows the correct file accordingly
  					  if ($format) {
  					  get_template_part( 'inc/postformats/'.$format );
  					  } else {
  					  get_template_part( 'inc/postformats/standard' );
  					  }
  					?>
  				</div>
  				<div class="five columns product-information portfolio-layout2">
  					<div class="product_nav">
  						<?php previous_post_link('%link', '<i class="fa fa-angle-left"></i>'); ?>
  						<a href="<?php echo $portfolio_link; ?>" class="gotoportfolio"><i class="fa fa-th"></i></a>
  						<?php next_post_link('%link', '<i class="fa fa-angle-right"></i>'); ?>
  					</div>
  					<header class="post-title">
	  					<aside class="post_categories"><?php echo $meta; ?></aside>
	  					<h2><?php the_title(); ?></h2>
  					</header>
  					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); ?>
  					<aside id="product_share" data-img="<?php echo $image[0]; ?>">
  						<div class="placeholder"></div>
  					</aside>
  					<div class="post-content">
  						<?php the_content(); ?>
  					</div>
  					<?php if($attributes) { ?>
  					<div class="title"><?php _e('Project Details', THB_THEME_NAME); ?></div>
  					<ul class="portfolio_attributes">
  						<?php foreach($attributes as $attribute) { ?>
  							<li>
  								<div class="row">
  									<div class="four columns">
  										<label><?php echo $attribute['title']; ?></label>
  									</div>
  									<div class="eight columns">
  										<p><?php echo $attribute['attribute_value']; ?></p>
  									</div>
  								</div>
  							</li>
  						<?php } ?>
  					</ul>
  					<?php } ?>
  				</div>
  			</div>
  		</article>
  		<?php get_template_part( 'inc/postformats/post-related' ); ?>
  	<?php } else { ?>
	  <article <?php post_class('post blog-post portfolio-post'); ?> id="post-<?php the_ID(); ?>">
	    <?php
	      // The following determines what the post format is and shows the correct file accordingly
	      if ($format) {
	      get_template_part( 'inc/postformats/'.$format );
	      } else {
	      get_template_part( 'inc/postformats/standard' );
	      }
	    ?>
	    <div class="row">
	    	<div class="two columns">
	    		<ul class="portfolio_attributes">
		    		<li>
		    			<label><?php _e('Categories', THB_THEME_NAME); ?></label>
			    		<p><?php $meta = get_the_term_list( $id, 'project-category', '', ',', '' ); 
			    		$meta = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $meta); echo $meta;?></p>
		    		</li>
		    		<?php if($attributes) {foreach($attributes as $attribute) { ?>
		    			<li>
		    				<label><?php echo $attribute['title']; ?></label>
		    				<p><?php echo $attribute['attribute_value']; ?></p>
		    			</li>
		    		<?php } }?>
	    		</ul>
	    	</div>
	    	<div class="seven columns portfolio-content portfolio-layout1 <?php if($format != 'video') { echo 'margin';} ?>">
	    		<header class="post-title">
	    			<h2><?php the_title(); ?></h2>
	    		</header>
	    		<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); ?>
	    		<aside id="product_share" data-img="<?php echo $image[0]; ?>">
	    			<div class="placeholder" data-url="<?php the_permalink(); ?>" data-text="<?php the_title();?>"></div>
	    		</aside>
	    		<div class="post-content">
	    			<?php the_content(); ?>
	    		</div>
	    	</div>
	    	<div class="three columns">
	    		<?php get_template_part( 'inc/postformats/post-related' ); ?>
	    	</div>
	    </div>
	  </article>
	  <?php } ?>
  <?php endwhile; else : endif; ?>
</section>
</div>
<?php get_footer(); ?>