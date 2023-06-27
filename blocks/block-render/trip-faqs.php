<?php
/**
 * 
 * Render Callback For Trip FAQS
 * 
 */


function wptravel_block_trip_faqs_render( $attributes ) {
	ob_start();
	$trip_id   = get_the_ID();
	?>
	<style>
		.wptravel-block-trip-faqs .open-all-link{
			border-color: <?php echo esc_attr( $attributes['btnBorderColor'] ); ?> !important;
			border-radius: <?php echo esc_attr( $attributes['btnBorderRadius'] ); ?>px !important;
			color: <?php echo esc_attr( $attributes['btnTextColor'] ); ?> !important;
			background-color: <?php echo esc_attr( $attributes['btnBackgroundColor'] ); ?> !important;
		}

		.wptravel-block-trip-faqs .panel-title a{
			background-color: <?php echo esc_attr( $attributes['questionBackgroundColor'] ); ?> !important;
			color: <?php echo esc_attr( $attributes['questionTextColor'] ); ?> !important;
		}
		
		.wptravel-block-trip-faqs .panel-collapse{
			background-color: <?php echo esc_attr( $attributes['answerBackgroundColor'] ); ?> !important;
			color: <?php echo esc_attr( $attributes['answerTextColor'] ); ?> !important;
		}
	</style>
	<div id="faq" class="wptravel-block-wrapper wptravel-block-trip-faqs">
		<div class="panel-group" id="accordion">
			<?php
			$faqs = wptravel_get_faqs( $trip_id );
			if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
				?>
				<div class="wp-collapse-open clearfix">
					<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel-block-pro' ); ?></span></a>
					<a href="#" class="close-all-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel-block-pro' ); ?></span></a>
				</div>
				<?php foreach ( $faqs as $k => $faq ) : ?>
					<div class="panel panel-default">
						<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k + 1 ); ?>">
							<?php echo esc_html( $faq['question'] ); ?>
							<span class="collapse-icon"></span>
							</a>
						</h4>
						</div>
						<div id="collapse<?php echo esc_attr( $k + 1 ); ?>" class="panel-collapse collapse">
						<div class="panel-body">
							<?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
						</div>
						</div>
					</div>
				<?php
			endforeach;
		} else {
			?>
			<div class="while-empty">
				<p class="wp-travel-no-detail-found-msg" >
					<?php esc_html_e( 'No Details Found', 'wp-travel-block-pro' ); ?>
				</p>
			</div>
		<?php } ?>
		</div>
	</div>
	
	<?php
	return ob_get_clean();
}