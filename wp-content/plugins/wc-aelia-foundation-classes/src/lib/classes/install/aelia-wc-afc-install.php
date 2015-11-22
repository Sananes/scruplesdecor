<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

use \Aelia\WC\Messages;

/**
 * Aelia Foundation Classes for WooCommerce.
 **/
class WC_AeliaFoundationClasses_Install extends Aelia_Install {
	// @var string The name of the lock that will be used by the installer to prevent race conditions.
	protected $lock_name = 'WC_AELIA_AFC';

	/**
	 * Runs plugin updates required by version 1.6.1.150728:
	 * - Automatic update of GeoIP database.
	 *
	 * @since 1.6.1.150728
	 */
	protected function update_to_1_6_1_150728() {
		IP2Location::install_database();
		return true;
	}
}
