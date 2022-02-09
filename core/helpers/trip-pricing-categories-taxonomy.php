<?php
class WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy {
	private static $taxonomy = 'itinerary_pricing_category';
	public static function get_trip_pricing_categories_terms() {
		$tax        = 'itinerary_pricing_category';
		$taxonomies = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => false,
			)
		);

		$pricing_categories = array();
		if ( ! empty( $taxonomies ) ) {
			$index = 0;
			foreach ( $taxonomies as $tax ) {
				$pricing_categories[ $index ]['id']       = $tax->term_id;
				$pricing_categories[ $index ]['title']    = $tax->name;
				$pricing_categories[ $index ]['pax_size'] = 1;
				$pax_size                                 = get_term_meta( $tax->term_id, 'pax_size', true );
				if ( ! empty( $pax_size ) ) {
					$pricing_categories[ $index ]['pax_size'] = absint( $pax_size );
				}
				$index++;
			}
		}
		if ( empty( $pricing_categories ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_PRICING_CATEGORIES' );
		}

		return array(
			'code'               => 'WP_TRAVEL_TRIP_PRICING_CATEGORIES',
			'pricing_categories' => $pricing_categories,
		);
	}

	public static function get_trip_pricing_categories_term( $category_id ) {
		$term = get_term( absint( $category_id ), self::$taxonomy );

		if ( is_wp_error( $term ) || empty( $term ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_PRICING_CATEGORIES_TERM' );
		}

		$pricing_categories_term['id']       = $term->term_id;
		$pricing_categories_term['title']    = $term->name;
		$pricing_categories_term['pax_size'] = 1;
		$pax_size                            = get_term_meta( $term->term_id, 'pax_size', true );
		if ( ! empty( $pax_size ) ) {
			$pricing_categories_term['pax_size'] = absint( $pax_size );
		}

		return array(
			'code'                       => 'WP_TRAVEL_TRIP_PRICING_CATEGORIES_TAXONOMY_TERM',
			'pricing_category_term_info' => $pricing_categories_term,
		);
	}
}
