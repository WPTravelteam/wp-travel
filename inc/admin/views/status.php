<?php
global $wpdb;

$common = new WT_Admin_status();
wp_enqueue_style( 'wp-travel-system-status', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/css/system-status.css' );
wp_enqueue_script( 'wp-travel-system-status-script', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/system-status.js' );
?>
<div class="wp-travel-system">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'System Information', 'wp-travel' ); ?> </h1>

	<table class="" cellspacing="0" id="data-d">

		<tr>
			<td colspan="2" ><h2><?php _e( 'WordPress Information', 'wp-travel' ); ?></h2></td>
		</tr>
		<tr>
			<td width="25%"><?php _e( 'Home URL', 'wp-travel' ); ?>:</td>
			<td><?php form_option( 'home' ); ?></td>
		</tr>
		<tr>
			<td ><?php _e( 'Site URL', 'wp-travel' ); ?>:</td>
			<td><?php form_option( 'siteurl' ); ?></td>
		</tr>
		<tr>
			<td ><?php _e( 'WP Version', 'wp-travel' ); ?>:</td>
			<td><?php bloginfo( 'version' ); ?></td>
		</tr>
		<tr>
			<td ><?php _e( 'WP Multisite', 'wp-travel' ); ?>:</td>
			<td>
			<?php
			if ( is_multisite() ) {
				echo '<span class="dashicons dashicons-yes"></span>';
			} else {
				echo '&ndash;';
			}
			?>
			</td>
		</tr>
		<tr>
			<td ><?php _e( 'WP Memory Limit', 'wp-travel' ); ?>:</td>

			<td>
			<?php
				$memory = $common->memory_size_convert( WP_MEMORY_LIMIT );


			if ( function_exists( 'memory_get_usage' ) ) {
				$system_memory = $common->memory_size_convert( @ini_get( 'memory_limit' ) );
				$memory        = max( $memory, $system_memory );
			}



			if ( $memory < 67108864 ) {
				echo '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - For better performance, we recommend setting memory to at least 64MB. See: %2$s', 'wp-travel' ), size_format( $memory ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __( 'Increasing memory allocated to PHP', 'wp-travel' ) . '</a>' ) . '</span>';
			} else {
				echo '<span class="ok">' . size_format( $memory ) . ' </span>';
			}
			?>
				</td>
		</tr>
		<tr>
			<td ><?php _e( 'WP Debug Mode', 'wp-travel' ); ?>:</td>

			<td>
				<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
					<span class="dashicons dashicons-yes"></span> Yes
				<?php else : ?>
					<span class="no"> <span class="dashicons dashicons-no"></span> No </span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td  ><?php _e( 'WP Cron', 'wp-travel' ); ?>:</td>

			<td>
				<?php if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) : ?>
					<span class="no"> <span class="dashicons dashicons-no"></span> No </span>
				<?php else : ?>
					<span class="yes"><span class="dashicons dashicons-yes"></span> Yes</span>
				<?php endif; ?>
			</td>
		</tr>

		<tr>
			<td  ><?php _e( 'Language', 'wp-travel' ); ?>:</td>

			<td><?php echo get_locale(); ?></td>
		</tr>

		<tr>
			<td  ><?php _e( 'Upload Directory  Location', 'wp-travel' ); ?>:</td>

			<td>
			<?php
				$upload_dir = wp_upload_dir();
				echo $upload_dir['baseurl'];
			?>
				</td>
		</tr>

		<tr>
			<td colspan="2"  ><h2><?php _e( 'Server Information', 'wp-travel' ); ?></h2></td>
		</tr>

		<tr>
			<td width="25%" ><?php _e( 'Server Info', 'wp-travel' ); ?>:</td>

			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td  ><?php _e( 'PHP Version', 'wp-travel' ); ?>:</td>

			<td>
			<?php
				// Check if phpversion function exists.
			if ( function_exists( 'phpversion' ) ) {
				$php_version = phpversion();

				if ( version_compare( $php_version, '5.6', '<' ) ) {
					echo '<span class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - Recommend  PHP version of 5.6. See: %2$s', 'wp-travel' ), esc_html( $php_version ), '<a href="#" target="_blank">' . __( 'How to update your PHP version', 'wp-travel' ) . '</a>' ) . '</span>';
				} else {
					echo '<span class="yes">' . esc_html( $php_version ) . '</span>';
				}
			} else {
				_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'wp-travel' );
			}
			?>
				</td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td ><?php _e( 'PHP Post Max Size', 'wp-travel' ); ?>:</td>

				<td><?php echo size_format( $common->memory_size_convert( ini_get( 'post_max_size' ) ) ); ?></td>
			</tr>
			<tr>
				<td  ><?php _e( 'PHP Time Limit', 'wp-travel' ); ?>:</td>

				<td><?php echo ini_get( 'max_execution_time' ); ?></td>
			</tr>
			<tr>
				<td  ><?php _e( 'PHP Max Input Vars', 'wp-travel' ); ?>:</td>

				<td><?php echo ini_get( 'max_input_vars' ); ?></td>
			</tr>
			<tr>
				<td  ><?php _e( 'cURL Version', 'wp-travel' ); ?>:</td>

				<td>
				<?php
				if ( function_exists( 'curl_version' ) ) {
					$curl_version = curl_version();
					echo $curl_version['version'] . ', ' . $curl_version['ssl_version'];
				} else {
					_e( 'N/A', 'wp-travel' );
				}
				?>
					</td>
			</tr>
			<tr>
				<td  ><?php _e( 'SUHOSIN Installed', 'wp-travel' ); ?>:</td>

				<td><?php echo extension_loaded( 'suhosin' ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
			</tr>
			<?php
		endif;

		if ( $wpdb->use_mysqli ) {
			$ver = mysqli_get_server_info( $wpdb->dbh );
		} else {
			$ver = mysql_get_server_info();
		}

		if ( ! empty( $wpdb->is_mysql ) && ! stristr( $ver, 'MariaDB' ) ) :
			?>
			<tr>
				<td  ><?php _e( 'MySQL Version', 'wp-travel' ); ?>:</td>

				<td>
					<?php
					$mysql_version = $wpdb->db_version();

					if ( version_compare( $mysql_version, '5.6', '<' ) ) {
						echo '<span class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%1$s - We recommend a minimum MySQL version of 5.6. See: %2$s', 'wp-travel' ), esc_html( $mysql_version ), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . __( 'WordPress Requirements', 'wp-travel' ) . '</a>' ) . '</span>';
					} else {
						echo '<span class="yes">' . esc_html( $mysql_version ) . '</span>';
					}
					?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td  ><?php _e( 'Max Upload Size', 'wp-travel' ); ?>:</td>

			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<tr>
			<td  ><?php _e( 'Default Timezone is UTC', 'wp-travel' ); ?>:</td>

			<td>
			<?php
				$default_timezone = date_default_timezone_get();
			if ( 'UTC' !== $default_timezone ) {
				echo '<span class="error"><span class="dashicons dashicons-warning"></span>No' . sprintf( __( 'Default timezone is %s - it should be UTC', 'wp-travel' ), $default_timezone ) . '</span>';
			} else {
				echo '<span class="yes"><span class="dashicons dashicons-yes"></span>Yes</span>';
			}
			?>
			</td>
		</tr>
		<tr>
			<td  ><?php _e( 'PHP Error Log File Location', 'wp-travel' ); ?>:</td>

			<td>
			<?php
				echo ini_get( 'error_log' );
			?>
				</td>
		</tr>


		<?php
		$fields = array();

		// fsockopen/cURL.
		$fields['fsockopen_curl']['name'] = 'fsockopen/cURL';


		if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
			$fields['fsockopen_curl']['success'] = true;
		} else {
			$fields['fsockopen_curl']['success'] = false;
		}

		// SOAP.
		$fields['soap_client']['name'] = 'SoapClient';


		if ( class_exists( 'SoapClient' ) ) {
			$fields['soap_client']['success'] = true;
		} else {
			$fields['soap_client']['success'] = false;
			$fields['soap_client']['note']    = sprintf( __( 'Your server does not have the %s class enabled - some gateway plugins which use SOAP may not work as expected.', 'wp-travel' ), '<a href="https://php.net/manual/en/class.soapclient.php">SoapClient</a>' );
		}

		// DOMDocument.
		$fields['dom_document']['name'] = 'DOMDocument';


		if ( class_exists( 'DOMDocument' ) ) {
			$fields['dom_document']['success'] = true;
		} else {
			$fields['dom_document']['success'] = false;
			$fields['dom_document']['note']    = sprintf( __( 'Your server does not have the %s class enabled - HTML/Multipart emails, and also some extensions, will not work without DOMDocument.', 'wp-travel' ), '<a href="https://php.net/manual/en/class.domdocument.php">DOMDocument</a>' );
		}

		// GZIP.
		$fields['gzip']['name'] = 'GZip';


		if ( is_callable( 'gzopen' ) ) {
			$fields['gzip']['success'] = true;
		} else {
			$fields['gzip']['success'] = false;
			$fields['gzip']['note']    = sprintf( __( 'Your server does not support the %s function - this is required to use the GeoIP database from MaxMind.', 'wp-travel' ), '<a href="https://php.net/manual/en/zlib.installation.php">gzopen</a>' );
		}

		// Multibyte String.
		$fields['mbstring']['name'] = 'Multibyte String';


		if ( extension_loaded( 'mbstring' ) ) {
			$fields['mbstring']['success'] = true;
		} else {
			$fields['mbstring']['success'] = false;
			$fields['mbstring']['note']    = sprintf( __( 'Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'wp-travel' ), '<a href="https://php.net/manual/en/mbstring.installation.php">mbstring</a>' );
		}

		// Remote Get.
		$fields['remote_get']['name'] = 'Remote Get Status';

		$response      = wp_remote_get(
			'https://www.paypal.com/cgi-bin/webscr',
			array(
				'timeout'     => 60,
				'user-agent'  => 'wp-travel/' . 1.0,
				'httpversion' => '1.1',
				'body'        => array(
					'cmd' => '_notify-validate',
				),
			)
		);
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code == 200 ) {

			$fields['remote_get']['success'] = true;
		} else {
			$fields['remote_get']['success'] = false;
		}

		foreach ( $fields as $field ) {
			$mark = ! empty( $field['success'] ) ? 'yes' : 'error';
			?>
			<tr>
				<td data-export-label="<?php echo esc_html( $field['name'] ); ?>"><?php echo esc_html( $field['name'] ); ?>:</td>

				<td>
					<span class="<?php echo $mark; ?>">
						<?php echo ! empty( $field['success'] ) ? '<span class="dashicons dashicons-yes"></span>Yes' : '<span class="dashicons dashicons-no-alt"></span> No'; ?> <?php echo ! empty( $field['note'] ) ? wp_kses_data( $field['note'] ) : ''; ?>
					</span>
				</td>
			</tr>
			<?php
		}
		?>

		<tr>
			<td colspan="2" ><h2><?php _e( 'Current Theme', 'wp-travel' ); ?></h2></td>
		</tr>

		<?php
		require_once ABSPATH . 'wp-admin/includes/theme-install.php';

		$active_theme  = wp_get_theme();
		$theme_version = $active_theme->Version;
		?>

		<tr>
			<td width="25%"><?php _e( 'Name', 'wp-travel' ); ?>:</td>

			<td><?php echo esc_html( $active_theme->Name ); ?></td>
		</tr>
		<tr>
			<td  ><?php _e( 'Version', 'wp-travel' ); ?>:</td>

			<td>
			<?php
			echo esc_html( $theme_version );
			?>
			</td>
		</tr>
		<tr>
			<td  ><?php _e( 'Author URL', 'wp-travel' ); ?>:</td>

			<td><?php echo $active_theme->{'Author URI'}; ?></td>
		</tr>
		<tr>
			<td  ><?php _e( 'Child Theme', 'wp-travel' ); ?>:</td>

			<td>
				<?php
					echo is_child_theme() ? '<span class="yes"><span class="dashicons dashicons-yes"></span>Yes</span>' : '<span class="dashicons dashicons-no-alt"></span> No.  ' . sprintf( __( 'If you\'re want to modifying a theme, it safe to create a child theme.  See: <a href="%s" target="_blank">How to create a child theme</a>', 'wp-travel' ), 'https://codex.wordpress.org/Child_Themes' );
				?>
			</td>
		</tr>
		<?php
		if ( is_child_theme() ) :
			$parent_theme = wp_get_theme( $active_theme->Template );
			?>
			<tr>
				<td  ><?php _e( 'Parent Theme Name', 'wp-travel' ); ?>:</td>

				<td><?php echo esc_html( $parent_theme->Name ); ?></td>
			</tr>
			<tr>
				<td  ><?php _e( 'Parent Theme Version', 'wp-travel' ); ?>:</td>

				<td>
					<?php
					echo esc_html( $parent_theme->Version );

					// if (version_compare($parent_theme->Version, $update_theme_version, '<')) {
					// echo ' &ndash; <strong style="color:red;">' . sprintf(__('%s is available', 'wp-travel'), esc_html($update_theme_version)) . '</strong>';
					// }
					?>
				</td>
			</tr>
			<tr>
				<td  ><?php _e( 'Parent Theme Author URL', 'wp-travel' ); ?>:</td>

				<td><?php echo $parent_theme->{'Author URI'}; ?></td>
			</tr>
		<?php endif ?>


		<tr>
			<td colspan="2"  ><h2><?php _e( 'Active Plugins', 'wp-travel' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</h2></td>
		</tr>

		<?php
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		foreach ( $active_plugins as $plugin ) {

			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';
			$network_string = '';

			if ( ! empty( $plugin_data['Name'] ) ) {

				// Link the plugin name to the plugin url if available.
				$plugin_name = esc_html( $plugin_data['Name'] );

				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage', 'wp-travel' ) . '" target="_blank">' . $plugin_name . '</a>';
				}
				?>
					<tr>
						<td width="25%"><?php echo $plugin_name; ?></td>

						<td><?php echo sprintf( _x( 'by %s', 'by author', 'wp-travel' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
					</tr>
				<?php
			}
		}
		?>

	</table>

	<a href="JavaScript:void(0)" class="export-wsi-data button button-primary button-large"> <?php _e( 'Export to CSV', 'wp-travel' ); ?></a>
</div>
