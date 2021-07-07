<?php
/**
 * WP Travel Post Duplicator.
 *
 * @package WP_Travel
 */

/**
 * Create Clone link in the trip listing.
 *
 * @param   Array  $actions    Action.
 * @param   Object $post       Post Object.
 *
 * @since   1.7.6
 *
 * @return  Array $actions;
 */
function wptravel_post_duplicator_action_row( $actions, $post ) {
	// Get the post type object.
	$post_type = get_post_type_object( $post->post_type );
	if ( WP_TRAVEL_POST_TYPE === $post_type->name && function_exists( 'wptravel_post_duplicator_action_row_link' ) ) {
		$actions['wp_travel_duplicate_post'] = wptravel_post_duplicator_action_row_link( $post );
	}
	return $actions;
}
add_filter( 'post_row_actions', 'wptravel_post_duplicator_action_row', 10, 2 );

function wptravel_post_duplicator_action_row_link( $post ) {

	$settings = wptravel_get_settings();

	// Get the post type object
	$post_type = get_post_type_object( $post->post_type );

	if ( WP_TRAVEL_POST_TYPE !== $post_type->name ) {
		return;
	}

	// Set the button label
	$label = sprintf( __( 'Clone %s', 'wp-travel' ), $post_type->labels->singular_name );

	// Create a nonce & add an action
	$nonce = wp_create_nonce( 'wp_travel_clone_post_nonce' );

	// Return the link
	return '<a class="wp-travel-clone-post" data-security="' . $nonce . '" href="#" data-post_id="' . $post->ID . '">' . $label . '</a>';
}
