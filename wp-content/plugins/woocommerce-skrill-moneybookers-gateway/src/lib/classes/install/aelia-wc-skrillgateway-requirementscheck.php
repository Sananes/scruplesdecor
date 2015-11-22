<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('aelia-wc-requirementscheck.php');

/**
 * Checks that plugin's requirements are met.
 */
class Aelia_WC_Skrill_Gateway_RequirementsChecks extends Aelia_WC_RequirementsChecks {
	// @var string The namespace for the messages displayed by the class.
	protected $text_domain = 'wc-aelia-skrill-gateway';
	// @var string The plugin for which the requirements are being checked. Change it in descendant classes.
	protected $plugin_name = 'WooCommerce Skrill Gateway';

	// @var array An array of WordPress plugins (name => version) required by the plugin.
	protected $required_plugins = array(
		'WooCommerce' => '2.0.10',
		'Aelia Foundation Classes for WooCommerce' => array(
			'version' => '1.0.10.140819',
			'extra_info' => 'You can get the plugin <a href="http://aelia.co/downloads/wc-aelia-foundation-classes.zip">from our site</a>, free of charge.',
			'autoload' => true,
			'url' => 'http://aelia.co/downloads/wc-aelia-foundation-classes.zip',
		),
	);

	/**
	 * Factory method. It MUST be copied to every descendant class, as it has to
	 * be compatible with PHP 5.2 and earlier, so that the class can be instantiated
	 * in any case and and gracefully tell the user if PHP version is insufficient.
	 *
	 * @return Aelia_WC_AFC_RequirementsChecks
	 */
	public static function factory() {
		$instance = new self();
		return $instance;
	}
}
