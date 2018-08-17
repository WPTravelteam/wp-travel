<?php
class Wp_Travel_Taxonomies {

	public static function init() {
		self::register_itinerary_types();
	}

	public static function register_itinerary_types() {
		$permalink = wp_travel_get_permalink_structure();
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
			'menu_name'         => __( 'Trip Types', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $permalink['wp_travel_trip_type_base'] ),
		);

		register_taxonomy( 'itinerary_types', apply_filters( 'wp_travel_trip_type_post_types', array( WP_TRAVEL_POST_TYPE ) ), $args );

		$labels = array(
			'name'              => _x( 'Destinations', 'general name', 'wp-travel' ),
			'singular_name'     => _x( 'Destination', 'singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Destinations', 'wp-travel' ),
			'all_items'         => __( 'All Destinations', 'wp-travel' ),
			'parent_item'       => __( 'Parent Destination', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Destination:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Destination', 'wp-travel' ),
			'update_item'       => __( 'Update Destination', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Destination', 'wp-travel' ),
			'new_item_name'     => __( 'New Destination', 'wp-travel' ),
			'menu_name'         => __( 'Destinations', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $permalink['wp_travel_destination_base'] ),
		);

		register_taxonomy( 'travel_locations', apply_filters( 'wp_travel_destinations_post_types', array( WP_TRAVEL_POST_TYPE ) ), $args );

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

		register_taxonomy( 'travel_keywords', apply_filters( 'wp_travel_kewords_post_types', array( WP_TRAVEL_POST_TYPE ) ), $args );

		$labels = array(
			'name'              => _x( 'Activities', 'general name', 'wp-travel' ),
			'singular_name'     => _x( 'Activity', 'singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Activities', 'wp-travel' ),
			'all_items'         => __( 'All Activities', 'wp-travel' ),
			'parent_item'       => __( 'Parent Activity', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Activity:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Activity', 'wp-travel' ),
			'update_item'       => __( 'Update Activity', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Activity', 'wp-travel' ),
			'new_item_name'     => __( 'New Activity', 'wp-travel' ),
			'menu_name'         => __( 'Activities', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => $permalink['wp_travel_activity_base'] ),
		);

		register_taxonomy( 'activity', apply_filters( 'wp_travel_activity_post_types', array( WP_TRAVEL_POST_TYPE ) ), $args );
	}
}
