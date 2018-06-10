<?php

// Fields array.
$checkout_fields    = wp_travel_get_checkout_form_fields();
$traveller_fields   = $checkout_fields['traveller_fields'];
$billing_fields     = $checkout_fields['billing_fields'];
$payment_fields     = $checkout_fields['payment_fields'];

global $wt_cart;
$form_field = new WP_Travel_FW_Field(); ?>
<form method="POST" action="" class="wp-travel-booking" id="wp-travel-booking">
	<!-- Travellers info -->  
	<?php foreach( $trips as $cart_id => $trip ) : ?>
		<div class="panel wp-travel-trip-details">
			<div class="section-title text-left">
				<h3><?php echo esc_html( 'Booking Details', 'wp-travel' ); ?></h3>
			</div>
			<p><?php esc_html_e( 'Please fill in your details below to book your trip(s) !', 'wp-travel' ); ?></p>
			
			<div class="panel-group number-accordion">
				<div class="panel ws-theme-timeline-block">
					<div class="panel-heading">										
						<h4 class="panel-title"><?php esc_html_e( 'Traveller Details', 'wp-travel' ) ?></h4>
					</div>
					<div id="number-accordion2" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="payment-content">
								<div class="payment-traveller">
									<?php foreach( $traveller_fields as $field_group => $field ) : 

										$wp_travel_get_multi_traveller_info = apply_filters( 'wp_travel_get_multi_traveller_info', false );
										
										if ( $wp_travel_get_multi_traveller_info ) {

											$field_name = sprintf( '%s[%s][]', $field['name'], $cart_id );
											$field['name'] = $field_name;

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
			</div>
		</div>
	<?php endforeach; ?>

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
					<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book and Pay"> 
				</div>
			</div>        
		</div>
		</div>
	</div>
</form>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sticky-kit/1.1.3/sticky-kit.min.js"></script>
<script type="text/javascript">
	
	function sidebarSticky(){
		var interval = setInterval(function(){
			if (Modernizr.mq('(min-width: 768px)')) {
			   jQuery(".container .sticky-sidebar").stick_in_parent({
				container: jQuery(".container"),
				parent: ".container",
				offset_top:120
			  });
			}else{
				$(".container .sticky-sidebar").trigger("sticky_kit:detach");
			}
		},1000)
	}
	jQuery(document).ready(sidebarSticky);
	jQuery(window).resize(sidebarSticky);
</script>