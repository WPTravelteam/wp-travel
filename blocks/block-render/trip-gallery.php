<?php
/**
 * 
 * Render Callback For Trip Gallery
 * 
 */

function wptravel_block_trip_gallery_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();

	$content = is_array( $tab_data ) && isset( $tab_data['gallery'] ) && isset( $tab_data['gallery']['content'] ) ? $tab_data['gallery']['content'] : '';
	
	$galleryDesign     = isset( $attributes['galleryDesign'] ) ? $attributes['galleryDesign'] : 'gridStyle';
	$sliderHeight     = isset( $attributes['sliderHeight'] ) ? $attributes['sliderHeight'] : 400;
	$sliderAutoplay     = isset( $attributes['sliderAutoplay'] ) ? $attributes['sliderAutoplay'] : false;
	$sliderArrow     = isset( $attributes['sliderArrow'] ) ? $attributes['sliderArrow'] : false;
	$sliderDots     = isset( $attributes['sliderDots'] ) ? $attributes['sliderDots'] : false;

	if( $galleryDesign == 'gridStyle' ){
		echo '<div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery">'; //@phpcs:ignore
		echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
		echo '</div>'; //@phpcs:ignore
	}else{
	
	?>	
		<div id="wptravel-block-trip-gallery" class="wptravel-block-wrapper wptravel-block-trip-gallery slider">
			<div class="wp-travel-advanced-gallery-items-list">
				<?php foreach( get_post_meta( get_the_id(), 'wp_travel_advanced_gallery' )[0]['items'] as $image ): ?>
					<div class="item image">
						<a href="<?php echo esc_url( $image['url'] ); ?>" class="mfp-image">
							<span class="wptag__media-card">
								<span class="wptag__thumbnail">
									<img decoding="async" alt="" src="<?php echo esc_url( $image['url'] ); ?>">
								</span>
							</span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<style>
			@media only screen and (min-width: 993px){
				.wptravel-block-trip-gallery .wp-travel-advanced-gallery-items-list.slick-slider .wptag__thumbnail img {
					max-height: <?php echo esc_attr($sliderHeight); ?>px;
				}
			}
		</style>
		<script>
			jQuery(document).ready(function(n) {
				n('.slider .wp-travel-advanced-gallery-items-list').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					<?php 
						if( $sliderAutoplay ){
						?>
						autoplay: true,
						<?php
						}else{
						?>
						autoplay: false,
						<?php
						}
					?>
					<?php 
						if( $sliderArrow ){
						?>
						arrows: true,
						<?php
						}else{
						?>
						arrows: false,
						<?php
						}
					?>
					<?php 
						if( $sliderDots ){
						?>
						dots: true,
						<?php
						}else{
						?>
						dots: false,
						<?php
						}
					?>
					infinite: true,
				});
			});
		</script>
	<?php
	}
	return ob_get_clean();
}

add_action( 'rest_api_init', 'wp_travel_get_trips_meta' );

function wp_travel_get_trips_meta(){
	register_rest_route(
		'wptravel/v1',
		'/get-trip-gallery',
		array(
			'methods'             => 'get',
			'permission_callback' => '__return_true',
			'callback'            => 'wp_travel_trip_gallery',
		)
	);
}

function wp_travel_trip_gallery( WP_REST_Request $request ){
	return get_post_meta( $request->get_param('id'), 'wp_travel_advanced_gallery', true )['items'];
}