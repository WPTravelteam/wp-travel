<?php
/**
 * Template Functions.
 *
 * @package wp-travel/inc/
 */

// Hooks.
add_action( 'wp_travel_single_trip_after_title', 'wp_travel_trip_price', 1 );
add_action( 'wp_travel_single_trip_after_title', 'wp_travel_single_excerpt', 1 );
add_action( 'wp_travel_single_trip_after_booknow', 'wp_travel_single_keywords', 1 );
add_action( 'wp_travel_single_trip_meta_list', 'wp_travel_single_location', 1 );
add_action( 'wp_travel_single_trip_after_price', 'wp_travel_single_trip_rating', 10, 2 );
add_action( 'wp_travel_single_trip_after_header', 'wp_travel_frontend_trip_facts' );
add_action( 'wp_travel_single_trip_after_header', 'wp_travel_frontend_contents', 15 );
add_action( 'wp_travel_single_trip_after_header', 'wp_travel_trip_map', 20 );
add_action( 'wp_travel_single_trip_after_header', 'wp_travel_related_itineraries', 25 );
add_filter( 'the_content', 'wp_travel_content_filter' );
add_filter( 'wp_travel_trip_tabs_output_raw', 'wp_travel_raw_output_on_tab_content', 10, 2 ); // @since 2.0.6. Need true to hide trip detail.
add_action( 'wp_travel_before_single_itinerary', 'wp_travel_wrapper_start' );
add_action( 'wp_travel_after_single_itinerary', 'wp_travel_wrapper_end' );

add_action( 'comment_post', 'wp_travel_add_comment_rating' );
add_filter( 'preprocess_comment', 'wp_travel_verify_comment_meta_data' );

// Clear transients.
add_action( 'wp_update_comment_count', 'wp_travel_clear_transients' );

add_filter( 'comments_template', 'wp_travel_comments_template_loader' );

add_filter( 'template_include', 'wp_travel_template_loader' );

add_filter( 'excerpt_length', 'wp_travel_excerpt_length', 999 );
add_filter( 'body_class', 'wp_travel_body_class', 100, 2 );

add_action( 'wp_travel_before_content_start', 'wp_travel_booking_message' );

add_action( 'the_post', 'wp_travel_setup_itinerary_data' );
// Filters HTML.
add_action( 'wp_travel_before_main_content', 'wp_travel_archive_toolbar' );
// add_action( 'parse_query', 'wp_travel_posts_filter' );
add_action( 'pre_get_posts', 'wp_travel_posts_filter' );
add_action( 'wp_travel_after_main_content', 'wp_travel_archive_wrapper_close' );
add_action( 'wp_travel_archive_listing_sidebar', 'wp_travel_archive_listing_sidebar' );
add_action( 'save_post', 'wp_travel_clear_booking_transient' );
add_filter( 'excerpt_more', 'wp_travel_excerpt_more' );
add_filter( 'wp_kses_allowed_html', 'wp_travel_wpkses_post_iframe', 10, 2 );
add_action( 'template_redirect', 'wp_travel_prevent_endpoint_indexing' );

add_action( 'wp_travel_booking_default_princing_list', 'wp_travel_booking_default_princing_list_content' ); // if any issue exists in default listing, please update in fixed departure listing.
add_action( 'wp_travel_booking_fixed_departure_list', 'wp_travel_booking_fixed_departure_list_content' );
add_filter( 'get_header_image_tag', 'wp_travel_get_header_image_tag', 10 );
add_filter( 'jetpack_relatedposts_filter_options', 'wp_travel_remove_jetpack_related_posts' );

/**
 * Return template.
 *
 * @param  String $template_name Path of template.
 * @param  array  $args arguments.
 * @return Mixed
 */
function wp_travel_get_template( $template_name, $args = array() ) {
	$template_path = apply_filters( 'wp_travel_template_path', 'wp-travel/' );
	$default_path  = sprintf( '%s/templates/', plugin_dir_path( dirname( __FILE__ ) ) );

	// Look templates in theme first.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}
	if ( file_exists( $template ) ) {
		return $template;
	}
	return false;
}

/**
 * Like wp_travel_get_template, but returns the HTML instead of outputting.
 *
 * @see wp_travel_get_template
 * @since 1.3.7
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 *
 * @return string
 */
function wp_travel_get_template_html( $template_name, $args = array() ) {
	ob_start();
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}
	include wp_travel_get_template( $template_name );
	return ob_get_clean();
}

/**
 * Get Template Part.
 *
 * @param  String $slug Name of slug.
 * @param  string $name Name of file / template.
 */
function wp_travel_get_template_part( $slug, $name = '' ) {
	$template  = '';
	$file_name = ( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
	if ( $name ) {
		$template = wp_travel_get_template( $file_name );
	}
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Load Template
 *
 * @param  String $path Path of template.
 * @param  array  $args Template arguments.
 */
function wp_travel_load_template( $path, $args = array() ) {
	$template = wp_travel_get_template( $path, $args );
	if ( $template ) {
		include $template;
	}
}

/**
 * WP Travel Single Page Content.
 *
 * @param  String $content HTML content.
 * @return String
 */
function wp_travel_content_filter( $content ) {

	if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
		return $content;
	}
	global $post;

	$settings = wp_travel_get_settings();

	ob_start();
	do_action( 'wp_travel_before_trip_details', $post, $settings );
	?>
	<div class="wp-travel-trip-details">
		<?php do_action( 'wp_travel_trip_details', $post, $settings ); ?>
	</div>
	<?php
	do_action( 'wp_travel_after_trip_details', $post, $settings );
	$content .= ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Wrapper Start.
 */
function wp_travel_wrapper_start() {
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$template = get_option( 'template' );

	switch ( $template ) {
		case 'twentyeleven':
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
			break;
		case 'twentytwelve':
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
			break;
		case 'twentythirteen':
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
			break;
		case 'twentyfourteen':
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfWSC">';
			break;
		case 'twentyfifteen':
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15WSC">';
			break;
		case 'twentysixteen':
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
			break;
		case 'twentyseventeen':
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><div id="main" class="site-main">';
			break;
		default:
			echo '<div id="wp-travel-content" class="wp-travel-content container" role="main">';
			break;
	}
}

/**
 * Wrapper Ends.
 */
function wp_travel_wrapper_end() {
	$template = get_option( 'template' );

	switch ( $template ) {
		case 'twentyeleven':
			echo '</div></div>';
			break;
		case 'twentytwelve':
			echo '</div></div>';
			break;
		case 'twentythirteen':
			echo '</div></div>';
			break;
		case 'twentyfourteen':
			echo '</div></div></div>';
			get_sidebar( 'content' );
			break;
		case 'twentyfifteen':
			echo '</div></div>';
			break;
		case 'twentysixteen':
			echo '</div></main>';
			break;
		case 'twentyseventeen':
			echo '</div></div></div>';
			break;
		default:
			echo '</div>';
			break;
	}
}

/**
 * Add html of trip price.
 *
 * @param int  $trip_id ID for current trip.
 * @param bool $hide_rating Boolean value to show/hide rating.
 */
function wp_travel_trip_price( $trip_id, $hide_rating = false ) {
	$settings = wp_travel_get_settings();

	$trip_price = wp_travel_get_price( $trip_id );
	$regular_price = wp_travel_get_price( $trip_id, true );
	$min_price_key = wp_travel_get_min_price_key( $trip_id ); // Need to deprecated.

	$enable_sale = wp_travel_is_enable_sale_price( $trip_id, true );
	$price_per_text  = wp_travel_get_price_per_text( $trip_id, $min_price_key );

	$hide_price_per = wp_travel_hide_price_per_field( $trip_id );

	?>

	<div class="wp-detail-review-wrap">
		<?php do_action( 'wp_travel_single_before_trip_price', $trip_id, $hide_rating ); ?>
		<div class="wp-travel-trip-detail">
			<?php if ( $trip_price ) : ?>
				<div class="trip-price" >

				<?php if ( $enable_sale ) : ?>
					<del>
						<span><?php echo wp_travel_get_formated_price_currency( $regular_price, true ); ?></span>
					</del>
				<?php endif; ?>
					<span class="person-count">
						<ins>
							<span><?php echo wp_travel_get_formated_price_currency( $trip_price ); ?></span>
						</ins>
						<?php if ( ! empty( $price_per_text ) && ! $hide_price_per ) : ?>
							/<?php echo esc_html( $price_per_text ); ?>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
		</div>
		<?php
			wp_travel_do_deprecated_action( 'wp_travel_single_after_trip_price', array( $trip_id, $hide_rating ), '2.0.4', 'wp_travel_single_trip_after_price' );  // deprecated in 2.0.4
			do_action( 'wp_travel_single_trip_after_price', $trip_id, $hide_rating );
		?>
	</div>

	<?php
}

/**
 * Add html of Rating.
 *
 * @param int  $post_id ID for current post.
 * @param bool $hide_rating Flag to sho hide rating.
 */
function wp_travel_single_trip_rating( $post_id, $hide_rating = false ) {
	if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
		return;
	}
	if ( ! $post_id ) {
		return;
	}
	if ( $hide_rating ) {
		return;
	}
	if ( ! wp_travel_tab_show_in_menu( 'reviews' ) ) {
		return;
	}
	$average_rating = wp_travel_get_average_rating( $post_id );
	?>
	<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">
		<a>
			<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
			</span>
		</a>

	</div>
	<?php
}

/**
 * Add html of Rating.
 *
 * @param int $post_id ID for current post.
 */
function wp_travel_trip_rating( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$average_rating = wp_travel_get_average_rating( $post_id );
	?>
	<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">
		<a>
			<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
			</span>
		</a>

	</div>
	<?php
}


/**
 * Add html for excerpt and booking button.
 *
 * @param int $post_id ID of current post.
 *
 * @since Modified in 2.0.7
 */
function wp_travel_single_excerpt( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
	$strings = wp_travel_get_strings();
	// Get Settings
	$settings = wp_travel_get_settings();

	$enquery_global_setting = isset( $settings['enable_trip_enquiry_option'] ) ? $settings['enable_trip_enquiry_option'] : 'yes';

	$global_enquiry_option = get_post_meta( $post_id, 'wp_travel_use_global_trip_enquiry_option', true );

	if ( '' === $global_enquiry_option ) {
		$global_enquiry_option = 'yes';
	}
	if ( 'yes' == $global_enquiry_option ) {

		$enable_enquiry = $enquery_global_setting;

	} else {
		$enable_enquiry = get_post_meta( $post_id, 'wp_travel_enable_trip_enquiry_option', true );
	}

	// Strings
	$trip_type_text       = isset( $strings['trip_type'] ) ? $strings['trip_type'] : __( 'Trip Type', 'wp-travel' );
	$activities_text      = isset( $strings['activities'] ) ? $strings['activities'] : __( 'Activities', 'wp-travel' );
	$group_size_text      = isset( $strings['group_size'] ) ? $strings['group_size'] : __( 'Group size', 'wp-travel' );
	$reviews_text         = isset( $strings['reviews'] ) ? $strings['reviews'] : __( 'Reviews', 'wp-travel' );

	$wp_travel_itinerary = new WP_Travel_Itinerary();
	?>
	<div class="trip-short-desc">
		<?php the_excerpt(); ?>
	</div>
	<div class="wp-travel-trip-meta-info">
		  <ul>
			<?php
			wp_travel_do_deprecated_action( 'wp_travel_single_itinerary_before_trip_meta_list', array( $post_id ), '2.0.4', 'wp_travel_single_trip_meta_list' );  // @since 1.0.4 and deprecated in 2.0.4
			?>
			  <li>
				   <div class="travel-info">
					<strong class="title"><?php echo esc_html( $trip_type_text ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">

					<?php
					$trip_types_list = $wp_travel_itinerary->get_trip_types_list();
					if ( $trip_types_list ) {
						echo wp_kses( $trip_types_list, wp_travel_allowed_html( array( 'a' ) ) );
					} else {
						echo esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', __( 'No trip type', 'wp-travel' ) ) );
					}
					?>
					</span>
				</div>
			   </li>
			   <li>
				<div class="travel-info">
					<strong class="title"><?php echo esc_html( $activities_text ); ?></strong>
				</div>
			   <div class="travel-info">
					<span class="value">

					<?php
					$activity_list = $wp_travel_itinerary->get_activities_list();
					if ( $activity_list ) {
						echo wp_kses( $activity_list, wp_travel_allowed_html( array( 'a' ) ) );
					} else {
						echo esc_html( apply_filters( 'wp_travel_default_no_activity_text', __( 'No Activities', 'wp-travel' ) ) );
					}
					?>
					</span>
				</div>
			   </li>
			   <li>
				   <div class="travel-info">
					<strong class="title"><?php echo esc_html( $group_size_text ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">
						<?php
						if ( $group_size = wp_travel_get_group_size( $post_id ) ) {
							printf( apply_filters( 'wp_travel_template_group_size_text', __( '%d pax', 'wp-travel' ) ), esc_html( $group_size ) );
						} else {
							echo esc_html( apply_filters( 'wp_travel_default_group_size_text', __( 'No size limit', 'wp-travel' ) ) );
						}
						?>
					</span>
				</div>
			   </li>
			<?php

			if ( wp_travel_tab_show_in_menu( 'reviews' ) && comments_open() ) :
				?>
			   <li>
				   <div class="travel-info">
					<strong class="title"><?php echo esc_html( $reviews_text ); ?></strong>
				</div>
				<div class="travel-info">
				<span class="value">
				<?php
					$count = (int) get_comments_number();
					echo '<a href="javascript:void(0)" class="wp-travel-count-info">';
					printf( _n( '%s review', '%s reviews', $count, 'wp-travel' ), $count );
					echo '</a>';
				?>
				</span>
				</div>
			   </li>
			<?php endif; ?>
			<?php
			wp_travel_do_deprecated_action( 'wp_travel_single_itinerary_after_trip_meta_list', array( $post_id ), '2.0.4', 'wp_travel_single_trip_meta_list' );  // @since 1.0.4 and deprecated in 2.0.4
			do_action( 'wp_travel_single_trip_meta_list', $post_id );
			?>
		  </ul>
	</div>

	  <div class="booking-form">
		<div class="wp-travel-booking-wrapper">
			<?php
			$trip_enquiry_text = isset( $strings['featured_trip_enquiry'] ) ? $strings['featured_trip_enquiry'] : __( 'Trip Enquiry', 'wp-travel' );
			if ( wp_travel_tab_show_in_menu( 'booking' ) ) :
				$book_now_text     = isset( $strings['featured_book_now'] ) ? $strings['featured_book_now'] : __( 'Book Now', 'wp-travel' );
				?>
				<button class="wp-travel-booknow-btn"><?php echo esc_html( apply_filters( 'wp_travel_template_book_now_text', $book_now_text ) ); ?></button>
			<?php endif; ?>
			<?php if ( 'yes' == $enable_enquiry ) : ?>
				<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
					<span class="wp-travel-booking-enquiry">
						<span class="dashicons dashicons-editor-help"></span>
						<span>
							<?php echo esc_attr( apply_filters( 'wp_travel_trip_enquiry_popup_link_text', $trip_enquiry_text ) ); ?>
						</span>
					</span>
				</a>
				<?php
			endif;
			?>
		</div>
	</div>
		<?php
		if ( 'yes' == $enable_enquiry ) :
			wp_travel_get_enquiries_form();
			endif;
		?>
	<?php
	wp_travel_do_deprecated_action( 'wp_travel_single_after_booknow', array( $post_id ), '2.0.4', 'wp_travel_single_trip_after_booknow' );  // @since 1.0.4 and deprecated in 2.0.4
	do_action( 'wp_travel_single_trip_after_booknow', $post_id ); // @since 2.0.4

}

/**
 * Add html for Keywords.
 *
 * @param int $post_id ID of current post.
 */
function wp_travel_single_keywords( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$terms = get_the_terms( $post_id, 'travel_keywords' );
	if ( is_array( $terms ) && count( $terms ) > 0 ) :
		?>
		<div class="wp-travel-keywords">
			<span class="label"><?php esc_html_e( 'Keywords : ', 'wp-travel' ); ?></span>
			<?php
			$i = 0;
			foreach ( $terms as $term ) :
				if ( $i > 0 ) :
					?>
					 ,
					<?php
				endif;
				?>
				<span class="wp-travel-keyword"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></span>
				<?php
				$i++;
			endforeach;
			?>
		</div>
		<?php
	endif;
	global $wp_travel_itinerary;
	if ( is_singular( WP_TRAVEL_POST_TYPE ) ) :
		?>
		<div class="wp-travel-trip-code"><span><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?> :</span><code><?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?></code></div>
		<?php
	endif;

}
/**
 * Add html for Keywords.
 *
 * @param int $post_id ID of current post.
 */
function wp_travel_single_location( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	// Get Strings
	$strings = wp_travel_get_strings();

	$terms = get_the_terms( $post_id, 'travel_locations' );

	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	$trip_duration       = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	// Strings
	$locations_text       = isset( $strings['locations'] ) ? $strings['locations'] : __( 'Locations', 'wp-travel' );
	$fixed_departure_text = isset( $strings['fixed_departure'] ) ? $strings['fixed_departure'] : __( 'Fixed departure', 'wp-travel' );
	$trip_duration_text   = isset( $strings['trip_duration'] ) ? $strings['trip_duration'] : __( 'Trip duration', 'wp-travel' );

	if ( is_array( $terms ) && count( $terms ) > 0 ) :
		?>
		<li class="no-border">
			<div class="travel-info">
				<strong class="title"><?php echo esc_html( $locations_text ); ?></strong>
			</div>
			<div class="travel-info">
				<span class="value">
					<?php
					$i = 0;
					foreach ( $terms as $term ) :
						if ( $i > 0 ) :
							?>
							 ,
							<?php
						endif;
						?>
						<span class="wp-travel-locations"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></span>
						<?php
						$i++;
					endforeach;
					?>
				</span>
			</div>
		</li>
	<?php endif; ?>
	<?php
	if ( 'yes' === $fixed_departure ) :
		if ( $dates = wp_travel_get_fixed_departure_date( $post_id ) ) {
			?>
				<li>
					<div class="travel-info">
						<strong class="title"><?php echo esc_html( $fixed_departure_text ); ?></strong>
					</div>
					<div class="travel-info">
						<span class="value">
						<?php echo $dates; ?>
						</span>
					</div>
				</li>
				<?php
		}
		?>

	<?php else : ?>
		<?php if ( $trip_duration || $trip_duration_night ) : ?>
			<li>
				<div class="travel-info">
					<strong class="title"><?php echo esc_html( $trip_duration_text ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">
						<?php printf( __( '%1$s Day(s) %2$s Night(s)', 'wp-travel' ), $trip_duration, $trip_duration_night ); ?>
					</span>
				</div>
			</li>
		<?php endif; ?>
		<?php
	endif;
}

/**
 * wp_travel_frontend_trip_facts Frontend facts content.
 *
 * @since 1.3.2
 */
function wp_travel_frontend_trip_facts( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
	$settings = wp_travel_get_settings();

	if ( ! isset( $settings['wp_travel_trip_facts_settings'] ) ) {
		return '';
	}
	if ( isset( $settings['wp_travel_trip_facts_settings'] ) ) {

		if ( ! count( $settings['wp_travel_trip_facts_settings'] ) > 0 ) {

			return '';
		}
	}

	$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';

	if ( 'no' === $wp_travel_trip_facts_enable ) {
		return;
	}

	$wp_travel_trip_facts = get_post_meta( $post_id, 'wp_travel_trip_facts', true );

	if ( is_string( $wp_travel_trip_facts ) && '' != $wp_travel_trip_facts ) {

		$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts, true );
	}

	if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {
		?>
		<!-- TRIP FACTS -->
		<div class="tour-info">
			<div class="tour-info-box clearfix">
				<div class="tour-info-column clearfix">
					<?php foreach ( $wp_travel_trip_facts as $key => $trip_fact ) : ?>
						<?php

							$icon = array_filter(
								$settings['wp_travel_trip_facts_settings'],
								function( $setting ) use ( $trip_fact ) {

									return $setting['name'] === $trip_fact['label'];
								}
							);

						foreach ( $icon as $key => $ico ) {

							$icon = $ico['icon'];
						}
						if ( isset( $trip_fact['value'] ) ) :
							?>
							<span class="tour-info-item tour-info-type">
								<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								<strong><?php echo esc_html( $trip_fact['label'] ); ?></strong>:
								<?php
								if ( $trip_fact['type'] === 'multiple' ) {
									$count = count( $trip_fact['value'] );
									$i     = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										echo esc_html( $val );
										if ( $count > 1 && $i !== $count ) {
											echo esc_html( ',', 'wp-travel' );
										}
										$i++;
									}
								} else {
									echo esc_html( $trip_fact['value'] );
								}

								?>
							</span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<!-- TRIP FACTS END -->
		<?php
	}
}

/**
 * Single Page Details
 *
 * @param Int $post_id
 * @return void
 */
function wp_travel_frontend_contents( $post_id ) {
	global $wp_travel_itinerary;
	$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
	$trip_content             = $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
	$trip_outline             = $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
	$trip_include             = $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
	$trip_exclude             = $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
	$gallery_ids              = $wp_travel_itinerary->get_gallery_ids();

	$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();

	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );

	$trip_start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$trip_end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );
	$trip_price      = wp_travel_get_trip_price( $post_id );
	$enable_sale     = wp_travel_is_enable_sale_price( $post_id );

	$trip_duration       = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$settings      = wp_travel_get_settings();
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$price_per_text  = wp_travel_get_price_per_text( $post_id );
	$sale_price      = wp_travel_get_trip_sale_price( $post_id );
	?>
	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper">
		<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
			<ul class="wp-travel tab-list resp-tabs-list ">
				<?php
				$index = 1;
				foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) :
					?>
					<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php $tab_label = $tab_info['label']; ?>
					<li class="wp-travel-ert <?php echo esc_attr( $tab_key ); ?> <?php echo esc_attr( $tab_info['label_class'] ); ?> tab-<?php echo esc_attr( $index ); ?>" data-tab="tab-<?php echo esc_attr( $index ); ?>-cont"><?php echo esc_attr( $tab_label ); ?></li>
					<?php
					$index++;
				endforeach;
				?>
			</ul>
		<div class="resp-tabs-container">
			<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
				<?php $index = 1; ?>
				<?php foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) : ?>
					<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php
					switch ( $tab_key ) {

						case 'reviews':
							?>
							<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
								<?php comments_template(); ?>
							</div>
							<?php
							break;
						case 'booking':
							$booking_template = wp_travel_get_template( 'content-pricing-options.php' );
							load_template( $booking_template );

							break;
						case 'faq':
							?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<div class="panel-group" id="accordion">
							<?php
							$faqs = wp_travel_get_faqs( $post_id );
							if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
								?>
								<div class="wp-collapse-open clearfix">
									<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
									<a href="#" class="close-all-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
								</div>
								<?php foreach ( $faqs as $k => $faq ) : ?>
								<div class="panel panel-default">
								<div class="panel-heading">
								<h4 class="panel-title">
									<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k + 1 ); ?>">
									<?php echo esc_html( $faq['question'] ); ?>
									<span class="collapse-icon"></span>
									</a>
								</h4>
								</div>
								<div id="collapse<?php echo esc_attr( $k + 1 ); ?>" class="panel-collapse collapse">
								<div class="panel-body">
									<?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
								</div>
								</div>
							</div>
									<?php
								endforeach;
							} else {
								?>
								<div class="while-empty">
									<p class="wp-travel-no-detail-found-msg" >
										<?php esc_html_e( 'No Details Found', 'wp-travel' ); ?>
									</p>
								</div>
							<?php } ?>
							</div>
						</div>
							<?php
							break;
						case 'trip_outline':
							?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<?php
								echo wp_kses_post( $tab_info['content'] );

								$itinerary_list_template = wp_travel_get_template( 'itineraries-list.php' );
								load_template( $itinerary_list_template );
							?>
						</div>
							<?php
							break;
						default:
							?>
							<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
								<?php
								if ( apply_filters( 'wp_travel_trip_tabs_output_raw', false, $tab_key ) ) {

									echo do_shortcode( $tab_info['content'] );

								} else {

									echo apply_filters( 'the_content', $tab_info['content'] );
								}

								?>
							</div>
						<?php break; ?>
					<?php } ?>
					<?php
					$index++;
				endforeach;
				?>
			<?php endif; ?>
		</div>
		<?php endif; ?>

	</div>
	<?php
}

function wp_travel_trip_map( $post_id ) {
	global $wp_travel_itinerary;
	if ( ! $wp_travel_itinerary->get_location() ) {
		return;
	}
	$get_maps        = wp_travel_get_maps();
	$current_map     = $get_maps['selected'];
	$show_google_map = ( 'google-map' === $current_map ) ? true : false;
	$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map );
	$settings        = wp_travel_get_settings();
	$api_key         = '';
	if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
		$api_key = $settings['google_map_api_key'];
	}

	$map_data = get_wp_travel_map_data();
	$lat      = isset( $map_data['lat'] ) ? $map_data['lat'] : '';
	$lng      = isset( $map_data['lng'] ) ? $map_data['lng'] : '';

	if ( '' != $api_key && $show_google_map && ! empty( $lat ) && ! empty( $lng ) ) {
		?>
		<div class="wp-travel-map">
			<div id="wp-travel-map" style="width:100%;height:300px"></div>
		</div>
		<?php
	}
}

/**
 * Display Related Product.
 *
 * @param Number $post_id Post ID.
 * @return HTML
 */
function wp_travel_related_itineraries( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	wp_travel_get_related_post( $post_id );
}

function wp_travel_add_comment_rating( $comment_id ) {
	if ( isset( $_POST['wp_travel_rate_val'] ) && WP_TRAVEL_POST_TYPE === get_post_type( $_POST['comment_post_ID'] ) ) {
		if ( ! $_POST['wp_travel_rate_val'] || $_POST['wp_travel_rate_val'] > 5 || $_POST['wp_travel_rate_val'] < 0 ) {
			return;
		}
		add_comment_meta( $comment_id, '_wp_travel_rating', (int) esc_attr( $_POST['wp_travel_rate_val'] ), true );
	}
}

function wp_travel_clear_transients( $post_id ) {
	delete_post_meta( $post_id, '_wpt_average_rating' );
	delete_post_meta( $post_id, '_wpt_rating_count' );
	delete_post_meta( $post_id, '_wpt_review_count' );
}

function wp_travel_verify_comment_meta_data( $commentdata ) {

	if (
	! is_admin()
	&& WP_TRAVEL_POST_TYPE === get_post_type( sanitize_text_field( $_POST['comment_post_ID'] ) )
	&& 1 > sanitize_text_field( $_POST['wp_travel_rate_val'] )
	&& '' === $commentdata['comment_type']
	) {
		wp_die( 'Please rate. <br><a href="javascript:history.go(-1);">Back </a>' );
		exit;
	}
	return $commentdata;
}

/**
 * Get the total amount (COUNT) of reviews.
 *
 * @param   Number $post_id Post ID.
 * @since 1.0.0 / Modified 1.6.7
 * @return int The total number of trips reviews
 */
function wp_travel_get_review_count( $post_id = null ) {
	global $wpdb, $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	}
	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $post_id, '_wpt_review_count' ) ) {
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT COUNT(*) FROM $wpdb->comments
			WHERE comment_parent = 0
			AND comment_post_ID = %d
			AND comment_approved = '1'
		",
				$post_id
			)
		);

		update_post_meta( $post_id, '_wpt_review_count', $count );
	} else {
		$count = get_post_meta( $post_id, '_wpt_review_count', true );
	}

	return apply_filters( 'wp_travel_review_count', $count, $post );
}

/**
 * Get the average rating of product. This is calculated once and stored in postmeta.
 *
 * @param Number $post_id   Post ID.
 *
 * @return string
 */
function wp_travel_get_average_rating( $post_id = null ) {
	global $wpdb, $post;

	if ( ! $post_id ) {
		$post_id = $post->ID;
	}

	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $post_id, '_wpt_average_rating' ) ) {

		if ( $count = wp_travel_get_rating_count() ) {
			$ratings = $wpdb->get_var(
				$wpdb->prepare(
					"
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = '_wp_travel_rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			",
					$post_id
				)
			);
			$average = number_format( $ratings / $count, 2, '.', '' );
		} else {
			$average = 0;
		}
		update_post_meta( $post_id, '_wpt_average_rating', $average );
	} else {

		$average = get_post_meta( $post_id, '_wpt_average_rating', true );
	}

	return (string) floatval( $average );
}

/**
 * Get the total amount (COUNT) of ratings.
 *
 * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
 * @return int
 */
function wp_travel_get_rating_count( $value = null ) {
	global $wpdb, $post;

	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $post->ID, '_wpt_rating_count' ) ) {

		$counts     = array();
		$raw_counts = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = '_wp_travel_rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		",
				$post->ID
			)
		);

		foreach ( $raw_counts as $count ) {
			$counts[ $count->meta_value ] = $count->meta_value_count;
		}
		update_post_meta( $post->ID, '_wpt_rating_count', $counts );
	} else {

		$counts = get_post_meta( $post->ID, '_wpt_rating_count', true );
	}

	if ( is_null( $value ) ) {
		return array_sum( $counts );
	} else {
		return isset( $counts[ $value ] ) ? $counts[ $value ] : 0;
	}
}



function wp_travel_comments_template_loader( $template ) {
	if ( WP_TRAVEL_POST_TYPE !== get_post_type() ) {
		return $template;
	}

	$check_dirs = array(
		trailingslashit( get_stylesheet_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
		trailingslashit( get_template_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
		trailingslashit( get_stylesheet_directory() ),
		trailingslashit( get_template_directory() ),
		trailingslashit( WP_TRAVEL_PLUGIN_PATH ) . 'templates/',
	);
	foreach ( $check_dirs as $dir ) {
		if ( file_exists( trailingslashit( $dir ) . 'single-wp-travel-reviews.php' ) ) {
			return trailingslashit( $dir ) . 'single-wp-travel-reviews.php';
		}
	}
}

/**
 * Load WP Travel Template file
 *
 * @param [type] $template Name of template.
 * @return String
 */
function wp_travel_template_loader( $template ) {

	// Load template for post archive / taxonomy archive.
	if ( is_post_type_archive( WP_TRAVEL_POST_TYPE ) || is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) {
		$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
			trailingslashit( get_template_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WP_TRAVEL_PLUGIN_PATH ) . 'templates/',
		);

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'archive-itineraries.php' ) ) {
				return trailingslashit( $dir ) . 'archive-itineraries.php';
			}
		}
	}

	return $template;
}

/**
 * Return excerpt length for archive pages.
 *
 * @param  int $length word length of excerpt.
 * @return int return word length
 */
function wp_travel_excerpt_length( $length ) {
	if ( get_post_type() !== WP_TRAVEL_POST_TYPE ) {
		return $length;
	}

	return 23;
}

/**
 * Pagination for archive pages
 *
 * @param  Int    $range range.
 * @param  String $pages Number of pages.
 * @return HTML
 */
function wp_travel_pagination( $range = 2, $pages = '' ) {
	if ( get_post_type() !== WP_TRAVEL_POST_TYPE ) {
		return;
	}

	$showitems = ( $range * 2 ) + 1;

	global $paged;
	if ( empty( $paged ) ) {
		$paged = 1;
	}

	if ( '' == $pages ) {
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if ( ! $pages ) {
			$pages = 1;
		}
	}
	$pagination = '';
	if ( 1 != $pages ) {
		$pagination .= '<nav class="wp-travel-navigation navigation wp-paging-navigation">';
		$pagination .= '<ul class="wp-page-numbers">';
		// if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
		// echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
		// }
		if ( $paged > 1 && $showitems < $pages ) {
			$pagination .= sprintf( '<li><a class="prev wp-page-numbers" href="%s">&laquo; </a></li>', get_pagenum_link( $paged - 1 ) );
		}

		for ( $i = 1; $i <= $pages; $i++ ) {
			if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
				if ( $paged == $i ) {

					$pagination .= sprintf( '<li><a class="wp-page-numbers current" href="javascript:void(0)">%d</a></li>', $i );
				} else {
					$pagination .= sprintf( '<li><a class="wp-page-numbers" href="%s">%d</a></li>', get_pagenum_link( $i ), $i );
				}
			}
		}

		if ( $paged < $pages && $showitems < $pages ) {
			$pagination .= sprintf( '<li><a class="next wp-page-numbers" href="%s">&raquo; </a></li>', get_pagenum_link( $paged + 1 ) );
		}

		// if ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) {
		// echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
		// }
		$pagination .= "</nav>\n";
		echo $pagination;
	}
}

/**
 * Offer HTML
 *
 * @param  int $post_id ID of current Trip Post.
 * @return HTML
 */
function wp_travel_save_offer( $post_id ) {
	if ( get_post_type() !== WP_TRAVEL_POST_TYPE ) {
		return;
	}

	if ( ! $post_id ) {
		return;
	}
	$enable_sale = wp_travel_is_enable_sale_price( $post_id );

	if ( ! $enable_sale ) {
		return;
	}

	$trip_price = wp_travel_get_trip_price( $post_id );
	$sale_price = wp_travel_get_trip_sale_price( $post_id );

	if ( $sale_price > $trip_price ) {
		$save = ( 1 - ( $trip_price / $sale_price ) ) * 100;
		$save = number_format( $save, 2, '.', ',' );
		?>
		<div class="wp-travel-savings"><?php printf( 'save <span>%s&#37;</span>', $save ); ?></div>
		<?php
	}
}

/**
 * Filter Body Class.
 *
 * @param  array  $classes [description].
 * @param  String $class   [description].
 * @return array
 */
function wp_travel_body_class( $classes, $class ) {

	if ( is_active_sidebar( 'sidebar-1' ) && is_singular( WP_TRAVEL_POST_TYPE ) ) {
		// If the has-sidebar class is in the $classes array, do some stuff.
		if ( in_array( 'has-sidebar', $classes ) ) {
			// Remove the class.
			unset( $classes[ array_search( 'has-sidebar', $classes ) ] );
		}
	}
	// Give me my new, modified $classes.
	return $classes;
}

/**
 * Booking Booked Message.
 *
 * @return String
 */
function wp_travel_booking_message() {
	if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
		return;
	}
	if ( isset( $_GET['booked'] ) && 1 == $_GET['booked'] ) :
		?>
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>
		<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo apply_filters( 'wp_travel_booked_message', __( "We've received your booking details. We'll contact you soon.", 'wp-travel' ) ); ?></p>

	<?php elseif ( isset( $_GET['booked'] ) && 'false' == $_GET['booked'] ) : ?>
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>

		<?php

			$err_msg = __( 'Your Item has been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' )

		?>

		<p class="col-xs-12 wp-travel-notice-danger wp-travel-notice"><?php echo apply_filters( 'wp_travel_booked_message', $err_msg ); ?></p>
		<?php
	endif;

	wp_travel_print_notices();
}

/**
 * Return No of Pax for current Trip.
 *
 * @param  int $post_id ID of current trip post.
 * @return String.
 */
function wp_travel_get_group_size( $post_id = null ) {
	if ( ! is_null( $post_id ) ) {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $post_id ) );
	} else {
		global $post;
		$wp_travel_itinerary = new WP_Travel_Itinerary( $post );
	}

	$group_size = $wp_travel_itinerary->get_group_size();

	if ( $group_size ) {
		return sprintf( apply_filters( 'wp_travel_template_group_size_text', __( '%d pax', 'wp-travel' ) ), $group_size );
	}

	return apply_filters( 'wp_travel_default_group_size_text', esc_html__( 'No Size Limit', 'wp-travel' ) );
}


/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post Post object or post id.
 * @return WP_Travel_Itinerary
 */
function wp_travel_setup_itinerary_data( $post ) {
	unset( $GLOBALS['wp_travel_itinerary'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}
	if ( empty( $post->post_type ) || WP_TRAVEL_POST_TYPE !== $post->post_type ) {
		return;
	}
	$GLOBALS['wp_travel_itinerary'] = new WP_Travel_Itinerary( $post );

	return $GLOBALS['wp_travel_itinerary'];
}

/**
 * WP Travel Filter By.
 *
 * @return void
 */
function wp_travel_archive_filter_by() {
	if ( ! is_wp_travel_archive_page() ) {
		return;
	}
	?>
	<div class="wp-travel-post-filter clearfix">
		<div class="wp-travel-filter-by-heading">
			<h4><?php esc_html_e( 'Filter By', 'wp-travel' ); ?></h4>
		</div>

		<?php do_action( 'wp_travel_before_post_filter' ); ?>
		<input type="hidden" id="wp-travel-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>" />
		<?php
			$price    = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
			$type     = ! empty( $_GET['itinerary_types'] ) ? $_GET['itinerary_types'] : '';
			$location = ! empty( $_GET['travel_locations'] ) ? $_GET['travel_locations'] : '';
		?>

		<?php $enable_filter_price = apply_filters( 'wp_travel_post_filter_by_price', true ); ?>
		<?php if ( $enable_filter_price ) : ?>
			<div class="wp-toolbar-filter-field wt-filter-by-price">
				<p><?php esc_html_e( 'Price', 'wp-travel' ); ?></p>
				<select name="price" class="wp_travel_input_filters price">
					<option value="">--</option>
					<option value="low_high" <?php selected( $price, 'low_high' ); ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ); ?></option>
					<option value="high_low" <?php selected( $price, 'high_low' ); ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ); ?></option>
				</select>
			</div>
		<?php endif; ?>
		<div class="wp-toolbar-filter-field wt-filter-by-itinerary-types">
			<p><?php esc_html_e( 'Trip Type', 'wp-travel' ); ?></p>
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'          => 'itinerary_types',
					'name'              => 'itinerary_types',
					'class'             => 'wp_travel_input_filters type',
					'show_option_none'  => '--',
					'option_none_value' => '',
					'selected'          => $type,
					'value_field'       => 'slug',
				)
			);
			?>
		</div>
		<div class="wp-toolbar-filter-field wt-filter-by-travel-locations">
			<p><?php esc_html_e( 'Location', 'wp-travel' ); ?></p>
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'          => 'travel_locations',
					'name'              => 'travel_locations',
					'class'             => 'wp_travel_input_filters location',
					'show_option_none'  => '--',
					'option_none_value' => '',
					'selected'          => $location,
					'value_field'       => 'slug',
				)
			);
			?>
		</div>
		<div class="wp-travel-filter-button">
			<button class="btn-wp-travel-filter"><?php esc_html_e( 'Show', 'wp-travel' ); ?></button>
		</div>
		<?php do_action( 'wp_travel_after_post_filter' ); ?>
	</div>
	<?php
}

/**
 * Check if the current page is WP Travel page or not.
 *
 * @since 1.0.4
 * @return boolean
 */
function is_wp_travel_archive_page() {

	if ( ( is_post_type_archive( WP_TRAVEL_POST_TYPE ) || is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) && ! is_search() ) {
		return true;
	}
	return false;
}

/**
 * Archive page toolbar.
 *
 * @since 1.0.4
 * @return void
 */
function wp_travel_archive_toolbar() {
	$view_mode = wp_travel_get_archive_view_mode();
	if ( ( is_wp_travel_archive_page() || is_search() ) && ! is_admin() ) :
		?>
		<?php if ( is_wp_travel_archive_page() ) : ?>
	<div class="wp-travel-toolbar clearfix">
		<div class="wp-toolbar-content wp-toolbar-left">
			<?php wp_travel_archive_filter_by(); ?>
		</div>
		<div class="wp-toolbar-content wp-toolbar-right">
			<?php
			$current_url = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			?>
			<ul class="wp-travel-view-mode-lists">
				<li class="wp-travel-view-mode <?php echo ( 'grid' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="grid" ><a href="<?php echo esc_url( add_query_arg( 'view_mode', 'grid', $current_url ) ); ?>"><i class="dashicons dashicons-grid-view"></i></a></li>
				<li class="wp-travel-view-mode <?php echo ( 'list' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="list" ><a href="<?php echo esc_url( add_query_arg( 'view_mode', 'list', $current_url ) ); ?>"><i class="dashicons dashicons-list-view"></i></a></li>
			</ul>
		</div>
	</div>
	<?php endif; ?>
		<?php

		$archive_sidebar_class = '';

		if ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
			$archive_sidebar_class = 'wp-travel-trips-has-sidebar';
		}

		?>
	<div class="wp-travel-archive-content <?php echo esc_attr( $archive_sidebar_class ); ?>">
		<?php if ( 'grid' === $view_mode ) : ?>
			<?php $col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row', '3' ); ?>
			<?php
			if ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
				$col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row', '2' );
			}
			?>
			<div class="wp-travel-itinerary-items">
				<ul class="wp-travel-itinerary-list itinerary-<?php esc_attr_e( $col_per_row, 'wp-travel' ); ?>-per-row">
		<?php endif; ?>
	<?php endif; ?>

	<?php
}
/**
 * Archive page wrapper close.
 *
 * @since 1.0.4
 * @return void
 */
function wp_travel_archive_wrapper_close() {
	if ( ( is_wp_travel_archive_page() || is_search() ) && ! is_admin() ) :
		$view_mode = wp_travel_get_archive_view_mode();
		?>
		<?php if ( 'grid' === $view_mode ) : ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php
		$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
		$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
		wp_travel_pagination( $pagination_range, $max_num_pages );
		?>
	</div>
		<?php
	endif;
}

/**
 * Archive page sidebar
 *
 * @since 1.2.1
 * @return void
 */

function wp_travel_archive_listing_sidebar() {

	if ( is_wp_travel_archive_page() && ! is_admin() && is_active_sidebar( 'wp-travel-archive-sidebar' ) ) :
		?>

		<div id="wp-travel-secondary" class="wp-travel-widget-area widget-area" role="complementary">
			<?php dynamic_sidebar( 'wp-travel-archive-sidebar' ); ?>
		</div>

		<?php

	endif;

}

/**
 * If submitted filter by post meta.
 *
 * @param  (wp_query object) $query object.
 *
 * @return void
 */
function wp_travel_posts_filter( $query ) {
	global $pagenow;
	$type = '';
	if ( isset( $_GET['post_type'] ) ) {
		$type = $_GET['post_type'];
	}

	if ( $query->is_main_query() ) {

		if ( 'itinerary-booking' == $type && is_admin() && 'edit.php' == $pagenow && isset( $_GET['wp_travel_post_id'] ) && '' !== $_GET['wp_travel_post_id'] ) {

			$query->set( 'meta_key', 'wp_travel_post_id' );
			$query->set( 'meta_value', $_GET['wp_travel_post_id'] );
		}

		if ( 'itinerary-enquiries' == $type && is_admin() && 'edit.php' == $pagenow && isset( $_GET['wp_travel_post_id'] ) && '' !== $_GET['wp_travel_post_id'] ) {

			$query->set( 'meta_key', 'wp_travel_post_id' );
			$query->set( 'meta_value', $_GET['wp_travel_post_id'] );
		}

		/**
		 * Archive /Taxonomy page filters
		 *
		 * @since 1.0.4
		 */
		if ( is_wp_travel_archive_page() && ! is_admin() ) {

			$current_meta = $query->get( 'meta_query' );
			$current_meta = ( $current_meta ) ? $current_meta : array();
			// Filter By Dates.
			if ( isset( $_GET['trip_start'] ) || isset( $_GET['trip_end'] ) ) {

				$trip_start = ! empty( $_GET['trip_start'] ) ? $_GET['trip_start'] : 0;

				$trip_end = ! empty( $_GET['trip_end'] ) ? $_GET['trip_end'] : 0;

				if ( $trip_start || $trip_end ) {

					$date_format = get_option( 'date_format' );
					// Convert to timestamp.
					if ( ! $trip_start ) {
						$trip_start = date( 'Y-m-d' );
					}

					$custom_meta = array(
						'relation' => 'AND',
						array(
							'key'     => 'wp_travel_start_date',
							'value'   => $trip_start,
							'type'    => 'DATE',
							'compare' => '>=',
						),
					);

					if ( $trip_end ) {
						$custom_meta[] = array(
							'key'     => 'wp_travel_end_date',
							'value'   => $trip_end,
							'type'    => 'DATE',
							'compare' => '<=',
						);
					}
					$current_meta[] = $custom_meta;
				}
			}

			// Filter By Price.
			if ( isset( $_GET['price'] ) && '' != $_GET['price'] ) {
				$filter_by = $_GET['price'];

				$query->set( 'meta_key', 'wp_travel_trip_price' );
				$query->set( 'orderby', 'meta_value_num' );

				switch ( $filter_by ) {
					case 'low_high':
						$query->set( 'order', 'asc' );
						break;
					case 'high_low':
						$query->set( 'order', 'desc' );
						break;
					default:
						break;
				}
			}
			// Trip Cost Range Filter.
			if ( isset( $_GET['max_price'] ) || isset( $_GET['min_price'] ) ) {

				$max_price = ! empty( $_GET['max_price'] ) ? $_GET['max_price'] : 0;

				$min_price = ! empty( $_GET['min_price'] ) ? $_GET['min_price'] : 0;

				if ( $min_price || $max_price ) {

					$query->set( 'meta_key', 'wp_travel_trip_price' );

					$custom_meta    = array(
						array(
							'key'     => 'wp_travel_trip_price',
							'value'   => array( $min_price, $max_price ),
							'type'    => 'numeric',
							'compare' => 'BETWEEN',
						),
					);
					$current_meta[] = $custom_meta;
				}
			}

			if ( isset( $_GET['fact'] ) && '' != $_GET['fact'] ) {
				$fact = $_GET['fact'];

				$query->set( 'meta_key', 'wp_travel_trip_facts' );

				$custom_meta    = array(
					array(
						'key'     => 'wp_travel_trip_facts',
						'value'   => $fact,
						'compare' => 'LIKE',
					),
				);
				$current_meta[] = $custom_meta;
			}
			$query->set( 'meta_query', array( $current_meta ) );

			// Filter by Keywords.
			$current_tax = $query->get( 'tax_query' );
			$current_tax = ( $current_tax ) ? $current_tax : array();
			if ( isset( $_GET['keyword'] ) && '' != $_GET['keyword'] ) {

				$keyword  = $_GET['keyword'];
				$keywords = explode( ' ', $keyword );

				$current_tax[] = array(
					array(
						'taxonomy' => 'travel_keywords',
						'field'    => 'name',
						'terms'    => $keywords,
					),
				);
			}
			$query->set( 'tax_query', $current_tax );
		}
	}
}

function wp_travel_tab_show_in_menu( $tab_name ) {
	if ( ! $tab_name ) {
		return false;
	}
	$tabs = wp_travel_get_frontend_tabs( $show_in_menu_query = true, $frontend_hide_content = true ); // $show_in_menu_query fixes the content filter in page builder.
	if ( ! isset( $tabs[ $tab_name ]['show_in_menu'] ) ) {
		return false;
	}

	if ( 'yes' === $tabs[ $tab_name ]['show_in_menu'] ) {
		return true;
	}
	return false;
}

function wp_travel_get_archive_view_mode() {
	$default_view_mode = 'list';
	$default_view_mode = apply_filters( 'wp_travel_default_view_mode', $default_view_mode );
	$view_mode         = $default_view_mode;
	if ( isset( $_GET['view_mode'] ) && ( 'grid' === $_GET['view_mode'] || 'list' === $_GET['view_mode'] ) ) {
		$view_mode = $_GET['view_mode'];
	}
	return $view_mode;
}

/**
 * Clear Booking Stat Transient.
 *
 * @return void
 */
function wp_travel_clear_booking_transient( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$post_type = get_post_type( $post_id );
	// If this isn't a 'book' post, don't update it.
	if ( 'itinerary-booking' != $post_type ) {
		return;
	}
	// Stat Transient
	delete_site_transient( '_transient_wt_booking_stat_data' );
	delete_site_transient( '_transient_wt_booking_top_country' );
	delete_site_transient( '_transient_wt_booking_top_itinerary' );

	// Booking Count Transient
	$itinerary_id = get_post_meta( $post_id, 'wp_travel_post_id', true );
	delete_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
	delete_site_transient( '_transient_wt_booking_payment_stat_data' );
	// @since 1.0.6
	do_action( 'wp_travel_after_deleting_booking_transient' );
}


/**
 * Excerpt.
 *
 * @param HTML $more Read more.
 * @return HTML
 */
function wp_travel_excerpt_more( $more ) {
	global $post;
	if ( empty( $post->post_type ) || WP_TRAVEL_POST_TYPE !== $post->post_type ) {
		return $more;
	}

	return '...';
}

function wp_travel_wpkses_post_iframe( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}
	return $tags;
}

if ( ! function_exists( 'is_wp_travel_endpoint_url' ) ) :
	/**
	 * Is_wp_travel_endpoint_url - Check if an endpoint is showing.
	 *
	 * @param string $endpoint Whether endpoint.
	 * @return bool
	 */
	function is_wp_travel_endpoint_url( $endpoint = false ) {
		global $wp;
		$query_class         = new WP_Travel_Query();
		$wp_travel_endpoints = $query_class->get_query_vars();

		if ( false !== $endpoint ) {
			if ( ! isset( $wp_travel_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $wp_travel_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $wp_travel_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
endif;


/**
 * No index our endpoints.
 * Prevent indexing pages like order-received.
 *
 * @since 2.5.3
 */
function wp_travel_prevent_endpoint_indexing() {
	if ( is_wp_travel_endpoint_url() ) { // WPCS: input var ok, CSRF ok.
		@header( 'X-Robots-Tag: noindex' ); // @codingStandardsIgnoreLine
	}
}

function wp_travel_booking_default_princing_list_content( $trip_id ) {

	if ( '' == $trip_id ) {
		return;
	}
	// Label Strings
	$strings               = wp_travel_get_strings();
	$pricing_name_string   = isset( $strings['bookings']['pricing_name'] ) ? $strings['bookings']['pricing_name'] : __( 'Pricing Name', 'wp-travel' );
	$start_date_string     = isset( $strings['bookings']['start_date'] ) ? $strings['bookings']['start_date'] : __( 'Start', 'wp-travel' );
	$end_date_string       = isset( $strings['bookings']['end_date'] ) ? $strings['bookings']['end_date'] : __( 'End', 'wp-travel' );
	$group_size_string     = isset( $strings['bookings']['group_size'] ) ? $strings['bookings']['group_size'] : __( 'Group (min-max)', 'wp-travel' );
	$seats_left_string     = isset( $strings['bookings']['seats_left'] ) ? $strings['bookings']['seats_left'] : __( 'Seats Left', 'wp-travel' );
	$pax_string            = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel' );
	$price_string          = isset( $strings['bookings']['price'] ) ? $strings['bookings']['price'] : __( 'Price', 'wp-travel' );
	$arrival_date_string   = isset( $strings['bookings']['arrival_date'] ) ? $strings['bookings']['arrival_date'] : __( 'Arrival Date', 'wp-travel' );
	$departure_date_string = isset( $strings['bookings']['departure_date'] ) ? $strings['bookings']['departure_date'] : __( 'Departure Date', 'wp-travel' );
	$sold_out_string       = isset( $strings['bookings']['sold_out'] ) ? $strings['bookings']['sold_out'] : __( 'Sold out', 'wp-travel' );
	$book_now_string       = isset( $strings['bookings']['book_now'] ) ? $strings['bookings']['book_now'] : __( 'Book Now', 'wp-travel' );
	$select_string         = isset( $strings['bookings']['select'] ) ? $strings['bookings']['select'] : __( 'Select', 'wp-travel' );
	$pricing_string        = isset( $strings['bookings']['combined_pricing'] ) ? $strings['bookings']['combined_pricing'] : __( 'Pricing', 'wp-travel' );
	// Endf of strings

	// Filter added @since 3.0.0
	$is_inventory_enabled  = apply_filters( 'inventory_enabled', false, $trip_id );

	// All Pricings.
	$pricings     = wp_travel_get_trip_pricing_option( $trip_id );

	$pricing_data = isset( $pricings['pricing_data'] ) ? $pricings['pricing_data'] : array();

	$is_single_pricing = 'single-price' === wp_travel_get_pricing_option_type( $trip_id ); // added to hide column on legacy single pricing option. @since 3.0.0
	// Pricing Lists.
	if ( is_array( $pricing_data ) && count( $pricing_data ) > 0 ) {
		$js_date_format = wp_travel_date_format_php_to_js();
		$date_format    = get_option( 'date_format' );

		$settings             = wp_travel_get_settings();
		$form_field           = new WP_Travel_FW_Field();
		$sold_out_btn_rep_msg = apply_filters( 'wp_travel_inventory_sold_out_button', '', $trip_id );

		$show_status_col   = apply_filters( 'wp_travel_inventory_enable_status_column', false, $trip_id );
		$show_end_date     = wp_travel_booking_show_end_date();

		$trip_extras_class = new Wp_Travel_Extras_Frontend();

		$default_columns = 5; // To determine width of columns;
		?>
		<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list additional-col">
					<li class="availabily-heading clearfix">
						<!-- Column: Pricing Name -->
						<?php if ( ! $is_single_pricing ) : $default_columns++;  ?>
						<div class="pricing-name">
							<?php echo esc_html( $pricing_name_string ); ?>
						</div>
						<?php endif; ?>
						<!-- Column: Start Date -->
						<div class="date-from">
							<?php echo esc_html( $start_date_string ); ?>
						</div>
						<!-- Column: End Date -->
						<?php
						if ( $show_end_date ) :
							$default_columns++;
							?>
							<div class="date-to">
								<?php echo esc_html( $end_date_string ); ?>
							</div>
						<?php endif; ?>
						<!-- Column: Group Size -->
						<div class="group-size">
							<?php echo esc_html( $group_size_string ); ?>
						</div>

						<!-- Column: Pricing -->
						<div class="pricing">
							<?php echo esc_html( $pricing_string ); ?>
						</div>

						<!-- Column: Action -->
						<div class="action">
							&nbsp;
						</div>
					</li>
					<!-- pricing loop -->
					<?php
					foreach ( $pricing_data as $pricing_data_key => $pricing ) :

						$pricing_categories = isset( $pricing['categories'] ) ? $pricing['categories'] : array();

						$parent_id = 'wp-travel-pricing-wrap';
						if ( ! empty( $pricing['pricing_id'] ) ) { // Multiple pricing.
							$parent_id = sprintf( 'pricing-%s-%s', $pricing['price_key'], rand( 1000, 9999 ) );
						}

						$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
						if ( 'yes' !== $pricing['fixed_departure'] && ! empty( $pricing['trip_duration_days'] ) ) :
							$cart_url = add_query_arg( 'trip_duration', $pricing['trip_duration_days'], $cart_url );
						endif;
						$cart_url = add_query_arg( 'price_key', $pricing['price_key'], $cart_url );

						$unavailable_class = '';
						$availability      = false;
						if ( isset( $pricing['arrival_date'] ) ) {
							$availability = wp_travel_trip_availability( $trip_id, $pricing['price_key'], $pricing['arrival_date'], $pricing['inventory']['sold_out'] );
							if ( ! $availability || ( $is_inventory_enabled && $pricing['inventory']['min_pax'] > $pricing['inventory']['available_pax'] ) ) {
								$unavailable_class = 'pricing_unavailable';
							}
						} ?>
						<li class="availabily-content clearfix <?php echo esc_attr( $unavailable_class ); ?>">
							<form action="<?php echo esc_url( $cart_url ); ?>" id="<?php echo esc_attr( $parent_id ); ?>" class="wp-travel-add-to-cart-form">
								<!-- Column: Pricing Name -->
								<?php if ( ! $is_single_pricing ) : ?>
									<div class="pricing-name">
										<span class="availabily-heading-label"><?php echo esc_html( $pricing_name_string ); ?></span>
										<span> <?php echo esc_html( $pricing['pricing_name'] ); ?> </span>
									</div>
								<?php endif; ?>
								<?php if ( 'yes' === $pricing['fixed_departure'] ) : ?>
									<!-- Column: Start Date -->
									<div class="date-from">
										<span class="availabily-heading-label"><?php echo esc_html( $start_date_string ); ?></span>
										<?php echo esc_html( date_i18n( 'l', strtotime( $pricing['arrival_date'] ) ) ); ?>
										<span><?php echo esc_html( date_i18n( $date_format, strtotime( $pricing['arrival_date'] ) ) ); ?></span>
										<input type="hidden" name="arrival_date" value="<?php echo esc_attr( $pricing['arrival_date'] ); ?>">
									</div>
									<?php if ( $show_end_date ) : ?>
										<div class="date-to">
											<span class="availabily-heading-label"><?php echo esc_html( $end_date_string ); ?></span>
											<?php echo esc_html( date_i18n( 'l', strtotime( $pricing['departure_date'] ) ) ); ?>
											<span><?php echo esc_html( date_i18n( $date_format, strtotime( $pricing['departure_date'] ) ) ); ?></span>
											<input type="hidden" name="departure_date" value="<?php echo esc_attr( $pricing['departure_date'] ); ?>">
										</div>
									<?php endif; ?>
								<?php else : ?>
									<div class="date-from">
										<span class="availabily-heading-label"><?php echo esc_html( $start_date_string ); ?></span>
										<?php
										$total_days = 0;
										if ( 'yes' !== $pricing['fixed_departure'] && ( ! empty( $pricing['trip_duration_days'] ) || ! empty($pricing['trip_duration_night'] ) ) ) {
											$days = $pricing['trip_duration_days'] > $pricing['trip_duration_night'] ? $pricing['trip_duration_days'] : $pricing['trip_duration_night'];
											$days--; // As we need to exclude current selected date.
											$total_days = $days ? $days : $total_days;
										}
										$start_field = array(
											'label'       => esc_html__( 'Start', 'wp-travel' ),
											'type'        => 'date',
											'name'        => 'arrival_date',
											'placeholder' => esc_html( $arrival_date_string  ),
											'class'       => 'wp-travel-pricing-days-night',
											'validations' => array(
												'required' => true,
											),
											'attributes'  => array(
												'data-parsley-trigger' => 'change',
												'data-parsley-required-message' => esc_attr__( 'Please Select a Date', 'wp-travel' ),
												'data-totaldays' => $total_days,
												'data-date-format' => $js_date_format,
											),
											'wrapper_class' => 'date-from',
										);
										$form_field->init()->render_input( $start_field );
										?>
									</div>
									<?php if ( $show_end_date ) : ?>

										<div class="date-to">
											<span class="availabily-heading-label"><?php echo esc_html( $end_date_string ); ?></span>
											<?php
											$end_field = array(
												'label' => esc_html__( 'End', 'wp-travel' ),
												'type'  => 'date',
												'name'  => 'departure_date',
												'placeholder' => esc_html( $departure_date_string ),
											);
											$end_field = wp_parse_args( $end_field, $start_field );
											$form_field->init()->render_input( $end_field );
											?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
								<!-- Column: Group Size -->
								<div class="group-size-min-max">
									<?php echo esc_html( $pricing['inventory']['min_pax'] . '-' . $pricing['inventory']['max_pax'] ); ?>
								</div>

								<?php if ( $unavailable_class !== 'pricing_unavailable' ) : ?>
									<!-- Column: Pricing -->
									<div class="group-size">
										<div id="paxpicker" class="paxpicker">
											<div class="icon-users summary">
												<input readonly="readonly" class="participants-summary-container" value="Select Pax" data-default="Select Pax" >
											</div>
											<div class="pricing-categories" id="pricing-categories-<?php echo esc_attr( $pricing['pricing_id'] ) . '-' . rand( 1000, 9999 ); ?>" data-selected-pax="0" data-available-pax="<?php echo esc_attr( $pricing['inventory']['available_pax'] ); ?>" data-parent-form-id="<?php echo esc_attr( $parent_id ); ?>" data-min="<?php echo esc_attr( $pricing['inventory']['min_pax'] ); ?>" data-max="<?php echo esc_attr( $pricing['inventory']['max_pax'] ); ?>">
												<span class="separator">&nbsp;</span>
												<?php
												if ( $is_inventory_enabled ) :
													$pricing_max_pax = ! empty( $pricing['inventory']['max_pax'] ) ? $pricing['inventory']['max_pax'] : get_post_meta( $trip_id, 'wp_travel_inventory_custom_max_pax', true );
													$available_pax = ! empty( $pricing['inventory']['available_pax'] ) ? $pricing['inventory']['available_pax'] : $pricing_max_pax;
												else :
													$available_pax = $pricing['inventory']['max_pax'];
												endif;
												?>

												<div class="category available-seats">
													<?php echo esc_html__( 'Available Seats: ', 'wp-travel' ) . '<span>' . (int) $available_pax . '</span>'; ?>
												</div>
												<?php
												if ( is_array( $pricing_categories ) && count( $pricing_categories ) > 0 ) {
													foreach ( $pricing_categories as $category_id =>  $pricing_category ) {
														$max      = $pricing['inventory']['max_pax'];
														$min      = $pricing['inventory']['min_pax'];
														$max_attr = "max={$max}";
														$min_attr = "min={$min}";
														?>
															<div class="category" id="<?php echo esc_attr( $category_id ); ?>">
																<p class="picker-info">
																	<span class="pax-type">
																		<strong><?php echo esc_html( $pricing_category['type'] ); ?></strong>
																		<span class="min-max-pax">
																			(<?php
																				if ( ! empty( $pricing['inventory']['max_pax'] ) ) {
																					echo sprintf( '%s - %s %s', $min, $max, $pax_string );
																				} else {
																					echo sprintf( '%s %s - %s', $min, $pax_string, $max );
																				}
																			?>)
																		</span>
																	</span>
																	<span class="price-per-info">
																		<?php if ( $pricing_category['price'] ) : ?>

																			<?php if ( 'yes' === $pricing_category['enable_sale'] ) : ?>
																				<del>
																					<span><?php echo wp_travel_get_formated_price_currency( $pricing_category['regular'], true ); ?></span>
																				</del>
																			<?php endif; ?>
																			<span class="person-count">
																				<ins>
																					<span><?php echo wp_travel_get_formated_price_currency( $pricing_category['price'] ); ?></span>
																				</ins>/<?php echo esc_html( $pricing_category['price_per'] ); ?>
																			</span>
																		<?php endif; ?>
																	</span>
																</p>
																<div class="pax-select-container">
																	<a href="#" class="icon-minus pax-picker-minus">-</a>
																	<input readonly class="input-num paxpicker-input" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" type="number" value="0" data-min="<?php echo $min ?>" data-max="<?php echo $max ?>" data-type="<?php echo esc_html( $pricing_category['type'] ); ?>" data-category-id="<?php echo esc_html( $category_id ); ?>" min="0" <?php echo sprintf( '%s', $max_attr ) ?>   step="1" maxlength="2" autocomplete="off">
																	<a href="#" class="icon-plus pax-picker-plus">+</a>
																</div>

															</div>
														<?php
													}
												}
												?>
												<span class="pricing-input"></span> <!-- pax inputs -->
											</div>
										</div>
									</div>
								<?php else: ?>
								<div class="group-size">&nbsp;</div>
								<?php endif; ?>
								<?php
									$pax_alerts = wp_travel_pax_alert_message( $min, $max );
									$range_alert = $pax_alerts['range'];
									$required_alert = $pax_alerts['required'];
								?>

								<!-- Column: Action -->
								<div class="action">
									<?php
									if ( $pricing['inventory']['sold_out'] ) :
										?>
										<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
										<?php
									else :
										if ( $trip_extras_class->has_trip_extras( $trip_id, $pricing['price_key'] ) ) {
											?>
											<a href="#" class="btn btn-primary btn-sm btn-inverse show-booking-row"><?php echo esc_html( $select_string ); ?></a>
											<?php
										} else {
											?>
											<input type="submit" value="<?php echo esc_html( $book_now_string ); ?>" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" >
											<?php
										}
										// @since 1.9.3 To display group discount pricing lists.
										do_action( 'wp_travel_booking_after_select_button', $trip_id, $pricing['price_key'] );
									endif;
									?>
									<?php if ( isset( $pricing['arrival_date'] ) ) : ?>
										<!-- if fixed departure -->
										<input type="hidden" name="arrival_date" value="<?php echo esc_attr( $pricing['arrival_date'] ); ?>" >
										<input type="hidden" name="departure_date" value="<?php echo esc_attr( $pricing['departure_date'] ); ?>" >
									<?php endif; ?>
									<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ); ?>" />
									<input type="hidden" name="price_key" value="<?php echo esc_attr( $pricing['price_key'] ); ?>" />
									<input type="hidden" name="pricing_id" value="<?php echo esc_attr( $pricing['pricing_id'] ); ?>" />
								</div>
								<?php if ( $availability || 'no' === $pricing['fixed_departure'] ) : // Remove Book now if trip is soldout. ?>
									<div class="wp-travel-booking-row">
										<?php
											/**
											 * Support For WP Travel Tour Extras Plugin.
											 *
											 * @since 1.5.8
											 */
											$arrival_date = isset( $pricing['arrival_date'] ) ? $pricing['arrival_date'] : '';
											$pricing_key = $pricing['price_key'];
											if ( 'default-pricing' === $pricing['price_key'] ) {
												$pricing_key = ''; // Quick fixing for single pricing
											}
											do_action( 'wp_travel_trip_extras', $pricing_key, $arrival_date );
										?>
										<div class="wp-travel-calender-aside">
											<div class="add-to-cart">

												<?php
												if ( 'yes' !== $pricing['fixed_departure'] && ! empty( $pricing['trip_duration_days'] ) ) :
													?>
													<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $pricing['trip_duration_days'] ); ?>" />
													<?php
												endif;
												?>
												<input type="submit" value="<?php echo esc_html( $book_now_string ); ?>" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" >
											</div>
										</div>
									</div>
								<?php endif; ?>
							</form>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php $column_width = sprintf( '%0.4f', 100 / $default_columns ); ?>
		<style>
		ul.availabily-list li.availabily-heading >div,
		ul.availabily-list li > form > div{
			width: <?php echo esc_attr( $column_width ); ?>%;
		}
		</style>
		<?php
	}
}

function wp_travel_booking_fixed_departure_list_content( $trip_id ) {

	if ( '' == $trip_id ) {
		return;
	}
	// Strings
	$strings               = wp_travel_get_strings();
	$pricing_name_string   = isset( $strings['bookings']['pricing_name'] ) ? $strings['bookings']['pricing_name'] : __( 'Pricing Name', 'wp-travel' );
	$start_date_string     = isset( $strings['bookings']['start_date'] ) ? $strings['bookings']['start_date'] : __( 'Start', 'wp-travel' );
	$end_date_string       = isset( $strings['bookings']['end_date'] ) ? $strings['bookings']['end_date'] : __( 'End', 'wp-travel' );
	$group_size_string     = isset( $strings['bookings']['group_size'] ) ? $strings['bookings']['group_size'] : __( 'Group (min-max)', 'wp-travel' );
	$seats_left_string     = isset( $strings['bookings']['seats_left'] ) ? $strings['bookings']['seats_left'] : __( 'Seats Left', 'wp-travel' );
	$pax_string            = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel' );
	$price_string          = isset( $strings['bookings']['price'] ) ? $strings['bookings']['price'] : __( 'Price', 'wp-travel' );
	$arrival_date_string   = isset( $strings['bookings']['arrival_date'] ) ? $strings['bookings']['arrival_date'] : __( 'Arrival Date', 'wp-travel' );
	$departure_date_string = isset( $strings['bookings']['departure_date'] ) ? $strings['bookings']['departure_date'] : __( 'Departure Date', 'wp-travel' );
	$sold_out_string       = isset( $strings['bookings']['sold_out'] ) ? $strings['bookings']['sold_out'] : __( 'Sold out', 'wp-travel' );
	$book_now_string       = isset( $strings['bookings']['book_now'] ) ? $strings['bookings']['book_now'] : __( 'Book Now', 'wp-travel' );
	$select_string         = isset( $strings['bookings']['select'] ) ? $strings['bookings']['select'] : __( 'Select', 'wp-travel' );
	// Endf of strings
	$is_inventory_enabled  = apply_filters( 'inventory_enabled', false, $trip_id );

	$pricings = wp_travel_get_trip_pricing_option( $trip_id );

	$pricing_data = isset( $pricings['pricing_data'] ) ? wp_travel_trip_pricing_sort_by_date( $pricings['pricing_data'] ) : array();
	// Pricing Lists.
	if ( is_array( $pricing_data ) && count( $pricing_data ) > 0 ) {
		$js_date_format = wp_travel_date_format_php_to_js();
		$date_format    = get_option( 'date_format' );

		$settings             = wp_travel_get_settings();
		$form_field           = new WP_Travel_FW_Field();
		$sold_out_btn_rep_msg = apply_filters( 'wp_travel_inventory_sold_out_button', '', $trip_id );

		$show_status_col = apply_filters( 'wp_travel_inventory_enable_status_column', false, $trip_id );
		$show_end_date   = wp_travel_booking_show_end_date();

		$is_single_pricing = 'single-price' === wp_travel_get_pricing_option_type( $trip_id ); // added to hide column on legacy single pricing option. @since 3.0.0

		$trip_extras_class = new Wp_Travel_Extras_Frontend();
		?>
		<div class="trip_list_by_fixed_departure_dates">
			<div class="trip_list_by_fixed_departure_dates_header">
				<span class="trip_list_by_fixed_departure_dates_wrap">
					<?php if ( ! $is_single_pricing ) : ?>
					<span class="trip_list_by_fixed_departure_dates_pricing_name_label"><?php echo esc_html( $pricing_name_string ); ?></span>
					<?php endif; ?>
					<span class="trip_list_by_fixed_departure_dates_start_label"><?php echo esc_html( $start_date_string ); ?></span>
					<?php if ( $show_end_date ) : ?>
						<span class="trip_list_by_fixed_departure_dates_end_label"><?php echo esc_html( $end_date_string ); ?></span>
					<?php endif ?>
					<span class="trip_list_by_fixed_departure_dates_seats_label group-size-label"><?php echo esc_html( $group_size_string ); ?></span>
					<?php if ( $show_status_col ) : ?>
					<span class="trip_list_by_fixed_departure_dates_seats_label"><?php echo esc_html( $seats_left_string ); ?></span>
					<?php endif; ?>
					<span class="trip_list_by_fixed_departure_dates_pax_label"><?php echo esc_html( $pax_string ); ?></span>
					<span class="trip_list_by_fixed_departure_dates_price_label"><?php echo esc_html( $price_string ); ?></span>
				</span>
			</div>

			<ul class="trip_list_by_fixed_departure_dates_list">
				<!-- pricing loop -->
				<?php
				foreach ( $pricing_data as $pricing ) :

					$pricing_categories = isset( $pricing['categories'] ) ? $pricing['categories'] : array();
					$max_attr = ! empty( $pricing['inventory']['max_pax'] ) ? ( ! empty( $pricing['inventory']['available_pax'] ) ? 'max=' . $pricing['inventory']['available_pax']  : 'max=' . $pricing['inventory']['max_pax'] ) : ''; // set available_pax as max_pax if available
                    $min_attr = ! empty( $pricing['inventory']['min_pax'] ) ? 'min=' . $pricing['inventory']['min_pax'] : 'min=1';

                    $rand = rand(); // Generate random key.

                    $parent_id = 'wp-travel-pricing-wrap-' . $rand; // Default random parent_id.

					if ( ! empty( $pricing['pricing_id'] ) ) { // Multiple pricing.
						$parent_id = sprintf( 'pricing-%s-%s', $pricing['price_key'], $rand );
					}

					$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
					if ( 'yes' !== $pricing['fixed_departure'] ) :
						$cart_url = add_query_arg( 'trip_duration', $pricing['trip_duration_days'], $cart_url );
					endif;
					$cart_url = add_query_arg( 'price_key', $pricing['price_key'], $cart_url );

					$unavailable_class = '';
					$availability      = false;
					if ( isset( $pricing['arrival_date'] ) ) {
						$availability = wp_travel_trip_availability( $trip_id, $pricing['price_key'], $pricing['arrival_date'], $pricing['inventory']['sold_out'] );
						if ( ! $availability || ( $is_inventory_enabled && $pricing['inventory']['min_pax'] > $pricing['inventory']['available_pax'] ) ) {
                            $unavailable_class = 'pricing_unavailable';
                        }
					}

					?>
					<li class="availabily-content clearfix <?php echo esc_attr( $unavailable_class ); ?>">
						<form action="<?php echo esc_url( $cart_url ); ?>" id="<?php echo esc_attr( $parent_id ); ?>" class="wp-travel-add-to-cart-form">
							<div class="trip_list_by_fixed_departure_dates_wrap">
								<?php if ( ! $is_single_pricing ) : ?>
								<span class="trip_list_by_fixed_departure_dates_pricing_name"> <?php echo esc_html( $pricing['pricing_name'] ); ?> </span>
								<?php endif; ?>
								<span class="trip_list_by_fixed_departure_dates_start"><!-- Start Date -->
									<div class="trip_list_by_fixed_departure_dates_day"><?php echo esc_html( date_i18n( 'l', strtotime( $pricing['arrival_date'] ) ) ); ?></div>
									<div class="trip_list_by_fixed_departure_dates_date"><?php echo esc_html( date_i18n( $date_format, strtotime( $pricing['arrival_date'] ) ) ); ?></div>
									<input type="hidden" name="arrival_date" value="<?php echo esc_attr( $pricing['arrival_date'] ); ?>">

									<?php if ( $show_end_date && '' !== $pricing['departure_date'] ) : ?>
										<div class="trip_list_by_fixed_departure_dates_length">
											<div><?php echo esc_html( wp_travel_get_date_diff( $pricing['arrival_date'], $pricing['departure_date'] ) ); ?></div>
										</div>
									<?php endif ?>
								</span><!-- /Start Date -->
								<?php if ( $show_end_date ) : ?>
									<span class="trip_list_by_fixed_departure_dates_end"><!-- End Date -->
										<?php if ( ! empty( $pricing['departure_date'] ) ) : ?>
											<div class="trip_list_by_fixed_departure_dates_day"><?php echo esc_html( date_i18n( 'l', strtotime( $pricing['departure_date'] ) ) ); ?></div>
											<div class="trip_list_by_fixed_departure_dates_date"><?php echo esc_html( wp_travel_format_date( $pricing['departure_date'] ) ); ?></div>
											<input type="hidden" name="departure_date" value="<?php echo esc_attr( $pricing['departure_date'] ); ?>">
										<?php endif; ?>
									</span>
								<?php endif; ?>
								<span class="trip_list_by_fixed_departure_dates_seats">
										<?php
										$min = ! empty( $pricing['inventory']['min_pax'] ) ? esc_html( $pricing['inventory']['min_pax'] ) : 1;
										$max = ! empty( $pricing['inventory']['max_pax'] ) ? esc_html( $pricing['inventory']['max_pax'] ) : esc_html__( 'No size limit', 'wp-travel' );

										if ( ! empty( $pricing['inventory']['max_pax'] ) ) {
											echo sprintf( '%s - %s %s', $min, $max, $pax_string );
										} else {
											echo sprintf( '%s %s - %s', $min, $pax_string, $max );
										}
										?>
								</span>
								<?php if ( $show_status_col ) : ?>
									<span class="trip_list_by_fixed_departure_dates_seats available-seats">
										<?php if ( $pricing['inventory']['sold_out'] ) : ?>
											<span><?php echo esc_html( $sold_out_string ); ?></span>
										<?php else : ?>
											<span><?php echo esc_html( $pricing['status'] ); ?></span>
										<?php endif; ?>
									</span>
								<?php endif; ?>
								<?php
									$pax_alerts = wp_travel_pax_alert_message( $min, $max );
									$range_alert = $pax_alerts['range'];
									$required_alert = $pax_alerts['required'];
								?>
								<?php if ( $unavailable_class !== 'pricing_unavailable' ) : ?>
									<div class="group-size pax-selection">
										<div id="paxpicker" class="paxpicker">
											<div class="icon-users summary">
												<input readonly="readonly" class="participants-summary-container" value="Select Pax" data-default="Select Pax" >
											</div>
											<div class="pricing-categories" id="pricing-categories-<?php echo esc_attr( $pricing['pricing_id'] ) . '-' . rand( 1000, 9999 ); ?>" data-booked-pax="<?php esc_attr( $pricing['inventory']['booked_pax'] ) ?>" data-available-pax="<?php echo esc_attr( $pricing['inventory']['available_pax'] ); ?>" data-min="<?php echo esc_attr( $pricing['inventory']['min_pax'] ); ?>" data-max="<?php echo esc_attr( $pricing['inventory']['max_pax'] ); ?>">
												<span class="separator">&nbsp;</span>
												<?php
												if ( $is_inventory_enabled ) :
													$pricing_max_pax = ! empty( $pricing['inventory']['max_pax'] ) ? $pricing['inventory']['max_pax'] : get_post_meta( $trip_id, 'wp_travel_inventory_custom_max_pax', true );
													$available_pax = ! empty( $pricing['inventory']['available_pax'] ) ? $pricing['inventory']['available_pax'] : $pricing_max_pax;
												else :
													$available_pax = $pricing['inventory']['max_pax'];
												endif;
												?>

												<div class="category available-seats">
													<?php echo esc_html( 'Available Seats: ' ) . '<span>' . (int) $available_pax . '</span>'; ?>
												</div>
												<?php
												if ( is_array( $pricing_categories ) && count( $pricing_categories ) > 0 ) {
													foreach ( $pricing_categories as $category_id =>  $pricing_category ) {
														$max      = $pricing['inventory']['max_pax'];
														$min      = $pricing['inventory']['min_pax'];
														$max_attr = "max={$max}";
														$min_attr = "min={$min}";
														// if ( ! empty( $pricing_category['min_pax'] ) ) {
														// 	$min      = $pricing_category['min_pax'];
														// 	$min_attr = "min={$min}";
														// } elseif ( ! empty( $pricing['min_pax'] ) ) {
														// 	$min      = $pricing['min_pax'];
														// 	$min_attr = "min={$min}";
														// }

														// if ( ! empty( $pricing_category['max_pax'] ) ) {
														// 	$max      = $pricing_category['max_pax'];
														// 	$max_attr = "max={$max}";
														// } elseif ( ! empty( $pricing['max_pax'] ) ) {
														// 	$max      = ! empty( $pricing['max_pax'] ) ? $pricing['max_pax'] : '99';
														// 	$max_attr = "max={$max}";
														// }

														// if ( $is_inventory_enabled && ! empty( $pricing['available_pax'] ) ) {
														// 	$max      = $pricing['available_pax'];
														// 	$max_attr = "max={$max}";
														// } else {
														// 	$max      = $pricing['inventory']['max_pax'];
														// 	$max_attr = "max={$max}";
														// }
														// $min = ! empty( $pricing_category['min_pax'] ) ? esc_html( $pricing_category['min_pax'] ) : 1;
														// $max = ! empty( $pricing_category['max_pax'] ) ? esc_html( $pricing_category['max_pax'] ) : esc_html__( 'No size limit', 'wp-travel' );
														?>
															<div class="category" id="<?php echo esc_attr( $category_id ); ?>">

																<p class="picker-info">
																	<span class="pax-type">
																		<strong><?php echo esc_html( $pricing_category['type'] ); ?></strong>
																		<span class="min-max-pax">
																			(<?php
																				if ( ! empty( $pricing['inventory']['max_pax'] ) ) {
																					echo sprintf( '%s - %s %s', $min, $max, $pax_string );
																				} else {
																					echo sprintf( '%s %s - %s', $min, $pax_string, $max );
																				}
																			?>)
																		</span>
																	</span>
																	<span class="price-per-info">
																		<?php if ( $pricing_category['price'] ) : ?>

																			<?php if ( 'yes' === $pricing_category['enable_sale'] ) : ?>
																				<del>
																					<span><?php echo wp_travel_get_formated_price_currency( $pricing_category['regular'], true ); ?></span>
																				</del>
																			<?php endif; ?>
																			<span class="person-count">
																				<ins>
																					<span><?php echo wp_travel_get_formated_price_currency( $pricing_category['price'] ); ?></span>
																				</ins>/<?php echo esc_html( $pricing_category['price_per'] ); ?>
																			</span>
																		<?php endif; ?>
																	</span>
																</p>
																<div class="pax-select-container">
																	<a href="#" class="icon-minus pax-picker-minus">-</a>
																	<input readonly class="input-num paxpicker-input" type="number" value="0" data-min="<?php echo $min ?>" data-max="<?php echo $max ?>" data-type="<?php echo esc_html( $pricing_category['type'] ); ?>" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" data-category-id="<?php echo esc_html( $category_id ); ?>" min="0" <?php echo sprintf( '%s', $max_attr ) ?>   step="1" maxlength="2" autocomplete="off">
																	<a href="#" class="icon-plus pax-picker-plus">+</a>
																</div>

															</div>
														<?php
													}
												}
												?>
												<span class="pricing-input"></span> <!-- pax inputs -->
											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<?php
							$sold_class = $pricing['inventory']['sold_out'] && 'show_sold_out_msg_only' === get_post_meta( $trip_id, 'wp_travel_inventory_sold_out_action', true ) ? 'sold-out' : '';
							?>
							<div class="trip_list_by_fixed_departure_dates_booking <?php echo esc_attr( $sold_class ); ?>">
								<div class="action">
									<?php if ( $pricing['inventory']['sold_out'] ) : ?>
										<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
										<?php
									else :
										if ( $trip_extras_class->has_trip_extras( $trip_id, $pricing['price_key'] ) ) {
											?>
											<a href="#" class="btn btn-primary btn-sm btn-inverse show-booking-row-fd"><?php echo esc_html( $select_string ); ?></a>
											<?php
										} else {
											?>
											<input type="submit" value="<?php echo esc_html( $book_now_string ); ?>" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" >
											<?php
										}
										// @since 1.9.3 To display group discount pricing lists.
										do_action( 'wp_travel_booking_after_select_button', $trip_id, $pricing['price_key'] );
										?>
									<?php endif; ?>
									<?php if ( isset( $pricing['arrival_date'] ) ) : ?>
										<!-- if fixed departure -->
										<input type="hidden" name="arrival_date" value="<?php echo esc_attr( $pricing['arrival_date'] ); ?>" >
										<input type="hidden" name="departure_date" value="<?php echo esc_attr( $pricing['departure_date'] ); ?>" >
									<?php endif; ?>
									<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ); ?>" />
									<input type="hidden" name="price_key" value="<?php echo esc_attr( $pricing['price_key'] ); // Need to remove price key. ?>" />
									<input type="hidden" name="pricing_id" value="<?php echo esc_attr( $pricing['pricing_id'] ); ?>" />
								</div>
							</div>
							<?php if ( $availability || 'no' === $pricing['fixed_departure'] ) : // Remove Book now if trip is soldout. ?>
								<div class="wp-travel-booking-row-fd">
									<?php
										/**
										 * Support For WP Travel Tour Extras Plugin.
										 *
										 * @since 1.5.8
										 */
										$arrival_date = isset( $pricing['arrival_date'] ) ? $pricing['arrival_date'] : '';
										$pricing_key = $pricing['price_key'];
										if ( 'default-pricing' === $pricing['price_key'] ) {
											$pricing_key = ''; // Quick fixing for single pricing
										}
										do_action( 'wp_travel_trip_extras', $pricing_key, $arrival_date );
									?>
									<div class="wp-travel-calender-aside">
										<div class="add-to-cart">

											<?php
											if ( 'yes' !== $pricing['fixed_departure'] ) :
												?>
												<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $pricing['trip_duration_days'] ); ?>" />
												<?php
											endif;
											?>
											<input type="submit" value="<?php echo esc_html( $book_now_string ); ?>" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" >

										</div>
									</div>
								</div>
							<?php endif; ?>
						</form>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Disable Jetpack Related Posts on Trips page
 *
 * @param array $options
 * @return void
 */
function wp_travel_remove_jetpack_related_posts( $options ) {

	$disable_jetpack_related_for_trips = apply_filters( 'wp_travel_disable_jetpack_rp', true );

	if ( is_singular( WP_TRAVEL_POST_TYPE ) && $disable_jetpack_related_for_trips ) {
		$options['enabled'] = false;
	}
	return $options;
}

function wp_travel_get_header_image_tag( $html ) {
	if ( ! is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) {
		return $html;
	}
		$attr           = array();
		$queried_object = get_queried_object();
		$image_id       = get_term_meta( $queried_object->term_id, 'wp_travel_trip_type_image_id', true );
	if ( false == $image_id || '' == $image_id ) {
			return $html;
	}
		$header                = new stdClass();
		$image_meta            = get_post_meta( $image_id, '_wp_attachment_metadata', true );
		$header->url           = wp_get_attachment_url( $image_id );
		$header->attachment_id = $image_id;
		$width                 = absint( $image_meta['width'] );
		$height                = absint( $image_meta['height'] );

		$attr = wp_parse_args(
			$attr,
			array(
				'src'    => $header->url,
				'width'  => $width,
				'height' => $height,
				'alt'    => get_bloginfo( 'name' ),
			)
		);

		// Generate 'srcset' and 'sizes' if not already present.
	if ( empty( $attr['srcset'] ) && ! empty( $header->attachment_id ) ) {
			$size_array = array( $width, $height );

		if ( is_array( $image_meta ) ) {
				$srcset = wp_calculate_image_srcset( $size_array, $header->url, $image_meta, $header->attachment_id );
				$sizes  = ! empty( $attr['sizes'] ) ? $attr['sizes'] : wp_calculate_image_sizes( $size_array, $header->url, $image_meta, $header->attachment_id );

			if ( $srcset && $sizes ) {
				$attr['srcset'] = $srcset;
				$attr['sizes']  = $sizes;
			}
		}
	}

		$attr = array_map( 'esc_attr', $attr );
		$html = '<img';

	foreach ( $attr as $name => $value ) {
			$html .= ' ' . $name . '="' . $value . '"';
	}

		$html .= ' />';
		return $html;
}

/**
 * If return false then, the_content filter used for tab content to display.
 *
 * @param boolean $raw false to use the_content filter to fetch content.
 * @param string  $tab_key Frontend tab key.
 *
 * @since 2.0.6
 *
 * @return bool
 */
function wp_travel_raw_output_on_tab_content( $raw, $tab_key ) {
	if ( 'gallery' === $tab_key ) { // Hide extra tab content on gallery tab.
		$raw = true;
	}
	return $raw;
}
