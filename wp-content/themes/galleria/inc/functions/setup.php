<?php
/**
 * Galleria setup functions
 *
 * @package galleria
 */

/**
 * Assign the Galleria version to a var
 */
$theme 		= wp_get_theme( 'galleria' );
$g_version 	= $theme['Version'];

/**
 * Enqueue Storefront Styles
 * @return void
 */
function g_enqueue_styles() {
	global $storefront_version;

    wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
}

/**
 * Enqueue Bootique Styles
 * @return void
 */
function g_enqueue_child_styles() {
	global $g_version;

    wp_enqueue_style( 'g-style', get_stylesheet_uri(), array( 'storefront-style' ), $g_version );
    wp_enqueue_style( 'karla', '//fonts.googleapis.com/css?family=Karla:400,700', array( 'g-style' ) );
    wp_enqueue_style( 'libre-baskerville', '//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic', array( 'g-style' ) );

    wp_enqueue_script( 'modernizr', get_stylesheet_directory_uri() . '/js/modernizr.min.js', array( 'jquery' ), '2.8.3' );
    wp_enqueue_script( 'galleria', get_stylesheet_directory_uri() . '/js/galleria.min.js', array( 'jquery' ), '1.0' );
    wp_enqueue_script( 'masonry', array( 'jquery' ) );
}

/**
 * Shop columns
 * @return int number of columns
 */
function g_loop_columns( $columns ) {
	$columns = 4;
	return $columns;
}

/**
 * Adjust related products columns
 * @return array $args the modified arguments
 */
function g_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']		= 4;

	return $args;
}