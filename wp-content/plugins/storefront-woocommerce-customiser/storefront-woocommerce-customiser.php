<?php
/**
 * Plugin Name: Storefront WooCommerce Customiser
 * Plugin URI: http://woothemes.com/products/storefront-woocommerce-customiser/
 * Description: Adds options to the customise the WooCommerce appearance and behaviour
 * Version: 1.3.0
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: storefront-woocommerce-customiser
 * Domain Path: /languages/
 *
 * @package Storefront_WooCommerce_Customiser
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
woothemes_queue_update( plugin_basename( __FILE__ ), '645b6c28ced85553f07e81e72c3e9186', '518369' );

/**
 * Returns the main instance of Storefront_WooCommerce_Customiser to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Storefront_WooCommerce_Customiser
 */
function Storefront_WooCommerce_Customiser() {
	return Storefront_WooCommerce_Customiser::instance();
} // End Storefront_WooCommerce_Customiser()

Storefront_WooCommerce_Customiser();

/**
 * Main Storefront_WooCommerce_Customiser Class
 *
 * @class Storefront_WooCommerce_Customiser
 * @version	1.0.0
 * @since 1.0.0
 * @package	Storefront_WooCommerce_Customiser
 */
final class Storefront_WooCommerce_Customiser {
	/**
	 * Storefront_WooCommerce_Customiser The single instance of Storefront_WooCommerce_Customiser.
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
		$this->token 			= 'storefront-woocommerce-customiser';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.3.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'swc_setup' ) );
	} // End __construct()

	/**
	 * Main Storefront_WooCommerce_Customiser Instance
	 *
	 * Ensures only one instance of Storefront_WooCommerce_Customiser is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Storefront_WooCommerce_Customiser()
	 * @return Main Storefront_WooCommerce_Customiser instance
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
		load_plugin_textdomain( 'storefront-woocommerce-customiser', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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

		$notices 		= get_option( 'swc_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the Storefront WooCommerce Customiser installing. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'storefront-woocommerce-customiser' ), '<p>', '<a href="' . $url . '">', '</a>', '</p>', '<p><a href="' . $url . '" class="button button-primary">', '</a></p>' );

		update_option( 'swc_activation_notice', $notices );

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
	public function swc_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'storefront_woocommerce_customizer_enabled', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'swc_script' ) );
			add_action( 'customize_register', array( $this, 'swc_customize_register' ) );
			add_filter( 'body_class', array( $this, 'swc_body_class' ) );

			add_filter( 'storefront_loop_columns', array( $this, 'swc_shop_columns' ), 999 );
			add_filter( 'storefront_products_per_page', array( $this, 'swc_shop_products_per_page' ), 999 );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'swc_product_loop_wrap' ), 40 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'swc_product_loop_wrap_close' ), 5 );
			add_action( 'wp', array( $this, 'swc_shop_layout' ), 999 );

			add_filter( 'storefront_product_categories_args', array( $this, 'swc_product_category_args' ) );
			add_filter( 'storefront_recent_products_args', array( $this, 'swc_recent_product_args' ) );
			add_filter( 'storefront_featured_products_args', array( $this, 'swc_featured_product_args' ) );
			add_filter( 'storefront_popular_products_args', array( $this, 'swc_popular_product_args' ) );
			add_filter( 'storefront_on_sale_products_args', array( $this, 'swc_on_sale_product_args' ) );
			add_filter( 'storefront_product_thumbnail_columns', array( $this, 'swc_product_thumbnails' ) );

			add_action( 'admin_notices', array( $this, 'customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );

			// Composite Products integration
			if ( class_exists( 'WC_Composite_Products' ) ) {
				global $woocommerce_composite_products;

				if ( isset( $woocommerce_composite_products->version ) && version_compare( $woocommerce_composite_products->version, '3.0', '>=' ) ) {
					// filter component options loop columns
					add_filter( 'woocommerce_composite_component_loop_columns', array( $this, 'swc_cp_component_options_loop_columns' ), 5 );
					// filter max component options per page
					add_filter( 'woocommerce_component_options_per_page', array( $this, 'swc_cp_component_options_per_page' ), 5 );
					// filter max component columns in review/summary
					add_filter( 'woocommerce_composite_component_summary_max_columns', array( $this, 'swc_cp_summary_max_columns' ), 5 );
					// filter toggle-box view
					add_filter( 'woocommerce_composite_component_toggled', array( $this, 'swc_cp_component_toggled' ), 5, 3 );

					// register additional customizer section
					add_action( 'customize_register', array( $this, 'swc_cp_customize_register' ) );
				}
			}
		}
	}

	/**
	 * Display a notice linking to the Customizer
	 * @since   1.0.0
	 * @return  void
	 */
	public function customizer_notice() {
		$notices = get_option( 'swc_activation_notice' );

		if ( $notices = get_option( 'swc_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'swc_activation_notice' );
		}
	}

	/**
	 * Enqueue CSS.
	 * @since   1.0.0
	 * @return  void
	 */
	public function swc_script() {
		wp_enqueue_style( 'swc-styles', plugins_url( '/assets/css/style.css', __FILE__ ), '', '1.2.1' );
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function swc_customize_register( $wp_customize ) {

        /**
		 * Header search bar toggle
		 */
		$wp_customize->add_setting( 'swc_header_search', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_header_search', array(
            'label'         => __( 'Search', 'storefront-woocommerce-customiser' ),
            'description' 	=> __( 'Toggle the display of the search form.', 'storefront-woocommerce-customiser' ),
            'section'       => 'header_image',
            'settings'      => 'swc_header_search',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

		/**
		 * Header cart toggle
		 */
		$wp_customize->add_setting( 'swc_header_cart', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_header_cart', array(
            'label'         => __( 'Cart link', 'storefront-woocommerce-customiser' ),
            'description' 	=> __( 'Toggle the display of the cart link / dropdown.', 'storefront-woocommerce-customiser' ),
            'section'       => 'header_image',
            'settings'      => 'swc_header_cart',
            'type'          => 'checkbox',
            'priority'		=> 60,
        ) ) );

	    /**
	     * Shop Section
	     */
        $wp_customize->add_section( 'swc_shop_section' , array(
		    'title'      	=> __( 'Shop', 'storefront-woocommerce-customiser' ),
		    'description' 	=> __( 'Customise the look & feel of your product catalog', 'storefront-woocommerce-customiser' ),
		    'priority'   	=> 55,
		) );

		/**
    	 * Shop Layout
    	 */
        $wp_customize->add_setting( 'swc_shop_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_shop_layout', array(
            'label'         => __( 'Shop layout', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Applied to the shop page & product archives.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_shop_layout',
            'type'     		=> 'radio',
            'priority'		=> 5,
			'choices'  		=> array(
				'default'			=> 'Default',
				'full-width'		=> 'Full Width',
			),
        ) ) );

        /**
         * Product Columns
         */
	    $wp_customize->add_setting( 'swc_product_columns', array(
	        'default'           => '3',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_columns', array(
            'label'         => __( 'Product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_columns',
            'type'     		=> 'select',
            'priority'		=> 7,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * Products per Page
	     */
        $wp_customize->add_setting( 'swc_products_per_page', array(
	        'default'           => '12',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_products_per_page', array(
            'label'         => __( 'Products per page', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_products_per_page',
            'type'     		=> 'select',
            'priority'		=> 10,
            'choices'  		=> array(
				'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
				'13'		=> '13',
				'14'		=> '14',
				'15'		=> '15',
				'16'		=> '16',
				'17'		=> '17',
				'18'		=> '18',
				'19'		=> '19',
				'20'		=> '20',
				'21'		=> '21',
				'22'		=> '22',
				'23'		=> '23',
				'24'		=> '24',
			),
        ) ) );

        /**
         * Product Alignment
         */
	    $wp_customize->add_setting( 'swc_shop_alignment', array(
	        'default'           => 'center',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_shop_alignment', array(
            'label'         => __( 'Product alignment', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Align product titles, prices, add to cart buttons, etc.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_shop_alignment',
            'type'     		=> 'select',
            'priority'		=> 11,
			'choices'  		=> array(
				'center'			=> 'Center',
				'left'				=> 'Left',
				'right' 			=> 'Right',
			),
        ) ) );

	    $wp_customize->add_setting( 'swc_product_archive_results_count', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_results_count', array(
            'label'         => __( 'Display product results count', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product results count.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_results_count',
            'type'          => 'checkbox',
            'priority'		=> 15,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_sorting', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_sorting', array(
            'label'         => __( 'Display product sorting', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product sorting dropdown.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_sorting',
            'type'          => 'checkbox',
            'priority'		=> 15,
        ) ) );

         $wp_customize->add_setting( 'swc_product_archive_image', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_image', array(
            'label'         => __( 'Display product image', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product images.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_image',
            'type'          => 'checkbox',
            'priority'		=> 20,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_title', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_title', array(
            'label'         => __( 'Display product title', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product titles.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_title',
            'type'          => 'checkbox',
            'priority'		=> 30,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_sale_flash', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_sale_flash', array(
            'label'         => __( 'Display sale flash', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the sale flashes.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_sale_flash',
            'type'          => 'checkbox',
            'priority'		=> 40,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_rating', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_rating', array(
            'label'         => __( 'Display rating', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product ratings.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_rating',
            'type'          => 'checkbox',
            'priority'		=> 50,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_price', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_price', array(
            'label'         => __( 'Display price', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product prices.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_price',
            'type'          => 'checkbox',
            'priority'		=> 60,
        ) ) );

        $wp_customize->add_setting( 'swc_product_archive_add_to_cart', array(
	        'default'           => true,
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_archive_add_to_cart', array(
            'label'         => __( 'Display add to cart button', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the add to cart buttons.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_shop_section',
            'settings'      => 'swc_product_archive_add_to_cart',
            'type'          => 'checkbox',
            'priority'		=> 70,
        ) ) );

	    /**
	     * Product Details Section
	     */
        $wp_customize->add_section( 'swc_product_details_section' , array(
		    'title'      	=> __( 'Product Details', 'storefront-woocommerce-customiser' ),
		    'description' 	=> __( 'Customise the look & feel of your product details pages', 'storefront-woocommerce-customiser' ),
		    'priority'   	=> 56,
		) );

		/**
    	 * Product Layout
    	 */
        $wp_customize->add_setting( 'swc_product_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_layout', array(
            'label'         => __( 'Layout', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Applied to the product details page', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_layout',
            'type'     		=> 'radio',
            'priority'		=> 5,
			'choices'  		=> array(
				'default'			=> 'Default',
				'full-width'		=> 'Full Width',
			),
        ) ) );

	    /**
	     * Product gallery layout
	     */
        $wp_customize->add_setting( 'swc_product_gallery_layout', array(
	        'default'           => 'default',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_gallery_layout', array(
            'label'         => __( 'Gallery layout', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_gallery_layout',
            'type'     		=> 'select',
            'priority'		=> 10,
            'choices'  		=> array(
				'default'			=> 'Default',
            	'stacked'			=> 'Stacked',
            	'hidden'			=> 'Hide product galleries',
			),
        ) ) );

        /**
         * Toggle product tabs
         */
        $wp_customize->add_setting( 'swc_product_details_tab', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_details_tab', array(
            'label'         => __( 'Display product tabs', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of the product tabs.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_details_tab',
            'type'          => 'checkbox',
            'priority'		=> 20,
        ) ) );

        /**
         * Toggle related products
         */
        $wp_customize->add_setting( 'swc_related_products', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_related_products', array(
            'label'         => __( 'Display related products', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of related products.', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_related_products',
            'type'          => 'checkbox',
            'priority'		=> 30,
        ) ) );

        /**
         * Toggle product meta
         */
        $wp_customize->add_setting( 'swc_product_meta', array(
	        'default'           => true,
	    ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_product_meta', array(
            'label'         => __( 'Display product meta', 'storefront-woocommerce-customiser' ),
            'description'	=> __( 'Toggle the display of product meta (category/sku).', 'storefront-woocommerce-customiser' ),
            'section'       => 'swc_product_details_section',
            'settings'      => 'swc_product_meta',
            'type'          => 'checkbox',
            'priority'		=> 40,
        ) ) );

        /**
	     * Homepage Section
	     */
	    $wp_customize->add_section( 'storefront_homepage' , array(
		    'title'      	=> __( 'Homepage', 'storefront' ),
		    'priority'   	=> 60,
		    'description' 	=> __( 'Customise the look & feel of the Storefront homepage template.', 'storefront' ),
		) );

		/**
		 * Page Content Toggle
		 */
		if ( ! $this->is_homepage_control_activated() ) {
			$wp_customize->add_setting( 'swc_homepage_content', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_content', array(
	            'label'         => __( 'Display page content', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the page content.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_content',
	            'type'          => 'checkbox',
	            'priority'		=> 10,
	        ) ) );
		}


		if ( ! $this->is_homepage_control_activated() ) {

			/**
			 * Product Category Toggle
			 */
			$wp_customize->add_setting( 'swc_homepage_categories', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_categories', array(
	            'label'         => __( 'Display product categories', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the product categories.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_categories',
	            'type'          => 'checkbox',
	            'priority'		=> 20,
	        ) ) );

		}

        /**
         * Category Title
         */
	    $wp_customize->add_setting( 'swc_homepage_category_title', array(
	        'default'           => __( 'Product Categories', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_title', array(
            'label'         => __( 'Product category title', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_category_title',
            'type'     		=> 'text',
            'priority'		=> 22,
        ) ) );

        /**
         * Category Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_category_columns', array(
	        'default'           => '3',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_columns', array(
            'label'         => __( 'Product category columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_category_columns',
            'type'     		=> 'select',
            'priority'		=> 24,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * Category limit
	     */
        $wp_customize->add_setting( 'swc_homepage_category_limit', array(
	        'default'           => '3',
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_category_limit', array(
            'label'         => __( 'Product categories to display', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_category_limit',
            'type'     		=> 'select',
            'priority'		=> 26,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
		 * Recent Products Toggle
		 */

		if ( ! $this->is_homepage_control_activated() ) {

			$wp_customize->add_setting( 'swc_homepage_recent', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent', array(
	            'label'         => __( 'Display recent products', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the recent products.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_recent',
	            'type'          => 'checkbox',
	            'priority'		=> 30,
	        ) ) );

		}

        /**
         * Recent Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_title', array(
	        'default'           => __( 'Recent Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_title', array(
            'label'         => __( 'Recent product title', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_recent_products_title',
            'type'     		=> 'text',
            'priority'		=> 32,
        ) ) );

        /**
         * Recent Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_recent_products_columns', array(
	        'default'           => '4',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_columns', array(
            'label'         => __( 'Recent product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_recent_products_columns',
            'type'     		=> 'select',
            'priority'		=> 34,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * Recent Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_recent_products_limit', array(
	        'default'           => '4',
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_recent_products_limit', array(
            'label'         => __( 'Recent products to display', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_recent_products_limit',
            'type'     		=> 'select',
            'priority'		=> 36,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
		 * Featured Products Toggle
		 */

		if ( ! $this->is_homepage_control_activated() ) {

			$wp_customize->add_setting( 'swc_homepage_featured', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured', array(
	            'label'         => __( 'Display featured products', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the featured products.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_featured',
	            'type'          => 'checkbox',
	            'priority'		=> 40,
	        ) ) );

		}

        /**
         * Featured Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_title', array(
	        'default'           => __( 'Featured Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_title', array(
            'label'         => __( 'Featured product title', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_featured_products_title',
            'type'     		=> 'text',
            'priority'		=> 42,
        ) ) );

        /**
         * Featured Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_featured_products_columns', array(
	        'default'           => '4',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_columns', array(
            'label'         => __( 'Featured product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_featured_products_columns',
            'type'     		=> 'select',
            'priority'		=> 44,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * Featured Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_featured_products_limit', array(
	        'default'           => '4',
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_featured_products_limit', array(
            'label'         => __( 'Featured products to display', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_featured_products_limit',
            'type'     		=> 'select',
            'priority'		=> 46,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
		 * Top Rated Toggle
		 */

		if ( ! $this->is_homepage_control_activated() ) {

			$wp_customize->add_setting( 'swc_homepage_top_rated', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated', array(
	            'label'         => __( 'Display top rated products', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the top rated products.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_top_rated',
	            'type'          => 'checkbox',
	            'priority'		=> 50,
	        ) ) );

		}

        /**
         * Top rated Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_top_rated_products_title', array(
	        'default'           => __( 'Top rated Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_title', array(
            'label'         => __( 'Top rated product title', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_top_rated_products_title',
            'type'     		=> 'text',
            'priority'		=> 52,
        ) ) );

        /**
         * Top rated Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_top_rated_products_columns', array(
	        'default'           => '4',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_columns', array(
            'label'         => __( 'Top rated product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_top_rated_products_columns',
            'type'     		=> 'select',
            'priority'		=> 54,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * Top rated Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_top_rated_products_limit', array(
	        'default'           => '4',
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_top_rated_products_limit', array(
            'label'         => __( 'Top rated products to display', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_top_rated_products_limit',
            'type'     		=> 'select',
            'priority'		=> 56,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );

        /**
		 * On Sale Toggle
		 */

		if ( ! $this->is_homepage_control_activated() ) {

			$wp_customize->add_setting( 'swc_homepage_on_sale', array(
		        'default'           => true,
		    ) );

		    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale', array(
	            'label'         => __( 'Display on sale products', 'storefront-woocommerce-customiser' ),
	            'description'	=> __( 'Toggle the display of the on sale products.', 'storefront-woocommerce-customiser' ),
	            'section'       => 'storefront_homepage',
	            'settings'      => 'swc_homepage_on_sale',
	            'type'          => 'checkbox',
	            'priority'		=> 60,
	        ) ) );

		}

        /**
         * On sale Products Title
         */
	    $wp_customize->add_setting( 'swc_homepage_on_sale_products_title', array(
	        'default'           => __( 'On sale Products', 'storefront-woocommerce-customiser' ),
	        'sanitize_callback' => 'sanitize_text_field',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_title', array(
            'label'         => __( 'On sale product title', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_on_sale_products_title',
            'type'     		=> 'text',
            'priority'		=> 62,
        ) ) );

        /**
         * On sale Products Columns
         */
	    $wp_customize->add_setting( 'swc_homepage_on_sale_products_columns', array(
	        'default'           => '4',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_columns', array(
            'label'         => __( 'On sale product columns', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_on_sale_products_columns',
            'type'     		=> 'select',
            'priority'		=> 64,
			'choices'  		=> array(
				'1'			=> '1',
				'2'			=> '2',
				'3' 		=> '3',
				'4'  		=> '4',
			),
        ) ) );

	    /**
	     * On sale Products limit
	     */
        $wp_customize->add_setting( 'swc_homepage_on_sale_products_limit', array(
	        'default'           => '4',
	    ) );


	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_homepage_on_sale_products_limit', array(
            'label'         => __( 'On sale products to display', 'storefront-woocommerce-customiser' ),
            'section'       => 'storefront_homepage',
            'settings'      => 'swc_homepage_on_sale_products_limit',
            'type'     		=> 'select',
            'priority'		=> 66,
            'choices'  		=> array(
            	'1'			=> '1',
            	'2'			=> '2',
            	'3'			=> '3',
				'4'			=> '4',
				'5'			=> '5',
				'6' 		=> '6',
				'7'  		=> '7',
				'8'			=> '8',
				'9'			=> '9',
				'10'		=> '10',
				'11'		=> '11',
				'12'		=> '12',
			),
        ) ) );
	}

	/**
	 * Filter the homepage product categories
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_product_category_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_category_title', __( 'Product Categories', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_category_columns', '3' );
		$limit 					= get_theme_mod( 'swc_homepage_category_limit', '3' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage recent product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_recent_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_recent_products_title', __( 'Recent Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_recent_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_recent_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage featured product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_featured_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_featured_products_title', __( 'Featured Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_featured_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_featured_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage popular product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_popular_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_top_rated_products_title', __( 'Top rated Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_top_rated_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_top_rated_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Filter the homepage on sale product args
	 * @param  array $args the default args
	 * @return array $args the filtered args based on settings
	 */
	public function swc_on_sale_product_args( $args ) {
		$title 					= get_theme_mod( 'swc_homepage_on_sale_products_title', __( 'On sale Products', 'storefront-woocommerce-customiser' ) );
		$columns 				= get_theme_mod( 'swc_homepage_on_sale_products_columns', '4' );
		$limit 					= get_theme_mod( 'swc_homepage_on_sale_products_limit', '4' );

		$args['title']			= $title;
		$args['columns'] 		= $columns;
		$args['limit'] 			= $limit;

		return $args;
	}

	/**
	 * Storefront WooCommerce Customiser Body Class
	 * @see get_theme_mod()
	 */
	public function swc_body_class( $classes ) {
		$shop_layout 	 		= get_theme_mod( 'swc_shop_layout', 'default' );
		$shop_alignment 	 	= get_theme_mod( 'swc_shop_alignment', 'center' );
		$product_layout 		= get_theme_mod( 'swc_product_layout', 'default' );
		$header_search 			= get_theme_mod( 'swc_header_search', true );
		$header_cart 			= get_theme_mod( 'swc_header_cart', true );
		$archive_titles 		= get_theme_mod( 'swc_product_archive_title', true );
		$product_gallery_layout = get_theme_mod( 'swc_product_gallery_layout', 'default' );

		if ( class_exists( 'WooCommerce' ) ) {

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' == $shop_layout ) {
					$classes[] = 'storefront-full-width-content';
				}
			}

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_page_template( 'template-homepage.php' ) ) {
				if ( false == $archive_titles ) {
					$classes[] = 'swc-archive-hide-product-titles';
				}
			}

			if ( is_product() ) {
				if ( 'full-width' == $product_layout ) {
					$classes[] = 'storefront-full-width-content';
				}
			}

			if ( is_product() && 'hidden' == $product_gallery_layout ) {
				$classes[] = 'swc-product-gallery-hidden';
			}

			if ( is_product() && 'stacked' == $product_gallery_layout ) {
				$classes[] = 'swc-product-gallery-stacked';
			}

			$classes[] = 'swc-shop-alignment-' . $shop_alignment;

		}

		if ( false == $header_search ) {
			$classes[] = 'swc-header-no-search';
		}

		if ( false == $header_cart ) {
			$classes[] = 'swc-header-no-cart';
		}

		return $classes;
	}

	/**
	 * Shop columns
	 * @return integer shop columns
	 */
	public function swc_shop_columns( $columns ) {
		$columns = get_theme_mod( 'swc_product_columns', '3' );

		if ( $columns ) {
			return $columns;
		} else {
			return apply_filters( 'storefront_loop_columns', 3 );
		}
	}

	/**
	 * Product thumbnail layout
	 * Tweak the number of columns thumbnails are arranged into based on settings
	 */
	public function swc_product_thumbnails( $cols ) {
		$product_layout 	 	= get_theme_mod( 'swc_product_layout', 'default' );
		$product_gallery_layout = get_theme_mod( 'swc_product_gallery_layout', 'default' );

		$cols = 4;

		if ( 'full-width' == $product_layout && 'stacked' == $product_gallery_layout ) {
			$cols = 6;
		}

		if ( 'default' == $product_layout && 'stacked' == $product_gallery_layout ) {
			$cols = 3;
		}

		return $cols;
	}

	/**
	 * Shop Layout
	 * Tweaks the WooCommerce layout based on settings
	 */
	public function swc_shop_layout() {
		$shop_layout 	 		= get_theme_mod( 'swc_shop_layout', 'default' );
		$product_layout 	 	= get_theme_mod( 'swc_product_layout', 'default' );
		$header_search 			= get_theme_mod( 'swc_header_search', true );
		$header_cart 			= get_theme_mod( 'swc_header_cart', true );
		$homepage_content 		= get_theme_mod( 'swc_homepage_content', true );
		$homepage_cats 			= get_theme_mod( 'swc_homepage_categories', true );
		$homepage_recent   		= get_theme_mod( 'swc_homepage_recent', true );
		$homepage_featured   	= get_theme_mod( 'swc_homepage_featured', true );
		$homepage_top_rated 	= get_theme_mod( 'swc_homepage_top_rated', true );
		$homepage_on_sale 		= get_theme_mod( 'swc_homepage_on_sale', true );
		$archive_results_count	= get_theme_mod( 'swc_product_archive_results_count', true );
		$archive_sorting		= get_theme_mod( 'swc_product_archive_sorting', true );
		$archive_image 			= get_theme_mod( 'swc_product_archive_image', true );
		$archive_sale_flash 	= get_theme_mod( 'swc_product_archive_sale_flash', true );
		$archive_rating 		= get_theme_mod( 'swc_product_archive_rating', true );
		$archive_price 			= get_theme_mod( 'swc_product_archive_price', true );
		$archive_add_to_cart 	= get_theme_mod( 'swc_product_archive_add_to_cart', true );
		$product_gallery_layout = get_theme_mod( 'swc_product_gallery_layout', 'default' );
		$product_details_tabs 	= get_theme_mod( 'swc_product_details_tab', true );
		$product_related 		= get_theme_mod( 'swc_related_products', true );
		$product_meta 			= get_theme_mod( 'swc_product_meta', true );

		if ( class_exists( 'WooCommerce' ) ) {

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				if ( 'full-width' == $shop_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}
			}

			if ( is_product() ) {
				if ( 'hidden' == $product_gallery_layout ) {
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				}

				if ( 'full-width' == $product_layout ) {
					remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
				}

				if ( false == $product_details_tabs ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
				}

				if ( false == $product_related ) {
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
				}

				if ( false == $product_meta ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				}
			}

		}

		if ( false == $header_search ) {
			remove_action( 'storefront_header', 'storefront_product_search', 	40 );
		}

		if ( false == $header_cart ) {
			remove_action( 'storefront_header', 'storefront_header_cart', 		60 );
		}

		if ( false == $homepage_content && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_homepage_content', 10 );
		}

		if ( false == $homepage_cats && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_product_categories', 20 );
		}

		if ( false == $homepage_recent && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_recent_products', 30 );
		}

		if ( false == $homepage_featured && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_featured_products', 40 );
		}

		if ( false == $homepage_top_rated && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_popular_products', 50 );
		}

		if ( false == $homepage_on_sale && ! $this->is_homepage_control_activated() ) {
			remove_action( 'homepage', 'storefront_on_sale_products', 60 );
		}

		if ( false == $archive_results_count ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		}

		if ( false == $archive_sorting ) {
			remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		}

		if ( false == $archive_image ) {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		}

		if ( false == $archive_sale_flash ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 6 );
		}

		if ( false == $archive_rating ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}

		if ( false == $archive_price ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		}

		if ( false == $archive_add_to_cart ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}
	}

	/**
	 * Shop products per page
	 * @return integer shop products per page
	 */
	public function swc_shop_products_per_page( $per_page ) {
		$per_page = get_theme_mod( 'swc_products_per_page', '12' );
		return $per_page;
	}

	/**
	 * Product loop wrap
	 * @return void
	 */
	public function swc_product_loop_wrap() {
		$columns = get_theme_mod( 'swc_product_columns', '3' );

		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '<div class="columns-' . $columns . '">';
		}
	}

	/**
	 * Product loop wrap
	 * @return void
	 */
	public function swc_product_loop_wrap_close() {
		if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
			echo '</div>';
		}
	}

	public function is_homepage_control_activated() {
		if ( class_exists( 'Homepage_Control' ) ) { return true; } else { return false; }
	}

	/* ---------------------------------- */
	/* Composite Products Integration
	/* -----------------------------------*/

	/**
	 * Number of component option columns when the Product Thumbnails setting is active
	 * @param  integer $cols
	 * @return integer
	 */
	function swc_cp_component_options_loop_columns( $cols ) {
		$cols = get_theme_mod( 'swc_cp_component_options_loop_columns', '3' );
		return $cols;
	}

	/**
	 * Number of component options per page when the Product Thumbnails setting is active
	 * @param  integer $num
	 * @return integer
	 */
	function swc_cp_component_options_per_page( $num_per_page ) {
		$num_per_page = get_theme_mod( 'swc_cp_component_options_per_page', '6' );
		return $num_per_page;
	}

	/**
	 * Max number of Review/Summary columns when a Multi-page layout is active
	 * @param  integer $num
	 * @return integer
	 */
	function swc_cp_summary_max_columns( $max_cols ) {
		$max_cols = get_theme_mod( 'swc_cp_summary_max_columns', '6' );
		return $max_cols;
	}

	/**
	 * Enable/disable the toggle-box component view when a Single-page layout is active
	 * @param  boolean              $show_toggle
	 * @param  string               $component_id
	 * @param  WC_Product_Composite $product
	 * @return boolean
	 */
	function swc_cp_component_toggled( $show_toggle, $component_id, $product ) {
		$show_toggle = get_theme_mod( 'swc_cp_component_toggled', 'progressive' );

		$style = $product->get_composite_layout_style();

		if ( $style === $show_toggle || $show_toggle === 'both' ) {
			return true;
		}

		return false;
	}

	/**
	 * Customizer Composite Products settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function swc_cp_customize_register( $wp_customize ) {

		/**
	     * Composite Products section
	     */
        $wp_customize->add_section( 'swc_cp_section' , array(
			'title'       => __( 'Composite Products', 'storefront-woocommerce-customiser' ),
			'description' => __( 'Customise the look & feel of Composite product pages', 'storefront-woocommerce-customiser' ),
			'priority'    => 59,
		) );

        /**
         * Component Options (Product) Columns
         */
	    $wp_customize->add_setting( 'swc_cp_component_options_loop_columns', array(
	        'default'           => '3',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_options_loop_columns', array(
			'label'       => __( 'Component options columns', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when the %sProduct Thumbnails%s options style is active', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_options_loop_columns',
			'type'        => 'select',
			'priority'    => 1,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
			),
        ) ) );

        /**
         * Component Options per Page
         */
	    $wp_customize->add_setting( 'swc_cp_component_options_per_page', array(
	        'default'           => '6',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_options_per_page', array(
			'label'       => __( 'Component options per page', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when the %sProduct Thumbnails%s options style is active', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_options_per_page',
			'type'        => 'select',
			'priority'    => 2,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
				'6'           => '6',
				'7'           => '7',
				'8'           => '8',
				'9'           => '9',
				'10'          => '10',
				'11'          => '11',
				'12'          => '12',
				'13'          => '13',
				'14'          => '14',
				'15'          => '15',
				'16'          => '16',
				'17'          => '17',
				'18'          => '18',
				'19'          => '19',
				'20'          => '20',
				'21'          => '21',
				'22'          => '22',
				'23'          => '23',
				'24'          => '24',
			),
        ) ) );

        /**
         * Max columns in Summary/Review section
         */
	    $wp_customize->add_setting( 'swc_cp_summary_max_columns', array(
	        'default'           => '6',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_summary_max_columns', array(
			'label'       => __( 'Max columns in Summary', 'storefront-woocommerce-customiser' ),
			'description' => sprintf( __( 'In effect when using a %sMulti-page%s layout', 'storefront-woocommerce-customiser' ), '<strong>', '</strong>' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_summary_max_columns',
			'type'        => 'select',
			'priority'    => 3,
			'choices'     => array(
				'1'           => '1',
				'2'           => '2',
				'3'           => '3',
				'4'           => '4',
				'5'           => '5',
				'6'           => '6',
				'7'           => '7',
				'8'           => '8',
			),
        ) ) );

        /**
         * Toggle Box
         */
	    $wp_customize->add_setting( 'swc_cp_component_toggled', array(
	        'default'           => 'progressive',
	    ) );

	    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'swc_cp_component_toggled', array(
			'label'       => __( 'Toggle-box view', 'storefront-woocommerce-customiser' ),
			'description' => __( 'Apply the "toggle-box" Component view to the following layout(s)', 'storefront-woocommerce-customiser' ),
			'section'     => 'swc_cp_section',
			'settings'    => 'swc_cp_component_toggled',
			'type'        => 'select',
			'priority'    => 5,
			'choices'     => array(
				'single'      => 'Single-page',
				'progressive' => 'Single-page progressive',
				'both'        => 'Both',
				'none'        => 'None',
			),
        ) ) );

	}

} // End Class
