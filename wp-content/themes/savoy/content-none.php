<div class="nm-row">
    <div class="col-xs-12">
        <div id="post-0" class="nm-blog-no-results">
            <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
                        
            <h1><?php wp_kses( printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'nm-framework' ), admin_url( 'post-new.php' ) ), array( 'a' => array( 'href' ) ) ); ?></h1>
            
            <?php elseif ( is_search() ) : ?>
            
            <h1><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'nm-framework' ); ?></h1>
            
            <?php else : ?>
            
            <h1><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'nm-framework' ); ?></h1>
        
            <?php endif; ?>
        </div>
    </div>
</div>
