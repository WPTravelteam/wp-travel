<?php
/**
 * Adds dashboard Widgets for WP Travel.
 *
 * @package WP_Travel
 * @since 1.5.4
 */

class WP_Travel_Admin_Dashboard_Widgets {
	/**
	 * Assets path.
	 */
	var $assets_path;

	public function __construct() {
		$this->assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );
		add_action( 'wp_dashboard_setup', array( $this, 'add_widgets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_widgets() {

		$bookings = wp_count_posts( 'itinerary-booking' );
		// latest Bookings Widget.
		if ( 0 !== $bookings->publish && current_user_can( 'administrator' ) ) {
			add_meta_box( 'wp-travel-recent-bookings', __( 'WP Travel: Bookings', 'wp-travel' ), array( $this, 'new_booking_callback' ), 'dashboard', 'side', 'high' );
		}
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'dashboard' === $screen->id ) {
			wp_enqueue_style( 'wp-travel-dashboard-widget-styles', $this->assets_path . 'app/assets/css/wp-travel-dashboard-widget.css' );
		}

	}

	public function new_booking_callback() {

		$args = array(
			'numberposts' => apply_filters( 'wp_travel_dashboard_widget_bookings', 5 ),
			'post_type'   => 'itinerary-booking',
		);

		$bookings = get_posts( $args );
		if ( ! empty( $bookings ) && is_array( $bookings ) ) : ?>
			<table class="wp_travel_booking_dashboard_widget">
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Contact Name', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Status', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Payment', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Date', 'wp-travel' ); ?></th>
					</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $bookings as $k => $booking ) :
					// Set Vars.
					$id         = $booking->ID;
					$booking_id = $booking->post_title;

					$trip_id   = get_post_meta( $id, 'wp_travel_post_id', true );
					$trip_code = wptravel_get_trip_code( $trip_id );

					$first_name = get_post_meta( $id, 'wp_travel_fname_traveller', true );
					if ( ! $first_name ) {
						// Legacy version less than 1.7.5 [ retriving value from old meta once. update post will update into new meta ].
						$first_name = get_post_meta( $id, 'wp_travel_fname', true );
					}
					$middle_name = get_post_meta( $id, 'wp_travel_mname_traveller', true );
					if ( ! $middle_name ) {
						$middle_name = get_post_meta( $id, 'wp_travel_mname', true );
					}
					$last_name = get_post_meta( $id, 'wp_travel_lname_traveller', true );
					if ( ! $last_name ) {
						$last_name = get_post_meta( $id, 'wp_travel_mname', true );
					}

					if ( is_array( $first_name ) ) { // Multiple Travelers.

						reset( $first_name );
						$first_key = key( $first_name );

						$name = '';
						if ( isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ) {
							$name .= $first_name[ $first_key ][0];
						}
						if ( isset( $middle_name[ $first_key ] ) && isset( $middle_name[ $first_key ][0] ) ) {
							$name .= ' ' . $middle_name[ $first_key ][0];
						}
						if ( isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ) {
							$name .= ' ' . $last_name[ $first_key ][0];
						}
					} else {
						$name  = $first_name;
						$name .= ' ' . $middle_name;
						$name .= ' ' . $last_name;
					}

					$date = wptravel_format_date( $booking->post_date, true, 'Y-m-d' );

					// Booking Status.
					$status    = wptravel_get_booking_status();
					$label_key = get_post_meta( $id, 'wp_travel_booking_status', true );
					if ( '' === $label_key ) {
						$label_key = 'pending';
						update_post_meta( $id, 'wp_travel_booking_status', $label_key );
					}

					// Payment.
					$payment_id = get_post_meta( $id, 'wp_travel_payment_id', true );

					$pmt_label_key = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
					if ( ! $pmt_label_key ) {
						$pmt_label_key = 'N/A';
						update_post_meta( $payment_id, 'wp_travel_payment_status', $pmt_label_key );
					}
					$Pmt_status = wptravel_get_payment_status();
					?>

					<tr>
						<td><a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php echo esc_html( $booking_id ); ?></a></td>
						<td><?php echo esc_html( $trip_code ); ?></td>
						<td><?php echo esc_html( $name ); ?></td>
						<td><?php echo '<span class="wp-travel-status wp-travel-booking-status" style="background: ' . esc_attr( $status[ $label_key ]['color'] ) . ' ">' . esc_attr( $status[ $label_key ]['text'] ) . '</span>'; ?></td>
						<td><?php echo '<span class="wp-travel-status wp-travel-payment-status" style="background: ' . esc_attr( $Pmt_status[ $pmt_label_key ]['color'], 'wp-travel' ) . ' ">' . esc_attr( $Pmt_status[ $pmt_label_key ]['text'], 'wp-travel' ) . '</span>'; ?></td>
						<td><?php echo esc_html( $date ); ?></td>
					</tr>

					<?php
					endforeach;
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=itinerary-booking' ) ); ?>" class="button button-primary"><?php esc_html_e( 'View All Bookings', 'wp-travel' ); ?></a></td>
				</tr>
			<tfoot>
			</table>
			<?php
		endif;

	}
}

new WP_Travel_Admin_Dashboard_Widgets();
