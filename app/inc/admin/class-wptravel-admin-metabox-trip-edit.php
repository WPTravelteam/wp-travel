<?php
/**
 * Trip edit metabox admin file.
 *
 * @package WP_Travel
 */

/**
 * WpTravel_Admin_Metabox_Trip_Edit class.
 */
class WpTravel_Admin_Metabox_Trip_Edit {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_data' ) );
	}

	/**
	 * Metabox register function.
	 *
	 * @return void
	 */
	public static function register_meta_box() {
		$switch_to_react = wptravel_is_react_version_enabled();
		if ( $switch_to_react ) {
			add_meta_box( 'wp-travel-trip-options', esc_html__( 'Trip Options', 'wp-travel' ), array( __CLASS__, 'meta_box_callback' ), WP_TRAVEL_POST_TYPE, 'advanced', 'high' );
		}
	}

	/**
	 * Callback for metabox.
	 *
	 * @return void
	 */
	public static function meta_box_callback() {
		do_action( 'wp_travel_trip_durations_valitions' );
		?>
		<div id="wp-travel-trip-options-wrap"></div>
		<?php
	}

	/**
	 * Metabox for Trip overview.
	 *
	 * @param object $trip Trip object.
	 * @since 5.0.2
	 */
	public static function overview_meta_box_callback( $trip ) {
		$trip_id  = $trip->ID;
		$overview = get_post_meta( $trip_id, 'wp_travel_overview', true );
		WP_Travel::create_nonce_field();
		wp_editor( $overview, 'wp_travel_overview' );
	}

	/**
	 * Save Post meta data.
	 *
	 * @param  int $trip_id ID of current post.
	 *
	 * @since 5.0.2
	 * @return Mixed
	 */
	public static function save_meta_data( $trip_id ) {
		if ( ! WP_Travel::verify_nonce( true ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $trip_id ) ) {
			return;
		}
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $trip_id ) ) {
			return;
		}

		$post_type = get_post_type( $trip_id );

		// If this isn't a WP_TRAVEL_POST_TYPE post, don't update it.
		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}

		$action = isset( $_POST['action'] ) ? $_POST['action'] : ''; // @phpcs:ignore.
		if ( $action ) {
			if ( 'inline-save' === $action ) {
				return; // Return if action is quick edit.
			}
			if ( 'elementor_ajax' === $action ) {
				return; // Return if action is elementor ajax.
			}
		}
		$screen = get_current_screen();
		if ( ! $screen ) { // Quick fixes [ Data override as empty form elementor ].
			return;
		}

		remove_action( 'save_post', array( __CLASS__, 'save_meta_data' ) );

		$overview = isset( $_POST['wp_travel_overview'] ) ? wp_kses_post( $_POST['wp_travel_overview'] ) : ''; // @phpcs:ignore
		update_post_meta( $trip_id, 'wp_travel_overview', $overview );
	}
}

WpTravel_Admin_Metabox_Trip_Edit::init();
