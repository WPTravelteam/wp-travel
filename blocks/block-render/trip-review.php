<?php
/**
 * 
 * Render Callback For Trip Reivew
 * 
 */

function wptravel_block_trip_review_render( $attributes ) {
	$trip_id = get_the_ID();

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class      = sprintf( 'has-text-align-%s', $align );

	ob_start();
	$count = (int) get_comments_number( $trip_id );
	?>
	<div id="wptravel-block-trip-review" class="wptravel-block-wrapper  wptravel-block-trip-review <?php echo esc_attr( $class ); ?>">
		<a href="javascript:void(0)" class="wp-travel-count-info">
			<?php printf( _n( '%s Review', '%s Reviews', $count, 'wp-travel-block-pro' ), esc_html( $count ) ); ?>
		</a>
	</div>
	<?php
	$html = ob_get_clean();
	return $html;
}