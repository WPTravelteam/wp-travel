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

		if ( ! isset( $_POST['booking_id'] ) ) {
			return;
		}

		$settings = wp_travel_get_settings();

		$wp_travel_bank_deposit_allowed_file = isset( $settings['wp_travel_bank_deposit_allowed_file'] ) ? $settings['wp_travel_bank_deposit_allowed_file'] : 'jpg, png';
		$wp_travel_bank_deposit_allowed_file = str_replace( ' ', '', $wp_travel_bank_deposit_allowed_file );
		$allowed_ext = explode( ',', $wp_travel_bank_deposit_allowed_file );
		$target_dir = WP_CONTENT_DIR . '/' . WP_TRAVEL_SLIP_UPLOAD_DIR . '/';
		if ( ! file_exists( $target_dir ) ) {
			$created = mkdir( $target_dir, 0755, true );

			if ( ! $created ) {
				WP_Travel()->notices->add( '<strong>' . __( 'Error:' ) . '</strong> ' . __( 'Unable to create directory "wp-travel-slip"' ), 'error' );
			}
		}
		$filename    = substr( md5( rand( 1, 1000000 ) ), 0, 10 ) . '-' . basename( $_FILES['wp_travel_bank_deposit_slip']['name'] );
		$target_file = $target_dir . $filename;

		$tmp_name = $tmp_name = $_FILES['wp_travel_bank_deposit_slip']['tmp_name'];

		$ext = strtolower( pathinfo( $target_file, PATHINFO_EXTENSION ) );
		
		if ( in_array( $ext, $allowed_ext ) ) {

			$move = move_uploaded_file( $tmp_name, $target_file );
			if ( $move ) {
				$upload_ok = true;
			}
		} else {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:' ) . '</strong> ' . __( 'Uploaded files are not allowed.' ), 'error' );
			$upload_ok = false;
		}

		// Update status if file is uploaded. and save image path to meta.
		if ( true === $upload_ok ) {
			$booking_id = $_POST['booking_id'];
			$data = wp_travel_booking_data( $booking_id );

			$total = $data['total'];
			if ( 'partial' == $_POST['wp_travel_payment_mode'] ) {
				$total = $data['total_partial'];
			}
			$paid = $data['paid_amount'];

			$amount = $total - $paid;
			$amount = wp_travel_get_formated_price( $amount );


			do_action( 'wt_before_payment_process', $booking_id );

			

			$detail['amount'] = $amount;

			$payment_id     = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
			$payment_method = get_post_meta( $payment_id, 'wp_travel_payment_gateway', true );
			update_post_meta( $payment_id, 'wp_travel_payment_gateway', $payment_method );
			update_post_meta( $payment_id, 'wp_travel_payment_slip_name', $filename );

			wp_travel_update_payment_status( $booking_id, $amount, 'voucher_submited', $detail, sprintf( '_%s_args', $payment_method ), $payment_id );
			do_action( 'wp_travel_after_successful_payment', $booking_id );

		}
	}
}

add_action( 'init', 'wp_travel_submit_bank_deposit_slip' );
