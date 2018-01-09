<?php
interface Wp_Travel_Payment_Interface {
	public function process_payment();
	
	public function render_settings();
}

$GLOBALS['wp_travel_payments'] = [];

if ( ! function_exists( 'wp_travel_register_payments' ) ) {

	/**
	 * Register payments here
	 *
	 * @param Object $object Payment Object.
	 */
	function wp_travel_register_payments( $object ) {

		if ( ! is_object( $object ) ) {
			throw new \Exception( 'Payment gateway must be an instance of class. ' . gettype( $object ) . ' given.' );
		}

		if ( ! ( $object instanceof Wp_Travel_Payment_Interface ) ) {
			throw new \Exception( 'Payment gateway must be an instance of Wp_Travel_Payment_Interface. Instance of ' . get_class( $object ) . ' given.' );
		}

		array_push( $GLOBALS['wp_travel_payments'], $object );
	}
}
