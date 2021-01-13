<?php
class WP_Travel_Helpers_License {

	// Store URL.
	const LIVE_STORE_URL = 'http://themepalace.com';

	/**
	 * Store URL
	 *
	 * @var String
	 */
	private static $store_url;

	/**
	 * Premium Addons List.
	 */
	private static $addons = array();

	public static function count_premium_addons() {
		return count( self::$addons );
	}
	/**
	 * Init Functions
	 *
	 * @return void
	 */
	public static function init() {
		self::$store_url = ( defined( 'WP_TRAVEL_TESTING_STORE_URL' ) && '' !== WP_TRAVEL_TESTING_STORE_URL ) ? WP_TRAVEL_TESTING_STORE_URL : self::LIVE_STORE_URL;
		$premium_addons  = apply_filters( 'wp_travel_premium_addons_list', array() );
		if ( count( $premium_addons ) > 0 ) {
			foreach ( $premium_addons as $key => $premium_addon ) {
				if ( is_array( $premium_addon ) ) {
					self::$addons[ $key ] = $premium_addon;
				}
			}
		}

		// Licesnse data for
		add_filter( 'wp_travel_settings_values', 'WP_Travel_Helpers_License::settings_data' );
		add_filter( 'wp_travel_block_before_save_settings', 'WP_Travel_Helpers_License::settings_data_v4', 10, 2 );
	}

	// License data for WP Settings block.
	public static function settings_data( $settings ) {
		$premium_addons = self::$addons;

		$premium_addons_keys = array(); // TO make loop in the license block.
		$premium_addons_data = array();
		foreach ( $premium_addons as $key => $premium_addon ) :
			// Get license status.
			$status       = get_option( $premium_addon['_option_prefix'] . 'status' );
			$license_key  = isset( $settings[ $premium_addon['_option_prefix'] . 'key' ] ) ? $settings[ $premium_addon['_option_prefix'] . 'key' ] : '';
			$license_data = get_transient( $premium_addon['_option_prefix'] . 'data' );
			$filtered_key = str_replace( '-', '_', $key );

			// Support for freemius @since WP Travel 4.4.0
			// $host         = get_option( $premium_addon['_option_prefix'] . 'host' );

			$license_link = '';
			$account_link = '';
			$host = 'tp';
			$plugin_prefix = $filtered_key . '_fs';
			if ( function_exists( $plugin_prefix ) ) {
				$host = 'freemius';

				$status = ''; // need empty because It may be valid/active in TP license.
				$license_link = admin_url( 'edit.php?post_type=itinerary-booking&page=' . $key . '-license' );
				$account_link = admin_url( 'edit.php?post_type=itinerary-booking&page=' . $key . '-license-account' );
				if ( $plugin_prefix()->is_paying() ) {
					$status = 'valid';
				}
			} 
			// else {

			// 	if ( 'valid' == $status && '' != $license_key ) {
			// 		if ( ! $host ) { // if host is already saved then it will renderd from above option.
			// 			$host = 'tp';
			// 		}
			// 	} 
			// }

			$data = array(
				'license_data'   => $license_data,
				'license_key'    => $license_key,
				'status'         => $status,
				'item_name'      => $premium_addon['item_name'],
				'_option_prefix' => $filtered_key . '_',
				// Additional options for Freemius.
				'host'           => $host,
				'license_link'   => $license_link,
				'account_link'   => $account_link,
			);
			


			$premium_addons_keys[] = $filtered_key;
			$premium_addons_data[] = $data;

		endforeach;

		$settings['premium_addons_keys'] = $premium_addons_keys;
		$settings['premium_addons_data'] = $premium_addons_data;
		return $settings;
	}

	public static function settings_data_v4( $settings, $settings_data ) {
		$premium_addons = ! empty( $settings_data['premium_addons_data'] ) ? ( $settings_data['premium_addons_data'] ) : array();
		foreach ( $premium_addons as $key => $premium_addon ) :
			$settings[ $premium_addon['_option_prefix'] . 'license_key' ] = $premium_addon['license_key'];
		endforeach;
		return $settings;
	}

	public static function activate_license( $license ) {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}

		if ( ! isset( $license['_option_prefix'] ) ) {
			return;
		}

		$license_option_prefix = $license['_option_prefix'];

		// Delete old transient.
		delete_transient( $license_option_prefix . 'data' );

		$license_key = ( isset( $license[ $license_option_prefix . 'key' ] ) && '' !== $license[ $license_option_prefix . 'key' ] ) ? $license[ $license_option_prefix . 'key' ] : '';

		// data to send in our API request.
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_name'  => urlencode( $license['item_name'] ), // the name of our product in EDD.
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			self::$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			WP_Travel_Helpers_REST_API::response( $response );
		}

		// Decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Set license data trasient.
		set_transient( $license['_option_prefix'] . 'data', $license_data, 12 * HOUR_IN_SECONDS );

		// Set license status.
		update_option( $license['_option_prefix'] . 'status', $license_data->license );

		// return $license_data;
		if ( $license_data ) {
			return WP_Travel_Helpers_Response_Codes::get_success_response(
				'WP_TRAVEL_LICENSE_ACTIVATION',
				array(
					'license' => $license_data,
				)
			);
		}

	}

	/**
	 * Deactivate License.
	 *
	 * @return void
	 */
	public static function deactivate_license( $license ) {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}

		if ( ! isset( $license['_option_prefix'] ) ) {
			return;
		}

		$license_option_prefix = $license['_option_prefix'];

		$license_key = ( isset( $license[ $license_option_prefix . 'key' ] ) && '' !== $license[ $license_option_prefix . 'key' ] ) ? $license[ $license_option_prefix . 'key' ] : '';

		// data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_name'  => urlencode( $license['item_name'] ), // the name of our product in EDD.
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			self::$store_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay.
		if ( is_wp_error( $response ) ) {
			WP_Travel_Helpers_REST_API::response( $response );
		}

		// Decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Set license data trasient.
		delete_transient( $license['_option_prefix'] . 'data' );

		// Set license status.
		update_option( $license['_option_prefix'] . 'status', $license_data->license );

		// return $license_data;
		if ( $license_data ) {
			return WP_Travel_Helpers_Response_Codes::get_success_response(
				'WP_TRAVEL_LICENSE_ACTIVATION',
				array(
					'license' => $license_data,
				)
			);
		}

	}
}


function wp_travel_helpers_license_init() {
	WP_Travel_Helpers_License::init();
}
add_action( 'init', 'wp_travel_helpers_license_init', 11 );


