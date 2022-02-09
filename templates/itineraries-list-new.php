<?php
/**
 * Frontend itineraries listing.
 *
 * @package WP_Travel
 */

global $post;
$trip_id     = $post->ID;
$itineraries = get_post_meta( $trip_id, 'wp_travel_trip_itinerary_data' );
if ( isset( $itineraries[0] ) && ! empty( $itineraries[0] ) ) :
	?>
		<?php $index = 1; ?>
		<?php foreach ( $itineraries[0] as $key => $itinerary ) : ?>
			<?php

			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );

			$itinerary_label = '';
			$itinerary_title = '';
			$itinerary_desc  = '';
			$itinerary_date  = '';
			$itinerary_time  = '';
			if ( isset( $itinerary['label'] ) && '' !== $itinerary['label'] ) {
				$itinerary_label = stripslashes( $itinerary['label'] );
			}
			if ( isset( $itinerary['title'] ) && '' !== $itinerary['title'] ) {
				$itinerary_title = stripslashes( $itinerary['title'] );
			}
			if ( isset( $itinerary['desc'] ) && '' !== $itinerary['desc'] ) {
				$itinerary_desc = stripslashes( $itinerary['desc'] );
			}
			if ( isset( $itinerary['date'] ) && '' !== $itinerary['date'] ) {
				$itinerary_date = wptravel_format_date( $itinerary['date'] );
			}
			if ( isset( $itinerary['time'] ) && '' !== $itinerary['time'] ) {
				$itinerary_time = stripslashes( $itinerary['time'] );
				$itinerary_time = gmdate( $time_format, strtotime( $itinerary_time ) );
			}
			?>
		<div class="trip-itinerary__item">
			<h5 class="trip-itinerary__title">
				<strong>
					<?php
					if ( '' !== $itinerary_label ) {
						echo esc_html( $itinerary_label ) . ': ';
					}
					if ( $itinerary_date ) {
						echo esc_html( $itinerary_date ) . '';
					}
					if ( $itinerary_time ) {
						echo ', ' . esc_html( $itinerary_time ) . ' ';
					}
					?>
				</strong> 
				<?php
				if ( '' !== $itinerary_title ) {
					echo esc_html( $itinerary_title );
				}
				?>
			</h5>
			<div class="trip-itinerary__content-wrapper">
				<?php do_action( 'wp_travel_itineraries_after_title', $itinerary ); ?>
				<p><?php echo wp_kses_post( $itinerary_desc ); ?></p>
			</div>
		</div>
			<?php $index++; ?>
	<?php endforeach; ?>
<?php endif; ?>
