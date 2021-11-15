<?php
/**
 * Helpers Enquiry.
 *
 * @package WP_Travel
 * @since 5.0.0
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Enquiry class.
 *
 * @since 5.0.0
 */
class WpTravel_Helpers_Enquiry {

	/**
	 * Get Enquiry Meta data along with enquiry code.
	 *
	 * @param int $enquiry_id enquiry id.
	 * @since 5.0.0
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
		$enquiry_name = get_post_meta(  $enquiry_id, 'wp_travel_enquiry_name', true );
		$enquiry_email = get_post_meta(  $enquiry_id, 'wp_travel_enquiry_email', true );
		$enquiry_query = get_post_meta(  $enquiry_id, 'wp_travel_enquiry_query', true );
		
		$enq_data = array(
			'trips' => $mapped_trip,
			'wp_travel_enquiry_name' => $enquiry_name,
			'wp_travel_enquiry_email' => $enquiry_email,
			 'wp_travel_enquiry_query' => $enquiry_query
		);

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_COUPON',
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
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function update_enquiry( $enquiry_id, $data ) {
		if ( ! $enquiry_id ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_ENQUIRY_ID' );
		}
        
		// Update enquiry data
	unset($data['trips']);
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_name', $data['wp_travel_enquiry_name'] );
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_email', $data['wp_travel_enquiry_email'] );
		update_post_meta( $enquiry_id, 'wp_travel_enquiry_query', $data['wp_travel_enquiry_query'] );
		update_post_meta( $enquiry_id, 'wp_travel_trip_enquiry_data', $data );

		
        $enq_updated_data = array(
			'wp_travel_enquiry_name' => $data['wp_travel_enquiry_name'],
			'wp_travel_enquiry_email' => $data['wp_travel_enquiry_email'],
			 'wp_travel_enquiry_query' => $data['wp_travel_enquiry_query']
		);
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_ENQUIRY',
			array(
				'enquiry' => $enq_updated_data
			)
		);
	}

	

}
