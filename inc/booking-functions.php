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
	return apply_filters( 'wp_travel_booking_form_fields',
		array(
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
					'max' => '60', // Make it dynamic.
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
		)
	);
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
				<?php foreach( $wp_travel_itinerary_list as $itinerary_id => $itinerary_name ) : ?>
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
				switch ( $field_type ) {
					case 'select': ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'],  'wp-travel' ) ?>">
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
						<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'],  'wp-travel' ) ?>">
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
						<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'],  'wp-travel' ) ?>">
						<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<textarea name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" placeholder="<?php esc_html_e( 'Some text...', 'wp-travel' ); ?>" rows="6" cols="150"><?php echo esc_html( $input_val, 'wp-travel' ); ?></textarea>
						</div>
					<?php break; ?>
					<?php case 'date' : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'],  'wp-travel' ) ?>">
							<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
							<input class="wp-travel-date" type="text" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $input_val, 'wp-travel' ); ?>" >
						</div>
					<?php break; ?>
					<?php case 'hidden' : ?>
						
						<input type="<?php echo esc_attr( $field['type'],  'wp-travel' ) ?>" id="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>" name="<?php echo esc_attr( $field['name'],  'wp-travel' ) ?>" value="<?php echo esc_attr( $input_val, 'wp-travel' ); ?>" >
						
					<?php break; ?>
					<?php default : ?>
						<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'],  'wp-travel' ) ?>">
							<label for="<?php echo esc_attr( $field['id'],  'wp-travel' ) ?>"><?php echo esc_attr__( $field['label'],  'wp-travel' ) ?></label>
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


add_action('restrict_manage_posts', 'wp_travel_restrict_manage_posts' );

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
