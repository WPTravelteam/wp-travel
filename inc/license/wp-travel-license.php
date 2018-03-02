<?php


/**
 * Return all Payment Methods.
 *
 * @since 1.1.0
 * @return Array
 */
function wp_travel_premium_addons() {
	return count( apply_filters( 'wp_travel_premium_addons_list', array() ) );
}
