<?php
/*
	Template Name: Contact
*/

/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

wp_enqueue_script(array('google-map', 'oxygen-contact'));

get_header();

get_template_part('tpls/contact');

get_footer();