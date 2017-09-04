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
			isset( $this->post_meta['wp_traval_lat'][0] )
			&& isset( $this->post_meta['wp_traval_lng'][0] )
			&& '' !== $this->post_meta['wp_traval_lat'][0]
			&& '' !== $this->post_meta['wp_traval_lng'][0]
		) {
			return array(
				'lat' => $this->post_meta['wp_traval_lat'][0],
				'lng' => $this->post_meta['wp_traval_lng'][0],
				'address' => $this->post_meta['wp_traval_location'][0],
			);
		}

		return false;
	}
}
