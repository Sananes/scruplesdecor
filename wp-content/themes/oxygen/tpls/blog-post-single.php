<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $post, $authordata, $comments_count;

$id             = get_the_id();
$author         = get_the_author();
$comments_count = wp_count_comments($id)->approved;

$posts_url      = get_author_posts_url($authordata->ID);
$user_url       = $authordata->user_url;

$thumbnails 	= get_data('blog_single_thumbnails');
$show_category	= get_data('blog_category');
$show_tags		= get_data('blog_tags');
$author_info	= get_data('blog_author_info');
$post_date		= get_data('blog_post_date');
$autoswitch		= get_data('blog_gallery_autoswitch');

if( ! $user_url)
	$user_url = $posts_url;

$author_link = '<a href="' . $user_url . '">' . get_the_author() . '</a>';

$post_slider_images = gb_field('post_slider_images', false, false);

$autoswitch = is_numeric($autoswitch) && $autoswitch > 0 ? $autoswitch : 0;

?>
<!-- single post -->
<div class="single_post">

	<?php if($thumbnails && count($post_slider_images)): ?>
	<div class="post_img nivo post-imgs-slider" data-cycle-timeout="<?php echo $autoswitch * 1000; ?>" data-cycle-auto-height="container" data-cycle-pause-on-hover="true">
		
		<div class="loading">
			<?php _e('Loading images...', 'oxygen'); ?>
		</div>
	<?php	
		wp_enqueue_script('cycle2');
		
		foreach($post_slider_images as $i => $attach):
			
			$img = wp_get_attachment_image_src($attach->ID, 'original');
			$caption = $attach->post_excerpt;
			
			$image_url = laborator_img($attach->guid, 'blog-thumb-2');
			#$image_url = wp_get_attachment_image_src($attach->ID, 'blog-thumb-2');
			$link = $attach->_wp_attachment_image_alt;
			
			if( ! $image_url)
				continue;
			
			?>
			<a href="<?php echo strstr($link, "http") ? $link : $img[0]; ?>" title="<?php echo esc_attr($caption); ?>" class="hidden" data-lightbox-gallery="post-gallery">
				<img src="<?php echo $image_url; ?>" class="img-responsive" />
			</a>
			<?php
			
		endforeach;
		
		if(count($post_slider_images)):
		
		?>
		<div class="pager">
			<span class="prev"></span>
			<span class="next"></span>
		</div>
		<?php
		
		endif;
	?>
	</div>
	<?php elseif($thumbnails && has_post_thumbnail()): $attachment = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'original'); ?>
	<div class="post_img nivo">		
		<a href="<?php echo is_array($attachment) ? $attachment[0] : site_url(); ?>">
			<?php echo laborator_show_img($id, 'blog-thumb-2'); ?>
		</a>
	</div>
	<?php endif; ?>
									
	<div class="post_details">
		
		<h1><?php the_title(); ?></h1>
		
		<h2>
			<?php if($show_category && has_category()): ?>
				<?php _e('In ', 'oxygen'); the_category(', '); ?>,
			<?php endif; ?>
			
			<?php if($post_date): ?>
			<strong><?php echo sprintf(__('on %s', 'oxygen'), get_the_time("F d, Y - H:i")); ?></strong><?php echo has_tag() ? ',' : ''; ?>
			<?php endif; ?>
			
			<?php if($show_tags && has_tag()): ?>
				<?php the_tags(__('Tags ', 'oxygen')); ?>
			<?php endif; ?>
		</h2>
		
		<hr>
		
		<div class="post-content">
			<?php the_content(); ?>
			
			<?php wp_link_pages(array(
					'before' => '<div class="post-pagination">' . __( 'Pages:', 'oxygen'), 
					'after' => '</div>',
					'pagelink' => '<span>%</span>'
				)); 
				?>
		</div>
								
		
		<?php if($author_info): ?>
		<div class="author_post">
			<div class="row">
			
				<div class="col-sm-2 col-xs-4">
					<div class="author_img">
						<a href="<?php echo $user_url; ?>"><?php echo get_avatar($authordata->ID); ?></a>
					</div>
				</div>

				<div class="col-sm-10 col-xs-8 mobile-padding">
				
					<span class="author_text">
						<?php echo sprintf(__('About the author: %s', 'oxygen'), $author_link); ?>
					</span>
					
					<p class="author_about">
						<?php echo $authordata->description ? nl2br($authordata->description) : __('No other information about this author.', 'oxygen'); ?>
					</p>
				</div>
			
			</div>
		</div>
		<?php endif; ?>
	
	</div>

</div>


<?php if(get_data('blog_share_story')): ?>
<!-- share post --> 
<div class="share-post">
	<h1><?php _e('Share This Story', 'oxygen'); ?>:</h1>
	
	<div class="share-post-links">	
		
		<?php 
		$share_story_networks = get_data('blog_share_story_networks');
		
		foreach($share_story_networks['visible'] as $network_id => $network):
			
			if($network_id == 'placebo')
				continue;
			
			share_story_network_link($network_id, $id);
			
		endforeach;
		?>
		
	</div>
	
</div>
<!-- / share post end-->  
<?php endif; ?>
								
						

<?php comments_template(); ?>
