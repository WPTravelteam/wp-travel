<?php
/**
 * Booking Class
 *
 * @since 4.4.2
 *
 * @package WP_Travel
 */

/**
 * WP_Travel_Admin_Booking Class.
 */
class WP_Travel_Admin_Booking {
	/**
	 * Private var $post_type.
	 *
	 * @var string
	 */
	private static $post_type = 'itinerary-booking';
	/**
	 * Constructor WP_Travel_Admin_Booking.
	 */
	public function __construct() {
		// ADMIN COLUMN - Title/Header.
		add_filter( 'manage_edit-itinerary-booking_columns', array( $this, 'booking_columns' ) );
		// ADMIN COLUMN - Content.
		add_action( 'manage_itinerary-booking_posts_custom_column', array( $this, 'booking_columns_content' ), 10, 2 );

		// Sort Admin Column - Title/Header.
		add_filter( 'manage_edit-itinerary-booking_sortable_columns', array( $this, 'booking_columns_sort' ) );
		// Sort ADMIN COLUMN - Content.
		add_filter( 'request', array( $this, 'booking_columns_content_sort' ) );

		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 ); // Add Custom Metabox.
		add_action( 'save_post', array( $this, 'save' ) );

	}

	/**
	 * To hide visibility, rivision in the publish meta.
	 */
	public function internal_style() {
		if ( get_current_screen()->base !== 'dashboard_page_wp-travel-setup-page' ) { 
			return;
		}
		global $post;
		if ( 'itinerary-booking' === $post->post_type ) : ?>
			<style type="text/css">
				#visibility { display: none; }
				#minor-publishing-actions,
				#misc-publishing-actions .misc-pub-section.misc-pub-post-status,
				#misc-publishing-actions .misc-pub-section.misc-pub-curtime{display:none}
			</style> 
			<?php
		endif;
	}

	/**
	 * Customize Admin column.
	 *
	 * @param Array $booking_columns List of columns.
	 * @since 4.4.2
	 * @return Array                  [description]
	 */
	public function booking_columns( $booking_columns ) {
		if ( ! $booking_columns ) {
			return;
		}
		$new_columns['cb']             = '<input type="checkbox" />';
		$new_columns['title']          = _x( 'Title', 'column name', 'wp-travel' );
		$new_columns['trip_code']      = __( 'Trip Code', 'wp-travel' );
		$new_columns['contact_name']   = __( 'Contact Name', 'wp-travel' );
		$new_columns['booking_status'] = __( 'Booking Status', 'wp-travel' );
		$new_columns['date']           = __( 'Booking Date', 'wp-travel' );
		return $new_columns;
	}

	/**
	 * Add data to custom column.
	 *
	 * @param String $column_name Custom column name.
	 * @since 4.4.2
	 * @param int    $id          Post ID.
	 */
	public function booking_columns_content( $column_name, $id ) {
		switch ( $column_name ) {
			case 'trip_code':
				$trip_id   = get_post_meta( $id, 'wp_travel_post_id', true );
				$trip_code = wptravel_get_trip_code( $trip_id );
				echo esc_attr( $trip_code );
				break;

			case 'contact_name':
				$full_name = get_post_meta( $id, 'wp_travel_fullname', true ); // @since 4.4.2. used for sorting and display the full name in the admin column.

				if ( ! $full_name ) {
					$first_name = get_post_meta( $id, 'wp_travel_fname_traveller', true );
					if ( ! $first_name ) {
						$first_name = get_post_meta( $id, 'wp_travel_fname', true ); // Legacy version less than WP Travel 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
					}
					$middle_name = get_post_meta( $id, 'wp_travel_mname_traveller', true );
					if ( ! $middle_name ) {
						$middle_name = get_post_meta( $id, 'wp_travel_mname', true );
					}
					$last_name = get_post_meta( $id, 'wp_travel_lname_traveller', true );
					if ( ! $last_name ) {
						$last_name = get_post_meta( $id, 'wp_travel_mname', true );
					}

					if ( is_array( $first_name ) ) { // Multiple Travelers [Extracting Traveller name from array].
						reset( $first_name );
						$first_key = key( $first_name );

						$full_name = '';
						if ( isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ) {
							$full_name .= $first_name[ $first_key ][0];
						}
						if ( isset( $middle_name[ $first_key ] ) && isset( $middle_name[ $first_key ][0] ) ) {
							$full_name .= ' ' . $middle_name[ $first_key ][0];
						}
						if ( isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ) {
							$full_name .= ' ' . $last_name[ $first_key ][0];
						}
					} else {
						$full_name  = $first_name;
						$full_name .= ' ' . $middle_name;
						$full_name .= ' ' . $last_name;
					}
					update_post_meta( $id, 'wp_travel_fullname', $full_name );
				}
				echo esc_attr( $full_name );
				break;
			case 'booking_status':
				$status    = wptravel_get_booking_status();
				$label_key = get_post_meta( $id, 'wp_travel_booking_status', true );
				if ( '' === $label_key ) {
					$label_key = 'pending';
					update_post_meta( $id, 'wp_travel_booking_status', $label_key );
				}
				echo '<span class="wp-travel-status wp-travel-booking-status" style="background: ' . esc_attr( $status[ $label_key ]['color'] ) . ' ">' . esc_attr( $status[ $label_key ]['text'] ) . '</span>';
				break;
			default:
				break;
		} // end switch
	}

	/**
	 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE.
	 *
	 * @param array $columns Custom column in the booking list.
	 * @since 4.4.2
	 */
	public function booking_columns_sort( $columns ) {
		$custom = array(
			'contact_name'   => 'contact_name',
			'booking_status' => 'booking_status',
		);
		return wp_parse_args( $custom, $columns ); // add $custom into $columns.
	}

	/**
	 * Manage Order By custom column.
	 *
	 * @param  Array $vars Order By array.
	 * @since 4.4.2
	 * @return Array       Order By array.
	 */
	public function booking_columns_content_sort( $vars ) {
		if ( isset( $vars['orderby'] ) && 'contact_name' === $vars['orderby'] ) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'wp_travel_fullname',
					'orderby'  => 'meta_value',
				)
			);
		}
		return $vars;
	}

	/**
	 * Register metabox.
	 *
	 * @since 4.4.2
	 */
	public function register_metaboxes() {		

		if ( get_current_screen()->base == 'woocommerce_page_wc-orders' ) { 
			return;
		}
		
		global $post;
		// Booking Metabox.
		$wp_travel_post_id = get_post_meta( $post->ID, 'wp_travel_post_id', true ); // Trip ID.
		add_meta_box( 'wp-travel-booking-info', __( 'Booking Detail <span class="wp-travel-view-bookings"><a href="edit.php?post_type=itinerary-booking&wp_travel_post_id=' . $wp_travel_post_id . '">View All ' . get_the_title( $wp_travel_post_id ) . ' Bookings</a></span>', 'wp-travel' ), array( $this, 'booking_info' ), 'itinerary-booking', 'normal', 'default' );
		add_action( 'admin_head', array( $this, 'internal_style' ) );

		
	}

	/**
	 * Call back for booking metabox.
	 *
	 * @param Object $post Post object.
	 * @since 4.4.2
	 */
	public function booking_info( $post ) {
		if ( ! $post ) {
			return $post;
		}
		if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
			include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
		}

		$nonce_value = WP_Travel::create_nonce();

		$form       = new WP_Travel_FW_Form();
		$form_field = new WP_Travel_FW_Field();
		$booking_id = $post->ID;

		$edit_link = get_admin_url() . 'post.php?post=' . $post->ID . '&action=edit';
		$edit_link = add_query_arg( 'edit_booking', 1, $edit_link );
		$edit_link = add_query_arg( '_nonce', $nonce_value, $edit_link );

		// 2. Edit Booking Section. Already checking nonce above.
		if ( WP_Travel::verify_nonce( true ) && isset( $_GET['edit_booking'] ) || ( isset( $_GET['post_type'] ) && 'itinerary-booking' === $_GET['post_type'] ) ) {
			$checkout_fields  = wptravel_get_checkout_form_fields();
			$traveller_fields = isset( $checkout_fields['traveller_fields'] ) ? $checkout_fields['traveller_fields'] : array();
			$billing_fields   = isset( $checkout_fields['billing_fields'] ) ? $checkout_fields['billing_fields'] : array();
			$payment_fields   = isset( $checkout_fields['payment_fields'] ) ? $checkout_fields['payment_fields'] : array();

			$wp_travel_post_id           = get_post_meta( $booking_id, 'wp_travel_post_id', true );
			$ordered_data                = get_post_meta( $booking_id, 'order_data', true );
			$payment_id                  = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
			$booking_option              = get_post_meta( $payment_id, 'wp_travel_booking_option', true );
			$multiple_trips_booking_data = get_post_meta( $booking_id, 'order_items_data', true );
			?> 
			<div class="wp-travel-booking-form-wrapper" >
				<?php
				do_action( 'wp_travel_booking_before_form_field' );

				$trip_field_args = array(
					'label'         => esc_html( ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ) ),
					'name'          => 'wp_travel_post_id',
					'id'            => 'wp-travel-post-id',
					'type'          => 'select',
					'class'         => 'wp-travel-select2',
					'options'       => wptravel_get_itineraries_array(),
					'wrapper_class' => 'full-width',
					'default'       => $wp_travel_post_id,
				);
				$form_field->init( $trip_field_args, array( 'single' => true ) )->render();
				
				$args       = array( 'trip_id' => $booking_id ); // why booking id ??
				$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );

				if ( '' === $trip_price || '0' === $trip_price ) {
					unset( $payment_fields['is_partial_payment'], $payment_fields['booking_option'], $payment_fields['payment_gateway'], $payment_fields['trip_price'], $payment_fields['payment_mode'], $payment_fields['trip_price_info'], $payment_fields['payment_amount_info'], $payment_fields['payment_amount'] );
				}

				if ( 'booking_only' === $booking_option ) {
					unset( $payment_fields['is_partial_payment'], $payment_fields['payment_gateway'], $payment_fields['payment_mode'], $payment_fields['payment_amount'], $payment_fields['payment_amount_info'] );
				}
				// Sort fields.
				$traveller_fields = wptravel_sort_form_fields( $traveller_fields );
				$billing_fields   = wptravel_sort_form_fields( $billing_fields );
				$payment_fields   = wptravel_sort_form_fields( $payment_fields );

				// Travelers Fields HTML
				$field_name = $traveller_fields['first_name']['name'];
				$input_val  = get_post_meta( $booking_id, $field_name, true );

				if ( ! $input_val ) {
					// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
					$field_name = str_replace( '_traveller', '', $field_name );
					$input_val  = get_post_meta( $booking_id, $field_name, true );
				}

				?><div id="wpcrm-added-code" ></div> <?php
				if ( $input_val && is_array( $input_val ) ) { // Multiple Travelers Section.
					foreach ( $input_val as $cart_id => $field_fname_values ) {
						$trip_id   = isset( $multiple_trips_booking_data[ $cart_id ]['trip_id'] ) ? $multiple_trips_booking_data[ $cart_id ]['trip_id'] : 0;
						$price_key = isset( $multiple_trips_booking_data[ $cart_id ]['price_key'] ) ? $multiple_trips_booking_data[ $cart_id ]['price_key'] : '';
						echo '<h3>' . wptravel_get_trip_pricing_name( $trip_id, $price_key ) . '</h3>'; //phpcs:ignore
						foreach ( $field_fname_values as $i => $field_fname_value ) {
							?>
							
							<div class="wp-travel-form-field-wrapper">
								<?php
								if ( 0 === $i ) {
									?>
									<h3><?php esc_html_e( 'Lead Traveler', 'wp-travel' ); ?></h3>
									<?php
								} else {
									?>
									<h3><?php printf( esc_html__( 'Traveler %d', 'wp-travel' ), ( $i + 1 ) ); ?></h3>
									<?php
								}

								foreach ( $traveller_fields as $field_group => $field ) {
									$field['id'] = $field['id'] . '-' . $cart_id . '-' . $i;

									$current_field_name   = $field['name'];
									$current_field_values = get_post_meta( $booking_id, $current_field_name, true );

									if ( ! $current_field_values ) {
										// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
										$current_field_name   = str_replace( '_traveller', '', $current_field_name );
										$current_field_values = get_post_meta( $booking_id, $current_field_name, true );
									}

									$current_field_value = isset( $current_field_values[ $cart_id ] ) && isset( $current_field_values[ $cart_id ][ $i ] ) ? $current_field_values[ $cart_id ][ $i ] : '';

									// @since 1.8.3
									if ( 'date' === $field['type'] && '' !== $current_field_value && ! wptravel_is_ymd_date( $current_field_value ) ) {
										$current_field_value = wptravel_format_ymd_date( $current_field_value, 'm/d/Y' );
									}

									$field_name       = sprintf( '%s[%s][%d]', $field['name'], $cart_id, $i );
									$field['name']    = $field_name;
									$field['default'] = $current_field_value;
									// Set required false to extra travellers.
									$field['validations']['required'] = ! empty( $field['validations']['required'] ) ? $field['validations']['required'] : false;
									$field['validations']['required'] = $i > 0 ? false : $field['validations']['required'];
									$form_field->init( $field, array( 'single' => true ) )->render();
								}
								?>
							</div>
							<?php
						}
					}
				} else {
					?>
					<div class="wp-travel-form-field-wrapper">
						<?php
						// single foreach for legacy version.
						$cart_id = wp_rand();
						foreach ( $traveller_fields as $field_group => $field ) {
							$input_val = get_post_meta( $booking_id, $field['name'], true );
							if ( ! $input_val ) {
								// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
								$field_name = str_replace( '_traveller', '', $field['name'] );
								$input_val  = get_post_meta( $booking_id, $field_name, true );
							}
							// @since 1.8.3
							if ( 'date' === $field['type'] && '' !== $input_val && ! wptravel_is_ymd_date( $input_val ) ) {
								$input_val = wptravel_format_ymd_date( $input_val, 'm/d/Y' );
							}
							$field['default'] = $input_val;

							// @since 2.0.1
							$field_name    = sprintf( '%s[%s][0]', $field['name'], $cart_id );
							$field['name'] = $field_name;

							$form_field->init( $field, array( 'single' => true ) )->render();
						}
						?>
					</div>
					<?php
				}
				?>
				<div class="wp-travel-form-field-wrapper">
					<?php
						$arrival_date     = isset( $multiple_trips_booking_data[ $cart_id ]['arrival_date'] ) ? $multiple_trips_booking_data[ $cart_id ]['arrival_date'] : '';
						$departure_date   = isset( $multiple_trips_booking_data[ $cart_id ]['departure_date'] ) ? $multiple_trips_booking_data[ $cart_id ]['departure_date'] : '';
						$pax              = isset( $multiple_trips_booking_data[ $cart_id ]['pax'] ) ? $multiple_trips_booking_data[ $cart_id ]['pax'] : '';
						$booking_fields   = array();
						$booking_fields[] = array(
							'label'         => esc_html( 'Arrival Date' ),
							'name'          => 'arrival_date',
							'type'          => 'date',
							'class'         => 'wp-travel-datepicker',
							'validations'   => array(
								'required' => true,
							),
							'attributes'    => array( 'readonly' => 'readonly' ),
							'wrapper_class' => '',
							'default'       => $arrival_date,

						);
						$booking_fields[] = array(
							'label'         => esc_html( 'Departure Date' ),
							'name'          => 'departure_date',
							'type'          => 'date',
							'class'         => 'wp-travel-datepicker',
							'validations'   => array(
								'required' => true,
							),
							'attributes'    => array( 'readonly' => 'readonly' ),
							'wrapper_class' => '',
							'default'       => $departure_date,
						);
						$booking_fields[] = array(
							'label'         => esc_html( 'Pax' ),
							'name'          => 'pax',
							'type'          => 'number',
							'class'         => '',
							'wrapper_class' => '',
							'default'       => $pax,
						);
						$booking_fields[] = array(
							'label'         => null,
							'name'          => '_nonce',
							'type'          => 'hidden',
							'class'         => '',
							'wrapper_class' => '',
							'default'       => $nonce_value,
						);
						foreach ( $booking_fields as $field ) {
							$form_field->init( $field, array( 'single' => true ) )->render();
						}

						?>

				</div>
				<div class="wp-travel-form-field-wrapper">
					<?php
					// Billing Fields HTML.
					unset( $billing_fields['price-unavailable'] );
					foreach ( $billing_fields as $field_group => $field ) {
						if( isset($field['name']) ){
							$field['default'] = get_post_meta( $booking_id, $field['name'], true );
							$form_field->init( $field, array( 'single' => true ) )->render();
						}
					}
					?>
				</div>

				<?php
				$form->init_validation( 'post' );
				wp_enqueue_script( 'jquery-datepicker-lib' );
				wp_enqueue_script( 'jquery-datepicker-lib-eng' );
				?>
				<script>
					jQuery(document).ready( function($){
						$(".wp-travel-date").wpt_datepicker({
								language: "en",
								minDate: new Date()
							});
					} )
				</script>
				<?php do_action( 'wp_travel_booking_after_form_field' ); ?>
			</div>
			<?php

		} else { // 1. Display Booking info fields.
			$details = wptravel_booking_data( $booking_id );

			if ( is_array( $details ) && count( $details ) > 0 ) {
				?>
				<input type="hidden" name="_nonce" value="<?php echo esc_attr( WP_Travel::create_nonce() ); ?>" />
				<div class="my-order my-order-details">
					<div class="view-order">
						<div class="order-list">
							<div class="order-wrapper">
								<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $edit_link ); ?>"><?php esc_html_e( 'Edit', 'wp-travel' ); ?></a></h3>
								<?php do_action( 'wp_travel_booking_metabox_after_title', $booking_id ); // @since 3.0.6 ?>
								<?php wptravel_view_booking_details_table( $booking_id, true ); ?>
							</div>
							<?php echo WpTravel_Helpers_Payment::render_payment_details( $booking_id ); // @phpcs:ignore ?>
						</div>
					</div>
				</div>
				<?php
			}
		}

	}

	/**
	 * Save Bookings.
	 *
	 * @since 4.4.2
	 * @param int $booking_id Booking ID.
	 */
	public function save( $booking_id ) {

		if ( ! WP_Travel::verify_nonce( true ) ) {
			return $booking_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $booking_id ) ) {
			return;
		}
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $booking_id ) ) {
			return;
		}

		$post_type = get_post_type( $booking_id );

		// If this isn't a 'itineraries' post, don't update it.
		if ( 'itinerary-booking' !== $post_type ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}
		$order_data        = array();
		$wp_travel_post_id = isset( $_POST['wp_travel_post_id'] ) ? absint( $_POST['wp_travel_post_id'] ) : 0;
		$testing_travel_info = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );
		if ( empty( $testing_travel_info ) ) {
			update_post_meta( $booking_id, 'wp_travel_backend_booking_add', 'yes' );
		}
		// For not modifying trip id after 'select' booked form booking admin section.
		if ( $wp_travel_post_id ) {
			update_post_meta( $booking_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
		}
		// Updating booking status.
		$booking_status = isset( $_POST['wp_travel_booking_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_booking_status'] ) ) : 'pending';
		$mail_sending_payment_status = apply_filters( 'wp_travel_change_payment_status_mail_sending', true );
		$mail_sending_booking_status = apply_filters( 'wp_travel_change_booking_status_mail_sending', true );
		$payment_status = isset( $_POST['wp_travel_payment_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_payment_status'] ) ) : 'N/A';
		$new_status = sanitize_text_field( $payment_status );
		$old_payment_status = get_post_meta( $booking_id, 'wp_travel_payment_status' );
		$old_status = isset( $old_payment_status[0] ) ? $old_payment_status[0] : 'N/A';

		if( $old_status == 'waiting_voucher' ){
			$old_status = __( 'waiting for voucher', 'wp-travel' );
		}elseif( $old_status == 'partially_paid' ){
			$old_status = __( 'partially paid', 'wp-travel' );
		}elseif( $old_status == 'voucher_submited' ){
			$old_status = __( 'voucher submited', 'wp-travel' );
		}

		if( $new_status == 'waiting_voucher' ){
			$new_status = __( 'waiting for voucher', 'wp-travel' );
		}elseif( $new_status == 'partially_paid' ){
			$new_status = __( 'partially paid', 'wp-travel' );
		}elseif( $new_status == 'voucher_submited' ){
			$new_status = __( 'Voucher submited', 'wp-travel' );
		}

		if ( $mail_sending_payment_status ) {
			if ( $new_status != $old_status ) {
				$traveler_email = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );
				$traveler_name = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
				$subject = 'Booking payment status update';
				$email_headers = "Content-Type: text/html; charset=UTF-8\r\n";
				foreach ( $traveler_email as $trip_key => $email_detail ) {
					$traveller_name = isset( $traveler_name[$trip_key] ) && isset( $traveler_name[$trip_key][0] ) ? $traveler_name[$trip_key][0] : '';
					$message = "<h2>Dear {$traveller_name},</h2><p>We've acknowledged receiving your payment detail for your trip booking. Your payment status has now been updated from {$old_status} to {$new_status}.</p><br><h3>Thank you.</h3>";
					
					$message = apply_filters( 'wptravel_email_content_for_payment_status', $message, $traveller_name, $old_status, $new_status );
					if ( ! wp_mail( $email_detail[0], $subject, $message, $email_headers ) ) {

					}
				}
			}
		}

		$old_status = isset( $_POST['wp_travel_booking_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_booking_status'] ) ) : 'N/A';
		$new_status = sanitize_text_field( $old_status );
		$old_booking_status = get_post_meta( $booking_id, 'wp_travel_booking_status' );
		$old_status = isset( $old_booking_status[0] ) ? $old_booking_status[0] : 'N/A';
		if ( $mail_sending_booking_status ) {
			if ( $new_status != $old_status ) {
				$traveler_email = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );
				$traveler_name = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
				$subject = 'Booking status update';
				$email_headers = "Content-Type: text/html; charset=UTF-8\r\n";
				foreach ( $traveler_email as $trip_key => $email_detail ) {
					$traveler_email = isset( $traveler_name[$trip_key] ) && isset( $traveler_name[$trip_key][0] ) ? $traveler_name[$trip_key][0] : '';
					$message = "<h2>Dear {$traveller_name},</h2><p>We are reaching out to inform you about some recent changes to your booking with us. Your booking status has been updated from <b>" . $old_status . "</b> to <b>" . $new_status ."</b>.</p><br><h3>Thank you.</h3>";
					$message = apply_filters( 'wptravel_email_content_for_booking_status', $message, $traveller_name, $old_status, $new_status );
					if ( ! wp_mail( $email_detail[0], $subject, $message, $email_headers ) ) {

					}
				}
			}
		}

		update_post_meta( $booking_id, 'wp_travel_booking_status', sanitize_text_field( $new_status ) );
		$checkout_fields = wptravel_get_checkout_form_fields();
		foreach ( $checkout_fields as $field_type => $fields ) {
			$priority = array();
			foreach ( $fields as $key => $row ) {
				$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
			}
			array_multisort( $priority, SORT_ASC, $fields );
			foreach ( $fields as $key => $field ) :
				// This $meta_val is sanitized latter.
				$meta_val   = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : ''; // @phpcs:ignore
				$booking_id = apply_filters( 'wp_travel_booking_post_id_to_update', $booking_id, $key, $field['name'] );
				if ( $meta_val ) {

					if ( is_array( $meta_val ) ) {
						$new_meta_value = array();
						foreach ( $meta_val as $key => $value ) {
							if ( is_array( $value ) ) {
								$new_meta_value[ $key ] = array_map( 'sanitize_text_field', $value );
							} else {
								$new_meta_value[ $key ] = sanitize_text_field( $value );
							}
						}
						update_post_meta( $booking_id, $field['name'], $new_meta_value );
					} else {
						update_post_meta( $booking_id, $field['name'], sanitize_text_field( $meta_val ) );
					}
				}
				$order_data[ $field['name'] ] = $meta_val;
			endforeach;
		}
		update_post_meta( $booking_id, 'order_data', $order_data ); // We will use only travellers info from here. for more detail about payment use order_items_data meta.

		// Prepare data for order_items_data [Need cart id to set order_items_data ].
		if ( isset( $_POST['wp_travel_fname_traveller'] ) && is_array( $_POST['wp_travel_fname_traveller'] ) ) {
			$order_items_data = get_post_meta( $booking_id, 'order_items_data', true );
			if ( ! $order_items_data ) {
				$order_items_data = array();
			}

			$firsname_traveller = wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_fname_traveller'] ) );
			foreach ( $firsname_traveller as $cart_id => $v ) {
				$pax            = isset( $_POST['pax'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['pax'] ) ) : 0;
				$arrival_date   = isset( $_POST['arrival_date'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['arrival_date'] ) ) : '';
				$departure_date = isset( $_POST['departure_date'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['departure_date'] ) ) : '';

				$order_items_data[ $cart_id ]['trip_id']        = $wp_travel_post_id;
				$order_items_data[ $cart_id ]['pax']            = $pax;
				$order_items_data[ $cart_id ]['arrival_date']   = $arrival_date;
				$order_items_data[ $cart_id ]['departure_date'] = $departure_date;
				// $order_items_data[ $cart_id ]['currency'] = $wp_travel_post_id;
			}
			update_post_meta( $booking_id, 'order_items_data', $order_items_data ); // use this instead of order_data meta.
		}
		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		$payment_data = wptravel_booking_data( $booking_id );
		$partial_mode = get_post_meta( $booking_id, 'wp_travel_is_partial_payment', true );
		$due_amount = isset( $payment_data['due_amount'] ) ? $payment_data['due_amount'] : 0;
		$paid_amount = isset( $payment_data['paid_amount'] ) ? $payment_data['paid_amount'] : 0;
		$total = isset( $payment_data['total'] ) ? $payment_data['total'] : 0;
		
		if ( $booking_status == 'booked' &&  $new_status == 'paid' ) {
			if ( $due_amount > 0 && $partial_mode != 'yes' && $due_amount == $total ) {
				update_post_meta( $payment_id, 'wp_travel_real_payment_amount_change_status', $total );
				update_post_meta( $payment_id, 'wp_travel_payment_amount', $due_amount );
				update_post_meta( $payment_id, 'wp_travel_payment_mode', 'full' );
			}
		} else {
			$old_payment_data = get_post_meta( $payment_id, 'wp_travel_real_payment_amount_change_status', true );
			if ( ! empty( $old_payment_data ) && $old_payment_data > 0 ) {
				update_post_meta( $payment_id, 'wp_travel_real_payment_amount_change_status', 0 );
				update_post_meta( $payment_id, 'wp_travel_payment_amount', 0 );
				update_post_meta( $payment_id, 'wp_travel_payment_mode', 'N/A' );
			}
		}
		do_action( 'wp_travel_after_booking_data_save', $booking_id ); // update payment status.
		$affiliate_status = apply_filters( 'wp_travel_booking_status_trigger_from', $booking_id, $booking_status );

	}
}
new WP_Travel_Admin_Booking();
