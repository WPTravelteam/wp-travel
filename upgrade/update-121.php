<?php
/**
 * Upgrade Functions.
 *
 * @package wp-travel/upgrade
 */

add_action( 'admin_notices', 'wp_travel_delete_paypal_notice' );


function wp_travel_delete_paypal_notice() {
	$paypal_plugin_path = 'wp-travel-standard-paypal/wp-travel-paypal.php';
	if ( is_plugin_active( $paypal_plugin_path )  ) {
		deactivate_plugins( $paypal_plugin_path );
	}

	if( file_exists( WP_CONTENT_DIR . "/plugins/wp-travel-standard-paypal/wp-travel-paypal.php" ) ) {
		?>
		<div class="notice notice-warning">
			<p>
			<strong><?php printf( __( 'We have merge WP Travel Standard Paypal in WP Travel due to user request. Please  Delete your WP Travel Standard Paypal addons from Plugin Directory.', 'wp-travel' ) ); ?></strong>
			</p>
		</div>
		<?php
	}
}
