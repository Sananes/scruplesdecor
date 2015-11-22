<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

# Constants
define('THEMEDIR', 		get_template_directory() . '/');
define('THEMEURL', 		get_template_directory_uri() . '/');
define('THEMEASSETS',	THEMEURL . 'assets/');
define('TD', 			'oxygen');
define('STIME',			microtime(true));


# Theme Content Width
$content_width = ! isset($content_width) ? 1170 : $content_width;


# Initial Actions
add_action('after_setup_theme', 	'laborator_after_setup_theme', 100);
add_action('init', 					'laborator_init');
add_action('init', 					'laborator_testimonials_postype');
add_action('widgets_init', 			'laborator_widgets_init');

add_action('wp_enqueue_scripts', 	'laborator_wp_head');
add_action('wp_enqueue_scripts', 	'laborator_wp_enqueue_scripts');
add_action('wp_print_scripts', 		'laborator_wp_print_scripts');
add_action('wp_head', 				'laborator_favicon');
add_action('wp_footer', 			'laborator_wp_footer');

add_action('admin_menu', 			'laborator_menu_page');
add_action('admin_menu', 			'laborator_menu_documentation', 100);
add_action('admin_print_styles', 	'laborator_admin_print_styles');
add_action('admin_enqueue_scripts', 'laborator_admin_enqueue_scripts');

add_action('tgmpa_register', 		'oxygen_register_required_plugins');

if(file_exists(dirname(__FILE__) . "/theme-demo/theme-demo.php"))
{
	require "theme-demo/theme-demo.php";
}


# Core Files
require 'inc/lib/smof/smof.php';
require 'inc/laborator_functions.php';
require 'inc/laborator_classes.php';
require 'inc/laborator_actions.php';
require 'inc/laborator_filters.php';

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || class_exists( 'WooCommerce' ))
	require THEMEDIR . 'inc/laborator_woocommerce.php';


# Library Files
require 'inc/lib/zebra.php';
require 'inc/lib/phpthumb/ThumbLib.inc.php';
require 'inc/lib/class-tgm-plugin-activation.php';
require 'inc/lib/laborator/laborator_image_resizer.php';
require 'inc/lib/laborator/laborator_dataopt.php';
require 'inc/lib/laborator/laborator_tgs.php';
require 'inc/lib/laborator/laborator_gallerybox.php';
require 'inc/lib/laborator/laborator_custom_css.php'; # New in v2.6
require 'inc/lib/laborator/laborator-demo-content-importer/laborator_demo_content_importer.php'; # New in v2.6

if(in_array('js_composer/js_composer.php', apply_filters('active_plugins', get_option('active_plugins'))) && function_exists('vc_add_params'))
{
	require 'inc/lib/visual-composer/config.php';
	#require 'inc/lib/visual-composer/vc-modify.php';
}

require 'inc/lib/widgets/laborator_subscribe.php';


# Advanced Custom Fields
if(function_exists("register_field_group"))
{
	if(in_array('revslider/revslider.php', apply_filters('active_plugins', get_option( 'active_plugins'))))
		require 'inc/lib/acf-revslider/acf-revslider.php';

	require 'inc/acf-fields.php';
}

require 'inc/laborator_data_blocks.php';

# Laborator SEO
if( ! defined("WPSEO_PATH"))
	require 'inc/lib/laborator/laborator_seo.php';


# Thumbnail Sizes
$blog_thumb_height = get_data('blog_thumbnail_height');

laborator_img_add_size('blog-thumb-1', 410, 410, 1);
laborator_img_add_size('blog-thumb-3', 300, 200, 1);
laborator_img_add_size('shop-thumb-1', 325, 390, 4);
laborator_img_add_size('shop-thumb-1-large', 500, 596, 4);
laborator_img_add_size('shop-thumb-2', 90, 110, 4);
laborator_img_add_size('shop-thumb-3', 105, 135, 4);
laborator_img_add_size('shop-thumb-4', 580, 0, 0);
laborator_img_add_size('shop-thumb-5', 135, 160, 4);
laborator_img_add_size('shop-thumb-6', 500, 500, 3);

add_image_size('blog-thumb-1', 410, 410, true);
add_image_size('blog-thumb-3', 300, 200, true);

add_image_size('shop-thumb-1', 325, 390, true);
add_image_size('shop-thumb-1-large', 500, 596, true);
add_image_size('shop-thumb-2', 90, 110, true);
add_image_size('shop-thumb-3', 105, 135, true);
add_image_size('shop-thumb-4', 580, 0);
add_image_size('shop-thumb-5', 135, 160, true);
add_image_size('shop-thumb-6', 500, 500, true);


if($blog_thumb_height)
	laborator_img_add_size('blog-thumb-2', 870, $blog_thumb_height, 1);
else
	laborator_img_add_size('blog-thumb-2', 870, 0, 0);


// Setup Menu Locations Notification
$nav_menu_locations = get_theme_mod('nav_menu_locations');

if( ! isset($nav_menu_locations['main-menu']) || $nav_menu_locations['main-menu'] == 0)
	add_action('admin_notices', 'laborator_setup_menus_notice');

