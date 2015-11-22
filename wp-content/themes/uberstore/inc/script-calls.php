<?php

// Main Styles
function main_styles() {	
		 
		 // Register 
		 wp_register_style('foundation', THB_THEME_ROOT . '/assets/css/foundation.css');
		 wp_register_style("app", THB_THEME_ROOT .  "/assets/css/app.css");
		 wp_register_style('selection', THB_THEME_ROOT . '/assets/css/selection.php');
		 wp_register_style("mp", THB_THEME_ROOT . "/assets/css/magnific-popup.css");
		 
		 // Enqueue
		 wp_enqueue_style('foundation');
		 wp_enqueue_style('app');
		 wp_enqueue_style('selection');
		 wp_enqueue_style('mp');
		 wp_enqueue_style('style', get_stylesheet_uri());	
}

add_action('wp_enqueue_scripts', 'main_styles');

// Main Scripts
function register_js() {
	
	if (!is_admin()) {
	
		// Register 
		wp_register_script('modernizr', THB_THEME_ROOT . '/assets/js/modernizr.foundation.js', 'jquery');
		wp_register_script('foundation', THB_THEME_ROOT . '/assets/js/jquery.foundation.plugins.js', 'jquery', null, TRUE);
		wp_register_script('flexslider', THB_THEME_ROOT . '/assets/js/jquery.flexslider-min.js', 'jquery', null, TRUE);
		wp_register_script('isotope', THB_THEME_ROOT . '/assets/js/jquery.isotope.min.js', 'jquery', null, TRUE);
		wp_register_script('easyzoom', THB_THEME_ROOT . '/assets/js/jquery.easyzoom.min.js', 'jquery', null, TRUE);
		wp_register_script('gmapdep', 'http://maps.google.com/maps/api/js?sensor=false', false, null, true);
		wp_register_script('gmap', THB_THEME_ROOT . '/assets/js/jquery.gmap.min.js', 'jquery', null, TRUE);
		wp_register_script('carousel', THB_THEME_ROOT . '/assets/js/jquery.owl.carousel.min.js', 'jquery', null, TRUE);
		wp_register_script('mp', THB_THEME_ROOT . '/assets/js/jquery.magnific-popup.min.js', 'jquery', null, TRUE);
		wp_register_script('parsley', THB_THEME_ROOT . '/assets/js/jquery.parsley.min.js', 'jquery', null, TRUE);
		wp_register_script('countto', THB_THEME_ROOT . '/assets/js/jquery.countTo.min.js', 'jquery', null, TRUE);
		wp_register_script('sharrre', THB_THEME_ROOT . '/assets/js/jquery.sharrre.min.js', 'jquery', null, TRUE);
		wp_register_script('jplayer', THB_THEME_ROOT . '/assets/js/jquery.jplayer.min.js', 'jquery', null, TRUE);
		wp_register_script('favico', THB_THEME_ROOT . '/assets/js/favico-0.3.4.min.js', 'jquery', null, TRUE);
		wp_register_script('packery', THB_THEME_ROOT . '/assets/js/packery.pkgd.min.js', 'jquery', null, TRUE);
		wp_register_script('app', THB_THEME_ROOT . '/assets/js/app.js', 'jquery', null, TRUE);
		
		// Enqueue
		wp_enqueue_script('modernizr');
		wp_enqueue_script('jquery');
		wp_enqueue_script('flexslider');
		wp_enqueue_script('foundation');
		wp_enqueue_script('carousel');
		wp_enqueue_script('icheck');
		wp_enqueue_script('mp');
		wp_enqueue_script('favico');
		wp_enqueue_script('packery');
		wp_enqueue_script('app');
		wp_localize_script( 'app', 'themeajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	}
}
add_action('wp_enqueue_scripts', 'register_js');

// Admin Scripts
function thb_admin_scripts() {
	wp_register_script('thb-admin-meta', THB_THEME_ROOT .'/assets/js/admin-meta.js', array('jquery'));
	wp_enqueue_script('thb-admin-meta');
	
	wp_register_style("thb-admin-css", THB_THEME_ROOT . "/assets/css/admin.css");
	wp_enqueue_style('thb-admin-css'); 
	if (class_exists('WPBakeryVisualComposerAbstract')) {
		wp_enqueue_style( 'vc_extra_css', THB_THEME_ROOT . '/assets/css/vc_extra.css' );
	}
}
add_action('admin_enqueue_scripts', 'thb_admin_scripts');

function single_scripts() {
	if (is_singular('post') || is_singular('portfolio') || is_attachment()) {
		wp_enqueue_script('sharrre');
	}
}
add_action('wp_enqueue_scripts', 'single_scripts');

function isotope_scripts() {
	if (is_page_template('template-portfolio.php') || is_page_template('template-blog-masonry.php')) {
		wp_enqueue_script('isotope');
	}
}
add_action('wp_enqueue_scripts', 'isotope_scripts');


function contact_scripts() {
	if (is_page_template('template-contact.php')) {
		wp_enqueue_script('gmapdep');
		wp_enqueue_script('gmap');
		wp_enqueue_script('parsley');
	}
}
add_action('wp_enqueue_scripts', 'contact_scripts');

/* WooCommerce */
if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function thb_woocommerce_scripts() {
		if (is_woocommerce() && is_product()) {
			wp_enqueue_script('easyzoom');
			wp_enqueue_script('sharrre');
		}
		wp_dequeue_script( 'prettyPhoto' );
		wp_dequeue_script( 'prettyPhoto-init' );
		wp_dequeue_script( 'jquery-blockui' );
		wp_dequeue_script( 'jquery-placeholder' );
		wp_dequeue_script( 'fancybox' );
	}
	add_action('wp_enqueue_scripts', 'thb_woocommerce_scripts');
	
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	} else {
		define( 'WOOCOMMERCE_USE_CSS', false );
	}
}
/* De-register Contact Form 7 styles */
remove_action( 'wp_enqueue_scripts', 'wpcf7_enqueue_styles' );
?>