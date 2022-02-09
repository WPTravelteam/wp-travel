<?php
class WP_Travel_Helpers_REST_API {
	public static function response( $response ) {
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response );
		}
		wp_send_json_success( $response );
	}
}
