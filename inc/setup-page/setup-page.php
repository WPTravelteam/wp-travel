<?php
/**
 * @author Wp-Travel
 * @since 6.1.0
 */
class WP_Travel_Setup_Page {



	public function __construct() {

		// require_once( ABSPATH . 'wp-admin/includes/ajax-actions.php' );
		require_once ABSPATH . 'wp-includes/pluggable.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/theme.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';

		include sprintf( '%s/inc/setup-page/trip-import-class.php', WP_TRAVEL_ABSPATH );

		add_action( 'admin_menu', array( $this, 'wp_travel_welcome_screen_pages' ) );
		add_action( 'admin_init', array( $this, 'wp_travel_setup_page_redirect' ) );
		add_action( 'admin_head', array( $this, 'wp_travel_welcome_screen_remove_menus' ) );
		add_action( 'rest_api_init', array( $this, 'add_custom_users_api' ) );
	}


	function add_custom_users_api() {

		register_rest_route(
			'wp-travel/v1',
			'/trip-import',
			array(
				'methods'             => 'POST',
				'permission_callback' => function ($request) {
						if (current_user_can('edit_others_posts'))
						return true;
					},
				'callback'            => array( $this, 'wp_travel_import_trip' ),
			)
		);
	}

	function wp_travel_import_trip() {

		$insert_trip = new WP_Travel_Import_Dummy_Trip();

		if ( is_wp_error( $insert_trip ) ) {
			return __( 'Error During Importing', 'wp-travel' );
		} else {

			$theme_slug = 'wp-travel-fse';

			$api = themes_api(
				'theme_information',
				array(
					'slug'   => $theme_slug,
					'fields' => array( 'sections' => false ),
				)
			);

			$skin     = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Theme_Upgrader( $skin );
			$result   = $upgrader->install( $api->download_link );

			if ( is_wp_error( $result ) ) {
				$status['errorCode']    = $result->get_error_code();
				$status['errorMessage'] = $result->get_error_message();
				wp_send_json_error( $status );
			} elseif ( is_wp_error( $skin->result ) ) {

				$plugin_slug = 'wp-travel-blocks';
				$plugin      = $plugin_slug . '/wp-travel-blocks.php';
				$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;

				$status = array(
					'install' => 'plugin',
					'slug'    => $plugin_slug,
				);
				if ( ! file_exists( $plugin_file ) ) {
					if ( ! current_user_can( 'install_plugins' ) ) {
						$status['errorMessage'] = __( 'Sorry, you are not allowed to install plugins on this site.', 'wp-travel' );
						return $status;
					}

					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

					$api = plugins_api(
						'plugin_information',
						array(
							'slug'   => $plugin_slug,
							'fields' => array(
								'sections' => false,
							),
						)
					);

					if ( is_wp_error( $api ) ) {
						$status['errorMessage'] = $api->get_error_message();
						return ( $status );
					}

					$status['pluginName'] = $api->name;

					$skin     = new WP_Ajax_Upgrader_Skin();
					$upgrader = new Plugin_Upgrader( $skin );
					$result   = $upgrader->install( $api->download_link );

					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						$status['debug'] = $skin->get_upgrade_messages();
					}

					if ( is_wp_error( $result ) ) {
						$status['errorCode']    = $result->get_error_code();
						$status['errorMessage'] = $result->get_error_message();
						return ( $status );
					} elseif ( is_wp_error( $skin->result ) ) {
						$status['errorCode']    = $skin->result->get_error_code();
						$status['errorMessage'] = $skin->result->get_error_message();
						return ( $status );
					} elseif ( $skin->get_errors()->has_errors() ) {
						$status['errorMessage'] = $skin->get_error_messages();
						return ( $status );
					} elseif ( is_null( $result ) ) {
						global $wp_filesystem;

						$status['errorCode']    = 'unable_to_connect_to_filesystem';
						$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'wp-travel' );

						// Pass through the error from WP_Filesystem if one was raised.
						if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
							$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
						}

						return ( $status );
					}

					$install_status = install_plugin_install_status( $api );
					$pagenow        = isset( $_POST['pagenow'] ) ? sanitize_key( $_POST['pagenow'] ) : ''; // @phpcs:ignore

					// If installation request is coming from import page, do not return network activation link.
					$plugins_url = ( 'import' === $pagenow ) ? admin_url( 'plugins.php' ) : network_admin_url( 'plugins.php' );

					if ( current_user_can( 'activate_plugin', $install_status['file'] ) && is_plugin_inactive( $install_status['file'] ) ) {
						$status['activateUrl'] = add_query_arg(
							array(
								'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $install_status['file'] ),
								'action'   => 'activate',
								'plugin'   => $install_status['file'],
							),
							$plugins_url
						);
					}
					if ( is_multisite() && current_user_can( 'manage_network_plugins' ) && 'import' !== $pagenow ) {
						$status['activateUrl'] = add_query_arg( array( 'networkwide' => 1 ), $status['activateUrl'] );
					}
				}

				if ( file_exists( $plugin_file ) && ! is_plugin_active( $plugin_file ) ) {
					activate_plugin( $plugin_file );
				}

				switch_theme( $theme_slug );
				return __( 'Complete Setup', 'wp-travel' );
			} elseif ( $skin->get_errors()->has_errors() ) {
				$status['errorMessage'] = $skin->get_error_messages();
				wp_send_json_error( $status );
			} elseif ( is_null( $result ) ) {
				global $wp_filesystem;

				$status['errorCode']    = 'unable_to_connect_to_filesystem';
				$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'wp-travel' );

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
					$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				wp_send_json_error( $status );
			} else {
				$plugin_slug = 'wp-travel-blocks';
				$plugin      = $plugin_slug . '/wp-travel-blocks.php';
				$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;

				$status = array(
					'install' => 'plugin',
					'slug'    => $plugin_slug,
				);
				if ( ! file_exists( $plugin_file ) ) {
					if ( ! current_user_can( 'install_plugins' ) ) {
						$status['errorMessage'] = __( 'Sorry, you are not allowed to install plugins on this site.', 'wp-travel' );
						return $status;
					}

					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

					$api = plugins_api(
						'plugin_information',
						array(
							'slug'   => $plugin_slug,
							'fields' => array(
								'sections' => false,
							),
						)
					);

					if ( is_wp_error( $api ) ) {
						$status['errorMessage'] = $api->get_error_message();
						return ( $status );
					}

					$status['pluginName'] = $api->name;

					$skin     = new WP_Ajax_Upgrader_Skin();
					$upgrader = new Plugin_Upgrader( $skin );
					$result   = $upgrader->install( $api->download_link );

					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						$status['debug'] = $skin->get_upgrade_messages();
					}

					if ( is_wp_error( $result ) ) {
						$status['errorCode']    = $result->get_error_code();
						$status['errorMessage'] = $result->get_error_message();
						return ( $status );
					} elseif ( is_wp_error( $skin->result ) ) {
						$status['errorCode']    = $skin->result->get_error_code();
						$status['errorMessage'] = $skin->result->get_error_message();
						return ( $status );
					} elseif ( $skin->get_errors()->has_errors() ) {
						$status['errorMessage'] = $skin->get_error_messages();
						return ( $status );
					} elseif ( is_null( $result ) ) {
						global $wp_filesystem;

						$status['errorCode']    = 'unable_to_connect_to_filesystem';
						$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'wp-travel' );

						// Pass through the error from WP_Filesystem if one was raised.
						if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
							$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
						}

						return ( $status );
					}

					$install_status = install_plugin_install_status( $api );
					$pagenow        = isset( $_POST['pagenow'] ) ? sanitize_key( $_POST['pagenow'] ) : ''; // @phpcs:ignore

					// If installation request is coming from import page, do not return network activation link.
					$plugins_url = ( 'import' === $pagenow ) ? admin_url( 'plugins.php' ) : network_admin_url( 'plugins.php' );

					if ( current_user_can( 'activate_plugin', $install_status['file'] ) && is_plugin_inactive( $install_status['file'] ) ) {
						$status['activateUrl'] = add_query_arg(
							array(
								'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $install_status['file'] ),
								'action'   => 'activate',
								'plugin'   => $install_status['file'],
							),
							$plugins_url
						);
					}
					if ( is_multisite() && current_user_can( 'manage_network_plugins' ) && 'import' !== $pagenow ) {
						$status['activateUrl'] = add_query_arg( array( 'networkwide' => 1 ), $status['activateUrl'] );
					}
				}

				if ( file_exists( $plugin_file ) && ! is_plugin_active( $plugin_file ) ) {
					activate_plugin( $plugin_file );
				}

				switch_theme( $theme_slug );
				return __( 'Complete Setup', 'wp-travel' );
			}
			
		}

	}

	function wp_travel_setup_page_redirect() {

		// Make sure it's the correct user
		if ( intval( get_option( 'wp_travel_setup_page_redirect', false ) ) === wp_get_current_user()->ID ) {
			if ( get_option( 'wp_travel_first_active' ) == '1' ) {
				return;
			} else {

				// Make sure we don't redirect again after this one
				delete_option( 'wp_travel_setup_page_redirect' );
				add_option( 'wp_travel_first_active', true );
				wp_safe_redirect( add_query_arg( array( 'page' => 'wp-travel-setup-page' ), admin_url( 'index.php' ) ) );
			}
		}

	}

	function wp_travel_welcome_screen_pages() {
		add_dashboard_page(
			'WP Travel Setup Page',
			'WP Travel Setup Page',
			'read',
			'wp-travel-setup-page',
			array( $this, 'wp_travel_setup_page' )
		);
	}

	function wp_travel_setup_page() {
		?>
		<?php if ( wp_script_is( 'wp-travel-admin-script' ) ) : ?>
			  <div id="wp_travel_setup_page">
		
			 </div>
			<?php else : ?>
			  <p><?php echo __( 'Scripts is not loaded..', 'wp-travel' ); ?></p>
		<?php endif ?>
	  
		<?php
	}

	function wp_travel_welcome_screen_remove_menus() {
		remove_submenu_page( 'index.php', 'wp-travel-setup-page' );
	}
}

new WP_Travel_Setup_Page();
