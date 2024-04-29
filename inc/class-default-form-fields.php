<?php
/**
 * Default form fields.
 *
 * @package WP_Travel
 */

/**
 * Default form fields.
 */
class WP_Travel_Default_Form_Fields { // @phpcs:ignore

	/**
	 * Default field to generate enquiry form fields.
	 *
	 * @return array Returns form fields.
	 */
	public static function enquiry() {

		$strings                  = WpTravel_Helpers_Strings::get();
		$label_full_name          = $strings['full_name'];
		$label_enter_your_name    = $strings['enter_your_name'];
		$label_email              = $strings['email'];
		$label_enter_your_email   = $strings['enter_your_email'];
		$label_enquiry_message    = $strings['enquiry_message'];
		$label_enter_your_enquiry = $strings['enter_your_enquiry'];
		$fields                   = array(
			'full_name' => array(
				'type'        => 'text',
				'label'       => $label_full_name,
				'name'        => 'wp_travel_enquiry_name',
				'id'          => 'wp-travel-enquiry-name',
				'placeholder' => $label_enter_your_name,
				'validations' => array(
					'required'  => true,
					'maxlength' => '80',
				),
				'attributes'  => array(
					'placeholder' => $label_enter_your_name,
				),
				'priority'    => 10,
			),
			'email'     => array(
				'type'        => 'email',
				'label'       => $label_email,
				'name'        => 'wp_travel_enquiry_email',
				'id'          => 'wp-travel-enquiry-email',
				'validations' => array(
					'required'  => true,
					'maxlength' => '60',
				),
				'attributes'  => array(
					'placeholder' => $label_enter_your_email,
				),
				'priority'    => 60,
			),
			'note'      => array(
				'type'          => 'textarea',
				'label'         => $label_enquiry_message,
				'name'          => 'wp_travel_enquiry_query',
				'id'            => 'wp-travel-enquiry-query',
				'attributes'    => array(
					'placeholder' => $label_enter_your_enquiry,
					'rows'        => 6,
					'cols'        => 150,
				),
				'priority'      => 90,
				'wrapper_class' => 'full-width textarea-field',
			),
		);
		return $fields;
	}

	/**
	 * Default field to generate booking form fields.
	 *
	 * @return array Returns form fields.
	 */
	public static function billing() {
		$fields = array(
			'wp_travel_billing_address_heading' => array(
				'type'        => 'heading',
				'label'       => __( 'Billing Address', 'wp-travel' ),
				'name'        => 'wp_travel_billing_address_heading',
				'id'          => 'wp-travel-billing-address-heading',
				'class'       => 'panel-title',
				'heading_tag' => 'h4',
				'priority'    => 1,
			),
			'address'                           => array(
				'type'        => 'text',
				'label'       => __( 'Address', 'wp-travel' ),
				'name'        => 'wp_travel_address',
				'id'          => 'wp-travel-address',
				'validations' => array(
					'required'  => true,
					'maxlength' => '50',
				),
				'priority'    => 10,
			),
			'billing_city'                      => array(
				'type'        => 'text',
				'label'       => __( 'City', 'wp-travel' ),
				'name'        => 'billing_city',
				'id'          => 'wp-travel-billing-city',
				'validations' => array(
					'required' => true,
				),
				'priority'    => 20,
			),
			'billing_postal'                    => array(
				'type'        => 'text',
				'label'       => __( 'Postal', 'wp-travel' ),
				'name'        => 'billing_postal',
				'id'          => 'wp-travel-billing-postal',
				'validations' => array(
					'required' => true,
				),
				'priority'    => 30,
			),
			'country'                           => array(
				'type'        => 'country_dropdown',
				'label'       => __( 'Country', 'wp-travel' ),
				'name'        => 'wp_travel_country',
				'id'          => 'wp-travel-country',
				'validations' => array(
					'required' => true,
				),
				'priority'    => 30,
			),
			'note'                              => array(
				'type'          => 'textarea',
				'label'         => __( 'Note', 'wp-travel' ),
				'name'          => 'wp_travel_note',
				'id'            => 'wp-travel-note',
				'placeholder'   => __( 'Enter some notes...', 'wp-travel' ),
				'rows'          => 6,
				'cols'          => 150,
				'priority'      => 90,
				'wrapper_class' => 'full-width textarea-field',
			),
		);

		return $fields;
	}

	/**
	 * Array List of form field to generate booking fields.
	 *
	 * @return array Returns form fields.
	 */
	public static function _billing() { // @phpcs:ignore
		if ( ! WP_Travel::verify_nonce( true ) ) {
			return;
		}
		global $post, $wt_cart;

		$trip_id    = isset( $post->ID ) ? $post->ID : 0;
		$cart_items = $wt_cart->getItems();

		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
			$cart_trip = array_slice( $cart_items, 0, 1 );
			$cart_trip = array_shift( $cart_trip );
			$trip_id   = isset( $cart_trip['trip_id'] ) ? $cart_trip['trip_id'] : $trip_id;
		}

		if ( $trip_id > 0 ) {
			$max_pax = get_post_meta( $trip_id, 'wp_travel_group_size', true );
			$max_pax = (int) $max_pax;
		}

		$pax_size = 1;
		/**
		 * We are checking nonce above using WP_Travel::verify_nonce();
		 */
		if ( isset( $_REQUEST['pax'] ) && is_array( $_REQUEST['pax'] ) ) { // @phpcs:ignore
			$booked_pax_size = array_sum( sanitize_text_field( wp_unslash( $_REQUEST['pax'] ) ) ); // @phpcs:ignore
			if ( $booked_pax_size <= $max_pax ) {
				$pax_size = $booked_pax_size;
			}
		}

		/**
		 * We are checking nonce above using WP_Travel::verify_nonce();
		 */
		$price_key = isset( $_GET['price_key'] ) && '' != $_GET['price_key'] ? sanitize_text_field( wp_unslash( $_GET['price_key'] ) ) : ''; // @phpcs:ignore

		$booking_fileds = array(
			'pax'            => array(
				'type'        => 'hidden',
				'label'       => __( 'Pax', 'wp-travel' ),
				'name'        => 'wp_travel_pax',
				'id'          => 'wp-travel-pax',
				'default'     => $pax_size,
				'validations' => array(
					'required' => '',
					'min'      => 1,
				),
				'attributes'  => array( 'min' => 1 ),
				'priority'    => 81,
			),
			'trip_price_key' => array(
				'type'     => 'hidden',
				'name'     => 'price_key',
				'id'       => 'wp-travel-price-key',
				'default'  => $price_key,
				'priority' => 98,
			),

		);
		if ( isset( $max_pax ) && '' != $max_pax ) {
			$booking_fileds['pax']['validations']['max'] = $max_pax;
			$booking_fileds['pax']['attributes']['max']  = $max_pax;
		}
		return $booking_fileds;
	}

	/**
	 * Default field to generate traveller form fields.
	 *
	 * @return array Returns form fields.
	 */
	public static function traveller() {
		$fields = array(
			'first_name'   => array(
				'type'        => 'text',
				'label'       => __( 'First Name', 'wp-travel' ),
				'name'        => 'wp_travel_fname_traveller',
				'id'          => 'wp-travel-fname',
				'validations' => array(
					'required'  => true,
					'maxlength' => '50',
				),
				'default'     => '',
				'priority'    => 10,
			),
			'last_name'    => array(
				'type'        => 'text',
				'label'       => __( 'Last Name', 'wp-travel' ),
				'name'        => 'wp_travel_lname_traveller',
				'id'          => 'wp-travel-lname',
				'validations' => array(
					'required'  => true,
					'maxlength' => '50',
				),
				'default'     => '',
				'priority'    => 20,
			),
			'country'                           => array(
				'type'        => 'country_dropdown',
				'label'       => __( 'Country', 'wp-travel' ),
				'name'        => 'wp_travel_country_traveller',
				'id'          => 'wp-travel-country',
				'validations' => array(
					'required' => true,
				),
				'priority'    => 30,
			),
			'phone_number' => array(
				'type'        => 'text',
				'label'       => __( 'Phone Number', 'wp-travel' ),
				'name'        => 'wp_travel_phone_traveller',
				'id'          => 'wp-travel-phone',
				'validations' => array(
					'required'  => true,
					'maxlength' => '50',
					'pattern'   => '^[\d\+\-\.\(\)\/\s]*$',
				),
				'default'     => '',
				'priority'    => 50,
			),
			'email'        => array(
				'type'        => 'email',
				'label'       => __( 'Email', 'wp-travel' ),
				'name'        => 'wp_travel_email_traveller',
				'id'          => 'wp-travel-email',
				'validations' => array(
					'required'  => true,
					'maxlength' => '60',
				),
				'default'     => '',
				'priority'    => 60,
			),
			'dob'          => array(
				'type'         => 'date',
				'label'        => __( 'Date of Birth', 'wp-travel' ),
				'name'         => 'wp_travel_date_of_birth_traveller',
				'id'           => 'wp-travel-date-of-birth',
				'class'        => 'wp-travel-datepicker',

				'attributes'   => array(
					'readonly'       => 'readonly',
					'data-max-today' => true,
				),
				'date_options' => array(),
				'priority'     => 80,
			),
			'gender'       => array(
				'type'          => 'radio',
				'label'         => __( 'Gender', 'wp-travel' ),
				'name'          => 'wp_travel_gender_traveller',
				'id'            => 'wp-travel-gender',
				'wrapper_class' => 'wp-travel-radio-group ',

				'options'       => array(
					'male'   => __( 'Male', 'wp-travel' ),
					'female' => __( 'Female', 'wp-travel' ),
				),
				'default'       => 'male',
				'priority'      => 100,
			),
		);
		return $fields;
	}
}
