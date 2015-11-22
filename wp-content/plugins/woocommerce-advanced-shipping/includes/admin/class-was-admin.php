<?php
/**
 * Class WAS_Admin.
 *
 * WAS_Admin class handles stuff for admin.
 *
 * @class       WAS_Admin
 * @author     	Jeroen Sormani
 * @package		WooCommerce Advanced Shipping
 * @version		1.0.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WAS_Admin {


	/**
	 * Constructor.
	 *
	 * @since 1.0.5
	 */
	public function __construct() {

		// Add to WC Screen IDs to load scripts.
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_was_screen_ids' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Keep WC menu open while in WAS edit screen
		add_action( 'admin_head', array( $this, 'menu_highlight' ) );

	}


	/**
	 * Screen IDs.
	 *
	 * Add 'was' to the screen IDs so the WooCommerce scripts are loaded.
	 *
	 * @since 1.0.5
	 *
	 * @param 	array	$screen_ids	List of existing screen IDs.
	 * @return 	array 				List of modified screen IDs.
	 */
	public function add_was_screen_ids( $screen_ids ) {

		$screen_ids[] = 'was';

		return $screen_ids;

	}


	/**
	 * Enqueue scripts.
	 *
	 * Enqueue style and java scripts.
	 *
	 * @since 1.0.5
	 */
	public function admin_enqueue_scripts() {

		// Only load scripts on relvant pages
		if (
			( isset( $_REQUEST['post'] ) && 'was' == get_post_type( $_REQUEST['post'] ) ) ||
			( isset( $_REQUEST['post_type'] ) && 'was' == $_REQUEST['post_type'] ) ||
			( isset( $_REQUEST['section'] ) && 'was_advanced_shipping_method' == $_REQUEST['section'] )
		) :

			// Style script
			wp_enqueue_style( 'woocommerce-advanced-shipping-css', plugins_url( 'assets/admin/css/woocommerce-advanced-shipping.css', WooCommerce_Advanced_Shipping()->file ), array(), WooCommerce_Advanced_Shipping()->version );

			// Javascript
			wp_enqueue_script( 'woocommerce-advanced-shipping-js', plugins_url( 'assets/admin/js/woocommerce-advanced-shipping.js', WooCommerce_Advanced_Shipping()->file ), array( 'jquery', 'jquery-ui-sortable' ), WooCommerce_Advanced_Shipping()->version, true );

		endif;

	}


	/**
	 * Keep menu open.
	 *
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 * @since 1.0.5
	 */
	public function menu_highlight() {

		global $parent_file, $submenu_file, $post_type;

		if ( 'was' == $post_type ) :
			$parent_file = 'woocommerce';
			$submenu_file = 'wc-settings';
		endif;

	}


}
