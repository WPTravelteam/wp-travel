<?php
/**
 * Helper Functions.
 *
 * @package wp-travel/inc
 */

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

/** Return All Settings of WP Traval. */
function wp_traval_get_settings() {
	$settings = get_option( 'wp_travel_settings' );
	return $settings;
}

/**
 * Return Trip Code.
 *
 * @param  int $post_id Post ID of post.
 * @return string Returns the trip code.
 */
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
function wp_traval_build_post_tree( array &$elements, $parent_id = 0 ) {
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

/**
 * [wp_traval_get_post_hierarchy_dropdown description]
 *
 * @param  [type]  $list_serialized [description].
 * @param  [type]  $selected        [description].
 * @param  integer $nesting_level   [description].
 * @param  boolean $echo            [description].
 * @return [type]                   [description]
 */
function wp_traval_get_post_hierarchy_dropdown( $list_serialized, $selected, $nesting_level = 0, $echo = true ) {
	$contents = '';
	if ( $list_serialized ) :

		$space = '';
		for ( $i = 1; $i <= $nesting_level; $i ++ ) {
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

/**
 * Get Map Data.
 */
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

	/* TODO: Add global Settings to show/hide related post. */

	$settings = wp_traval_get_settings();
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code );

	// For use in the loop, list 5 post titles related to first tag on current post.
	$terms = wp_get_object_terms( $post_id, 'itinerary_types' );

	$no_related_post_message = '<p class="wp-travel-no-detail-found-msg">' . esc_html( 'Related itineraries not found.' ) . '</p>';
	?>
	 <div class="wp-travel-related-posts wp-travel-container-wrap">
		 <h2><?php echo apply_filters( 'wp_travel_related_post_title', esc_html( 'Related Itineraries', 'wp-travel' ) ); ?></h2>
		 	<?php
		 	if ( ! empty( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );

				$args = array(
					'post_type' => 'itineraries',
					'post__not_in' => array( $post_id ),
					'posts_per_page' => 4,
					'tax_query' => array(
						array(
							'taxonomy' => 'itinerary_types',
							'field' => 'id',
							'terms' => $term_ids,
						),
					),
				);
				$query = new WP_Query( $args );
			if ( $query->have_posts() ) { ?>
				    <div class="wp-travel-row-wrap">
						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php
							$enable_sale 	= get_post_meta( get_the_ID(), 'wp_travel_enable_sale', true );
							$trip_price 	= wp_travel_get_trip_price( get_the_ID() );
							$sale_price 	= wp_travel_get_trip_sale_price( get_the_ID() ); ?>
							<div class="related-post-item-wrapper">
							    <div class="related-post-wrap-bg">
									<div class="related-post-content">

										<h4 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
										<span class="post-category">


											<div class="entry-meta">
												<div class="category-list-items">
													<span class="post-category">
														<?php $terms = get_the_terms( get_the_ID(), 'itinerary_types' ); ?>
													    <?php if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
													    	<i class="fa fa-folder-o" aria-hidden="true"></i>
													    	<?php
													    	$first_term = array_shift( $terms );
													    	$term_name = $first_term->name;
													    	$term_link = get_term_link( $first_term->term_id, 'itinerary_types' ); ?>
															<a href="<?php echo esc_url( $term_link, 'wp-travel' ); ?>" rel="tag">
																<?php esc_html_e( $term_name, 'wp-travel' ); ?>
															</a>
															<div class="wp-travel-caret">
															<?php if ( count( $terms ) > 0 ) : ?>
																<i class="fa fa-caret-down"></i>

																<div class="sub-category-menu">
																	<?php foreach( $terms as $term ) : ?>
																		<?php
																			$term_name = $term->name;
													    					$term_link = get_term_link( $term->term_id, 'itinerary_types' ); ?>
																		<a href="<?php echo esc_url( $term_link, 'wp-travel' ); ?>">
																			<?php esc_html_e( $term_name, 'wp-travel' ); ?>
																		</a>
																	<?php endforeach; ?>
																</div>
															<?php endif; ?>
															</div>
														<?php endif; ?>
													</span>
												</div>
												<div class="wp-travel-average-review">
												    <span><?php printf( '%s reviews',wp_travel_get_rating_count() ); ?></span>
												</div>
											</div>
										</span>
									</div>
									<div class="related-post-thumbnail">
									 	<a href="<?php the_permalink() ?>">
										<?php echo wp_travel_get_post_thumbnail( get_the_ID(), 'wp_travel_thumbnail' ); ?>
									   	</a>
									   	<?php wp_travel_save_offer( get_the_ID() ); ?>
									</div>
									<div class="recent-post-bottom-meta">
										<?php wp_travel_trip_price( get_the_ID(), true ); ?>
									</div>
									<?php if ( $enable_sale ) : ?>
						      			<div class="wp-travel-offer">
						      			    <span><?php esc_html_e( 'Offer', 'wp-travel' ); ?></span>
						      			</div>
						      			<?php endif; ?>

								</div>
							</div>

						<?php endwhile; ?>
					</div>
			<?php
			} else {
				echo $no_related_post_message;
			}
			wp_reset_query();
	 } else {
	 	echo $no_related_post_message;
	 }
	 ?>
	 </div>
	 <?php
}

/**
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post
 * @return int Trip Price.
 */
function wp_travel_get_trip_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_price 	= get_post_meta( $post_id, 'wp_travel_price', true );
	if ( $trip_price ) {
		return $trip_price;
	}
	return 0;
}

/**
 * Return Trip Sale Price.
 *
 * @param  int $post_id Post id of the post.
 * @return int Trip Price.
 */
function wp_travel_get_trip_sale_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_sale_price 	= get_post_meta( $post_id, 'wp_travel_sale_price', true );
	if ( $trip_sale_price ) {
		return $trip_sale_price;
	}
	return 0;
}

/**
 * Get post thumbnail.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image tag.
 */
function wp_travel_get_post_thumbnail( $post_id, $size = 'post-thumbnail' ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}
	$thumbnail = get_the_post_thumbnail( $post_id, $size );

	if ( ! $thumbnail ) {
		$placeholder_image_url = wp_travel_get_post_placeholder_image_url();
		$thumbnail = '<img width="100%" height="100%" src="' . $placeholder_image_url . '">';
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
function wp_travel_get_post_thumbnail_url( $post_id, $size = 'post-thumbnail' ) {
	if ( ! $post_id ) {
		return;
	}
	$thumbnail = get_the_post_thumbnail_url( $post_id, $size );

	if ( ! $thumbnail ) {
		$thumbnail = wp_travel_get_post_placeholder_image_url();
	}
	return $thumbnail;
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
