<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cron job handler
 */
class WC_Booking_Cron_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wc-booking-reminder', array( $this, 'send_booking_reminder' ) );
		add_action( 'wc-booking-complete', array( $this, 'mark_booking_complete' ) );
		add_action( 'wc-booking-remove-inactive-cart', array( $this, 'remove_inactive_booking_from_cart' ) );
	}

	/**
	 * Send booking reminder email
	 */
	public function send_booking_reminder( $booking_id ) {
		$mailer   = WC()->mailer();
		$reminder = $mailer->emails['WC_Email_Booking_Reminder'];
		$reminder ->trigger( $booking_id );
	}

	/**
	 * Change the booking status
	 */
	public function mark_booking_complete( $booking_id ) {
		$booking = get_wc_booking( $booking_id );
		$booking->update_status( 'complete' );
	}

	/**
	 * Remove inactive booking
	 */
	public function remove_inactive_booking_from_cart( $booking_id ) {
		if ( $booking_id && ( $booking = get_wc_booking( $booking_id ) ) && $booking->has_status( 'in-cart' ) ) {
			wp_delete_post( $booking_id );
		}
	}
}

new WC_Booking_Cron_Manager();

