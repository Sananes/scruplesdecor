<?php
/**
 * Class dependencies
 */
if ( ! class_exists( 'WC_Booking_Form_Picker' ) ) {
	include_once( 'class-wc-booking-form-picker.php' );
}

/**
 * Date Picker class
 */
class WC_Booking_Form_Date_Picker extends WC_Booking_Form_Picker {

	private $field_type = 'date-picker';
	private $field_name = 'start_date';

	/**
	 * Constructor
	 * @param object $booking_form The booking form which called this picker
	 */
	public function __construct( $booking_form ) {
		$this->booking_form                    = $booking_form;
		$this->args                            = array();
		$this->args['type']                    = $this->field_type;
		$this->args['name']                    = $this->field_name;
		$this->args['min_date']                = $this->booking_form->product->get_min_date();
		$this->args['max_date']                = $this->booking_form->product->get_max_date();
		$this->args['default_availability']    = $this->booking_form->product->get_default_availability();
		$this->args['min_date_js']             = $this->get_min_date();
		$this->args['max_date_js']             = $this->get_max_date();
		$this->args['duration_type']           = $this->booking_form->product->get_duration_type();
		$this->args['is_range_picker_enabled'] = $this->booking_form->product->is_range_picker_enabled();
		$this->args['display']                 = $this->booking_form->product->wc_booking_calendar_display_mode;
		$this->args['availability_rules']      = array();
		$this->args['availability_rules'][0]   = $this->booking_form->product->get_availability_rules();
		$this->args['label']                   = $this->get_field_label( __( 'Date', 'woocommerce-bookings' ) );
		$this->args['default_date']            = date( 'Y-m-d', $this->get_default_date() );

		if ( $this->booking_form->product->has_resources() ) {
			foreach ( $this->booking_form->product->get_resources() as $resource ) {
				$this->args['availability_rules'][ $resource->ID ] = $this->booking_form->product->get_availability_rules( $resource->ID );
			}
		}

		$this->find_fully_booked_blocks();
	}

	/**
	 * Attempts to find what date to default to in the date picker
	 * by looking at the fist available block. Otherwise, the current date is used.
	 */
	function get_default_date() {
		$now = strtotime( 'midnight', current_time( 'timestamp' ) );
		$min = $this->booking_form->product->get_min_date();
		if ( empty ( $min ) ) {
			$min_date = strtotime( 'midnight' );
		} else {
			$min_date = $max_date = strtotime( "+{$min['value']} {$min['unit']}", $now );
		}
		$max = $this->booking_form->product->get_max_date();
		$max_date = strtotime( "+{$max['value']} {$max['unit']}", $now );

		$blocks_in_range  = $this->booking_form->product->get_blocks_in_range( $min_date, $max_date );
		$available_blocks = $this->booking_form->product->get_available_blocks( $blocks_in_range );

		if ( empty( $available_blocks[0] ) ) {
			return strtotime( 'midnight' );
		} else {
			return $available_blocks[0];
		}
	}

	/**
	 * Finds days which are fully booked already so they can be blocked on the date picker
	 * @return array()
	 */
	protected function find_fully_booked_blocks() {
		// Bare existing bookings into consideration for datepicker
		$fully_booked_days     = array();
		$partially_booked_days = array();
		$find_bookings_for     = array( $this->booking_form->product->id );
		$resource_count        = 0;

		if ( $this->booking_form->product->has_resources() ) {
			foreach (  $this->booking_form->product->get_resources() as $resource ) {
				$find_bookings_for[] = $resource->ID;
				$resource_count ++;
			}
		}

		$booking_statuses = apply_filters( 'woocommerce_bookings_fully_booked_statuses', array(
			'unpaid',
			'pending-confirmation',
			'confirmed',
			'paid',
			'complete',
			'in-cart'
			) );

		$existing_bookings  = WC_Bookings_Controller::get_bookings_for_objects( $find_bookings_for, $booking_statuses );

		// Is today fully booked/no longer available?
		$blocks_in_range  = $this->booking_form->product->get_blocks_in_range( strtotime( 'midnight' ), strtotime( 'tomorrow midnight' ) );
		$available_blocks = $this->booking_form->product->get_available_blocks( $blocks_in_range );

		if ( sizeof( $available_blocks ) < sizeof( $blocks_in_range ) ) {
			$partially_booked_days[ date( 'Y-n-j' ) ][0] = true;
		}

		if ( ! $available_blocks ) {
			$fully_booked_days[ date( 'Y-n-j' ) ][0] = true;
		}

		// Use the existing bookings to find days which are fully booked
		foreach ( $existing_bookings as $existing_booking ) {
			$start_date  = $existing_booking->start;
			$end_date    = $existing_booking->is_all_day() ? strtotime( 'tomorrow midnight', $existing_booking->end ) : $existing_booking->end;
			$resource_id = $existing_booking->get_resource_id();
			$check_date  = $start_date; // Take it from the top

			// Loop over all booked days in this booking
			while ( $check_date < $end_date ) {
				$js_date = date( 'Y-n-j', $check_date );

				if ( $check_date < current_time( 'timestamp' ) ) {
					$check_date = strtotime( "+1 day", $check_date );
					continue;
				}

				if ( $this->booking_form->product->has_resources() ) {

					// Skip if we've already found this resource is unavailable
					if ( ! empty( $fully_booked_days[ $js_date ][ $resource_id ] ) ) {
						$check_date = strtotime( "+1 day", $check_date );
						continue;
					}

					$blocks_in_range  = $this->booking_form->product->get_blocks_in_range( strtotime( 'midnight', $check_date ), strtotime( 'tomorrow midnight', $check_date ), array(), $resource_id );
					$available_blocks = $this->booking_form->product->get_available_blocks( $blocks_in_range, array(), $resource_id );

					if ( sizeof( $available_blocks ) < sizeof( $blocks_in_range ) ) {
						$partially_booked_days[ $js_date ][ $resource_id ] = true;

						if ( 1 === $resource_count || sizeof( $partially_booked_days[ $js_date ] ) === $resource_count ) {
							$partially_booked_days[ $js_date ][0] = true;
						}
					}

					if ( ! $available_blocks ) {
						$fully_booked_days[ $js_date ][ $resource_id ] = true;

						if ( 1 === $resource_count || sizeof( $fully_booked_days[ $js_date ] ) === $resource_count ) {
							$fully_booked_days[ $js_date ][0] = true;
						}
					}

				} else {

					// Skip if we've already found this product is unavailable
					if ( ! empty( $fully_booked_days[ $js_date ] ) ) {
						$check_date = strtotime( "+1 day", $check_date );
						continue;
					}

					$blocks_in_range  = $this->booking_form->product->get_blocks_in_range( strtotime( 'midnight', $check_date ), strtotime( 'tomorrow midnight -1 min', $check_date ) );
					$available_blocks = $this->booking_form->product->get_available_blocks( $blocks_in_range );

					if ( sizeof( $available_blocks ) < sizeof( $blocks_in_range ) ) {
						$partially_booked_days[ $js_date ][0] = true;
					}

					if ( ! $available_blocks ) {
						$fully_booked_days[ $js_date ][0] = true;
					}
				}
				$check_date = strtotime( "+1 day", $check_date );
			}
		}

		$this->args['partially_booked_days'] = $partially_booked_days;
		$this->args['fully_booked_days']     = $fully_booked_days;
	}
}