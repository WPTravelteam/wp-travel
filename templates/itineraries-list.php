<?php
	global $post;
	$post_id     = $post->ID;
	$itineraries = get_post_meta( $post_id, 'wp_travel_trip_itinerary_data' );
if ( isset( $itineraries[0] ) && ! empty( $itineraries[0] ) ) : ?>
		<div class="itenary clearfix">
			<div class="timeline-contents clearfix">
				<h2><?php esc_html_e( 'Itineraries', 'wp-travel' ); ?></h2>
					<?php $index = 1; ?>
					<?php foreach ( $itineraries[0] as $key => $itinerary ) : ?>
						<?php if ( $index % 2 === 0 ) : ?>
							<?php
								$first_class  = 'right';
								$second_class = 'left';
								$row_reverse  = 'row-reverse';
							?>
						<?php else : ?>
							<?php
								$first_class  = 'left';
								$second_class = 'right';
								$row_reverse  = '';
							?>
						<?php endif; ?>
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
							$itinerary_date = wp_travel_format_date( $itinerary['date'] );
						}
						if ( isset( $itinerary['time'] ) && '' !== $itinerary['time'] ) {
							$itinerary_time = stripslashes( $itinerary['time'] );
							$itinerary_time = date( $time_format, strtotime( $itinerary_time ) );
						}
						?>
						<div class="col clearfix <?php echo esc_attr( $row_reverse ); ?>">
							<div class="tc-heading <?php echo esc_attr( $first_class ); ?> clearfix">
								<?php if ( '' !== $itinerary_label ) : ?>
								<h4><?php echo esc_html( $itinerary_label ); ?></h4>
								<?php endif; ?>
								<?php if ( $itinerary_date ) : ?>
									<h3 class="arrival"><?php esc_html_e( 'Date', 'wp-travel' ); ?> : <?php echo esc_html( $itinerary_date ); ?></h3>
								<?php endif; ?>
								<?php if ( $itinerary_time ) : ?>
									<h3><?php esc_html_e( 'Time', 'wp-travel' ); ?> : <?php echo esc_html( $itinerary_time ); ?></h3>
								<?php endif; ?>
							</div><!-- tc-content -->
							<div class="tc-content <?php echo esc_attr( $second_class ); ?> clearfix" >
								<?php if ( '' !== $itinerary_title ) : ?>
								<h3><?php echo esc_html( $itinerary_title ); ?></h3>
								<?php endif; ?>
                                <?php do_action( 'wp_travel_itineraries_after_title', $itinerary ); ?>
								<?php echo wp_kses_post( $itinerary_desc ); ?>
								<div class="image"></div>
							</div><!-- tc-content -->
						</div><!-- first-content -->
						<?php $index++; ?>
					<?php endforeach; ?>

			</div><!-- timeline-contents -->
		</div><!-- itenary -->

	<?php endif; ?>
