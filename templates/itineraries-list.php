<?php
/**
 * Termplate file for itinerary list.
 *
 * @package WP_Travel
 */

global $post;
$wptravel_trip_id     = $post->ID;
$wptravel_itineraries = get_post_meta( $wptravel_trip_id, 'wp_travel_trip_itinerary_data', true );
if ( isset( $wptravel_itineraries ) && ! empty( $wptravel_itineraries ) ) : ?>
	<div class="itenary clearfix">
		<div class="timeline-contents clearfix">
			<h2><?php esc_html_e( apply_filters( 'wp_travel_ititneraries_trip_outline_tab', 'Itineraries', $wptravel_trip_id ), 'wp-travel' ); ?></h2>
				<?php
				$wptravel_index = 1;
				foreach ( $wptravel_itineraries as $wptravel_itinerary ) :
					if ( 0 === $wptravel_index % 2 ) :
						$wptravel_first_class  = 'right';
						$wptravel_second_class = 'left';
						$wptravel_row_reverse  = 'row-reverse';
					else :
						$wptravel_first_class  = 'left';
						$wptravel_second_class = 'right';
						$wptravel_row_reverse  = '';
					endif;
					$wptravel_time_format = get_option( 'time_format' );

					$wptravel_itinerary_label = '';
					$wptravel_itinerary_title = '';
					$wptravel_itinerary_desc  = '';
					$wptravel_itinerary_date  = '';
					$wptravel_itinerary_time  = '';
					if ( isset( $wptravel_itinerary['label'] ) && '' !== $wptravel_itinerary['label'] ) {
						$wptravel_itinerary_label = stripslashes( $wptravel_itinerary['label'] );
					}
					if ( isset( $wptravel_itinerary['title'] ) && '' !== $wptravel_itinerary['title'] ) {
						$wptravel_itinerary_title = stripslashes( $wptravel_itinerary['title'] );
					}
					if ( isset( $wptravel_itinerary['desc'] ) && '' !== $wptravel_itinerary['desc'] ) {
						$wptravel_itinerary_desc = stripslashes( $wptravel_itinerary['desc'] );
					}
					if ( isset( $wptravel_itinerary['date'] ) && '' !== $wptravel_itinerary['date'] && 'invalid date' !== strtolower( $wptravel_itinerary['date'] ) ) {
						$wptravel_itinerary_date = wptravel_format_date( $wptravel_itinerary['date'] );
					}
					if ( isset( $wptravel_itinerary['time'] ) && '' !== $wptravel_itinerary['time'] ) {
						$wptravel_itinerary_time = stripslashes( $wptravel_itinerary['time'] );
						$wptravel_itinerary_time = date( $wptravel_time_format, strtotime( $wptravel_itinerary_time ) ); // @phpcs:ignore
					}
					?>
					<div class="col clearfix <?php echo esc_attr( $wptravel_row_reverse ); ?>">
						<div class="tc-heading <?php echo esc_attr( $wptravel_first_class ); ?> clearfix">
							<?php if ( '' !== $wptravel_itinerary_label ) : ?>
							<h4><?php echo esc_html( $wptravel_itinerary_label ); ?></h4>
							<?php endif; ?>
							<?php if ( $wptravel_itinerary_date ) : ?>
								<h3 class="arrival"><?php esc_html_e( 'Date', 'wp-travel' ); ?> : <?php echo esc_html( $wptravel_itinerary_date ); ?></h3>
							<?php endif; ?>
							<?php if ( $wptravel_itinerary_time ) : ?>
								<h3><?php esc_html_e( 'Time', 'wp-travel' ); ?> : <?php echo esc_html( $wptravel_itinerary_time ); ?></h3>
							<?php endif; ?>
						</div><!-- tc-content -->
						<div class="tc-content <?php echo esc_attr( $wptravel_second_class ); ?> clearfix" >
							<?php if ( '' !== $wptravel_itinerary_title ) : ?>
							<h3><?php echo esc_html( $wptravel_itinerary_title ); ?></h3>
							<?php endif; ?>
							<?php do_action( 'wp_travel_itineraries_after_title', $wptravel_itinerary ); ?>
							<?php echo wp_kses_post( wpautop( $wptravel_itinerary_desc ) ); ?>
							<div class="image"></div>
						</div><!-- tc-content -->
					</div><!-- first-content -->
					<?php $wptravel_index++; ?>
				<?php endforeach; ?>
		</div><!-- timeline-contents -->
	</div><!-- itenary -->
<?php endif; ?>
