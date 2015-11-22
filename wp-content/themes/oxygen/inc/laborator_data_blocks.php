<?php
/**
 *	Laborator DataOpt Blocks
 *	
 *	Laborator.co
 *	www.laborator.co 
 */



// ! Supported Payments Block
$fields = array();

$fields['p_image_1'] 	= array('field_type' => 'image', 'field_name' => 'Payment Image', 'image_sizes' => array('th' => array(0,0,0)), 'desc' => 'Image height should have maximum height of 24 pixels.');
$fields['p_image_2'] 	= array('field_type' => 'image', 'field_name' => 'Payment Hover Image', 'image_sizes' => array('th' => array(0,0,0)), 'desc' => 'The image that will be displayed on hover.');
$fields['name']			= array('field_type' => 'text', 'field_name' => 'Name', 'required' => true);
$fields['link'] 		= array('field_type' => 'text', 'field_name' => 'Link', 'required' => false, 'placeholder' => 'http://');
$fields['blank_page']	= array('field_type' => 'checkbox', 'field_name' => 'Open link in new window', 'params' => array('checked' => false));

$supported_payments_instance = new LaboratorDataOpt(array(
	'parent_slug'				=> 'laborator_options', 
	'menu_slug' 				=> 'laborator_supported_payments', 
	'access_global'				=> 'laborator_supported_payments',
	'title' 					=> 'Supported Payments', 
	'fields' 					=> $fields,
	'table_fields'				=> array('p_image_1' => array('width' => 140), 'name', "link" => array('title' => "URL")),
	'sortable'					=> true,
	'sortable_column' 			=> -1,
	'order' 					=> 'ASC',
	'labels'					=> array(
									'plural' => 'Supported Payments (Footer Logos)',
									'singluar' => 'Supported Payments',
									'add_new' => 'Add Payment Logo'
								),
	'on_edit_return_to_main'	=> false
));

function get_supported_payments()
{
	global $supported_payments_instance;
	
	return $supported_payments_instance->get_entries();
}

