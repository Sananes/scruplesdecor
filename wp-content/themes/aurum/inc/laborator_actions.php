<?php
/**
 *	Aurum WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */


# Base Functionality
function laborator_init()
{
	# Styles
	wp_register_style('admin-css', THEMEASSETS . 'css/admin/main.css', null, null);
	wp_register_style('bootstrap', THEMEASSETS . 'css/bootstrap.css', null, null);
	wp_register_style('bootstrap-rtl', THEMEASSETS . 'css/bootstrap-rtl.css', null, null);
	wp_register_style('aurum-main', THEMEASSETS . 'css/aurum.css', null, null);

	wp_register_style('animate-css', THEMEASSETS . 'css/animate.css', null, null);

	wp_register_style('icons-entypo', THEMEASSETS . 'css/fonts/entypo/css/entyporegular.css', null, null);

	wp_register_style('style', get_template_directory_uri() . '/style.css', null, null);



	# Scripts
	wp_register_script('bootstrap', THEMEASSETS . 'js/bootstrap.min.js', null, null, true);
	wp_register_script('tweenmax', THEMEASSETS . 'js/TweenMax.min.js', null, null, true);
	wp_register_script('joinable', THEMEASSETS . 'js/min/joinable.min.js', null, null, true);
	wp_register_script('aurum-custom', THEMEASSETS . 'js/min/aurum-custom.min.js', null, null, true);
	wp_register_script('aurum-contact', THEMEASSETS . 'js/min/aurum-contact.min.js', null, null, true);



	# Nivo Lightbox
	wp_register_script('nivo-lightbox', THEMEASSETS . 'js/nivo-lightbox/nivo-lightbox.min.js', null, null, true);
	wp_register_style('nivo-lightbox', THEMEASSETS . 'js/nivo-lightbox/nivo-lightbox.css', null, null);
	wp_register_style('nivo-lightbox-default', THEMEASSETS . 'js/nivo-lightbox/themes/default/default.css', array('nivo-lightbox'), null);

	# Owl Carousel
	if(is_rtl())
	{
		wp_register_script('owl-carousel', THEMEASSETS . 'js/owl-carousel/rtl/owl.carousel.js', null, null, true);
		wp_register_style('owl-carousel', THEMEASSETS . 'js/owl-carousel/rtl/owl.carousel.css', null, null);
	}
	else
	{
		wp_register_script('owl-carousel', THEMEASSETS . 'js/owl-carousel/owl.carousel.min.js', null, null, true);
		wp_register_style('owl-carousel', THEMEASSETS . 'js/owl-carousel/owl.carousel.css', null, null);
	}



	# Owl Carousel 2
	wp_register_script('owl-carousel-2', THEMEASSETS . 'js/owl-carousel-2/owl.carousel.min.js', null, null, true);
	wp_register_style('owl-carousel-2', THEMEASSETS . 'js/owl-carousel-2/assets/owl.carousel.css', null, null);

	# Bootstrap Select
	wp_register_script('bootstrap-select', THEMEASSETS . 'js/min/bootstrap-select.min.js', null, null, true);

	# Cycle 2
	wp_register_script('cycle-2', THEMEASSETS . 'js/jquery.cycle2.min.js', null, null, true);

	# Google Maps
	wp_enqueue_script('google-maps', '//maps.googleapis.com/maps/api/js?sensor=false', null, null, true);

}


# Enqueue Scritps and other stuff
function laborator_wp_enqueue_scripts()
{
	# Styles
	$rtl_include = '';

	wp_enqueue_style(array('icons-entypo', 'bootstrap', 'aurum-main', 'style'));


	if(is_rtl())
	{
		wp_enqueue_style(array('bootstrap-rtl'));
	}


	# Scripts
	wp_enqueue_script(array('jquery', 'bootstrap', 'tweenmax', 'joinable'));
}


# Print scripts in the header
function laborator_wp_print_scripts()
{
?>
<script type="text/javascript">
var ajaxurl = ajaxurl || '<?php echo esc_attr( admin_url("admin-ajax.php") ); ?>';
</script>
<?php
}


# After Setup Theme
function laborator_after_setup_theme()
{
	# Theme Support
	add_theme_support('menus');
	add_theme_support('widgets');
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');
	add_theme_support('featured-image');
	add_theme_support('woocommerce');


	# Theme Textdomain
	load_theme_textdomain(TD, get_template_directory() . '/languages');


	# Custom Post Types

		# Testimonials Post type
		register_post_type( 'testimonial',
			array(
				'labels' => array(
					'name'          => __( 'Testimonials', TD),
					'singular_name' => __( 'Testimonial', TD)
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
				'menu_icon' => 'dashicons-testimonial'
			)
		);


	# Register Menus
	register_nav_menus(
		array(
			'main-menu'      => 'Main Menu',
			'secondary-menu' => 'Secondary Menu',
			'mobile-menu'    => 'Mobile Menu',
		)
	);


	# Gallery Boxes
	new GalleryBox('post_slider_images', array('title' => 'Post Slider Images', 'post_types' => array('post')));
}



# Laborator Menu Page
function laborator_menu_page()
{
	add_menu_page('Laborator', 'Laborator', 'edit_theme_options', 'laborator_options', 'laborator_main_page');

	if(get('page') == 'laborator_options')
	{
		wp_redirect( admin_url('themes.php?page=theme-options') );
	}
}


# Redirect to Theme Options
function laborator_options()
{
	wp_redirect( admin_url('themes.php?page=theme-options') );
}


# Documentation Page iFrame
function laborator_menu_documentation()
{
	add_submenu_page('laborator_options', 'Documentation', 'Help', 'edit_theme_options', 'laborator_docs', 'laborator_documentation_page');
}

function laborator_documentation_page()
{
	add_thickbox();
?>
<div class="wrap">
	<h2>Documentation</h2>

	<p>You can read full theme documentation by clicking the button below:</p>

	<p>
		<a href="http://documentation.laborator.co/aurum" class="button button-primary" id="lab_read_docs">Read Documentation</a>
	</p>


	<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		$("#lab_read_docs").click(function(ev)
		{
			ev.preventDefault();

			var href = $(this).attr('href');

			tb_show('Theme Documentation' , href + '?TB_iframe=1&width=1024&height=568');
		});
	});
	</script>

	<style>
		.lab-faq-links {

		}

		.lab-faq-links li {
			margin-top: 18px;
			background: #FFF;
			border: 1px solid #E0E0E0;
			padding: 0;
		}

		.lab-faq-links li > strong {
			display: block;
			padding: 10px 15px;
			background: rgba(238,238,238,0.6);
		}

		.lab-faq-links li pre {
			font-size: 13px;
			max-width: 100%;
			word-break: break-word;
			padding: 10px 15px;
			padding-top: 5px;
		}

		.lab-faq-links .warn {
			display: block;
			font-family: Arial, Helvetica, sans-serif;
			border: 1px solid #999;
			padding: 10px;
			font-size: 12px;
			text-transform: uppercase;
		}
	</style>

	<br />
	<h3>Frequently Asked Questions</h3>
	<hr />

	<ul class="lab-faq-links">
		<li id="update-theme">

			<strong>How do I update the theme?</strong>

			<pre>1. Go to Envato Toolkit link in the menu (firstly activate it on: Appearance > Install Plugins)

2. There you type your username i.e. <strong>MyEnvatoUsername</strong> and your <strong>Secret API Key</strong> that can be found on &quot;My Settings&quot; page on ThemeForest,
   example: <a href="http://cl.ly/WT2j" target="_blank">http://cl.ly/WT2j</a>

3. When new update its ready, you'll see a notification under <strong>Envato Toolkit</strong> link. From there you can update the theme.</pre>
		</li>

		<li id="update-visual-composer">

			<strong>How to update Visual Composer (or any other plugin)?</strong>

			<pre>When new theme update is applied it happens often that external plugins needs to be updated too.
This is an extra step you should take in order to keep some plugins up to date (not all necessarily).

To update a specific plugin, for this example we will take "Visual Composer" follow these steps:

1. Go to Plugins, find "WPBakery Visual Composer" plugin (relevant plugin you want to update).

2. Click "Deactivate", when page refreshes click "Delete" on the same plugin.

3. Go to <strong>Appearance > Install Plugins</strong>, find the Visual Composer (or relevant) plugin click Install then Activate.

4. Everything is done.

The same steps can be repeated for other plugins in the <strong>Install Plugins</strong> list.

<strong class="warn">Important Note: You don't have to buy these plugins, they are bundled with the theme</strong></pre>
		</li>

		<li id="regenerate-thumbnails">

			<strong>Regenerate Thumbnails</strong>

			<pre>If your thumbnails are not correctly cropped, you can regenerate them by following these steps:

1. Go to Plugins > Add New

2. Search for "<strong>Regenerate Thumbnails</strong>" (created by <strong>Viper007Bond</strong>)

3. Install and activate that plugin.

4. Go to Tools > Regen. Thumbnails

5. Click "Regenerate All Thumbnails" button and let the process to finish till it reaches 100 percent.</pre>
		</li>
	</ul>
</div>
<?php
}


# Admin Enqueue
function laborator_admin_enqueue_scripts()
{
	wp_enqueue_style('admin-css');
}



# Admin Print Styles
function laborator_admin_print_styles()
{
?>
<style>

#toplevel_page_laborator_options .wp-menu-image {
	background: url(<?php echo get_template_directory_uri(); ?>/assets/images/laborator-icon-adminmenu16-sprite.png) no-repeat 11px 8px !important;
}

#toplevel_page_laborator_options .wp-menu-image:before {
	display: none;
}

#toplevel_page_laborator_options .wp-menu-image img {
	display: none;
}

#toplevel_page_laborator_options:hover .wp-menu-image, #toplevel_page_laborator_options.wp-has-current-submenu .wp-menu-image {
	background-position: 11px -24px !important;
}

</style>
<?php
}



function laborator_wp_head()
{
	laborator_load_font_style();
?>

	<!--[if lt IE 9]>
	<script src="<?php echo THEMEASSETS; ?>js/ie8-responsive-file-warning.js"></script>
	<![endif]-->

	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

<?php
}


function laborator_wp_footer()
{
	# Custom.js
	wp_enqueue_script('aurum-custom');

	# Tracking Code
	echo get_data('google_analytics');

	# Page Generation Speed
	#echo '<!-- Generated in ' . (microtime(true) - STIME) . ' seconds -->';
}



# Fav Icon
function laborator_favicon()
{
	$favicon_image = get_data('favicon_image');
	$apple_touch_icon = get_data('apple_touch_icon');

	if($favicon_image || $apple_touch_icon)
	{
	?>
	<!-- Favicons -->
	<?php if($favicon_image): ?>
	<link rel="shortcut icon" href="<?php echo $favicon_image; ?>">
	<?php endif; ?>
	<?php if($apple_touch_icon): ?>
	<link rel="apple-touch-icon-precomposed" href="<?php echo $apple_touch_icon; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $apple_touch_icon; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $apple_touch_icon; ?>">
	<?php endif; ?>
	<?php
	}
}



# Widgets Init
function laborator_widgets_init()
{
	# Blog Sidebar
	$blog_sidebar = array(
		'id' => 'blog_sidebar',
		'name' => 'Blog Widgets',

		'before_widget' => '<div class="sidebar-entry %2$s %1$s">',
		'after_widget' => '</div>',

		'before_title' => '<h3 class="sidebar-entry-title">',
		'after_title' => '</h3>'
	);

	register_sidebar($blog_sidebar);


	# Footer Sidebar
	$footer_sidebar_column = 'col-md-2 col-sm-4';

	switch(get_data('footer_widgets_columns'))
	{
		case "two":
			$footer_sidebar_column = 'col-sm-6';
			break;

		case "three":
			$footer_sidebar_column = 'col-sm-4';
			break;

		case "four":
			$footer_sidebar_column = 'col-sm-3';
			break;
	}

	$footer_sidebar = array(
		'id' => 'footer_sidebar',
		'name' => 'Footer Widgets',

		'before_widget' =>
			'<div class="'.$footer_sidebar_column.'">'
				. '<div class="sidebar %2$s %1$s">',

		'after_widget' =>
			'</div>' .
		'</div>',

		'before_title' => '<h3>',
		'after_title' => '</h3>'
	);

	register_sidebar($footer_sidebar);


	# Shop Footer Sidebar
	$shop_footer_sidebar_column = 'col-md-2 col-sm-4';

	switch(get_data('shop_sidebar_footer_columns'))
	{
		case 2:
			$shop_footer_sidebar_column = 'col-sm-6';
			break;

		case 3:
			$shop_footer_sidebar_column = 'col-sm-4';
			break;

		case 4:
			$shop_footer_sidebar_column = 'col-md-3 col-sm-6';
			break;
	}

	$shop_footer_sidebar = array(
		'id' => 'shop_footer_sidebar',
		'name' => 'Shop Footer Widgets',

		'before_widget' =>
			'<div class="'.$shop_footer_sidebar_column.'">'
				. '<div class="sidebar-entry %2$s %1$s">',

		'after_widget' =>
			'</div>' .
		'</div>',

		'before_title' => '<h3 class="sidebar-entry-title">',
		'after_title' => '</h3>'
	);

	register_sidebar($shop_footer_sidebar);


	# Shop Sidebar
	$shop_sidebar = array(
		'id' => 'shop_sidebar',
		'name' => 'Shop Widgets',

		'before_widget' => '<div class="sidebar-entry %2$s %1$s">',
		'after_widget' => '</div>',

		'before_title' => '<h3 class="sidebar-entry-title">',
		'after_title' => '</h3>'
	);

	register_sidebar($shop_sidebar);


	# Shop Single Sidebar
	$shop_single_sidebar = array(
		'id' => 'shop_single_sidebar',
		'name' => 'Shop Single Widgets',
		'description' => 'The Widgets you put here will be shown only when viewing single product page. If there are no widgets in here, "Shop Widgets" will be shown instead.',

		'before_widget' => '<div class="sidebar-entry %2$s %1$s">',
		'after_widget' => '</div>',

		'before_title' => '<h3 class="sidebar-entry-title">',
		'after_title' => '</h3>'
	);

	register_sidebar($shop_single_sidebar);
}




# Contact Form
add_action('wp_ajax_lab_req_contact_token', 'lab_req_contact_token');
add_action('wp_ajax_nopriv_lab_req_contact_token', 'lab_req_contact_token');

add_action('wp_ajax_lab_contact_form', 'lab_contact_form');
add_action('wp_ajax_nopriv_lab_contact_form', 'lab_contact_form');

function lab_req_contact_token()
{
	$name	  = post('name');
	$subject   = post('subject');
	$email	 = post('email');
	$message   = post('message');

	$hash = md5($name . $email . $message);

	$nonce = wp_create_nonce('cf_' . $hash);

	die("{$hash}_{$nonce}");
}

function lab_contact_form()
{
	$resp = array('errors' => true);

	$id		   = post('id');

	$name	  = post('name');
	$subject   = post('subject');
	$email	 = post('email');
	$message   = post('message');

	$hash	  = '';
	$nonce	 = '';

	foreach($_POST as $key => $val)
	{
		if(strlen($key) == 32)
		{
			$hash = "cf_{$key}";
			$nonce = $val;
		}
	}

	if(wp_verify_nonce($nonce, $hash))
	{
		$admin_email = get_option('admin_email');
		$ip = $_SERVER['REMOTE_ADDR'];

		if($id)
		{
			$custom_receiver = get_post_meta($id, 'email_notifications', true);

			if(is_email($custom_receiver))
				$admin_email = $custom_receiver;
		}

		$email_subject = "[" . get_bloginfo("name") . "] New contact form message submitted.";
		$email_message = "New message has been submitted on your website contact form. IP Address: {$ip}\n\n=====\n\n";

		$fields = array('name', 'email', 'subject', 'message');

		foreach($fields as $key)
		{
			$val = post($key);

			$field_label = isset($field_names[$key]) ? $field_names[$key] : ucfirst($key);

			$email_message .= "{$field_label}:\n" . ($val ? $val : '/') . "\n\n";
		}

		$email_message .= "=====\n\nThis email has been automatically sent from Contact Form.";

		$headers = array();

		if($email)
		{
			$headers[] = "Reply-To: {$name} <{$email}>";
		}

		wp_mail($admin_email, $email_subject, $email_message, $headers);

		$resp['errors'] = false;
	}

	echo json_encode($resp);

	die();
}



# VC Theme Setup
add_action('vc_before_init', 'laborator_vc_set_as_theme');

function laborator_vc_set_as_theme()
{
	require THEMEDIR . 'inc/lib/visual-composer/config.php';

	vc_set_default_editor_post_types(array('page'));
	vc_set_as_theme();
}


# Visual Composer Mapping
add_action('vc_before_mapping', 'laborator_vc_mapping');

function laborator_vc_mapping()
{
	$dir = THEMEDIR . '/vc-shortcodes/';
	vc_set_shortcodes_templates_dir($dir);

	include_once THEMEDIR . 'inc/lib/visual-composer/map.php';
}




# Third party plugins
add_action('tgmpa_register', 'aurum_plugins');

function aurum_plugins()
{
	$plugins = array(

		array(
			'name'               => 'Visual Composer',
			'slug'               => 'js_composer',
			'source'             => get_stylesheet_directory() . '/inc/thirdparty-plugins/js_composer.zip',
			'required'           => true,
			'version'            => '',
		),

		array(
			'name'               => 'Layer Slider',
			'slug'               => 'LayerSlider',
			'source'             => get_stylesheet_directory() . '/inc/thirdparty-plugins/layersliderwp-5.3.2.installable.zip',
			'required'           => false,
			'version'            => '',
		),

		array(
			'name'               => 'Envato WordPress Toolkit',
			'slug'               => 'envato-wordpress-toolkit-1.7.0',
			'source'             => get_stylesheet_directory() . '/inc/thirdparty-plugins/envato-wordpress-toolkit.zip',
		),

		array(
			'name'               => 'Advanced Custom Fields',
			'slug'               => 'advanced-custom-fields',
			'required'           => false,
		),

		array(
			'name'               => 'ACF - Field Type: Repeater',
			'slug'               => 'acf-repeater',
			'source'             => get_stylesheet_directory() . '/inc/thirdparty-plugins/acf-repeater.zip',
			'required'           => false,
		),

		array(
			'name'               => 'Advanced Custom Fields - Field Type: Coordinates',
			'slug'               => 'advanced-custom-fields-coordinates',
			'required'           => false,
		),

		array(
			'name'               => 'WooCommerce',
			'slug'               => 'woocommerce',
			'required'           => false,
		),

	);

	$config = array(
		'default_path'    => '',
		'menu'            => 'tgmpa-install-plugins',
		'has_notices'     => true,
		'dismissable'     => true,
		'dismiss_msg'     => '',
		'is_automatic'    => false,
		'message'         => '',
		'strings'         => array(
			'page_title'                         => __( 'Install Required Plugins', 'tgmpa' ),
			'menu_title'                         => __( 'Install Plugins', 'tgmpa' ),
			'installing'                         => __( 'Installing Plugin: %s', 'tgmpa' ),
			'oops'                               => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
			'notice_can_install_required'        => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_install_recommended'     => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_install'              => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
			'notice_can_activate_required'       => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_activate_recommended'    => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_activate'             => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
			'notice_ask_to_update'               => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_update'               => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
			'install_link'                       => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link'                      => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
			'return'                             => __( 'Return to Required Plugins Installer', 'tgmpa' ),
			'plugin_activated'                   => __( 'Plugin activated successfully.', 'tgmpa' ),
			'complete'                           => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ),
			'nag_type'                           => 'updated'
		)
	);

	tgmpa( $plugins, $config );
}



# Remove greensock from LayerSlider because it causes theme incompatibility issues
add_action('wp_enqueue_scripts', 'layerslider_remove_greensock');

function layerslider_remove_greensock()
{
	wp_dequeue_script('greensock');
}