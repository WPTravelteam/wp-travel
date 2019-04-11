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
			<style>
			a.wp-travel-tablenav{
				display: inline-block;
				padding: 3px;
				margin: 4px;
			}
			a.wp-travel-tablenav span{
				border: 1px solid green;
				font-size: 8px;
				padding: 1px 3px;
				border-radius: 2px;
				color: green;
				margin: 0px 0px 0px 6px;
				display: inline-block;
				line-height: 11px;
			}
			</style>
			<a href="https://wptravel.io/downloads/wp-travel-import-export/" class="wp-travel-tablenav" target="_blank" >
				<?php esc_html_e( 'Import or Export CSV' ); ?>
				<span ><?php esc_html_e( 'Get Pro' ); ?></span>
			</a>
			<?php
		}
	}
}

add_action( 'manage_posts_extra_tablenav', 'wp_travel_tablenav' );
