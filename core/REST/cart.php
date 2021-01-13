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
        $allow_multiple_items = WP_Travel_Cart::allow_multiple_items();

		if ( ! $allow_multiple_items ) {
			$wt_cart->clear();
        }
        wp_send_json_success( $postData );
    }
}