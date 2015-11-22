<?php
/*
Plugin Name: WooCommerce Additional Variation Images 
Plugin URI: http://woothemes.com/products/woocommerce-additional-variation-images/
Description: A WooCommerce plugin/extension that adds ability for shop/store owners to add variation specific images in a group.
Version: 1.5.0
Author: Roy Ho
Author URI: http://royho.me

Copyright: (c) 2014 Roy Ho

License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'c61dd6de57dcecb32bd7358866de4539', '477384' );

if ( ! class_exists( 'WC_Additional_Variation_Images' ) ) :

/**
 * main class.
 *
 * @package  WC_Additional_Variation_Images
 */
class WC_Additional_Variation_Images {

	/**
	 * init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	function __construct() {

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		if ( is_woocommerce_active() ) {

			if ( is_admin() ) {
				include_once( 'classes/class-wc-additional-variation-images-admin.php' );
			}

			include_once( 'classes/class-wc-additional-variation-images-frontend.php' );

		} else {

			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );

		}

		return true;
	}

	/**
	 * load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'wc_additional_variation_images_plugin_locale', get_locale(), 'woocommerce-additional-variation-images' );

		load_textdomain( 'woocommerce-additional-variation-images', trailingslashit( WP_LANG_DIR ) . 'woocommerce-additional-variation-images/woocommerce-additional-variation-images' . '-' . $locale . '.mo' );

		load_plugin_textdomain( 'woocommerce-additional-variation-images', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		return true;
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Additional Variation Images Plugin requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-additional-variation-images' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', 'woocommerce_additional_variation_images_init', 0 );

/**
 * init function
 *
 * @package  WC_Additional_Variation_Images
 * @since 1.0.0
 * @return bool
 */
function woocommerce_additional_variation_images_init() {
	new WC_Additional_Variation_Images();

	return true;
}

endif;