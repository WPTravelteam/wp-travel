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
		'id' => 'wp-travel-booking',
		'wrapper_class' => 'wp-travel-booking-form-wrapper',
		'submit_button' => array(
			'name' => 'wp_travel_book_now',
			'id' => 'wp-travel-book-now',
			'value' => __( 'Book Now', 'wp-travel' ),
		),
		'nonce' => array(
			'action' => 'wp_travel_security_action',
			'field' => 'wp_travel_security',
		),
	);

	$fields = wp_travel_booking_form_fields();

		// GDPR Support
		$settings = wp_travel_get_settings();

		$gdpr_msg = isset( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );

		$privacy_policy_url = false;

		if ( function_exists( 'get_privacy_policy_url' ) ) {

			$privacy_policy_url = get_privacy_policy_url();

		}

		if ( function_exists( 'get_the_privacy_policy_link' ) && ! empty( $gdpr_msg ) && $privacy_policy_url ) {

			// GDPR Compatibility for enquiry.
			$fields['wp_travel_booking_gdpr'] = array(
				'type' => 'checkbox',
				'label' => __('Privacy Policy', 'wp-travel'),
				'options' => array( 'gdpr_agree' => sprintf( '%1s %2s', $gdpr_msg, get_the_privacy_policy_link() ) ),
				'name' => 'wp_travel_booking_gdpr_msg',
				'id' => 'wp-travel-enquiry-gdpr-msg',
				'validations' => array(
					'required' => true,
				),
				'option_attributes' => array(
					'required' => true,
				),
				'priority' => 100,
				'wrapper_class' => 'full-width',
			);

		}
	
	$form = new WP_Travel_FW_Form();
	$fields['post_id'] = array(
		'type' => 'hidden',
		'name' => 'wp_travel_post_id',
		'id' => 'wp-travel-post-id',
		'default' => $trip_id,
	);
	$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
	$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );

	if ( 'yes' === $fixed_departure ) {
		$fields['arrival_date']['class'] = '';
		$fields['arrival_date']['default'] = date( 'm/d/Y', strtotime( $trip_start_date ) );
		$fields['arrival_date']['type'] = 'hidden';
		$fields['departure_date']['class'] = '';
		$fields['departure_date']['default'] = date( 'm/d/Y', strtotime( $trip_end_date ) );
		$fields['departure_date']['type'] = 'hidden';
		unset( $fields['trip_duration'] );
	}

	$trip_duration = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	
	$fields['trip_duration']['default'] = $trip_duration;
	$fields['trip_duration']['type'] = 'hidden';

	$group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );

	if ( isset( $group_size ) && '' != $group_size ) {

		$fields['pax']['validations']['max'] = $group_size;

	}

	$trip_price = wp_travel_get_actual_trip_price( $trip_id );

	if ( '' == $trip_price || '0' == $trip_price ) {

		unset( $fields['is_partial_payment'], $fields['payment_gateway'] , $fields['booking_option'], $fields['trip_price'], $fields['payment_mode'], $fields['payment_amount'], $fields['trip_price_info'], $fields['payment_amount_info'] );

	}

	$form->init( $form_options )->fields( $fields )->template();
	// return apply_filters( 'wp_travel_booking_form_contents', $content );
}

add_action( 'add_meta_boxes', 'wp_travel_register_booking_metaboxes', 10, 2 );

/**
 * This will add metabox in booking post type.
 */
function wp_travel_register_booking_metaboxes($a) {
	global $post;
	global $wp_travel_itinerary;

	$wp_travel_post_id = get_post_meta( $post->ID, 'wp_travel_post_id', true );
	// $trip_code = $wp_travel_itinerary->get_trip_code( $wp_travel_post_id );
	add_meta_box( 'wp-travel-booking-info', __( 'Booking Detail <span class="wp-travel-view-bookings"><a href="edit.php?post_type=itinerary-booking&wp_travel_post_id=' . $wp_travel_post_id . '">View All ' . get_the_title( $wp_travel_post_id ) . ' Bookings</a></span>', 'wp-travel' ), 'wp_travel_booking_info', 'itinerary-booking', 'normal', 'default' );

	add_action('admin_head', 'wp_travel_admin_head_meta' );
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

	<?php endif;
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
	$wp_travel_post_id = get_post_meta( $post->ID, 'wp_travel_post_id', true );
	$ordered_data = get_post_meta( $post->ID, 'order_data', true );

	$multiple_trips_booking_data = get_post_meta( $post->ID, 'order_items_data', true );

	// Support new layout for multiple trips booking.
	if ( ! empty( $multiple_trips_booking_data ) ) {

		do_action( 'wp_travel_bookings_backend_multiple_trip_details', $post );
		exit;

	}

	$wp_travel_itinerary_list = wp_travel_get_itineraries_array(); ?>

	<div class="wp-travel-booking-form-wrapper">
		<form action="" method="post">
			<?php do_action( 'wp_travel_booking_before_form_field' ); ?>
			<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>
			<div class="wp-travel-form-field full-width">
				<label for="wp-travel-post-id"><?php echo esc_html( ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ) ); ?></label>
				<select id="wp-travel-post-id" name="wp_travel_post_id" >
				<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
					<option value="<?php echo esc_attr( $itinerary_id ); ?>" <?php selected( $wp_travel_post_id, $itinerary_id ) ?>>
						<?php echo esc_html( $itinerary_name ); ?>
					</option>
				<?php endforeach; ?>
				</select>
			</div>

			<?php
			$fields = wp_travel_booking_form_fields();
				
				$trip_price = wp_travel_get_actual_trip_price( $post->ID );

				if ( '' == $trip_price || '0' == $trip_price ) {

					unset( $fields['is_partial_payment'], $fields['payment_gateway'] , $fields['booking_option'], $fields['trip_price'], $fields['payment_mode'], $fields['payment_amount'], $fields['trip_price_info'], $fields['payment_amount_info'] );

				}

				$payment_id = get_post_meta( $post->ID , 'wp_travel_payment_id' , true );
				$booking_option = get_post_meta( $payment_id , 'wp_travel_booking_option' , true );

				if ( 'booking_only' == $booking_option ) {

					unset( $fields['is_partial_payment'], $fields['payment_gateway'], $fields['payment_mode'], $fields['payment_amount'], $fields['payment_amount_info'] );
				}

			$priority = array();
			foreach ( $fields as $key => $row ) {
				$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
			}
			array_multisort( $priority, SORT_ASC, $fields );
			foreach ( $fields as $key => $field ) : ?>
				<?php
				$input_val = get_post_meta( $post->ID, $field['name'], true );
				/**
				 * Hook Since  
				 * @since 1.0.6.
				 * */
				$input_val = apply_filters( 'wp_travel_booking_field_value', $input_val, $post->ID, $key, $field['name'] );
				$field_type = $field['type'];
				$before_field = '';
				if ( isset( $field['before_field'] ) ) {
					$before_field_class = isset( $field['before_field_class'] ) ? $field['before_field_class'] : '';
					$before_field = sprintf( '<span class="wp-travel-field-before %s">%s</span>', $before_field_class, $field['before_field'] );
				}
				$wrapper_class = '';
				if ( isset( $field['wrapper_class'] ) ) {
					$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';

				}
				$attributes = '';				
				if ( isset( $field['attributes'] ) ) {					
					foreach ( $field['attributes'] as $attribute => $attribute_val ) {
						$attributes .= sprintf( '%s=%s ', $attribute, $attribute_val );
					}
				}
				switch ( $field_type ) {
					case 'select': ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
						<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
							<?php $options = $field['options']; ?>
							<?php if ( count( $options ) > 0 ) : ?>
							<select <?php echo esc_attr( $attributes ) ?> id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>">
								<?php foreach ( $options as $short_name => $name ) : ?>
									<option <?php selected( $input_val, $short_name ); ?> value="<?php echo esc_attr( $short_name ) ?>"><?php echo esc_html( $name ) ?></option>
								<?php endforeach; ?>
							</select>
							<?php endif; ?>
						</div>
					<?php break; ?>
					<?php case 'radio' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
							<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>						
							<?php
							if ( ! empty( $field['options'] ) ) {
								foreach ( $field['options'] as $key => $value ) { ?>
									<label class="radio-checkbox-label"><input type="<?php echo esc_attr( $field['type'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $input_val, $key ); ?> ><?php echo esc_html( $value ) ?></label>
								<?php
								}
							}
							?>
						</div>
					
					<?php break; ?>
					<?php case 'checkbox' : ?>
					<?php break; ?>
					<?php case 'textarea' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
						<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
							<textarea <?php echo esc_attr( $attributes ) ?> name="<?php echo esc_attr( $field['name'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" placeholder="<?php esc_html_e( 'Some text...', 'wp-travel' ); ?>" rows="6" cols="150"><?php echo esc_html( $input_val ); ?></textarea>
						</div>
					<?php break; ?>
					<?php case 'date' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
							<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
							<input <?php echo esc_attr( $attributes ) ?> class="wp-travel-date" type="text" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $input_val ); ?>" >
						</div>
					<?php break; ?>
					<?php case 'hidden' : ?>
						
						<input <?php echo esc_attr( $attributes ) ?> type="<?php echo esc_attr( $field['type'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $input_val ); ?>" >
						
					<?php break; ?>
					<?php default : ?>
					<?php if ( 'wp_travel_payment_amount' == $field['name'] ) : ?>

						<?php $payment_id = get_post_meta( $post->ID, 'wp_travel_payment_id', true ); 

						if ( $payment_id ) :

							$payment_amount = get_post_meta( $payment_id, 'wp_travel_payment_amount' , true )
						
						?>

							<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
								<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
								<?php echo $before_field; ?>
								<input <?php echo esc_attr( $attributes ) ?> type="<?php echo esc_attr( $field['type'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $payment_amount ); ?>" >
							</div>
						
						<?php 
						endif;
					else : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
							<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
							<?php echo $before_field; ?>
							<input <?php echo esc_attr( $attributes ) ?> type="<?php echo esc_attr( $field['type'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $input_val ); ?>" >
						</div>
					<?php endif; ?>
					<?php break;
				}
				?>
				
			<?php endforeach; ?>
			<?php 
				wp_enqueue_script('jquery-datepicker-lib');
				wp_enqueue_script('jquery-datepicker-lib-eng');
			?>
			<script>
				jQuery(document).ready( function($){
					$(".wp-travel-date").datepicker({
							language: "en",		
							minDate: new Date()
						});
				} )
			</script>
			<?php do_action( 'wp_travel_booking_after_form_field' ); ?>
		</form>
	</div>

<?php
}


/**
 * Save Post meta data.
 *
 * @param  int $trip_id ID of current post.
 *
 * @return Mixed
 */
function wp_travel_save_booking_data( $trip_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $trip_id ) ) {
		return;
	}
	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $trip_id ) ) {
		return;
	}

	$post_type = get_post_type( $trip_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}
	$order_data = array();
	$wp_travel_post_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
	update_post_meta( $trip_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
	$order_data['wp_travel_post_id'] = $wp_travel_post_id;

	// Updating booking status.
	$booking_status = isset( $_POST['wp_travel_booking_status'] ) ? $_POST['wp_travel_booking_status'] : 'pending';
	update_post_meta( $trip_id, 'wp_travel_booking_status', sanitize_text_field( $booking_status ) );

	$fields = wp_travel_booking_form_fields();
	$priority = array();
	foreach ( $fields as $key => $row ) {
		$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
	}
	array_multisort( $priority, SORT_ASC, $fields );
	foreach ( $fields as $key => $field ) :
		$meta_val = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';
		$trip_id_to_update = apply_filters( 'wp_travel_booking_post_id_to_update', $trip_id, $key, $field['name'] );
		update_post_meta( $trip_id_to_update, $field['name'], sanitize_text_field( $meta_val ) );
		$order_data[ $field['name'] ] = $meta_val;
	endforeach;

	$order_data = array_map( 'sanitize_text_field', wp_unslash( $order_data ) );
	update_post_meta( $trip_id, 'order_data', $order_data );
	do_action( 'wp_travel_after_booking_data_save', $trip_id );
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

	$new_columns['cb'] 			 = '<input type="checkbox" />';
	$new_columns['title'] 		 = _x( 'Title', 'column name', 'wp-travel' );
	$new_columns['contact_name'] = __( 'Contact Name', 'wp-travel' );
	$new_columns['booking_status'] = __( 'Booking Status', 'wp-travel' );
	$new_columns['date'] 		 = __( 'Booking Date', 'wp-travel' );
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
 * @param  int 	  $id          Post ID.
 */
function wp_travel_booking_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'contact_name':
			$name = get_post_meta( $id , 'wp_travel_fname' , true );
			$name .= ' ' . get_post_meta( $id , 'wp_travel_mname' , true );
			$name .= ' ' . get_post_meta( $id , 'wp_travel_lname' , true );
			echo esc_attr( $name );
			break;
		case 'booking_status':
			$status = wp_travel_get_booking_status();
			$label_key = get_post_meta( $id , 'wp_travel_booking_status' , true );
			if ( '' === $label_key ) {
				$label_key = 'pending';
				update_post_meta( $id, 'wp_travel_booking_status' , $label_key );
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
add_filter( 'manage_edit-itinerary-booking_sortable_columns', 'wp_travel_booking_sort' );
function wp_travel_booking_sort( $columns ) {

	$custom = array(
		'contact_name' 	 => 'contact_name',
		'booking_status' => 'booking_status',
	);
	return wp_parse_args( $custom, $columns );
	/* or this way
		$columns['concertdate'] = 'concertdate';
		$columns['city'] = 'city';
		return $columns;
	*/
}

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'wp_travel_booking_column_orderby' );

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @return Array       Order By array.
 */
function wp_travel_booking_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'contact_name' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'wp_travel_fname',
			'orderby' => 'meta_value',
		) );
	}
	return $vars;
}


// add_action( 'restrict_manage_posts', 'wp_travel_restrict_manage_posts' );

/**
 * Restrict Manage Post.
 * @param  String $post_type Post type name.
 */
function wp_travel_restrict_manage_posts( $post_type ) {

	if ( 'itinerary-booking' === $post_type ) {
		echo <<<EOS
			<script type="text/javascript">
			jQuery(document).ready(function($) {
			    $("input[name='keep_private']").parents("div.inline-edit-group:first").hide();
			});
			</script>
EOS;
	}
}

/** Send Email after clicking Book Now. */
function wp_travel_book_now() {

	if ( ! isset( $_POST[ 'wp_travel_book_now' ] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['wp_travel_security'],  'wp_travel_security_action' ) ) {
		return;
	}
	
	$date_format = get_option('date_format') ? get_option('date_format') : 'Y m d';
	$current_date = date( $date_format  );

	$trip_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : '';

	$trip_price      = wp_travel_get_trip_price( $trip_id );

	$enable_checkout = apply_filters( 'wp_travel_enable_checkout', true );

	$pax = isset( $_POST['wp_travel_pax'] ) ? $_POST['wp_travel_pax'] : 1;

	$booking_arrival_date = isset( $_POST['wp_travel_arrival_date'] ) ? wp_travel_format_date( $_POST['wp_travel_arrival_date'] ) : '';

	$booking_departure_date = isset( $_POST['wp_travel_departure_date'] ) ? wp_travel_format_date( $_POST['wp_travel_departure_date'] ) : '';

	if ( $enable_checkout && wp_travel_is_payment_enabled() && 0 !== $trip_price ) :
	
		global $wt_cart;

		$items = $wt_cart->getItems();
		if ( ! count( $items ) ) {
			return;
		}

		$trip_ids = array();
		$pax_array = array();
		$booking_arrival_date = array();
		$booking_departure_date = array();
		foreach( $items as $key => $item ) {

			$trip_ids[]  = $item['trip_id'];
			$pax_array[] = $item['pax'];
			$booking_arrival_date[] = $item['arrival_date'];
			$booking_departure_date[] = $item['departure_date'];

		}
		$trip_id = '';
		$pax = 0;
		$allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );
			
			if ( ! $allow_multiple_cart_items || ( 1 === count( $items ) ) ) {

				$trip_id = $trip_ids['0'];
				$pax = $pax_array['0'];
				$booking_arrival_date = $booking_arrival_date[0];
				$booking_departure_date = $booking_departure_date[0];

			}else{

				// Support for multiple cart items.
				do_action( 'wp_travel_multiple_trip_booking' );
				exit;

			}

	endif;
	
	if ( empty( $trip_id ) ) {
		return;
	}

	$trip_code = wp_travel_get_trip_code( $trip_id );
	$thankyou_page_url = get_permalink( $trip_id );
	$title = 'Booking - ' . $current_date;

	$post_array = array(
		'post_title'   => $title,
		'post_content' => '',
		'post_status'  => 'publish',
		'post_slug'    => uniqid(),
		'post_type'    => 'itinerary-booking',
	);
	$order_id = wp_insert_post( $post_array );
	update_post_meta( $order_id, 'order_data', $_POST );

	// Update Booking Title.
	$update_data_array = array(
		'ID'           => $order_id,
		'post_title'   => 'Booking - # ' . $order_id,
	);

	$order_id = wp_update_post( $update_data_array );

	$trip_id = sanitize_text_field( $trip_id );
	$booking_count = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
	$booking_count = ( isset( $booking_count ) && '' != $booking_count ) ? $booking_count : 0;
	$new_booking_count = $booking_count + 1;
	update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

	/**
	 * Update Arrival and Departure dates metas.
	 */
	update_post_meta( $order_id, 'wp_travel_arrival_date', $booking_arrival_date );
	update_post_meta( $order_id, 'wp_travel_departure_date', $booking_departure_date );

	$post_ignore = array( '_wp_http_referer', 'wp_travel_security', 'wp_travel_book_now', 'wp_travel_payment_amount' );
	foreach ( $_POST as $meta_name => $meta_val ) {
		if ( in_array( $meta_name , $post_ignore ) ) {
			continue;
		}
		update_post_meta( $order_id, $meta_name, sanitize_text_field( $meta_val ) );
	}

	if ( array_key_exists( 'wp_travel_date', $_POST ) ) {

		$pax_count_based_by_date = get_post_meta( $trip_id, 'total_pax_booked', true );

		if ( ! array_key_exists( $_POST['wp_travel_date'], $pax_count_based_by_date ) ) {
			$pax_count_based_by_date[ $_POST['wp_travel_date'] ] = 'default';
		}

		$pax_count_based_by_date[$_POST['wp_travel_date']] += $_POST['wp_travel_pax'];

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
	do_action( 'wp_travel_update_trip_inventory_values' );

	$settings = wp_travel_get_settings();

	$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';

	// Prepare variables to assign in email.
	$client_email = $_POST['wp_travel_email'];

	$site_admin_email = get_option( 'admin_email' );

	$admin_email = apply_filters( 'wp_travel_booking_admin_emails', $site_admin_email  );

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
	$booking_id 		  	= $order_id;
	$itinerary_id 			= sanitize_text_field( $trip_id );
	$itinerary_title 		= get_the_title( $itinerary_id );

	$booking_no_of_pax 		= $pax;
	$booking_scheduled_date = esc_html__( 'N/A', 'wp-travel' );
	$date_format            = get_option('date_format');
	$booking_arrival_date 	= $booking_arrival_date;
	$booking_departure_date = $booking_departure_date;

	$customer_name 		  	= $_POST['wp_travel_fname'] . ' ' . $_POST['wp_travel_lname'];
	$customer_country 		= $_POST['wp_travel_country'];
	$customer_address 		= $_POST['wp_travel_address'];
	$customer_phone 		= $_POST['wp_travel_phone'];
	$customer_email 		= $_POST['wp_travel_email'];
	$customer_note 			= $_POST['wp_travel_note'];

	$email_tags = array(
		'{sitename}'				=> $sitename,
		'{itinerary_link}'			=> get_permalink( $itinerary_id ),
		'{itinerary_title}'			=> $itinerary_title,
		'{booking_id}'				=> $booking_id,
		'{booking_edit_link}'		=> get_edit_post_link( $booking_id ),
		'{booking_no_of_pax}'		=> $booking_no_of_pax,
		'{booking_scheduled_date}'	=> $booking_scheduled_date,
		'{booking_arrival_date}'	=> $booking_arrival_date,
		'{booking_departure_date}'	=> $booking_departure_date,

		'{customer_name}'			=> $customer_name,
		'{customer_country}'		=> $customer_country,
		'{customer_address}'		=> $customer_address,
		'{customer_phone}'			=> $customer_phone,
		'{customer_email}'			=> $customer_email,
		'{customer_note}'			=> $customer_note,
	);
	apply_filters( 'wp_travel_admin_email_tags', $email_tags );
		
	$email = new WP_Travel_Emails();

	$admin_template = $email->wp_travel_get_email_template( 'bookings', 'admin' );

	$admin_message_data = $admin_template['mail_header'];
	$admin_message_data .= $admin_template['mail_content'];
	$admin_message_data .= $admin_template['mail_footer'];

	//Admin message.
	$admin_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message_data );
	//Admin Subject.
	$admin_subject = $admin_template['subject'];

	// Client Template.
	$client_template = $email->wp_travel_get_email_template( 'bookings', 'client' );

	$client_message_data = $client_template['mail_header'];
	$client_message_data .= $client_template['mail_content'];
	$client_message_data .= $client_template['mail_footer'];
	
	//Client message.
	$client_message = str_replace( array_keys( $email_tags ), $email_tags, $client_message_data );
	
	//Client Subject.
	$client_subject = $client_template['subject'];

	// Send mail to admin if booking email is set to yes.
	if ( 'yes' == $send_booking_email_to_admin ) {
		
		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $client_email, $client_email );

		if ( ! wp_mail( $admin_email, $admin_subject, $admin_message, $headers ) ) {
				
				if ( isset( $wt_cart ) ) {
					$discounts = $wt_cart->get_discounts();
					if ( is_array( $discounts ) && ! empty( $discounts ) ) :
			
						WP_Travel()->coupon->update_usage_count( $discounts['coupon_id'] );
			
					endif;
					// Clear Cart After process is complete.
					$wt_cart->clear();
				}

			$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url );
			$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
			header( 'Location: ' . $thankyou_page_url );
			exit;
		}
	}

	// Send email to client.
	// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $site_admin_email, $site_admin_email );

		if ( ! wp_mail( $client_email, $client_subject, $client_message, $headers ) ) {
				
				if ( isset( $wt_cart ) ) {
					$discounts = $wt_cart->get_discounts();
					if ( is_array( $discounts ) && ! empty( $discounts ) ) :
			
						WP_Travel()->coupon->update_usage_count( $discounts['coupon_id'] );
			
					endif;
					// Clear Cart After process is complete.
					$wt_cart->clear();
				}
			
			$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url );
			$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
			header( 'Location: ' . $thankyou_page_url );
			exit;
		}
	/**
	 * Hook used to add payment and its info.
	 *
	 * @since 1.0.5 // For Payment.
	 */
	do_action( 'wp_travel_after_frontend_booking_save', $order_id );

	if ( isset( $wt_cart ) ) {
		$discounts = $wt_cart->get_discounts();
		if ( is_array( $discounts ) && ! empty( $discounts ) ) :

			WP_Travel()->coupon->update_usage_count( $discounts['coupon_id'] );

		endif;
		// Clear Cart After process is complete.
		$wt_cart->clear();
	}

	// Clear Transient To update booking Count.
	delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );

	$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url );
	$thankyou_page_url = add_query_arg( 'booked', true, $thankyou_page_url );
	header( 'Location: ' . $thankyou_page_url );
	exit;
}

/**
 * Get All booking stat data.
 *
 * @return void
 */
function get_booking_chart() {
	$wp_travel_itinerary_list = wp_travel_get_itineraries_array();
	$wp_travel_post_id = ( isset( $_REQUEST['booking_itinerary'] ) && '' !== $_REQUEST['booking_itinerary'] ) ? $_REQUEST['booking_itinerary'] : 0;

	$country_list = wp_travel_get_countries();
	$selected_country = ( isset( $_REQUEST['booking_country'] ) && '' !== $_REQUEST['booking_country'] ) ? $_REQUEST['booking_country'] : '';

	$from_date = ( isset( $_REQUEST['booking_stat_from'] ) && '' !== $_REQUEST['booking_stat_from'] ) ? rawurldecode( $_REQUEST['booking_stat_from'] ) : '';
	$to_date   = ( isset( $_REQUEST['booking_stat_to'] ) && '' !== $_REQUEST['booking_stat_to'] ) ? rawurldecode( $_REQUEST['booking_stat_to'] ) : '';
	
	$compare_stat = ( isset( $_REQUEST['compare_stat'] ) && '' !== $_REQUEST['compare_stat'] ) ? rawurldecode( $_REQUEST['compare_stat'] ) : '';
	
	$compare_from_date = ( isset( $_REQUEST['compare_stat_from'] ) && '' !== $_REQUEST['compare_stat_from'] ) ? rawurldecode( $_REQUEST['compare_stat_from'] ) : '';
	$compare_to_date   = ( isset( $_REQUEST['compare_stat_to'] ) && '' !== $_REQUEST['compare_stat_to'] ) ? rawurldecode( $_REQUEST['compare_stat_to'] ) : '';
	$compare_selected_country = ( isset( $_REQUEST['compare_country'] ) && '' !== $_REQUEST['compare_country'] ) ? $_REQUEST['compare_country'] : '';
	$compare_itinerary_post_id = ( isset( $_REQUEST['compare_itinerary'] ) && '' !== $_REQUEST['compare_itinerary'] ) ? $_REQUEST['compare_itinerary'] : 0;
	$chart_type = isset( $_REQUEST['chart_type'] ) ? $_REQUEST['chart_type'] : '';
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Statistics', 'wp-travel' ); ?></h2>
		<div class="stat-toolbar">
				<form name="stat_toolbar" class="stat-toolbar-form" action="" method="get" >
					<input type="hidden" name="post_type" value="itineraries" >
					<input type="hidden" name="page" value="booking_chart">
					<p class="field-group full-width">
						<span class="field-label"><?php esc_html_e( 'Display Chart', 'wp-travel' ); ?>:</span>
						<select name="chart_type" >
							<option value="booking" <?php selected( 'booking', $chart_type ) ?> ><?php esc_html_e( 'Booking', 'wp-travel' ) ?></option>
							<option value="payment" <?php selected( 'payment', $chart_type ) ?> ><?php esc_html_e( 'Payment', 'wp-travel' ) ?></option>
						</select>
					</p>
					<?php
					// @since 1.0.6 // Hook since
					do_action( 'wp_travel_before_stat_toolbar_fields' ); ?>
					<div class="show-all compare">
						<p class="show-compare-stat">
						<span class="checkbox-default-design">
							<span class="field-label"><?php esc_html_e( 'Compare Stat', 'wp-travel' ); ?>:</span>
							<label data-on="ON" data-off="OFF">
								<input id="compare-stat" type="checkbox" name="compare_stat" value="yes" <?php checked( 'yes', $compare_stat ) ?>>						
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
							<input type="text" name="booking_stat_from" class="datepicker-from" class="form-control" value="<?php echo esc_attr( $from_date ) ?>" id="fromdate1" />
							<label class="input-group-addon btn" for="fromdate1">
							<span class="dashicons dashicons-calendar-alt"></span>
							</label>        
						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php esc_html_e( 'To', 'wp-travel' ); ?>:</span>
							<input type="text" name="booking_stat_to" class="datepicker-to" class="form-control" value="<?php echo esc_attr( $to_date ) ?>" id="fromdate2" />
							<label class="input-group-addon btn" for="fromdate2">
							<span class="dashicons dashicons-calendar-alt"></span>
							</label> 
						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php esc_html_e( 'Country', 'wp-travel' ); ?>:</span>

							<select class="selectpicker form-control" name="booking_country">
							
								<option value=""><?php esc_html_e( 'All Country', 'wp-travel' ) ?></option>
								
								<?php foreach ( $country_list as $key => $value ) : ?>
									<option value="<?php echo esc_html( $key ); ?>" <?php selected( $key, $selected_country ) ?>>
										<?php echo esc_html( $value ); ?>
									</option>
								<?php endforeach; ?>
							</select>

						</p>
						<p class="field-group field-group-stat">
							<span class="field-label"><?php echo esc_html( WP_TRAVEL_POST_TITLE ); ?>:</span>
							<select class="selectpicker form-control" name="booking_itinerary">
								<option value=""><?php esc_html_e( 'All ', 'wp-travel' ); echo esc_html( WP_TRAVEL_POST_TITLE_SINGULAR );  ?></option>
								<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
									<option value="<?php echo esc_html( $itinerary_id ); ?>" <?php selected( $wp_travel_post_id, $itinerary_id ) ?>>
										<?php echo esc_html( $itinerary_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</p>

						<?php
						// @since 1.0.6 // Hook since
						do_action( 'wp_travel_after_stat_toolbar_fields' ); ?>
						<div class="show-all btn-show-all" style="display:<?php echo esc_attr( 'yes' ===$compare_stat ? 'none' : 'block' ); ?>" >
							<?php submit_button( esc_attr__( 'Show All', 'wp-travel' ), 'primary', 'submit' ) ?>
						</div>
						
					</div>

					<?php $field_group_display = ( 'yes' === $compare_stat ) ? 'block' : 'none';  ?>
					<div class="additional-compare-stat clearfix">
					<!-- Field groups to compare -->
					<p class="field-group field-group-compare" style="display:<?php echo esc_attr( $field_group_display ) ?>" >
						<span class="field-label"><?php esc_html_e( 'From', 'wp-travel' ); ?>:</span>
						<input type="text" name="compare_stat_from" class="datepicker-from" class="form-control" value="<?php echo esc_attr( $compare_from_date ) ?>" id="fromdate3" />
						<label class="input-group-addon btn" for="fromdate3">
						<span class="dashicons dashicons-calendar-alt"></span>
						</label>        
					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ) ?>" >
						<span class="field-label"><?php esc_html_e( 'To', 'wp-travel' ); ?>:</span>
						<input type="text" name="compare_stat_to" class="datepicker-to" class="form-control" value="<?php echo esc_attr( $compare_to_date ) ?>" id="fromdate4" />
						<label class="input-group-addon btn" for="fromdate4">
						<span class="dashicons dashicons-calendar-alt"></span>
						</label> 
					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ) ?>" >
						<span class="field-label"><?php esc_html_e( 'Country', 'wp-travel' ); ?>:</span>

						<select class="selectpicker form-control" name="compare_country">
						
							<option value=""><?php esc_html_e( 'All Country', 'wp-travel' ) ?></option>
							
							<?php foreach ( $country_list as $key => $value ) : ?>
								<option value="<?php echo esc_html( $key ); ?>" <?php selected( $key, $compare_selected_country ) ?>>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>

					</p>
					<p class="field-group field-group-compare"  style="display:<?php echo esc_attr( $field_group_display ) ?>" >
						<span class="field-label"><?php echo esc_html( WP_TRAVEL_POST_TITLE ); ?>:</span>
						<select class="selectpicker form-control" name="compare_itinerary">
							<option value=""><?php esc_html_e( 'All ', 'wp-travel' ); echo esc_html( WP_TRAVEL_POST_TITLE_SINGULAR ); ?></option>
							<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
								<option value="<?php echo esc_html( $itinerary_id ); ?>" <?php selected( $compare_itinerary_post_id, $itinerary_id ) ?>>
									<?php echo esc_html( $itinerary_name ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
					<div class="compare-all field-group-compare" style="display:<?php echo esc_attr( $field_group_display ) ?>">
						<?php submit_button( esc_attr__( 'Compare', 'wp-travel' ), 'primary', 'submit' ) ?>
					</div>
					</div>


				</form>
			</div>	
		<div class="left-block stat-toolbar-wrap">
					
		</div>
		<div class="left-block">
			<canvas id="wp-travel-booking-canvas"></canvas>
		</div>
		<div class="right-block <?php echo esc_attr( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ? 'has-compare' : '' ) ?>">

			<div class="wp-travel-stat-info">
				<?php if ( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ) : ?>
				<div class="right-block-single for-compare">
					<h3><?php esc_html_e( 'Compare 1', 'wp-travel' ) ?></h3>
				</div>
				<?php endif; ?>

				<?php
				// @since 1.0.6 // Hook since
				// do_action( 'wp_travel_before_stat_info_box' );
				//if ( class_exists( 'WP_travel_paypal' ) ) : ?>
					<div class="right-block-single">
						<strong><big><?php echo esc_attr( wp_travel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales">0</big></strong><br />
						<p><?php esc_html_e( 'Total Sales', 'wp-travel' ) ?></p>
					</div>
				<?php //endif; ?>
				<div class="right-block-single">
					<strong><big class="wp-travel-max-bookings">0</big></strong><br />
					<p><?php esc_html_e( 'Bookings', 'wp-travel' ) ?></p>

				</div>
				<div class="right-block-single">
					<strong><big  class="wp-travel-max-pax">0</big></strong><br />
					<p><?php esc_html_e( 'Pax', 'wp-travel' ) ?></p>
				</div>
				<div class="right-block-single">
					<strong class="wp-travel-top-countries wp-travel-more"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></strong>
					<p><?php esc_html_e( 'Countries', 'wp-travel' ) ?></p>
				</div>
				<div class="right-block-single">
					<strong><a href="#" class="wp-travel-top-itineraries" target="_blank"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></a></strong>
					<p><?php esc_html_e( 'Top itinerary', 'wp-travel' ) ?></p>
				</div>
			</div>
			<?php if ( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ) : ?>

				<div class="wp-travel-stat-info">
					<div class="right-block-single for-compare">
						<h3><?php esc_html_e( 'Compare 2', 'wp-travel' ) ?></h3>
					</div>
					<?php
					//if ( class_exists( 'WP_travel_paypal' ) ) : ?>
						<div class="right-block-single">
							<strong><big><?php echo esc_attr( wp_travel_get_currency_symbol() ); ?></big><big class="wp-travel-total-sales-compare">0</big></strong><br />
							<p><?php esc_html_e( 'Total Sales', 'wp-travel' ) ?></p>

						</div>
					<?php //endif; ?>
					<div class="right-block-single">
						<strong><big class="wp-travel-max-bookings-compare">0</big></strong><br />
						<p><?php esc_html_e( 'Bookings', 'wp-travel' ) ?></p>

					</div>
					<div class="right-block-single">
						<strong><big  class="wp-travel-max-pax-compare">0</big></strong><br />
						<p><?php esc_html_e( 'Pax', 'wp-travel' ) ?></p>
					</div>
					<div class="right-block-single">
						<strong class="wp-travel-top-countries-compare wp-travel-more"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></strong>
						<p><?php esc_html_e( 'Countries', 'wp-travel' ) ?></p>
					</div>
					<div class="right-block-single">
						<strong><a href="#" class="wp-travel-top-itineraries-compare" target="_blank"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></a></strong>
						<p><?php esc_html_e( 'Top itinerary', 'wp-travel' ) ?></p>
					</div>
				</div>
			<?php endif; ?>
			<?php
			// @since 1.0.6 // Hook since
			// do_action( 'wp_travel_after_stat_info_box' ); ?>
		</div>
	</div>	
	<?php
}
