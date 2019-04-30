<?php

require_once dirname( __FILE__ ) . '/settings.php';

function wp_travel_booking_bank_deposit( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	$payment_id = wp_travel_get_payment_id( $booking_id );
	update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
	update_post_meta( $payment_id, 'wp_travel_payment_status', 'waiting_voucher' );

}

add_action( 'wp_travel_after_frontend_booking_save', 'wp_travel_booking_bank_deposit' );
