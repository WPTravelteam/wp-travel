<?php

class WP_Travel_Notices {

	/**
	 * Error Messages.
	 *
	 * @var $errors list of error message.
	 */
	private $errors = array();

	/**
	 * Success Messages.
	 *
	 * @var $errors list of success message.
	 */
	private $success = array();
	function __construct() {

	}

	function add( $value, $type = 'error' ) {
		if ( empty( $value ) ) {
			return;
		}

		if ( 'error' === $type ) {
			$this->errors = wp_parse_args( array( $value ), $this->errors );
			WPTravel()->session->set( 'wp_travel_errors', $this->errors );
		} elseif ( 'success' === $type ) {
			$this->success = wp_parse_args( array( $value ), $this->success );
			WPTravel()->session->set( 'wp_travel_success', $this->success );
		}
	}

	function get( $type = 'error', $destroy = true ) {
		if ( 'error' === $type ) {
			$errors = WPTravel()->session->get( 'wp_travel_errors' );
			if ( $destroy ) {
				$this->destroy( $type );
			}
			return $errors;
		} elseif ( 'success' === $type ) {
			$success = WPTravel()->session->get( 'wp_travel_success' );
			if ( $destroy ) {
				$this->destroy( $type );
			}
			return $success;
		}
	}

	function destroy( $type ) {
		if ( 'error' === $type ) {
			$this->errors = array();
			WPTravel()->session->set( 'wp_travel_errors', $this->errors );
		} elseif ( 'success' === $type ) {
			$this->success = array();
			WPTravel()->session->set( 'wp_travel_success', $this->success );
		}
	}

	function print_notices( $type, $destroy = true ) {

		$notices = $this->get( $type, $destroy );

		if ( empty( $notices ) ) {
			return;
		}

		if ( $notices && 'error' === $type ) {
			foreach ( $notices as $key => $notice ) {
				if ( 'error ' === $notice ) {
					return;
				}
				?>
				<div class="wp-travel-error">
					<strong><?php esc_html_e( 'Error:', 'wp-travel' ); ?></strong>
					<?php echo esc_html( $notice ); ?>
				</div>
				<?php
			}
			return;

		} elseif ( $notices && 'success' === $type ) {

			foreach ( $notices as $key => $notice ) {
				?>
				<div class="wp-travel-message">
					<?php echo esc_html( $notice ); ?>
				</div>
				<?php
			}
			return;

		}

		return false;

	}
}
