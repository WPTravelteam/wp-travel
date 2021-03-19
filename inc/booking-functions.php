<?php
/**
 * Booking Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Frontend booking and send Email after clicking Book Now.
 *
 * @since WP Travel 1.7.5
 */
function wptravel_book_now() {
	if (
		! isset( $_POST['wp_travel_book_now'] )
		|| ! isset( $_POST['wp_travel_security'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_travel_security'] ) ), 'wp_travel_security_action' )
		) {
		return;
	}

	global $wt_cart;

	/**
	 * Trigger any action before Booking Process.
	 *
	 * @hooked array( 'WP_Travel_Coupon', 'process_update_count' )
	 * @since WP Travel 4.4.2
	 */
	do_action( 'wp_travel_action_before_booking_process' ); // phpcs:ignore
	do_action( 'wptravel_action_before_booking_process' );

	// Start Booking Process.
	$items = $wt_cart->getItems();
	if ( ! count( $items ) ) {
		return;
	}

	$price_key            = false;
	$pax                  = 1;
	$allow_multiple_items = WP_Travel_Cart::allow_multiple_items();

	$trip_ids               = array();
	$pax_array              = array();
	$price_keys             = array();
	$arrival_date           = array();
	$departure_date         = array();
	$arrival_date_email_tag = array(); // quick fix to add arrival date along with time in email.
	$pricing_id             = array(); // @since WP Travel v4.0
	$trip_time              = array(); // @since WP Travel v4.0
	foreach ( $items as $key => $item ) {
		// @since 3.1.3
		$email_travel_date = apply_filters( 'wp_travel_email_travel_date', $item['arrival_date'], $item ); // phpcs:ignore
		$email_travel_date = apply_filters( 'wptravel_email_travel_date', $email_travel_date, $item );

		$trip_ids[]               = $item['trip_id'];
		$pax_array[]              = $item['pax'];
		$price_keys[]             = $item['price_key'];
		$arrival_date[]           = $item['arrival_date'];
		$departure_date[]         = $item['departure_date'];
		$arrival_date_email_tag[] = $email_travel_date;
		$pricing_id[]             = isset( $item['pricing_id'] ) ? $item['pricing_id'] : 0; // @since WP Travel v4.0
		$trip_time[]              = isset( $item['trip_time'] ) ? $item['trip_time'] : ''; // @since WP Travel v4.0
	}

	if ( ! $allow_multiple_items || ( 1 === count( $items ) ) ) {
		$pax                    = isset( $pax_array[0] ) ? $pax_array[0] : $pax;
		$price_key              = isset( $price_keys[0] ) ? $price_keys[0] : '';
		$arrival_date           = $arrival_date[0];
		$departure_date         = $departure_date[0];
		$pricing_id             = $pricing_id[0];
		$trip_time              = $trip_time[0];
		$arrival_date_email_tag = wptravel_format_date( $arrival_date_email_tag[0], true, 'Y-m-d' );
	}
	$trip_id = isset( $trip_ids[0] ) ? $trip_ids[0] : 0;

	if ( empty( $trip_id ) ) {
		return;
	}
	$thankyou_page_url = wptravel_thankyou_page_url( $trip_id );

	// Insert Booking.
	$post_array = array(
		'post_title'   => '',
		'post_content' => '',
		'post_status'  => 'publish',
		'post_slug'    => uniqid(),
		'post_type'    => 'itinerary-booking',
	);
	$booking_id = wp_insert_post( $post_array );
	// Update Booking Title.
	$update_data_array = array(
		'ID'         => $booking_id,
		'post_title' => 'Booking - # ' . $booking_id,
	);
	wp_update_post( $update_data_array );

	// Updating Booking Metas.
	update_post_meta( $booking_id, 'order_data', wptravel_sanitize_array( $_POST ) );
	update_post_meta( $booking_id, 'order_items_data', $items ); // @since 1.8.3
	update_post_meta( $booking_id, 'order_totals', $wt_cart->get_total() );
	/**
	 * Update Arrival and Departure dates metas.
	 */
	update_post_meta( $booking_id, 'wp_travel_arrival_date', sanitize_text_field( $arrival_date ) );
	update_post_meta( $booking_id, 'wp_travel_departure_date', sanitize_text_field( $departure_date ) );
	update_post_meta( $booking_id, 'wp_travel_post_id', absint( $trip_id ) ); // quick fix [booking not listing in user dashboard].
	update_post_meta( $booking_id, 'wp_travel_arrival_date_email_tag', sanitize_text_field( $arrival_date_email_tag ) ); // quick fix arrival date with time.

	// Insert $_POST as Booking Meta.
	$post_ignore = array( '_wp_http_referer', 'wp_travel_security', 'wptravel_book_now', 'wp_travel_payment_amount' );
	$meta_array  = wptravel_sanitize_array( $_POST );
	foreach ( $meta_array as $meta_name => $meta_val ) {
		if ( in_array( $meta_name, $post_ignore, true ) ) {
			continue;
		}
		if ( is_array( $meta_val ) ) {
			$new_meta_value = array();
			foreach ( $meta_val as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_meta_value[ $key ] = array_map( 'sanitize_text_field', $value );
					/**
					 * Quick fix for the field editor checkbox issue for the data save.
					 *
					 * @since 2.1.0
					 */
					if ( isset( $value[0] ) && is_array( $value[0] ) ) {
						$new_value = array();
						foreach ( $value as $nested_value ) {
							$new_value[] = implode( ', ', $nested_value );
						}
						$new_meta_value[ $key ] = array_map( 'sanitize_text_field', $new_value );
					}
				} else {
					$new_meta_value[ $key ] = sanitize_text_field( $value );
				}
			}
			update_post_meta( $booking_id, $meta_name, $new_meta_value );
		} else {
			update_post_meta( $booking_id, $meta_name, sanitize_text_field( $meta_val ) );
		}
	}

	// Insert/Update Booking IDs in user meta to fetch bookings of those user.
	if ( is_user_logged_in() ) {
		$user              = wp_get_current_user();
		$saved_booking_ids = get_user_meta( $user->ID, 'wp_travel_user_bookings', true );
		$saved_booking_ids = ! $saved_booking_ids ? array() : $saved_booking_ids;
		array_push( $saved_booking_ids, $booking_id );
		update_user_meta( $user->ID, 'wp_travel_user_bookings', $saved_booking_ids );
	}

	$settings       = wptravel_get_settings();
	$customer_email = isset( $_POST['wp_travel_email_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_email_traveller'] ) ) : array(); // @phpcs:ignore
	reset( $customer_email );
	$first_key = key( $customer_email );
	$customer_email = isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';
	if ( ! $allow_multiple_items || ( 1 === count( $items ) ) ) {
		$args = array(
			'trip_id'        => $trip_id,
			'booking_id'     => $booking_id,
			'pricing_id'     => $pricing_id,
			'pax'            => $pax,
			'selected_date'  => $arrival_date, // [used in inventory]
			'time'           => $trip_time,
			'price_key'      => $price_key, // Just for legacy. Note: Not used for inventory [For Email].
			'arrival_date'   => $arrival_date_email_tag, // For Email [arrival_date along with time].
			'departure_date' => $departure_date, // For Email.
			'customer_email' => $customer_email,
		);
		/**
		 * Add Support for invertory addon options.
		 */
		wptravel_do_deprecated_action( 'wp_travel_update_trip_inventory_values', array( $trip_id, $pax, $price_key, $arrival_date, $booking_id ), '4.4.0', 'wp_travel_trip_inventory' );
		/**
		 * Trigger Update inventory values.
		 *
		 * @hooked array( 'WP_Travel_Util_Inventory', 'update_inventory' )
		 * @since WP Travel 4.0.0
		 */
		$inventory_args = apply_filters( 'wp_travel_inventory_args', $args ); // phpcs:ignore
		$inventory_args = apply_filters( 'wptravel_inventory_args', $inventory_args );

		do_action( 'wp_travel_trip_inventory', $inventory_args ); // phpcs:ignore
		do_action( 'wptravel_trip_inventory', $inventory_args );
		// End of Inventory.

		// Begin Send Email to client / admin.
		/**
		 * Trigger Email functions. sends email to admin and client.
		 *
		 * @hooked array( 'WP_Travel_Email', 'send_booking_emails' )
		 * @since WP Travel 4.4.2
		 */
		do_action( 'wp_travel_action_after_inventory_update', $args ); // phpcs:ignore
		do_action( 'wptravel_action_after_inventory_update', $args );
	} else {

		// Update single trip vals. // Need Enhancement. lots of loop with this $items in this functions.
		foreach ( $items as $item_key => $trip ) {

			$trip_id      = $trip['trip_id'];
			$pax          = $trip['pax'];
			$price_key    = isset( $trip['price_key'] ) && ! empty( $trip['price_key'] ) ? $trip['price_key'] : false;
			$arrival_date = isset( $trip['arrival_date'] ) && ! empty( $trip['arrival_date'] ) ? $trip['arrival_date'] : '';

			$booking_count     = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
			$booking_count     = ( isset( $booking_count ) && '' !== $booking_count ) ? $booking_count : 0;
			$new_booking_count = $booking_count + 1;
			update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( in_array( 'wp-travel-customer', (array) $user->roles, true ) ) {

					$saved_booking_ids = get_user_meta( $user->ID, 'wp_travel_user_bookings', true );

					if ( ! $saved_booking_ids ) {
						$saved_booking_ids = array();
					}

					array_push( $saved_booking_ids, $booking_id );

					update_user_meta( $user->ID, 'wp_travel_user_bookings', $saved_booking_ids );

				}
			}

			/**
			 * Add Support for invertory addon options.
			 */
			wptravel_do_deprecated_action( 'wp_travel_update_trip_inventory_values', array( $trip_id, $pax, $price_key, $arrival_date, $booking_id ), '4.4.0', 'wp_travel_trip_inventory' );
			$args = array(
				'trip_id'       => $trip_id,
				'booking_id'    => $booking_id,
				'pricing_id'    => $pricing_id,
				'pax'           => $pax,
				'selected_date' => $arrival_date,
				'time'          => $trip_time,
			);
			$args = array(
				'trip_id'       => $trip_id,
				'booking_id'    => $booking_id,
				'pricing_id'    => $pricing_id,
				'pax'           => $pax,
				'selected_date' => $arrival_date, // [used in inventory].
				'time'          => $trip_time,
				'price_key'     => $price_key, // Just for legacy. Note: Not used for inventory [For Email].
			);
			/**
			 * Trigger Update inventory values action.
			 *
			 * @hooked array( 'WP_Travel_Util_Inventory', 'update_inventory' )
			 * @since WP Travel 4.0.0
			 */
			$inventory_args = apply_filters( 'wp_travel_inventory_args', $args ); // phpcs:ignore
			$inventory_args = apply_filters( 'wptravel_inventory_args', $inventory_args );

			do_action( 'wp_travel_trip_inventory', $inventory_args ); // phpcs:ignore
			do_action( 'wptravel_trip_inventory', $inventory_args );
			// End of Inventory.
		}
		if ( class_exists( 'WP_Travel_Multiple_Cart_Booking' ) ) {
			$multiple_order = new WP_Travel_Multiple_Cart_Booking();
			// Finally, send the booking e-mails.
			$multiple_order->send_emails( $booking_id );
		}
	}

	/**
	 * Hook used to add payment and its info.
	 *
	 * @since 1.0.5 // For Payment.
	 */
	do_action( 'wp_travel_after_frontend_booking_save', $booking_id, $first_key ); // phpcs:ignore
	do_action( 'wptravel_after_frontend_booking_save', $booking_id, $first_key );

	$require_login_to_checkout = isset( $settings['enable_checkout_customer_registration'] ) ? $settings['enable_checkout_customer_registration'] : 'no'; // if required login then there is registration option as well. so we continue if this is no.
	$create_user_while_booking = isset( $settings['create_user_while_booking'] ) ? $settings['create_user_while_booking'] : 'no';
	if ( 'no' === $require_login_to_checkout && 'yes' === $create_user_while_booking && ! is_user_logged_in() ) {
		wptravel_create_new_customer( $customer_email );
	}
	// Clear Transient To update booking Count.
	// delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );.
	delete_post_meta( $trip_id, 'wp_travel_booking_count' );

	// Clear Cart After process is complete.
	$wt_cart->clear();

	$thankyou_page_url = add_query_arg( 'booked', true, $thankyou_page_url );
	$thankyou_page_url = add_query_arg( '_nonce', WP_Travel::create_nonce(), $thankyou_page_url );
	$thankyou_page_url = add_query_arg( 'order_id', $booking_id, $thankyou_page_url );
	header( 'Location: ' . $thankyou_page_url );
	exit;
}

/**
 * Get All booking stat data.
 *
 * @return void
 */
function wptravel_get_booking_chart() {

	$submission_request = WP_Travel::get_sanitize_request();

	$wp_travel_itinerary_list = wptravel_get_itineraries_array();
	$wp_travel_post_id        = ( isset( $submission_request['booking_itinerary'] ) && '' !== $submission_request['booking_itinerary'] ) ? absint( $submission_request['booking_itinerary'] ) : 0;

	$country_list     = wptravel_get_countries();
	$selected_country = ( isset( $submission_request['booking_country'] ) && '' !== $submission_request['booking_country'] ) ? esc_attr( $submission_request['booking_country'] ) : '';

	$from_date = ( isset( $submission_request['booking_stat_from'] ) && '' !== $submission_request['booking_stat_from'] ) ? rawurldecode( $submission_request['booking_stat_from'] ) : '';
	$to_date   = ( isset( $submission_request['booking_stat_to'] ) && '' !== $submission_request['booking_stat_to'] ) ? rawurldecode( $submission_request['booking_stat_to'] ) : '';

	$compare_stat = ( isset( $submission_request['compare_stat'] ) && '' !== $submission_request['compare_stat'] ) ? rawurldecode( $submission_request['compare_stat'] ) : '';

	$compare_from_date         = ( isset( $submission_request['compare_stat_from'] ) && '' !== $submission_request['compare_stat_from'] ) ? rawurldecode( $submission_request['compare_stat_from'] ) : '';
	$compare_to_date           = ( isset( $submission_request['compare_stat_to'] ) && '' !== $submission_request['compare_stat_to'] ) ? rawurldecode( $submission_request['compare_stat_to'] ) : '';
	$compare_selected_country  = ( isset( $submission_request['compare_country'] ) && '' !== $submission_request['compare_country'] ) ? esc_attr( $submission_request['compare_country'] ) : '';
	$compare_itinerary_post_id = ( isset( $submission_request['compare_itinerary'] ) && '' !== $submission_request['compare_itinerary'] ) ? esc_attr( $submission_request['compare_itinerary'] ) : 0;
	$chart_type                = isset( $submission_request['chart_type'] ) ? esc_attr( $submission_request['chart_type'] ) : '';
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Statistics', 'wp-travel' ); ?></h2>
		<div class="stat-toolbar">
				<form name="stat_toolbar" class="stat-toolbar-form" action="" method="get" >
					<input type="hidden" name="_nonce" value="<?php echo esc_attr( WP_Travel::create_nonce() ); ?>" />
					<input type="hidden" name="post_type" value="itinerary-booking" >
					<input type="hidden" name="page" value="booking_chart">
					<p class="field-group full-width">
						<span class="field-label"><?php esc_html_e( 'Display Chart', 'wp-travel' ); ?>:</span>
						<select name="chart_type" >
							<option value="booking" <?php selected( 'booking', $chart_type ); ?> ><?php esc_html_e( 'Booking', 'wp-travel' ); ?></option>
							<option value="payment" <?php selected( 'payment', $chart_type ); ?> ><?php esc_html_e( 'Payment', 'wp-travel' ); ?></option>
						</select>
					</p>
					<?php
					// @since 1.0.6 // Hook since
					do_action( 'wp_travel_before_stat_toolbar_fields' ); // phpcs:ignore
					do_action( 'wptravel_before_stat_toolbar_fields' );
					?>
					<div class="show-all compare">
						<p class="show-compare-stat">
						<span class="checkbox-default-design">
							<span class="field-label"><?php esc_html_e( 'Compare Stat', 'wp-travel' ); ?>:</span>
							<label data-on="ON" data-off="OFF">
								<input id="compare-stat" type="checkbox" name="compare_stat" value="yes" <?php checked( 'yes', $compare_stat ); ?>>
								<span class="switch">
								</span>
							</label>
						</span>

						</p>
					</div>
					<div class="form-compare-stat clearfix">
						<!-- Field groups -->
						<p class="field-group field-group-stat">
							<span class="field-label"><?php esc_html_e( 'From', 'wp-travel' ); ?>:</span>
							<input type="text" name="booking_stat_from" class="datepicker-from" class="form-control" value="<?php echo esc_attr( $from_date ); ?>" id="fromdate1" />
							<label class="input-group-addon btn" for="fromdate1">
							<span class="dashicons dashicons-calendar-alt"></span>
							</label>
						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php esc_html_e( 'To', 'wp-travel' ); ?>:</span>
							<input type="text" name="booking_stat_to" class="datepicker-to" class="form-control" value="<?php echo esc_attr( $to_date ); ?>" id="fromdate2" />
							<label class="input-group-addon btn" for="fromdate2">
							<span class="dashicons dashicons-calendar-alt"></span>
							</label>
						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php esc_html_e( 'Country', 'wp-travel' ); ?>:</span>

							<select class="selectpicker form-control" name="booking_country">

								<option value=""><?php esc_html_e( 'All Country', 'wp-travel' ); ?></option>

								<?php foreach ( $country_list as $key => $value ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $selected_country ); ?>>
										<?php echo esc_html( $value ); ?>
									</option>
								<?php endforeach; ?>
							</select>

						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php echo esc_html( WP_TRAVEL_POST_TITLE ); ?>:</span>
							<select class="selectpicker form-control" name="booking_itinerary">
								<option value="">
								<?php
								esc_html_e( 'All ', 'wp-travel' );
								echo esc_html( WP_TRAVEL_POST_TITLE_SINGULAR );
								?>
								</option>
								<?php foreach ( $wp_travel_itinerary_list as $trip_id => $itinerary_name ) : ?>
									<option value="<?php echo esc_attr( $trip_id ); ?>" <?php selected( $wp_travel_post_id, $trip_id ); ?>>
										<?php echo esc_html( $itinerary_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</p>

						<?php
						// @since 1.0.6 // Hook since
						do_action( 'wp_travel_after_stat_toolbar_fields' ); // phpcs:ignore
						do_action( 'wptravel_after_stat_toolbar_fields' );
						?>
						<div class="show-all btn-show-all" style="display:<?php echo esc_attr( 'yes' === $compare_stat ? 'none' : 'block' ); ?>" >
							<?php submit_button( esc_attr__( 'Show All', 'wp-travel' ), 'primary', 'submit' ); ?>
						</div>

					</div>

					<?php $field_group_display = ( 'yes' === $compare_stat ) ? 'block' : 'none'; ?>
					<div class="additional-compare-stat clearfix">
					<!-- Field groups to compare -->
					<p class="field-group field-group-compare" style="display:<?php echo esc_attr( $field_group_display ); ?>" >
						<span class="field-label"><?php esc_html_e( 'From', 'wp-travel' ); ?>:</span>
						<input type="text" name="compare_stat_from" class="datepicker-from" class="form-control" value="<?php echo esc_attr( $compare_from_date ); ?>" id="fromdate3" />
						<label class="input-group-addon btn" for="fromdate3">
						<span class="dashicons dashicons-calendar-alt"></span>
						</label>
					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ); ?>" >
						<span class="field-label"><?php esc_html_e( 'To', 'wp-travel' ); ?>:</span>
						<input type="text" name="compare_stat_to" class="datepicker-to" class="form-control" value="<?php echo esc_attr( $compare_to_date ); ?>" id="fromdate4" />
						<label class="input-group-addon btn" for="fromdate4">
						<span class="dashicons dashicons-calendar-alt"></span>
						</label>
					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ); ?>" >
						<span class="field-label"><?php esc_html_e( 'Country', 'wp-travel' ); ?>:</span>

						<select class="selectpicker form-control" name="compare_country">

							<option value=""><?php esc_html_e( 'All Country', 'wp-travel' ); ?></option>

							<?php foreach ( $country_list as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $compare_selected_country ); ?>>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>

					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ); ?>" >
						<span class="field-label"><?php echo esc_html( WP_TRAVEL_POST_TITLE ); ?>:</span>
						<select class="selectpicker form-control" name="compare_itinerary">
							<option value="">
							<?php
							esc_html_e( 'All ', 'wp-travel' );
							echo esc_html( WP_TRAVEL_POST_TITLE_SINGULAR );
							?>
							</option>
							<?php foreach ( $wp_travel_itinerary_list as $trip_id => $itinerary_name ) : ?>
								<option value="<?php echo esc_attr( $trip_id ); ?>" <?php selected( $compare_itinerary_post_id, $trip_id ); ?>>
									<?php echo esc_html( $itinerary_name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
					<div class="compare-all field-group-compare" style="display:<?php echo esc_attr( $field_group_display ); ?>">
						<?php submit_button( esc_attr__( 'Compare', 'wp-travel' ), 'primary', 'submit' ); ?>
					</div>
					</div>

				</form>
			</div>
		<div class="left-block stat-toolbar-wrap">

		</div>
		<div class="left-block">
			<canvas id="wp-travel-booking-canvas"></canvas>
		</div>
		<div class="right-block <?php echo esc_attr( isset( $submission_request['compare_stat'] ) && 'yes' === $submission_request['compare_stat'] ? 'has-compare' : '' ); ?>">

			<div class="wp-travel-stat-info">
				<?php if ( isset( $submission_request['compare_stat'] ) && 'yes' === $submission_request['compare_stat'] ) : ?>
				<div class="right-block-single for-compare">
					<h3><?php esc_html_e( 'Compare 1', 'wp-travel' ); ?></h3>
				</div>
				<?php endif; ?>

				<div class="right-block-single">
					<strong><big><?php echo esc_attr( wptravel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales">0</big></strong><br />
					<p><?php esc_html_e( 'Total Sales', 'wp-travel' ); ?></p>
				</div>

				<div class="right-block-single">
					<strong><big class="wp-travel-max-bookings">0</big></strong><br />
					<p><?php esc_html_e( 'Bookings', 'wp-travel' ); ?></p>

				</div>
				<div class="right-block-single">
					<strong><big  class="wp-travel-max-pax">0</big></strong><br />
					<p><?php esc_html_e( 'Pax', 'wp-travel' ); ?></p>
				</div>
				<div class="right-block-single">
					<strong class="wp-travel-top-countries wp-travel-more"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></strong>
					<p><?php esc_html_e( 'Countries', 'wp-travel' ); ?></p>
				</div>
				<div class="right-block-single">
					<strong><a href="#" class="wp-travel-top-itineraries" target="_blank"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></a></strong>
					<p><?php esc_html_e( 'Top itinerary', 'wp-travel' ); ?></p>
				</div>
			</div>
			<?php if ( isset( $submission_request['compare_stat'] ) && 'yes' === $submission_request['compare_stat'] ) : ?>

				<div class="wp-travel-stat-info">
					<div class="right-block-single for-compare">
						<h3><?php esc_html_e( 'Compare 2', 'wp-travel' ); ?></h3>
					</div>
					<div class="right-block-single">
						<strong><big><?php echo esc_html( wptravel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales-compare">0</big></strong><br />
						<p><?php esc_html_e( 'Total Sales', 'wp-travel' ); ?></p>
					</div>
					<div class="right-block-single">
						<strong><big class="wp-travel-max-bookings-compare">0</big></strong><br />
						<p><?php esc_html_e( 'Bookings', 'wp-travel' ); ?></p>

					</div>
					<div class="right-block-single">
						<strong><big  class="wp-travel-max-pax-compare">0</big></strong><br />
						<p><?php esc_html_e( 'Pax', 'wp-travel' ); ?></p>
					</div>
					<div class="right-block-single">
						<strong class="wp-travel-top-countries-compare wp-travel-more"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></strong>
						<p><?php esc_html_e( 'Countries', 'wp-travel' ); ?></p>
					</div>
					<div class="right-block-single">
						<strong><a href="#" class="wp-travel-top-itineraries-compare" target="_blank"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></a></strong>
						<p><?php esc_html_e( 'Top itinerary', 'wp-travel' ); ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
