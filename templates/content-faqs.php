<?php
/**
 * Itinerary Pricing Options Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-pricing-options.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   1.1.5
 */
global $post;
global $wp_travel_itinerary;
$trip_id    = $post->ID;
?>
<div class="panel-group" id="accordion">
<?php
$faqs = wptravel_get_faqs( $trip_id );
if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
	?>
	<div class="wp-collapse-open clearfix">
		<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
		<a href="#" class="close-all-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
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
			<?php esc_html_e( 'No Details Found', 'wp-travel' ); ?>
		</p>
	</div>
<?php } ?>
</div>
