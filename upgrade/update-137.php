<?php
/**
 * Update 1.3.7
 */

function wp_travel_maybe_create_new_roles() {

	$wp_travel_customer = get_role( 'wp-travel-customer' );

	if ( ! $wp_travel_customer || null == $wp_travel_customer ) {

		global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
			}

			// Customer role.
			add_role(
				'wp-travel-customer',
				__( 'WP Travel Customer', 'wp-travel' ),
				array(
					'read' => true,
				)
			);
	}

}

/**
 * Create new required pages.
 */
function wp_travel_maybe_create_pages() {

	include_once sprintf( '%s/inc/admin/admin-helper.php', WP_TRAVEL_ABSPATH );

	$get_dashboard_page = wp_travel_get_page_id( 'wp-travel-dashboard' );

	if ( -1 === $get_dashboard_page ) {

		$pages = apply_filters(
			'wp_travel_create_pages', array(
				'wp-travel-cart'      => array(
					'name'    => _x( 'wp-travel-cart', 'Page slug', 'wp-travel' ),
					'title'   => _x( 'WP Travel Cart', 'Page title', 'wp-travel' ),
					'content' => '[' . apply_filters( 'wp_travel_cart_shortcode_tag', 'wp_travel_cart' ) . ']',
				),
				'wp-travel-checkout'  => array(
					'name'    => _x( 'wp-travel-checkout', 'Page slug', 'wp-travel' ),
					'title'   => _x( 'WP Travel Checkout', 'Page title', 'wp-travel' ),
					'content' => '[' . apply_filters( 'wp_travel_checkout_shortcode_tag', 'wp_travel_checkout' ) . ']',
				),
				'wp-travel-dashboard' => array(
					'name'    => _x( 'wp-travel-dashboard', 'Page slug', 'wp-travel' ),
					'title'   => _x( 'WP Travel Dashboard', 'Page title', 'wp-travel' ),
					'content' => '[' . apply_filters( 'wp_travel_account_shortcode_tag', 'wp_travel_user_account' ) . ']',
				),
			)
		);

		foreach ( $pages as $key => $page ) {
			wp_travel_create_page( esc_sql( $page['name'] ), 'wp_travel_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wp_travel_get_page_id( $page['parent'] ) : '' );
		}
	}

}

wp_travel_maybe_create_new_roles();
wp_travel_maybe_create_pages();
