<?php
/**
 * WP Travel Itinerary class
 *
 * @package WP_Travel
 */

/**
 * WP Travel Itinerary class.
 */
class WP_Travel_Itinerary {
	private $post;
	private $post_meta;
	/**
	 * Constructor.
	 */
	function __construct( $post = null ) {
		$this->post      = is_null( $post ) ? get_post( get_the_ID() ) : $post;
		$this->post_meta = get_post_meta( $this->post->ID );
		return $this->post;
	}

	// depricated. use WP_Travel_Helpers_Trips::is_sale_enabled() instead.
	function is_sale_enabled() {
		$sale_enabled = get_post_meta( $this->post->ID, 'wp_travel_enable_sale', true );
		if ( false !== $sale_enabled && '1' === $sale_enabled ) {
			return true;
		}
		return false;
	}

	function get_gallery_ids() {
		$gallery_ids      = get_post_meta( $this->post->ID, 'wp_travel_itinerary_gallery_ids', true );
		$gallery_id_array = $gallery_ids;
		if ( is_array( $gallery_ids ) && ! empty( $gallery_ids ) ) {
			return $gallery_ids;
		} elseif ( is_array( $gallery_id_array ) && ! empty( $gallery_id_array ) ) {
			$gallery_id_array = json_decode( $gallery_id_array );
			return wp_list_pluck( $gallery_id_array, 'id' );
		}

		$adv_gallery_ids = (array) get_post_meta( $this->post->ID, 'wp_travel_advanced_gallery', true );

		if ( false !== $adv_gallery_ids && ! empty( $adv_gallery_ids ) && isset( $adv_gallery_ids['items'] ) ) {
			/**
			 * Resolves data type issues
			 *
			 * @since 1.0.8
			 */
			return array_map(
				function( $item ) {
					$item = (object) $item;
					return $item->id;
				},
				$adv_gallery_ids['items']
			);
		}

		return false;

	}
	function has_multiple_images() {
		$gallery_ids = $this->get_gallery_ids();
		if ( $gallery_ids && count( $gallery_ids ) > 1 ) {
			return true;
		}
		return false;
	}

	/**
	 * Get trip location.
	 */
	function get_location() {
		if (
			isset( $this->post_meta['wp_travel_lat'][0] )
			&& isset( $this->post_meta['wp_travel_lng'][0] )
			&& isset( $this->post_meta['wp_travel_location'][0] )
			&& '' !== $this->post_meta['wp_travel_lat'][0]
			&& '' !== $this->post_meta['wp_travel_lng'][0]
			&& '' !== $this->post_meta['wp_travel_location'][0]
		) {
			return array(
				'lat'     => $this->post_meta['wp_travel_lat'][0],
				'lng'     => $this->post_meta['wp_travel_lng'][0],
				'address' => $this->post_meta['wp_travel_location'][0],
			);
		}

		return array(
			'lat'     => '',
			'lng'     => '',
			'address' => '',
		);
	}

	function get_outline() {
		if (
			isset( $this->post_meta['wp_travel_outline'][0] )
			&& '' !== $this->post_meta['wp_travel_outline'][0]
		) {
			return wpautop( $GLOBALS['wp_embed']->run_shortcode( ( $this->post_meta['wp_travel_outline'][0] ) ) ); // Changes for rendering videos since 4.2.2.
		}

		return false;
	}

	function get_content() {
		$wp_travel_overview = get_post_meta( $this->post->ID, 'wp_travel_overview', true );
		if ( $wp_travel_overview ) {
			return apply_filters( 'wp_travel_the_content', $wp_travel_overview );
		} elseif ( isset( $this->post->post_content ) && '' !== $this->post->post_content ) {
			update_post_meta( $this->post->ID, 'wp_travel_overview', ' ' );
			return apply_filters( 'wp_travel_the_content', $this->post->post_content );
		}
		return false;
	}

	function get_trip_include() {
		if ( isset( $this->post_meta['wp_travel_trip_include'][0] ) && '' !== $this->post_meta['wp_travel_trip_include'][0] ) {
			return apply_filters( 'wp_travel_the_content', $this->post_meta['wp_travel_trip_include'][0] );
		}

		return false;
	}

	function get_trip_exclude() {
		if ( isset( $this->post_meta['wp_travel_trip_exclude'][0] ) && '' !== $this->post_meta['wp_travel_trip_exclude'][0] ) {
			return apply_filters( 'wp_travel_the_content', $this->post_meta['wp_travel_trip_exclude'][0] );
		}

		return false;
	}

	/**
	 * Get Trip group size.
	 *
	 * @since 1.0.0
	 */
	function get_group_size() {
		$group_size = isset( $this->post_meta['wp_travel_group_size'] ) ? $this->post_meta['wp_travel_group_size'][0] : '';

		if ( $group_size ) {
			return apply_filters( 'wp_travel_min_max_show_frontend', $group_size, $this->post->ID ); // If group size saved in meta. return it from meta.
		}

		// Multiple Pricing.
		$pricing_options = get_post_meta( $this->post->ID, 'wp_travel_pricing_options', true );

		if ( wptravel_is_react_version_enabled() ) {
			$pricing_options = wptravel_get_trip_pricings( $this->post->ID );
		}

		if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
			$group_size = 0;
			foreach ( $pricing_options as $pricing_option ) {
				if ( isset( $pricing_option['max_pax'] ) ) {
					if ( $pricing_option['max_pax'] > $group_size ) {
						$group_size = $pricing_option['max_pax'];
					}
				} elseif ( isset( $pricing_option['categories'] ) ) { // Added for new category pricing options.
					$max_pax_array = array_column( $pricing_option['categories'], 'max_pax' );
					$max_pax       = array_sum( $max_pax_array );
					$group_size    = $max_pax;
				}
			}
		}
		/**
		 * @since 5.1.2
		 */
		$group_size = apply_filters( 'wptravel_group_size', $group_size, $this->post->ID );
		if ( $group_size ) {
			return (int) $group_size;
		}
		return false;
	}

	/**
	 * Update Itinerary group size.
	 *
	 * @since 5.0.8
	 */
	public function update_group_size() {
		$group_size = 0;
		$minimum_pax =  0;
		// Multiple Pricing.
		$pricing_options = get_post_meta( $this->post->ID, 'wp_travel_pricing_options', true );

		if ( wptravel_is_react_version_enabled() ) {
			$pricing_options = wptravel_get_trip_pricings( $this->post->ID );
		}
		// print_r( $pricing_options );die;
		if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
			$group_size = 0;
			foreach ( $pricing_options as $pricing_option ) {
				if ( isset( $pricing_option['max_pax'] ) ) {
					if ( $pricing_option['max_pax'] > $group_size ) {
						$group_size = $pricing_option['max_pax'];
						$minimum_pax = isset( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : 0;
					}
				} elseif ( isset( $pricing_option['categories'] ) ) { // Added for new category pricing options.
					$max_pax_array = array_column( $pricing_option['categories'], 'max_pax' );
					$max_pax       = array_sum( $max_pax_array );
					$group_size    = $max_pax;
				}
			}
		}
		update_post_meta( $this->post->ID, 'wp_travel_group_size', $group_size );
		update_post_meta( $this->post->ID, 'wp_travel_group_min_size', $minimum_pax );
	}

	function get_trip_code() {
		$post_id = $this->post->ID;
		if ( (int) $post_id < 10 ) {
			$post_id = '0' . $post_id;
		}
		return apply_filters( 'wp_travel_trip_code', 'WT-CODE ' . $post_id, $post_id );
	}

	function get_trip_types( $fields = null ) {
		if ( is_null( $fields ) ) {
			$fields = array( 'fields' => 'all' );
		}
		$tripe_types = wp_get_post_terms( $this->post->ID, 'itinerary_types', $fields );
		if ( ! empty( $trip_types ) ) {
			return $trip_types;
		}
		return false;
	}

	function get_trip_types_list( $before = '', $sep = ', ', $after = '' ) {
		$lists = get_the_term_list( $this->post->ID, 'itinerary_types', $before, $sep, $after );
		if ( '' !== $lists ) {
			return $lists;
		}
		return false;
	}

	function get_activities_list( $before = '', $sep = ', ', $after = '' ) {
		$lists = get_the_term_list( $this->post->ID, 'activity', $before, $sep, $after );
		if ( '' !== $lists ) {
			return $lists;
		}
		return false;
	}

	/**
	 * Get faqs.
	 *
	 * @since 2.0.7
	 */
	public function get_faqs() {
		$post_id = $this->post->ID;
		return wptravel_get_faqs( $post_id );
	}
}
