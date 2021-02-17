<?php
class WP_Travel_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_envira_gallery_load_image', array( $this, 'post_gallery_ajax_load_image' ) );

		// Ajax for cart
		// Add
		add_action( 'wp_ajax_wt_add_to_cart', array( $this, 'wp_travel_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_add_to_cart', array( $this, 'wp_travel_add_to_cart' ) );

		// Update
		add_action( 'wp_ajax_wt_update_cart', array( $this, 'wp_travel_update_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_update_cart', array( $this, 'wp_travel_update_cart' ) );

		// Apply Coupon
		add_action( 'wp_ajax_wt_cart_apply_coupon', array( $this, 'wt_cart_apply_coupon' ) );
		add_action( 'wp_ajax_nopriv_wt_cart_apply_coupon', array( $this, 'wt_cart_apply_coupon' ) );

		// Delete cart item
		add_action( 'wp_ajax_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );

		// Check Coupon Code
		add_action( 'wp_ajax_wp_travel_check_coupon_code', array( $this, 'wp_travel_check_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_check_coupon_code', array( $this, 'wp_travel_check_coupon_code' ) );

		// Clone Trip @since 1.7.6
		add_action( 'wp_ajax_wp_travel_clone_trip', array( $this, 'wp_travel_clone_trip' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_clone_trip', array( $this, 'wp_travel_clone_trip' ) );

	}

	/**
	 * Ajax callback function to clone trip
	 *
	 * @since 1.7.6
	 */
	public function wp_travel_clone_trip() {
		// Run a security check first.
		check_ajax_referer( 'wp_travel_clone_post_nonce', 'security' );

		if ( ! isset( $_POST['post_id'] ) ) {
			return;
		}

		$post_id   = absint( $_POST['post_id'] );
		$post_type = get_post_type( $post_id );

		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}
		$post = get_post( $post_id );

		$post_array = array(
			'post_title'   => $post->post_title,
			'post_content' => $post->post_content,
			'post_status'  => 'draft',
			'post_type'    => WP_TRAVEL_POST_TYPE,
		);

		// Cloning old trip.
		$new_post_id = wp_insert_post( $post_array );

		// Cloning old trip meta.
		$all_old_meta = get_post_meta( $post_id );

		if ( is_array( $all_old_meta ) && count( $all_old_meta ) > 0 ) {
			foreach ( $all_old_meta as $meta_key => $meta_value_array ) {
				$meta_value = isset( $meta_value_array[0] ) ? $meta_value_array[0] : '';

				if ( '' !== $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
				}
				update_post_meta( $new_post_id, $meta_key, $meta_value );
			}
		}

		// Cloning taxonomies
		$trip_taxonomies = array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' );
		foreach ( $trip_taxonomies as $taxonomy ) {
			$trip_terms      = get_the_terms( $post_id, $taxonomy );
			$trip_term_names = array();
			if ( is_array( $trip_terms ) && count( $trip_terms ) > 0 ) {
				foreach ( $trip_terms as $post_terms ) {
					$trip_term_names[] = $post_terms->name;
				}
			}
			wp_set_object_terms( $new_post_id, $trip_term_names, $taxonomy );
		}
		wp_send_json( array( 'true' ) );
	}

	public function wp_travel_check_coupon_code() {

		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['coupon_code'] ) || ! isset( $_POST['coupon_id'] ) ) {
			return;
		}

		$coupon_id   = absint( $_POST['coupon_id'] );
		$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) );

		$coupon = WP_Travel()->coupon->get_coupon_id_by_code( $coupon_code );

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

	public function wp_travel_add_to_cart() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		$http_post_data = wp_travel_sanitize_array( $_POST );
		$post_data      = json_decode( file_get_contents( 'php://input' ) );
		$post_data      = ! empty( $post_data ) ? (array) $post_data : $http_post_data;

		if ( ! isset( $post_data['trip_id'] ) ) {
			return;
		}
		global $wt_cart;

		// $allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );
		$allow_multiple_cart_items = WP_Travel_Cart::allow_multiple_items();
		error_log( 'allow multipl;e ' . $allow_multiple_cart_items );
		if ( ! $allow_multiple_cart_items ) {
			$wt_cart->clear();
		}

		$trip_id        = absint( $post_data['trip_id'] );
		$price_key      = isset( $post_data['price_key'] ) ? sanitize_text_field( $post_data['price_key'] ) : '';
		$pricing_id     = isset( $post_data['pricing_id'] ) ? absint( $post_data['pricing_id'] ) : ''; // @since 3.0.0
		$arrival_date   = isset( $post_data['arrival_date'] ) ? sanitize_text_field( $post_data['arrival_date'] ) : '';
		$departure_date = isset( $post_data['departure_date'] ) ? sanitize_text_field( $post_data['departure_date'] ) : ''; // Need to remove. is't post value.
		$pax            = isset( $post_data['pax'] ) ? (array) wp_travel_sanitize_array( $post_data['pax'] ) : 0;
		$trip_extras    = isset( $post_data['wp_travel_trip_extras'] ) ? $post_data['wp_travel_trip_extras'] : array();
		$trip_price     = 0;

		$attrs = wp_travel_get_cart_attrs( $trip_id, $pax, $price_key, $pricing_id, $arrival_date ); // pricing_id && $trip_start_date @since 4.0.0.
		if ( isset( $post_data['trip_time'] ) ) {
			$attrs['trip_time'] = $post_data['trip_time'];
		}

		$pricing_option_type = wp_travel_get_pricing_option_type( $trip_id );
		if ( ( is_object( $pax ) || is_array( $pax ) ) && 'multiple-price' === $pricing_option_type ) { // @since 3.0.0
			// if ( is_array( $pax ) && 'multiple-price' === $pricing_option_type ) { // @since 3.0.0
			$total_pax          = array_sum( $pax );
			$pricings           = wp_travel_get_trip_pricing_option( $trip_id ); // Get Pricing Options for the trip.
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

				if ( wp_travel_is_partial_payment_enabled() ) {
					$percent                = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
					$category_price_partial = ( $category_price * $percent ) / 100;
				}

				$pricing_index = null;
				foreach ( $pricing_data as $index => $pricing ) {
					if ( wp_travel_is_react_version_enabled() ) {
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
					'price'         => wp_travel_get_formated_price( $category_price ),
					'price_partial' => wp_travel_get_formated_price( $category_price_partial ),
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
			if ( wp_travel_is_partial_payment_enabled() ) {
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
					'price_partial' => wp_travel_get_formated_price( $category_price_partial ),
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
			if ( wp_travel_is_partial_payment_enabled() ) {
				$percent            = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
				$trip_price_partial = $trip_price * $percent / 100;
			}
		}

		$attrs['enable_partial'] = wp_travel_is_partial_payment_enabled();
		if ( $attrs['enable_partial'] ) {
			$trip_price_partial             = $trip_price;
			$payout_percent                 = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
			$attrs['partial_payout_figure'] = $payout_percent; // added in 1.8.4

			if ( $payout_percent > 0 ) {
				$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
				$trip_price_partial = wp_travel_get_formated_price( $trip_price_partial );
			}
			$attrs['trip_price_partial'] = $trip_price_partial;
		}

		$attrs['pricing_id']     = $pricing_id;
		$attrs['arrival_date']   = $arrival_date;
		$attrs['departure_date'] = $departure_date;
		$attrs['trip_extras']    = $trip_extras;

		$attrs = apply_filters( 'wp_travel_cart_attributes', $attrs, $post_data );

		$cart_item_id = $wt_cart->wp_travel_get_cart_item_id( $trip_id, $price_key, $arrival_date );

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
		// $wt_cart->update( $cart_item_id, $pax );
		echo true;

	}

	/**
	 * Updates Cart.
	 *
	 * @return void
	 */
	public function wp_travel_update_cart() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['update_cart_fields'] ) || count( $_POST['update_cart_fields'] ) < 1 ) {
			return;
		}

		global $wt_cart;

		$cart_fields = wp_travel_sanitize_array( $_POST['update_cart_fields'] ); // @phpcs:ignore

		foreach ( $cart_fields as $cart_field ) {

			$trip_extras = false;

			if ( isset( $cart_field['extras']['id'] ) && ! empty( $cart_field['extras']['id'] ) ) {
				$trip_extras = $cart_field['extras'];
			}

			$wt_cart->update( $cart_field['cart_id'], $cart_field['pax'], $trip_extras );
		}

		WP_Travel()->notices->add( apply_filters( 'wp_travel_cart_success', __( '<strong> </strong>Cart updated succesfully.Please Proceed to Checkout', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	public function wt_cart_apply_coupon() {
		check_ajax_referer( 'wp_travel_nonce', '_nonce' );
		if ( ! isset( $_POST['CouponCode'] ) ) {
			return;
		}

		if ( ! isset( $_POST['trip_ids'] ) ) {
			return;
		}

		if ( empty( $_POST['CouponCode'] ) ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Coupon Code cannot be empty', 'wp-travel' ) ), 'error' );

			return;
		}

		$coupon_code = sanitize_text_field( wp_unslash( $_POST['CouponCode'] ) );

		$coupon_id = WP_Travel()->coupon->get_coupon_id_by_code( $coupon_code );

		if ( ! $coupon_id ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Invalid Coupon Code', 'wp-travel' ) ), 'error' );

			return;

		}

		$date_validity = WP_Travel()->coupon->is_coupon_valid( $coupon_id );

		if ( ! $date_validity ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>The coupoun is either inactive or has expired. Coupon Code could not be applied.', 'wp-travel' ) ), 'error' );

			return;

		}

		$trip_ids = wp_travel_sanitize_array( $_POST['trip_ids'] ); // @phpcs:ignore

		$trips_validity = WP_Travel()->coupon->trip_ids_allowed( $coupon_id, $trip_ids );

		if ( ! $trips_validity ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>This coupon cannot be applied to the selected trip', 'wp-travel' ) ), 'error' );

			return;

		}

		$coupon_metas        = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab    = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$coupon_limit_number = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';

		if ( ! empty( $coupon_limit_number ) ) {

			$usage_count = WP_Travel()->coupon->get_usage_count( $coupon_id );

			if ( absint( $usage_count ) >= absint( $coupon_limit_number ) ) {

				WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Coupon Expired. Maximum no. of coupon usage exceeded.', 'wp-travel' ) ), 'error' );

				return;

			}
		}

		// Prepare Coupon Application.
		global $wt_cart;

		$discount_type   = WP_Travel()->coupon->get_discount_type( $coupon_id );
		$discount_amount = WP_Travel()->coupon->get_discount_amount( $coupon_id );

		if ( 'fixed' === $discount_type ) {
			$cart_amounts = $wt_cart->get_total( $with_discount = false );
			$total        = $cart_amounts['total'];
			if ( $discount_amount >= $total ) {
				WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Cannot apply coupon for this trip.', 'wp-travel' ) ), 'error' );
				return;
			}
		}

		$wt_cart->add_discount_values( $coupon_id, $discount_type, $discount_amount, sanitize_text_field( $_POST['CouponCode'] ) ); // $_POST['CouponCode'] @since 3.1.7

		WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong> </strong>Coupon applied succesfully.', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	public function wp_travel_remove_from_cart() {
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
