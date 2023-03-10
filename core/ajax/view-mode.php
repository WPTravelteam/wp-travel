<?php
/**
 * View Mode ajax
 */
class WP_Travel_Ajax_View_Mode {
	public static function init() {
		// Get Pricings.
		add_action( 'wp_ajax_wptravel_view_mode', array( __CLASS__, 'set_view_mode_cookie' ) );
		add_action( 'wp_ajax_nopriv_wptravel_view_mode', array( __CLASS__, 'set_view_mode_cookie' ) );
	}

	public static function set_view_mode_cookie() {
        
		WP_Travel::verify_nonce();

        $payload = WP_Travel::get_sanitize_request('post');
        $view_mode = $payload['mode'];
        setcookie( 'wptravel_view_mode', $view_mode, time() + 604800, '/' );
		$response  = array( 'success' => true );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_View_Mode::init();
