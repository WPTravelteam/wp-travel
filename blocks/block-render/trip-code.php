<?php
/**
 * 
 * Render Callback For Trip Code
 * 
 */

function wptravel_block_trip_code_render( $attributes, $content, $block ) {

	ob_start();
	$client_id = $attributes['extraClass'] ;
	$align     = ! empty( $attributes['align'] ) ? $attributes['align'] : 'full';
	$class     = ! empty( $attributes['className'] ) ? $attributes['className'] : '';
	$align     = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class     = sprintf( 'wptravel-block-trip-code has-text-align-%s %s', $align, $class );
	?>
	<?php if( !empty( $attributes['textColor'] ) ): ?>
		<style>
		.wptravel-block-<?php echo esc_attr( $client_id ); ?> code{
			color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
			}
		</style>
	<?php endif; ?>
	
	<div id="wptravel-block-trip-code" class="wptravel-block-wrapper wptravel-block-<?php echo esc_attr( $client_id ); ?> <?php echo esc_attr( $class ); ?>" >
		<code><?php echo esc_html( wptravel_get_trip_code( get_the_id() ) ); ?></code>
	</div>
	<?php
	return ob_get_clean();
}
