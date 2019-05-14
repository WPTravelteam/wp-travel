<?php
/**
 * Callback for Addons setings tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_addons_settings( $tab, $args ) {
	?>
	<p><?php echo esc_html( 'You can enable or disable addons features from here.' ); ?></p>
	<?php
	wp_travel_upsell_message( array(
		'title' => __( 'Want to add more features in WP Travel?', 'wp-travel' ),
		'main_wrapper_class' => array( 'wp-travel-upsell-message-center', 'wp-travel-upsell-message-wide' ),
	) );
	do_action( 'wp_travel_addons_setings_tab_fields', $args );
}
