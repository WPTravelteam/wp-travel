<?php
class Wp_Travel_Taxonomies {
	public static function init() {
		self::register_itinerary_types();
	}

	public static function register_itinerary_types() {
		// Add new taxonomy, make it hierarchical (like categories).
		$labels = array(
			'name'              => _x( 'Trip Types', 'taxonomy general name', 'wp-travel' ),
			'singular_name'     => _x( 'Trip Type', 'taxonomy singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Trip Types', 'wp-travel' ),
			'all_items'         => __( 'All Trip Types', 'wp-travel' ),
			'parent_item'       => __( 'Parent Trip Type', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Trip Type:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Trip Type', 'wp-travel' ),
			'update_item'       => __( 'Update Trip Type', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Trip Type', 'wp-travel' ),
			'new_item_name'     => __( 'New Tour Trip Name', 'wp-travel' ),
			'menu_name'         => __( 'Trip Type', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'trip-type' ),
		);

		register_taxonomy( 'itinerary_types', array( WP_TRAVEL_POST_TYPE ), $args );

		$labels = array(
			'name'              => _x( 'Locations', 'general name', 'wp-travel' ),
			'singular_name'     => _x( 'Location', 'singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Locations', 'wp-travel' ),
			'all_items'         => __( 'All Locations', 'wp-travel' ),
			'parent_item'       => __( 'Parent Location', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Location:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Location', 'wp-travel' ),
			'update_item'       => __( 'Update Location', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Location', 'wp-travel' ),
			'new_item_name'     => __( 'New Location', 'wp-travel' ),
			'menu_name'         => __( 'Location', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'travel-locations' ),
		);

		register_taxonomy( 'travel_locations', array( WP_TRAVEL_POST_TYPE ), $args );

		$labels = array(
			'name'              => _x( 'Keywords', 'general name', 'wp-travel' ),
			'singular_name'     => _x( 'Keyword', 'singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Keywords', 'wp-travel' ),
			'all_items'         => __( 'All Keywords', 'wp-travel' ),
			'parent_item'       => __( 'Parent Keyword', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Keyword:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Keyword', 'wp-travel' ),
			'update_item'       => __( 'Update Keyword', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Keyword', 'wp-travel' ),
			'new_item_name'     => __( 'New Keyword', 'wp-travel' ),
			'menu_name'         => __( 'Keywords', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'travel-keywords' ),
		);

		register_taxonomy( 'travel_keywords', array( WP_TRAVEL_POST_TYPE ), $args );
	}
}
