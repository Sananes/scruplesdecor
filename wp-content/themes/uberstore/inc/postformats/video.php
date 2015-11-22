<?php global $wp_embed; ?>
<?php $embed = get_post_meta($post->ID, 'post_video', TRUE);
			$vimeo = get_post_meta($post->ID, 'post_video_vimeo', TRUE); 
			if ( 'portfolio' == get_post_type() ) { 
				$embed = get_post_meta($post->ID, 'portfolio_video', TRUE);
				$vimeo = get_post_meta($post->ID, 'portfolio_video_vimeo', TRUE);
			} ?>
<div class="post-gallery flex-video widescreen <?php if ($vimeo[0] == 'vimeo') { ?>vimeo<?php } ?>">
	<?php if ($embed !='') { ?>
	  <?php echo $wp_embed->run_shortcode('[embed]'.$embed.'[/embed]'); ?>
	<?php } ?>
	
</div>