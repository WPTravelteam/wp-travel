<?php
/**
 * Template file for WP Travel inventory tab.
 *
 * @package WP Travel
 */

/**
 * Callback Function For Itineraries Content Tabs
 *
 * @param string $tab  tab name 'itineraries_content'.
 * @param array  $args arguments function arugments.
 * @return Mixed
 */
function wp_travel_trip_callback_tabs( $tab, $args ) {


	$post_id = $args['post']->ID;

	$wp_travel_use_global_tabs    = get_post_meta( $post_id, 'wp_travel_use_global_tabs', true );
	$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );

	$default_tabs = wp_travel_get_default_trip_tabs();
	$tabs         = wp_travel_get_admin_trip_tabs( $post_id, $enable_custom_itinerary_tabs );

	if ( $enable_custom_itinerary_tabs ) { // If utilities is activated.
		$custom_tabs = get_post_meta( $post_id, 'wp_travel_itinerary_custom_tab_cnt_', true );
		$custom_tabs = ( $custom_tabs ) ? $custom_tabs : array();

		$default_tabs = array_merge( $default_tabs, $custom_tabs ); // To get Default label of custom tab.
	}
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'      => __( 'Need Additional Tabs ?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can get trip specific custom tabs addition options with customized content and sorting !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
        	'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;

	// Custom itinerary tabs support.
	do_action( 'wp_travel_itinerary_custom_tabs', $post_id );
	if ( is_array( $tabs ) && count( $tabs ) > 0 ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<label for="wp-travel-use-global-tabs" class="show-in-frontend-label"><?php esc_html_e( 'Use Global Tabs Layout', 'wp-travel' ); ?></label>
					<input name="wp_travel_use_global_tabs" type="hidden"  value="no">
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
					<input type="checkbox" name="wp_travel_use_global_tabs" id="wp-travel-use-global-tabs" value="yes" <?php checked( 'yes', $wp_travel_use_global_tabs ); ?> />
						<span class="switch">
						</span>
					</label>
				</span>
				</td>
			</tr>
			<tr>
				<td>
					<p class="description wp-travel-custom-tabs-message"><?php _e( 'Uncheck above checkbox to add custom tab settings for this trip.', 'wp-travel' ); ?> </p>
				</td>
			</tr>
		</table>
		<table class="wp-travel-sorting-tabs form-table">
			<thead>
				<th width="50px"><?php esc_html_e( 'Sorting', 'wp-travel' ); ?></th>
				<th width="35%"><?php esc_html_e( 'Global Trip Title', 'wp-travel' ); ?></th>
				<th width="35%"><?php esc_html_e( 'Custom Trip Title', 'wp-travel' ); ?></th>
				<th width="20%"><?php esc_html_e( 'Display', 'wp-travel' ); ?></th>
			</thead>
			<tbody>
			<?php
			foreach ( $tabs as $key => $tab ) :
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
					<td>
						<div class="wp-travel-sorting-tabs-wrap">
						<input type="text" class="wp_travel_tabs_input-field section_title" name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $default_label ); ?>" />
						<input type="hidden" name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][show_in_menu]" value="no" />
					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design">
							<label data-on="ON" data-off="OFF"><input name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ); ?> /><?php // esc_html_e( 'Display', 'wp-travel' ); ?>
							<span class="switch">
								</span>
							</label>
						</span>
						<span class="check-handeller"></span>
					</td>
				</tr>
				<?php
			endforeach;
			?>
			</tbody>
		</table>
		<?php
	}
}
