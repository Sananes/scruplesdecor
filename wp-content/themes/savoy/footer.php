<?php 
	global $nm_theme_options, $nm_globals;
	
	// Copyright text
	$copyright_text = ( isset( $nm_theme_options['footer_bar_text'] ) && strlen( $nm_theme_options['footer_bar_text'] ) > 0 ) ? $nm_theme_options['footer_bar_text'] : '';
	
	// Bar right-column content
	if ( $nm_theme_options['footer_bar_content'] !== 'social_icons' ) {
		$display_social_icons = false;
		$display_copyright_in_menu = ( $nm_theme_options['footer_bar_content'] !== 'copyright_text' ) ? true : false;
		$bar_content = ( $display_copyright_in_menu ) ? do_shortcode( $nm_theme_options['footer_bar_custom_content'] ) : $copyright_text;
	} else {
		$display_social_icons = true;
		$display_copyright_in_menu = true;
	}
?>                

                </div>
            </div>
            <!-- /page wrappers -->
            
            <div id="nm-page-overlay" class="nm-page-overlay"></div>
            <div id="nm-widget-panel-overlay" class="nm-page-overlay"></div>
            
            <!-- footer -->
            <footer id="nm-footer" class="nm-footer" role="contentinfo">
                <?php
                    if ( is_active_sidebar( 'footer' ) ) {
                        get_footer( 'widgets' );
                    }
                ?>
                
                <div class="nm-footer-bar">
                    <div class="nm-footer-bar-inner">
                        <div class="nm-row">
                            <div class="nm-footer-bar-left col-md-8 col-xs-12">
                                <?php 
									if ( isset( $nm_theme_options['footer_bar_logo'] ) && strlen( $nm_theme_options['footer_bar_logo']['url'] ) > 0 ) : 
									
									$footer_bar_logo_src = ( is_ssl() ) ? str_replace( 'http://', 'https://', $nm_theme_options['footer_bar_logo']['url'] ) : $nm_theme_options['footer_bar_logo']['url'];
								?>
                                <div class="nm-footer-bar-logo">
                                    <img src="<?php echo esc_url( $footer_bar_logo_src ); ?>" />
                                </div>
                                <?php endif; ?>
                                
                                <ul id="nm-footer-bar-menu" class="menu">
                                    <?php
                                        // Footer menu
                                        wp_nav_menu( array(
                                            'theme_location'	=> 'footer-menu',
                                            'container'       	=> false,
                                            'fallback_cb'     	=> false,
                                            'items_wrap'      	=> '%3$s'
                                        ) );
                                    ?>
                                    <?php if ( $display_copyright_in_menu ) : ?>
                                    <li class="nm-footer-bar-text menu-item"><div><?php echo wp_kses_post( $copyright_text ); ?></div></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            
                            <div class="nm-footer-bar-right col-md-4 col-xs-12">
                                <?php if ( $display_social_icons ) : ?>
									<?php echo nm_get_social_profiles( 'nm-footer-bar-social' ); // Args: $wrapper_class ?>
                                <?php else : ?>
                                <ul class="menu">
                                    <li class="nm-footer-bar-text menu-item"><div><?php echo wp_kses_post( $bar_content ); ?></div></li>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- /footer -->
            
            <!-- slide menu -->
            <div id="nm-slide-menu" class="nm-slide-menu">
                <div class="nm-slide-menu-scroll">
                    <div class="nm-slide-menu-content">
                        <div class="nm-row">
                                                    
                            <div class="nm-slide-menu-top col-xs-12">
                                <ul id="nm-slide-menu-top-ul" class="menu">
                                    <?php if ( $nm_globals['cart_link'] ) : ?>
                                    <li class="nm-slide-menu-item-cart menu-item">
                                        <a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" id="nm-slide-menu-cart-btn">
                                            <?php echo nm_get_cart_title(); ?>
                                            <?php echo nm_get_cart_contents_count(); ?>
                                        </a>
                                    </li>
                                    <?php 
                                        endif;
                                        
                                        if ( $nm_globals['header_shop_search'] ) :
                                    ?>
                                    <li class="nm-slide-menu-item-search menu-item">
                                        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                                            <input type="text" id="nm-slide-menu-shop-search-input" class="nm-slide-menu-search" autocomplete="off" value="" name="s" placeholder="<?php esc_html_e( 'Search store', 'nm-framework' ); ?>" />
                                            <span class="nm-font nm-font-search-alt flip"></span>
                                            <input type="hidden" name="post_type" value="product" />
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                             
                            <div class="nm-slide-menu-main col-xs-12">
                                <ul id="nm-slide-menu-main-ul" class="menu">
                                    <?php
                                        // Main menu
                                        wp_nav_menu( array(
                                            'theme_location'	=> 'main-menu',
                                            'container'       	=> false,
                                            'fallback_cb'     	=> false,
                                            'after' 	 		=> '<span class="nm-menu-toggle"></span>',
                                            'items_wrap'      	=> '%3$s'
                                        ) );
                                        
                                        // Right menu                        
                                        wp_nav_menu( array(
                                            'theme_location'	=> 'right-menu',
                                            'container'       	=> false,
                                            'fallback_cb'     	=> false,
                                            'after' 	 		=> '<span class="nm-menu-toggle"></span>',
                                            'items_wrap'      	=> '%3$s'
                                        ) );
                                    ?>
                                </ul>
                            </div>
        
                            <div class="nm-slide-menu-secondary col-xs-12">
                                <ul id="nm-slide-menu-secondary-ul" class="menu">
                                    <?php
                                        // Top bar menu
                                        if ( $nm_theme_options['top_bar'] ) {
                                            wp_nav_menu( array(
                                                'theme_location'	=> 'top-bar-menu',
                                                'container'       	=> false,
                                                'fallback_cb'     	=> false,
                                                'after' 	 		=> '<span class="nm-menu-toggle"></span>',
                                                'items_wrap'      	=> '%3$s'
                                            ) );
                                        }
                                        
                                        if ( $nm_theme_options['menu_login'] ) :
                                    ?>
                                    <li class="nm-menu-item-login menu-item">
                                        <?php echo nm_get_myaccount_link( false ); ?>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
            <!-- /slide menu -->
            
            <?php if ( $nm_globals['cart_panel'] ) : ?>
            <!-- widget panel -->                
            <div id="nm-widget-panel" class="nm-widget-panel <?php echo esc_attr( $nm_theme_options['widget_panel_color'] ); ?>">
                <div class="nm-widget-panel-scroll">
                    <div class="nm-widget-panel-content">
                        <div class="nm-widget-panel-header">
                            <a href="#" id="nm-widget-panel-close"><?php esc_html_e( 'Close', 'nm-framework' ); ?></a>
                        </div>
                                            
                        <?php woocommerce_mini_cart(); ?>
                    </div>
                </div>
            </div>
            <!-- /widget panel -->
            <?php endif; ?>
            
            <!-- quickview -->
            <div id="nm-quickview" class="clearfix"></div>
            <!-- /quickview -->
            
            <?php if ( strlen( $nm_theme_options['custom_js'] ) > 0 ) : ?>
            <!-- Custom Javascript -->
            <script type="text/javascript">
                <?php echo $nm_theme_options['custom_js']; ?>
            </script>
            <?php endif; ?>
            
            <?php
                // WordPress footer hook
                wp_footer();
            ?>
        
        </div>
        <!-- /page overflow wrapper -->
    	
	</body>
    
</html>
