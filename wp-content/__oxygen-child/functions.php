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