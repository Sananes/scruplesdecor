<?php
/**
 *	Laborator 1 Click Demo Content Importer
 *
 *	Developed by: Arlind Nushi
 *	URL: www.laborator.co
 */

# Run wordpress importer independently
require 'wordpress-importer/wordpress-importer.php';

# Demo Content File (XML)
$file = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $pack['file'];

# Theme Options Import
$theme_options = $pack['options'];

if($theme_options)
{
	$theme_options = dirname(__FILE__) . DIRECTORY_SEPARATOR . $theme_options;

	if($theme_options = file_get_contents($theme_options))
	{
		$smof_data = unserialize(base64_decode($theme_options));
		of_save_options($smof_data);
	}
}

# Theme Options Import
$custom_css = $pack['custom_css'];

if($custom_css)
{
	$custom_css = dirname(__FILE__) . DIRECTORY_SEPARATOR . $custom_css;

	if($custom_css = file_get_contents($custom_css))
	{
		$custom_css_options = json_decode(base64_decode($custom_css));
		lab_1cl_demo_installer_custom_css_save($custom_css_options);
	}
}

?>
<style>
body {
	background: #f0f0f0;
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	padding: 5px 25px;
	font-size: 14px;
}

a {
	color: #555;
	text-decoration: none;
}

a:hover {
	color: #111;
}

pre {
	border-left: 5px solid #ccc;
	padding-left: 20px;
	margin-bottom: 25px;
}

.btn {
	background: #333;
	color: #fff;
	display: inline-block;
	padding: 5px 10px;
	border-radius: 3px;
	text-transform: uppercase;
}

.btn:hover {
	color: #ccc;
}
</style>
	<h2>
		Installing demo content pack <strong><?php echo $pack['name']; ?></strong>...
	</h2>

	<small>Importing demo content may take a while, please be patient...</small>

<pre><?php
set_time_limit(0);

$wp_importer = new WP_Import();

$wp_importer->fetch_attachments = get_option('lab_1cl_demo_installer_download_media', true) ? true : false;
$wp_importer->id = sanitize_title($pack['title']);

$wp_importer->import($file);
?></pre>

<a href="<?php echo admin_url("edit.php"); ?>" target="_parent" class="btn">Go to posts &raquo;</a>

<?php

# Import Layer Slider
if($pack['layerslider'] && function_exists('ls_import_sliders'))
{
	$layerslider = dirname(__FILE__) . DIRECTORY_SEPARATOR . $pack['layerslider'];

	include LS_ROOT_PATH.'/classes/class.ls.importutil.php';

	$import = new LS_ImportUtil($layerslider, basename($layerslider));
}

if(get_option('lab_should_activate_wp_importer'))
{
	$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
	$active_plugins[] = 'wordpress-importer/wordpress-importer.php';

	update_option('active_plugins', $active_plugins);
	delete_option('lab_should_activate_wp_importer');
}
?>