<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit ifaccessed directly

if(!class_exists('Aelia\WC\Aelia_SessionManager')) {
	/**
	 * A simple Session handler. Compatible with WooCommerce 2.0 and later.
	 */
	class Aelia_SessionManager {
		// @var bool Indicates if WooCommerce session was started
		protected static $_wc_session_started = null;

		/**
		 * Indicates if the WooCommerce session was started.
		 *
		 * @return bool
		 * @since 1.5.8.150429
		 */
		public static function has_session() {
			if(self::$_wc_session_started === null) {
				self::$_wc_session_started = is_object(self::session()) &&
																		 self::session()->has_session();
			}
			return self::$_wc_session_started;
		}

		/**
		 * Returns the instance of WooCommerce session.
		 *
		 * @return WC_Session
		 */
		protected static function session() {
			return self::wc()->session;
		}

		/**
		 * Returns global instance of WooCommerce.
		 *
		 * @return object The global instance of WC.
		 */
		protected static function wc() {
			global $woocommerce;
			return $woocommerce;
		}

		/**
		 * Safely store data into the session. Compatible with WooCommerce 2.0+ and
		 * backwards compatible with previous versions.
		 *
		 * @param string key The Key of the value to retrieve.
		 * @param mixed value The value to set.
		 */
		public static function set_value($key, $value) {
			$woocommerce = self::wc();

			// WooCommerce 2.1
			if(version_compare($woocommerce->version, '2.1', '>=')) {
				if(isset($woocommerce->session)) {
					$woocommerce->session->set($key, $value);
				}
				return;
			}

			// WooCommerce 2.0
			if(version_compare($woocommerce->version, '2.0', '>=')) {
				if(isset($woocommerce->session)) {
					$woocommerce->session->$key = $value;
				}
				return;
			}
		}

		/**
		 * Safely retrieve data from the session. Compatible with WooCommerce 2.0+ and
		 * backwards compatible with previous versions.
		 *
		 * @param string key The Key of the value to retrieve.
		 * @param mixed default The default value to return if the key is not found.
		 * @param bool remove_after_get Indicates if the value should be removed after
		 * having been retrieved.
		 * @return mixed The value associated with the key, or the default.
		 */
		public static function get_value($key, $default = null, $remove_after_get = false) {
			$woocommerce = self::wc();
			$result = null;

			// WooCommerce 2.1
			if(is_null($result) && version_compare($woocommerce->version, '2.1', '>=')) {
				if(!isset($woocommerce->session)) {
					return $default;
				}
				$result = @$woocommerce->session->get($key);
			}

			// WooCommerce 2.0
			if(is_null($result) && version_compare($woocommerce->version, '2.0', '>=')) {
				if(!isset($woocommerce->session)) {
					return $default;
				}
				$result = @$woocommerce->session->$key;
			}

			if($remove_after_get) {
				self::delete_value($key);
			}

			return empty($result) ? $default : $result;
		}

		/**
		 * Safely remove data from the session. Compatible with WooCommerce 2.0+ and
		 * backwards compatible with previous versions.
		 *
		 * @param string key The Key of the value to retrieve.
		 */
		public static function delete_value($key) {
			$woocommerce = self::wc();

			// WooCommerce 2.1
			if(version_compare($woocommerce->version, '2.1', '>=')) {
				if(isset($woocommerce->session)) {
					$woocommerce->session->set($key, null);
				}
				return;
			}

			// WooCommerce 2.0
			if(version_compare($woocommerce->version, '2.0', '>=')) {
				if(isset($woocommerce->session)) {
					unset($woocommerce->session->$key);
				}
				return;
			}
		}

		/**
		 * Set a cookie. This method is a wrapper for setcookie() function, which
		 * automatically uses WordPress constants.
		 *
		 * @param string $name The name of the cookie being set.
		 * @param string $value The value of the cookie.
		 * @param integer $expire The expiration of the cookie.
		 * @param string $secure Whether the cookie should be served only over https.
		 * @since 1.5.11.150507
		 */
		public static function set_cookie($name, $value, $expire = 0, $secure = false) {
			if(!headers_sent()) {
				setcookie($name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure);
				// Overwrite the cookie in the global variable, so that it can be accessed immediately
				$_COOKIE[$name] = $value;
			}
			elseif(defined('WP_DEBUG') && WP_DEBUG) {
				headers_sent($file, $line);
				trigger_error("{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE);
			}
		}

		/**
		 * Returns the value of a cookie, or a default if such cookie is not found.
		 *
		 * @param string $name The name of the cookie being retrieved.
		 * @param string $default The default value to return if the cookie is not
		 * set.
		 * @since 1.5.11.150507
		 */
		public static function get_cookie($name, $default = null) {
			return get_value($name, $_COOKIE, $default);
		}
	}
}
