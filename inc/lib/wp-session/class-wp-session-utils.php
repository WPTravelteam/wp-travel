<?php
/**
 * Utility class for sesion utilities
 *
 * THIS CLASS SHOULD NEVER BE INSTANTIATED
 *
 * @phpcs:disable
 *
 * @package WP_Travel
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility class for sesion utilities
 *
 * THIS CLASS SHOULD NEVER BE INSTANTIATED
 */
class WP_Session_Utils {
	/**
	 * Count the total sessions in the database.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int
	 */
	public static function count_sessions() {
		global $wpdb;

		$sessions = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_wp_session_expires_%'" ) );

		return absint( $sessions );
	}

	/**
	 * Create a new, random session in the database.
	 *
	 * @param null|string $date
	 */
	public static function create_dummy_session( $date = null ) {
		
		// Generate our date
		if ( null !== $date ) {
			$time = strtotime( $date );

			if ( false === $time ) {
				$date = null;
			} else {
				$expires = gmdate( 'U', strtotime( $date ) );
			}
		}

		// If null was passed, or if the string parsing failed, fall back on a default
		if ( null === $date ) {
			/**
			 * Filter the expiration of the session in the database
			 *
			 * @param int
			 */
			$expires = time() + (int) apply_filters( 'wp_session_expiration', 30 * 60 );
		}

		$session_id = self::generate_id();

		// Store the session
		add_option( "_wp_session_{$session_id}", array(), '', 'no' );
		add_option( "_wp_session_expires_{$session_id}", $expires, '', 'no' );
	}

	/**
	 * Delete old sessions from the database.
	 *
	 * @param int $limit Maximum number of sessions to delete.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int Sessions deleted.
	 */
	public static function delete_old_sessions( $limit = 1000 ) {
		global $wpdb;

		// $limit = absint( $limit );
		// $keys  = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '_wp_session_expires_%' ORDER BY option_value ASC LIMIT 0, {$limit}" );

		// $now     = time();
		// $expired = array();
		// $count   = 0;

		// foreach ( $keys as $expiration ) {
		// 	$key     = $expiration->option_name;
		// 	$expires = $expiration->option_value;

		// 	if ( $now > $expires ) {
		// 		$session_id = addslashes( substr( $key, 20 ) );

		// 		$expired[] = $key;
		// 		$expired[] = "_wp_session_{$session_id}";

		// 		$count += 1;
		// 	}
		// }

		// // Delete expired sessions
		// if ( ! empty( $expired ) ) {
		// 	$names = implode( "','", $expired );
		// 	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name IN ('{$names}')" );
		// }

		// return $count;

		$count = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_wp_session_%'" ) );

		return (int) ( $count / 2 );
	}

	/**
	 * Remove all sessions from the database, regardless of expiration.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int Sessions deleted
	 */
	public static function delete_all_sessions() {
		global $wpdb;

		$count = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_wp_session_%'" ) );

		return (int) ( $count / 2 );
	}

	/**
	 * Generate a new, random session ID.
	 *
	 * @return string
	 */
	public static function generate_id() {
		require_once ABSPATH . 'wp-includes/class-phpass.php';
		$hash = new PasswordHash( 8, false );

		return md5( $hash->get_random_bytes( 32 ) );
	}
}
