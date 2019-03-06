<?php
if ( ! class_exists( 'WP_TRAVEL_ABSPATH' ) ) {
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
}

// Fields array.
$checkout_fields  = wp_travel_get_checkout_form_fields();
$traveller_fields = $checkout_fields['traveller_fields'];
$billing_fields   = $checkout_fields['billing_fields'];
$payment_fields   = $checkout_fields['payment_fields'];
$settings         = wp_travel_get_settings();

$enable_multiple_travellers = isset( $settings['enable_multiple_travellers'] ) ? esc_html( $settings['enable_multiple_travellers'] ) : 'no';
$all_travelers_fields_require = apply_filters( 'wp_travel_require_all_travelers_fields', false );
global $wt_cart;
$form_fw    = new WP_Travel_FW_Form();
$form_field = new WP_Travel_FW_Field();
$form_fw->init_validation( 'wp-travel-booking' );
?>
<form method="POST" action="" class="wp-travel-booking" id="wp-travel-booking">
	<?php do_action( 'wp_travel_action_before_checkout_field' ); ?>
	<!-- Travelers info -->
	<?php
	foreach ( $trips as $cart_id => $trip ) :
		$trip_id        = $trip['trip_id'];
		$price_key      = isset( $trip['price_key'] ) ? $trip['price_key'] : '';
		$pricing_name   = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
		$repeator_count = isset( $trip['pax'] ) ? $trip['pax'] : 1;
		if ( 'no' === $enable_multiple_travellers ) {
			$repeator_count = 1;
		}
		?>
		<div class="wp-travel-trip-details">
			<?php if ( 'yes' === $enable_multiple_travellers ) : ?>
				<div class="section-title text-left">
					<h3><?php echo esc_html( $pricing_name ); ?><!-- <small> / 8 days 7 nights</small> --></h3>
				</div>
			<?php endif; ?>
			<div class="panel-group number-accordion">
				<div class="panel-heading">
					<h4 class="panel-title"><?php esc_html_e( 'Traveler Details', 'wp-travel' ); ?></h4>
				</div>
				<div class="ws-theme-timeline-block panel-group checkout-accordion" id="checkout-accordion-<?php echo esc_attr( $cart_id ); ?>">
					<?php

					for ( $i = 0; $i < $repeator_count; $i++ ) :
						?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#checkout-accordion-<?php echo esc_attr( $cart_id ); ?>" href="#collapse-<?php echo esc_attr( $cart_id . '-' . $i ); ?>" aria-expanded="true">
										<?php
										if ( 0 === $i ) :
											esc_html_e( 'Lead Traveler', 'wp-travel' );
											$collapse      = 'collapse in';
											$area_expanded = 'true';
											else :
												echo esc_html( __( sprintf( 'Traveler %d', ( $i + 1 ) ), 'wp-travel' ) );
												$collapse      = 'collapse';
												$area_expanded = 'false';
											endif;
											?>
										<span class="collapse-icon"></span>
									</a>
								</h4>
							</div>
							<div id="collapse-<?php echo esc_attr( $cart_id . '-' . $i ); ?>" class="panel-collapse <?php echo esc_attr( $collapse ); ?>" aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
								<div class="panel-body">
									<div class="payment-content">
										<div class="row gap-0">
											<div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
												<h6 class="heading mt-0 mb-15">

												</h6>
											</div>
										</div>
										<div class="payment-traveller">
											<?php
											foreach ( $traveller_fields as $field_group => $field ) :
												$field_name    = sprintf( '%s[%s][%d]', $field['name'], $cart_id, $i );
												$field['name'] = $field_name;
												$field['id']   = sprintf( '%s-%s-%d', $field['id'], $cart_id, $i );

												if ( ! $all_travelers_fields_require ) {
													// Set required false to extra travellers.
													$field['validations']['required'] = ! empty( $field['validations']['required'] ) ? $field['validations']['required'] : false;
													$field['validations']['required'] = $i > 0 ? false : $field['validations']['required'];
												}

												$form_field->init( array( $field ) )->render();
											endforeach;
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endfor; ?>
				</div>
			</div>
		</div>
		<?php
		if ( 'no' === $enable_multiple_travellers ) {
			break;} // Only add one travellers fields.
		?>
	<?php endforeach; ?>

	<?php do_action( 'wp_travel_action_before_billing_info_field' ); ?>
	<?php if ( is_array( $billing_fields ) && count( $billing_fields ) > 0 ) : ?>
		<!-- Billing info -->
		<div class="panel ws-theme-timeline-block">
			<div id="number-accordion3" class="panel-collapse collapse in">
				<div class="panel-body">
					<div class="payment-content">
						<?php $form_field->init( $billing_fields )->render(); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php do_action( 'wp_travel_action_before_payment_info_field' ); ?>
	<!-- Payment info -->
	<div class="panel ws-theme-timeline-block">
		<div id="number-accordion4" class="panel-collapse collapse in">
			<div class="panel-body">
				<div class="payment-content">
					<?php $form_field->init( $payment_fields )->render(); ?>
					<div class="wp-travel-form-field button-field">
						<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>
						<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="<?php esc_html_e( 'Book Now', 'wp-travel' ); ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action( 'wp_travel_action_after_payment_info_field' ); ?>
</form>
