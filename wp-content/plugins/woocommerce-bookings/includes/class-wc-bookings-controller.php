<?php
/**
 * Gets bookings
 */
class WC_Bookings_Controller {

	/**
	 * Return all bookings for a product in a given range
	 * @param  int $product_id
	 * @param  timestamp $start_date
	 * @param  timestamp $end_date
	 * @param  int product_or_resource_id
	 * @return array of bookings
	 */
	public static function get_bookings_in_date_range( $start_date, $end_date, $product_or_resource_id = '', $check_in_cart = true ) {
		$transient_name = 'book_dr_' . md5( http_build_query( array( $start_date, $end_date, $product_or_resource_id, WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );

		if ( false === ( $booking_ids = get_transient( $transient_name ) ) ) {
			$booking_ids = self::get_bookings_in_date_range_query( $start_date, $end_date, $product_or_resource_id, $check_in_cart );
			set_transient( $transient_name, $booking_ids, DAY_IN_SECONDS * 30 );
		}

		// Get objects
		$bookings = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}

	/**
	 * Return all bookings for a product in a given range - the query part (no cache)
	 * @param  int $product_id
	 * @param  timestamp $start_date
	 * @param  timestamp $end_date
	 * @param  int product_or_resource_id
	 * @return array of booking ids
	 */
	private static function get_bookings_in_date_range_query( $start_date, $end_date, $product_or_resource_id = '', $check_in_cart = true ) {
		global $wpdb;

		if ( $product_or_resource_id ) {
			if ( get_post_type( $product_or_resource_id ) === 'bookable_resource' ) {
				$product_meta_key_q    = ' AND idmeta.meta_key = "_booking_resource_id" AND idmeta.meta_value = "' . absint( $product_or_resource_id ) . '" ';
				$product_meta_key_join = " LEFT JOIN {$wpdb->postmeta} as idmeta ON {$wpdb->posts}.ID = idmeta.post_id ";
			} else {
				$product_meta_key_q    = ' AND idmeta.meta_key = "_booking_product_id" AND idmeta.meta_value = "' . absint( $product_or_resource_id ) . '" ';
				$product_meta_key_join = " LEFT JOIN {$wpdb->postmeta} as idmeta ON {$wpdb->posts}.ID = idmeta.post_id ";
			}
		} else {
			$product_meta_key_join = '';
			$product_meta_key_q    = '';
		}

		$booking_statuses = apply_filters( 'woocommerce_bookings_fully_booked_statuses', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
			'complete',
			'in-cart'
		) );

		if ( ! $check_in_cart ) {
			$booking_statuses = array_diff( $booking_statuses, array( 'in-cart' ) );
		}

		$booking_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT ID FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta} as startmeta ON {$wpdb->posts}.ID = startmeta.post_id
			LEFT JOIN {$wpdb->postmeta} as endmeta ON {$wpdb->posts}.ID = endmeta.post_id
			LEFT JOIN {$wpdb->postmeta} as daymeta ON {$wpdb->posts}.ID = daymeta.post_id
			" . $product_meta_key_join . "

			WHERE post_type = 'wc_booking'
			AND post_status IN ( '" . implode( "','", array_map( 'esc_sql', $booking_statuses ) ) . "' )
			AND startmeta.meta_key = '_booking_start'
			AND endmeta.meta_key   = '_booking_end'
			AND daymeta.meta_key   = '_booking_all_day'
			" . $product_meta_key_q . "
			AND (
				(
					startmeta.meta_value < %s
					AND endmeta.meta_value > %s
					AND daymeta.meta_value = '0'
				)
				OR
				(
					startmeta.meta_value <= %s
					AND endmeta.meta_value >= %s
					AND daymeta.meta_value = '1'
				)
			)
		", date( 'YmdHis', $end_date ), date( 'YmdHis', $start_date ), date( 'Ymd000000', $end_date ), date( 'Ymd000000', $start_date ) ) );

		return $booking_ids;
	}

	/**
	 * Gets bookings for product ids and resource ids
	 * @param  array  $ids
	 * @param  array  $status
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_objects( $ids = array(), $status = array( 'confirmed', 'paid' ) ) {
		$transient_name = 'book_fo_' . md5( http_build_query( array( $ids, $status, WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );

		if ( false === ( $booking_ids = get_transient( $transient_name ) ) ) {
			$booking_ids = self::get_bookings_for_objects_query( $ids, $status );
			set_transient( $transient_name, $booking_ids, DAY_IN_SECONDS * 30 );
		}

		$bookings = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}

	/**
	 * Gets bookings for product ids and resource ids
	 * @param  array  $ids
	 * @param  array  $status
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_objects_query( $ids, $status ) {
		global $wpdb;

		$booking_ids = $wpdb->get_col( "
			SELECT ID FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta} as _booking_product_id ON {$wpdb->posts}.ID = _booking_product_id.post_id
			LEFT JOIN {$wpdb->postmeta} as _booking_resource_id ON {$wpdb->posts}.ID = _booking_resource_id.post_id
			WHERE post_type = 'wc_booking'
			AND post_status IN ('" . implode( "','", $status ) . "')
			AND _booking_product_id.meta_key = '_booking_product_id'
			AND _booking_resource_id.meta_key = '_booking_resource_id'
			AND (
				_booking_product_id.meta_value IN ('" . implode( "','", array_map( 'absint', $ids ) ) . "')
				OR _booking_resource_id.meta_value IN ('" . implode( "','", array_map( 'absint', $ids ) ) . "')
			)
		" );

		return $booking_ids;
	}

	/**
	 * Gets bookings for a resource
	 *
	 * @param  int $resource_id ID
	 * @param  array  $status
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_resource( $resource_id, $status = array( 'confirmed', 'paid' ) ) {
		$booking_ids = get_posts( array(
			'numberposts'   => -1,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_booking',
			'post_status'   => $status,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query' => array(
				array(
					'key'     => '_booking_resource_id',
					'value'   => absint( $resource_id )
				)
			)
		) );

		$bookings    = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}

	/**
	 * Gets bookings for a product by ID
	 *
	 * @param int $product_id The id of the product that we want bookings for
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_product( $product_id, $status = array( 'confirmed', 'paid' ) ) {
		$booking_ids = get_posts( array(
			'numberposts'   => -1,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_booking',
			'post_status'   => $status,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query' => array(
				array(
					'key'     => '_booking_product_id',
					'value'   => absint( $product_id )
				)
			)
		) );

		$bookings    = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}

	/**
	 * Get latest bookings
	 *
	 * @param int $numberitems Number of objects returned (default to unlimited)
	 * @param int $offset The number of objects to skip (as a query offset)
	 * @return array of WC_Booking objects
	 */
	public static function get_latest_bookings( $numberitems = -1, $offset = 0 ) {
		$booking_ids = get_posts( array(
			'numberposts' => $numberitems,
			'offset'      => $offset,
			'orderby'     => 'post_date',
			'order'       => 'DESC',
			'post_type'   => 'wc_booking',
			'post_status' => apply_filters( 'woocommerce_bookings_fully_booked_statuses', array(
																					'unpaid',
																					'pending-confirmation',
																					'confirmed',
																					'paid',
																					'complete',
																					'in-cart'
																					) ),
			'fields'      => 'ids',
		) );

		$bookings = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}

	/**
	 * Gets bookings for a user by ID
	 *
	 * @param int $user_id The id of the user that we want bookings for
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_user( $user_id ) {
		$booking_statuses = apply_filters( 'woocommerce_bookings_for_user_statuses', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
			'cancelled',
			'complete'
			) );

		$booking_ids = get_posts( array(
			'numberposts'   => -1,
			'offset'        => 0,
			'orderby'       => 'post_date',
			'order'         => 'DESC',
			'post_type'     => 'wc_booking',
			'post_status'   => $booking_statuses,
			'fields'        => 'ids',
			'no_found_rows' => true,
			'meta_query' => array(
				array(
					'key'     => '_booking_customer_id',
					'value'   => absint( $user_id ),
					'compare' => 'IN',
				)
			)
		) );

		$bookings    = array();

		foreach ( $booking_ids as $booking_id ) {
			$bookings[] = get_wc_booking( $booking_id );
		}

		return $bookings;
	}
}
