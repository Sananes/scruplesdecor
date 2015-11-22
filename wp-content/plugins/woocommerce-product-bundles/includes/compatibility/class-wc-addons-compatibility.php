<?php
/**
 * Product Addons and NYP Compatibility.
 *
 * @since 4.11.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Addons_Compatibility {

	public static function init() {

		// Support for Product Addons
		add_action( 'woocommerce_bundled_product_add_to_cart', array( __CLASS__, 'addons_support' ), 10, 2 );
		add_filter( 'product_addons_field_prefix', array( __CLASS__, 'addons_cart_prefix' ), 10, 2 );

		add_filter( 'woocommerce_addons_price_for_display_product', array( __CLASS__, 'addons_price_for_display_product' ) );

		// Support for NYP
		add_action( 'woocommerce_bundled_product_add_to_cart', array( __CLASS__, 'nyp_price_input_support' ), 9, 2 );
		add_filter( 'nyp_field_prefix', array( __CLASS__, 'nyp_cart_prefix' ), 10, 2 );

		// Validate add to cart NYP and Addons
		add_filter( 'woocommerce_bundled_item_add_to_cart_validation', array( __CLASS__, 'validate_bundled_item_nyp_and_addons' ), 10, 5 );

		// Add addons identifier to bundled item stamp
		add_filter( 'woocommerce_bundled_item_cart_item_identifier', array( __CLASS__, 'bundled_item_addons_stamp' ), 10, 2 );

		// Add NYP identifier to bundled item stamp
		add_filter( 'woocommerce_bundled_item_cart_item_identifier', array( __CLASS__, 'bundled_item_nyp_stamp' ), 10, 2 );

		// Before and after add-to-cart handling
		add_action( 'woocommerce_bundled_item_before_add_to_cart', array( __CLASS__, 'before_bundled_add_to_cart' ), 10, 5 );
		add_action( 'woocommerce_bundled_item_after_add_to_cart', array( __CLASS__, 'after_bundled_add_to_cart' ), 10, 5 );

		// Load child NYP/Addons data from the parent cart item data array
		add_filter( 'woocommerce_bundled_item_cart_data', array( __CLASS__, 'get_bundled_cart_item_data_from_parent' ), 10, 2 );
	}

	/**
	 * Support for bundled item addons.
	 *
	 * @param  int               $product_id    the product id
	 * @param  WC_Bundled_Item   $item          the bundled item
	 * @return void
	 */
	public static function addons_support( $product_id, $item ) {

		global $Product_Addon_Display, $product;

		if ( ! empty( $Product_Addon_Display ) ) {

			$product_bak = isset( $product ) ? $product : false;
			$product     = $item->product;

			WC_PB_Compatibility::$addons_prefix          = $item->item_id;
			WC_PB_Compatibility::$compat_bundled_product = $item->product;

			$Product_Addon_Display->display( $product_id, false );

			WC_PB_Compatibility::$addons_prefix = WC_PB_Compatibility::$compat_bundled_product = '';

			if ( $product_bak ) {
				$product = $product_bak;
			}
		}
	}

	/**
	 * Sets a unique prefix for unique add-ons. The prefix is set and re-set globally before validating and adding to cart.
	 *
	 * @param  string   $prefix         unique prefix
	 * @param  int      $product_id     the product id
	 * @return string                   a unique prefix
	 */
	public static function addons_cart_prefix( $prefix, $product_id ) {

		if ( ! empty( WC_PB_Compatibility::$addons_prefix ) ) {
			$prefix = WC_PB_Compatibility::$addons_prefix . '-';
		}

		if ( ! empty( WC_PB_Compatibility::$bundle_prefix ) ) {
			$prefix = WC_PB_Compatibility::$bundle_prefix . '-' . WC_PB_Compatibility::$addons_prefix . '-';
		}

		return $prefix;
	}

	/**
	 * Filter the product which add-ons prices are displayed for.
	 *
	 * @param  WC_Product  $product
	 * @return WC_Product
	 */
	public static function addons_price_for_display_product( $product ) {

		if ( ! empty( WC_PB_Compatibility::$compat_bundled_product ) ) {
			return WC_PB_Compatibility::$compat_bundled_product;
		}

		return $product;
	}

	/**
	 * Support for bundled item NYP.
	 *
	 * @param  int               $product_id     the product id
	 * @param  WC_Bundled_Item   $item           the bundled item
	 * @return void
	 */
	public static function nyp_price_input_support( $product_id, $item ) {

		global $product;

		$the_product = ! empty( WC_PB_Compatibility::$compat_product ) ? WC_PB_Compatibility::$compat_product : $product;

		if ( $the_product->product_type === 'bundle' && $the_product->is_priced_per_product() == false ) {
			return;
		}

		if ( function_exists( 'WC_Name_Your_Price' ) && $item->product->product_type == 'simple' ) {

			WC_PB_Compatibility::$nyp_prefix = $item->item_id;

			WC_Name_Your_Price()->display->display_price_input( $product_id, self::nyp_cart_prefix( false, $product_id ) );

			WC_PB_Compatibility::$nyp_prefix = '';
		}
	}

	/**
	 * Sets a unique prefix for unique NYP products. The prefix is set and re-set globally before validating and adding to cart.
	 *
	 * @param  string   $prefix         unique prefix
	 * @param  int      $product_id     the product id
	 * @return string                   a unique prefix
	 */
	public static function nyp_cart_prefix( $prefix, $product_id ) {

		if ( ! empty( WC_PB_Compatibility::$nyp_prefix ) ) {
			$prefix = '-' . WC_PB_Compatibility::$nyp_prefix;
		}

		if ( ! empty( WC_PB_Compatibility::$bundle_prefix ) ) {
			$prefix = '-' . WC_PB_Compatibility::$nyp_prefix . '-' . WC_PB_Compatibility::$bundle_prefix;
		}

		return $prefix;
	}

	/**
	 * Add addons identifier to bundled item stamp, in order to generate new cart ids for bundles with different addons configurations.
	 *
	 * @param  array  $bundled_item_stamp
	 * @param  string $bundled_item_id
	 * @return array
	 */
	public static function bundled_item_addons_stamp( $bundled_item_stamp, $bundled_item_id ) {

		global $Product_Addon_Cart;

		// Store bundled item addons add-ons config in stamp to avoid generating the same bundle cart id
		if ( ! empty( $Product_Addon_Cart ) ) {

			$addon_data = array();

			// Set addons prefix
			WC_PB_Compatibility::$addons_prefix = $bundled_item_id;

			$bundled_product_id = $bundled_item_stamp[ 'product_id' ];

			$addon_data = $Product_Addon_Cart->add_cart_item_data( $addon_data, $bundled_product_id );

			// Reset addons prefix
			WC_PB_Compatibility::$addons_prefix = '';

			if ( ! empty( $addon_data[ 'addons' ] ) ) {
				$bundled_item_stamp[ 'addons' ] = $addon_data[ 'addons' ];
			}
		}

		return $bundled_item_stamp;
	}

	/**
	 * Add nyp identifier to bundled item stamp, in order to generate new cart ids for bundles with different nyp configurations.
	 *
	 * @param  array  $bundled_item_stamp
	 * @param  string $bundled_item_id
	 * @return array
	 */
	public static function bundled_item_nyp_stamp( $bundled_item_stamp, $bundled_item_id ) {

		if ( function_exists( 'WC_Name_Your_Price' ) ) {

			$nyp_data = array();

			// Set nyp prefix
			WC_PB_Compatibility::$nyp_prefix = $bundled_item_id;

			$bundled_product_id = $bundled_item_stamp[ 'product_id' ];

			$nyp_data = WC_Name_Your_Price()->cart->add_cart_item_data( $nyp_data, $bundled_product_id, '' );

			// Reset nyp prefix
			WC_PB_Compatibility::$nyp_prefix = '';

			if ( ! empty( $nyp_data[ 'nyp' ] ) ) {
				$bundled_item_stamp[ 'nyp' ] = $nyp_data[ 'nyp' ];
			}
		}

		return $bundled_item_stamp;
	}

	/**
	 * Validate bundled item NYP and Addons.
	 *
	 * @param  bool   $add
	 * @param  int    $product_id
	 * @param  int    $quantity
	 * @return bool
	 */
	public static function validate_bundled_item_nyp_and_addons( $add, $bundle, $bundled_item, $quantity, $variation_id ) {

		// Ordering again? When ordering again, do not revalidate addons & nyp
		$order_again = isset( $_GET[ 'order_again' ] ) && isset( $_GET[ '_wpnonce' ] ) && wp_verify_nonce( $_GET[ '_wpnonce' ], 'woocommerce-order_again' );

		if ( $order_again  ) {
			return $add;
		}

		$bundled_item_id = $bundled_item->item_id;
		$product_id      = $bundled_item->product_id;

		// Validate add-ons
		global $Product_Addon_Cart;

		if ( ! empty( $Product_Addon_Cart ) ) {

			WC_PB_Compatibility::$addons_prefix = $bundled_item_id;

			if ( ! $Product_Addon_Cart->validate_add_cart_item( true, $product_id, $quantity ) ) {
				return false;
			}

			WC_PB_Compatibility::$addons_prefix = '';
		}

		// Validate nyp
		if ( WC_PB_Compatibility::$bundle_prefix ) {
			$has_parent_priced_statically = get_post_meta( WC_PB_Compatibility::$bundle_prefix, '_per_product_pricing_bto', true ) == 'yes' ? false : true;
		} else {
			$has_parent_priced_statically = false;
		}

		if ( $bundled_item->is_priced_per_product() && ( ! $has_parent_priced_statically ) && function_exists( 'WC_Name_Your_Price' ) ) {

			WC_PB_Compatibility::$nyp_prefix = $bundled_item_id;

			if ( ! WC_Name_Your_Price()->cart->validate_add_cart_item( true, $product_id, $quantity ) ) {
				return false;
			}

			WC_PB_Compatibility::$nyp_prefix = '';
		}

		return $add;
	}

	/**
	 * Runs before adding a bundled item to the cart.
	 *
	 * @param  int                $product_id
	 * @param  int                $quantity
	 * @param  int                $variation_id
	 * @param  array              $variations
	 * @param  array              $bundled_item_cart_data
	 * @return void
	 */
	public static function after_bundled_add_to_cart( $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data ) {

		global $Product_Addon_Cart;

		// Reset addons and nyp prefix
		WC_PB_Compatibility::$addons_prefix = WC_PB_Compatibility::$nyp_prefix = '';

		if ( ! empty ( $Product_Addon_Cart ) ) {
			add_filter( 'woocommerce_add_cart_item_data', array( $Product_Addon_Cart, 'add_cart_item_data' ), 10, 2 );
		}

		// Similarly with NYP
		if ( function_exists( 'WC_Name_Your_Price' ) ) {
			add_filter( 'woocommerce_add_cart_item_data', array( WC_Name_Your_Price()->cart, 'add_cart_item_data' ), 5, 3 );
		}
	}

	/**
	 * Runs after adding a bundled item to the cart.
	 *
	 * @param  int                $product_id
	 * @param  int                $quantity
	 * @param  int                $variation_id
	 * @param  array              $variations
	 * @param  array              $bundled_item_cart_data
	 * @return void
	 */
	public static function before_bundled_add_to_cart( $product_id, $quantity, $variation_id, $variations, $bundled_item_cart_data ) {

		global $Product_Addon_Cart;

		// Set addons and nyp prefixes
		WC_PB_Compatibility::$addons_prefix = WC_PB_Compatibility::$nyp_prefix = $bundled_item_cart_data[ 'bundled_item_id' ];

		// Add-ons cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing Addons to re-add it
		// Not doing so results in issues with file upload validation

		if ( ! empty ( $Product_Addon_Cart ) ) {
			remove_filter( 'woocommerce_add_cart_item_data', array( $Product_Addon_Cart, 'add_cart_item_data' ), 10, 2 );
		}

		// Similarly with NYP
		if ( function_exists( 'WC_Name_Your_Price' ) ) {
			remove_filter( 'woocommerce_add_cart_item_data', array( WC_Name_Your_Price()->cart, 'add_cart_item_data' ), 5, 3 );
		}
	}

	/**
	 * Retrieve child cart item data from the parent cart item data array, if necessary.
	 *
	 * @param  array  $bundled_item_cart_data
	 * @param  array  $cart_item_data
	 * @return array
	 */
	public static function get_bundled_cart_item_data_from_parent( $bundled_item_cart_data, $cart_item_data ) {

		// Add-ons cart item data is already stored in the composite_data array, so we can grab it from there instead of allowing Addons to re-add it
		if ( isset( $bundled_item_cart_data[ 'bundled_item_id' ] ) && isset( $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'addons' ] ) ) {
			$bundled_item_cart_data[ 'addons' ] = $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'addons' ];
		}

		// Similarly with NYP
		if ( isset( $bundled_item_cart_data[ 'bundled_item_id' ] ) && isset( $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'nyp' ] ) ) {
			$bundled_item_cart_data[ 'nyp' ] = $cart_item_data[ 'stamp' ][ $bundled_item_cart_data[ 'bundled_item_id' ] ][ 'nyp' ];
		}

		return $bundled_item_cart_data;
	}
}

WC_PB_Addons_Compatibility::init();
