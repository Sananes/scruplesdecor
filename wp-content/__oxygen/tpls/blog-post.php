<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $authordata;

$id             = get_the_id();
$author         = get_the_author();
$comments_count = wp_count_comments($id)->approved;

$thumbnails 	= get_data('blog_thumbnails');
$show_category	= get_data('blog_category');
$show_comments	= get_data('blog_comments_count');
$author_name	= get_data('blog_author_name');
$post_date		= get_data('blog_post_date');

$has_sidebar	= get_data('blog_sidebar_position') != 'Hide';

$has_thumb		= $thumbnails && has_post_thumbnail();

$posts_url      = get_author_posts_url($authordata->ID);
$user_url       = $authordata->user_url;

$post_meta		= $post_date || $author_name || $show_comments;

if( ! $user_url)
	$user_url = $posts_url;
	

$post_class = array('blog-post');

if( ! $has_thumb)
	$post_class[] = 'no-thumbnail';
	
?>
<div <?php post_class(implode(' ', $post_class)); ?>>

	<div class="row">
	
		<?php if($has_thumb): ?>
		<div class="col-sm-<?php echo $has_sidebar ? 4 : 3; ?> no-padding">
			
			<div class="blog-img<?php echo get_data('blog_thumbnail_hover_effect') ? ' hover-effect' : ''; ?>">
				<a href="<?php the_permalink(); ?>">
					<?php #echo laborator_show_img($id, 'blog-thumb-1'); ?>
					<?php the_post_thumbnail('blog-thumb-1'); ?>
					<span class="hover">
						<em><?php _e('Read more...', 'oxygen'); ?></em>
					</span>
				</a>
			</div>
			
		</div>
		
		<div class="col-sm-<?php echo $has_sidebar ? 8 : 9; ?>">
		<?php else: ?>
		
		<div class="col-sm-12">
		<?php endif; ?>
		
			<div class="blog_content">
				<h1>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h1>
				
				<?php if($show_category): ?>
				<h2><?php the_category(', '); ?></h2>
				<?php endif; ?>
				
				<?php the_excerpt(); ?>
				
				<?php if($post_meta): ?>
				<div class="post-meta">
				
					<?php if($post_date): ?>
					<div class="blog_date">	
						<span class="glyphicon glyphicon-calendar"></span>
						<?php the_time('F d, Y'); ?>
					</div>
					<?php endif; ?>
					
					<?php if($author_name): ?>
					<div class="blog_date blog_author">	
						<span class="glyphicon glyphicon-user"></span>
						<a href="<?php echo $user_url; ?>"><?php the_author(); ?></a>
					</div>
					<?php endif; ?>
					
					<?php if($show_comments): ?>
					<a href="<?php the_permalink(); ?>#comments" class="comment_text">
						<span class="glyphicon glyphicon-comment"></span>
						<?php echo sprintf(_n('%d comment', '%d comments', $comments_count, 'oxygen'), $comments_count); ?>
					</a>
					<?php endif; ?>
					
				</div>
				<?php endif; ?>
				
			</div>
			
		</div>
		
	</div>
	
</div>