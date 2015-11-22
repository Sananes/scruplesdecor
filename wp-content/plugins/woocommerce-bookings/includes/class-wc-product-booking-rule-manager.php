<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that parses and returns rules for bookable products
 */
class WC_Product_Booking_Rule_Manager {

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_custom_range( $from, $to, $value ) {
		$availability = array();
		$from_date    = strtotime( $from );
		$to_date      = strtotime( $to );

		if ( empty( $to ) || empty( $from ) || $to_date < $from_date )
			return;

		// We have at least 1 day, even if from_date == to_date
		$numdays = 1 + ( $to_date - $from_date ) / 60 / 60 / 24;

		for ( $i = 0; $i < $numdays; $i ++ ) {
			$year  = date( 'Y', strtotime( "+{$i} days", $from_date ) );
			$month = date( 'n', strtotime( "+{$i} days", $from_date ) );
			$day   = date( 'j', strtotime( "+{$i} days", $from_date ) );

			$availability[ $year ][ $month ][ $day ] = $value;
		}

		return $availability;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_months_range( $from, $to, $value ) {
		$months = array();
		$diff   = $to - $from;
		$diff   = ( $diff < 0 ) ? 12 + $diff : $diff;
		$month  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$months[ $month ] = $value;

			$month ++;

			if ( $month > 52 )
				$month = 1;
		}

		return $months;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_weeks_range( $from, $to, $value ) {
		$weeks = array();
		$diff  = $to - $from;
		$diff  = ( $diff < 0 ) ? 52 + $diff : $diff;
		$week  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$weeks[ $week ] = $value;

			$week ++;

			if ( $week > 52 )
				$week = 1;
		}

		return $weeks;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_days_range( $from, $to, $value ) {
		$day_of_week  = $from;
		$diff         = $to - $from;
		$diff         = ( $diff < 0 ) ? 7 + $diff : $diff;
		$days         = array();

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$days[ $day_of_week ] = $value;

			$day_of_week ++;

			if ( $day_of_week > 7 ) {
				$day_of_week = 1;
			}
		}

		return $days;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_time_range( $from, $to, $value, $day = 0 ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			'day'  => $day
		);
	}

	/**
	 * Get duration range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_duration_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value
			);
	}

	/**
	 * Get Persons range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_persons_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value
			);
	}

	/**
	 * Get blocks range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_blocks_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value
			);
	}

	/**
	 * Process and return formatted cost rules
	 * @param  $rules array
	 * @return array
	 */
	public static function process_cost_rules( $rules ) {
		$costs = array();
		$index = 1;

		// Go through rules
		foreach ( $rules as $key => $fields ) {
			if ( empty( $fields['cost'] ) && empty( $fields['base_cost'] ) ) {
				continue;
			}

			$cost          = apply_filters( 'woocommerce_bookings_process_cost_rules_cost', $fields['cost'], $fields, $key );
			$modifier      = $fields['modifier'];
			$base_cost     = apply_filters( 'woocommerce_bookings_process_cost_rules_base_cost', $fields['base_cost'], $fields, $key );
			$base_modifier = $fields['base_modifier'];
			$type_function = strrpos( $fields['type'], 'time:' ) === 0 ? 'get_time_range' : 'get_' . $fields['type'] . '_range';

			$type_costs    = self::$type_function( $fields['from'], $fields['to'], array(
				'base'  => array( $base_modifier, $base_cost ),
				'block' => array( $modifier, $cost )
			) );

			// Ensure day gets specified for time: rules
			if ( strrpos( $fields['type'], 'time:' ) === 0 ) {
				list( , $day ) = explode( ':', $fields['type'] );
				$type_costs['day'] = absint( $day );
			}

			if ( $type_costs ) {
				$costs[ $index ] = array( $fields['type'], $type_costs );
				$index ++;
			}
		}

		return $costs;
	}

	/**
	 * Process and return formatted availability rules
	 * @param  $rules array
	 * @return array
	 */
	public static function process_availability_rules( $rules ) {
		$processed_rules = array();

		if ( empty( $rules ) ) {
			return $processed_rules;
		}

		// See what types of rules we have before getting the rules themselves
		$rule_types = array();

		foreach ( $rules as $fields ) {
			if ( empty( $fields['bookable'] ) ) {
				continue;
			}
			$rule_types[] = $fields['type'];
		}
		$rule_types = array_filter( $rule_types );

		// Go through rules
		foreach ( $rules as $fields ) {
			if ( empty( $fields['bookable'] ) ) {
				continue;
			}
			$type_function     = strrpos( $fields['type'], 'time:' ) === 0 ? 'get_time_range' : 'get_' . $fields['type'] . '_range';
			$type_availability = self::$type_function( $fields['from'], $fields['to'], $fields['bookable'] === 'yes' ? true : false );

			// Ensure day gets specified for time: rules
			if ( strrpos( $fields['type'], 'time:' ) === 0 ) {
				list( , $day ) = explode( ':', $fields['type'] );
				$type_availability['day'] = absint( $day );
			}

			// Enable days when user defines time rules, but not day rules
			if ( ! in_array( 'custom', $rule_types ) && ! in_array( 'days', $rule_types ) && ! in_array( 'months', $rule_types ) && ! in_array( 'weeks', $rule_types ) ) {
				if ( strrpos( $fields['type'], 'time:' ) === 0 ) {
					list( , $day ) = explode( ':', $fields['type'] );
					if ( $fields['bookable'] === 'yes' ) {
						$processed_rules[] = array( 'days', self::get_days_range( $day, $day, true ) );
					}
				} elseif ( strrpos( $fields['type'], 'time' ) === 0 ) {
					if ( $fields['bookable'] === 'yes' ) {
						$processed_rules[] = array( 'days', self::get_days_range( 0, 7, true ) );
					}
				}
			}
			if ( $type_availability ) {
				$processed_rules[] = array( $fields['type'], $type_availability );
			}
		}

		return $processed_rules;
	}
}