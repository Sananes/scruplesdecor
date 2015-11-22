<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles email sending
 */
class WC_Booking_Email_Manager {

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );

		// Email Actions
		$email_actions = array(
			// New & Pending Confirmation
			'woocommerce_booking_in-cart_to_paid',
			'woocommerce_booking_in-cart_to_pending-confirmation',
			'woocommerce_booking_unpaid_to_paid',
			'woocommerce_booking_unpaid_to_pending-confirmation',
			'woocommerce_booking_confirmed_to_paid',
			'woocommerce_new_booking',
			'woocommerce_admin_new_booking',

			// Confirmed
			'woocommerce_booking_confirmed',

			// Cancelled
			'woocommerce_booking_pending-confirmation_to_cancelled',
			'woocommerce_booking_confirmed_to_cancelled',
			'woocommerce_booking_paid_to_cancelled'
		);

		foreach ( $email_actions as $action ) {
			if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
				add_action( $action, array( $GLOBALS['woocommerce'], 'send_transactional_email' ), 10, 10 );
			} else {
				add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
			}
		}

		add_filter( 'woocommerce_email_attachments', array( $this, 'attach_ics_file' ), 10, 3 );

		add_filter( 'woocommerce_template_directory', array( $this, 'template_directory' ), 10, 2 );
	}

	/**
	 * Include our mail templates
	 *
	 * @param  array $emails
	 * @return array
	 */
	public function init_emails( $emails ) {
		if ( ! isset( $emails['WC_Email_New_Booking'] ) ) {
			$emails['WC_Email_New_Booking'] = include( 'emails/class-wc-email-new-booking.php' );
		}

		if ( ! isset( $emails['WC_Email_Booking_Reminder'] ) ) {
			$emails['WC_Email_Booking_Reminder'] = include( 'emails/class-wc-email-booking-reminder.php' );
		}

		if ( ! isset( $emails['WC_Email_Booking_Confirmed'] ) ) {
			$emails['WC_Email_Booking_Confirmed'] = include( 'emails/class-wc-email-booking-confirmed.php' );
		}

		if ( ! isset( $emails['WC_Email_Booking_Notification'] ) ) {
			$emails['WC_Email_Booking_Notification'] = include( 'emails/class-wc-email-booking-notification.php' );
		}

		if ( ! isset( $emails['WC_Email_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Booking_Cancelled'] = include( 'emails/class-wc-email-booking-cancelled.php' );
		}

		if ( ! isset( $emails['WC_Email_Admin_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Admin_Booking_Cancelled'] = include( 'emails/class-wc-email-admin-booking-cancelled.php' );
		}

		return $emails;
	}

	/**
	 * Attach the .ics files in the emails.
	 *
	 * @param  array  $attachments
	 * @param  string $email_id
	 * @param  mixed  $booking
	 *
	 * @return array
	 */
	public function attach_ics_file( $attachments, $email_id, $booking ) {
		$available = apply_filters( 'woocommerce_bookings_emails_ics', array( 'booking_confirmed', 'booking_reminder' ) );

		if ( in_array( $email_id, $available ) ) {
			$generate = new WC_Bookings_ICS_Exporter;
			$attachments[] = $generate->get_booking_ics( $booking );
		}

		return $attachments;
	}

	/**
	 * Custom template directory.
	 *
	 * @param  string $directory
	 * @param  string $template
	 *
	 * @return string
	 */
	public function template_directory( $directory, $template ) {
		if ( false !== strpos( $template, '-booking' ) ) {
			return 'woocommerce-bookings';
		}

		return $directory;
	}
}

new WC_Booking_Email_Manager();
