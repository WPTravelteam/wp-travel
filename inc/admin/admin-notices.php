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
			<h3><?php echo esc_html( 'WP Travel Critical Notifications' ); ?></h3>
		</div>
		<div class="notification-content">
			<ul>
				<?php do_action( 'wp_travel_critical_admin_notice' ); ?>
			</ul>
		</div>
	</div>
	<?php

}

add_action( 'admin_notices', 'wp_travel_display_critical_admin_notices' );

 /**
  * Display General admin notices.
  */
  function wp_travel_display_general_admin_notices() {
	$show_notices = apply_filters( 'wp_travel_display_general_admin_notices', false );
	if ( ! $show_notices ) {
		return;
	}
	?>
	<div class="wp-travel-notification notification-warning notice notice-info is-dismissible"> 
		<div class="notification-title">
			<h3><?php echo esc_html( 'WP Travel Notifications' ); ?></h3>
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
