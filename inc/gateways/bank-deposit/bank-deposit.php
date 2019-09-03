<?php

require_once dirname( __FILE__ ) . '/settings.php';

function wp_travel_booking_bank_deposit( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	$gateway = isset( $_POST['wp_travel_payment_gateway'] ) ? $_POST['wp_travel_payment_gateway'] : '';
	if ( 'bank_deposit' === $gateway ) {

		$payment_id = wp_travel_get_payment_id( $booking_id );

		$payment_mode = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );
		update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
		update_post_meta( $payment_id, 'wp_travel_payment_status', 'waiting_voucher' );
	}

}

add_action( 'wp_travel_after_frontend_booking_save', 'wp_travel_booking_bank_deposit' );

function wp_travel_submit_bank_deposit_slip() {
	if ( isset( $_POST['wp_travel_submit_slip'] ) ) {

		if ( ! isset( $_POST['booking_id'] ) ) {
			return;
		}

		$settings = wp_travel_get_settings();

		$allowed_files = apply_filters( 'wp_travel_bank_deposit_allowed_files', 'jpg, png, txt, pdf' );

		$allowed_files = str_replace( ' ', '', $allowed_files );
		$allowed_ext   = explode( ',', $allowed_files );
		$target_dir    = WP_CONTENT_DIR . '/' . WP_TRAVEL_SLIP_UPLOAD_DIR . '/';
		if ( ! file_exists( $target_dir ) ) {
			$created = mkdir( $target_dir, 0755, true );

			if ( ! $created ) {
				WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Unable to create directory "wp-travel-slip"', 'wp-travel' ), 'error' );
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

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Uploaded files are not allowed.', 'wp-travel' ), 'error' );
			$upload_ok = false;
		}

		// Update status if file is uploaded. and save image path to meta.
		if ( true === $upload_ok ) {
			$booking_id = $_POST['booking_id'];
			$txn_id     = isset( $_POST['wp_travel_bank_deposit_transaction_id'] ) ? $_POST['wp_travel_bank_deposit_transaction_id'] : '';
			$data       = wp_travel_booking_data( $booking_id );

			$total = $data['total'];
			if ( 'partial' == $_POST['wp_travel_payment_mode'] ) {
				$total = $data['total_partial'];
			}
			$paid = $data['paid_amount'];

			$amount = $total - $paid;
			$amount = wp_travel_get_formated_price( $amount );

			do_action( 'wt_before_payment_process', $booking_id );

			$detail['amount'] = $amount;
			$detail['txn_id'] = $txn_id;

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


function wp_travel_bank_deposite_button( $booking_id = null, $details = array() ) {
	// In Case of partial payment activated.
	if ( ! $booking_id ) {
		$booking_id = isset( $_GET['detail_id'] ) ? $_GET['detail_id'] : 0;
	}
	if ( ! $booking_id ) {
		return;
	}
	$enabled_payment_gateways = wp_travel_enabled_payment_gateways();
	$details    = wp_travel_booking_data( $booking_id );
	if ( in_array( 'bank_deposit', $enabled_payment_gateways, true ) && in_array( $details['payment_status'], array( 'waiting_voucher' ), true ) ) :
		if ( ! class_exists( 'WP_Travel_Partial_Payment_Core' ) ) :
			$details['due_amount'] = apply_filters( 'wp_travel_partial_payment_due_amount', $details['due_amount'] );
			?>
			<div class="wp-travel-form-field  wp-travel-text-info">
				<label for="wp-travel-amount-info"><?php _e( 'Amount', 'wp-travel' ); ?></label>
				<div class="wp-travel-text-info"><?php echo wp_travel_get_formated_price_currency( $details['due_amount'] ); ?></div>
			</div>
		<?php endif; ?>
		<div class="wp-travel-bank-deposit-wrap">
			<h3 class="my-order-single-title"><?php _e( 'Bank Payment', 'wp-travel' ); ?></h3>
			<a href="#wp-travel-bank-deposit-content" class="wp-travel-upload-slip wp-travel-magnific-popup button"><?php esc_html_e( 'Submit Payment Receipt', 'wp-travel' ); ?></a>
			<a href="#wp-travel-bank-details-content" class="wp-travel-magnific-popup view-bank-deposit-button" style="display:block; padding:5px 0" ><?php _e( 'View Bank Details', 'wp-travel' ); ?></a>
		</div>
		<?php
	endif;
}

// Bank deposit Payment button in wp travel dashboard. @since 2.0.0
// function wp_travel_show_bank_payment_section() {

// 	if ( class_exists( 'WP_Travel_Partial_Payment_Core' ) ) {
// 		add_action( 'wp_travel_partial_payment_before_submit_button', 'wp_travel_bank_deposite_button', 100 );
// 	} else {
// 	}
// }
// add_action( 'init', 'wp_travel_show_bank_payment_section', 20 );
add_action( 'wp_travel_dashboard_booking_after_detail', 'wp_travel_bank_deposite_button', 20, 2 );

function wp_travel_bank_deposite_content( $booking_id = null, $details = array() ) {
	// In Case of partial payment activated.
	if ( ! $booking_id ) {
		$booking_id = isset( $_GET['detail_id'] ) ? $_GET['detail_id'] : 0;
	}
	if ( ! $booking_id ) {
		return;
	}

	$details = wp_travel_booking_data( $booking_id );

	// End of in Case of partial payment activated.
	if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	}

	$form = new WP_Travel_FW_Form();

	$form_options                      = array(
		'id'            => 'wp-travel-submit-slip',
		'wrapper_class' => 'wp-travel-submit-slip-form-wrapper',
		'submit_button' => array(
			'name'  => 'wp_travel_submit_slip',
			'id'    => 'wp-travel-submit-slip',
			'value' => __( 'Submit', 'wp-travel' ),
		),
		'hook_prefix' => 'wp_travel_partial_payment',
		'multipart'     => true,
		'nonce'         => array(
			'action' => 'wp_travel_security_action',
			'field'  => 'wp_travel_security',
		),
	);
	$bank_deposit_fields               = wp_travel_get_bank_deposit_form_fields();
	$bank_deposit_fields['booking_id'] = array(
		'type'    => 'hidden',
		'name'    => 'booking_id',
		'id'      => 'wp-travel-booking_id',
		'default' => $booking_id,
	);
	?>
	<div class="wp-travel-bank-deposit-wrap">		
		<div id="wp-travel-bank-deposit-content" class="wp-travel-popup" >
			<h3 class="popup-title"><?php esc_html_e( 'Submit Bank Payment Receipt', 'wp-travel' ); ?></h3>
			<?php $form->init( $form_options )->fields( $bank_deposit_fields )->template(); ?>
			<button title="Close (Esc)" type="button" class="mfp-close close-button">x</button>
		</div>
		<div id="wp-travel-bank-details-content" class="wp-travel-popup" >
			<h3 class="popup-title"><?php esc_html_e( 'Bank Details', 'wp-travel' ); ?></h3>
			<?php echo wp_travel_get_bank_deposit_account_table(); ?>
			<button title="Close (Esc)" type="button" class="mfp-close close-button">x</button>
		</div>
	</div>
	<?php

}

// Bank deposit Payment content.
add_action( 'wp_travel_dashboard_booking_after_detail', 'wp_travel_bank_deposite_content', 9, 2 );
