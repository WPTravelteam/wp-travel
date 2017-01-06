<?php
class WP_Travel_Post_Types {

	public function __construct() {

	}

	public function init() {
		self::register_itineraries();
	}

	public function register_itineraries() {
		$labels = array(
			'name'               => _x( 'Trips', 'post type general name', 'trip' ),
			'singular_name'      => _x( 'Trip', 'post type singular name', 'trip' ),
			'menu_name'          => _x( 'Traval Door Trips', 'admin menu', 'trip' ),
			'name_admin_bar'     => _x( 'Trip', 'add new on admin bar', 'trip' ),
			'add_new'            => _x( 'Add New', 'trip', 'trip' ),
			'add_new_item'       => __( 'Add New trip', 'trip' ),
			'new_item'           => __( 'New trip', 'trip' ),
			'edit_item'          => __( 'Edit trip', 'trip' ),
			'view_item'          => __( 'View trip', 'trip' ),
			'all_items'          => __( 'All trips', 'trip' ),
			'search_items'       => __( 'Search trips', 'trip' ),
			'parent_item_colon'  => __( 'Parent trips:', 'trip' ),
			'not_found'          => __( 'No trips found.', 'trip' ),
			'not_found_in_trash' => __( 'No trips found in Trash.', 'trip' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'trip' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'trip' ),
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
}
