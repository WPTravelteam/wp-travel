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
			'rewrite'           => array( 'slug' => 'trip_type' ),
		);

		register_taxonomy( 'itinerary_types', array( 'itineraries' ), $args );
	}
}
