<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for the booking product type
 */
class WC_Product_Booking extends WC_Product {
	private $availability_rules = array();

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		$this->product_type = 'booking';
		parent::__construct( $product );
	}

	/**
	 * We want to sell bookings one at a time
	 * @return boolean
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Bookings can always be purchased regardless of price.
	 * @return boolean
	 */
	public function is_purchasable() {

		$purchasable = true;

		// Products must exist of course
		if ( ! $this->exists() ) {
			$purchasable = false;

		// Check that the product is published
		} elseif ( $this->post->post_status !== 'publish' && ! current_user_can( 'edit_post', $this->id ) ) {
			$purchasable = false;
		}

		return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
	}

	/**
	 * Get tje qty available to book per block.
	 * @return boolean
	 */
	public function get_qty() {
		return $this->wc_booking_qty ? absint( $this->wc_booking_qty ) : 1;
	}

	/**
	 * See if this booking product has persons enabled.
	 * @return boolean
	 */
	public function has_persons() {
		return $this->wc_booking_has_persons === 'yes';
	}

	/**
	 * See if this booking product has person types enabled.
	 * @return boolean
	 */
	public function has_person_types() {
		return $this->wc_booking_has_person_types === 'yes';
	}

	/**
	 * See if persons affect the booked qty
	 * @return boolean
	 */
	public function has_person_qty_multiplier() {
		return $this->has_persons() && $this->wc_booking_person_qty_multiplier === 'yes';
	}

	/**
	 * Get persons allowed per group
	 * @return int
	 */
	public function get_min_persons() {
		return absint( $this->wc_booking_min_persons_group );
	}

	/**
	 * Get persons allowed per group
	 * @return int
	 */
	public function get_max_persons() {
		return absint( $this->wc_booking_max_persons_group );
	}

	/**
	 * See if this booking product has reasources enabled.
	 * @return boolean
	 */
	public function has_resources() {
		return $this->wc_booking_has_resources === 'yes';
	}

	/**
	 * get duration
	 * @return string
	 */
	public function get_duration() {
		return $this->wc_booking_duration;
	}

	/**
	 * get duration
	 * @return string
	 */
	public function get_duration_unit() {
		return apply_filters( 'woocommerce_bookings_get_duration_unit', $this->wc_booking_duration_unit, $this );
	}

	/**
	 * Get duration type
	 * @return string
	 */
	public function get_duration_type() {
		return $this->wc_booking_duration_type;
	}

	/**
	 * Test duration type
	 * @return string
	 */
	public function is_duration_type( $type ) {
		return $this->wc_booking_duration_type === $type;
	}

	/**
	 * Get duration setting
	 * @return int
	 */
	public function get_min_duration() {
		return absint( $this->wc_booking_min_duration );
	}

	/**
	 * Get duration setting
	 * @return int
	 */
	public function get_max_duration() {
		return absint( $this->wc_booking_max_duration );
	}

	/**
	 * is_range_picker_enabled
	 * @return bool
	 */
	public function is_range_picker_enabled() {
		return "yes" === $this->wc_booking_enable_range_picker && 'day' === $this->get_duration_unit() && $this->is_duration_type( 'customer' ) && 1 == $this->get_duration();
	}

	/**
	 * The base cost will either be the 'base' cost or the base cost + cheapest resource
	 * @return string
	 */
	public function get_base_cost() {
		if ( '' !== $this->wc_display_cost ) {
			return $this->wc_display_cost;
		}

		$base = ( $this->wc_booking_base_cost * $this->get_min_duration() ) + $this->wc_booking_cost;

		if ( $this->has_resources() ) {
			$resources = $this->get_resources();
			$cheapest  = null;

			foreach ( $resources as $resource ) {
				if ( is_null( $cheapest ) || ( $resource->get_base_cost() + $resource->get_block_cost() ) < $cheapest ) {
					$cheapest = $resource->get_base_cost() + $resource->get_block_cost();
				}
			}
			$base += $cheapest;
		}
		if ( $this->has_persons() && $this->has_person_types() ) {
			$persons   = $this->get_person_types();
			$cheapest  = null;
			foreach ( $persons as $person ) {
				$cost = $person->cost * $person->min;
				if ( is_null( $cheapest ) || $cost < $cheapest ) {
					if ( $cost ) {
						$cheapest = $cost;
					}
				}
			}
			$base += $cheapest ? $cheapest : 0;
		}
		if ( $this->has_persons() && $this->get_min_persons() > 1 && $this->wc_booking_person_cost_multiplier === 'yes' ) {
			$base = $base * $this->get_min_persons();
		}

		return $base;
	}

	/**
	 * Return if booking has extra costs
	 * @return bool
	 */
	public function has_additional_costs() {
		$has_additional_costs = 'yes' === $this->has_additional_costs;

		if ( $this->has_persons() && 'yes' === $this->wc_booking_person_cost_multiplier ) {
			$has_additional_costs = true;
		}

		if ( $this->get_min_duration() > 1 && $this->wc_booking_base_cost ) {
			$has_additional_costs = true;
		}

		return $has_additional_costs;
	}

	/**
	 * Get product price
	 * @return string
	 */
	public function get_price() {
		return apply_filters( 'woocommerce_get_price', $this->price ? $this->price : $this->get_base_cost(), $this );
	}

	/**
	 * Get price HTML
	 * @return string
	 */
	public function get_price_html( $price = '' ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = $tax_display_mode == 'incl' ? $this->get_price_including_tax( 1, $this->get_base_cost() ) : $this->get_price_excluding_tax( 1, $this->get_base_cost() );

		if ( $display_price ) {
			if ( $this->has_additional_costs() ) {
				$price_html = sprintf( __( 'From: %s', 'woocommerce-bookings' ), wc_price( $display_price ) ) . $this->get_price_suffix();
			} else {
				$price_html = wc_price( $display_price ) . $this->get_price_suffix();
			}
		} elseif ( ! $this->has_additional_costs() ) {
			$price_html = __( 'Free', 'woocommerce-bookings' );
		} else {
			$price_html = '';
		}
		return apply_filters( 'woocommerce_get_price_html', $price_html, $this );
	}

	/**
	 * Find the minimum block's timestamp based on settings
	 * @return int
	 */
	public function get_min_timestamp_for_date( $start_date ) {
		if ( $min = $this->get_min_date() ) {
			$today    = ( date( 'y-m-d', $start_date ) === date( 'y-m-d', current_time( 'timestamp' ) ) );
			$timestamp = strtotime( ( $today ? '' : 'midnight ' ) . "+{$min['value']} {$min['unit']}", current_time( 'timestamp' ) );
		} else {
			$timestamp = current_time( 'timestamp' );
		}
		return $timestamp;
	}

	/**
	 * Get Min date
	 * @return array|bool
	 */
	public function get_min_date() {
		$min_date['value'] = ! empty( $this->wc_booking_min_date ) ? apply_filters( 'woocommerce_bookings_min_date_value', absint( $this->wc_booking_min_date ), $this->id ) : 0;
		$min_date['unit']  = ! empty( $this->wc_booking_min_date_unit ) ? apply_filters( 'woocommerce_bookings_min_date_unit', $this->wc_booking_min_date_unit, $this->id ) : 'month';
		if ( $min_date['value'] ) {
			return $min_date;
		}
		return false;
	}

	/**
	 * Get max date
	 * @return array
	 */
	public function get_max_date() {
		$max_date['value'] = ! empty( $this->wc_booking_max_date ) ? apply_filters( 'woocommerce_bookings_max_date_value', absint( $this->wc_booking_max_date ), $this->id ) : 1;
		$max_date['unit']  = ! empty( $this->wc_booking_max_date_unit ) ? apply_filters( 'woocommerce_bookings_max_date_unit', $this->wc_booking_max_date_unit, $this->id ) : 'month';
		if ( $max_date['value'] ) {
			return $max_date;
		}
		return false;
	}

	/**
	 * Get max year
	 * @return string
	 */
	private function get_max_year() {
		// Find max to get first
		$max_date = $this->get_max_date();
		$max_date_timestamp = strtotime( "+{$max_date['value']} {$max_date['unit']}" );
		$max_year = date( 'Y', $max_date_timestamp );
		if ( ! $max_year ) {
			$max_year = date( 'Y' );
		}
		return $max_year;
	}

	/**
	 * Get person type by ID
	 * @param  int $id
	 * @return WP_POST object
	 */
	public function get_person( $id ) {
		$id = absint( $id );

		if ( $id ) {
			$person = get_post( $id );

			if ( 'bookable_person' == $person->post_type && $person->post_parent == $this->id ) {
				return $person;
			}
		}

		return false;
	}

	/**
	 * Get all person types
	 * @return array of WP_Post objects
	 */
	public function get_person_types() {
		return get_posts( array(
			'post_parent'    => $this->id,
			'post_type'      => 'bookable_person',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'asc'
		) );
	}

	/**
	 * Get resource by ID
	 * @param  int $id
	 * @return WC_Product_Booking_Resource object
	 */
	public function get_resource( $id ) {
		global $wpdb;

		$id = absint( $id );

		if ( $id ) {
			$resource = get_post( $id );
			$relationship_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}wc_booking_relationships WHERE product_id = %d AND resource_id = %d", $this->id, $id ) );

			if ( is_object( $resource ) && $resource->post_type == 'bookable_resource' && 0 < $relationship_id ) {
				return new WC_Product_Booking_Resource( $resource, $this->id );
			}
		}

		return false;
	}

	/**
	 * How resources are assigned
	 * @return string customer or automatic
	 */
	public function is_resource_assignment_type( $type ) {
		return $this->wc_booking_resources_assignment === $type;
	}

	/**
	 * Get all resources
	 * @return array of WP_Post objects
	 */
	public function get_resources() {
		return wc_booking_get_product_resources( $this->id );
	}

	/**
	 * Get array of costs
	 *
	 * @return array
	 */
	public function get_costs() {
		return WC_Product_Booking_Rule_Manager::process_cost_rules( $this->wc_booking_pricing );
	}

	/**
	 * See if dates are by default bookable
	 * @return bool
	 */
	public function get_default_availability() {
		return $this->wc_booking_default_date_availability === 'available';
	}

	/**
	 * Checks if a product requires confirmation.
	 *
	 * @return bool
	 */
	public function requires_confirmation() {
		return apply_filters( 'woocommerce_booking_requires_confirmation', 'yes' === $this->wc_booking_requires_confirmation, $this );
	}

	/**
	 * See if the booking can be cancelled.
	 *
	 * @return boolean
	 */
	public function can_be_cancelled() {
		return apply_filters( 'woocommerce_booking_user_can_canel', 'yes' === $this->wc_booking_user_can_cancel, $this );
	}

	/**
	 * Get the add to cart button text for the single page
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {
		return 'yes' === $this->wc_booking_requires_confirmation ? apply_filters( 'woocommerce_booking_single_check_availability_text', __( 'Check Availability', 'woocommerce-bookings' ), $this ) : apply_filters( 'woocommerce_booking_single_add_to_cart_text', __( 'Book now', 'woocommerce-bookings' ), $this );
	}

	/**
	 * Return an array of resources which can be booked for a defined start/end date
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int $resource_id
	 * @param  integer $qty being booked
	 * @return bool|WP_ERROR if no blocks available, or int count of bookings that can be made, or array of available resources
	 */
	public function get_available_bookings( $start_date, $end_date, $resource_id = '', $qty = 1 ) {
		// Check the date is not in the past
		if ( date( 'Ymd', $start_date ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
			return false;
		}

		// Check we have a resource if needed
		$booking_resource = $resource_id ? $this->get_resource( $resource_id ) : null;

		if ( $this->has_resources() && ! is_numeric( $resource_id ) ) {
			return false;
		}

		$interval   = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();
		$min_date   = $this->get_min_date();
		$max_date   = $this->get_max_date();
		$check_from = strtotime( "midnight +{$min_date['value']} {$min_date['unit']}", current_time('timestamp') );
		$check_to   = strtotime( "+{$max_date['value']} {$max_date['unit']}", current_time('timestamp') );

		// Min max checks
		if ( 'month' === $this->get_duration_unit() ) {
			$check_to = strtotime( 'midnight', strtotime( date( 'Y-m-t', $check_to ) ) );
		}
		if ( $end_date < $check_from || $start_date > $check_to ) {
			return false;
		}

		// Get availability of each resource - no resource has been chosen yet
		if ( $this->has_resources() && ! $resource_id ) {
			return $this->get_all_resources_availability( $start_date, $end_date, $qty );
		// If we are checking for bookings for a specific resource, or have none...
		} else {
			$check_date     = $start_date;

			while ( $check_date < $end_date ) {
				if ( ! $this->check_availability_rules_against_date( $check_date, $resource_id ) ) {
					return false;
				}
				if ( 'start' === $this->wc_booking_check_availability_against ) {
					break; // Only need to check first day
				}
				$check_date = strtotime( "+1 day", $check_date );
			}

			if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) && ! $this->check_availability_rules_against_time( $start_date, $end_date, $resource_id ) ) {
				return false;
			}

			// Get blocks availability
			return $this->get_blocks_availability( $start_date, $end_date, $qty, $resource_id, $booking_resource );
		}
	}

	/**
	 * Get the availability of all resources
	 *
	 * @param string $start_date
	 * @param string $end_date
	 * @return array|WP_Error
	 */
	public function get_all_resources_availability( $start_date, $end_date, $qty ) {
		$resources           = $this->get_resources();
		$available_resources = array();

		foreach ( $resources as $resource ) {
			$availability = $this->get_available_bookings( $start_date, $end_date, $resource->ID, $qty );

			if ( $availability && ! is_wp_error( $availability ) ) {
				$available_resources[ $resource->ID ] = $availability;
			}
		}

		if ( empty( $available_resources ) ) {
			return new WP_Error( 'Error', __( 'This block cannot be booked.', 'woocommerce-bookings' ) );
		}

		return $available_resources;
	}

	/**
	 * Check the resources availability against all the blocks.
	 *
	 * @param  string $start_date
	 * @param  string $end_date
	 * @param  int    $qty
	 * @param  int    $resource_id
	 * @param  object $booking_resource
	 * @return string|WP_Error
	 */
	public function get_blocks_availability( $start_date, $end_date, $qty, $resource_id, $booking_resource ) {
		$blocks   = $this->get_blocks_in_range( $start_date, $end_date, '', $resource_id );
		$interval = 'hour' === $this->get_duration_unit() ? $this->get_duration() * 60 : $this->get_duration();

		if ( ! $blocks ) {
			return false;
		}

		/**
		 * Grab all existing bookings for the date range
		 * @var array
		 */
		$existing_bookings = $this->get_bookings_in_date_range( $start_date, $end_date, $resource_id );
		$available_qtys    = array();

		// Check all blocks availability
		foreach ( $blocks as $block ) {
			$available_qty       = $this->has_resources() && $booking_resource->has_qty() ? $booking_resource->get_qty() : $this->get_qty();
			$qty_booked_in_block = 0;

			foreach ( $existing_bookings as $existing_booking ) {
				if ( $existing_booking->is_booked_on_day( $block, strtotime( "+{$interval} minutes", $block ) ) ) {
					$qty_to_add = $this->has_person_qty_multiplier() ? max( 1, array_sum( $existing_booking->get_persons() ) ) : 1;

					if ( $this->has_resources() ) {
						if ( $existing_booking->get_resource_id() === absint( $resource_id ) || ( ! $booking_resource->has_qty() && $existing_booking->get_resource() && ! $existing_booking->get_resource()->has_qty() ) ) {
							$qty_booked_in_block += $qty_to_add;
						}
					} else {
						$qty_booked_in_block += $qty_to_add;
					}
				}
			}

			$available_qty = $available_qty - $qty_booked_in_block;

			// Remaining places are less than requested qty, return an error.
			if ( $available_qty < $qty ) {
				if ( in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) ) ) {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is %d place remaining', 'There are %d places remaining', $available_qty , 'woocommerce-bookings' ),
						$available_qty
					) );
				} elseif ( ! $available_qtys ) {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is %d place remaining on %s', 'There are %d places remaining on %s', $available_qty , 'woocommerce-bookings' ),
						$available_qty,
						date_i18n( wc_date_format(), $block )
					) );
				} else {
					return new WP_Error( 'Error', sprintf(
						_n( 'There is %d place remaining on %s', 'There are %d places remaining on %s', $available_qty , 'woocommerce-bookings' ),
						max( $available_qtys ),
						date_i18n( wc_date_format(), $block )
					) );
				}
			}

			$available_qtys[] = $available_qty;
		}

		return min( $available_qtys );
	}

	/**
	 * Get existing bookings in a given date range
	 *
	 * @param string $start-date
	 * @param string $end_date
	 * @param int    $resource_id
	 * @return array
	 */
	public function get_bookings_in_date_range( $start_date, $end_date, $resource_id = null ) {
		if ( $this->has_resources() && $resource_id ) {
			return WC_Bookings_Controller::get_bookings_in_date_range( $start_date, $end_date, $resource_id );
		} else {
			return WC_Bookings_Controller::get_bookings_in_date_range( $start_date, $end_date, $this->id );
		}
	}

	/**
	 * Get array of rules.
	 * @return array
	 */
	public function get_availability_rules( $for_resource = 0 ) {
		if ( empty( $this->availability_rules[ $for_resource ] ) ) {
			$this->availability_rules[ $for_resource ] = array();

			// Rule types
			$resource_rules = array();
			$product_rules  = $this->wc_booking_availability;
			$global_rules   = get_option( 'wc_global_booking_availability', array() );

			// Get availability of each resource - no resource has been chosen yet
			if ( $this->has_resources() && ! $for_resource ) {
				$resources      = $this->get_resources();
				$resource_rules = array();

				if ( $this->get_default_availability() ) {
					// If all blocks are available by default, we should not hide days if we don't know which resource is going to be used.
				} else {
					foreach ( $resources as $resource ) {
						$resource_rule = (array) get_post_meta( $resource->ID, '_wc_booking_availability', true );
						$resource_rules = array_merge( $resource_rules, $resource_rule );
					}
				}

			// Standard handling
			} elseif ( $for_resource ) {
				$resource_rules = (array) get_post_meta( $for_resource, '_wc_booking_availability', true );
			}

			// Merge and reverse order so lower rules are evaluated first
			$this->availability_rules[ $for_resource ] = array_filter( array_reverse( array_merge( WC_Product_Booking_Rule_Manager::process_availability_rules( $resource_rules ), WC_Product_Booking_Rule_Manager::process_availability_rules( $product_rules ), WC_Product_Booking_Rule_Manager::process_availability_rules( $global_rules ) ) ) );
		}
		return apply_filters( 'woocommerce_booking_get_availability_rules', $this->availability_rules[ $for_resource ], $for_resource, $this );
	}

	/**
	 * Check a date against the availability rules
	 * @param  string $check_date date to check
	 * @return bool available or not
	 */
	public function check_availability_rules_against_date( $check_date, $resource_id ) {
		$year        = date( 'Y', $check_date );
		$month       = absint( date( 'm', $check_date ) );
		$day         = absint( date( 'd', $check_date ) );
		$day_of_week = absint( date( 'N', $check_date ) );
		$week        = absint( date( 'W', $check_date ) );
		$bookable    = $this->get_default_availability();

		foreach ( $this->get_availability_rules( $resource_id ) as $rule ) {
			$type  = $rule[0];
			$rules = $rule[1];

			switch ( $type ) {
				case 'months' :
					if ( isset( $rules[ $month ] ) ) {
						$bookable = $rules[ $month ];
						break 2;
					}
				break;
				case 'weeks':
					if ( isset( $rules[ $week ] ) ) {
						$bookable = $rules[ $week ];
						break 2;
					}
				break;
				case 'days' :
					if ( isset( $rules[ $day_of_week ] ) ) {
						$bookable = $rules[ $day_of_week ];
						break 2;
					}
				break;
				case 'custom' :
					if ( isset( $rules[ $year ][ $month ][ $day ] ) ) {
						$bookable = $rules[ $year ][ $month ][ $day ];
						break 2;
					}
				break;
			}
		}

		return $bookable;
	}

	/**
	 * Check a time against the availability rules
	 * @param  string $start_time timestamp to check
	 * @param  string $end_time timestamp to check
	 * @return bool available or not
	 */
	public function check_availability_rules_against_time( $start_time, $end_time, $resource_id ) {
		$bookable   = $this->get_default_availability();
		$start_time = is_numeric( $start_time ) ? $start_time : strtotime( $start_time );
		$end_time   = is_numeric( $end_time ) ? $end_time : strtotime( $end_time );

		foreach ( $this->get_availability_rules( $resource_id ) as $rule ) {
			$type  = $rule[0];
			$rules = $rule[1];

			if ( strrpos( $type, 'time' ) === 0 ) {
				if ( ! empty( $rules['day'] ) ) {
					if ( $rules['day'] != date( 'N', $start_time ) ) {
						continue;
					}
				}

				$start_time_hi      = date( 'YmdHis', $start_time );
				$end_time_hi        = date( 'YmdHis', $end_time );
				$rule_start_time_hi = date( 'YmdHis', strtotime( $rules['from'], $start_time ) );
				$rule_end_time_hi   = date( 'YmdHis', strtotime( $rules['to'], $start_time ) );

				// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
				if ( $rule_end_time_hi <= $rule_start_time_hi ) {
					if ( $end_time_hi > $rule_start_time_hi ) {
						$bookable = $rules['rule'];
						break;
					}
					if ( $start_time_hi >= $rule_start_time_hi && $end_time_hi >= $rule_end_time_hi ) {
						$bookable = $rules['rule'];
						break;
					}
					if ( $start_time_hi <= $rule_start_time_hi && $end_time_hi <= $rule_end_time_hi ) {
						$bookable = $rules['rule'];
						break;
					}

				// Normal rule
				} else {
					if ( $start_time_hi >= $rule_start_time_hi && $end_time_hi <= $rule_end_time_hi ) {
						$bookable = $rules['rule'];
						break;
					}
				}
			}
		}

		return $bookable;
	}

	/**
	 * Get an array of blocks within in a specified date range - might be days, might be blocks within days, depending on settings.
	 * @return array
	 */
	public function get_blocks_in_range( $start_date, $end_date, $intervals = array(), $resource_id = 0, $booked = array() ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_booking_duration * 60 : $this->wc_booking_duration;
			$intervals        = array( $default_interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;

		$blocks = array();

		// For day, minute and hour blocks we need to loop through each day in the range
		if ( in_array( $this->get_duration_unit(), array( 'day', 'minute', 'hour' ) ) ) {
			$check_date = $start_date;

			// <= fixes https://github.com/woothemes/woocommerce-bookings/issues/325
			while ( $check_date <= $end_date ) {
				if ( in_array( $this->get_duration_unit(), array( 'day' ) ) && ! $this->check_availability_rules_against_date( $check_date, $resource_id ) ) {
					$check_date = strtotime( "+1 day", $check_date );
					continue;
				}

				$booking_resource = $resource_id ? $this->get_resource( $resource_id ) : null;
				$available_qty    = $this->has_resources() && $booking_resource && $booking_resource->has_qty() ? $booking_resource->get_qty() : $this->get_qty();

				// For mins and hours find valid blocks within THIS DAY ($check_date)
				if ( in_array( $this->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
					$first_block_time_minute = $this->wc_booking_first_block_time ? ( date( 'H', strtotime( $this->wc_booking_first_block_time ) ) * 60 ) + date( 'i', strtotime( $this->wc_booking_first_block_time ) ) : 0;
					$min_date                = $this->get_min_timestamp_for_date( $start_date );

					// Work out what minutes are actually bookable on this day
					$bookable_minutes = $this->get_default_availability() ? range( $first_block_time_minute, 1440 ) : array();
					$rules            = $this->get_availability_rules( $resource_id );

					// Since we evaluate all time rules and don't break out when one matches, reverse the array
					$rules            = array_reverse( $rules );

					foreach ( $rules as $rule ) {
						$type  = $rule[0];
						$rules = $rule[1];

						if ( strrpos( $type, 'time' ) === 0 ) {
							if ( ! empty( $rules['day'] ) ) {
								if ( $rules['day'] != date( 'N', $check_date ) ) {
									continue;
								}
							}
							$from_hour    = absint( date( 'H', strtotime( $rules['from'] ) ) );
							$from_min     = absint( date( 'i', strtotime( $rules['from'] ) ) );
							$to_hour      = absint( date( 'H', strtotime( $rules['to'] ) ) );
							$to_min       = absint( date( 'i', strtotime( $rules['to'] ) ) );
							$minute_range = array( ( $from_hour * 60 ) + $from_min, ( $to_hour * 60 ) + $to_min );
							$merge_ranges = array();

							if ( $minute_range[0] > $minute_range[1] ) {
								$merge_ranges[] = array( $minute_range[0], 1440 );
								$merge_ranges[] = array( 0, $minute_range[1] );
							} else {
								$merge_ranges[] = array( $minute_range[0], $minute_range[1] );
							}

							foreach ( $merge_ranges as $range ) {
								if ( $bookable = $rules['rule'] ) {
									// If this time range is bookable, add to bookable minutes
									$bookable_minutes = array_merge( $bookable_minutes, range( $range[0], $range[1] ) );
								} else {
									// If this time range is not bookable, remove from bookable minutes
									$bookable_minutes = array_diff( $bookable_minutes, range( $range[0] + 1, $range[1] - 1 ) );
								}
							}
						}
					}

					$bookable_minutes = array_unique( $bookable_minutes );
					sort( $bookable_minutes );

					// Break bookable minutes into sequences - bookings cannot have breaks
					$bookable_minute_blocks     = array();
					$bookable_minute_block_from = current( $bookable_minutes );

					foreach ( $bookable_minutes as $key => $minute ) {
						if ( isset( $bookable_minutes[ $key + 1 ] ) ) {
							if ( $bookable_minutes[ $key + 1 ] - 1 === $minute ) {
								continue;
							} else {
								// There was a break in the sequence
								$bookable_minute_blocks[]   = array( $bookable_minute_block_from, $minute );
								$bookable_minute_block_from = $bookable_minutes[ $key + 1 ];
							}
						} else {
							// We're at the end of the bookable minutes
							$bookable_minute_blocks[] = array( $bookable_minute_block_from, $minute );
						}
					}

					// Create an array of already booked blocks
					$booked_blocks = array();

					foreach( $booked as $booked_block ) {
						for ( $i = $booked_block[0]; $i < $booked_block[1]; $i += 60 ) {
							array_push( $booked_blocks, $i );
						}
					}

					$booked_blocks    = array_count_values( $booked_blocks );

					// Loop the blocks of bookable minutes and add a block if there is enough room to book
					foreach ( $bookable_minute_blocks as $time_block ) {
						$time_block_start        = strtotime( "midnight +{$time_block[0]} minutes", $check_date );
						$minutes_in_block        = $time_block[1] - $time_block[0];
						$base_intervals_in_block = floor( $minutes_in_block / $base_interval );

						for ( $i = 0; $i < $base_intervals_in_block; $i ++ ) {
							$from_interval = $i * $base_interval;
							$start_time    = strtotime( "+{$from_interval} minutes", $time_block_start );

							// Break if start time is after the end date being calced, or if the start time is on the next day
							if ( $start_time > $end_date || date( 'ymd', $start_time ) !== date( 'ymd', $start_date )  ) {
								break 2;
							}

							// Must be in the future
							if ( $start_time <= $min_date || $start_time <= current_time( 'timestamp' ) ) {
								continue;
							}

							if ( isset( $booked_blocks[ $start_time ] ) && $booked_blocks[ $start_time ] >= $available_qty ) {
								continue;
							}

							// make sure minute & hour blocks are not past minimum & max booking settings
							$product_min_date = $this->get_min_date();
							$product_max_date = $this->get_max_date();
							$min_check_from   = strtotime( "+{$product_min_date['value']} {$product_min_date['unit']}", current_time( 'timestamp' ) );
							$max_check_to     = strtotime( "+{$product_max_date['value']} {$product_max_date['unit']}", current_time( 'timestamp' ) );

							if ( $end_date < $min_check_from || $start_time > $max_check_to ) {
								continue;
							}

							/**
							 * Ensure block can fit the entire user set interval
							 * @note Known issue - hour blocks cannot span multi-days
							 */
							$to_interval         = $from_interval + $interval;
							$end_time            = strtotime( "+{$to_interval} minutes", $time_block_start );
							$time_block_end_time = strtotime( "midnight +{$time_block[1]} minutes", $check_date );
							$loop_time           = $start_time;

							// This checks all minutes in block for availability
							while ( $loop_time < $end_time ) {
								if ( isset( $booked_blocks[ $loop_time ] ) && $booked_blocks[ $loop_time ] >= $available_qty ) {
									continue 2;
								}
								$loop_time = $loop_time + 60;
							}

							if ( $end_time > $time_block_end_time ) {
								continue;
							}

							$blocks[] = $start_time;
						}
					}

				// For days, the day is the block so we can just count the already booked blocks rather than check their block times
				} else {
					if ( sizeof( $booked ) < $available_qty ) {
						$blocks[] = $check_date;
					}
				}

				// Check next day
				$check_date = strtotime( "+1 day", $check_date );
			}

		// For months, loop each month in the range to find blocks
		} elseif ( 'month' === $this->get_duration_unit() ) {

			// Generate a range of blocks for months
			$from       = strtotime( date( 'Y-m-01', $start_date ) );
			$to         = strtotime( date( 'Y-m-t', $end_date ) );
			$month_diff = 0;
			$month_from = $from;

			while ( ( $month_from = strtotime( "+1 MONTH", $month_from ) ) <= $to ) {
			    $month_diff ++;
			}

			for ( $i = 0; $i <= $month_diff; $i ++ ) {
				$year  = date( 'Y', ( $i ? strtotime( "+ {$i} month", $from ) : $from ) );
				$month = date( 'n', ( $i ? strtotime( "+ {$i} month", $from ) : $from ) );

				if ( ! $this->check_availability_rules_against_date( strtotime( "{$year}-{$month}-01" ), $resource_id ) ) {
					continue;
				}

				$blocks[] = strtotime( "+ {$i} month", $from );
			}

		}
		return $blocks;
	}

	/**
	 * Returns available blocks from a range of blocks by looking at existing bookings.
	 * @param  array   $blocks      The blocks we'll be checking availability for.
	 * @param  array   $intervals   Array containing 2 items; the interval of the block (maybe user set), and the base interval for the block/product.
	 * @param  integer $resource_id Resource we're getting blocks for. Falls backs to product as a whole if 0.
	 * @return array The available blocks array
	 */
	public function get_available_blocks( $blocks, $intervals = array(), $resource_id = 0 ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_booking_duration * 60 : $this->wc_booking_duration;
			$intervals        = array( $default_interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;

		$available_blocks   = array();
		$start_date         = current( $blocks );
		$end_date           = end( $blocks );
		$base_interval_unit = in_array( $this->get_duration_unit(), array( 'hour', 'minute' ) ) ? 'mins' : $this->get_duration_unit();

		if ( ! empty( $blocks ) ) {
			/**
			 * Grab all existing bookings for the date range.
			 * Extend end_date to end of last block. Note: base_interval is in minutes.
			 * @var array
			 */
			$existing_bookings = $this->get_bookings_in_date_range( $start_date, $end_date + ( $base_interval * 60 ), $resource_id );

			// Resources booked array. Resource can be a "resource" but also just a booking if it has no resources
			$resources_booked = array( 0 => array() );

			// Loop all existing bookings
			foreach ( $existing_bookings as $booking ) {
				$booking_resource_id = $booking->get_resource_id();

				// prepare resource array for resource id
				$resources_booked[ $booking_resource_id ] = isset( $resources_booked[ $booking_resource_id ] ) ? $resources_booked[ $booking_resource_id ] : array();

				// if person multiplier is on we should disable stuff where nothing is avalible
				$repeat = max( 1, $this->has_person_qty_multiplier() && $booking->has_persons() ? $booking->get_persons_total() : 1 );

				for ( $i = 0; $i < $repeat; $i++ ) {
					array_push( $resources_booked[ $booking_resource_id ], array( $booking->start, $booking->end ) );
				}
			}

			// Generate arrays that contain information about what blocks to unset
			if ( $this->has_resources() && ! $resource_id ) {
				$resources       = $this->get_resources();
				$available_times = array();

				// Loop all resources
				foreach ( $resources as $resource ) {
					$times           = $this->get_blocks_in_range( $start_date, $end_date, array( $interval, $base_interval ), $resource->ID, isset( $resources_booked[ $resource->ID ] ) ? $resources_booked[ $resource->ID ] : array() );
					$available_times = array_merge( $available_times, $times );
				}
			} else {
				$available_times = $this->get_blocks_in_range( $start_date, $end_date, array( $interval, $base_interval ), $resource_id, isset( $resources_booked[ $resource_id ] ) ? $resources_booked[ $resource_id ] : $resources_booked[ 0 ] );
			}

			// Count booked times then loop the blocks
			$available_times = array_count_values( $available_times );

			// Loop through all blocks and unset if they are allready booked
			foreach ( $blocks as $block ) {
				if ( isset( $available_times[ $block ] ) ) {
					$available_blocks[] = $block;
				}
			}
		}

		return $available_blocks;
	}

	/**
	 * Find available blocks and return HTML for the user to choose a block. Used in class-wc-bookings-ajax.php
	 * @param  array  $blocks
	 * @param  array  $intervals
	 * @param  integer $resource_id
	 * @return string
	 */
	public function get_available_blocks_html( $blocks, $intervals = array(), $resource_id = 0 ) {
		if ( empty( $intervals ) ) {
			$default_interval = 'hour' === $this->get_duration_unit() ? $this->wc_booking_duration * 60 : $this->wc_booking_duration;
			$intervals        = array( $default_interval, $default_interval );
		}

		list( $interval, $base_interval ) = $intervals;

		$blocks            = $this->get_available_blocks( $blocks, $intervals, $resource_id );
		$existing_bookings = $this->get_bookings_in_date_range( current( $blocks ), end( $blocks ), $resource_id );
		$booking_resource  = $resource_id ? $this->get_resource( $resource_id ) : null;
		$block_html        = '';

		foreach ( $blocks as $block ) {
			$available_qty       = $this->has_resources() && $booking_resource && $booking_resource->has_qty() ? $booking_resource->get_qty() : $this->get_qty();
			$qty_booked_in_block = 0;

			foreach ( $existing_bookings as $existing_booking ) {
				if ( $existing_booking->is_within_block( $block, strtotime( "+{$interval} minutes", $block ) ) ) {
					$qty_to_add = $this->has_person_qty_multiplier() ? max( 1, array_sum( $existing_booking->get_persons() ) ) : 1;

					if ( $this->has_resources() ) {
						if ( $existing_booking->get_resource_id() === absint( $resource_id ) || ( ( is_null( $booking_resource ) || ! $booking_resource->has_qty() ) && $existing_booking->get_resource() && ! $existing_booking->get_resource()->has_qty() ) ) {
							$qty_booked_in_block += $qty_to_add;
						}
					} else {
						$qty_booked_in_block += $qty_to_add;
					}
				}
			}

			$available_qty = $available_qty - $qty_booked_in_block;

			if ( $qty_booked_in_block ) {
				$block_html .= '<li class="block" data-block="' . esc_attr( date( 'Hi', $block ) ) . '"><a href="#" data-value="' . date( 'G:i', $block ) . '">' . date_i18n( get_option( 'time_format' ), $block ) . ' <small class="booking-spaces-left">(' . sprintf( _n( '%d left', '%d left', $available_qty, 'woocommerce-bookings' ), absint( $available_qty ) ) . ')</small></a></li>';
			} else {
				$block_html .= '<li class="block" data-block="' . esc_attr( date( 'Hi', $block ) ) . '"><a href="#" data-value="' . date( 'G:i', $block ) . '">' . date_i18n( get_option( 'time_format' ), $block ) . '</a></li>';
			}
		}

		return $block_html;
	}
}
