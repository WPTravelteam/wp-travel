<?php
function wp_travel_get_gallery_ids( $post_id ) {
	$gallery_ids = get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	if ( false === $gallery_ids || empty( $gallery_ids ) ) {
		return false;
	}
	return $gallery_ids;
}

/** Return All Settings of WP Traval. */
function wp_traval_get_settings() {
	$settings = get_option( 'wp_travel_settings' );
	return $settings;
}
