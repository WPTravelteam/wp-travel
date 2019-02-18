<?php

class WP_Travel_License {

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

	public function __construct() {

	}

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

		$premium_addons = apply_filters( 'wp_travel_premium_addons_list', array() );
		if ( count( $premium_addons ) > 0 ) {
			foreach ( $premium_addons as $key => $premium_addon ) {
				if ( is_array( $premium_addon ) ) {
					self::$addons[ $key ] = $premium_addon;
				}
			}
		}

		add_action( 'admin_init', 'WP_Travel_License::plugin_updater', 0 );
		add_action( 'wp_travel_license_tab_fields', 'WP_Travel_License::setting_fields' );
		add_filter( 'wp_travel_before_save_settings', 'WP_Travel_License::save_license' );

		add_action( 'admin_init', 'WP_Travel_License::activate_license' );
		add_action( 'admin_init', 'WP_Travel_License::deactivate_license' );
		add_action( 'admin_notices', 'WP_Travel_License::show_admin_notice' );
	}

	/**
	 * Updater Functions
	 *
	 * @return void
	 */
	public static function plugin_updater() {
		$settings             = get_option( 'wp_travel_settings' );
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons > 0 ) {
			foreach ( self::$addons as $key => $premium_addon ) {
				// retrieve the license from the database.
				$license_key = ( isset( $settings[ $premium_addon['_option_prefix'] . 'key' ] ) ) ? $settings[ $premium_addon['_option_prefix'] . 'key' ] : '';
				$args        = wp_parse_args(
					$premium_addon,
					array(
						'license' => $license_key,
						'author'  => 'WEN Solutions',
					)
				);
				unset( $args['_option_prefix'] );
				unset( $args['_file_path'] );

				// Setup the updater.
				$updater = new WP_Travel_Plugin_Updater( self::$store_url, $premium_addon['_file_path'], $args );
			}
		}

	}

	/**
	 * Add Fields To settings Page.
	 *
	 * @param Array $settings_args Settings fields args.
	 * @return void
	 */
	public static function setting_fields( $settings_args ) {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons > 0 ) {
			$premium_addons = self::$addons;
			$settings       = isset( $settings_args['settings'] ) ? $settings_args['settings'] : array();
			foreach ( $premium_addons as $key => $premium_addon ) :
				// Get license status.
				$status      = get_option( $premium_addon['_option_prefix'] . 'status' );
				$license_key = isset( $settings[ $premium_addon['_option_prefix'] . 'key' ] ) ? $settings[ $premium_addon['_option_prefix'] . 'key' ] : '';
				?>
		<style>
			#wp-travel-tab-content-license .form-table input[class*=button].button-license{height:35px;line-height:35px}
		</style>
		<div class="wp-travel-tab-product-single">
			<h4 class="wp-travel-tab-content-title wp-travel-tab-product-title"><?php echo esc_html( $premium_addon['item_name'] ); ?></h4>
			<table class="form-table">
				<tbody>
					<tr>
						<th><label for="<?php echo $key; ?>-license-key"><?php esc_html_e( 'License Key', 'wp-travel' ); ?></label></th>
						<td>
							<input type="text" value="<?php echo esc_attr( $license_key ); ?>" name="<?php echo $premium_addon['_option_prefix']; ?>key" id="<?php echo $key; ?>-license-key">
							<?php if ( $license_key ) : ?>
								<span class="license-status-warp" style="position:relative;" >
									<span style="position:absolute;top:0;right:10px">
									<?php if ( 'valid' === $status ) : ?>
										<span style="color:green;"><?php esc_html_e( 'Active', 'wp-travel' ); ?></span>
									<?php elseif ( 'invalid' === $status ) : ?>
										<span style="color:red;"><?php esc_html_e( 'Invalid', 'wp-travel' ); ?></span>
									<?php elseif ( 'expired' === $status ) : ?>
										<span style="color:red;"><?php esc_html_e( 'Expired', 'wp-travel' ); ?></span>
									<?php elseif ( 'inactive' === $status ) : ?>
										<span style="color:orange;"><?php esc_html_e( 'Inactive', 'wp-travel' ); ?></span>
									<?php endif; ?>
									</span>
								</span>
							<?php endif; ?>

							<?php if ( $license_key || 'valid' !== $status ) : ?>

								<?php wp_nonce_field( $premium_addon['_option_prefix'] . 'nonce', $premium_addon['_option_prefix'] . 'nonce' ); ?>

								<?php if ( false !== $status && 'valid' === $status ) { ?>
									<input type="submit" class="button button-secondary button-license" name="<?php echo $premium_addon['_option_prefix']; ?>deactivate" value="<?php esc_html_e( 'Deactivate License', 'wp-travel' ); ?>" />
								<?php } else { ?>
									<input type="submit" class="button button-primary button-license" name="<?php echo $premium_addon['_option_prefix']; ?>activate" value="<?php esc_html_e( 'Activate License', 'wp-travel' ); ?>" />
								<?php } ?>
								<input type="hidden" name="save_settings_button" value="true" />

							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

				<?php
			endforeach;
		}
		?>
		<div class="wp-travel-upsell-message">
			<div class="wp-travel-pro-feature-notice">
				<h4><?php esc_html_e( 'Want to add more features in WP Travel?', 'wp-travel' ); ?></h4>
				<p><?php esc_html_e( 'Get addon for payment, trip extras, Inventory management and other premium features.', 'wp-travel' ); ?></p>
				<a target="_blank" href="https://wptravel.io/downloads/">Get WP Travel Addons</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Update Settings Args value before save.
	 *
	 * @param Array $settings Settings value.
	 * @return void
	 */
	public static function save_license( $settings ) {
		if ( ! $settings ) {
			return;
		}
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return $settings;
		}
		$premium_addons = self::$addons;
		foreach ( $premium_addons as $key => $premium_addon ) {
			$key_option_name              = $premium_addon['_option_prefix'] . 'key';
			$license_key                  = ( isset( $_POST[ $key_option_name ] ) && '' !== $_POST[ $key_option_name ] ) ? $_POST[ $key_option_name ] : '';
			$settings[ $key_option_name ] = $license_key;
		}
		return $settings;
	}

	/**
	 * Activate License.
	 *
	 * @return void
	 */
	public static function activate_license() {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}
		$premium_addons = self::$addons;
		foreach ( $premium_addons as $key => $premium_addon ) {
			// listen for our activate button to be clicked.
			if ( isset( $_POST[ $premium_addon['_option_prefix'] . 'activate' ] ) ) {
				delete_transient( $premium_addon['_option_prefix'] . 'data' );
				// run a quick security check.
				if ( ! check_admin_referer( $premium_addon['_option_prefix'] . 'nonce', $premium_addon['_option_prefix'] . 'nonce' ) ) {
					return; // get out if we didn't click the Activate button.
				}

				// retrieve the license from the database.
				$license = ( isset( $_POST[ $premium_addon['_option_prefix'] . 'key' ] ) && '' !== $_POST[ $premium_addon['_option_prefix'] . 'key' ] ) ? $_POST[ $premium_addon['_option_prefix'] . 'key' ] : '';

				// data to send in our API request.
				$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $license,
					'item_name'  => urlencode( $premium_addon['item_name'] ), // the name of our product in EDD.
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
					error_log( print_r( $response, true ) );
					return false;
				}

				// Decode the license data.
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// Set license data trasient.
				set_transient( $premium_addon['_option_prefix'] . 'data', $license_data, 12 * HOUR_IN_SECONDS );

				// Set license status.
				update_option( $premium_addon['_option_prefix'] . 'status', $license_data->license );
			}
		}
	}

	/**
	 * Deactivate License.
	 *
	 * @return void
	 */
	public static function deactivate_license() {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}
		foreach ( self::$addons as $key => $premium_addon ) {
			// listen for our activate button to be clicked.
			if ( isset( $_POST[ $premium_addon['_option_prefix'] . 'deactivate' ] ) ) {

				// run a quick security check.
				if ( ! check_admin_referer( $premium_addon['_option_prefix'] . 'nonce', $premium_addon['_option_prefix'] . 'nonce' ) ) {
					return; // get out if we didn't click the Activate button.
				}
				$settings_args = get_option( 'wp_travel_settings' );

				// retrieve the license from the database.
				$license = isset( $settings_args[ $premium_addon['_option_prefix'] . 'key' ] ) ? trim( $settings_args[ $premium_addon['_option_prefix'] . 'key' ] ) : trim( $_POST[ $premium_addon['_option_prefix'] . 'key' ] );

				// data to send in our API request.
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $license,
					'item_name'  => urlencode( $premium_addon['item_name'] ), // the name of our product in EDD.
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
					return false;
				}

				// decode the license data.
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				delete_transient( $premium_addon['_option_prefix'] . 'data' );
				update_option( $premium_addon['_option_prefix'] . 'status', $license_data->license );

			}
		}
	}

	/**
	 * Check License Status.
	 *
	 * @return String
	 */
	public static function check_license( $addon ) {

		global $wp_version;

		$license_data = get_transient( $addon['_option_prefix'] . 'data' );
		if ( empty( $license_data ) ) {
			$settings_args = get_option( 'wp_travel_settings' );

			// retrieve the license from the database.
			$license = isset( $settings_args[ $addon['_option_prefix'] . 'key' ] ) ? trim( $settings_args[ $addon['_option_prefix'] . 'key' ] ) : '';

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( $addon['item_name'] ),
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

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( $license ) {
				set_transient( $addon['_option_prefix'] . 'data', $license_data, 12 * HOUR_IN_SECONDS );
				update_option( $addon['_option_prefix'] . 'status', $license_data->license );
			} else {
				delete_transient( $addon['_option_prefix'] . 'data' );
				update_option( $addon['_option_prefix'] . 'status', '' );
			}
		}

		if ( isset( $license_data->license ) ) {
			return $license_data->license;
		} else {
			return 'invalid';
		}
	}

	/**
	 * Show Notice to activate license.
	 *
	 * @return Mixed
	 */
	public static function show_admin_notice() {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}
		foreach ( self::$addons as $key => $premium_addon ) {
			$check_license = self::check_license( $premium_addon );
			if ( false !== $check_license && 'valid' === $check_license ) {
				return false;
			}
			$class   = 'notice notice-error';
			$link    = admin_url( 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-license' );
			$message = sprintf( __( 'You have not activated the license for %1$s Addon Go to <a href="%2$s"> settings </a> to activate your license.', 'wp-travel' ), $premium_addon['item_name'], $link );

			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}
	}

}

function wp_travel_license_init() {
	WP_Travel_License::init();
}
add_action( 'init', 'wp_travel_license_init', 11 );
