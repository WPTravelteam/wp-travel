<?php
/**
 * Booking Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	return apply_filters( 'wp_travel_booking_form_fields',
		array(
		'first_name'	=> array( 'type' => 'text', 'label' => 'First Name', 'name' => 'first_name' ),
		'middle_name'	=> array( 'type' => 'text', 'label' => 'Middle Name', 'name' => 'middle_name' ),
		'last_name'		=> array( 'type' => 'text', 'label' => 'Last Name', 'name' => 'last_name' ),
		// 'country'		=> array( 'type'=> 'select', 'label'=>'Country','name'=>'country' , 'options' => wp_travel_get_countries() ),
		'address'		=> array( 'type' => 'text', 'label' => 'Address', 'name' => 'address' ),
		'phone_number'	=> array( 'type' => 'text', 'label' => 'Phone Number', 'name' => 'phone_number' ),
		'email'			=> array( 'type' => 'email', 'label' => 'Email', 'name' => 'email' ),
		'pax'			=> array( 'type' => 'number', 'label' => 'Pax', 'name' => 'pax' ),
		)
	);
}

/**
 * Return HTM of Booking Form
 *
 * @return [type] [description]
 */
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
						<option value="<?php echo esc_html( $short_name, 'wp-travel' ) ?>"><?php echo esc_html( $name, 'wp-travel' ) ?></option>
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
	        <?php echo apply_filters( 'wp_travel_booking_form_after_email_html_content', '' ); ?>
			<div class="wp-travel-form-field">
				<label for="wp-travel-pax"><?php esc_html_e( 'No of PAX' ); ?></label>
				<input type="number" max="<?php echo apply_filters( 'wp_travel_pax', 100000 ); ?> " id="wp-travel-pax" name="wp_travel_pax">
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

add_action( 'add_meta_boxes', 'wp_travel_register_booking_metaboxes', 10, 2 );

/**
 * This will add metabox in booking post type.
 */
function wp_travel_register_booking_metaboxes() {
	add_meta_box( 'wp-travel-booking-info', __( 'Booking Detail' ), 'wp_travel_booking_info', 'itinerary-booking', 'normal', 'default' );
}

/**
 * Call back for booking metabox.
 *
 * @param Object $post Post object.
 */
function wp_travel_booking_info( $post ) {
	if ( ! $post ) {
		return;
	}
	$wp_travel_fname = get_post_meta( $post->ID, 'wp_travel_fname', true );
	$wp_travel_mname = get_post_meta( $post->ID, 'wp_travel_mname', true );
	$wp_travel_lname = get_post_meta( $post->ID, 'wp_travel_lname', true );
	$wp_travel_country = get_post_meta( $post->ID, 'wp_travel_country', true );
	$wp_travel_address = get_post_meta( $post->ID, 'wp_travel_address', true );
	$wp_travel_phone = get_post_meta( $post->ID, 'wp_travel_phone', true );
	$wp_travel_email = get_post_meta( $post->ID, 'wp_travel_email', true );
	$wp_travel_pax = get_post_meta( $post->ID, 'wp_travel_pax', true );
	$wp_travel_note = get_post_meta( $post->ID, 'wp_travel_note', true );
?>
	<div class="wp-travel-booking-form-wrapper">
		<form action="" method="post">
			<?php do_action( 'wp_travel_booking_before_form_field' ); ?>			
			<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>

			<div class="wp-travel-form-field">
				<label for="wp-trevel-fname"><?php esc_html_e( 'First Name' ); ?></label>
				<input type="text" id="wp-trevel-fname" name="wp_travel_fname" value="<?php _e( $wp_travel_fname, 'wp-travel' ); ?>" >
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-trevel-mname"><?php esc_html_e( 'Middle Name' ); ?></label>
				<input type="text" id="wp-trevel-mname" name="wp_travel_mname"  value="<?php _e( $wp_travel_mname, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-trevel-lname"><?php esc_html_e( 'Last Name' ); ?></label>
				<input type="text" id="wp-trevel-lname" name="wp_travel_lname"  value="<?php _e( $wp_travel_lname, 'wp-travel' ); ?>">
			</div>

			<div class="wp-travel-form-field">
				<label for="wp-trevel-country"><?php esc_html_e( 'Country' ); ?></label>
				<?php $countries = wp_travel_get_countries(); ?>
				<?php if ( count( $countries ) > 0 ) : ?>
				<select id="wp-trevel-country" name="wp_travel_country">
					<?php foreach ( $countries as $short_name => $name ) : ?>
						<option <?php selected( $wp_travel_country, $short_name ); ?> value="<?php esc_html_e( $short_name, 'wp-travel' ) ?>"><?php esc_html_e( $name, 'wp-travel' ) ?></option>
					<?php endforeach; ?>			      
			    </select>
			    <?php endif; ?>
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-address"><?php esc_html_e( 'Address' ); ?></label>
				<input type="text" id="wp-travel-address" name="wp_travel_address"  value="<?php _e( $wp_travel_address, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-phone"><?php esc_html_e( 'Phone' ); ?></label>
				<input type="number" id="wp-travel-phone" name="wp_travel_phone"  value="<?php _e( $wp_travel_phone, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field">
				<label for="wp-travel-email"><?php esc_html_e( 'Email' ); ?></label>
				<input type="email" id="wp-travel-email" name="wp_travel_email"  value="<?php _e( $wp_travel_email, 'wp-travel' ); ?>">
			</div>
	        <?php echo apply_filters('wp_travel_booking_form_after_email_html_content',''); ?>
			<div class="wp-travel-form-field">
				<label for="wp-travel-pax"><?php esc_html_e( 'No of PAX' ); ?></label>
				<input type="number" max="<?php echo apply_filters( 'wp_travel_max_pax_number', 100000 ); ?> " id="wp-travel-pax" name="wp_travel_pax"  value="<?php _e( $wp_travel_pax, 'wp-travel' ); ?>">
			</div>
			<div class="wp-travel-form-field textarea-field">
				<label for="wp-travel-note"><?php esc_html_e( 'Note' ); ?></label>
				<textarea name="wp_travel_note" id="wp-travel-note" placeholder="<?php esc_html_e( 'Some text...' ); ?>" rows="6" cols="150"><?php _e( $wp_travel_note, 'wp-travel' ); ?></textarea>
			</div>			
			<?php do_action( 'wp_travel_booking_after_form_field' ); ?>
		</form>
	</div>

<?php
}


/**
 * Save Post meta data.
 *
 * @param  int $post_id ID of current post.
 *
 * @return Mixed
 */
function wp_travel_save_booking_data( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$wp_travel_fname = sanitize_text_field( $_POST['wp_travel_fname'] );
	$wp_travel_mname = sanitize_text_field( $_POST['wp_travel_mname'] );
	$wp_travel_lname = sanitize_text_field( $_POST['wp_travel_lname'] );
	$wp_travel_country = sanitize_text_field( $_POST['wp_travel_country'] );
	$wp_travel_address = sanitize_text_field( $_POST['wp_travel_address'] );
	$wp_travel_phone = sanitize_text_field( $_POST['wp_travel_phone'] );
	$wp_travel_email = sanitize_text_field( $_POST['wp_travel_email'] );
	$wp_travel_pax = sanitize_text_field( $_POST['wp_travel_pax'] );
	$wp_travel_note = sanitize_text_field( $_POST['wp_travel_note'] );

	update_post_meta( $post_id, 'wp_travel_fname', sanitize_text_field( $wp_travel_fname ) );
	update_post_meta( $post_id, 'wp_travel_mname', sanitize_text_field( $wp_travel_mname ) );
	update_post_meta( $post_id, 'wp_travel_lname', sanitize_text_field( $wp_travel_lname ) );
	update_post_meta( $post_id, 'wp_travel_country', sanitize_text_field( $wp_travel_country ) );
	update_post_meta( $post_id, 'wp_travel_address', sanitize_text_field( $wp_travel_address ) );
	update_post_meta( $post_id, 'wp_travel_phone', sanitize_text_field( $wp_travel_phone ) );
	update_post_meta( $post_id, 'wp_travel_email', sanitize_text_field( $wp_travel_email ) );
	update_post_meta( $post_id, 'wp_travel_pax', sanitize_text_field( $wp_travel_pax ) );
}

add_action( 'save_post', 'wp_travel_save_booking_data' );
