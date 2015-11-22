<?php
/**
 *	Oxygen WordPress Theme
 *	
 *	Laborator.co
 *	www.laborator.co 
 */

wp_enqueue_script(array('comment-reply', 'nivo-lightbox'));
wp_enqueue_style(array('nivo-lightbox', 'nivo-lightbox-default'));

get_header();

get_template_part('tpls/blog-single');

get_footer();