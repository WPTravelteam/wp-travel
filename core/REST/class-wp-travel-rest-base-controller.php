<?php
/**
 * REST API: WP_Travel_REST_Base_Controller Base class for API
 *
 * @package WP Travel API Core
 * @subpackage API Core
 * @since WP Travel 4.4.5
 */

if ( ! class_exists( 'WP_Travel_REST_Base_Controller' ) ) {
	/**
	 * Core base controller for managing and interacting with REST API items.
	 *
	 * @since WP Travel 4.4.5
	 */
	abstract class WP_Travel_REST_Base_Controller {

		/**
		 * The namespace of this controller's route.
		 *
		 * @since WP Travel 4.4.5
		 * @var string
		 */
		protected $namespace = 'wptravel/v1';

		/**
		 * The base of this controller's route.
		 *
		 * @since WP Travel 4.4.5
		 * @var string
		 */
		protected $rest_base;

		/**
		 * Check permissions for the posts.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_items_permissions_check( $request ) {

			if ( ! current_user_can( 'read' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.', 'wp-travel-api' ), array( 'status' => $this->authorization_status_code() ) );
			}
			return true;
		}

		/**
		 * Check permissions for the posts.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_item_permissions_check( $request ) {
			if ( ! current_user_can( 'read' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.', 'wp-travel-api' ), array( 'status' => $this->authorization_status_code() ) );
			}
			return true;
		}

		/**
		 * Sets Authorization Status Code.
		 *
		 * @return void
		 */
		public function authorization_status_code() {
			$status = 401;

			if ( is_user_logged_in() ) {
				$status = 403;
			}

			return $status;
		}

		/**
		 * Check for authentication error.
		 *
		 * @param WP_Error|null|bool $error Error data.
		 * @return WP_Error|null|bool
		 */
		public function check_authentication_error( $error ) {
			// Pass through other errors.
			if ( ! empty( $error ) ) {
				return $error;
			}

			return $this->get_error();
		}

		/**
		 * Gets an array of fields to be included on the response.
		 *
		 * Included fields are based on item schema and `_fields=` request argument.
		 *
		 * @since WP Travel 4.4.5
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 * @return array Fields to be included in the response.
		 */
		public function get_fields_for_response( $request ) {
			$schema = $this->get_item_schema();
			$fields = isset( $schema['properties'] ) ? array_keys( $schema['properties'] ) : array();

			return $fields;
		}

	}
}


