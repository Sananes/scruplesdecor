<?php
/**
 * Plugin Name: Storefront Designer
 * Plugin URI: http://woothemes.com/products/storefront-designer/
 * Description: Adds a bunch of additional design options to the Storefront theme
 * Version: 1.4.2
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: storefront-designer
 * Domain Path: /languages/
 *
 * @package Storefront_Designer
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '40c9040f4cd8d35668e9c82c6cdbe001', '518358' );

/**
 * Returns the main instance of Storefront_Designer to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_Designer
 */
function Storefront_Designer() {
	return Storefront_Designer::instance();
} // End Storefront_Designer()

Storefront_Designer();

/**
 * Main Storefront_Designer Class
 *
 * @class Storefront_Designer
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_Designer
 */
final class Storefront_Designer {
	/**
	 * Storefront_Designer The single instance of Storefront_Designer.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'storefront-designer';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.4.2';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'sd_setup' ) );
	} // End __construct()

	/**
	 * Main Storefront_Designer Instance
	 *
	 * Ensures only one instance of Storefront_Designer is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_Designer()
	 * @return Main Storefront_Designer instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'storefront-designer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'sd_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront Designer extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-woocommerce-customiser' ), '<p>', '<a href="' . $url . '">', '</a>', '</p>', '<p><a href="' . $url . '" class="button button-primary">', '</a></p>' );

		update_option( 'sd_activation_notice', $notices );
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Setup all the things, if Storefront or a child theme using Storefront that has not disabled the Customizer settings is active
	 * @return void
	 */
	public function sd_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'storefront_designer_enabled', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'sd_script' ) );
			add_action( 'customize_register', array( $this, 'sd_customize_register' ) );
			add_filter( 'body_class', array( $this, 'sd_body_class' ) );
			add_action( 'wp_head', array( $this, 'sd_add_customizer_css' ) );

			add_action( 'wp', array( $this, 'sd_layout' ), 999 );

			add_action( 'customize_preview_init', array( $this, 'sd_customize_preview_js' ) );

			add_action( 'admin_notices', array( $this, 'customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
		}
	}

	/**
	 * Display a notice linking to the Customizer
	 * @since   1.0.0
	 * @return  void
	 */
	public function customizer_notice() {
		$notices = get_option( 'sd_activation_notice' );

		if ( $notices = get_option( 'sd_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'sd_activation_notice' );
		}
	}

	/**
	 * Enqueue CSS.
	 * @since   1.0.0
	 * @return  void
	 */
	public function sd_script() {
		$typographical_scheme 	= get_theme_mod( 'sd_typography', 'helvetica' );
		$sticky 				= get_theme_mod( 'sd_header_sticky', 'default' );
		$header_layout    		= get_theme_mod( 'sd_header_layout', 'compact' );

		wp_enqueue_style( 'sd-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );

		if ( 'lora' == $typographical_scheme ) {
			wp_enqueue_style( 'lora', '//fonts.googleapis.com/css?family=Lora:400,700,400italic' );
		}

		if ( 'roboto-slab' == $typographical_scheme ) {
			wp_enqueue_style( 'roboto-slab', '//fonts.googleapis.com/css?family=Roboto+Slab:400,700' );
		}

		if ( 'sticky-header' == $sticky ) {
			wp_enqueue_script( 'sd-sticky-script', plugins_url( '/assets/js/sticky-header.min.js', __FILE__ ), '1.0.1' );
		}

		if ( 'sticky-nav' == $sticky && ( 'compact' == $header_layout || 'expanded' == $header_layout )  ) {
			wp_enqueue_script( 'sd-sticky-header', plugins_url( '/assets/js/jquery-sticky.min.js', __FILE__ ), '1.0.0' );
			wp_enqueue_script( 'sd-sticky-navigation', plugins_url( '/assets/js/sticky-navigation.min.js', __FILE__ ), '1.0.0' );
		}
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  1.0.0
	 */
	public function sd_customize_preview_js() {
		wp_enqueue_script( 'sd-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.0', true );
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function sd_customize_register( $wp_customize ) {

		$theme	= wp_get_theme();

		/**
		 * Custom controls
		 */
		require_once dirname( __FILE__ ) . '/includes/class-control-header-layout.php';

		/**
		 * Modify existing controls
		 */
		$wp_customize->get_setting( 'storefront_header_background_color' )->transport = 'refresh';

		/**
		 * Header layout
		 */
		$wp_customize->add_setting( 'sd_header_layout', array(
			'default'    		=> 'compact',
		) );

		$wp_customize->add_control( new Header_Layout_Picker_Storefront_Control( $wp_customize, 'sd_header_layout', array(
			'label'    => __( 'Header layout', 'storefront' ),
			'section'  => 'header_image',
			'settings' => 'sd_header_layout',
			'priority' => 40,
		) ) );

		/**
		 * Sticky header
		 */
		$wp_customize->add_setting( 'sd_header_sticky', array(
			'default'           => 'default',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_header_sticky', array(
			'label'         => __( 'Sticky header', 'storefront-designer' ),
			'description'   => __( 'Stick the site header or navigation to the top of the browser window.', 'storefront-designer' ),
			'section'       => 'header_image',
			'settings'      => 'sd_header_sticky',
			'type'          => 'select',
			'choices'     	=> array(
				'default'     		=> 'None',
				'sticky-header'    	=> 'Sticky header',
				'sticky-nav' 		=> 'Sticky navigation',
			),
			'priority'      => 70,
		) ) );

		/**
		 * Footer copyright text
		 */
		if ( version_compare( $theme['Version'], "1.1.1" ) == 1 ) {
			$wp_customize->add_setting( 'sd_footer_copyright', array(
				'default' 		=> apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . get_the_date( 'Y' ) ),
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_footer_copyright', array(
				'label'         => __( 'Footer text', 'storefront-designer' ),
				'description'   => __( 'Tweak the copyright text in the footer.', 'storefront-designer' ),
				'section'       => 'storefront_footer',
				'settings'      => 'sd_footer_copyright',
				'type'          => 'text',
				'priority'      => 35,
			) ) );
		}

		/**
		 * Footer credit
		 */
		$wp_customize->add_setting( 'sd_footer_credit', array(
			'default' => true,
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_footer_credit', array(
			'label'         => __( 'Display credit link', 'storefront-designer' ),
			'description'   => __( 'Toggle the Storefront/WooThemes credit link in the footer.', 'storefront-designer' ),
			'section'       => 'storefront_footer',
			'settings'      => 'sd_footer_credit',
			'type'          => 'checkbox',
			'priority'      => 40,
		) ) );

		/**
		 * Max Width
		 */
		$wp_customize->add_setting( 'sd_max_width', array(
			'default'   => false,
			'transport' => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_max_width', array(
			'label'         => __( 'Max Width', 'storefront-designer' ),
			'description'   => __( 'Enlarge the width of the entire site to fill the browser window', 'storefront-designer' ),
			'section'       => 'storefront_layout',
			'settings'      => 'sd_max_width',
			'type'          => 'checkbox',
			'priority'      => 3,
		) ) );

		/**
		 * Content Frame
		 */
		$wp_customize->add_setting( 'sd_fixed_width', array(
			'default'           => false,
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_fixed_width', array(
			'label'       => __( 'Content frame', 'storefront-designer' ),
			'description' => __( 'Wraps the site content in a frame, offsetting it from the background.', 'storefront-designer' ),
			'section'     => 'storefront_layout',
			'settings'    => 'sd_fixed_width',
			'type'        => 'checkbox',
			'priority'    => 5,
		) ) );

		/**
		 * Content background color setting
		 */
		$wp_customize->add_setting( 'sd_content_background_color', array(
			'default'           => apply_filters( 'storefront_default_background_color', '#fcfcfc' ),
			'sanitize_callback' => 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sd_content_background_color', array(
			'label'       => __( 'Content background color', 'storefront-designer' ),
			'description' => __( 'Applied to the content background when utilising the content frame option', 'storefront-designer' ),
			'section'     => 'storefront_layout',
			'settings'    => 'sd_content_background_color',
			'priority'    => 6,
		) ) );

		/**
		 * Button flatten
		 */
		$wp_customize->add_setting( 'sd_button_flat', array(
			'default'   => false,
			'transport' => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_button_flat', array(
			'label'       => __( '2D', 'storefront-designer' ),
			'description' => __( 'Toggles the default "3D" button affect.', 'storefront-designer' ),
			'section'     => 'storefront_buttons',
			'settings'    => 'sd_button_flat',
			'type'        => 'checkbox',
			'priority'    => 50,
		) ) );

		/**
		 * Button shadows
		 */
		$wp_customize->add_setting( 'sd_button_shadows', array(
			'default'   => false,
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_button_shadows', array(
			'label'       => __( 'Button shadows', 'storefront-designer' ),
			'description' => __( 'Toggles the button text and box shadows', 'storefront-designer' ),
			'section'     => 'storefront_buttons',
			'settings'    => 'sd_button_shadows',
			'type'        => 'checkbox',
			'priority'    => 55,
		) ) );

		/**
		 * Button background style
		 */
		$wp_customize->add_setting( 'sd_button_background_style', array(
			'default' => 'default',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_button_background_style', array(
			'label'       => __( 'Background style', 'storefront-designer' ),
			'description' => __( 'Choose a background style for your buttons', 'storefront-designer' ),
			'section'     => 'storefront_buttons',
			'settings'    => 'sd_button_background_style',
			'type'        => 'select',
			'priority'    => 60,
			'choices'     => array(
				'default'     => 'Solid fill',
				'gradient'    => 'Gradient fill',
				'transparent' => 'Transparent',
			),
		) ) );

		/**
		 * Button radius
		 */
		$wp_customize->add_setting( 'sd_button_rounded', array(
			'default' => 'default',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_button_rounded', array(
			'label'       => __( 'Button radius', 'storefront-designer' ),
			'description' => __( 'Apply rounded corners to buttons', 'storefront-designer' ),
			'section'     => 'storefront_buttons',
			'settings'    => 'sd_button_rounded',
			'type'        => 'select',
			'priority'    => 70,
			'choices'     => array(
				'default' => 'Square',
				'small'   => 'Small',
				'full'    => 'Full',
			),
		) ) );

		/**
		 * Button size
		 */
		$wp_customize->add_setting( 'sd_button_size', array(
			'default' => 'default',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_button_size', array(
			'label'       => __( 'Button size', 'storefront-designer' ),
			'description' => __( 'Increase / decrease the button size', 'storefront-designer' ),
			'section'     => 'storefront_buttons',
			'settings'    => 'sd_button_size',
			'type'        => 'select',
			'priority'    => 80,
			'choices'     => array(
				'default' 	=> 'Default',
				'smaller'   => 'Smaller',
				'larger'    => 'Larger',
			),
		) ) );

		/**
		 * Typographical scheme
		 */
		$wp_customize->add_setting( 'sd_typography', array(
			'default' => 'helvetica',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_typography', array(
			'label'    => __( 'Typographical scheme', 'storefront-designer' ),
			'section'  => 'storefront_typography',
			'settings' => 'sd_typography',
			'priority' => 60,
			'type'     => 'select',
			'choices'  => array(
				'helvetica'   => 'Helvetica',
				'lora'        => 'Lora',
				'roboto-slab' => 'Roboto Slab',
				'courier'     => 'Courier',
			),
		) ) );

		/**
		 * Typographical Scale
		 */
		$wp_customize->add_setting( 'sd_scale', array(
			'default'   => 'default',
			'transport' => 'postMessage',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sd_scale', array(
			'label'    => __( 'Typographical scale', 'storefront-designer' ),
			'section'  => 'storefront_typography',
			'settings' => 'sd_scale',
			'priority' => 60,
			'type'     => 'select',
			'choices'  => array(
				'smaller' => 'Smaller',
				'default' => 'Default',
				'larger'  => 'Larger',
			),
		) ) );
	}

	/**
	 * Storefront Designer Body Class
	 * @see get_theme_mod()
	 */
	public function sd_body_class( $classes ) {
		$fixed_width 			= get_theme_mod( 'sd_fixed_width', false );
		$max_width 				= get_theme_mod( 'sd_max_width', false );
		$typographical_scheme 	= get_theme_mod( 'sd_typography', 'helvetica' );
		$button_rounded 		= get_theme_mod( 'sd_button_rounded', 'default' );
		$button_size 			= get_theme_mod( 'sd_button_size', 'default' );
		$button_flat 	 		= get_theme_mod( 'sd_button_flat', false );
		$button_shadows 		= get_theme_mod( 'sd_button_shadows', false );
		$bg_style				= get_theme_mod( 'sd_button_background_style', 'default' );
		$scale 					= get_theme_mod( 'sd_scale', 'default' );
		$header_layout 			= get_theme_mod( 'sd_header_layout', 'compact' );
		$sticky 				= get_theme_mod( 'sd_header_sticky', 'default' );

		if ( true == $fixed_width ) {
			$classes[] = 'sd-fixed-width';
		}

		if ( true == $max_width ) {
			$classes[] = 'sd-max-width';
		}

		if ( 'small' == $button_rounded ) {
			$classes[] = 'sd-buttons-rounded';
		}

		if ( 'full' == $button_rounded ) {
			$classes[] = 'sd-buttons-rounded-full';
		}

		if ( true == $button_flat ) {
			$classes[] = 'sd-buttons-flat';
		}

		if ( true == $button_shadows ) {
			$classes[] = 'sd-buttons-shadows';
		}

		if ( 'transparent' == $bg_style ) {
			$classes[] = 'sd-buttons-transparent';
		}

		if ( 'smaller' == $scale ) {
			$classes[] = 'sd-scale-smaller';
		}

		if ( 'larger' == $scale ) {
			$classes[] = 'sd-scale-larger';
		}

		if ( 'sticky-header' == $sticky ) {
			$classes[] = 'sd-header-sticky';
		}

		$classes[] = 'sd-button-size-' . $button_size;

		$classes[] = 'sd-header-' . $header_layout;

		$classes[] = 'sd-typography-' . $typographical_scheme;

		return $classes;
	}

	/**
	 * Layout
	 * Tweaks layout based on settings
	 */
	public function sd_layout() {
		$header_layout    	= get_theme_mod( 'sd_header_layout', 'compact' );
		$footer_credit    	= get_theme_mod( 'sd_footer_credit', true );
		$footer_copyright 	= trim( get_theme_mod( 'sd_footer_copyright', '' ) );
		$sticky 			= get_theme_mod( 'sd_header_sticky', 'default' );

		if ( 'expanded' == $header_layout ) {
			remove_action( 'storefront_header', 'storefront_site_branding', 		20 );
			add_action( 'storefront_header', 'storefront_site_branding', 			45 );
		}

		if ( 'inline' == $header_layout ) {
			remove_action( 'storefront_header', 'storefront_product_search', 		40 );
			remove_action( 'storefront_header', 'storefront_secondary_navigation', 	30 );
		}

		if ( false == $footer_credit ) {
			add_filter( 'storefront_credit_link', '__return_false' );
		}

		if ( ! empty( $footer_copyright ) ) {
			add_filter( 'storefront_copyright_text', array( $this, 'sd_tweak_copyright_text' ), 20 );
		}

		if ( 'sticky-nav' == $sticky && ( 'compact' == $header_layout || 'expanded' == $header_layout )  ) {
			add_action( 'storefront_header', array( $this, 'sd_primary_navigation_wrapper' ), 45 );
			add_action( 'storefront_header', array( $this, 'sd_primary_navigation_wrapper_close' ), 65 );
		}
	}

	/**
	 * Add CSS in <head> for styles handled by the theme customizer
	 *
	 * @since 1.0.0
	 */
	public function sd_add_customizer_css() {
		$content_background_color 		= storefront_sanitize_hex_color( get_theme_mod( 'sd_content_background_color', apply_filters( 'storefront_default_background_color', '#fcfcfc' ) ) );
		$button_background_color 		= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_background_color', apply_filters( 'storefront_default_button_background_color', '#787E87' ) ) );
		$button_alt_background_color 	= storefront_sanitize_hex_color( get_theme_mod( 'storefront_button_alt_background_color', apply_filters( 'storefront_default_button_alt_background_color', '#a46497' ) ) );
		$header_background_color 		= storefront_sanitize_hex_color( get_theme_mod( 'storefront_header_background_color', apply_filters( 'storefront_default_header_background_color', '#2c2d33' ) ) );
		$bg_style 						= get_theme_mod( 'sd_button_background_style', 'default' );
		$brighten_factor 				= apply_filters( 'storefront_brighten_factor', 25 );
		$darken_factor 					= apply_filters( 'storefront_darken_factor', -25 );

		?>
		<!-- storefront designer customizer CSS -->
		<style>
		<?php if ( 'gradient' == $bg_style ) { ?>
			button, input[type="button"], input[type="reset"], input[type="submit"], .button, .added_to_cart, .widget-area .widget a.button, .site-header-cart .widget_shopping_cart a.button {
				background: <?php echo $button_background_color; ?>; /* Old browsers */
				background: -moz-linear-gradient(top,  <?php echo $button_background_color; ?> 0%, <?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?> 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $button_background_color; ?>), color-stop(100%,<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?>)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  <?php echo $button_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?> 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  <?php echo $button_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?> 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  <?php echo $button_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?> 100%); /* IE10+ */
				background: linear-gradient(to bottom,  <?php echo $button_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?> 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $button_background_color; ?>', endColorstr='<?php echo storefront_adjust_color_brightness( $button_background_color, $darken_factor ); ?>',GradientType=0 ); /* IE6-9 */
			}

			button.alt, input[type="button"].alt, input[type="reset"].alt, input[type="submit"].alt, .button.alt, .added_to_cart.alt, .widget-area .widget a.button.alt, .added_to_cart {
				background: <?php echo $button_alt_background_color; ?>; /* Old browsers */
				background: -moz-linear-gradient(top,  <?php echo $button_alt_background_color; ?> 0%, <?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?> 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $button_alt_background_color; ?>), color-stop(100%,<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?>)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  <?php echo $button_alt_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?> 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  <?php echo $button_alt_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?> 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  <?php echo $button_alt_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?> 100%); /* IE10+ */
				background: linear-gradient(to bottom,  <?php echo $button_alt_background_color; ?> 0%,<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?> 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $button_alt_background_color; ?>', endColorstr='<?php echo storefront_adjust_color_brightness( $button_alt_background_color, $darken_factor ); ?>',GradientType=0 ); /* IE6-9 */
			}
		<?php } ?>

			.plus,
			.minus {
				background: none !important;
			}

			.sd-fixed-width .site {
				background-color: <?php echo $content_background_color; ?>;
			}

			.sticky-wrapper,
			.sd-sticky-navigation,
			.sd-sticky-navigation:before,
			.sd-sticky-navigation:after {
				background-color: <?php echo $header_background_color; ?>;
			}

		</style>
		<?php
	}

	/**
	 * Tweak the copyright section text in the footer.
	 *
	 * @since 1.0.1
	 */
	public function sd_tweak_copyright_text() {
		return make_clickable( esc_html( trim( get_theme_mod( 'sd_footer_copyright', '' ) ) ) );
	}

	/**
	 * Primary navigation wrapper
	 * @return void
	 */
	function sd_primary_navigation_wrapper() {
		echo '<section class="sd-sticky-navigation fixedsticky">';
	}

	/**
	 * Primary navigation wrapper close
	 * @return void
	 */
	function sd_primary_navigation_wrapper_close() {
		echo '</section>';
	}

} // End Class
