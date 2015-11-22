<?php
/**
 * Product Bundle Helper Functions.
 *
 * @class   WC_PB_Helpers
 * @version 4.11.4
 * @since   4.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PB_Helpers {

	/**
	 * Updates post_meta v1 storage scheme (scattered post_meta) to v2 (serialized post_meta)
	 * @param  int    $bundle_id     bundle product_id
	 * @return void
	 */
	public static function serialize_bundle_meta( $bundle_id ) {

		global $wpdb;

		$bundled_item_ids 	= maybe_unserialize( get_post_meta( $bundle_id, '_bundled_ids', true ) );
		$default_attributes = maybe_unserialize( get_post_meta( $bundle_id, '_bundle_defaults', true ) );
		$allowed_variations = maybe_unserialize( get_post_meta( $bundle_id, '_allowed_variations', true ) );

		$bundle_data = array();

		foreach ( $bundled_item_ids as $bundled_item_id ) {

			$bundle_data[ $bundled_item_id ] = array();

			$filtered       = get_post_meta( $bundle_id, 'filter_variations_' . $bundled_item_id, true );
			$o_defaults     = get_post_meta( $bundle_id, 'override_defaults_' . $bundled_item_id, true );
			$hide_thumbnail = get_post_meta( $bundle_id, 'hide_thumbnail_' . $bundled_item_id, true );
			$item_o_title   = get_post_meta( $bundle_id, 'override_title_' . $bundled_item_id, true );
			$item_title     = get_post_meta( $bundle_id, 'product_title_' . $bundled_item_id, true );
			$item_o_desc    = get_post_meta( $bundle_id, 'override_description_' . $bundled_item_id, true );
			$item_desc      = get_post_meta( $bundle_id, 'product_description_' . $bundled_item_id, true );
			$item_qty       = get_post_meta( $bundle_id, 'bundle_quantity_' . $bundled_item_id, true );
			$discount       = get_post_meta( $bundle_id, 'bundle_discount_' . $bundled_item_id, true );
			$visibility     = get_post_meta( $bundle_id, 'visibility_' . $bundled_item_id, true );

			$sep = explode( '_', $bundled_item_id );

			$bundle_data[ $bundled_item_id ][ 'product_id' ]        = $sep[0];
			$bundle_data[ $bundled_item_id ][ 'filter_variations' ] = ( $filtered === 'yes' ) ? 'yes' : 'no';

			if ( isset( $allowed_variations[ $bundled_item_id ] ) ) {
				$bundle_data[ $bundled_item_id ][ 'allowed_variations' ] = $allowed_variations[ $bundled_item_id ];
			}

			$bundle_data[ $bundled_item_id ][ 'override_defaults' ] = ( $o_defaults === 'yes' ) ? 'yes' : 'no';

			if ( isset( $default_attributes[ $bundled_item_id ] ) ) {
				$bundle_data[ $bundled_item_id ][ 'bundle_defaults' ] = $default_attributes[ $bundled_item_id ];
			}

			$bundle_data[ $bundled_item_id ][ 'hide_thumbnail' ] = ( $hide_thumbnail === 'yes' ) ? 'yes' : 'no';
			$bundle_data[ $bundled_item_id ][ 'override_title' ] = ( $item_o_title === 'yes' ) ? 'yes' : 'no';

			if ( $item_o_title === 'yes' ) {
				$bundle_data[ $bundled_item_id ][ 'product_title' ] = $item_title;
			}

			$bundle_data[ $bundled_item_id ][ 'override_description' ] = ( $item_o_desc === 'yes' ) ? 'yes' : 'no';

			if ( $item_o_desc === 'yes' ) {
				$bundle_data[ $bundled_item_id ][ 'product_description' ] = $item_desc;
			}

			$bundle_data[ $bundled_item_id ][ 'bundle_quantity' ]          = $item_qty;
			$bundle_data[ $bundled_item_id ][ 'bundle_discount' ]          = $discount;
			$bundle_data[ $bundled_item_id ][ 'visibility' ]               = ( $visibility === 'hidden' ) ? 'hidden' : 'visible';
			$bundle_data[ $bundled_item_id ][ 'hide_filtered_variations' ] = 'no';
		}

		update_post_meta( $bundle_id, '_bundle_data', $bundle_data );

		$wpdb->query( $wpdb->prepare( "DELETE FROM `$wpdb->postmeta` WHERE `post_id` LIKE %s AND (
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE %s OR
			`meta_key` LIKE ('_bundled_ids') OR
			`meta_key` LIKE ('_bundle_defaults') OR
			`meta_key` LIKE ('_allowed_variations')
		)", $bundle_id, 'filter_variations_%', 'override_defaults_%', 'bundle_quantity_%', 'bundle_discount_%', 'hide_thumbnail_%', 'override_title_%', 'product_title_%', 'override_description_%', 'product_description_%', 'hide_filtered_variations_%', 'visibility_%' ) );

		return $bundle_data;
	}

	/**
	 * Calculates bundled product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @param  WC_Product   $product    the product
	 * @param  double       $price      the product price
	 * @return double                   modified product price incl. or excl. tax
	 */
	public static function get_product_display_price( $product, $price ) {

		if ( ! $price ) {
			return $price;
		}

		if ( get_option( 'woocommerce_tax_display_shop' ) === 'excl' ) {
			$product_price = $product->get_price_excluding_tax( 1, $price );
		} else {
			$product_price = $product->get_price_including_tax( 1, $price );
		}

		return $product_price;
	}

	/**
	 * Loads variation ids for a given variable product.
	 *
	 * @param  int    $item_id
	 * @return array
	 */
	public function get_product_variations( $item_id ) {

		$transient_name = 'wc_product_children_ids_' . $item_id;

        if ( false === ( $variations = get_transient( $transient_name ) ) ) {

			$args = array(
				'post_type'   => 'product_variation',
				'post_status' => array( 'publish' ),
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'asc',
				'post_parent' => $item_id,
				'fields'      => 'ids'
			);

			$variations = get_posts( $args );
		}

		return $variations;
	}

	/**
	 * Return a formatted product title based on id.
	 *
	 * @param  int    $product_id
	 * @return string
	 */
	public function get_product_title( $product_id, $suffix = '' ) {

		$title = get_the_title( $product_id );

		if ( $suffix ) {
			$title = sprintf( _x( '%1$s %2$s', 'product title followed by suffix', 'woocommerce-product-bundles' ), $title, $suffix );
		}

		$sku = get_post_meta( $product_id, '_sku', true );

		if ( ! $title ) {
			return false;
		}

		if ( $sku ) {
			$sku = sprintf( __( 'SKU: %s', 'woocommerce-product-bundles' ), $sku );
		} else {
			$sku = '';
		}

		return self::format_product_title( $title, $sku, '', true );
	}

	/**
	 * Format a product title.
	 *
	 * @param  string  $title
	 * @param  string  $sku
	 * @param  string  $meta
	 * @param  boolean $paren
	 * @return string
	 */
	public static function format_product_title( $title, $sku = '', $meta = '', $paren = false ) {

		if ( $sku && $meta ) {
			if ( $paren ) {
				$title = sprintf( _x( '%1$s &mdash; %2$s (%3$s)', 'product title followed by meta and sku in parenthesis', 'woocommerce-product-bundles' ), $title, $meta, $sku );
			} else {
				$title = sprintf( _x( '%1$s &ndash; %2$s &mdash; %3$s', 'sku followed by product title and meta', 'woocommerce-product-bundles' ), $sku, $title, $meta );
			}
		} elseif ( $sku ) {
			if ( $paren ) {
				$title = sprintf( _x( '%1$s (%2$s)', 'product title followed by sku in parenthesis', 'woocommerce-product-bundles' ), $title, $sku );
			} else {
				$title = sprintf( _x( '%1$s &ndash; %2$s', 'sku followed by product title', 'woocommerce-product-bundles' ), $sku, $title );
			}
		} elseif ( $meta ) {
			$title = sprintf( _x( '%1$s &mdash; %2$s', 'product title followed by meta', 'woocommerce-product-bundles' ), $title, $meta );
		}

		return $title;
	}

	/**
	 * Format a product title incl qty, price and suffix.
	 *
	 * @param  string $title
	 * @param  string $qty
	 * @param  string $price
	 * @param  string $suffix
	 * @return string
	 */
	public static function format_product_shop_title( $title, $qty = '', $price = '', $suffix = '' ) {

		$quantity_string = '';
		$price_string    = '';
		$suffix_string   = '';

		if ( $qty ) {
			$quantity_string = sprintf( _x( ' &times; %s', 'qty string', 'woocommerce-product-bundles' ), $qty );
		}

		if ( $price ) {
			$price_string = sprintf( _x( ' &ndash; %s', 'price suffix', 'woocommerce-product-bundles' ), $price );
		}

		if ( $suffix ) {
			$suffix_string = sprintf( _x( ' &ndash; %s', 'suffix', 'woocommerce-product-bundles' ), $suffix );
		}

		$title_string = sprintf( _x( '%1$s%2$s%3$s%4$s', 'title, quantity, price, suffix', 'woocommerce-product-bundles' ), $title, $quantity_string, $price_string, $suffix_string );

		return $title_string;
	}

	/**
	 * Calculates bundled product prices incl. or excl. tax depending on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @param  WC_Product   $product    the product
	 * @param  double       $price      the product price
	 * @return double                   modified product price incl. or excl. tax
	 */
	public function get_product_price_incl_or_excl_tax( $product, $price ) {
		_deprecated_function( 'get_product_price_incl_or_excl_tax', '4.11.4', 'get_product_display_price' );
		return self::get_product_display_price( $product, $price );
	}
}
