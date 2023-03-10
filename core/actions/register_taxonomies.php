<?php
class WP_Travel_Actions_Register_Taxonomies {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'create_taxonomies' ), 0 );
	}

	public static function create_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Pricing Categories', 'taxonomy general name', 'wp-travel' ),
			'singular_name'     => _x( 'Pricing Category', 'taxonomy singular name', 'wp-travel' ),
			'search_items'      => __( 'Search Pricing Categories', 'wp-travel' ),
			'all_items'         => __( 'All Pricing Categories', 'wp-travel' ),
			'parent_item'       => __( 'Parent Pricing Category', 'wp-travel' ),
			'parent_item_colon' => __( 'Parent Pricing Categorie:', 'wp-travel' ),
			'edit_item'         => __( 'Edit Pricing Category', 'wp-travel' ),
			'update_item'       => __( 'Update Pricing Category', 'wp-travel' ),
			'add_new_item'      => __( 'Add New Pricing Category', 'wp-travel' ),
			'new_item_name'     => __( 'New Pricing Categorie Name', 'wp-travel' ),
			'menu_name'         => __( 'Pricing Category', 'wp-travel' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'genre' ),
		);

		register_taxonomy( 'itinerary_pricing_category', array( 'itineraries' ), $args );
	}
}

WP_Travel_Actions_Register_Taxonomies::init();
