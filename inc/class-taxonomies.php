<?php
class Wp_Travel_Taxonomies {
	public static function init() {
		self::register_locations();
		self::register_itinerary_types();
	}

	public static function register_locations() {
		// Add new taxonomy, make it hierarchical (like categories).
		$labels = array(
			'name'              => _x( 'Locations', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Locations', 'textdomain' ),
			'all_items'         => __( 'All Locations', 'textdomain' ),
			'parent_item'       => __( 'Parent Location', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Location:', 'textdomain' ),
			'edit_item'         => __( 'Edit Location', 'textdomain' ),
			'update_item'       => __( 'Update Location', 'textdomain' ),
			'add_new_item'      => __( 'Add New Location', 'textdomain' ),
			'new_item_name'     => __( 'New Location Name', 'textdomain' ),
			'menu_name'         => __( 'Location', 'textdomain' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'location' ),
		);

		register_taxonomy( 'itinerary_locations', array( 'itineraries' ), $args );
	}

	public static function register_itinerary_types() {
		// Add new taxonomy, make it hierarchical (like categories).
		$labels = array(
			'name'              => _x( 'Trip Types', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Trip Type', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Search Trip Types', 'textdomain' ),
			'all_items'         => __( 'All Trip Types', 'textdomain' ),
			'parent_item'       => __( 'Parent Trip Type', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Trip Type:', 'textdomain' ),
			'edit_item'         => __( 'Edit Trip Type', 'textdomain' ),
			'update_item'       => __( 'Update Trip Type', 'textdomain' ),
			'add_new_item'      => __( 'Add New Trip Type', 'textdomain' ),
			'new_item_name'     => __( 'New Tour Trip Name', 'textdomain' ),
			'menu_name'         => __( 'Trip Type', 'textdomain' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'trip_type' ),
		);

		register_taxonomy( 'itinerary_types', array( 'itineraries' ), $args );
	}
}
