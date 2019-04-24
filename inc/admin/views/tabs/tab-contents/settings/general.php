<?php

/**
 * Callback for General tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_general( $tab, $args ) {
	$settings = $args['settings'];

		$currency_list         = wp_travel_get_currency_list();
		$currency              = $settings['currency'];
		$google_map_api_key    = $settings['google_map_api_key'];
		$google_map_zoom_level = $settings['google_map_zoom_level'];

		// Pages.
		$selected_cart_page      = $settings['cart_page_id'];
		$selected_checkout_page  = $settings['checkout_page_id'];
		$selected_dashboard_page = $settings['dashboard_page_id'];

		$currency_args = array(
			'id'         => 'currency',
			'class'      => 'currency wp-travel-select2',
			'name'       => 'currency',
			'selected'   => $currency,
			'option'     => __( 'Select Currency', 'wp-travel' ),
			'options'    => $currency_list,
			'attributes' => array(
				'style' => 'width: 300px;',
			),
		);

		$map_data       = wp_travel_get_maps();
		$wp_travel_maps = $map_data['maps'];
		$selected_map   = $map_data['selected'];

		$map_dropdown_args = array(
			'id'           => 'wp-travel-map-select',
			'class'        => 'wp-travel-select2',
			'name'         => 'wp_travel_map',
			'option'       => '',
			'options'      => $wp_travel_maps,
			'selected'     => $selected_map,
			'before_label' => '',
			'after_label'  => '',
			'attributes'   => array(
				'style' => 'width: 300px;',
			),
		);
		$map_key           = 'google-map';
		?>
		<table class="form-table">
			<tr>
				<th><label for="currency"><?php echo esc_html__( 'Currency', 'wp-travel' ); ?></label></th>
				<td>
				<?php echo wp_travel_get_dropdown_currency_list( $currency_args ); ?>
					<p class="description"><?php echo esc_html__( 'Choose your currency', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th clospan="2">
					<h3><?php esc_html_e( 'Maps', 'wp-travel' ); ?></h3>
				</th>
			</tr>
			<tr>
				<th><label for="wp-travel-map-select"><?php echo esc_html__( 'Select Map', 'wp-travel' ); ?></label></th>
				<td>
				<?php echo wp_travel_get_dropdown_list( $map_dropdown_args ); ?>
					<p class="description"><?php echo esc_html__( 'Choose your map', 'wp-travel' ); ?></p>
				</td>
			</tr>
		<?php do_action( 'wp_travel_settings_after_currency', $tab, $args ); ?>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_api_key"><?php echo esc_html__( 'Google Map API Key', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $google_map_api_key ); ?>" name="google_map_api_key" id="google_map_api_key"/>
					<p class="description"><?php echo sprintf( 'Don\'t have api key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a>', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_zoom_level"><?php echo esc_html__( 'Map Zoom Level', 'wp-travel' ); ?></label></th>
				<td>
					<input step="1" min="1" type="number" value="<?php echo esc_attr( $google_map_zoom_level ); ?>" name="google_map_zoom_level" id="google_map_zoom_level"/>
				</td>
			</tr>
		</table>
		<?php
			$upsell_args = array(
				'title'      => __( 'Need alternative maps ?', 'wp-travel' ),
				'content'    => __( 'If you need alternative to current map then you can get free or pro maps for WP Travel.', 'wp-travel' ),
				'link'       => 'https://wptravel.io/wp-travel-pro/',
        		'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
				'link2'       => 'https://wptravel.io/downloads/category/map/',
				'link2_label' => __( 'View WP Travel Map addons', 'wp-travel' ),
			);
			wp_travel_upsell_message( $upsell_args );
		?>
		<br>
		<h3 class="wp-travel-tab-content-title"><?php echo esc_html__( 'Pages', 'wp-travel' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="cart-page-id"><?php echo esc_html__( 'Cart Page', 'wp-travel' ); ?></label></th>
				<td>
				<?php
				wp_dropdown_pages(
					array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $selected_cart_page,
						'echo'                  => 1,
						'name'                  => 'cart_page_id',
						'id'                    => 'cart-page-id', // string
						'class'                 => 'wp-travel-select2', // string
						'show_option_none'      => null, // string
						'show_option_no_change' => null, // string
						'option_none_value'     => null, // string
					)
				);
				?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as cart page for trip bookings which contents cart page shortcode [wp_travel_cart]', 'wp-travel' ); ?></p>
				</td>
			<tr>

			<tr>
				<th><label for="checkout-page-id"><?php echo esc_html__( 'Checkout Page', 'wp-travel' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'depth'                 => 0,
							'child_of'              => 0,
							'selected'              => $selected_checkout_page,
							'echo'                  => 1,
							'name'                  => 'checkout_page_id',
							'id'                    => 'checkout-page-id', // string
							'class'                 => 'wp-travel-select2', // string
							'show_option_none'      => null, // string
							'show_option_no_change' => null, // string
							'option_none_value'     => null, // string
						)
					);
					?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as checkout page for booking which contents checkout page shortcode [wp_travel_checkout]', 'wp-travel' ); ?></p>
				</td>
			<tr>
			<tr>
				<th><label for="dashboard-page-id"><?php echo esc_html__( 'Dashboard Page', 'wp-travel' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'depth'                 => 0,
							'child_of'              => 0,
							'selected'              => $selected_dashboard_page,
							'echo'                  => 1,
							'name'                  => 'dashboard_page_id',
							'id'                    => 'dashboard-page-id', // string
							'class'                 => 'wp-travel-select2', // string
							'show_option_none'      => null, // string
							'show_option_no_change' => null, // string
							'option_none_value'     => null, // string
						)
					);
					?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as dashboard page which contents dashboard page shortcode [wp_travel_user_account].', 'wp-travel' ); ?></p>
				</td>
			<tr>
				<?php
				/**
				 * Hook.
				 *
				 * @since 1.8.0
				 */
				do_action( 'wp_travel_after_page_settings', $tab, $args )
				?>
		</table>
			<?php
}
