<?php
/**
 * REST API: WP_Travel_Rest_Trips_Controller class
 *
 * @package WP_Travel
 * @subpackage REST_API
 * @since 4.0.5
 */

/**
 * Core class to access trips via the REST API.
 *
 * @since 4.0.5
 *
 * @see WP_REST_Controller
 */
class WP_Travel_REST_Trips_Controller extends WP_Travel_REST_Controller {
	/**
	 * Post type.
	 *
	 * @since 4.0.5
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 * @since 4.0.5
	 *
	 * @param string $post_type Post type.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
		$this->namespace = 'wptravel/v1';
		$obj             = get_post_type_object( $post_type );
		$this->rest_base = ! empty( $obj->rest_base ) ? $obj->rest_base : $post_type;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.0.5
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		$schema        = $this->get_item_schema();
		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
		if ( isset( $schema['properties']['password'] ) ) {
			$get_item_args['password'] = array(
				'description' => __( 'The password for the post if it is password protected.' ),
				'type'        => 'string',
			);
		}
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the object.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $get_item_args,
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'Whether to bypass trash and force deletion.' ),
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Retrieves a collection of posts.
	 *
	 * @since 4.0.5
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return array|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$params = $request->get_query_params();

		$data     = WP_Travel_Helpers_Trips::get_trips( $params );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$total_posts = isset( $data['total_trips'] ) ? $data['total_trips'] : 0;
		$max_pages   = isset( $data['max_pages'] ) ? $data['max_pages'] : 0;
		unset( $data['total_trips'] );
		unset( $data['max_pages'] );

		$response = rest_ensure_response( $data );

		$response->header( 'X-WP-Total', (int) $total_posts );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );
		
		return $response;
	}

	public function get_items_permissions_check( $request ) {
		$post_type = get_post_type_object( $this->post_type );

		if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit posts in this post type.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	public function get_item( $request ) {
		return WP_Travel_Helpers_Trips::get_trip( $request['id'] );
	}

	public function get_item_permissions_check( $request ) {
		$post = get_post( $request['id'] );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		if ( is_null( $post ) || $post->post_type !== $this->post_type ) {
			return new WP_Error( 'WP_TRAVEL_NO_TRIP_FOUND', __( 'No trip found with the provided ID', 'wp-travel' ) );
		}

		if ( 'edit' === $request['context'] && $post && ! $this->check_update_permission( $post ) ) {
			return new WP_Error(
				'rest_forbidden_context',
				__( 'Sorry, you are not allowed to edit this post.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( $post && ! empty( $request['password'] ) ) {
			// Check post password, and return error if invalid.
			if ( ! hash_equals( $post->post_password, $request['password'] ) ) {
				return new WP_Error(
					'rest_post_incorrect_password',
					__( 'Incorrect post password.' ),
					array( 'status' => 403 )
				);
			}
		}

		// Allow access to all password protected posts if the context is edit.
		if ( 'edit' === $request['context'] ) {
			add_filter( 'post_password_required', '__return_false' );
		}

		if ( $post ) {
			return $this->check_read_permission( $post );
		}

		return true;
	}

	/**
	 * Checks if trip can be viewed.
	 */
	public function check_read_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( 'publish' === $post->post_status || current_user_can( $post_type->cap->read_post, $post->ID ) ) {
			return true;
		}
		return false;
	}
}
