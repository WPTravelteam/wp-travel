<?php
/**
 * Booking Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	global $post;
	$max_pax = get_post_meta( $post->ID, 'wp_travel_group_size', true );

	$booking_fileds = array(
		'first_name'	=> array(
			'type' => 'text',
			'label' => __( 'First Name', 'wp-travel' ),
			'name' => 'wp_travel_fname',
			'id' => 'wp-travel-fname',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'priority' => 10,
		),

		'last_name'		=> array(
			'type' => 'text',
			'label' => __( 'Last Name', 'wp-travel' ),
			'name' => 'wp_travel_lname',
			'id' => 'wp-travel-lname',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'priority' => 20,
		),
		'country'		=> array(
			'type' => 'select',
			'label' => __( 'Country', 'wp-travel' ),
			'name' => 'wp_travel_country',
			'id' => 'wp-travel-country',
			'options' => wp_travel_get_countries(),
			'validations' => array(
				'required' => true,
			),
			'priority' => 30,
		),
		'address'		=> array(
			'type' => 'text',
			'label' => __( 'Address', 'wp-travel' ),
			'name' => 'wp_travel_address',
			'id' => 'wp-travel-address',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
			),
			'priority' => 40,
		),
		'phone_number'	=> array(
			'type' => 'text',
			'label' => __( 'Phone Number', 'wp-travel' ),
			'name' => 'wp_travel_phone',
			'id' => 'wp-travel-phone',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				'type' => 'number',
			),
			'priority' => 50,
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'Email', 'wp-travel' ),
			'name' => 'wp_travel_email',
			'id' => 'wp-travel-email',
			'validations' => array(
				'required' => true,
				'maxlength' => '60',
			),
			'priority' => 60,
		),
		'arrival_date' => array(
			'type' => 'date',
			'label' => __( 'Arrival Date', 'wp-travel' ),
			'name' => 'wp_travel_arrival_date',
			'id' => 'wp-travel-arrival-date',
			'class' => 'wp-travel-datepicker',
			'validations' => array(
				'required' => true,
			),
			'attributes' => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority' => 70,
		),
		'departure_date' => array(
			'type' => 'date',
			'label' => __( 'Departure Date', 'wp-travel' ),
			'name' => 'wp_travel_departure_date',
			'id' => 'wp-travel-departure-date',
			'class' => 'wp-travel-datepicker',
			'validations' => array(
				'required' => true,
			),
			'attributes' => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority' => 80,
		),
		'trip_duration' => array(
			'type' => 'number',
			'label' => __( 'Trip Duration', 'wp-travel' ),
			'name' => 'wp_travel_trip_duration',
			'id' => 'wp-travel-trip-duration',
			'class' => 'wp-travel-trip-duration',
			'validations' => array(
				'required' => true,
				'min' => '1',
			),
			'attributes' => array( 'min' => 1 ),
			'priority' => 70,
		),
		'pax' => array(
			'type' => 'number',
			'label' => __( 'Pax', 'wp-travel' ),
			'name' => 'wp_travel_pax',
			'id' => 'wp-travel-pax',
			'default' => 1,
			'validations' => array(
				'required' => '',
				'min' => '1',				
			),
			'attributes' => array( 'min' => 1 ),
			'priority' => 81,
		),
		'note' => array(
			'type' => 'textarea',
			'label' => __( 'Note', 'wp-travel' ),
			'name' => 'wp_travel_note',
			'id' => 'wp-travel-note',
			'placeholder' => 'Enter some notes...',
			'rows' => 6,
			'cols' => 150,
			'priority' => 90,
			'wrapper_class' => 'full-width textarea-field',
		),
	);
	if ( $max_pax ) {
		$booking_fileds['pax']['validations']['max'] = $max_pax;
		$booking_fileds['pax']['attributes']['max'] = $max_pax;
	}
	return apply_filters( 'wp_travel_booking_form_fields', $booking_fileds );
}

/**
 * Return HTM of Booking Form
 *
 * @return [type] [description]
 */
function wp_travel_get_booking_form() {
	global $post;
	include WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
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
	$form = new WP_Travel_FW_Form();
	$fields['post_id'] = array(
		'type' => 'hidden',
		'name' => 'wp_travel_post_id',
		'id' => 'wp-travel-post-id',
		'default' => $post->ID,
	);
	$fixed_departure = get_post_meta( $post->ID, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	if ( 'no' === $fixed_departure ) {
		unset( $fields['arrival_date'], $fields['departure_date'] );		
	} else {
		unset( $fields['trip_duration'] );
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
	if ( 'itinerary-booking' === $post->post_type ): ?>
        
			<style type="text/css">
				#visibility {
				    display: none;
				}
				#misc-publishing-actions, #minor-publishing-actions{display:none}
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

	$wp_travel_itinerary_list = wp_travel_get_itineraries_array(); ?>

	<div class="wp-travel-booking-form-wrapper">
		<form action="" method="post">
			<?php do_action( 'wp_travel_booking_before_form_field' ); ?>
			<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>
			<div class="wp-travel-form-field full-width">
				<label for="wp-travel-post-id"><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></label>
				<select id="wp-travel-post-id" name="wp_travel_post_id" >
				<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
					<option value="<?php echo esc_html( $itinerary_id, 'wp-travel' ); ?>" <?php selected( $wp_travel_post_id, $itinerary_id ) ?>>
						<?php echo esc_html( $itinerary_name, 'wp-travel' ); ?>
					</option>
				<?php endforeach; ?>
				</select>
			</div>

			<?php
			$fields = wp_travel_booking_form_fields();			
			$priority = array();
			foreach ( $fields as $key => $row ) {
				$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
			}
			array_multisort( $priority, SORT_ASC, $fields );
			foreach ( $fields as $field ) : ?>
				<?php
				$input_val = get_post_meta( $post->ID, $field['name'], true );
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
				switch ( $field_type ) {
					case 'select': ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class,  'wp-travel' ) ?>">
						<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<?php $options = $field['options']; ?>
							<?php if ( count( $options ) > 0 ) : ?>
							<select id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>">
								<?php foreach ( $options as $short_name => $name ) : ?>
									<option <?php selected( $input_val, $short_name ); ?> value="<?php echo esc_attr( $short_name, 'wp-travel' ) ?>"><?php esc_html_e( $name, 'wp-travel' ) ?></option>
								<?php endforeach; ?>
							</select>
							<?php endif; ?>
						</div>
					<?php break; ?>
					<?php case 'radio' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class,  'wp-travel' ) ?>">
							<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>							
							<?php
							if ( ! empty( $field['options'] ) ) {
								foreach ( $field['options'] as $key => $value ) { ?>
									<label class="radio-checkbox-label"><input type="<?php echo esc_attr( $field['type'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $key, 'wp-travel' ); ?>" <?php checked( $input_val, $key ); ?> ><?php echo esc_html( $value, 'wp-travel' ) ?></label>
								<?php 
								}
							}
							?>
						</div>
					
					<?php break; ?>
					<?php case 'checkbox' : ?>
					<?php break; ?>
					<?php case 'textarea' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class,  'wp-travel' ) ?>">
						<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<textarea name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" placeholder="<?php esc_html_e( 'Some text...', 'wp-travel' ); ?>" rows="6" cols="150"><?php echo esc_html( $input_val, 'wp-travel' ); ?></textarea>
						</div>
					<?php break; ?>
					<?php case 'date' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class,  'wp-travel' ) ?>">
							<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<input class="wp-travel-date" type="text" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $input_val, 'wp-travel' ); ?>" >
						</div>
					<?php break; ?>
					<?php case 'hidden' : ?>
						
						<input type="<?php echo esc_attr( $field['type'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $input_val, 'wp-travel' ); ?>" >
						
					<?php break; ?>
					<?php default : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class,  'wp-travel' ) ?>">
							<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<?php echo $before_field; ?>
							<input type="<?php echo esc_attr( $field['type'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $input_val, 'wp-travel' ); ?>" >
						</div>
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
 * @param  int $post_id ID of current post.
 *
 * @return Mixed
 */
function wp_travel_save_booking_data( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}
	$order_data = array();
	$wp_travel_post_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
	update_post_meta( $post_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
	$order_data['wp_travel_post_id'] = $wp_travel_post_id;

	$fields = wp_travel_booking_form_fields();
	$priority = array();
	foreach ( $fields as $key => $row ) {
		$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
	}
	array_multisort( $priority, SORT_ASC, $fields );
	foreach ( $fields as $field ) :
		$meta_val = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';
		update_post_meta( $post_id, $field['name'], sanitize_text_field( $meta_val ) );
		$order_data[ $field['name'] ] = $meta_val;
	endforeach;

	$order_data = array_map( 'sanitize_text_field', wp_unslash( $order_data ) );
	update_post_meta( $post_id, 'order_data', $order_data );
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
			echo esc_attr( $name, 'wp-travel' );
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
		'contact_name' 	=> 'contact_name',
		// 'city' 		=> 'city'
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


add_action( 'restrict_manage_posts', 'wp_travel_restrict_manage_posts' );

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
function wp_traval_book_now() {
	if ( ! isset( $_POST[ 'wp_travel_book_now' ] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['wp_travel_security'],  'wp_travel_security_action' ) ) {
		return;
	}
	if ( ! isset( $_POST['wp_travel_post_id'] ) ) {
		return;
	}

	$trip_code = wp_travel_get_trip_code( $_POST['wp_travel_post_id'] );
	$title = 'Booking - ' . $trip_code;

	$post_array = array(
		'post_title' => $title,
		'post_content' => '',
		'post_status' => 'publish',
		'post_slug' => uniqid(),
		'post_type' => 'itinerary-booking',
		);
	$order_id = wp_insert_post( $post_array );
	update_post_meta( $order_id, 'order_data', $_POST );

	$trip_id = sanitize_text_field( $_POST['wp_travel_post_id'] );
	$booking_count = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
	$booking_count = ( isset( $booking_count ) && '' != $booking_count ) ? $booking_count : 0;
	$new_booking_count = $booking_count + 1;
	update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

	$post_ignore = array( '_wp_http_referer', 'wp_travel_security', 'wp_travel_book_now' );
	foreach ( $_POST as $meta_name => $meta_val ) {
		if ( in_array( $meta_name , $post_ignore ) ) {
			continue;
		}
		update_post_meta( $order_id, $meta_name, sanitize_text_field( $meta_val ) );
	}

	if ( array_key_exists( 'wp_travel_date', $_POST ) ) {

		$pax_count_based_by_date = get_post_meta( $_POST['wp_travel_post_id'], 'total_pax_booked', true );

		if ( ! array_key_exists( $_POST['wp_travel_date'], $pax_count_based_by_date ) ) {
			$pax_count_based_by_date[ $_POST['wp_travel_date'] ] = 'default';
		}

		$pax_count_based_by_date[$_POST['wp_travel_date']] += $_POST['wp_travel_pax'];

		update_post_meta( $_POST['wp_travel_post_id'], 'total_pax_booked', $pax_count_based_by_date );

		$order_ids = get_post_meta( $_POST['wp_travel_post_id'], 'order_ids', true );

		if ( ! $order_ids ) {
			$order_ids = [];
		}

		array_push( $order_ids, [ 'order_id' => $order_id, 'count' => $_POST['wp_travel_pax'], 'date' => $_POST['wp_travel_date'] ] );

		update_post_meta( $_POST['wp_travel_post_id'], 'order_ids', $order_ids );
	}
	/**
	 * Hook used to add payment and its info.
	 *
	 * @since 1.0.5 // For Payment.
	 */
	do_action( 'wp_travel_after_frontend_booking_save', $order_id );
	$settings = wp_traval_get_settings();

	$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';

	// Prepare variables to assign in email.
	$client_email = $_POST['wp_travel_email'];

	$admin_email = get_option( 'admin_email' );

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
	$itinerary_id 			= sanitize_text_field( $_POST['wp_travel_post_id'] );
	$itinerary_title 		= get_the_title( $itinerary_id );

	$booking_no_of_pax 		= $_POST['wp_travel_pax'];
	$booking_scheduled_date = esc_html__( 'N/A', 'wp-travel' );
	$booking_arrival_date 	= $_POST['wp_travel_arrival_date'];
	$booking_departure_date = $_POST['wp_travel_departure_date'];

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

	$admin_message = wp_travel_admin_email_template();
	$admin_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message );
	// Client message.
	$message = wp_travel_customer_email_template();
	$message = str_replace( array_keys( $email_tags ), $email_tags, $message );		

	// Send mail to admin if booking email is set to yes.
	if ( 'yes' == $send_booking_email_to_admin ) {
		// To send HTML mail, the Content-type header must be set.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Create email headers.
		$headers .= 'From: ' . $client_email . "\r\n" .
		'Reply-To: ' . $client_email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		if ( ! wp_mail( $admin_email, wp_specialchars_decode( $title ), $admin_message, $headers ) ) {
			wp_send_json( array(
				'result'  => 0,
				'message' => __( 'Your Item Has Been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' ),
			) );
		}
	}

	// Send email to client.
	// To send HTML mail, the Content-type header must be set.
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Create email headers.
	$headers .= 'From: ' . $admin_email . "\r\n" .
	'Reply-To: ' . $admin_email . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	if ( ! wp_mail( $client_email, wp_specialchars_decode( $title ), $message, $headers ) ) {

		wp_send_json( array(
			'result'  => 0,
			'message' => __( 'Your Item Has Been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' ),
		) );
	}
	header( 'Location: ' . $_SERVER['REDIRECT_URL'] . '?' . http_build_query( [ 'booked' => true ] ) );
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
	?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Booking Stat' ); ?></h2>
		<div class="left-block">
			<div class="stat-toolbar">
				<form name="stat_toolbar" class="stat-toolbar-form" action="" method="get" >
					<input type="hidden" name="post_type" value="itineraries" >
					<input type="hidden" name="page" value="booking_chart">
					<p class="field-group">
						<span><?php esc_html_e( 'From', 'wp-travel' ); ?>:</span>
						<input type="text" name="booking_stat_from" id="datepicker-from" class="form-control" value="<?php echo esc_attr( $from_date, 'wp-travel' ) ?>">
						<label class="input-group-addon btn" for="testdate">
						<span class="fa fa-calendar"></span>
						</label>        
					</p>
					<p class="field-group">
						<span><?php esc_html_e( 'To', 'wp-travel' ); ?>:</span>
						<input type="text" name="booking_stat_to" id="datepicker-to" class="form-control" value="<?php echo esc_attr( $to_date, 'wp-travel' ) ?>"/>
						<label class="input-group-addon btn" for="testdate">
						<span class="fa fa-calendar"></span>
						</label> 
					</p>
					<p class="field-group">
						<span><?php esc_html_e( 'Country', 'wp-travel' ); ?>:</span>

						<select class="selectpicker" name="booking_country">
						
							<option value=""><?php esc_html_e( 'Select Country', 'wp-travel' ) ?></option>
							
							<?php foreach ( $country_list as $key => $value ) : ?>
								<option value="<?php echo esc_html( $key, 'wp-travel' ); ?>" <?php selected( $key, $selected_country ) ?>>
									<?php echo esc_html( $value, 'wp-travel' ); ?>
								</option>
							<?php endforeach; ?>
						</select>

					</p>
					<p class="field-group">
						<span><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?>:</span>
						<select class="selectpicker" name="booking_itinerary">
							<option value=""><?php esc_html_e( 'Select Itinerary', 'wp-travel' ) ?></option>
							<?php foreach ( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
								<option value="<?php echo esc_html( $itinerary_id, 'wp-travel' ); ?>" <?php selected( $wp_travel_post_id, $itinerary_id ) ?>>
									<?php echo esc_html( $itinerary_name, 'wp-travel' ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class="show-all">
						<input type="submit" class="btn btn-show-all" value="<?php esc_attr_e( 'Show All', 'wp-travel' ) ?>" >						
					</p>
				</form>
			</div>			
		</div>
		<div class="left-block">
			<canvas id="wp-travel-booking-canvas"></canvas>
		</div>
		<div class="right-block">
			<div>
				<strong><big class="wp-travel-max-bookings">0</big></strong><br />
				<p><?php esc_html_e( 'Bookings', 'wp-travel' ) ?></p>

			</div>
			<div>
				<strong><big  class="wp-travel-max-pax">0</big></strong><br />
				<p><?php esc_html_e( 'Pax', 'wp-travel' ) ?></p>
			</div>
			<div>
				<strong class="wp-travel-top-countries"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></strong>
				<p><?php esc_html_e( 'Countries', 'wp-travel' ) ?></p>
			</div>
			<div>
				<strong><a href="#" class="wp-travel-top-itineraries" target="_blank"><?php esc_html_e( 'N/A', 'wp-travel' ); ?></a></strong>
				<p><?php esc_html_e( 'Top itinerary', 'wp-travel' ) ?></p>
			</div>
		</div>

	</div>	
	<?php
}
