<?php
/**
 *	Laborator 1 Click Demo Content Importer
 *
 *	Version: 1.0
 *
 *	Developed by: Arlind Nushi
 *	URL: www.laborator.co
 */

# Get Demo Content Packs
function lab_1cl_demo_installer_get_packs()
{
	return array(
		array(
			# Pack Info
			'name'           => 'Fashion Store',
			'desc'           => 'This will install Aurum fashion store demo content. It includes all theme features. All images are grey (takes 1-3 mins to install).',

			# Pack Data
			'thumb'          => 'demo-content/fashion-store/screenshot.png',
			'file'           => 'demo-content/fashion-store/content.xml',
			'options'        => 'demo-content/fashion-store/options.json',
			'layerslider'    => 'demo-content/fashion-store/layerslider.zip',
			'custom_css'     => '',
		),

		array(
			# Pack Info
			'name'           => 'Jewelry Store',
			'desc'           => 'This will install Aurum jewelry store demo content. Has only basic features.',

			# Pack Data
			'thumb'          => 'demo-content/jewelry-store/screenshot.png',
			'file'           => 'demo-content/jewelry-store/content.xml',
			'options'        => 'demo-content/jewelry-store/options.json',
			'layerslider'    => '',
			'custom_css'     => 'demo-content/jewelry-store/css.json',
		),

		array(
			# Pack Info
			'name'           => 'Tech Store',
			'desc'           => 'This will install Aurum technology store demo content. Has only basic features.',

			# Pack Data
			'thumb'          => 'demo-content/tech-store/screenshot.png',
			'file'           => 'demo-content/tech-store/content.xml',
			'options'        => 'demo-content/tech-store/options.json',
			'layerslider'    => 'demo-content/tech-store/layerslider.zip',
			'custom_css'     => 'demo-content/tech-store/css.json',
		),
		array(
			# Pack Info
			'name'           => 'Multilingual Site',
			'desc'           => 'This will install multilingual demo content. The content is available in 4 languages.',

			# Pack Data
			'thumb'          => 'demo-content/multilingual-site/screenshot.png',
			'file'           => 'demo-content/multilingual-site/content.xml',
			'options'        => 'demo-content/multilingual-site/options.json',
			'layerslider'    => 'demo-content/multilingual-site/layerslider.zip',
			'custom_css'     => '',
		),
	);
}


# Required Plugins
function lab_1cl_demo_installer_get_required_plugins()
{
	return array(
		'LayerSlider/layerslider.php' => 'Layer Slider',
		'woocommerce/woocommerce.php' => 'WooCommerce',
	);
}


function lab_1cl_demo_installer_required_plugins_missing()
{
	$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

	$required_plugins = lab_1cl_demo_installer_get_required_plugins();
	$missing_plugins = array();

	foreach($required_plugins as $plugin_id => $plugin_name)
	{
		if( ! in_array($plugin_id, $active_plugins))
		{
			$missing_plugins[$plugin_id] = $plugin_name;
		}
	}

	return $missing_plugins;
}


# Importer Page
add_action('admin_menu', 'lab_1cl_demo_installer_menu');

function lab_1cl_demo_installer_menu()
{
	add_submenu_page('laborator_options', '1-Click Demo Content Installer', 'Demo Content Install', 'edit_theme_options', 'laborator_demo_content_installer', 'lab_1cl_demo_installer_page');
}

function lab_1cl_demo_installer_page()
{
	# Change Media Download Status
	if(isset($_POST['lab_change_media_status']))
	{
		update_option('lab_1cl_demo_installer_download_media', post('lab_1cl_demo_installer_download_media') ? true : false);
	}

	$lab_demo_content_url = site_url(str_replace(ABSPATH, '', dirname(__FILE__)) . '/');

	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	include 'demo-content-page.php';
}


function lab_1cl_demo_installer_get_pack($name)
{
	foreach(lab_1cl_demo_installer_get_packs() as $pack)
	{
		if(sanitize_title($pack['name']) == $name)
		{
			return $pack;
		}
	}

	return null;
}


# Import Content Pack
add_action('admin_init', 'lab_1cl_demo_installer_admin_init');

function lab_1cl_demo_installer_admin_init()
{
	if(get('page') == 'laborator_demo_content_installer' && ($pack_name = get('install-pack')))
	{
		$pack = lab_1cl_demo_installer_get_pack($pack_name);

		if($pack)
		{
			if(is_plugin_active('wordpress-importer/wordpress-importer.php'))
			{
				deactivate_plugins(array('wordpress-importer/wordpress-importer.php'));
				update_option('lab_should_activate_wp_importer', true);

				wp_redirect(admin_url('admin.php?page=laborator_demo_content_installer&install-pack="' . sanitize_title($pack_name)));
				exit;
			}

			require 'demo-content-install-pack.php';

			die();
		}
	}
}


# Save Custom CSS Options
function lab_1cl_demo_installer_custom_css_save($custom_css_vars)
{
	foreach($custom_css_vars as $var_name => $value)
	{
		update_option($var_name, $value);
	}
}