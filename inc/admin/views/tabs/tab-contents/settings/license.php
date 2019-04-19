<?php
/**
 * Callback for License tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_license( $tab, $args ) {
    do_action( 'wp_travel_license_tab_fields', $args );
}