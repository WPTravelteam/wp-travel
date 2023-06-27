<?php
/**
 * 
 * Render Callback For Trip Departure
 * 
 */

function wptravel_block_trip_departure_render( ) {

	global $wpdb;
	$trip_id = get_the_ID();
	$trip_departures = (array)$wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_wt_dates WHERE trip_id = %d", $trip_id ) );

	ob_start();
	?>
	<div id="wptravel-trip-departure-block">
		<?php 
			if( !get_the_ID() ){ ?>
				<div class="date-list">
					<span class="departure-title"><?php echo __( 'Departure 1', 'wp-travel-block-pro' ); ?></span>
					<div class="date">
						<span><?php echo __( 'Start Date: ', 'wp-travel-block-pro' );?></span><?php echo '2023-06-22'; ?>
					</div>
					<div class="date">
						<span><?php echo __( 'End Date: ', 'wp-travel-block-pro' );?></span><?php echo '2023-06-30'; ?>
					</div>		
				</div>
			<?php }else{
				if( get_post()->post_type == 'itineraries' ){
					foreach( $trip_departures as $departure ){ ?>
						<div class="date-list">
							<span class="departure-title"><?php echo esc_html($departure->title); ?></span>
							<div class="date">
								<span><?php echo __( 'Start Date: ', 'wp-travel-block-pro' );?></span><?php echo esc_html($departure->start_date); ?>
							</div>
							<?php if( !empty( $departure->end_date ) ){ ?>
								<div class="date">
									<span><?php echo __( 'End Date: ', 'wp-travel-block-pro' );?></span><?php echo esc_html($departure->end_date); ?>
								</div>
							<?php } ?>				
						</div>
					<?php }
				}else{ ?>
					<div class="date-list">
						<span class="departure-title"><?php echo __( 'Departure 1', 'wp-travel-block-pro' ); ?></span>
						<div class="date">
							<span><?php echo __( 'Start Date: ', 'wp-travel-block-pro' );?></span><?php echo '2023-06-22'; ?>
						</div>
						<div class="date">
							<span><?php echo __( 'End Date: ', 'wp-travel-block-pro' );?></span><?php echo '2023-06-30'; ?>
						</div>		
					</div>
				<?php }
				
			}
		?>
		
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
