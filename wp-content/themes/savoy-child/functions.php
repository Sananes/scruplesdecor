<?php
	
	/* Styles
	=============================================================== */
	
	function nm_child_theme_styles() {
		 // Enqueue child theme styles
		 wp_enqueue_style( 'nm-child-theme', get_stylesheet_directory_uri() . '/style.css' );
	}
	add_action( 'wp_enqueue_scripts', 'nm_child_theme_styles', 1000 ); // Note: Use priority "1000" to include the stylesheet after the parent theme stylesheets
	