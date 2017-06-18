<?php
class WP_Travel_Post_Types {

	public function __construct() {

	}

	public static function init() {
		self::register_itineraries();
		self::register_bookings();
	}

	public static function register_itineraries() {
		$labels = array(
			'name'               => _x( 'Itineraries', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Itinerary', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Itineraries', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Itinerary', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New itinerary', 'wp-travel' ),
			'new_item'           => __( 'New itinerary', 'wp-travel' ),
			'edit_item'          => __( 'Edit itinerary', 'wp-travel' ),
			'view_item'          => __( 'View itinerary', 'wp-travel' ),
			'all_items'          => __( 'All Itineraries', 'wp-travel' ),
			'search_items'       => __( 'Search Itineraries', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Itineraries:', 'wp-travel' ),
			'not_found'          => __( 'No Itineraries found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Itineraries found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'itinerary' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'comments' ),
			'menu_icon'          => 'dashicons-location',
		);
		/**
		 * Register a travaldoor_trip post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'itineraries', $args );
	}

	public static function register_bookings() {
		$labels = array(
			'name'               => _x( 'Bookings', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Booking', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Bookings', 'admin menu', 'wp-travel' ),
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
			'show_in_menu'       => 'edit.php?post_type=itineraries',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'itinerary-booking' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title' ),
			'menu_icon'          => 'dashicons-location',
		);
		/**
		 * Register a travaldoor_trip post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'itinerary-booking', $args );
	}
}
