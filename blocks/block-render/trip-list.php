<?php

/**
 * 
 * Render Callback For Trip List
 * 
 */
function wptravel_block_trip_list_render( $attributes, $content ){
	$query_args = isset( $attributes['query'] ) ? $attributes['query'] : array();

	// Legacy Block Compatibility & fixed conflict with yoast.
	if ( isset( $attributes['location'] ) ) {
		$filter_term = get_term( $attributes['location'], 'travel_locations' );
		if ( is_object( $filter_term ) && isset( $filter_term->term_id ) ) {
			$selected_term                          = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripDestinations'] = array( $selected_term );
		}
	}
	if ( isset( $attributes['tripType'] ) ) {
		$filter_term = get_term( $attributes['tripType'], 'itinerary_types' );
		if ( is_object( $filter_term ) && isset( $filter_term->term_id ) ) {
			$selected_term                   = array(
				'count'       => $filter_term->count,
				'id'          => $filter_term->term_id,
				'description' => $filter_term->description,
				'taxonomy'    => $filter_term->taxonomy,
				'name'        => $filter_term->name,
				'slug'        => $filter_term->slug,
			);
			$query_args['selectedTripTypes'] = array( $selected_term );
		}
	}

	// Options / Attributes.
	$numberposts = isset( $query_args['numberOfItems'] ) && $query_args['numberOfItems'] ? $query_args['numberOfItems'] : 3;

	$args = array(
		'post_type'    => WP_TRAVEL_POST_TYPE,
		'numberposts'  => $numberposts,
		'post__not_in' => array( get_the_ID() ),
	);

	if ( isset( $query_args['orderBy'] ) ) {
		switch ( $query_args['orderBy'] ) {
			case 'title':
				$args['orderby'] = 'post_title';
				break;
			case 'date':
				$args['orderby'] = 'post_date';
				break;
		}
		$args['order'] = $query_args['order'];
	}
	if ( isset( $query_args['selectedTripTypes'] ) && ! empty( $query_args['selectedTripTypes'] ) ) {
		$args['itinerary_types'] = wp_list_pluck( $query_args['selectedTripTypes'], 'slug' );
	}
	if ( isset( $query_args['selectedTripDestinations'] ) && ! empty( $query_args['selectedTripDestinations'] ) ) {
		$args['travel_locations'] = wp_list_pluck( $query_args['selectedTripDestinations'], 'slug' );
	}

	if ( isset( $query_args['selectedTripActivities'] ) && ! empty( $query_args['selectedTripActivities'] ) ) {
		$args['activity'] = wp_list_pluck( $query_args['selectedTripActivities'], 'slug' );
	}

	if ( isset( $query_args['selectedTripKeywords'] ) && ! empty( $query_args['selectedTripKeywords'] ) ) {
		$args['travel_keywords'] = wp_list_pluck( $query_args['selectedTripKeywords'], 'slug' );
	}

	// Meta Query.
	$sale_trip     = isset( $attributes['saleTrip'] ) ? $attributes['saleTrip'] : false;
	$featured_trip = isset( $attributes['featuredTrip'] ) ? $attributes['featuredTrip'] : false;
	if ( $sale_trip ) {
		$args['sale_trip'] = $sale_trip;
	}
	if ( $featured_trip ) {
		$args['featured_trip'] = $featured_trip;
	}
	ob_start();

	$trip_data = WpTravel_Helpers_Trips::filter_trips( $args );

	if ( is_array( $trip_data ) && isset( $trip_data['code'] ) && 'WP_TRAVEL_FILTER_RESULTS' === $trip_data['code'] ) {
		$trips          = $trip_data['trip'];
		$trip_ids       = wp_list_pluck( $trips, 'id' );
		$col_per_row    = 3;
		if ( $numberposts < 3 ) {
			$col_per_row = $numberposts;
		}
		$layout_version = 'v1';
		if ( function_exists( 'wptravel_layout_version' ) ) {
			$layout_version = wptravel_layout_version();
		}
		?>
		<div id="wptravel-block-trips-list" class="wptravel-block-wrapper wptravel-block-trips-list wptravel-block-preview">
			<div class="wp-travel-itinerary-items"> 
				<?php
					if( $sale_trip || $featured_trip ){
						$args  = array(
							'post_type' => WP_TRAVEL_POST_TYPE,
							'post__in'  => $trip_ids,
						);
					}
					
					$query = new WP_Query( $args );
					if ( $query->have_posts() ) {
						if ( 'v1' === $layout_version ) :
							?>
							<ul style="" class="wp-travel-itinerary-list grid-view itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row">
								<?php
								while ( $query->have_posts() ) :
									$query->the_post();
									?>
									<?php
									wptravel_get_block_template_part( 'shortcode/itinerary', 'item' );
									?>
								<?php endwhile; ?>
							</ul>
						<?php else : ?>
							<div class="wp-travel-itinerary-items wptravel-archive-wrapper  grid-view " >
								<?php
								while ( $query->have_posts() ) :
									$query->the_post();
									wptravel_get_block_template_part( 'v2/content', 'archive-itineraries' );
								endwhile;
								?>
							</div>
							<?php
						endif;
						wp_reset_postdata();
					}
					?>
			</div>
		</div>
		<?php
	} else {
		wptravel_get_block_template_part( 'shortcode/itinerary', 'item-none' );
	}

	$html = ob_get_clean();

	return $html;
}