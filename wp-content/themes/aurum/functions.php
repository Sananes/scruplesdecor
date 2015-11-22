<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

# Constants
define('THEMEDIR', 		get_template_directory() . '/');
define('THEMEURL', 		get_template_directory_uri() . '/');
define('THEMEASSETS',	THEMEURL . 'assets/');
define('TD', 			'aurum');


# Theme Content Width
$content_width = ! isset($content_width) ? 1170 : $content_width;


# Initial Actions
add_action('after_setup_theme', 	'laborator_after_setup_theme');
add_action('init', 					'laborator_init');

add_action('widgets_init', 			'laborator_widgets_init');

add_action('wp_head', 				'laborator_favicon');
add_action('wp_enqueue_scripts', 	'laborator_wp_enqueue_scripts');
add_action('wp_enqueue_scripts', 	'laborator_wp_head');
add_action('wp_print_scripts', 		'laborator_wp_print_scripts');

add_action('admin_print_styles', 	'laborator_admin_print_styles');
add_action('admin_menu', 			'laborator_menu_page');
add_action('admin_menu', 			'laborator_menu_documentation', 100);
add_action('admin_enqueue_scripts', 'laborator_admin_enqueue_scripts');

add_action('wp_footer', 			'laborator_wp_footer');


# Core Files
require 'inc/lib/smof/smof.php';
require 'inc/laborator_actions.php';
require 'inc/laborator_filters.php';
require 'inc/laborator_functions.php';
require 'inc/laborator_woocommerce.php';
require 'inc/acf-fields.php';


# Library
require 'inc/lib/laborator/laborator_gallerybox.php';
require 'inc/lib/laborator/laborator_custom_css.php';
require 'inc/lib/class-tgm-plugin-activation.php';

if(is_admin())
	require 'inc/lib/laborator/laborator-demo-content-importer/laborator_demo_content_importer.php';


# Thumbnails
$blog_thumbnail_height      = get_data('blog_thumbnail_height');
$blog_thumbnail_height      = is_numeric($blog_thumbnail_height) && $blog_thumbnail_height > 100 ? $blog_thumbnail_height : 640;

$shop_catalog_image_size    = explode("x", get_data('shop_catalog_image_size'));
$shop_catalog_image_size	= count($shop_catalog_image_size) == 2 ? $shop_catalog_image_size : array(290, 370);

$shop_single_image_size     = explode("x", get_data('shop_single_image_size'));
$shop_single_image_size		= count($shop_single_image_size) == 2 ? $shop_single_image_size : array(555, 710);


add_image_size('post-thumb-big', 1140, $blog_thumbnail_height, true);
add_image_size('shop-thumb', $shop_catalog_image_size[0], $shop_catalog_image_size[1], true);
add_image_size('shop-thumb-2', 70, 90, true);
add_image_size('shop-thumb-main', $shop_single_image_size[0], $shop_single_image_size[1], true);
add_image_size('shop-category-thumb', 320, 256, true);
