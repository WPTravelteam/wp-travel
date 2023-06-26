<?php
/**
 * Templates
 *
 * @package WP Travel Templates
 */

/**
 * wptravel_templates
 */
class wptravel_templates {
	/**
	 * wptravel_templates constructor.
	 */
	public function __construct() {
		// Register custom post type.
		add_action( 'init', array( $this, 'add_custom_post_type' ) );
	}

	/**
	 * Register custom post type.
	 */
	public function add_custom_post_type() {
		register_post_type(
			'wptravel_template',
			array(
				'labels'              => array(
					'name'               => _x( 'Templates', 'Post Type General Name' ),
					'singular_name'      => _x( 'Template', 'Post Type Singular Name' ),
					'menu_name'          => __( 'WP Travel Templates' ),
					'parent_item_colon'  => __( 'Parent Template' ),
					'all_items'          => __( 'Templates' ),
					'view_item'          => __( 'View Template' ),
					'add_new_item'       => __( 'Add New Template' ),
					'add_new'            => __( 'Add New' ),
					'edit_item'          => __( 'Edit Template' ),
					'update_item'        => __( 'Update Template' ),
					'search_items'       => __( 'Search Template' ),
					'not_found'          => __( 'Not Found' ),
					'not_found_in_trash' => __( 'Not found in Trash' ),
				),
				'public'              => false, // true?
				'publicly_queryable'  => false, // true?
				'has_archive'         => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'menu_position'       => 57,
				'hierarchical'        => false,
				'show_in_menu'        => 'edit.php?post_type=itinerary-booking',
				'show_in_admin_bar'   => true,
				'show_in_rest'        => true,
				'taxonomies'          => array(
					'wptravel_blocks_template_category',
				),
				'capability_type'     => 'post',
				'supports'            => array(
					'title',
					'editor',
					'thumbnail',
					'custom-fields',
				),
			)
		);

		register_taxonomy(
			'wptravel_template_category',
			'wptravel_template',
			array(
				'label'              => esc_html__( 'Categories' ),
				'labels'             => array(
					'menu_name' => esc_html__( 'Categories' ),
				),
				'rewrite'            => false,
				'hierarchical'       => false,
				'publicly_queryable' => false,
				'show_in_nav_menus'  => false,
				'show_in_rest'       => true,
				'show_admin_column'  => true,
			)
		);
	}
}

new wptravel_templates();
