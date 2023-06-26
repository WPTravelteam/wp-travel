<?php
/**
 * 
 * Render Callback For Trip Rating
 * 
 */

function wptravel_block_trip_rating_render( $attributes ) {
	$trip_id = get_the_ID();

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );

	ob_start();
		$review_color = isset( $attributes['reviewColor'] ) ? $attributes['reviewColor'] : '';
		if ( $review_color ) {
		// Review Gutenberg Block.
		$inline_style  = sprintf( '.wptravel-block-wrapper.wptravel-block-trip-rating .wp-travel-average-review:before{ color: %s}', esc_attr( $review_color ) );
		$inline_style .= sprintf( '.wptravel-block-wrapper.wptravel-block-trip-rating .wp-travel-average-review span{ color: %s}', esc_attr( $review_color ) );
		?>
			<style>
			<?php
			echo $inline_style; // @phpcs:ignore
			?>
			</style>
			<?php
	}
	echo '<div id="wptravel-block-trip-rating" class="wptravel-block-wrapper wptravel-block-trip-rating ' . esc_attr( $class ) . '">'; // @phpcs:ignore
	if( !get_the_ID() ){
	?>
		<div class="wp-travel-average-review" title="Rated 4.33 out of 5">
			<a href="#">
				<span style="width:86.6%">
					<strong itemprop="ratingValue" class="rating">4.33</strong> out of <span itemprop="bestRating">5</span>	
				</span>
			</a>

		</div>
	<?php }else{
		if( get_post()->post_type == 'itineraries' ){
			wptravel_single_trip_rating( $trip_id );
		}else{ ?>
			<div class="wp-travel-average-review" title="Rated 4.33 out of 5">
				<a href="#">
					<span style="width:86.6%">
						<strong itemprop="ratingValue" class="rating">4.33</strong> out of <span itemprop="bestRating">5</span>	
					</span>
				</a>

			</div>
		<?php }
		
	}
	
	echo '</div>'; // @phpcs:ignore
	$html = ob_get_clean();
	return $html;
}