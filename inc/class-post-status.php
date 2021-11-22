<?php
/**
 * Class to add Custom Post status.
 *
 * @package WP_Travel
 */

/**
 * Class to add Custom Post status.
 *
 * @since 4.4.4
 */
class WP_Travel_Post_Status { // @phpcs:ignore

	/**
	 * Init post status class.
	 *
	 * @return void
	 */
	public static function init() {
		// Post Type Itinerary Status.
		self::register_trip_status();
		add_action( 'post_submitbox_misc_actions', array( 'WP_Travel_Post_Status', 'trip_status_dropdown' ) ); // Dropdown in Trip edit page.
		add_action( 'admin_footer-edit.php', array( 'WP_Travel_Post_Status', 'quick_edit_trip_status_dropdown' ) ); // Dropdown in Quick edit.
		add_filter( 'display_post_states', array( 'WP_Travel_Post_Status', 'trip_states_column' ) ); // Status expired along with Title in the trip archive list.

		add_filter( 'post_row_actions', array( 'WP_Travel_Post_Status', 'remove_view_link' ), 10, 2 );
	}

	/**
	 * Register Trip status.
	 *
	 * @since 4.4.4
	 * @return void
	 */
	public static function register_trip_status() {
		register_post_status(
			'expired',
			array(
				'label'                     => _x( 'Expired', 'post', 'wp-travel' ),
				'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'wp-travel' ), // @phpcs:ignore
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => false,
				'show_in_admin_status_list' => true,
			)
		);
	}

	/**
	 * Add custom status in dropdown.
	 *
	 * @since 4.4.4
	 * @return Mixed
	 */
	public static function trip_status_dropdown() {
		global $post;
		if ( 'itineraries' !== $post->post_type ) {
			return false;
		}
		?>
		<script>
			jQuery(document).ready( function() {
				jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"expired\">Expired</option>' );
				<?php if ( 'expired' === $post->post_status ) : ?>
					jQuery( '#post-status-display' ).text( 'Expired' ); 
					jQuery( 'select[name="post_status"]' ).val('expired');
				<?php endif; ?>
			});
		</script>
		<?php
	}

	/**
	 * Add custom status in dropdown for quick edit.
	 *
	 * @since 4.4.4
	 * @return void
	 */
	public static function quick_edit_trip_status_dropdown() {
		global $post;

		if ( ! is_object( $post ) ) {
			return;
		}
		if ( 'itineraries' !== $post->post_type ) {
			return false;
		}
		?>
		<script>
			jQuery(document).ready( function() {
				jQuery( 'select[name="_status"]' ).append( '<option value="expired">Expired</option>' );
			});
		</script>
		<?php
	}

	/**
	 * Status expired along with Title in the trip archive list.
	 *
	 * @param String $status Name of status.
	 * @since 4.4.4
	 * @return string
	 */
	public static function trip_states_column( $status ) {
		global $post;
		if ( $post && 'expired' === $post->post_status ) {
			$status['expired'] = 'Expired';
		}
		return $status;
	}

	/**
	 * Remove edit, view.
	 *
	 * @param array  $actions Remove View trips in admin trip list.
	 * @param object $trip Trip post object.
	 * @since 4.4.4
	 * @return Mixed
	 */
	public static function remove_view_link( $actions, $trip ) {
		if ( WP_TRAVEL_POST_TYPE === $trip->post_type && 'expired' === $trip->post_status ) {
			unset( $actions['view'], $actions['edit'], $actions['wp_travel_duplicate_post'], $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}
}
