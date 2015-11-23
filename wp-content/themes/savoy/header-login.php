<header class="nm-header-login clearfix" role="banner">
     <?php
		// Header part: Logo
		include( NM_THEME_DIR . '/header-part-logo.php' );
	?>
    
    <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
    <div class="nm-header-login-menu">
    	<a href="#" id="nm-show-register-link"><?php esc_html_e( 'Register', 'woocommerce' ); ?></a>
        <a href="#" id="nm-show-login-link" class="hide"><?php esc_html_e( 'Login', 'woocommerce' ); ?></a>
    </div>
    <?php endif; ?>
</header>
