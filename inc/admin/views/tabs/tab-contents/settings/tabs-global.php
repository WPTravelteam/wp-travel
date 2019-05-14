<?php
/**
 * Callback for Tabs Settings.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_tabs_global( $tab, $args ) {
	$settings           = $args['settings'];
		$custom_tab_enabled = apply_filters( 'wp_travel_is_custom_tabs_support_enabled', false );

		$default_tabs = wp_travel_get_default_trip_tabs();

		// Global tab.
		$global_tabs = wp_travel_get_global_tabs( $settings, $custom_tab_enabled );

		if ( $custom_tab_enabled ) { // If utilities is activated.
			$custom_tabs  = isset( $settings['wp_travel_custom_global_tabs'] ) ? $settings['wp_travel_custom_global_tabs'] : array();
			$default_tabs = array_merge( $default_tabs, $custom_tabs ); // To get Default label of custom tab.
		}

		$upsell_args = array(
			'title'      => __( 'Need Additional Tabs?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can get global custom tabs addition options with customized content and sorting !', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
			'type'        => array( 'wp-travel-utilities', 'wp-travel-pro' ),
		);

		wp_travel_upsell_message( $upsell_args );
		// Add custom Tabs Support.
		do_action( 'wp_travel_custom_global_tabs' );

		if ( is_array( $global_tabs ) && count( $global_tabs ) > 0 ) {
			echo '<table class="wp-travel-sorting-tabs form-table">';
			?>
				<thead>
					<th width="50px"><?php esc_html_e( '', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Default Tab Title', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Custom Tab Title', 'wp-travel' ); ?></th>
					<th width="20%"><?php esc_html_e( 'Display', 'wp-travel' ); ?></th>
				</thead>
				<tbody>
			<?php
			foreach ( $global_tabs as $key => $tab ) :
				$default_label = isset( $default_tabs[ $key ]['label'] ) ? $default_tabs[ $key ]['label'] : $tab['label'];
				?>
				<tr>
					<td width="50px">
						<div class="wp-travel-sorting-handle">
						</div>
					</td>
					<td width="35%">
						<div class="wp-travel-sorting-tabs-wrap">
						<span class="wp-travel-tab-label wp-travel-accordion-title"><?php echo esc_html( $default_label ); ?></span>
					</div>
					</td>
					<td width="35%">
						<div class="wp-travel-sorting-tabs-wrap">
						<input type="text" class="wp_travel_tabs_input-field section_title" name="global_tab_settings[<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $default_label ); ?>" />
						<input type="hidden" name="global_tab_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" value="no" />

					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design"><label data-on="ON" data-off="OFF"><input name="global_tab_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ); ?> /><?php // esc_html_e( 'Display', 'wp-travel' ); ?>
						<span class="switch">
						  </span>
						</label></span>
					</td>
				</tr>
				<?php
				endforeach;

			echo '<tbody></table>';
		}
}
