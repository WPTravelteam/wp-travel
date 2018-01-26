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
	// return apply_filters( 'wp_travel_booking_form_contents', $content );
}

