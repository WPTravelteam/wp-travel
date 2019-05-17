<?php

/**
 * Callback for General tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_general( $tab, $args ) {
	$settings = $args['settings'];

		$currency_list      = wp_travel_get_currency_list();
		$currency           = $settings['currency'];
		$currency_position  = $settings['currency_position'];
		$thousand_separator = $settings['thousand_separator'];
		$decimal_separator  = $settings['decimal_separator'];
		$number_of_decimals = $settings['number_of_decimals'];

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

		$currency_position_args = array(
			'id'         => 'currency-position',
			'class'      => 'currency-position wp-travel-select2',
			'name'       => 'currency_position',
			'selected'   => $currency_position,
			// 'option'     => __( 'Select Currency', 'wp-travel' ),
			'options'    => array(
				'left'             => __( 'Left', 'wp-travel' ),
				'right'            => __( 'Right', 'wp-travel' ),
				'left_with_space'  => __( 'Left with space', 'wp-travel' ),
				'right_with_space' => __( 'Right with space', 'wp-travel' ),
			),
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
					<p class="description"><?php echo esc_html__( 'Choose currency you accept payments in.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="currency-position"><?php echo esc_html__( 'Currency Position', 'wp-travel' ); ?></label></th>
				<td>
				<?php echo wp_travel_get_dropdown_list( $currency_position_args ); ?>
					<p class="description"><?php echo esc_html__( 'Choose currency position.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="thousand-separator"><?php echo esc_html__( 'Thousand separator', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $thousand_separator ); ?>" name="thousand_separator" id="thousand-separator"/>
					<p class="description"><?php echo esc_html__( 'This sets the thousand separator of displayed prices.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="decimal-separator"><?php echo esc_html__( 'Decimal separator', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $decimal_separator ); ?>" name="decimal_separator" id="decimal-separator"/>
					<p class="description"><?php echo esc_html__( 'This sets the Decimal separator of displayed prices.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="number-of-decimals"><?php echo esc_html__( 'Number of decimals', 'wp-travel' ); ?></label></th>
				<td>
					<input type="number" min="1" max="9" value="<?php echo esc_attr( $number_of_decimals ); ?>" name="number_of_decimals" id="number-of-decimals"/>
					<p class="description"><?php echo esc_html__( 'This sets the Number of decimal of displayed prices.', 'wp-travel' ); ?></p>
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
					<p class="description"><?php echo esc_html__( 'Choose your map provider to display map in site.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		<?php do_action( 'wp_travel_settings_after_currency', $tab, $args ); ?>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_api_key"><?php echo esc_html__( 'API Key', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $google_map_api_key ); ?>" name="google_map_api_key" id="google_map_api_key"/>
					<p class="description"><?php echo sprintf( __( 'To get your Google map API keys %1$sclick here%2$s', 'wp-travel' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">', '</a>' ); ?></p>
				</td>
			</tr>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_zoom_level"><?php echo esc_html__( 'Zoom Level', 'wp-travel' ); ?></label></th>
				<td>
					<input step="1" min="1" type="number" value="<?php echo esc_attr( $google_map_zoom_level ); ?>" name="google_map_zoom_level" id="google_map_zoom_level"/>
					<p class="description"><?php _e( 'Set default zoom level of map.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
			$upsell_args = array(
				'title'              => __( 'Need alternative maps?', 'wp-travel' ),
				'content'            => sprintf( __( 'If you need alternative to current map then you can get free or pro maps for WP Travel. Get %1$s WP Travel Pro %2$s or %3$sView WP Travel Map addons%4$s', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="__blank">', '</a>', '<a href="https://wptravel.io/downloads/category/map/" target="__blank">', '</a>' ),
				'link'               => '',
				'link_label'         => '',
				'link2'              => '',
				'link2_label'        => '',
				'main_wrapper_class' => array( 'wp-travel-upsell-message-normal' ),
				'type'               => 'maps',
			);
			wp_travel_upsell_message( $upsell_args );

			// if ( apply_filters( 'wp_travel_show_upsell_message', true, 'maps' ) ) {
			// $upsell_args = array(
			// 'title'              => __( 'Need alternative maps?', 'wp-travel' ),
			// 'content'            => sprintf( __( 'If you need alternative to current map then you can get free or pro maps for WP Travel %sfrom here%s or %srequest a new one%s.', 'wp-travel' ) , '<a href="https://wptravel.io/downloads/category/map/" target="__blank">', '</a>', '<a href="https://wptravel.io/contact/" target="__blank">', '</a>' ),
			// 'link'               => '',
			// 'link_label'         => '',
			// 'link2'              => '',
			// 'link2_label'        => '',
			// 'main_wrapper_class' => array( 'wp-travel-upsell-message-normal' ),
			// 'type'               => 'general',
			// );
			// wp_travel_upsell_message( $upsell_args );
			// }
		?>

		<table class="form-table">
			<tr>
				<th clospan="2">
					<h3><?php esc_html_e( 'Pages', 'wp-travel' ); ?></h3>
				</th>
			</tr>
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
