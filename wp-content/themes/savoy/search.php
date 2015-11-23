<?php get_header(); ?>

<div class="nm-search-results nm-blog">
	<div class="nm-blog-heading">
    	<div class="nm-row">	
        	<div class="col-xs-12">
                <h1><?php wp_kses( printf( __( '%s Search Results for: %s', 'nm-framework' ), $wp_query->found_posts, '<strong>' . get_search_query() . '</strong>' ), array( 'strong' => array() ) ); ?></h1>
            </div>
        </div>
	</div>
	
    <?php if ( have_posts() ) : ?>
            
        <div class="nm-search-results">
            <?php
				while ( have_posts() ) : the_post();
					get_template_part( 'content', 'search' );
				endwhile;
            ?>
        </div>
        
        <?php get_template_part( 'pagination' ); ?>
    
    <?php 
        else :
        
            // If no content, include the "No posts found" template
            get_template_part( 'content', 'none' );
            
        endif;
    ?>
</div>

<?php get_footer(); ?>
