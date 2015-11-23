<?php
	global $nm_theme_options;
			
	// Logo URL
	if ( isset( $nm_theme_options['logo'] ) && strlen( $nm_theme_options['logo']['url'] ) > 0 ) {
		$logo_href = ( is_ssl() ) ? str_replace( 'http://', 'https://', $nm_theme_options['logo']['url'] ) : $nm_theme_options['logo']['url'];
	} else {
		$logo_href = NM_THEME_URI . '/img/logo@2x.png';
	}
?>

    <div class="nm-header-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img src="<?php echo esc_url( $logo_href ); ?>" class="nm-logo">
        </a>
    </div>
                    