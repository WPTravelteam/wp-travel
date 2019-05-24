<?php
/**
 * Admin Notices.
 *
 * @package inc/admin/
 */

 /**
  * Display critical admin notices.
  */
function wp_travel_display_critical_admin_notices() {
	$show_notices = apply_filters( 'wp_travel_display_critical_admin_notices', false );
	if ( ! $show_notices ) {
		return;
	}
	?>
	<div class="wp-travel-notification notification-warning notice notice-error"> 
		<div class="notification-title">
			<h3><?php echo esc_html__( 'WP Travel Alert', 'wp-travel' ); ?></h3>
		</div>
		<div class="notification-content">
			<ul>
				<?php do_action( 'wp_travel_critical_admin_notice' ); ?>
			</ul>
		</div>
	</div>
	<?php

}
if ( ! is_multisite() ) {
	add_action( 'admin_notices', 'wp_travel_display_critical_admin_notices' );
	
} else {
	add_action( 'network_admin_notices', 'wp_travel_display_critical_admin_notices' );
	if ( is_main_site() ) {
		add_action( 'admin_notices', 'wp_travel_display_critical_admin_notices' );
	}
}

 /**
  * Display General admin notices.
  */
  function wp_travel_display_general_admin_notices() {
	$screen       = get_current_screen();
	$screen_id    = $screen->id;
	$notice_pages = array(
		'itinerary-booking_page_settings',
		'itineraries_page_booking_chart', // may be not reqd
		'itinerary-booking_page_booking_chart',
		'edit-itinerary-booking',
		'edit-travel_keywords',
		'edit-travel_locations',
		'edit-itinerary_types',
		'edit-itineraries',
		'itineraries',
		'itinerary-booking',
		'edit-activity',
		'edit-wp-travel-coupons',
		'edit-itinerary-enquiries',
		'edit-tour-extras',
		'edit-wp_travel_downloads',
		'itinerary-booking_page_wp-travel-marketplace',
		'itinerary-booking_page_wp_travel_custom_filters_page',
	);
	$notice_pages = apply_filters( 'wp_travel_admin_general_notice_page_screen_ids', $notice_pages );
	if ( ! in_array( $screen_id, $notice_pages ) ) { // Only display general notice on WP Travel pages.
		return false;
	}

	$show_notices = apply_filters( 'wp_travel_display_general_admin_notices', false );
	if ( ! $show_notices ) {
		return;
	}
	?>
	<div class="wp-travel-notification notification-warning notice notice-info is-dismissible"> 
		<div class="notification-title">
			<h3><?php echo esc_html__( 'WP Travel Notifications', 'wp-travel' ); ?></h3>
		</div>
		<div class="notification-content">
			<ul>
				<?php do_action( 'wp_travel_general_admin_notice' ); ?>
			</ul>
		</div>
	</div>
	<?php

}

add_action( 'admin_notices', 'wp_travel_display_general_admin_notices' );
