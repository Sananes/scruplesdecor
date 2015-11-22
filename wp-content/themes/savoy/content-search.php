<?php
	global $nm_theme_options;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="nm-row">
        <div class="nm-title-col col-xs-4">
            <h1 class="nm-post-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h1>
            <div class="nm-post-meta">
                <span><?php the_date(); ?></span>
            </div>
        </div>
        
        <div class="nm-content-col col-xs-8">
            <div class="nm-post-content">
				<?php the_excerpt(); ?>
            </div>
        </div>
        
        <div class="nm-divider-col col-xs-12">
           <div class="nm-post-divider">&nbsp;</div>
        </div>
    </div>
</div>
