<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

global $wp_query, $paged, $page, $s;

$cols = 3;
$results_count = $cols * absint(get_data('search_results_count'));
$page_num = $page > $paged ? $page : $paged;

$post_types = array();

foreach(get_data('search_post_types') as $post_type => $include)
{
	if($include)
	{
		$post_types[] = $post_type;
	}
}

query_posts(array(
	"posts_per_page"   => $results_count,
	"s"                => $s,
	"paged"            => $page_num,
	"post_type"		   => $post_types
));

# Meta Information about WP Posts Query
$found_posts			= $wp_query->found_posts;
$max_num_pages          = $wp_query->max_num_pages;
$paged                  = get_query_var('paged');
$pagination_position    = 'center';


get_header();

?>
<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$("#search-again").on('click', function(ev)
		{
			ev.preventDefault();
			
			$(".search_input").focus().select();
		});
	});
</script>
<div class="row">
	<div class="col-md-12">
				
		<div class="search-results-header">
			<div class="row">
				<div class="col-sm-8">
					
					<div class="results-text">
						<h3>
							<?php echo sprintf( _n('Showing <span>%d</span> result for', 'Showing <span>%d</span> results for:', $found_posts, 'oxygen') , $found_posts); ?>
							<span class="search-text">&quot;<?php echo esc_html(get('s')); ?>&quot;</span>
						</h3>
						
						<p>
							<?php _e("Didn't find what you were looking for?", 'oxygen'); ?>
							<a href="#" id="search-again"><?php _e("Search again", 'oxygen'); ?></a>
						</p>
					</div>
					
				</div>
				
				<div class="col-sm-4">
					
					<form action="<?php echo home_url(); ?>" method="get" class="search search-box" enctype="application/x-www-form-urlencoded">
					
						<button type="submit" class="search-submit">
							<span class="glyphicon glyphicon-search"></span>
						</button>
						
						<input type="text" class="search_input" name="s" alt="" placeholder="<?php _e('Search...', 'oxygen'); ?>" value="" /> 
						
					</form>
					
				</div>
			</div>
		</div>
		
	</div>
</div>

<section class="search-results">
	<div class="row">
		<?php if(have_posts()): ?>
			
			<?php while(have_posts()): the_post(); global $post; ?>
			<div class="col-sm-4">
				
				<article class="search-entry">
				
					<a href="<?php the_permalink(); ?>" class="thumb">
						<?php
						
						$image_link = str_replace(site_url('/'), '', wp_get_attachment_url( get_post_thumbnail_id() ));
						
						if(has_post_thumbnail() && file_exists(ABSPATH . $image_link)):
						
							echo laborator_show_thumbnail(get_the_id(), 'shop-thumb-2');
						
						else:
							
							?>
							<img src="<?php echo THEMEASSETS; ?>images/search-type-<?php echo $post->post_type; ?>.png" />
							<?php
						
						endif;
						?>
					</a>
					
					<a href="<?php the_permalink(); ?>" class="title">
						<?php the_title(); ?>
						
						<span><?php echo get_post_type_object($post->post_type)->labels->singular_name; ?></span>
					</a>
					
				</article>
				
			</div>
			<?php endwhile; ?>
			
			
			<?php
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
			
		<?php endif; ?>
	</div>
</section>
<?php

get_footer();
?>