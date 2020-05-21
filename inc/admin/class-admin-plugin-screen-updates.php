<?php
/**
 * Admin Update Notices.
 *
 * @package inc/admin/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class WP_Travel_Admin_Plugin_Screen_Updates {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'in_plugin_update_message-wp-travel/wp-travel.php', array( $this, 'wp_travel_in_plugin_update_message' ), 10, 2 );
	}

	public function wp_travel_in_plugin_update_message( $args, $response ) {
		$version = $response->new_version;

		$transient_name = 'wt_upgrade_notice_' . $version;
		$upgrade_notice = get_transient( $transient_name );
		if ( false === $upgrade_notice ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/wp-travel/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = $this->parse_update_notice( $response['body'], $version );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}
		echo wp_kses_post( $upgrade_notice );

	}

	public function parse_update_notice( $content, $new_version ) {

		$version_parts     = explode( '.', $new_version );
		$check_for_notices = array(
			$version_parts[0] . '.0', // Major.
			$version_parts[0] . '.0.0', // Major.
			$version_parts[0] . '.' . $version_parts[1], // Minor.
			$version_parts[0] . '.' . $version_parts[1] . '.' . $version_parts[2], // Patch.
		);
		$notice_regexp     = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $new_version ) . '\s*=|$)~Uis';
		$upgrade_notice    = '';
		foreach ( $check_for_notices as $check_version ) {
			if ( version_compare( WP_TRAVEL_VERSION, $check_version, '>' ) ) {
				continue;
			}

			$matches = null;
			if ( preg_match( $notice_regexp, $content, $matches ) ) {
				$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );
				if ( version_compare( trim( $matches[1] ), $check_version, '=' ) ) {
					$upgrade_notice .= '<br/><br/><span class="wp_travel_plugin_upgrade_notice"><strong>Note: </strong>';

					foreach ( $notices as $index => $line ) {
						$upgrade_notice .= preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
					}
					$upgrade_notice .= '<span>';
				}
				break;
			}
		}
		return $upgrade_notice;
	}
}
new WP_Travel_Admin_Plugin_Screen_Updates();
