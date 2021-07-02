<?php
class WP_Travel_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_envira_gallery_load_image', array( $this, 'post_gallery_ajax_load_image' ) );

		// Ajax for cart
		// Add
		add_action( 'wp_ajax_wt_add_to_cart', array( $this, 'add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_add_to_cart', array( $this, 'add_to_cart' ) );

		// Update
		add_action( 'wp_ajax_wt_update_cart', array( $this, 'update_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_update_cart', array( $this, 'update_cart' ) );

		// Apply Coupon
		add_action( 'wp_ajax_wt_cart_apply_coupon', array( $this, 'apply_coupon' ) );
		add_action( 'wp_ajax_nopriv_wt_cart_apply_coupon', array( $this, 'apply_coupon' ) );

		// Delete cart item
		add_action( 'wp_ajax_wt_remove_from_cart', array( $this, 'remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_remove_from_cart', array( $this, 'remove_from_cart' ) );

		// Check Coupon Code
		add_action( 'wp_ajax_wp_travel_check_coupon_code', array( $this, 'check_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_check_coupon_code', array( $this, 'check_coupon_code' ) );

		// Clone Trip @since 1.7.6
		add_action( 'wp_ajax_wp_travel_clone_trip', array( $this, 'clone_trip' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_clone_trip', array( $this, 'clone_trip' ) );

	}

	/**
	 * Ajax callback function to clone trip
	 *
	 * @since 1.7.6
	 */
	public function clone_trip() {
		// Run a security check first.
		check_ajax_referer( 'wp_travel_clone_post_nonce', 'security' );

		if ( ! isset( $_POST['post_id'] ) ) {
			return;
		}
		global $wpdb;

		$trip_id   = absint( $_POST['post_id'] );
		$post_type = get_post_type( $trip_id );

		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}
		$trip = get_post( $trip_id );

		$post_array = array(
			'post_title'   => $trip->post_title,
			'post_content' => $trip->post_content,
			'post_status'  => 'draft',
			'post_type'    => WP_TRAVEL_POST_TYPE,
		);

		// Cloning trip.
		$new_trip_id = wp_insert_post( $post_array );

		// Cloning trip meta.
		$all_old_meta = get_post_meta( $trip_id );

		if ( is_array( $all_old_meta ) && count( $all_old_meta ) > 0 ) {
			foreach ( $all_old_meta as $meta_key => $meta_value_array ) {
				$meta_value = isset( $meta_value_array[0] ) ? $meta_value_array[0] : '';

				if ( '' !== $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
				}
				update_post_meta( $new_trip_id, $meta_key, $meta_value );
			}
		}

		// Cloning taxonomies.
		$trip_taxonomies = array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' );
		foreach ( $trip_taxonomies as $taxonomy ) {
			$trip_terms      = get_the_terms( $trip_id, $taxonomy );
			$trip_term_names = array();
			if ( is_array( $trip_terms ) && count( $trip_terms ) > 0 ) {
				foreach ( $trip_terms as $post_terms ) {
					$trip_term_names[] = $post_terms->name;
				}
			}
			wp_set_object_terms( $new_trip_id, $trip_term_names, $taxonomy );
		}

		// Clone Price table data.
		$pricing_ids = array(); // To add cloned pricing ids into cloned dates table.
		$pricings    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_pricings WHERE trip_id=%d", $trip_id ) );
		if ( ! empty( $pricings ) ) {
			foreach ( $pricings as $pricing ) {
				$pricing_data = array(
					'title'           => $pricing->title,
					'max_pax'         => $pricing->max_pax,
					'min_pax'         => $pricing->min_pax,
					'has_group_price' => $pricing->has_group_price,
					'group_prices'    => $pricing->group_prices,
					'trip_extras'     => $pricing->trip_extras,
				);

				$pricing_id              = $pricing->id;
				$pricing_insert_response = WpTravel_Helpers_Pricings::add_individual_pricing( $new_trip_id, $pricing_data );

				if ( is_array( $pricing_insert_response ) && isset( $pricing_insert_response['code'] ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $pricing_insert_response['code'] ) {
					$new_pricing_id     = $pricing_insert_response['pricing_id'];
					$pricing_categories = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_price_category_relation WHERE `pricing_id` = %d", $pricing_id ) );
					if ( ! empty( $pricing_categories ) ) {
						foreach ( $pricing_categories as $pricing_category ) {
							$category = array(
								'id'              => $pricing_category->pricing_category_id,
								'price_per'       => $pricing_category->price_per,
								'regular_price'   => $pricing_category->regular_price,
								'is_sale'         => $pricing_category->is_sale,
								'sale_price'      => $pricing_category->sale_price,
								'has_group_price' => $pricing_category->has_group_price,
								'group_prices'    => $pricing_category->has_group_price,
								'default_pax'     => $pricing_category->default_pax,
							);
							WpTravel_Helpers_Trip_Pricing_Categories::add_individual_pricing_category( $new_pricing_id, $category );
						}
					}
					$pricing_ids[ $new_pricing_id ] = $pricing_id; // assign pricing id to used in date table.
				}
			}
		}
		// Date Migration.
		$dates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_dates WHERE `trip_id` = %d", $trip_id ) );
		if ( ! empty( $dates ) ) {
			foreach ( $dates as $date ) {

				// Add new pricing id in newly inserted date.
				$pricing_ids_of_date  = $date->pricing_ids;
				$new_temp_ids = array();
				if ( ! empty( $pricing_ids_of_date ) ) {
					$temp_ids = explode( ',', $pricing_ids_of_date );
					foreach ( $temp_ids as $temp_id ) {
						if ( false !== $key = array_search( $temp_id, $pricing_ids ) ) {
							$new_temp_ids[] = $key;
						}
					}
				}
				$new_pricing_ids = implode( ',', $new_temp_ids );

				$new_date = array(
					'title'       => $date->title,
					'recurring'   => $date->recurring,
					'years'       => $date->years,
					'months'      => $date->months,
					'weeks'       => $date->weeks,
					'days'        => $date->days,
					'date_days'   => $date->date_days,
					'start_date'  => $date->start_date,
					'end_date'    => $date->end_date,
					'trip_time'   => $date->trip_time,
					'pricing_ids' => $new_pricing_ids,
				);
				WpTravel_Helpers_Trip_Dates::add_individual_date( $new_trip_id, $new_date );
			}
		}
		// Exclued Date Migration.
		$exclude_dates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_excluded_dates_times WHERE `trip_id` = %d", $trip_id ) );
		if ( ! empty( $exclude_dates ) ) {
			foreach ( $exclude_dates as $exclude_date ) {
				$new_exclude_date = array(
					'title'      => $exclude_date->title,
					'recurring'  => $exclude_date->recurring,
					'years'      => $exclude_date->years,
					'months'     => $exclude_date->months,
					'weeks'      => $exclude_date->weeks,
					'days'       => $exclude_date->days,
					'date_days'  => $exclude_date->date_days,
					'start_date' => $exclude_date->start_date,
					'end_date'   => $exclude_date->end_date,
					'time'       => $exclude_date->trip_time,
				);
				WpTravel_Helpers_Trip_Excluded_Dates_Times::add_individual_date_time( $new_trip_id, $new_exclude_date );
			}
		}
		wp_send_json( array( 'true' ) );
	}

	public function check_coupon_code() {

		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['coupon_code'] ) || ! isset( $_POST['coupon_id'] ) ) {
			return;
		}

		$coupon_id   = absint( $_POST['coupon_id'] );
		$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) );

		$coupon = WPTravel()->coupon->get_coupon_id_by_code( $coupon_code );

		if ( ! $coupon || $coupon_id === $coupon ) {

			wp_send_json_success( $coupon_code );
		}

		wp_send_json_error( $coupon_code );

	}

	public function post_gallery_ajax_load_image() {
		// Run a security check first.
		check_ajax_referer( 'wp-travel-drag-drop-nonce', 'nonce' );

		if ( ! isset( $_POST['id'] ) ) {
			return;
		}
		// Prepare variables.
		$id = absint( $_POST['id'] );
		echo wp_json_encode(
			array(
				'id'  => $id,
				'url' => wp_get_attachment_thumb_url( $id ),
			)
		);
		exit;
	}

	public function add_to_cart() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$http_post_data = wptravel_sanitize_array( $_POST ); // phpcs:ignore
		$post_data      = json_decode( file_get_contents( 'php://input' ) );
		$post_data      = wptravel_sanitize_array( $post_data );
		$post_data      = ! empty( $post_data ) ? (array) $post_data : $http_post_data;

		if ( ! isset( $post_data['trip_id'] ) ) {
			return;
		}

		$skip_cart = apply_filters( 'wptravel_skip_add_to_cart', false );
		if ( $skip_cart ) {
			return;
		}

		wptravel_nocache_headers();
		global $wt_cart;

		$allow_multiple_cart_items = WP_Travel_Cart::allow_multiple_items();
		if ( ! $allow_multiple_cart_items ) {
			$wt_cart->clear();
		}

		$trip_id   = absint( $post_data['trip_id'] );
		$price_key = isset( $post_data['price_key'] ) ? sanitize_text_field( $post_data['price_key'] ) : '';
		// Pricing id contains string for legacy version below WP Travel 3.0.0.
		$pricing_id     = isset( $post_data['pricing_id'] ) ? sanitize_text_field( $post_data['pricing_id'] ) : ''; // @since 3.0.0
		$arrival_date   = isset( $post_data['arrival_date'] ) ? sanitize_text_field( $post_data['arrival_date'] ) : '';
		$departure_date = isset( $post_data['departure_date'] ) ? sanitize_text_field( $post_data['departure_date'] ) : ''; // Need to remove. is't post value.
		$pax            = isset( $post_data['pax'] ) ? (array) wptravel_sanitize_array( $post_data['pax'] ) : 0;
		$trip_extras    = isset( $post_data['wp_travel_trip_extras'] ) ? $post_data['wp_travel_trip_extras'] : array();
		$trip_price     = 0;

		$args  = array(
			'trip_id'         => $trip_id,
			'pax'             => $pax,
			'price_key'       => $price_key,
			'pricing_id'      => $pricing_id,
			'trip_start_date' => $arrival_date,
			'return_price'    => false,
			'request_data'    => $post_data,
		);
		$attrs = wptravel_get_cart_attrs( $args ); // pricing_id && $trip_start_date @since 4.0.0.
		if ( isset( $post_data['trip_time'] ) ) {
			$attrs['trip_time'] = $post_data['trip_time'];
		}

		$pricing_option_type = wptravel_get_pricing_option_type( $trip_id );
		if ( ( is_object( $pax ) || is_array( $pax ) ) && 'multiple-price' === $pricing_option_type ) { // @since 3.0.0
			// if ( is_array( $pax ) && 'multiple-price' === $pricing_option_type ) { // @since 3.0.0
			$total_pax          = array_sum( $pax );
			$pricings           = wptravel_get_trip_pricing_option( $trip_id ); // Get Pricing Options for the trip.
			$pricing_data       = isset( $pricings['pricing_data'] ) ? $pricings['pricing_data'] : array();
			$trip               = array();
			$trip_price_partial = 0;

			foreach ( $pax as $category_id => $pax_value ) {
				$args           = array(
					'trip_id'     => $trip_id,
					'pricing_id'  => $pricing_id,
					'category_id' => $category_id,
					'price_key'   => $price_key,
				);
				$category_price = WP_Travel_Helpers_Pricings::get_price( $args );
				if ( function_exists( 'wp_travel_group_discount_price' ) ) { // From Group Discount addons.
					$group_trip_price = wp_travel_group_discount_price( $trip_id, $pax_value, $pricing_id, $category_id );

					if ( $group_trip_price ) {
						$category_price = $group_trip_price;
					}
				}
				$category_price_partial = $category_price;

				if ( wptravel_is_partial_payment_enabled() ) {
					$percent                = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
					$category_price_partial = ( $category_price * $percent ) / 100;
				}

				$pricing_index = null;
				foreach ( $pricing_data as $index => $pricing ) {
					if ( wptravel_is_react_version_enabled() ) {
						if ( (int) $pricing_id === (int) $pricing['pricing_id'] ) {
							$pricing_index = $index;
							break;
						}
						continue;
					}
					if ( isset( $pricing['categories'] ) && is_array( $pricing['categories'] ) ) {
						if ( array_key_exists( $category_id, $pricing['categories'] ) ) {
							$pricing_index = $index;
							break;
						};
					}
				}
				$category = isset( $pricing_data[ $pricing_index ]['categories'][ $category_id ] ) ? $pricing_data[ $pricing_index ]['categories'][ $category_id ] : array();

				$catetory_type = isset( $category['type'] ) ? $category['type'] : ''; // Old Way to get type in WP Travel.
				if ( empty( $catetory_type ) && is_numeric( $category_id ) ) { // Set category type if category is taxonomy term.
					$pricing_category = get_term( $category_id, 'itinerary_pricing_category' );
					$catetory_type    = $pricing_category->name;
				}

				$trip[ $category_id ] = array(
					'pax'           => $pax_value,
					'price'         => wptravel_get_formated_price( $category_price ),
					'price_partial' => wptravel_get_formated_price( $category_price_partial ),
					'type'          => $catetory_type,
					'custom_label'  => isset( $category['custom_label'] ) ? $category['custom_label'] : __( 'Custom', 'wp-travel' ),
					'price_per'     => isset( $category['price_per'] ) ? $category['price_per'] : 'person',
				);

				// multiply category_price by pax to add in trip price if price per is person.
				if ( 'person' == $trip[ $category_id ]['price_per'] ) {
					$category_price         *= $pax_value;
					$category_price_partial *= $pax_value;
				}
				// add price.
				$trip_price         += $category_price;
				$trip_price_partial += $category_price_partial;
			}
			$attrs['trip'] = $trip;
			// $pax   = $total_pax;
		} else {
			$pax       = array_sum( (array) $pax );
			$price_per = get_post_meta( $trip_id, 'wp_travel_price_per', true );
			$price_per = ! empty( $price_per ) ? $price_per : 'person';
			// multiply category_price by pax to add in trip price if price per is person.
			$args       = array(
				'trip_id'   => $trip_id,
				'price_key' => $price_key,
			);
			$price      = WP_Travel_Helpers_Pricings::get_price( $args );
			$trip_price = $price;
			if ( wptravel_is_partial_payment_enabled() ) {
				$percent                = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
				$category_price_partial = ( $trip_price * $percent ) / 100;
			}
			if ( 'person' == $price_per ) {
				$trip_price = $price * $pax;
			}

			// Custom Trip Price.
			if ( isset( $post_data['trip_price'] ) && $post_data['trip_price'] > 0 ) {
				$trip_price = $post_data['trip_price'];
			}
			$attrs['trip'] = array(
				"category-{$trip_id}" => array( // assigned category for single pricing to match data structure @since 3.0.0
					'pax'           => $pax,
					'price'         => $price,
					'price_partial' => wptravel_get_formated_price( $category_price_partial ),
					'type'          => 'adult', // Not set yet.
					'custom_label'  => __( 'Custom', 'wp-travel' ),
					'price_per'     => $price_per,
					'trip_price'    => $trip_price,
				),
			);

			if ( function_exists( 'wp_travel_group_discount_price' ) && 'single-pricing-id' !== $pricing_id ) { // From Group Discount addons.
				$group_trip_price = wp_travel_group_discount_price( $trip_id, $pax, $pricing_id, $pricing_id ); // for old price pricing id is treated as category id.
				if ( $group_trip_price ) {
					$trip_price = $group_trip_price;
				}
			}
		}

		// Custom Trip Price.
		if ( isset( $post_data['trip_price'] ) && $post_data['trip_price'] > 0 ) {
			$trip_price = $post_data['trip_price'];
			if ( wptravel_is_partial_payment_enabled() ) {
				$percent            = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
				$trip_price_partial = $trip_price * $percent / 100;
			}
		}

		$attrs['enable_partial'] = wptravel_is_partial_payment_enabled();
		if ( $attrs['enable_partial'] ) {
			$trip_price_partial             = $trip_price;
			$payout_percent                 = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
			$attrs['partial_payout_figure'] = $payout_percent; // added in 1.8.4

			if ( $payout_percent > 0 ) {
				$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
				$trip_price_partial = wptravel_get_formated_price( $trip_price_partial );
			}
			$attrs['trip_price_partial'] = $trip_price_partial;
		}

		$attrs['pricing_id']     = $pricing_id;
		$attrs['arrival_date']   = $arrival_date;
		$attrs['departure_date'] = $departure_date;
		$attrs['trip_extras']    = $trip_extras;

		$attrs = apply_filters( 'wp_travel_cart_attributes', $attrs, $post_data );

		$cart_item_id = $wt_cart->wptravel_get_cart_item_id( $trip_id, $price_key, $arrival_date );

		$update_cart_on_add = apply_filters( 'wp_travel_filter_update_cart_on_add', true );

		$add_to_cart_args = array(
			'trip_id'            => $trip_id,
			'trip_price'         => $trip_price,
			'trip_price_partial' => $trip_price_partial,
			'pax'                => $total_pax,
			'price_key'          => $price_key,
			'attrs'              => $attrs,
		);
		if ( true === $update_cart_on_add ) {
			$items = $wt_cart->getItems();

			if ( isset( $items[ $cart_item_id ] ) ) {
				if ( is_array( $pax ) ) {
					$trip_extras = isset( $post_data['wp_travel_trip_extras'] ) ? (array) $post_data['wp_travel_trip_extras'] : array();
					$wt_cart->update( $cart_item_id, $pax, $trip_extras, $post_data );
				} else {
					$pax += $items[ $cart_item_id ]['pax'];
					$wt_cart->update( $cart_item_id, $pax );
				}
			} else {
				$wt_cart->add( $add_to_cart_args );
			}
		} else {
			$wt_cart->add( $add_to_cart_args );
		}
		// Need to update cart add. in calse of multiple items partial figure need to update in individual item
		echo true;

	}

	/**
	 * Updates Cart.
	 *
	 * @return void
	 */
	public function update_cart() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['update_cart_fields'] ) || count( $_POST['update_cart_fields'] ) < 1 ) {
			return;
		}

		global $wt_cart;

		$cart_fields = wptravel_sanitize_array( $_POST['update_cart_fields'] ); // @phpcs:ignore

		foreach ( $cart_fields as $cart_field ) {

			$trip_extras = false;

			if ( isset( $cart_field['extras']['id'] ) && ! empty( $cart_field['extras']['id'] ) ) {
				$trip_extras = $cart_field['extras'];
			}

			$wt_cart->update( $cart_field['cart_id'], $cart_field['pax'], $trip_extras );
		}

		WPTravel()->notices->add( apply_filters( 'wp_travel_cart_success', __( '<strong> </strong>Cart updated succesfully.Please Proceed to Checkout', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	public function apply_coupon() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['CouponCode'] ) ) {
			return;
		}

		if ( ! isset( $_POST['trip_ids'] ) ) {
			return;
		}

		if ( empty( $_POST['CouponCode'] ) ) {

			WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'Coupon Code cannot be empty', 'wp-travel' ) ), 'error' );

			return;
		}

		$coupon_code = sanitize_text_field( wp_unslash( $_POST['CouponCode'] ) );

		$coupon_id = WPTravel()->coupon->get_coupon_id_by_code( $coupon_code );

		if ( ! $coupon_id ) {

			WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'Invalid Coupon Code', 'wp-travel' ) ), 'error' );

			return;

		}

		$date_validity = WPTravel()->coupon->is_coupon_valid( $coupon_id );

		if ( ! $date_validity ) {

			WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'The coupoun is either inactive or has expired. Coupon Code could not be applied.', 'wp-travel' ) ), 'error' );

			return;

		}

		$trip_ids = wptravel_sanitize_array( $_POST['trip_ids'] ); // @phpcs:ignore

		$trips_validity = WPTravel()->coupon->trip_ids_allowed( $coupon_id, $trip_ids );

		if ( ! $trips_validity ) {

			WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'This coupon cannot be applied to the selected trip', 'wp-travel' ) ), 'error' );

			return;

		}

		$coupon_metas        = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab    = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$coupon_limit_number = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';

		if ( ! empty( $coupon_limit_number ) ) {

			$usage_count = WPTravel()->coupon->get_usage_count( $coupon_id );

			if ( absint( $usage_count ) >= absint( $coupon_limit_number ) ) {

				WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'Coupon Expired. Maximum no. of coupon usage exceeded.', 'wp-travel' ) ), 'error' );

				return;

			}
		}

		// Prepare Coupon Application.
		global $wt_cart;

		$discount_type   = WPTravel()->coupon->get_discount_type( $coupon_id );
		$discount_amount = WPTravel()->coupon->get_discount_amount( $coupon_id );

		if ( 'fixed' === $discount_type ) {
			$cart_amounts = $wt_cart->get_total( $with_discount = false );
			$total        = $cart_amounts['total'];
			if ( $discount_amount >= $total ) {
				WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'Cannot apply coupon for this trip.', 'wp-travel' ) ), 'error' );
				return;
			}
		}

		$wt_cart->add_discount_values( $coupon_id, $discount_type, $discount_amount, sanitize_text_field( $_POST['CouponCode'] ) ); // $_POST['CouponCode'] @since 3.1.7

		WPTravel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( 'Coupon applied succesfully.', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	public function remove_from_cart() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_REQUEST['cart_id'] ) ) {
			return;
		}
		global $wt_cart;

		$wt_cart->remove( sanitize_text_field( wp_unslash( $_REQUEST['cart_id'] ) ) );
		return true;
	}


}
new WP_Travel_Ajax();
