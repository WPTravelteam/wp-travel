<?php
/**
 * 
 * Render Callback For Trip Duration
 * 
 */

function wptravel_block_trip_duration_render( $attributes ) {

	$trip_id = get_the_ID();

	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	$type            = $fixed_departure ? 'type-fixed-departure' : 'type-trip-duartion';

	$trip_duration       = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	$extra_class = $attributes['extraClass'];

	ob_start();
	
	if( !empty( $attributes['textColor'] ) ): ?>
		<style>
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .dropbtn,
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?>{
				<?php if( $attributes['textColor'] ): ?>
					color: <?php echo esc_attr( $attributes['textColor'] ); ?>!important;
				<?php endif; ?>
			}
			.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .fixed-date-dropdown .dropbtn::after {
				color: <?php echo esc_attr( $attributes['textColor'] ); ?>!important;
			}
		</style>
	<?php
	endif;
	echo '<div id="wptravel-block-trip-duration" class="wptravel-block-wrapper wptravel-block-' .$extra_class. ' wptravel-block-trip-duration-date ' . $class . '">'; // @phpcs:ignore
	if( !get_the_ID() ){ ?>
		<div class="travel-info trip-duration">
			<span class="value">
				<?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel-block-pro' ), 3, 2 ); ?>
			</span>
		</div>
	<?php }else{  
		if( get_post()->post_type == 'itineraries' ){
			if ( $fixed_departure ) {
				$dates = wptravel_get_fixed_departure_date( $trip_id );
				if ( $dates ) {
					?>
					<div class="travel-info trip-fixed-departure <?php echo esc_attr( $type ); ?>">
						<span class="value">
							<?php echo $dates; // @phpcs:ignore ?>
						</span>
					</div>
					<?php
				}
			} else {
				if ( ( $trip_duration || $trip_duration_night ) ) :
					?>
				   <div class="travel-info trip-duration <?php echo esc_attr( $type ); ?>">
					   <span class="value">
						   <?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel-block-pro' ), $trip_duration, $trip_duration_night ); ?>
					   </span>
				   </div>
					<?php
			   endif;
			}
		}else{ ?>
			<div class="travel-info trip-duration">
				<span class="value">
					<?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel-block-pro' ), 3, 2 ); ?>
				</span>
			</div>
		<?php }
		
	}
	
	
	echo '</div>'; // @phpcs:ignore
	$html = ob_get_clean();

	return $html;
}