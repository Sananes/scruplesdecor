<?php
/**
 * General setup hooks and filters
 *
 * @package galleria
 */

/**
 * Add Galleria specific CSS selectors based on customizer settings
 */
add_action( 'wp_enqueue_scripts', 								'g_add_customizer_css', 1000 );

/**
 * Customizer controls
 */
add_action( 'customize_register', 								'g_customize_register' );

/**
 * Customizer default color tweaks
 */
add_filter( 'storefront_default_heading_color', 				'g_color_charcoal' );
add_filter( 'storefront_default_footer_heading_color', 			'g_color_charcoal' );
add_filter( 'storefront_default_header_background_color', 		'g_color_white' );
add_filter( 'storefront_default_footer_background_color', 		'g_color_white' );

add_filter( 'storefront_default_header_link_color', 			'g_color_charcoal' );
add_filter( 'storefront_default_header_text_color', 			'g_color_asphalt' );

add_filter( 'storefront_default_background_color', 				'g_color_white' );


add_filter( 'storefront_default_footer_link_color', 			'g_color_black' );

add_filter( 'storefront_default_text_color', 					'g_color_asphalt' );
add_filter( 'storefront_default_footer_text_color', 			'g_color_asphalt' );

add_filter( 'storefront_default_accent_color', 					'g_color_charcoal' );


add_filter( 'storefront_default_button_background_color', 		'g_color_white' );
add_filter( 'storefront_default_button_text_color', 			'g_color_charcoal' );

add_filter( 'storefront_default_button_alt_background_color', 	'g_color_charcoal' );
add_filter( 'storefront_default_button_alt_text_color', 		'g_color_white' );