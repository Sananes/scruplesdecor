<?php
	global $nm_theme_options;
	
	$blog_layout = $nm_theme_options['blog_layout'];
?>

<?php if ( have_posts() ) : ?>
	    
	<div class="nm-blog-<?php echo esc_attr( $blog_layout ); ?>">
		<?php 
			if ( $blog_layout == 'grid' ) {
				get_template_part( 'content', $blog_layout ); // Loop is included in the "content-grid" template for the grid layout
			} else {
				while ( have_posts() ) : the_post();
					get_template_part( 'content', $blog_layout );
				endwhile;
			}
        ?>
	</div>
    
	<?php get_template_part( 'pagination' ); ?>

<?php 
    else :
    
        // If no content, include the "No posts found" template
        get_template_part( 'content', 'none' );
        
    endif;
?>