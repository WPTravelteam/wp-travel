<?php
class WPTravel_Helpers_Modules {

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
		$premium_addons = apply_filters( 'wp_travel_premium_addons_list', array() );
		if ( count( $premium_addons ) > 0 ) {
			foreach ( $premium_addons as $key => $premium_addon ) {
				if ( is_array( $premium_addon ) ) {
					self::$addons[ $key ] = $premium_addon;
				}
			}
		}

		// Licesnse data for
		add_filter( 'wp_travel_settings_values', 'WPTravel_Helpers_Modules::settings_data' );
		add_filter( 'wp_travel_block_before_save_settings', 'WPTravel_Helpers_Modules::settings_data_v4', 10, 2 );
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

			$license_link  = '';
			$account_link  = '';
			$host          = 'tp';
			$plugin_prefix = $filtered_key . '_fs';
			if ( function_exists( $plugin_prefix ) ) {
				$host = 'freemius';

				$status       = ''; // need empty because It may be valid/active in TP license.
				$license_link = admin_url( 'edit.php?post_type=itinerary-booking&page=' . $key . '-license' );
				$account_link = admin_url( 'edit.php?post_type=itinerary-booking&page=' . $key . '-license-account' );
				if ( $plugin_prefix()->is_paying() ) {
					$status = 'valid';
				}
			}

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
}

function wptravel_helpers_license_init() {
	WPTravel_Helpers_Modules::init();
}
add_action( 'init', 'wptravel_helpers_license_init', 11 );


