<?php
class WP_Travel_Post_Types {

	public function __construct() {

	}

	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		self::register_bookings();
		self::register_trip();
		// self::register_enquiries();
		self::register_payment();
		// self::register_tour_extras();
	}
	/**
	 * Register Post Type Trip.
	 *
	 * @return void
	 */
	public static function register_trip() {
		$permalink = wp_travel_get_permalink_structure();
		$labels    = array(
			'name'               => _x( 'Trips', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Trip', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Trips', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Trip', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'New Trip', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New Trip', 'wp-travel' ),
			'new_item'           => __( 'New Trip', 'wp-travel' ),
			'edit_item'          => __( 'Edit Trip', 'wp-travel' ),
			'view_item'          => __( 'View Trip', 'wp-travel' ),
			'all_items'          => __( 'All Trips', 'wp-travel' ),
			'search_items'       => __( 'Search Trips', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Trips:', 'wp-travel' ),
			'not_found'          => __( 'No Trips found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Trips found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => $permalink['wp_travel_trip_base'],
				'with_front' => true,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'comments', 'excerpt' ),
			'menu_icon'          => 'dashicons-location',
			'menu_position'      => 30,

		);
		/**
		 * Register a itineraries post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( WP_TRAVEL_POST_TYPE, $args );
	}

	/**
	 * Register Post Type Bookings.
	 *
	 * @return void
	 */
	public static function register_bookings() {
		$labels = array(
			'name'               => _x( 'Bookings', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Booking', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'WP Travel', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Booking', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New booking', 'wp-travel' ),
			'new_item'           => __( 'New booking', 'wp-travel' ),
			'edit_item'          => __( 'View booking', 'wp-travel' ),
			'view_item'          => __( 'View booking', 'wp-travel' ),
			'all_items'          => __( 'Bookings', 'wp-travel' ),
			'search_items'       => __( 'Search bookings', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent bookings:', 'wp-travel' ),
			'not_found'          => __( 'No bookings found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No bookings found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			// 'show_in_menu'       => 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'menu_icon'          => 'dashicons-wp-travel',
			'with_front'         => true,
			'menu_position'      => 30,
		);
		/**
		 * Register a itinerary-booking post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'itinerary-booking', $args );
	}

	/**
	 * Register Post Type Enquiries.
	 *
	 * @return void
	 */
	public static function register_enquiries() {
		$labels = array(
			'name'               => _x( 'Enquiries', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Enquiry', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Enquiries', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Enquiry', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New Enquiry', 'wp-travel' ),
			'new_item'           => __( 'New Enquiry', 'wp-travel' ),
			'edit_item'          => __( 'View Enquiry', 'wp-travel' ),
			'view_item'          => __( 'View Enquiry', 'wp-travel' ),
			'all_items'          => __( 'Enquiries', 'wp-travel' ),
			'search_items'       => __( 'Search Enquiries', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Enquiries:', 'wp-travel' ),
			'not_found'          => __( 'No Enquiries found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Enquiries found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=itinerary-booking',
			'query_var'          => true,
			// 'rewrite'            => array( 'slug' => 'itinerary-booking' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'menu_icon'          => 'dashicons-help',
			'with_front'         => true,
		);
		/**
		 * Register a itinerary-booking post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'itinerary-enquiries', $args );
	}

	/**
	 * Register Post Type Payment.
	 *
	 * @return void
	 */
	public static function register_payment() {
		$labels = array(
			'name'               => _x( 'Payments', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Payment', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Payments', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Payment', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New Payment', 'wp-travel' ),
			'new_item'           => __( 'New Payment', 'wp-travel' ),
			'edit_item'          => __( 'Edit Payment', 'wp-travel' ),
			'view_item'          => __( 'View Payment', 'wp-travel' ),
			'all_items'          => __( 'All Payments', 'wp-travel' ),
			'search_items'       => __( 'Search Payments', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Payments:', 'wp-travel' ),
			'not_found'          => __( 'No Payments found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Payments found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'itinerary-payment' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'comments' ),
			'menu_icon'          => 'dashicons-cart',
		);
		/**
		 * Register a Payments post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'wp-travel-payment', $args );
	}

	/**
	 * Register Post Type WP Travel Tour Extras.
	 *
	 * @return void
	 */
	public static function register_tour_extras() {
		$labels = array(
			'name'               => _x( 'Trip Extras', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Trip Extra', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Trip Extras', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Trip Extra', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New Trip Extra', 'wp-travel' ),
			'new_item'           => __( 'New Trip Extra', 'wp-travel' ),
			'edit_item'          => __( 'Edit Trip Extra', 'wp-travel' ),
			'view_item'          => __( 'View Trip Extra', 'wp-travel' ),
			'all_items'          => __( 'Trip Extras', 'wp-travel' ),
			'search_items'       => __( 'Search Trip Extras', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Trip Extras:', 'wp-travel' ),
			'not_found'          => __( 'No Trip Extras found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Trip Extras found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=itinerary-booking',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'tour-extras' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'thumbnail' ),
			'menu_icon'          => 'dashicons-wp-travel',
		);

		$args = apply_filters( 'wp_travel_tour_extras_post_type_args', $args );
		/**
		 * Register a WP Travel Tour Extras post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'tour-extras', $args );
	}
}
