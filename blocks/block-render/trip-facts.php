<?php
/**
 * 
 * Render Callback For Trip Facts
 * 
 */

function wptravel_block_trip_facts_render( $attributes ) {
	ob_start();
	$trip_id   = get_the_ID();

	$background_color = 'transparent';
	if ( isset( $attributes['backgroundColor'] ) && ! empty( $attributes['backgroundColor'] ) ) {
		$background_color = $attributes['backgroundColor'];
	}
	?>
	<style>
		.wptravel-block-trip-fact .tour-info .tour-info-box{
			border:none;
			background: <?php echo esc_attr( $background_color ); ?>
		}

		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item{
			color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
		}

		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item strong,
		.wptravel-block-trip-fact .tour-info .tour-info-box .tour-info-item i{
			color: <?php echo esc_attr( $attributes['labelColor'] ); ?>;
		}
	</style>
	<div id="wptravel-block-trip-fact" class="wptravel-block-wrapper wptravel-block-trip-fact">
		<?php wptravel_frontend_trip_facts( $trip_id ); ?>
	</div>
	<?php
	return ob_get_clean();
}