<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $wp_query, $page;

$blog_title             = __('Blog', TD);
$sidebar_position       = get_data('blog_sidebar_position');
$pagination_position    = get_data('blog_pagination_position');

		
# Switch Title
if(is_category())
{
	$cat_slug      = isset($wp_query->query['category_name']) ? $wp_query->query['category_name'] : $wp_query->query['cat'];
	$category_name = get_term_by((is_numeric($cat_slug) ? 'id' : 'slug'), basename($cat_slug), 'category')->name;
	$blog_title    = sprintf(__('Category - %s', 'oxygen'), $category_name);
}
else
if(is_tag())
{
	$tag_name      = get_term_by('slug', $wp_query->query['tag'], 'post_tag')->name;
	$blog_title    = sprintf(__('Tag - %s', 'oxygen'), $tag_name);
}
else
if(is_author())
{
	global $authordata;
	
	$blog_title = sprintf(__('Author - %s', 'oxygen'), $authordata->display_name);
}

# Meta Information about WP Posts Query
$max_num_pages  = $wp_query->max_num_pages;
$paged			= get_query_var('paged');

if($page > $paged)
	$paged = $page;
?>

<div class="row">
	<div class="col-md-12">
		<h1 class="page-head-title"><?php echo $blog_title; ?></h1>
	</div>
</div>


<div class="row">

	<div class="col-md-<?php echo $sidebar_position == 'Hide' ? 12 : 9; ?><?php echo $sidebar_position == 'Left' ? ' blog-content-right' : ''; ?>">
	
		<!--blog01-->
		<div class="blog">
		
			<?php 
			
			while(have_posts()):
				
				the_post();
			
				get_template_part('tpls/blog-post'); 
			
			endwhile;
			
			if($max_num_pages > 1):
	
				$_from               = 1;
				$_to                 = $max_num_pages;
				$current_page        = $paged ? $paged : 1;
				$numbers_to_show     = 5;
				$pagination_position = strtolower($pagination_position);
				
				list($from, $to) = generate_from_to($_from, $_to, $current_page, $max_num_pages, $numbers_to_show);
				
				laborator_show_pagination($current_page, $max_num_pages, $from, $to, $pagination_position);
			
			endif;
			?>
		
		</div>
		
	</div>
	
	
	<?php if($sidebar_position == 'Right' || $sidebar_position == 'Left'): ?>
	<div class="col-md-3<?php echo $sidebar_position == 'Left' ? ' blog-left-sidebar' : ''; ?>">
		<?php get_template_part('tpls/blog-sidebar'); ?>
	</div>
	<?php endif; ?>
	
</div>