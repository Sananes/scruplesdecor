<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Bookings_Customer_Meta_Box {
	public $id;
	public $title;
	public $context;
	public $priority;
	public $post_types;

	public function __construct() {
		$this->id         = 'woocommerce-customer-data';
		$this->title      = __( 'Customer details', 'woocommerce-bookings' );
		$this->context    = 'side';
		$this->priority   = 'default';
		$this->post_types = array( 'wc_booking' );
	}

	public function meta_box_inner( $post ) {
		$customer_id = get_post_meta( $post->ID, '_booking_customer_id', true );
		$order_id    = $post->post_parent;
		$has_data    = false;

		echo '<table class="booking-customer-details">';

		if ( $customer_id && ( $user = get_user_by( 'id', $customer_id ) ) ) {
			echo '<tr>';
				echo '<th>' . __( 'Name:', 'woocommerce-bookings' ) . '</th>';
				echo '<td>';
				if ( $user->last_name && $user->first_name ) {
					echo $user->first_name . ' ' . $user->last_name;
				} else {
					echo '-';
				}
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<th>' . __( 'User Email:', 'woocommerce-bookings' ) . '</th>';
				echo '<td>';
				echo '<a href="mailto:' . esc_attr( $user->user_email ) . '">' . esc_html( $user->user_email ) . '</a>';
				echo '</td>';
			echo '</tr>';
			echo '<tr class="view">';
				echo '<th>&nbsp;</th>';
				echo '<td>';
				echo '<a class="button" target="_blank" href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $user->ID ) ) . '">' . __( 'View User', 'woocommerce-bookings' ) . '</a>';
				echo '</td>';
			echo '</tr>';

			$has_data = true;
		}

		if ( $order_id && ( $order = wc_get_order( $order_id ) ) ) {
			echo '<tr>';
				echo '<th>' . __( 'Address:', 'woocommerce-bookings' ) . '</th>';
				echo '<td>';
				if ( $order->get_formatted_billing_address() ) {
					echo wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) );
				} else {
					echo __( 'No billing address set.', 'woocommerce-bookings' );
				}
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<th>' . __( 'Email:', 'woocommerce-bookings' ) . '</th>';
				echo '<td>';
				echo '<a href="mailto:' . esc_attr( $order->billing_email ) . '">' . esc_html( $order->billing_email ) . '</a>';
				echo '</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<th>' . __( 'Phone:', 'woocommerce-bookings' ) . '</th>';
				echo '<td>';
				echo esc_html( $order->billing_phone );
				echo '</td>';
			echo '</tr>';
			echo '<tr class="view">';
				echo '<th>&nbsp;</th>';
				echo '<td>';
				echo '<a class="button" target="_blank" href="' . esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ) . '">' . __( 'View Order', 'woocommerce-bookings' ) . '</a>';
				echo '</td>';
			echo '</tr>';

			$has_data = true;
		}

		if ( ! $has_data ) {
			echo '<tr>';
				echo '<td colspan="2">' . __( 'N/A', 'woocommerce-bookings' ) . '</td>';
			echo '</tr>';
		}

		echo '</table>';
	}
}

return new WC_Bookings_Customer_Meta_Box();
