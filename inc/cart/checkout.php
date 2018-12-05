<?php

// Fields array.
$checkout_fields    = wp_travel_get_checkout_form_fields();
$traveller_fields   = $checkout_fields['traveller_fields'];
$billing_fields     = $checkout_fields['billing_fields'];
$payment_fields     = $checkout_fields['payment_fields'];

// GDPR Support
$settings = wp_travel_get_settings();

	$gdpr_msg = isset( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ): __( 'By contacting us, you agree to our ', 'wp-travel' );

	$privacy_policy_url = false;

	if ( function_exists( 'get_privacy_policy_url' ) ) {

		$privacy_policy_url = get_privacy_policy_url();

	}

if ( function_exists( 'get_the_privacy_policy_link' ) && ! empty( $gdpr_msg ) && $privacy_policy_url ) {

	// GDPR Compatibility for enquiry.
	$billing_fields['wp_travel_checkout_gdpr'] = array(
		'type' => 'checkbox',
		'label' => __('Privacy Policy', 'wp-travel'),
		'options' => array( 'gdpr_agree' => sprintf( '%1s %2s', $gdpr_msg, get_the_privacy_policy_link() ) ),
		'name' => 'wp_travel_checkout_gdpr_msg',
		'id' => 'wp-travel-enquiry-gdpr-msg',
		'validations' => array(
			'required' => true,
		),
		'option_attributes' => array(
			'required' => true,
		),
		'priority' => 100,
	);

}

global $wt_cart;
$form_field = new WP_Travel_FW_Field(); ?>
<form method="POST" action="" class="wp-travel-booking" id="wp-travel-booking">
	<?php do_action( 'wp_travel_action_before_checkout_field' ); ?>
	<!-- Travellers info -->
	<?php foreach( $trips as $cart_id => $trip ) : ?>
		<div class="wp-travel-trip-details">
			<div class="section-title text-left">
				<h3><?php echo esc_html( get_the_title( $trip['trip_id'] ) ) ?><!-- <small> / 8 days 7 nights</small> --></h3>
			</div>
			<div class="panel-group number-accordion">
				<div class="panel-heading">										
					<h4 class="panel-title"><?php esc_html_e( 'Traveller Details', 'wp-travel' ) ?></h4>
				</div>
				<div class="ws-theme-timeline-block panel-group checkout-accordion" id="checkout-accordion-<?php echo esc_attr( $cart_id ) ?>">
					<?php
					$no_of_travellers = isset( $trip['pax'] ) ? $trip['pax']  : 1;
					for ( $i = 0; $i < $no_of_travellers; $i++ ) : ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle" data-toggle="collapse" data-parent="#checkout-accordion-<?php echo esc_attr( $cart_id ) ?>" href="#collapse-<?php echo esc_attr( $cart_id . '-' . $i ); ?>" aria-expanded="true">
										<?php
											if ( 0 === $i ) : 
												esc_html_e( 'Lead Traveller', 'wp-travel' );
												$collapse = 'collapse in';
												$area_expanded = 'true';
											else :
												echo esc_html( __( sprintf( 'Traveller %d', ( $i + 1 ) ), 'wp-travel' ) );
												$collapse = 'collapse';
												$area_expanded = 'false';
											endif; ?>
										<span class="collapse-icon"></span>
									</a>
								</h4>
							</div>
							<div id="collapse-<?php echo esc_attr( $cart_id . '-' . $i ); ?>" class="panel-collapse <?php echo esc_attr( $collapse ) ?>" aria-expanded="<?php echo esc_attr( $area_expanded ) ?>">
								<div class="panel-body">
									<div class="payment-content">
										<div class="row gap-0">
											<div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
												<h6 class="heading mt-0 mb-15">
													
												</h6>
											</div>
										</div>
										<div class="payment-traveller">
										
											<?php foreach( $traveller_fields as $field_group => $field ) :
												$field_name = sprintf( '%s[%s][%d]', $field['name'], $cart_id, $i );
												$field['name'] = $field_name;
												$field['id'] = $field['id'] . '-' . $i;
												
												if ( $i > 0 ) { // Set required false to extra travellers.
													unset( $field['validations']['required'] );
												}
												if ( 'hidden' === $field['type'] ) {
													echo $form_field->init()->render_input( $field );
													continue;
												}
												$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>
												<div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
													<div class="form-group gap-20">
														<label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
														<div class="col-sm-8 col-md-9">
															<?php echo $form_field->init()->render_input( $field ); ?>
														</div>
													</div>
												</div>
											<?php endforeach; ?>                                    
										</div>														
									</div>								
								</div>
							</div>
						</div>
					<?php endfor; ?> 	
				</div>                                        
			</div>
		</div>
				
    <?php endforeach; ?>	
	
	<?php do_action( 'wp_travel_action_before_billing_info_field' ); ?>
	<!-- Billing info -->
	<div class="panel ws-theme-timeline-block">
		<div class="panel-heading">
		
		<h4 class="panel-title"><?php esc_html_e( 'Billing Address', 'wp-travel' ); ?></h4>
		</div>
		<div id="number-accordion3" class="panel-collapse collapse in">
		<div class="panel-body">
			<div class="payment-content">
				<?php foreach( $billing_fields as $field_group => $field ) : ?>
					<?php
					if ( 'hidden' === $field['type'] ) {
						echo $form_field->init()->render_input( $field );
						continue;
					}
					$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>               
					<div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
						<div class="form-group gap-20">
							<label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
							<div class="col-sm-8 col-md-9">
								<?php echo $form_field->init()->render_input( $field ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		</div>
	</div>
	<?php do_action( 'wp_travel_action_before_payment_info_field' ); ?>
	<!-- Payment info -->
	<div class="panel ws-theme-timeline-block">
		<div class="panel-heading">
		
		<h4 class="panel-title"><?php esc_html_e( 'Booking / Payments', 'wp-travel' ); ?></h4>
		</div>
		<div id="number-accordion4" class="panel-collapse collapse in">
		<div class="panel-body">           
			<div class="payment-content">
				<?php foreach( $payment_fields as $field_group => $field ) : ?>
					<?php if ( 'hidden' === $field['type'] ) {
						echo $form_field->init()->render_input( $field );
						continue;
					}
					$wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>
					<div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
						<div class="form-group gap-20">
							<label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
							<div class="col-sm-8 col-md-9">
								<?php echo $form_field->init()->render_input( $field ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>  
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

<script type="text/javascript">
	
	function sidebarSticky(){
		var interval = setInterval(function(){
			if (Modernizr.mq('(min-width: 768px)')) {
			   jQuery(".container .sticky-sidebar").stick_in_parent({
				container: jQuery(".container"),
				parent: ".container",
				offset_top:50
			  });
			}
		},1000)
	}
	jQuery(document).ready(sidebarSticky);
	jQuery(window).resize(sidebarSticky);
</script>
