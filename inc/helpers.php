<?php
/**
 * Helper Functions.
 *
 * @package wp-travel/inc
 */

require sprintf( '%s/inc/helpers/helpers-price.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/helpers/helpers-stat.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/helpers/helpers-fontawesome.php', WP_TRAVEL_ABSPATH );
/**
 * Return all Gallery ID of specific post.
 *
 * @param  int $post_id ID f the post.
 * @return array Return gallery ids.
 */
function wptravel_get_gallery_ids( $post_id ) {
	$gallery_ids = get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	if ( false === $gallery_ids || empty( $gallery_ids ) ) {
		return false;
	}
	return $gallery_ids;
}

// @since 1.9.0
function wptravel_settings_default_fields() {

	// Booking Admin Defaults.
	$booking_admin_email_defaults = array(
		'admin_subject'      => __( 'New Booking', 'wp-travel' ),
		'admin_title'        => __( 'New Booking', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wptravel_booking_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	// Booking client Defaults.
	$booking_client_email_defaults = array(
		'client_subject'      => __( 'Booking Received', 'wp-travel' ),
		'client_title'        => __( 'Booking Received', 'wp-travel' ),
		'client_header_color' => '',
		'email_content'       => wptravel_booking_client_default_email_content(),
		'from_email'          => get_option( 'admin_email' ),
	);

	// Payment Admin Defaults.
	$payment_admin_email_defaults = array(
		'admin_subject'      => __( 'New Booking', 'wp-travel' ),
		'admin_title'        => __( 'New Booking', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wptravel_payment_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	// Payment client Defaults.
	$payment_client_email_defaults = array(
		'client_subject'      => __( 'Payment Received', 'wp-travel' ),
		'client_title'        => __( 'Payment Received', 'wp-travel' ),
		'client_header_color' => '',
		'email_content'       => wptravel_payment_client_default_email_content(),
		'from_email'          => get_option( 'admin_email' ),
	);

	// emquiry Admin Defaults.
	$enquiry_admin_email_defaults = array(
		'admin_subject'      => __( 'New Enquiry', 'wp-travel' ),
		'admin_title'        => __( 'New Enquiry', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wptravel_enquiries_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	$settings_fields = array(
		// General Settings Fields.
		'currency'                                => 'USD',
		'use_currency_name'                       => 'no',
		'currency_position'                       => 'left',
		'thousand_separator'                      => ',',
		'decimal_separator'                       => '.',
		'number_of_decimals'                      => 2,
		'wp_travel_map'                           => 'google-map',
		'google_map_api_key'                      => '',
		'google_map_zoom_level'                   => 15,

		'cart_page_id'                            => wptravel_get_page_id( 'wp-travel-cart' ),
		'checkout_page_id'                        => wptravel_get_page_id( 'wp-travel-checkout' ),
		'dashboard_page_id'                       => wptravel_get_page_id( 'wp-travel-dashboard' ),

		// Trip Settings Fields.
		'hide_related_itinerary'                  => 'no',
		'enable_expired_trip_option'              => 'no',
		'expired_trip_set_to'                     => 'expired',
		'wp_travel_switch_to_react'               => 'no',
		'enable_multiple_travellers'              => 'no',
		'enable_multiple_category_on_pricing'     => 'yes', // This settings isn't visible for new user. So, it is always on for new settings. it means only new category layout will show in the admin and frontend.
		'trip_pricing_options_layout'             => 'by-pricing-option',

		// Email Settings Fields.
		'wp_travel_from_email'                    => get_option( 'admin_email' ),
		'send_booking_email_to_admin'             => 'yes',
		'booking_admin_template_settings'         => $booking_admin_email_defaults, // _settings appended in legacy version <= 1.8.9 settings.
		'booking_client_template_settings'        => $booking_client_email_defaults, // _settings appended in legacy version <= 1.8.9 settings.
		'payment_admin_template_settings'         => $payment_admin_email_defaults, // _settings appended in legacy version <= 1.8.9 settings.
		'payment_client_template_settings'        => $payment_client_email_defaults, // _settings appended in legacy version <= 1.8.9 settings.
		'enquiry_admin_template_settings'         => $enquiry_admin_email_defaults, // _settings appended in legacy version <= 1.8.9 settings.

		// Account Settings Fields.
		'enable_checkout_customer_registration'   => 'no',
		'enable_my_account_customer_registration' => 'yes',
		'create_user_while_booking'               => 'no',
		'generate_username_from_email'            => 'no',
		'generate_user_password'                  => 'no',

		// Tabs Settings Fields.
		'global_tab_settings'                     => wptravel_get_default_trip_tabs( true ), // @since 1.1.1 Global tabs settings.

		// Payment Settings Fields.
		'partial_payment'                         => 'no',
		'minimum_partial_payout'                  => WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT,
		'payment_option_paypal'                   => 'no',
		'paypal_email'                            => '',
		'trip_tax_enable'                         => 'no',
		'trip_tax_price_inclusive'                => 'yes',
		'trip_tax_percentage'                     => 13,

		'sorted_gateways'                         => wptravel_payment_gateway_lists(),

		// Fact Tab Settings Fields.
		'wp_travel_trip_facts_enable'             => 'yes',
		'wp_travel_trip_facts_settings'           => array(),

		// Misc Settings Fields.
		'enable_trip_enquiry_option'              => 'yes', // @since 1.2 Misc. Options
		'enable_og_tags'                          => 'no', // @since 1.7.6 Misc. Option
		'wp_travel_gdpr_message'                  => __( 'By contacting us, you agree to our ', 'wp-travel' ),
		'open_gdpr_in_new_tab'                    => 'no',

		// Debug Settings field.
		'wt_test_mode'                            => 'yes',
		'wt_test_email'                           => '',

		/**
		 * Load Optimized assets.
		 *
		 * @since 4.0.6
		 */
		'wt_load_optimized_script'                => 'no',

		/**
		 * @since 4.0.7
		 */
		'calender_view'                           => 'no',

		/**
		 * @since 4.4.5
		 */
		'trip_date_listing'                       => 'calendar', // calendar | dates.
	);

	$user_since = get_option( 'wp_travel_user_since' );
	if ( version_compare( $user_since, '4.0.0', '>=' ) ) {
		$settings_fields['wp_travel_switch_to_react'] = 'yes';
	}
	return apply_filters( 'wp_travel_settings_fields', $settings_fields ); // flter @since 1.9.0.
}

/** Return All Settings of WP travel. */
function wptravel_get_settings() {
	$default_settings = wptravel_settings_default_fields();
	$settings         = get_option( 'wp_travel_settings' ) ? get_option( 'wp_travel_settings' ) : array();

	$settings = array_merge( $default_settings, $settings );
	return $settings;
}

/**
 * Return Trip Code.
 *
 * @param  int $post_id Post ID of post.
 * @return string Returns the trip code.
 */
function wptravel_get_trip_code( $post_id = null ) {
	if ( ! is_null( $post_id ) ) {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $post_id ) );
	} else {
		global $post;
		$wp_travel_itinerary = new WP_Travel_Itinerary( $post );
	}

	return $wp_travel_itinerary->get_trip_code();
}

/**
 * Return dropdown.
 *
 * @param  array $args Arguments for dropdown list.
 * @return HTML  return dropdown list.
 */
function wptravel_get_dropdown_currency_list( $args = array() ) {

	$currency_list = wptravel_get_currency_list();

	$defaults = array(
		'id'         => '',
		'class'      => '',
		'name'       => '',
		'option'     => '',
		'options'    => '',
		'selected'   => '',
		'attributes' => array(),
	);

	$args     = wp_parse_args( $args, $defaults );
	$dropdown = '';
	if ( is_array( $currency_list ) && count( $currency_list ) > 0 ) {
		$attributes = '';
		if ( ! empty( $args['attributes'] ) ) {
			foreach ( $args['attributes'] as $key => $value ) {
				$attributes .= sprintf( $key . '="%s" ', $value );
			}
		}
		$dropdown .= '<select name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" ' . $attributes . '>';
		if ( '' != $args['option'] ) {
			$dropdown .= '<option value="" >' . $args['option'] . '</option>';
		}

		foreach ( $currency_list as $key => $currency ) {

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $currency . ' (' . wptravel_get_currency_symbol( $key ) . ')</option>';
		}
		$dropdown .= '</select>';

	}

	return $dropdown;
}

/**
 * Return dropdown. [ need to depricate function with this function  ]
 *
 * @param  array $args Arguments for dropdown list.
 *
 * @since   1.7.6
 * @return HTML  return dropdown list.
 */
function wptravel_get_dropdown_list( $args = array() ) {

	$defaults = array(
		'id'           => '',
		'class'        => '',
		'name'         => '',
		'option'       => '',
		'options'      => '',
		'selected'     => '',
		'before_label' => '',
		'after_label'  => '',
		'attributes'   => array(),
	);

	$args = wp_parse_args( $args, $defaults );

	$options = $args['options'];

	$dropdown = '';
	if ( is_array( $options ) && count( $options ) > 0 ) {
		$attributes = '';
		if ( ! empty( $args['attributes'] ) ) {
			foreach ( $args['attributes'] as $key => $value ) {
				$attributes .= sprintf( $key . '="%s" ', $value );
			}
		}
		$dropdown .= '<select name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" ' . $attributes . '>';
		if ( '' != $args['option'] ) {
			$dropdown .= '<option value="" >' . $args['option'] . '</option>';
		}

		foreach ( $options as $key => $label ) {

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $label . '</option>';
		}
		$dropdown .= '</select>';

	}

	return $dropdown;
}

function wptravel_sanitize_array( $array ) {
	if ( is_string( $array ) ) {
		$array = sanitize_text_field( $array );
	} elseif ( is_array( $array ) ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = wptravel_sanitize_array( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}
	}

	return $array;
}

/**
 * List all avalable and selceted maps data.
 *
 * @since 1.7.6
 * Return Array list
 */
function wptravel_get_maps() {

	$map_key  = 'google-map';
	$settings = wptravel_get_settings();

	$wp_travel_maps = array( $map_key => __( 'Google Map', 'wp-travel' ) );
	$wp_travel_maps = apply_filters( 'wp_travel_maps', $wp_travel_maps );

	$selected_map = ( isset( $settings['wp_travel_map'] ) && in_array( $settings['wp_travel_map'], array_keys( $wp_travel_maps ) ) ) ? $settings['wp_travel_map'] : $map_key;

	$map = array(
		'maps'     => $wp_travel_maps,
		'selected' => $selected_map,
	);
	return $map;
}

/**
 * Get Map Data.
 */
function wptravel_get_map_data( $trip_id = null ) {
	if ( ! $trip_id ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$trip_id = $post->ID;
	}

	$lat = ( '' != get_post_meta( $trip_id, 'wp_travel_lat', true ) ) ? get_post_meta( $trip_id, 'wp_travel_lat', true ) : '';
	$lng = ( '' != get_post_meta( $trip_id, 'wp_travel_lng', true ) ) ? get_post_meta( $trip_id, 'wp_travel_lng', true ) : '';
	$loc = ( '' != get_post_meta( $trip_id, 'wp_travel_location', true ) ) ? get_post_meta( $trip_id, 'wp_travel_location', true ) : '';

	$map_meta = array(
		'lat' => $lat,
		'lng' => $lng,
		'loc' => $loc,
	);
	return $map_meta;
}

/**
 * Return Related post HTML.
 *
 * @param Number $post_id Post ID of current post.
 * @return void
 */
function wptravel_get_related_post( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
	/**
	 * Load template for related trips.
	 */
	echo wptravel_get_template_html( 'content-related-posts.php', $post_id );

}

/**
 * Get post thumbnail.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image tag.
 */
function wptravel_get_post_thumbnail( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	$size      = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
	$thumbnail = get_the_post_thumbnail( $post_id, $size );

	if ( ! $thumbnail ) {
		$placeholder_image_url = wptravel_get_post_placeholder_image_url();
		$thumbnail             = '<img width="100%" height="100%" src="' . $placeholder_image_url . '">';
	}
	return $thumbnail;
}

/**
 * Get post thumbnail URL.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image URL.
 */
function wptravel_get_post_thumbnail_url( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( ! $post_id ) {
		return;
	}
	$size          = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
	$thumbnail_url = get_the_post_thumbnail_url( $post_id, $size );

	if ( ! $thumbnail_url ) {
		$thumbnail_url = wptravel_get_post_placeholder_image_url();
	}
	return $thumbnail_url;
}

/**
 * Post palceholder image URL.
 *
 * @return string Placeholder image URL.
 */
function wptravel_get_post_placeholder_image_url() {
	$thumbnail_url = plugins_url( '/wp-travel/assets/images/wp-travel-placeholder.png' );
	return apply_filters( 'wp_travel_placeholder_image_url', $thumbnail_url ); // filter since WP Travel 4.4.2
}

/**
 * Allowed tags.
 *
 * @param array $tags filter tags.
 * @return array allowed tags.
 */
function wptravel_allowed_html( $tags = array() ) {

	$allowed_tags = array(
		'a'          => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr'       => array(
			'title' => array(),
		),
		'b'          => array(),
		'blockquote' => array(
			'cite' => array(),
		),
		'cite'       => array(
			'title' => array(),
		),
		'code'       => array(),
		'del'        => array(
			'datetime' => array(),
			'title'    => array(),
		),
		'dd'         => array(),
		'div'        => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'dl'         => array(),
		'dt'         => array(),
		'em'         => array(),
		'h1'         => array(),
		'h2'         => array(),
		'h3'         => array(),
		'h4'         => array(),
		'h5'         => array(),
		'h6'         => array(),
		'i'          => array(),
		'img'        => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li'         => array(
			'class' => array(),
		),
		'ol'         => array(
			'class' => array(),
		),
		'p'          => array(
			'class' => array(),
		),
		'q'          => array(
			'cite'  => array(),
			'title' => array(),
		),
		'span'       => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike'     => array(),
		'strong'     => array(),
		'ul'         => array(
			'class' => array(),
		),
	);

	if ( ! empty( $tags ) ) {
		$output = array();
		foreach ( $tags as $key ) {
			if ( array_key_exists( $key, $allowed_tags ) ) {
				$output[ $key ] = $allowed_tags[ $key ];
			}
		}
		return $output;
	}
	return $allowed_tags;
}

/**
 * Return array list of itinerary.
 *
 * @return Array
 */
function wptravel_get_itineraries_array() {
	$args = array(
		'post_type'   => WP_TRAVEL_POST_TYPE,
		'numberposts' => -1,
	);

	$itineraries = get_posts( $args );

	$itineraries_array = array();
	foreach ( $itineraries as $itinerary ) {
		$itineraries_array[ $itinerary->ID ] = $itinerary->post_title;
	}
	return apply_filters( 'wp_travel_itineraries_array', $itineraries_array, $args );
}

/**
 * Return array list of itinerary.
 *
 * @return Array
 */
function wptravel_get_tour_extras_array() {
	$args = array(
		'post_type'   => 'tour-extras',
		'numberposts' => -1,
	);

	$itineraries = get_posts( $args );

	$itineraries_array = array();
	foreach ( $itineraries as $itinerary ) {
		$itineraries_array[ $itinerary->ID ] = $itinerary->post_title;
	}
	return apply_filters( 'wp_travel_tour_extras_array', $itineraries_array, $args );
}

/**
 * Return JSON Encoded Itinerary price oblect
 */
function wptravel_get_itinereries_prices_array() {

	$itineraries = wptravel_get_itineraries_array();

	$prices = array();

	if ( $itineraries ) {

		foreach ( $itineraries as $trip_id => $itinerary ) {
			$args     = array( 'trip_id' => $trip_id );
			$prices[] = WP_Travel_Helpers_Pricings::get_price( $args );
		}
		if ( is_array( $prices ) && '' !== $prices ) :
			return $prices;
		endif;
	}
	return false;
}

/**
 * Return WP Travel Featured post.
 *
 * @param integer $no_of_post_to_show No of post to show.
 * @return array
 */
function wptravel_featured_itineraries( $no_of_post_to_show = 3 ) {
	$args        = array(
		'numberposts'      => $no_of_post_to_show,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'meta_key'         => 'wp_travel_featured',
		'meta_value'       => 'yes',
		'post_type'        => WP_TRAVEL_POST_TYPE,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);
	$posts_array = get_posts( $args );
	return $posts_array;
}


/**
 * Show WP Travel search form.
 *
 * @since  1.0.2
 */
function wptravel_search_form() {

	$submission_get = array();

	if ( isset( $_GET['__wp_travel_search_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['__wp_travel_search_nonce'] ) ), '__wp_travel_search_nonce_action' ) ) {
		$submission_get = wptravel_sanitize_array( wp_unslash( $_GET ) );
	}

	$label_string = apply_filters(
		'wp_travel_search_filter_label_strings',
		array(
			'search'        => __( 'Search:', 'wp-travel' ),
			'trip_type'     => __( 'Trip Type:', 'wp-travel' ),
			'location'      => __( 'Location:', 'wp-travel' ),
			'search_button' => __( 'Search', 'wp-travel' ),
		)
	);

	$search_string        = ! empty( $label_string['search'] ) ? $label_string['search'] : '';
	$trip_type_string     = ! empty( $label_string['trip_type'] ) ? $label_string['trip_type'] : '';
	$location_string      = ! empty( $label_string['location'] ) ? $label_string['location'] : '';
	$search_button_string = ! empty( $label_string['search_button'] ) ? $label_string['search_button'] : '';
	ob_start(); ?>
	<div class="wp-travel-search">
		<form method="get" name="wp-travel_search" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
			<input type="hidden" name="post_type" value="<?php echo esc_attr( WP_TRAVEL_POST_TYPE ); ?>" />
			<p>
				<label><?php echo esc_html( $search_string ); ?></label>
				<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
				<!-- <input type="text" name="s" id="s" value="<?php // echo ( isset( $_GET['s'] ) ) ? esc_textarea( $_GET['s'] ) : ''; ?>" placeholder="<?php // echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>"> -->
				<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
			</p>
			<p>
				<label><?php echo esc_html( $trip_type_string ); ?></label>
				<?php
				$taxonomy = 'itinerary_types';
				$args     = array(
					'show_option_all' => __( 'All', 'wp-travel' ),
					'hide_empty'      => 0,
					'selected'        => 1,
					'hierarchical'    => 1,
					'name'            => $taxonomy,
					'class'           => 'wp-travel-taxonomy',
					'taxonomy'        => $taxonomy,
					'selected'        => ( isset( $submission_get[ $taxonomy ] ) ) ? esc_textarea( $submission_get[ $taxonomy ] ) : 0,
					'value_field'     => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>
			<p>
				<label><?php echo esc_html( $location_string ); ?></label>
				<?php
				$taxonomy = 'travel_locations';
				$args     = array(
					'show_option_all' => __( 'All', 'wp-travel' ),
					'hide_empty'      => 0,
					'selected'        => 1,
					'hierarchical'    => 1,
					'name'            => $taxonomy,
					'class'           => 'wp-travel-taxonomy',
					'taxonomy'        => $taxonomy,
					'selected'        => ( isset( $submission_get[ $taxonomy ] ) ) ? esc_textarea( $submission_get[ $taxonomy ] ) : 0,
					'value_field'     => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>

			<?php wp_nonce_field( '_wp_travel_search_nonce_action', '_wp_travel_search_nonce' ); ?>

			<p class="wp-travel-search"><input type="submit" name="wp-travel_search" id="wp-travel-search" class="button button-primary" value="<?php echo esc_attr( $search_button_string ); ?>"  /></p>
		</form>
	</div>
	<?php
	$content = apply_filters( 'wptravel_search_form', ob_get_clean() );
	echo $content; // @phpcs:ignore
}

/**
 * This will optput Trip duration HTML
 *
 * @param int $post_id Post ID.
 * @return void
 */
function wptravel_get_trip_duration( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $post_id );
	ob_start();
	if ( $fixed_departure ) :
		?>
		<div class="wp-travel-trip-time trip-duration">
			<i class="wt-icon-regular wt-icon-clock"></i>
			<span class="wp-travel-trip-duration">
				<?php echo wptravel_get_fixed_departure_date( $post_id ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
		<?php
	else :
		?>
		<?php
		$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
		$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
		?>
		<div class="wp-travel-trip-time trip-duration">
			<i class="wt-icon-regular wt-icon-clock"></i>
			<span class="wp-travel-trip-duration">
				<?php if ( (int) $trip_duration > 0 ) : ?>
					<?php echo esc_html( $trip_duration . __( ' Days', 'wp-travel' ) ); ?>
				<?php else : ?>
					<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
				<?php endif; ?>
			</span>
		</div>
		<?php
	endif;
	$content = ob_get_contents();
	ob_end_clean();
	$content = apply_filters( 'wp_travel_trip_duration', $content, $post_id );
	echo $content; // phpcs:ignore
}

/**
 * Get Payment Status List.
 *
 * @since 1.0.6
 */
function wptravel_get_payment_status() {
	$status = array(
		'pending'          => array(
			'color' => '#FF9800',
			'text'  => __( 'Pending', 'wp-travel' ),
		),
		'partially_paid'   => array(
			'color' => '#FF9800',
			'text'  => __( 'Partially Paid', 'wp-travel' ),
		),
		'paid'             => array(
			'color' => '#008600',
			'text'  => __( 'Paid', 'wp-travel' ),
		),
		'waiting_voucher'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Waiting for voucher', 'wp-travel' ),
		),
		'voucher_submited' => array(
			'color' => '#FF9800',
			'text'  => __( 'Voucher submited', 'wp-travel' ),
		),
		'canceled'         => array(
			'color' => '#FE450E',
			'text'  => __( 'Canceled', 'wp-travel' ),
		),
		'N/A'              => array(
			'color' => '#892E2C',
			'text'  => __( 'N/A', 'wp-travel' ),
		),
		'refund'           => array(
			'color' => '#892E2C',
			'text'  => __( 'Refund', 'wp-travel' ),
		),
	);

	return apply_filters( 'wp_travel_payment_status_list', $status );
}

/**
 * Get Payment Mode List.
 *
 * @since 1.0.5
 */
function wptravel_get_payment_mode() {
	$mode = array(
		'partial' => array(
			'color' => '#FF9F33',
			'text'  => __( 'Partial', 'wp-travel' ),
		),
		'full'    => array(
			'color' => '#FF8A33',
			'text'  => __( 'Full', 'wp-travel' ),
		),
		'N/A'     => array(
			'color' => '#892E2C',
			'text'  => __( 'N/A', 'wp-travel' ),
		),
	);

	return apply_filters( 'wp_travel_payment_mode_list', $mode );
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @since 1.0.7
 * @return array $sizes Data for all currently-registered image sizes.
 */
function wptravel_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}

/**
 * Get permalink settings for WP Travel independent of the user locale.
 *
 * @since  1.1.0
 * @return array
 */
function wptravel_get_permalink_structure() {

	$permalinks = wp_parse_args(
		(array) get_option( 'wp_travel_permalinks', array() ),
		array(
			'wp_travel_trip_base'        => '',
			'wp_travel_trip_type_base'   => '',
			'wp_travel_destination_base' => '',
			'wp_travel_activity_base'    => '',
		)
	);

	// Ensure rewrite slugs are set.
	$permalinks['wp_travel_trip_base']        = untrailingslashit( empty( $permalinks['wp_travel_trip_base'] ) ? 'itinerary' : $permalinks['wp_travel_trip_base'] );
	$permalinks['wp_travel_trip_type_base']   = untrailingslashit( empty( $permalinks['wp_travel_trip_type_base'] ) ? 'trip-type' : $permalinks['wp_travel_trip_type_base'] );
	$permalinks['wp_travel_destination_base'] = untrailingslashit( empty( $permalinks['wp_travel_destination_base'] ) ? 'travel-locations' : $permalinks['wp_travel_destination_base'] );
	$permalinks['wp_travel_activity_base']    = untrailingslashit( empty( $permalinks['wp_travel_activity_base'] ) ? 'activity' : $permalinks['wp_travel_activity_base'] );

	return $permalinks;
}

/**
 * Return Tabs and its content for single page.
 *
 * @since 1.1.2 Modified in 2.0.7
 *
 * @return void
 */
function wptravel_get_frontend_tabs( $show_in_menu_query = false, $frontend_hide_content = false ) {

	global $post;
	$trip_id = $post->ID;

	$settings                  = wptravel_get_settings();
	$wp_travel_use_global_tabs = get_post_meta( $trip_id, 'wp_travel_use_global_tabs', true );
	if ( 'yes' === $wp_travel_use_global_tabs ) {
		$custom_tab_enabled = apply_filters( 'wp_travel_is_custom_tabs_support_enabled', false );
		$wp_travel_tabs     = wptravel_get_global_tabs( $settings, $custom_tab_enabled );
	} else {
		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );
		$wp_travel_tabs               = wptravel_get_admin_trip_tabs( $trip_id, $enable_custom_itinerary_tabs, $frontend_hide_content );
	}

	// Adding Content to the tabs.
	$return_tabs = array();

	if ( is_array( $wp_travel_tabs ) && count( $wp_travel_tabs ) > 0 ) {
		$wp_travel_itinerary_tabs = wptravel_get_default_trip_tabs( $show_in_menu_query, $frontend_hide_content ); // 2nd param is used to return only show_in_menu key.
		foreach ( $wp_travel_tabs as $key => $tab ) {
			$show_in_menu = isset( $tab['show_in_menu'] ) ? $tab['show_in_menu'] : 'yes';
			$show_in_menu = apply_filters( 'wp_travel_frontend_tab_show_in_menu', $show_in_menu, $trip_id, $key ); // @since 1.9.3.

			if ( ! $frontend_hide_content ) {  // this var is passed to check value whether current tab show in frontend or not. so content is not required
				$tab_content = isset( $wp_travel_itinerary_tabs[ $key ]['content'] ) ? $wp_travel_itinerary_tabs[ $key ]['content'] : '';

				// Adding custom tab content.
				if ( isset( $tab['custom'] ) && 'yes' === $tab['custom'] ) {
					$tab_content = isset( $tab['content'] ) ? $tab['content'] : '';
				}
				$new_tabs[ $key ]['label']       = ( $tab['label'] ) ? $tab['label'] : $wp_travel_itinerary_tabs[ $key ]['label'];
				$new_tabs[ $key ]['label_class'] = isset( $wp_travel_itinerary_tabs[ $key ]['label_class'] ) ? $wp_travel_itinerary_tabs[ $key ]['label_class'] : '';
				$new_tabs[ $key ]['content']     = $tab_content;
				$new_tabs[ $key ]['use_global']  = isset( $tab['use_global'] ) ? $tab['use_global'] : 'yes';
				$new_tabs[ $key ]['custom']      = isset( $tab['custom'] ) ? $tab['custom'] : 'no';
				$new_tabs[ $key ]['global']      = isset( $tab['global'] ) ? $tab['global'] : 'no';
			}

			$new_tabs[ $key ]['show_in_menu'] = $show_in_menu;

		}

		foreach ( $wp_travel_itinerary_tabs as $k => $val ) {
			if ( ! array_key_exists( $k, $new_tabs ) ) {
				$new_tabs[ $k ] = $val;
			}
		}
		$return_tabs = $new_tabs;
	}

	return $return_tabs = apply_filters( 'wp_travel_itinerary_tabs', $return_tabs );
}

/**
 * Default Tabs and its content.
 *
 * @var bool $is_show_in_menu_query  Set true when this function need to call from admin.
 * @since Modified in 2.0.7
 * @return array
 */
function wptravel_get_default_trip_tabs( $is_show_in_menu_query = false, $frontend_hide_content = false ) {
	$trip_content = '';
	$trip_outline = '';
	$trip_include = '';
	$trip_exclude = '';
	$gallery_ids  = '';
	$faqs         = array();

	if ( ! is_admin() && ! $is_show_in_menu_query ) { // fixes the content filter in page builder. Multiple content issue.
		if ( ! $frontend_hide_content ) {
			global $wp_travel_itinerary;
			if ( $wp_travel_itinerary ) {
				$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';

				$trip_content = $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
				$trip_outline = $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
				$trip_include = $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
				$trip_exclude = $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
				$gallery_ids  = $wp_travel_itinerary->get_gallery_ids();
				$faqs         = $wp_travel_itinerary->get_faqs() ? $wp_travel_itinerary->get_faqs() : $no_details_found_message;
			}
		}
	}
	$return_tabs = $wp_travel_itinerary_tabs = array(
		'overview'      => array(
			'label'        => __( 'Overview', 'wp-travel' ),
			'label_class'  => '',
			'content'      => $trip_content,
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'trip_outline'  => array(
			'label'        => __( 'Trip Outline', 'wp-travel' ),
			'label_class'  => '',
			'content'      => $trip_outline,
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'trip_includes' => array(
			'label'        => __( 'Trip Includes', 'wp-travel' ),
			'label_class'  => '',
			'content'      => $trip_include,
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'trip_excludes' => array(
			'label'        => __( 'Trip Excludes', 'wp-travel' ),
			'label_class'  => '',
			'content'      => $trip_exclude,
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'gallery'       => array(
			'label'        => __( 'Gallery', 'wp-travel' ),
			'label_class'  => 'wp-travel-tab-gallery-contnet',
			'content'      => wptravel_frontend_tab_gallery( $gallery_ids ),
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'reviews'       => array(
			'label'        => __( 'Reviews', 'wp-travel' ),
			'label_class'  => 'wp-travel-review',
			'content'      => '',
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'booking'       => array(
			'label'        => __( 'Booking', 'wp-travel' ),
			'label_class'  => 'wp-travel-booking-form',
			'content'      => '',
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
		'faq'           => array(
			'label'        => __( 'FAQ', 'wp-travel' ),
			'label_class'  => '',
			'content'      => $faqs,
			'use_global'   => 'yes',
			'show_in_menu' => 'yes',
		),
	);

	$return_tabs = apply_filters( 'wp_travel_default_trip_tabs', $return_tabs ); // Added in 1.9.3
	return apply_filters( 'wp_travel_default_frontend_tabs', $return_tabs );   // Need to deprecate.
}

/**
 * Return list of global tabs for settigns page.
 *
 * @param array $settings Settings data.
 * @since 1.9.3
 * @return array
 */
function wptravel_get_global_tabs( $settings, $custom_tab_enabled = false ) {
	if ( ! $settings ) {
		$settings = wptravel_get_settings();
	}

	// Default tab.
	$global_tabs = wptravel_get_default_trip_tabs();

	if ( $custom_tab_enabled ) { // Need to merge custom tabs. Note: Only enabled if WP Travel Utilities plugin is activated.
		$custom_tabs = isset( $settings['wp_travel_custom_global_tabs'] ) ? $settings['wp_travel_custom_global_tabs'] : array();
		$global_tabs = array_merge( $global_tabs, $custom_tabs );

		// Updating Utilities settings to global settings.
		if ( isset( $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'] ) && ! empty( $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'] ) ) {
			$settings['global_tab_settings'] = $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'];
			unset( $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'] );
			update_option( 'wp_travel_settings', $settings );
		}
	}

	if ( ! empty( $settings['global_tab_settings'] ) ) {
		// Add Tabs into saved tab array which newly added tabs in default tabs via hook.
		$default_tabs      = $global_tabs;
		$default_tabs_keys = array_keys( $default_tabs );

		// Saved Tabs.
		$global_tabs     = $settings['global_tab_settings'];
		$saved_tabs_keys = array_keys( $global_tabs );

		foreach ( $default_tabs_keys as $tab_key ) {
			if ( ! in_array( $tab_key, $saved_tabs_keys ) ) {
				$global_tabs[ $tab_key ] = $default_tabs[ $tab_key ];
			}
		}

		if ( $custom_tab_enabled ) {
			// Add Custom tabs content which is override by above assignment $global_tabs = $settings['global_tab_settings'];.
			// $global_tabs = array_merge( $global_tabs, $custom_tabs );
			if ( is_array( $custom_tabs ) && count( $custom_tabs ) > 0 ) {
				foreach ( $custom_tabs as $tab_key => $tab ) {
					if ( isset( $tab['content'] ) ) {
						$global_tabs[ $tab_key ]['content'] = $tab['content'];
					}
					if ( isset( $tab['custom'] ) ) {
						$global_tabs[ $tab_key ]['custom'] = $tab['custom'];
					}
				}
			}
		}

		// Remove Tabs from saved tab array which newly added tabs in default tabs via hook.
		foreach ( $saved_tabs_keys as $tab_key ) {
			if ( ! in_array( $tab_key, $default_tabs_keys ) ) {
				unset( $global_tabs[ $tab_key ] );
			}
		}
	}
	return $global_tabs;
}

/**
 * Return list of trip tabs for admin trip page.
 *
 * @param array $settings Settings data.
 * @since 1.9.3 Modified in 2.0.7
 * @return array
 */
function wptravel_get_admin_trip_tabs( $post_id, $custom_tab_enabled = false, $frontend_hide_content = false ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	// Default tab.
	// $trip_tabs = wptravel_get_default_trip_tabs();
	$trip_tabs = wptravel_get_default_trip_tabs( false, $frontend_hide_content );

	$wp_travel_tabs = get_post_meta( $post_id, 'wp_travel_tabs', true );

	if ( $custom_tab_enabled ) { // Need to merge custom tabs. Note: Only enabled if WP Travel Utilities plugin is activated.
		$custom_tabs = get_post_meta( $post_id, 'wp_travel_itinerary_custom_tab_cnt_', true );
		$custom_tabs = ( $custom_tabs ) ? $custom_tabs : array();
		$trip_tabs   = array_merge( $trip_tabs, $custom_tabs );

		// Updating Utilities tabs to global settings.
		$trip_tabs_utilities = get_post_meta( $post_id, 'wp_travel_utilities_custom_itinerary_tabs_sorting_settings', true );

		if ( $trip_tabs_utilities ) {
			$wp_travel_tabs = $trip_tabs_utilities;
			delete_post_meta( $post_id, 'wp_travel_utilities_custom_itinerary_tabs_sorting_settings' );
		}
	}

	if ( ! empty( $wp_travel_tabs ) && is_array( $wp_travel_tabs ) ) {
		// Add Tabs into saved tab array which newly added tabs in default tabs via hook.
		$default_tabs      = $trip_tabs;
		$default_tabs_keys = array_keys( $default_tabs );

		// Saved Tabs.
		$trip_tabs       = $wp_travel_tabs;
		$saved_tabs_keys = array_keys( $trip_tabs );

		foreach ( $default_tabs_keys as $tab_key ) {
			if ( ! in_array( $tab_key, $saved_tabs_keys ) ) {
				$trip_tabs[ $tab_key ] = $default_tabs[ $tab_key ];
			}
		}

		if ( $custom_tab_enabled ) {
			// Add Custom tabs content which is override by above  by above assignment $trip_tabs = $wp_travel_tabs;.
			// $trip_tabs = array_merge( $trip_tabs, $custom_tabs );
			if ( is_array( $custom_tabs ) && count( $custom_tabs ) > 0 ) {
				foreach ( $custom_tabs as $tab_key => $tab ) {
					if ( isset( $tab['content'] ) ) {
						$trip_tabs[ $tab_key ]['content'] = $tab['content'];
					}
					if ( isset( $tab['custom'] ) ) {
						$trip_tabs[ $tab_key ]['custom'] = $tab['custom'];
					}
				}
			}
		}
		// Remove Tabs from saved tab array which newly added tabs in default tabs via hook.
		foreach ( $saved_tabs_keys as $tab_key ) {
			if ( ! in_array( $tab_key, $default_tabs_keys ) ) {
				unset( $trip_tabs[ $tab_key ] );
			}
		}
	}
	return $trip_tabs;
}

/**
 * Return FAQs
 *
 * @param Int $post_id Post ID.
 *
 * @since 1.1.2
 * @return array.
 */
function wptravel_get_faqs( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$faq = array();

	$questions = get_post_meta( $post_id, 'wp_travel_faq_question', true );
	$questions = ( $questions ) ? $questions : array();
	$questions = apply_filters( 'wp_travel_itinerary_faq_questions', $questions, $post_id ); // Modified in 2.0.7

	$settings         = wptravel_get_settings();
	$is_global_faq    = get_post_meta( $post_id, 'wp_travel_is_global_faq', true );
	$global_questions = isset( $settings['wp_travel_utils_global_faq_question'] ) ? $settings['wp_travel_utils_global_faq_question'] : array(); // value to check whether trip faq exists in gloabl on not.

	$use_global_faq = get_post_meta( $post_id, 'wp_travel_utils_use_global_faq_for_trip', true );
	$use_global_faq = ( $use_global_faq ) ? $use_global_faq : 'no';

	$use_trip_faq = get_post_meta( $post_id, 'wp_travel_utils_use_trip_faq_for_trip', true );
	$use_trip_faq = ( $use_trip_faq ) ? $use_trip_faq : 'no';

	if ( is_array( $questions ) && count( $questions ) > 0 ) :
		$answers = get_post_meta( $post_id, 'wp_travel_faq_answer', true );
		$answers = apply_filters( 'wp_travel_itinerary_faq_answers', $answers, $post_id ); // Modified in 2.0.7
		foreach ( $questions as $key => $question ) :

			// This will check the current question exists in global question or not. if yes then set this as global. This will work for initial check.
			$global_faq = 'no';
			if ( ! empty( $global_questions ) && in_array( $question, $global_questions ) ) {
				$global_faq = 'yes';
			}

			// with only above condition if faq is saved and after that delete global settings, it will treat as trip faq because global faq are saved along with trip faq. so we need to seperate faq type (global or individual).
			if ( isset( $is_global_faq[ $key ] ) && ! empty( $is_global_faq[ $key ] ) ) {
				$global_faq = $is_global_faq[ $key ];
			}

			$answer = isset( $answers[ $key ] ) ? $answers[ $key ] : '';

			// remove global if utilities is not exists.
			if ( ! class_exists( 'WP_Travel_Utilities_Core' ) && 'yes' == $global_faq ) {
				continue;
			}

			if ( ! is_admin() ) { // filter for frontend.
				if ( 'no' === $use_global_faq && 'yes' == $global_faq ) { // Trip faq.
					continue;
				} elseif ( 'yes' == $use_global_faq && 'no' == $use_trip_faq && 'no' == $global_faq ) { // only global
					continue;
				}
			}

			if ( 'yes' === $global_faq && is_array( $global_questions ) && ! in_array( $question, $global_questions ) ) { // If this is global faq and deleted this faq from global then need to remove this faq.
				continue;
			}
			$faq[] = array(
				'question' => $question,
				'answer'   => $answer,
				'global'   => $global_faq,
			);
		endforeach;
	endif;
	return $faq;
}


/**
 * Retrieve page ids - cart, checkout. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function wptravel_get_page_id( $page ) {

	$settings = get_option( 'wp_travel_settings' ); // Not used wptravel_get_settings due to infinite loop.
	$page     = str_replace( 'wp-travel-', '', $page );
	$page_id  = ( isset( $settings[ $page . '_page_id' ] ) ) ? $settings[ $page . '_page_id' ] : '';

	if ( ! $page_id ) {
		$page_id = get_option( 'wp_travel_wp-travel-' . $page . '_page_id' );
	}

	$page_id = apply_filters( 'wp_travel_get_' . $page . '_page_id', $page_id );

	return $page_id ? absint( $page_id ) : -1;
}

/**
 * Retrieve page permalink.
 *
 * @param string $page page slug.
 * @return string
 */
function wptravel_get_page_permalink( $page ) {
	$page_id   = wptravel_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'wp_travel_get_' . $page . '_page_permalink', $permalink );
}

/**
 * Gets the url to the Cart page.
 *
 * @since  1.5.7
 *
 * @return string Url to cart page
 */
function wptravel_get_cart_url() {
	return apply_filters( 'wptravel_get_cart_url', wptravel_get_page_permalink( 'cart' ) );
}

/**
 * Gets the URL of checkout page.
 *
 * @since 1.5.7
 *
 * @return string Url to checkout page
 */
function wptravel_get_checkout_url() {
	return apply_filters( 'wptravel_get_checkout_url', wptravel_get_page_permalink( 'wp-travel-checkout' ) );
}

function wptravel_is_itinerary( $post_id = null ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	if ( ! $post_id ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	// If this isn't a 'itineraries' post, don't update it.
	if ( WP_TRAVEL_POST_TYPE === $post_type ) {
		return true;
	}
	return false;
}

/**
 * Check whether payment script is loadable or not.
 */
function wptravel_can_load_payment_scripts() {
	return ( WP_Travel::is_page( 'dashboard' ) || WP_Travel::is_page( 'checkout' ) ) && wptravel_is_payment_enabled();
}

// WP Travel Pricing Varition options.

/**
 * Get default pricing variation options.
 *
 * @return array $variation_options Variation Options.
 */
function wptravel_get_pricing_variation_options() {

	$variation_options = array(
		'adult'    => __( 'Adult', 'wp-travel' ),
		'children' => __( 'Child', 'wp-travel' ),
		'infant'   => __( 'Infant', 'wp-travel' ),
		'couple'   => __( 'Couple', 'wp-travel' ),
		'group'    => __( 'Group', 'wp-travel' ),
		'custom'   => __( 'Custom', 'wp-travel' ),
	);

	return apply_filters( 'wp_travel_variation_pricing_options', $variation_options );
}

/**
 * @since 3.0.7
 */
function wptravel_get_pricing_category_by_key( $key = null ) {
	if ( ! $key ) {
		return;
	}

	$variation = wptravel_get_pricing_variation_options();

	if ( array_key_exists( $key, $variation ) ) {
		if ( 'custom' === $key ) { // Fix for translated string compare.
			return $key;
		}
		return $variation[ $key ];
	}
	return;
}

/**
 * Get single pricing variation by key.
 *
 * @return array $pricing Pricing variations data.
 */
function wptravel_get_pricing_variation( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {
		return false;
	}

	// Get Pricing variations.
	$pricing_variations = get_post_meta( $post_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) ) {

		$result = array_filter(
			$pricing_variations,
			function( $single ) use ( $pricing_key ) {
				if ( isset( $single['price_key'] ) ) {
					return $single['price_key'] === $pricing_key;
				}
			}
		);
		return $result;
	}
	return false;

}


/**
 * Retrieves unvalidated referer from '_wp_http_referer' or HTTP referer.
 *
 * Do not use for redirects, use {@see wp_get_referer()} instead.
 *
 * @since 1.3.3
 * @return string|false Referer URL on success, false on failure.
 */
function wptravel_get_raw_referer() {
	if ( function_exists( 'wp_get_raw_referer' ) ) {
		return wp_get_raw_referer();
	}

	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) { // @phpcs:ignore
		return wptravel_sanitize_array( wp_unslash( $_REQUEST['_wp_http_referer'] ) ); // @phpcs:ignore
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wptravel_sanitize_array( wp_unslash( $_SERVER['HTTP_REFERER'] ) ); // @phpcs:ignore
	}

	return false;
}

/**
 * Get pricing variation start dates.
 *
 * @return array $available_dates Variation Options.
 */
function wptravel_get_pricing_variation_start_dates( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {

		return false;

	}

	// Get Dates.
	$trip_dates = wptravel_get_pricing_variation_dates( $post_id, $pricing_key );

	$result = array();

	if ( is_array( $trip_dates ) && '' !== $trip_dates ) {

		foreach ( $trip_dates as $d_k => $d_v ) {

			$result[] = $d_v['start_date'];

		}

		return $result;

	}

	return false;

}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function wptravel_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function wptravel_clean_vars( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wptravel_clean_vars', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function wptravel_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			WPTravel()->notices->add( $error, 'error' );
		}
	}
}
/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @param  string $notice_type Optional. The name of the notice type - either error, success or notice.
 * @return int
 */
function wptravel_get_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = WPTravel()->notices->get( $notice_type, false );

	if ( ! empty( $all_notices ) && is_array( $all_notices ) ) {

		foreach ( $all_notices as $key => $notices ) {
			$notice_count++;
		}
	}

	return $notice_count;
}

/**
 * Send new account notification to users.
 */
function wptravel_user_new_account_created( $customer_id, $new_customer_data, $password_generated ) {

	// Send email notification.
	$email_content = wptravel_get_template_html(
		'emails/customer-new-account.php',
		array(
			'user_login'         => $new_customer_data['user_login'],
			'user_pass'          => $new_customer_data['user_pass'],
			'blogname'           => get_bloginfo( 'name' ),
			'password_generated' => $password_generated,
		)
	);

	// To send HTML mail, the Content-type header must be set.
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$from = get_option( 'admin_email' );
	// Create email headers.
	$headers .= 'From: ' . $from . "\r\n";
	$headers .= 'Reply-To: ' . $from . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

	if ( $new_customer_data['user_login'] ) {

		$user_object     = get_user_by( 'login', $new_customer_data['user_login'] );
		$user_user_login = $new_customer_data['user_login'];
		$user_user_email = stripslashes( $user_object->user_email );
		$user_subject    = __( 'New Account Created', 'wp-travel' );
		$args            = array(
			'customer_id'   => $customer_id,
			'user_email'    => $user_user_email,
			'user_login'    => $user_user_login,
			'user_subject'  => $user_subject,
			'email_content' => $email_content,
			'headers'       => $headers,
		);
		$user_email_data = apply_filters( 'wp_travel_before_user_registration_email', $args );
		$user_user_email = ! empty( $user_email_data['user_email'] ) ? $user_email_data['user_email'] : $user_user_email;
		$user_subject    = ! empty( $user_email_data['user_subject'] ) ? $user_email_data['user_subject'] : $user_subject;
		$email_content   = ! empty( $user_email_data['email_content'] ) ? $user_email_data['email_content'] : $email_content;
		$headers         = ! empty( $user_email_data['headers'] ) ? $user_email_data['headers'] : $headers;
		if ( ! wp_mail( $user_user_email, $user_subject, $email_content, $headers ) ) {

			return false;

		}
	}
}

add_action( 'wp_travel_created_customer', 'wptravel_user_new_account_created', 20, 3 );

/**
 * Filters the from name in e-mails
 */
function wptravel_emails_from_name_filter( $from_name ) {

	return $from_name = apply_filters( 'wp_travel_email_from_name', get_bloginfo( 'name' ) );

}

add_filter( 'wp_mail_from_name', 'wptravel_emails_from_name_filter', 30 );


if ( ! function_exists( 'wptravel_format_date' ) ) :

	/**
	 * Format Date.
	 */
	function wptravel_format_date( $date, $localize = true, $base_date_format = '' ) {
		if ( ! $date ) {
			return;
		}
		$date_format = get_option( 'date_format' );
		if ( ! $date_format ) :
			$date_format = 'jS M, Y';
		endif;

		$strtotime = $date;

		if ( '' !== $base_date_format ) { // Fixes.
			if ( 'Y-m-d' !== $base_date_format ) {
				$date      = DateTime::createFromFormat( $base_date_format, $date );
				$strtotime = date_format( $date, 'Y-m-d' );
			}
		} else {

			if ( 'Y-m-d' !== $date_format ) {
				$date = str_replace( '/', '-', $date );
				$date = str_replace( '.', '-', $date );

				$dashed_format = str_replace( '/', '-', $date_format );
				$dashed_format = str_replace( '.', '-', $dashed_format );
				$date          = DateTime::createFromFormat( $dashed_format, $date );
				if ( $date && is_object( $date ) ) {
					$strtotime = date_format( $date, 'Y-m-d' );
				}
			}
		}
		$strtotime = strtotime( stripslashes( $strtotime ) );

		if ( $localize ) {
			$formated_date = esc_html( date_i18n( $date_format, $strtotime ) );
		} else {
			$formated_date = esc_html( date( $date_format, $strtotime ) );
		}

		return $formated_date;

	}

	/**
	 * Format Date to YMD.
	 *
	 * @param String $date        Date.
	 * @param String $date_format Date.
	 * @since 1.8.3
	 */
	function wptravel_format_ymd_date( $date, $date_format = '' ) {
		if ( ! $date ) {
			return;
		}

		if ( ! $date_format ) :
			$date_format = get_option( 'date_format' );
		endif;

		$strtotime = $date;

		if ( 'Y-m-d' !== $date_format ) {

			$date = str_replace( '/', '-', $date );
			$date = str_replace( '.', '-', $date );

			$dashed_format = str_replace( '/', '-', $date_format );
			$dashed_format = str_replace( '.', '-', $dashed_format );
			$date          = DateTime::createFromFormat( $dashed_format, $date );
			if ( $date && is_object( $date ) ) {
				$strtotime = date_format( $date, 'Y-m-d' );
			} else {
				// Fallback date [today]
				$strtotime = (string) date( 'Y-m-d' );
			}
		}
		return $strtotime;

		$strtotime = strtotime( stripslashes( $strtotime ) );

		if ( $localize ) {
			$formated_date = esc_html( date_i18n( $date_format, $strtotime ) );
		} else {
			$formated_date = esc_html( date( $date_format, $strtotime ) );
		}

		return $formated_date;

	}

endif;

if ( ! function_exists( 'wptravel_get_trip_available_dates' ) ) {

	/**
	 * Get Available Dates for specific trip.
	 *
	 * @param Number $trip_id Current trip id.
	 * @since 1.8.3
	 */
	function wptravel_get_trip_available_dates( $trip_id, $price_key = '' ) {

		if ( ! $trip_id ) {
			return;
		}

		$multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = array();

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

		if ( wptravel_is_react_version_enabled() && 'yes' === $fixed_departure ) {
			$data = WP_Travel_Helpers_Trip_Dates::get_dates( $trip_id );
			if ( is_array( $data ) && 'WP_TRAVEL_TRIP_DATES' === $data['code'] ) {
				$dates = $data['dates'];
				foreach ( $dates as $date ) {
					$available_dates[] = $date['start_date'];
				}
			}
			return $available_dates;
		}

		if ( 'yes' === $fixed_departure ) {
			if ( 'yes' === $multiple_fixed_departue ) {
				$available_dates = wptravel_get_pricing_variation_start_dates( $trip_id, $price_key );
			} else {
				$date            = get_post_meta( $trip_id, 'wp_travel_start_date', true );
				$available_dates = array( $date );
			}
		}

		return $available_dates;
	}
}

/**
 * Helper function that checks if react version of WP Travel.
 *
 * @since 4.0.3
 */

function wptravel_is_react_version_enabled() {
	$settings = get_option( 'wp_travel_settings', array() );
	return isset( $settings['wp_travel_switch_to_react'] ) && 'yes' === $settings['wp_travel_switch_to_react'];
}

if ( ! function_exists( 'wptravel_get_multiple_pricing_available_dates' ) ) {

	/**
	 * Get Available Arrival and departure dates of multiple pricing.
	 *
	 * @since 2.0.8
	 */
	function wptravel_get_multiple_pricing_available_dates( $trip_id, $price_key = '' ) {

		if ( ! $trip_id ) {
			return;
		}

		$multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = array();

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

		if ( 'yes' === $fixed_departure ) {
			if ( 'yes' === $multiple_fixed_departue ) {
				if ( ! empty( $price_key ) ) {
					// Get Dates.
					$trip_dates = wptravel_get_pricing_variation_dates( $trip_id, $price_key );
					if ( is_array( $trip_dates ) && '' !== $trip_dates ) {
						foreach ( $trip_dates as $d_k => $d_v ) {
							$available_dates['arrival_dates'][]   = $d_v['start_date'];
							$available_dates['departure_dates'][] = $d_v['end_date'];
						}
					}
				}
			} else {
				$start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
				$end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );
				// $available_dates[] = array( 'arrival_date' => $start_date, 'departure_date' => $end_date );
				$available_dates['arrival_dates'][]   = $start_date;
				$available_dates['departure_dates'][] = $end_date;
			}
		}
		return $available_dates;
	}
}

if ( ! function_exists( 'wptravel_get_total_booked_pax' ) ) :
	/**
	 * Get Total booked Count.
	 */
	function wptravel_get_total_booked_pax( $trip_id, $including_cart = true ) {

		if ( ! $trip_id ) {
			return;
		}
		$trip_pricing_options_data = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( empty( $trip_pricing_options_data ) || ! is_array( $trip_pricing_options_data ) ) {
			return;
		}

		$total_booked_pax = 0;
		if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {
			$inventory = new WP_Travel_Util_Inventory();
			foreach ( $trip_pricing_options_data as $pricing ) :
				$price_key = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';

				$booked_pax        = $inventory->get_booking_pax_count( $trip_id, $price_key );
				$booked_pax        = ( $booked_pax ) ? $booked_pax : 0;
				$total_booked_pax += $booked_pax;
			endforeach;
		}
		if ( $including_cart ) {
			global $wt_cart;
			$total_pax_on_cart = 0;
			$items             = $wt_cart->getItems();
			if ( is_array( $items ) && count( $items ) > 0 ) {
				foreach ( $items as $item ) {
					$cart_trip_id = $item['trip_id'];
					if ( $trip_id != $cart_trip_id ) {
						continue;
					}
					$pax_on_cart        = $item['pax'];
					$total_pax_on_cart += $pax_on_cart;
				}
			}
			$total_booked_pax += $total_pax_on_cart;
		}
		return $total_booked_pax;
	}
endif;
/**
 * Get no. of days.
 */
function wptravel_get_date_diff( $start_date, $end_date ) {

	$date11       = strtotime( $start_date );
	$date22       = strtotime( $end_date );
	$diff         = $date22 - $date11;
	$diff_in_days = floor( $diff / ( 60 * 60 * 24 ) ) + 1;

	return sprintf( __( '%s days', 'wp-travel' ), $diff_in_days );

}

/**
 * Print success and error notices set by WP Travel Plugin.
 */
function wptravel_print_notices() {
	// Print Errors / Notices.
	WPTravel()->notices->print_notices( 'error', true );
	WPTravel()->notices->print_notices( 'success', true );
}

/**
 * Convert Date Format String form PHP to JS.
 *
 * @param string $date_format Date Fromat.
 *
 * @since   1.6.7
 * @return  array
 */
function wptravel_date_format_php_to_js( $date_format = null ) {
	$js_date_format = 'yyyy-mm-dd';
	return apply_filters( 'wp_travel_js_date_format', $js_date_format );
}

/**
 * Convert Date Format String form PHP to JS for moment.
 *
 * @param string $date_format Date Fromat.
 *
 * @since   1.7.6
 * @return  array
 */
function wptravel_moment_date_format( $date_format = null ) {
	$js_date_format = 'YYYY-MM-DD';
	return apply_filters( 'wptravel_moment_date_format', $js_date_format );
}

/**
 * Check current date formant is Y-m-d or not.
 *
 * @param string $date Date.
 *
 * @since   1.8.3
 * @return  array
 */
function wptravel_is_ymd_date( $date ) {
	if ( ! $date ) {
		return;
	}

	if ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date ) ) {
		return true;
	} else {
		return false;
	}

}

/**
 * Return All Payment Details.
 *
 * @since 1.8.0
 * @return array
 */
function wptravel_payment_data( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	$payment_ids = array();
	// get previous payment ids.
	$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

	if ( is_string( $payment_id ) && '' !== $payment_id ) {
		$payment_ids[] = $payment_id;
	} else {
		$payment_ids = $payment_id;
	}
	$payment_data = array();
	if ( is_array( $payment_ids ) && count( $payment_ids ) > 0 ) {
		$i = 0;
		foreach ( $payment_ids as $payment_id ) :
			$payment_method = get_post_meta( $payment_id, 'wp_travel_payment_gateway', true );
			$meta_name      = sprintf( '_%s_args', $payment_method );
			if ( $meta_name ) :
				$payment_args = get_post_meta( $payment_id, $meta_name, true );
				if ( $payment_args && ( is_object( $payment_args ) || is_array( $payment_args ) ) ) :
					$payment_data[ $i ]['data']           = $payment_args;
					$payment_data[ $i ]['payment_id']     = $payment_id;
					$payment_data[ $i ]['payment_method'] = $payment_method;
					$payment_data[ $i ]['payment_date']   = get_the_date( '', $payment_id );
					$i++;
				endif;
			endif;
		endforeach;
	}
	return $payment_data;
}

/**
 * Filter to show hide end date in booking.
 *
 * @since 1.8.0
 * @return  boolean
 */
function wptravel_booking_show_end_date() {
	return apply_filters( 'wptravel_booking_show_end_date', true );
}

/**
 * Gets Pricing Name/Label.
 *
 * @param $trip_id Trip Id.
 * @param $pricing_id Pricing ID.
 *
 * @since 4.0.3
 */
function wptravel_get_trip_pricing_name_by_pricing_id( $trip_id, $pricing_id ) {
	$pricing_name       = get_the_title( $trip_id );
	$pricing            = wptravel_get_pricing_by_pricing_id( $trip_id, $pricing_id );
	$show_pricing_label = apply_filters( 'wp_travel_show_pricing_label_on_name', true ); // filter @since WP Travel 4.3.1
	if ( ! is_null( $pricing ) && $show_pricing_label ) {
		$pricing_name = sprintf( '%s (%s)', $pricing_name, $pricing['title'] );
	}
	return $pricing_name;
}

/**
 * Return Pricing name as per trip id and pricing key.
 *
 * @param Number $trip_id   Trip ID.
 * @param String $price_key Name of pricing.
 *
 * @since 1.8.2
 *
 * @deprecated 4.0.3
 *
 * @return String
 */
function wptravel_get_trip_pricing_name( $trip_id, $price_key = '' ) {
	if ( wptravel_is_react_version_enabled() ) {
		$pricing_id = $price_key;
		return wptravel_get_trip_pricing_name_by_pricing_id( $trip_id, $pricing_id );
	}

	if ( ! $trip_id ) {
		return;
	}

	$pricing_name = get_the_title( $trip_id );

	if ( ! empty( $price_key ) ) :
		$pricing_options = wptravel_get_pricing_variation( $trip_id, $price_key );
		$pricing_option  = ( is_array( $pricing_options ) && ! empty( $pricing_options ) ) ? reset( $pricing_options ) : false;

		if ( $pricing_option ) {
			$pricing_label      = isset( $pricing_option['pricing_name'] ) ? $pricing_option['pricing_name'] : false;
			$show_pricing_label = apply_filters( 'wp_travel_show_pricing_lable_on_name', true ); // filter @since WP Travel 4.3.1
			if ( $pricing_label && $show_pricing_label ) {
				$pricing_name = sprintf( '%s (%s)', $pricing_name, $pricing_label );
			}
		}
	endif;
	return $pricing_name;
}

/**
 * Sort array by priority.
 *
 * @return array $array
 */
function wptravel_sort_array_by_priority( $array, $priority_key = 'priority' ) {
	$priority = array();
	if ( is_array( $array ) && count( $array ) > 0 ) {
		foreach ( $array as $key => $row ) {
			$priority[ $key ] = isset( $row[ $priority_key ] ) ? $row[ $priority_key ] : 1;
		}
		array_multisort( $priority, SORT_ASC, $array );
	}
	return $array;
}

/**
 * Sort Checkout form fields.
 *
 * @return array $fields
 */
function wptravel_sort_form_fields( $fields ) {
	return wptravel_sort_array_by_priority( $fields );
}

/**
 * Get Inquiry Link.
 */
function wptravel_get_inquiry_link() {

	ob_start();

	$enquiry_text = apply_filters( 'wp_travel_trip_enquiry_label', __( 'Trip Enquiry', 'wp-travel' ) );

	?>

		<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
			<span class="wp-travel-booking-enquiry">
				<span class="dashicons dashicons-editor-help"></span>
				<span>
					<?php echo esc_html( $enquiry_text ); ?>
				</span>
			</span>
		</a>

	<?php

	$data = ob_get_clean();

	return $data;

}


function wptravel_get_search_filter_form( $args ) {

	if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	}
	$form_field    = new WP_Travel_FW_Field();
	$search_fields = wptravel_search_filter_widget_form_fields();
	$index         = uniqid();
	$instance      = array();
	if ( isset( $args['widget'] ) ) {
		$instance = $args['widget'];
	} elseif ( isset( $args['shortcode'] ) ) {
		$instance = $args['shortcode'];
	} else {
		return;
	}
	?>

		<div class="wp-travel-itinerary-items">
			<div>
				<?php
				foreach ( $search_fields as $key => $search_field ) {

					$show_fields = isset( $instance[ $key ] ) ? $instance[ $key ] : '';
					if ( $show_fields ) {
						$search_field['class'] = isset( $search_field['class'] ) && '' !== $search_field['class'] ? $search_field['class'] . $index : '';
						$form_field->init( $search_field, array( 'single' => true ) )->render();
					}
				}
				$view_mode = wptravel_get_archive_view_mode();
				?>

				<div class="wp-travel-search">

					<input class="filter-data-index" type="hidden" data-index="<?php echo esc_attr( $index ); ?>">

					<input class="wp-travel-widget-filter-view-mode" type="hidden" name="view_mode" data-mode="<?php echo esc_attr( $view_mode ); ?>" value="<?php echo esc_attr( $view_mode ); ?>" >

					<input type="hidden" class="wp-travel-widget-filter-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>" />
					<input type="submit" id="wp-travel-filter-search-submit" class="button button-primary wp-travel-filter-search-submit" value="<?php esc_html_e( 'Search', 'wp-travel' ); ?>">
				</div>
			</div>
		</div>
	<?php
}

function wptravel_get_pricing_option_listing_type( $settings = null ) {
	if ( ! $settings ) {
		$settings = wptravel_get_settings();
	}
	$list_type = isset( $settings['trip_pricing_options_layout'] ) ? $settings['trip_pricing_options_layout'] : 'by-pricing-option';
	// $list_type = 'by-date';
	return apply_filters( 'wp_travel_pricing_option_listing_type', $list_type );
}
/**
 * @since 3.1.5
 */
function wptravel_get_trip_archive_filter_by( $settings = null ) {
	if ( ! $settings ) {
		$settings = wptravel_get_settings();
	}
	$list_type = isset( $settings['trip_archive_filter_by'] ) ? $settings['trip_archive_filter_by'] : 'default';
	return apply_filters( 'wp_travel_trip_archive_filter_by', $list_type );
}

function wptravel_view_booking_details_table( $booking_id, $hide_payment_column = false ) {
	if ( ! $booking_id ) {
		return;
	}

	$details = wptravel_booking_data( $booking_id );

	$order_details = get_post_meta( $booking_id, 'order_items_data', true ); // Multiple Trips.

	$customer_note = get_post_meta( $booking_id, 'wp_travel_note', true );
	$travel_date   = get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$trip_id       = get_post_meta( $booking_id, 'wp_travel_post_id', true );

	$title = get_the_title( $trip_id );
	$pax   = get_post_meta( $booking_id, 'wp_travel_pax', true );

	// Billing fields.
	$billing_address = get_post_meta( $booking_id, 'wp_travel_address', true );
	$billing_city    = get_post_meta( $booking_id, 'billing_city', true );
	$billing_country = get_post_meta( $booking_id, 'wp_travel_country', true );
	$billing_postal  = get_post_meta( $booking_id, 'billing_postal', true );

	$status_list  = wptravel_get_payment_status();
	$status_color = isset( $details['payment_status'] ) && isset( $status_list[ $details['payment_status'] ]['color'] ) ? $status_list[ $details['payment_status'] ]['color'] : '';

	if ( is_array( $details ) && count( $details ) > 0 ) {
		?>
		<div class="table-wrp">
			<!-- Started Here -->
			<div class="my-order-single-content-wrap">
				<?php if ( wptravel_is_payment_enabled() && ! $hide_payment_column ) : ?>
					<div class="my-order-single-sidebar">
						<h3 class="my-order-single-title"><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></h3>
						<div class="my-order-status my-order-status-<?php echo esc_html( $details['payment_status'] ); ?>" style="background:<?php echo esc_attr( $status_color ); ?>" >
							<?php
							$status_lists   = wptravel_get_payment_status();
							$payment_status = $status_lists[ $details['payment_status'] ];
							echo esc_html( $payment_status['text'] );
							?>
						</div>

						<?php do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id, $details ); ?>
					</div>
				<?php endif; ?>
				<div class="my-order-single-content">
					<div class="row">
						<div class="col-md-6">
							<h3 class="my-order-single-title"><?php esc_html_e( 'Order Status', 'wp-travel' ); ?></h3>
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Order Number :', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo sprintf( '#%s', $booking_id ); ?></span>
							</div>
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Booking Date :', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo get_the_date( '', $booking_id ); ?></span>
							</div>
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Tour :', 'wp-travel' ); ?></span>
								<span class="my-order-tail">
									<?php
									if ( $order_details && is_array( $order_details ) && count( $order_details ) > 0 ) : // Multiple.
										$travel_date = '';
										foreach ( $order_details as $order_detail ) :
											$trip_id      = $order_detail['trip_id'];
											$price_key    = isset( $order_detail['price_key'] ) ? $order_detail['price_key'] : '';
											$pricing_name = wptravel_get_trip_pricing_name( $trip_id, $price_key );

											if ( '' !== $order_detail['arrival_date'] ) {
												$travel_date .= wptravel_format_date( $order_detail['arrival_date'] );
											} else {
												$travel_date .= __( 'N/A', 'wp-travel' );
											}

											$travel_date  = apply_filters( 'wp_travel_booking_travel_date', $travel_date, $order_detail );  // @since 3.1.3
											$travel_date .= ' | ';
											?>
											<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>,
											<?php
										endforeach;
									else :
										$pricing_name = wptravel_get_trip_pricing_name( $trip_id );
										?>
										<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>
									<?php endif; ?>
								</span>
							</div>
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Travel Date :', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo $travel_date; ?></span>
							</div>

							<?php
							/**
							 * Hook to add booking time details at booking.
							 * Will be deprecated in future, use wp_travel_after_bookings_travel_date instead.
							 *
							 * @todo Must deprecate this hook letter.
							 */
							// do_action( 'wp_travel_booked_times_details', $order_details );
							wptravel_do_deprecated_action( 'wp_travel_booked_times_details', array( $order_details ), '4.4.0', 'wp_travel_after_bookings_travel_date' );

							/**
							 * @since 3.0.4
							 */
							do_action( 'wp_travel_after_bookings_travel_date', $booking_id );

							?>
						</div>
						<div class="col-md-6">
							<?php
							$checkout_fields = wptravel_get_checkout_form_fields();
							$billing_fields  = isset( $checkout_fields['billing_fields'] ) ? $checkout_fields['billing_fields'] : array();
							$billing_fields  = wptravel_sort_form_fields( $billing_fields );
							if ( ! empty( $billing_fields ) ) {
								foreach ( $billing_fields as $field ) {
									$billing_data = get_post_meta( $booking_id, $field['name'], true );
									if ( is_array( $billing_data ) && count( $billing_data ) > 0 ) {
										/**
										 * Fix for field editor billing checkbox issue.
										 *
										 * @since v2.1.0
										 */
										$billing_data = implode( ', ', $billing_data );
									}

									if ( 'heading' === $field['type'] ) {
										printf( '<h3 class="my-order-single-title">%s</h3> ', $field['label'] );
									} elseif ( in_array( $field['type'], array( 'hidden' ) ) ) {
										// Do nothing
									} else {
										echo '<div class="my-order-single-field clearfix">';
										printf( '<span class="my-order-head">%s:</span>', $field['label'] );
										printf( '<span class="my-order-tail">%s</span>', $billing_data );
										echo '</div>';
									}
								}
							}
							?>
						</div>
					</div>
					<?php

					// Travelers info.
					$fname            = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
					$lname            = get_post_meta( $booking_id, 'wp_travel_lname_traveller', true );
					$country          = get_post_meta( $booking_id, 'wp_travel_country_traveller', true );
					$phone            = get_post_meta( $booking_id, 'wp_travel_phone_traveller', true );
					$email            = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );
					$dob              = get_post_meta( $booking_id, 'wp_travel_date_of_birth_traveller', true );
					$gender           = get_post_meta( $booking_id, 'wp_travel_gender_traveller', true );
					$traveller_infos  = get_post_meta( $booking_id );
					$order_items_data = get_post_meta( $booking_id, 'order_items_data', true );
					if ( is_array( $fname ) && count( $fname ) > 0 ) :
						foreach ( $fname as $cart_id => $first_names ) :
							if ( is_array( $first_names ) && count( $first_names ) > 0 ) :
								$trip_id = $order_items_data[ $cart_id ]['trip_id'];
								?>
								<div class="my-order-single-traveller-info">
									<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Travelers info [ %s ]', get_the_title( $trip_id ) ), 'wp-travel' ); ?></h3>
									<div class="row">
										<?php foreach ( $first_names as $key => $first_name ) : ?>
											<div class="col-md-6">
												<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Traveler %s :', $key + 1 ), 'wp-travel' ); ?></h3>
												<?php
												$traveller_fields = isset( $checkout_fields['traveller_fields'] ) ? $checkout_fields['traveller_fields'] : array();
												$traveller_fields = wptravel_sort_form_fields( $traveller_fields );
												if ( ! empty( $traveller_fields ) ) {
													foreach ( $traveller_fields as $field ) {
														if ( 'heading' === $field['type'] ) {
															// Do nothing.
														} elseif ( in_array( $field['type'], array( 'hidden' ) ) ) {
															// Do nothing.
														} else {
															$value = isset( $traveller_infos[ $field['name'] ] ) && isset( $traveller_infos[ $field['name'] ][0] ) ? maybe_unserialize( $traveller_infos[ $field['name'] ][0] ) : '';
															$value = is_array( $value ) ? array_values( $value ) : $value;
															$value = is_array( $value ) ? array_shift( $value ) : $value;
															$value = is_array( $value ) ? $value[ $key ] : $value;
															echo '<div class="my-order-single-field clearfix">';
															printf( '<span class="my-order-head">%s:</span>', $field['label'] );
															printf( '<span class="my-order-tail">%s</span>', $value );
															echo '</div>';
														}
													}
												}
												?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
								<?php
							endif;
						endforeach;
						else :
							?>
						<div class="my-order-single-traveller-info">
							<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Travelers info [ %s ]', get_the_title( $trip_id ) ), 'wp-travel' ); ?></h3>
							<div class="row">
								<div class="col-md-6">
									<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Lead Traveler :' ), 'wp-travel' ); ?></h3>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Name :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $fname . ' ' . $lname ); ?></span>
									</div>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Country :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $country ); ?></span>
									</div>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Phone No. :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $phone ); ?></span>
									</div>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Email :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $email ); ?></span>
									</div>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Date of Birth :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $dob ); ?></span>
									</div>
									<div class="my-order-single-field clearfix">
										<span class="my-order-head"><?php esc_html_e( 'Gender :', 'wp-travel' ); ?></span>
										<span class="my-order-tail"><?php echo esc_html( $gender ); ?></span>
									</div>
								</div>
							</div>
						</div>
							<?php
					endif;
						?>

					<?php
					if ( isset( $details['total'] ) && $details['total'] > 0 ) :
						?>
					<div class="my-order-single-price-breakdown">
						<h3 class="my-order-single-title"><?php echo esc_html_e( 'Price Breakdown', 'wp-travel' ); ?></h3>
						<div class="my-order-price-breakdown">
							<?php

							if ( $order_details ) { // Multiple Trips. Now from 1.8.3 it also included in single trip.
								$order_prices = get_post_meta( $booking_id, 'order_totals', true );
								foreach ( $order_details as $order_detail ) {
									$order_detail['trip_extras'] = (array) $order_detail['trip_extras'];

									if ( isset( $order_detail['trip'] ) ) { // @since 3.0.0.
										$total = $order_detail['trip_price'];
										?>
										<div class="my-order-price-breakdown-base-price-wrap">
											<div class="my-order-price-breakdown-base-price">
												<span class="my-order-head"><?php echo esc_html( get_the_title( $order_detail['trip_id'] ) ); ?></span>
												<span class="my-order-tail">
													<?php if ( ! empty( $order_detail['trip'] ) ) : ?>
														<?php foreach ( $order_detail['trip'] as $category_id => $trip ) : ?>
															<span class="my-order-price-detail">(<?php echo esc_html( $trip['pax'] ) . ' x ' . wptravel_get_formated_price_currency( $trip['price'], false, '', $booking_id ); ?>) </span>
														<?php endforeach; ?>
													<?php endif; ?>
													<span class="my-order-price"><?php echo wptravel_get_formated_price_currency( $total, false, '', $booking_id ); ?></span>
												</span>
											</div>
										</div>
										<?php
									} else { // Legacy Version.

										$pax        = $order_detail['pax'];
										$trip_price = $order_detail['trip_price'];
										$total      = wptravel_get_formated_price( $trip_price * $pax );

										?>
										<div class="my-order-price-breakdown-base-price-wrap">
											<div class="my-order-price-breakdown-base-price">
												<span class="my-order-head"><?php echo esc_html( get_the_title( $order_detail['trip_id'] ) ); ?></span>
												<span class="my-order-tail">
													<span class="my-order-price-detail">(<?php echo esc_html( $pax ) . ' x ' . wptravel_get_formated_price_currency( $trip_price, false, '', $booking_id ); ?>) </span>
													<span class="my-order-price"><?php echo wptravel_get_formated_price_currency( $total, false, '', $booking_id ); ?></span>
												</span>
											</div>
										</div>
										<?php
									}
									if ( isset( $order_detail['trip_extras'] ) && isset( $order_detail['trip_extras']['id'] ) && count( $order_detail['trip_extras']['id'] ) > 0 ) :
										$extras = $order_detail['trip_extras'];
										?>
										<div class="my-order-price-breakdown-additional-service">
											<h3 class="my-order-price-breakdown-additional-service-title"><?php esc_html_e( 'Additional Services', 'wp-travel' ); ?></h3>
											<?php
											foreach ( $order_detail['trip_extras']['id'] as $k => $extra_id ) :

												$trip_extras_data = get_post_meta( $extra_id, 'wp_travel_tour_extras_metas', true );

												$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
												$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

												if ( $sale_price ) {
													$price = $sale_price;
												}

												$qty = isset( $extras['qty'][ $k ] ) && $extras['qty'][ $k ] ? $extras['qty'][ $k ] : 1;

												$total = $price * $qty;
												?>
												<div class="my-order-price-breakdown-additional-service-item clearfix">
													<span class="my-order-head"><?php echo esc_html( get_the_title( $extra_id ) ); ?> (<?php echo esc_attr( $qty ) . ' x ' . wptravel_get_formated_price_currency( $price, false, '', $booking_id ); ?>)</span>
													<span class="my-order-tail my-order-right"><?php echo wptravel_get_formated_price_currency( $total, false, '', $booking_id ); ?></span>
												</div>
											<?php endforeach; ?>

										</div>

										<?php
									endif;
								}
							} else { // single Trips.
								?>
								<div class="my-order-price-breakdown-base-price-wrap">
									<div class="my-order-price-breakdown-base-price">
										<span class="my-order-head"><?php echo esc_html( get_the_title( $trip_id ) ); ?></span>
										<span class="my-order-tail">
											<span class="my-order-price-detail"> x <?php echo esc_html( $pax ) . ' ' . __( 'Person/s', 'wp-travel' ); ?> </span>
											<span class="my-order-price"><?php echo wptravel_get_formated_price_currency( $details['sub_total'], false, '', $booking_id ); ?></span>
										</span>
									</div>
								</div>
								<?php
							}
							?>

							<div class="my-order-price-breakdown-summary clearfix">
								<div class="my-order-price-breakdown-sub-total">
									<span class="my-order-head"><?php esc_html_e( 'Sub Total Price', 'wp-travel' ); ?></span>
									<span class="my-order-tail my-order-right"><?php echo wptravel_get_formated_price_currency( $details['sub_total'], false, '', $booking_id ); ?></span>
								</div>

								<?php if ( $details['discount'] ) : ?>
									<div class="my-order-price-breakdown-coupon-amount">
										<span class="my-order-head"><?php esc_html_e( 'Discount Price', 'wp-travel' ); ?></span>
										<span class="my-order-tail my-order-right">- <?php echo wptravel_get_formated_price_currency( $details['discount'], false, '', $booking_id ); ?></span>
									</div>
								<?php endif; ?>

								<div class="my-order-price-breakdown-tax-due">
									<span class="my-order-head"><?php esc_html_e( 'Tax', 'wp-travel' ); ?> </span>
									<span class="my-order-tail my-order-right"><?php echo wptravel_get_formated_price_currency( $details['tax'], false, '', $booking_id ); ?></span>
								</div>
							</div>
						</div>
						<div class="my-order-single-total-price clearfix">
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Total', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo wptravel_get_formated_price_currency( $details['total'], false, '', $booking_id ); ?></span>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

function wptravel_view_payment_details_table( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	$payment_data = wptravel_payment_data( $booking_id );
	$status_list  = wptravel_get_payment_status();
	if ( $payment_data && count( $payment_data ) > 0 ) {
		$payment_id   = wptravel_get_payment_id( $booking_id );
		$payment_slip = get_post_meta( $payment_id, 'wp_travel_payment_slip_name', true );
		if ( ! empty( $payment_slip ) ) {
			$img_url = content_url( WP_TRAVEL_SLIP_UPLOAD_DIR . '/' . $payment_slip );
			?>
			<div class="wp-travel-bank-deposit-wrap">
				<div id="wp-travel-magnific-popup-image" class="wp-travel-magnific-popup-image wp-travel-popup">
					<img src="<?php echo esc_url( $img_url ); ?>" alt="Payment slip">
				</div>
			</div>
			<?php
		}
		?>
		<h3><?php esc_html_e( 'Payment Details', 'wp-travel' ); ?></h3>
		<table class="my-order-payment-details">
			<tr>
				<th><?php esc_html_e( 'Date', 'wp-travel' ); ?></th>
				<th><?php esc_html_e( 'Payment ID / Txn ID', 'wp-travel' ); ?></th>
				<th><?php esc_html_e( 'Payment Method', 'wp-travel' ); ?></th>
				<th><?php esc_html_e( 'Payment Amount', 'wp-travel' ); ?></th>
			</tr>
			<?php
			foreach ( $payment_data as $payment_args ) {
				if ( isset( $payment_args['data'] ) && ( is_object( $payment_args['data'] ) || is_array( $payment_args['data'] ) ) ) :
					$payment_amount = get_post_meta( $payment_args['payment_id'], 'wp_travel_payment_amount', true );
					?>
					<tr>
						<td><?php echo esc_html( $payment_args['payment_date'] ); ?></td>
						<td>
							<?php
							echo esc_html( $payment_args['payment_id'] );
							if ( 'bank_deposit' === $payment_args['payment_method'] ) {
								$txn_id = get_post_meta( $payment_args['payment_id'], 'txn_id', true );
								if ( ! empty( $txt_id ) ) {
									echo ' / ' . esc_html( $txt_id );
								}
							}
							?>
						</td>
						<td>
							<?php
							$gateway_lists = wptravel_payment_gateway_lists();

							// use payment method key in case of payment disabled or deactivated.
							$payment_method = isset( $gateway_lists[ $payment_args['payment_method'] ] ) ? $gateway_lists[ $payment_args['payment_method'] ] : $payment_args['payment_method'];

							echo esc_html( $payment_method );

							if ( 'bank_deposit' === $payment_args['payment_method'] ) {
								$payment_id   = $payment_args['payment_id'];
								$payment_slip = get_post_meta( $payment_id, 'wp_travel_payment_slip_name', true );
								if ( ! empty( $payment_slip ) ) {
									$img_url = content_url( WP_TRAVEL_SLIP_UPLOAD_DIR . '/' . $payment_slip );
									?>
									<a href="#wp-travel-magnific-popup-image-payment-table" class="wp-travel-magnific-popup" ><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'View Payment Receipt', 'wp-travel' ); ?></a>
									<div id="wp-travel-magnific-popup-image-payment-table" class="wp-travel-magnific-popup-image wp-travel-popup">
										<img src="<?php echo esc_url( $img_url ); ?>" alt="Payment slip">
									</div>
									<style>
										td #wp-travel-magnific-popup-image-payment-table.wp-travel-popup{
											display:none;
										}
									</style>
									<?php
								}
							}
							?>

						</td>
						<td>
							<?php
							if ( $payment_amount > 0 ) :
								echo wptravel_get_formated_price_currency( $payment_amount, false, '', $booking_id );
							endif;
							?>
						</td>
					</tr>
					<?php
				endif;
			}
			?>
		</table>
		<?php
	}
}

/**
 * Return Thankyou page url.
 *
 * @param Mixed $trip_id Number or null.
 *
 * @since 1.8.5
 * @return String URL.
 */
function wptravel_thankyou_page_url( $trip_id = null ) {
	$thankyou_page_id = $trip_id;
	$settings         = wptravel_get_settings();
	if ( ! $trip_id ) {
		global $wt_cart;
		$items = $wt_cart->getItems();
		if ( count( $items ) > 0 ) {
			reset( $items );
			$first_key        = key( $items );
			$thankyou_page_id = $first_key && isset( $items[ $first_key ]['trip_id'] ) ? $items[ $first_key ]['trip_id'] : 0;
		}
	}

	if ( class_exists( 'WP_Travel_Cart_Checkout_Addon' ) ) {
		$thankyou_page_id = isset( $settings['thank_you_page_id'] ) && ! empty( $settings['thank_you_page_id'] ) ? $settings['thank_you_page_id'] : wptravel_get_page_id( 'booking-thank-you' );
	}
	$thankyou_page_url = 0 < $thankyou_page_id ? get_permalink( $thankyou_page_id ) : get_home_url();
	return apply_filters( 'wptravel_thankyou_page_url', $thankyou_page_url, $trip_id );
}

/**
 * Function to check current trip is available or not.
 */
function wptravel_trip_availability( $trip_id, $price_key, $start_date, $sold_out ) {

	// For now only start date and sold out is used to determine availability. Need Enhancement in future.
	$availability = true;
	if ( strtotime( $start_date . ' 23:59:59' ) < time() || $sold_out ) {
		$availability = false;
	}
	return apply_filters( 'wptravel_trip_availability', $availability, $trip_id, $price_key, $start_date );
}

/**
 * Privacy Policy Link.
 */
function wptravel_privacy_link() {
	$settings = wptravel_get_settings();
	$link     = '';

	$privacy_policy_url = false;
	if ( function_exists( 'get_privacy_policy_url' ) ) {
		$privacy_policy_url = get_privacy_policy_url();
	}

	if ( $privacy_policy_url ) {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		$page_title     = ( $policy_page_id ) ? get_the_title( $policy_page_id ) : '';

		$open_in_new_tab = isset( $settings['open_gdpr_in_new_tab'] ) ? esc_html( $settings['open_gdpr_in_new_tab'] ) : '';

		$attr = '';
		if ( 'yes' === $open_in_new_tab ) {
			$attr = 'target="_blank"';
		}

		$link = sprintf( '<a href="%1s" %2s >%3s</a>', esc_url( $privacy_policy_url ), esc_attr( $attr ), $page_title );
	}
	return $link;
}

/**
 * Return WP Travel Strings.
 *
 * @since 2.0.0, Modified in 2.0.9
 */
function wptravel_get_strings() {
	$localized_strings = array(
		'from'                      => __( 'From', 'wp-travel' ),
		'to'                        => __( 'To', 'wp-travel' ),
		'confirm'                   => __( 'Are you sure you want to remove?', 'wp-travel' ),
		'book_now'                  => __( 'Book Now', 'wp-travel' ),
		'book_n_pay'                => __( 'Book and Pay', 'wp-travel' ),
		'select'                    => __( 'Select', 'wp-travel' ),
		'close'                     => __( 'Close', 'wp-travel' ),
		'featured_book_now'         => __( 'Book Now', 'wp-travel' ), // Book Now at the featured section.
		'featured_trip_enquiry'     => __( 'Trip Enquiry', 'wp-travel' ), // Trip Enquiry at the featured section.
		'trip_enquiry'              => __( 'Trip Enquiry', 'wp-travel' ),
		'trip_type'                 => __( 'Trip Type', 'wp-travel' ),
		'activities'                => __( 'Activities', 'wp-travel' ),
		'group_size'                => __( 'Group Size', 'wp-travel' ),
		'reviews'                   => __( 'Reviews', 'wp-travel' ),
		'locations'                 => __( 'Locations', 'wp-travel' ),
		'fixed_departure'           => __( 'Fixed Departure', 'wp-travel' ),
		'trip_duration'             => __( 'Trip Duration', 'wp-travel' ),
		'filter_by'                 => __( 'Filter By', 'wp-travel' ),
		'price'                     => __( 'Price', 'wp-travel' ),
		'location'                  => __( 'Location', 'wp-travel' ),
		'trip_date'                 => __( 'Trip date', 'wp-travel' ),
		'trip_name'                 => __( 'Trip Name', 'wp-travel' ),
		'trip_code'                 => __( 'Trip code', 'wp-travel' ),
		'show'                      => __( 'Show', 'wp-travel' ),
		'booking_tab_content_label' => __( 'Select Date and Pricing Options', 'wp-travel' ),
		'bookings'                  => array(
			'pricing_name'                  => __( 'Pricing Name', 'wp-travel' ),
			'start_date'                    => __( 'Start', 'wp-travel' ),
			'end_date'                      => __( 'End', 'wp-travel' ),
			'group_size'                    => __( 'Group (Min-Max)', 'wp-travel' ),
			'seats_left'                    => __( 'Seats left', 'wp-travel' ),
			'pax'                           => __( 'Pax', 'wp-travel' ),
			'select_pax'                    => __( 'Select Pax', 'wp-travel' ),
			'price'                         => __( 'Price', 'wp-travel' ),
			'arrival_date'                  => __( 'Arrival date', 'wp-travel' ),
			'departure_date'                => __( 'Departure date', 'wp-travel' ),
			'sold_out'                      => __( 'Sold Out', 'wp-travel' ),
			'select'                        => __( 'Select', 'wp-travel' ),
			'close'                         => __( 'Close', 'wp-travel' ),
			'book_now'                      => __( 'Book Now', 'wp-travel' ),
			'combined_pricing'              => __( 'Pricing', 'wp-travel' ), // Added for combined pricing label for categorized pricing @since 3.0.0
			'pricing_not_available'         => __( 'The pricing is not available on the selected Date. Please choose another date or pricing.', 'wp-travel' ),
			'max_pax_exceeded'              => __( 'Max. Pax Exceeded.', 'wp-travel' ),
			'date_select'                   => __( 'Select a Date', 'wp-travel' ),
			'date_select_to_view_options'   => __( 'Select a Date to view available pricings and other options.', 'wp-travel' ),
			'booking_tab_clear_all'         => __( 'Clear All', 'wp-travel' ),
			'booking_tab_cart_total'        => __( 'Total:', 'wp-travel' ),
			'booking_tab_booking_btn_label' => __( 'Book Now', 'wp-travel' ),
			'booking_tab_pax_selector'      => __( 'Pax Selector', 'wp-travel' ),
			'group_discount_tooltip'        => __( 'Group Discounts', 'wp-travel' ),
			'view_group_discount'           => __( 'Discounts', 'wp-travel' ),
			'pricings_list_label'           => __( 'Pricings', 'wp-travel' ),
			'trip_extras_list_label'        => __( 'Trip Extras', 'wp-travel' ),
			'trip_extras_link_label'        => __( 'Learn More', 'wp-travel' ),
			'available_trip_times'          => __( 'Available times', 'wp-travel' ),
			'booking_option'                => __( 'Booking Options', 'wp-travel' ),
			'booking_with_payment'          => __( 'Booking with payment', 'wp-travel' ),
			'booking_only'                  => __( 'Booking only', 'wp-travel' ),
		),
		'empty_results'             => array(
			'trip_type'  => __( 'No Trip Type', 'wp-travel' ),
			'activities' => __( 'No Activities', 'wp-travel' ),
			'group_size' => __( 'No size limit', 'wp-travel' ),

		),
		'alert'                     => array(
			'required_pax_alert'    => __( 'Pax is required.', 'wp-travel' ),
			'atleast_min_pax_alert' => __( 'Please select at least minimum pax.', 'wp-travel' ),
			'min_pax_alert'         => __( 'Pax should be greater than or equal to {min_pax}.', 'wp-travel' ),
			'max_pax_alert'         => __( 'Pax should be lower than or equal to {max_pax}.', 'wp-travel' ),
			'both_pax_alert'        => __( 'Pax should be between {min_pax} and {max_pax}.', 'wp-travel' ),
		),

	);

	return apply_filters( 'wp_travel_strings', $localized_strings );
}

/**
 * Return Pax alert message.
 *
 * @param number $min Min pax.
 * @param number $max Max pax.
 *
 * @since 2.0.9
 */
function wptravel_pax_alert_message( $min = false, $max = false ) {

	// Strings
	$strings = wptravel_get_strings();

	$range_alert = '';
	if ( $min && $max ) {
		$range_alert = isset( $strings['alert']['both_pax_alert'] ) ? $strings['alert']['both_pax_alert'] : __( 'Pax should be between {min_pax} and {max_pax}.', 'wp-travel' );
	} elseif ( $min ) {
		$range_alert = isset( $strings['alert']['min_pax_alert'] ) ? $strings['alert']['min_pax_alert'] : __( 'Pax should be greater than or equal to {min_pax}.', 'wp-travel' );
	} elseif ( $max ) {
		$range_alert = isset( $strings['alert']['max_pax_alert'] ) ? $strings['alert']['max_pax_alert'] : __( 'Pax should be lower than or equal to {max_pax}.', 'wp-travel' );
	}

	$pax_tags           = array(
		'{min_pax}' => $min,
		'{max_pax}' => $max,
	);
	$range_alert        = str_replace( array_keys( $pax_tags ), $pax_tags, $range_alert );
	$required_pax_alert = isset( $strings['alert']['required_pax_alert'] ) ? $strings['alert']['required_pax_alert'] : __( 'Pax is required.', 'wp-travel' );

	return array(
		'range'    => $range_alert,
		'required' => $required_pax_alert,
	);
}

/**
 * Return WP Travel Bank deposit account details.
 *
 * @since 2.0.0
 */
function wptravel_get_bank_deposit_account_details( $display_all_row = false ) {
	$settings = wptravel_get_settings();

	$bank_account_details = array();

	$display_fields = array(
		'account_name',
		'account_number',
		'bank_name',
		'sort_code',
		'iban',
		'swift',
	);
	$display_fields = apply_filters( 'wp_travel_filter_bank_deposit_account_fields', $display_fields );

	$bank_deposits = $settings['wp_travel_bank_deposits'];
	if ( isset( $bank_deposits['account_name'] ) && is_array( $bank_deposits['account_name'] ) && count( $bank_deposits['account_name'] ) > 0 ) {
		foreach ( $bank_deposits['account_name'] as $i => $account_name ) {
			$enable = isset( $bank_deposits['enable'][ $i ] ) ? $bank_deposits['enable'][ $i ] : 'no';

			if ( ! $display_all_row && 'no' === $enable ) { // Controls to display each enabled row.
				continue;
			}

			$account_number = isset( $bank_deposits['account_number'][ $i ] ) ? $bank_deposits['account_number'][ $i ] : '';
			$bank_name      = isset( $bank_deposits['bank_name'][ $i ] ) ? $bank_deposits['bank_name'][ $i ] : '';
			$sort_code      = isset( $bank_deposits['sort_code'][ $i ] ) ? $bank_deposits['sort_code'][ $i ] : '';
			$iban           = isset( $bank_deposits['iban'][ $i ] ) ? $bank_deposits['iban'][ $i ] : '';
			$swift          = isset( $bank_deposits['swift'][ $i ] ) ? $bank_deposits['swift'][ $i ] : '';

			$field = array();
			foreach ( $display_fields as $field_name ) {
				$field[ $field_name ] = isset( $$field_name ) ? $$field_name : ''; // Filtered fields.
			}

			$bank_account_details[] = $field;
		}
	}
	return $bank_account_details;
}

/**
 * Return WP Travel Bank deposit account table.
 *
 * @since 2.0.0
 */
function wptravel_get_bank_deposit_account_table( $show_description = true ) {
	$account_data = wptravel_get_bank_deposit_account_details();
	$settings     = wptravel_get_settings();
	ob_start();
	if ( is_array( $account_data ) && count( $account_data ) > 0 ) {
		$wp_travel_bank_deposit_description = isset( $settings['wp_travel_bank_deposit_description'] ) ? $settings['wp_travel_bank_deposit_description'] : '';

		if ( ! empty( $wp_travel_bank_deposit_description ) && true == $show_description ) :
			?>
			<p class="description"><?php echo esc_html( $wp_travel_bank_deposit_description ); ?></p>
		<?php endif; ?>

		<table width="100%">
			<tr>
				<?php if ( isset( $account_data[0]['account_name'] ) ) : ?>
					<td><?php esc_html_e( 'Account Name', 'wp-travel' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['account_number'] ) ) : ?>
					<td><?php esc_html_e( 'Account Number', 'wp-travel' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['bank_name'] ) ) : ?>
					<td><?php esc_html_e( 'Bank Name', 'wp-travel' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['sort_code'] ) ) : ?>
					<td><?php esc_html_e( 'Sort Code', 'wp-travel' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['iban'] ) ) : ?>
					<td><?php esc_html_e( 'IBAN', 'wp-travel' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['swift'] ) ) : ?>
					<td><?php esc_html_e( 'Swift', 'wp-travel' ); ?></td>
				<?php endif; ?>
			</tr>
			<?php foreach ( $account_data as $data ) { ?>
				<tr>
					<?php if ( isset( $data['account_name'] ) ) : ?>
						<td><?php echo esc_html( $data['account_name'] ); ?></td>
					<?php endif; ?>
					<?php if ( isset( $data['account_number'] ) ) : ?>
						<td><?php echo esc_html( $data['account_number'] ); ?></td>
					<?php endif; ?>
					<?php if ( isset( $data['bank_name'] ) ) : ?>
						<td><?php echo esc_html( $data['bank_name'] ); ?></td>
					<?php endif; ?>
					<?php if ( isset( $data['sort_code'] ) ) : ?>
						<td><?php echo esc_html( $data['sort_code'] ); ?></td>
					<?php endif; ?>
					<?php if ( isset( $data['iban'] ) ) : ?>
						<td><?php echo esc_html( $data['iban'] ); ?></td>
					<?php endif; ?>
					<?php if ( isset( $data['swift'] ) ) : ?>
						<td><?php echo esc_html( $data['swift'] ); ?></td>
					<?php endif; ?>
				</tr>
			<?php } ?>
		</table>
		<?php
	} else {
		esc_html_e( 'No detail found', 'wp-travel' );
	}
	$content = ob_get_contents();
	ob_end_clean();
	return $content;

}

/**
 * Return list of submenu array.
 *
 * @since 2.0.2
 */
function wptravel_get_submenu() {
	$all_submenus = array(
		'bookings' => array(

			'coupon'         => array( 'priority' => '20' ), // post types.
			'enquiries'      => array( 'priority' => '30' ), // post types.
			'extras'         => array( 'priority' => '40' ), // post types.
			'downloads'      => array( 'priority' => '100' ), // post types.

			'system_info'    => array(
				'priority'   => '100',
				'page_title' => __( 'System Status', 'wp-travel' ),
				'menu_title' => __( 'Status', 'wp-travel' ),
				'menu_slug'  => 'sysinfo',
				'callback'   => array( 'WP_Travel_Admin_Settings', 'get_system_info' ),
			),
			'reports'        => array(
				'priority'   => '120',
				'page_title' => __( 'Reports', 'wp-travel' ),
				'menu_title' => __( 'Reports', 'wp-travel' ),
				'menu_slug'  => 'booking_chart',
				'callback'   => 'wptravel_get_booking_chart',
			),
			'custom_filters' => array(
				'priority'   => '125',
				'page_title' => __( 'Custom Filters', 'wp-travel' ),
				'menu_title' => __( 'Custom Filters', 'wp-travel' ),
				'menu_slug'  => 'wp_travel_custom_filters_page',
			),
			'marketplace'    => array(
				'priority'   => '140',
				'page_title' => __( 'Marketplace', 'wp-travel' ),
				'menu_title' => __( 'Marketplace', 'wp-travel' ),
				'menu_slug'  => 'wp-travel-marketplace',
				'callback'   => 'wptravel_marketplace_page',
			),
		),
	);

	$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true ); // need to remove if no queries made for old settings page.

	if ( ! $react_settings_enable ) {
		$all_submenus['bookings']['settings'] = array(
			'priority'   => '130',
			'page_title' => __( 'WP Travel Settings', 'wp-travel' ),
			'menu_title' => __( 'Settings', 'wp-travel' ),
			'menu_slug'  => 'settings',
			'callback'   => array( 'WP_Travel_Admin_Settings', 'setting_page_callback' ),
		);
	} else {
		$all_submenus['bookings']['settings'] = array(
			'priority'   => '130',
			'page_title' => __( 'WP Travel Settings', 'wp-travel' ),
			'menu_title' => __( 'Settings', 'wp-travel' ),
			'menu_slug'  => 'settings',
			'callback'   => array( 'WP_Travel_Admin_Settings', 'setting_page_callback_new' ),
		);
		// Temporary page [Need to remove in a while]
		$all_submenus['bookings']['settings2'] = array(
			'priority'   => '130',
			'page_title' => __( 'WP Travel Settings', 'wp-travel' ),
			'menu_title' => __( 'Settings', 'wp-travel' ),
			'menu_slug'  => 'settings2',
			'callback'   => array( 'WP_Travel_Admin_Settings', 'setting_page_callback_new' ),
		);
	}

	if ( ! class_exists( 'WP_Travel_Downloads_Core' ) ) :
		$all_submenus['bookings']['downloads']['page_title'] = __( 'Downloads', 'wp-travel' );
		$all_submenus['bookings']['downloads']['menu_title'] = __( 'Downloads', 'wp-travel' );
		$all_submenus['bookings']['downloads']['menu_slug']  = 'download_upsell_page';
		$all_submenus['bookings']['downloads']['callback']   = 'wptravel_get_download_upsell';
	endif;

	if ( ! class_exists( 'WP_Travel_Custom_Filters_Core' ) ) {
		$all_submenus['bookings']['custom_filters']['callback'] = 'wptravel_custom_filters_upsell';
	}

	return apply_filters( 'wp_travel_submenus', $all_submenus );
}


/**
 * Return all fixed departure date. single and multiple date.
 *
 * @since 2.0.5
 */
function wptravel_get_fixed_departure_date( $trip_id ) {

	$start_date    = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$end_date      = get_post_meta( $trip_id, 'wp_travel_end_date', true );
	$show_end_date = wptravel_booking_show_end_date();
	$date_format   = get_option( 'date_format' );
	if ( ! $date_format ) :
		$date_format = 'jS M, Y';
	endif;

	$react_version_enabled = wptravel_is_react_version_enabled();

	ob_start();
	if ( 'single-price' === wptravel_get_pricing_option_type() ) {
		if ( $start_date || $end_date ) :
			if ( $start_date || $end_date ) :

				if ( '' !== $end_date && $show_end_date ) {
					printf( '%s - %s', date_i18n( $date_format, strtotime( $start_date ) ), date_i18n( $date_format, strtotime( $end_date ) ) );
				} else {
					printf( '%s', date_i18n( $date_format, strtotime( $start_date ) ) );
				}
			else :
				esc_html_e( 'N/A', 'wp-travel' );
			endif;
		endif;

	} elseif ( 'multiple-price' === wptravel_get_pricing_option_type() ) {
		$dates                = array();
		$trip_pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );
		/**
		 * @since 4.0.3
		 */
		if ( $react_version_enabled ) {
			$trip_pricing_options = wptravel_get_trip_pricings_with_dates( $trip_id );
		}
		if ( is_array( $trip_pricing_options ) && count( $trip_pricing_options ) > 0 ) {
			if ( $react_version_enabled ) { // @since 4.0.3
				$dates = wptravel_get_trip_available_dates( $trip_id );
			} else {
				foreach ( $trip_pricing_options as $price_key => $pricing ) :
					// Set Vars.
					$price_key       = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';
					$available_dates = wptravel_get_trip_available_dates( $trip_id, $price_key ); // No need to pass date
					if ( is_array( $available_dates ) && count( $available_dates ) > 0 ) { // multiple available dates
						foreach ( $available_dates as $available_date ) {
							$dates[] = $available_date;
						}
					}
				endforeach;
			}
			$dates = array_unique( $dates );
			usort( $dates, 'wptravel_date_sort' );
			$show_multiple = apply_filters( 'wp_travel_show_multiple_fixed_departure_dates', false );

			$date_found = false;
			if ( $show_multiple ) {
				echo '<ul>';
				foreach ( $dates as $date ) {
					if ( date( 'Y-m-d ', strtotime( $date ) ) >= date( 'Y-m-d' ) ) {
						$date_found = true;
						printf( '<li>%s</li>', date_i18n( $date_format, strtotime( $date ) ) );
					}
				}
				echo '</ul>';
			} else {
				if ( is_array( $dates ) && count( $dates ) > 0 ) {
					foreach ( $dates as $date ) {
						if ( date( 'Y-m-d ', strtotime( $date ) ) >= date( 'Y-m-d' ) ) {
							$date_found = true;
							printf( '%s', date_i18n( $date_format, strtotime( $date ) ) );
							break;
						}
					}
				}
			}
			if ( ! $date_found ) {
				echo __( 'N/A', 'wp-travel' );
			}
		}
	}

	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Custom Function to sort date array. [Just to sort date]. Do not use it directly.
 *
 * @since 2.0.5
 */
function wptravel_date_sort( $a, $b ) {
	return strtotime( $a ) - strtotime( $b );
}

/**
 * Custom Function to sort date array desc. [Just to sort date]. Do not use it directly.
 *
 * @since 3.0.5
 */
function wptravel_date_sort_desc( $a, $b ) {
	return strtotime( $b ) - strtotime( $a );
}

/**
 * Removes expired date form the array.
 *
 * @param array $dates date.
 * @since 3.0.5
 */
function wptravel_filter_expired_date( $dates ) {
	$dates = array_filter(
		$dates,
		function( $date ) {
			return strtotime( $date ) >= strtotime( 'today' );
		}
	);
	return $dates;
}

/**
 * Front end Booking tab sort pricing array by date asc.
 *
 * @since 2.0.8
 */
function wptravel_pricing_date_sort_asc( $a, $b ) {
	if ( isset( $a['arrival_date'] ) && isset( $b['arrival_date'] ) ) {
		return strtotime( $a['arrival_date'] ) - strtotime( $b['arrival_date'] );
	}
}

/**
 * Front end Booking tab sort pricing array by date desc.
 *
 * @since 2.0.8
 */
function wptravel_pricing_date_sort_desc( $a, $b ) {
	if ( isset( $a['arrival_date'] ) && isset( $b['arrival_date'] ) ) {
		return strtotime( $b['arrival_date'] ) - strtotime( $a['arrival_date'] );
	}
}

/**
 * Used to display price per field in single trip page main price, related trip and archive.
 *
 * @since 2.0.5
 */
function wptravel_hide_price_per_field( $trip_id = null, $price_key = null ) {
	return apply_filters( 'wp_travel_hide_per_person', false, $trip_id, $price_key );
}

/**
 *
 * Function to return Frontend Gallery Block.
 *
 * @param array $gallery_ids All gallery ids.
 *
 * @since 2.0.6
 */
function wptravel_frontend_tab_gallery( $gallery_ids ) {
	if ( ! $gallery_ids ) {
		return;
	}
	ob_start();
	if ( is_array( $gallery_ids ) && count( $gallery_ids ) > 0 ) :
		$image_size = apply_filters( 'wp_travel_gallery_image', 'thumbnail' ); // previously using 'medium' before 1.9.0
		?>
		<div class="wp-travel-gallery wp-travel-container-wrap">
			<div class="wp-travel-row-wrap">
				<ul>
					<?php foreach ( $gallery_ids as $gallery_id ) : ?>
					<li>
						<?php $gallery_image = wp_get_attachment_image_src( $gallery_id, $image_size ); ?>
						<a title="<?php echo esc_attr( wp_get_attachment_caption( $gallery_id ) ); ?>" href="<?php echo esc_url( wp_get_attachment_url( $gallery_id ) ); ?>">
						<img alt="" src="<?php echo esc_attr( $gallery_image[0] ); ?>" />
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php else : ?>
		<p class="wp-travel-no-detail-found-msg"><?php esc_html_e( 'No gallery images found.', 'wp-travel' ); ?></p>
		<?php
	endif;

	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Get Cart Item Price with Trip Extras.
 *
 * @since 4.0.3
 */
function wptravel_get_cart_item_price_with_extras( $cart_id, $trip_id, $partial = false ) {
	global $wt_cart;

	$items = $wt_cart->getItems();

	$price = 0;

	if ( isset( $items[ $cart_id ] ) ) {
		$item = $items[ $cart_id ];

		$price       = $partial ? (float) $item['trip_price_partial'] : (float) $item['trip_price'];
		$pricing     = wptravel_get_cart_pricing( $cart_id );
		$trip_extras = isset( $pricing['trip_extras'] ) ? array_column( $pricing['trip_extras'], null, 'id' ) : array();

		$cart_extras = isset( $item['trip_extras'] ) && ! empty( $item['trip_extras'] ) ? (array) $item['trip_extras'] : array(
			'id'  => array(),
			'qty' => array(),
		);

		$cart_extras = array_combine( $cart_extras['id'], $cart_extras['qty'] );

		foreach ( $cart_extras as $xid => $xqty ) {
			$extra = isset( $trip_extras[ $xid ] ) ? $trip_extras[ $xid ] : null;
			if ( is_null( $extra ) || empty( $extra['tour_extras_metas'] ) ) {
				continue;
			}
			$extra_metas = $extra['tour_extras_metas'];
			$xprice      = isset( $extra['is_sale'] ) && $extra['is_sale'] ? $extra_metas['extras_item_sale_price'] : $extra_metas['extras_item_price'];
			$xqty        = (int) $xqty;
			$xqty        = $extra['is_required'] && $xqty <= 0 ? 1 : $xqty;

			$price += $xqty * (float) $xprice;
		}
	}

	return $price;
}

/**
 * Get Pricing by pricing Id
 *
 * @since 4.0.3
 * @return array|null
 */
function wptravel_get_pricing_by_pricing_id( $trip_id, $pricing_id ) {
	$pricings_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, true );
	$trip_pricings = ! is_wp_error( $pricings_data ) && isset( $pricings_data['pricings'] ) ? $pricings_data['pricings'] : array(); // Trip Pricings.
	/**
	 * Pricing by pricing id to add date easily on looping array item.
	 */
	$trip_pricings_by_id = array_column( $trip_pricings, null, 'id' );

	return isset( $trip_pricings_by_id[ $pricing_id ] ) ? $trip_pricings_by_id[ $pricing_id ] : null;
}

/**
 * Get Pricing by Cart ID.
 *
 * @since 4.0.3
 */
function wptravel_get_cart_pricing( $cart_id ) {
	global $wt_cart;
	$items = $wt_cart->getItems();

	if ( isset( $items[ $cart_id ] ) ) {
		$item       = $items[ $cart_id ];
		$pricing_id = $item['pricing_id'];
		$trip_id    = $item['trip_id'];
		return wptravel_get_pricing_by_pricing_id( $trip_id, $pricing_id );
	}
	return null;
}

/**
 * @since 4.0.6
 */
if ( ! function_exists( 'wptravel_get_trip_pricings' ) ) :
	function wptravel_get_trip_pricings( $trip_id ) {
		$pricings_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, true );
		return ! is_wp_error( $pricings_data ) && isset( $pricings_data['pricings'] ) ? $pricings_data['pricings'] : array(); // Trip Pricings.
	}
endif;

/**
 * Get Pricing Options.
 *
 * @since 4.0.0
 */
if ( ! function_exists( 'wptravel_get_trip_pricings_with_dates' ) ) {
	function wptravel_get_trip_pricings_with_dates( $trip_id ) {
		if ( ! $trip_id ) {
			global $post;
			if ( WP_TRAVEL_POST_TYPE !== $post->post_type ) {
				return;
			}
			$trip_id = $post->ID;
		}

		$trip_pricings = wptravel_get_trip_pricings( $trip_id );

		$dates_data = WP_Travel_Helpers_Trip_Dates::get_dates( $trip_id );
		$trip_dates = ! is_wp_error( $dates_data ) && 'WP_TRAVEL_TRIP_DATES' === $dates_data['code'] ? $dates_data['dates'] : array(); // All the trip related dates;

		$trip_pricings_with_dates = array(); // This will contains all the looping pricing while listing.

		/**
		 * Pricing by pricing id to add date easily on looping array item.
		 */
		$trip_pricings_by_id = array_column( $trip_pricings, null, 'id' );
		if ( ! empty( $trip_dates ) ) {
			/**
			 * Pricing by pricing id to add date easily on looping array item.
			 */
			$trip_pricings_by_id = array_column( $trip_pricings, null, 'id' );

			$i = 0;
			foreach ( $trip_dates as $trip_date ) {
				$date_pricings = explode( ',', $trip_date['pricing_ids'] ); // Date may contains multiple pricing ids separated by (,).

				/**
				 * Making final looping array with both pricing and dates.
				 * Each item for each pricing and each pricing for each date.
				 */
				foreach ( $date_pricings as $pricing_id ) {
					$pricing = array();
					if ( isset( $trip_pricings_by_id[ $pricing_id ] ) ) {
						$pricing = $trip_pricings_by_id[ $pricing_id ];

						$trip_pricings_with_dates[ $i ]         = $pricing;
						$trip_pricings_with_dates[ $i ]['date'] = $trip_date;
						$i++;
					}
				}
			}
		} else {
			foreach ( $trip_pricings as $index => $pricing ) {
				$pricing['date']                    = array(
					'start_date' => '',
					'end_date'   => '',
				);
				$trip_pricings_with_dates[ $index ] = $pricing;
			}
		}

		uasort(
			$trip_pricings_with_dates,
			function( $a, $b ) {
				$date_a = $a['date']['start_date'];
				$date_b = $b['date']['start_date'];
				if ( $date_a == $date_b ) {
					return 0;
				}
				return $date_a < $date_b ? -1 : 1;
			}
		);

		return $trip_pricings_with_dates; // index must be pricing id to work in most cases.
	} // ends.
}

/**
 *
 * Return All Pricing. Use to display regular listing.
 *
 * @param array $trip_id Current Trip ID.
 *
 * @since 2.0.8
 */
function wptravel_get_trip_pricing_option( $trip_id = null ) {

	if ( ! $trip_id ) {
		global $post;
		$trip_id = $post->ID;
	}

	if ( WP_TRAVEL_POST_TYPE !== get_post_type( $trip_id ) ) {
		return;
	}

	// Dates and prices.
	$dates         = array();
	$pricing       = array();
	$trip_duration = array();

	$start_date         = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$end_date           = get_post_meta( $trip_id, 'wp_travel_end_date', true );
	$days               = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$night              = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$default_group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );
	$default_group_size = ! empty( $default_group_size ) ? $default_group_size : 999;

	// Fixed Departures
	$fixed_departure         = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );
	if ( ! empty( $days ) ) {
		$trip_duration['days']  = $days;
		$trip_duration['night'] = $night;
	}

	$settings                                       = wptravel_get_settings();
	$enable_multiple_category_on_pricing            = $settings['enable_multiple_category_on_pricing'];
	$wp_travel_user_after_multiple_pricing_category = get_option( 'wp_travel_user_after_multiple_pricing_category' ); // Hide enable_multiple_category_on_pricing option if user is new from @since 3.0.0

	$switch_to_react = wptravel_is_react_version_enabled();
	if ( 'yes' === $wp_travel_user_after_multiple_pricing_category || 'yes' === $enable_multiple_category_on_pricing ) : // New Multiple category on pricing. // From this version single pricing is removed for new users.

		if ( $switch_to_react ) {
			$pricing_options = wptravel_get_trip_pricings_with_dates( $trip_id );
		} else { // Our tradition before < 4.0.0
			// Price.
			$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

			// Dates.
			$available_trip_dates = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true );
		}

		// variable used for api data.
		// End of variable used for api data.
		$pricing_option_type = wptravel_get_pricing_option_type( $trip_id );
		if ( 'single-price' === $pricing_option_type ) { // Legacy Support for single price option @since 3.0.0
			$args                             = $args_regular = array( 'trip_id' => $trip_id );
			$args_regular['is_regular_price'] = true;
			$pricing_data                     = array(
				'pricing_name'             => 'Default Price',
				'price_key'                => 'default-pricing',
				'group_size'               => $default_group_size,
				'fixed_departure'          => $fixed_departure,
				'multiple_fixed_departure' => $multiple_fixed_departue,
				'arrival_date'             => $start_date,
				'departure_date'           => $end_date,
				'categories'               => array(
					array(
						'type'         => 'default',
						'custom_label' => __( 'Custom', 'wp-travel' ),
						'price_per'    => wptravel_get_price_per_text( $trip_id ),
						'enable_sale'  => ( WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id ) ) ) ? 'yes' : 'no',
						'regular'      => WP_Travel_Helpers_Pricings::get_price( $args_regular ),
						'price'        => WP_Travel_Helpers_Pricings::get_price( $args ),
					),
				),
				'inventory'                => array(
					'status_message' => __( 'N/A', 'wp-travel' ),
					'sold_out'       => false,
					'available_pax'  => $default_group_size,
					'booked_pax'     => 0,
					'pax_limit'      => $default_group_size,
					'min_pax'        => 1,
					'max_pax'        => $default_group_size,
				),
				'pricing_id'               => 'single-pricing-id',
			);
			if ( 'no' === $fixed_departure ) {
				$pricing_data['trip_duration_days']  = $days;
				$pricing_data['trip_duration_night'] = $night;
			}
			$pricing['single-pricing'] = $pricing_data; // Legacy single pricing migration @since 3.0.0
		} elseif ( 'multiple-price' === $pricing_option_type ) { // Case: Multiple Pricing.
			if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) :
				foreach ( $pricing_options as $pricing_id => $pricing_option ) {
					if ( $switch_to_react ) {
						$pricing_id = $pricing_option['id'];
					}
					if ( $switch_to_react ) {
						$pricing_name    = isset( $pricing_option['title'] ) ? $pricing_option['title'] : '';
						$price_key       = isset( $pricing_option['id'] ) ? $pricing_option['id'] : '';
						$pricing_min_pax = ! empty( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : 1;
						$pricing_max_pax = ! empty( $pricing_option['max_pax'] ) ? $pricing_option['max_pax'] : $default_group_size;
					} else {
						$pricing_name    = isset( $pricing_option['pricing_name'] ) ? $pricing_option['pricing_name'] : '';
						$price_key       = isset( $pricing_option['price_key'] ) ? $pricing_option['price_key'] : '';
						$pricing_min_pax = ! empty( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : 1;
						$pricing_max_pax = ! empty( $pricing_option['max_pax'] ) ? $pricing_option['max_pax'] : $default_group_size;
					}

					$pricing_categories = isset( $pricing_option['categories'] ) ? $pricing_option['categories'] : array();

					// Pricing Category.
					$categories = array();

					if ( is_array( $pricing_categories ) && count( $pricing_categories ) > 0 ) {
						foreach ( $pricing_categories as $index => $pricing_category ) {

							if ( $switch_to_react ) {
								$pricing_category_id = $pricing_category['id'];
								// $categories[ $pricing_category['id'] ] = $pricing_category;
								$categories[ $pricing_category_id ]['type']         = 'custom'; // following the tradition.
								$categories[ $pricing_category_id ]['custom_label'] = $pricing_category['term_info']['title'];
								$categories[ $pricing_category_id ]['price_per']    = $pricing_category['price_per'];
								$categories[ $pricing_category_id ]['enable_sale']  = isset( $pricing_category['is_sale'] ) ? 'yes' : 'no';
								$categories[ $pricing_category_id ]['regular']      = $pricing_category['regular_price'];
								$categories[ $pricing_category_id ]['price']        = $pricing_category['sale_price'];
							} else {
								$category_id                      = $index;
								$args                             = $args_regular = array(
									'trip_id'     => $trip_id,
									'pricing_id'  => $pricing_id,
									'category_id' => $category_id,
								);
								$args_regular['is_regular_price'] = true;

								$categories[ $category_id ]['type']         = $pricing_category['type'];
								$categories[ $category_id ]['custom_label'] = $pricing_category['custom_label'];
								$categories[ $category_id ]['price_per']    = $pricing_category['price_per'];
								$categories[ $category_id ]['enable_sale']  = isset( $pricing_category['enable_sale'] ) ? $pricing_category['enable_sale'] : 'no';
								$categories[ $category_id ]['regular']      = WP_Travel_Helpers_Pricings::get_price( $args_regular );
								$categories[ $category_id ]['price']        = WP_Travel_Helpers_Pricings::get_price( $args );
							}
						}
					} else {
						$args                             = $args_regular = array(
							'trip_id'    => $trip_id,
							'pricing_id' => $pricing_id,
							'price_key'  => @$pricing_option['price_key'],
						);
						$args_regular['is_regular_price'] = true;

						$categories[ $pricing_id ]['type']         = isset( $pricing_option['type'] ) ? $pricing_option['type'] : '';
						$categories[ $pricing_id ]['custom_label'] = isset( $pricing_option['custom_label'] ) ? $pricing_option['custom_label'] : '';
						$categories[ $pricing_id ]['price_per']    = isset( $pricing_option['price_per'] ) ? $pricing_option['price_per'] : '';
						// $categories[ $pricing_id ]['sale_price']  = $pricing_option['sale_price'];
						$categories[ $pricing_id ]['enable_sale'] = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
						$categories[ $pricing_id ]['regular']     = WP_Travel_Helpers_Pricings::get_price( $args_regular );
						$categories[ $pricing_id ]['price']       = $categories[ $pricing_id ]['enable_sale'] === 'yes' ? @$pricing_option['sale_price'] : @$pricing_option['price'];
					}

					$inventory_data = array(
						'max_pax'        => $pricing_max_pax,
						'min_pax'        => $pricing_min_pax,
						'available_pax'  => $pricing_max_pax,
						'status_message' => '',
						'sold_out'       => false,
						'booked_pax'     => 0,
						'pax_limit'      => $pricing_max_pax,
					);

					$pricing_data = array(
						'pricing_name'             => $pricing_name,
						'price_key'                => $price_key,
						'group_size'               => $default_group_size,
						'categories'               => $categories,
						'fixed_departure'          => $fixed_departure,
						'multiple_fixed_departure' => $multiple_fixed_departue,
						'trip_duration_days'       => $days,
						'trip_duration_night'      => $night,
						'arrival_date'             => '',
						'departure_date'           => '',
						'status'                   => '',
						'pricing_id'               => $pricing_id,
					);

					if ( 'yes' === $fixed_departure ) {
						// If Multiple Fixed Departure Enabled.
						if ( 'yes' === $multiple_fixed_departue ) {
							if ( ! $switch_to_react ) : // If data not migrated yet follow the tradition.
								$available_trip_dates = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true );
								foreach ( $available_trip_dates as $date_options_key => $date_option ) {
									$start_date = isset( $date_option['start_date'] ) ? $date_option['start_date'] : null;
									$end_date   = isset( $date_option['end_date'] ) ? $date_option['end_date'] : null;
									$date_label = isset( $date_option['date_label'] ) ? $date_option['date_label'] : null;

									$price_keys = isset( $date_option['pricing_options'] ) ? $date_option['pricing_options'] : null; // Price key to validate pricing. need to remove in future version. use $pricing_id instead of this.
									if ( is_array( $price_keys ) && in_array( $price_key, $price_keys ) ) {

										// Inventory option in multiple dates.
										$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );

										$pricing_data['date_id']        = $date_options_key;
										$pricing_data['arrival_date']   = $start_date;
										$pricing_data['departure_date'] = $end_date;
										$pricing_data['date_label']     = $date_label;
										$pricing_data['inventory']      = $inventory_data;
										$pricing[]                      = $pricing_data;
									}
								}
							else : // If pricing data migrated.
								$pricing_date = $pricing_option['date'];
								$start_date   = isset( $pricing_date['start_date'] ) ? $pricing_date['start_date'] : null;
								$end_date     = isset( $pricing_date['end_date'] ) ? $pricing_date['end_date'] : null;
								$date_label   = isset( $pricing_date['title'] ) ? $pricing_date['title'] : null;

								$pricing_data['arrival_date']   = $start_date;
								$pricing_data['departure_date'] = $end_date;
								// Inventory option in single dates.
								$inventory_data            = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );
								$pricing_data['inventory'] = $inventory_data;
								$pricing_data['date']      = $pricing_date;
								$pricing[]                 = $pricing_data;

								// $pricing[] = $pricing_data;
							endif;// If not migrated condition block ends.
						} else { // Fixed departure but not multi fixed departure.
							$pricing_data['arrival_date']   = $start_date;
							$pricing_data['departure_date'] = $end_date;
							// Inventory option in single dates.
							$inventory_data            = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );
							$pricing_data['inventory'] = $inventory_data;
							$pricing[ $pricing_id ]    = $pricing_data;
						}
					} else {
						$pricing_data['inventory'] = $inventory_data;
						$pricing[ $pricing_id ]    = $pricing_data;
					}
				}
			endif;
		} //  End Case: Multiple Pricing.
	endif;

	return array(
		'pricing_data' => $pricing,
	);
}

// @since 2.0.8
function wptravel_trip_pricing_sort_by_date( $pricings, $sort = 'asc' ) {
	if ( ! $pricings ) {
		return;
	}

	if ( is_array( $pricings ) && count( $pricings ) > 0 ) {
		if ( 'asc' === $sort ) {
			usort( $pricings, 'wptravel_pricing_date_sort_asc' );
		} else {
			usort( $pricings, 'wptravel_pricing_date_sort_desc' );
		}
	}
	return $pricings;
}

/**
 *
 * Return All Pricing. Use to display Multiple pricing option listing.
 *
 * @param array $trip_id Current Trip ID.
 *
 * @since 2.0.9 // Need to depricate
 **/
function wptravel_get_trip_listing_option( $trip_id = null ) {

	if ( ! $trip_id ) {
		global $post;
		$trip_id = $post->ID;
	}

	if ( WP_TRAVEL_POST_TYPE !== get_post_type( $trip_id ) ) {
		return;
	}

	// Dates and prices.
	$dates               = array();
	$pricing             = array();
	$trip_duration       = array();
	$pricing_option_type = wptravel_get_pricing_option_type( $trip_id );
	$fixed_departure     = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

	// Date. [need in multiple pricing also, if multiple date is disabled].
	$start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );

	$days  = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	if ( ! empty( $days ) ) {
		$trip_duration['days']  = $days;
		$trip_duration['night'] = $night;
	}
	if ( 'single-price' === $pricing_option_type ) {
		$group_size     = get_post_meta( $trip_id, 'wp_travel_group_size', true );
		$inventory_data = array(
			'status_message' => __( 'N/A', 'wp-travel' ),
			'sold_out'       => false,
			'available_pax'  => 0,
			'booked_pax'     => 0,
			'pax_limit'      => 0,
			'min_pax'        => 1,
			'max_pax'        => $group_size,
		);

		// Price.
		$args                             = $args_regular = array( 'trip_id' => $trip_id );
		$args_regular['is_regular_price'] = true;
		$pricing_data                     = array(
			'pricing_name'    => '',
			'price_key'       => '',
			'group_size'      => wptravel_get_group_size(),
			'enable_sale'     => ( WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id ) ) ) ? 'yes' : 'no',
			'regular'         => WP_Travel_Helpers_Pricings::get_price( $args_regular ),
			'price'           => WP_Travel_Helpers_Pricings::get_price( $args ),
			'price_per'       => wptravel_get_price_per_text( $trip_id ),
			'price_per_key'   => wptravel_get_price_per_text( $trip_id, '', true ),

			'status'          => $inventory_data['status_message'], // Seats left.
			'sold_out'        => $inventory_data['sold_out'],
			'available_pax'   => $inventory_data['available_pax'],
			'booked_pax'      => $inventory_data['booked_pax'],
			'pax_limit'       => $inventory_data['pax_limit'],
			'min_pax'         => $inventory_data['min_pax'],
			'max_pax'         => $inventory_data['max_pax'],
			'fixed_departure' => $fixed_departure,
		);

		if ( 'no' === $fixed_departure ) {
			$pricing_data['trip_duration_days']  = $days;
			$pricing_data['trip_duration_night'] = $night;
		} else {
			$pricing_data['arrival_date']   = $start_date;
			$pricing_data['departure_date'] = $end_date;

			$inventory_data                = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, '', $start_date );
			$pricing_data['status']        = $inventory_data['status_message'];
			$pricing_data['sold_out']      = $inventory_data['sold_out'];
			$pricing_data['available_pax'] = $inventory_data['available_pax'];
			$pricing_data['booked_pax']    = $inventory_data['booked_pax'];
			$pricing_data['pax_limit']     = $inventory_data['pax_limit'];
			$pricing_data['min_pax']       = $inventory_data['min_pax'];
			$pricing_data['max_pax']       = $inventory_data['max_pax'];
		}

		$pricing[] = $pricing_data;

	} elseif ( 'multiple-price' === $pricing_option_type ) {
		// Price.
		$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		// Dates.
		$available_trip_dates = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true );

		// End of variable used for api data.
		if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
			foreach ( $pricing_options as $pricing_id => $pricing_option ) {
				$pricing_name         = isset( $pricing_option['pricing_name'] ) ? $pricing_option['pricing_name'] : '';
				$price_key            = isset( $pricing_option['price_key'] ) ? $pricing_option['price_key'] : '';
				$pricing_type         = isset( $pricing_option['type'] ) ? $pricing_option['type'] : '';
				$pricing_custom_label = isset( $pricing_option['custom_label'] ) ? $pricing_option['custom_label'] : '';
				$pricing_option_price = isset( $pricing_option['price'] ) ? $pricing_option['price'] : '';
				$pricing_sale_enabled = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
				$pricing_sale_price   = isset( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : '';
				$pricing_min_pax      = isset( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : '';
				$pricing_max_pax      = isset( $pricing_option['max_pax'] ) ? $pricing_option['max_pax'] : '';

				// Default Inventory data.
				$inventory_data                   = array(
					'status_message' => __( 'N/A', 'wp-travel' ),
					'sold_out'       => false,
					'available_pax'  => 0,
					'booked_pax'     => 0,
					'pax_limit'      => 0,
					'min_pax'        => $pricing_min_pax,
					'max_pax'        => $pricing_max_pax,
				);
				$args                             = $args_regular = array( 'trip_id' => $trip_id );
				$args_regular['is_regular_price'] = true;
				$pricing_data                     = array(
					'pricing_name'    => $pricing_name,
					'price_key'       => $price_key,
					'group_size'      => '',
					'enable_sale'     => $pricing_sale_enabled,
					'regular'         => WP_Travel_Helpers_Pricings::get_price( $args_regular ),
					'price'           => WP_Travel_Helpers_Pricings::get_price( $args ),
					'price_per'       => wptravel_get_price_per_text( $trip_id, $price_key ),
					'price_per_key'   => wptravel_get_price_per_text( $trip_id, $price_key, true ),

					'fixed_departure' => $fixed_departure,
					// Inventory defaults.
					'status'          => $inventory_data['status_message'], // Seats left.
					'sold_out'        => $inventory_data['sold_out'],
					'available_pax'   => $inventory_data['available_pax'],
					'booked_pax'      => $inventory_data['booked_pax'],
					'pax_limit'       => $inventory_data['pax_limit'],
					'min_pax'         => $inventory_data['min_pax'],
					'max_pax'         => $inventory_data['max_pax'],
				);

				if ( 'no' === $fixed_departure ) {
					$pricing_data['trip_duration_days']  = $days;
					$pricing_data['trip_duration_night'] = $night;

					$pricing[] = $pricing_data; // Trip duration, multiple pricing.
				} else {
					$multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

					if ( 'yes' === $multiple_fixed_departue ) {
						// Dates.
						$available_trip_dates = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true );
						foreach ( $available_trip_dates as $date_options_key => $date_option ) {
							$start_date = isset( $date_option['start_date'] ) ? $date_option['start_date'] : null;
							$end_date   = isset( $date_option['end_date'] ) ? $date_option['end_date'] : null;
							$date_label = isset( $date_option['date_label'] ) ? $date_option['date_label'] : null;

							$price_keys = isset( $date_option['pricing_options'] ) ? $date_option['pricing_options'] : null; // Price key to validate pricing.

							if ( in_array( $price_key, $price_keys ) ) {

								// Inventory option in multiple dates.
								$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );

								$pricing_data['status']        = $inventory_data['status_message'];
								$pricing_data['sold_out']      = $inventory_data['sold_out'];
								$pricing_data['available_pax'] = $inventory_data['available_pax'];
								$pricing_data['booked_pax']    = $inventory_data['booked_pax'];
								$pricing_data['pax_limit']     = $inventory_data['pax_limit'];
								$pricing_data['min_pax']       = $inventory_data['min_pax'];
								$pricing_data['max_pax']       = $inventory_data['max_pax'];
								// Inventory Ends.
								$pricing_data['arrival_date']   = $start_date;
								$pricing_data['departure_date'] = $end_date;
								$pricing_data['date_label']     = $date_label;
								$pricing[]                      = $pricing_data;
							}
						}
					} else {
						$pricing_data['arrival_date']   = $start_date;
						$pricing_data['departure_date'] = $end_date;
						// Inventory option in single dates.
						$inventory_data                = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );
						$pricing_data['status']        = $inventory_data['status_message'];
						$pricing_data['sold_out']      = $inventory_data['sold_out'];
						$pricing_data['available_pax'] = $inventory_data['available_pax'];
						$pricing_data['booked_pax']    = $inventory_data['booked_pax'];
						$pricing_data['pax_limit']     = $inventory_data['pax_limit'];
						$pricing_data['min_pax']       = $inventory_data['min_pax'];
						$pricing_data['max_pax']       = $inventory_data['max_pax'];
						// Inventory Ends.
						$pricing[] = $pricing_data;  // single date multiple pricing.
					}
				}
			}
		}
	}

	return array(
		'pricing_data' => $pricing,
	);
}

/**
 * Return DB Privieges as per current db user.
 */
function wptravel_db_user_privileges() {
	global $wpdb;
	$query      = "SELECT * FROM mysql.user WHERE user = '" . DB_USER . "'";
	$privileges = $wpdb->get_row( $query );

	$response = array(
		'insert' => 'Y' === $privileges->Insert_priv ? true : false,
		'update' => 'Y' === $privileges->Update_priv ? true : false,
		'delete' => 'Y' === $privileges->Delete_priv ? true : false,
		'create' => 'Y' === $privileges->Create_priv ? true : false,
		'drop'   => 'Y' === $privileges->Drop_priv ? true : false,
		'alter'  => 'Y' === $privileges->Alter_priv ? true : false,

	);
	return $response;
}

if ( ! function_exists( 'wptravel_comments' ) ) {
	function wptravel_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		wptravel_load_template(
			'review.php',
			array(
				'comment' => $comment,
				'args'    => $args,
				'depth'   => $depth,
			)
		);
	}
}

/**
 * Checks if Load optimized Scripts Enabled.
 *
 * @since 4.0.6
 */
function wptravel_can_load_bundled_scripts() {
	$settings = get_option( 'wp_travel_settings', array() );
	return isset( $settings['wt_load_optimized_script'] ) && 'yes' === $settings['wt_load_optimized_script'];
}

/**
 * Function to bypass cart page while clicking book now button.
 *
 * @since 4.3.2
 */
function wptravel_enable_cart_page( $enabled, $settings ) {
	return false;
}

add_filter( 'wp_travel_filter_is_enabled_cart_page', 'wptravel_enable_cart_page', 10, 2 );

add_filter( 'wp_travel_settings_options', 'wptravel_core_fontawesome_icons', 10, 2 );
/**
 * Function for making options of fa icon list on v4 settings.
 *
 * @param array $settings_options Options.
 * @param array $settings settings wp-travel.
 */
function wptravel_core_fontawesome_icons( $settings_options, $settings ) {

	$fa_icons      = wptravel_fa_icons();
	$fa_icons_list = array();
	$i             = 0;
	if ( is_array( $fa_icons ) && count( $fa_icons ) > 0 ) {
		foreach ( $fa_icons as $key => $fa_icon ) {
			$fa_icons_list[ $i ]['label'] = $fa_icon;
			$fa_icons_list[ $i ]['value'] = $key;
			$i++;
		}
	}

	$settings_options['wp_travel_fontawesome_icons'] = $fa_icons_list;

	return $settings_options;
}
