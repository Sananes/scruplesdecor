<?php
/**
 * Galleria Customizer controls
 *
 * @package galleria
 */

/**
 * Set up galleria customizer controls/settings
 */
function g_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'background_color' )->transport 	= 'refresh';
}