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

function wp_travel_submit_bank_deposit_slip() {
	if ( isset( $_POST['wp_travel_submit_slip'] ) ) {

		$target_dir = WP_CONTENT_DIR . '/wp-travel-slip/';
		if ( ! file_exists( $target_dir ) ) {
			$created = mkdir( $target_dir, 0755, true );

			if ( ! $created ) {
				// unable to create directory notice.
			}
		}
		$target_file = $target_dir . basename( $_FILES['wp_travel_bank_deposit_slip']['name'] );

		$imageFileType = strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) );
		$tmp_name      = $tmp_name = $_FILES['wp_travel_bank_deposit_slip']['tmp_name'];

		// Check if image file is a actual image or fake image.
		$check = getimagesize( $tmp_name );
		if ( $check !== false ) {
			$move = move_uploaded_file( $tmp_name, $target_file );
			if ( $move ) {
				$uploadOk = true;
			}
		} else {
			echo 'File is not an image.';
			$uploadOk = false;
		}

		// Update status if file is uploaded. and save image path to meta.
		if ( true === $uploadOk ) {

		}
	}
}

add_action( 'init', 'wp_travel_submit_bank_deposit_slip' );
