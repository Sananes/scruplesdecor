<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly
/*
Plugin Name: Aelia Foundation Classes for WooCommerce
Description: This plugin implements common classes for other WooCommerce plugins developed by Aelia.
Author: Aelia
Author URI: http://aelia.co
Version: 1.6.6.150825
Text Domain: wc-aelia-foundation-classes
Domain Path: /languages
*/

require_once(dirname(__FILE__) . '/src/lib/classes/install/aelia-wc-afc-requirementscheck.php');

// If requirements are not met, deactivate the plugin
if(Aelia_WC_AFC_RequirementsChecks::factory()->check_requirements()) {
	require_once dirname(__FILE__) . '/src/plugin-main.php';

	// Check for plugin updates (only when in Admin pages)
	function wc_aelia_afc_check_for_updates() {
		$GLOBALS['wc-aelia-foundation-classes']->check_for_updates(__FILE__);
	}
	add_action('admin_init', 'wc_aelia_afc_check_for_updates');

	register_activation_hook(__FILE__, array($GLOBALS['wc-aelia-foundation-classes'], 'setup'));
	register_uninstall_hook(__FILE__, array('WC_AeliaFoundationClasses', 'uninstall'));
}
