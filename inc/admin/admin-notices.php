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


// Black Friday Notices.
function wp_travel_black_friday_notice() {

	$user_id = get_current_user_id();

	if ( ! get_option( 'wp_travel_black_friday_2019_' . $user_id, false ) ) {
		?>
			<div class="updated notice wp-travel-notice-black-friday is-dismissible" data-notice="wp-travel-black-friday">
				<p><?php _e( sprintf( 'WP Travel offers Thanksgiving, Black Friday and Cyber Monday Deals on WP Travel bundles and extensions offering <b>25&#37; off</b>. Coupon Code: bftgcm <a href="%s" target="_blank"> Shop now!</a> <br><b>Offer valid</b>: 22nd Nov – 2nd Dec 2019.', esc_url( 'wp-travel' ) ), 'wp-travel' ); ?></p>

			</div>
		<?php
	}
}
add_action( 'admin_notices', 'wp_travel_black_friday_notice' );
add_action( 'wp_ajax_wp_travel_black_friday_dismiss', 'wp_travel_black_friday_dismiss_notice_ajax' );


function wp_travel_black_friday_dismiss_notice_ajax() {
	$user_id = get_current_user_id();
	$key = 'wp_travel_black_friday_2019_' . $user_id;
	update_option( $key, true );
}
