<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Semaphore Lock Management.
 */
class Semaphore extends Base_Class {
	// @var string Identifies the lock.
	protected $lock_name = 'lock';
	// @var string Convenience variable. Stores the AFC plugin text domain.
	protected $text_domain;
	// @var bool Indicates if the lock was obtained
	protected $lock_obtained = false;

	const DEFAULT_SEMAPHORE_LOCK_WAIT = 180;
	const SEMAPHORE_ROWS = 3;

	/**
	 * Class constructor.
	 *
	 * @param string lock_name The name to assign to the lock.
	 */
	public function __construct($lock_name) {
		parent::__construct();

		$this->text_domain = WC_AeliaFoundationClasses::$text_domain;
		if(empty($lock_name)) {
			throw new \InvalidArgumentException('Invalid lock name specified for semaphore.',
																					$this->text_domain);
		}
		$this->lock_name = $lock_name;
	}

	/**
	 * Initializes the semaphore object.
	 *
	 * @static
	 * @return Semaphore
	 */
	public static function factory($lock_name) {
		$result = new self($lock_name);
	}

	/**
	 * Initializes the lock.
	 */
	public function initialize() {
		global $wpdb;
	}

	/**
	 * Attempts to start the lock. If the rename works, the lock is started.
	 *
	 * @return bool
	 */
	public function lock() {
		global $wpdb;

		$lock_available = $wpdb->get_var("SELECT IS_FREE_LOCK('" . $this->lock_name . "')");
		if($lock_available) {
			$this->lock_obtained = $wpdb->get_var("SELECT GET_LOCK('" . $this->lock_name . "', 0)");
		}

		if(!$lock_available || !$this->lock_obtained) {
			$this->log(sprintf(__('Semaphore lock "%s" failed (line %s).', $this->text_domain),
												 $this->lock_name,
												 __LINE__));
			return false;
		}

		$this->log(sprintf(__('Semaphore lock "%s" obtained at %s.', $this->text_domain),
											 $this->lock_name,
											 gmdate('Y-m-d H:i:s')));
		return true;
	}

	/**
	 * Unlocks the process.
	 *
	 * @return bool
	 */
	public function unlock() {
		global $wpdb;

		if(!$this->lock_obtained) {
			return true;
		}

		$lock_released = $wpdb->get_var("SELECT RELEASE_LOCK('" . $this->lock_name . "')");

		if($lock_released) {
			$this->log(sprintf(__('Semaphore "%s" unlocked.', $this->text_domain), $this->lock_name));
		}
		return $lock_released;
	}
}
