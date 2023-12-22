<?php
/**
 * WP Travel Data Migrator.
 *
 * @package WP_Travel
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * This Migration will migrate all the Global Faqs structure into new structured with custom index like faq id.
 */
if ( ! function_exists( 'wptravel_migrate_523' ) ) {
	/**
	 * Migrate the faqs.
	 */
	function wptravel_migrate_523() {
		if ( 'yes' === get_option( 'wptravel_migrate_523', 'no' ) ) {
			return;
		}

		$settings  = wptravel_get_settings();
		$questions = isset( $settings['wp_travel_utils_global_faq_question'] ) ? $settings['wp_travel_utils_global_faq_question'] : array();
		$answers   = isset( $settings['wp_travel_utils_global_faq_answer'] ) ? $settings['wp_travel_utils_global_faq_answer'] : array();

		$global_faqs = array();
		$new_index   = time();
		if ( ! empty( $questions ) && ! empty( $answers ) ) {
			foreach ( $questions as $index => $question ) {
				$faq    = array(
					'question' => $question,
					'answer'   => $answers[ $index ],
				);
				$global_faqs[ $new_index ] = $faq;

				$new_index++;
			}
			$settings['global_faqs'] = $global_faqs;
			update_option( 'wp_travel_settings', $settings ); // update data.
			update_option( 'wptravel_migrate_523', 'yes' ); // migration flag.
		}
	}
	wptravel_migrate_523();
}