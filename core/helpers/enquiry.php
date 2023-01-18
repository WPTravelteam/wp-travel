<?php
/**
 * Helpers Enquiry.
 *
 * @package WP_Travel
 * @since 5.0.5
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Enquiry class.
 *
 * @since 5.0.5
 */
class WpTravel_Helpers_Enquiry {

	/**
	 * Get Enquiry Meta data along with enquiry code.
	 *
	 * @param int $enquiry_id enquiry id.
	 * @since 5.0.5
	 *
	 * @return array
	 */
	public static function get_enquiry( $enquiry_id ) {

		$trips       = wptravel_get_itineraries_array();
		$mapped_trip = array();
		foreach ( $trips as $id => $title ) {
			$mapped_trip[] = array(
				'id'    => $id,
				'title' => $title,
			);

		}
		$enquiry_field = array();
		$enquiry_forms = array();
		$index         = 0;
		if ( class_exists( 'WP_Travel_Field_Editor_Core' ) ) {
			$enquiry_field = WP_Travel_Field_Editor_Core::enquiries_form_fields( $enquiry_fields = '' );
		}
		if ( ! empty( $enquiry_field ) ) {
			foreach ( $enquiry_field as $key => $value ) {
				$enquiry_forms[ $index ] = array( 'form_field' => $value );
				$index++;
			}
		}
		$enquiry_data       = get_post_meta( $enquiry_id, 'wp_travel_trip_enquiry_data', true );
		$enquiry_field_data = $enquiry_data ? $enquiry_data : '';
		if ( is_array( $enquiry_data ) && count( $enquiry_data ) > 0 ) {
			$enquiry_name    = $enquiry_data['wp_travel_enquiry_name'];
			$enquiry_email   = $enquiry_data['wp_travel_enquiry_email'];
			$enquiry_query   = $enquiry_data['wp_travel_enquiry_query'];
			$enquiry_trip_id = isset( $enquiry_data['post_id'] ) ? $enquiry_data['post_id'] : '';
		} else {
			$enquiry_name    = get_post_meta( $enquiry_id, 'wp_travel_enquiry_name', true );
			$enquiry_email   = get_post_meta( $enquiry_id, 'wp_travel_enquiry_email', true );
			$enquiry_trip_id = isset( $enquiry_data['post_id'] ) ? $enquiry_data['post_id'] : '';
		}
		$enq_data = array(
			'trips'                     => $mapped_trip,
			'wp_travel_enquiry_name'    => $enquiry_name,
			'wp_travel_enquiry_email'   => $enquiry_email,
			'wp_travel_enquiry_query'   => $enquiry_query,
			'wp_travel_trip_id'         => $enquiry_trip_id,
			'wp_travel_form_field'      => $enquiry_forms,
			'wp_travel_form_field_data' => $enquiry_field_data,

		);

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_ENQUIRY',
			array(
				'enquiry' => $enq_data,
			)
		);
	}

	/**
	 * Get all strings used in WP Travel.
	 *
	 * @param int   $enquiry_id Enquiry id.
	 * @param array $data Enquiry data.
	 * @since 5.0.5
	 *
	 * @return array
	 */
	public static function update_enquiry( $enquiry_id, $data ) {
		if ( ! $enquiry_id ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_ENQUIRY_ID' );
		}
		$enquiry = get_post_meta( $enquiry_id, 'wp_travel_trip_enquiry_data', true );
		foreach ( $enquiry as $key => $value ) {
			$enquiry[ $key ] = $data[ $key ] ? $data[ $key ] : $value;
		}
		// Update enquiry data.
		$data['post_id'] = $data['wp_travel_trip_id'];
		unset( $data['wp_travel_trip_id'] );
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_name', $data['wp_travel_enquiry_name'] );
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_email', $data['wp_travel_enquiry_email'] );
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_query', $data['wp_travel_enquiry_query'] );
		update_post_meta( $enquiry_id, 'wp_travel_trip_id', $data['post_id'] );
		update_post_meta( $enquiry_id, 'wp_travel_trip_enquiry_data', $enquiry ); // later going to remove from this portion.

		$enq_updated_data = array(
			'wp_travel_enquiry_name'      => $data['wp_travel_enquiry_name'],
			'wp_travel_enquiry_email'     => $data['wp_travel_enquiry_email'],
			'wp_travel_enquiry_query'     => $data['wp_travel_enquiry_query'],
			'wp_travel_trip_id'           => $data['post_id'],
			'wp_travel_trip_enquiry_data' => $enquiry,
		);
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_ENQUIRY',
			array(
				'enquiry' => $enq_updated_data,
			)
		);
	}
}
