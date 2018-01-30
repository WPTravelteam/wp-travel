<?php
/**
 * Enquiry Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Array List of form field to generate enquiry form fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_enquiries_form_fields() {
	global $post;

	$post_id = 0;
	if ( isset( $post->ID ) ) {
		$post_id = $post->ID;
	}
	if ( isset( $_POST['wp_travel_post_id'] ) ) {
		$post_id = $_POST['wp_travel_post_id'];
	}

	$enquiry_fields = array(
		'full_name'	=> array(
			'type' => 'text',
			'label' => __( 'Full Name', 'wp-travel' ),
			'name' => 'wp_travel_enquiry_name',
			'id' => 'wp-travel-enquiry-name',
			'placeholder' => __( 'Enter your name', 'wp-travel' ),
			'validations' => array(
				'required' => true,
				'maxlength' => '80',
				// 'type' => 'alphanum',
			),
			'attributes' => array(
				'placeholder' => __( 'Enter your full name', 'wp-travel' ),
			),
			'priority' => 10,
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'Email', 'wp-travel' ),
			'name' => 'wp_travel_enquiry_email',
			'id' => 'wp-travel-enquiry-email',
			'validations' => array(
				'required' => true,
				'maxlength' => '60',
			),
			'attributes' => array(
				'placeholder' => __( 'Enter your email', 'wp-travel' ),
			),
			'priority' => 60,
		),
		'note' => array(
			'type' => 'textarea',
			'label' => __( 'Enquiry Message', 'wp-travel' ),
			'name' => 'wp_travel_enquiry_query',
			'id' => 'wp-travel-enquiry-query',
			'placeholder' => __( 'Enter your enqiury...', 'wp-travel' ),
			'rows' => 6,
			'cols' => 150,
			'priority' => 90,
			'wrapper_class' => 'full-width textarea-field',
		),
	);
	return apply_filters( 'wp_travel_enquiries_form_fields', $enquiry_fields );
}

/**
 * Return HTM of Enquiry Form
 *
 * @return [type] [description]
 */
function wp_travel_get_enquiries_form() {
	global $post;
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	$form_options = array(
        'id' => 'wp-travel-enquiries',
        'class' => 'mfp-hide',
		'wrapper_class' => 'wp-travel-enquiries-form-wrapper',
		'submit_button' => array(
			'name' => 'wp_travel_enquiry_submit',
			'id' => 'wp-travel-enquiry-submit',
			'value' => __( 'SUBMIT ENQUIRY', 'wp-travel' ),
		),
		'nonce' => array(
			'action' => 'wp_travel_security_action',
			'field' => 'wp_travel_security',
		),
	);

	$fields = wp_travel_enquiries_form_fields();
	$form = new WP_Travel_FW_Form();
	$fields['wp_travel_enquiry_post_id'] = array(
		'type' => 'hidden',
		'name' => 'wp_travel_enquiry_post_id',
		'id' => 'wp-travel-enquiry-post-id',
		'default' => $post->ID,
	);

	$form->init( $form_options )->fields( $fields )->template();

}

add_action( 'add_meta_boxes', 'wp_travel_add_enquiries_data_metaboxes', 10, 2 );

/**
 * Add Enquiries Metaboxes.
 */
function wp_travel_add_enquiries_data_metaboxes(){

	global $post;
	global $wp_travel_itinerary;

	$wp_travel_post_id = get_post_meta( $post->ID, 'wp_travel_post_id', true );
	
	add_meta_box( 'wp-travel-enquiries-info', __( 'Enquiry Details <span class="wp-travel-view-enquiries"><a href="edit.php?post_type=itinerary-enquiries&wp_travel_post_id=' . $wp_travel_post_id . '">View All ' . get_the_title( $wp_travel_post_id ) . ' enquiries</a></span>', 'wp-travel' ), 'wp_travel_enquiries_info', 'itinerary-enquiries', 'normal', 'default' );

}

/**
 * WP Travel Enquiries Info
 *
 */
function wp_travel_enquiries_info(){

	global $post_id;

	$enquiry_data = get_post_meta(  $post_id, 'wp_travel_trip_enquiry_data', true );

	$form_fields = wp_travel_enquiries_form_fields();

	$priority = array();
		foreach ( $form_fields as $key => $row ) {
			$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
		}
	array_multisort( $priority, SORT_ASC, $form_fields );

	$wp_travel_post_id = isset( $enquiry_data['post_id'] ) ? $enquiry_data['post_id'] : '';
	
	$wp_travel_itinerary_list = wp_travel_get_itineraries_array(); ?>

	<div class="wp-travel-booking-form-wrapper">
		<form action="" method="post">
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

			<?php foreach ( $form_fields as $key => $field ) :

				$field_type = $field['type'];

				$attributes = '';				
				if ( isset( $field['attributes'] ) ) {					
					foreach ( $field['attributes'] as $attribute => $attribute_val ) {
						$attributes .= sprintf( '%s=%s ', $attribute, $attribute_val );
					}
				}
				$wrapper_class = '';
				if ( isset( $field['wrapper_class'] ) ) {
					$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';

				}

				$before_field = '';
				if ( isset( $field['before_field'] ) ) {
					$before_field_class = isset( $field['before_field_class'] ) ? $field['before_field_class'] : '';
					$before_field = sprintf( '<span class="wp-travel-field-before %s">%s</span>', $before_field_class, $field['before_field'] );
				}

				$input_val = isset( $enquiry_data[$field['name']] ) ? $enquiry_data[$field['name']] : '';

				switch ( $field_type ) { 
				case 'textarea' : ?>
					<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
					<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
						<textarea <?php echo esc_attr( $attributes ) ?> name="<?php echo esc_attr( $field['name'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" placeholder="<?php esc_html_e( 'Some text...', 'wp-travel' ); ?>" rows="6" cols="150"><?php echo esc_html( $input_val ); ?></textarea>
					</div>
				<?php break; ?>
				
				<?php default : ?>
					
				<div class="wp-travel-form-field <?php echo esc_attr( $wrapper_class ) ?>">
					<label for="<?php echo esc_attr( $field['id'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></label>
					<?php echo $before_field; ?>
					<input <?php echo esc_attr( $attributes ) ?> type="<?php echo esc_attr( $field['type'] ) ?>" id="<?php echo esc_attr( $field['id'] ) ?>" name="<?php echo esc_attr( $field['name'] ) ?>" value="<?php echo esc_attr( $input_val ); ?>" >
				</div>
				
				<?php break; ?>
			
			<?php  }
			
			endforeach; ?>

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
function wp_travel_save_backend_enqueries_data( $post_id ) {
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
	if ( 'itinerary-enquiries' !== $post_type ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}
	$enqueries_data = array();
	$wp_travel_post_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
	update_post_meta( $post_id, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );
	$enquery_data['post_id'] = $wp_travel_post_id;


	$fields = wp_travel_enquiries_form_fields();
	$priority = array();
	foreach ( $fields as $key => $row ) {
		$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
	}
	array_multisort( $priority, SORT_ASC, $fields );
	foreach ( $fields as $key => $field ) :
		$meta_val = isset( $_POST[ $field['name'] ] ) ? $_POST[ $field['name'] ] : '';
		$post_id_to_update = apply_filters( 'wp_travel_booking_post_id_to_update', $post_id, $key, $field['name'] );
		update_post_meta( $post_id_to_update, $field['name'], sanitize_text_field( $meta_val ) );
		$enquery_data[ $field['name'] ] = $meta_val;
	endforeach;

	$enquery_data = array_map( 'sanitize_text_field', wp_unslash( $enquery_data ) );
	
	update_post_meta( $post_id, 'wp_travel_trip_enquiry_data', $enquery_data );
}

add_action( 'save_post', 'wp_travel_save_backend_enqueries_data' );

/**
 * Save Front End Trip Enqueries data.
 */
function wp_travel_save_user_enquiry(){

	$formdata = $_POST;

	if ( ! wp_verify_nonce( $_POST['nonce'],  'wp_travel_frontend_enqueries' ) ) {

		$errors['message'] = __('Nonce Verification Failed !!', 'wp-travel' );
		
		wp_send_json_error($errors);

		return;

	}

	$post_id = $formdata['post_id'];

	$post_type = get_post_type( $post_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
		
		$errors['message'] = __( 'Invalid Post Type', 'wp-travel' );
		
		wp_send_json_error( $errors );

		return;
	
	}

	$enquiry_data = array();

	$enquiry_data['post_id'] = isset( $formdata['post_id'] ) ? $formdata['post_id'] : '' ;

	$enquiry_data['wp_travel_enquiry_name'] = isset( $formdata['wp_travel_enquiry_name'] ) ? $formdata['wp_travel_enquiry_name'] : '' ;

	$enquiry_data['wp_travel_enquiry_email'] = isset( $formdata['wp_travel_enquiry_email'] ) ? $formdata['wp_travel_enquiry_email'] : '';

	$enquiry_data['wp_travel_enquiry_query'] = isset( $formdata['wp_travel_enquiry_query'] ) ? $formdata['wp_travel_enquiry_query'] : '' ;

	$trip_code = wp_travel_get_trip_code( $post_id );
	
	$title = 'Enquiry - ' . $trip_code;

	$post_array = array(
		'post_title' => $title,
		'post_content' => '',
		'post_status' => 'publish',
		'post_slug' => uniqid(),
		'post_type' => 'itinerary-enquiries',
		);
	
	$new_enquiry = wp_insert_post( $post_array );

	//Update Data.
	if ( ! empty( $enquiry_data ) ) {

		//Sanitize Values.
		$enquiry_data = stripslashes_deep( $enquiry_data );

		$wp_travel_post_id = isset( $enquiry_data['post_id'] ) ? $enquiry_data['post_id'] : 0;
		update_post_meta( $new_enquiry, 'wp_travel_post_id', sanitize_text_field( $wp_travel_post_id ) );

		// Finally Update enquiry data.
		update_post_meta( $new_enquiry, 'wp_travel_trip_enquiry_data', $enquiry_data );

	}

	// Prepare variables to assign in email.
	$client_email = $enquiry_data['email'];

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
	$enquiry_id 		  	= $new_enquiry;
	$itinerary_id 			= sanitize_text_field( $formdata['post_id'] );
	$itinerary_title 		= get_the_title( $itinerary_id );

	$customer_name 		  	= $enquiry_data['wp_travel_enquiry_name'];
	$customer_email 		= $enquiry_data['wp_travel_enquiry_email'];
	$customer_note 			= $enquiry_data['wp_travel_enquiry_query'];

	$email_tags = array(
		'{sitename}'				=> $sitename,
		'{itinerary_link}'			=> get_permalink( $itinerary_id ),
		'{itinerary_title}'			=> $itinerary_title,
		'{enquery_id}'				=> $enquiry_id,
		'{enquery_edit_link}'		=> get_edit_post_link( $enquiry_id ),

		'{customer_name}'			=> $customer_name,
		'{customer_email}'			=> $customer_email,
		'{customer_note}'			=> $customer_note,
	);
	apply_filters( 'wp_travel_admin_enquery_email_tags', $email_tags );

	$admin_message = wp_travel_enqueries_admin_email_template();
	$admin_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message );

		// To send HTML mail, the Content-type header must be set.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Create email headers.
		$headers .= 'From: ' . $customer_email . "\r\n" .
		'Reply-To: ' . $customer_email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		if ( ! wp_mail( $admin_email, wp_specialchars_decode( $title ), $admin_message, $headers ) ) {

			$errors = array(
				'result'  => 0,
				'message' => __( 'Your Enquiery has been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' ),
			);

			wp_send_json_error( $errors );
			return;
		}
	// If we reach here, Send Success message !!
	$trip_name = get_the_title( $post_id );
	
	$success = array(
		'message' => __( 'Enquiry sent succesfully !!', 'wp-travel' ),
	);

	// Send Success Message.
	wp_send_json_success( $success );

	die();
}
add_action('wp_ajax_wp_travel_save_user_enquiry','wp_travel_save_user_enquiry');
add_action('wp_ajax_nopriv_wp_travel_save_user_enquiry','wp_travel_save_user_enquiry');
