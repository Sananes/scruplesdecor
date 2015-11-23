<?php
    /**
     * ReduxFramework Sample Config File
     * For full documentation, please visit: http://docs.reduxframework.com/
     */

    if ( ! class_exists( 'Redux' ) ) {
        return;
	}

    // This is your option name where all the Redux data is stored.
    $opt_name = 'nm_theme_options';
	

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        // NM: Disable tracking
		'disable_tracking' => true,
		// TYPICAL -> Change these values as you need/desire
        'opt_name'             => $opt_name,
        // This is where your data is stored in the database and also becomes your global variable name.
        'display_name'         => $theme->get( 'Name' ),
        // Name that appears at the top of your panel
        'display_version'      => $theme->get( 'Version' ),
        // Version that appears at the top of your panel
        'menu_type'            => 'menu',
        //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
        'allow_sub_menu'       => true,
        // Show the sections below the admin menu item or not
		'menu_title'			=> __( 'Theme Settings', 'nm-framework-admin' ),
		'page_title'			=> __( 'Theme Settings', 'nm-framework-admin' ),
        // You will need to generate a Google API key to use this feature.
        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
        'google_api_key'       => 'AIzaSyAX_2L_UzCDPEnAHTG7zhESRVpMPS4ssII',
        // Set it you want google fonts to update weekly. A google_api_key value is required.
        'google_update_weekly' => false,
        // Must be defined to add google fonts to the typography module
        'async_typography'     => false,
        // Use a asynchronous font on the front end or font string
        //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
        'admin_bar'            => false,
        // Show the panel pages on the admin bar
        'admin_bar_icon'       => 'dashicons-portfolio',
        // Choose an icon for the admin bar menu
        'admin_bar_priority'   => 50,
        // Choose an priority for the admin bar menu
        'global_variable'      => '',
        // Set a different name for your global variable other than the opt_name
        'dev_mode'             => false,
        // Show the time the page took to load, etc
        'update_notice'        => false,
        // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
        'customizer'           => false,
        // Enable basic customizer support
        //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
        //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

        // OPTIONAL -> Give you extra features
        'page_priority'        => null,
        // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
        'page_parent'          => 'themes.php',
        // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
        'page_permissions'     => 'manage_options',
        // Permissions needed to access the options panel.
        'menu_icon'            => '',
        // Specify a custom URL to an icon
        'last_tab'             => '',
        // Force your panel to always open to a specific tab (by id)
        'page_icon'            => 'icon-themes',
        // Icon displayed in the admin panel next to your menu_title
        'page_slug'            => '',
        // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
        'save_defaults'        => true,
        // On load save the defaults to DB before user clicks save or not
        'default_show'         => false,
        // If true, shows the default value next to each field that is not the default value.
        'default_mark'         => '',
        // What to print by the field's title if the value shown is default. Suggested: *
        'show_import_export'   => true,
        // Shows the Import/Export panel when not used as a field.

        // CAREFUL -> These options are for advanced use only
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
        'output_tag'           => true,
        // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
        'footer_credit'     => '&nbsp;',
		// Footer credit text

        // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
        'database'             => '',
        // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
        'use_cdn'              => true,
        // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
		'system_info'          => false,
        // REMOVE

        //'compiler'             => true,
		
        // HINTS
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'red',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                )
            )
        )
    );
	
    Redux::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */
	
	
    /*
     *
     * ---> START SECTIONS
     *
     */
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'General', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-cog',
		'fields'	=> array(
			array(
				'id' 		=> 'full_width_layout',
				'type' 		=> 'switch', 
				'title' 	=> __( 'Full Width Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Enable to display full-width page layout.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on' 		=> 'Enable',
				'off' 		=> 'Disable'
			),
			array(
				'id' 		=> 'custom_title',
				'type' 		=> 'switch', 
				'title' 	=> __( 'Custom Title', 'nm-framework-admin' ),
				'desc'		=> __( "Use the theme's custom document title (disable to use default WordPress title).", 'nm-framework-admin' ),
				'default'	=> 1,
				'on' 		=> 'Enable',
				'off' 		=> 'Disable'
			),
			array(
				'id' 	=> 'favicon',
				'type' 	=> 'media', 
				'title'	=> __( 'Favicon', 'nm-framework-admin' ),
				'desc'	=> __( 'Upload a .ico/.png image to display as your favicon.', 'nm-framework-admin' )
			),
			array(
				'id' 		=> 'wp_admin_bar',
				'type' 		=> 'switch', 
				'title' 	=> __( 'WordPress Admin Bar', 'nm-framework-admin' ),
				'desc'		=> __( 'Front-end WordPress admin bar for logged-in users.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on' 		=> 'Enable',
				'off' 		=> 'Disable'
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Header', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-upload',
		'fields'	=> array(
			array (
				'id' 	=> 'top_bar_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Top Bar', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id' 		=> 'top_bar',
				'type' 		=> 'switch', 
				'title' 	=> __( 'Top Bar', 'nm-framework-admin' ),
				'desc'		=> __( 'Display the top-bar.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on' 		=> 'Enable',
				'off' 		=> 'Disable'
			),
			array(
				'id' 		=> 'top_bar_text',
				'type' 		=> 'editor',
				'title' 	=> __( 'Text', 'nm-framework-admin' ),
				'desc' 		=> __( 'Enter the top-bar text.', 'nm-framework-admin' ),
				'default'	=> __( 'Welcome to our shop!', 'nm-framework-admin' ),
				'args'		=> array(
					'wpautop'	=> false,
					'teeny' 	=> true
				),
				'required'	=> array( 'top_bar', '=', '1' )
			),
			array(
				'id'			=> 'top_bar_left_column_size',
				'type'			=> 'slider',
				'title'			=> __( 'Text Column Size', 'nm-framework-admin' ),
				'desc'			=> __( 'Select size-span of top-bar Text column.', 'nm-framework-admin' ),
				'default'		=> 6,
				'min'			=> 1,
				'max'			=> 12,
				'step'			=> 1,
				'display_value'	=> 'text',
				'required'	=> array( 'top_bar', '=', '1' )
			),
			array(
				'id'		=> 'top_bar_social_icons',
				'type'		=> 'select',
				'title'		=> __( 'Social Icons', 'nm-framework-admin' ),
				'desc'		=> __( 'Display social profile icons (from the "Social Profiles" settings tab).', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'None', 'l_c' => 'Display in Text (left) column', 'r_c' => 'Display in Menu (right) column' ),
				'default'	=> '0',
				'required'	=> array( 'top_bar', '=', '1' )
			),
			array (
				'id'	=> 'header_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Header', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id' 		=> 'header_layout',
				'type' 		=> 'image_select',
				'title' 	=> __( 'Layout', 'nm-framework-admin' ),
				'desc' 		=> __( 'Select header layout.', 'nm-framework-admin' ),
				'options'	=> array(
					'default' 	=> array( 'alt' => 'Default', 'img' => NM_URI . '/assets/img/option-panel/header-default.png' ),
					'centered'	=> array( 'alt' => 'Centered logo', 'img' => NM_URI . '/assets/img/option-panel/header-centered.png' )
				),
				'default' 	=> 'centered'
			),
			array(
				'id'		=> 'header_fixed',
				'type'		=> 'switch', 
				'title'		=> __( 'Float', 'nm-framework-admin' ),
				'desc'		=> __( 'Float the header above the content when scrolling down the page.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'home_header_transparent',
				'type'		=> 'switch', 
				'title' 	=> __( 'Transparent - Home Page', 'nm-framework-admin' ),
				'desc'		=> __( 'Transparent header on the Home page.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'	=> 'logo',
				'type'	=> 'media', 
				'title'	=> __( 'Logo', 'nm-framework-admin' ),
				'desc'	=> __( 'Upload your logo here.', 'nm-framework-admin' )
			),
			array(
				'id'			=> 'logo_height',
				'type'			=> 'slider',
				'title'			=> __( 'Logo Height', 'nm-framework-admin' ),
				'desc'			=> __( 'Default logo height.', 'nm-framework-admin'),
				'default'		=> 16,
				'min'			=> 10,
				'max'			=> 250,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'logo_height_tablet',
				'type'			=> 'slider',
				'title'			=> __( 'Logo Height - Tablet and "Float header"', 'nm-framework-admin' ),
				'desc'			=> __( 'Logo height for tablet sized screen widths and "floating" header.', 'nm-framework-admin'),
				'default'		=> 16,
				'min'			=> 10,
				'max'			=> 250,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'logo_height_mobile',
				'type'			=> 'slider',
				'title'			=> __( 'Logo Height - Mobile and "Float header"', 'nm-framework-admin' ),
				'desc'			=> __( 'Logo height for mobile sized screen widths and "floating" header.', 'nm-framework-admin'),
				'default'		=> 16,
				'min'			=> 10,
				'max'			=> 250,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'header_spacing_top',
				'type'			=> 'slider',
				'title'			=> __( 'Top Spacing', 'nm-framework-admin' ),
				'desc'			=> __( 'Set the top header spacing.', 'nm-framework-admin'),
				'default'		=> 17,
				'min'			=> 0,
				'max'			=> 280,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'header_spacing_bottom',
				'type'			=> 'slider',
				'title'			=> __( 'Bottom Spacing', 'nm-framework-admin' ),
				'desc'			=> __( 'Set the bottom header spacing.', 'nm-framework-admin'),
				'default'		=> 17,
				'min'			=> 0,
				'max'			=> 280,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'		=> 'header_border',
				'type'		=> 'switch', 
				'title'		=> __( 'Border', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a header border.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'home_header_border',
				'type'		=> 'switch', 
				'title'		=> __( 'Border - Home Page', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a header border on the home page.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'shop_header_border',
				'type'		=> 'switch', 
				'title'		=> __( 'Border - Shop', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a header border on the Shop archive/listing pages.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'menu_login',
				'type'		=> 'switch', 
				'title'		=> __( 'Login Menu', 'nm-framework-admin' ),
				'desc'		=> __( 'Display login/my-account link.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'menu_login_icon',
				'type'		=> 'switch', 
				'title'		=> __( 'Login Menu - Icon', 'nm-framework-admin' ),
				'desc'		=> __( 'Display login/my-account menu icon (instead of text).', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable',
				'required'	=> array( 'menu_login', '=', '1' )
			),
			array(
				'id'		=> 'menu_cart',
				'type'		=> 'select',
				'title'		=> __( 'Cart Menu', 'nm-framework-admin' ),
				'desc'		=> __( 'Configure the Cart menu widget.', 'nm-framework-admin' ),
				'options'	=> array( '1' => 'Enable', 'link' => 'Link Only (no slide panel)', '0' => 'Disable' ),
				'default'	=> '1'
			),
			array(
				'id'		=> 'menu_cart_icon',
				'type'		=> 'switch', 
				'title'		=> __( 'Cart Menu - Icon', 'nm-framework-admin' ),
				'desc'		=> __( 'Display cart menu icon (instead of text).', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable',
				'required'	=> array( 'menu_cart', '!=', '0' )
			),
			array(
				'id'		=> 'widget_panel_color',
				'type'		=> 'select',
				'title'		=> __( 'Cart Panel Color', 'nm-framework-admin' ),
				'desc'		=> __( 'Select a color scheme for the cart slide-panel.', 'nm-framework-admin' ),
				'options'	=> array( 'light' => 'Light', 'dark' => 'Dark' ),
				'default'	=> 'dark'
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Footer', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-download',
		'fields'	=> array(
			array(
				'id'		=> 'footer_sticky',
				'type'		=> 'switch', 
				'title'		=> __( 'Sticky', 'nm-framework-admin' ),
				'desc'		=> __( 'Make the footer sections "stick" to the bottom of the page.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array (
				'id'	=> 'footer_widgets_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Widgets', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'footer_widgets_layout',
				'type'		=> 'select',
				'title'		=> __( 'Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select a layout for the footer widgets section.', 'nm-framework-admin' ),
				'options'	=> array( 'boxed' => 'Boxed', 'full' => 'Full', 'full-nopad' => 'Full (No padding)' ),
				'default'	=> 'boxed'
			),
			array(
				'id'		=> 'footer_widgets_border',
				'type'		=> 'switch',
				'title'		=> __( 'Top Border', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a top-border on the footer widgets sections.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'			=> 'footer_widgets_columns',
				'type'			=> 'slider',
				'title'			=> __( 'Columns', 'nm-framework-admin' ),
				'desc'			=> __( 'Select the number of footer widget columns to display.', 'nm-framework-admin' ),
				'default'		=> 2,
				'min'			=> 1,
				'max'			=> 4,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array (
				'id'	=> 'footer_bar_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Bar', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'	=> 'footer_bar_logo',
				'type'	=> 'media', 
				'title'	=> __( 'Logo', 'nm-framework-admin' ),
				'desc'	=> __( 'Upload a custom logo (max-height is set to 30px).', 'nm-framework-admin' )
			),
			array(
				'id'		=> 'footer_bar_text',
				'type'		=> 'text',
				'title'		=> __( 'Copyright Text', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your copyright text.', 'nm-framework-admin' ),
				'validate'	=> 'html'
			),
			array(
				'id'		=> 'footer_bar_content',
				'type'		=> 'select',
				'title'		=> __( 'Right Column', 'nm-framework-admin' ),
				'desc'		=> __( 'Content in the right column.', 'nm-framework-admin' ),
				'options'	=> array( 'copyright_text' => 'Copyright Text', 'social_icons' => 'Social Media Icons (From the "Social Profiles" settings tab)', 'custom' => 'Custom Content' ),
				'default'	=> 'copyright_text'
			),
			array(
				'id'		=> 'footer_bar_custom_content',
				'type'		=> 'text',
				'title'		=> __( 'Custom Content', 'nm-framework-admin' ),
				'desc'		=> __( 'Custom content (HTML allowed).', 'nm-framework-admin' ),
				'validate'	=> 'html',
				'required'	=> array( 'footer_bar_content', '=', 'custom' )
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Styling', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-pencil',
		'fields'	=> array(
			array(
				'id'	=> 'info_styling_general',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'General', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'highlight_color',
				'type'			=> 'color',
				'title'			=> __( 'Highlight Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Main theme highlight color.', 'nm-framework-admin' ),
				'default'		=> '#dc9814',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'button_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Button - Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Product buttons text.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'button_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Button - Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Product buttons background-color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_typography',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Typography', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'main_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Main Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Body text color.', 'nm-framework-admin' ),
				'default'		=> '#777777',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'heading_color',
				'type'			=> 'color',
				'title'			=> __( 'Heading Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Heading text color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_background',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Background', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'main_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Main site background-color.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'main_background_image',
				'type'	=> 'media', 
				'url'	=> true,
				'title'	=> __( 'Background Image', 'nm-framework-admin' ),
				'desc'	=> __( 'Upload a background image or specify a URL (boxed layout).', 'nm-framework-admin' )
			),
			array(
				'id'		=> 'main_background_image_type',
				'type'		=> 'select',
				'title'		=> __( 'Background Type', 'nm-framework-admin' ),
				'desc'		=> __( 'Select the background-image type (fixed image or repeat pattern/texture).', 'nm-framework-admin' ),
				'options'	=> array( 'fixed' => 'Fixed (Full)', 'repeat' => 'Repeat (Pattern)' ),
				'default'	=> 'fixed'
			),
			array(
				'id'	=> 'info_styling_top_bar',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Top Bar', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'top_bar_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Top bar text color.', 'nm-framework-admin' ),
				'default'		=> '#eeeeee',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'top_bar_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Top bar background-color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_header',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Header', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'header_navigation_color',
				'type'			=> 'color',
				'title'			=> __( 'Menu Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Header menu links color.', 'nm-framework-admin' ),
				'default'		=> '#707070',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'		=> 'header_background_color',
				'type'		=> 'color',
				'title'		=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'		=> __( 'Header background-color.', 'nm-framework-admin' ),
				'default'	=> '#ffffff',
				'validate'	=> 'color'
			),
			array(
				'id'		=> 'header_home_background_color',
				'type'		=> 'color',
				'title'		=> __( 'Background Color - Home Page', 'nm-framework-admin' ),
				'desc'		=> __( 'Header background-color on the Home page.', 'nm-framework-admin' ),
				'default'	=> '#ffffff',
				'validate'	=> 'color'
			),
			array(
				'id'		=> 'header_float_background_color',
				'type'		=> 'color',
				'title'		=> __( 'Background Color - Floating', 'nm-framework-admin' ),
				'desc'		=> __( 'Floating header background-color.', 'nm-framework-admin' ),
				'default'	=> '#ffffff',
				'validate'	=> 'color'
			),
			array(
				'id'			=> 'header_slide_menu_open_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color - Mobile Menu Open', 'nm-framework-admin' ),
				'desc'			=> __( 'Header background-color when the mobile menu is open.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'header_login_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color - Login', 'nm-framework-admin' ),
				'desc'			=> __( 'Header background-color on the Login page.', 'nm-framework-admin' ),
				'default'		=> '#f5f5f5',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_dropdown_menu',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Dropdown Menu', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'dropdown_menu_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Header dropdown menu links color.', 'nm-framework-admin' ),
				'transparent'	=> false,
				'default'		=> '#a0a0a0',
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'dropdown_menu_font_highlight_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color - Highlight', 'nm-framework-admin' ),
				'desc'			=> __( 'Used for "highlighting" links in the header dropdown menu.', 'nm-framework-admin' ),
				'transparent'	=> false,
				'default'		=> '#eeeeee',
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'dropdown_menu_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Header dropdown menu background-color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_footer_widgets',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Footer Widgets', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'footer_widgets_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Footer widgets text color.', 'nm-framework-admin' ),
				'default'		=> '#777777',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'footer_widgets_highlight_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color - Highlights', 'nm-framework-admin' ),
				'desc'			=> __( 'Link hover states color.', 'nm-framework-admin' ),
				'default'		=> '#dc9814',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'footer_widgets_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Footer widgets background-color.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_footer_bar',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Footer Bar', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'footer_bar_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Footer-bar text color.', 'nm-framework-admin' ),
				'default'		=> '#aaaaaa',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'footer_bar_highlight_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Font Color - Highlights', 'nm-framework-admin' ),
				'desc'			=> __( 'Link hover states color.', 'nm-framework-admin' ),
				'default'		=> '#eeeeee',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'footer_bar_menu_border_color',
				'type'			=> 'color',
				'title'			=> __( 'Menu Border Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Menu border color on smaller screen widths.', 'nm-framework-admin' ),
				'default'		=> '#3a3a3a',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'footer_bar_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Footer-bar background-color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_shop',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Shop', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'sale_flash_font_color',
				'type'			=> 'color',
				'title'			=> __( 'Sale Badge - Font Color', 'nm-framework-admin' ),
				'desc'			=> __( '"Sale badges" text color.', 'nm-framework-admin' ),
				'default'		=> '#373737',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'sale_flash_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Sale Badge - Background Color', 'nm-framework-admin' ),
				'desc'			=> __( '"Sale badges" background-color.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'	=> 'info_styling_shop_single_product',
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Shop - Single Product', 'nm-framework-admin' ) . '</h3>'
			),
			array(
				'id'			=> 'single_product_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Background', 'nm-framework-admin' ),
				'desc'			=> __( 'Single product details background-color.', 'nm-framework-admin' ),
				'default'		=> '#eeeeee',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'featured_video_icon_color',
				'type'			=> 'color',
				'title'			=> __( 'Featured Video Icon - Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Featured video icon color.', 'nm-framework-admin' ),
				'default'		=> '#282828',
				'transparent'	=> false,
				'validate'		=> 'color'
			),
			array(
				'id'			=> 'featured_video_background_color',
				'type'			=> 'color',
				'title'			=> __( 'Featured Video Icon - Background Color', 'nm-framework-admin' ),
				'desc'			=> __( 'Featured video icon background-color.', 'nm-framework-admin' ),
				'default'		=> '#ffffff',
				'transparent'	=> false,
				'validate'		=> 'color'
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Typography', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-font',
		'fields'	=> array(
			// Main font
			array (
				'id'	=> 'main_font_info',
				'type'	=> 'info',
				'icon'	=> true,
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Main Font', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'main_font_source',
				'type'		=> 'radio',
				'title'		=> __( 'Font Source', 'nm-framework-admin' ),
				'options'	=> array(
					'1'	=> 'Standard + Google Webfonts', 
					'2'	=> 'Adobe Typekit',
					'3'	=> 'Fontdeck'
				),
				'default'	=> '1'
			),
			// Main font: Standard + Google Webfonts
			array (
				'id'			=> 'main_font',
				'type'			=> 'typography',
				'title'			=> __( 'Font Face', 'nm-framework-admin' ),
				'line-height'	=> false,
				'text-align'	=> false,
				'font-style'	=> false,
				'font-weight'	=> false,
				'font-size'		=> false,
				'color'			=> false,
				'default'		=> array (
					'font-family'	=> 'Open Sans',
					'subsets'		=> '',
				),
				'required'		=> array( 'main_font_source', '=', '1' )
			),
			// Main font: Adobe Typekit
			array(
				'id'		=> 'main_font_typekit_kit_id',
				'type'		=> 'text',
				'title'		=> __( 'Typekit Kit ID', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Typekit Kit ID for the Main Font.', 'nm-framework-admin' ),
				'default'	=> '',
				'required'	=> array( 'main_font_source', '=', '2' )
			),
			array (
				'id'		=> 'main_typekit_font',
				'type'		=> 'text',
				'title'		=> __( 'Typekit Font Face', 'nm-framework-admin' ),
				'desc'		=> __( 'Example: futura-pt', 'nm-framework-admin' ),
				'default'	=> '',
				'required'	=> array( 'main_font_source', '=', '2' )
			),
			// Main font: Fontdeck
			array(
				'id'		=> 'fontdeck_project_id',
				'type'		=> 'text',
				'title'		=> __( 'Fontdeck Project ID', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Fontdeck Project ID.', 'nm-framework-admin' ),
				'default'	=> '',
				'required'	=> array( 'main_font_source', '=', '3' )
			),			
			array(
				'id'		=> 'fontdeck_css',
				'type'		=> 'ace_editor',
				'title' 	=> __( 'Fontdeck CSS', 'nm-framework-admin' ),
				'desc' 		=> __( 'Enter your Fontdeck CSS rules.<br><br>Example: body { font-family:"Proxima Nova Regular", sans-serif; }', 'nm-framework-admin' ),
				'mode'		=> 'css',
				'theme'		=> 'chrome',
				'default'	=> '',
				'required'	=> array( 'main_font_source', '=', '3' )
			),
			// Secondary font
			array (
				'id'	=> 'secondary_font_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Secondary Font', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'secondary_font_source',
				'type'		=> 'radio',
				'title'		=> __('Font Source', 'nm-framework-admin'),
				'options'	=> array(
					'0' => '(none)',
					'1'	=> 'Standard + Google Webfonts', 
					'2'	=> 'Adobe Typekit',
					'3'	=> 'Fontdeck'
				),
				'default'	=> '0'
			),
			// Secondary font: Standard + Google Webfonts
			array (
				'id'			=> 'secondary_font',
				'type'			=> 'typography',
				'title'			=> __( 'Font Face', 'nm-framework-admin' ),
				'line-height'	=> false,
				'text-align'	=> false,
				'font-style'	=> false,
				'font-weight'	=> false,
				'font-size'		=> false,
				'color'			=> false,
				'default'		=> array (
					'font-family'	=> 'Open Sans',
					'subsets'		=> '',
				),
				'required'		=> array( 'secondary_font_source', '=', '1' )
			),
			// Secondary font: Adobe Typekit
			array(
				'id'		=> 'secondary_font_typekit_kit_id',
				'type'		=> 'text',
				'title'		=> __( 'Typekit Kit ID', 'nm-framework-admin' ), 
				'desc'		=> __( 'Enter your Typekit Kit ID for the Secondary Font.', 'nm-framework-admin' ),
				'default'	=> '',
				'required'	=> array( 'secondary_font_source', '=', '2' )
			),
			array (
				'id'		=> 'secondary_typekit_font',
				'type'		=> 'text',
				'title'		=> __( 'Typekit Font Face', 'nm-framework-admin' ),
				'desc'		=> __( 'Example: proxima-nova', 'nm-framework-admin' ),
				'default'	=> '',
				'required'	=> array( 'secondary_font_source', '=', '2' )
			),
			// Secondary font: Fontdeck
			array(
				'id'  	=> 'secondary_font_fontdeck_info',
				'type'	=> 'info',
				'style'	=> 'info',
				'desc'	=> __( 'Fontdeck: No need to specify a secondary font for Fontdeck. Edit your Fontdeck CSS instead.', 'nm-framework-admin' ),
				'required'	=> array( 'secondary_font_source', '=', '3' )
			)
		)
	) );

	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Blog', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-website',
		'fields'	=> array(
			array(
				'id'		=> 'blog_static_page',
				'type'		=> 'switch', 
				'title'		=> __( 'Static Content', 'nm-framework-admin' ),
				'desc'		=> __( "Display static page content on the blog's index page.", 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'blog_static_page_id',
				'type'		=> 'select',
				'title'		=> __( 'Static Content - Page', 'nm-framework-admin' ),
				'desc'		=> __( "Select a page to display on the blog's index page.", 'nm-framework-admin' ),
				'data'		=> 'pages',
				'required'	=> array( 'blog_static_page', '=', '1' )
			),
			array (
				'id'	=> 'blog_archive_info',
				'type'	=> 'info',
				'icon'	=> true,
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Archive/Listing', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'blog_layout',
				'type'		=> 'select',
				'title'		=> __( 'Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select blog layout.', 'nm-framework-admin' ),
				'options'	=> array( 'grid' => 'Grid', 'list' => 'List' ),
				'default'	=> 'grid'
			),
			array(
				'id'		=> 'blog_categories_layout',
				'type'		=> 'select',
				'title'		=> __( 'Categories - Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select categories menu layout.', 'nm-framework-admin' ),
				'options'	=> array( 'list' => 'Separated List', 'list_nosep' => 'List', 'columns' => 'Columns' ),
				'default'	=> 'list'
			),
			array(
				'id'			=> 'blog_categories_columns',
				'type'			=> 'slider',
				'title'			=> __( 'Categories - Columns', 'nm-framework-admin' ),
				'desc'			=> __( 'Select the number of category columns to display.', 'nm-framework-admin' ),
				'default'		=> 4,
				'min'			=> 2,
				'max'			=> 5,
				'step'			=> 1,
				'display_value'	=> 'text',
				'required'	=> array( 'blog_categories_layout', '=', 'columns' )
			),
			array(
				'id'		=> 'blog_categories_toggle',
				'type'		=> 'switch', 
				'title'		=> __( 'Categories - Toggle', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a link to show/hide categories on small browser widths.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'blog_show_full_posts',
				'type'		=> 'switch', 
				'title'		=> __( 'Show Full Posts', 'nm-framework-admin' ),
				'desc'		=> __( 'Show full posts on blog listing.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'blog_gallery',
				'type'		=> 'switch', 
				'title'		=> __( 'Blog Gallery', 'nm-framework-admin' ),
				'desc'		=> __( 'Display image galleries on blog listing', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array (
				'id'	=> 'blog_single_post_info',
				'type'	=> 'info',
				'icon'	=> true,
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Single Post', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'single_post_sidebar',
				'type'		=> 'select',
				'title'		=> __( 'Single Post Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select single post layout.', 'nm-framework-admin' ),
				'options'	=> array( 'none' => 'No sidebar (default)', 'left' => 'Sidebar Left', 'right' => 'Sidebar Right' ),
				'default'	=> 'none'
			),
			array(
				'id'		=> 'custom_wp_gallery',
				'type'		=> 'switch', 
				'title'		=> __( 'Custom WordPress Gallery', 'nm-framework-admin' ),
				'desc'		=> __( 'Replace the default WordPress gallery with a custom image slider.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Shop Header', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-shopping-cart',
		'fields'	=> array(
			array(
				'id'		=> 'shop_header',
				'type'		=> 'switch',
				'title'		=> __( 'Header', 'nm-framework-admin' ),
				'desc'		=> __( 'Display shop header.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'shop_filters_enable_ajax',
				'type'		=> 'select',
				'title'		=> __( 'AJAX', 'nm-framework-admin' ),
				'desc'		=> __( 'Use AJAX to filter shop content (AJAX allows new content without reloading the whole page).', 'nm-framework-admin' ),
				'options'	=> array( '1' => 'Enable', 'desktop' => 'Disable on Touch devices', '0' => 'Disable' ),
				'default'	=> '1'
			),
			array (
				'id' 	=> 'shop_header_categories_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Categories', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'shop_categories',
				'type'		=> 'switch',
				'title'		=> __( 'Categories', 'nm-framework-admin' ),
				'desc'		=> __( 'Display product categories in the shop header.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'shop_categories_top_level',
				'type'		=> 'select',
				'title'		=> __( 'Display Type', 'nm-framework-admin' ),
				'desc'		=> __( 'Select product categories display type.', 'nm-framework-admin' ),
				'options'	=> array( '1' => 'Always show top-level categories', '0' => 'Hide top-level categories (on category pages)' ),
				'default'	=> '1',
				'required'	=> array( 'shop_categories', '=', '1' )
			),
			array(
				'id'		=> 'shop_categories_layout',
				'type'		=> 'select',
				'title'		=> __( 'Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select product categories menu layout.', 'nm-framework-admin' ),
				'options'	=> array( 'list_sep' => 'Separated List', 'list_nosep' => 'List' ),
				'default'	=> 'list_sep',
				'required'	=> array( 'shop_categories', '=', '1' )
			),
			array(
				'id'		=> 'shop_categories_orderby',
				'type'		=> 'select',
				'title'		=> __( 'Order', 'nm-framework-admin' ),
				'desc'		=> __( 'Select product categories order.', 'nm-framework-admin' ),
				'options'	=> array( 'id' => 'ID', 'name' => 'Name/Menu-order', 'slug' => 'Slug', 'count' => 'Count', 'term_group' => 'Term Group' ),
				'default'	=> 'slug',
				'required'	=> array( 'shop_categories', '=', '1' )
			),
			array(
				'id'		=> 'shop_categories_order',
				'type'		=> 'select',
				'title'		=> __( 'Order Direction', 'nm-framework-admin' ),
				'desc'		=> __( 'Select product categories order direction.', 'nm-framework-admin' ),
				'options'	=> array( 'asc' => 'Ascending', 'desc' => 'Descending' ),
				'default'	=> 'asc',
				'required'	=> array( 'shop_categories', '=', '1' )
			),
			array (
				'id' 	=> 'shop_header_filters_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Filters', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'shop_filters',
				'type'		=> 'switch',
				'title'		=> __( 'Filters', 'nm-framework-admin' ),
				'desc'		=> __( 'Display product filters in the shop header.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'			=> 'shop_filters_columns',
				'type'			=> 'slider',
				'title'			=> __( 'Columns', 'nm-framework-admin' ),
				'desc'			=> __( 'Select the number of filter columns to display.', 'nm-framework-admin' ),
				'default'		=> 4,
				'min'			=> 1,
				'max'			=> 4,
				'step'			=> 1,
				'display_value'	=> 'text',
				'required'	=> array( 'shop_filters', '=', '1' )
			),
			array(
				'id'		=> 'shop_filters_scrollbar',
				'type'		=> 'select',
				'title'		=> __( 'Scrollbar', 'nm-framework-admin' ),
				'desc'		=> __( 'Enable scrollbar for product filters with long content (set height below).', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'Disable', 'default' => 'Default Scrollbar', 'js' => 'Custom Scrollbar' ),
				'default'	=> '0',
				'required'	=> array( 'shop_filters', '=', '1' )
			),
			array(
				'id'			=> 'shop_filters_height',
				'type'			=> 'slider',
				'title'			=> __( 'Filter Height', 'nm-framework-admin' ),
				'desc'			=> __( 'Set product filter height (longer content is scrollable).', 'nm-framework-admin' ),
				'default'		=> 145,
				'min'			=> 80,
				'max'			=> 1000,
				'step'			=> 1,
				'display_value'	=> 'text',
				'required'		=> array( 'shop_filters_scrollbar', '!=', '0' )
			),
			array (
				'id' 	=> 'shop_header_search_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Search', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'shop_search',
				'type'		=> 'select',
				'title'		=> __( 'Search', 'nm-framework-admin' ),
				'desc'		=> __( 'Select product search layout.', 'nm-framework-admin' ),
				'options'	=> array( 'header' => 'Display in Header', 'shop' => 'Display in Shop', '0' => 'Disable' ),
				'default'	=> 'shop'
			),
			array(
				'id'			=> 'shop_search_min_char',
				'type'			=> 'slider',
				'title'			=> __( 'Minimum Characters', 'nm-framework-admin' ),
				'desc'			=> __( 'Minimum number of characters required to search.', 'nm-framework-admin' ),
				'default'		=> 2,
				'min'			=> 1,
				'max'			=> 10,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'		=> 'shop_search_by_titles',
				'type'		=> 'switch',
				'title'		=> __( 'Titles Only', 'nm-framework-admin' ),
				'desc'		=> __( 'Search by product titles only.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Shop', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-shopping-cart',
		'fields'	=> array(
			array(
				'id'		=> 'shop_content_home',
				'type'		=> 'select',
				'title'		=> __( 'Page Content', 'nm-framework-admin' ),
				'desc'		=> __( 'Select when to display shop-page content (like a Banner Slider).', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'Display on all shop pages', '1' => 'Display on home-page only' ),
				'default'	=> '0'
			),
			array (
				'id' 	=> 'shop_category_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Category', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'shop_category_description',
				'type'		=> 'switch',
				'title'		=> __( 'Description', 'nm-framework-admin' ),
				'desc'		=> __( 'Display category description.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'shop_category_description_layout',
				'type'		=> 'select',
				'title'		=> __( 'Description Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select a category description layout.', 'nm-framework-admin' ),
				'options'	=> array( 'clean' => 'Clean', 'borders' => 'Borders' ),
				'default'	=> 'clean',
				'required'	=> array( 'shop_category_description', '=', '1' )
			),
			array (
				'id' 	=> 'shop_catalog_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Catalog', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'			=> 'shop_columns',
				'type'			=> 'slider',
				'title'			=> __( 'Columns', 'nm-framework-admin' ),
				'desc'			=> __( 'Select the number of product columns to display.', 'nm-framework-admin' ),
				'default'		=> 4,
				'min'			=> 1,
				'max'			=> 6,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'shop_columns_mobile',
				'type'			=> 'slider',
				'title'			=> __( 'Columns - Mobile', 'nm-framework-admin' ),
				'desc'			=> __( 'Select the number of product columns to display on mobile sized screen widths.', 'nm-framework-admin' ),
				'default'		=> 1,
				'min'			=> 1,
				'max'			=> 2,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'products_per_page',
				'type'			=> 'slider',
				'title'			=> __( 'Products per Page', 'nm-framework-admin' ),
				'desc'			=> __( 'Enter the number of products to display per page in the shop-catalog.', 'nm-framework-admin' ),
				'default'		=> 12,
				'min'			=> 1,
				'max'			=> 48,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'		=> 'product_sale_flash',
				'type'		=> 'select',
				'title'		=> __( 'Product Sale Flash', 'nm-framework-admin' ),
				'desc'		=> __( 'Product sale flash badges.', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'Disable', 'txt' => 'Display Sale Text', 'pct' => 'Display Sale Percentage' ),
				'default'	=> 'pct'
			),
			array(
				'id'		=> 'product_image_lazy_loading',
				'type'		=> 'switch',
				'title'		=> __( 'Image Lazy Loading', 'nm-framework-admin' ),
				'desc'		=> __( 'Lazy load product catalog images when scrolling down the page (speeds up load times).', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'product_hover_image_global',
				'type'		=> 'switch',
				'title'		=> __( 'Hover Image', 'nm-framework-admin' ),
				'desc'		=> __( 'Display a secondary image from the gallery when a product is "hovered".', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'product_action_link',
				'type'		=> 'select',
				'title'		=> __( 'Product Action Link', 'nm-framework-admin' ),
				'desc'		=> __( 'Configure the product action link (e.g. "Show more").', 'nm-framework-admin' ),
				'options'	=> array( 'action-link-hide' => 'Show on hover', 'action-link-show' => 'Always show', 'action-link-touch' => 'Always show on touch devices' ),
				'default'	=> 'action-link-hide'
			),
			array(
				'id'		=> 'shop_infinite_load',
				'type'		=> 'select',
				'title'		=> __( 'Infinite Load', 'nm-framework-admin' ),
				'desc'		=> __( 'Configure "infinite" product loading.', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'Disable', 'button' => 'Button', 'scroll' => 'Scroll' ),
				'default'	=> 'button'
			),
			array(
				'id'			=> 'shop_scroll_offset',
				'type'			=> 'slider',
				'title'			=> __( 'Scroll Offset', 'nm-framework-admin' ),
				'desc'			=> __( 'Used to offset the shop scroll position (for example when a category link is clicked).', 'nm-framework-admin' ),
				'default'		=> 70,
				'min'			=> 0,
				'max'			=> 1000,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array (
				'id' 	=> 'product_quickview_info',
				'icon'	=> true,
				'type'	=> 'info',
				'raw'	=> '<h3 style="margin: 0;">' . __( 'Quick View', 'nm-framework-admin' ) . '</h3>',
			),
			array(
				'id'		=> 'product_quickview',
				'type'		=> 'switch',
				'title'		=> __( 'Links', 'nm-framework-admin' ),
				'desc'		=> __( 'Display product quick view links.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'product_quickview_summary_layout',
				'type'		=> 'select',
				'title'		=> __( 'Product Summary', 'nm-framework-admin' ),
				'desc'		=> __( 'Select quick view product summary layout.', 'nm-framework-admin' ),
				'options'	=> array( 'align-top' => 'Align to Top (suitable for shorter images)', 'align-bottom' => 'Align to Bottom' ),
				'default'	=> 'align-bottom',
				'required'	=> array( 'product_quickview', '=', '1' )
			),
			array(
				'id'		=> 'product_quickview_atc',
				'type'		=> 'switch',
				'title'		=> __( 'Add to Cart Button', 'nm-framework-admin' ),
				'desc'		=> __( 'Display add-to-cart button.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable',
				'required'	=> array( 'product_quickview', '=', '1' )
			),
			array(
				'id'		=> 'product_quickview_details_button',
				'type'		=> 'switch',
				'title'		=> __( 'Details Button', 'nm-framework-admin' ),
				'desc'		=> __( 'Display button to full product details.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable',
				'required'	=> array( 'product_quickview', '=', '1' )
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Single Product', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-shopping-cart',
		'fields'	=> array(
			array(
				'id'			=> 'product_image_column_size',
				'type'			=> 'slider',
				'title'			=> __( 'Image Column Size', 'nm-framework-admin' ),
				'desc'			=> __( 'Select size-span of the product image column.', 'nm-framework-admin' ),
				'default'		=> 6,
				'min'			=> 2,
				'max'			=> 6,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'			=> 'product_image_max_size',
				'type'			=> 'slider',
				'title'			=> __( 'Image Size (Single column view)', 'nm-framework-admin' ),
				'desc'			=> __( 'Select a max-size (in pixels) for the product image when displayed in a single column (on smaller screen sizes).', 'nm-framework-admin' ),
				'default'		=> 500,
				'min'			=> 100,
				'max'			=> 1220,
				'step'			=> 1,
				'display_value'	=> 'text'
			),
			array(
				'id'		=> 'product_image_zoom',
				'type'		=> 'switch',
				'title'		=> __( 'Image Modal Gallery', 'nm-framework-admin' ),
				'desc'		=> __( 'Modal gallery for viewing full-size product images.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'product_image_hover_zoom',
				'type'		=> 'switch',
				'title'		=> __( 'Image Mouseover Zoom', 'nm-framework-admin' ),
				'desc'		=> __( 'Mouseover product images to zoom and pan.', 'nm-framework-admin' ),
				'default'	=> 0,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			),
			array(
				'id'		=> 'single_product_sale_flash',
				'type'		=> 'select',
				'title'		=> __( 'Sale Flash', 'nm-framework-admin' ),
				'desc'		=> __( 'Product sale flash badges.', 'nm-framework-admin' ),
				'options'	=> array( '0' => 'Disable', 'txt' => 'Display Sale Text', 'pct' => 'Display Sale Percentage' ),
				'default'	=> '0'
			),
			array(
				'id'		=> 'product_description_layout',
				'type'		=> 'select',
				'title'		=> __( 'Description Layout', 'nm-framework-admin' ),
				'desc'		=> __( 'Select layout for the product description.', 'nm-framework-admin' ),
				'options'	=> array( 'boxed' => 'Boxed', 'full' => 'Full Width' ),
				'default'	=> 'boxed'
			),
			array(
				'id'		=> 'product_reviews',
				'type'		=> 'switch',
				'title'		=> __( 'Reviews', 'nm-framework-admin' ),
				'desc'		=> __( 'Display product reviews tab.', 'nm-framework-admin' ),
				'default'	=> 1,
				'on'		=> 'Enable',
				'off'		=> 'Disable'
			)
		)
	) );
	
	/*Redux::setSection( $opt_name, array(
		'title'		=> __( 'Portfolio', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-website',
		'fields'	=> array(
			array(
				'id'	=> 'portfolio_home_id',
				'type'	=> 'select',
				'title'	=> __( 'Portfolio Home', 'nm-framework-admin' ),
				'desc'	=> __( 'Select portfolio home page (used for the "Show all projects" link on single portfolio pages).', 'nm-framework-admin' ),
				'data'	=> 'pages'
			)
		)
	) );*/
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Social Profiles', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-share',
		'fields'	=> array(
			array(
				'id'		=> 'social_media_facebook',
				'type' 		=> 'text',
				'title' 	=> __( 'Facebook', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Facebook profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_instagram',
				'type'		=> 'text',
				'title'		=> __( 'Instagram', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Instagram profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_twitter',
				'type'		=> 'text',
				'title'		=> __( 'Twitter', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Twitter profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_googleplus',
				'type'		=> 'text',
				'title'		=> __( 'Google+', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Google+ profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_flickr',
				'type' 		=> 'text',
				'title' 	=> __( 'Flickr', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Flickr profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_linkedin',
				'type'		=> 'text',
				'title'		=> __( 'LinedIn', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your LinkedIn profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_pinterest',
				'type'		=> 'text',
				'title'		=> __( 'Pinterest', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Pinterest profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_rss',
				'type'		=> 'text',
				'title'		=> __( 'RSS', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your RSS feed URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_tumblr',
				'type'		=> 'text',
				'title'		=> __( 'Tumblr', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Tumblr profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_vimeo',
				'type'		=> 'text',
				'title'		=> __( 'Vimeo', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Vimeo profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_vk',
				'type'		=> 'text',
				'title'		=> __( 'VK', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your VK profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_weibo',
				'type'		=> 'text',
				'title'		=> __( 'Weibo', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your Weibo profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			),
			array(
				'id'		=> 'social_media_youtube',
				'type'		=> 'text',
				'title'		=> __( 'YouTube', 'nm-framework-admin' ),
				'desc'		=> __( 'Enter your YouTube profile URL.', 'nm-framework-admin' ),
				'validate'	=> 'url',
				'default'	=> ''
			)
		)
	) );
	
	Redux::setSection( $opt_name, array(
		'title'		=> __( 'Custom Code', 'nm-framework-admin' ),
		'icon'		=> 'el-icon-lines',
		'fields'	=> array(
			array(
				'id'		=> 'custom_css',
				'type'		=> 'ace_editor',
				'title'		=> __( 'Custom CSS', 'nm-framework-admin' ),
				'desc'		=> __( "Add custom CSS to the head/top of your site.", 'nm-framework-admin' ),
				'mode'		=> 'css',
				'theme'		=> 'chrome',
				'default'	=> ''
			),
			array(
				'id'		=> 'custom_js',
				'type'		=> 'ace_editor',
				'title'		=> __( 'Custom JavaScript', 'nm-framework-admin' ),
				'desc'		=> __( "Add custom JavaScript to the footer/bottom of your theme.", 'nm-framework-admin' ),
				'mode'		=> 'javascript',
				'theme'		=> 'chrome',
				'default'	=> ''
			)
		)
	) );
    
    /*
     * <--- END SECTIONS
     */
	