<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * A very basic database model class.
 */
class Model extends Base_Class {
	/**
	 * A reference to the global wpdb object.
	 */
	protected $wpdb;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Suppresses all error message. This method is mainly used as a workaround
	 * to prevent warning from being raised when START TRANSACTION, COMMIT and
	 * ROLLBACK queries are executed.
	 */
	protected function suppress_errors() {
		set_error_handler(function() { /* ignore errors */ });
	}

	/**
	 * Restores the error handler, which will display errors again.
	 */
	protected function enable_errors() {
		restore_error_handler();
	}

	/**
	 * Starts a database transaction.
	 *
	 * @return bool
	 */
	protected function start_transaction() {
		// Suppressing errors is necessary because the WPDB class doesn't expect
		// a transaction command and tries to fetch a result set after running the
		// query, triggering a warning
		$this->suppress_errors();
		$result = $this->wpdb->query('START TRANSACTION');
		$this->enable_errors();
		return $result;
	}

	/**
	 * Rolls back a database transaction.
	 *
	 * @return bool
	 */
	protected function rollback_transaction() {
		// Suppressing errors is necessary because the WPDB class doesn't expect
		// a transaction command and tries to fetch a result set after running the
		// query, triggering a warning
		$this->suppress_errors();
		$result = $this->wpdb->query('ROLLBACK');
		$this->enable_errors();
		return $result;
	}

	/**
	 * Commits a database transaction.
	 *
	 * @return bool
	 */
	protected function commit_transaction() {
		// Suppressing errors is necessary because the WPDB class doesn't expect
		// a transaction command and tries to fetch a result set after running the
		// query, triggering a warning
		$this->suppress_errors();
		$result = $this->wpdb->query('COMMIT');
		$this->enable_errors();
		return $result;
	}
}
