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
function wp_traval_get_trip_code( $post_id = null ) {
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
	$lat = ( '' != get_post_meta( $post->ID, 'wp_traval_lat', true ) ) ? get_post_meta( $post->ID, 'wp_traval_lat', true ) :'';
	$lng = ( '' != get_post_meta( $post->ID, 'wp_traval_lng', true ) ) ? get_post_meta( $post->ID, 'wp_traval_lng', true ) : '';
	$loc = ( '' != get_post_meta( $post->ID, 'wp_traval_location', true ) ) ? get_post_meta( $post->ID, 'wp_traval_location', true ) : '';

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
	$hide_related_itinerary = ( isset( $settings['hide_related_itinerary'] ) && '' !== $settings['hide_related_itinerary'] ) ? $settings['hide_related_itinerary'] : 'no';

	if ( 'yes' === $hide_related_itinerary ) {
		return;
	}
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code );

	// For use in the loop, list 5 post titles related to first tag on current post.
	$terms = wp_get_object_terms( $post_id, 'itinerary_types' );

	$no_related_post_message = '<p class="wp-travel-no-detail-found-msg">' . esc_html__( 'Related itineraries not found.', 'wp-travel' ) . '</p>';
	?>
	 <div class="wp-travel-related-posts wp-travel-container-wrap">
		 <h2><?php echo apply_filters( 'wp_travel_related_post_title', esc_html__( 'Related Itineraries', 'wp-travel' ) ); ?></h2>
		<div class="wp-travel-itinerary-items"> 
			 <?php
		 	if ( ! empty( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$col_per_row = apply_filters( 'wp_travel_related_itineraries_col_per_row' , '3' );
				$args = array(
					'post_type' => 'itineraries',
					'post__not_in' => array( $post_id ),
					'posts_per_page' => $col_per_row,
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
				
				<ul style="grid-template-columns:repeat(<?php esc_attr_e( $col_per_row, 'wp-travel') ?>, 1fr)" class="wp-travel-itinerary-list">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item' ); ?>
					<?php endwhile; ?>
				</ul>
			<?php
			} else {
				wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
			}
			wp_reset_query();
	 } else {
		wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
	 }
	 ?>
	 </div>
	 </div>
	 <?php
}

/**
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
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
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
 *
 * @since 1.0.5
 * @return int Trip Price.
 */
function wp_travel_get_actual_trip_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}

	$trip_price = get_post_meta( $post_id, 'wp_travel_trip_price', true );
	if ( ! $trip_price ) {
		$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );

		if ( $enable_sale ) {
			$trip_price = wp_travel_get_trip_sale_price( $post_id );
		} else {
			$trip_price = wp_travel_get_trip_price( $post_id );
		}
	}
	return $trip_price;
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
	$size = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
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

/**
 * Allowed tags.
 *
 * @param array $tags filter tags.
 * @return array allowed tags.
 */
function wp_travel_allowed_html( $tags = array() ) {

	$allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr' => array(
			'title' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),
		'cite' => array(
			'title' => array(),
		),
		'code' => array(),
		'del' => array(
			'datetime' => array(),
			'title' => array(),
		),
		'dd' => array(),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'i' => array(),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'q' => array(
			'cite' => array(),
			'title' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'ul' => array(
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
	  'post_type'   => 'itineraries',
	);

	$itineraries = get_posts( $args );

	$itineraries_array = array();
	foreach ( $itineraries as $itinerary ) {
		$itineraries_array[ $itinerary->ID ] = $itinerary->post_title;
	}
	return apply_filters( 'wp_travel_itineraries_array', $itineraries_array, $args );
}

/**
 * Return WP Travel Featured post.
 *
 * @param integer $no_of_post_to_show No of post to show.
 * @return array
 */
function wp_travel_featured_itineraries( $no_of_post_to_show = 3 ) {
	$args = array(
		'numberposts' => $no_of_post_to_show,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'meta_key'         => 'wp_travel_featured',
		'meta_value'       => 'yes',
		'post_type'        => 'itineraries',
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
		<form method="get" name="wp-travel_search" action="<?php echo esc_url( home_url( '/' ) );  ?>" > 
			<input type="hidden" name="post_type" value="itineraries" />
			<p>
				<label><?php esc_html_e( 'Search:', 'wp-travel' ) ?></label>
				<input type="text" name="s" id="s" value="<?php echo ( isset( $_GET['s'] ) ) ? esc_textarea( $_GET['s'] ) : ''; ?>" placeholder="<?php esc_html_e( apply_filters( 'wp_travel_search_placeholder', 'wp-travel' ), 'wp-travel' ) ?>">
			</p>
			<p>
				<label><?php esc_html_e( 'Trip Type:', 'wp-travel' ) ?></label>
				<?php
				$taxonomy = 'itinerary_types';
				$args = array(
					'show_option_all'    => __( 'All', 'wp-travel' ),
					'hide_empty'         => 0,
					'selected'           => 1,
					'hierarchical'       => 1,
					'name'               => $taxonomy,
					'class'              => 'wp-travel-taxonomy',
					'taxonomy'           => $taxonomy,
					'selected'           => ( isset( $_GET[$taxonomy] ) ) ? esc_textarea( $_GET[$taxonomy] ) : 0,
					'value_field'		 => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Location:', 'wp-travel' ) ?></label>
				<?php
				$taxonomy = 'travel_locations';
				$args = array(
					'show_option_all'    => __( 'All', 'wp-travel' ),
					'hide_empty'         => 0,
					'selected'           => 1,
					'hierarchical'       => 1,
					'name'               => $taxonomy,
					'class'              => 'wp-travel-taxonomy',
					'taxonomy'           => $taxonomy,
					'selected'           => ( isset( $_GET[$taxonomy] ) ) ? esc_textarea( $_GET[$taxonomy] ) : 0,
					'value_field'		 => 'slug',
				);

				wp_dropdown_categories( $args, $taxonomy );
				?>
			</p>
			
			<p class="wp-travel-search"><input type="submit" name="wp-travel_search" id="wp-travel-search" class="button button-primary" value="<?php esc_html_e( 'Search', 'wp-travel' ) ?>"  /></p>
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
	?>
	<?php if ( 'yes' === $fixed_departure ) : ?>
		<?php
			$start_date	= get_post_meta( $post_id, 'wp_travel_start_date', true );
			$end_date 	= get_post_meta( $post_id, 'wp_travel_end_date', true );
		?>
			
		<div class="wp-travel-trip-time trip-fixed-departure">
			<i class="fa fa-calendar"></i>
			<span class="wp-travel-trip-duration">
				<?php if ( $start_date && $end_date ) : ?>
					<?php $date_format = get_option( 'date_format' ); ?>
					<?php if ( ! $date_format ) : ?>
						<?php $date_format = 'jS M, Y'; ?>
					<?php endif; ?>
					<?php printf( '%s - %s', date( $date_format, strtotime( $start_date ) ), date( $date_format, strtotime( $end_date ) ) ); ?> 
				<?php else : ?>
					<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
				<?php endif; ?>
			</span>
		</div>
		
	<?php else : ?>
		<?php
		$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
		$trip_duration = ( $trip_duration ) ? $trip_duration : 0; ?>
		
		<div class="wp-travel-trip-time trip-duration">
			<i class="fa fa-clock-o"></i>
			<span class="wp-travel-trip-duration">
				<?php if ( ( int ) $trip_duration > 0 ) : ?>
					<?php echo esc_html( $trip_duration . ' Days' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif;
}

/**
 * Get Price Per text.
 *
 * @param Number $post_id Current post id.
 * @since 1.0.5
 */
function wp_travel_get_price_per_text( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$per_person_text = get_post_meta( $post_id, 'wp_travel_price_per', true );
	if ( ! $per_person_text ) {
		$per_person_text = 'person';
	}
	return $per_person_text;
}

/**
 * Check sale price enable or not.
 *
 * @param Number $post_id Current post id.
 * @since 1.0.5
 */
function wp_travel_is_enable_sale( $post_id ) {
	if ( ! $post_id ) {
		return false;
	}
	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );

	if ( $enable_sale  ) {
		return true;
	}
	return false;
}
