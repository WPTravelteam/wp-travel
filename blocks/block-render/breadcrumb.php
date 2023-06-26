<?php
/**
 * 
 * Render Callback For Breadcrumb
 * 
 */

function wptravel_block_breadcrumb_render( $attributes ) {
	ob_start();

?>
	<style>
		#wptravel-breadcrumb-block{
			<?php 
				if(! empty( $attributes['textColor'] ) ){
			?>
				color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
			<?php }	?>
			<?php 
				if(! empty( $attributes['textAlign'] ) ){
			?>
				text-align: <?php echo esc_attr( $attributes['textAlign'] ); ?>;
			<?php }	?>
			<?php 
				if(! empty( $attributes['textSize'] ) ){
			?>
				font-size: <?php echo esc_attr( $attributes['textSize'] ); ?>px;
			<?php }	?>
		}
		#wptravel-breadcrumb-block a{
			<?php 
				if( ! empty( $attributes['linkColor'] ) ){
			?>
				color: <?php echo esc_attr( $attributes['linkColor'] ); ?>!important;
			<?php }	?>
		}
			
		<?php 
			if( ! empty( $attributes['separatorColor'] ) ){
		?>	
			#wptravel-breadcrumb-block .trail-items li.trail-item::after {			
				color: <?php echo esc_attr( $attributes['separatorColor'] ); ?>!important;	
			}
		<?php }	?>	
	
	</style>
	<div id="wptravel-breadcrumb-block" class="wptravel-block-wrapper">
        <?php
			if( function_exists('wp_travel_breadcrumb_trail') ){
				wp_travel_breadcrumb_trail(); 
			} 			
		?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
