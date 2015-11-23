<?php get_header(); ?>

<div class="nm-row">
    <div class="col-xs-12">
        <div class="nm-page-not-found">
            <div class="nm-page-not-found-icon"></div>
            <h2><?php esc_html_e( 'Oops, page not found.', 'nm-framework' ); ?></h2>
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Click the link below to return home.', 'nm-framework' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Homepage &nbsp;&nbsp;&rarr;', 'nm-framework' ); ?></a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
