<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

use Aelia\WC\Logger as Logger;

class Base_Class {
	// @var Aelia\WC\Logger The logger used by the class.
	protected $logger;

	// @var string The class name to use as a prefix for log messages.
	protected $class_for_log = '';

	/**
	 * Returns the class name to use as a prefix for log messages.
	 *
	 * @return string
	 * @since 1.6.1.150728
	 */
	protected function get_class_for_log() {
		if(empty($this->class_for_log)) {
			$reflection = new \ReflectionClass($this);
			$this->class_for_log = $reflection->getShortName();
		}
		return $this->class_for_log;
	}

	/**
	 * Logs a message.
	 *
	 * @param string message The message to log.
	 * @param bool debug Indicates if the message is for debugging. Debug messages
	 * are not saved if the "debug mode" flag is turned off.
	 */
	protected function log($message, $debug = true) {
		// Prefix message with the class name, for easier identification
		$message = sprintf('[%s] %s',
											 $this->get_class_for_log(),
											 $message);
		$this->logger->log($message, $debug);
	}

	/**
	 * Returns global instance of WooCommerce.
	 *
	 * @return object The global instance of WC.
	 */
	protected function wc() {
		global $woocommerce;
		return $woocommerce;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->logger = new Logger(get_class());
	}
}
