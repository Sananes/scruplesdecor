<?php

	/* Visual Composer: Initialize
	================================================== */
	
	if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
		global $nm_vcomp_stock;
		
		
		// Framework VC directory
		define( 'NM_VC_DIR', NM_DIR . '/visual-composer/' );
		
		
		// Enable 'theme mode' (disables plugin update message)
		if ( function_exists( 'vc_set_as_theme' ) ) {
			vc_set_as_theme( true );
		}
		
		
		// Disable frontend editor
		if ( ! defined( 'NM_VCOMP_ENABLE_FRONTEND' ) ) {
			vc_disable_frontend();
		}
		
		
		/* Frontend assets */
		if ( ! $nm_vcomp_stock ) {
			function nm_vc_frontend_assets() {
				// Deregister styles
				wp_deregister_style( 'js_composer_front' );
				wp_deregister_style( 'font-awesome' );
				
				// Deregister scripts
				wp_deregister_script( 'wpb_composer_front_js' );
				
				// Disable custom WooCommerce add-to-cart script (action is located in: "../js_composer/include/autoload/vendors/woocommerce.php)
				remove_action( 'wp_enqueue_scripts', 'vc_woocommerce_add_to_cart_script' );
				
				// Disable "enqueueStyle()" function (looks through the content for "vc_row" elements using "preg_match()")
				remove_action( 'wp_enqueue_scripts', array( visual_composer(), 'enqueueStyle' ) ); // "enqueueStyle()" is located in: "../js_composer/include/classes/core/class-vc-base.php"
				
				
				// Enqueue frontend styles (original stylesheet with unused styles removed)
				wp_enqueue_style( 'nm_js_composer_front', NM_THEME_URI . '/css/third-party/nm-js_composer.css', array(), '1.0', 'all' );
				
				// Enqueue frontend scripts (original file with unused scripts removed)
				wp_enqueue_script( 'nm_composer_front_js', NM_THEME_URI . '/js/plugins/nm-js_composer_front.min.js', array( 'jquery' ), '1.0', true );
			}
			add_action( 'wp_enqueue_scripts', 'nm_vc_frontend_assets', 1 );
		}
		
		
		// Set element template files directory
		$vc_element_templates_dir = NM_VC_DIR . '/element-templates/';
		vc_set_shortcodes_templates_dir( $vc_element_templates_dir );
		
		
		// Check if "CF7" is enabled
		global $nm_cf7_enabled;
		$nm_cf7_enabled = ( defined( 'WPCF7_PLUGIN' ) || is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ? true : false;
		
		
		/* Include external shortcodes */
		function nm_vc_register_shortcodes() {
			global $nm_cf7_enabled;
			
			include( NM_VC_DIR . '/shortcodes/banner.php' );
			include( NM_VC_DIR . '/shortcodes/banner-slider.php' );
			include( NM_VC_DIR . '/shortcodes/button.php' );
			if ( $nm_cf7_enabled ) { include( NM_VC_DIR . '/shortcodes/contact-form-7.php' ); }
			include( NM_VC_DIR . '/shortcodes/feature-box.php' );
			include( NM_VC_DIR . '/shortcodes/google-map.php' );
			include( NM_VC_DIR . '/shortcodes/lightbox.php' );
			include( NM_VC_DIR . '/shortcodes/post-slider.php' );
			include( NM_VC_DIR . '/shortcodes/social-profiles.php' );
			include( NM_VC_DIR . '/shortcodes/testimonial.php' );
			
			if ( nm_woocommerce_activated() ) {
				// Include external WooCommerce shortcodes
				include( NM_VC_DIR . '/shortcodes/woocommerce/product-categories.php' );
				//todo: include( NM_VC_DIR . '/shortcodes/woocommerce/product-slider.php' );
			}
		}
		add_action( 'init', 'nm_vc_register_shortcodes' );
		
		
		if ( is_admin() ) {
			// Page templates
			include( NM_VC_DIR . '/page-templates.php' );
			
			
			/* Include external elements */
			function nm_vc_register_elements() {
				global $nm_cf7_enabled;
				
				include( NM_VC_DIR . '/elements/banner.php' );
				include( NM_VC_DIR . '/elements/banner-slider.php' );
				include( NM_VC_DIR . '/elements/button.php' );
				if ( $nm_cf7_enabled ) { include( NM_VC_DIR . '/elements/contact-form-7.php' ); }
				include( NM_VC_DIR . '/elements/feature-box.php' );
				include( NM_VC_DIR . '/elements/google-map.php' );
				include( NM_VC_DIR . '/elements/lightbox.php' );
				include( NM_VC_DIR . '/elements/post-slider.php' );
				include( NM_VC_DIR . '/elements/social-profiles.php' );
				include( NM_VC_DIR . '/elements/testimonial.php' );
				
				if ( nm_woocommerce_activated() ) {
					// Include external WooCommerce elements
					include( NM_VC_DIR . '/elements/woocommerce/product-categories.php' );
					//todo: include( NM_VC_DIR . '/elements/woocommerce/product-slider.php' );
				}
			}
			add_action( 'vc_build_admin_page', 'nm_vc_register_elements' ); // Note: Using the "vc_build_admin_page" action so external elements are added before default WooCommerce elements
			
			
			// Include elements configuration
			include( NM_DIR . '/visual-composer/elements-config.php' );
			
			
			/* Make elements "un-deprecated" */
			if ( ! $nm_vcomp_stock ) {
				function nm_vc_undeprecate_elements() {
					vc_map_update( 'vc_tabs', array( 'deprecated' => false ) );
					vc_map_update( 'vc_tour', array( 'deprecated' => false ) );
					vc_map_update( 'vc_accordion', array( 'deprecated' => false ) );
				}
				add_action( 'init', 'nm_vc_undeprecate_elements' );
			}
			
			
			// Include custom params
			include( NM_VC_DIR . '/params/iconpicker.php' );
			
			
			if ( ! $nm_vcomp_stock ) {
				if ( nm_woocommerce_activated() ) {
					/* Remove default WooCommerce elements */
					function nm_vc_remove_woocommerce_elements() {
						vc_remove_element( 'woocommerce_cart' );
						vc_remove_element( 'woocommerce_checkout' );
						vc_remove_element( 'woocommerce_my_account' );
						vc_remove_element( 'product' );
						vc_remove_element( 'product_page' );
						vc_remove_element( 'product_categories' );
					}
					add_action( 'vc_build_admin_page', 'nm_vc_remove_woocommerce_elements', 11 ); // Hook for admin editor
					add_action( 'vc_load_shortcode', 'nm_vc_remove_woocommerce_elements', 11 ); // Hook for frontend editor
				}
			}
			
			
			/* Remove admin menus */
			if ( ! $nm_vcomp_stock ) {
				function nm_vc_remove_admin_menus() {
					remove_submenu_page( 'vc-general', 'vc-automapper' );
					remove_submenu_page( 'vc-general', 'edit.php?post_type=vc_grid_item' );
					//remove_submenu_page( 'vc-general', 'vc-welcome' ); // Note: Don't disable, this page is displayed after plugin activation (blank page with permission message displaying otherwise)
				}
				add_action( 'admin_menu', 'nm_vc_remove_admin_menus', 1000 );
			}
			
												
			// Disable shortcode automapper feature
			if ( ! $nm_vcomp_stock ) {
				if ( function_exists( 'vc_automapper' ) ) {
					vc_automapper()->setDisabled( true );
				}
			}
			
			
			/* Remove "vc_teaser" metabox */
			function nm_vc_remove_teaser_metabox() {
				remove_meta_box( 'vc_teaser', '', 'side' );
			}
			add_action( 'admin_head', 'nm_vc_remove_teaser_metabox' );
			
			
			// Set default editor post types (will not be used if the "content_types" VC setting is already saved - see fix below)
			$post_types = array(
				'page'
			);
			vc_set_default_editor_post_types( $post_types );
			
			// Default editor post types: Un-comment and refresh WP admin to save/reset the "content_types" VC option
			// NOTE: Remember to comment-out after page refresh!
			//vc_settings()->set( 'content_types', $post_types );
		}
		
		
		/* Remove header meta tag */
		function nm_vc_remove_meta() {
			remove_action( 'wp_head', array( visual_composer(), 'addMetaData' ) );
		}
		add_action( 'init', 'nm_vc_remove_meta', 100 );
		
		
		/*
		 * VC: Output custom styles - Visual Composer doesn't output custom styles on non-singular pages (like blog-index and shop archives)
		 *
		 * See "addFrontCss()" in "../js_composer/include/classes/core/class-vc-base.php"
		 */
		function nm_vc_addFrontCss( $page_id ) {
			// Get custom styles from the post meta (returns empty strings if no results)
			$post_custom_css = get_post_meta( $page_id, '_wpb_post_custom_css', true );
			$shortcodes_custom_css = get_post_meta( $page_id, '_wpb_shortcodes_custom_css', true );
							
			if ( $post_custom_css != '' || $shortcodes_custom_css != '' ) {
				echo '<style type="text/css" class="nm-vc-styles">' . $post_custom_css . $shortcodes_custom_css . '</style>';
			}
		}
		
		
		/* Shop: Output custom styles for page content on shop archives */
		function nm_shop_vc_styles() {
			if ( is_shop() || is_product_taxonomy() ) {
				global $nm_globals;
				
				nm_vc_addFrontCss( $nm_globals['shop_page_id'] );
			}
		}
		if ( nm_woocommerce_activated() ) {
			add_action( 'wp_head', 'nm_shop_vc_styles', 1000 );
		}
		
		
		/* Blog: Output custom styles for page content on blog index archive */
		function nm_blog_index_vc_styles() {
			global $nm_theme_options;
			
			nm_vc_addFrontCss( $nm_theme_options['blog_static_page_id'] );
		}
		
	}
