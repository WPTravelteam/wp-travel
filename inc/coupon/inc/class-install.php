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
				'show_in_menu'       => 'edit.php?post_type=itinerary-booking',
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

			/*
			* ADMIN COLUMN - HEADERS
			*/
			add_filter( 'manage_edit-wp-travel-coupons_columns', array( 'WP_Travel_Coupons_Pro_Install','coupon_columns') );

			/*
			* ADMIN COLUMN - CONTENT
			*/
			add_action( 'manage_wp-travel-coupons_posts_custom_column', array( 'WP_Travel_Coupons_Pro_Install', 'coupons_manage_columns' ), 10, 2 );


		}

		/**
		 * Customize Admin column.
		 *
		 * @param  Array $booking_columns List of columns.
		 * @return Array                  [description]
		 */
		public static function coupon_columns( $booking_columns ) {

			$new_columns['cb'] 			 = '<input type="checkbox" />';
			$new_columns['title'] 		 = _x( 'Coupon Name', 'column name', 'wp-travel' );
			$new_columns['coupon_code']  = _x( 'Coupon Code', 'column name', 'wp-travel' );
			$new_columns['discount_value']  = _x( 'Discount Value', 'column name', 'wp-travel' );
			$new_columns['max_users']  = _x( 'Max Uses', 'column name', 'wp-travel' );
			$new_columns['used_so_far']  = _x( 'Usage Count', 'column name', 'wp-travel' );
			$new_columns['expiration_date']  = _x( 'Expitration Date', 'column name', 'wp-travel' );
			$new_columns['coupon_status'] = __( 'Coupon Status', 'wp-travel' );
			$new_columns['date'] 		 = __( 'Coupon Created Date', 'wp-travel' );
			return $new_columns;
		}

		/**
		 * Add data to custom column.
		 *
		 * @param  String $column_name Custom column name.
		 * @param  int 	  $id          Post ID.
		 */
		public static function coupons_manage_columns( $column_name, $id ) {
			switch ( $column_name ) {
				case 'coupon_status':

					$coupon        = WP_Travel()->coupon;
					$coupon_status = $coupon->get_coupon_status( $id  );

					if ( 'active' === $coupon_status ) {
						?>

							<span class="wp-travel-info-msg">
								<?php echo esc_html( 'Active', 'wp-travel' ); ?>
							</span>

						<?php 

					}
					else {

						?>

							<span class="wp-travel-error-msg">
								<?php echo esc_html( 'Expired', 'wp-travel' ); ?>
							</span>

						<?php 


					}
					break;
				case 'coupon_code' :
					$coupon = WP_Travel()->coupon;

					$coupon_code = get_post_meta( $id, 'wp_travel_coupon_code', true );

					echo '<span><strong>' . $coupon_code . '</strong></span>';

				break;
				case 'discount_value' :

					$coupon         = WP_Travel()->coupon;
					$discount_type  = $coupon->get_coupon_meta( $id, 'general', 'coupon_type' );
					$discount_value = $coupon->get_coupon_meta( $id, 'general', 'coupon_value'  );
					$symbol         = ( 'percentage' === $discount_type ) ? '%' : wp_travel_get_currency_symbol();

					?>
						<span><strong><?php echo $discount_value ?> ( <?php echo $symbol; ?> )</strong></span>

					<?php

				break;
				case 'max_users' :

					$coupon         = WP_Travel()->coupon;
					$max_users  = $coupon->get_coupon_meta( $id, 'restriction', 'coupon_limit_number' );

					?>
						<span><strong><?php echo $max_users ?></strong></span>

					<?php

				break;
				case 'used_so_far' :

					$coupon         = WP_Travel()->coupon;
					$used_so_far  = $coupon->get_usage_count( $id );

					?>
						<span><strong><?php echo $used_so_far ?></strong></span>

					<?php

				break;
				case 'expiration_date' :

					$coupon         = WP_Travel()->coupon;
					$expiration_date  = $coupon->get_coupon_meta( $id, 'general', 'coupon_expiry_date' );

					?>
						<span><strong><?php echo $expiration_date ?></strong></span>

					<?php

				break;

				default:
					break;
			} // end switch
		}

	}

endif;
