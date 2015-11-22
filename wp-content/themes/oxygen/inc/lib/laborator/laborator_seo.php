<?php
/**
 *	Laborator - SEO Settings for Posts
 *
 *	Developed by: Arlind Nushi
 *	Version: 1.2
 *
 *	www.laborator.co
 */


add_action('add_meta_boxes', 'laborator_seo_meta_box');
add_action('save_post', 'laborator_seo_save_post');


function laborator_seo_meta_box()
{
	$meta_box_title = 'SEO Settings';
	
	add_meta_box('laborator_seo_settings', $meta_box_title, 'laborator_seo_html', 'page', 'normal');
	add_meta_box('laborator_seo_settings', $meta_box_title, 'laborator_seo_html', 'post', 'normal');
	add_meta_box('laborator_seo_settings', $meta_box_title, 'laborator_seo_html', 'portfolio', 'normal');
	add_meta_box('laborator_seo_settings', $meta_box_title, 'laborator_seo_html', 'gallery', 'normal');
}


function laborator_seo_html($post)
{
	$post_id = $post->ID;
	

	# Fields
	$custom_page_title 				= get_post_meta($post_id, 'custom_page_title', TRUE);
	$laborator_meta_description		= get_post_meta($post_id, 'laborator_meta_description', TRUE);
	$laborator_meta_keywords 		= get_post_meta($post_id, 'laborator_meta_keywords', TRUE);
	$laborator_meta_robots_index 	= get_post_meta($post_id, 'laborator_meta_robots_index', TRUE);
	$laborator_meta_robots_follow 	= get_post_meta($post_id, 'laborator_meta_robots_follow', TRUE);
	
?>
<style>
	#laborator_seo_settings  .inside {
		margin: 0px;
		padding: 0px;
	}
	
	.laborator_seo_settings {
	}
	
	.laborator_seo_settings > label {
		width: 180px;
		display: inline-block;
		float: left;
		font-weight: bold;
	}

	
	.laborator_seo_settings .field {
		float: left;
	}
	
	.laborator_seo_settings .field label {
		margin-right: 10px;
	}
	
	.laborator_seo_settings .field input[type="text"], .laborator_seo_settings .field textarea {
		width: 500px !important;
	}
	
	.laborator_seo_settings .desc {
		display: block;
		padding-left: 182px;
		color: #999;
		font-style: italic;
		margin-top: 8px;
	}
	
	.laborator_seo_settings .clear {
		clear: both;
	}
	
	.laborator_seo_settings .highlight {
		background: #fff;
		padding: 2px 5px;
		border: 1px solid #eee;
		font-size: 11px;
		margin-left: 2px;
	}
	
</style>

<div class="misc-pub-section laborator_seo_settings">
	
	<label for="custom_page_title">Custom Title</label>
	
	<div class="field">
		<input type="text" name="custom_page_title" id="custom_page_title" class="regular-text" value="<?php echo esc_attr($custom_page_title); ?>" />
	</div>
	
	<div class="clear"></div>
	
	<div class="desc">
		<span class="char-counter" data-for="custom_page_title"></span>
		Leave empty if you want to use this post title as page title. 
		<br />
		Special values: <strong class="highlight">-</strong> (minus) will show only post title, <strong class="highlight">_</strong> (underscore) will show only site/blog name.
	</div>
	
</div>

<div class="misc-pub-section laborator_seo_settings">
	
	<label for="laborator_meta_description">Meta Description</label>
	
	<div class="field">
		<textarea name="laborator_meta_description" id="laborator_meta_description" rows="5" cols="60"><?php echo $laborator_meta_description; ?></textarea>
	</div>
	
	<div class="clear"></div>
	
	<div class="desc">
		<span class="char-counter" data-for="laborator_meta_description"></span>
		Describe correctly (and briefly - up to 160 chars) your <strong>post content</strong> and have more chances to rank higher in Search Engines
	</div>

</div>

<div class="misc-pub-section laborator_seo_settings">
	
	<label for="laborator_meta_keywords">Meta Keywords</label>
	
	<div class="field">
		<input type="text" name="laborator_meta_keywords" id="laborator_meta_keywords" class="regular-text" value="<?php echo esc_attr($laborator_meta_keywords); ?>" />
	</div>
	
	<div class="clear"></div>
	
	<div class="desc">
		<span class="char-counter" data-for="laborator_meta_keywords"></span>
		Enter (comma-separated) your top keywords used in the content
	</div>
	
</div>


<div class="misc-pub-section laborator_seo_settings">
	
	<label for="laborator_meta_robots_index_yes">Meta Robots Index</label>
	
	<div class="field">
		<label>
			<input type="radio" name="laborator_meta_robots_index" id="laborator_meta_robots_index_none" class="radio" value="0"<?php echo ! $laborator_meta_robots_index ? ' checked="checked"' : ''; ?> />
			&nbsp; auto
		</label>
		
		<label>
			<input type="radio" name="laborator_meta_robots_index" id="laborator_meta_robots_index_yes" class="radio" value="1"<?php echo $laborator_meta_robots_index == 1 ? ' checked="checked"' : ''; ?> />
			&nbsp;index
		</label>

		<label>
			<input type="radio" name="laborator_meta_robots_index" id="laborator_meta_robots_index_no" class="radio" value="-1"<?php echo $laborator_meta_robots_index == -1 ? ' checked="checked"' : ''; ?> />
			&nbsp;noindex
		</label>
	</div>
	
	<div class="clear"></div>
	
	<div class="desc">Allow search engines to index this page</div>
	
</div>



<div class="misc-pub-section laborator_seo_settings">
	
	<label for="laborator_meta_robots_follow_yes">Meta Robots Follow:</label>
	
	<div class="field">
		<label>
			<input type="radio" name="laborator_meta_robots_follow" id="laborator_meta_robots_follow_none" class="radio" value="0"<?php echo ! $laborator_meta_robots_index ? ' checked="checked"' : ''; ?> />
			&nbsp; auto
		</label>
		
		<label>
			<input type="radio" name="laborator_meta_robots_follow" id="laborator_meta_robots_follow_yes" class="radio" value="1"<?php echo $laborator_meta_robots_follow == 1 ? ' checked="checked"' : ''; ?> />
			&nbsp;follow
		</label>

		<label>
			<input type="radio" name="laborator_meta_robots_follow" id="laborator_meta_robots_follow_no" class="radio" value="-1"<?php echo $laborator_meta_robots_follow == -1 ? ' checked="checked"' : ''; ?> />
			&nbsp;nofollow
		</label>
	</div>
	
	<div class="clear"></div>
	
	<div class="desc">Allow search engines to follow thinks in this page</div>
	
</div>

<style>
.char-counter {
	font-weight: bold;
	color: #444;
	display: block;
	font-size: 10px;
	position: relative;
	margin-top: -3px;
	top: -3px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	var $cc = $(".char-counter");
	
	$cc.each(function(i, el)
	{
		var $this = $(el),
			$input = $('#' + $this.data('for') );
			
		if($input.length)
		{
			var chars = '', len;
			
			if(len = $input.val().length)
			{
				chars = len == 1 ? '1 char' : (len + ' chars');
			}
			
			$this.html(chars);
			
			$input.on('keyup', function(ev)
			{
				chars = '', len;
			
				if(len = $input.val().length)
				{
					chars = len == 1 ? '1 char' : (len + ' chars');
				}
				
				$this.html(chars);
			});
		}
	});
});
</script>
<?php

}


function laborator_seo_save_post($post_id)
{
	# Set Field Contents
	$custom_page_title 				= isset($_POST['custom_page_title']) ? $_POST['custom_page_title'] : '';
	$laborator_meta_description 	= isset($_POST['laborator_meta_description']) ? $_POST['laborator_meta_description'] : '';
	$laborator_meta_keywords 		= isset($_POST['laborator_meta_keywords']) ? $_POST['laborator_meta_keywords'] : '';
	$laborator_meta_robots_index 	= isset($_POST['laborator_meta_robots_index']) ? $_POST['laborator_meta_robots_index'] : '';
	$laborator_meta_robots_follow	= isset($_POST['laborator_meta_robots_follow']) ? $_POST['laborator_meta_robots_follow'] : '';
	
	update_post_meta($post_id, 'custom_page_title', $custom_page_title);
	update_post_meta($post_id, 'laborator_meta_description', trim($laborator_meta_description));
	update_post_meta($post_id, 'laborator_meta_keywords', trim($laborator_meta_keywords));
	update_post_meta($post_id, 'laborator_meta_robots_index', trim($laborator_meta_robots_index));
	update_post_meta($post_id, 'laborator_meta_robots_follow', trim($laborator_meta_robots_follow));
}


function laborator_seo()
{
	global $post;
	
	if( ! isset($post) || ! $post)
		return null;
	
	$post_id = $post->ID;
	
	$meta_description 	= $post->laborator_meta_description;
	$meta_keywords 		= $post->laborator_meta_keywords;
	$meta_robots_index	= $post->laborator_meta_robots_index;
	$meta_robots_follow	= $post->laborator_meta_robots_follow;
	
	# META Description & Keywords
	$meta_description 	= str_replace(PHP_EOL, ' ', $meta_description);
	
	# META Robots
	$meta_robots = array();
	
	if(! $meta_robots_index || $meta_robots_index == 1)
		$meta_robots[] = 'index';
	else
		$meta_robots[] = 'noindex';
		
	
	if(! $meta_robots_follow || $meta_robots_follow == 1)
		$meta_robots[] = 'follow';
	else
		$meta_robots[] = 'nofollow';
?>

<!-- Laborator SEO -->
<?php if($meta_description): ?>
<meta name="description" content="<?php echo esc_attr($meta_description); ?>">
<?php endif; ?>
<?php if($meta_keywords): ?>
<meta name="keywords" content="<?php echo esc_attr($meta_keywords); ?>">
<?php endif; ?>
<?php if(get_option('blog_public')): ?>
<meta name="robots" content="<?php echo implode(',', $meta_robots); ?>">
<?php endif; ?>
<!-- End: Laborator SEO -->
<?php
}

add_action('wp_head', 'laborator_seo', 1);
add_filter('wp_title', 'laborator_seo_title', 100);

function laborator_seo_title($title)
{
	global $post;
	
	if($post)
	{
		$custom_page_title = $post->custom_page_title;
		
		if($custom_page_title)
		{
			if($custom_page_title == '-')
			{
				$custom_page_title = get_the_title();
			}
			else
			if($custom_page_title == '_')
			{
				$custom_page_title = get_bloginfo('name');
			}
			
			return $custom_page_title;
		}
	}
	
	return $title;
}