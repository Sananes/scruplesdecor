<?php get_header(); ?>

<div class="nm-blog">
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
