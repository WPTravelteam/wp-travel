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

		add_filter( 'wp_travel_display_critical_admin_notices', 'WP_Travel_License::display_critical_notices' );
		add_action( 'wp_travel_critical_admin_notice', 'WP_Travel_License::critical_admin_notices' );
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

			$grid_template_columns = '';
			if ( $count_premium_addons < 3 ) {
				$grid_template_columns = 'grid-template-columns: repeat(' . $count_premium_addons . ', 1fr)';
			}
			?>
			<div class="license_wrapper" style="<?php echo esc_attr( $grid_template_columns ); ?>">
			<?php
			$premium_addons = self::$addons;
			$settings       = isset( $settings_args['settings'] ) ? $settings_args['settings'] : array();
			foreach ( $premium_addons as $key => $premium_addon ) :
				// Get license status.
				$status      = get_option( $premium_addon['_option_prefix'] . 'status' );
				$license_key = isset( $settings[ $premium_addon['_option_prefix'] . 'key' ] ) ? $settings[ $premium_addon['_option_prefix'] . 'key' ] : '';
				$license_data = get_transient( $premium_addon['_option_prefix'] . 'data' );
				$status_message = '';
				$status_class = '';
				$expires_in = '';
				if ( $license_key ) :
					if ( 'valid' === $status ) :
						$status_message = __( 'License Active', 'wp-travel' );
						$status_class = 'fa-check';
					elseif ( 'invalid' === $status ) :
						$status_message = __( 'Invalid License', 'wp-travel' );
						$status_class = 'fa-times';
					elseif ( 'expired' === $status ) :
						$status_message = __( 'License Expired', 'wp-travel' );
						$status_class = 'fa-times';
					elseif ( 'inactive' === $status ) :
						$status_message = __( 'License Inactive', 'wp-travel' );
						$status_class = 'fa-times';
					endif;
				endif;

				if ( isset( $license_data->expires ) && 'lifetime' != $license_data->expires ) {
					$expires_in = $license_data->expires;
				}
				?>

				<div class="license_grid">
					<div class="form_field">
						<h3>
							<label for="<?php echo $key; ?>-license-key" class="control-label label_title">
								<?php echo esc_html( $premium_addon['item_name'] ); ?>
								<?php echo ( $status_message ) ? esc_html( sprintf( '[%s]', $status_message ) ) : ''; ?>
							</label>
						</h3>
						<div class="subject_input">
							<input type="text" value="<?php echo esc_attr( $license_key ); ?>" name="<?php echo $premium_addon['_option_prefix']; ?>key" id="<?php echo $key; ?>-license-key" placeholder="<?php _e( 'Enter license key', 'wp-travel' ); ?>">
							<?php if ( $license_key || 'valid' !== $status ) : ?>

								<?php wp_nonce_field( $premium_addon['_option_prefix'] . 'nonce', $premium_addon['_option_prefix'] . 'nonce' ); ?>

								<?php if ( false !== $status && 'valid' === $status ) { ?>
									<input type="submit" class="button button-secondary button-license" name="<?php echo $premium_addon['_option_prefix']; ?>deactivate" value="<?php esc_html_e( 'Deactivate License', 'wp-travel' ); ?>" />
								<?php } else { ?>
									<input type="submit" class="button button-primary button-license" name="<?php echo $premium_addon['_option_prefix']; ?>activate" value="<?php esc_html_e( 'Activate License', 'wp-travel' ); ?>" />
								<?php } ?>
								<input type="hidden" name="save_settings_button" value="true" />

							<?php endif; ?>
							<?php if ( $status_class ) : ?>
								<i class="fas <?php echo esc_html( $status_class ); ?>"></i>
								<span><?php echo $status_message; ?>
							<?php endif; ?>
							<?php if ( $expires_in  ) :
								$date_format = get_option('date_format');
								?>
								<br>
								<span class="expire-in"><?php esc_html_e( 'Expires in', 'wp-travel' ); ?><strong><?php echo date( $date_format, strtotime( $expires_in ) ); ?></strong></span>
							<?php endif; ?>
							<p class="description"><?php printf( __( 'Enter license key for %s here.', 'wp-travel'), $premium_addon['item_name']); ?></p>
						</div>
					</div>
				</div>

				<?php
			endforeach;
			?>
			</div>
			<?php
		}
		$args = array(
			'title' => __( 'Want to add more features in WP Travel?', 'wp-travel' ),
			'content' => __( 'Get addon for payment, trip extras, Inventory management and other premium features.', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
            'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2' => 'https://wptravel.io/downloads/',
			'link2_label' => __( 'Get WP Travel Addons', 'wp-travel' ),
		);

		if ( class_exists( 'WP_Travel_Pro' ) ) {
			$args['link'] = $args['link2'];
			$args['link_label'] = $args['link2_label'];
			unset( $args['link2'], $args['link2_label'] );
		}
		wp_travel_upsell_message( $args );
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

		if ( is_multisite() ) {
			$main_site_id = defined( 'SITE_ID_CURRENT_SITE' ) ? SITE_ID_CURRENT_SITE : 1;
			switch_to_blog( $main_site_id );
		}
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
		if ( is_multisite() ) {
			restore_current_blog();
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
	public static function critical_admin_notices() {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}
		foreach ( self::$addons as $key => $premium_addon ) {
			$check_license = self::check_license( $premium_addon );
			if ( false !== $check_license && 'valid' === $check_license ) {
				continue;
			}
			$screen = get_current_screen();
			$class   = '';
			$link    = admin_url( 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-license' );
			$message = sprintf( __( 'You have not activated the license for %1$s addon.', 'wp-travel' ), $premium_addon['item_name'] );

			if ( ! is_multisite() || ( is_multisite() && 'toplevel_page_wp_travel_network_settings-network' != $screen->id ) ) {

				if ( is_multisite() && 'toplevel_page_wp_travel_network_settings-network' != $screen->id ) {
					$link    = admin_url( 'network/admin.php?page=wp_travel_network_settings#wp-travel-tab-content-license' );
				}
				$message = rtrim( $message, '.' ) .  sprintf( __( ', go to <a href="%1$s"> settings </a> to activate your license.', 'wp-travel' ), $link );
			}

			printf( '<li class="%1$s"><p>%2$s</p></li>', $class, $message );
		}
	}

	public static function display_critical_notices( $display ) {
		$count_premium_addons = self::count_premium_addons();
		if ( $count_premium_addons < 1 ) {
			return;
		}
		foreach ( self::$addons as $key => $premium_addon ) {
			$check_license = self::check_license( $premium_addon );
			if ( false !== $check_license && 'valid' === $check_license ) {
				continue;
			}
			$display = true;
			break;
		}
		return $display;
	}

}

function wp_travel_license_init() {
	WP_Travel_License::init();
}
add_action( 'init', 'wp_travel_license_init', 11 );
