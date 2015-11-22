<?php
/**
 * Galleria Storefront extension compatibility
 *
 * @package galleria
 */

/**
 * Remove Customizer settings added by Storefront extensions that this theme is incompatible with.
 * @return void
 */
function g_customize_storefront_extensions( $wp_customize ) {
	$wp_customize->remove_control( 'sd_header_layout' );
	$wp_customize->remove_control( 'sd_button_flat' );
	$wp_customize->remove_control( 'sd_button_shadows' );
	$wp_customize->remove_control( 'sd_button_background_style' );
	$wp_customize->remove_control( 'sd_button_rounded' );
	$wp_customize->remove_control( 'sd_button_size' );
	$wp_customize->remove_control( 'sd_header_layout_divider_after' );
	$wp_customize->remove_control( 'sd_button_divider_1' );
	$wp_customize->remove_control( 'sd_button_divider_2' );
}

/**
 * Set / remove theme mods to suit this theme after activation
 * @return void
 */
function g_set_theme_mods() {
	// Reset certain theme settings from the db
	remove_theme_mod( 'sd_header_layout' );
	remove_theme_mod( 'sd_button_flat' );
	remove_theme_mod( 'sd_button_shadows' );
	remove_theme_mod( 'sd_button_background_style' );
	remove_theme_mod( 'sd_button_rounded' );
	remove_theme_mod( 'sd_button_size' );
	remove_theme_mod( 'storefront_footer_background_color' );
}
