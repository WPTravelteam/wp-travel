<?php
/**
 * 
 * Render Callback For Trip Wishlists
 * 
 */

function wptravel_block_trip_wishlists_render( $attributes ) {


	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';

	ob_start();
	?>
	<div id="wptravel-wishlists-block" data-align="<?php echo esc_attr( $align ); ?>" class="wptravel-block-wrapper">
		<?php
		if( !get_the_ID() ){ ?>
			<div class="favourite">
				<a href="javascript:void(0);" data-id="375" data-event="add" title="Add to wishlists" class="wp-travel-add-to-wishlists">
					<i class="far fa-bookmark"></i> 
				</a>
			</div>
		<?php }else{
			if( get_post()->post_type == 'itineraries' ){
				if ( function_exists( 'wp_travel_wishlists_show_button' ) ) {
					wp_travel_wishlists_show_button( get_the_ID() );
				}
			}else{ ?>
				<div class="favourite">
					<a href="javascript:void(0);" data-id="375" data-event="add" title="Add to wishlists" class="wp-travel-add-to-wishlists">
						<i class="far fa-bookmark"></i> 
					</a>
				</div>
			<?php }			
		}
		?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
