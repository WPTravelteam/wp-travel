<?php
class WP_Travel_API_Cart{
    public static function add_to_cart(){
        $postData = json_decode( file_get_contents('php://input') );
        if ( empty( $postData->trip_id ) ) {
            $response['message'] = __( 'Invalid trip id', 'wp-travel' );
            $response['status_code'] = 'WP_TRAVEL_NO_TRIP_ID';
			wp_send_json_error( $response );
		}
        global $wt_cart;
        $allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );

		if ( ! $allow_multiple_cart_items ) {
			$wt_cart->clear();
        }

        // $trip_id        = $postData['trip_id'];
        // $price_key      = isset( $postData['price_key'] ) ? $postData['price_key'] : '';
        // $pricing_id     = isset( $_POST['pricing_id'] ) ? $_POST['pricing_id'] : ''; // @since 3.0.0
		// $arrival_date   = isset( $_POST['arrival_date'] ) ? $_POST['arrival_date'] : '';
		// $departure_date = isset( $_POST['departure_date'] ) ? $_POST['departure_date'] : ''; // Need to remove. is't post value.

        wp_send_json_success( $postData );
    }
}