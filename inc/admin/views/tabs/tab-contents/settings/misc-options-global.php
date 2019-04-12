<?php
/**
 * Callback for Misc Options Tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_misc_options_global( $tab, $args ) {
	$settings                   = $args['settings'];
	$enable_trip_enquiry_option = $settings['enable_trip_enquiry_option'];
	$enable_og_tags             = $settings['enable_og_tags'];
	$wp_travel_gdpr_message     = $settings['wp_travel_gdpr_message'];
	$open_gdpr_in_new_tab       = $settings['open_gdpr_in_new_tab'];

	
	if ( ! class_exists( 'WP_Travel_Wishlists_Core' ) ) :
		?>
		<h3 class="wp-travel-tab-content-title"><?php echo esc_html( 'Wishlists Options', 'wp-travel' ); ?></h3>
		<?php
		$args = array(
			'title'      => __( 'Allow customers to save trip for future.', 'wp-travel' ),
			'content'    => __( 'Whishlists helps user to save trip they like for future, so that they can book them later.!', 'wp-travel' ),
			'link'       => 'https://wptravel.io/downloads/wp-travel-wishlists/',
			'link_label' => __( 'Get WP Travel Wishlists', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
	
	?>
	<table class="form-table">
		<tr>
			<th>
				<label for="enable_trip_enquiry_option"><?php esc_html_e( 'Enable Trip Enquiry', 'wp-travel' ); ?></label>
			</th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="enable_trip_enquiry_option" type="hidden" />
						<input <?php checked( $enable_trip_enquiry_option, 'yes' ); ?> value="yes" name="enable_trip_enquiry_option" id="enable_trip_enquiry_option" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
			</td>
		<tr>
		<tr>
			<th>
				<label for="enable_og_tags"><?php esc_html_e( 'Enable OG Tags', 'wp-travel' ); ?></label>
			</th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="enable_og_tags" type="hidden" />
						<input <?php checked( $enable_og_tags, 'yes' ); ?> value="yes" name="enable_og_tags" id="enable_og_tags" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
			</td>
		<tr>
		<tr>
			<th>
				<label for="wp_travel_gdpr_message"><?php _e( 'GDPR Message : ', 'wp-travel' ); ?></label>
			</th>
			<td>
				<textarea rows="4" cols="30" id="wp_travel_gdpr_message" name="wp_travel_gdpr_message"><?php echo $wp_travel_gdpr_message; ?></textarea>
			</td>
		</tr>
		<tr>
			<th>
				<label for="open_gdpr_in_new_tab"><?php _e( 'Open GDPR in new tab: ', 'wp-travel' ); ?></label>
			</th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
					<input value="no" name="open_gdpr_in_new_tab" type="hidden" />
						<input <?php checked( $open_gdpr_in_new_tab, 'yes' ); ?> value="yes" name="open_gdpr_in_new_tab" id="open_gdpr_in_new_tab" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
			</td>
		</tr>
	</table>
	<?php
	if ( ! class_exists( 'WP_Travel_Currency_Exchange_Rates_Core' ) ) :
		?>
		<h3 class="wp-travel-tab-content-title"><?php echo esc_html( 'Currency Exchange Rate API', 'wp-travel' ); ?></h3>
		<?php
		$args = array(
			'title'      => __( 'Display current exchange rate in your site.', 'wp-travel' ),
			'content'    => __( 'You can display current exchange rate for different currency in pages or sidebar of your site.!', 'wp-travel' ),
			'link'       => 'https://wptravel.io/downloads/',
			'link_label' => __( 'Get WP Travel Currency Exchange Rates', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
	echo '<br>';
	if ( ! class_exists( 'WP_Travel_Mailchimp_Core' ) ) :
		?>
		<h3 class="wp-travel-tab-content-title"><?php echo esc_html( 'Mailchimp Settings', 'wp-travel' ); ?></h3>
		<?php
		$args = array(
			'title'      => __( 'Using Mailchimp for email marketing ?', 'wp-travel' ),
			'content'    => __( 'You can import customer email from booking and inquiry to Mailchimp. That help you grow your business.', 'wp-travel' ),
			'link'       => 'https://wptravel.io/downloads/wp-travel-mailchimp/',
			'link_label' => __( 'Get WP Travel Mailchimp', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;

	do_action( 'wp_travel_settings_tab_misc_options_fields', $args );
}
