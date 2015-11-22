<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Implements a base class to store and handle the messages returned by the
 * plugin. This class is used to extend the basic functionalities provided by
 * standard WP_Error class.
 */
class Messages {
	const DEFAULT_TEXTDOMAIN = 'wc-aelia';

	protected static $message_css_classes = array(
		E_USER_ERROR => 'error',
		E_USER_WARNING => 'updated', // "updated" is the WordPress style that shows update messages and warnings
		E_USER_NOTICE => 'updated',
		E_ERROR => 'error',
		E_WARNING => 'updated', // "updated" is the WordPress style that shows update messages and warnings
		E_NOTICE => 'updated',
	);

	// @var array Message headers display on top of message lists
	protected static $message_headers = array(
		// Message headers are populated inside the constructor
	);

	// Result constants
	const RES_OK = 0;
	const ERR_FILE_NOT_FOUND = 100;
	const ERR_NOT_IMPLEMENTED = 101;
	const ERR_INVALID_TEMPLATE = 102;
	const ERR_INVALID_WIDGET_CLASS = 103;

	// @var WP_Error Holds the error messages registered by the plugin
	protected $wp_error;

	// @var string The text domain used by the class
	protected $text_domain = self::DEFAULT_TEXTDOMAIN;

	// @var string A list of admin messages to display
	protected static $admin_messages = array();

	public function __construct($text_domain = self::DEFAULT_TEXTDOMAIN) {
		$this->text_domain = $text_domain;
		$this->wp_error = new \WP_Error();
		$this->load_error_messages();
	}

	/**
	 * Loads all the messages used by the plugin. This class should be
	 * extended during implementation, to add all error messages used by
	 * the plugin.
	 */
	public function load_messages() {
		$this->add_message(self::ERR_FILE_NOT_FOUND, __('File not found: "%s".', $this->text_domain));
		$this->add_message(self::ERR_NOT_IMPLEMENTED, __('Not implemented.', $this->text_domain));
		$this->add_error_message(self::ERR_INVALID_TEMPLATE,
														 __('Rendering - Requested template could not be found in either plugin\'s ' .
																'folders, nor in your theme. Plugin slug: "%s". Template name: "%s".',
																$this->text_domain));
		$this->add_error_message(self::ERR_INVALID_WIDGET_CLASS,
														 __('Invalid widget class: "%s".',
																$this->text_domain));

		// TODO Add here all the error messages used by the plugin
	}

	/**
	 * Registers an error message in the internal wp_error object.
	 *
	 * @param mixed error_code The Error Code.
	 * @param string error_message The Error Message.
	 */
	public function add_message($error_code, $error_message) {
		$this->wp_error->add($error_code, $error_message);
	}

	/**
	 * Retrieves an error message from the internal wp_error object.
	 *
	 * @param mixed error_code The Error Code.
	 * @return string The Error Message corresponding to the specified Code.
	 */
	public function get_message($error_code) {
		return $this->wp_error->get_error_message($error_code);
	}

	/**
	 * Calls Aelia\WC\Messages::load_messages(). Implemented for backward
	 * compatibility.
	 */
	public function load_error_messages() {
		$this->load_messages();
	}

	/**
	 * Calls Aelia\WC\Messages::add_message(). Implemented for backward
	 * compatibility.
	 */
	public function add_error_message($error_code, $error_message) {
		$this->add_message($error_code, $error_message);
	}

	/**
	 * Calls Aelia\WC\Messages::get_message(). Implemented for backward
	 * compatibility.
	 */
	public function get_error_message($error_code) {
		return $this->get_message($error_code);
	}

	/**
	 * Initialises the message system.
	 *
	 * @since 1.6.1.150728
	 */
	public static function init() {
		self::init_message_headers();
		add_action('admin_notices', array(__CLASS__, 'admin_notices'));
	}

	/**
	 * Adds an admin message to the list.
	 *
	 * @param string sender_id The ID of the sender. Used to distinguish messages
	 * with the same code.
	 * @param int level The message level.
	 * @param string message The message.
	 * @param string code The message code.
	 * @param bool dismissable Indicates if the message can be dismissed.
	 * @since 1.6.1.150728
	 */
	public static function admin_message($sender_id, $level, $message, $code = '', $dismissable = false) {
		if(!isset(self::$admin_messages[$sender_id])) {
			self::$admin_messages[$sender_id] = array();
		}

		self::$admin_messages[$sender_id][] = new Admin_Message($sender_id, $level, $message, $code = '', $dismissable);
	}

	/**
	 * Initialises the message headers.
	 *
	 * @since 1.6.1.150728
	 */
	protected static function init_message_headers() {
		self::$message_headers = array(
			E_USER_ERROR => __('Error'),
			E_USER_WARNING => __('Warning'),
			E_USER_NOTICE => __('Notice'),
			E_ERROR => __('Error'),
			E_WARNING => __('Warning'),
			E_NOTICE => __('Notice'),
		);
	}

	/**
	 * Returns the header to be displayed for a specific message level.
	 *
	 * @param int level The message level.
	 * @return string
	 * @since 1.6.1.150728
	 */
	protected static function get_message_header($level) {
		return get_value($level, self::$message_headers, '');
	}

	/**
	 * Displays all stored admin messages.
	 *
	 * @since 1.6.1.150728
	 */
	public static function admin_notices() {
		if(empty(self::$admin_messages)) {
			return;
		}
		$last_message_level = null;
		foreach(self::$admin_messages as $sender_id => $messages) {
			foreach($messages as $message) {
				$css_class = get_value($message->level, self::$message_css_classes, '');

				if($message->level != $last_message_level) {
					if(!empty($last_message_level)) {
						echo '</div>';
					}
					echo '<div class="' . $css_class . '">';
					$message_header = sprintf('[%s] - ', $sender_id);
					$message_header .= self::get_message_header($message->level);
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
	}
}
// Initialise the messages system
Messages::init();
