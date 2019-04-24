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
	do_action( 'wp_travel_addons_setings_tab_fields', $args );
}
