<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!class_exists('Aelia\WC\Aelia_Install')) {
	/**
	 * Helper class to handle installation and update of a plugin.
	 */
	class Aelia_Install extends Base_Class {
		// @var string Prefix used to retrieve the methods to run sequentially to perform the updates.
		const UPDATE_METHOD_PREFIX = 'update_to_';

		// @var string The name of the lock that will be used by the installer to prevent race conditions.
		protected $lock_name = 'WC_AFC';

		// @var array An array containing error messages returned by the class.
		// TODO Use Aelia\WC\Messages class to handle messages
		protected $messages = array();

		// @var array CSS Classes used to display the messages generated by the plugin
		// TODO Use Aelia\WC\Messages class to handle messages
		protected $message_css_classes = array(
			E_USER_ERROR => 'error',
			E_USER_WARNING => 'updated', // "updated" is the WordPress style that shows update messages and warnings
			E_USER_NOTICE => 'updated',
			E_ERROR => 'error',
			E_WARNING => 'updated', // "updated" is the WordPress style that shows update messages and warnings
			E_NOTICE => 'updated',
		);

		// @var array Message headers display on top of message lists
		protected $message_headers = array(
			// Message headers are populated inside the constructor
		);

		/**
		 * Class constructor.
		 */
		public function __construct() {
			parent::__construct();

			global $wpdb;

			$this->wpdb = $wpdb;

			$this->load_message_headers();
			$this->set_hooks();
		}

		/**
		 * Determines if WordPress maintenance mode is active.
		 *
		 * @return bool
		 */
		protected function maintenance_mode() {
			return file_exists(ABSPATH . '.maintenance') || defined('WP_INSTALLING');
		}

		/**
		 * Initialises the message headers.
		 */
		// TODO Use Aelia\WC\Messages class to handle messages
		protected function load_message_headers() {
			$this->message_headers = array(
				E_USER_ERROR => __('Error'),
				E_USER_WARNING => __('Warning'),
				E_USER_NOTICE => __('Notice'),
				E_ERROR => __('Error'),
				E_WARNING => __('Warning'),
				E_NOTICE => __('Notice'),
			);
		}

		/**
		 * Sets the hooks required by the class.
		 */
		protected function set_hooks() {
			add_action('admin_notices', array($this, 'display_messages'));
		}

		/**
		 * Returns the header to be displayed for a specific message level.
		 *
		 * @param int level The message level.
		 * @return string
		 */
		// TODO Move display of messages to a View
		protected function get_message_header($level) {
			return get_value($level, $this->message_headers, '');
		}

		/**
		 * Displays all stored messages.
		 */
		// TODO Use Aelia\WC\Messages class to handle messages
		// TODO Move display of messages to a View
		public function display_messages() {
			if(empty($this->messages)) {
				return;
			}

			$last_message_level = null;
			foreach($this->messages as $message) {
				$css_class = get_value($message->level, $this->message_css_classes, '');

				if($message->level != $last_message_level) {
					if(!empty($last_message_level)) {
						echo '</div>';
					}
					echo '<div class="' . $css_class . '">';

					$message_header = $this->get_message_header($message->level);
					if(!empty($message_header)) {
						echo '<h4 class="wc_aelia message_header">';
						echo $message_header;
						echo '</h4>';
					}
				}

				$output_msg = empty($message->code) ? '' : $message->code . ' ';
				$output_msg .= $message->message;
				echo '<p class="wc_aelia message">';
				echo $output_msg;
				echo '</p>';

				$last_message_level = $message->level;
			}
			echo '</div>';
		}

		/**
		 * Adds a message to the list.
		 *
		 * @param int level The message level.
		 * @param string message The message.
		 * @param string code The message code.
		 */
		protected function add_message($level, $message, $code = '') {
			$this->messages[] = new Message($level, $message, $code = '');
		}

		/**
		 * Deletes all stored messages.
		 */
		// TODO Use Aelia\WC\Messages class to handle messages
		protected function clear_messages() {
			$this->messages = array();
		}

		/**
		 * Compares the version extracted from two update methods to sort them.
		 *
		 * @param string a The version of first method.
		 * @param string b The version of second method.
		 * @return int
		 *
		 * @see version_compare().
		 * @see uksort().
		 */
		protected function sort_update_methods($a, $b) {
			return version_compare($a, $b);
		}

		/**
		 * Returns a list of the methods that will perform the updates.
		 *
		 * @param string current_version Current version of the plugin. This will
		 * determine which update methods still have to be executed.
		 * @return array
		 */
		protected function get_update_methods($current_version) {
			if(empty($current_version)) {
				$current_version = '0';
			}
			$update_methods = array();

			$class_methods = get_class_methods($this);
			foreach($class_methods as $method) {
				if(stripos($method, self::UPDATE_METHOD_PREFIX) === 0) {
					$method_version = str_ireplace(self::UPDATE_METHOD_PREFIX, '', $method);
					if(version_compare($method_version, $current_version, '>')) {
						$update_methods[$method_version] = $method;
					}
				}
			}
			uksort($update_methods, array($this, 'sort_update_methods'));
			return $update_methods;
		}

		/**
		 * Given a plugin version, like "1.23.456 Beta", returns said version stripping
		 * all non alphanumeric characters.
		 *
		 * @param string version The version to process.
		 * @return string
		 */
		protected function get_alphanum_version($version) {
			$version = str_replace(' ', '_', $version);
			// Remove dots, dashes, spaces and so on, to get a plain alphanumeric version
			// That is, version 1.2.3.45 Alpha becomes 12345_alpha
			$version = strtolower(preg_replace("~[^A-Za-z0-9]~", "", $version));
			return $version;
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
			$result = $this->exec('START TRANSACTION');
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
			$result = $this->exec('ROLLBACK');
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
			$result = $this->exec('COMMIT');
			$this->enable_errors();
			return $result;
		}

		/**
		 * Executes a non-query SQL statement (i.e. INSERT, UPDATE, DELETE).
		 *
		 * @param string sql The statement to execute.
		 * @return int
		 * @see wpdb::query()
		 */
		protected function exec($sql) {
			return $this->wpdb->query($sql);
		}

		/**
		 * Checks if a column exists in a table.
		 *
		 * @param string table The table name.
		 * @param string column The column name.
		 * @since 1.4.10.150209
		 */
		protected function column_exists($table, $column) {
			global $wpdb;
			$SQL = "
				SELECT COUNT(*)
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					(TABLE_NAME = %s) AND
					(COLUMN_NAME = %s);
			";

			return $wpdb->get_var($wpdb->prepare(
				$SQL,
				$table,
				$column
			));
		}

		/**
		 * Adds a column to a table.
		 *
		 * @param string table The table name.
		 * @param string column The column name.
		 * @param string column_type The column type.
		 * @param string collate Collation settings. If left empty, the default
		 * settings will be taken from global $wpdb object. Used only for text
		 * columns.
		 * @options string Additional options for column creation (e.g. UNSIGNED,
		 * NOT NULL, etc).
		 * @since 1.4.10.150209
		 */
		protected function add_column($table, $column, $column_type, $collate = '', $options = '') {
			global $wpdb;

			if(in_array(strtoupper($column_type), array('CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'))) {
				if(empty($collate)) {
					if($wpdb->has_cap('collation')) {
						if(!empty($wpdb->charset)) {
							$collate .= "CHARACTER SET $wpdb->charset";
						}
						if(!empty($wpdb->collate)) {
							$collate .= " COLLATE $wpdb->collate";
						}
					}
				}
				$column_type .= ' ' . $collate;
			}

			$SQL = "
				ALTER TABLE {$table}
				ADD COLUMN {$column} {$column_type} {$options}
			";
			return $wpdb->query($SQL);
		}

		/**
		 * Executes a query SQL statement (i.e. SELECT).
		 *
		 * @param string sql The statement to execute.
		 * @return array
		 * @see wpdb::query()
		 */
		protected function select($sql, $output_type = OBJECT) {
			return $this->wpdb->get_results($sql, $output_type);
		}

		/**
		 * Extracts the version number from the name of an update method. The version
		 * number has all the underscores replaced with periods.
		 *
		 * @param string method_name The name of an update method.
		 * @return string The version extracted from the method name, with underscores
		 * replaced with periods.
		 * @since 1.5.6.150402
		 */
		protected function extract_version_from_method($method_name) {
			$version = str_ireplace(self::UPDATE_METHOD_PREFIX, '', $method_name);
			return str_ireplace('_', '.', $version);
		}

		/**
		 * Runs all the update methods required to update the plugin to the latest
		 * version.
		 *
		 * @param string plugin_id The ID of the plugin.
		 * @param string new_version The new version of the plugin, which will be
		 * stored after a successful update to keep track of the status.
		 * @return bool
		 */
		public function update($plugin_id, $new_version) {
			// Don't run updates while maintenance mode is active
			if($this->maintenance_mode()) {
				return true;
			}

			$current_version = get_option($plugin_id);
			if(version_compare($current_version, $new_version, '>=')) {
				return true;
			}

			// Initialize the semaphore that will be used to prevent race conditions
			$this->semaphore = new Semaphore($this->lock_name);
			$this->semaphore->initialize();
			if(!$this->semaphore->lock()) {
				$this->log(sprintf(__('%s Plugin Autoupdate - Could not obtain semaphore lock. ' .
															'This may mean that the process has already started, or ' .
															'that the lock is stuck. Update process will run again ' .
															'later.', WC_AeliaFoundationClasses::$text_domain),
													 get_called_class()));
				// Return true as the process already running is considered ok
				return true;
			}

			$this->clear_messages();
			$result = true;
			$update_methods = $this->get_update_methods($current_version);

			$this->add_message(E_USER_NOTICE,
												 sprintf(__('Running updates for plugin <span class="wc_aeliaplugin_id">%s</span>...',
																		WC_AeliaFoundationClasses::$text_domain),
																 $plugin_id));
			if(!empty($update_methods)) {
				// Force display of database errors. If plugin doesn't update correctly, we
				// definitely want to know why
				$this->wpdb->show_errors();
				foreach($update_methods as $version => $method) {
					if(!is_callable(array($this, $method))) {
						$this->add_message(E_USER_WARNING,
															 sprintf(__('Update method "%s::%s()" is not a "callable" and was ' .
																					'skipped. Please report this issue to Support',
																					WC_AeliaFoundationClasses::$text_domain),
																			 get_class($this),
																			 $method));
						continue;
					}
					try {
						$this->add_message(E_USER_NOTICE,
															 sprintf(__('Running update method %s::%s()...',
																					WC_AeliaFoundationClasses::$text_domain),
																			 get_class($this),
																			 $method));
						$result = $this->$method();
						if($result === false) {
							break;
						}
						else {
							// Keep track of the last successful update
							$version = $this->extract_version_from_method($method);
							update_option($plugin_id, $version);
						}
					}
					catch(Exception $e) {
						$this->add_message(E_USER_WARNING,
															 sprintf(__('Update method "%s::%s() raised exception "%s". Update halted. ' .
																					'Please contact Support and provide the error details ' .
																					'that you will find below.',
																					WC_AeliaFoundationClasses::$text_domain),
																			 $e->getMessage(),
																			 get_class($this),
																			 $method));
						$result = false;
					}
				}

				// Unless WP_DEBUG is defined, hide database errors (bad idea, but that's
				// standard WordPress behaviour)
				if(!defined('WP_DEBUG') || (WP_DEBUG == false)) {
					$this->wpdb->hide_errors();
				}
			}

			if($result === true) {
				update_option($plugin_id, $new_version);
				$this->add_message(E_USER_NOTICE,
													 __('<span class="wc_aeliaimportant">Update completed successfully</span>.',
															WC_AeliaFoundationClasses::$text_domain));
			}
			else {
				$this->add_message(E_USER_ERROR, __('<span class="wc_aeliaimportant">Update halted</span>. Please review displayed messages and ' .
																						'correct any issue that was reported.',
																						WC_AeliaFoundationClasses::$text_domain));
			}

			// Unlock the semaphore, to allow update to run again later
			$this->semaphore->unlock();

			return $result;
		}
	}
}
