<?php
class WP_Travel_Default_Form_Fields {

  /**
   * Array List of form field to generate enquiry form fields.
   *
   * @return array Returns form fields.
   */
  static public function enquiry() {
    $fields = array(
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
   			'type'          => 'textarea',
   			'label'         => __( 'Enquiry Message', 'wp-travel' ),
   			'name'          => 'wp_travel_enquiry_query',
   			'id'            => 'wp-travel-enquiry-query',
   			'attributes' => array(
   				'placeholder' => __( 'Enter your enqiury...', 'wp-travel' ),
   				'rows'          => 6,
   				'cols'          => 150,
   			),
   			'priority'      => 90,
   			'wrapper_class' => 'full-width textarea-field',
   		),
   	);
   	return $fields;
  }
}
