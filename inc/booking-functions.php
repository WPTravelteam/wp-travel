<?php
/**
 * Booking Functions.
 *
 * @package wp-travel/inc/
 */
/**
 * Return HTM of Booking Form
 *
 * @return [type] [description]
 */
function wp_travel_get_booking_form() {
	global $post;

	$trip_id = 0;
	if ( isset( $_REQUEST['trip_id'] ) ) {
		$trip_id = $_REQUEST['trip_id'];
	} elseif ( isset( $_POST['wp_travel_post_id'] ) ) {
		$trip_id = $_POST['wp_travel_post_id'];
	} elseif ( isset( $post->ID ) ) {
		$trip_id = $post->ID;
	}
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	$form_options = array(
		'id'            => 'wp-travel-booking',
		'wrapper_class' => 'wp-travel-booking-form-wrapper',
		'submit_button' => array(
			'name'  => 'wp_travel_book_now',
			'id'    => 'wp-travel-book-now',
			'value' => __( 'Book Now', 'wp-travel' ),
		),
		'nonce'         => array(
			'action' => 'wp_travel_security_action',
			'field'  => 'wp_travel_security',
		),
	);

	$fields = wp_travel_booking_form_fields();

	// GDPR Support
	$settings = wp_travel_get_settings();

	$gdpr_msg = isset( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );

	$policy_link = wp_travel_privacy_link();
	if ( ! empty( $gdpr_msg ) && $policy_link ) {

		// GDPR Compatibility for enquiry.
		$fields['wp_travel_booking_gdpr'] = array(
			'type'              => 'checkbox',
			'label'             => __( 'Privacy Policy', 'wp-travel' ),
			'options'           => array( 'gdpr_agree' => sprintf( '%1s %2s', $gdpr_msg, $policy_link ) ),
			'name'              => 'wp_travel_booking_gdpr_msg',
			'id'                => 'wp-travel-enquiry-gdpr-msg',
			'validations'       => array(
				'required' => true,
			),
			'option_attributes' => array(
				'required' => true,
			),
			'priority'          => 100,
			'wrapper_class'     => 'full-width',
		);

	}

	$form              = new WP_Travel_FW_Form();
	$fields['post_id'] = array(
		'type'    => 'hidden',
		'name'    => 'wp_travel_post_id',
		'id'      => 'wp-travel-post-id',
		'default' => $trip_id,
	);
	$fixed_departure   = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$fixed_departure   = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure   = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
	$trip_start_date   = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date     = get_post_meta( $trip_id, 'wp_travel_end_date', true );

	if ( 'yes' === $fixed_departure ) {
		$fields['arrival_date']['class']     = '';
		$fields['arrival_date']['default']   = date( 'Y-m-d', strtotime( $trip_start_date ) );
		$fields['arrival_date']['type']      = 'hidden';
		$fields['departure_date']['class']   = '';
		$fields['departure_date']['default'] = date( 'Y-m-d', strtotime( $trip_end_date ) );
		$fields['departure_date']['type']    = 'hidden';
		unset( $fields['trip_duration'] );
	}

	$trip_duration = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );

	$fields['trip_duration']['default'] = $trip_duration;
	$fields['trip_duration']['type']    = 'hidden';

	$group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );

	if ( isset( $group_size ) && '' != $group_size ) {

		$fields['pax']['validations']['max'] = $group_size;

	}

	$trip_price = wp_travel_get_actual_trip_price( $trip_id );

	if ( '' == $trip_price || '0' == $trip_price ) {

		unset( $fields['is_partial_payment'], $fields['payment_gateway'], $fields['booking_option'], $fields['trip_price'], $fields['payment_mode'], $fields['payment_amount'], $fields['trip_price_info'], $fields['payment_amount_info'] );

	}

	$form->init( $form_options )->fields( $fields )->template();
	// return apply_filters( 'wp_travel_booking_form_contents', $content );
}

add_action( 'add_meta_boxes', 'wp_travel_register_booking_metaboxes', 10, 2 );

/**
 * This will add metabox in booking post type.
 */
function wp_travel_register_booking_metaboxes( $a ) {
	global $post;
	global $wp_travel_itinerary;

	$wp_travel_post_id = get_post_meta( $post->ID, 'wp_travel_post_id', true );
	// $trip_code = $wp_travel_itinerary->get_trip_code( $wp_travel_post_id );
	add_meta_box( 'wp-travel-booking-info', __( 'Booking Detail <span class="wp-travel-view-bookings"><a href="edit.php?post_type=itinerary-booking&wp_travel_post_id=' . $wp_travel_post_id . '">View All ' . get_the_title( $wp_travel_post_id ) . ' Bookings</a></span>', 'wp-travel' ), 'wp_travel_booking_info', 'itinerary-booking', 'normal', 'default' );

	add_action( 'admin_head', 'wp_travel_admin_head_meta' );
}

/**
 * Hide publish and visibility.
 */
function wp_travel_admin_head_meta() {
	global $post;
	if ( 'itinerary-booking' === $post->post_type ) : ?>

			<style type="text/css">
				#visibility {
					display: none;
				}
				#minor-publishing-actions,
				#misc-publishing-actions .misc-pub-section.misc-pub-post-status,
				#misc-publishing-actions .misc-pub-section.misc-pub-curtime{display:none}
			</style>

		<?php
	endif;
}

/**
 * Call back for booking metabox.
 *
 * @param Object $post Post object.
 */
function wp_travel_booking_info( $post ) {
	if ( ! $post ) {
		return;
	}
	if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	}

	$form       = new WP_Travel_FW_Form();
	$form_field = new WP_Travel_FW_Field();
	$booking_id = $post->ID;

	$edit_link = get_admin_url() . 'post.php?post=' . $post->ID . '&action=edit';
	$edit_link = add_query_arg( 'edit_booking', 1, $edit_link );
	wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' );

	// 2. Edit Booking Section.
	if ( isset( $_GET['edit_booking'] ) || ( isset( $_GET['post_type'] ) && 'itinerary-booking' === $_GET['post_type'] ) ) {
		$checkout_fields  = wp_travel_get_checkout_form_fields();
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
				'options'       => wp_travel_get_itineraries_array(),
				'wrapper_class' => 'full-width',
				'default'       => $wp_travel_post_id,
			);
			$form_field->init( $trip_field_args, array( 'single' => true ) )->render();

			$trip_price = wp_travel_get_actual_trip_price( $booking_id );

			if ( '' == $trip_price || '0' == $trip_price ) {
				unset( $payment_fields['is_partial_payment'], $payment_fields['booking_option'], $payment_fields['payment_gateway'], $payment_fields['trip_price'], $payment_fields['payment_mode'], $payment_fields['trip_price_info'], $payment_fields['payment_amount_info'], $payment_fields['payment_amount'] );
			}

			if ( 'booking_only' == $booking_option ) {
				unset( $payment_fields['is_partial_payment'], $payment_fields['payment_gateway'], $payment_fields['payment_mode'], $payment_fields['payment_amount'], $payment_fields['payment_amount_info'] );
			}

			// Sort fields.
			$traveller_fields = wp_travel_sort_form_fields( $traveller_fields );
			$billing_fields   = wp_travel_sort_form_fields( $billing_fields );
			$payment_fields   = wp_travel_sort_form_fields( $payment_fields );

			// Travelers Fields HTML
			$field_name = $traveller_fields['first_name']['name'];
			$input_val  = get_post_meta( $booking_id, $field_name, true );

			if ( ! $input_val ) {
				// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
				$field_name = str_replace( '_traveller', '', $field_name );
				$input_val  = get_post_meta( $booking_id, $field_name, true );
			}

			if ( $input_val && is_array( $input_val ) ) { // Multiple Travelers Section.
				foreach ( $input_val as $cart_id => $field_fname_values ) {
					$trip_id   = isset( $multiple_trips_booking_data[ $cart_id ]['trip_id'] ) ? $multiple_trips_booking_data[ $cart_id ]['trip_id'] : 0;
					$price_key = isset( $multiple_trips_booking_data[ $cart_id ]['price_key'] ) ? $multiple_trips_booking_data[ $cart_id ]['price_key'] : '';
					echo '<h3>' . wp_travel_get_trip_pricing_name( $trip_id, $price_key ) . '</h3>';
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
								<h3><?php printf( __( 'Traveler %d', 'wp-travel' ), ( $i + 1 ) ); ?></h3>
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
								if ( 'date' === $field['type'] && '' !== $current_field_value && ! wp_travel_is_ymd_date( $current_field_value ) ) {
									$current_field_value = wp_travel_format_ymd_date( $current_field_value, 'm/d/Y' );
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
					$cart_id = rand();
					foreach ( $traveller_fields as $field_group => $field ) {
						$input_val = get_post_meta( $booking_id, $field['name'], true );
						if ( ! $input_val ) {
							// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
							$field_name = str_replace( '_traveller', '', $field['name'] );
							$input_val  = get_post_meta( $booking_id, $field_name, true );
						}
						// @since 1.8.3
						if ( 'date' === $field['type'] && '' !== $input_val && ! wp_travel_is_ymd_date( $input_val ) ) {
							$input_val = wp_travel_format_ymd_date( $input_val, 'm/d/Y' );
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
					$arrival_date   = isset( $multiple_trips_booking_data[ $cart_id ]['arrival_date'] ) ? $multiple_trips_booking_data[ $cart_id ]['arrival_date'] : '';
					$departure_date = isset( $multiple_trips_booking_data[ $cart_id ]['departure_date'] ) ? $multiple_trips_booking_data[ $cart_id ]['departure_date'] : '';
					$pax            = isset( $multiple_trips_booking_data[ $cart_id ]['pax'] ) ? $multiple_trips_booking_data[ $cart_id ]['pax'] : '';
					$booking_fields   = array();
					$booking_fields[] = array(
						'label'         => esc_html( 'Arrival Date' ),
						'name'          => 'arrival_date',
						'type'          => 'date',
						'class'        => 'wp-travel-datepicker',
						'validations'  => array(
							'required' => true,
						),
						'attributes'   => array( 'readonly' => 'readonly' ),
						'wrapper_class' => '',
						'default'       => $arrival_date,

					);
					$booking_fields[] = array(
						'label'         => esc_html( 'Departure Date' ),
						'name'          => 'departure_date',
						'type'          => 'date',
						'class'        => 'wp-travel-datepicker',
						'validations'  => array(
							'required' => true,
						),
						'attributes'   => array( 'readonly' => 'readonly' ),
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
					foreach ( $booking_fields as $field ) {
						$form_field->init( $field, array( 'single' => true ) )->render();
					}

				?>

			</div>
			<div class="wp-travel-form-field-wrapper">
				<?php
				// Billing Fields HTML
				unset( $billing_fields['price-unavailable'] );
				foreach ( $billing_fields as $field_group => $field ) {
					$field['default'] = get_post_meta( $booking_id, $field['name'], true );
					$form_field->init( $field, array( 'single' => true ) )->render();
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
		$details = wp_travel_booking_data( $booking_id );

		if ( is_array( $details ) && count( $details ) > 0 ) {
			?>
			<div class="my-order my-order-details">
				<div class="view-order">
					<div class="order-list">
						<div class="order-wrapper">
							<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $edit_link ); ?>"><?php esc_html_e( 'Edit', 'wp-travel' ); ?></a></h3>
							<?php wp_travel_view_booking_details_table( $booking_id, true ); ?>
						</div>
						<?php wp_travel_view_payment_details_table( $booking_id ); ?>
					</div>
				</div>
			</div>
			<?php
		}
	}

}


/**
 * Save Post meta data from admin.
 *
 * @param  int $trip_id ID of current post.
 *
 * @return Mixed
 */
function wp_travel_save_booking_data( $booking_id ) {
	if ( ! isset( $_POST['wp_travel_security'] ) || ! wp_verify_nonce( $_POST['wp_travel_security'], 'wp_travel_security_action' ) ) {
		return;
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
	$wp_travel_post_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
	update_post_meta( $booking_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
	// $order_data['wp_travel_post_id'] = $wp_travel_post_id; // Commented since 2.0.1
	// Updating booking status.
	$booking_status = isset( $_POST['wp_travel_booking_status'] ) ? $_POST['wp_travel_booking_status'] : 'pending';
	update_post_meta( $booking_id, 'wp_travel_booking_status', sanitize_text_field( $booking_status ) );

	$checkout_fields = wp_travel_get_checkout_form_fields();
	foreach ( $checkout_fields as $field_type => $fields ) {
		$priority = array();
		foreach ( $fields as $key => $row ) {
			$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
		}
		array_multisort( $priority, SORT_ASC, $fields );
		foreach ( $fields as $key => $field ) :
			$meta_val   = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';
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

		foreach ( $_POST['wp_travel_fname_traveller'] as $cart_id => $v ) {
			$pax            = isset( $_POST['pax'] ) ? sanitize_text_field( $_POST['pax'] ) : 0;
			$arrival_date   = isset( $_POST['arrival_date'] ) ? sanitize_text_field( $_POST['arrival_date'] ) : '';
			$departure_date = isset( $_POST['departure_date'] ) ? sanitize_text_field( $_POST['departure_date'] ) : '';

			$order_items_data[ $cart_id ]['trip_id']        = $wp_travel_post_id;
			$order_items_data[ $cart_id ]['pax']            = $pax;
			$order_items_data[ $cart_id ]['arrival_date']   = $arrival_date;
			$order_items_data[ $cart_id ]['departure_date'] = $departure_date;
			// $order_items_data[ $cart_id ]['currency'] = $wp_travel_post_id;
		}
		update_post_meta( $booking_id, 'order_items_data', $order_items_data ); // use this instead of order_data meta.
	}

	do_action( 'wp_travel_after_booking_data_save', $booking_id ); // update payment status.
}
add_action( 'save_post', 'wp_travel_save_booking_data' );

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter( 'manage_edit-itinerary-booking_columns', 'wp_travel_booking_columns' );

/**
 * Customize Admin column.
 *
 * @param  Array $booking_columns List of columns.
 * @return Array                  [description]
 */
function wp_travel_booking_columns( $booking_columns ) {

	$new_columns['cb']             = '<input type="checkbox" />';
	$new_columns['title']          = _x( 'Title', 'column name', 'wp-travel' );
	$new_columns['contact_name']   = __( 'Contact Name', 'wp-travel' );
	$new_columns['booking_status'] = __( 'Booking Status', 'wp-travel' );
	$new_columns['date']           = __( 'Booking Date', 'wp-travel' );
	return $new_columns;
}

/*
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_itinerary-booking_posts_custom_column', 'wp_travel_booking_manage_columns', 10, 2 );

/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int    $id          Post ID.
 */
function wp_travel_booking_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'contact_name':
			$first_name = get_post_meta( $id, 'wp_travel_fname_traveller', true );
			if ( ! $first_name ) {
				// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
				$first_name = get_post_meta( $id, 'wp_travel_fname', true );
			}
			$middle_name = get_post_meta( $id, 'wp_travel_mname_traveller', true );
			if ( ! $middle_name ) {
				$middle_name = get_post_meta( $id, 'wp_travel_mname', true );
			}
			$last_name = get_post_meta( $id, 'wp_travel_lname_traveller', true );
			if ( ! $last_name ) {
				$last_name = get_post_meta( $id, 'wp_travel_mname', true );
			}

			if ( is_array( $first_name ) ) { // Multiple Travelers.

				reset( $first_name );
				$first_key = key( $first_name );

				$name = '';
				if ( isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ) {
					$name .= $first_name[ $first_key ][0];
				}
				if ( isset( $middle_name[ $first_key ] ) && isset( $middle_name[ $first_key ][0] ) ) {
					$name .= ' ' . $middle_name[ $first_key ][0];
				}
				if ( isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ) {
					$name .= ' ' . $last_name[ $first_key ][0];
				}
			} else {
				$name  = $first_name;
				$name .= ' ' . $middle_name;
				$name .= ' ' . $last_name;
			}
			echo esc_attr( $name );
			break;
		case 'booking_status':
			$status    = wp_travel_get_booking_status();
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

/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
function wp_travel_booking_sort( $columns ) {

	$custom = array(
		'contact_name'   => 'contact_name',
		'booking_status' => 'booking_status',
	);
	return wp_parse_args( $custom, $columns );
	/*
	 or this way
	$columns['concertdate'] = 'concertdate';
	$columns['city'] = 'city';
	return $columns;
	*/
}
add_filter( 'manage_edit-itinerary-booking_sortable_columns', 'wp_travel_booking_sort' );

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @return Array       Order By array.
 */
function wp_travel_booking_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'contact_name' == $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_fname',
				'orderby'  => 'meta_value',
			)
		);
	}
	return $vars;
}
add_filter( 'request', 'wp_travel_booking_column_orderby' );

/** Front end Booking and send Email after clicking Book Now. */
function wp_travel_book_now() {
	if (
		! isset( $_POST['wp_travel_book_now'] )
		|| ! isset( $_POST['wp_travel_security'] )
		|| ! wp_verify_nonce( $_POST['wp_travel_security'], 'wp_travel_security_action' )
		) {
		return;
	}

	global $wt_cart;

	if ( isset( $wt_cart ) ) {
		$discounts = $wt_cart->get_discounts();
		if ( is_array( $discounts ) && ! empty( $discounts ) ) :

			WP_Travel()->coupon->update_usage_count( $discounts['coupon_id'] );

		endif;
	}

	$date_format            = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'Y m d';
	$current_date           = date( $date_format );
	$trip_id                = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : '';
	$trip_price             = wp_travel_get_trip_price( $trip_id );
	$enable_checkout        = apply_filters( 'wp_travel_enable_checkout', true );
	$pax                    = isset( $_POST['wp_travel_pax'] ) ? $_POST['wp_travel_pax'] : 1;
	$booking_arrival_date   = isset( $_POST['wp_travel_arrival_date'] ) ? wp_travel_format_date( $_POST['wp_travel_arrival_date'] ) : '';
	$booking_departure_date = isset( $_POST['wp_travel_departure_date'] ) ? wp_travel_format_date( $_POST['wp_travel_departure_date'] ) : '';

	$items = $wt_cart->getItems();
	// if ( $enable_checkout && 0 !== $trip_price ) :
	if ( $enable_checkout ) :

		if ( ! count( $items ) ) {
			return;
		}

		$trip_ids               = array();
		$pax_array              = array();
		$price_keys             = array();
		$booking_arrival_date   = array();
		$booking_departure_date = array();
		foreach ( $items as $key => $item ) {

			$trip_ids[]               = $item['trip_id'];
			$pax_array[]              = $item['pax'];
			$price_keys[]             = $item['price_key'];
			$booking_arrival_date[]   = $item['arrival_date'];
			$booking_departure_date[] = $item['departure_date'];

		}
		$price_key                 = false;
		$allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );

		if ( ! $allow_multiple_cart_items || ( 1 === count( $items ) ) ) {

			// $trip_id                = $trip_ids[0];
			$pax                    = $pax_array[0];
			$price_key              = isset( $price_keys[0] ) ? $price_keys[0] : '';
			$booking_arrival_date   = $booking_arrival_date[0];
			$booking_departure_date = $booking_departure_date[0];

		}
		// Quick fixes trip id.
		$trip_id = isset( $trip_ids[0] ) ? $trip_ids[0] : $trip_id;
	endif;

	if ( empty( $trip_id ) ) {
		return;
	}

	// $trip_code         = wp_travel_get_trip_code( $trip_id );
	$thankyou_page_url = wp_travel_thankyou_page_url( $trip_id );

	$title = 'Booking - ' . $current_date;

	$post_array = array(
		'post_title'   => $title,
		'post_content' => '',
		'post_status'  => 'publish',
		'post_slug'    => uniqid(),
		'post_type'    => 'itinerary-booking',
	);
	$booking_id = wp_insert_post( $post_array );
	update_post_meta( $booking_id, 'order_data', $_POST );

	// Update Booking Title.
	$update_data_array = array(
		'ID'         => $booking_id,
		'post_title' => 'Booking - # ' . $booking_id,
	);

	$booking_id = wp_update_post( $update_data_array );

	// @since 1.8.3
	$order_items  = update_post_meta( $booking_id, 'order_items_data', $items );
	$totals       = $wt_cart->get_total();
	$order_totals = update_post_meta( $booking_id, 'order_totals', $totals );

	$trip_id           = sanitize_text_field( $trip_id );
	$booking_count     = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
	$booking_count     = ( isset( $booking_count ) && '' != $booking_count ) ? $booking_count : 0;
	$new_booking_count = $booking_count + 1;
	update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

	/**
	 * Update Arrival and Departure dates metas.
	 */
	update_post_meta( $booking_id, 'wp_travel_arrival_date', $booking_arrival_date );
	update_post_meta( $booking_id, 'wp_travel_departure_date', $booking_departure_date );
	update_post_meta( $booking_id, 'wp_travel_post_id', $trip_id ); // quick fix [booking not listing in user dashboard].

	$post_ignore = array( '_wp_http_referer', 'wp_travel_security', 'wp_travel_book_now', 'wp_travel_payment_amount' );
	foreach ( $_POST as $meta_name => $meta_val ) {
		if ( in_array( $meta_name, $post_ignore ) ) {
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

	// Why this code?
	if ( array_key_exists( 'wp_travel_date', $_POST ) ) {

		$pax_count_based_by_date = get_post_meta( $trip_id, 'total_pax_booked', true );

		if ( ! array_key_exists( $_POST['wp_travel_date'], $pax_count_based_by_date ) ) {
			$pax_count_based_by_date[ $_POST['wp_travel_date'] ] = 'default';
		}

		$pax_count_based_by_date[ $_POST['wp_travel_date'] ] += $_POST['wp_travel_pax'];

		update_post_meta( $trip_id, 'total_pax_booked', $pax_count_based_by_date );

		$booking_ids = get_post_meta( $trip_id, 'order_ids', true );

		if ( ! $booking_ids ) {
			$booking_ids = array();
		}

		update_post_meta( $trip_id, 'order_ids', $booking_ids );
	}

	if ( is_user_logged_in() ) {
		$user                  = wp_get_current_user();
			$saved_booking_ids = get_user_meta( $user->ID, 'wp_travel_user_bookings', true );
		if ( ! $saved_booking_ids ) {
			$saved_booking_ids = array();
		}
		array_push( $saved_booking_ids, $booking_id );
		update_user_meta( $user->ID, 'wp_travel_user_bookings', $saved_booking_ids );
	}

	$settings = wp_travel_get_settings();
	$first_key = '';
	if ( ! $allow_multiple_cart_items || ( 1 === count( $items ) ) ) {
		/**
		 * Add Support for invertory addon options.
		 */
		do_action( 'wp_travel_update_trip_inventory_values', $trip_id, $pax, $price_key, $booking_arrival_date );

		$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';

		if ( isset( $_POST['wp_travel_fname'] ) || isset( $_POST['wp_travel_email'] ) ) { // Booking using old booking form
			$first_name = $_POST['wp_travel_fname'];
			$last_name  = $_POST['wp_travel_lname'];
			$country    = $_POST['wp_travel_country'];
			$phone      = $_POST['wp_travel_phone'];
			$email      = $_POST['wp_travel_email'];
		} else {
			$first_name = $_POST['wp_travel_fname_traveller'];
			$last_name  = $_POST['wp_travel_lname_traveller'];
			$country    = $_POST['wp_travel_country_traveller'];
			$phone      = $_POST['wp_travel_phone_traveller'];
			$email      = $_POST['wp_travel_email_traveller'];

			reset( $first_name );
			$first_key = key( $first_name );

			$first_name = isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ? $first_name[ $first_key ][0] : '';
			$last_name  = isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ? $last_name[ $first_key ][0] : '';
			$country    = isset( $country[ $first_key ] ) && isset( $country[ $first_key ][0] ) ? $country[ $first_key ][0] : '';
			$phone      = isset( $phone[ $first_key ] ) && isset( $phone[ $first_key ][0] ) ? $phone[ $first_key ][0] : '';
			$email      = isset( $email[ $first_key ] ) && isset( $email[ $first_key ][0] ) ? $email[ $first_key ][0] : '';
		}

		// Prepare variables to assign in email.
		$client_email = $email;

		$site_admin_email = get_option( 'admin_email' );

		$admin_email = apply_filters( 'wp_travel_booking_admin_emails', $site_admin_email );

		// Email Variables.
		if ( is_multisite() ) {
			$sitename = get_network()->site_name;
		} else {
			/*
			* The blogname option is escaped with esc_html on the way into the database
			* in sanitize_option we want to reverse this for the plain text arena of emails.
			*/
			$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}
		$itinerary_id    = sanitize_text_field( $trip_id );
		$itinerary_title = get_the_title( $itinerary_id );

		$booking_no_of_pax      = $pax;
		$booking_scheduled_date = esc_html__( 'N/A', 'wp-travel' );
		$date_format            = get_option( 'date_format' );
		$booking_arrival_date   = ( '' !== $booking_arrival_date ) ? wp_travel_format_date( $booking_arrival_date, true, 'Y-m-d' ) : '';
		$booking_departure_date = ( '' !== $booking_departure_date ) ? wp_travel_format_date( $booking_departure_date, true, 'Y-m-d' ) : '';

		$customer_name    = $first_name . ' ' . $last_name;
		$customer_country = $country;
		$customer_address = isset( $_POST['wp_travel_address'] ) ? $_POST['wp_travel_address'] : '';
		$customer_phone   = $phone;
		$customer_email   = $email;
		$customer_note    = isset( $_POST['wp_travel_note'] ) ? $_POST['wp_travel_note'] : '';

		$bank_deposit_table = '';
		if ( isset( $_POST['wp_travel_payment_gateway'] ) && 'bank_deposit' === $_POST['wp_travel_payment_gateway'] ) {
			$bank_deposit_table = wp_travel_get_bank_deposit_account_table( false );
		}

		$email_tags = array(
			'{sitename}'               => $sitename,
			'{itinerary_link}'         => get_permalink( $itinerary_id ),
			'{itinerary_title}'        => wp_travel_get_trip_pricing_name( $trip_id, $price_key ),
			'{booking_id}'             => $booking_id,
			'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
			'{booking_no_of_pax}'      => $booking_no_of_pax,
			'{booking_scheduled_date}' => $booking_scheduled_date,
			'{booking_arrival_date}'   => $booking_arrival_date,
			'{booking_departure_date}' => $booking_departure_date,

			'{customer_name}'          => $customer_name,
			'{customer_country}'       => $customer_country,
			'{customer_address}'       => $customer_address,
			'{customer_phone}'         => $customer_phone,
			'{customer_email}'         => $customer_email,
			'{customer_note}'          => $customer_note,
			'{bank_deposit_table}'     => $bank_deposit_table,
		);
		$email_tags = apply_filters( 'wp_travel_admin_email_tags', $email_tags, $booking_id );
		$email_tags = apply_filters( 'wp_travel_admin_booking_email_tags', $email_tags, $booking_id );

		$email = new WP_Travel_Emails();

		$admin_template = $email->wp_travel_get_email_template( 'bookings', 'admin' );

		$admin_message_data  = $admin_template['mail_header'];
		$admin_message_data .= $admin_template['mail_content'];
		$admin_message_data .= $admin_template['mail_footer'];

		// Admin message.
		$admin_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message_data );
		// Admin Subject.
		$admin_subject = $admin_template['subject'];

		// Client Template.
		$client_template = $email->wp_travel_get_email_template( 'bookings', 'client' );

		$client_message_data  = $client_template['mail_header'];
		$client_message_data .= $client_template['mail_content'];
		$client_message_data .= $client_template['mail_footer'];

		// Client message.
		$client_message = str_replace( array_keys( $email_tags ), $email_tags, $client_message_data );

		// Client Subject.
		$client_subject = $client_template['subject'];

		$reply_to_email = isset( $settings['wp_travel_from_email'] ) ? $settings['wp_travel_from_email'] : $site_admin_email;

		// Send mail to admin if booking email is set to yes.
		if ( 'yes' == $send_booking_email_to_admin ) {

			// To send HTML mail, the Content-type header must be set.
			$headers = $email->email_headers( $reply_to_email, $client_email );

			if ( ! wp_mail( $admin_email, $admin_subject, $admin_message, $headers ) ) {
				WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Your Item has been added but the email could not be sent.Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
			}
		}

		// Send email to client.
		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $reply_to_email, $reply_to_email );

		if ( ! wp_mail( $client_email, $client_subject, $client_message, $headers ) ) {
			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Your Item has been added but the email could not be sent.Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
		}
	} else {

		// Update single trip vals. // Need Enhancement. lots of loop with this $items in this functions.
		foreach ( $items as $item_key => $trip ) {

			$trip_id   = $trip['trip_id'];
			$pax       = $trip['pax'];
			$price_key = isset( $trip['price_key'] ) && ! empty( $trip['price_key'] ) ? $trip['price_key'] : false;
			$arrival_date = isset( $trip['arrival_date'] ) && ! empty( $trip['arrival_date'] ) ? $trip['arrival_date'] : '';

			$booking_count     = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
			$booking_count     = ( isset( $booking_count ) && '' != $booking_count ) ? $booking_count : 0;
			$new_booking_count = $booking_count + 1;
			update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

			// Why This?
			if ( array_key_exists( 'wp_travel_date', $_POST ) ) {

				$pax_count_based_by_date = get_post_meta( $trip_id, 'total_pax_booked', true );
				if ( ! array_key_exists( $_POST['wp_travel_date'], $pax_count_based_by_date ) ) {
					$pax_count_based_by_date[ $_POST['wp_travel_date'] ] = 'default';
				}

				$pax_count_based_by_date[ $_POST['wp_travel_date'] ] += $pax;

				update_post_meta( $trip_id, 'total_pax_booked', $pax_count_based_by_date );

				$order_ids = get_post_meta( $trip_id, 'order_ids', true );

				if ( ! $order_ids ) {
					$order_ids = array();
				}

				update_post_meta( $trip_id, 'order_ids', $order_ids );
			}

			if ( is_user_logged_in() ) {

				$user = wp_get_current_user();

				if ( in_array( 'wp-travel-customer', (array) $user->roles ) ) {

					$saved_booking_ids = get_user_meta( $user->ID, 'wp_travel_user_bookings', true );

					if ( ! $saved_booking_ids ) {
						$saved_booking_ids = array();
					}

					array_push( $saved_booking_ids, $order_id );

					update_user_meta( $user->ID, 'wp_travel_user_bookings', $saved_booking_ids );

				}
			}

			/**
			 * Add Support for invertory addon options.
			 */
			// wp_travel_utilities_update_inventory_pax_count( $trip_id );
			// do_action( 'wp_travel_update_trip_multiple_inventory_values', $trip_id, $pax, $price_key );
			do_action( 'wp_travel_update_trip_inventory_values', $trip_id, $pax, $price_key, $arrival_date );

			if ( class_exists( 'WP_Travel_Multiple_Cart_Booking' ) ) {
				$multiple_order = new WP_Travel_Multiple_Cart_Booking();
				// Finally, send the booking e-mails.
				$multiple_order->send_emails( $booking_id );
			}
		}
	}

	/**
	 * Hook used to add payment and its info.
	 *
	 * @since 1.0.5 // For Payment.
	 */
	do_action( 'wp_travel_after_frontend_booking_save', $booking_id, $first_key );

	// Clear Transient To update booking Count.
	delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );

	// Clear Cart After process is complete.
	$wt_cart->clear();

	$thankyou_page_url = add_query_arg( 'booked', true, $thankyou_page_url );
	$thankyou_page_url = add_query_arg( 'order_id', $booking_id, $thankyou_page_url );
	$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url, $booking_id );
	header( 'Location: ' . $thankyou_page_url );
	exit;
}

function wp_travel_sanitize_array( $array ) {
	foreach ( $array as $k => $value ) {
		if ( is_array( $value ) ) {
			wp_travel_sanitize_array( $value );
		} else {
			return sanitize_text_field( $value );
		}
	}
	return false;
}

/**
 * Get All booking stat data.
 *
 * @return void
 */
function get_booking_chart() {
	$wp_travel_itinerary_list = wp_travel_get_itineraries_array();
	$wp_travel_post_id        = ( isset( $_REQUEST['booking_itinerary'] ) && '' !== $_REQUEST['booking_itinerary'] ) ? $_REQUEST['booking_itinerary'] : 0;

	$country_list     = wp_travel_get_countries();
	$selected_country = ( isset( $_REQUEST['booking_country'] ) && '' !== $_REQUEST['booking_country'] ) ? $_REQUEST['booking_country'] : '';

	$from_date = ( isset( $_REQUEST['booking_stat_from'] ) && '' !== $_REQUEST['booking_stat_from'] ) ? rawurldecode( $_REQUEST['booking_stat_from'] ) : '';
	$to_date   = ( isset( $_REQUEST['booking_stat_to'] ) && '' !== $_REQUEST['booking_stat_to'] ) ? rawurldecode( $_REQUEST['booking_stat_to'] ) : '';

	$compare_stat = ( isset( $_REQUEST['compare_stat'] ) && '' !== $_REQUEST['compare_stat'] ) ? rawurldecode( $_REQUEST['compare_stat'] ) : '';

	$compare_from_date         = ( isset( $_REQUEST['compare_stat_from'] ) && '' !== $_REQUEST['compare_stat_from'] ) ? rawurldecode( $_REQUEST['compare_stat_from'] ) : '';
	$compare_to_date           = ( isset( $_REQUEST['compare_stat_to'] ) && '' !== $_REQUEST['compare_stat_to'] ) ? rawurldecode( $_REQUEST['compare_stat_to'] ) : '';
	$compare_selected_country  = ( isset( $_REQUEST['compare_country'] ) && '' !== $_REQUEST['compare_country'] ) ? $_REQUEST['compare_country'] : '';
	$compare_itinerary_post_id = ( isset( $_REQUEST['compare_itinerary'] ) && '' !== $_REQUEST['compare_itinerary'] ) ? $_REQUEST['compare_itinerary'] : 0;
	$chart_type                = isset( $_REQUEST['chart_type'] ) ? $_REQUEST['chart_type'] : '';
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Statistics', 'wp-travel' ); ?></h2>
		<div class="stat-toolbar">
				<form name="stat_toolbar" class="stat-toolbar-form" action="" method="get" >
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
					do_action( 'wp_travel_before_stat_toolbar_fields' );
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
									<option value="<?php echo esc_html( $key ); ?>" <?php selected( $key, $selected_country ); ?>>
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
								<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
									<option value="<?php echo esc_html( $itinerary_id ); ?>" <?php selected( $wp_travel_post_id, $itinerary_id ); ?>>
										<?php echo esc_html( $itinerary_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</p>

						<?php
						// @since 1.0.6 // Hook since
						do_action( 'wp_travel_after_stat_toolbar_fields' );
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
								<option value="<?php echo esc_html( $key ); ?>" <?php selected( $key, $compare_selected_country ); ?>>
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
							<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
								<option value="<?php echo esc_html( $itinerary_id ); ?>" <?php selected( $compare_itinerary_post_id, $itinerary_id ); ?>>
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
		<div class="right-block <?php echo esc_attr( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ? 'has-compare' : '' ); ?>">

			<div class="wp-travel-stat-info">
				<?php if ( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ) : ?>
				<div class="right-block-single for-compare">
					<h3><?php esc_html_e( 'Compare 1', 'wp-travel' ); ?></h3>
				</div>
				<?php endif; ?>

				<div class="right-block-single">
					<strong><big><?php echo esc_attr( wp_travel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales">0</big></strong><br />
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
			<?php if ( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ) : ?>

				<div class="wp-travel-stat-info">
					<div class="right-block-single for-compare">
						<h3><?php esc_html_e( 'Compare 2', 'wp-travel' ); ?></h3>
					</div>
					<div class="right-block-single">
						<strong><big><?php echo esc_attr( wp_travel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales-compare">0</big></strong><br />
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


/**
 * WP Travel Post Duplicator.
 *
 * @param   Array  $actions    Action.
 * @param   Object $post       Post Object.
 *
 * @since   1.7.6
 *
 * @return  Array $actions;
 */
function wp_travel_post_duplicator_action_row( $actions, $post ) {
	// Get the post type object
	$post_type = get_post_type_object( $post->post_type );
	if ( WP_TRAVEL_POST_TYPE === $post_type->name && function_exists( 'wp_travel_post_duplicator_action_row_link' ) ) {
		$actions['wp_travel_duplicate_post'] = wp_travel_post_duplicator_action_row_link( $post );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'wp_travel_post_duplicator_action_row', 10, 2 );


function wp_travel_post_duplicator_action_row_link( $post ) {

	$settings = wp_travel_get_settings();

	// Get the post type object
	$post_type = get_post_type_object( $post->post_type );

	if ( WP_TRAVEL_POST_TYPE !== $post_type->name ) {
		return;
	}

	// Set the button label
	$label = sprintf( __( 'Clone %s', 'wp-travel' ), $post_type->labels->singular_name );

	// Create a nonce & add an action
	$nonce = wp_create_nonce( 'wp_travel_clone_post_nonce' );

	// Return the link
	return '<a class="wp-travel-clone-post" data-security="' . $nonce . '" href="#" data-post_id="' . $post->ID . '">' . $label . '</a>';
}
