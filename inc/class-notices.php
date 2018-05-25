<?php

class WP_Travel_Notices {
	private $errors = array();
	function __construct() {

	}

	function add( $value, $type = 'error' ) {
		if ( 'error' === $type ) {
			$this->errors = wp_parse_args( array( $value ), $this->errors );
			WP_Travel()->session->set( 'wp_travel_errors', $this->errors );
		}
	}

	function get( $type = 'error', $destroy = true ) {
		if ( 'error' === $type ) {
			$errors = WP_Travel()->session->get( 'wp_travel_errors' );
			if ( $destroy ) {
				$this->destroy( $type );
			}
			return $errors;
		}
	}

	function destroy( $type ) {
		if ( 'error' === $type ) {
			$this->errors = array();
			WP_Travel()->session->set( 'wp_travel_errors', $this->errors );
		}
	}

	function print_notices( $type, $destroy = true ){

		$notices = $this->get( 'error', $destroy );
		if ( $notices ) {

			foreach ( $notices as $key => $notice ) {

				echo '<p class="col-xs-12 wp-travel-notice-danger wp-travel-notice">' . $notice . '</p>';

			}
			return;

		}

		return false;

	}
}
