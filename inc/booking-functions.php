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
				'label' => 'First Name',
				'name' => 'wp_travel_fname',
				'id' => 'wp-travel-fname',
				'validations' => array(
					'required' => '',
					'maxlength' => '50',
					'type' => 'alphanum',
				),
				'priority' => 10,
			),

			'last_name'		=> array(
				'type' => 'text',
				'label' => 'Last Name',
				'name' => 'wp_travel_lname',
				'id' => 'wp-travel-lname',
				'validations' => array(
					'required' => '',
					'maxlength' => '50',
					'type' => 'alphanum',
				),
				'priority' => 20,
			),
			'country'		=> array(
				'type' => 'select',
				'label' => 'Country',
				'name' => 'wp_travel_country',
				'id' => 'wp-travel-country',
				'options' => wp_travel_get_countries(),
				'validations' => array(
					'required' => '',
				),
				'priority' => 30,
			),
			'address'		=> array(
				'type' => 'text',
				'label' => 'Address',
				'name' => 'wp_travel_address',
				'id' => 'wp-travel-address',
				'validations' => array(
					'required' => '',
					'maxlength' => '50',
				),
				'priority' => 40,
			),
			'phone_number'	=> array(
				'type' => 'text',
				'label' => 'Phone Number',
				'name' => 'wp_travel_phone',
				'id' => 'wp-travel-phone',
				'validations' => array(
					'required' => '',
					'maxlength' => '50',
				),
				'priority' => 50,
			),
			'email' => array(
				'type' => 'email',
				'label' => 'Email',
				'name' => 'wp_travel_email',
				'id' => 'wp-travel-email',
				'validations' => array(
					'required' => '',
					'maxlength' => '60',
				),
				'priority' => 60,
			),
			'arrival_date' => array(
				'type' => 'date',
				'label' => 'Arrival Date',
				'name' => 'wp_travel_arrival_date',
				'id' => 'wp-travel-arrival-date',
				'class' => 'wp-travel-datepicker',
				'validations' => array(
					'required' => '',
				),
				'attributes' => array( 'readonly' => 'readonly' ),
				'date_options' => array(),
				'priority' => 70,
			),
			'departure_date' => array(
				'type' => 'date',
				'label' => 'Departure Date',
				'name' => 'wp_travel_departure_date',
				'id' => 'wp-travel-departure-date',
				'class' => 'wp-travel-datepicker',
				'validations' => array(
					'required' => '',
				),
				'attributes' => array( 'readonly' => 'readonly' ),
				'date_options' => array(),
				'priority' => 80,
			),
			'note' => array(
				'type' => 'textarea',
				'label' => 'Note',
				'name' => 'wp_travel_note',
				'id' => 'wp-travel-note',
				'placeholder' => 'Enter some notes...',
				'rows' => 6,
				'cols' => 150,
				'priority' => 90,
				'wrapper_class' => 'textarea-field',
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
			'value' => 'Book Now',
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
	$wp_travel_fname = get_post_meta( $post->ID, 'wp_travel_fname', true );
	$wp_travel_mname = get_post_meta( $post->ID, 'wp_travel_mname', true );
	$wp_travel_lname = get_post_meta( $post->ID, 'wp_travel_lname', true );
	$wp_travel_country = get_post_meta( $post->ID, 'wp_travel_country', true );
	$wp_travel_address = get_post_meta( $post->ID, 'wp_travel_address', true );
	$wp_travel_phone = get_post_meta( $post->ID, 'wp_travel_phone', true );
	$wp_travel_email = get_post_meta( $post->ID, 'wp_travel_email', true );
	$wp_travel_pax = get_post_meta( $post->ID, 'wp_travel_pax', true );
	$wp_travel_pax = $wp_travel_pax ? $wp_travel_pax : 1;
	$wp_travel_arrival_date = get_post_meta( $post->ID, 'wp_travel_arrival_date', true );
	$wp_travel_departure_date = get_post_meta( $post->ID, 'wp_travel_departure_date', true );
	$wp_travel_note = get_post_meta( $post->ID, 'wp_travel_note', true );

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
			<div class="wp-travel-form-field">
				<label for="wp-travel-fname"><?php esc_html_e( 'First Name', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-fname" name="wp_travel_fname" value="<?php _e( $wp_travel_fname, 'wp-travel' ); ?>" >
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-mname"><?php esc_html_e( 'Middle Name', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-mname" name="wp_travel_mname"  value="<?php _e( $wp_travel_mname, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-lname"><?php esc_html_e( 'Last Name', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-lname" name="wp_travel_lname"  value="<?php _e( $wp_travel_lname, 'wp-travel' ); ?>">
			</div>

			<div class="wp-travel-form-field">
				<label for="wp-travel-country"><?php esc_html_e( 'Country', 'wp-travel' ); ?></label>
				<?php $countries = wp_travel_get_countries(); ?>
				<?php if ( count( $countries ) > 0 ) : ?>
				<select id="wp-travel-country" name="wp_travel_country">
					<?php foreach ( $countries as $short_name => $name ) : ?>
						<option <?php selected( $wp_travel_country, $short_name ); ?> value="<?php esc_html_e( $short_name, 'wp-travel' ) ?>"><?php esc_html_e( $name, 'wp-travel' ) ?></option>
					<?php endforeach; ?>
			    </select>
			    <?php endif; ?>
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-address"><?php esc_html_e( 'Address', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-address" name="wp_travel_address"  value="<?php _e( $wp_travel_address, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-phone"><?php esc_html_e( 'Phone', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-phone" name="wp_travel_phone"  value="<?php _e( $wp_travel_phone, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-email"><?php esc_html_e( 'Email', 'wp-travel' ); ?></label>
				<input type="email" id="wp-travel-email" name="wp_travel_email"  value="<?php _e( $wp_travel_email, 'wp-travel' ); ?>">
			</div>
	        <?php echo apply_filters('wp_travel_booking_form_after_email_html_content',''); ?>
			<div class="wp-travel-form-field">
				<label for="wp-travel-pax"><?php esc_html_e( 'No of PAX', 'wp-travel' ); ?></label>
				<input type="number" min="1" max="<?php echo apply_filters( 'wp_travel_max_pax_number', 100000 ); ?> " id="wp-travel-pax" name="wp_travel_pax"  value="<?php _e( $wp_travel_pax, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-arrival-date"><?php esc_html_e( 'Arrival Date', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-arrival-date" name="wp_travel_arrival_date" value="<?php _e( $wp_travel_arrival_date, 'wp-travel' ); ?>" >
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-departure-date"><?php esc_html_e( 'Departure Date', 'wp-travel' ); ?></label>
				<input type="text" id="wp-travel-departure-date" name="wp_travel_departure_date" value="<?php _e( $wp_travel_departure_date, 'wp-travel' ); ?>" >
			</div>
			<div class="wp-travel-form-field textarea-field">
				<label for="wp-travel-note"><?php esc_html_e( 'Note', 'wp-travel' ); ?></label>
				<textarea name="wp_travel_note" id="wp-travel-note" placeholder="<?php esc_html_e( 'Some text...', 'wp-travel' ); ?>" rows="6" cols="150"><?php _e( $wp_travel_note, 'wp-travel' ); ?></textarea>
			</div>
			<?php 
				wp_enqueue_script('jquery-datepicker-lib');
				wp_enqueue_script('jquery-datepicker-lib-eng');
			?>
			<script>
				jQuery(document).ready( function($){
					$("#wp-travel-arrival-date, #wp-travel-departure-date").datepicker({
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
	$wp_travel_post_id = sanitize_text_field( $_POST['wp_travel_post_id'] );
	$wp_travel_fname = sanitize_text_field( $_POST['wp_travel_fname'] );
	$wp_travel_mname = sanitize_text_field( $_POST['wp_travel_mname'] );
	$wp_travel_lname = sanitize_text_field( $_POST['wp_travel_lname'] );
	$wp_travel_country = sanitize_text_field( $_POST['wp_travel_country'] );
	$wp_travel_address = sanitize_text_field( $_POST['wp_travel_address'] );
	$wp_travel_phone = sanitize_text_field( $_POST['wp_travel_phone'] );
	$wp_travel_email = sanitize_text_field( $_POST['wp_travel_email'] );
	$wp_travel_pax = sanitize_text_field( $_POST['wp_travel_pax'] );
	$wp_travel_arrival_date = sanitize_text_field( $_POST['wp_travel_arrival_date'] );
	$wp_travel_departure_date = sanitize_text_field( $_POST['wp_travel_departure_date'] );	
	$wp_travel_note = sanitize_text_field( $_POST['wp_travel_note'] );

	update_post_meta( $post_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
	update_post_meta( $post_id, 'wp_travel_fname', sanitize_text_field( $wp_travel_fname ) );
	update_post_meta( $post_id, 'wp_travel_mname', sanitize_text_field( $wp_travel_mname ) );
	update_post_meta( $post_id, 'wp_travel_lname', sanitize_text_field( $wp_travel_lname ) );
	update_post_meta( $post_id, 'wp_travel_country', sanitize_text_field( $wp_travel_country ) );
	update_post_meta( $post_id, 'wp_travel_address', sanitize_text_field( $wp_travel_address ) );
	update_post_meta( $post_id, 'wp_travel_phone', sanitize_text_field( $wp_travel_phone ) );
	update_post_meta( $post_id, 'wp_travel_email', sanitize_text_field( $wp_travel_email ) );
	update_post_meta( $post_id, 'wp_travel_pax', sanitize_text_field( $wp_travel_pax ) );

	update_post_meta( $post_id, 'wp_travel_arrival_date', sanitize_text_field( $wp_travel_arrival_date ) );
	update_post_meta( $post_id, 'wp_travel_departure_date', sanitize_text_field( $wp_travel_departure_date ) );
	update_post_meta( $post_id, 'wp_travel_note', sanitize_text_field( $wp_travel_note ) );

	$order_data['wp_travel_post_id'] = $wp_travel_post_id;
	$order_data['wp_travel_fname'] = $wp_travel_fname;
	$order_data['wp_travel_mname'] = $wp_travel_mname;
	$order_data['wp_travel_lname'] = $wp_travel_lname;
	$order_data['wp_travel_country'] = $wp_travel_country;
	$order_data['wp_travel_address'] = $wp_travel_address;
	$order_data['wp_travel_phone'] = $wp_travel_phone;
	$order_data['wp_travel_email'] = $wp_travel_email;
	$order_data['wp_travel_pax'] = $wp_travel_pax;
	$order_data['wp_travel_arrival_date'] = $wp_travel_arrival_date;
	$order_data['wp_travel_departure_date'] = $wp_travel_departure_date;
	$order_data['wp_travel_note'] = $wp_travel_note;

	$order_data = array_map( 'sanitize_text_field', wp_unslash( $order_data ) );
	update_post_meta( $post_id, 'order_data', $order_data );
	
	header("Location: ". $_SERVER['REDIRECT_URL'].'?'.http_build_query(['booked'=>true]));
	exit;
}

add_action( 'save_post', 'wp_travel_save_booking_data' );

add_filter( 'parse_query', 'wp_travel_posts_filter' );

/**
 * If submitted filter by post meta.
 *
 * @param  (wp_query object) $query object.
 *
 * @return void
 */
function wp_travel_posts_filter( $query ) {
	global $pagenow;
	$type = '';
	if ( isset( $_GET['post_type'] ) ) {
		$type = $_GET['post_type'];
	}

	if ( 'itinerary-booking' == $type && is_admin() && 'edit.php' == $pagenow && isset( $_GET['wp_travel_post_id'] ) && '' !== $_GET['wp_travel_post_id'] ) {

		$query->query_vars['meta_key'] = 'wp_travel_post_id';
		$query->query_vars['meta_value'] = $_GET['wp_travel_post_id'];
	}
}

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
