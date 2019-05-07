<?php
/**
 * Helper Functions.
 *
 * @package wp-travel/inc
 */

require sprintf( '%s/inc/helpers/helpers-price.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/helpers/helpers-stat.php', WP_TRAVEL_ABSPATH );
/**
 * Return all Gallery ID of specific post.
 *
 * @param  int $post_id ID f the post.
 * @return array Return gallery ids.
 */
function wp_travel_get_gallery_ids( $post_id ) {
	$gallery_ids = get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	if ( false === $gallery_ids || empty( $gallery_ids ) ) {
		return false;
	}
	return $gallery_ids;
}

// @since 1.9.0
function wp_travel_settings_default_fields() {

	// Booking Admin Defaults.
	$booking_admin_email_defaults = array(
		'admin_subject'      => __( 'New Booking', 'wp-travel' ),
		'admin_title'        => __( 'New Booking', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wp_travel_booking_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	// Booking client Defaults.
	$booking_client_email_defaults = array(
		'client_subject'      => __( 'Booking Recieved', 'wp-travel' ),
		'client_title'        => __( 'Booking Recieved', 'wp-travel' ),
		'client_header_color' => '',
		'email_content'       => wp_travel_booking_client_default_email_content(),
		'from_email'          => get_option( 'admin_email' ),
	);

	// Payment Admin Defaults.
	$payment_admin_email_defaults = array(
		'admin_subject'      => __( 'New Booking', 'wp-travel' ),
		'admin_title'        => __( 'New Booking', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wp_travel_payment_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	// Payment client Defaults.
	$payment_client_email_defaults = array(
		'client_subject'      => __( 'Payment Recieved', 'wp-travel' ),
		'client_title'        => __( 'Payment Recieved', 'wp-travel' ),
		'client_header_color' => '',
		'email_content'       => wp_travel_payment_client_default_email_content(),
		'from_email'          => get_option( 'admin_email' ),
	);

	// emquiry Admin Defaults.
	$enquiry_admin_email_defaults = array(
		'admin_subject'      => __( 'New Enquiry', 'wp-travel' ),
		'admin_title'        => __( 'New Enquiry', 'wp-travel' ),
		'admin_header_color' => '',
		'email_content'      => wp_travel_enquiries_admin_default_email_content(),
		'from_email'         => get_option( 'admin_email' ),
	);

	$settings_fields = array(
		// General Settings Fields.
		'currency'                                => 'USD',
		'wp_travel_map'                           => 'google-map',
		'google_map_api_key'                      => '',
		'google_map_zoom_level'                   => 15,

		'cart_page_id'                            => wp_travel_get_page_id( 'wp-travel-cart' ),
		'checkout_page_id'                        => wp_travel_get_page_id( 'wp-travel-checkout' ),
		'dashboard_page_id'                       => wp_travel_get_page_id( 'wp-travel-dashboard' ),

		// Trip Settings Fields.
		'hide_related_itinerary'                  => 'no',
		'enable_multiple_travellers'              => 'no',
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
		'generate_username_from_email'            => 'no',
		'generate_user_password'                  => 'no',

		// Tabs Settings Fields.
		'global_tab_settings'                     => wp_travel_get_default_trip_tabs( true ), // @since 1.1.1 Global tabs settings.

		// Payment Settings Fields.
		'partial_payment'                         => 'no',
		'minimum_partial_payout'                  => WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT,
		'payment_option_paypal'                   => 'no',
		'paypal_email'                            => '',
		'trip_tax_enable'                         => 'no',
		'trip_tax_price_inclusive'                => 'yes',
		'trip_tax_percentage'                     => 13,

		'sorted_gateways'                         => wp_travel_payment_gateway_lists(),

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
	);
	return apply_filters( 'wp_travel_settings_fields', $settings_fields ); // flter @since 1.9.0.
}

/** Return All Settings of WP travel. */
function wp_travel_get_settings() {
	$default_settings = wp_travel_settings_default_fields();
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
function wp_travel_get_trip_code( $post_id = null ) {
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
function wp_travel_get_dropdown_currency_list( $args = array() ) {

	$currency_list = wp_travel_get_currency_list();

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

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $currency . ' (' . wp_travel_get_currency_symbol( $key ) . ')</option>';
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
function wp_travel_get_dropdown_list( $args = array() ) {

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

/**
 * List all avalable and selceted maps data.
 *
 * @since 1.7.6
 * Return Array list
 */
function wp_travel_get_maps() {

	$map_key  = 'google-map';
	$settings = wp_travel_get_settings();

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
 * Return Tree Form of post Object.
 *
 * @param Object $elements Post Object.
 * @param Int    $parent_id Parent ID of post.
 * @return Object Return Tree Form of post Object.
 */
function wp_travel_build_post_tree( array &$elements, $parent_id = 0 ) {
	$branch = array();

	foreach ( $elements as $element ) {
		if ( $element->post_parent == $parent_id ) {
			$children = wp_travel_build_post_tree( $elements, $element->ID );
			if ( $children ) {
				$element->children = $children;
			}
			$branch[ $element->ID ] = $element;
			unset( $elements[ $element->ID ] );
		}
	}
	return $branch;
}

/**
 * [wp_travel_get_post_hierarchy_dropdown description]
 *
 * @param  [type]  $list_serialized [description].
 * @param  [type]  $selected        [description].
 * @param  integer $nesting_level   [description].
 * @param  boolean $echo            [description].
 * @return [type]                   [description]
 */
function wp_travel_get_post_hierarchy_dropdown( $list_serialized, $selected, $nesting_level = 0, $echo = true ) {
	$contents = '';
	if ( $list_serialized ) :

		$space = '';
		for ( $i = 1; $i <= $nesting_level; $i ++ ) {
			$space .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ( $list_serialized as $content ) {

			$contents .= '<option value="' . $content->ID . '" ' . selected( $selected, $content->ID, false ) . ' >' . $space . $content->post_title . '</option>';
			if ( isset( $content->children ) ) {
				$contents .= wp_travel_get_post_hierarchy_dropdown( $content->children, $selected, ( $nesting_level + 1 ), false );
			}
		}
	endif;
	if ( ! $echo ) {
		return $contents;
	}
	echo $contents;
	return false;
}

/**
 * Get Map Data.
 */
function get_wp_travel_map_data() {
	global $post;
	if ( ! $post ) {
		return;
	}
	$lat = ( '' != get_post_meta( $post->ID, 'wp_travel_lat', true ) ) ? get_post_meta( $post->ID, 'wp_travel_lat', true ) : '';
	$lng = ( '' != get_post_meta( $post->ID, 'wp_travel_lng', true ) ) ? get_post_meta( $post->ID, 'wp_travel_lng', true ) : '';
	$loc = ( '' != get_post_meta( $post->ID, 'wp_travel_location', true ) ) ? get_post_meta( $post->ID, 'wp_travel_location', true ) : '';

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
function wp_travel_get_related_post( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
	/**
	 * Load template for related trips.
	 */
	echo wp_travel_get_template_html( 'content-related-posts.php', $post_id );

}

/**
 * Get post thumbnail.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image tag.
 */
function wp_travel_get_post_thumbnail( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	$size      = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
	$thumbnail = get_the_post_thumbnail( $post_id, $size );

	if ( ! $thumbnail ) {
		$placeholder_image_url = wp_travel_get_post_placeholder_image_url();
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
function wp_travel_get_post_thumbnail_url( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( ! $post_id ) {
		return;
	}
	$size          = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
	$thumbnail_url = get_the_post_thumbnail_url( $post_id, $size );

	if ( ! $thumbnail_url ) {
		$thumbnail_url = wp_travel_get_post_placeholder_image_url();
	}
	return $thumbnail_url;
}

/**
 * Post palceholder image URL.
 *
 * @return string Placeholder image URL.
 */
function wp_travel_get_post_placeholder_image_url() {
	$thumbnail_url = plugins_url( '/wp-travel/assets/images/wp-travel-placeholder.png' );
	return $thumbnail_url;
}

/**
 * Allowed tags.
 *
 * @param array $tags filter tags.
 * @return array allowed tags.
 */
function wp_travel_allowed_html( $tags = array() ) {

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
function wp_travel_get_itineraries_array() {
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
function wp_travel_get_tour_extras_array() {
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
function wp_reavel_get_itinereries_prices_array() {

	$itineraries = wp_travel_get_itineraries_array();

	$prices = array();

	if ( $itineraries ) {

		foreach ( $itineraries as $key => $itinerary ) {

			$prices[] = wp_travel_get_actual_trip_price( $key );

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
function wp_travel_featured_itineraries( $no_of_post_to_show = 3 ) {
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
function wp_travel_search_form() {
	ob_start(); ?>
	<div class="wp-travel-search">
		<form method="get" name="wp-travel_search" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
			<input type="hidden" name="post_type" value="<?php echo esc_attr( WP_TRAVEL_POST_TYPE ); ?>" />
			<p>
				<label><?php esc_html_e( 'Search:', 'wp-travel' ); ?></label>
				<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
				<input type="text" name="s" id="s" value="<?php echo ( isset( $_GET['s'] ) ) ? esc_textarea( $_GET['s'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
			</p>
			<p>
				<label><?php esc_html_e( 'Trip Type:', 'wp-travel' ); ?></label>
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
					'selected'        => ( isset( $_GET[ $taxonomy ] ) ) ? esc_textarea( $_GET[ $taxonomy ] ) : 0,
					'value_field'     => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Location:', 'wp-travel' ); ?></label>
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
					'selected'        => ( isset( $_GET[ $taxonomy ] ) ) ? esc_textarea( $_GET[ $taxonomy ] ) : 0,
					'value_field'     => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>

			<p class="wp-travel-search"><input type="submit" name="wp-travel_search" id="wp-travel-search" class="button button-primary" value="<?php esc_html_e( 'Search', 'wp-travel' ); ?>"  /></p>
		</form>
	</div>
	<?php
	$content = apply_filters( 'wp_travel_search_form', ob_get_clean() );
	echo $content;
}

/**
 * This will optput Trip duration HTML
 *
 * @param int $post_id Post ID.
 * @return void
 */
function wp_travel_get_trip_duration( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

		$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
		$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
		$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

		$show_end_date = wp_travel_booking_show_end_date();

	if ( 'yes' === $fixed_departure ) :
		$start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
		$end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );
		?>

		<div class="wp-travel-trip-time trip-fixed-departure">
			<i class="wt-icon-regular wt-icon-calendar-alt"></i>
			<span class="wp-travel-trip-duration">
			<?php
			if ( $start_date || $end_date ) :
				$date_format = get_option( 'date_format' );
				if ( ! $date_format ) :
					$date_format = 'jS M, Y';
					endif;
				if ( '' !== $end_date && $show_end_date ) {
					printf( '%s - %s', date_i18n( $date_format, strtotime( $start_date ) ), date_i18n( $date_format, strtotime( $end_date ) ) );
				} else {
					printf( '%s', date_i18n( $date_format, strtotime( $start_date ) ) );
				}

				else :
					esc_html_e( 'N/A', 'wp-travel' );
				endif;
				?>
			</span>
		</div>

	<?php else : ?>
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
}

/**
 * Get Payment Status List.
 *
 * @since 1.0.6
 */
function wp_travel_get_payment_status() {
	$status = array(
		'pending'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Pending', 'wp-travel' ),
		),
		'paid'     => array(
			'color' => '#008600',
			'text'  => __( 'Paid', 'wp-travel' ),
		),
		'waiting_voucher'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Waiting for voucher', 'wp-travel' ),
		),
		'voucher_submited'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Voucher submited', 'wp-travel' ),
		),
		'canceled' => array(
			'color' => '#FE450E',
			'text'  => __( 'Canceled', 'wp-travel' ),
		),
		'N/A'      => array(
			'color' => '#892E2C',
			'text'  => __( 'N/A', 'wp-travel' ),
		),
	);

	return apply_filters( 'wp_travel_payment_status_list', $status );
}

/**
 * Get Payment Mode List.
 *
 * @since 1.0.5
 */
function wp_travel_get_payment_mode() {
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
function wp_travel_get_image_sizes() {
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
 * Determine if post type is itinerary
 */
function is_itinerary() {
	return get_post_type() === WP_TRAVEL_POST_TYPE;
}

/**
 * Get permalink settings for WP Travel independent of the user locale.
 *
 * @since  1.1.0
 * @return array
 */
function wp_travel_get_permalink_structure() {

	$permalinks = wp_parse_args(
		(array) get_option( 'wp_travel_permalinks', array() ),
		array(
			'wp_travel_trip_base'        => '',
			'wp_travel_trip_type_base'   => '',
			'wp_travel_destination_base' => '',
			'wp_travel_activity_base'    => '',
		)
	);

	// $db_version = get_option( 'wp_travel_version' );
	// $current_version = WP_TRAVEL_VERSION;
	// // Fallback slug
	// if ( ( ! $db_version ) && '' === $permalinks['wp_travel_trip_base'] ) {
	// $permalinks['wp_travel_trip_base'] = 'itinerary';
	// }
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
 * @since 1.1.2
 *
 * @return void
 */
function wp_travel_get_frontend_tabs( $show_in_menu_query = false ) {

	global $post;
	$settings                  = wp_travel_get_settings();
	$wp_travel_use_global_tabs = get_post_meta( $post->ID, 'wp_travel_use_global_tabs', true );

	if ( 'yes' === $wp_travel_use_global_tabs ) {
		$custom_tab_enabled = apply_filters( 'wp_travel_is_custom_tabs_support_enabled', false );
		$wp_travel_tabs     = wp_travel_get_global_tabs( $settings, $custom_tab_enabled );
	} else {
		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );
		$wp_travel_tabs               = wp_travel_get_admin_trip_tabs( $post->ID, $enable_custom_itinerary_tabs );
	}

	// Adding Content to the tabs.
	$return_tabs              = array();
	$wp_travel_itinerary_tabs = wp_travel_get_default_trip_tabs( $show_in_menu_query );
	if ( is_array( $wp_travel_tabs ) && count( $wp_travel_tabs ) > 0 ) {
		foreach ( $wp_travel_tabs as $key => $tab ) {

			$tab_content = isset( $wp_travel_itinerary_tabs[ $key ]['content'] ) ? $wp_travel_itinerary_tabs[ $key ]['content'] : '';

			// Adding custom tab content.
			if ( isset( $tab['custom'] ) && 'yes' === $tab['custom'] ) {
				$tab_content = isset( $tab['content'] ) ? $tab['content'] : '';
			}

			$show_in_menu = isset( $tab['show_in_menu'] ) ? $tab['show_in_menu'] : 'yes';
			$show_in_menu = apply_filters( 'wp_travel_frontend_tab_show_in_menu', $show_in_menu, $post->ID, $key ); // @since 1.9.3.

			$new_tabs[ $key ]['label']        = ( $tab['label'] ) ? $tab['label'] : $wp_travel_itinerary_tabs[ $key ]['label'];
			$new_tabs[ $key ]['label_class']  = isset( $wp_travel_itinerary_tabs[ $key ]['label_class'] ) ? $wp_travel_itinerary_tabs[ $key ]['label_class'] : '';
			$new_tabs[ $key ]['content']      = $tab_content;
			$new_tabs[ $key ]['use_global']   = isset( $tab['use_global'] ) ? $tab['use_global'] : 'yes';
			$new_tabs[ $key ]['show_in_menu'] = $show_in_menu;

			$new_tabs[ $key ]['custom'] = isset( $tab['custom'] ) ? $tab['custom'] : 'no';

			$new_tabs[ $key ]['global'] = isset( $tab['global'] ) ? $tab['global'] : 'no';
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
 * @return array
 */
function wp_travel_get_default_trip_tabs( $is_show_in_menu_query = false ) {
	$trip_content = '';
	$trip_outline = '';
	$trip_include = '';
	$trip_exclude = '';
	$gallery_ids  = '';
	$faqs         = array();

	if ( ! is_admin() && ! $is_show_in_menu_query ) { // fixes the content filter in page builder. Multiple content issue.
		global $wp_travel_itinerary;
		if ( $wp_travel_itinerary ) {
			$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
			$trip_content             = $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
			$trip_outline             = $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
			$trip_include             = $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
			$trip_exclude             = $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
			$gallery_ids              = $wp_travel_itinerary->get_gallery_ids();
			global $post;
			if ( $post ) {
				$post_id = $post->ID;
				$faqs    = wp_travel_get_faqs( $post_id );
				$faqs    = $faqs ? $faqs : $no_details_found_message;
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
			'content'      => $gallery_ids,
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
function wp_travel_get_global_tabs( $settings, $custom_tab_enabled = false ) {
	if ( ! $settings ) {
		$settings = wp_travel_get_settings();
	}

	// Default tab.
	$global_tabs = wp_travel_get_default_trip_tabs();

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
 * @since 1.9.3
 * @return array
 */
function wp_travel_get_admin_trip_tabs( $post_id, $custom_tab_enabled = false ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	// Default tab.
	$trip_tabs = wp_travel_get_default_trip_tabs();

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
function wp_travel_get_faqs( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

	$faq = array();

	$questions = get_post_meta( $post_id, 'wp_travel_faq_question', true );
	$questions = apply_filters( 'wp_travel_itinerary_faq_questions', $questions );

	if ( is_array( $questions ) && count( $questions ) > 0 ) :
		$answers = get_post_meta( $post_id, 'wp_travel_faq_answer', true );
		$answers = apply_filters( 'wp_travel_itinerary_faq_answers', $answers );
		foreach ( $questions as $key => $question ) :
			$answer = isset( $answers[ $key ] ) ? $answers[ $key ] : '';
			$faq[]  = array(
				'question' => $question,
				'answer'   => $answer,
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
function wp_travel_get_page_id( $page ) {

	$settings = get_option( 'wp_travel_settings' ); // Not used wp_travel_get_settings due to infinite loop.
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
function wp_travel_get_page_permalink( $page ) {
	$page_id   = wp_travel_get_page_id( $page );
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
function wp_travel_get_cart_url() {
	return apply_filters( 'wp_travel_get_cart_url', wp_travel_get_page_permalink( 'cart' ) );
}

/**
 * Gets the URL of checkout page.
 *
 * @since 1.5.7
 *
 * @return string Url to checkout page
 */
function wp_travel_get_checkout_url() {
	return apply_filters( 'wp_travel_get_checkout_url', wp_travel_get_page_permalink( 'wp-travel-checkout' ) );
}

/**
 * Check whether page is checkout page or not.
 *
 * @return Boolean
 */
function wp_travel_is_checkout_page() {

	if ( is_admin() ) {
		return false;
	}
	global $post;
	$page_id  = get_the_ID();
	$settings = wp_travel_get_settings();
	if ( isset( $settings['checkout_page_id'] ) && (int) $settings['checkout_page_id'] === $page_id ) {
		return true;
	}
	return false;
}

/**
 * Check whether page is cart page or not.
 *
 * @return Boolean
 */
function wp_travel_is_cart_page() {
	if ( is_admin() ) {
		return false;
	}
	$page_id  = get_the_ID();
	$settings = wp_travel_get_settings();
	if ( isset( $settings['cart_page_id'] ) && (int) $settings['cart_page_id'] === $page_id ) {
		return true;
	}
	return false;
}

/**
 * Check whether page is dashboard page or not.
 *
 * @return Boolean
 */
function wp_travel_is_dashboard_page() {
	if ( is_admin() ) {
		return false;
	}
	$page_id  = get_the_ID();
	$settings = wp_travel_get_settings();
	if ( isset( $settings['dashboard_page_id'] ) && (int) $settings['dashboard_page_id'] === $page_id ) {
		return true;
	}
	return false;
}

if ( ! function_exists( 'wp_travel_is_account_page' ) ) {

	/**
	 * wp_travel_Is_account_page - Returns true when viewing an account page.
	 *
	 * @return bool
	 */
	function wp_travel_is_account_page() {
		return is_page( wp_travel_get_page_id( 'wp-travel-dashboard' ) ) || wp_travel_post_content_has_shortcode( 'wp_travel_user_account' ) || apply_filters( 'wp_travel_is_account_page', false );
	}
}

function wp_travel_is_itinerary( $post_id = null ) {
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
function wp_travel_can_load_payment_scripts() {
	return ( wp_travel_is_dashboard_page() || wp_travel_is_checkout_page() ) && wp_travel_is_payment_enabled();
}

// WP Travel Pricing Varition options.

/**
 * Get default pricing variation options.
 *
 * @return array $variation_options Variation Options.
 */
function wp_travel_get_pricing_variation_options() {

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
 * Get single pricing variation by key.
 *
 * @return array $pricing Pricing variations data.
 */
function wp_travel_get_pricing_variation( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {

		return false;

	}

	// Get Pricing variations.
	$pricing_variations = get_post_meta( $post_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

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
function wp_travel_get_raw_referer() {
	if ( function_exists( 'wp_get_raw_referer' ) ) {
		return wp_get_raw_referer();
	}

	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		return wp_unslash( $_REQUEST['_wp_http_referer'] );
	} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		return wp_unslash( $_SERVER['HTTP_REFERER'] );
	}

	return false;
}

/**
 * Get pricing variation start dates.
 *
 * @return array $available_dates Variation Options.
 */
function wp_travel_get_pricing_variation_start_dates( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {

		return false;

	}

	// Get Dates.
	$trip_dates = wp_travel_get_pricing_variation_dates( $post_id, $pricing_key );

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
function wp_travel_post_content_has_shortcode( $tag = '' ) {
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
function wp_travel_clean_vars( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wp_travel_clean_vars', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Add notices for WP Errors.
 *
 * @param WP_Error $errors Errors.
 */
function wp_travel_add_wp_error_notices( $errors ) {
	if ( is_wp_error( $errors ) && $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			WP_Travel()->notices->add( $error, 'error' );
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
function wp_travel_get_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = WP_Travel()->notices->get( $notice_type, false );

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
function wp_travel_user_new_account_created( $customer_id, $new_customer_data, $password_generated ) {

	// Send email notification.
	$email_content = wp_travel_get_template_html(
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
		$user_recipient  = $user_user_email;
		$user_subject    = __( 'New Account Created', 'wp-travel' );

		if ( ! wp_mail( $user_recipient, $user_subject, $email_content, $headers ) ) {

			return false;

		}
	}
}

add_action( 'wp_travel_created_customer', 'wp_travel_user_new_account_created', 20, 3 );

/**
 * Filters the from name in e-mails
 */
function wp_travel_emails_from_name_filter( $from_name ) {

	return $from_name = apply_filters( 'wp_travel_email_from_name', get_bloginfo( 'name' ) );

}

add_filter( 'wp_mail_from_name', 'wp_travel_emails_from_name_filter', 30 );


if ( ! function_exists( 'wp_travel_format_date' ) ) :

	/**
	 * Format Date.
	 */
	function wp_travel_format_date( $date, $localize = true, $base_date_format = '' ) {
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
	function wp_travel_format_ymd_date( $date, $date_format = '' ) {
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

if ( ! function_exists( 'wp_travel_get_trip_available_dates' ) ) {

	/**
	 * Get Available Dates for specific trip.
	 *
	 * @param Number $trip_id Current trip id.
	 * @since 1.8.3
	 */
	function wp_travel_get_trip_available_dates( $trip_id, $price_key = '' ) {

		if ( ! $trip_id ) {
			return;
		}

		$multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = array();

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
		if ( 'yes' === $fixed_departure ) {
			if ( 'yes' === $multiple_fixed_departue ) {
				$available_dates = wp_travel_get_pricing_variation_start_dates( $trip_id, $price_key );
			} else {
				$date            = get_post_meta( $trip_id, 'wp_travel_start_date', true );
				$available_dates = array( $date );
			}
		}
		return $available_dates;
	}
}

if ( ! function_exists( 'wp_travel_get_total_booked_pax' ) ) :
	/**
	 * Get Total booked Count.
	 */
	function wp_travel_get_total_booked_pax( $trip_id, $including_cart = true ) {

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
function wp_travel_get_date_diff( $start_date, $end_date ) {

	$date11       = strtotime( $start_date );
	$date22       = strtotime( $end_date );
	$diff         = $date22 - $date11;
	$diff_in_days = floor( $diff / ( 60 * 60 * 24 ) ) + 1;

	return sprintf( __( '%s days', 'wp-travel' ), $diff_in_days );

}

/**
 * Print success and error notices set by WP Travel Plugin.
 */
function wp_travel_print_notices() {
	// Print Errors / Notices.
	WP_Travel()->notices->print_notices( 'error', true );
	WP_Travel()->notices->print_notices( 'success', true );
}

/**
 * Convert Date Format String form PHP to JS.
 *
 * @param string $date_format Date Fromat.
 *
 * @since   1.6.7
 * @return  array
 */
function wp_travel_date_format_php_to_js( $date_format = null ) {
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
function wp_travel_moment_date_format( $date_format = null ) {
	$js_date_format = 'YYYY-MM-DD';
	return apply_filters( 'wp_travel_moment_date_format', $js_date_format );
}

/**
 * Check current date formant is Y-m-d or not.
 *
 * @param string $date Date.
 *
 * @since   1.8.3
 * @return  array
 */
function wp_travel_is_ymd_date( $date ) {
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
function wp_travel_payment_data( $booking_id ) {
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
function wp_travel_booking_show_end_date() {
	return apply_filters( 'wp_travel_booking_show_end_date', true );
}

/**
 * Return Pricing name as per trip id and pricing key.
 *
 * @param Number $trip_id   Trip ID.
 * @param String $price_key Name of pricing.
 *
 * @since 1.8.2
 *
 * @return String
 */
function wp_travel_get_trip_pricing_name( $trip_id, $price_key = '' ) {

	if ( ! $trip_id ) {
		return;
	}

	$pricing_name = get_the_title( $trip_id );

	if ( ! empty( $price_key ) ) :
		$pricing_options = wp_travel_get_pricing_variation( $trip_id, $price_key );
		$pricing_option  = ( is_array( $pricing_options ) && ! empty( $pricing_options ) ) ? reset( $pricing_options ) : false;

		if ( $pricing_option ) {
			$pricing_label = isset( $pricing_option['pricing_name'] ) ? $pricing_option['pricing_name'] : false;
			if ( $pricing_label ) {
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
function wp_travel_sort_array_by_priority( $array, $priority_key = 'priority' ) {
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
function wp_travel_sort_form_fields( $fields ) {
	return wp_travel_sort_array_by_priority( $fields );
}

/**
 * Get Inquiry Link.
 */
function wp_travel_get_inquiry_link() {

	ob_start();

	?>

		<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
			<span class="wp-travel-booking-enquiry">
				<span class="dashicons dashicons-editor-help"></span>
				<span>
					<?php esc_html_e( 'Trip Enquiry', 'wp-travel' ); ?>
				</span>
			</span>
		</a>

	<?php

	$data = ob_get_clean();

	return $data;

}


function wp_travel_get_search_filter_form( $args ) {

	if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	}
	$form_field    = new WP_Travel_FW_Field();
	$search_fields = wp_travel_search_filter_widget_form_fields();
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
				$view_mode = wp_travel_get_archive_view_mode();
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

function wp_travel_get_pricing_option_listing_type( $settings = null ) {
	if ( ! $settings ) {
		$settings = wp_travel_get_settings();
	}
	$list_type = isset( $settings['trip_pricing_options_layout'] ) ? $settings['trip_pricing_options_layout'] : 'by-pricing-option';
	// $list_type = 'by-date';
	return apply_filters( 'wp_travel_pricing_option_listing_type', $list_type );
}

function wp_travel_view_booking_details_table( $booking_id, $hide_payment_column = false ) {
	if ( ! $booking_id ) {
		return;
	}

	$details = wp_travel_booking_data( $booking_id );

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

	$status_list  = wp_travel_get_payment_status();
	$status_color = isset( $details['payment_status'] ) && isset( $status_list[ $details['payment_status'] ]['color'] ) ? $status_list[ $details['payment_status'] ]['color'] : '';
	
	$form              = new WP_Travel_FW_Form();
	$form_options = array(
		'id'            => 'wp-travel-submit-slip',
		'wrapper_class' => 'wp-travel-submit-slip-form-wrapper',
		'submit_button' => array(
			'name'  => 'wp_travel_submit_slip',
			'id'    => 'wp-travel-submit-slip',
			'value' => __( 'Submit', 'wp-travel' ),
		),
		'multipart' => true,
		'nonce'         => array(
			'action' => 'wp_travel_security_action',
			'field'  => 'wp_travel_security',
		),
	);
	$bank_deposit_fields = wp_travel_get_bank_deposit_form_fields();
	if ( is_array( $details ) && count( $details ) > 0 ) {
		?>
		<div class="table-wrp">
			<!-- Started Here -->
			<div class="my-order-single-content-wrap">
				<?php if ( wp_travel_is_payment_enabled() && ! $hide_payment_column ) : ?>
					<div class="my-order-single-sidebar">
						<h3 class="my-order-single-title"><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></h3>
						<div class="my-order-status my-order-status-<?php echo esc_html( $details['payment_status'] ); ?>" style="background:<?php echo esc_attr( $status_color ); ?>" ><?php echo esc_html( ucfirst( $details['payment_status'] ) ); ?></div>
						<?php do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id ); ?>

						<?php
							$bank_deposit_status = array( 'waiting_voucher' );
							if ( in_array( $details['payment_status'], $bank_deposit_status ) ) { ?>
									<div class="wp-travel-bank-deposit-wrap ">
										<a href="#wp-travel-bank-deposit-content" class="wp-travel-upload-slip">Upload Slip</a>
										<div id="wp-travel-bank-deposit-content" >
											<h3><?php esc_html_e( 'Submit Bank Payment Receipt' ); ?></h3>
											<?php $form->init( $form_options )->fields( $bank_deposit_fields )->template(); ?>
										</div>
									</div>
									
									<script type="text/javascript">
										jQuery(document).ready(function($){
											// popup
											$('.wp-travel-upload-slip').magnificPopup({
												type: 'inline',
											});

											// form submit
											$('.wp-travel-submit-slip').submit(function (event) {
												// event.preventDefault();

												// // Validate all input fields.
												// var parent = '#' + $(this).attr('id');
												// var data = $(this).serializeArray();
												// console.log( data );
												// var cart_fields = {};
												
												// cart_fields['action'] = 'wt_add_to_cart';
												// // cart_fields['nonce'] =  'wt_add_to_cart_nonce';

												// $.ajax({
												// 	type: "POST",
												// 	url: wp_travel.ajaxUrl,
												// 	data: cart_fields,
												// 	beforeSend: function () { },
												// 	success: function (data) {
												// 		// location.href = wp_travel.cartUrl;
												// 	}
												// });

											});
										});
									</script>
									<style>
										#wp-travel-bank-deposit-content{
											width:600px;
											height:400px;
											display:none;
											background:rgba( 255, 255, 255, 0.5 );
											margin-left: calc( 50% - 300px );
											padding:20px;
											box-sizing:border-box;
										}
										.mfp-content #wp-travel-bank-deposit-content{
											display:block;
										}
									</style>
								<?php
							}
						?>
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
											$price_key    = $order_detail['price_key'];
											$pricing_name = wp_travel_get_trip_pricing_name( $trip_id, $price_key );

											if ( '' !== $order_detail['arrival_date'] ) {
												$travel_date .= wp_travel_format_date( $order_detail['arrival_date'] ) . ', ';
											} else {
												$travel_date .= __( 'N/A', 'wp-travel' ) . ', ';
											}
											?>
											<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>,
											<?php
										endforeach;
									else :
										$pricing_name = wp_travel_get_trip_pricing_name( $trip_id );
										?>
										<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>
									<?php endif; ?>
								</span>
							</div>
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Travel Date :', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo $travel_date; ?></span>
							</div>

							<!-- Hook to add booking time details at booking-->
							<?php do_action( 'wp_travel_booked_times_details', $order_details ); ?>
						</div>
						<div class="col-md-6">
							<?php
							$checkout_fields  = wp_travel_get_checkout_form_fields();
							$billing_fields   = isset( $checkout_fields['billing_fields'] ) ? $checkout_fields['billing_fields'] : array();
							$billing_fields   = wp_travel_sort_form_fields( $billing_fields );
							if ( ! empty( $billing_fields ) ) {
								foreach( $billing_fields as $field ) {
									if ( 'heading' === $field['type'] ) {
										printf( '<h3 class="my-order-single-title">%s</h3> ', $field['label'] );
									} else if ( in_array( $field['type'], array( 'hidden' ) ) ) {
										// Do nothing
									} else {
										echo '<div class="my-order-single-field clearfix">';
										printf( '<span class="my-order-head">%s:</span>', $field['label'] );
										printf( '<span class="my-order-tail">%s</span>', get_post_meta( $booking_id, $field['name'], true ) );
										echo '</div>';
									}
								}
							}
							?>
						</div>
					</div>
					<?php

					// Travelers info.
					$fname   = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
					$lname   = get_post_meta( $booking_id, 'wp_travel_lname_traveller', true );
					$country = get_post_meta( $booking_id, 'wp_travel_country_traveller', true );
					$phone   = get_post_meta( $booking_id, 'wp_travel_phone_traveller', true );
					$email   = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );
					$dob     = get_post_meta( $booking_id, 'wp_travel_date_of_birth_traveller', true );
					$gender  = get_post_meta( $booking_id, 'wp_travel_gender_traveller', true );
					$traveller_infos = get_post_meta( $booking_id );

					if ( is_array( $fname ) && count( $fname ) > 0 ) :
						foreach ( $fname as $booking_trip_id => $first_names ) :
							if ( is_array( $first_names ) && count( $first_names ) > 0 ) :
								?>
								<div class="my-order-single-traveller-info">
									<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Travelers info [ %s ]', get_the_title( $booking_trip_id ) ), 'wp-travel' ); ?></h3>
									<div class="row">
										<?php foreach ( $first_names as $key => $first_name ) : ?>
											<div class="col-md-6">
												<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Traveler %s :', $key + 1 ), 'wp-travel' ); ?></h3>
												<?php
												$traveller_fields = isset( $checkout_fields['traveller_fields'] ) ? $checkout_fields['traveller_fields'] : array();
												$traveller_fields   = wp_travel_sort_form_fields( $traveller_fields );
												if ( ! empty( $traveller_fields ) ) {
													foreach( $traveller_fields as $field ) {
														if ( 'heading' === $field['type'] ) {
															// Do nothing.
														} else if ( in_array( $field['type'], array( 'hidden' ) ) ) {
															// Do nothing.
														} else {
															$value = maybe_unserialize(  $traveller_infos[ $field['name'] ][0] );
															$value = is_array( $value ) ? array_values( $value ) : $value;
															$value = is_array( $value ) ? array_shift( $value ) : $value;
															$value = is_array( $value ) ? $value[$key] : $value;
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
									$pax        = $order_detail['pax'];
									$trip_price = $order_detail['trip_price'];
									$total      = wp_travel_get_formated_price( $trip_price * $pax );
									?>
									<div class="my-order-price-breakdown-base-price-wrap">
										<div class="my-order-price-breakdown-base-price">
											<span class="my-order-head"><?php echo esc_html( get_the_title( $order_detail['trip_id'] ) ); ?></span>
											<span class="my-order-tail">
												<span class="my-order-price-detail">(<?php echo sprintf( '%s x %s%s', $pax, wp_travel_get_currency_symbol(), $trip_price ); ?>) </span>
												<span class="my-order-price"><?php echo wp_travel_get_currency_symbol() . esc_html( $total ); ?></span>
											</span>
										</div>
									</div>
									<?php
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

												$price = wp_travel_get_formated_price( $price );
												$total = wp_travel_get_formated_price( $price * $qty );
												?>
												<div class="my-order-price-breakdown-additional-service-item clearfix">
													<span class="my-order-head"><?php echo esc_html( get_the_title( $extra_id ) ); ?> (<?php echo sprintf( '%s x %s%s', $qty, wp_travel_get_currency_symbol(), $price ); ?> )</span>
													<span class="my-order-tail my-order-right"><?php echo esc_html( wp_travel_get_currency_symbol() . $total ); ?></span>
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
											<span class="my-order-price"><?php echo wp_travel_get_currency_symbol() . esc_html( $details['sub_total'] ); ?></span>
										</span>
									</div>
								</div>
								<?php
							}
							?>

							<div class="my-order-price-breakdown-summary clearfix">
								<div class="my-order-price-breakdown-sub-total">
									<span class="my-order-head"><?php esc_html_e( 'Sub Total Price', 'wp-travel' ); ?></span>
									<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['sub_total'] ); ?></span>
								</div>

								<?php if ( $details['discount'] ) : ?>
									<div class="my-order-price-breakdown-coupon-amount">
										<span class="my-order-head"><?php esc_html_e( 'Discount Price', 'wp-travel' ); ?></span>
										<span class="my-order-tail my-order-right">- <?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['discount'] ); ?></span>
									</div>
								<?php endif; ?>

								<div class="my-order-price-breakdown-tax-due">
									<span class="my-order-head"><?php esc_html_e( 'Tax', 'wp-travel' ); ?> </span>
									<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['tax'] ); ?></span>
								</div>
							</div>
						</div>
						<div class="my-order-single-total-price clearfix">
							<div class="my-order-single-field clearfix">
								<span class="my-order-head"><?php esc_html_e( 'Total', 'wp-travel' ); ?></span>
								<span class="my-order-tail"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['total'] ); ?></span>
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

function wp_travel_view_payment_details_table( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	$payment_data = wp_travel_payment_data( $booking_id );
	$status_list  = wp_travel_get_payment_status();
	if ( $payment_data && count( $payment_data ) > 0 ) {
		?>
		<h3><?php esc_html_e( 'Payment Details', 'wp-travel' ); ?></h3>
		<table class="my-order-payment-details">
			<tr>
				<th><?php esc_html_e( 'Date', 'wp-travel' ); ?></th>
				<th><?php esc_html_e( 'Payment ID', 'wp-travel' ); ?></th>
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
						<td><?php echo esc_html( $payment_args['payment_id'] ); ?></td>
						<td><?php echo esc_html( $payment_args['payment_method'] ); ?></td>
						<td>
							<?php
							if ( $payment_amount > 0 ) :
								echo esc_html( sprintf( ' %s %s ', wp_travel_get_currency_symbol(), sprintf( '%0.2f', $payment_amount ) ) );
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
function wp_travel_thankyou_page_url( $trip_id = null ) {
	$thankyou_page_id = $trip_id;
	$settings         = wp_travel_get_settings();
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
		$thankyou_page_id = isset( $settings['thank_you_page_id'] ) && ! empty( $settings['thank_you_page_id'] ) ? $settings['thank_you_page_id'] : wp_travel_get_page_id( 'booking-thank-you' );
	}
	$thankyou_page_url = 0 < $thankyou_page_id ? get_permalink( $thankyou_page_id ) : get_home_url();
	return apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url, $trip_id );
}

/**
 * Function to check current trip is available or not.
 */
function wp_travel_trip_availability( $trip_id, $price_key, $start_date, $sold_out ) {

	// For now only start date and sold out is used to determine availability. Need Enhancement in future.
	$availability = true;
	if ( strtotime( $start_date . ' 23:59:59' ) < time() || $sold_out ) {
		$availability = false;
	}
	return apply_filters( 'wp_travel_trip_availability', $availability, $trip_id, $price_key, $start_date );
}

/**
 * Privacy Policy Link.
 */
function wp_travel_privacy_link() {
	$settings = wp_travel_get_settings();
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
 * @since 2.0.0
 */
function wp_travel_get_strings() {
	$localized_strings = array(
		'confirm'    => __( 'Are you sure you want to remove?', 'wp-travel' ),
		'book_now'   => __( 'Book Now', 'wp-travel' ),
		'book_n_pay' => __( 'Book and Pay', 'wp-travel' ),
		'select'     => __( 'Select', 'wp-travel' ),
		'close'      => __( 'Close', 'wp-travel' ),
	);

	return apply_filters( 'wp_travel_strings', $localized_strings );
}

/**
 * Return WP Travel Bank deposit account details.
 *
 * @since 2.0.0
 */
function wp_travel_get_bank_deposit_account_details( $display_all_row = false ) {
	$settings = wp_travel_get_settings();

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
			$enable         = isset( $bank_deposits['enable'][ $i ] ) ? $bank_deposits['enable'][ $i ] : 'no';

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

function wp_travel_get_bank_deposit_account_table() {
	$account_data = wp_travel_get_bank_deposit_account_details();
	ob_start();
	if ( is_array( $account_data ) && count( $account_data ) > 0 ) {
		?>
		<table>
			<tr>
				<?php if ( isset( $account_data[0]['account_name'] ) ) : ?>
					<td><?php _e( 'Account Name' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['account_number'] ) ) : ?>
					<td><?php _e( 'Account Number' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['bank_name'] ) ) : ?>
					<td><?php _e( 'Bank Name' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['sort_code'] ) ) : ?>
					<td><?php _e( 'Sort Code' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['iban'] ) ) : ?>
					<td><?php _e( 'IBAN' ); ?></td>
				<?php endif; ?>
				<?php if ( isset( $account_data[0]['swift'] ) ) : ?>
					<td><?php _e( 'Swift' ); ?></td>
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
		esc_html_e( 'No detail found' );
	}
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
	
}
