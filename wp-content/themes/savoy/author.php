<?php get_header(); ?>

<div class="nm-blog">
    <div class="nm-blog-heading">
    	<div class="nm-row">	
        	<div class="col-xs-12">
                <h1><?php wp_kses( printf( __( 'Author Archives: %s', 'nm-framework' ), '<strong>' . get_the_author() . '</strong>' ), array( 'strong' => array() ) ); ?></h1>
            </div>
    	</div>
    </div>
	
	<?php get_template_part( 'content' ); ?>
</div>

<?php get_footer(); ?>