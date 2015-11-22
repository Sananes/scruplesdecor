<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

global $wp_query, $more;

$hover_effect   = get_data('blog_thumbnail_hover_effect');
$author_info 	= get_data('blog_author_info');
$permalink      = get_permalink();

$post_classes = array();

if($wp_query->current_post == 0)
{
	$post_classes[] = 'first-post';
}
?>
<article <?php post_class(implode(' ', $post_classes)); ?>>

	<?php include('blog-post-thumbnail.php'); ?>

	<div class="post-content">
		<h2 class="title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php include('blog-post-meta.php'); ?>
		</h2>

		<?php include('blog-post-content.php'); ?>
	</div>

	<?php if(is_single()): ?>

		<?php include('blog-post-share.php'); ?>

		<?php include('blog-post-author-info.php'); ?>

		<?php comments_template(); ?>

	<?php endif; ?>
</article>