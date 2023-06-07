<?php
/**
 * Helpers class to do Add Schema for WP Travel Pages.
 *
 * @package WP_Travel
 */

/**
 * Schema Helper.
 */
class WpTravel_Helpers_Schema {

	/**
	 * This variable include trip data which is used in schema.
	 *
	 * @var array $trip
	 */
	public static $trip;

	/**
	 * Initialize schema in WP Travel.
	 *
	 * @since 5.0.0
	 */
	public static function init() {
		// if ( wptravel_dev_mode() ) {
			add_action( 'wp_head', array( __CLASS__, 'run' ) );
		// }
	}

	/**
	 * Display all available schema for WP Travel.
	 *
	 * @since 5.0.0
	 */
	public static function run() {
		$use_schema = apply_filters( 'wptravel_use_schema', true );
		if ( ! $use_schema ) {
			return;
		}
		if ( WP_Travel::is_page( 'single' ) ) {
			global $post;
			$trip_id   = $post->ID;
			$trip_data = WpTravel_Helpers_Trips::get_trip( $trip_id );
			$trip      = array();
			if ( is_array( $trip_data ) && ! is_wp_error( $trip_data ) && isset( $trip_data['code'] ) && 'WP_TRAVEL_TRIP_INFO' === $trip_data['code'] ) {
				$trip = $trip_data['trip'];
			}
			self::$trip = $trip;
		}
		self::get_trip_schema();
		self::get_trip_rating_schema();
	}

	/**
	 * Generate schema as per $schema array.
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public static function get_trip_schema() {
		if ( ! self::$trip ) {
			return;
		}
		$trip    = self::$trip;
		$trip_id = $trip['id'];

		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Trip', // Fixed.
			'name'     => isset( $trip['title'] ) ? ucwords( $trip['title'] ) : '',
		);

		if ( isset( $trip['itineraries'] ) && is_array( $trip['itineraries'] ) && count( $trip['itineraries'] ) > 0 ) {
			$itineraries         = $trip['itineraries'];
			$schema['itinerary'] = array(
				'@type'         => 'ItemList',
				'numberOfItems' => count( $itineraries ),
			);

			$i = 1;
			foreach ( $itineraries as $itinerary ) {
				$schema['itinerary']['itemListElement'][] = array(
					'@type'    => 'ListItem', // Fixed.
					'position' => $i,
					'item'     => array(
						'@type'       => 'TouristAttraction', // Fixed.
						'name'        => sprintf( '%s - %s', $itinerary['label'], $itinerary['title'] ),
						'description' => $itinerary['desc'],
					),
				);
				$i++;
			}
		}
		/**
		 * Trip schema structure.
		 *
		 * @param array $schema Schema data for trip.
		 * @since 5.0.0
		 */
		$schema = apply_filters( 'wptravel_trip_schema', $schema, $trip_id, $trip );
		self::generate_schema( $schema );
	}

	/**
	 * Generate schema as per $schema array.
	 *
	 * @since 5.0.0
	 * @return string
	 */
	public static function get_trip_rating_schema() {
		if ( ! self::$trip ) {
			return;
		}
		$trip         = self::$trip;
		$trip_id      = $trip['id'];
		$review_count = wptravel_get_rating_count();

		if ( ! $review_count ) {
			return;
		}
		$brand_name = apply_filters( 'wp_travel_schema_brand', get_bloginfo(), $trip_id );
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Product', // Fixed.
			'name'        => isset( $trip['title'] ) ? ucwords( $trip['title'] ) : '',
			'sku'         => wptravel_get_trip_code( $trip_id ),
			'description' => wp_strip_all_tags( $trip['trip_overview'] ),
			'image'       => wptravel_get_post_thumbnail_url( $trip_id ),
			'brand'       => array(
				'@type' => 'Brand',
				'name'  => $brand_name,
			),
		);
		$get_rating = wptravel_get_average_rating( $trip_id );
		$get_rating = apply_filters( 'wp_travel_schema_ratting_value', $get_rating, $trip_id );
		$calculate_rating = $get_rating * 20;
		$calculate_rating = apply_filters( 'wp_travel_calculated_rating', $calculate_rating, $trip_id );
		$rount_rating = apply_filters( 'wp_travel_round_rating', round( $calculate_rating ), $trip_id );
		$final_rating = apply_filters( 'wp_travel_schema_final_rating', $rount_rating < 20 ? 20 : $rount_rating, $trip_id );
		// Rating Data.
		$schema['aggregateRating']        = array(
			'@type'       => 'AggregateRating', // Fixed.
			'bestRating'  => 100,
			'ratingValue' => $final_rating,
			'reviewCount' => $review_count,
		);
		/**
		 * added affers in schema
		 * @since 6.8.0
		 */
		$args                             = array( 'trip_id' => $trip_id );
		$args_regular                     = $args;
		$args_regular['is_regular_price'] = true;
		$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
		$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
		$enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
			array(
				'trip_id'                => $trip_id,
				'from_price_sale_enable' => true,
			)
		);
		$settings = wptravel_get_settings();
		$currency = isset( $settings['currency'] ) ? $settings['currency'] : 'USD';
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $enable_sale ? $trip_price : $regular_price,
			'priceCurrency' => $currency,
			'availability'  => 'https://schema.org/InStock',
		);

		$schema['identifier']	= [
			"@type"		=> "PropertyValue",
			"name"		=> $brand_name,
			"value"		=> $trip_id,
		];

		/**
		 * Trip Review schema structure.
		 *
		 * @param array $schema Schema data for trip rating.
		 * @since 5.0.0
		 */
		$schema = apply_filters( 'wptravel_trip_rating_schema', $schema, $trip_id, $trip );
		self::generate_schema( $schema );
	}

	/**
	 * Generate schema as per $schema array.
	 *
	 * @since 5.0.0
	 * @param array $schema Schema structure array.
	 * @return string
	 */
	public static function generate_schema( $schema = array() ) {
		if ( ! $schema ) {
			return;
		}

		$schema_structure = '';

		if ( $schema ) {
			$schema_structure .= "\n\n";
			$schema_structure .= '<!-- This schema is generated by WP Travel v' . WP_TRAVEL_VERSION . ' -->';
			$schema_structure .= "\n";
			$schema_structure .= '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) . '</script>';
			$schema_structure .= "\n\n";
		}
		echo $schema_structure; // @phpcs:ignore
	}

}
WpTravel_Helpers_Schema::init();
