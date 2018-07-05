<?php

class WP_Travel_Notices {
	private $errors = array();
	private $success = array();
	function __construct() {

	}

	function add( $value, $type = 'error' ) {

		if ( empty( $value ) ) {

			return;

		}

		if ( 'error' === $type ) {
			$this->errors = wp_parse_args( array( $value ), $this->errors );
			WP_Travel()->session->set( 'wp_travel_errors', $this->errors );
		} elseif ( 'success' === $type ) {
			$this->success = wp_parse_args( array( $value ), $this->success );
			WP_Travel()->session->set( 'wp_travel_success', $this->success );
		}
	}

	function get( $type = 'error', $destroy = true ) {
		if ( 'error' === $type ) {
			$errors = WP_Travel()->session->get( 'wp_travel_errors' );
			if ( $destroy ) {
				$this->destroy( $type );
			}
			return $errors;
		} elseif ( 'success' === $type ) {
			$success = WP_Travel()->session->get( 'wp_travel_success' );
			if ( $destroy ) {
				$this->destroy( $type );
			}
			return $success;
		}
	}

	function destroy( $type ) {
		if ( 'error' === $type ) {
			$this->errors = array();
			WP_Travel()->session->set( 'wp_travel_errors', $this->errors );
		} elseif ( 'success' === $type ) {
			$this->success = array();
			WP_Travel()->session->set( 'wp_travel_success', $this->success );
		}
	}

	function print_notices( $type, $destroy = true ){

		$notices = $this->get( $type, $destroy );

		if ( empty( $notices ) ) {

			return;

		}

		if ( $notices && 'error' === $type ) {

			foreach ( $notices as $key => $notice ) {

				if ( 'error ' === $notice ) {

					return;
				}

				echo '<div class="wp-travel-error">' . $notice . '</div>';

			}
			return;

		} elseif ( $notices && 'success' === $type ) {

			foreach ( $notices as $key => $notice ) {

				echo '<div class="wp-travel-message">' . $notice . '</div>';

			}
			return;

		}

		return false;

	}
}
