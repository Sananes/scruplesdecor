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
add_action('after_setup_theme', 	'laborator_after_setup_theme');
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

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
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

if(in_array('js_composer/js_composer.php', apply_filters('active_plugins', get_option('active_plugins'))))
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

// Custom Category

class My_Category_Walker extends Walker_Category {

  var $lev = -1;
  var $skip = 0;
  static $current_parent;

  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $this->lev = 0;
    $output .= "<ul class='sub-menu'>" . PHP_EOL;
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= "</ul>" . PHP_EOL;
    $this->lev = -1;
  }

  function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
    extract($args);
    $cat_name = esc_attr( $category->name );
    $class_current = $current_class ? $current_class . ' ' : 'current ';
    if ( ! empty($current_category) ) {
      $_current_category = get_term( $current_category, $category->taxonomy );
      if ( $category->term_id == $current_category ) $class = $class_current;
      elseif ( $category->term_id == $_current_category->parent ) $class = rtrim($class_current) . '-parent ';
    } else {
      $class = '';
    }
    if ( ! $category->parent ) {
      if ( ! get_term_children( $category->term_id, $category->taxonomy ) ) {
          $this->skip = 1;
      } else {
        if ($class == $class_current) self::$current_parent = $category->term_id;
        $output .= "<li id='main-menu' class='main-menu menu-item " . $class . $level_class . "'>" . PHP_EOL;
        $output .= "<a href='" . esc_url( get_term_link($category) ) . "'>"  .$cat_name . "</a>" . PHP_EOL;
      }
    } else { 
      if ( $this->lev == 0 && $category->parent) {
        $link = get_term_link(intval($category->parent) , $category->taxonomy);
        $stored_parent = intval(self::$current_parent);
        $now_parent = intval($category->parent);
        $all_class = ($stored_parent > 0 && ( $stored_parent === $now_parent) ) ? $class_current . ' all' : 'all';
        self::$current_parent = null;
      }
      $link = '<a href="' . esc_url( get_term_link($category) ) . '" >' . $cat_name . '</a>';
      $output .= "<li";
      $class .= $category->taxonomy . '-item ' . $category->taxonomy . '-item-' . $category->term_id;
      $output .=  ' class="' . $class . '"';
      $output .= ">" . $link;
    }
  }

  function end_el( &$output, $page, $depth = 0, $args = array() ) {
    $this->lev++;
    if ( $this->skip == 1 ) {
      $this->skip = 0;
      return;
    }
    $output .= "</li>" . PHP_EOL;
  }

}

function custom_list_categories( $args = '' ) {
  $defaults = array(
    'taxonomy' => 'category',
    'show_option_none' => '',
    'echo' => 1,
    'depth' => 3,
    'wrap_class' => '',
    'level_class' => '',
    'parent_title_format' => '%s',
    'current_class' => 'current'
  );
  $r = wp_parse_args( $args, $defaults );
  if ( ! isset( $r['wrap_class'] ) ) $r['wrap_class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];
  extract( $r );
  if ( ! taxonomy_exists($taxonomy) ) return false;
  $categories = get_categories( $r );
  $output = "<ul class='" . esc_attr( $wrap_class ) . " nav'>" . PHP_EOL;
  if ( empty( $categories ) ) {
    if ( ! empty( $show_option_none ) ) $output .= "<li>" . $show_option_none . "</li>" . PHP_EOL;
  } else {
    if ( is_category() || is_tax() || is_tag() ) {
      $current_term_object = get_queried_object();
      if ( $r['taxonomy'] == $current_term_object->taxonomy ) $r['current_category'] = get_queried_object_id();
    }
    $depth = $r['depth'];
    $walker = new My_Category_Walker;
    $output .= $walker->walk($categories, $depth, $r);
  }
  $output .= "</ul>" . PHP_EOL;
  if ( $echo ) echo $output; else return $output;
}
