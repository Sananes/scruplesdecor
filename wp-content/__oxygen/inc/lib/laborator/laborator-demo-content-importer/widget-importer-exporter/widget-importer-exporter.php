<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main class
 *
 * @since 0.1
 */
class Widget_Importer_Exporter {

	/**
	 * Plugin data from get_plugins()
	 *
	 * @since 0.1
	 * @var object
	 */
	public $plugin_data;

	/**
	 * Includes to load
	 *
	 * @since 0.1
	 * @var array
	 */
	public $includes;

	/**
	 * Constructor
	 *
	 * Add actions for methods that define constants, load translation and load includes.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function __construct() {

		$this->set_plugin_data();
		$this->define_constants();
		$this->set_includes();
		$this->load_includes();

	}

	/**
	 * Set plugin data
	 *
	 * This data is used by constants.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function set_plugin_data() {

		// Load plugin.php if get_plugins() not available
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get path to plugin's directory
		$plugin_dir = plugin_basename( dirname( __FILE__ ) );

		// Get plugin data
		$plugin_data = current( get_plugins( '/' . $plugin_dir ) );

		// Set plugin data
		$this->plugin_data = apply_filters( 'wie_plugin_data', $plugin_data );

	}

	/**
	 * Define constants
	 *
	 * @since 0.1
	 * @access public
	 */
	public function define_constants() {

		// Plugin details
		define( 'WIE_VERSION', 		$this->plugin_data['Version'] );					// plugin version
		define( 'WIE_FILE', 		__FILE__ );											// plugin's main file path
		define( 'WIE_DIR', 			dirname( plugin_basename( WIE_FILE ) ) );			// plugin's directory
		define( 'WIE_PATH',			untrailingslashit( plugin_dir_path( WIE_FILE ) ) );	// plugin's directory path
		define( 'WIE_URL', 			untrailingslashit( plugin_dir_url( WIE_FILE ) ) );	// plugin's directory URL

		// Directories
		define( 'WIE_INC_DIR',		'includes' );	// includes directory
		define( 'WIE_CSS_DIR', 		'css' );		// stylesheets directory
		define( 'WIE_LANG_DIR', 	'languages' );	// languages directory

	}

	/**
	 * Load language file
	 *
	 * This will load the MO file for the current locale.
	 * The translation file must be named widget-importer-exporter-$locale.mo.
	 *
	 * First it will check to see if the MO file exists in wp-content/languages/plugins.
	 * If not, then the 'languages' direcory inside the plugin will be used.
	 * It is ideal to keep translation files outside of the plugin to avoid loss during updates.\
	 *
	 * @since 0.1
	 * @access public
	 */
	public function load_textdomain() {}

	/**
	 * Set includes
	 *
	 * @since 0.1
	 * @access public
	 */
	public function set_includes() {

		$this->includes = apply_filters( 'wie_includes', array(

			// Admin only
			'admin' => array(

				// Functions
				WIE_INC_DIR . '/export.php',
				WIE_INC_DIR . '/import.php',
				WIE_INC_DIR . '/mime-types.php',
				WIE_INC_DIR . '/page.php',
				WIE_INC_DIR . '/widgets.php'

			)

		) );
	}

	/**
	 * Load includes
	 *
 	 * Include files based on whether or not condition is met.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function load_includes() {

		// Get includes
		$includes = $this->includes;

		// Loop conditions
		foreach ( $includes as $condition => $files ) {

			$do_includes = false;

			// Check condition
			switch( $condition ) {

				// Admin Only
				case 'admin':

					if ( is_admin() ) {
						$do_includes = true;
					}

					break;

				// Frontend Only
				case 'frontend':

					if ( ! is_admin() ) {
						$do_includes = true;
					}

					break;

				// Admin or Frontend (always)
				default:

					$do_includes = true;

					break;

			}

			// Loop files if condition met
			if ( $do_includes ) {

				foreach ( $files as $file ) {
					require_once trailingslashit( WIE_PATH ) . $file;
				}

			}

		}

	}

}

// Instantiate the main class
new Widget_Importer_Exporter();
