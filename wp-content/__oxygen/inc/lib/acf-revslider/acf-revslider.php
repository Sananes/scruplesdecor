<?php
/**
 *	Revolution slider field type
 *
 *	Created by Arlind Nushi
 *	
 *	Laborator.co
 *	www.laborator.co 
 */




// 1. set text domain
// Reference: https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
load_plugin_textdomain( 'acf-revslider', false, dirname( plugin_basename(__FILE__) ) . '/lang/' ); 




// 2. Include field type for ACF5
// $version = 5 and can be ignored until ACF6 exists
function include_field_types_revslider( $version ) {
	
	include_once('acf-revslider-v5.php');
	
}

add_action('acf/include_field_types', 'include_field_types_revslider');	




// 3. Include field type for ACF4
function register_fields_revslider() {
	
	include_once('acf-revslider-v4.php');
	
}

add_action('acf/register_fields', 'register_fields_revslider');	



	
?>