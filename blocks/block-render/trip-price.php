<?php
/**
 * 
 * Render Callback For Trip Price
 * 
 */

function wptravel_block_trip_price_render( $attributes ) {

	$show_from_label    = $attributes['showFromLabel'];
	$hide_regular_price = $attributes['hideRegularPrice'];

	$trip_id = get_the_ID();

	$args                             = array( 'trip_id' => $trip_id );
	$args_regular                     = $args;
	$args_regular['is_regular_price'] = true;
	$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
	$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
	$enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
		array(
			'trip_id'                => $trip_id,
			'from_price_sale_enable' => true,
		)
	);

	$strings = WpTravel_Helpers_Strings::get();

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );

	// Styles
	$text_style = '';
	if ( ! empty( $attributes['color'] ) ) {
		$text_style .= sprintf( '.wptravel-block-trip-price .trip-price{ color: %s}', $attributes['color'] );
	}

	if ( ! empty( $attributes['fontSize'] ) ) {
		$text_style .= sprintf( '.wptravel-block-trip-price .trip-price{ font-size: %spx}', $attributes['fontSize'] );
	}

	ob_start();

	if ( $text_style  ) {
		?>
		<style>
			<?php
			echo $text_style;
			?>
		</style>
		<?php
	}
	?>
	<div id="wptravel-block-trip-price" class="wptravel-block-wrapper wptravel-block-trip-price <?php echo esc_attr( $class ); ?>">
		<div class="wp-travel-trip-detail">
			<?php 
				if( !get_the_ID() ){
			?>
				<div class="trip-price">
					<span class="price-from">From</span>
					<span class="person-count">
						<ins>
							<span>			
								<span class="wp-travel-trip-price-figure">20.00</span> 
								<span class="wp-travel-trip-currency">$</span>
							</span>
						</ins>
					</span>
				</div>
			<?php
				}else{ 
					if( get_post()->post_type == 'itineraries' ){
						if ( $trip_price ) : ?>
							<div class="trip-price" >
								<?php if ( $show_from_label ) : ?>
									<span class="price-from">
										<?php echo esc_html( $strings['from'] ); ?>
									</span>
								<?php endif; ?>
								<?php if ( ! $hide_regular_price && $enable_sale && $regular_price !== $trip_price ) : ?>
									<del><span><?php echo wptravel_get_formated_price_currency( $regular_price, true ); ?></span></del>
								<?php endif; ?>
								<span class="person-count">
									<ins>
										<span><?php echo wptravel_get_formated_price_currency( $trip_price ); ?></span>
									</ins>
								</span>
							</div>
						<?php endif;
					}else{ ?>
						<div class="trip-price">
							<span class="price-from">From</span>
							<span class="person-count">
								<ins>
									<span>			
										<span class="wp-travel-trip-price-figure">20.00</span> 
										<span class="wp-travel-trip-currency">$</span>
									</span>
								</ins>
							</span>
						</div>
					<?php }
				}
			?>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}