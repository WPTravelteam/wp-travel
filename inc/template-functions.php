<?php
/**
 * Template Functions.
 *
 * @package wp-travel/inc/
 */

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
 * @param int $post_id ID for current post.
 */
function wp_travel_trip_price( $post_id, $hide_rating = false ) {
	$settings   = wp_travel_get_settings();
	$trip_price = wp_travel_get_trip_price( $post_id );

	$enable_sale     = get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	$sale_price      = wp_travel_get_trip_sale_price( $post_id );
	$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text = wp_travel_get_price_per_text( $post_id );	
	?>

	<div class="wp-detail-review-wrap">
		<?php do_action( 'wp_travel_single_before_trip_price', $post_id, $hide_rating ); ?>
		<div class="wp-travel-trip-detail">
		<?php if ( '' != $trip_price || '0' != $trip_price ) : ?>
			<div class="trip-price" >

			<?php if ( $enable_sale ) : ?>
				<del>
					<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
				</del>
			<?php endif; ?>
				<span class="person-count">
					<ins>
						<span>
							<?php
							if ( $enable_sale ) {
								echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price );
							} else {
								echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price );
							}
							?>
						</span>
					</ins>
					<?php if ( ! empty( $per_person_text ) ) : ?>
						/<?php echo esc_html( $per_person_text ); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>
		</div>
		<?php do_action( 'wp_travel_single_after_trip_price', $post_id, $hide_rating ); ?>
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
 */
function wp_travel_single_excerpt( $post_id ) {

	if ( ! $post_id ) {
		return;
	}
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

	$wp_travel_itinerary = new WP_Travel_Itinerary();
	?>
	<div class="trip-short-desc">
		<?php the_excerpt(); ?>
	</div>
	<div class="wp-travel-trip-meta-info">
		  <ul>
			<?php
			/**
			 * @since 1.0.4
			 */
			do_action( 'wp_travel_single_itinerary_before_trip_meta_list', $post_id );
			?>
			  <li>
				   <div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Trip Type', 'wp-travel' ); ?></strong>
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
					<strong class="title"><?php esc_html_e( 'Activities', 'wp-travel' ); ?></strong>
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
					<strong class="title"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">
						<?php
						if ( $group_size = $wp_travel_itinerary->get_group_size() ) {
								printf( apply_filters( 'wp_travel_template_group_size_text', __( '%d pax', 'wp-travel' ) ), esc_html( $group_size ) );
						} else {
							echo esc_html( apply_filters( 'wp_travel_default_group_size_text', __( 'No Size Limit', 'wp-travel' ) ) );
						}
						?>
					</span>
				</div>
			   </li>
			<?php
			$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();

			if ( is_array( $wp_travel_itinerary_tabs ) && 'no' !== $wp_travel_itinerary_tabs['reviews']['show_in_menu'] && comments_open() ) :
				?>
			   <li>
				   <div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Reviews', 'wp-travel' ); ?></strong>
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
			/**
			 * @since 1.0.4
			 */
			do_action( 'wp_travel_single_itinerary_after_trip_meta_list', $post_id );
			?>
		  </ul>
	</div>

	  <div class="booking-form">
		<div class="wp-travel-booking-wrapper">
			<?php
			$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();
			$booking_tab              = $wp_travel_itinerary_tabs['booking'];

			if ( isset( $booking_tab['show_in_menu'] ) && 'yes' === $booking_tab['show_in_menu'] ) :
				?>
			<button class="wp-travel-booknow-btn"><?php echo esc_html( apply_filters( 'wp_travel_template_book_now_text', __( 'Book Now', 'wp-travel' ) ) ); ?></button>
			<?php endif; ?>
			<?php if ( 'yes' == $enable_enquiry ) : ?>

				<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
					<span class="wp-travel-booking-enquiry">
						<span class="dashicons dashicons-editor-help"></span>
						<span>
							<?php echo esc_attr( apply_filters( 'wp_travel_trip_enquiry_popup_link_text', __( 'Trip Enquiry', 'wp-travel' ) ) ); ?>
						</span>
					</span>
				</a>
			<?php endif; ?>

		</div>
	</div>
		<?php
		if ( 'yes' == $enable_enquiry ) :
			wp_travel_get_enquiries_form();
			endif;
		?>
	<?php
	/**
	 * @since 1.0.4
	 */
	do_action( 'wp_travel_single_after_booknow', $post_id );
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
					?> , 
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
	$terms = get_the_terms( $post_id, 'travel_locations' );

	$start_date    = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$end_date      = get_post_meta( $post_id, 'wp_travel_end_date', true );
	$show_end_date = wp_travel_booking_show_end_date();

	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	$trip_duration       = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;
	if ( is_array( $terms ) && count( $terms ) > 0 ) :
		?>
		<li class="no-border">
			<div class="travel-info">
				<strong class="title"><?php esc_html_e( 'Locations', 'wp-travel' ); ?></strong>
			</div>
			<div class="travel-info">
				<span class="value">
					<?php
					$i = 0;
					foreach ( $terms as $term ) :
						if ( $i > 0 ) :
							?> , 
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
	<?php if ( 'yes' === $fixed_departure ) : ?>
		<?php if ( $start_date || $end_date ) : ?>
			<li>
				<div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">
						<?php
						if ( $start_date || $end_date ) :
							$date_format = get_option( 'date_format' );
							if ( ! $date_format ) :
								$date_format = 'jS M, Y';
								endif;
							if ( '' !== $end_date && $show_end_date ) {
								printf( '%s - %s', date_i18n( $date_format, strtotime( $start_date ) ), date_i18n( $date_format, strtotime( $end_date ) ) );
							} else {
								printf( '%s', date_i18n( $date_format, strtotime( $start_date ) ) );
							}

							else :
								esc_html_e( 'N/A', 'wp-travel' );
							endif;
							?>
					</span>
				</div>
			</li>
		<?php endif; ?>
	<?php else : ?>
		<?php if ( $trip_duration || $trip_duration_night ) : ?>
			<li>
				<div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></strong>
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
	$enable_sale     = get_post_meta( $post_id, 'wp_travel_enable_sale', true );

	$trip_duration       = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$settings      = wp_travel_get_settings();
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text = wp_travel_get_price_per_text( $post_id );
	$sale_price      = wp_travel_get_trip_sale_price( $post_id );
	?>
	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper">
		<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
			<ul class="wp-travel tab-list resp-tabs-list ">
				<?php
				$index = 1;
				foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) : ?>
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
					case 'gallery':
						?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<?php if ( false !== $tab_info['content'] ) : ?>
							<div class="wp-travel-gallery wp-travel-container-wrap">
								<div class="wp-travel-row-wrap">
									<ul>
										<?php foreach ( $tab_info['content'] as $gallery_id ) : ?>
										<li>
											<?php $gallery_image = wp_get_attachment_image_src( $gallery_id, 'medium' ); ?>
											<a title="<?php echo esc_attr( wp_get_attachment_caption( $gallery_id ) ); ?>" href="<?php echo esc_url( wp_get_attachment_url( $gallery_id ) ); ?>">
											<img alt="" src="<?php echo esc_attr( $gallery_image[0] ); ?>" />
											</a>
										</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
							<?php else : ?>
								<p class="wp-travel-no-detail-found-msg"><?php esc_html_e( 'No gallery images found.', 'wp-travel' ); ?></p>
							<?php endif; ?>
						</div>
						<?php
						break;
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
								<a href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
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
							if ( apply_filters( 'wp_travel_trip_tabs_output_raw', false ) ) {

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

	if ( '' != $api_key && $show_google_map ) {
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
	$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );

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

	$tabs = wp_travel_get_frontend_tabs( $show_in_menu_query = true ); // fixes the content filter in page builder.

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

// Hooks.
add_action( 'wp_travel_after_single_title', 'wp_travel_trip_price', 1 );
add_action( 'wp_travel_after_single_title', 'wp_travel_single_excerpt', 1 );
add_action( 'wp_travel_single_after_booknow', 'wp_travel_single_keywords', 1 );
add_action( 'wp_travel_single_itinerary_after_trip_meta_list', 'wp_travel_single_location', 1 );
add_action( 'wp_travel_single_after_trip_price', 'wp_travel_single_trip_rating', 10, 2 );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_trip_facts' );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_contents', 20 );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_trip_map', 20 );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_related_itineraries', 25 );
add_filter( 'the_content', 'wp_travel_content_filter' );
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
add_filter( 'excerpt_more', 'wp_travel_excerpt_more' );

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
add_filter( 'wp_kses_allowed_html', 'wp_travel_wpkses_post_iframe', 10, 2 );

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
add_action( 'template_redirect', 'wp_travel_prevent_endpoint_indexing' );


/**
 * wp_travel_booking_tab_pricing_options_list
 *
 * @param array $trip_data
 * @return void
 */
function wp_travel_booking_tab_pricing_options_list( $trip_data = null ) {

	if ( '' == $trip_data ) {
		return;
	}
	global $wp_travel_itinerary;

	if ( is_array( $trip_data ) ) {
		global $post;
		$trip_id = $post->ID;
	} elseif ( is_numeric( $trip_data ) ) {
		$trip_id = $trip_data;
	}

	$js_date_format = wp_travel_date_format_php_to_js();

	$settings   = wp_travel_get_settings();
	$form       = new WP_Travel_FW_Form();
	$form_field = new WP_Travel_FW_Field();

	$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$show_end_date   = wp_travel_booking_show_end_date();
	$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );
	$trip_price      = wp_travel_get_trip_price( $trip_id );
	$enable_sale     = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );

	$trip_duration       = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$per_person_text = wp_travel_get_price_per_text( $trip_id );
	$sale_price      = wp_travel_get_trip_sale_price( $trip_id );

	$available_pax    = false;
	$booked_pax       = false;
	$pax_limit        = false;
	$general_sold_out = false;

	$status_col = apply_filters( 'wp_travel_inventory_enable_status_column', false, $trip_id );

	$status_msg           = get_post_meta( $trip_id, 'wp_travel_inventory_status_message_format', true );
	$sold_out_btn_rep_msg = apply_filters( 'wp_travel_inventory_sold_out_button', '', $trip_id );

	// Multiple Pricing.
	if ( is_array( $trip_data ) ) {
		if ( empty( $trip_data ) ) {
			return;
		}
		?>
		<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list additional-col">
					<li class="availabily-heading clearfix">
						<div class="date-from">
							<?php echo esc_html__( 'Pricing Name', 'wp-travel' ); ?>
						</div>
						<div class="date-from">
							<?php echo esc_html__( 'Start', 'wp-travel' ); ?>
						</div>
						<div class="status">
							<?php echo esc_html__( 'Min Group Size', 'wp-travel' ); ?>
						</div>
						<div class="status">
							<?php echo esc_html__( 'Max Group Size', 'wp-travel' ); ?>
						</div>
						<?php if ( $status_col ) : ?>
							<div class="status">
								<?php echo esc_html__( 'Status', 'wp-travel' ); ?>
							</div>
						<?php endif; ?>
						<div class="price">
							<?php echo esc_html__( 'Price', 'wp-travel' ); ?>
						</div>
						<div class="action">
							&nbsp;
						</div>
					</li>
					<?php
					foreach ( $trip_data as $price_key => $pricing ) :
						// Set Vars.
						$pricing_name         = isset( $pricing['pricing_name'] ) ? $pricing['pricing_name'] : '';
						$price_key            = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';
						$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
						$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';
						$pricing_option_price = isset( $pricing['price'] ) ? $pricing['price'] : '';
						$pricing_sale_enabled = isset( $pricing['enable_sale'] ) ? $pricing['enable_sale'] : '';
						$pricing_sale_price   = isset( $pricing['sale_price'] ) ? $pricing['sale_price'] : '';
						$pricing_min_pax      = isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : '';
						$pricing_max_pax      = isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : '';

						$available_dates = wp_travel_get_trip_available_dates( $trip_id, $price_key ); // No need to pass date

						$pricing_sold_out = false;

						$inventory_data = array(
							'status_message' => __( 'N/A', 'wp-travel' ),
							'sold_out'       => false,
							'available_pax'  => 0,
							'booked_pax'     => 0,
							'pax_limit'      => 0,
							'min_pax'        => $pricing_min_pax,
							'max_pax'        => $pricing_max_pax,
						);

						if ( is_array( $available_dates ) && count( $available_dates ) > 0 ) { // multiple available dates
							foreach ( $available_dates as $available_date ) {
								// echo $available_date;
								$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $available_date ); // Need to pass inventory date to get availability as per specific date.

								$pricing_status_msg = $inventory_data['status_message'];
								$pricing_sold_out   = $inventory_data['sold_out'];
								$available_pax      = $inventory_data['available_pax'];
								$booked_pax         = $inventory_data['booked_pax'];
								$pax_limit          = $inventory_data['pax_limit'];
								$min_pax            = $inventory_data['min_pax'];
								$max_pax            = $inventory_data['max_pax'];

								if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {
									$inventory = new WP_Travel_Util_Inventory();
									if ( $inventory->is_inventory_enabled( $trip_id ) && $available_pax ) {
										$pricing_max_pax = $available_pax;
									}
								}
								$max_attr  = 'max=' . $pricing_max_pax;
								$parent_id = sprintf( 'pricing-%s-%s', esc_attr( $price_key ), $available_date );

								$unavailable_class = '';
								$availability = wp_travel_trip_availability( $trip_id, $price_key, $available_date, $pricing_sold_out );
								if ( ! $availability ) {
									$unavailable_class = 'pricing_unavailable';
								}
								?>
								<li id="<?php echo esc_html( $parent_id ); ?>" class="availabily-content clearfix <?php echo esc_attr( $unavailable_class ) ?>">
									<div class="date-from">
										<span class="availabily-heading-label"><?php echo esc_html__( 'Pricing Name:', 'wp-travel' ); ?></span>
										<span> <?php echo esc_html( $pricing_name ); ?> </span>
									</div>
									<div class="date-from">
										<span class="availabily-heading-label"><?php echo esc_html__( 'Start:', 'wp-travel' ); ?></span>
										<span> <?php echo esc_html( wp_travel_format_date( $available_date ) ); ?> </span>
									</div>
									<div class="status">
										<span class="availabily-heading-label"><?php echo esc_html__( 'Min Group Size:', 'wp-travel' ); ?></span>
										<span><?php echo ! empty( $pricing_min_pax ) ? esc_html( $pricing_min_pax . __( ' pax', 'wp-travel' ) ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
									</div>
									<div class="status">
										<span class="availabily-heading-label"><?php echo esc_html__( 'Max Group Size:', 'wp-travel' ); ?></span>
										<span><?php echo ! empty( $pricing_max_pax ) ? esc_html( $pricing_max_pax . __( ' pax', 'wp-travel' ) ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
									</div>
									<?php
									if ( $status_col ) :

										if ( $pricing_sold_out ) :
											?>
											<div class="status">
												<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
												<span><?php echo esc_html__( 'SOLD OUT', 'wp-travel' ); ?></span>
											</div>
										<?php else : ?>
											<div class="status">
												<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
												<span><?php echo esc_html( $pricing_status_msg ); ?></span>

											</div>
											<?php
										endif;
									endif;
									?>
									<div class="price">
										<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
										<?php if ( '' !== $pricing_option_price || '0' !== $pricing_option_price ) : ?>

											<?php if ( 'yes' === $pricing_sale_enabled ) : ?>
												<del>
													<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price ); ?></span>
												</del>
											<?php endif; ?>
												<span class="person-count">
													<ins>
														<span>
															<?php
															if ( 'yes' === $pricing_sale_enabled ) {
																echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $pricing_sale_price ), $currency_symbol, $pricing_sale_price );
															} else {
																echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price );
															}
															?>
														</span>
													</ins>/<?php echo esc_html( $per_person_text ); ?>
												</span>
										<?php endif; ?>
									</div>
									<div class="action">
		
										<?php if ( $pricing_sold_out ) : ?>
		
											<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
		
										<?php else : ?>
											<a href="#0" class="btn btn-primary btn-sm btn-inverse show-booking-row"><?php echo esc_html__( 'Select', 'wp-travel' ); ?></a>
										<?php endif; ?>
									</div>
									<?php if ( $availability ) : // Remove Book now if trip is soldout. ?>
										<div class="wp-travel-booking-row">
											<?php
												/**
												 * Support For WP Travel Tour Extras Plugin.
												 *
												 * @since 1.5.8
												 */
												do_action( 'wp_travel_trip_extras', $price_key, $available_date );
											?>
											<div class="wp-travel-calender-aside">
												<?php
												$pricing_default_types = wp_travel_get_pricing_variation_options();

												$pricing_type_label = ( 'custom' === $pricing_type ) ? $pricing_custom_label : $pricing_default_types[ $pricing_type ];

												$max_attr = '';
												$min_attr = 'min=1';
												if ( '' !== $pricing_max_pax ) {

													$max_attr = 'max=' . $pricing_max_pax;
												}
												if ( '' !== $pricing_min_pax ) {
													$min_attr = 'min=' . $pricing_min_pax;
												}

												?>
												<div class="col-sm-3">
													<label for=""><?php echo esc_html( ucfirst( $pricing_type_label ) ); ?></label>
													<input name="pax" type="number" <?php echo esc_attr( $min_attr ); ?> <?php echo esc_attr( $max_attr ); ?> placeholder="<?php echo esc_attr__( 'size', 'wp-travel' ); ?>" required data-parsley-trigger="change">
												</div>
												<div class="add-to-cart">
													<input type="hidden" name="trip_date" value="<?php echo esc_attr( $available_date ); ?>" >
													<input type="hidden" name="trip_id" value="<?php echo esc_attr( get_the_ID() ); ?>" />
													<input type="hidden" name="price_key" value="<?php echo esc_attr( $price_key ); ?>" />
													<?php
														$button   = '<a href="%s" data-parent-id="' . $parent_id . '" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse">%s</a>';
														$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
													if ( 'yes' !== $fixed_departure ) :
														$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url );
														?>
															<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ); ?>" />
														<?php
														endif;

														$cart_url = add_query_arg( 'price_key', $price_key, $cart_url );
														printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
													?>
												</div>
											</div>
										</div>
									<?php endif; ?>
								</li>
								<?php
							}
						} else {
							$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key ); // Need to pass inventory date to get availability as per specific date.

							$pricing_status_msg = $inventory_data['status_message'];
							$pricing_sold_out   = $inventory_data['sold_out'];
							$available_pax      = $inventory_data['available_pax'];
							$booked_pax         = $inventory_data['booked_pax'];
							$pax_limit          = $inventory_data['pax_limit'];
							$min_pax            = $inventory_data['min_pax'];
							$max_pax            = $inventory_data['max_pax'];

							if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {
								$inventory = new WP_Travel_Util_Inventory();
								if ( $inventory->is_inventory_enabled( $trip_id ) && $available_pax ) {
									$pricing_max_pax = $available_pax;
								}
							}
							$max_attr = 'max=' . $pricing_max_pax;
							?>
							<li id="pricing-<?php echo esc_attr( $price_key ); ?>" class="availabily-content clearfix">
								<div class="date-from">
									<span class="availabily-heading-label"><?php echo esc_html__( 'Pricing Name:', 'wp-travel' ); ?></span> <span><?php echo esc_html( $pricing_name ); ?></span>
								</div>
								<div class="date-from">
									<span class="availabily-heading-label"><?php echo esc_html__( 'Start:', 'wp-travel' ); ?></span>
									<span></span>
								</div>
								<div class="status">
									<span class="availabily-heading-label"><?php echo esc_html__( 'Min Group Size:', 'wp-travel' ); ?></span>
									<span><?php echo ! empty( $pricing_min_pax ) ? esc_html( $pricing_min_pax . __( ' pax', 'wp-travel' ) ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
								</div>
								<div class="status">
									<span class="availabily-heading-label"><?php echo esc_html__( 'Max Group Size:', 'wp-travel' ); ?></span>
									<span><?php echo ! empty( $pricing_max_pax ) ? esc_html( $pricing_max_pax . __( ' pax', 'wp-travel' ) ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
								</div>
								<?php
								if ( $status_col ) :

									if ( $pricing_sold_out ) :
										?>
										<div class="status">
											<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
											<span><?php echo esc_html__( 'SOLD OUT', 'wp-travel' ); ?></span>
										</div>
									<?php else : ?>
										<div class="status">
											<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
											<span><?php echo esc_html( $pricing_status_msg ); ?></span>
										</div>
										<?php
									endif;
														endif;
								?>
								<div class="price">
									<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
									<?php if ( '' !== $pricing_option_price || '0' !== $pricing_option_price ) : ?>
	
										<?php if ( 'yes' === $pricing_sale_enabled ) : ?>
											<del>
												<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price ); ?></span>
											</del>
										<?php endif; ?>
											<span class="person-count">
												<ins>
													<span>
														<?php
														if ( 'yes' === $pricing_sale_enabled ) {
															echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $pricing_sale_price ), $currency_symbol, $pricing_sale_price );
														} else {
															echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price );
														}
														?>
													</span>
												</ins>/<?php echo esc_html( $per_person_text ); ?>
											</span>
									<?php endif; ?>
								</div>
								<div class="action">
	
															<?php if ( $pricing_sold_out ) : ?>
	
										<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
	
									<?php else : ?>
										<a href="#0" class="btn btn-primary btn-sm btn-inverse show-booking-row"><?php echo esc_html__( 'Select', 'wp-travel' ); ?></a>
									<?php endif; ?>
								</div>
								<div class="wp-travel-booking-row">
															<?php
																/**
																 * Support For WP Travel Tour Extras Plugin.
																 *
																 * @since 1.5.8
																 */
																do_action( 'wp_travel_trip_extras', $price_key );
															?>
									<div class="wp-travel-calender-column no-padding ">
	
										<label for=""><?php echo esc_html__( 'Select a Date:', 'wp-travel' ); ?></label>
										<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="trip_date" type="text" data-available-dates="<?php echo ( $available_dates ) ? esc_attr( wp_json_encode( $available_dates ) ) : ''; ?>" readonly class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
	
									</div>
									<div class="wp-travel-calender-aside">
										<?php
										$pricing_default_types = wp_travel_get_pricing_variation_options();

										$pricing_type_label = ( 'custom' === $pricing_type ) ? $pricing_custom_label : $pricing_default_types[ $pricing_type ];

										$max_attr = '';
										$min_attr = 'min=1';
										if ( '' !== $pricing_max_pax ) {

											$max_attr = 'max=' . $pricing_max_pax;
										}
										if ( '' !== $pricing_min_pax ) {
											$min_attr = 'min=' . $pricing_min_pax;
										}

										?>
										<div class="col-sm-3">
											<label for=""><?php echo esc_html( ucfirst( $pricing_type_label ) ); ?></label>
											<input name="pax" type="number" <?php echo esc_attr( $min_attr ); ?> <?php echo esc_attr( $max_attr ); ?> placeholder="<?php echo esc_attr__( 'size', 'wp-travel' ); ?>" required data-parsley-trigger="change">
										</div>
										<div class="add-to-cart">
											<input type="hidden" name="trip_id" value="<?php echo esc_attr( get_the_ID() ); ?>" />
											<input type="hidden" name="price_key" value="<?php echo esc_attr( $price_key ); ?>" />
											<?php
												$button   = '<a href="%s" data-parent-id="pricing-' . esc_attr( $price_key ) . '" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse">%s</a>';
												$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
											if ( 'yes' !== $fixed_departure ) :
												$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url );
												?>
													<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ); ?>" />
												<?php
												endif;

												$cart_url = add_query_arg( 'price_key', $price_key, $cart_url );
												printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
											?>
										</div>
									</div>
								</div>
							</li>
													<?php
						}
					endforeach;
					?>
				</ul>
			</div>
		</div>
		<?php

	} elseif ( is_numeric( $trip_data ) ) { // Single Pricing
		$inventory_data = array(
			'status_message' => __( 'N/A', 'wp-travel' ),
			'sold_out'       => false,
			'available_pax'  => 0,
			'booked_pax'     => 0,
			'pax_limit'      => 0,
			'min_pax'        => '',
			'max_pax'        => 0,
		);

		$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, '' );

		$pricing_status_msg = $inventory_data['status_message'];
		$pricing_sold_out   = $inventory_data['sold_out'];
		$available_pax      = $inventory_data['available_pax'];
		$booked_pax         = $inventory_data['booked_pax'];
		$pax_limit          = $inventory_data['pax_limit'];
		$min_pax            = $inventory_data['min_pax'];
		$max_pax            = $inventory_data['max_pax'];
		?>
		<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list <?php echo 'yes' === $fixed_departure ? 'additional-col' : ''; ?>">
					<li class="availabily-heading clearfix">
						<div class="date-from">
							<?php echo esc_html__( 'Start', 'wp-travel' ); ?>
						</div>
						<?php if ( $show_end_date ) : ?>
							<div class="date-to">
								<?php echo esc_html__( 'End', 'wp-travel' ); ?>
							</div>
						<?php endif; ?>
						<div class="status">
							<?php echo esc_html__( 'Group Size', 'wp-travel' ); ?>
						</div>
						<?php if ( $status_col ) : ?>
							<div class="status">
								<?php echo esc_html__( 'Status', 'wp-travel' ); ?>
							</div>
						<?php endif; ?>
						<div class="price">
							<?php echo esc_html__( 'Price', 'wp-travel' ); ?>
						</div>
						<div class="action">
							&nbsp;
						</div>
					</li>
					<li class="availabily-content clearfix" id="trip-duration-content">
						<?php if ( 'yes' == $fixed_departure ) : ?>
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html__( 'start:', 'wp-travel' ); ?></span>
								<?php echo esc_html( date_i18n( 'l', strtotime( $trip_start_date ) ) ); ?>
								<?php $date_format = get_option( 'date_format' ); ?>
								<?php if ( ! $date_format ) : ?>
									<?php $date_format = 'jS M, Y'; ?>
								<?php endif; ?>
								<span><?php echo esc_html( date_i18n( $date_format, strtotime( $trip_start_date ) ) ); ?></span>
								<input type="hidden" name="trip_date" value="<?php echo esc_attr( $trip_start_date ); ?>">
							</div>
							<?php
							if ( $show_end_date ) :
								?>
								<div class="date-to">
									<?php if ( '' !== $trip_end_date ) : ?>
										<span class="availabily-heading-label"><?php echo esc_html__( 'end:', 'wp-travel' ); ?></span>
										<?php echo esc_html( date_i18n( 'l', strtotime( $trip_end_date ) ) ); ?>
										<span><?php echo esc_html( date_i18n( $date_format, strtotime( $trip_end_date ) ) ); ?></span>
										<input type="hidden" name="trip_departure_date" value="<?php echo esc_attr( $trip_end_date ); ?>">
									<?php else : ?>
										<?php esc_html_e( '-', 'wp-travel' ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						<?php else : ?>
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html__( 'start:', 'wp-travel' ); ?></span>
								<?php
								$total_days = 0;
								if ( $trip_duration > 0 || $trip_duration_night > 0 ) {
									$days = $trip_duration > $trip_duration_night ? $trip_duration : $trip_duration_night;
									$days--; // As we need to exclude current selected date.
									$total_days = $days ? $days : $total_days;
								}
								$start_field = array(
									'label'         => esc_html__( 'start', 'wp-travel' ),
									'type'          => 'date',
									'name'          => 'trip_date',
									'placeholder'   => esc_html__( 'Arrival date', 'wp-travel' ),
									'class'         => 'wp-travel-pricing-days-night',
									'validations'   => array(
										'required' => true,
									),
									'attributes'    => array(
										'data-parsley-trigger' => 'change',
										'data-parsley-required-message' => esc_attr__( 'Please Select a Date', 'wp-travel' ),
										'data-totaldays' => $total_days,
									),
									'wrapper_class' => 'date-from',
								);
								$form_field->init()->render_input( $start_field );
								?>
							</div>
							<div class="date-to">
								<?php
								$end_field = array(
									'label'       => esc_html__( 'End', 'wp-travel' ),
									'type'        => 'date',
									'name'        => 'trip_departure_date',
									'placeholder' => esc_html__( 'Departure date', 'wp-travel' ),
								);
								$end_field = wp_parse_args( $end_field, $start_field );
								$form_field->init()->render_input( $end_field );
								?>
							</div>
						<?php endif; ?>
						<div class="status">
							<span class="availabily-heading-label"><?php echo esc_html__( 'Group Size:', 'wp-travel' ); ?></span>
							<span><?php echo esc_html( wp_travel_get_group_size() ); ?></span>
						</div>
						<?php if ( $status_col ) : ?>

							<div class="status">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
								<span><?php echo esc_html( $pricing_status_msg ); ?></span>
							</div>

							<?php endif; ?>
						<?php
						if ( class_exists( 'WP_Travel_Util_Inventory' ) && ! $trip_price ) :
							// display price unavailable text
							$no_price_text = isset( $settings['price_unavailable_text'] ) && '' !== $settings['price_unavailable_text'] ? $settings['price_unavailable_text'] : '';
							echo '<div class="price"><strong>' . esc_html( $no_price_text ) . '</strong></div>';
						else :
							?>
							<div class="price">
								<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
								<?php if ( $enable_sale ) : ?>
									<del>
										<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
									</del>
								<?php endif; ?>
								<span class="person-count">
									<ins>
										<span>
											<?php
											if ( $enable_sale ) {
												echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price );
											} else {
												echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price );
											}
											?>
										</span>
									</ins>/<?php echo esc_html( $per_person_text ); ?>
								</span>
							</div>
						<?php endif; ?>
						<div class="action">
							<?php
							// if ( $inventory_enabled && $general_sold_out ) :
							if ( $general_sold_out ) :
								?>

								<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>

							<?php else : ?>
								<?php $pax = 1; ?>

									<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ); ?>">
									<input type="hidden" name="pax" value="<?php echo esc_attr( $pax ); ?>">
									<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ); ?>">
									<input type="hidden" name="trip_duration_night" value="<?php echo esc_attr( $trip_duration_night ); ?>">
									<?php
										$button   = '<a href="%s" class="btn btn-primary add-to-cart-btn btn-sm btn-inverse" data-parent-id="trip-duration-content">%s</a>';
										$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
									if ( 'yes' !== $fixed_departure ) :
										$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url );
										endif;
										printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
									?>
							<?php endif; ?>
						</div>
					</li>
				</ul>
				<?php do_action( 'wp_travel_trip_extras' ); ?>
			</div>
		</div>
		<?php
	}

}
add_action( 'wp_travel_booking_princing_options_list', 'wp_travel_booking_tab_pricing_options_list' );

/**
 * WP Travel Fixed Departure listing
 *
 * @return void
 */
function wp_travel_booking_fixed_departure_listing( $trip_multiple_dates_data ) {

	if ( empty( $trip_multiple_dates_data ) || ! is_array( $trip_multiple_dates_data ) ) {
		return;
	}

	global $post;

	if ( ! $post ) {
		return;
	}

	$trip_id = $post->ID;

	$status_col           = apply_filters( 'wp_travel_inventory_enable_status_column', false, $trip_id );
	$status_msg           = get_post_meta( $trip_id, 'wp_travel_inventory_status_message_format', true );
	$sold_out_btn_rep_msg = apply_filters( 'wp_travel_inventory_sold_out_button', '', $trip_id );

	$settings          = wp_travel_get_settings();
	$currency_code     = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol   = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text   = wp_travel_get_price_per_text( $trip_id );
	$inventory_enabled = false;
	$show_end_date     = wp_travel_booking_show_end_date();

	?>
	<div class="trip_list_by_fixed_departure_dates">
		<div class="trip_list_by_fixed_departure_dates_header">
			<span class="trip_list_by_fixed_departure_dates_wrap">
				<span class="trip_list_by_fixed_departure_dates_pricing_name_label"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></span>
				<span class="trip_list_by_fixed_departure_dates_start_label"><?php esc_html_e( 'START', 'wp-travel' ); ?></span>
				<?php if ( $show_end_date ) : ?>
					<span class="trip_list_by_fixed_departure_dates_end_label"><?php esc_html_e( 'END', 'wp-travel' ); ?></span>
				<?php endif ?>
				<?php if ( $status_col ) : ?>
					<span class="trip_list_by_fixed_departure_dates_seats_label"><?php esc_html_e( 'SEATS LEFT', 'wp-travel' ); ?></span>
				<?php endif; ?>
				<span class="trip_list_by_fixed_departure_dates_pax_label"><?php esc_html_e( 'PAX', 'wp-travel' ); ?></span>
				<span class="trip_list_by_fixed_departure_dates_price_label"><?php esc_html_e( 'PRICE', 'wp-travel' ); ?></span>
			</span>
		</div>
		<ul class="trip_list_by_fixed_departure_dates_list">
			<?php
			foreach ( $trip_multiple_dates_data as $ky => $date_option ) :

				$start_date      = isset( $date_option['start_date'] ) && ! empty( $date_option['start_date'] ) ? $date_option['start_date'] : '';
				$end_date        = isset( $date_option['end_date'] ) && ! empty( $date_option['end_date'] ) ? $date_option['end_date'] : '';
				$pricing_options = isset( $date_option['pricing_options'] ) && ! empty( $date_option['pricing_options'] ) ? $date_option['pricing_options'] : array();
				?>
				<?php if ( ! empty( $pricing_options ) && is_array( $pricing_options ) ) : ?>
					<?php
					foreach ( $pricing_options as $indx => $price_key ) :
						$variation = wp_travel_get_pricing_variation( $trip_id, $price_key );

						if ( ! $variation ) {
							continue;
						}

						foreach ( $variation as $k => $var ) :

							$rand = rand();

							$pricing_name         = isset( $var['pricing_name'] ) ? $var['pricing_name'] : '';
							$min_pax              = isset( $var['min_pax'] ) && ! empty( $var['min_pax'] ) ? sprintf( __( '%s Pax', 'wp-travel' ), $var['min_pax'] ) : false;
							$max_pax              = isset( $var['max_pax'] ) && ! empty( $var['min_pax'] ) ? sprintf( __( '%s Pax', 'wp-travel' ), $var['max_pax'] ) : false;
							$pricing_sale_enabled = isset( $var['enable_sale'] ) ? $var['enable_sale'] : 'no';
							$pricing_sale_price   = isset( $var['sale_price'] ) ? $var['sale_price'] : '';
							$pricing_option_price = isset( $var['price'] ) ? $var['price'] : '';
							$price_key            = isset( $var['price_key'] ) ? $var['price_key'] : '';

							$price_variations = wp_travel_get_pricing_variation_options();
							$per_label        = isset( $var['type'] ) && 'custom' !== $var['type'] ? $price_variations[ $var['type'] ] : ucfirst( $var['custom_label'] );

							$inventory_data = array(
								'status_message' => __( 'N/A', 'wp-travel' ),
								'sold_out'       => false,
								'available_pax'  => 0,
								'booked_pax'     => 0,
								'pax_limit'      => 0,
								'min_pax'        => $min_pax,
								'max_pax'        => $max_pax,
							);

							$inventory_data = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, $price_key, $start_date );

							$pricing_status_msg = $inventory_data['status_message'];
							$pricing_sold_out   = $inventory_data['sold_out'];
							$available_pax      = $inventory_data['available_pax'];
							$booked_pax         = $inventory_data['booked_pax'];
							$pax_limit          = $inventory_data['pax_limit'];
							$min_pax            = $inventory_data['min_pax'];
							$max_pax            = $inventory_data['max_pax'];

							$min_attr = 'min=1';
							if ( $min_pax ) {
								$min_attr = 'min=' . $min_pax;
							}
							$max_attr = '';
							if ( $max_pax ) {
								$max_attr = 'max=' . $max_pax;
							}

							$unavailable_class = '';
							$availability = wp_travel_trip_availability( $trip_id, $price_key, $start_date, $pricing_sold_out );
							if ( ! $availability ) {
								$unavailable_class = 'pricing_unavailable';
							}
							?>
						<li class="<?php echo esc_attr( $unavailable_class ); ?>" id="princing-<?php echo esc_attr( $price_key ); ?>-<?php echo esc_attr( $rand ); ?>">
							<div class="trip_list_by_fixed_departure_dates_wrap">
								<span class="trip_list_by_fixed_departure_dates_pricing_name"><?php echo $pricing_name; ?></span>
								<span class="trip_list_by_fixed_departure_dates_start">
									<div class="trip_list_by_fixed_departure_dates_day"><?php echo esc_html( date_i18n( 'l', strtotime( $start_date ) ) ); ?></div>
									<div class="trip_list_by_fixed_departure_dates_date"><?php echo esc_html( wp_travel_format_date( $start_date ) ); ?>
									<input type="hidden" name="trip_date" value="<?php echo esc_attr( $start_date ); ?>">
									</div>
									<?php if ( $show_end_date && '' !== $end_date ) : ?>
										<div class="trip_list_by_fixed_departure_dates_length">
											<div><?php echo esc_html( wp_travel_get_date_diff( $start_date, $end_date ) ); ?></div>
										</div>
									<?php endif ?>
								</span>

								<?php if ( $show_end_date ) : ?>
									<span class="trip_list_by_fixed_departure_dates_end">
										<?php if ( '' !== $end_date ) : ?>
											<div class="trip_list_by_fixed_departure_dates_day"><?php echo esc_html( date( 'l', strtotime( $end_date ) ) ); ?></div>
											<div class="trip_list_by_fixed_departure_dates_date"><?php echo esc_html( wp_travel_format_date( $end_date ) ); ?></div>
										<?php endif ?>
									</span>
								<?php endif; ?>
								<?php if ( $status_col ) : ?>
									<span class="trip_list_by_fixed_departure_dates_seats">
										<!-- <span class="seat_qty"><?php echo esc_html( $available_pax ); ?></span> -->
										<?php echo esc_html( $pricing_status_msg ); ?>
									</span>
								<?php endif; ?>
								<span class="trip_list_by_fixed_departure_dates_pax">
									<input <?php echo esc_attr( $min_attr ); ?> <?php echo esc_attr( $max_attr ); ?> name="pax" placeholder="<?php echo esc_attr__( 'PAX', 'wp-travel' ); ?>" required type="number">
								</span>
								<span class="trip_list_by_fixed_departure_dates_price">
								<?php
									$display_price = $pricing_option_price;
								if ( 'yes' === $pricing_sale_enabled ) {
									$display_price = $pricing_sale_price;
								}
								?>
								<?php if ( 'yes' === $pricing_sale_enabled ) { ?>
									<div class="del_price"><?php echo wp_travel_get_currency_symbol(); ?> <?php echo( esc_html( $pricing_option_price ) ); ?></div>
									<div class="minus_ribbon">
										<p><?php esc_html_e( 'sale', 'wp-travel' ); ?></p>
									</div>
								<?php } ?>
									<div class="real_price">
									<?php echo wp_travel_get_currency_symbol(); ?> <?php echo( esc_html( $display_price ) ); ?>
										<!--<i class="wt-icon wt-icon-rocket"></i>-->
									</div>
									<?php echo __( 'Per ', 'wp-travel' ) . esc_html( $per_label ); ?>
								</span>
							</div>
							<div class="trip_list_by_fixed_departure_dates_booking">
								<div class="action">
									<?php
									$trip_extras_class = new Wp_Travel_Extras_Frontend();
									if ( $pricing_sold_out ) {
										?>
										<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>

									<?php } else { ?>
										<?php if ( $trip_extras_class->has_trip_extras( $trip_id, $price_key ) ) { ?>
											<a href="#0" class="btn btn-primary btn-sm btn-inverse show-booking-row-fd"><?php echo esc_html__( 'Select', 'wp-travel' ); ?></a>
											<?php
										} else {
											$button   = '<a href="%s" data-parent-id="princing-' . esc_attr( $price_key ) . '-' . esc_attr( $rand ) . '" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse">%s</a>';
											$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );

											$cart_url = add_query_arg( 'price_key', $price_key, $cart_url );
											printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
										}
									}
									?>
								</div>

							</div>
							<?php if ( $availability ) : ?>
								<div class="wp-travel-booking-row-fd">
									<?php
									/**
									 * Support For WP Travel Tour Extras Plugin.
									 *
									 * @since 1.5.8
									 */
									do_action( 'wp_travel_trip_extras', $price_key );
									?>
									<input type="hidden" name="trip_id" value="<?php echo esc_attr( get_the_ID() ); ?>" />
									<input type="hidden" name="price_key" value="<?php echo esc_attr( $price_key ); ?>" />
									<?php if ( $pricing_sold_out ) : ?>
										<p class="wp-travel-sold-out">
											<?php echo $sold_out_btn_rep_msg; ?>
										</p>
										<?php
									else :
										$button   = '<a href="%s" data-parent-id="princing-' . esc_attr( $price_key ) . '-' . esc_attr( $rand ) . '" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse">%s</a>';
										$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );

										$cart_url = add_query_arg( 'price_key', $price_key, $cart_url );
										printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
										endif;
									?>
								</div>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					<?php endforeach; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Pricing options not found.', 'wp-travel' ); ?></p>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
}

add_action( 'wp_travel_booking_departure_date_list', 'wp_travel_booking_fixed_departure_listing' );

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
add_filter( 'jetpack_relatedposts_filter_options', 'wp_travel_remove_jetpack_related_posts' );

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

add_filter( 'get_header_image_tag', 'wp_travel_get_header_image_tag', 10 );
