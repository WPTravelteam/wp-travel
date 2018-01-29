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
			'validations' => array(
				'required' => true,
				'maxlength' => '80',
				// 'type' => 'alphanum',
			),
			'priority' => 10,
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'Your Email', 'wp-travel' ),
			'name' => 'wp_travel_enquiry_email',
			'id' => 'wp-travel-enquiry-email',
			'validations' => array(
				'required' => true,
				'maxlength' => '60',
			),
			'priority' => 60,
		),
		'note' => array(
			'type' => 'textarea',
			'label' => __( 'Your Enquiry', 'wp-travel' ),
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

	print_r( $form_fields );


}

/**
 * Save Front End Trip Enqueries data.
 */
function wp_travel_save_user_enquiry(){

	$formdata = $_POST;

	if ( ! wp_verify_nonce( $_POST['nonce'],  'wp_travel_frontend_enqueries' ) ) {

		$errors[] = __('Nonce Verification Failed !!', 'wp-travel' );
		
		wp_send_json_error($errors);

		return;

	}

	$post_id = $formdata['post_id'];

	$post_type = get_post_type( $post_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
		
		$errors[] = __( 'Invalid Post Type', 'wp-travel' );
		
		wp_send_json_error( $errors );

		return;
	
	}

	$enquiry_data = array();

	$enquiry_data['name'] = isset( $formdata['wp_travel_enquiry_name'] ) ? $formdata['wp_travel_enquiry_name'] : '' ;

	$enquiry_data['email'] = isset( $formdata['wp_travel_enquiry_email'] ) ? $formdata['wp_travel_enquiry_email'] : '';

	$enquiry_data['enquery_message'] = isset( $formdata['wp_travel_enquiry_query'] ) ? $formdata['wp_travel_enquiry_query'] : '' ;

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

		// Finally Update enquiry data.
		update_post_meta( $new_enquiry, 'wp_travel_trip_enquiry_data', $enquiry_data );

	}

	// Send Success Message.
	wp_send_json_success( $post_id );

	die();
}
add_action('wp_ajax_wp_travel_save_user_enquiry','wp_travel_save_user_enquiry');
add_action('wp_ajax_nopriv_wp_travel_save_user_enquiry','wp_travel_save_user_enquiry');
