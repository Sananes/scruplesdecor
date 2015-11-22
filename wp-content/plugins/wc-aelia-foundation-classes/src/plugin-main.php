<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

//define('SCRIPT_DEBUG', 1);
//error_reporting(E_ALL);

require_once('lib/classes/base/plugin/aelia-plugin.php');
require_once('lib/classes/definitions/definitions.php');

/**
 * Aelia Foundation Classes for WooCommerce.
 **/
class WC_AeliaFoundationClasses extends Aelia_Plugin {
	public static $version = '1.6.6.150825';

	public static $plugin_slug = Definitions::PLUGIN_SLUG;
	public static $text_domain = Definitions::TEXT_DOMAIN;
	public static $plugin_name = 'Aelia Foundation Classes for WooCommerce';

	public static function factory() {
		// Load Composer autoloader
		require_once(__DIR__ . '/vendor/autoload.php');

		$settings_key = self::$plugin_slug;

		$settings_controller = null;
		$messages_controller = null;

		$plugin_instance = new self($settings_controller, $messages_controller);
		return $plugin_instance;
	}

	/**
	 * Constructor.
	 *
	 * @param Aelia\WC\Settings settings_controller The controller that will handle
	 * the plugin settings.
	 * @param Aelia\WC\Messages messages_controller The controller that will handle
	 * the messages produced by the plugin.
	 */
	public function __construct($settings_controller,
															$messages_controller) {
		// Load Composer autoloader
		require_once(__DIR__ . '/vendor/autoload.php');
		require_once('lib/wc-core-aux-functions.php');

		parent::__construct($settings_controller, $messages_controller);
	}

	/**
	 * Sets the hooks required by the plugin.
	 *
	 * @since 1.6.0.150724
	 */
	protected function set_hooks() {
		parent::set_hooks();
		add_filter('cron_schedules', array($this, 'cron_schedules'));
		add_action('aelia_afc_geoip_updater', array('\Aelia\WC\IP2Location', 'update_database'));
	}

	/**
	 * Adds more scheduling options to WordPress Cron.
	 *
	 * @param array schedules Existing Cron scheduling options.
	 * @return array The schedules, with "weekly" and "monthly" added to the list.
	 * @since 1.6.0.150724
	 */
	public function cron_schedules($schedules) {
		if(empty($schedules['weekly'])) {
			// Adds "weekly" to the existing schedules
			$schedules['weekly'] = array(
				'interval' => 604800,
				'display' => __('Weekly', self::$text_domain),
			);
		}

		if(empty($schedules['monthly'])) {
			// Adds "monthly" to the existing schedules
			$schedules['monthly'] = array(
				'interval' => 2592000,
				'display' => __('Monthly (every 30 days)', self::$text_domain),
			);
		}
		return $schedules;
	}

	/**
	 * Registers the script and style files required in the backend (even outside
	 * of plugin's pages).
	 *
	 * @since 1.6.1.150728
	 */
	protected function register_common_admin_scripts() {
		//// Scripts
		//wp_register_script('wc-aelia-currency-switcher-admin-overrides',
		//									 $this->url('plugin') . '/js/admin/wc-aelia-currency-switcher-overrides.js',
		//									 array(),
		//									 self::$version,
		//									 true);

		// Styles
		wp_register_style(self::$plugin_slug . '-admin',
											$this->url('plugin') . '/design/css/admin.css',
											array(),
											self::$version,
											'all');
		// Styles - Enqueue styles required for plugin Admin page
		wp_enqueue_style(static::$plugin_slug . '-admin');
	}

	/**
	 * Sets the Cron schedules required by the plugin.
	 *
	 * @since 1.6.0.150724
	 */
	protected function set_cron_schedules() {
		//wp_schedule_event(strtotime('first tuesday of next month'), 'monthly', 'woocommerce_geoip_updater' );
		if(!wp_get_schedule('aelia_afc_geoip_updater')) {
			wp_schedule_event(time(), 'weekly', 'aelia_afc_geoip_updater');
		}
	}

	/**
	 * Setup function. Called when plugin is enabled.
	 *
	 * @since 1.6.0.150724
	 */
	public function setup() {
		// Keep track of the fact that we are in the setup phase
		$this->running_setup = true;
		IP2Location::install_database();
	}

	/**
	 * Performs cleanup operations when the plugin is uninstalled.
	 *
	 * @since 1.6.0.150724
	 */
	public function uninstall() {
		wp_clear_scheduled_hook('aelia_afc_geoip_updater');
	}

	/**
	 * Performs operations required on WooCommerce load.
	 */
	public function woocommerce_loaded() {
		if(!$this->running_setup && current_user_can('manage_woocommerce')) {
			// Check if the forced installation of the GeoIP database was requested
			if(!empty($_REQUEST[Definitions::ARG_INSTALL_GEOIP_DB])) {
				$this->running_setup = true;
				IP2Location::install_database();
			}

			if(is_admin()) {
				// Ensure that the GeoIP database exists, and inform the Administrator if
				// it doesn't
				if(!$this->running_setup && !file_exists(IP2Location::geoip_db_file())) {
					Messages::admin_message(
						Definitions::PLUGIN_SLUG,
						E_USER_ERROR,
						__('GeoIP database file not found.', self::$text_domain) .
						'&nbsp;' .
						sprintf(__('Please %s.', self::$text_domain),
										IP2Location::get_geoip_install_html(__('try to install the database again',
																										 self::$text_domain))) .
						'&nbsp;' .
						sprintf(__('If the error persists, please download the the database ' .
											 'manually, from <a href="%1$s">%1$s</a>. Extract file ' .
											 '<strong>%2$s</strong> from the archive and copy it to ' .
											 '<code>%3$s</code>.',
											 self::$text_domain),
										IP2Location::GEOLITE_DB,
										IP2Location::$geoip_db_file,
										dirname(IP2Location::geoip_db_file())) .
						'&nbsp;' .
						__('Geolocation will become available automatically, as soon as the ' .
							 'GeoIP database is copied in the indicated folder.',
							 self::$text_domain) .
						'&nbsp;<br /><br />' .
						sprintf(__('For more information about this message, <a href="%s">please refer to our ' .
											 'knowledge base</a>.',
											 self::$text_domain),
									 'http://bit.ly/AFC_Geolocation'),
						Definitions::ERR_COULD_NOT_UPDATE_GEOIP_DATABASE
					);
				}
			}
		}
	}
}
class_alias('\Aelia\WC\WC_AeliaFoundationClasses', 'WC_AeliaFoundationClasses');

// Instantiate plugin and add it to the set of globals
$GLOBALS[WC_AeliaFoundationClasses::$plugin_slug] = WC_AeliaFoundationClasses::factory();
