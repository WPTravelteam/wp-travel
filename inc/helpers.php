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

/** Return All Settings of WP travel. */
function wp_travel_get_settings() {
	$settings = get_option( 'wp_travel_settings' );
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

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $currency . ' (' . wp_travel_get_currency_symbol( $key ) . ')</option>';
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
				$contents .= wp_travel_get_post_hierarchy_dropdown( $content->children, $selected, ( $nesting_level + 1 ) , false );
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
	$lat = ( '' != get_post_meta( $post->ID, 'wp_travel_lat', true ) ) ? get_post_meta( $post->ID, 'wp_travel_lat', true ) :'';
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

	/* TODO: Add global Settings to show/hide related post. */

	$settings = wp_travel_get_settings();
	$hide_related_itinerary = ( isset( $settings['hide_related_itinerary'] ) && '' !== $settings['hide_related_itinerary'] ) ? $settings['hide_related_itinerary'] : 'no';

	if ( 'yes' === $hide_related_itinerary ) {
		return;
	}
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	// For use in the loop, list 5 post titles related to first tag on current post.
	$terms = wp_get_object_terms( $post_id, 'itinerary_types' );

	$no_related_post_message = '<p class="wp-travel-no-detail-found-msg">' . esc_html__( 'Related trip not found.', 'wp-travel' ) . '</p>';
	?>
	 <div class="wp-travel-related-posts wp-travel-container-wrap">
		 <h2><?php echo apply_filters( 'wp_travel_related_post_title', esc_html__( 'Related Trips', 'wp-travel' ) ); ?></h2>
		<div class="wp-travel-itinerary-items"> 
			 <?php
		 	if ( ! empty( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$col_per_row = apply_filters( 'wp_travel_related_itineraries_col_per_row' , '3' );
				$args = array(
					'post_type' => WP_TRAVEL_POST_TYPE,
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
function wp_travel_get_post_thumbnail_url( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( ! $post_id ) {
		return;
	}
	$size = apply_filters( 'wp_travel_itinerary_thumbnail_size', $size );
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
 * Return JSON Encoded Itinerary price oblect
 * 
 */
function wp_reavel_get_itinereries_prices_array(){

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
	$args = array(
		'numberposts' => $no_of_post_to_show,
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
		<form method="get" name="wp-travel_search" action="<?php echo esc_url( home_url( '/' ) );  ?>" > 
			<input type="hidden" name="post_type" value="<?php echo esc_attr( WP_TRAVEL_POST_TYPE ) ?>" />
			<p>
				<label><?php esc_html_e( 'Search:', 'wp-travel' ) ?></label>
				<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
				<input type="text" name="s" id="s" value="<?php echo ( isset( $_GET['s'] ) ) ? esc_textarea( $_GET['s'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
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
					<?php echo esc_html( $trip_duration . __( ' Days', 'wp-travel' ) ); ?>
				<?php else : ?>
					<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
				<?php endif; ?>
			</span>
		</div>
	<?php endif;
}

/**
 * Return price per fields.
 *
 * @since 1.0.5
 * @return array
 */
function wp_travel_get_price_per_fields() {
	$price_per = array(
		'person' => __( 'Person', 'wp-travel' ),
		'group'	 => __( 'Group', 'wp-travel' ),
	);

	return apply_filters( 'wp_travel_price_per_fields', $price_per );
}

/**
 * Get Price Per text.
 *
 * @param Number $post_id Current post id.
 * @since 1.0.5
 */
function wp_travel_get_price_per_text( $post_id, $key = false ) {
	if ( ! $post_id ) {
		return;
	}
	$per_person_text = get_post_meta( $post_id, 'wp_travel_price_per', true );
	if ( ! $per_person_text ) {
		$per_person_text = 'person';
	}
	$price_per_fields = wp_travel_get_price_per_fields();

	if ( true === $key ) {
		return $per_person_text;
	}

	return $price_per_fields[ $per_person_text ];
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

/**
 * Get All Data Needed for booking stat.
 *
 * @since 1.0.5
 * @return Array
 */
function wp_travel_get_booking_data() {
	global $wpdb;
	$data = array();

	$initial_load = true;

	// Default variables.
	$query_limit = apply_filters( 'wp_travel_stat_default_query_limit', 10 );
	$limit = "limit {$query_limit}";
	$where = '';
	$top_country_where = '';
	$top_itinerary_where = '';
	$groupby = '';

	$from_date = '';
	if ( isset( $_REQUEST['booking_stat_from'] ) && '' !== $_REQUEST['booking_stat_from'] ) {
		$from_date = $_REQUEST['booking_stat_from'];
	}
	$to_date = '';
	if ( isset( $_REQUEST['booking_stat_to'] ) && '' !== $_REQUEST['booking_stat_to'] ) {
		$to_date = $_REQUEST['booking_stat_to'] . ' 23:59:59';
	}
	$country = '';
	if ( isset( $_REQUEST['booking_country'] ) && '' !== $_REQUEST['booking_country'] ) {
		$country = $_REQUEST['booking_country'];
	}

	$itinerary = '';
	if ( isset( $_REQUEST['booking_itinerary'] ) && '' !== $_REQUEST['booking_itinerary'] ) {
		$itinerary = $_REQUEST['booking_itinerary'];
	}

	// Stat Data Array

	// Setting conditions.
	if ( '' !== $from_date || '' !== $to_date || '' !== $country || '' !== $itinerary ) {
		// Set initial load to false if there is extra get variables.
		$initial_load = false;

		if ( '' !== $itinerary ) {
			$where 	 .= " and itinerary_id={$itinerary} ";
			$top_country_where .= $where;
			$groupby .= ' itinerary_id,';
		}
		if ( '' !== $country ) {
			$where   .= " and country='{$country}'";
			$top_itinerary_where .= " and country='{$country}'";
			$groupby .= ' country,';
		}

		if ( '' !== $from_date && '' !== $to_date ) {

			$date_format = 'Y-m-d H:i:s';

			$booking_from = date( $date_format, strtotime( $from_date ) );
			$booking_to   = date( $date_format, strtotime( $to_date ) );

			$where 	 .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			$top_country_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			$top_itinerary_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
		}
		$limit = '';
	}

	$stat_data = array();
	$date_format = 'm/d/Y';
	$booking_stat_from = $booking_stat_to = date( $date_format );
	$temp_stat_data = array();
	$max_bookings = 0;
	$max_pax = 0;

	if ( ! isset( $_REQUEST['chart_type'] ) || ( isset( $_REQUEST['chart_type'] ) && 'booking' === $_REQUEST['chart_type']  ) ) {
		// Booking Data Default Query.
		$initial_transient = $results = get_site_transient( '_transient_wt_booking_stat_data' );
		if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
			$query = "SELECT count(ID) as wt_total, YEAR(post_date) as wt_year, MONTH(post_date) as wt_month, DAY(post_date) as wt_day, sum(no_of_pax) as no_of_pax
			from (
				Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id, PAX.no_of_pax from {$wpdb->posts} P 
				join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on P.ID = C.post_id 
				join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
				join ( Select distinct( post_id ), meta_value as no_of_pax from  {$wpdb->postmeta} WHERE meta_key = 'wp_travel_pax' ) PAX on P.ID = PAX.post_id
				group by P.ID, C.country, I.itinerary_id, PAX.no_of_pax
			) Booking 
			where post_type='itinerary-booking' AND post_status='publish' {$where} group by {$groupby} YEAR(post_date), MONTH(post_date), DAY(post_date) {$limit}";
			$results =  $wpdb->get_results( $query );
			// set initial load transient for stat data.
			if ( $initial_load && ! $initial_transient ) {
				set_site_transient( '_transient_wt_booking_stat_data', $results );
			}
		}

		
		$temp_stat_data['data_label'] = __( 'Bookings', 'wp-travel' );
		if( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
			$temp_stat_data['data_label'] = __( 'Booking 1', 'wp-travel' );
		}
		$temp_stat_data['data_bg_color'] = __( '#00f', 'wp-travel' );
		$temp_stat_data['data_border_color'] = __( '#00f', 'wp-travel' );
	} else { 
		// Payment Data Default Query.		
		$query = "Select count( BOOKING.ID ) as wt_total, YEAR( payment_date ) as wt_year, Month( payment_date ) as wt_month, DAY( payment_date ) as wt_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING 
		join ( 
			Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost 
			join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID    
			WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
		) PMT on BOOKING.ID = PMT.post_id
		join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id 
		join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
		join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
		join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
       end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
		where post_status='publish' and post_type = 'itinerary-booking' {$where}
		group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";

		$results =  $wpdb->get_results( $query );
		
		$temp_stat_data['data_label'] = __( 'Payment', 'wp-travel' );
		if( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
			$temp_stat_data['data_label'] = __( 'Payment 1', 'wp-travel' );
		}
		$temp_stat_data['data_bg_color'] = __( '#1DFE0E', 'wp-travel' );
		$temp_stat_data['data_border_color'] = __( '#1DFE0E', 'wp-travel' );
	}
	
	if ( is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			$label_date = $result->wt_year . '-' . $result->wt_month . '-' . $result->wt_day;
			$label_date = date( $date_format, strtotime( $label_date ) );

			$temp_stat_data['data'][$label_date] = $result->wt_total;

			$max_bookings += ( int ) $result->wt_total;
			$max_pax += ( int ) $result->no_of_pax;

			if ( strtotime( $booking_stat_from ) > strtotime( $label_date ) ) {

				$booking_stat_from = date( 'm/d/Y', strtotime( $label_date ) );
			}

			if ( strtotime( $booking_stat_to ) < strtotime( $label_date ) ) {
				$booking_stat_to = date( 'm/d/Y', strtotime( $label_date ) );
			}
		}
	}

	// Booking Calculation ends here.
	if ( '' !== $from_date ) {
		$booking_stat_from = date( 'm/d/Y', strtotime( $from_date ) );
	}

	if ( '' !== $to_date ) {
		$booking_stat_to = date( 'm/d/Y', strtotime( $to_date ) );
	}
	
	// Query for top country.
	$initial_transient = $results = get_site_transient( '_transient_wt_booking_top_country' );
	if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
		$top_country_query = "SELECT count(ID) as wt_total, country
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P 
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking 
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by country order by wt_total desc";

		$top_countries = array();
		$results =  $wpdb->get_results( $top_country_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_country', $results );
		}
	}

	if ( is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			$top_countries[] = $result->country;
		}
	}
	// End of query for top country.
	// Query for top Itinerary.
	$initial_transient = $results = get_site_transient( '_transient_wt_booking_top_itinerary' );
	if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
		$top_itinerary_query = "SELECT count(ID) as wt_total, itinerary_id
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P 
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking 
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by itinerary_id order by wt_total desc";

		$results =  $wpdb->get_results( $top_itinerary_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_itinerary', $results );
		}
	}
	$top_itinerary = array( 'name' => esc_html__( 'N/A', 'wp-travel' ), 'url' => '' );
	if ( is_array( $results ) && count( $results ) > 0 ) {
		$itinerary_id = $results['0']->itinerary_id;

		if ( $itinerary_id ) {
			$top_itinerary['name'] = get_the_title( $itinerary_id );
			$top_itinerary['id'] = $itinerary_id;
		}
	}

	$booking_additional_data = array(
		'from' => $booking_stat_from,
		'to' => $booking_stat_to,
		'max_bookings' => $max_bookings,
		'max_pax' => $max_pax,
		'top_countries' => $top_countries,
		'top_itinerary' => $top_itinerary,
	);

	$data[] = $temp_stat_data;

	// End of Booking Data Default Query.
	$where = '';
	$top_country_where = '';
	$top_itinerary_where = '';
	$groupby = '';
	if( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {

		$compare_from_date = '';
		if ( isset( $_REQUEST['compare_stat_from'] ) && '' !== $_REQUEST['compare_stat_from'] ) {
			$compare_from_date = $_REQUEST['compare_stat_from'];
		}
		$compare_to_date = '';
		if ( isset( $_REQUEST['compare_stat_to'] ) && '' !== $_REQUEST['compare_stat_to'] ) {
			$compare_to_date = $_REQUEST['compare_stat_to'] . ' 23:59:59';
		}
		$compare_country = '';
		if ( isset( $_REQUEST['compare_country'] ) && '' !== $_REQUEST['compare_country'] ) {
			$compare_country = $_REQUEST['compare_country'];
		}

		$compare_itinerary = '';
		if ( isset( $_REQUEST['compare_itinerary'] ) && '' !== $_REQUEST['compare_itinerary'] ) {
			$compare_itinerary = $_REQUEST['compare_itinerary'];
		}

		// Setting conditions.
		if ( '' !== $compare_from_date || '' !== $compare_to_date || '' !== $compare_country || '' !== $compare_itinerary ) {
			// Set initial load to false if there is extra get variables.
			$initial_load = false;

			if ( '' !== $compare_itinerary ) {
				$where 	 .= " and itinerary_id={$compare_itinerary} ";
				$top_country_where .= $where;
				$groupby .= ' itinerary_id,';
			}
			if ( '' !== $compare_country ) {
				$where   .= " and country='{$compare_country}'";
				$top_itinerary_where .= " and country='{$compare_country}'";
				$groupby .= ' country,';
			}

			if ( '' !== $compare_from_date && '' !== $compare_to_date ) {

				$date_format = 'Y-m-d H:i:s';

				$booking_from = date( $date_format, strtotime( $compare_from_date ) );
				$booking_to   = date( $date_format, strtotime( $compare_to_date ) );

				$where 	 .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
				$top_country_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
				$top_itinerary_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			}
			$limit = '';
		}

		$temp_compare_data = array();
		if ( ! isset( $_REQUEST['chart_type'] ) || ( isset( $_REQUEST['chart_type'] ) && 'booking' === $_REQUEST['chart_type']  ) ) {

			// Compare Data Default Query.
			$query = "SELECT count(ID) as wt_total, YEAR(post_date) as wt_year, MONTH(post_date) as wt_month, DAY(post_date) as wt_day, sum(no_of_pax) as no_of_pax
			from (
				Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id, PAX.no_of_pax from {$wpdb->posts} P 
				join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on P.ID = C.post_id 
				join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
				join ( Select distinct( post_id ), meta_value as no_of_pax from  {$wpdb->postmeta} WHERE meta_key = 'wp_travel_pax' ) PAX on P.ID = PAX.post_id
				group by P.ID, C.country, I.itinerary_id, PAX.no_of_pax
			) Booking 
			where post_type='itinerary-booking' AND post_status='publish' {$where} group by {$groupby} YEAR(post_date), MONTH(post_date), DAY(post_date) {$limit}";
			$results =  $wpdb->get_results( $query );
	
			$temp_compare_data['data_label'] = __( 'Booking 2', 'wp-travel' );
			$temp_compare_data['data_bg_color'] = __( '#3c0', 'wp-travel' );
			$temp_compare_data['data_border_color'] = __( '#3c0', 'wp-travel' );
		} else {
			// Payment Data Default Query.		
			$query = "Select count( BOOKING.ID ) as wt_total, YEAR( payment_date ) as wt_year, Month( payment_date ) as wt_month, DAY( payment_date ) as wt_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING 
			join ( 
				Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost 
				join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID    
				WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
			) PMT on BOOKING.ID = PMT.post_id
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id 
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
			join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
			join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
		end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
			where post_status='publish' and post_type = 'itinerary-booking' {$where}
			group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";

			$results =  $wpdb->get_results( $query );

			$temp_compare_data['data_label'] = __( 'Payment', 'wp-travel' );
			if( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
				$temp_compare_data['data_label'] = __( 'Payment 2', 'wp-travel' );
			}
			$temp_compare_data['data_bg_color'] = __( '#000', 'wp-travel' );
			$temp_compare_data['data_border_color'] = __( '#000', 'wp-travel' );
		}

		$date_format = 'm/d/Y';

		$max_bookings = 0;
		$max_pax = 0;
		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $result ) {
				$label_date = $result->wt_year . '-' . $result->wt_month . '-' . $result->wt_day;
				$label_date = date( $date_format, strtotime( $label_date ) );
				$temp_compare_data['data'][$label_date] = $result->wt_total;

				$max_bookings += ( int ) $result->wt_total;
				$max_pax += ( int ) $result->no_of_pax;
			}
		}

		// Query for top country.
		$top_country_query = "SELECT count(ID) as wt_total, country
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P 
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking 
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by country order by wt_total desc";

		$top_countries = array();
		$results =  $wpdb->get_results( $top_country_query );		

		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $result ) {
				$top_countries[] = $result->country;
			}
		}
		// End of query for top country.
		// Query for top Itinerary.
		$top_itinerary_query = "SELECT count(ID) as wt_total, itinerary_id
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P 
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking 
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by itinerary_id order by wt_total desc";

		$results =  $wpdb->get_results( $top_itinerary_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_itinerary', $results );
		}
		$top_itinerary = array( 'name' => esc_html__( 'N/A', 'wp-travel' ), 'url' => '' );
		if ( is_array( $results ) && count( $results ) > 0 ) {
			$itinerary_id = $results['0']->itinerary_id;

			if ( $itinerary_id ) {
				$top_itinerary['name'] = get_the_title( $itinerary_id );
				$top_itinerary['id'] = $itinerary_id;
			}
		}
		// Compare Calculation ends here.
		if ( '' !== $compare_from_date ) {
			$compare_from_date = date( 'm/d/Y', strtotime( $compare_from_date ) );
		}

		if ( '' !== $compare_to_date ) {
			$compare_to_date = date( 'm/d/Y', strtotime( $compare_to_date ) );
		}

		$compare_additional_data = array(
			'from' => $compare_from_date,
			'to' => $compare_to_date,
			'max_bookings' => $max_bookings,
			'max_pax' => $max_pax,
			'top_countries' => $top_countries,
			'top_itinerary' => $top_itinerary,
		);
		// Compare Calculation ends here.
		$data[] = $temp_compare_data;
	}
	$data = apply_filters( 'wp_travel_stat_data', $data, $_REQUEST );
	$new_stat_data = wp_travel_make_stat_data( $data );

	// End of query for top Itinerary.
	$stat_data['stat_data']  	= $new_stat_data;

	$stat_data['booking_stat_from'] = $booking_additional_data['from'];
	$stat_data['booking_stat_to']   = $booking_additional_data['to'];
	$stat_data['max_bookings']  = $booking_additional_data['max_bookings'];
	$stat_data['max_pax']       = $booking_additional_data['max_pax'];
	$stat_data['top_countries'] = wp_travel_get_country_by_code( $booking_additional_data['top_countries'] );
	$stat_data['top_itinerary'] = $booking_additional_data['top_itinerary'];

	if( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
		$stat_data['compare_stat_from'] = $compare_additional_data['from'];
		$stat_data['compare_stat_to']   = $compare_additional_data['to'];
		$stat_data['compare_max_bookings']  = $compare_additional_data['max_bookings'];
		$stat_data['compare_max_pax']       = $compare_additional_data['max_pax'];
		$stat_data['compare_top_countries'] = wp_travel_get_country_by_code( $compare_additional_data['top_countries'] );
		$stat_data['compare_top_itinerary'] = $compare_additional_data['top_itinerary'];

		// Query for total 2 in compare stat.
		// if ( class_exists( 'WP_travel_paypal' ) ) :
			$query = "Select count( BOOKING.ID ) as no_of_payment, YEAR( payment_date ) as payment_year, Month( payment_date ) as payment_month, DAY( payment_date ) as payment_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING 
			join ( 
				Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost 
				join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID    
				WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
			) PMT on BOOKING.ID = PMT.post_id
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id 
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
			join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
			join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
		end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
			where post_status='publish' and post_type = 'itinerary-booking' {$where}
			group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";
			$results = $wpdb->get_results( $query );

			$total_sales_compare = 0;
			if ( $results ) {
				foreach ( $results as $result ) {				
					$total_sales_compare += $result->payment_amount;
				}
			}
			$stat_data['total_sales_compare'] = number_format( $total_sales_compare, 2, '.', '' );
		// endif;
	}

	return $stat_data;
}

/**
 * Get Booking Status List.
 *
 * @since 1.0.5
 */
function wp_travel_get_booking_status() {
	$status = array(
		'pending' => array( 'color' => '#FF9800', 'text' => __( 'Pending', 'wp-travel' ) ),
		'booked' => array( 'color' => '#008600', 'text' => __( 'Booked', 'wp-travel' ) ),
		'canceled' => array( 'color' => '#FE450E', 'text' => __( 'Canceled', 'wp-travel' ) ),
		'N/A' => array( 'color' => '#892E2C', 'text' => __( 'N/A', 'wp-travel' ) ),
	);

	return apply_filters( 'wp_travel_booking_status_list', $status );
}

/**
 * Get Payment Status List.
 *
 * @since 1.0.6
 */
function wp_travel_get_payment_status() {
	$status = array(
		'pending' => array( 'color' => '#FF9800', 'text' => __( 'Pending', 'wp-travel' ) ),
		'paid' => array( 'color' => '#008600', 'text' => __( 'Paid', 'wp-travel' ) ),
		'canceled' => array( 'color' => '#FE450E', 'text' => __( 'Canceled', 'wp-travel' ) ),
		'N/A' => array( 'color' => '#892E2C', 'text' => __( 'N/A', 'wp-travel' ) ),
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
		'partial' => array( 'color' => '#FF9F33', 'text' => __( 'Partial', 'wp-travel' ) ),
		'full' => array( 'color' => '#FF8A33', 'text' => __( 'Full', 'wp-travel' ) ),
		'N/A' => array( 'color' => '#892E2C', 'text' => __( 'N/A', 'wp-travel' ) ),
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
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
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

	$permalinks = wp_parse_args( (array) get_option( 'wp_travel_permalinks', array() ), array(
		'wp_travel_trip_base' => '',
		'wp_travel_trip_type_base' => '',
		'wp_travel_destination_base' => '',
		'wp_travel_activity_base' => '',
	) );

	// $db_version = get_option( 'wp_travel_version' );
	// $current_version = WP_TRAVEL_VERSION;

	// // Fallback slug
	// if ( ( ! $db_version ) && '' === $permalinks['wp_travel_trip_base'] ) {
	// 	$permalinks['wp_travel_trip_base'] = 'itinerary';
	// }

	// Ensure rewrite slugs are set.
	$permalinks['wp_travel_trip_base']   = untrailingslashit( empty( $permalinks['wp_travel_trip_base'] ) ? 'itinerary' : $permalinks['wp_travel_trip_base'] );
	$permalinks['wp_travel_trip_type_base']   = untrailingslashit( empty( $permalinks['wp_travel_trip_type_base'] ) ? 'trip-type' : $permalinks['wp_travel_trip_type_base'] );
	$permalinks['wp_travel_destination_base']   = untrailingslashit( empty( $permalinks['wp_travel_destination_base'] ) ? 'travel-locations' : $permalinks['wp_travel_destination_base'] );
	$permalinks['wp_travel_activity_base']   = untrailingslashit( empty( $permalinks['wp_travel_activity_base'] ) ? 'activity' : $permalinks['wp_travel_activity_base'] );

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

	$return_tabs = $wp_travel_itinerary_tabs = wp_travel_get_default_frontend_tabs( $show_in_menu_query );
	global $post;

	$settings = wp_travel_get_settings();

	$custom_tabs_enable = defined( 'WP_TRAVEL_UTILITIES_PLUGIN_NAME' ) ? true : false;

	$custom_global_tabs = isset( $settings['wp_travel_custom_global_tabs'] ) && '' !== $settings['wp_travel_custom_global_tabs'] ?  $settings['wp_travel_custom_global_tabs'] : array();

	$custom_itinerary_tabs = get_post_meta( $post->ID, 'wp_travel_itinerary_custom_tab_cnt_', true );

	$custom_itinerary_tabs = is_array( $custom_itinerary_tabs ) && count( $custom_itinerary_tabs ) != 0 ? $custom_itinerary_tabs : array(); 

	$wp_travel_use_global_tabs = get_post_meta( $post->ID, 'wp_travel_use_global_tabs', true );

	$wp_travel_tabs = get_post_meta( $post->ID, 'wp_travel_tabs', true );

	$custom_itinerary_tabs_sorting = get_post_meta( $post->ID, 'wp_travel_utilities_custom_itinerary_tabs_sorting_settings', true );

	if ( $custom_tabs_enable && is_array( $custom_itinerary_tabs_sorting ) && ! empty( $custom_itinerary_tabs_sorting ) ) {

		$wp_travel_tabs = $custom_itinerary_tabs_sorting;

	}
	
	if ( 'yes' == $wp_travel_use_global_tabs && isset( $settings['global_tab_settings'] ) ) {

		
		$wp_travel_tabs = $settings['global_tab_settings'];
		
		if ( $custom_tabs_enable && isset( $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'] ) && '' !== $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'] ) {
			
			$wp_travel_tabs = $settings['wp_travel_utilities_custom_global_tabs_sorting_settings'];
			
		}
		
		
	}
	
	
	if ( is_array( $wp_travel_tabs ) && count( $wp_travel_tabs ) > 0 ) {
		foreach ( $wp_travel_tabs as $key => $tab ) {
			
			$tab_content = isset( $wp_travel_itinerary_tabs[ $key ]['content'] ) ? $wp_travel_itinerary_tabs[ $key ]['content'] : '';

			if ( isset( $tab['custom'] ) && 'yes' === $tab['custom'] ) {

				if ( isset( $tab['global'] ) && 'yes' === $tab['global'] ) {

					$tab_content = isset( $custom_global_tabs[$key]['content'] ) ? $custom_global_tabs[$key]['content'] : '';
				
				}
				else {

					$tab_content = isset( $custom_itinerary_tabs[$key]['content'] ) ? $custom_itinerary_tabs[$key]['content'] : '';

				}
				

			}

			$new_tabs[ $key ]['label'] = ( $tab['label'] ) ? $tab['label'] : $wp_travel_itinerary_tabs[ $key ]['label'];
			// $new_tabs[ $key ]['global_label'] = $wp_travel_itinerary_tabs[ $key ]['label'];
			$new_tabs[ $key ]['label_class'] = isset( $wp_travel_itinerary_tabs[ $key ]['label_class'] ) ? $wp_travel_itinerary_tabs[ $key ]['label_class'] : '';
			$new_tabs[ $key ]['content'] = $tab_content;
			$new_tabs[ $key ]['use_global'] = isset( $tab['use_global'] ) ? $tab['use_global'] : 'yes';
			$new_tabs[ $key ]['show_in_menu'] = isset( $tab['show_in_menu'] ) ? $tab['show_in_menu'] : 'yes';

			$new_tabs[ $key ]['custom'] = isset( $tab['custom'] ) ? $tab['custom'] : 'no';

			$new_tabs[ $key ]['global'] = isset( $tab['global'] ) ? $tab['global'] : 'no';

			// override if is global.
			// if ( ! is_admin() ) {
			// 	if ( 'yes' === $new_tabs[ $key ]['use_global'] ) {
			// 		$new_tabs[ $key ]['label'] = $wp_travel_itinerary_tabs[ $key ]['label'];
			// 		$new_tabs[ $key ]['show_in_menu'] = $wp_travel_itinerary_tabs[ $key ]['show_in_menu'];
			// 	}
			// }
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
 * @return void
 */
function wp_travel_get_default_frontend_tabs( $is_show_in_menu_query = false ) { // fixes the content filter in page builder.
	// return $is_show_in_menu_query;
	$trip_content = '';
	$trip_outline = '';
	$trip_include = '';
	$trip_exclude = '';
	$gallery_ids  = '';
	$faqs		  = array();

	if ( ! is_admin() && ! $is_show_in_menu_query ) { // fixes the content filter in page builder.
		global $wp_travel_itinerary;
		$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
		$trip_content	= $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
		$trip_outline	= $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
		$trip_include	= $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
		$trip_exclude	= $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
		$gallery_ids 	= $wp_travel_itinerary->get_gallery_ids();
		global $post;
		if ( $post ) {
			$post_id = $post->ID;
			$faqs           = wp_travel_get_faqs( $post_id );
		}
	}
	$return_tabs = $wp_travel_itinerary_tabs = array(
		'overview' 		=> array( 'label' => __( 'Overview', 'wp-travel' ), 'label_class' => '', 'content' => $trip_content, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'trip_outline' 	=> array( 'label' => __( 'Trip Outline', 'wp-travel' ), 'label_class' => '', 'content' => $trip_outline, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'trip_includes' => array( 'label' => __( 'Trip Includes', 'wp-travel' ), 'label_class' => '', 'content' => $trip_include, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'trip_excludes' => array( 'label' => __( 'Trip Excludes', 'wp-travel' ), 'label_class' => '', 'content' => $trip_exclude, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'gallery' 		=> array( 'label' => __( 'Gallery', 'wp-travel' ), 'label_class' => 'wp-travel-tab-gallery-contnet', 'content' => $gallery_ids, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'reviews' 		=> array( 'label' => __( 'Reviews', 'wp-travel' ), 'label_class' => 'wp-travel-review', 'content' => '', 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'booking' 		=> array( 'label' => __( 'Booking', 'wp-travel' ), 'label_class' => 'wp-travel-booking-form', 'content' => '', 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
		'faq' 			=> array( 'label' => __( 'FAQ', 'wp-travel' ), 'label_class' => '', 'content' => $faqs, 'use_global' => 'yes', 'show_in_menu' => 'yes' ),
	);

	return $return_tabs;
}

/**
 * Return FAQs
 *
 * @param Integer $post_id
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
			$faq[]  = array( 'question' => $question, 'answer' => $answer );
		endforeach;
	endif;
	return $faq;
}

function wp_travel_make_stat_data( $stat_datas, $show_empty = false ) {
	if ( ! $stat_datas ) {
		return;
	}
	// Split stat data.
	if ( is_array( $stat_datas ) && count( $stat_datas ) > 0 ) {
		$data = array();
		$data_label = array();
		$data_bg_color = array();
		$data_border_color = array();
		foreach ( $stat_datas as $stat_data ) {
			$data[] = isset( $stat_data['data'] ) ? $stat_data['data'] : array();
			$data_label[] = isset( $stat_data['data_label'] ) ? $stat_data['data_label'] : array();
			$data_bg_color[] = isset( $stat_data['data_bg_color'] ) ? $stat_data['data_bg_color'] : array();
			$data_border_color[] = isset( $stat_data['data_border_color'] ) ? $stat_data['data_border_color'] : array();
		}
	}

	if ( is_array( $data ) ) {
		if ( count( $data ) == 1  ) {
			$default_array_key = array_keys( $data[0] );
			$new_data[] = array_values( $data[0] );

		} else if ( count( $data ) > 1 ) {
			if ( count( $data ) > 1 ) {
				$array_with_all_keys = $data[0];
				for( $i=0; $i< count( $data ) - 1; $i++ ) {
					$next_array_key = array_keys( $data[ $i+1 ] );
					$next_array_default_val = array_fill_keys( $next_array_key, 0 );

					$array_with_all_keys = array_merge( $next_array_default_val, $array_with_all_keys );
					uksort($array_with_all_keys,function($a, $b){
						return strtotime( $a ) > strtotime( $b );
					});
				}
				$default_array_key = array_keys( $array_with_all_keys );
				$default_stat_val = null;
				if ( $show_empty ) {
					$default_stat_val = 0;
				}
				$array_key_default_val = array_fill_keys( $default_array_key, $default_stat_val );

				$new_data = array();
				for( $i=0; $i< count( $data ); $i++ ) {
					$new_array = array_merge( $array_key_default_val, $data[$i] );
					uksort($new_array,function($a, $b){
						return strtotime( $a ) > strtotime( $b );
					});
					$new_data[] = array_values($new_array);
				}

			}			
		}
		$new_return_data['stat_label'] = $default_array_key;
		$new_return_data['data'] = $new_data;
		$new_return_data['data_label'] = $data_label;
		$new_return_data['data_bg_color'] = $data_bg_color;
		$new_return_data['data_border_color'] = $data_border_color;

		return $new_return_data;
	}
}

/**
 * WP Travel Trip is trip type enable.
 * @return bool
 */
function wp_travel_is_trip_price_tax_enabled(){

	$settings = wp_travel_get_settings();

	if ( isset( $settings['trip_tax_enable'] ) && 'yes' == $settings['trip_tax_enable'] ) {

		return true;
	}

	return false;

}

/**
 * Wp Tarvel Process Trip Price Tax.
 * @param int $post_id post id.
 * @return mixed $trip_price | $tax_details.
 */
function wp_travel_process_trip_price_tax( $post_id ){

	if( ! $post_id ) {
		return 0;
	}
	$settings = wp_travel_get_settings();

	$trip_price = wp_travel_get_actual_trip_price( $post_id );

	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {

		$tax_details = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$trip_price = wp_travel_get_actual_trip_price( $post_id );
		$tax_percentage = @$settings['trip_tax_percentage'];
		
		if ( 0 == $trip_price || '' == $tax_percentage ) {

			return array( 'trip_price' => $trip_price );
		}

		if ( 'yes' == $tax_inclusive_price ) {

			$tax_details['tax_type'] = 'inclusive';
			$tax_details['tax_percentage'] = $tax_percentage;
			$actual_trip_price = ( 100 * $trip_price ) / ( 100 + $tax_percentage );
			$tax_details['trip_price'] = $actual_trip_price;
			$tax_details['actual_trip_price'] = $trip_price;

			return $tax_details;

		}
		else{

			$tax_details['tax_type'] = 'excluxive';
			$tax_details['trip_price'] = $trip_price;
			$tax_details['tax_percentage'] = $tax_percentage;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2 , '.', '' );

			return $tax_details;

		}

	}

	return array( 'trip_price' => $trip_price );

}

/**
 * Wp Tarvel Process Trip Price Tax.
 * @param int $post_id post id.
 * @return mixed $trip_price | $tax_details.
 */
function wp_travel_process_trip_price_tax_by_price( $post_id, $price ){

	if( ! $post_id || ! $price ) {
		return 0;
	}
	$settings = wp_travel_get_settings();

	$trip_price = $price;

	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {

		$tax_details = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$trip_price = $price;
		$tax_percentage = @$settings['trip_tax_percentage'];
		
		if ( 0 == $trip_price || '' == $tax_percentage ) {

			return array( 'trip_price' => $trip_price );
		}

		if ( 'yes' == $tax_inclusive_price ) {

			$tax_details['tax_type'] = 'inclusive';
			$tax_details['tax_percentage'] = $tax_percentage;
			$actual_trip_price = ( 100 * $trip_price ) / ( 100 + $tax_percentage );
			$tax_details['trip_price'] = $actual_trip_price;
			$tax_details['actual_trip_price'] = $trip_price;

			return $tax_details;

		}
		else{

			$tax_details['tax_type'] = 'excluxive';
			$tax_details['trip_price'] = $trip_price;
			$tax_details['tax_percentage'] = $tax_percentage;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2 , '.', '' );

			return $tax_details;

		}

	}

	return array( 'trip_price' => $trip_price );

}


function taxed_amount($amount, $tax_percent, $inclusive =  true){
	if($inclusive){
		return number_format( ( $amount - ( ( $amount * $tax_percent ) / 100 ) ), 2 , '.', '' );
	}
	return number_format( ( $amount + ( ( $amount * $tax_percent ) / 100 ) ), 2 , '.', '' );
}

/**
 * Retrieve page ids - cart, checkout. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function wp_travel_get_page_id( $page ) {

	$page = apply_filters( 'wp_travel_get_' . $page . '_page_id', get_option( 'wp_travel_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
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
 * @since  2.2.3
 *
 * @return string Url to cart page
 */
function wp_travel_get_cart_url() {
	return apply_filters( 'wp_travel_get_cart_url', wp_travel_get_page_permalink( 'wp-travel-cart' ) );
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
	$page_id = get_the_ID();
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
	$page_id = get_the_ID();
	$settings = wp_travel_get_settings();
	if ( isset( $settings['cart_page_id'] ) && (int) $settings['cart_page_id'] === $page_id ) {
		return true;
	}
	return false;
}

function wp_travel_is_itinerary( $post_id ) {
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

// WP Travel Pricing Varition options.

/**
 * Get default pricing variation options.
 *
 * @return array $variation_options Variation Options.
 */
function wp_travel_get_pricing_variation_options(){

	$variation_options = array(
		'adult'         => __( 'Adult', 'wp-travel' ),
		'children'      => __( 'Children', 'wp-travel' ),
		'infant'        => __( 'Infant', 'wp-travel' ),
		'couple'        => __( 'Couple', 'wp-travel' ),
		'group'         => __( 'Group', 'wp-travel' ),
		'custom'        => __( 'Custom', 'wp-travel' ),
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

	//Get Pricing variations.
	$pricing_variations = get_post_meta( $post_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

		$result = array_filter($pricing_variations, function($single) use($pricing_key){
			if ( isset( $single['price_key'] ) ){
				return $single['price_key'] === $pricing_key;
			}
		});
		return $result;
	}
	return false;

}

/**
 * Get pricing variation dates.
 *
 * @return array $available_dates Variation Options.
 */
function wp_travel_get_pricing_variation_dates( $post_id, $pricing_key ){

	if ( '' === $post_id || '' === $pricing_key ) {
		
		return false;
	
	}

	//Get Dates.
	$available_trip_dates = get_post_meta( $post_id, 'wp_travel_multiple_trip_dates', true );

	if ( is_array( $available_trip_dates ) && '' !== $available_trip_dates ) {

		$result = array_filter($available_trip_dates, function($single) use($pricing_key){
			$pricing_options = isset( $single['pricing_options'] ) ? $single['pricing_options'] : array();
			return in_array($pricing_key, $pricing_options );
		});

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

/**
 * Get pricing variation start dates.
 *
 * @return array $available_dates Variation Options.
 */
function wp_travel_get_pricing_variation_start_dates( $post_id, $pricing_key ){

	if ( '' === $post_id || '' === $pricing_key ) {
		
		return false;
	
	}

	//Get Dates.
	$trip_dates = wp_travel_get_pricing_variation_dates( $post_id, $pricing_key );

	$result = array();

	if ( is_array( $trip_dates ) && '' !== $trip_dates ) {

		foreach ( $trip_dates as $d_k => $d_v ){

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
	$email_content = wp_travel_get_template_html( 'emails/customer-new-account.php', array(
		'user_login'         => $new_customer_data['user_login'],
		'user_pass'          => $new_customer_data['user_pass'],
		'blogname'           => get_bloginfo('name'),
		'password_generated' => $password_generated,
	) );

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
	function wp_travel_format_date( $date ) {

		$date_format = get_option( 'date_format' );

		if ( ! $date_format ) :
			$date_format = 'jS M, Y';
		endif;
		
		$formated_date = esc_html( date_i18n( $date_format, strtotime( $date ) ) );

		return $formated_date;

	}


endif;
