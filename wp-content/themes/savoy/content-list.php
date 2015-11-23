<?php
	global $nm_theme_options;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="nm-row">
        <div class="nm-title-col col-xs-4">
            <h1 class="nm-post-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h1>
        </div>
        
        <div class="nm-content-col col-xs-8">
            <?php
				$blog_slider = ( $nm_theme_options['blog_gallery'] === '1' ) ? nm_get_blog_slider( get_the_ID(), 'blog-grid' ) : false;
				
				if ( $blog_slider ) :
					
					echo $blog_slider;
				
				elseif ( has_post_thumbnail() ) :
			?>
            <div class="nm-post-thumbnail">   
                <a href="<?php esc_url( the_permalink() ); ?>"><?php the_post_thumbnail( 'nm_blog_list' ); ?></a>
            </div>
            <?php endif; ?>
            
            <div class="nm-row">
                <div class="col-lg-4 col-xs-12">
                	<div class="nm-post-meta">
                        <span><?php the_date(); ?></span>
                    </div>
                </div>
                    
                <div class="col-lg-8 col-xs-12">
                	<div class="nm-post-content">
						<?php if ( $nm_theme_options['blog_show_full_posts'] === '1' ) : ?>
                            <div class="entry-content">
								<?php the_content(); ?>
                            </div>
                            <?php
								wp_link_pages( array(
									'before' 		=> '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'nm-framework' ) . '</span>',
									'after' 		=> '</div>',
									'link_before'	=> '<span>',
									'link_after'	=> '</span>'
								) );
							?>
                        <?php else : ?>
                            <div class="nm-post-excerpt">
								<?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="nm-divider-col col-xs-12">
           <div class="nm-post-divider">&nbsp;</div>
        </div>
    </div>
</div>
