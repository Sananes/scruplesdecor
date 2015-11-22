<?php
/**
 * Booking form class
 */
class WC_Booking_Form {

	/**
	 * Booking product data.
	 * @var WC_Product_Booking
	 */
	public $product;

	/**
	 * Booking fields.
	 * @var array
	 */
	private $fields;

	/**
	 * Constructor
	 * @param $product WC_Product_Booking
	 */
	public function __construct( $product ) {
		$this->product = $product;
	}

	/**
	 * Booking form scripts
	 */
	public function scripts() {
		global $wp_locale;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$wc_bookings_booking_form_args = array(
			'closeText'                  => __( 'Close', 'woocommerce-bookings' ),
			'currentText'                => __( 'Today', 'woocommerce-bookings' ),
			'monthNames'                 => array_values( $wp_locale->month ),
			'monthNamesShort'            => array_values( $wp_locale->month_abbrev ),
			'dayNames'                   => array_values( $wp_locale->weekday ),
			'dayNamesShort'              => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'                => array_values( $wp_locale->weekday_initial ),
			'firstDay'                   => get_option( 'start_of_week' ),
			'current_time'               => date( 'Ymd', current_time( 'timestamp' ) ),
			'check_availability_against' => $this->product->wc_booking_check_availability_against,
			'duration_unit'              => $this->product->wc_booking_duration_unit
		);

		if ( in_array( $this->product->wc_booking_duration_unit, array( 'minute', 'hour' ) ) ) {
			$wc_bookings_booking_form_args['booking_duration'] = 1;
		} else {
			$wc_bookings_booking_form_args['booking_duration']        = $this->product->wc_booking_duration;
			$wc_bookings_booking_form_args['booking_duration_type']   = $this->product->get_duration_type();

			if ( 'customer' == $wc_bookings_booking_form_args['booking_duration_type'] ) {
				$wc_bookings_booking_form_args['booking_min_duration'] = $this->product->get_min_duration();
				$wc_bookings_booking_form_args['booking_max_duration'] = $this->product->get_max_duration();
			} else {
				$wc_bookings_booking_form_args['booking_min_duration'] = $wc_bookings_booking_form_args['booking_duration'];
				$wc_bookings_booking_form_args['booking_max_duration'] = $wc_bookings_booking_form_args['booking_duration'];
			}
		}

		wp_enqueue_script( 'wc-bookings-booking-form', WC_BOOKINGS_PLUGIN_URL . '/assets/js/booking-form' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), WC_BOOKINGS_VERSION, true );
		wp_localize_script( 'wc-bookings-booking-form', 'wc_bookings_booking_form', $wc_bookings_booking_form_args );
		wp_register_script( 'wc-bookings-date-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/date-picker' . $suffix . '.js', array( 'wc-bookings-booking-form', 'jquery-ui-datepicker' ), WC_BOOKINGS_VERSION, true );
		wp_register_script( 'wc-bookings-month-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/month-picker' . $suffix . '.js', array( 'wc-bookings-booking-form' ), WC_BOOKINGS_VERSION, true );
		wp_register_script( 'wc-bookings-time-picker', WC_BOOKINGS_PLUGIN_URL . '/assets/js/time-picker' . $suffix . '.js', array( 'wc-bookings-booking-form' ), WC_BOOKINGS_VERSION, true );

		// Variables for JS scripts
		$booking_form_params = array(
			'ajax_url'              => WC()->ajax_url(),
			'i18n_date_unavailable' => __( 'This date is unavailable', 'woocommerce-bookings' ),
			'i18n_start_date'       => __( 'Choose a Start Date', 'woocommerce-bookings' ),
			'i18n_end_date'         => __( 'Choose an End Date', 'woocommerce-bookings' ),
			'i18n_dates'            => __( 'Dates', 'woocommerce-bookings' ),
			'i18n_choose_options'   => __( 'Please select the options for your booking above first', 'woocommerce-bookings' ),
		);

		wp_localize_script( 'wc-bookings-booking-form', 'booking_form_params', apply_filters( 'booking_form_params', $booking_form_params ) );
	}

	/**
	 * Prepare fields for the booking form
	 */
	public function prepare_fields() {
		// Destroy existing fields
		$this->reset_fields();

		// Add fields in order
		$this->duration_field();
		$this->persons_field();
		$this->resources_field();
		$this->date_field();

		$this->fields = apply_filters( 'booking_form_fields', $this->fields );
	}

	/**
	 * Reset fields array
	 */
	public function reset_fields() {
		$this->fields = array();
	}

	/**
	 * Add duration field to the form
	 */
	private function duration_field() {
		// Customer defined bookings
		if ( 'customer' == $this->product->wc_booking_duration_type ) {
			$after = '';
			$type  = '';
			switch ( $this->product->wc_booking_duration_unit ) {
				case 'month' :
					if ( $this->product->wc_booking_duration > 1 ) {
						$after = sprintf( __( '&times; %s Months', 'woocommerce-bookings' ), $this->product->wc_booking_duration );
					} else {
						$after = __( 'Month(s)', 'woocommerce-bookings' );
					}
					break;
				case 'week' :
					if ( $this->product->wc_booking_duration > 1 ) {
						$after = sprintf( __( '&times; %s weeks', 'woocommerce-bookings' ), $this->product->wc_booking_duration );
					} else {
						$after = __( 'Week(s)', 'woocommerce-bookings' );
					}
					break;
				case 'day' :
					if ( $this->product->wc_booking_duration % 7 ) {
						if ( $this->product->wc_booking_duration > 1 ) {
							$after = sprintf( __( '&times; %s days', 'woocommerce-bookings' ), $this->product->wc_booking_duration );
						} else {
							$after = __( 'Day(s)', 'woocommerce-bookings' );
						}
					} else {
						if ( $this->product->wc_booking_duration / 7 == 1 ) {
							$after = __( 'Week(s)', 'woocommerce-bookings' );
						} else {
							$after = sprintf( __( '&times; %s weeks', 'woocommerce-bookings' ), $this->product->wc_booking_duration / 7 );
						}
					}
					break;
				case 'hour' :
					if ( $this->product->wc_booking_duration > 1 ) {
						$after = sprintf( __( '&times; %s hours', 'woocommerce-bookings' ), $this->product->wc_booking_duration );
					} else {
						$after = __( 'Hour(s)', 'woocommerce-bookings' );
					}
					break;
				case 'minute' :
					if ( $this->product->wc_booking_duration > 1 ) {
						$after = sprintf( __( '&times; %s minutes', 'woocommerce-bookings' ), $this->product->wc_booking_duration );
					} else {
						$after = __( 'Minute(s)', 'woocommerce-bookings' );
					}
					break;
			}

			$this->add_field( array(
				'type'  => 'number',
				'name'  => 'duration',
				'label' => __( 'Duration', 'woocommerce-bookings' ),
				'after' => $after,
				'min'   => $this->product->wc_booking_min_duration,
				'max'   => $this->product->wc_booking_max_duration,
				'step'  => 1
			) );
		}
	}

	/**
	 * Add persons field
	 */
	private function persons_field() {
		// Persons field
		if ( $this->product->has_persons() ) {
			if ( $this->product->has_person_types() ) {
				$person_types = $this->product->get_person_types();

				foreach ( $person_types as $person_type ) {
					$min_person_type_persons = get_post_meta( $person_type->ID, 'min', true );
					$max_person_type_persons = get_post_meta( $person_type->ID, 'max', true );

					$this->add_field( array(
						'type'  => 'number',
						'step'  => 1,
						'min'   => is_numeric( $min_person_type_persons ) ? $min_person_type_persons : 0,
						'max'   => ! empty( $max_person_type_persons ) ? absint( $max_person_type_persons ) : $this->product->wc_booking_max_persons_group,
						'name'  => 'persons_' . $person_type->ID,
						'label' => $person_type->post_title,
						'after' => $person_type->post_excerpt
					) );
				}
			} else {
				$this->add_field( array(
					'type'  => 'number',
					'step'  => 1,
					'min'   => $this->product->get_min_persons(),
					'max'   => $this->product->get_max_persons() ? $this->product->get_max_persons() : '',
					'name'  => 'persons',
					'label' => __( 'Persons', 'woocommerce-bookings' )
				) );
			}
		}
	}

	/**
	 * Add resources field
	 */
	private function resources_field() {
		// Resources field
		if ( 'yes' == $this->product->wc_booking_has_resources && 'customer' == $this->product->wc_booking_resources_assignment ) {

			$resources          = $this->product->get_resources();
			$resource_options   = array();
			$data               = array();

			foreach ( $resources as $resource ) {
				$additional_cost = '';
				$cost_plus_base  = ( $resource->get_base_cost() + $resource->get_block_cost() + $this->product->wc_booking_base_cost + $this->product->wc_booking_cost );
				$additional_cost = array();

				if ( $resource->get_base_cost() && $this->product->get_base_cost() < $cost_plus_base ) {
					$additional_cost[] = '+' . wc_price( $cost_plus_base - $this->product->get_base_cost() );
				}

				if ( $resource->get_block_cost() ) {
					switch ( $this->product->get_duration_unit() ) {
						case 'month' :
							$additional_cost[] = sprintf( __( '+%s per month', 'woocommerce-bookings' ), wc_price( $resource->get_block_cost() + $this->product->wc_booking_base_cost ) );
							break;
						case 'day' :
							$additional_cost[] = sprintf( __( '+%s per day', 'woocommerce-bookings' ), wc_price( $resource->get_block_cost() + $this->product->wc_booking_base_cost ) );
							break;
						default :
							$additional_cost[] = sprintf( __( '+%s per block', 'woocommerce-bookings' ), wc_price( $resource->get_block_cost() + $this->product->wc_booking_base_cost ) );
							break;
					}
				}

				if ( $additional_cost ) {
					$additional_cost_string = ' (' . implode( ', +', $additional_cost ) . ')';
				} else {
					$additional_cost_string = '';
				}

				$resource_options[ $resource->ID ] = $resource->post_title . apply_filters( 'woocommerce_bookings_resource_additional_cost_string', $additional_cost_string, $resource );
			}

			$this->add_field( array(
				'type'    => 'select',
				'name'    => 'resource',
				'label'   => $this->product->wc_booking_resouce_label ? $this->product->wc_booking_resouce_label : __( 'Type', 'woocommerce-bookings' ),
				'class'   => array( 'wc_booking_field_' . sanitize_title( $this->product->wc_booking_resouce_label ) ),
				'options' => $resource_options
			) );
		}
	}

	/**
	 * Add the date field to the booking form
	 */
	private function date_field() {
		$picker = null;

		// Get date picker specific to the duration unit for this product
		switch ( $this->product->get_duration_unit() ) {
			case 'month' :
				include_once( 'class-wc-booking-form-month-picker.php' );
				$picker = new WC_Booking_Form_Month_Picker( $this );
				break;
			case 'day' :
				include_once( 'class-wc-booking-form-date-picker.php' );
				$picker = new WC_Booking_Form_Date_Picker( $this );
				break;
			case 'minute' :
			case 'hour' :
				include_once( 'class-wc-booking-form-datetime-picker.php' );
				$picker = new WC_Booking_Form_Datetime_Picker( $this );
				break;
			default :
				break;
		}

		if ( ! is_null( $picker ) ) {
			$this->add_field( $picker->get_args() );
		}
	}

	/**
	 * Add Field
	 * @param  array $field
	 * @return void
	 */
	public function add_field( $field ) {
		$default = array(
			'name'  => '',
			'class' => array(),
			'label' => '',
			'type'  => 'text'
		);

		$field = wp_parse_args( $field, $default );

		if ( ! $field['name'] || ! $field['type'] ) {
			return;
		}

		$nicename = 'wc_bookings_field_' . sanitize_title( $field['name'] );

		$field['name']    = $nicename;
		$field['class'][] = $nicename;

		$this->fields[ sanitize_title( $field['name'] ) ] = $field;
	}

	/**
	 * Output the form - called from the add to cart templates
	 */
	public function output() {
		$this->scripts();
		$this->prepare_fields();

		foreach ( $this->fields as $key => $field ) {
			wc_get_template( 'booking-form/' . $field['type'] . '.php', array( 'field' => $field ), 'woocommerce-bookings', WC_BOOKINGS_TEMPLATE_PATH );
		}
	}

	/**
	 * Get posted form data into a neat array
	 * @param  array $posted
	 * @return array
	 */
	public function get_posted_data( $posted = array() ) {
		if ( empty( $posted ) ) {
			$posted = $_POST;
		}

		$data = array(
			'_year'    => '',
			'_month'   => '',
			'_day'     => '',
			'_persons' => array()
		);

		// Get date fields (y, m, d)
		if ( ! empty( $posted['wc_bookings_field_start_date_year'] ) && ! empty( $posted['wc_bookings_field_start_date_month'] ) && ! empty( $posted['wc_bookings_field_start_date_day'] ) ) {
			$data['_year']  = absint( $posted['wc_bookings_field_start_date_year'] );
			$data['_year']  = $data['_year'] ? $data['_year'] : date('Y');
			$data['_month'] = absint( $posted['wc_bookings_field_start_date_month'] );
			$data['_day']   = absint( $posted['wc_bookings_field_start_date_day'] );
			$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
			$data['date']   = date_i18n( wc_date_format(), strtotime( $data['_date'] ) );
		}

		// Get year month field
		if ( ! empty( $posted['wc_bookings_field_start_date_yearmonth'] ) ) {
			$yearmonth      = strtotime( $posted['wc_bookings_field_start_date_yearmonth'] . '-01' );
			$data['_year']  = absint( date( 'Y', $yearmonth ) );
			$data['_month'] = absint( date( 'm', $yearmonth ) );
			$data['_day']   = 1;
			$data['_date']  = $data['_year'] . '-' . $data['_month'] . '-' . $data['_day'];
			$data['date']   = date_i18n( 'F Y', $yearmonth );
		}

		// Get time field
		if ( ! empty( $posted['wc_bookings_field_start_date_time'] ) ) {
			$data['_time'] = wc_clean( $posted['wc_bookings_field_start_date_time'] );

			$data['time']  = date_i18n( get_option( 'time_format' ), strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" ) );
		} else {
			$data['_time'] = '';
		}

		// Quantity being booked
		$data['_qty'] = 1;

		// Work out persons
		if ( $this->product->has_persons() ) {
			if ( $this->product->has_person_types() ) {
				$person_types = $this->product->get_person_types();

				foreach ( $person_types as $person_type ) {
					if ( absint( $posted[ 'wc_bookings_field_persons_' . $person_type->ID ] ) > 0 ) {
						$data[ $person_type->post_title ]     = absint( $posted[ 'wc_bookings_field_persons_' . $person_type->ID ] );
						$data['_persons'][ $person_type->ID ] = $data[ $person_type->post_title ];
					}
				}
			} elseif ( isset( $posted[ 'wc_bookings_field_persons' ] ) ) {
				$data[ __( 'Persons', 'woocommerce-bookings' ) ] = absint( $posted[ 'wc_bookings_field_persons' ] );
				$data['_persons'][0]                             = absint( $posted[ 'wc_bookings_field_persons' ] );
			}

			if ( 'yes' == $this->product->wc_booking_person_qty_multiplier ) {
				$data['_qty'] = array_sum( $data['_persons'] );
			}
		}

		// Duration
		if ( 'customer' == $this->product->wc_booking_duration_type ) {
			$booking_duration       = isset( $posted['wc_bookings_field_duration'] ) ? max( 0, absint( $posted['wc_bookings_field_duration'] ) ) : 0;
			$booking_duration_unit  = $this->product->get_duration_unit();

			$data['_duration_unit'] = $booking_duration_unit;
			$data['_duration']      = $booking_duration;

			// Get the duration * block duration
			$total_duration = $booking_duration * $this->product->wc_booking_duration;

			// Nice formatted version
			switch ( $booking_duration_unit ) {
				case 'month' :
					$data['duration'] = $total_duration . ' ' . _n( 'month', 'months', $total_duration, 'woocommerce-bookings' );
					break;
				case 'day' :
					if ( $total_duration % 7 ) {
						$data['duration'] = $total_duration . ' ' . _n( 'day', 'days', $total_duration, 'woocommerce-bookings' );
					} else {
						$data['duration'] = ( $total_duration / 7 ) . ' ' . _n( 'week', 'weeks', $total_duration, 'woocommerce-bookings' );
					}
					break;
				case 'hour' :
					$data['duration'] = $total_duration . ' ' . _n( 'hour', 'hours', $total_duration, 'woocommerce-bookings' );
					break;
				case 'minute' :
					$data['duration'] = $total_duration . ' ' . _n( 'minute', 'minutes', $total_duration, 'woocommerce-bookings' );
					break;
				default :
					$data['duration'] = $total_duration;
					break;
			}
		} else {
			// Fixed duration
			$booking_duration      = $this->product->get_duration();
			$booking_duration_unit = $this->product->get_duration_unit();
			$total_duration        = $booking_duration;
		}

		// Work out start and end dates/times
		if ( ! empty( $data['_time'] ) ) {
			$data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']} {$data['_time']}" );
			$data['_end_date']   = strtotime( "+{$total_duration} {$booking_duration_unit}", $data['_start_date'] );
			$data['_all_day']    = 0;
		} else {
			$data['_start_date'] = strtotime( "{$data['_year']}-{$data['_month']}-{$data['_day']}" );
			$data['_end_date']   = strtotime( "+{$total_duration} {$booking_duration_unit} - 1 second", $data['_start_date'] );
			$data['_all_day']    = 1;
		}

		// Get posted resource or assign one for the date range
		if ( $this->product->has_resources() ) {
			if ( $this->product->is_resource_assignment_type( 'customer' ) ) {
				if ( ! empty( $posted['wc_bookings_field_resource'] ) && ( $resource = $this->product->get_resource( absint( $posted['wc_bookings_field_resource'] ) ) ) ) {
					$data['_resource_id'] = $resource->ID;
					$data['type']         = $resource->post_title;
				} else {
					$data['_resource_id'] = 0;
				}
			} else {
				// Assign an available resource automatically
				$available_bookings = $this->product->get_available_bookings( $data['_start_date'], $data['_end_date'], 0, $data['_qty'] );

				if ( is_array( $available_bookings ) ) {
					$data['_resource_id'] = current( array_keys( $available_bookings ) );
					$data['type']         = get_the_title( current( array_keys( $available_bookings ) ) );
				}
			}
		}

		return $data;
	}

	/**
	 * Checks booking data is correctly set, and that the chosen blocks are indeed available.
	 *
	 * @param  array $data
	 * @return WP_Error on failure, true on success
	 */
	public function is_bookable( $data ) {
		// Validate resources are set
		if ( $this->product->has_resources() && $this->product->is_resource_assignment_type( 'customer' ) ) {
			if ( empty( $data['_resource_id'] ) ) {
				return new WP_Error( 'Error', __( 'Please choose a resource type', 'woocommerce-bookings' ) );
			}
		} elseif ( $this->product->has_resources() && $this->product->is_resource_assignment_type( 'automatic' ) ) {
			$data['_resource_id'] = 0;
		} else {
			$data['_resource_id'] = '';
		}

		// Validate customer set durations
		if ( $this->product->is_duration_type( 'customer' ) ) {
			if ( empty( $data['_duration'] ) ) {
				return new WP_Error( 'Error', __( 'Duration is required - please enter a duration greater than zero above', 'woocommerce-bookings' ) );
			}
			if ( $data['_duration'] > $this->product->get_max_duration() ) {
				return new WP_Error( 'Error', sprintf( __( 'The maximum duration is %d', 'woocommerce-bookings' ), $this->product->wc_booking_max_duration ) );
			}
			if ( $data['_duration'] < $this->product->get_min_duration() ) {
				return new WP_Error( 'Error', sprintf( __( 'The minimum duration is %d', 'woocommerce-bookings' ), $this->product->wc_booking_min_duration ) );
			}
		}

		// Validate date and time
		if ( empty( $data['date'] ) ) {
			return new WP_Error( 'Error', __( 'Date is required - please choose one above', 'woocommerce-bookings' ) );
		}
		if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) && empty( $data['time'] ) ) {
			return new WP_Error( 'Error', __( 'Time is required - please choose one above', 'woocommerce-bookings' ) );
		}
		if ( $data['_date'] && date( 'Ymd', strtotime( $data['_date'] ) ) < date( 'Ymd', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
		}
		if ( $data['_date'] && ! empty( $data['_time'] ) && date( 'YmdHi', strtotime( $data['_date'] . ' ' . $data['_time'] ) ) < date( 'YmdHi', current_time( 'timestamp' ) ) ) {
			return new WP_Error( 'Error', __( 'You must choose a future date and time.', 'woocommerce-bookings' ) );
		}

		// Validate min date and max date
		if ( in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
			$now = current_time( 'timestamp' );
		} elseif ( 'month' === $this->product->get_duration_unit() ) {
			$now = strtotime( 'midnight first day of this month', current_time( 'timestamp' ) );
		} else {
			$now = strtotime( 'midnight', current_time( 'timestamp' ) );
		}
		if ( $min = $this->product->get_min_date() ) {
			$min_date = $this->product->get_min_timestamp_for_date( strtotime( $data['date'] ) );

			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) < $min_date ) {
				return new WP_Error( 'Error', sprintf( __( 'The earliest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $min_date ) ) );
			}
		}
		if ( $max = $this->product->get_max_date() ) {
			$max_date = strtotime( "+{$max['value']} {$max['unit']}", $now );
			if ( strtotime( $data['_date'] . ' ' . $data['_time'] ) > $max_date ) {
				return new WP_Error( 'Error', sprintf( __( 'The latest booking possible is currently %s.', 'woocommerce-bookings' ), date_i18n( wc_date_format() . ' ' . get_option( 'time_format' ), $max_date ) ) );
			}
		}

		// Validate persons
		if ( $this->product->has_persons() ) {
			$persons = array_sum( $data['_persons'] );

			if ( empty( $persons ) ) {
				return new WP_Error( 'Error', __( 'Persons are required - please enter the number of persons above', 'woocommerce-bookings' ) );
			}
			if ( $this->product->get_max_persons() && $persons > $this->product->get_max_persons() ) {
				return new WP_Error( 'Error', sprintf( __( 'The maximum persons per group is %d', 'woocommerce-bookings' ), $this->product->wc_booking_max_persons_group ) );
			}
			if ( $persons < $this->product->get_min_persons() ) {
				return new WP_Error( 'Error', sprintf( __( 'The minimum persons per group is %d', 'woocommerce-bookings' ), $this->product->wc_booking_min_persons_group ) );
			}

			if ( $this->product->has_person_types() ) {
				$person_types = $this->product->get_person_types();
				foreach( $person_types as $person ) {
					$person_max = get_post_meta( $person->ID, 'max', true );
					if ( is_numeric( $person_max ) && isset( $data['_persons'][ $person->ID ] ) && $data['_persons'][ $person->ID ] > $person_max ) {
						return new WP_Error( 'Error', sprintf( __( 'The maximum %s per group is %d', 'woocommerce-bookings' ), $person->post_title, $person_max ) );
					}

					$person_min = get_post_meta( $person->ID, 'min', true );
					if ( is_numeric( $person_min ) && isset( $data['_persons'][ $person->ID ] ) && $data['_persons'][ $person->ID ] < $person_min ) {
						return new WP_Error( 'Error', sprintf( __( 'The minimum %s per group is %d', 'woocommerce-bookings' ), $person->post_title, $person_min ) );
					}
				}
			}
		}

		// Get availability for the dates
		$available_bookings = $this->product->get_available_bookings( $data['_start_date'], $data['_end_date'], $data['_resource_id'], $data['_qty'] );

		if ( is_array( $available_bookings ) ) {
			$this->auto_assigned_resource_id = current( array_keys( $available_bookings ) );
		}

		if ( is_wp_error( $available_bookings ) ) {
			return $available_bookings;
		} elseif ( ! $available_bookings ) {
			return new WP_Error( 'Error', __( 'Sorry, the selected block is not available', 'woocommerce-bookings' ) );
		}

		return true;
	}

	/**
	 * Get an array of formatted time values
	 * @param  string $timestamp
	 * @return array
	 */
	public function get_formatted_times( $timestamp ) {
		return array(
			'timestamp'   => $timestamp,
			'year'        => date( 'Y', $timestamp ),
			'month'       => date( 'n', $timestamp ),
			'day'         => date( 'j', $timestamp ),
			'week'        => date( 'W', $timestamp ),
			'day_of_week' => date( 'N', $timestamp ),
			'time'        => date( 'YmdHi', $timestamp ),
		);
	}

	/**
	 * Calculate costs from posted values
	 * @param  array $posted
	 * @return string cost
	 */
	public function calculate_booking_cost( $posted ) {
		if ( ! empty( $this->booking_cost ) ) {
			return $this->booking_cost;
		}

		// Get costs
		$costs              = $this->product->get_costs();

		// Get posted data
		$data               = $this->get_posted_data( $posted );
		$validate           = $this->is_bookable( $data );

		if ( is_wp_error( $validate ) ) {
			return $validate;
		}

		$base_cost          = max( 0, $this->product->wc_booking_cost );
		$base_block_cost    = max( 0, $this->product->wc_booking_base_cost );
		$total_block_cost   = 0;
		$person_block_costs = 0;

		// See if we have an auto_assigned_resource_id
		if ( isset( $this->auto_assigned_resource_id ) ) {
			$data['_resource_id'] = $this->auto_assigned_resource_id;
		}

		// Get resource cost
		if ( isset( $data['_resource_id'] ) ) {
			$resource        = $this->product->get_resource( $data['_resource_id'] );
			$base_block_cost += $resource->get_block_cost();
			$base_cost       += $resource->get_base_cost();
		}

		// Potentially increase costs if dealing with persons
		if ( ! empty( $data['_persons'] ) ) {
			if ( $this->product->has_person_types() ) {
				foreach ( $data['_persons'] as $person_id => $person_count ) {
					$person_cost       = get_post_meta( $person_id, 'cost', true );
					$person_block_cost = get_post_meta( $person_id, 'block_cost', true );

					// Only a single cost - multiplication comes later if wc_booking_person_cost_multiplier is enabled
					if ( $person_count > 0 ) {
						if ( $person_cost > 0 ) {
							$base_cost += ( $person_cost * $person_count );
						}
						if ( $person_block_cost > 0 ) {
							$person_block_costs += ( $person_block_cost * $person_count );
						}
					}
				}
			}
		}

		$this->applied_cost_rules = array();
		$block_duration           = $this->product->get_duration();
		$block_unit               = $this->product->get_duration_unit();
		$blocks_booked            = isset( $data['_duration'] ) ? absint( $data['_duration'] ) : $block_duration;
		$block_timestamp          = $data['_start_date'];

		if ( $this->product->is_duration_type( 'fixed' ) ) {
			$blocks_booked = ceil( $blocks_booked / $block_duration );
		}

		// Evaluate costs for each booked block
		for ( $block = 0; $block < $blocks_booked; $block ++ ) {
			$block_cost              = $base_block_cost + $person_block_costs;
			$block_start_time_offset = $block * $block_duration;
			$block_end_time_offset   = ( $block + 1 ) * $block_duration;
			$block_start_time        = $this->get_formatted_times( strtotime( "+{$block_start_time_offset} {$block_unit}", $block_timestamp ) );
			$block_end_time          = $this->get_formatted_times( strtotime( "+{$block_end_time_offset} {$block_unit}", $block_timestamp ) );

			foreach ( $costs as $rule_key => $rule ) {
				$type  = $rule[0];
				$rules = $rule[1];

				if ( strrpos( $type, 'time' ) === 0 ) {
					if ( ! in_array( $this->product->get_duration_unit(), array( 'minute', 'hour' ) ) ) {
						continue;
					}
					if ( ! empty( $rules['day'] ) ) {
						if ( $rules['day'] != $block_start_time['day_of_week'] ) {
							continue;
						}
					}
					$rule_start_time_hi = date( "YmdHi", strtotime( str_replace( ':', '', $rules['from'] ), $block_start_time['timestamp'] ) );
					$rule_end_time_hi   = date( "YmdHi", strtotime( str_replace( ':', '', $rules['to'] ), $block_start_time['timestamp'] ) );
					$matched            = false;

					// Reverse time rule - The end time is tomorrow e.g. 16:00 today - 12:00 tomorrow
					if ( $rule_end_time_hi <= $rule_start_time_hi ) {

						if ( $block_end_time['time'] > $rule_start_time_hi ) {
							$matched = true;
						}
						if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] >= $rule_end_time_hi ) {
							$matched = true;
						}
						if ( $block_start_time['time'] <= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}

					// Normal rule
					} else {
						if ( $block_start_time['time'] >= $rule_start_time_hi && $block_end_time['time'] <= $rule_end_time_hi ) {
							$matched = true;
						}
					}

					if ( $matched ) {
						$block_cost = $this->apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
						$base_cost  = $this->apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
					}
				} else {
					switch ( $type ) {
						case 'months' :
						case 'weeks' :
						case 'days' :
							$check_date = $block_start_time['timestamp'];

							while ( $check_date < $block_end_time['timestamp'] ) {
								$checking_date = $this->get_formatted_times( $check_date );
								$date_key      = $type == 'days' ? 'day_of_week' : substr( $type, 0, -1 );

								if ( isset( $rules[ $checking_date[ $date_key ] ] ) ) {
									$rule       = $rules[ $checking_date[ $date_key ] ];
									$block_cost = $this->apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
								}
								$check_date = strtotime( "+1 {$type}", $check_date );
							}
						break;
						case 'custom' :
							$check_date = $block_start_time['timestamp'];

							while ( $check_date < $block_end_time['timestamp'] ) {
								$checking_date = $this->get_formatted_times( $check_date );
								if ( isset( $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ] ) ) {
									$rule       = $rules[ $checking_date['year'] ][ $checking_date['month'] ][ $checking_date['day'] ];
									$block_cost = $this->apply_cost( $block_cost, $rule['block'][0], $rule['block'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rule['base'][0], $rule['base'][1], $rule_key );
								}
								$check_date = strtotime( "+1 day", $check_date );
							}
						break;
						case 'persons' :
							if ( ! empty( $data['_persons'] ) ) {
								if ( $rules['from'] <= array_sum( $data['_persons'] ) && $rules['to'] >= array_sum( $data['_persons'] ) ) {
									$block_cost = $this->apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
								}
							}
						break;
						case 'blocks' :
							if ( ! empty( $data['_duration'] ) ) {
								if ( $rules['from'] <= $data['_duration'] && $rules['to'] >= $data['_duration'] ) {
									$block_cost = $this->apply_cost( $block_cost, $rules['rule']['block'][0], $rules['rule']['block'][1] );
									$base_cost  = $this->apply_base_cost( $base_cost, $rules['rule']['base'][0], $rules['rule']['base'][1], $rule_key );
								}
							}
						break;
					}
				}
			}
			$total_block_cost += $block_cost;
		}

		// Person multiplier mutliplies all costs
		$this->booking_cost = max( 0, $total_block_cost + $base_cost );

		if ( ! empty( $data['_persons'] ) ) {
			if ( 'yes' === $this->product->wc_booking_person_cost_multiplier ) {
				$this->booking_cost = $this->booking_cost * array_sum( $data['_persons'] );
			}
		}

		return apply_filters( 'booking_form_calculated_booking_cost', $this->booking_cost, $this, $posted );
	}

	/**
	 * Apply a cost
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @return float
	 */
	private function apply_cost( $base, $multiplier, $cost) {
		switch ( $multiplier ) {
			case 'times' :
				$new_cost = $base * $cost;
				break;
			case 'divide' :
				$new_cost = $base / $cost;
				break;
			case 'minus' :
				$new_cost = $base - $cost;
				break;
			default :
				$new_cost = $base + $cost;
				break;
		}
		return $new_cost;
	}

	/**
	 * Apply a cost
	 * @param  float $base
	 * @param  string $multiplier
	 * @param  float $cost
	 * @param  float $apply_to Cost to apply the rule to - used for * and /
	 * @return float
	 */
	private function apply_base_cost( $base, $multiplier, $cost, $rule_key = '' ) {
		if ( in_array( $rule_key, $this->applied_cost_rules ) ) {
			return $base;
		}
		switch ( $multiplier ) {
			case 'times' :
				$new_cost = $base * $cost;
				break;
			case 'divide' :
				$new_cost = $base / $cost;
				break;
			case 'minus' :
				$new_cost = $base - $cost;
				break;
			default :
				$new_cost = $base + $cost;
				break;
		}
		$this->applied_cost_rules[] = $rule_key;
		return $new_cost;
	}
}
