<!-- Start Sharing -->
<section id="post-sharing">
	<div class="row">
		<div class="eight columns">
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); ?>
			<aside id="product_share" data-img="<?php echo $image[0]; ?>">
				<div class="placeholder" data-url="<?php the_permalink(); ?>" data-text="<?php the_title();?>"></div>
			</aside>
		</div>
		<div class="four columns tags">
			<?php $posttags = get_the_tags();
			if ($posttags) {
				foreach($posttags as $tag) {
					echo '<a href="'. get_tag_link($tag->term_id).'" class="tag-link">' . $tag->name . '</a>';
				}
			} ?>
		</div>
	</div>
</section>
<!-- End Sharing -->
<!-- Start About Author -->
<section id="post-author">
	<?php echo get_avatar( get_the_author_meta( 'ID' ), '80'); ?>
	<strong><?php the_author_posts_link(); ?></strong>
	<p><?php the_author_meta('description'); ?></p>
	<?php if(get_the_author_meta('url') != '') { ?>
		<a href="<?php echo get_the_author_meta('url'); ?>" class="boxed-icon"><i class="fa fa-link icon-1x"></i></a>
	<?php } ?>
	<?php if(get_the_author_meta('twitter') != '') { ?>
		<a href="<?php echo get_the_author_meta('twitter'); ?>" class="boxed-icon twitter"><i class="fa fa-twitter icon-1x"></i></a>
	<?php } ?>
	<?php if(get_the_author_meta('facebook') != '') { ?>
		<a href="<?php echo get_the_author_meta('facebook'); ?>" class="boxed-icon facebook"><i class="fa fa-facebook icon-1x"></i></a>
	<?php } ?>
	<?php if(get_the_author_meta('googleplus') != '') { ?>
		<a href="<?php echo get_the_author_meta('googleplus'); ?>" class="boxed-icon google-plus"><i class="fa fa-google-plus icon-1x"></i></a>
	<?php } ?>
</section>
<!-- End About Author -->
<!-- Start Previous / Next Post -->
<section id="post-prevnext">
<div class="row">
	<?php 
		$prev_post = get_adjacent_post(false, '', true);
		
		if(!empty($prev_post)) {
			$excerpt = $prev_post->post_content;
			$previd = $prev_post->ID;
			
			echo '<div class="six columns"><div class="post-navi hide-on-print prev"><a href="' . get_permalink($previd) . '" title="' . $prev_post->post_title . '"><i class="fa fa-long-arrow-left "></i>' . __("Previous Article", THB_THEME_NAME) . '</a></div></div>'; 
		}
	?>
	<?php
		$next_post = get_adjacent_post(false, '', false);
		
		if(!empty($next_post)) {
			$excerptnext = $next_post->post_content;
			$nextid = $next_post->ID;
			
			echo '<div class="six columns"><div class="post-navi hide-on-print next"><a href="' . get_permalink($nextid) . '" title="' . $next_post->post_title . '"><i class="fa fa-long-arrow-right "></i>' . __("Next Article", THB_THEME_NAME) . '</a></div></div>'; 
		}
	?>
</div>
</section>
<?php wp_reset_query(); ?>
<!-- End Previous / Next Post -->