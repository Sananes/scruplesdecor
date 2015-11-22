<?php
global $nm_theme_options;

$show_static_page = ( $nm_theme_options['blog_static_page'] ) ? true : false;

if ( $show_static_page ) {
	if ( function_exists( 'nm_blog_index_vc_styles' ) ) {
		// Custom vcomp styles - Must be included before "get_header()"
		add_action( 'wp_head', 'nm_blog_index_vc_styles', 1000 );
	}
}

get_header(); ?>

<div class="nm-blog">
    <?php
		// Note: Keep below "get_header()" (page not loading properly in some cases otherwise)
		$blog_page = ( $show_static_page ) ? get_page( $nm_theme_options['blog_static_page_id'] ) : false;
		
		if ( $blog_page ) :
	?>
    <div class="nm-page-full">
        <div class="entry-content">
            <?php echo do_shortcode( $blog_page->post_content ); ?>
        </div>
    </div>
	<?php endif; ?>
    
    <div class="nm-blog-categories">
        <div class="nm-row">
            <div class="col-xs-12">
                <?php echo nm_blog_category_menu(); ?>
            </div>
        </div>
    </div>
	
    <?php get_template_part( 'content' ); ?>
</div>

<?php get_footer(); ?>
