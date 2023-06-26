<?php
/**
 * 
 * Render Callback For Trip Departure
 * 
 */

function wptravel_block_trip_downloads_render( $attributes, $content, $block ) {
	// Options / Attributes
	$wp_travel_itinerary_tabs = wptravel_get_frontend_tabs();
    if( !isset( $wp_travel_itinerary_tabs['downloads'] ) ){
        return;
    }
	$extra_class = $attributes['extraClass'];
	ob_start();
	?>
	<style>
		.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .send_email_checkbox{
			<?php if( $attributes['textColor'] ): ?>
				color: <?php echo esc_attr( $attributes['textColor'] ); ?>!important;
			<?php endif; ?>
			<?php if( $attributes['titleContainerColor'] ): ?>
				background-color: <?php echo esc_attr( $attributes['titleContainerColor'] ); ?>!important;
			<?php endif; ?>
			
		}

		.wptravel-block-<?php echo esc_attr( $extra_class ); ?> #user-email-address{
			<?php if( $attributes['emailContainerColor'] ): ?>
				background-color: <?php echo esc_attr( $attributes['emailContainerColor'] ); ?>!important;
			<?php endif; ?>
		}

		.wptravel-block-<?php echo esc_attr( $extra_class ); ?> #user-email-address .btn-submit{
			<?php if( $attributes['sendbtnbgColor'] ): ?>
				background-color: <?php echo esc_attr( $attributes['sendbtnbgColor'] ); ?>!important;
			<?php endif; ?>

			<?php if( $attributes['sendbtntextColor'] ): ?>
				color: <?php echo esc_attr( $attributes['sendbtntextColor'] ); ?>!important;
			<?php endif; ?>
		}

		.wptravel-block-<?php echo esc_attr( $extra_class ); ?> .wp-travel-itinerary-downloads a{
			<?php if( $attributes['downloadbtnbgColor'] ): ?>
				background-color: <?php echo esc_attr( $attributes['downloadbtnbgColor'] ); ?>!important;
			<?php endif; ?>

			<?php if( $attributes['downloadbtntextColor'] ): ?>
				color: <?php echo esc_attr( $attributes['downloadbtntextColor'] ); ?>!important;
			<?php endif; ?>
		}
	</style>
	<div id="wptravel-trip-downloads-block"  class="wptravel-block-<?php echo esc_attr($extra_class) ?>">
        <?php echo do_shortcode( $wp_travel_itinerary_tabs['downloads']['content'] ); // @phpcs:ignore ?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
