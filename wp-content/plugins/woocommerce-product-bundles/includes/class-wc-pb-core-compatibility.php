<?php
/**
 * Functions related to core back-compatibility.
 *
 * @class  WC_PB_Core_Compatibility
 * @since  4.7.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Core_Compatibility {

	/**
	 * Get the WC Product instance for a given product ID or post
	 *
	 * get_product() is soft-deprecated in WC 2.2
	 *
	 * @since 4.7.6
	 * @param bool|int|string|\WP_Post $the_product
	 * @param array $args
	 * @return WC_Product
	 */
	public static function wc_get_product( $the_product = false, $args = array() ) {

		if ( self::is_wc_version_gte_2_2() ) {

			return wc_get_product( $the_product, $args );

		} else {

			return get_product( $the_product, $args );
		}
	}

	/**
	 * Helper method to get the version of the currently installed WooCommerce
	 *
	 * @since 4.7.6
	 * @return string woocommerce version number or null
	 */
	private static function get_wc_version() {

		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater
	 *
	 * @since 4.10.2
	 * @return boolean true if the installed version of WooCommerce is 2.2 or greater
	 */
	public static function is_wc_version_gte_2_4() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.4', '>=' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater
	 *
	 * @since 4.7.6
	 * @return boolean true if the installed version of WooCommerce is 2.2 or greater
	 */
	public static function is_wc_version_gte_2_3() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.3', '>=' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.2 or greater
	 *
	 * @since 4.7.6
	 * @return boolean true if the installed version of WooCommerce is 2.2 or greater
	 */
	public static function is_wc_version_gte_2_2() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.2', '>=' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is less than 2.2
	 *
	 * @since 4.7.6
	 * @return boolean true if the installed version of WooCommerce is less than 2.2
	 */
	public static function is_wc_version_lt_2_2() {
		return self::get_wc_version() && version_compare( self::get_wc_version(), '2.2', '<' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than $version
	 *
	 * @since 4.7.6
	 * @param string $version the version to compare
	 * @return boolean true if the installed version of WooCommerce is > $version
	 */
	public static function is_wc_version_gt( $version ) {
		return self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
	}
}
