<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Addons integration class.
 */
class WC_Bookings_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_addons_show_grand_total', array( $this, 'addons_show_grand_total' ), 20, 2 );
		add_action( 'woocommerce_product_addons_panel_before_options', array( $this, 'addon_options' ), 20, 3 );
		add_filter( 'woocommerce_product_addons_save_data', array( $this, 'save_addon_options' ), 20, 2 );
		add_filter( 'woocommerce_product_addon_cart_item_data', array( $this, 'addon_price' ), 20, 4 );
		add_filter( 'woocommerce_product_addons_adjust_price', array( $this, 'adjust_price' ), 20, 2 );
		add_filter( 'booking_form_calculated_booking_cost', array( $this, 'adjust_booking_cost' ), 10, 3 );
	}

	/**
	 * Show grand total or not?
	 * @param  bool $show_grand_total
	 * @param  object $product
	 * @return bool
	 */
	public function addons_show_grand_total( $show_grand_total, $product ) {
		if ( $product->is_type( 'booking' ) ) {
			$show_grand_total = false;
		}
		return $show_grand_total;
	}

	/**
	 * Show options
	 */
	public function addon_options( $post, $addon, $loop ) {
		?>
		<tr class="show_if_booking">
			<td class="addon_wc_booking_person_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>"><?php _e( 'Bookings: Multiply cost by person count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_person_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_person_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_person_qty_multiplier'] ), true ) ?> />
			</td>
			<td class="addon_wc_booking_block_qty_multiplier addon_required" width="50%">
				<label for="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>"><?php _e( 'Bookings: Multiply cost by block count', 'woocommerce-bookings' ); ?></label>
				<input type="checkbox" id="addon_wc_booking_block_qty_multiplier_<?php echo $loop; ?>" name="addon_wc_booking_block_qty_multiplier[<?php echo $loop; ?>]" <?php checked( ! empty( $addon['wc_booking_block_qty_multiplier'] ), true ) ?> />
			</td>
		</tr>
		<?php
	}

	/**
	 * Save options
	 */
	public function save_addon_options( $data, $i ) {
		$data['wc_booking_person_qty_multiplier'] = isset( $_POST['addon_wc_booking_person_qty_multiplier'][ $i ] ) ? 1 : 0;
		$data['wc_booking_block_qty_multiplier']  = isset( $_POST['addon_wc_booking_block_qty_multiplier'][ $i ] ) ? 1 : 0;

		return $data;
	}

	/**
	 * Change addon price based on settings
	 * @return float
	 */
	public function addon_price( $cart_item_data, $addon, $product_id, $post_data ) {
		$product = get_product( $product_id );

		// Make sure we know if multipliers are enabled so adjust_booking_cost can see them
		foreach ( $cart_item_data as $key => $data ) {
			if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_person_qty_multiplier'] = 1;
			}
			if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) ) {
				$cart_item_data[ $key ]['wc_booking_block_qty_multiplier'] = 1;
			}
		}

		/*// Adjust booking products in cart
		if ( $product->is_type( 'booking' ) ) {
			$booking_form = new WC_Booking_Form( $product );
			$booking_data = $booking_form->get_posted_data( $post_data );

			foreach ( $cart_item_data as $key => $data ) {
				if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
					$cart_item_data[ $key ]['price'] = $data['price'] * array_sum( $booking_data['_persons'] );
				}
				if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) && ! empty( $booking_data['_duration'] ) ) {
					$cart_item_data[ $key ]['price'] = $data['price'] * $booking_data['_duration'];
				}
			}
		}*/
		return $cart_item_data;
	}

	/**
	 * Don't adjust price for bookings since the booking form class adds the costs itself
	 * @return bool
	 */
	public function adjust_price( $bool, $cart_item ) {
		if ( $cart_item['data']->is_type( 'booking' ) ) {
			return false;
		}
		return $bool;
	}

	/**
	 * Adjust the final booking cost
	 */
	public function adjust_booking_cost( $booking_cost, $booking_form, $posted ) {
		// Product add-ons
		$addons       = $GLOBALS['Product_Addon_Cart']->add_cart_item_data( array(), $booking_form->product->id, $posted, true );
		$addon_costs  = 0;
		$booking_data = $booking_form->get_posted_data( $posted );

		if ( ! empty( $addons['addons'] ) ) {
			foreach ( $addons['addons'] as $addon ) {
				$addon_cost = 0;

				if ( ! empty( $addon['wc_booking_person_qty_multiplier'] ) && ! empty( $booking_data['_persons'] ) && array_sum( $booking_data['_persons'] ) ) {
					$addon_cost += $addon['price'] * array_sum( $booking_data['_persons'] );
				}
				if ( ! empty( $addon['wc_booking_block_qty_multiplier'] ) && ! empty( $booking_data['_duration'] ) ) {
					$addon_cost += $addon['price'] * $booking_data['_duration'];
				}
				if ( ! $addon_cost ) {
					$addon_cost += $addon['price'];
				}
				$addon_costs += $addon_cost;
			}
		}

		return $booking_cost + $addon_costs;
	}
}

new WC_Bookings_Addons();