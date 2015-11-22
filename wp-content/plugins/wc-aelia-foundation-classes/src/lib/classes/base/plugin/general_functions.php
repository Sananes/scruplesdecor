<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

// General functions used throughout the plugin.

if(!function_exists('get_value')) {
	/**
	 * Return the value from an associative array or an object.
	 *
	 * @param string $key The key or property name of the value.
	 * @param mixed $collection The array or object to search.
	 * @param mixed $default The value to return if the key does not exist.
	 * @return mixed The value from the array or object.
	 */
	function get_value($key, $collection, $default = FALSE) {
		$result = $default;
		if(is_array($collection) && isset($collection[$key])) {
			$result = $collection[$key];
		} elseif(is_object($collection) && isset($collection->$key)) {
			$result = $collection->$key;
		}

		return $result;
	}
}

if(!function_exists('get_arr_value')) {
	/**
	 * Return the value from an associative array.
	 *
	 * @param string $key The key of the value.
	 * @param mixed $collection The array search.
	 * @param mixed $default The value to return if the key does not exist.
	 * @return mixed The value from the array, or the default.
	 * @since 1.5.12.150512
	 */
	function get_arr_value($key, array $collection, $default = FALSE) {
		return isset($collection[$key]) ? $collection[$key] : $default;
	}
}

if(!function_exists('get_datetime_format')) {
	/**
	 * Returns a concatenation of WordPress settings for date and time formats.
	 *
	 * @param string separator A string to separate date and time formatting
	 * strings.
	 * @return string The concatenation of date_format, separator and time_format.
	 */
	function get_datetime_format($separator = ' ') {
		return get_option('date_format') . $separator . get_option('time_format');
	}
}

if(!function_exists('coalesce')) {
	/**
	 * Returns the value of the first non-empty argument received.
	 *
	 * @param mixed Any arguments.
	 * @return mixed The value of the first non-empty argument.
	 */
	function coalesce() {
		$args = func_get_args();
		foreach($args as $arg) {
			if(!empty($arg)) {
				return $arg;
			}
		}
		return null;
	}
}
