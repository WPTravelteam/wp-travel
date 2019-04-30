<?php
/**
 * Callback for Addons setings tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_addons_settings( $tab, $args ) {
	?>
	<p><?php echo esc_html( 'You can enable/disable addons here.' ); ?></p>
	<?php
	$args = array(
		'title' => __( 'Want to add more features in WP Travel?', 'wp-travel' ),
		'content' => __( 'Get addon for payment, trip extras, Inventory management and other premium features.', 'wp-travel' ),
		'link'       => 'https://wptravel.io/wp-travel-pro/',
		'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		'link2' => 'https://wptravel.io/downloads/',
		'link2_label' => __( 'Get WP Travel Addons', 'wp-travel' ),
	);

	if ( class_exists( 'WP_Travel_Pro' ) ) {
		$args['link'] = $args['link2'];
		$args['link_label'] = $args['link2_label'];
		unset( $args['link2'], $args['link2_label'] );
	}
	wp_travel_upsell_message( $args );
	do_action( 'wp_travel_addons_setings_tab_fields', $args );
}
