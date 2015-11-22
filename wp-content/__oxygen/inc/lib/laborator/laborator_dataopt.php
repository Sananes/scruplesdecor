<?php
/**
 *	Laborator - Data via Options
 *	This plugin is to give flexible Data Blocks for multipurpose use
 *
 *	Developed by: Arlind Nushi
 *
 *	www.laborator.co
 *
 *	Last Update: 26 Sep, 2013
 *	Version: 1.2.5
 */

if( ! class_exists('Zebra_Image'))
	include("Zebra_Image.php");
	
class LaboratorDataOpt
{
	private	$instance_id	= ''; # Will be generated from parent_slug and menu_slug
	
	private $parent_slug 	= '';
	private $menu_slug 		= '';
	private $title			= '';
	private $capability		= 'administrator'; # user roles
	
	private $sortable		= false; # false|0|true|DESC
	private $order			= 'ASC';
	
	private $max_items		= -1;
	
	private $per_page		= 10; # Entries per page
	
	# Table Fields to Show on Main Page
	private $table_fields	= array();
	
	private $labels			= array(
								'plural' 		=> '',
								'singular' 		=> '',
								'add_new' 		=> 'Add New %s',
								'edit'			=> 'Edit %s',
								'after_added' 	=> 'New %s has been added successfully.',
								'after_edited' 	=> '%s has been changed successfully.',
								'table_actions'	=> 'Actions',
								'no_entries'	=> 'No entries',
								'total_number'	=> 'Total Items',
								'deleted'		=> '%s has been deleted.',
								'none'			=> 'None',
								'checked'		=> 'Yes',
								'unchecked'		=> 'No',
								'max_items'		=> '* You cannot add more than <strong>%s</strong> entries on <strong>%s</strong>.'
							);
	
	# Block Fields
	private $fields	= array();
	
	
	# Name of global variable to access and save entries
	private $access_global = '';
	
	# WPML Compatible
	private $multilang = false;
	
	
	# Load Custom Scripts & Styles
	public $load_scripts = array();
	public $load_styles = array();
	public $on_load = '';
	
	
	# Other Vars
	private $sortable_column 		= 0;
	private $on_add_return_to_main 	= false;
	private $on_edit_return_to_main	= true;
	private $pattern_errors			= array();
	private $relations				= array();
	private $initial_data			= '';
	
	
	/**
	 *	param args:
	 *	:: 	menu_slug (required) - menu item id of the current data block
	 *	:: 	parent_slug (optional) - menu item id to be attached to, if not set will be top level menu item
	 *	::	access_global (required) - global variable to save data (wordpress default feature - get_option) - let this be unique variable!
	 *	::	title (optional) - title of the data block
	 *	::	capability (required) - default for administrators use only
	 *	::	sortable (boolean,string) - default false (available options: 0,1,true,false,DESC)
	 *	::	order (string) - data list order type, available choices ASC or DESC (default ASC)
	 *	::	table_fields - field contents to show on main/dashboard table
	 *	::	labels - text labels for the module block
	 *
	 *	:: 	fields (required,array)
	 		-	field_type (string) :: available types: text,image,file,select,checkbox,radio
	 		-	field_name (string)
	 		-	required (boolean) - default false
	 		-	image_sizes (array) :: thumnails to be created ie. array('th_' => array(width, height, crop), 'th2_' => array(width, height, crop)
	 		-	options (array,string) - for select field type, list all the available options to show in the list as array or string
	 		-	desc (string,optional) - help text, guide about the field usage
	 		-	pattern (optional) - regex for string to match
	 		-	params (array,options) - extra parameters for custom types such a file field (min_size,max_size,allowed_extensions)
	 *
	 *	:: sortable_column - If sortable then, set column index to make moving cursor appear. Set -1 to appear everywhere
	 *	:: on_add_return_to_main - When add, if form is successfully processed, redirect to main page if true
	 *	:: on_edit_return_to_main - When edit, if form is successfully processed, redirect to main page if true
	 *	:: pattern_errors - Pattern errors labels
	 *	:: max_items - Maximum items user can add to the table entry (-1 means no limit)
	 *	
	 *	V 1.2
	 *	:: multilang - make this structure compatible with WPML
	 *	:: multilang_code (read only) - current language code (is set automatically)
	 *
	 *	V 1.2.2
	 *	:: load_scripts - (array) variable to enqueue all required scripts with this data block
	 *	:: load_styles - (array) the same as load_scripts but this loads only stylesheets
	 *
	 *	V 1.3
	 *	:: relations - (array) list of links to be linked with other Data Options Blocks
	 *
	 *	V 1.4
	 *	:: on_load - (string) function name to execute when the data block page is loaded
	 *
	 *	V 1.5
	 *	:: initial_data - (string) base64 encoded array of data to be initialized when theme is activated
	 */


	public function __construct($args)
	{
		$default_labels = $this->labels;
		
		# Require Zebra Image Library
		if( ! class_exists('Zebra_Image'))
		{
			die(basename(__FILE__) . '/LaboratorDataOpt: <strong>Zebra_Image</strong> library is required!');
		}
		
		# Extend Options
		foreach($args as $key => $value)
		{
			$this->$key = $value;
		}
		
		# Is Multilang
		$this->multilang = $this->multilang ? true : false;
		
		if($this->multilang)
		{
			$this->set_current_language();
		}
		
		# Relations
		if( empty( $this->relations ) || ! is_array($this->relations))
		{
			$this->relations = array();
		}
		
		# Predefined Patterns
		$predefined_patterns = $this->get_predefined_patterns();
		
		# Define Pattern Errors
		foreach($predefined_patterns as $pattern_id => $pattern)
			$this->pattern_errors[$pattern['pattern']] = $pattern['pattern_error'];
		
		
		# Field Process
		if( ! is_array($this->fields))
			$this->fields = array();
		
		$field_types = array('text','textarea','file','select','checkbox','radio','image','email','number','integer');
		
		foreach($this->fields as $field_id => $field)
		{
			$field_defaults = array(
				'field_type' => '',
				'placeholder' => '',
				'required' => '',
				'class' => '',
				'desc'	=> '',
				'pattern' => '',
				'options' => '',
				'params' => array()
			);
			
			$field = array_merge($field_defaults, $field);
			
			# Field Type Process
			$this->fields[$field_id]['field_type'] = strtolower($field['field_type']);
			
			if( ! in_array($field['field_type'], $field_types))
			{
				$this->fields[$field_id]['field_type'] = reset($field_types);
			}
			
			# Required Or Not
			$this->fields[$field_id]['required'] = $field['required'] ? 1 : 0;
			
			# Field Description
			$this->fields[$field_id]['desc'] = $field['desc'];
			
			# Placeholder
			$this->fields[$field_id]['placeholder'] = $field['placeholder'];
			
			# Class
			$this->fields[$field_id]['class'] = explode(' ', trim($field['class']));
			
			# Regular Expression Pattern to Match
			$this->fields[$field_id]['pattern'] = $field['pattern'];
			
			# Params
			$this->fields[$field_id]['params'] = is_array($field['params']) ? $field['params'] : array();

			
			# For field type options
			switch($field['field_type'])
			{
				# Basic Field Types
				case 'image':
					if( ! isset($this->fields[$field_id]['image_sizes']) || ! is_array($this->fields[$field_id]['image_sizes']))
						$this->fields[$field_id]['image_sizes'] = array();
					
					$this->fields[$field_id]['field_type'] = 'file';
					$this->fields[$field_id]['params']['allowed_extensions'] = 'JPG, JPEG, PNG and GIF';
					$this->fields[$field_id]['params']['is_image'] = true;
					break;
				
				case 'select':
					if(is_string($field['options']))
					{
						$field['options'] = explode(',', $field['options']);
					}
					
					# Set Options
					$this->fields[$field_id]['options'] = $field['options'];
					break;
					
				case 'checkbox':
				case 'radio':
					
					$multiple = true;
					
					if(is_string($field['options']))
					{
						$field['options'] = explode(',', $field['options']);
						$multiple = false;
					}
					
					# Set Options
					$this->fields[$field_id]['options'] = $field['options'];
					
					if($multiple && count($this->fields[$field_id]['options']))
					{
						$this->fields[$field_id]['params']['multiple_options'] = true;
					}
					
					if($field['field_type'] == 'radio')
						$this->fields[$field_id]['params']['is_radio'] = true;
					
					break;
				
				# Pattern Type Fields
				case 'email':
					$this->fields[$field_id]['field_type'] = 'text';
					$this->fields[$field_id]['pattern'] = $predefined_patterns['email']['pattern'];					
					break;
				
				case 'number':
					$this->fields[$field_id]['field_type'] = 'text';
					$this->fields[$field_id]['pattern'] = $predefined_patterns['number']['pattern'];					
					break;
				
				case 'integer':
					$this->fields[$field_id]['field_type'] = 'text';
					$this->fields[$field_id]['pattern'] = $predefined_patterns['integer']['pattern'];					
					break;
			}
		}
		
		
		
		# Title Setup
		if( ! $this->title)
			$this->title = $this->menu_slug;
		
		# Order Setup
		if(strtoupper($this->order) != 'DESC')
			$this->order = 'ASC';
		
		# Maximum Items
		if(is_numeric($this->max_items) && $this->max_items <= 0)
			$this->max_items = -1;
		
		# In Case When Required Fields are not Filled
		if( is_admin() && ( ! $this->menu_slug || ! $this->access_global) )
		{
			if(function_exists('debug_backtrace'))
			{
				$line_num = debug_backtrace();
				$curr_line = $line_num[0];
				
				$err_file_loc = str_replace(ABSPATH, './', $curr_line['file']);
				$err_line_num = $curr_line['line'];
				
				
				$extra_info = " <br /><br />File: {$err_file_loc}<br /><br />Line number: {$err_line_num}";
			}
			
			wp_die(__CLASS__ . ': Menu Slug and Global Access variable are required.' . $extra_info);
		}
		
		# Instance ID Generate
		$this->instance_id = md5($this->parent_slug . '_' . $this->menu_slug);
		
		# Replace Labels
		foreach($this->labels as $key => $value)
			$default_labels[$key] = $value;
		
		if( ! $default_labels['plural'])
			$default_labels['plural'] = $this->title;
		
		if( ! $default_labels['singluar'])
			$default_labels['singluar'] = $default_labels['plural'];
	
		$default_labels['add_new'] 		= sprintf($default_labels['add_new'], $default_labels['singluar']);
		$default_labels['edit'] 		= sprintf($default_labels['edit'], $default_labels['singluar']);
		$default_labels['after_added'] 	= sprintf($default_labels['after_added'], $default_labels['singluar']);
		$default_labels['after_edited']	= sprintf($default_labels['after_edited'], $default_labels['singluar']);
		$default_labels['total_number']	= sprintf($default_labels['total_number'], $default_labels['plural']);
		$default_labels['deleted']		= sprintf($default_labels['deleted'], $default_labels['singluar']);
		
		$this->labels = $default_labels;
		
		
		# Process & Register Table Fields		
		if( ! count($this->table_fields))
		{
			# Show all fields
			$fields_to_show = $this->fields;
		}
		else
		if(is_string($this->table_fields))
		{
			$fields_delimitered = explode(',', $this->table_fields);
			$fields_to_show = array();
			
			foreach($fields_delimitered as $field_id)
			{
				$field_id = trim($field_id);
				
				if($this->fields[$field_id])
					$fields_to_show[$field_id] = $this->fields[$field_id];
			}
		}
		else
		if(is_array($this->table_fields))
		{
			$fields_to_show = array();
			
			foreach($this->table_fields as $field_id => $value)
			{
				$field = isset($this->fields[$field_id]) ? $this->fields[$field_id] : array();
				$is_regular_field = false;
				
				if( ! $field)
				{					
					# Numeric Index (plain value)
					if(is_numeric($field_id) && $value)
					{
						$field = isset($this->fields[$value]) ? $this->fields[$value] : null;
						
						if( ! $field)
							continue;
						
						$is_regular_field = true;
					}
					else
					{
						$field = array('field_name' => $field_id);
					}
				}
				
				# Add as Table Field
				if(is_array($value))
				{
					$value_defaults = array(
						'title' => '',
						'view_render' => '',
						'value' => '',
						'width' => '',
					);
					
					$value = array_merge($value_defaults, $value);
					
					$_title 		= $value['title'];
					$_view_render 	= $value['view_render'];
					$_value 		= $value['value'];
					$_width 		= $value['width'];
					
					if($_title)
						$field['field_name'] 	= $_title;
						
					$field['view_render'] 	= $_view_render;
					$field['value'] 		= $_value;
					$field['width'] 		= $_width;
										
					$fields_to_show[$field_id] = $field;
				}
				else
				{				
					# Check value if function and it exists inside this class
					if(method_exists($this, $value))
					{
						$field['view_render'] = array($this, $value);
						$fields_to_show[$field_id] = $field;
					}
					else
					# Check value if function and it exists outside this class
					if(function_exists($value))
					{
						$field['view_render'] = $value;
						$fields_to_show[$field_id] = $field;
					}
					# Show as just a text
					else
					{
						if($is_regular_field)
						{
							$fields_to_show[$value] = $field;
						}
						else
						if( ! $this->fields[$field_id])
						{
							$field['field_name'] = $field_id;
							$field['value'] = $value;
							$fields_to_show[$field_id] = $field;
						}
						else
						{
							$field['field_name'] = $value;
							$field['value'] = $value;
							
							$fields_to_show[$field_id] = $field;
						}
					}
				}
			}
		}
		
		
		foreach($fields_to_show as $field_id => $field)
		{
			$field_defaults = array(
				'title' => '',
				'view_render' => '',
				'value' => '',
				'width' => ''
			);
			
			
			$field = array_merge($field_defaults, $field);
			
			$fields_to_show[$field_id] = array(
				'title' 		=> $field['field_name'],
				'view_render' 	=> $field['view_render'],
				'value'			=> $field['value'],
				'width'			=> $field['width']
			);
		}
		
		$this->table_fields = $fields_to_show;
		
		
		# Add Actions Table Column (end column)
		if( ! isset($this->table_fields['actions']))
		{
			$this->table_fields['actions'] = array(
				'title' 		=> $this->labels['table_actions'],
				'view_render' 	=> 'render_table_actions', # function call
				'width'			=> ''
			);
		}
		
		
		# Additional Table Actions
		foreach($this->table_fields as $field_id => & $table_field)
		{
			$field_defaults = array(
				'field_type' => '',
				'params' => array(
					'is_image' => '',
					'view_render' => '',
				)
			);
			
			$field = isset($this->fields[$field_id]) ? $this->fields[$field_id] : $field_defaults;
			
			# Add defualt renderer for file type fields
			if($field['field_type'] == 'file' && ! $field['params']['is_image'] && $table_field['view_render'] == '')
			{
				$table_field['view_render'] = array($this, 'render_table_file');
			}
			
			# Add default renderer for image type fields
			if($field['field_type'] == 'file' && $field['params']['is_image'] && $table_field['view_render'] == '')
			{
				$table_field['view_render'] = array($this, 'render_table_image');
			}
			
			# Add default renderer for checkbox and radio type fields
			if($field['field_type'] == 'checkbox' || $field['field_type'] == 'radio')
			{
				$table_field['view_render'] = array($this, 'render_checkbox_radio');
			}
			
			# Add defualt renderer for select type fields
			if($field['field_type'] == 'select' && $table_field['view_render'] == '')
			{
				$table_field['view_render'] = array($this, 'render_table_select');
			}
		}
		
			
		# Setup Admin Menu
		add_action('admin_menu', array(&$this, 'setup_menu'));
		
		# If Sorting Allowed
		if(is_admin() && $this->sortable)
		{	
			# WP Ajax Process
			add_action('wp_ajax_laborator_dataopt_sort', array( & $this, 'process_sorting'));
			add_action('wp_ajax_nopriv_laborator_dataopt_sort', array( & $this, 'process_sorting'));
		}
		
		
		# Process In-Page Requests
		if(is_admin() && (isset($_GET['page']) ? $_GET['page'] : 0) == $this->menu_slug)
		{				
			if( ! function_exists('wp_verify_nonce'))
				require_once(ABSPATH . WPINC . '/pluggable.php');
				
				
			# Enqueue Scripts
			if($this->sortable)
			{
				if( ! function_exists('laborator_dataopt_enqueue_scripts'))
				{
					$load_scripts = maybe_serialize($this->load_scripts);
					$load_styles = maybe_serialize($this->load_styles);
					
					define('DATAOPT_load_scripts', $load_scripts);
					define('DATAOPT_load_styles', $load_styles);
					
					function laborator_dataopt_enqueue_scripts()
					{	
						wp_enqueue_script(array('jquery-ui-core', 'jquery-ui-sortable', 'thickbox'));
						wp_enqueue_style('thickbox');
						
						
						# Load Extra Scripts & Styles
						$load_scripts = maybe_unserialize(DATAOPT_load_scripts);
						$load_styles = maybe_unserialize(DATAOPT_load_styles);
						
						if( ! is_array($load_scripts))
							$load_scripts = array($load_scripts);
							
						if( ! is_array($load_styles))
							$load_styles = array($load_styles);
						
						foreach($load_scripts as $script_url)
						{
							$fileinfo = pathinfo($script_url);
							
							wp_enqueue_script('data_opt_js_' . $fileinfo['filename'], $script_url, null, null, true);
						}
						
						foreach($load_styles as $stylesheet_url)
						{
							$fileinfo = pathinfo($stylesheet_url);
							
							wp_enqueue_style('data_opt_css_' . $fileinfo['filename'], $stylesheet_url);
						}
					}
				}
				
				add_action('init', 'laborator_dataopt_enqueue_scripts');
			}
			
			# On Load Callback
			if($this->on_load && function_exists($this->on_load))
			{
				call_user_func($this->on_load, $this->instance_id);
			}
			
			// Process Inner Requests
			$p = $_POST;
			$iid = $this->instance_id;
			
			foreach($p as $k => $v)
			{
				$p[$k] = is_string($v) ? stripslashes($v) : $v;
			}
				
				
			# Add New Entry
			if(isset($_POST["add_{$iid}"]) || isset($_POST["edit_{$iid}"]))
			{
				$nonce = $p["nonce_{$iid}"];
				$entry_id = isset($p["entry_id_{$iid}"]) ? $p["entry_id_{$iid}"] : 0;
				
				if($entry_id)
					$is_editing = true;
				
				if(wp_verify_nonce($nonce, __CLASS__))
				{
					$errors = array();
					$prep_data = isset($is_editing) ? $this->get_entry($entry_id) : array();
					
					$files_queue = array();
					
					# Total Entries (if limited number, throw an error)
					$total_entries = $this->total_entries();
					
					if(isset($is_editing) && $is_editing == false && $this->max_items > 0 && $total_entries >= $this->max_items)
					{
						$errors[] = sprintf($this->labels['max_items'], $this->max_items, $this->labels['plural']);
					}
					
					# Set language Code
					if($this->is_multilang())
					{
						$prep_data['_lang'] = $this->multilang_code;
					}
					
					# Check Fields
					foreach($this->fields as $field_id => $field)
					{						
						$field_defaults = array(
							'field_type' => '',
							'placeholder' => '',
							'required' => '',
							'class' => '',
							'desc'	=> '',
							'pattern' => '',
							'options' => '',
							'params' => array()
						);
						
						$field = array_merge($field_defaults, $field);
						
						$field_name 	= $field['field_name'];
						$field_type 	= $field['field_type'];
						$required		= $field['required'];
						$pattern		= $field['pattern'];
						$params			= $field['params'];
						$options		= $field['options'];
						
												
						switch($field_type)
						{
							case 'text':
							case 'textarea':
							case 'select':
								$value = $p[$field_id];
								
								$prep_data[$field_id] = $value;
								
								if($required && ! $value)
								{
									$errors[] = sprintf("* Field <strong>%s</strong> is required!", $field_name);
								}
								break;
							
							case 'checkbox':
							case 'radio':
								$value = 0;
								
								if(isset($params['multiple_options']))
								{
									if(isset($params['is_radio']) && $params['is_radio'])
									{
										$value = $p[$field_id];
									}
									else
									{
										$value = array();
										
										foreach($options as $key => $val)
										{									
											$field_id_key = $field_id . "_{$key}";
										
											if(isset($p[$field_id_key]) && $p[$field_id_key])
											{
												$value[ $p[$field_id_key] ] = 1;
											}
										}
									}
								}
								else
								{
									if(isset($p[$field_id]))
										$value = 1;
								}
								
								$prep_data[$field_id] = $value;
								break;
							
							
							case 'file':
								$file 				= $_FILES[$field_id];
								$value				= isset($prep_data[$field_id]) ? $prep_data[$field_id] : '';
								
								$params_defaults	= array(
									'allowed_extensions' => '',
									'min_size' => '',
									'max_size' => '',
									'is_image' => '',
									'min_img_size' => '',
									'max_img_size' => '',
								);
								
								$params = array_merge($params_defaults, $params);
								
								
								$allowed_extensions	= $params['allowed_extensions'];
								$min_size 			= $params['min_size'];
								$max_size 			= $params['max_size'];
								
								$is_image 			= $params['is_image'];
								$min_img_size		= $params['min_img_size'];
								$max_img_size 		= $params['max_img_size'];
																
								if($is_image && $file['tmp_name'])
								{
									$original_img_size = getimagesize($file['tmp_name']);
								}
								
								# While editing, if has file, and no file is given for this required field, do not require another file.
								if( isset($is_editing) && isset($value) && isset($required) && ! $file['name'])
								{
									continue;
								}
								
								if($required && ! $file['name'] )
								{
									if($is_image)
									{
										$errors[] = sprintf("* Please upload an image at field <strong>%s</strong>", $field_name);
									}
									else
									{
										$errors[] = sprintf("* You must upload a file at <strong>%s</strong>", $field_name);
									}
								}
								else
								if($allowed_extensions && ! $this->valid_file_extension($file['name'], $allowed_extensions))
								{
									$errors[] = sprintf("* Invalid file extension for <strong>%s</strong>. Allowed extensions: %s", $field_name, $allowed_extensions);
								}
								else
								if($min_size && ! $this->file_size_ok($file['size'], $min_size, true))
								{
									$errors[] = sprintf("* File size of <strong>%s</strong> must be larger. Minimum file size: %s", $field_name, $min_size);
								}
								else
								if($max_size && ! $this->file_size_ok($file['size'], $max_size))
								{
									$errors[] = sprintf("* Maximum file size exceeded at <strong>%s</strong>. Maximum file size: %s", $field_name, $max_size);
								}
								else
								if($min_img_size && ! $this->valid_image_size($original_img_size, $min_img_size, true))
								{
									if(is_array($min_img_size))
									{
										$cs_width = $min_img_size[0];
										$cs_height = $min_img_size[1];
										
										if($cs_width > 0 && $cs_height > 0)
											$min_img_size = "{$cs_width}x{$cs_height} pixels";
										else
										if($cs_height <= 0)
											$min_img_size = "{$cs_width}px by width";
										else
										if($cs_width <= 0)
											$min_img_size = "{$cs_height}px by height";
									}
										
									$errors[] = sprintf("* Minimun image size for <strong>%s</strong> must be %s.", $field_name, $min_img_size);
								}
								else
								if($max_img_size && ! $this->valid_image_size($original_img_size, $max_img_size))
								{
									if(is_array($max_img_size))
									{
										$cs_width = $max_img_size[0];
										$cs_height = $max_img_size[1];
										
										if($cs_width > 0 && $cs_height > 0)
											$max_img_size = "{$cs_width}x{$cs_height} pixels";
										else
										if($cs_height <= 0)
											$max_img_size = "{$cs_width}px by width";
										else
										if($cs_width <= 0)
											$max_img_size = "{$cs_height}px by height";
									}
										
									$errors[] = sprintf("* Maximum image size for <strong>%s</strong> must be %s.", $field_name, $max_img_size);
								}
							
								if($file['name'])
									$files_queue[$field_id] = $file;
								break;
						}
						
						
								
						# Validate Field Based on the Value and Pattern
						if($required && $pattern || ($value && $pattern))
						{															
							if( ! preg_match("/{$pattern}/", $value))
							{
								if($pattern_error_label = $this->pattern_errors[$pattern])
									$errors[] = sprintf($pattern_error_label, $field_name);
								else
									$errors[] = sprintf("* Enter correct value at field <strong>%s</strong>", $field_name);
							}
						}
					}
					
					
												
					# Upload File Queue
					if(count($errors) == 0)
					{
						$upload_dir = wp_upload_dir();
						
						$path = $upload_dir['path'];
						$relative = _wp_relative_upload_path($path);
						
						if( ! file_exists($path))
							mkdir($path, 0777, true);
						
						foreach($files_queue as $field_id => $file)
						{
							# Delete the old one (if exits)
							$value = isset($prep_data[$field_id]) ? $prep_data[$field_id] : null;
							
							if((isset($is_editing) && $is_editing) && $value)
							{
								$this->delete_file_from_entry($entry_id, $field_id);
							}
							
							# Upload the File
							$file_name = strtolower(sanitize_file_name($file['name']));
							
							$unique_filename = wp_unique_filename($path, $file_name);							
							$upload_file_path = $path . DIRECTORY_SEPARATOR . $unique_filename;
							
							move_uploaded_file($file['tmp_name'], $upload_file_path);
							
							$prep_data[$field_id] = _wp_relative_upload_path($upload_file_path);
							
							# Image Thumbnail Generator
							$is_image = $this->fields[$field_id]['params']['is_image'];
							$image_sizes = $this->fields[$field_id]['image_sizes'];
							
							if($is_image)
							{
								$prep_data[$field_id] = $this->generate_thumbnails($upload_file_path, $image_sizes);
							}
							# End: Image Thumbnail Generator
						}
					}
					
					
					# In Case When Errors happen
					if(count($errors) > 0)
					{
						$errors_str = 'Please correct the following errors:<br />';
						$errors_str .= implode('<br />', $errors);
						
						define('LDO_ERROR', $errors_str);
					}
					# No errors happen, add entry
					else
					{
						$action = $_GET['action'];
						
						# Edit Entry
						if(isset($is_editing) && $is_editing)
						{
							$message = $this->labels['after_edited'];
							
							if($message)
								setcookie('ldo_update_message', $message, time() + 5);
							
							$this->edit_entry($entry_id, $prep_data);
							
							if($this->on_edit_return_to_main)
								wp_redirect(admin_url("admin.php?page={$this->menu_slug}&id_set={$entry_id}"));
							else
								wp_redirect(admin_url("admin.php?page={$this->menu_slug}&action={$action}&edit_{$iid}={$entry_id}"));

						}
						# Add Entry
						else
						{
							$message 	= $this->labels['after_added'];
							
							# Add Entry to Variable
							$this->add_entry($prep_data);
							
							if($message)
								setcookie('ldo_update_message', $message, time() + 5);
							
							if($this->on_add_return_to_main)
								wp_redirect(admin_url("admin.php?page={$this->menu_slug}&id_set={$id}"));
							else
								wp_redirect(admin_url("admin.php?page={$this->menu_slug}&action={$action}"));
						}
						
					}	
				}
			}
			
			
			
			
			# Delete Entry
			if(isset($_REQUEST["delete_{$iid}"]))
			{
				$delete_entry_id = $_REQUEST["delete_{$iid}"];
				
				if($this->delete_entry($delete_entry_id))
				{
					$ldo_update_message = $this->labels['deleted'];
					define('LDO_UPDATE', $ldo_update_message);
				}
			}
			
			
			# Delete File From Entry
			if(isset($_REQUEST['file_delete']))
			{
				$file_delete = $_REQUEST['file_delete'];
				
				$entry_id = $_GET["edit_{$iid}"];
				
				if(($entry = $this->get_entry($entry_id)) && $this->delete_file_from_entry($entry_id, $file_delete))
				{					
					$file_name = $entry[$file_delete];
					
					if(is_array($file_name))
						$file_name = basename(reset($file_name));
					
					define('LDO_UPDATE', sprintf("File <strong>%s</strong> has been deleted.", $file_name));
				}
			}
				
			
			// Update Message Cookie
			if($ldo_update_message = (isset($_COOKIE['ldo_update_message']) ? $_COOKIE['ldo_update_message'] : ''))
				define('LDO_UPDATE', $ldo_update_message);
		}
		
		
		# Setup Initial Data
		$initialized_data_global = $this->access_global . '_initialized';
		
		if($this->initial_data && get_option($initialized_data_global) != true)
		{
			$import_array = objectToArray(json_decode(base64_decode($this->initial_data)));
			
			if(is_array($import_array))
			{
				update_option($this->access_global, $import_array);
			}
			
			update_option($initialized_data_global, true);
		}
	}
	
	public function setup_menu()
	{
		global $menu;
		
		$parent_slug = $this->parent_slug;
		$title = $this->title;
		$capability = $this->capability;
		$menu_slug = $this->menu_slug;
		
		$function = array( & $this, 'admin_main_page');
		
		# Assign to Parent Menu
		if($parent_slug)
		{
			add_submenu_page( $parent_slug, $title, $title, $capability, $menu_slug, $function );
		}
		# Create Top Level Menu Item
		else
		{
			add_menu_page( $title, $title, $capability, $menu_slug, $function ); 
		}
		
	}
	
	
	/* Predefined Patterns */
	private function get_predefined_patterns()
	{
		$arr = array();
		
		$arr['email'] 	= array(
									'pattern' => '^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$',  
									'pattern_error' => '* Please enter valid email address at <strong>%s</strong>'
						);
						
		$arr['number'] 	= array(
									'pattern' => '^-?[0-9]+(\.[0-9]+)?$', 
									'pattern_error' => '* Please enter valid number at <strong>%s</strong>'
						);
						
		$arr['integer'] = array(
									'pattern' => '^[0-9]+$', 
									'pattern_error' => '* Please enter valid integer at <strong>%s</strong>'
						);
		
		return $arr;
	}
	
	
	/* Match file extension */
	private function valid_file_extension($filename, $allowed_extensions = 'jpg|jpeg|png')
	{
		if( ! is_array($allowed_extensions))
		{
			$allowed_extensions = str_replace(array(' ', ',',';','and'), '|', $allowed_extensions);
			$allowed_extensions = explode("|", $allowed_extensions);
			
			foreach($allowed_extensions as $i => $ext)
			{
				$ext = trim(strtolower($ext));
				
				if($ext)
					$allowed_extensions[$i] = $ext;
			}
		}
		
		
		$fn_x = explode('.', $filename);
		$file_extension = strtolower(end($fn_x));
		
		if($file_extension == '*')
			return true;
		
		return in_array($file_extension, $allowed_extensions);
	}
	
	
	/* Check File Size */
	public function file_size_ok($current_size, $max_size, $min = false)
	{
		preg_match("/([0-9]+)([a-z]K|[a-z]+)?/i", $max_size, $number_and_unit);
		
		$number = $number_and_unit[1];
		$unit = strtoupper($number_and_unit[2]);
		
		if($unit)
			$unit = substr($unit, 0, 1);
		
		switch($unit)
		{
			case 'T':
				$mult = pow(1024,4);
				break;
				
			case 'G':
				$mult = pow(1024,3);
				break;
				
			case 'M':
				$mult = pow(1024,2);
				break;
				
			default:
				$mult = 1024;
		}
		
		$max_size_len = $mult * $number;
		
		
		
		return $min ? ($current_size >= $max_size_len) : ($current_size <= $max_size_len);
	}
	
	
	/* Validate Image Size */
	public function valid_image_size($original_size, $fit_size, $min = false)
	{
		$o_width = $original_size[0];
		$o_height = $original_size[1];
		
		# Size to Fit
		$sf_width = 0;
		$sf_height = 0;
		
		if(is_string($fit_size))
		{
			preg_match_all("/([0-9]+)/", $fit_size, $fs_extracted);
		
			$sizes_extraced = $fs_extracted[1];
			
			if( ! count($sizes_extraced))
			{	
				return true;
			}
			else
			if(count($sizes_extraced) == 1)
			{
				# Check only width
				$sf_width = $sizes_extraced[0];
			}
			else
			{
				$sf_width = $sizes_extraced[0];
				$sf_height = $sizes_extraced[1];
			}
		}
		else
		if(is_array($fit_size))
		{
			$fs_arr_width = intval($fit_size[0]);
			$fs_arr_height = intval($fit_size[1]);
			
			if( ! $fs_arr_width && ! $fs_arr_height)
			{
				return true;
			}
			else
			{
				$sf_width = $fs_arr_width;
				$sf_height = $fs_arr_height;
			}
		}
		
		if($min)
		{
			return $sf_width && $sf_height ? ($o_width >= $sf_width && $o_height >= $sf_height) : ($sf_width ? ($o_width >= $sf_width) : ($o_height >= $sf_height));
		}
		else
		{
			return $sf_width && $sf_height ? ($o_width <= $sf_width && $o_height <= $sf_height) : ($sf_width ? ($o_width <= $sf_width) : ($o_height <= $sf_height));
		}
		
		return false;
	}
	
	
	/* Generate Thumnails from an image */
	public function generate_thumbnails($image_path, $image_sizes)
	{
		$files = array();
		
		# Checking if Image exists
		if( ! file_exists($image_path))
			;#return array();
		
		$files['original'] = str_replace(ABSPATH, '', $image_path);
		
		$image_dir = dirname($image_path) . '/';
		$image_name = basename($image_path);
		
		$image_size = getimagesize($image_path);
		
		# Create Thumbnails
		foreach($image_sizes as $prefix => $size)
		{
			$thumbnail_name = $prefix . $image_name;
			$thumbnail_path = $image_dir . $thumbnail_name;
			
			$width	= $size[0];
			$height	= $size[1];
			$crop	= isset($size[2]);
			$boxed	= isset($size[3]) ? $size[3] : 0; # Boxed to width and height
			
			# Resize only one dimension
			if($width <= 0 || $height <= 0)
			{
				if($height <= 0)
				{
					# Resize by Width
					$ratio = $width / $image_size[0];
					
					$new_width = $width;
					$new_height = $image_size[1] * $ratio;
				}
				else
				{
					# Resize by Height
					$ratio = $height / $image_size[1];
					
					$new_width = $image_size[0] * $ratio;
					$new_height = $height;
				}
				
				$width = $new_width;
				$height = $new_height;
			}
			
			# Rresize Image
			
			/* 
				DEPRECATED METHOD
				$thumbnail_path = image_resize($image_path, $width, $height, $crop, '', $thumbnail_path);
			*/
			
			# Resize Image Using Zebra_Image Library
			$img = new Zebra_Image();
			
			$thumbnail_path = dirname($image_path) . "/{$prefix}_" . basename($image_path);
			
			$img->source_path = $image_path;
			$img->target_path = $thumbnail_path;
			
			if($crop)
			{
				switch($boxed)
				{
					// Do not enlarge Smaller Images
					case 2:
						$img->enlarge_smaller_images = false;
						$img->resize($width, $height, ZEBRA_IMAGE_BOXED, '#FFF');
						break;
					
					// Fit images to box size
					case 1:
					case true:
						$img->resize($width, $height, ZEBRA_IMAGE_BOXED, '#FFF');
						break;
					
					// Crop in the center
					default:
						$img->resize($width, $height, ZEBRA_IMAGE_CROP_CENTER, '#FFF');
				}
			}
			else
			{
				$img->preserve_aspect_ratio = true;
				$img->resize($width, $height, ZEBRA_IMAGE_NOT_BOXED, '#FFF');
			}
			
			$thumbnail_path_relative = str_replace(ABSPATH, '', $thumbnail_path);	
			
			# Remove prefix last character which is _ or -
			$arr_index = preg_replace('/(_|-)$/', '', $prefix);
			
			$files[$arr_index] = $thumbnail_path_relative;
			
		}
		
		return $files;
	}
	
	
	
	/* Format File size to adecuate Unit */
	public function format_filesize($file_size)
	{
		$units = array('KB','MB','GB','TB');
		$unit = $units[0];
		
		$i = 0;
		
		do 
		{
			$file_size /= 1024;
			$unit = $units[$i];
			$i++;
		}
		while($file_size > 1024);
		
		return number_format($file_size, 1) . ' ' . $unit;
	}
	
	
	/* Admin Page for module */
	public function admin_main_page()
	{	
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		
		# Export
		if(get('export'))
		{
			$export_string = json_encode(get_option($this->access_global));
			$export_string = base64_encode($export_string);
			
			?>
			<style>
				#export_<?php echo $this->access_global; ?> {
					height: 500px;
					width: 99%;
					box-sizing: border-box;
					-webkit-box-sizing: border-box;
					margin-right: 10px;
					margin-bottom: 10px;
					margin-top: 10px;
				}
			</style>
			<div class="wrap">
				<div id="icon-tools" class="icon32"><br></div>
				<h2><?php echo $this->labels['plural']; ?> - Export</h2>
				<textarea id="export_<?php echo $this->access_global; ?>"><?php echo $export_string; ?></textarea>
				<a href="admin.php?page=<?php echo $_GET['page']; ?>">&laquo; Go Back</a>
			</div>
			<?php
			return;
		}
		
		switch(strtolower($action))
		{
			case 'add':
				$this->admin_page_add_or_edit_entry();
				return;
			
			case 'edit':
				if($entry = $this->get_entry($_GET["edit_{$this->instance_id}"]))
				{
					$this->admin_page_add_or_edit_entry($entry);
					return;
				}
		}
		
		# Retrieve Data
		$table_fields = $this->table_fields;
		$entries = $this->paginate_items( $this->get_entries() );
		
		$entries_count = $this->pagination_data['total_entries'];
			
		
?>
<style>
.laborator_dataopt_img {
	border: 5px solid #FFF;
	box-shadow: 0px 0px 1px rgba(0,0,0,0.5);
}

.ui-sortable-helper {
	background: #FFF;
	border: none !important;
	opacity: 0.75
}

.move_cur {
	cursor: move;
}

.image_render {
	background: #FFF;
	padding: 5px;
	border: 1px solid #EEE;
	display: inline-block;
	box-shadow: 0px 0px 3px rgba(0,0,0,0.1);
}

.image_render img {
	display: block;
}

.file_render {
	display: inline-block;
	background: no-repeat;
	background-size: 14px;
	padding-left: 20px;
}

#dataopt_table td {
	padding-top: 6px;
	padding-bottom: 6px;
}

.items_count {
	background: #FAFAFA;	
	background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -o-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));
	background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
	background-image: linear-gradient(top,#F9F9F9,#ECECEC);
	border-radius: 3px;
	border: 1px solid #E0E0E0;
	margin-bottom: 12px;
	display: inline-block;
	box-shadow: inset 0px 1px 0px #FFF;
}

.items_count .num, .items_count .txt {
	display: inline-block;
	padding: 5px 10px;
}

.items_count .num {
	border-right: 1px solid #CCC;
	font-weight: bold;
	background-image: -webkit-linear-gradient(top,#ECECEC,#F9F9F9);
	font-family: "Georgia";
	font-size: 15px;
	color: #666;
}

.items_count .txt {
	border-left:  1px solid #FFF;
	color: #888;
}

.gray {
	color: gray;
}
</style>
<script type="text/javascript">
jQuery(function($)
{
	$(".confirm_laborator_dataopt_delete").on('click', function(e)
	{
		if( ! confirm('Are you sure you want to delete this entry?'))
		{
			e.preventDefault();
		}
	});
	
	$("#dataopt_table").sortable({
		items: 'tbody tr',
		axis: 'y',
		update: function(e)
		{
			var arr = $(this).sortable('toArray');

			$.post(ajaxurl, {action: 'laborator_dataopt_sort', instance_id: "<?php echo $this->instance_id; ?>", new_order: arr}, function(resp)
			{
				console.log(resp);
			});
		}
	});
});
</script>
<div class="wrap">
<h2>
	<?php echo $this->labels['plural']; ?>
	
	<?php if($this->max_items > 0 && $entries_count < $this->max_items || $this->max_items == -1): ?>
	<a href="admin.php?page=<?php echo $_GET['page']; ?>&action=add" class="add-new-h2"><?php echo $this->labels['add_new']; ?></a>
	<?php endif; ?>
	
	<?php 
	foreach($this->relations as $relation_id => $menu_text): 
		$link = 'admin.php?page=' . $relation_id;
		
		if(strstr($relation_id, '#'))
		{
			$link = $relation_id;
		}
	?>
	<a href="<?php echo $link; ?>" <?php echo strstr($relation_id, '#') ? ('id="'.str_replace('#', '', $relation_id).'"') : ''; ?> class="add-new-h2"><?php echo $menu_text; ?></a>
	<?php endforeach; ?>
</h2>

<?php if(defined("LDO_ERROR")): ?>
<div id="message" class="error below-h2">
	<p><?php echo LDO_ERROR; ?></p>
</div>
<?php elseif(defined("LDO_UPDATE")): ?>
<div id="message" class="updated below-h2">
	<p><?php echo LDO_UPDATE; ?></p>
</div>
<?php else: ?>
<br />
<?php endif; ?>


<?php if($entries_count): ?>
<div class="items_count">
	<span class="num"><?php echo $entries_count; ?></span><span class="txt"><?php echo $this->labels['total_number']; ?></span>
</div>
<?php endif; ?>

<table id="dataopt_table" class="wp-list-table widefat fixed posts" cellspacing="0">
	
	<thead>
		<tr>
			<?php 
			# Table Header
			foreach($table_fields as $field_id => $table_field): 
				
				$table_field_title = $table_field['title'];
				$width = intval($table_field['width']);
				
				?>
				<th<?php echo $width ? (' style="width: ' . $width . 'px !important"') : ''; ?>><?php echo $table_field_title; ?></th>
				<?php
			endforeach;
			?>
		</tr>
	</thead>
	
	<tbody>
	<?php 
	if($entries_count):
		
		$i = 0;
		foreach($entries as $entry_id => $entry): 
			
			$entry_id = $entry['ID'];
			
			# Class
			$classes		= array();

			
			if($i%2 == 0)
				$classes[] 		= 'alternate';
		?>
		<tr id="<?php echo $entry_id; ?>" class="<?php echo implode(' ', $classes); ?>">
		<?php
			foreach($table_fields as $field_id => $table_field): 
			
				$view_render = $table_field['view_render'];
				
				$is_function_call = false;
				
				# Call Function for rendering
				if($view_render)
				{
					if(is_string($view_render))
					{
						# First Check if method exists inside this class, then check outside
						if(method_exists($this, $view_render))
						{
							$view_render = array($this, $view_render);
							$is_function_call = true;
						}
						else
						if(function_exists($view_render))
						{
							$is_function_call = true;
						}
					}
					else
					if(is_array($view_render) && count($view_render) == 2)
					{
						$is_function_call = true;
					}
				}
				
				$classes = array();
				
				if($this->sortable && ($i == $this->sortable_column || $this->sortable_column == -1))
				{
					$classes[] = 'move_cur';
				}
			?>
			<td class="<?php echo implode(' ', $classes); ?>">
			<?php
			
				
				if($is_function_call):
					echo call_user_func($view_render, $entry_id, $entry, $field_id, $this->instance_id);
				elseif(! $this->fields[$field_id] || $table_field['value']):
					echo $table_field['value'];
				else:
					echo isset($entry[$field_id]) ? esc_html($entry[$field_id]) : '';
				endif;
				
			?>
			</td>
			<?php
			endforeach;
		?>
		</tr>
		<?php
			$i++;
		endforeach;
		
	else: 
	?>
		<tr>
			<td colspan="<?php echo count($this->table_fields); ?>"><?php echo $this->labels['no_entries']; ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
	
</table>

<?php if($entries_count): ?>
<div class="tablenav">	
	<?php if($this->sortable): ?><strong>Drag items to reorder</strong><?php endif; ?>
</div>
<?php endif; ?>

<?php $this->generate_pagination(); ?>

</div>
<?php
	}
	
	
	public function admin_page_add_or_edit_entry($entry = null)
	{
		$instance_id = $this->instance_id;
		
		?>
<style>
.laborator_dataopt_img {
	border: 5px solid #FFF;
	box-shadow: 0px 0px 1px rgba(0,0,0,0.5);
	display: block;
	margin-bottom: 15px;
}

.filename_entry {
	background: #FAFAFA;
	padding: 5px 10px;
	margin-top: 8px;
	border-radius: 3px;
	width: auto;
	display: inline-block;
	box-shadow: 0px 0px 3px rgba(0,0,0,0.1);
}

.filename_entry span {
	display: inline-block;
}

.filename_entry .name {
	margin-right: 25px;
}

.filename_entry .size {
	margin-right: 25px;
	font-weight: bold;
}

.filename_entry .date {
	color: #999;
}

.filename_entry .options {
	margin-right: 25px;
}

.filename_entry .options a {
	color: #BC0B0B;
	text-decoration: none;
}

.filename_entry .options a:hover {
	text-decoration: underline;
	color: red;
}

.image_render {
	background: #FFF;
	padding: 5px;
	border: 1px solid #EEE;
	display: inline-block;
	box-shadow: 0px 0px 3px rgba(0,0,0,0.1);
	margin-top: 10px;
}

.image_render img {
	display: block;
	max-width: 100%;
}

.label_block {
	display: block;
}

.laborator_dataopt_heading_title {
	margin: 0px;
	padding: 0px;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	$(".file_delete_confirm").on('click', function(ev)
	{
		if( ! confirm('Confirm file deletion?'))
			ev.preventDefault();
		
	});
});
</script>

<div class="wrap">
<h2><?php echo $this->labels['plural']; ?> &raquo; <?php echo $entry ? $this->labels['edit'] : $this->labels['add_new']; ?></h2>

<?php if(defined("LDO_ERROR")): ?>
<div id="message" class="error below-h2">
	<p><?php echo LDO_ERROR; ?></p>
</div>
<?php elseif(defined("LDO_UPDATE")): ?>
<div id="message" class="updated below-h2">
	<p><?php echo LDO_UPDATE; ?></p>
</div>
<?php endif; ?>


<?php
	$params = $_GET;
	unset($params['file_delete']);
?>

<?php do_action('laborator_dataopt_before_form', $instance_id); ?>

<form method="post" id="form_<?php echo $instance_id; ?>" action="<?php echo admin_url("/admin.php?" . http_build_query($params)); ?>" enctype="multipart/form-data">
	
	<table class="form-table">
		<tbody>
		<?php
		$p = $_POST;
		$ud = wp_upload_dir();

		foreach($this->fields as $field_id => $field):
		
			$field_defaults = array(
				'field_name' => '',
				'field_type' => '',
				'required' => '',
				'image_sizes' => '',
				'desc' => '',
				'placeholder' => '',
				'class' => '',
			);
			
			$field = array_merge($field_defaults, $field);
			
			
			$field_name 	= $field['field_name'];
			$field_type 	= $field['field_type'];
			$required		= $field['required'];
			$image_sizes	= $field['image_sizes'];
			$desc			= $field['desc'];
			$placeholder	= $field['placeholder'];
			$style_class 	= $field['class'];
			
			$value			= ! empty($entry[$field_id]) ? $entry[$field_id] : '';
			
			# Field Params
			$params = $this->fields[$field_id]['params'];
			
			# Value
			if(isset($p[$field_id]))
				$value = $p[$field_id];
			
			# Class
			$classes		= $style_class;

			
			$classes[] 		= 'laborator_field';
			$classes[] 		= 'regular-text';
			$classes[]		= 'laborator_' . $field_type;
			
			if($required)
				$classes[]	= 'required';
			
			
			# Heading Title for field
			if(isset($params['heading'])):
				$heading_title = $params['heading'];
			?>
			<tr>
				<th colspan="2"><h3 class="laborator_dataopt_heading_title"><?php echo $heading_title; ?></h3></th>
			</tr>
			<?php
			endif;
			?>
			<tr>
				<th>
					<label for="<?php echo $field_id; ?>"><?php echo $field_name; ?>:</label>
				</th>
				<td>
				<?php
				switch($field_type)
				{
					case 'textarea':
						$rows = isset($params['rows']) ? $params['rows'] : 5;
						$cols = isset($params['cols']) ? $params['cols'] : 45;
						
						?>
						<textarea name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="<?php echo implode(' ', $classes); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>"><?php echo esc_attr($value); ?></textarea>
						<?php
						break;
					
					case 'file':
						$is_image = $this->fields[$field_id]['params']['is_image'];
						
						?>
						<input type="file" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="<?php echo implode(' ', $classes); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" />
						<?php
						
						# If is image, show thumbnail
						if($is_image && is_array($value) && count($value)):
							
							$value = ABSPATH . $value['original'];
							$value = _wp_relative_upload_path($value);
							
							echo '<br />';
							$this->render_table_image($entry['ID'], $entry, $field_id);
							
						endif;
						
						if($value):
							
							$file_path = $ud['basedir'] . "/{$value}";
							$file_url = $ud['baseurl'] . "/{$value}";
														
							if(file_exists($file_path)):
								$file_size = $this->format_filesize(filesize($file_path));
								
								$file_delete_url = admin_url("admin.php?page={$_GET['page']}&action={$_GET['action']}&edit_{$this->instance_id}=" . $_GET["edit_{$this->instance_id}"] . "&file_delete={$field_id}");
								?>
								<br />
								<div class="filename_entry">
									<span class="options">
										<a href="<?php echo $file_delete_url; ?>" class="file_delete_confirm"><?php echo $is_image ? 'Delete Image' : 'Delete File'; ?></a>
									</span>
									
									<a href="<?php echo $file_url; ?>" class="name" target="_blank"><?php echo basename($file_url); ?></a>
									<span class="size"><?php echo $file_size; ?></span>
									<span class="date"><?php echo date_i18n("m/d/y H:i:s", filemtime($file_path)); ?></span>
								</div>
								<?php
							endif;
						endif;
						
						break;
					
					case 'select':
						
						$options = $this->fields[$field_id]['options'];
						$selected = isset($this->fields[$field_id]['params']['selected']) ? $this->fields[$field_id]['params']['selected'] : false;
						
						# Set Default Selected for `Add Entry`
						if( ! $entry && $selected)
						{
							$value = $selected;
						}
						
						?>
						<select name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="<?php echo implode(' ', $classes); ?>">
						<?php
						foreach($options as $key => $val)
						{
							$is_selected = is_string($key) ? ($key == $value) : ($val == $value);
							
							?>
							<option<?php echo $is_selected ? ' selected="selected"' : ''; echo is_string($key) ? (' value="'.esc_attr($key).'"') : ''; ?>><?php echo $val; ?></option>
							<?php
						}
						?>
						</select>
						<?php
						break;
					
					case 'checkbox':
					case 'radio':
						
						$multiple_options = isset($this->fields[$field_id]['params']['multiple_options']) ? $this->fields[$field_id]['params']['multiple_options'] : false;
						$is_radio = isset($this->fields[$field_id]['params']['is_radio']) && $this->fields[$field_id]['params']['is_radio'] ? true : false;
						
						
						if( ! $multiple_options && ! $is_radio):
							
							if( ! $entry && $this->fields[$field_id]['params']['checked'])
								$value = 1;
						?>
						<input<?php echo $value ? ' checked="checked"' : ''; ?> type="<?php echo $is_radio ? 'radio' : 'checkbox'; ?>" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" value="1" />
						<?php
						else:
							
							$options = $this->fields[$field_id]['options'];

							if( ! is_array($options))
								continue;
							
							$checked_options = $this->fields[$field_id]['params']['checked'];
							
							# Set Checked Options
							if($checked_options && ! $entry)
							{
								$checked_options_rs = array();
									
								if(is_string($checked_options))
								{
									$checked_options = explode(',', $checked_options);
									
									foreach($checked_options as $checked_val)
									{
										$checked_options_rs[$checked_val] = 1;
									}
								}
								else
								{
									foreach($checked_options as $checked_val)
										$checked_options_rs[$checked_val] = 1;	
								}
								
								if($is_radio)
								{
									$value = reset($checked_options);
								}	
								else
								{
									$value = $checked_options_rs;
								}
							}
							
							foreach($options as $key => $val)
							{
								if($is_radio)
									$field_id_key = $field_id;
								else
									$field_id_key = $field_id . "_{$key}";
								
								
								if($is_radio)
								{
									$is_checked = is_string($key) ? $value == $key : $value == $val;
								}
								else
								{
									if(is_string($key))
										$is_checked = isset($value[$val]) && $value[$key] ? true : false;
									else
										$is_checked = isset($value[$val]) && $value[$val] ? true : false;
								}
									
								?>
								<label class="label_block">
									<input<?php echo $is_checked ? ' checked="checked"' : ''; ?> type="<?php echo $is_radio ? 'radio' : 'checkbox'; ?>" name="<?php echo $field_id_key; ?>" id="<?php echo $field_id_key; ?>" value="<?php echo is_string($key) ? esc_attr($key) : esc_attr($val); ?>" />
									<span><?php echo $val; ?></span>
								</label>
								<?php
							}
						
						endif;
						
						break;
						
					default:
					?>
					<input type="text" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" class="<?php echo implode(' ', $classes); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($value); ?>" />
					<?php
				}
				
					
				if($desc)
				{
					?>
					<p class="description"><?php echo $desc; ?></p>
					<?php
				}
				?>
				</td>
			</tr>
			<?php
		
		endforeach;
		?>
		</tbody>
	</table>
	
	<p>
		<?php if($entry): ?>
		<button type="submit" name="edit_<?php echo $instance_id; ?>" class="button-primary"><?php echo $this->labels['edit']; ?></button>
		<input type="hidden" name="entry_id_<?php echo $instance_id; ?>" value="<?php echo $_GET["edit_{$this->instance_id}"]; ?>" />
		<?php else: ?>
		<button type="submit" name="add_<?php echo $instance_id; ?>" class="button-primary"><?php echo $this->labels['add_new']; ?></button>
		<?php endif; ?>
		
		<input type="hidden" name="nonce_<?php echo $this->instance_id; ?>" value="<?php echo wp_create_nonce(__CLASS__); ?>" />
	</p>
	
</form>


<br />
<a href="admin.php?page=<?php echo $_GET['page']; ?>">&laquo; Go Back</a>

</div>
		<?php
	}
	
	
	
	
	/* Add Entry */
	private function add_entry($entry)
	{
		define("LABORATOR_DATAOPT_NOFILTER", 1);
		
		$entries = $this->get_entries();
		$next_id = $this->get_next_id($entries);
		

		# Add Order ID		
		$order_id = count($entries);
		$entry['order_id'] = $order_id;
			
			
		$entries[$next_id] = $entry;

		update_option($this->access_global, $entries);
		
		return $next_id;
	}
	
	
	/* Edit Entry */
	private function edit_entry($entry_id, $new_entry)
	{
		define("LABORATOR_DATAOPT_NOFILTER", 1);
		
		$entries = $this->get_entries();
		
		if($entries[$entry_id])
		{
			$entries[$entry_id] = $new_entry;
			update_option($this->access_global, $entries);
		}
		
		return false;
	}
	
	
	# Generate New ID based on entries
	private function get_next_id($entries, $add = 0)
	{
		$id = 1 + $add;
		
		foreach($entries as $index => $dt)
		{
			$id = $add + $index + 1;
		}
		
		$id++;
		
		if(isset($entries[$id]))
			return $this->get_next_id($entries, $id + 1);
		
		return $id;
	}
	
	# Generate Largest ID based on entries
	private function get_next_order_id($entries, $add = 0)
	{
		$order_id = 1 + $add;
		
		foreach($entries as $index => $dt)
		{
			$order_id = max($dt['order_id'], $order_id);
		}
		
		$order_id++;
		
		return $order_id;
	}
	
	
	/* Delete Entry */
	private function delete_entry($entry_id)
	{
		$entries = $this->get_indexed_entries(true);
		
		if(isset($entries[$entry_id]))
		{
			$entry = $entries[$entry_id];
			
			# DELETE OTHER RESOURCES BEFORE REMOVAL
			$upload_dir = wp_upload_dir();
			
			foreach($entry as $field_id => $value)
			{
				if( ! isset($this->fields[$field_id]))
					continue;
					
				$field = $this->fields[$field_id];
				
				switch($field['field_type'])
				{
					case 'file':
					case 'image':
					
						# Delete Belonging File(s)
						if( ! is_array($value))
							$value = array('file_to_delete' => str_replace(ABSPATH, '', $upload_dir['basedir']) . DIRECTORY_SEPARATOR . $value);
						
						foreach($value as $prefix => $file_path)
						{
							$path_to_file = ABSPATH . $file_path;
							
							if(file_exists($path_to_file))
								@unlink($path_to_file);
						}
						
						break;
				}
			}
			# END: DELETE OTHER RESOURCES BEFORE REMOVAL
			
			unset($entries[$entry_id]);
			update_option($this->access_global, $entries);
			
			return true;
		}
		
		return false;
	}
	
	
	/* Delete File From Entry */
	private function delete_file_from_entry($entry_id, $field_id)
	{
		$entry = $this->get_entry($entry_id);
		
		if($value = $entry[$field_id])
		{
			$field = $this->fields[$field_id];
			
			$entries = $this->get_entries();
			$upload_dir = wp_upload_dir();
			
			if( ! is_array($value))
				$value = array('file_to_delete' => $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $value);
			
			# Image Type
			if($field['params']['is_image'])
			{
				foreach($value as $k => & $file)
				{
					$file = ABSPATH . _wp_relative_upload_path($file);
				}
			}
			
			# Delete Files (array)
			foreach($value as $k => $path_to_file)
			{			
				@unlink($path_to_file);	
			}
						
			$entry[$field_id] = '';
			
			$entries[$entry_id] = $entry;
			
			update_option($this->access_global, $entries);
			
			return true;
		}
		
		return false;
	}
	
	
	/* Get Entries */
	public function get_indexed_entries($no_filter = false)
	{
		$entries = get_option($this->access_global);
		
		if( ! is_array($entries))
			return array();
		
		foreach($entries as $entry_id => &$entry)
		{
			if( ! is_array($entry))
				$entry = (array)$entry;
				
			$entry['ID'] = $entry_id;
		}
		
		# WPML Filter
		if( ! defined("LABORATOR_DATAOPT_NOFILTER") && ! $no_filter && $this->is_multilang() && $this->get_language_code())
		{
			$lang_code = $this->get_language_code();
			$entries = array_filter($entries, array( & $this, 'filter_by_language_code'));
		}
		
		
		$descending_sort = strtoupper($this->order) == 'DESC';
		
		if( ! defined('DSORT'))
			define('DSORT', $descending_sort);
		
		uasort($entries, array( & $this, 'sort_entries'));
		
		return $entries;
	}
	
	public function sort_entries($a, $b)
	{		
		if(DSORT)
			return $a['order_id'] < $b['order_id'] ? 1 : -1;
		else
			return $a['order_id'] > $b['order_id'] ? 1 : -1;
	}
	
	public function get_entries($entry_id = '')
	{		
		if($this->sortable && ! $entry_id)
		{
			return $this->get_sorted_entries();
		}
		
		$entries = $this->get_indexed_entries();
		
		if( ! is_array($entries))
			return array();
		
		if($entry_id)
		{
			return $entries[$entry_id];
		}
			
		return $entries;
	}
	
	
	public function get_sorted_entries()
	{
		$entries = $this->get_indexed_entries();
		
		if( ! is_array($entries))
			return array();
		
		$sorting_enabled = false;
		$first = reset($entries);
		
		
		if($first && array_key_exists('order_id', $first))
		{
			$sorting_enabled = true;
		}
		
		return $entries;
	}
	
	
	public function total_entries()
	{
		return count( $this->get_indexed_entries() );
	}
	
	
	/* Get Single Entry */
	public function get_entry($field_id)
	{
		return $this->get_entries($field_id);
	}

	
	
	/* Sorting */
	public function process_sorting()
	{
		$instance_id = $_POST['instance_id'];
		
		if($instance_id == $this->instance_id)
		{
			$new_order = $_POST['new_order'];
			
			$entries = $this->get_indexed_entries();
			
			$descending_sort = strtoupper($this->order) == 'DESC';
			
			if($descending_sort)
				$new_order = array_reverse($new_order);
			
			foreach($new_order as $order_id => $entry_id)
			{
				$entries[$entry_id]['order_id'] = $order_id;
			}
			
			update_option($this->access_global, $entries);
			
			die();
		}
	}
	
	
	/* Pagination */
	private $pagination_key = 'pg_num';
	private $pagination_data = array();
	
	public function paginate_items($entries)
	{
		$total_entries = count($entries);
		$per_page = $this->per_page;
		
		$current_page = max((isset($_GET[$this->pagination_key]) ? $_GET[$this->pagination_key] : 0), 1);
		$max_pages = ceil($total_entries / $per_page);
		
		if($current_page > $max_pages)
			$current_page = $max_pages;

		$offset = ($current_page - 1) * $per_page;
		
		$entries_paginated = array_splice($entries, $offset, $per_page);
		
		# Set Pagination Info
		$this->pagination_data['max_pages'] 		= $max_pages;
		$this->pagination_data['current_page'] 		= $current_page;
		$this->pagination_data['offset'] 			= $offset;
		$this->pagination_data['per_page'] 			= $per_page;
		$this->pagination_data['total_entries'] 	= $total_entries;
		$this->pagination_data['entries_count_now'] = count($entries_paginated);
		$this->pagination_data['numbers_to_show'] 	= 10;
		
		
		$_pagination_key = isset($_GET[$this->pagination_key]) ? $_GET[$this->pagination_key] : 1;
		
		if($_pagination_key == -1)
		{
			$this->pagination_data['entries_count_now'] = $total_entries;
			return $this->get_entries();
		}
		
		return $entries_paginated;
	}
	
	public function generate_pagination()
	{
		extract($this->pagination_data);
		
		$pg_num = isset($_GET[$this->pagination_key]) ? $_GET[$this->pagination_key] : 1;
		
		if($max_pages > 1 && $pg_num != -1)
		{
			$add_sub_1 = round($numbers_to_show/2);
			$add_sub_2 = round($numbers_to_show - $add_sub_1);
			
			$from = $current_page - $add_sub_1;
			$to = $current_page + $add_sub_2;
			
			$limits_exceeded_l = false;
			$limits_exceeded_r = false;
			
			if($from < 1)
			{
				$from = 1;
				$limits_exceeded_l = true;
			}
			
			if($to > $max_pages)
			{
				$to = $max_pages;
				$limits_exceeded_r = true;
			}
			
			
			if($limits_exceeded_l)
			{
				$from = 1;
				$to = $numbers_to_show;
			}
			else
			if($limits_exceeded_r)
			{
				$from = $max_pages - $numbers_to_show + 1;
				$to = $max_pages;
			}
			else
			{
				$from += 1;
			}
			
			if($from < 1)
				$from = 1;
			
			if($to > $max_pages)
			{
				$to = $max_pages;
			}
			
			$show_all_entries = esc_url(add_query_arg($this->pagination_key, -1));
			
			?>
			<div id="laborator_dataopt_pagination" class="clearfix">
				<ul class="list">
				<?php
				
				if($current_page > 1)
				{
					$page_num_url = add_query_arg($this->pagination_key, $current_page - 1);
					?>
					<li>
						<a href="<?php echo $page_num_url; ?>">Previous</a>
					</li>
					<?php
				}
				
				for($i=$from; $i<=$to; $i++)
				{
					$page_num_url = add_query_arg($this->pagination_key, $i);
					?>
					<li>
						<a<?php echo $i == $current_page ? ' class="current-page"' : ''; ?> href="<?php echo $page_num_url; ?>"><?php echo $i; ?></a>
					</li>
					<?php
				}
				
				if($max_pages > $current_page)
				{
					$page_num_url = add_query_arg($this->pagination_key, $current_page + 1);
					?>
					<li>
						<a href="<?php echo $page_num_url; ?>">Next</a>
					</li>
					<?php
				}
				?>
				</ul>
				
				<span class="pagination_info">
					You are viewing page <strong><?php echo $current_page; ?></strong> of <strong><?php echo $max_pages; ?></strong>.
					Showing entries from <?php echo ($current_page - 1) * $per_page; ?> - <?php echo ($current_page - 1) * $per_page + $entries_count_now; ?>
					
					<?php if($pg_num != -1): ?> - <a href="<?php echo $show_all_entries; ?>">Show all entries</a>
					<?php endif; ?>
				</span>
			</div>
			
			<style>
			#laborator_dataopt_pagination {
				list-style: none;
				margin: 0px !important;
				margin-bottom: 10px;
				text-align: center;
			}
			
			#laborator_dataopt_pagination .list {
				margin: 0px;
				margin-bottom: 10px;
			}
			
			#laborator_dataopt_pagination .list li {
				display: inline-block !important;
			}
			
			
			#laborator_dataopt_pagination .list li a {
				display: inline-block;
				padding: 3px 8px;				
				background-color: #F1F1F1;
				background-image: -ms-linear-gradient(top,#F9F9F9,#ECECEC);
				background-image: -moz-linear-gradient(top,#F9F9F9,#ECECEC);
				background-image: -o-linear-gradient(top,#F9F9F9,#ECECEC);
				background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(#ECECEC));
				background-image: -webkit-linear-gradient(top,#F9F9F9,#ECECEC);
				background-image: linear-gradient(top,#F9F9F9,#ECECEC);
				color: #777;
				text-decoration: none;
				border: 1px solid #E0E0E0;
				border-radius: 3px;
				min-width: 13px;
				text-align: center;
			}
			
			#laborator_dataopt_pagination .list li a.current-page {
				font-weight: bold;
				color: @dark-text-color;
				background: #F1F1F1 !important;
				border: 1px solid #CCC;
			}
			
			.clearfix:after {
				content: ".";
				display: block;
				clear: both;
				visibility: hidden;
				line-height: 0;
				height: 0;
			}
			
			.clearfix {
				display: inline-block;
			}
			
			html[xmlns] .clearfix {
				display: block;
			}
			
			* html .clearfix {
				height: 1%;
			}
			</style>
			<?php
		}
	}
	
	
	
	/* Render Table Fields */
	
	# Actions Field
	public function render_table_actions($entry_id, $entry)
	{
		?>
		<a href="<?php echo esc_url(remove_query_arg("delete_{$this->instance_id}", add_query_arg(array("edit_{$this->instance_id}" => $entry_id, 'action' => 'edit')))); ?>">Edit</a> -
		<span class="trash"><a href="<?php echo esc_url(remove_query_arg("edit_{$this->instance_id}", add_query_arg(array("delete_{$this->instance_id}" => $entry_id)))); ?>" class="confirm_laborator_dataopt_delete">Delete</a></span>
		<?php
	}
	
	
	# File Field
	public function render_table_file($entry_id, $entry, $field_id)
	{
		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];
		
		$file_path = $entry[$field_id];
		
		if($file_path)
		{
			$file_type = wp_check_filetype($file_path);
			$extension = $file_type['ext'];
			$mime = $file_type['type'];
			
			$file_name = basename($file_path);
			$file_url = $base_url . '/' . $file_path;
			
			?>
			<a href="<?php echo $file_url; ?>" style="background-image:url(<?php echo wp_mime_type_icon($mime); ?>);" class="file_render type_<?php echo $extension; ?>" target="_blank"><?php echo basename($file_name); ?></a>
			<?php
		}
		else
		{
			echo '/';
		}
	}
	
	
	# Image Field
	public function render_table_image($entry_id, $entry, $field_id)
	{
		if(is_array($entry)):
			
			$images_arr = $entry[$field_id];
			
			if( ! is_array($images_arr))
				return;
			
			# Original Image
			$original = $images_arr['original']; # or reset($entry);
			
			# Lookup for default table thumbnail ('th' prefix)
			$thumbnail = $images_arr['th'];
			
			# Use Predefined Thumbnail
			$thumbnail_prefix 	= isset($this->fields[$field_id]['params']['table_thumbnail']) ? $this->fields[$field_id]['params']['table_thumbnail'] : '';
			$thumbnail_width 	= isset($this->fields[$field_id]['params']['table_thumbnail_width']) ? $this->fields[$field_id]['params']['table_thumbnail_width'] : '';
			$thumbnail_height 	= isset($this->fields[$field_id]['params']['table_thumbnail_height']) ? $this->fields[$field_id]['params']['table_thumbnail_height'] : '';
			
			if($thumbnail_prefix)
			{
				$thumbnail_prefix = preg_replace('/(_|-)$/', '', $thumbnail_prefix);
				
				if($images_arr[$thumbnail_prefix])
					$thumbnail = $images_arr[$thumbnail_prefix];
			}
			
			
			# Use first image after original image as thumbnail
			if(!$thumbnail)
			{
				array_shift($images_arr);
				$thumbnail = reset($images_arr);
			}
			
			?>
			<div class="image_render">
				<a href="<?php echo site_url($original); ?>" target="_blank">
					<img src="<?php echo site_url($thumbnail); ?>" alt="<?php echo "image-{$entry_id}"; ?>"<?php echo $thumbnail_width ? " width=\"{$thumbnail_width}\"" : ''; echo $thumbnail_height ? " height=\"{$thumbnail_height}\"" : ''; ?> />
				</a>
			</div>
			<?php
		
		else:
			echo '/';
		endif;
	}
	
	
	# Checkbox and Radio Field Renderer
	public function render_checkbox_radio($entry_id, $entry, $field_id)
	{
		$checked_options 	= $entry[$field_id];
		
		$multiple_options 	= isset($this->fields[$field_id]['params']['multiple_options']) ? $this->fields[$field_id]['params']['multiple_options'] : array();
		$is_radio 			= isset($this->fields[$field_id]['params']['is_radio']) ? $this->fields[$field_id]['params']['is_radio'] : array();
		
		if($multiple_options)
		{
			$options = $this->fields[$field_id]['options'];
			
			if(is_array($checked_options) && count($checked_options))
			{
				foreach($checked_options as $key => $val)
				{
					$val_txt = $key;
					
					if($options[$key])
						$val_txt = $options[$key];
						
					?>
					<div><?php echo $val_txt; ?></div>
					<?php
				}
			}
			else
			if($is_radio && $checked_options)
			{
				$val_txt = $checked_options;
				
				if($options[$val_txt])
					$val_txt = $options[$val_txt];
				
				?>
				<div><?php echo $val_txt; ?></div>
				<?php
			}
			else
			{
			
				echo '<em class="gray">' . $this->labels['none'] . '</em>';
			}
		}
		else
		{
			?>
			<div><?php echo $checked_options ? $this->labels['checked'] : $this->labels['unchecked']; ?></div>
			<?php
		}
	}
		
	
	# Select Field Renderer
	public function render_table_select($entry_id, $entry, $field_id)
	{
		$options 		= $this->fields[$field_id]['options'];
		$current_value 	= $entry[ $field_id ];
		
		if(isset($options[$current_value]))
		{
			echo $options[$current_value];
		}
		else
		{
			echo $current_value;
		}
	}
	
	/* V1.2 */
	public function is_multilang()
	{
		return $this->multilang;
	}
	
	
	private function set_current_language()
	{
		$lang_code = '';
		
		if(defined("ICL_LANGUAGE_CODE"))
		{
			$lang_code = strtolower(ICL_LANGUAGE_CODE);
		}
		
		$this->multilang_code = $lang_code;
	}
	
	public function get_language_code()
	{
		return $this->multilang_code;
	}
	
	public function filter_by_language_code($entry)
	{
		global $sitepress;
		
		if( ! $sitepress || ! method_exists($sitepress, 'get_language_code'))
			return true;
		
		$code = $this->get_language_code();
		$is_default_language = $sitepress->get_default_language() == $this->multilang_code;

		
		if($code && isset($entry['_lang']) && $entry['_lang'] != $code)
		{
			if($is_default_language && $entry['_lang'] == '')
				return true;
				
			return false;
		}
		
		if( ! isset($entry['_lang']))
		{
			return false;
		}
		
		return true;
	}
}