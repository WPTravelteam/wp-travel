<?php
/**
 * Admin Tablenav
 *
 * @package inc/admin/
 */


 /**
  * Display Upsell notice in table nav.
  *
  * @param string $which Which section to display.
  */
function wp_travel_tablenav( $which ) {
	if ( ! $which ) {
		return;
	}
	if ( ! class_exists( 'WP_Travel_Import_Export_Core' ) ) {
		if ( 'top' === $which ) {
			$allowed_screen = array(
				'edit-itineraries',
				'edit-itinerary-booking',
				'edit-wp-travel-coupons',
				'edit-itinerary-enquiries',
				'edit-tour-extras',
			);
			$screen = get_current_screen();
			$screen_id = $screen->id;
			if ( ! in_array( $screen_id, $allowed_screen ) ) {
				return;
			}
			?>			
			<a href="https://wptravel.io/downloads/wp-travel-import-export/" class="wp-travel-tablenav" target="_blank" >
				<?php esc_html_e( 'Import or Export CSV', 'wp-travel' ); ?>
				<span ><?php esc_html_e( 'Get Pro', 'wp-travel' ); ?></span>
			</a>
			<?php
		}
	}
}

add_action( 'manage_posts_extra_tablenav', 'wp_travel_tablenav' );
