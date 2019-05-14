<?php
/**
 * Callback for Misc Options Tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_misc_options_global( $tab, $args ) {
	$settings                   = $args['settings'];
		$enable_trip_enquiry_option = $settings['enable_trip_enquiry_option'];
		$enable_og_tags             = $settings['enable_og_tags'];
		$wp_travel_gdpr_message     = $settings['wp_travel_gdpr_message'];
		$open_gdpr_in_new_tab       = $settings['open_gdpr_in_new_tab'];
		
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
			<h3><?php echo esc_html( 'Currency Exchange Rate API', 'wp-travel' ); ?></h3>
			<?php
			$upsell_args = array(
				'title'      => __( 'Display current exchange rate in your site.', 'wp-travel' ),
				'content'    => sprintf( __( 'You can display current exchange rate for different currency in pages or sidebar of your site. Checkout out %sWP Travel PRO%s or %sWP Travel Currency Exchange Rates%s.', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="__blank">', '</a>', '<a href="https://wptravel.io/downloads/" target="__blank">', '</a>' ),
				'link'       => '',
        		'link_label' => '',
				'link2'       => '',
				'link2_label' => '',
				'main_wrapper_class' => array( 'wp-travel-upsell-message-normal' ),
			);
			wp_travel_upsell_message( $upsell_args );
		endif;
		echo '<br>';
		if ( ! class_exists( 'WP_Travel_Mailchimp_Core' ) ) :
			?>
			<h3><?php echo esc_html( 'Mailchimp Settings', 'wp-travel' ); ?></h3>
			<?php
			$upsell_args = array(
				'title'      => __( 'Using Mailchimp for email marketing?', 'wp-travel' ),
				'content'    => sprintf( __( 'You can import customer email from booking and inquiry to Mailchimp. That help you grow your business. Checkout out %sWP Travel PRO%s or %sWP Travel Mailchimp%s.', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="__blank">', '</a>', '<a href="https://wptravel.io/downloads/wp-travel-mailchimp/" target="__blank">', '</a>' ),
				'link'       => '',
        		'link_label' => '',
				'link2'       => '',
				'link2_label' => '',
				'main_wrapper_class' => array( 'wp-travel-upsell-message-normal' ),
			);
			wp_travel_upsell_message( $upsell_args );
		endif;

		do_action( 'wp_travel_settings_tab_misc_options_fields', $args );
		

		
		if ( ! class_exists( 'WP_Travel_Wishlists_Core' ) ) :
			?>
			<h3><?php echo esc_html( 'Wishlists Options', 'wp-travel' ); ?></h3>
			<?php
			$upsell_args = array(
				'title'      => __( 'Allow customers to save trip for future.', 'wp-travel' ),
				'content'    => sprintf( __( 'Whishlists helps user to save trip they like for future, so that they can book them later. Checkout out %sWP Travel PRO%s or %sWP Travel Wishlists%s.', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="__blank">', '</a>', '<a href="https://wptravel.io/downloads/wp-travel-wishlists/" target="__blank">', '</a>' ),
				'link'       => '',
        		'link_label' => '',
				'link2'       => '',
				'link2_label' => '',
				'main_wrapper_class' => array( 'wp-travel-upsell-message-normal' ),
			);
			wp_travel_upsell_message( $upsell_args );
		endif;
}
