<?php
namespace Aelia\WC;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

use \Exception;
use \InvalidArgumentException;

interface IExchangeRatesModel {
	public function get_exchange_rates($base_currency, array $currencies);
}

/**
 * Implements methods to retrieve the Exchange Rates of one or more Currencies
 * in relation to a Base Currency
 */
abstract class ExchangeRatesModel implements IExchangeRatesModel {
	const ERR_EXCEPTION_OCCURRED = 1001;
	const ERR_REQUEST_FAILED = 1002;
	const ERR_ERROR_RETURNED = 1003;
	const ERR_BASE_CURRENCY_NOT_FOUND = 1004;
	const ERR_UNEXPECTED_ERROR_FETCHING_EXCHANGE_RATES = 1005;

	// @var int The amount of decimal digits to preserve from Exchange Rates.
	const EXCHANGE_RATE_DECIMALS = 4;

	private $_errors = array();

	/**
	 * Class constructor.
	 *
	 * @param array An array of Settings that can be used to override the ones
	 * currently saved in the configuration.
	 * @return WC_Aelia_ExchangeRatesModel
	 */
	public function __construct($settings = null) {

	}

	public function get_errors() {
		return $this->_errors;
	}

	protected function add_error($error_code, $error_msg) {
		$this->_errors[$error_code] = $error_msg;
	}

	/**
	 * Caches the exchange rates for the specified period.
	 *
	 * @param string cache_key The cache key to use for storage. It will be
	 * appended to the object's class name to form a unique key.
	 * @param array exchange_rates The exchange rates to cache.
	 * @param int cache_duration The lifespan of the cached value, in seconds.
	 * @return bool
	 */
	protected function cache_exchange_rates($cache_key, $exchange_rates, $cache_duration = HOUR_IN_SECONDS) {
		return set_transient(md5(get_class($this)) . '_' . $cache_key, $exchange_rates, $cache_duration);
	}

	/**
	 * Returns the cached exchange rates, if available.
	 *
	 * @param string cache_key The cache key to use for retrieval. It will be
	 * appended to the object's class name to form a unique key.
	 * @return array|false
	 */
	protected function get_cached_exchange_rates($cache_key) {
		return get_transient(md5(get_class($this)) . '_' . $cache_key);
	}

	/**
	 * Returns the Exchange Rate of a Currency in respect to a Base Currency.
	 *
	 * @throws An Exception. This method must be implemented by descendant classes.
	 */
	protected function get_rate($base_currency, $currency) {
		throw new Exception(__('Not implemented. Descendant classes must implement this method.'),
												Definitions::TEXT_DOMAIN);
	}

	/**
	 * Returns an associative array containing the exchange rate for each of the
	 * Currencies passed as a parameter, based on a Base Currency.
	 *
	 * @param string base_currency The code of the Base Currency.
	 * @param array currencies A list of Currency Codes.
	 * @return array An associative array of Currency => Exchange Rate pairs.
	 */
	public function get_exchange_rates($base_currency, array $currencies) {
		$result = array();

		if(empty($base_currency)) {
			throw new InvalidArgumentException(__('Base Currency is required, empty value received.',
																						Definitions::TEXT_DOMAIN));
		}

		foreach($currencies as $currency) {
			if(empty($currency)) {
				throw new InvalidArgumentException(__('Destination Currency is required, empty value received.',
																							Definitions::TEXT_DOMAIN));
			}

			if($currency === $base_currency) {
				$exchange_rate = 1;
			}
			else {
				// Note: if exchange rate cannot be found, null is stored instead
				// TODO Find a way to notify Admins of such occurrence
				$exchange_rate = $this->get_rate($base_currency, $currency);

				if(!empty($exchange_rate) && ($exchange_rate > 0)) {
					$exchange_rate = round($exchange_rate, self::EXCHANGE_RATE_DECIMALS);
				}
				else {
					// If an invalid exchange rate is returned, just skip it altogether
					continue;
				}
			}

			$result[$currency] = $exchange_rate;
		}
		return $result;
	}
}
