<?php
/**
 * Galleria custom selectors that adopt Storefront customizer settings
 *
 * @package galleria
 */

/**
 * Add custom CSS based on settings in Storefront core
 * @return void
 */
function g_add_customizer_css() {
	$header_bg_color 				= storefront_sanitize_hex_color( get_theme_mod( 'storefront_header_background_color', apply_filters( 'galleria_default_header_background_color', '#ffffff' ) ) );
	$accent_color					= storefront_sanitize_hex_color( get_theme_mod( 'storefront_accent_color', apply_filters( 'galleria_default_accent_color', '#cf5916' ) ) );
	$header_link_color 				= storefront_sanitize_hex_color( get_theme_mod( 'storefront_header_link_color', apply_filters( 'galleria_default_header_background_color', '#2b2b2b' ) ) );
	$text_color 					= storefront_sanitize_hex_color( get_theme_mod( 'storefront_text_color', apply_filters( 'galleria_default_text_color', '#2b2b2b' ) ) );
	$button_text_color 				= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_text_color', apply_filters( 'storefront_default_button_text_color', '#ffffff' ) ) );
	$button_background_color 		= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_background_color', apply_filters( 'galleria_default_button_background_color', '#2b2b2b' ) ) );
	$button_alt_background_color 	= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_alt_background_color', apply_filters( 'galleria_default_button_alt_background_color', '#c41f61' ) ) );
	$button_alt_text_color 			= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_alt_text_color', apply_filters( 'storefront_default_button_alt_text_color', '#ffffff' ) ) );
	$content_bg_color				= storefront_sanitize_hex_color( get_theme_mod( 'sd_content_background_color' ) );
	$content_frame 					= get_theme_mod( 'sd_fixed_width' );

	if ( $content_bg_color && 'true' == $content_frame && class_exists( 'Storefront_Designer' ) ) {
		$bg_color 	= str_replace( '#', '', $content_bg_color );
	} else {
		$bg_color	= str_replace( '#', '', get_theme_mod( 'background_color', apply_filters( 'galleria_default_bg_color', 'ffffff' ) ) );
	}

	$brighten_factor 				= apply_filters( 'storefront_brighten_factor', 25 );
	$darken_factor 					= apply_filters( 'storefront_darken_factor', -25 );

	$style = '
		.onsale {
			background-color: ' . $button_alt_background_color . ';
			color: ' . $button_alt_text_color . ';
		}

		.woocommerce-pagination .page-numbers li .page-numbers.current,
		.pagination .page-numbers li .page-numbers.current {
			background-color: #' . $bg_color . ';
			color: ' . $text_color . ';
		}

		button, input[type="button"], input[type="reset"], input[type="submit"], .button, .added_to_cart, .widget-area .widget a.button, .site-header-cart .widget_shopping_cart a.button,
		button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, .button:hover, .added_to_cart:hover, .widget-area .widget a.button:hover, .site-header-cart .widget_shopping_cart a.button:hover {
			border-color: ' . $button_text_color . ';
		}

		@media screen and (min-width: 768px) {
			ul.products li.product .g-product-title,
			ul.products li.product .g-product-title h3 {
				background-color: ' . $header_link_color . ';
				color: ' . $header_bg_color . ';
			}

			ul.products li.product-category a {
				background-color: ' . $header_bg_color . ';
			}

			ul.products li.product-category .g-product-title h3 {
				color: ' . $header_link_color . ';
			}

			ul.products li.product .g-product-title .price {
				color: ' . $header_bg_color . ';
			}

			.main-navigation ul.menu > li:first-child:before, .main-navigation ul.menu > li:last-child:after, .main-navigation ul.nav-menu > li:first-child:before, .main-navigation ul.nav-menu > li:last-child:after {
				color: ' . $header_link_color . ';
			}

			.site-header .g-primary-navigation,
			.footer-widgets,
			.site-footer,
			.main-navigation ul.menu ul.sub-menu, .main-navigation ul.nav-menu ul.sub-menu,
			.site-header-cart .widget_shopping_cart,
			.site-branding h1 a,
			.site-header .g-top-bar {
				border-color: ' . $header_link_color . ';
			}

			.site-header .site-branding {
				border-bottom-color: ' . $header_link_color . ';
			}

			ul.products li.product .star-rating span:before,
			ul.products li.product .star-rating:before {
				color: ' . $header_bg_color . ';
			}
		}';

	wp_add_inline_style( 'g-style', $style );
}
