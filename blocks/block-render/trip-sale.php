<?php
/**
 * 
 * Render Callback For Trip Sale
 * 
 */

function wptravel_block_trip_sale_render( $attributes ) {
	// Options / Attributes
	$trip_id = get_the_ID();

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	
	ob_start();
	?>
	<div id="wptravel-block-trip-sale" data-align="<?php echo esc_attr( $align ); ?>" class="wptravel-block-wrapper">
		<?php if( !get_the_ID() ) { ?>
			<span> <?php echo '10' . __( ' % off', 'wp-travel-block-pro' ); ?>  </span>
		<?php }else{
			if( get_post()->post_type == 'itineraries' ){
				if( isset(get_post_meta( get_the_ID(), 'wptravel_enable_sale' )[0]) &&  get_post_meta( get_the_ID(), 'wptravel_enable_sale' )[0] !== '1' ){
					return '';
				}
				if( count( WpTravel_Helpers_Trips::get_trip( $trip_id )['trip']['pricings'] ) == 0 ){
					return '';
				}
				$sale_price = WpTravel_Helpers_Trips::get_trip( $trip_id )['trip']['pricings']['0']['categories']['0']['sale_price'];
				$regular_price = WpTravel_Helpers_Trips::get_trip( $trip_id )['trip']['pricings']['0']['categories']['0']['regular_price'];
				$discount = ( $regular_price - $sale_price )/$regular_price * 100;
			?>
			<span> <?php echo esc_html( $discount ) . __( ' % off', 'wp-travel-block-pro'. 'wp-travel-block-pro' ); ?>  </span>
			<?php }else{ ?>
				<span> <?php echo '10' . __( ' % off', 'wp-travel-block-pro' ); ?>  </span>
			<?php }
			
		} ?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
