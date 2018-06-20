<?php
/**
 * Installation Class for Coupon Pro
 *
 * @package Wp_Travel_Coupons_Pro
 */

if ( ! class_exists( 'WP_Travel_Coupons_Pro_Install' ) ) :
	/**
	 * Start Installation for Coupons Pro
	 */
	class WP_Travel_Coupons_Pro_Install {

		/**
		 * WP_Travel_Coupons_Pro_Install Constructor.
		 */
		public function __construct() {

		}
		/**
		 * Init.
		 *
		 * @return void
		 */
		public static function init() {
			self::register_coupon_post_type();
			self::init_hooks();
		}
		/**
		 * Register Post Type Bookings.
		 *
		 * @return void
		 */
		public static function register_coupon_post_type() {

			$labels = array(
				'name'               => _x( 'Coupons', 'post type general name', 'wp-travel' ),
				'singular_name'      => _x( 'Coupon', 'post type singular name', 'wp-travel' ),
				'menu_name'          => _x( 'Coupons', 'admin menu', 'wp-travel' ),
				'name_admin_bar'     => _x( 'Coupon', 'add new on admin bar', 'wp-travel' ),
				'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel' ),
				'add_new_item'       => __( 'Add New Coupon', 'wp-travel' ),
				'new_item'           => __( 'New Coupon', 'wp-travel' ),
				'edit_item'          => __( 'View Coupon', 'wp-travel' ),
				'view_item'          => __( 'View Coupon', 'wp-travel' ),
				'all_items'          => __( 'Coupons', 'wp-travel' ),
				'search_items'       => __( 'Search Coupons', 'wp-travel' ),
				'parent_item_colon'  => __( 'Parent Coupons:', 'wp-travel' ),
				'not_found'          => __( 'No Coupons found.', 'wp-travel' ),
				'not_found_in_trash' => __( 'No Coupons found in Trash.', 'wp-travel' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wp-travel' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'wp-travel-coupon' ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' ),
				'menu_icon'          => 'dashicons-location',
				'with_front'		 => true,
			);
			/**
			 * Register a itinerary-booking post type.
			 *
			 * @link http://codex.wordpress.org/Function_Reference/register_post_type
			 */
			register_post_type( 'wp-travel-coupons', $args );
		}
		/**
		 * Init Hooks
		 *
		 * @return void
		 */
		public static function init_hooks() {


		}

	}

endif;
