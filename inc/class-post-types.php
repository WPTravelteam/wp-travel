<?php
class WP_Travel_Post_Types {

	public function __construct() {

	}

	public static function init() {
		self::register_itineraries();
		self::register_locations();
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
			'supports'           => array( 'title' ),
			'menu_icon'          => 'dashicons-location',
		);
		/**
		 * Register a travaldoor_trip post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'itineraries', $args );
	}

	public static function register_locations() {
		$labels = array(
			'name'               => _x( 'Locations', 'post type general name', 'wp-travel' ),
			'singular_name'      => _x( 'Location', 'post type singular name', 'wp-travel' ),
			'menu_name'          => _x( 'Locations', 'admin menu', 'wp-travel' ),
			'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'wp-travel' ),
			'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
			'add_new_item'       => __( 'Add New location', 'wp-travel' ),
			'new_item'           => __( 'New location', 'wp-travel' ),
			'edit_item'          => __( 'Edit location', 'wp-travel' ),
			'view_item'          => __( 'View location', 'wp-travel' ),
			'all_items'          => __( 'Locations', 'wp-travel' ),
			'search_items'       => __( 'Search Locations', 'wp-travel' ),
			'parent_item_colon'  => __( 'Parent Locations:', 'wp-travel' ),
			'not_found'          => __( 'No Locations found.', 'wp-travel' ),
			'not_found_in_trash' => __( 'No Locations found in Trash.', 'wp-travel' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'wp-travel' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=itineraries',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'location' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => true,
			'menu_position'      => null,
			'supports'           => array( 'title', 'page-attributes' ),
			'menu_icon'          => 'dashicons-location',
		);
		/**
		 * Register a travaldoor_trip post type.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/register_post_type
		 */
		register_post_type( 'travel_locations', $args );
	}
}
