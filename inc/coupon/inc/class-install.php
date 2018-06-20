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
				'name'               => _x( 'Coupons', 'post type general name', 'wp-travel-coupon-pro' ),
				'singular_name'      => _x( 'Coupon', 'post type singular name', 'wp-travel-coupon-pro' ),
				'menu_name'          => _x( 'Coupons', 'admin menu', 'wp-travel-coupon-pro' ),
				'name_admin_bar'     => _x( 'Coupon', 'add new on admin bar', 'wp-travel-coupon-pro' ),
				'add_new'            => _x( 'Add New', 'wp-travel', 'wp-travel-coupon-pro' ),
				'add_new_item'       => __( 'Add New Coupon', 'wp-travel-coupon-pro' ),
				'new_item'           => __( 'New Coupon', 'wp-travel-coupon-pro' ),
				'edit_item'          => __( 'View Coupon', 'wp-travel-coupon-pro' ),
				'view_item'          => __( 'View Coupon', 'wp-travel-coupon-pro' ),
				'all_items'          => __( 'Coupons', 'wp-travel-coupon-pro' ),
				'search_items'       => __( 'Search Coupons', 'wp-travel-coupon-pro' ),
				'parent_item_colon'  => __( 'Parent Coupons:', 'wp-travel-coupon-pro' ),
				'not_found'          => __( 'No Coupons found.', 'wp-travel-coupon-pro' ),
				'not_found_in_trash' => __( 'No Coupons found in Trash.', 'wp-travel-coupon-pro' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wp-travel-coupon-pro' ),
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
