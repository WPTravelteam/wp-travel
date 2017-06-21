<?php

function wp_travel_booking_form_fields(){
	return apply_filters( 'wp_travel_booking_form_fields', 
		array(
		'first_name'	=>array('type'=>'text','label'=>'First Name','name'=>'first_name'),
		'middle_name'	=>array('type'=>'text','label'=>'Middle Name','name'=>'middle_name'),
		'last_name'		=>array('type'=>'text','label'=>'Last Name','name'=>'last_name'),
		// 'country'		=>array('type'=>'select','label'=>'Country','name'=>'country' , 'options' => wp_travel_get_countries() ),
		'address'		=>array('type'=>'text','label'=>'Address','name'=>'address'),
		'phone_number'	=>array('type'=>'text','label'=>'Phone Number','name'=>'phone_number'),
		'email'			=>array('type'=>'email','label'=>'Email','name'=>'email'),
		'pax'			=>array('type'=>'number','label'=>'Pax','name'=>'pax'),
	) );
}


function wp_travel_get_booking_form() {

	global $post;
	ob_start(); ?>
	
	<div class="wp-travel-booking-form-wrapper">
		<form action="" method="post">
			<?php do_action( 'wp_travel_booking_before_form_field' ); ?>
			<input type="hidden" name="wp_travel_post_id" value="<?php echo $post->ID; ?>">
			<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>

			<div class="wp-travel-form-field">
				<label for="wp-trevel-fname"><?php esc_html_e( 'First Name' ); ?></label>
				<input type="text" id="wp-trevel-fname" name="wp_travel_fname">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-trevel-mname"><?php esc_html_e( 'Middle Name' ); ?></label>
				<input type="text" id="wp-trevel-mname" name="wp_travel_mname">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-trevel-lname"><?php esc_html_e( 'Last Name' ); ?></label>
				<input type="text" id="wp-trevel-lname" name="wp_travel_lname">
			</div>

			<div class="wp-travel-form-field">
				<label for="wp-trevel-country"><?php esc_html_e( 'Country' ); ?></label>
				<?php $countries = wp_travel_get_countries(); ?>
				<?php if ( count( $countries ) > 0 ) : ?>
				<select id="wp-trevel-country" name="wp_travel_country">
					<?php foreach ( $countries as $short_name => $name ) : ?>
						<option value="<?php esc_html_e( $short_name, 'wp-travel' ) ?>"><?php esc_html_e( $name, 'wp-travel' ) ?></option>
					<?php endforeach; ?>			      
			    </select>
			    <?php endif; ?>
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-address"><?php esc_html_e( 'Address' ); ?></label>
				<input type="text" id="wp-travel-address" name="wp_travel_address">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-phone"><?php esc_html_e( 'Phone' ); ?></label>
				<input type="number" id="wp-travel-phone" name="wp_travel_phone">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-email"><?php esc_html_e( 'Email' ); ?></label>
				<input type="email" id="wp-travel-email" name="wp_travel_email">
			</div>
	        <?php echo apply_filters('wp_travel_booking_form_after_email_html_content',''); ?>
			<div class="wp-travel-form-field">
				<label for="wp-travel-pax"><?php esc_html_e( 'No of PAX' ); ?></label>
				<input type="number" max="<?php echo apply_filters( 'wp_travel_max_pax_number', 100000 ); ?> " id="wp-travel-pax" name="wp_travel_pax">
			</div>
			<div class="wp-travel-form-field textarea-field">
				<label for="wp-travel-note"><?php esc_html_e( 'Note' ); ?></label>
				<textarea name="wp_travel_note" id="wp-travel-note" placeholder="<?php esc_html_e( 'Some text...' ); ?>" rows="6" cols="150"></textarea>
			</div>
			
			<div class="wp-travel-form-field button-field">
				<?php do_action( 'wp_travel_booking_before_submit_button' ); ?>
				<input type="hidden" name="wp_travel_post_id" value="<?php echo $post->ID; ?>" >
				<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book Now">
				
				<?php do_action( 'wp_travel_booking_after_submit_button' ); ?>
			</div>
			<?php do_action( 'wp_travel_booking_after_form_field' ); ?>
		</form>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return apply_filters( 'wp_travel_booking_form_contents', $content );
}
