<?php
	global $nm_theme_options, $nm_globals, $nm_body_class;
	
	// Favicon
	$custom_favicon = false;
	if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) {
		if ( isset( $nm_theme_options['favicon'] ) && strlen( $nm_theme_options['favicon']['url'] ) > 0 ) {
			$custom_favicon = true;
			$favicon_url = ( is_ssl() ) ? str_replace( 'http://', 'https://', $nm_theme_options['favicon']['url'] ) : $nm_theme_options['favicon']['url'];
		}
	}
	
	// CSS animations preload class
	$nm_body_class .= ' nm-preload';
	
	// Top bar
	$top_bar = ( isset( $_GET['top_bar'] ) ) ? true : $nm_theme_options['top_bar'];
	$top_bar_column_left_size = intval( $nm_theme_options['top_bar_left_column_size'] );
	$top_bar_column_right_size = 12 - $top_bar_column_left_size;
	
	// Fixed header body class
	$nm_body_class .= ( $nm_theme_options['header_fixed'] ) ? ' header-fixed' : '';
		
	if ( is_front_page() ) {
		// Header border class - Home-page
		$nm_body_class .= ( isset( $_GET['header_border'] ) ) ? ' header-border-1' : ' header-border-' . $nm_theme_options['home_header_border'];
				
		// Header transparency class - Home-page
		$nm_body_class .= ( isset( $_GET['transparent_header'] ) ) ? ' header-transparent-1' : ' header-transparent-' . $nm_theme_options['home_header_transparent'];
	} elseif ( nm_woocommerce_activated() && ( is_shop() || is_product_taxonomy() ) ) {
		// Header border class - Shop archive/listing
		$nm_body_class .= ' header-border-' . $nm_theme_options['shop_header_border'];
	} else {
		// Header border class
		$nm_body_class .= ' header-border-' . $nm_theme_options['header_border'];
	}
	
	// Sticky footer class
	$sticky_footer_class = ' footer-sticky-' . $nm_theme_options['footer_sticky'];
?>
<!DOCTYPE html>

<html <?php language_attributes(); ?> class="<?php echo esc_attr( $sticky_footer_class ); ?>">
	
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		
        <?php if ( $nm_theme_options['custom_title'] ) : ?>
        <!-- Title -->
        <title><?php wp_title( '&ndash;', true, 'right' ); ?></title>
		<?php endif; ?>
        
        <link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        
        <?php if ( $custom_favicon ) : ?>
		<!-- Favicon -->
		<link href="<?php echo esc_url( $favicon_url ); ?>" rel="shortcut icon">
        <?php endif; ?>
        	
		<?php wp_head(); ?>
    </head>
    
	<body <?php body_class( esc_attr( $nm_body_class ) ); ?>>
        
        <!-- page overflow wrapper -->
        <div class="nm-page-overflow">
        
            <!-- page wrapper -->
            <div class="nm-page-wrap">
            
                <?php if ( $top_bar ) : ?>
                <!-- top bar -->
                <div id="nm-top-bar" class="nm-top-bar">
                    <div class="nm-row">
                        <div class="nm-top-bar-left col-xs-<?php echo esc_attr( $top_bar_column_left_size ); ?>">
                            <?php
								// Social icons (left column)
								if ( $nm_theme_options['top_bar_social_icons'] == 'l_c' ) {
									echo nm_get_social_profiles( 'nm-top-bar-social' ); // Args: $wrapper_class 
								}
							?>
                            
                            <div class="nm-top-bar-text">
                                <?php echo wp_kses_post( $nm_theme_options['top_bar_text'] ); ?>
                            </div>
                        </div>
                                                
                        <div class="nm-top-bar-right col-xs-<?php echo esc_attr( $top_bar_column_right_size ); ?>">
                            <?php
								// Social icons (right column)
								if ( $nm_theme_options['top_bar_social_icons'] == 'r_c' ) {
									echo nm_get_social_profiles( 'nm-top-bar-social' ); // Args: $wrapper_class 
								}
							?>
							
							<?php
								// Top-bar menu
								wp_nav_menu( array(
                                    'theme_location'	=> 'top-bar-menu',
                                    'container'       	=> false,
                                    'menu_id'			=> 'nm-top-menu',
                                    'fallback_cb'     	=> false,
                                    'items_wrap'      	=> '<ul id="%1$s" class="nm-menu">%3$s</ul>'
                                ) );
                            ?>
                        </div>
                    </div>                
                </div>
                <!-- /top bar -->
                <?php endif; ?>
                            
                <div class="nm-page-wrap-inner">
                
                    <div id="nm-header-placeholder" class="nm-header-placeholder"></div>
                            
                    <?php
                        $header_layout = ( isset( $_GET['header'] ) ) ? $_GET['header'] : $nm_theme_options['header_layout'];
                        
                        if ( $header_layout == 'default' ) {
                            // Include default header
                            get_header( 'default' );
                        } else {
                            // Include centered header
                            get_header( 'centered' );
                        }
                        
                        if ( nm_woocommerce_activated() && is_account_page() ) {
                            // Include login header
                            get_header( 'login' );
                        }
                    ?>
