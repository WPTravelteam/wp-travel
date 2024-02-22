<?php
/**
 * Checkout page Form.
 *
 * @package WP_Travel
 */

if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
}
global $wt_cart;

// Fields array.
$wptravel_checkout_fields              = wptravel_get_checkout_form_fields();
$wptravel_traveller_fields             = isset( $wptravel_checkout_fields['traveller_fields'] ) ? $wptravel_checkout_fields['traveller_fields'] : array();
$wptravel_billing_fields               = isset( $wptravel_checkout_fields['billing_fields'] ) ? $wptravel_checkout_fields['billing_fields'] : array();
$wptravel_payment_fields               = isset( $wptravel_checkout_fields['payment_fields'] ) ? $wptravel_checkout_fields['payment_fields'] : array();
$wptravel_settings                     = wptravel_get_settings();
$wptravel_enable_multiple_travellers   = isset( $wptravel_settings['enable_multiple_travellers'] ) && $wptravel_settings['enable_multiple_travellers'] ? esc_html( $wptravel_settings['enable_multiple_travellers'] ) : 'no';
$wptravel_all_travelers_fields_require = apply_filters( 'wp_travel_require_all_travelers_fields', false ); // @phpcs:ignore
$wptravel_form_fw                      = new WP_Travel_FW_Form();
$wptravel_form_field                   = new WP_Travel_FW_Field();
$wptravel_trips                        = $trips;
$wptravel_form_fw->init_validation( 'wp-travel-booking' );
?>
<form method="POST" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" class="wp-travel-booking" id="wp-travel-booking">
	<?php do_action( 'wp_travel_action_before_checkout_field' ); // @phpcs:ignore ?>
	<!-- Travelers info -->
	<?php
	foreach ( $wptravel_trips as $wptravel_cart_id => $wptravel_trip ) :
		$wptravel_trip_id   = $wptravel_trip['trip_id'];
		$wptravel_price_key = isset( $wptravel_trip['price_key'] ) ? $wptravel_trip['price_key'] : '';

		if ( wptravel_is_react_version_enabled() ) {
			$wptravel_pricing_id = $wptravel_trip['pricing_id'];
		} else {
			$wptravel_pricing_id = $wptravel_price_key;
		}

		$wptravel_pricing_name   = wptravel_get_trip_pricing_name( $wptravel_trip_id, $wptravel_pricing_id );
		$wptravel_repeator_count = isset( $wptravel_trip['pax'] ) ? $wptravel_trip['pax'] : 1;

		// New value @since 3.0.0.
		$wptravel_cart_trip = isset( $wptravel_trip['trip'] ) ? $wptravel_trip['trip'] : array();
		if ( is_array( $wptravel_cart_trip ) && count( $wptravel_cart_trip ) > 0 ) {
			$wptravel_repeator_count = 0;
			foreach ( $wptravel_cart_trip as $wptravel_category_id => $wptravel_category ) {
				$wptravel_repeator_count += isset( $wptravel_category['pax'] ) ? $wptravel_category['pax'] : 0;
			}
		}
		// endo of new.

		if ( 'no' === $wptravel_enable_multiple_travellers ) {
			$wptravel_repeator_count = 1;
		}
		?>
		<div class="wp-travel-trip-details">
			<?php if ( 'yes' === $wptravel_enable_multiple_travellers ) : ?>
				<div class="section-title text-left">
					<h3><?php echo esc_html( $wptravel_pricing_name ); ?><!-- <small> / 8 days 7 nights</small> --></h3>
				</div>
			<?php endif; ?>
			<div class="panel-group number-accordion">
				<div class="panel-heading">
					<h4 class="panel-title"><?php esc_html_e( 'Traveler Details', 'wp-travel' ); ?></h4>
				</div>
				<div class="ws-theme-timeline-block panel-group checkout-accordion" id="checkout-accordion-<?php echo esc_attr( $wptravel_cart_id ); ?>">
					<?php if ( $wptravel_repeator_count > 1 ) : ?>
						<div class="wp-collapse-open clearfix">
							<a href="#" class="open-all-link" style="display: none;"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
							<a href="#" class="close-all-link" style="display: block;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
						</div>
					<?php endif; ?>
					<?php

					for ( $i = 0; $i < $wptravel_repeator_count; $i++ ) : // @phpcs:ignore
						?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#checkout-accordion-<?php echo esc_attr( $wptravel_cart_id ); ?>" href="#collapse-<?php echo esc_attr( $wptravel_cart_id . '-' . $i ); ?>" aria-expanded="true">
										<?php
										$wptravel_collapse      = 'collapse in';
										$wptravel_area_expanded = 'true';
										if ( 0 === $i ) :
											esc_html_e( 'Lead Traveler', 'wp-travel' );
											else :
												$wptravel_traveler_index = $i + 1;
												/**
												 * Translators: %d placeholder is used to show number of traveler except lead traveler.
												 */
												echo sprintf( __( 'Traveler %d', 'wp-travel' ), $wptravel_traveler_index ); // @phpcs:ignore
											endif;
											?>
										<span class="collapse-icon"></span>
									</a>
								</h4>
							</div>
							<div id="collapse-<?php echo esc_attr( $wptravel_cart_id . '-' . $i ); ?>" class="panel-collapse <?php echo esc_attr( $wptravel_collapse ); ?>" aria-expanded="<?php echo esc_attr( $wptravel_area_expanded ); ?>">
								<div class="panel-body">
									<div class="payment-content">
										<div class="row gap-0">
											<div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
												<h6 class="heading mt-0 mb-15"></h6>
											</div>
										</div>
										<div class="payment-traveller">
											<?php
											if ( 0 === $i ) {
												
												foreach ( $wptravel_traveller_fields as $wptravel_field_group => $wptravel_field ) :
													$wptravel_field_name    = sprintf( '%s[%s][%d]', $wptravel_field['name'], $wptravel_cart_id, $i ); // @phpcs:ignore
													$wptravel_field['name'] = $wptravel_field_name;
													$wptravel_field['id']   = sprintf( '%s-%s-%d', $wptravel_field['id'], $wptravel_cart_id, $i ); // @phpcs:ignore
													if ( $i > 0 ) {
														$wptravel_field['default'] = ''; // make empty default if other than lead traveler.
													}
													if( isset($wptravel_settings['enable_multiple_checkout']) && $wptravel_settings['enable_multiple_checkout'] == 'no' && isset( $wptravel_field['validations']['specific_trip'] ) && $wptravel_field['validations']['specific_trip'] == '1'  ){
														if( in_array( $wptravel_trip_id, explode( ',', $wptravel_field['trip_ids'] ) ) ){
															if ( ! $wptravel_all_travelers_fields_require && $wptravel_field['validations']['specific_trip'] !== '1' ) {
																// Added to control over required fields for travellers @since 3.1.3.
																if ( isset( $wptravel_field['validations']['required_for_all'] ) && $wptravel_field['validations']['required_for_all'] ) {
																	// if ( $wptravel_field['validations']['required'] ) {
																		$wptravel_field['validations']['required'] = $i == 0 ? true : $wptravel_field['validations']['required'];
																	// }
																} else {
																	// Set required false to extra travellers.
																	$wptravel_field['validations']['required'] = ! empty( $wptravel_field['validations']['required'] ) ? $wptravel_field['validations']['required'] : false;
																	$wptravel_field['validations']['required'] = $i > 0 ? false : $wptravel_field['validations']['required'];
																}
															}
															$wptravel_form_field->init( array( $wptravel_field ) )->render( $wptravel_trips );
														}
													}else{
														if ( ! $wptravel_all_travelers_fields_require ) {
															// Added to control over required fields for travellers @since 3.1.3.
															if ( isset( $wptravel_field['validations']['required_for_all'] ) && $wptravel_field['validations']['required_for_all'] ) {
																// if ( $wptravel_field['validations']['required'] ) {
																	$wptravel_field['validations']['required'] = $i == 0 ? true : $wptravel_field['validations']['required'];
																// }
															} else {
																// Set required false to extra travellers.
																$wptravel_field['validations']['required'] = ! empty( $wptravel_field['validations']['required'] ) ? $wptravel_field['validations']['required'] : false;
																$wptravel_field['validations']['required'] = $i > 0 ? false : $wptravel_field['validations']['required'];
															}
														}

														$wptravel_form_field->init( array( $wptravel_field ) )->render( $wptravel_trips );
													}
													

													// $wptravel_form_field->init( array( $wptravel_field ) )->render( $wptravel_trips );
												endforeach;
											} else {
												foreach ( $wptravel_traveller_fields as $wptravel_field_group => $wptravel_field ) :
													$field_remove = false;
													foreach ( $wptravel_field as $field_key => $field_value ) {
														if ( $field_key == 'remove_field' ) {
															if ( $field_value == true ) {
																$field_remove = true;
															}
														}
													}
													if ( $field_remove == false ) {
														$wptravel_field_name    = sprintf( '%s[%s][%d]', $wptravel_field['name'], $wptravel_cart_id, $i ); // @phpcs:ignore
														$wptravel_field['name'] = $wptravel_field_name;
														$wptravel_field['id']   = sprintf( '%s-%s-%d', $wptravel_field['id'], $wptravel_cart_id, $i ); // @phpcs:ignore
														if ( $i > 0 ) {
															$wptravel_field['default'] = ''; // make empty default if other than lead traveler.
														}
														if( isset($wptravel_settings['enable_multiple_checkout']) && $wptravel_settings['enable_multiple_checkout'] == 'no' && isset( $wptravel_field['validations']['specific_trip'] ) && $wptravel_field['validations']['specific_trip'] == '1'  ){
															if( in_array( $wptravel_trip_id, explode( ',', $wptravel_field['trip_ids'] ) ) ){
																if ( ! $wptravel_all_travelers_fields_require && $wptravel_field['validations']['specific_trip'] !== '1' ) {
																	// Added to control over required fields for travellers @since 3.1.3.
																	if ( isset( $wptravel_field['validations']['required_for_all'] ) && $wptravel_field['validations']['required_for_all'] ) {
																		// if ( $wptravel_field['validations']['required'] ) {
																			$wptravel_field['validations']['required'] = $i == 0 ? true : $wptravel_field['validations']['required'];
																		// }
																	} else {
																		// Set required false to extra travellers.
																		$wptravel_field['validations']['required'] = ! empty( $wptravel_field['validations']['required'] ) ? $wptravel_field['validations']['required'] : false;
																		$wptravel_field['validations']['required'] = $i > 0 ? false : $wptravel_field['validations']['required'];
																	}
																}
																$wptravel_form_field->init( array( $wptravel_field ) )->render( $wptravel_trips );
															}
														}else{
															if ( ! $wptravel_all_travelers_fields_require ) {
																// Added to control over required fields for travellers @since 3.1.3.
																if ( isset( $wptravel_field['validations']['required_for_all'] ) && $wptravel_field['validations']['required_for_all'] ) {
																	// if ( $wptravel_field['validations']['required'] ) {
																		$wptravel_field['validations']['required'] = $i == 0 ? true : $wptravel_field['validations']['required'];
																	// }
																} else {
																	// Set required false to extra travellers.
																	$wptravel_field['validations']['required'] = ! empty( $wptravel_field['validations']['required'] ) ? $wptravel_field['validations']['required'] : false;
																	$wptravel_field['validations']['required'] = $i > 0 ? false : $wptravel_field['validations']['required'];
																}
															}
	
															$wptravel_form_field->init( array( $wptravel_field ) )->render( $wptravel_trips );
														}
													}
												endforeach;
											}
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
		if ( 'no' === $wptravel_enable_multiple_travellers ) {
			break;} // Only add one travellers fields.
		?>
	<?php endforeach; ?>

	<?php do_action( 'wp_travel_action_before_billing_info_field' ); // @phpcs:ignore ?>
	<?php if ( is_array( $wptravel_billing_fields ) && count( $wptravel_billing_fields ) > 0 ) : ?>
		<!-- Billing info -->
		<div class="panel ws-theme-timeline-block">
			<!-- <div id="number-accordion3" class="panel-collapse collapse in"> -->
				<div class="panel-body">
					<div class="payment-content">
						<?php $wptravel_form_field->init( $wptravel_billing_fields )->render( $wptravel_trips ); ?>
					</div>
				</div>
			<!-- </div> -->
		</div>
	<?php endif; ?>
	<?php do_action( 'wp_travel_action_before_payment_info_field' ); // @phpcs:ignore ?>
	<!-- Payment info -->
	<div class="panel ws-theme-timeline-block">
		<!-- <div id="number-accordion4" class="panel-collapse collapse in"> -->
			<div class="panel-body">
				<div class="payment-content">
					<?php $wptravel_form_field->init( $wptravel_payment_fields )->render( $wptravel_trips ); ?>
					<?php
					/**
					 * Before Booknow button on checkout page.
					 *
					 * @since 4.3.0
					 */
					do_action( 'wp_travel_action_before_book_now' ); // @phpcs:ignore
					?>
					<div class="wp-travel-form-field button-field">
						<?php
						WP_Travel::create_nonce_field();
						?>
						<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="<?php esc_html_e( 'Book Now', 'wp-travel' ); ?>">
					</div>
				</div>
			</div>
		<!-- </div> -->
	</div>
	<?php do_action( 'wp_travel_action_after_payment_info_field' ); // @phpcs:ignore ?>
</form>
