<?php
add_action('init', 'TheShortcodesForVC');
function TheShortcodesForVC() {
	if (!class_exists('WPBakeryVisualComposerAbstract')) { // or using plugins path function
		return;
	}
	// Remove Front End 
	vc_disable_frontend();
	// Settings
	
  vc_set_as_theme(true);
  
  // Removing Default shortcodes
  vc_remove_element("vc_widget_sidebar");
  vc_remove_element("vc_wp_search");
  vc_remove_element("vc_wp_meta");
  vc_remove_element("vc_wp_recentcomments");
  vc_remove_element("vc_wp_calendar");
  vc_remove_element("vc_wp_pages");
  vc_remove_element("vc_wp_tagcloud");
  vc_remove_element("vc_wp_custommenu");
  vc_remove_element("vc_wp_text");
  vc_remove_element("vc_wp_posts");
  vc_remove_element("vc_wp_links");
  vc_remove_element("vc_wp_categories");
  vc_remove_element("vc_wp_archives");
  vc_remove_element("vc_wp_rss");
  vc_remove_element("vc_teaser_grid");
  vc_remove_element("vc_button");
  vc_remove_element("vc_cta_button");
  vc_remove_element("vc_message");
  vc_remove_element("vc_progress_bar");
  vc_remove_element("vc_pie");
  vc_remove_element("vc_posts_slider");
  vc_remove_element("vc_posts_grid");
  vc_remove_element("vc_images_carousel");
  vc_remove_element("vc_carousel");
  vc_remove_element("vc_gallery");
  
  //vc_remove_element("vc_single_image");
  
  add_action( 'admin_head', 'remove_my_meta_box' );
  
  function remove_my_meta_box() {
  	remove_meta_box("vc_teaser", "portfolio", "side");
  	remove_meta_box("vc_teaser", "page", "side"); 
  	remove_meta_box("vc_teaser", "product", "side"); 
  }
  
  // Adding Extra Shortcodes
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/button/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/image/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/styled_header/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/product/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/product_list/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/product_cat/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/post/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/portfolio/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/iconlist/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/iconbox/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/lookbook/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/product_grid/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/counter/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/notification/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/banner/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/progress_bar/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/team_member/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/testimonials/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/clients/shortcode.php');
  require_once(THB_THEME_ROOT_ABS.'/vc_templates/gap/shortcode.php');
  
  /* Visual Composer Mappings */
  // Adding animation to columns
  vc_add_param("vc_column", array(
  	"type" => "dropdown",
  	"class" => "",
  	"heading" => __("Animation"),
  	"admin_label" => true,
  	"param_name" => "animation",
  	"value" => array(
  		"None" => "",
  		"Left" => "animation right-to-left",
  		"Right" => "animation left-to-right",
  		"Top" => "animation bottom-to-top",
  		"Bottom" => "animation top-to-bottom",
  		"Scale" => "animation scale",
  		"Fade" => "animation fade-in"
  	),
  	"description" => ""
  ));
  
  // Add parameters to rows
  vc_add_param("vc_row", array(
  	"type" => "checkbox",
  	"class" => "",
  	"heading" => __("Full-width Content"),
  	"param_name" => "full_width",
  	"value" => array(
  		"" => "true"
  	)
  ));
  vc_add_param("vc_row", array(
  	"type" => "checkbox",
  	"class" => "",
  	"heading" => __("Enable parallax"),
  	"param_name" => "enable_parallax",
  	"value" => array(
  		"" => "false"
  	)
  ));
  vc_add_param("vc_row", array(
  	"type" => "textfield",
  	"class" => "",
  	"heading" => __("Parallax Speed"),
  	"param_name" => "parallax_speed",
  	"value" => "1",
  	"dependency" => array(
  		"element" => "enable_parallax",
  		"not_empty" => true
  	),
  	"description" => __("A value between 0 and 1 is recommended")
  ));
  vc_add_param("vc_row", array(
  	"type" => "textfield",
  	"class" => "",
  	"heading" => __("Video background (mp4)"),
  	"param_name" => "bg_video_src_mp4",
  	"value" => "",
  	"description" => _("You must include the ogv & the mp4 format to render your video with cross browser compatibility. OGV is optional. Video must be in a 16:9 aspect ratio. The row background image will be used as in mobile devices.")
  ));
  vc_add_param("vc_row", array(
  	"type" => "textfield",
  	"class" => "",
  	"heading" => __("Video background (ogv)"),
  	"param_name" => "bg_video_src_ogv",
  	"value" => ""
  ));
  vc_add_param("vc_row", array(
  	"type" => "textfield",
  	"class" => "",
  	"heading" => __("Video background (webm)"),
  	"param_name" => "bg_video_src_webm",
  	"value" => ""
  ));
  vc_add_param("vc_row", array(
  	"type" => "colorpicker",
  	"class" => "",
  	"heading" => __("Video Overlay Color"),
  	"param_name" => "bg_video_overlay_color",
  	"value" => "",
  	"description" => __("If you want, you can select an overlay color.")
  ));
  
  // Button shortcode
  vc_map( array(
  	"name" => __("Button"),
  	"base" => "thb_button",
  	"icon" => "thb_vc_ico_button",
  	"class" => "thb_vc_sc_button",
  	"category" => "by Fuel Themes",
  	"params" => array(
  		array(
  			"type" => "textfield",
  			"class" => "",
  			"heading" => __("Caption"),
  			"admin_label" => true,
  			"param_name" => "content",
  			"value" => "",
  			"description" => ""
  		),
  		array(
  			"type" => "textfield",
  			"class" => "",
  			"heading" => __("Link URL"),
  			"param_name" => "link",
  			"value" => "",
  			"description" => ""
  		),
  		array(
  			"type" => "dropdown",
  			"class" => "",
  			"heading" => __("Icon"),
  			"param_name" => "icon",
  			"value" => getFontAwesomeIconArray(),
  			"description" => ""
  		),
  		array(
  			"type" => "dropdown",
  			"class" => "",
  			"heading" => __("Open link in"),
  			"param_name" => "target_blank",
  			"value" => array(
  				"Same window" => "",
  				"New window" => "true"
  			),
  			"description" => ""
  		),
  		array(
  			"type" => "dropdown",
  			"class" => "",
  			"heading" => __("Style"),
  			"param_name" => "size",
  			"value" => array(
  				"Small button" => "small",
  				"Medium button" => "medium",
  				"Big button" => "large"
  			),
  			"description" => ""
  		),
  		array(
  			"type" => "dropdown",
  			"class" => "",
  			"heading" => __("Button color"),
  			"param_name" => "color",
  			"value" => array(
  				"White" => "white",
  				"Light Grey" => "grey",
  				"Black" => "black",
  				"Blue" => "blue",
  				"Green" => "green",
  				"Yellow" => "yellow",
  				"Orange" => "orange",
  				"Pink" => "pink",
  				"Petrol Green" => "petrol",
  				"Gray" => "darkgrey"
  			),
  			"description" => ""
  		),
  		array(
  			"type" => "dropdown",
  			"class" => "",
  			"heading" => __("Animation"),
  			"param_name" => "animation",
  			"value" => array(
  				"None" => "",
  				"Left" => "animation right-to-left",
  				"Right" => "animation left-to-right",
  				"Top" => "animation bottom-to-top",
  				"Bottom" => "animation top-to-bottom",
  				"Scale" => "animation scale",
  				"Fade" => "animation fade-in"
  			),
  			"description" => ""
  		)
  	)
  ) );
	
	// Image shortcode
	vc_map( array(
		"name" => __("Image"),
		"base" => "thb_image",
		"icon" => "thb_vc_ico_image",
		"class" => "thb_vc_sc_image",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Select Image"),
				"param_name" => "image",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Animation"),
				"param_name" => "animation",
				"value" => array(
					"None" => "",
					"Left" => "animation right-to-left",
					"Right" => "animation left-to-right",
					"Top" => "animation bottom-to-top",
					"Bottom" => "animation top-to-bottom",
					"Scale" => "animation scale",
					"Fade" => "animation fade-in"
				),
				"description" => ""
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Image size"),
			  "param_name" => "img_size",
			  "description" => __("Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use 'thumbnail' size.")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Image alignment"),
			  "param_name" => "alignment",
			  "value" => array(__("Align left") => "", __("Align right") => "right", __("Align center") => "center"),
			  "description" => __("Select image alignment.")
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Link to Full-Width Image?"),
				"param_name" => "lightbox",
				"value" => array(
					"" => "true"
				)
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Image link"),
			  "param_name" => "img_link",
			  "description" => __("Enter url if you want this image to have link."),
			  "dependency" => Array('element' => "lightbox", 'is_empty' => true)
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Link Target"),
			  "param_name" => "img_link_target",
			  "value" => array(
			  	"Same window" => "",
			  	"New window" => "true"
			  ),
			  "dependency" => Array('element' => "lightbox", 'is_empty' => true)
			)
		)
	) );
	
	// Styled Header
	vc_map( array(
		"name" => __("Styled Header"),
		"base" => "thb_header",
		"icon" => "thb_vc_ico_styled",
		"class" => "thb_vc_sc_styled",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
			  "type" => "textfield",
			  "heading" => __("Title"),
			  "param_name" => "title",
			  "admin_label" => true,
			  "description" => __("Title of the header")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Sub-Title"),
			  "param_name" => "sub_title",
			  "description" => __("Sub - Title of the header. It's actually above the title.")
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Icon"),
				"param_name" => "icon",
				"value" => getFontAwesomeIconArray(),
				"description" => ""
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Use image instead of icon?"),
				"param_name" => "is_image",
				"value" => array(
					"" => "true"
				),
				"description" => __("20px width is recommended (40px) for retina.")
			),
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Select Image"),
				"param_name" => "image",
				"description" => "",
				"dependency" => Array('element' => "is_image", 'not_empty' => true)
			)
		)
	) );
	
	// Products
	vc_map( array(
		"name" => __("Products"),
		"base" => "thb_product",
		"icon" => "thb_vc_ico_product",
		"class" => "thb_vc_sc_product",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "dropdown",
		      "heading" => __("Product Sort"),
		      "param_name" => "product_sort",
		      "value" => array(
		      	__('Best Sellers') => "best-sellers",
		      	__('Latest Products') => "latest-products",
		      	__('Top Rated') => "top-rated",
		      	__('Sale Products') => "sale-products",
		      	__('By Category') => "by-category",
		      	__('By Product ID') => "by-id",
		      	),
		      "description" => __("Select the order of the products you'd like to show.")
		  ),
		  array(
		      "type" => "checkbox",
		      "heading" => __("Product Category"),
		      "param_name" => "cat",
		      "value" => thb_productCategories(),
		      "description" => __("Select the order of the products you'd like to show."),
		      "dependency" => Array('element' => "product_sort", 'value' => array('by-category'))
		  ),
		  array(
		      "type" => "textfield",
		      "heading" => __("Product IDs"),
		      "param_name" => "product_ids",
		      "description" => __("Enter the products IDs you would like to display seperated by comma"),
		      "dependency" => Array('element' => "product_sort", 'value' => array('by-id'))
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Carousel"),
		      "param_name" => "carousel",
		      "value" => array(
		      	'Yes' => "yes",
		        'No' => "no",
		      	),
		      "description" => __("Select yes to display the products in a carousel.")
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of items"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of products to show."),
		      "dependency" => Array('element' => "product_sort", 'value' => array('by-category', 'sale-products', 'top-rated', 'latest-products', 'best-sellers'))
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Columns"),
		      "param_name" => "columns",
		      "admin_label" => true,
		      "value" => array(
		      	__('Four Columns') => "4",
		      	__('Three Columns') => "3",
		      	__('Two Columns') => "2"
		      ),
		      "description" => __("Select the layout of the products.")
		  ),
		)
	) );
	
	// Product List
	vc_map( array(
		"name" => __("Product List"),
		"base" => "thb_product_list",
		"icon" => "thb_vc_ico_product_list",
		"class" => "thb_vc_sc_product_list",
		"category" => "by Fuel Themes",
		"params"	=> array(
			array(
			    "type" => "textfield",
			    "class" => "",
			    "heading" => __("Title"),
			    "param_name" => "title",
			    "value" => "",
			    "admin_label" => true,
			    "description" => __("Title of the widget")
			),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Product Sort"),
		      "param_name" => "product_sort",
		      "value" => array(
		      	__('Best Sellers') => "best-sellers",
		      	__('Latest Products') => "latest-products",
		      	__('Top Rated') => "top-rated",
		      	__('Sale Products') => "sale-products",
		      	__('By Product ID') => "by-id"
		      	),
		      "admin_label" => true,
		      "description" => __("Select the order of the products you'd like to show.")
		  ),
		  array(
		      "type" => "textfield",
		      "heading" => __("Product IDs"),
		      "param_name" => "product_ids",
		      "description" => __("Enter the products IDs you would like to display seperated by comma"),
		      "dependency" => Array('element' => "product_sort", 'value' => array('by-id'))
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of items"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of products to show."),
		      "dependency" => Array('element' => "product_sort", 'value' => array('by-category', 'sale-products', 'top-rated', 'latest-products', 'best-sellers'))
		  )
		)
	) );
	
	// Product Categories
	vc_map( array(
		"name" => __("Product Categories"),
		"base" => "thb_product_categories",
		"icon" => "thb_vc_ico_product_categories",
		"class" => "thb_vc_sc_product_categories",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "checkbox",
		      "heading" => __("Product Category"),
		      "param_name" => "cat",
		      "value" => thb_productCategories(),
		      "description" => __("Select the categories you would like to display")
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Carousel"),
		      "param_name" => "carousel",
		      "value" => array(
		      	'Yes' => "yes",
		        'No' => "no",
		      	),
		      "description" => __("Select yes to display the categories in a carousel.")
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Columns"),
		      "param_name" => "columns",
		      "admin_label" => true,
		      "value" => array(
		      	__('Four Columns') => "4",
		      	__('Three Columns') => "3",
		      	__('Two Columns') => "2"
		      ),
		      "description" => __("Select the layout of the products.")
		  ),
		)
	) );
	
	// Posts
	vc_map( array(
		"name" => __("Posts"),
		"base" => "thb_post",
		"icon" => "thb_vc_ico_post",
		"class" => "thb_vc_sc_post",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "dropdown",
		      "heading" => __("Carousel"),
		      "param_name" => "carousel",
		      "value" => array(
		      	'Yes' => "yes",
		        'No' => "no",
		      	),
		      "description" => __("Select yes to display the products in a carousel.")
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of posts"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of posts to show.")
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Columns"),
		      "param_name" => "columns",
		      "admin_label" => true,
		      "value" => array(
		      	__('Four Columns') => "4",
		      	__('Three Columns') => "3",
		      	__('Two Columns') => "2"
		      ),
		      "description" => __("Select the layout of the posts.")
		  ),
		)
	) );
	
	// Portfolio
	vc_map( array(
		"name" => __("Portfolios"),
		"base" => "thb_portfolio",
		"icon" => "thb_vc_ico_portfolio",
		"class" => "thb_vc_sc_portfolio",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "dropdown",
		      "heading" => __("Carousel"),
		      "param_name" => "carousel",
		      "value" => array(
		      	'Yes' => "yes",
		        'No' => "no",
		      	),
		      "description" => __("Select yes to display the portfolios in a carousel.")
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of portfolios"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of portfolios to show.")
		  ),
		  array(
		      "type" => "dropdown",
		      "heading" => __("Columns"),
		      "param_name" => "columns",
		      "value" => array(
		      	__('Four Columns') => "4",
		      	__('Three Columns') => "3",
		      	__('Two Columns') => "2"
		      ),
		      "description" => __("Select the layout of the portfolios.")
		  ),
		  array(
		      "type" => "checkbox",
		      "heading" => __("Categories"),
		      "param_name" => "categories",
		      "value" => thb_portfolioCategories(),
		      "description" => __("Select which categories of portfolios you would like to display.")
		  ),
		)
	) );
	// Icon List shortcode
	vc_map( array(
		"name" => __("Icon List"),
		"base" => "thb_iconlist",
		"icon" => "thb_vc_ico_iconlist",
		"class" => "thb_vc_sc_iconlist",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Icon"),
				"param_name" => "icon",
				"value" => getFontAwesomeIconArray(),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => __("Icon color"),
				"param_name" => "color",
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Animation"),
				"param_name" => "animation",
				"value" => array(
					"None" => "",
					"Left" => "animation right-to-left",
					"Right" => "animation left-to-right",
					"Top" => "animation bottom-to-top",
					"Bottom" => "animation top-to-bottom",
					"Scale" => "animation scale",
					"Fade" => "animation fade-in"
				),
				"description" => ""
			),
			array(
				"type" => "exploded_textarea",
				"class" => "",
				"heading" => __("List Items"),
				"admin_label" => true,
				"param_name" => "content",
				"value" => "",
				"description" => __("Every new line will be treated as a list item")
			)
		)
	) );
	// Iconbox shortcode
	vc_map( array(
		"name" => __("Iconbox"),
		"base" => "thb_iconbox",
		"icon" => "thb_vc_ico_iconbox",
		"class" => "thb_vc_sc_iconbox",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Type"),
				"param_name" => "type",
				"value" => array(
					"Top Icon - Type 1" => "top type1",
					"Top Icon - Type 2" => "top type2",
					"Top Icon - Type 3" => "top type3",
					"Left Icon - Round" => "left type1",
					"Left Icon - Square" => "left type2",
					"Left Icon - Only Icon" => "left type3",
					"Right Icon - Round" => "right type1",
					"Right Icon - Square" => "right type2",
					"Right Icon - Only Icon" => "right type3"
				),
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Icon"),
				"param_name" => "icon",
				"value" => getFontAwesomeIconArray(),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => __("Color"),
				"param_name" => "color",
				"value" => "",
				"description" => __("Leave empty to use default color")
			),
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Image"),
				"param_name" => "image",
				"description" => __("Use image instead of icon? Image uploaded should be 130*130 or 260*260 for retina. For small icons, 78*78 or 156*156 for retina."),
				"dependency" => Array('element' => "type", 'value' => array('top type1', 'top type2', 'top type3', 'left type1', 'left type2', 'right type1', 'right type2'))
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Heading"),
				"param_name" => "heading",
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "textarea",
				"class" => "",
				"heading" => __("Content"),
				"admin_label" => true,
				"param_name" => "content",
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Animation"),
				"param_name" => "animation",
				"value" => array(
					"None" => "",
					"Left" => "animation right-to-left",
					"Right" => "animation left-to-right",
					"Top" => "animation bottom-to-top",
					"Bottom" => "animation top-to-bottom",
					"Scale" => "animation scale",
					"Fade" => "animation fade-in"
				),
				"description" => ""
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Add Button?"),
				"param_name" => "use_btn",
				"value" => array(
					"" => "true"
				),
				"description" => __("Check if you want to add a button.")
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Content"),
				"admin_label" => true,
				"param_name" => "btn_content",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Button Caption"),
				"admin_label" => true,
				"param_name" => "btn_content",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Button Link URL"),
				"param_name" => "btn_link",
				"value" => "",
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Button Icon"),
				"param_name" => "btn_icon",
				"value" => getFontAwesomeIconArray(),
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Button Open link in"),
				"param_name" => "btn_target_blank",
				"value" => array(
					"Same window" => "",
					"New window" => "true"
				),
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Button Style"),
				"param_name" => "btn_size",
				"value" => array(
					"Small button" => "small",
					"Medium button" => "medium",
					"Big button" => "big"
				),
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			),
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Button color"),
				"param_name" => "btn_color",
				"value" => array(
					"White" => "white",
					"Light Grey" => "lightgrey",
					"Black" => "black",
					"Blue" => "blue",
					"Green" => "green",
					"Yellow" => "yellow",
					"Orange" => "orange",
					"Pink" => "pink",
					"Petrol Green" => "petrol",
					"Gray" => "gray"
				),
				"description" => "",
				"dependency" => Array('element' => "use_btn", 'not_empty' => true)
			)
		)
	) );
	
	// Look Book
	vc_map( array(
		"name" => __("Look Book"),
		"base" => "thb_lookbook",
		"icon" => "thb_vc_ico_lookbook",
		"class" => "thb_vc_sc_lookbook",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "checkbox",
		      "heading" => __("Product Category"),
		      "param_name" => "cat",
		      "value" => thb_productCategories(),
		      "description" => __("Select the order of the products you'd like to show.")
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of items"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of products to show.")
		  ),
		  array(
		  	"type" => "textarea_html",
		  	"class" => "",
		  	"heading" => __("Content"),
		  	"admin_label" => true,
		  	"param_name" => "content",
		  	"value" => "",
		  	"description" => __("Enter a starting content to be displayed before lookbook products.")
		  )
		)
	) );
	
	// Product Grid
	vc_map( array(
		"name" => __("Product Grid"),
		"base" => "thb_productgrid",
		"icon" => "thb_vc_ico_productgrid",
		"class" => "thb_vc_sc_productgrid",
		"category" => "by Fuel Themes",
		"params"	=> array(
		  array(
		      "type" => "checkbox",
		      "heading" => __("Product Category"),
		      "param_name" => "cat",
		      "value" => thb_productCategories(),
		      "admin_label" => true,
		      "description" => __("Select the order of the products you'd like to show.")
		  ),
		  array(
		      "type" => "textfield",
		      "class" => "",
		      "heading" => __("Number of items"),
		      "param_name" => "item_count",
		      "value" => "4",
		      "description" => __("The number of products to show.")
		  )
		)
	) );
	// Counter
	vc_map( array(
		"name" => __("Counter"),
		"base" => "thb_counter",
		"icon" => "thb_vc_ico_counter",
		"class" => "thb_vc_sc_counter",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Icon"),
				"param_name" => "icon",
				"value" => getFontAwesomeIconArray(),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => __("Color"),
				"param_name" => "color",
				"value" => "",
				"description" => __("Leave empty to use default color")
			),
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Image"),
				"param_name" => "image",
				"description" => __("Use image instead of icon? Image uploaded should be 70*70 or 140*140 for retina.")
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Number to count to"),
				"param_name" => "content",
				"value" => "",
				"description" => ""
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Speed of the counter animation"),
				"param_name" => "speed",
				"value" => "",
				"description" => __("Speed of the counter animation, default 1500")
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Heading"),
				"param_name" => "heading",
				"value" => "",
				"admin_label" => true,
				"description" => ""
			)
		)
	) );
	
	
	// Notification shortcode
	vc_map( array(
		"name" => __("Notification"),
		"base" => "thb_notification",
		"icon" => "thb_vc_ico_notification",
		"class" => "thb_vc_sc_notification",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Type"),
				"param_name" => "type",
				"value" => array(
					"Information" => "information",
					"Success" => "success",
					"Warning" => "warning",
					"Error" => "error",
					"Note" => "note"
				),
				"description" => ""
			),
			array(
				"type" => "textarea",
				"class" => "",
				"heading" => __("Content"),
				"admin_label" => true,
				"param_name" => "content",
				"value" => "",
				"description" => ""
			)
		)
	) );
	
	// Banner shortcode
	vc_map( array(
		"name" => __("Banner"),
		"base" => "thb_banner",
		"icon" => "thb_vc_ico_banner",
		"class" => "thb_vc_sc_banner",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => __("Background Color"),
				"param_name" => "banner_color",
				"value" => "",
				"description" => __("Select Background Color")
			),
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Select Background Image"),
				"param_name" => "banner_bg",
				"description" => ""
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Banner Height"),
			  "param_name" => "banner_height",
			  "description" => __("Enter height of the banner in px.")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Padding"),
			  "param_name" => "banner_padding",
			  "description" => __("Enter padding value of the content. <small>This does not affect border offset values, only the content.</small>")
			),
			
			array(
				"type" => "dropdown",
				"class" => "",
				"heading" => __("Banner Type"),
				"param_name" => "type",
				"value" => array(
					"Type - 1 (5px Border with offset)" => "type1",
					"Type - 2 (10px Border)" => "type2",
					"Type - 3 (Call to Action style without border)" => "type3",
					"Type - 4 (Simple no border)" => "type4",
					"Type - 5 (With overlay link)" => "type5",
				),
				"description" => ""
			),
			array(
				"type" => "colorpicker",
				"class" => "",
				"heading" => __("Border Color"),
				"param_name" => "banner_border_color",
				"value" => "",
				"description" => __("Select Border Color if the banner type supports it"),
				"dependency" => array(
					"element" => "type",
					"value" => array('type1', 'type2')
				)
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Title"),
			  "param_name" => "title",
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type3')
			  )
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Sub Title"),
			  "param_name" => "subtitle",
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type3')
			  )
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Button Text"),
			  "param_name" => "button_text",
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type3')
			  )
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Button Link"),
			  "param_name" => "button_link",
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type3')
			  )
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Overlay Link"),
			  "param_name" => "overlay_link",
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type5')
			  )
			),
			array(
				"type" => "checkbox",
				"class" => "",
				"heading" => __("Enable parallax"),
				"param_name" => "enable_parallax",
				"value" => array(
					"" => "false"
				)
			),
			array(
				"type" => "textfield",
				"class" => "",
				"heading" => __("Parallax Speed"),
				"param_name" => "parallax_speed",
				"value" => "1",
				"dependency" => array(
					"element" => "enable_parallax",
					"not_empty" => true
				),
				"description" => __("A value between 0 and 10 is recommended. Lower is faster")
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Text alignment"),
			  "param_name" => "alignment",
			  "value" => array( __("Align center") => "", __("Align left") => "textleft", __("Align right") => "textright" ),
			  "description" => __("Select text alignment."),
			  "dependency" => array(
			  	"element" => "type",
			  	"value" => array('type1', 'type2', 'type4', 'type5')
			  )
			),
			array(
				"type" => "textarea_html",
				"class" => "",
				"heading" => __("Content"),
				"param_name" => "content",
				"value" => "",
				"admin_label" => true,
				"description" => __("Content you would like to place inside the banner"),
				"dependency" => array(
					"element" => "type",
					"value" => array('type1', 'type2', 'type4', 'type5')
				)
			)
		)
	) );
	// Banner shortcode
	vc_map( array(
		"name" => __("Gap"),
		"base" => "thb_gap",
		"icon" => "thb_vc_ico_gap",
		"class" => "thb_vc_sc_gap",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
			  "type" => "textfield",
			  "heading" => __("Gap Height"),
			  "param_name" => "height",
			  "admin_label" => true,
			  "description" => __("Enter height of the gap in px.")
			)
		)
	) );
	// Progress Bar Shortcode
	vc_map( array(
		"name" => __("Progress Bar"),
		"base" => "thb_progressbar",
		"icon" => "thb_vc_ico_progressbar",
		"class" => "thb_vc_sc_progressbar",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
			  "type" => "exploded_textarea",
			  "heading" => __("Graphic values"),
			  "param_name" => "values",
			  "description" => __('Input graph values here. Divide values with linebreaks (Enter). Example: 90|Development', 'js_composer'),
			  "value" => "90|Development,80|Design,70|Marketing"
			),
			array(
			  "type" => "dropdown",
			  "heading" => __("Bar color"),
			  "param_name" => "bgcolor",
			  "value" => array(
			  	"Light Grey" => "lightgrey",
			  	"Black" => "black",
			  	"Blue" => "blue",
			  	"Green" => "green",
			  	"Yellow" => "yellow",
			  	"Orange" => "orange",
			  	"Pink" => "pink",
			  	"Petrol Green" => "petrol",
			  	"Gray" => "gray"
			  ),
			  "description" => __("Select bar background color.")
			)
		)
	) );
	
	// Testimonials Shortcode
	vc_map( array(
		"name" => __("Testimonials"),
		"base" => "thb_testimonials",
		"icon" => "thb_vc_ico_testimonials",
		"class" => "thb_vc_sc_testimonials",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
			  "type" => "exploded_textarea",
			  "heading" => __("Testimonials"),
			  "param_name" => "values",
			  "admin_label" => true,
			  "description" => __('Enter testimonials here. Divide values with linebreaks (Enter). Example: Abraham Lincoln|Lorem ipsum ....', 'js_composer'),
			  "value" => "Abraham Lincoln|Lorem Ipsum is simply dummy text of the printing and typesetting industry,George Bush|Lorem Ipsum is simply dummy text of the printing and typesetting industry."
			)
		)
	) );
	
	// Team Member shortcode
	vc_map( array(
		"name" => __("Team Member"),
		"base" => "thb_teammember",
		"icon" => "thb_vc_ico_teammember",
		"class" => "thb_vc_sc_teammember",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "attach_image", //attach_images
				"class" => "",
				"heading" => __("Select Team Member Image"),
				"param_name" => "image",
				"description" => __("Minimum size is 270x270 pixels")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Name"),
			  "param_name" => "name",
			  "admin_label" => true,
			  "description" => __("Enter name of the team member")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Position"),
			  "param_name" => "position",
			  "description" => __("Enter position/title of the team member")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Facebook"),
			  "param_name" => "facebook",
			  "description" => __("Enter Facebook Link")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Twitter"),
			  "param_name" => "twitter",
			  "description" => __("Enter Twitter Link")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Google Plus"),
			  "param_name" => "googleplus",
			  "description" => __("Enter Google Plus Link")
			),
			array(
			  "type" => "textfield",
			  "heading" => __("Linkedin"),
			  "param_name" => "linkedin",
			  "description" => __("Enter Linkedin Link")
			)
		)
	) );
	
	// Clients shortcode
	vc_map( array(
		"name" => __("Clients"),
		"base" => "thb_clients",
		"icon" => "thb_vc_ico_clients",
		"class" => "thb_vc_sc_clients",
		"category" => "by Fuel Themes",
		"params" => array(
			array(
				"type" => "attach_images", //attach_images
				"class" => "",
				"heading" => __("Select Images"),
				"param_name" => "images",
				"description" => __("Add as many client images as possible.")
			)
		)
	) );
	
	
	function thb_translateColumnWidthToSpan($width) {
	  switch ( $width ) {
	    case "1/6" :
	      $w = "two";
	      break;    
	    case "1/4" :
	      $w = "three";
	      break;
	    case "1/3" :
	      $w = "four";
	      break;    
	    case "1/2" :
	      $w = "six";
	      break;    
	    case "2/3" :
	      $w = "eight";
	      break;    
	    case "3/4" :
	      $w = "nine";
	      break;    
	    case "5/6" :
	      $w = "ten";
	      break;    
	    case "1/1" :
	      $w = "twelve";
	      break;
	    case "5/12" :
	      $w = "five";
	      break;
	    case "7/12" :
	      $w = "seven";
	      break;
	    default :
	      $w = $width;
	  }
	  return $w.' columns';
	}
}
