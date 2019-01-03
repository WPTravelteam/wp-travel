<?php
/**
 * WP Travel Itinerary class
 *
 * @package WP Travel
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
		$this->post = is_null( $post ) ? get_post( get_the_ID() ) : $post;
		$this->post_meta = get_post_meta( $this->post->ID );
		return $this->post;
	}

	function is_sale_enabled() {
		$sale_enabled = get_post_meta( $this->post->ID, 'wp_travel_enable_sale', true );
		if ( false !== $sale_enabled && '1' === $sale_enabled ) {
			return true;
		}
		return false;
	}

	function get_gallery_ids() {
		$gallery_ids = get_post_meta( $this->post->ID, 'wp_travel_itinerary_gallery_ids', true );
		if ( false !== $gallery_ids && ! empty( $gallery_ids ) ) {
			return $gallery_ids;
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
				'lat' => $this->post_meta['wp_travel_lat'][0],
				'lng' => $this->post_meta['wp_travel_lng'][0],
				'address' => $this->post_meta['wp_travel_location'][0],
			);
		}

		return array(
			'lat' => '',
			'lng' => '',
			'address' => '',
		);
	}

	function get_outline() {
		if (
			isset( $this->post_meta['wp_travel_outline'][0] )
			&& '' !== $this->post_meta['wp_travel_outline'][0]
		) {
			return wpautop( do_shortcode( $this->post_meta['wp_travel_outline'][0] ) );
		}

		return false;
	}

	function get_content() {
		if ( isset( $this->post->post_content ) && '' !== $this->post->post_content ) {
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

	function get_group_size() {
		if (
			isset( $this->post_meta['wp_travel_group_size'][0] )
			&& '' !== $this->post_meta['wp_travel_group_size'][0]
		) {
			return (int) $this->post_meta['wp_travel_group_size'][0];
		}
		return false;
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
}
