<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Implements a base class to store definitions for the plugin.
 */
class Definitions {
	// @var string The menu slug for plugin's settings page.
	const MENU_SLUG = 'aelia_foundation_classes';
	// @var string The plugin slug
	const PLUGIN_SLUG = 'wc-aelia-foundation-classes';
	// @var string The plugin text domain
	const TEXT_DOMAIN = 'wc-aelia-foundation-classes';

	// Get/Post Arguments
	const ARG_INSTALL_GEOIP_DB = 'aelia_install_geoip_db';

	// Error codes
	const OK = 0;
	const ERR_COULD_NOT_UPDATE_GEOIP_DATABASE = 1100;

	// Session/User Keys

	// Transients
}
