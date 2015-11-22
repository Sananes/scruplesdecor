<?php
	
	/* Constants & Globals
	=============================================================== */
	
	// Uncomment to include un-minified JavaScript files
	//define( 'NM_SCRIPT_DEBUG', TRUE );
	
	// Constants: Folder directories/uri's
	define( 'NM_THEME_DIR', get_template_directory() );
	define( 'NM_DIR', get_template_directory() . '/includes' );
	define( 'NM_THEME_URI', get_template_directory_uri() );
	define( 'NM_URI', get_template_directory_uri() . '/includes' );
	
	// Constant: Framework namespace
	define( 'NM_NAMESPACE', 'nm-framework' );
	
	// Constant: Theme version
	define( 'NM_THEME_VERSION', '1.2.2' );
	
	// Global: Theme options
	global $nm_theme_options;
	
	// Global: Page includes
	global $nm_page_includes;
	$nm_page_includes = array();
	
	// Global: <body> class
	global $nm_body_class;
	$nm_body_class = '';
	
	// Global: Visual composer "stock" features
	global $nm_vcomp_stock;
	$nm_vcomp_stock = ( defined( 'NM_VCOMP_STOCK' ) ) ? true : false;
	
	// Global: Theme globals
	global $nm_globals;
	$nm_globals = array();
	
	// Globals: Cart link/panel
	$nm_globals['cart_link'] = false;
	$nm_globals['cart_panel'] = false;
	
	// Globals: Shop search
	$nm_globals['header_shop_search'] = false;
	$nm_globals['shop_search'] = false;
	$nm_globals['shop_search_layout'] = 'shop';
	
	// Global: "Product Slider" shortcode loop
	$nm_globals['product_slider_loop'] = false;
	
	// Global: Shop image lazy-loading
	$nm_globals['shop_image_lazy_loading'] = false;
	
	
	
	/* Includes
	=============================================================== */
	
	// Redux: Theme options framework
	if ( ! class_exists( 'ReduxFramework' ) ) {
		require_once( NM_DIR . '/options/ReduxCore/framework.php' );
	
		// Remove dashboard widget
		function nm_redux_remove_dashboard_widget() {
			remove_meta_box( 'redux_dashboard_widget', 'dashboard', 'side' );
		}
		add_action( 'wp_dashboard_setup', 'nm_redux_remove_dashboard_widget', 100 );
	}
		
	if ( ! isset( $redux_demo ) ) {
		require( NM_DIR . '/options/options-config.php' );
	}
	
	// Get theme options
	$nm_theme_options = get_option( 'nm_theme_options' );
	
	// Is the theme options array saved?
	if ( ! $nm_theme_options ) {
		// Save default options array
		require( NM_DIR . '/options/default-options.php' );
	}
	
	// TGM plugin activation
	if ( is_admin() ) {
		require( NM_DIR . '/tgmpa/config.php' );
	}
	
	// Helper functions
	require( NM_DIR . '/helpers.php' );
	
	// Post meta
	require( NM_DIR . '/post-meta.php' );
	
	// Visual composer
	require( NM_DIR . '/visual-composer/init.php' );
	
	// Custom CSS
	if ( is_admin() ) {
		require( NM_DIR . '/custom-styles.php' );
	}
	
	if ( nm_woocommerce_activated() ) {
		// Only include if global product hover image is disabled
		if ( is_admin() ) {
			// WooCommerce: Product details meta boxes
			include( NM_DIR . '/woocommerce/admin/product-details-meta-boxes.php' );
			
			// WooCommerce: Product category "description" field
			include( NM_DIR . '/woocommerce/admin/product-category-description-field.php' );
		}
		
		// WooCommerce: Functions
		include( NM_DIR . '/woocommerce/woocommerce.php' );
		
		// WooCommerce: Wishlist
		$nm_globals['wishlist_enabled'] = class_exists( 'NM_Wishlist' );
		
		// WooCommerce: Quick view
		if ( $nm_theme_options['product_quickview'] ) {
			$nm_page_includes['quickview'] = true;
			include( NM_DIR . '/woocommerce/quickview.php' );
		}
		
		// WooCommerce: Shop search
		$nm_globals['shop_search_layout'] = ( isset( $_GET['search_layout'] ) ) ? $_GET['search_layout'] : $nm_theme_options['shop_search'];
		if ( $nm_globals['shop_search_layout'] !== '0' ) {
			include( NM_DIR . '/woocommerce/search.php' );
			
			if ( $nm_globals['shop_search_layout'] === 'header' ) {
				$nm_globals['header_shop_search'] = true;
			} else {
				$nm_globals['shop_search'] = true;
			}
		}
	}
	
	
	
	/* Globals (requires includes)
	=============================================================== */
	
	// Globals: Shop filters scrollbar
	$nm_globals['shop_filters_scrollbar'] = false;
	$nm_globals['shop_filters_scrollbar_custom'] = false;
	
	if ( nm_woocommerce_activated() ) {
		// Global: Shop page id
		global $nm_globals;
		$nm_globals['shop_page_id'] = ( ! empty( $_GET['shop_page'] ) ) ? intval( $_GET['shop_page'] ) : wc_get_page_id( 'shop' );
		
		// Global: Cart link/panel
		if ( $nm_theme_options['menu_cart'] != '0' ) {
			$nm_globals['cart_link'] = true;
			
			// Is mini cart panel enabled?
			if ( $nm_theme_options['menu_cart'] != 'link' ) {
				$nm_globals['cart_panel'] = true;
			}
		}
		
		// Globals: Shop filters scrollbar
		if ( $nm_theme_options['shop_filters_scrollbar'] !== '0' ) {
			$nm_globals['shop_filters_scrollbar'] = true;
			$nm_globals['shop_filters_scrollbar_custom'] = ( $nm_theme_options['shop_filters_scrollbar'] == 'js' ) ? true : false;
		}
	}
	
	
	
	/* Theme Support
	=============================================================== */

	if ( ! function_exists( 'nm_theme_support' ) ) {
		function nm_theme_support() {
			global $nm_theme_options;
			
			if ( isset( $nm_theme_options['custom_title'] ) && ! $nm_theme_options['custom_title'] ) {
				// Let WordPress manage the document title (no hard-coded <title> tag in the document head)
				add_theme_support( 'title-tag' );
			}
			
			// Add menu support
			add_theme_support( 'menus' );
			
			// Enables post and comment RSS feed links to head
			add_theme_support( 'automatic-feed-links' );
			
			// Add WooCommerce support
			add_theme_support( 'woocommerce' );
			
			// Add thumbnail theme support
			add_theme_support( 'post-thumbnails' );
			
			// Add image sizes
			add_image_size( 'nm_large', 700, '', true );
			add_image_size( 'nm_medium', 220, '', true );
			add_image_size( 'nm_small', 140, '', true );
			add_image_size( 'nm_blog_list', 940, '', true );
						
			// Localisation support
			// WordPress language directory: wp-content/languages/theme-name/en_US.mo
			load_theme_textdomain( 'nm-framework', trailingslashit( WP_LANG_DIR ) . 'nm-framework' );
			// Child theme language directory: wp-content/themes/child-theme-name/languages/en_US.mo
			load_theme_textdomain( 'nm-framework', get_stylesheet_directory() . '/languages' );
			// Theme language directory: wp-content/themes/theme-name/languages/en_US.mo
			load_theme_textdomain( 'nm-framework', NM_THEME_DIR . '/languages' );
		}
	}
	add_action( 'after_setup_theme', 'nm_theme_support' );
	
	// Maximum width for media
	if ( ! isset( $content_width ) ) {
		$content_width = 1220; // Pixels
	}
	
	
	/* Styles
	=============================================================== */
	
	function nm_styles() {
		global $nm_theme_options, $nm_globals;
		
		// Third-party styles				
		wp_enqueue_style( 'normalize', NM_THEME_URI . '/css/third-party/normalize.css', array(), '3.0.2', 'all' );
		wp_enqueue_style( 'slick-slider', NM_THEME_URI . '/css/third-party/slick.css', array(), '1.5.5', 'all' );
		wp_enqueue_style( 'slick-slider-theme', NM_THEME_URI . '/css/third-party/slick-theme.css', array(), '1.5.5', 'all' );
		wp_enqueue_style( 'magnific-popup', NM_THEME_URI . '/css/third-party/magnific-popup.css', array(), '0.9.7', 'all' );
		
		// Theme styles: Grid (enqueue before shop styles)
		wp_enqueue_style( 'nm-grid', NM_THEME_URI . '/css/grid.css', array(), NM_THEME_VERSION, 'all' );
		
		// WooCommerce styles		
		if ( nm_woocommerce_activated() ) {
			// Dequeue styles
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			
			// Dequeue WooCommerce scripts
			// Note: Keep these in the "nm_styles()" function ("BWP Minify" includes them otherwise)
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			
			// Single product page
			if ( is_product() ) {
				// Single product gallery: Image hover-zoom
				$nm_globals['product_image_hover_zoom'] = ( $nm_theme_options['product_image_hover_zoom'] || isset( $_GET['zoom'] ) );
				
				wp_enqueue_style( 'photoswipe', NM_THEME_URI . '/css/third-party/photoswipe/photoswipe.css', array(), '4.0.0', 'all' );
				wp_enqueue_style( 'photoswipe-skin', NM_THEME_URI . '/css/third-party/photoswipe/photoswipe-skin.css', array(), '4.0.0', 'all' );
			} else {
				if ( is_cart() ) {
					// Cart widget: Disable on cart page
					$nm_globals['cart_link'] = false;
				} else if ( is_checkout() ) {
					// Cart widget: Disable on checkout page
					$nm_globals['cart_link'] = false;
					
					// Default checkout page styles
					if ( defined( 'NM_SHOP_DEFAULT_CHECKOUT' ) ) {
						wp_enqueue_style( 'nm-shop-default-checkout', NM_THEME_URI . '/css/shop-default-checkout.css', array(), NM_THEME_VERSION, 'all' );
					}
				}
			}
			
			wp_enqueue_style( 'selectod', NM_THEME_URI . '/css/third-party/selectod.css', array(), '3.8.1', 'all' );
			wp_enqueue_style( 'nm-shop', NM_THEME_URI . '/css/shop.css', array(), NM_THEME_VERSION, 'all' );
		}
		
		// Theme styles
		wp_enqueue_style( 'nm-icons', NM_THEME_URI . '/css/font-icons/theme-icons/theme-icons.css', array(), NM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'nm-core', NM_THEME_URI . '/style.css', array(), NM_THEME_VERSION, 'all' );
		wp_enqueue_style( 'nm-elements', NM_THEME_URI . '/css/elements.css', array(), NM_THEME_VERSION, 'all' );
	}
	add_action( 'wp_enqueue_scripts', 'nm_styles', 99 );
	
	
	
	/* Custom styles
	=============================================================== */
	
	function nm_custom_styles() {
		$styles = get_option( 'nm_theme_custom_styles' );
		
		// Output pre-escaped custom styles
		echo $styles . "\n";
	}
	add_action( 'wp_head', 'nm_custom_styles', 100 );
	
	
	
	/* Scripts
	=============================================================== */
	
	function nm_scripts() {
		if ( ! is_admin() ) {
			global $nm_theme_options, $nm_globals, $nm_page_includes;
			
			
			// Script path and suffix setup (debug mode loads un-minified scripts)
			if ( defined( 'NM_SCRIPT_DEBUG' ) && NM_SCRIPT_DEBUG ) {
				$script_path = NM_THEME_URI . '/js/dev/';
				$suffix = '';
			} else {
				$script_path = NM_THEME_URI . '/js/';
				$suffix = '.min';
			}
			
			
			// Enqueue scripts
			wp_enqueue_script( 'modernizr', NM_THEME_URI . '/js/plugins/modernizr.min.js', array( 'jquery' ), '2.8.3' );
			wp_enqueue_script( 'unveil', NM_THEME_URI . '/js/plugins/jquery.unveil.min.js', array( 'jquery' ), '1.0' );
			wp_enqueue_script( 'slick-slider', NM_THEME_URI . '/js/plugins/slick.min.js', array( 'jquery' ), '1.5.5' );
			wp_enqueue_script( 'magnific-popup', NM_THEME_URI . '/js/plugins/jquery.magnific-popup.min.js', array( 'jquery' ), '0.9.9' );
			wp_enqueue_script( 'nm-core', $script_path . 'nm-core' . $suffix . '.js', array( 'jquery' ), NM_THEME_VERSION );
			
			
			// Enqueue blog-grid scripts
			if ( isset( $nm_page_includes['blog-grid'] ) )
				wp_enqueue_script( 'packery', NM_THEME_URI . '/js/plugins/packery.pkgd.min.js', array(), '1.3.2', true );
			
			
			// WP comments script
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
			
			
			// Enqueue "Contact form 7" scripts
			if ( isset( $nm_page_includes['contact-form-7'] ) ) {
				wpcf7_enqueue_scripts();
			}
			
			
			if ( nm_woocommerce_activated() ) {
				// Register shop/product scripts
				wp_register_script( 'selectod', NM_THEME_URI . '/js/plugins/selectod.custom.min.js', array( 'jquery' ), '3.8.1' );
				wp_register_script( 'nm-shop-add-to-cart', $script_path . 'nm-shop-add-to-cart' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop', $script_path . 'nm-shop' . $suffix . '.js', array( 'jquery', 'nm-core', 'selectod' ), NM_THEME_VERSION );
				wp_register_script( 'wc-add-to-cart-variation', NM_THEME_URI . '/js/woocommerce/add-to-cart-variation.min.js', array( 'jquery' ), '2.x', true ); // Needed for variation product quick views
				wp_register_script( 'nm-shop-quickview', $script_path . 'nm-shop-quickview' . $suffix . '.js', array( 'jquery', 'nm-shop', 'wc-add-to-cart-variation' ), NM_THEME_VERSION );
				
				
				// Enqueue shop/product scripts
				if ( isset( $nm_page_includes['products'] ) ) {
					wp_enqueue_script( 'selectod' );
					wp_enqueue_script( 'nm-shop-add-to-cart' );
					if ( $nm_theme_options['product_quickview'] ) {
						wp_enqueue_script( 'wc-add-to-cart-variation' ); // Needed for variation product quick views
						wp_enqueue_script( 'nm-shop-quickview' );
					}
				} else if ( isset( $nm_page_includes['wishlist-home'] ) ) {
					wp_enqueue_script( 'nm-shop-add-to-cart' );
				}
				
				
				// Register shop scripts
				wp_register_script( 'nm-shop-infload', $script_path . 'nm-shop-infload' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-filters', $script_path . 'nm-shop-filters' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				wp_register_script( 'nm-shop-search', $script_path . 'nm-shop-search' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
				
				
				// WooCommerce page - Note: Does not include the Cart, Checkout or Account pages
				if ( is_woocommerce() ) {
					// Single product page
					if ( is_product() ) {
						// Single product page: Modal gallery
						if ( $nm_theme_options['product_image_zoom'] ) {
							wp_enqueue_script( 'photoswipe', NM_THEME_URI . '/js/plugins/photoswipe.min.js', array( 'jquery' ), '4.0.0' );
							wp_enqueue_script( 'photoswipe-ui', NM_THEME_URI . '/js/plugins/photoswipe-ui-default.min.js', array( 'jquery' ), '4.0.0' );
						}
						// Single product page: Hover image-zoom
						if ( $nm_globals['product_image_hover_zoom'] ) {
							wp_enqueue_script( 'easyzoom', NM_THEME_URI . '/js/plugins/easyzoom.min.js', array( 'jquery' ), '2.3.0' );
						}
						wp_enqueue_script( 'nm-shop-add-to-cart' );
						wp_enqueue_script( 'nm-shop-single-product', $script_path . 'nm-shop-single-product' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					} 
					// Shop page (except Single product, Cart and Checkout)
					else {
						wp_enqueue_script( 'smartscroll', NM_THEME_URI . '/js/plugins/jquery.smartscroll.min.js', array( 'jquery' ), '1.0' );
						wp_enqueue_script( 'nm-shop-infload' );
						wp_enqueue_script( 'nm-shop-filters' );
						if ( $nm_globals['shop_filters_scrollbar_custom'] ) {
							wp_enqueue_script( 'nm-shop-filters-scrollbar', $script_path . 'nm-shop-filters-scrollbar' . $suffix . '.js', array( 'jquery', 'nm-shop-filters' ), NM_THEME_VERSION );
						}
						wp_enqueue_script( 'nm-shop-search' );
					}
				} else {
					// Cart page
					if ( is_cart() ) {
						wp_enqueue_script( 'nm-shop-cart', $script_path . 'nm-shop-cart' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					} 
					// Checkout page
					else if ( is_checkout() ) {
						wp_enqueue_script( 'nm-shop-checkout', $script_path . 'nm-shop-checkout' . $suffix . '.js', array( 'jquery', 'nm-shop' ), NM_THEME_VERSION );
					}
					// Account page
					else if ( is_account_page() ) {
						wp_enqueue_script( 'nm-shop-myaccount', $script_path . 'nm-shop-myaccount' . $suffix . '.js', array( 'jquery' ), NM_THEME_VERSION );
					}
				}
			}
			
			
			// Add local Javascript variables
			$local_js_vars = array(
				'themeDir' 				=> NM_THEME_DIR,
				'themeUri' 				=> NM_THEME_URI,
				'ajaxUrl' 				=> admin_url( 'admin-ajax.php' ),
				'searchUrl'				=> home_url( '?s=' ),
				'shopFiltersAjax'		=> isset( $_GET['ajax_filters'] ) ? esc_attr( $_GET['ajax_filters'] ) : esc_attr( $nm_theme_options['shop_filters_enable_ajax'] ),
				'shopFilterScrollbars'	=> ( $nm_globals['shop_filters_scrollbar_custom'] ) ? 1 : 0,
				'shopImageLazyLoad'		=> intval( $nm_theme_options['product_image_lazy_loading'] ),
				'shopScrollOffset' 		=> intval( $nm_theme_options['shop_scroll_offset'] ),
				'shopSearch'			=> esc_attr( $nm_globals['shop_search_layout'] ),
				'shopSearchMinChar'		=> intval( $nm_theme_options['shop_search_min_char'] ),
				'shopAjaxAddToCart'		=> ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' && get_option( 'woocommerce_cart_redirect_after_add' ) == 'no' ) ? 1 : 0
			);
    		wp_localize_script( 'nm-core', 'nm_wp_vars', $local_js_vars );
		}
	}
	add_action( 'wp_footer', 'nm_scripts' ); // Add footer scripts
	
	
	
	/* Admin Assets
	=============================================================== */
	
	function nm_admin_assets( $hook ) {
		// Styles
		wp_enqueue_style( 'nm-admin-styles', NM_URI . '/assets/css/nm-wp-admin.css', array(), NM_THEME_VERSION, 'all' );
		
		// Widgets page
		if ( 'widgets.php' == $hook ) {
			wp_enqueue_style( 'wp-color-picker' );
			
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'nm-wp-color-picker', NM_URI . '/assets/js/nm-color-picker-init.js', array( 'jquery' ), false );
		}
	}
	add_action( 'admin_enqueue_scripts', 'nm_admin_assets' ); // Admin assets
	
	
	
	/* Web fonts
	=============================================================== */
	
	global $webfont_status;
	$webfont_status = array( 'typekit' => false, 'fontdeck' => false );
	
	/* Web fonts: Enqueue scripts */
	function nm_webfonts() {
		global $nm_theme_options, $webfont_status;
		
		// Fontdeck
		if ( $nm_theme_options['main_font_source'] === '3' && isset( $nm_theme_options['fontdeck_project_id'] ) ) {
			$webfont_status['fontdeck'] = true;
		} else {
			// Typekit: Main font kit
			if ( $nm_theme_options['main_font_source'] === '2' && isset( $nm_theme_options['main_font_typekit_kit_id'] ) ) {
				$webfont_status['typekit'] = true;
				wp_enqueue_script( 'nm_typekit_main', '//use.typekit.net/' . esc_attr( $nm_theme_options['main_font_typekit_kit_id'] ) . '.js' );
			}
			
			// Typekit: Secondary font kit
			if ( $nm_theme_options['secondary_font_source'] === '2' && isset( $nm_theme_options['secondary_font_typekit_kit_id'] ) ) {
				// Make sure typekit kit-id's are different (no need to include the same typekit file for both fonts)
				if ( $nm_theme_options['secondary_font_typekit_kit_id'] !== $nm_theme_options['main_font_typekit_kit_id'] ) {
					$webfont_status['typekit'] = true;
					wp_enqueue_script( 'nm_typekit_secondary', '//use.typekit.net/' . esc_attr( $nm_theme_options['secondary_font_typekit_kit_id'] ) . '.js' );
				}
			}
		}
	};
	add_action( 'wp_enqueue_scripts', 'nm_webfonts' );
	
	
	/* Web fonts: Add inline scripts */
	function nm_webfonts_inline() {
		global $webfont_status, $nm_theme_options;
		
		if ( $webfont_status['typekit'] ) {
			//if ( wp_script_is( 'nm_typekit_main', 'done' ) ) {
			echo "\n" . '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>';
			//}
		} else if ( $webfont_status['fontdeck'] ) {
			echo "\n" . "<script type='text/javascript'>WebFontConfig={fontdeck:{id:'" . $nm_theme_options['fontdeck_project_id'] . "'}};(function(){var wf=document.createElement('script');wf.src=('https:'==document.location.protocol?'https':'http')+'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';wf.type='text/javascript';wf.async='true';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(wf,s);})();</script>";
		}
	};
	add_action( 'wp_head', 'nm_webfonts_inline' );
	
	
	
	/* Redux Framework
	=============================================================== */
	
	/* Remove redux sub-menu from "Tools" admin menu */
	function nm_remove_redux_menu() {
		remove_submenu_page( 'tools.php', 'redux-about' );
	}
	add_action( 'admin_menu', 'nm_remove_redux_menu', 12 );
	
	
	
	/* Theme Setup
	=============================================================== */
	
	if ( $nm_theme_options['custom_title'] ) {
		/* Page title helper: Build title string with site description */
		function nm_build_description_title( $site_title, $sep ) {
			$site_description = get_bloginfo( 'description', 'display' );
			return "$site_title $sep $site_description";
		};
		/* Page title */
		if ( ! function_exists( 'nm_wp_title' ) ) {
			function nm_wp_title( $title, $sep ) {
				if ( is_feed() ) {
					return $title;
				}
			
				$site_title = get_bloginfo( 'name' );
				
				// Default homepage
				if ( is_front_page() && is_home() ) {
					$title = nm_build_description_title( $site_title, $sep );
				} 
				// Static homepage
				elseif ( is_front_page() ) {
					$title = nm_build_description_title( $site_title, $sep );
				} 
				// Blog page
				elseif ( is_home() ) {
					$title .= nm_build_description_title( $site_title, $sep ); // Note: Using ".="
				}
				// Everything else
				else {
					$title .= $site_title;
				}
				
				return $title;
			}
		}
		add_filter( 'wp_title', 'nm_wp_title', 10, 2 );
	}
	
	
	/* Front-end WordPress admin bar */
	if ( ! $nm_theme_options['wp_admin_bar'] ) {
		function nm_remove_admin_bar() {		
			return false;
		}
		add_filter( 'show_admin_bar', 'nm_remove_admin_bar' );
	}
	
		
	/* Register menus */
	if ( ! function_exists( 'nm_register_menus' ) ) {
		function nm_register_menus() {
			register_nav_menus( array(
				'top-bar-menu'	=> __( 'Top Bar Menu', 'nm-framework' ),
				'main-menu'		=> __( 'Main Menu', 'nm-framework' ),
				'right-menu'	=> __( 'Right Menu', 'nm-framework' ),
				'footer-menu'	=> __( 'Footer Menu', 'nm-framework' )
			) );
		}
	}
	add_action( 'init', 'nm_register_menus' ); // Register menus
	
	
	/*
	 *	Disable emoji icons
	 * 	Source: https://wordpress.org/plugins/disable-emojis/
	 */
	if ( ! function_exists( 'nm_disable_emojis' ) ) {
		function nm_disable_emojis() {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );	
			
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			
			add_filter( 'tiny_mce_plugins', 'nm_disable_emojis_tinymce' );
		}
	}
	/* Filter function: Remove TinyMCE emoji plugin */
	function nm_disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	// Hook: Disable emoji icons
	add_action( 'init', 'nm_disable_emojis' );
	
	
	/* Set number of posts to display in search results */
	/*function nm_wp_search_size( $query ) {
		if ( $query->is_search ) {
			$post_per_page = get_option( 'posts_per_page' );
			$query->query_vars['posts_per_page'] = ( $post_per_page > 10 ) ? $post_per_page : 10;
		}
		
		return $query; // Return our modified query variables
	}
	add_filter( 'pre_get_posts', 'nm_wp_search_size' ); // Hook our custom function onto the request filter*/
	
	
	/* Comments callback */
	function nm_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php esc_html_e( 'Pingback:', 'nm-framework' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( 'Edit', 'nm-framework' ), ' ' ); ?></p>
		<?php
			break;
			default :
		?>
		<li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
            <div class="comment-inner-wrap">
            	<?php if ( function_exists( 'get_avatar' ) ) { echo get_avatar( $comment, '60' ); } ?>
                
				<div class="comment-text">
                    <p class="meta">
                        <strong itemprop="author"><?php printf( '%1$s', get_comment_author_link() ); ?></strong>
                        <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php printf( esc_html__( '%1$s at %2$s', 'nm-framework' ), get_comment_date(), get_comment_time() ); ?></time>
                    </p>
                
                    <div itemprop="description" class="description entry-content">
                        <?php if ( $comment->comment_approved == '0' ) : ?>
                            <p class="moderating"><em><?php esc_html_e( 'Your comment is awaiting moderation', 'nm-framework' ); ?></em></p>
                        <?php endif; ?>
                        
                        <?php comment_text(); ?>
                    </div>
                    
                    <div class="reply">
                        <?php 
                            edit_comment_link( esc_html__( 'Edit', 'nm-framework' ), '<span class="edit-link">', '</span><span> &nbsp;-&nbsp; </span>' );
                            
                            comment_reply_link( array_merge( $args, array(
                                'depth' 	=> $depth,
                                'max_depth'	=> $args['max_depth']
                            ) ) );
                        ?>
                    </div>
                </div>
            </div>
		<?php
			break;
		endswitch;
	}
	
	
	
	/* Blog
	=============================================================== */
	
	/* Post excerpt brackets - [...] */
	function nm_excerpt_read_more( $excerpt ) {
		$excerpt_more = '&hellip;';
		$trans = array(
			'[&hellip;]' => $excerpt_more // WordPress >= v3.6
		);
		
		return strtr( $excerpt, $trans );
	}
	add_filter( 'wp_trim_excerpt', 'nm_excerpt_read_more' );
	
	
	/* Blog categories menu */
	function nm_blog_category_menu() {
		global $wp_query, $nm_theme_options;

		$current_cat = ( is_category() ) ? $wp_query->queried_object->cat_ID : '';
		
		$args = array(
			'type'			=> 'post',
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> 0,
			'hierarchical'	=> 1,
			'taxonomy'		=> 'category'
		); 
		
		$categories = get_categories( $args );
		
		$current_class_set = false;
		$categories_output = '';
		
		// Categories menu divider
		$categories_menu_divider = apply_filters( 'nm_blog_categories_divider', '<span>&frasl;</span>' );
		
		foreach ( $categories as $category ) {
			if ( $current_cat == $category->cat_ID ) {
				$current_class_set = true;
				$current_class = ' class="current-cat"';
			} else {
				$current_class = '';
			}
			$category_link = get_category_link( $category->cat_ID );
			
			$categories_output .= '<li' . $current_class . '>' . $categories_menu_divider . '<a href="' . esc_url( $category_link ) . '">' . esc_attr( $category->name ) . '</a></li>';
		}
		
		$categories_count = count( $categories );
		
		// Categories layout classes
		$categories_class = ' toggle-' . $nm_theme_options['blog_categories_toggle'];
		if ( $nm_theme_options['blog_categories_layout'] === 'columns' ) {
			$column_small = ( intval( $nm_theme_options['blog_categories_columns'] ) > 4 ) ? '3' : '2';
			$categories_ul_class = 'columns small-block-grid-' . $column_small . ' medium-block-grid-' . $nm_theme_options['blog_categories_columns'];
		} else {
			$categories_ul_class = $nm_theme_options['blog_categories_layout'];
		}
		
		// "All" category class attr
		$current_class = ( $current_class_set ) ? '' : ' class="current-cat"';
		
		$output = '<div class="nm-blog-categories-wrap ' . esc_attr( $categories_class ) . '">';
		$output .= '<ul class="nm-blog-categories-toggle"><li><a href="#" id="nm-blog-categories-toggle-link">' . esc_html__( 'Categories', 'nm-framework' ) . '</a> <em class="count">' . $categories_count . '</em></li></ul>';
		$output .= '<ul id="nm-blog-categories-list" class="nm-blog-categories-list ' . esc_attr( $categories_ul_class ) . '"><li' . $current_class . '><a href="' . esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) . '">' . esc_html__( 'All', 'nm-framework' ) . '</a></li>' . $categories_output . '</ul>';
		$output .= '</div>';
		
		return $output;
	}
	
	
	/* Blog slider */
	function nm_get_blog_slider( $post_id, $image_size ) {
		$slider = get_post_gallery( $post_id, false );
		
		if ( $slider ) {
			nm_add_page_include( 'blog-slider' );
						
			$slider_id = "nm-blog-slider-{$post_id}";
			$image_ids = explode( ',', $slider['ids'] );
			$post_permalink = get_permalink();
			
			$slider = "<div id='$slider_id' class='nm-blog-slider slick-slider slick-controls-gray slick-dots-inside slick-dots-centered slick-dots-active-small'>";
		
			foreach ( $image_ids as $image_id ) {
				$image_src = wp_get_attachment_image_src( $image_id, $image_size );
				$slider .= '<div><a href="' . esc_url( $post_permalink ) . '"><img src="' . esc_url( $image_src[0] ) . '" width="' . esc_attr( $image_src[1] ) . '" height="' . esc_attr( $image_src[2] ) . '" /></a></div>';
			}
					
			$slider .= "</div>\n";
		}
		
		return $slider;
	}
	
	
	/* 
	 *	WP gallery (override via action)
	 *	Note: Code inside "// WP default" comments is located in: "../wp-includes/media.php" ("gallery_shortcode()" function)
	 */
	function nm_wp_gallery( $val, $attr ) {
		nm_add_page_include( 'blog-slider' );
		
		// WP default
		$post = get_post();
		
		static $instance = 0;
		$instance++;
		// /WP default
		
		$atts = shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => '',
			'icontag'    => '',
			'captiontag' => '',
			'columns'    => 2,
			'size'       => 'blog-list',
			'include'    => '',
			'exclude'    => '',
			'link'       => ''
		), $attr, 'gallery' );
		
		// WP default
		$id = intval( $atts['id'] );
	
		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	
			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}
	
		if ( empty( $attachments ) ) {
			return '';
		}
	
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
			}
			return $output;
		}
		// /WP default
		
		$gallery_id = "nm-wp-gallery-{$instance}";
		$slider_settings_data = ' data-slides-to-show="' . intval( $atts['columns'] ) . '"';
		
		$output = "<div id='$gallery_id' class='nm-blog-slider slick-slider slick-controls-gray slick-dots-inside'" . $slider_settings_data . ">";
		
		foreach ( $attachments as $id => $attachment ) {
			$image_src = wp_get_attachment_image_src( $id, $atts['size'] );
			$output .= '<div><img src="' . esc_url( $image_src[0] ) . '" width="' . esc_attr( $image_src[1] ) . '" height="' . esc_attr( $image_src[2] ) . '" /></div>';
		}
				
		$output .= "</div>\n";
	
		return $output;
	}
		
	/* WP gallery: Set page include value */
	function nm_wp_gallery_set_include() {
		nm_add_page_include( 'wp-gallery' );
		
		return ''; // Returning an empty string will output the default WP gallery
	}
	
	if ( $nm_theme_options['custom_wp_gallery'] ) {
		add_filter( 'post_gallery', 'nm_wp_gallery', 10, 2 );
	} else {
		add_filter( 'post_gallery', 'nm_wp_gallery_set_include' );
	}
	
	
	
	/* Sidebars & Widgets
	=============================================================== */
	
	/* Register/include sidebars & widgets */
	function nm_widgets_init() {
		global $nm_globals, $nm_theme_options;
		
		// Sidebar: Default
		register_sidebar( array(
			'name' 				=> __( 'Sidebar', 'nm-framework' ),
			'id' 				=> 'sidebar',
			'before_widget'		=> '<div id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</div>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
		
		
		// Sidebar: Shop
		if ( $nm_globals['shop_filters_scrollbar'] ) {
			register_sidebar( array(
				'name' 				=> __( 'Shop', 'nm-framework' ),
				'id' 				=> 'widgets-shop',
				'before_widget'		=> '<li id="%1$s" class="scroll-enabled scroll-type-' . esc_attr( $nm_theme_options['shop_filters_scrollbar'] ) . ' widget %2$s">',
				'after_widget' 		=> '</div></div></li>',
				'before_title' 		=> '<h3 class="nm-widget-title">',
				'after_title' 		=> '</h3><div class="nm-shop-widget-content"><div class="nm-shop-widget-scroll">'
			));
			
			/* Sidebar: Shop - Add opening "div" wrapper to widgets with no title */
			function nm_shop_widgets_empty_title_fix( $params ) {
				// Make sure widget is in the "Shop" sidebar
				if ( $params[0]['id'] === 'widgets-shop' ) {
					global $wp_registered_widgets;
					
					// Get widget settings
					$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
					$settings = $settings_getter->get_settings();
					$settings = $settings[ $params[1]['number'] ];
					
					// Check if widget title is empty
					if ( isset( $settings['title'] ) && empty( $settings['title'] ) ) {	
						// Append opening wrapper element
						$params[0]['before_widget'] .= '<div class="nm-shop-widget-content"><div class="nm-shop-widget-scroll">';
					}
				}
				
				return $params;
			}
			add_filter( 'dynamic_sidebar_params', 'nm_shop_widgets_empty_title_fix' );
		} else {
			register_sidebar( array(
				'name' 				=> __( 'Shop', 'nm-framework' ),
				'id' 				=> 'widgets-shop',
				'before_widget'		=> '<li id="%1$s" class="widget %2$s">',
				'after_widget' 		=> '</li>',
				'before_title' 		=> '<h3 class="nm-widget-title">',
				'after_title' 		=> '</h3>'
			) );
		}
		
		
		// Sidebar: Footer
		register_sidebar( array(
			'name' 				=> __( 'Footer', 'nm-framework' ),
			'id' 				=> 'footer',
			'before_widget'		=> '<li id="%1$s" class="widget %2$s">',
			'after_widget' 		=> '</li>',
			'before_title' 		=> '<h3 class="nm-widget-title">',
			'after_title' 		=> '</h3>'
		) );
		
		
		// Custom WooCommerce widgets
		// NOTE: The custom WooCommerce -filter- widgets will not work without the widget-id fix (see "nm_add_woocommerce_widget_ids()" below)
		if ( class_exists( 'WC_Widget' ) ) {
			// Product sorting
			include_once( NM_DIR . '/widgets/woocommerce-product-sorting.php' );
			register_widget( 'NM_WC_Widget_Product_Sorting' );
			
			// Price filter list
			include_once( NM_DIR . '/widgets/woocommerce-price-filter.php' );
			register_widget( 'NM_WC_Widget_Price_Filter' );
			
			// Color filter list
			include_once( NM_DIR . '/widgets/woocommerce-color-filter.php' );
			register_widget( 'WC_Widget_Color_Filter' );
		}
		
		
		// Unregister widgets
		unregister_widget( 'WC_Widget_Cart' );
		unregister_widget( 'WC_Widget_Price_Filter' );
	}
	add_action( 'widgets_init', 'nm_widgets_init' ); // Register widget sidebars
	
	/* 
	 *	Add relevant WooCommerce widget-id's to "sidebars_widgets" option so the custom product filters will work
	 *
	 * 	Note: WooCommerce use "is_active_widget()" to check for active widgets in: "../includes/class-wc-query.php"
	 */
	function nm_add_woocommerce_widget_ids( $sidebars_widgets, $old_sidebars_widgets = array() ) {
		$shop_sidebar_id = 'widgets-shop';
		$shop_widgets = $sidebars_widgets[$shop_sidebar_id];
		
		if ( is_array( $shop_widgets ) ) {
			foreach ( $shop_widgets as $widget ) {
				$widget_id = _get_widget_id_base( $widget );
				
				if ( $widget_id === 'nm_woocommerce_price_filter' ) {
					$sidebars_widgets[$shop_sidebar_id][] = 'woocommerce_price_filter-12345';
				} else if ( $widget_id === 'nm_woocommerce_color_filter' ) {
					$sidebars_widgets[$shop_sidebar_id][] = 'woocommerce_layered_nav-12345';
				}
			}
		}
		
		return $sidebars_widgets;
	}
	add_action( 'pre_update_option_sidebars_widgets', 'nm_add_woocommerce_widget_ids' );
	
	/*function nm_check_sidebars_array() {
		global $sidebars_widgets;
		echo '<pre>';
		var_dump( $sidebars_widgets['widgets-shop'] );
		echo '</pre>';
	}
	add_action( 'init', 'nm_check_sidebars_array' );*/
	
	
	/* Page includes: Include element */
	function nm_include_page_includes_element() {
		global $nm_page_includes;
		
		$classes = '';
		
		foreach ( $nm_page_includes as $class => $value )
			$classes .= $class . ' ';
		
		echo '<div id="nm-page-includes" class="' . esc_attr( $classes ) . '" style="display:none;">&nbsp;</div>' . "\n\n";
	}
	add_action( 'wp_footer', 'nm_include_page_includes_element' ); // Include "page includes" element
	
	
	
	/* Actions & Filters
	=============================================================== */
	
	// Add Filters
	add_filter( 'widget_text', 'do_shortcode' ); 					// Allow shortcodes in text-widgets
	add_filter( 'widget_text', 'shortcode_unautop' ); 				// Disable auto-formatting (line breaks) in text-widgets
	add_filter( 'the_excerpt', 'shortcode_unautop' ); 				// Remove auto <p> tags in Excerpt (Manual Excerpts only)
	//add_filter( 'the_excerpt', 'do_shortcode' ); 					// Allow shortcodes in excerpts
	add_filter( 'use_default_gallery_style', '__return_false' );	// Remove default inline WP gallery styles
	
	// Add Filters: Contact form 7
	add_filter( 'wpcf7_load_css', '__return_false' );	// Disable CF7 styles
	add_filter( 'wpcf7_load_js', '__return_false' ); 	// Disable CF7 JavaScript (included via custom shortcode instead)