<?php
function wp_travel_get_gallery_ids( $post_id ) {
	$gallery_ids = get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	if ( false === $gallery_ids || empty( $gallery_ids ) ) {
		return false;
	}
	return $gallery_ids;
}

/** Return All Settings of WP Traval. */
function wp_traval_get_settings() {
	$settings = get_option( 'wp_travel_settings' );
	return $settings;
}

/** Return Trip Code */
function wp_traval_get_trip_code( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

	if ( ( int ) $post_id < 10 ) {
		$post_id = '0' . $post_id;
	}
	return apply_filters( 'wp_traval_trip_code', 'WT-CODE ' . $post_id, $post_id );
}

/**
 * Return dropdown.
 *
 * @param  array $args Arguments for dropdown list.
 * @return HTML  return dropdown list.
 */
function wp_traval_get_dropdown_currency_list( $args = array() ) {

	$currency_list = wp_traval_get_currency_list();

	$default = array(
		'id'		=> '',
		'class'		=> '',
		'name'		=> '',
		'option'	=> '',
		'options'	=> '',
		'selected'	=> '',
		);

	$args = array_merge( $default, $args );

	$dropdown = '';
	if ( is_array( $currency_list )  && count( $currency_list ) > 0 ) {
		$dropdown .= '<select name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" >';
		if ( '' != $args['option'] ) {
			$dropdown .= '<option value="" >' . $args['option'] . '</option>';
		}

		foreach ( $currency_list as $key => $currency ) {

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $currency . ' (' . wp_traval_get_currency_symbol( $key ) . ')</option>';
		}
		$dropdown .= '</select>';

	}

	return $dropdown;
}

/**
 * Return Tree Form of post Object.
 *
 * @param Object $elements Post Object.
 * @param Int    $parent_id Parent ID of post.
 * @return Object Return Tree Form of post Object.
 */
function wp_traval_build_post_tree(array &$elements, $parent_id = 0) {
	$branch = array();

	foreach ( $elements as $element ) {
		if ( $element->post_parent == $parent_id ) {
			$children = wp_traval_build_post_tree( $elements, $element->ID );
			if ( $children ) {
				$element->children = $children;
			}
			$branch[ $element->ID ] = $element;
			unset( $elements[ $element->ID ] );
		}
	}
	return $branch;
}

function wp_traval_get_post_hierarchy_dropdown( $list_serialized, $selected, $nesting_level= 0, $echo = true ) {
	$contents = '';
	if ( $list_serialized ) :
		
		$space = '';
		for ( $i=1; $i<= $nesting_level; $i ++ ) {
			$space .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ( $list_serialized as $content ) {

			$contents .= '<option value="' . $content->ID . '" ' . selected( $selected, $content->ID, false ) . ' >' . $space . $content->post_title . '</option>';
			if ( isset( $content->children ) ) {
				$contents .= wp_traval_get_post_hierarchy_dropdown( $content->children, $selected, ( $nesting_level + 1 ) , false );
			}
		}
	endif;
	if ( ! $echo ) {
		return $contents;
	}

	echo $contents;
	return false;
}

function get_wp_travel_map_data() {
	global $post;
	$lat = ( '' != get_post_meta( $post->ID, 'wp_traval_lat', true ) ) ? get_post_meta( $post->ID, 'wp_traval_lat', true ) : 27.6727305;
	$lng = ( '' != get_post_meta( $post->ID, 'wp_traval_lng', true ) ) ? get_post_meta( $post->ID, 'wp_traval_lng', true ) : 85.3252943;
	$loc = ( '' != get_post_meta( $post->ID, 'wp_traval_location', true ) ) ? get_post_meta( $post->ID, 'wp_traval_location', true ) : 'Mangal Bazaar, Patan 44600, Nepal';

	$map_meta = array(
		'lat' => $lat,
		'lng' => $lng,
		'loc' => $loc,
		);
	return $map_meta;
}


function wp_travel_get_related_post( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
	$settings = wp_traval_get_settings();	
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code );

	//for use in the loop, list 5 post titles related to first tag on current post
	 $terms = wp_get_object_terms( $post_id, 'itinerary_types' );
	 if ( $terms ) {
	 	if ( isset( $terms[0]->term_id ) ) {

		 	$args = array(
			'post_type' => 'itineraries',
			'post__not_in' => array( $post_id ),
			'posts_per_page' => 4,
			'tax_query' => array(
			    array(
			    'taxonomy' => 'itinerary_types',
			    'field' => 'id',
			    'terms' => $terms[0]->term_id
			     )
			  )
			);
			$query = new WP_Query( $args );

			if( $query->have_posts() ) { ?>
				<div class="wp-travel-related-posts wp-travel-container-wrap">
					<h2><?php echo apply_filters( 'wp_travel_related_post_title', esc_html( 'Related Trips', 'wp-travel' ) ); ?></h2>
				    <div class="wp-travel-row-wrap">
						
						<?php while ($query->have_posts()) : $query->the_post(); ?>
							<?php 
							$trip_price 	= get_post_meta( get_the_ID(), 'wp_travel_price', true );
							$enable_sale 	= get_post_meta( get_the_ID(), 'wp_travel_enable_sale', true );
							$sale_price 	= get_post_meta( get_the_ID(), 'wp_travel_sale_price', true );
							?>
							<div class="related-post-item-wrapper">
							    <div class="related-post-wrap-bg">
									<div class="related-post-content">

										<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
										<span class="post-category">
											<?php echo get_the_term_list( get_the_ID(), 'itinerary_types', '', ', ', '' ) ?>
										</span>
									</div>
									<div class="related-post-thumbnail">
									  <a href="<?php the_permalink() ?>">
										<?php the_post_thumbnail(); ?>
									   </a>
									</div>
									<div class="recent-post-bottom-meta">
										<div class="wp-travel-trip-detail">
											<div class="trip-price" >
											<?php if ( $enable_sale ) : ?>
											    <del>
											<?php else: ?>
												<ins>
											<?php endif; ?>
											      <span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
											<?php if ( $enable_sale ) : ?>
											    </del>
											    <ins>
											      <span><?php echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price ); ?></span>
											    </ins>
											<?php else: ?>
												</ins>
											<?php endif; ?>
											    <span class="person-count">/person</span>
											</div>
										</div>
									</div>
								</div>
							</div>

						<?php endwhile; ?>
					</div>
				</div>
			<?php
			}
			wp_reset_query();
	 	}

	 }
}
