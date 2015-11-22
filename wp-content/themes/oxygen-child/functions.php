<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


add_action('wp_enqueue_scripts', 'enqueue_childtheme_scripts', 1000);

function enqueue_childtheme_scripts()
{
	wp_enqueue_style('oxygen-child', get_stylesheet_directory_uri() . '/style.css');
}

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