<?php
/**
 * REST API: WP_Travel_REST_Enquiries_Controller class
 *
 * @package WP Travel API Core
 * @subpackage API Core
 * @since WP Travel 4.4.5
 */


/**
 * Core base controller for managing and interacting with Trip Enquiries.
 *
 * @since WP Travel 4.4.5
 */
if ( ! class_exists( 'WP_Travel_REST_Bookings_Controller' ) ) {
	class WP_Travel_REST_Bookings_Controller extends WP_Travel_REST_Base_Controller {
		/**
		 * The single instance of the class.
		 *
		 * @var WP Travel API
		 * @since WP Travel 4.4.5
		 */
		protected static $_instance = null;
	
		/**
		 * init.
		 */
		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->base_name = 'itinerary-booking';
			$this->namespace = 'wptravel/' . WP_TRAVEL_API_VERSION;
		}
	
		// Register our routes.
		public function register_routes() {
			// error_log('register');
			register_rest_route(
				$this->namespace,
				'/' . $this->base_name,
				array(
					// Here we register the readable endpoint for collections.
					array(
						'methods'             => 'GET',
						'callback'            => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
					),
					// Register our schema callback.
					'schema' => array( $this, 'get_item_schema' ),
				)
			);
			register_rest_route(
				$this->namespace,
				'/' . $this->base_name . '/(?P<id>[\d]+)',
				array(
					// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
					array(
						'methods'             => 'GET',
						'callback'            => array( $this, 'get_item' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
					),
					// Register our schema callback.
					'schema' => array( $this, 'get_item_schema' ),
				)
			);
		}
	
		/**
		 * Grabs the five most recent posts and outputs them as a rest response.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_items( $request ) {
			// Retrieve the list of registered collection query parameters.
			$registered = $this->get_collection_params();
			$mappings = array(
				'per_page' => 'posts_per_page',
				'page' => 'paged',
			);
			$args  = array(
				'posts_per_page' => 10,
				'post_type'     => $this->base_name,
			);
			foreach ( $mappings as $api_param => $wp_param ) {
				if( isset( $registered[ $api_param ], $request[ $api_param ] ) ){
					$args[ $wp_param ] = $request[ $api_param ];
				}
			}
			$posts_query = new WP_Query( $args );
	
			$posts = $posts_query->get_posts();
	
			$data = array(
				'success' => true,
				'message' => 'Bookings Found.',
				'datas'    => array(),
			);
	
			if ( empty( $posts ) ) {
				return rest_ensure_response(
					array(
						'success' => false,
						'message' => 'No Bookings Found.',
						'datas'    => array(),
					)
				);
			}
	
			foreach ( $posts as $post ) {
				$response       = $this->prepare_item_for_response( $post, $request );
				$data['datas'][] = $this->prepare_response_for_collection( $response );
			}
	
			$response = rest_ensure_response( $data );
	
			$response->header( 'X-WP-Total', (int) $posts_query->found_posts );
			$response->header( 'X-WP-TotalPages', (int) $posts_query->max_num_pages );
	
			// Return all of our comment response data.
			return $response;
		}
	
		/**
		 * Grabs a single Enquiry if vald id is provided.
		 *
		 * @param $request WP_REST_Request Current request.
		 */
		public function get_item( $request ) {
			$id   = (int) $request['id'];
			$post = get_post( $id );
	
			$data = array(
				'success' => true,
				'message' => 'Booking Found.',
			);
			if ( empty( $post ) ) {
				return rest_ensure_response(
					array(
						'success' => false,
						'message' => 'Booking not found by ID.',
						'datas'    => array(),
					)
				);
			}
	
			$response     = $this->prepare_item_for_response( $post, $request );
			$data['datas'] = $this->prepare_response_for_collection( $response );
			// Return all of our post response data.
			return $data;
		}
	
		/**
		 * Matches the post data to the schema we want.
		 *
		 * @param WP_Post $post The comment object whose response is being prepared.
		 */
		public function prepare_item_for_response( $post, $request ) {
	
			$schema = $this->get_item_schema( $request );
			$fields = $this->get_fields_for_response( $request );
	
			$order_data       = get_post_meta( $post->ID, 'order_data', true );
			$order_items_data = get_post_meta( $post->ID, 'order_items_data', true );
			$order_totals     = get_post_meta( $post->ID, 'order_totals', true );
			$payment_id       = get_post_meta( $post->ID, 'wp_travel_payment_id' );
			$settings         = wp_travel_get_settings();
			if ( is_array( $payment_id ) ) {
				foreach ( $payment_id as $id ) {
					$payment_data[]    = array(
						'amount'          => get_post_meta( $id, 'wp_travel_payment_amount', true ),
						'payment_gateway' => get_post_meta( $id, 'wp_travel_payment_gateway', true ),
						'payment_mode'    => get_post_meta( $id, 'wp_travel_payment_mode', true ),
						'payment_status'  => get_post_meta( $id, 'wp_travel_payment_status', true ),
					);
				}
			}
	
			$travellers   = array();
			$booked_trips = array();
			if ( is_array( $order_items_data ) ){
				foreach ( $order_items_data as $key => $item ){
					$travellers_info = array();
					foreach ( $order_data['wp_travel_fname_traveller'][$key] as $k => $value ) {
						$travellers_info[ $k ]['full_name'] = $value;
					}
					foreach ( $order_data['wp_travel_lname_traveller'][$key] as $k => $value ) {
						$travellers_info[ $k ]['last_name'] = $value;
					}
					foreach ( $order_data['wp_travel_country_traveller'][$key] as $k => $value ) {
						$travellers_info[ $k ]['country'] = $value;
					}
					foreach ( $order_data['wp_travel_phone_traveller'][$key] as $k => $value ) {
						$travellers_info[ $k ]['phone'] = $value;
					}
					foreach ( $order_data['wp_travel_email_traveller'][$key] as $k => $value ) {
						$travellers_info[ $k ]['email'] = $value;
					}
	
					$extras = $this->get_trip_extras_data( $item['trip_extras'] );
					// foreach( $item['trip_extras'] as $ ) {
	
					// }
					$booked_trips[] = array(
						'trip_id'        => $item['trip_id'],
						'trip_name'      => get_the_title( (int) $item['trip_id'] ),
						'pax'            => $item['pax'],
						'trip_price'     => $item['pax'] * $item['trip_price'],
						'arrival_date'   => $item['arrival_date'],
						'departure_date' => $item['departure_date'],
						'travellers'     => $travellers_info,
						'extras'         => $extras,
					);
				}
			}
	
			$data = array(
				'id'             => $post->ID,
				'note'           => $order_data['wp_travel_note'],
				'booking_date'   => $this->prepare_date_response( $post->post_date_gmt, $post->post_date ),
				'booking_status' => get_post_meta( $post->ID, 'wp_travel_booking_status', true ),
				'currency'       => $settings['currency'],
				'currency_code'  => html_entity_decode( wp_travel_get_currency_symbol() ),
				'booked_trips'   => $booked_trips,
				'booking_option' => $order_data['wp_travel_booking_option'],
				'billing_info'        => array(
					'city'    => $order_data['billing_city'],
					'country' => $order_data['wp_travel_country'],
					'postal'  => $order_data['billing_postal'],
					'address' => $order_data['wp_travel_address'],
				),
				'pricing_info'        => array(
					'sub_total'         => isset( $order_totals['sub_total'] ) ? $order_totals['sub_total'] : 0,
					'tax'               => isset( $order_totals['tax'] ) ? $order_totals['tax'] : 0,
					'discount'          => isset( $order_totals['discount'] ) ? $order_totals['discount'] : 0,
					'sub_total_partial' => isset( $order_totals['sub_total_partial'] ) ? $order_totals['sub_total_partial'] : 0,
					'tax_partial'       => isset( $order_totals['tax_partial'] ) ? $order_totals['tax_partial'] : 0,
					'discount_partial'  => isset( $order_totals['discount_partial'] ) ? $order_totals['discount_partial'] : 0,
					'total_partial'     => isset( $order_totals['total_partial'] ) ? $order_totals['total_partial'] : 0,
					'total'             => isset( $order_totals['total'] ) ? $order_totals['total'] : 0,
				),
				'payment_info'         => $payment_data,
			);
	
			if ( 'booking_only' === $data['booking_option'] ) {
				$data['payment_info'] = array();
			}
	
			return rest_ensure_response( $data );
		}
	
		private function get_trip_extras_data( $trip_extras ) {
			$extras = array();
			if( ! empty( $trip_extras['id'] ) && count( $trip_extras['id'] ) > 0 ) :
				foreach ( $trip_extras['id'] as $key => $value ) {
	
					$meta = get_post_meta( (int) $value, 'wp_travel_tour_extras_metas', true );
					$price = isset( $meta['extras_item_price'] ) && ! empty( $meta['extras_item_price'] ) ? $meta['extras_item_price'] : 0;
					$sale_price = isset( $meta['extras_item_sale_price'] ) && ! empty( $meta['extras_item_sale_price'] ) ? $meta['extras_item_sale_price'] : 0;
					if ( $sale_price ) {
						$price = $sale_price;
					}
					$extras[$key] = array(
						'id'             => $value,
						'title'          => get_the_title( (int) $value ),
						'quantity'       => $trip_extras['qty'][ $key ],
						'price_per_item' => $price,
					);
	
					$extras[ $key ]['total'] = $price * (int) $extras[ $key ]['quantity'];
				}
			endif;
	
			return $extras;
		}
	
		/**
		 * Prepare a response for inserting into a collection of responses.
		 *
		 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
		 *
		 * @param WP_REST_Response $response Response object.
		 * @return array Response data, ready for insertion into collection data.
		 */
		public function prepare_response_for_collection( $response ) {
			if ( ! ( $response instanceof WP_REST_Response ) ) {
				return $response;
			}
	
			$data   = (array) $response->get_data();
			$server = rest_get_server();
	
			if ( method_exists( $server, 'get_compact_response_links' ) ) {
				$links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
			} else {
				$links = call_user_func( array( $server, 'get_response_links' ), $response );
			}
	
			if ( ! empty( $links ) ) {
				$data['_links'] = $links;
			}
	
			return $data;
		}
	
		/**
		 * Retrieves the query params for the collections.
		 *
		 * @since 4.7.0
		 *
		 * @return array Query parameters for the collection.
		 */
		public function get_collection_params() {
			return array(
				'page'     => array(
					'description'       => __( 'Current page of the collection.', 'wp-travel' ),
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
					'minimum'           => 1,
				),
				'per_page' => array(
					'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-travel' ),
					'type'              => 'integer',
					'default'           => 10,
					'minimum'           => 1,
					'maximum'           => 100,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
				),
			);
		}
	
		/**
		 * Checks the post_date_gmt or modified_gmt and prepare any post or
		 * modified date for single post output.
		 *
		 * @since WP Travel 4.4.5
		 *
		 * @param string      $date_gmt GMT publication time.
		 * @param string|null $date     Optional. Local publication time. Default null.
		 * @return string|null ISO8601/RFC3339 formatted datetime.
		 */
		protected function prepare_date_response( $date_gmt, $date = null ) {
			// Use the date if passed.
			if ( isset( $date ) ) {
				return mysql_to_rfc3339( $date );
			}
	
			// Return null if $date_gmt is empty/zeros.
			if ( '0000-00-00 00:00:00' === $date_gmt ) {
				return null;
			}
	
			// Return the formatted datetime.
			return mysql_to_rfc3339( $date_gmt );
		}
	
		/**
		 * Get our sample schema for a post.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_item_schema( $request = null ) {
			$schema = array(
				// This tells the spec of JSON Schema we are using which is draft 4.
				'$schema' => 'http://json-schema.org/draft-04/schema#',
				// The title property marks the identity of the resource.
				'title' => 'itinerary_enquiries',
				'type'  => 'object',
				// In JSON Schema you can specify object properties in the properties attribute.
				'properties' => array(
					'id' => array(
						'description' => __( 'Unique identifier for the object.', 'wp-travel' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'date' => array(
						'description' => __( "The date the object was published, in the site's timezone.", 'wp-travel' ),
						'type'        => 'string',
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'title' => array(
						'description' => __( 'The title for the object.', 'wp-travel' ),
						'type'        => 'object',
						'context'     => array( 'view', 'edit', 'embed' ),
						'arg_options' => array(
							'sanitize_callback' => null,   // Note: sanitization implemented in self::prepare_item_for_database()
							'validate_callback' => null,   // Note: validation implemented in self::prepare_item_for_database()
						),
						'properties'  => array(
							'raw' => array(
								'description' => __( 'Title for the object, as it exists in the database.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'edit' ),
							),
							'rendered' => array(
								'description' => __( 'HTML title for the object, transformed for display.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit', 'embed' ),
								'readonly'    => true,
							),
						),
					),
					'link'            => array(
						'description' => __( 'URL to the object.', 'wp-travel' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'modified'        => array(
						'description' => __( "The date the object was last modified, in the site's timezone.", 'wp-travel' ),
						'type'        => 'string',
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'modified_gmt'    => array(
						'description' => __( 'The date the object was last modified, as GMT.', 'wp-travel' ),
						'type'        => 'string',
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit' ),
						'readonly'    => true,
					),
					'status'          => array(
						'description' => __( 'A named status for the object.', 'wp-travel' ),
						'type'        => 'string',
						'enum'        => array_keys( get_post_stati( array( 'internal' => false ) ) ),
						'context'     => array( 'view', 'edit' ),
					),
					'type'            => array(
						'description' => __( 'Type of Post for the object.', 'wp-travel' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'booking_data' => array(
						'description' => __( 'The Enquiry Object.', 'wp-travel' ),
						'type'        => 'object',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'trip_id'  => array(
								'description' => __( 'The globally unique identifier for the object.', 'wp-travel' ),
								'type'        => 'integer',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'name' => array(
								'description' => __( 'The globally unique identifier for the object.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'email' => array(
								'description' => __( 'The globally unique identifier for the object.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'message' => array(
								'description' => __( 'The globally unique identifier for the object.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
					'billing' => array(
						'description' => __( 'Unique identifier for the object.', 'wp-travel' ),
						'type'        => 'object',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'first_name' => array(
								'description' => __( 'First Name.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'last_name' => array(
								'description' => __( 'Last Name.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'company' => array(
								'description' => __( 'Company Name.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'address' => array(
								'description' => __( 'Address.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'city' => array(
								'description' => __( 'City Name.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'state' => array(
								'description' => __( 'State.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'postcode' => array(
								'description' => __( 'Postal Code.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'country' => array(
								'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'email' => array(
								'description' => __( 'Email Address.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'phone' => array(
								'description' => __( 'Phone.', 'wp-travel' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
	
						),
					),
	
				),
			);
	
			return $this->add_additional_fields_schema( $schema );
		}
	
		/**
		 * Adds the schema from additional fields to a schema array.
		 *
		 * The type of object is inferred from the passed schema.
		 *
		 * @since WP Travel 4.4.5
		 *
		 * @param array $schema Schema array.
		 * @return array Modified Schema array.
		 */
		protected function add_additional_fields_schema( $schema ) {
			if ( empty( $schema['title'] ) ) {
				return $schema;
			}
	
			// Can't use $this->get_object_type otherwise we cause an inf loop.
			$object_type = $schema['title'];
			return $schema;
		}
	
	}
}

