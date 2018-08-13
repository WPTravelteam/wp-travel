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
	$template_path = apply_filters( 'wp_travel_template_path',  'wp-travel/' );
	$default_path = sprintf( '%s/templates/', plugin_dir_path( dirname( __FILE__ ) ) );

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
	$template = '';
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
		case 'twentyeleven' :
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
			break;
		case 'twentytwelve' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
			break;
		case 'twentythirteen' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
			break;
		case 'twentyfourteen' :
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfWSC">';
			break;
		case 'twentyfifteen' :
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15WSC">';
			break;
		case 'twentysixteen' :
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
			break;
		case 'twentyseventeen' :
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><div id="main" class="site-main">';
			break;
		default :
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
		case 'twentyeleven' :
			echo '</div></div>';
			break;
		case 'twentytwelve' :
			echo '</div></div>';
			break;
		case 'twentythirteen' :
			echo '</div></div>';
			break;
		case 'twentyfourteen' :
			echo '</div></div></div>';
			get_sidebar( 'content' );
			break;
		case 'twentyfifteen' :
			echo '</div></div>';
			break;
		case 'twentysixteen' :
			echo '</div></main>';
			break;
		case 'twentyseventeen' :
			echo '</div></div></div>';
			break;
		default :
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
	$settings = wp_travel_get_settings();
	$trip_price 	= wp_travel_get_trip_price( $post_id );

	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	$sale_price 	= wp_travel_get_trip_sale_price( $post_id );
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text = wp_travel_get_price_per_text( $post_id );

	// $wp_travel_itinerary = new WP_Travel_Itinerary();
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
	$average_rating = wp_travel_get_average_rating(); ?>
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
	$average_rating = wp_travel_get_average_rating(); ?>
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
	//Get Settings
	$settings = wp_travel_get_settings();

	$enquery_global_setting = isset( $settings['enable_trip_enquiry_option'] ) ? $settings['enable_trip_enquiry_option'] : 'yes';

	$global_enquiry_option = get_post_meta( $post_id, 'wp_travel_use_global_trip_enquiry_option', true );

	if ( '' === $global_enquiry_option  ) {
		$global_enquiry_option = 'yes';
	}
	if( 'yes' == $global_enquiry_option ) {

		$enable_enquiry = $enquery_global_setting;
		
	}
	else {
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
								printf( apply_filters( 'wp_travel_template_group_size_text' ,__( '%d pax', 'wp-travel' ) ), esc_html( $group_size ) );
						} else {
							echo esc_html( apply_filters( 'wp_travel_default_group_size_text', __( 'No Size Limit', 'wp-travel' ) ) );
						}
						?>
					</span>
				</div>
	  	 	</li>
			<?php 
			$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();
			
			if ( is_array( $wp_travel_itinerary_tabs ) && 'no' !== $wp_travel_itinerary_tabs['reviews']['show_in_menu'] && comments_open() ) : ?>
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
					echo '</a>'; ?>
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
			$booking_tab = $wp_travel_itinerary_tabs['booking'];
		
			if ( isset( $booking_tab['show_in_menu'] ) && 'yes' === $booking_tab['show_in_menu'] ) :	?>
			<button class="wp-travel-booknow-btn"><?php echo esc_html( apply_filters( 'wp_travel_template_book_now_text', __( 'Book Now', 'wp-travel' ) ) ); ?></button>
			<?php endif; ?>
			<?php if ( 'yes' == $enable_enquiry ) : ?>
			
				<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
					<span class="wp-travel-booking-enquiry">
						<span class="dashicons dashicons-editor-help"></span>
						<span>
							<?php esc_html_e( 'Trip Enquiry', 'wp-travel'); ?>
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
	if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
		<div class="wp-travel-keywords">
		<span class="label"><?php esc_html_e( 'Keywords : ', 'wp-travel' ) ?></span>
		<?php $i = 0; ?>
		<?php foreach ( $terms as $term ) : ?>
			<?php if ( $i > 0 ) : ?>,
			<?php endif; ?>
			<span class="wp-travel-keyword"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ) ?>"><?php echo esc_html( $term->name ); ?></a></span>
		<?php $i++; endforeach; ?>
		</div>
	<?php
	endif;
	global $wp_travel_itinerary;
	if ( is_singular( WP_TRAVEL_POST_TYPE ) ) : ?>
		<div class="wp-travel-trip-code"><span><?php esc_html_e( 'Trip Code', 'wp-travel' ) ?> :</span><code><?php echo esc_html( $wp_travel_itinerary->get_trip_code() ) ?></code></div>
	<?php endif; 

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

	$start_date	= get_post_meta( $post_id, 'wp_travel_start_date', true );
	$end_date 	= get_post_meta( $post_id, 'wp_travel_end_date', true );
	
	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;
	if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
		<li class="no-border">
			<div class="travel-info">
				<strong class="title"><?php esc_html_e( 'Locations', 'wp-travel' ); ?></strong>
			</div>
			<div class="travel-info">
				<span class="value"><?php $i = 0; ?><?php foreach ( $terms as $term ) : ?><?php if ( $i > 0 ) : ?>, <?php endif; ?><span class="wp-travel-locations"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ) ?>"><?php echo esc_html( $term->name ); ?></a></span><?php $i++; endforeach; ?></span>
			</div>
		</li>
	<?php endif; ?>
		<li>
			<?php if ( 'yes' === $fixed_departure ) : ?>
				<?php if ( $start_date && $end_date ) :  ?>
					<div class="travel-info">
						<strong class="title"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></strong>
					</div>
					<div class="travel-info">
						<span class="value">
							<?php $date_format = get_option( 'date_format' ); ?>
							<?php if ( ! $date_format ) : ?>
								<?php $date_format = 'jS M, Y'; ?>
							<?php endif; ?>
							<?php printf( '%s - %s', date( $date_format, strtotime( $start_date ) ), date( $date_format, strtotime( $end_date ) ) ); ?> 
						</span>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( $trip_duration || $trip_duration_night ) : ?>
					<div class="travel-info">
						<strong class="title"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></strong>
					</div>
					<div class="travel-info">
						<span class="value">
							<?php printf( __( '%s Day(s) %s Night(s)', 'wp-travel' ), $trip_duration, $trip_duration_night ); ?>
						</span>
					</div>
				<?php endif; ?>

			<?php endif; ?>
		</li>
	<?php

}

/**
 * wp_travel_frontend_trip_facts Frontend facts content.
 *
 * @since 1.3.2
 */
function wp_travel_frontend_trip_facts(  $post_id ) {

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

	if ( is_string( $wp_travel_trip_facts ) && '' != $wp_travel_trip_facts ){

		$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts,true );
	}

	if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) { 
	?>
		<!-- TRIP FACTS -->
		<div class="tour-info">
			<div class="tour-info-box clearfix">
				<div class="tour-info-column clearfix">
					<?php foreach ( $wp_travel_trip_facts as $key => $trip_fact ) : ?>
						<?php

							$icon = array_filter( $settings['wp_travel_trip_facts_settings'], function( $setting ) use ( $trip_fact ) {

								return $setting['name'] === $trip_fact['label'];
							} );

						foreach ( $icon as $key => $ico ) {

							$icon = $ico['icon'];
						}
						if ( isset( $trip_fact['value'] ) ) :
						?>
							<span class="tour-info-item tour-info-type">
								<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								<strong><?php echo esc_html( $trip_fact['label'] );?></strong>: 
								<?php 
								if ( $trip_fact['type'] === 'multiple' ) {
									$count = count( $trip_fact['value'] );
									$i = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										echo esc_html( $val );
										if ( $count > 1 && $i !== $count ) {
											echo esc_html( ',', 'wp-travel' );
										}
									$i++;
									}

								}
								else {
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
	$trip_content	= $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
	$trip_outline	= $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
	$trip_include	= $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
	$trip_exclude	= $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
	$gallery_ids 	= $wp_travel_itinerary->get_gallery_ids();

	$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();
	
	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );

	$trip_start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$trip_end_date 	= get_post_meta( $post_id, 'wp_travel_end_date', true );
	$trip_price 	= wp_travel_get_trip_price( $post_id );
	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );

	$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$settings = wp_travel_get_settings();
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text = wp_travel_get_price_per_text( $post_id );
	$sale_price 	= wp_travel_get_trip_sale_price( $post_id ); ?>
	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper">
		<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
		<ul class="wp-travel tab-list resp-tabs-list ">
			<?php $index = 1; ?>
			<?php foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) : ?>
				<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php $tab_label = $tab_info['label'] ;?>
				<li class="wp-travel-ert <?php echo esc_attr( $tab_key ); ?> <?php echo esc_attr( $tab_info['label_class'] ); ?> tab-<?php echo esc_attr( $index ); ?>" data-tab="tab-<?php echo esc_attr( $index ); ?>-cont"><?php echo esc_attr( $tab_label ); ?></li>
			<?php $index++; endforeach; ?>
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
				<?php switch ( $tab_key ) {
					case 'gallery' : ?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<?php if ( false !== $tab_info['content'] ) : ?>
							<div class="wp-travel-gallery wp-travel-container-wrap">
								<div class="wp-travel-row-wrap">
									<ul>
										<?php foreach ( $tab_info['content'] as $gallery_id ) : ?>
										<li>
											<?php $gallery_image = wp_get_attachment_image_src( $gallery_id, 'medium' );  ?>
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
					<?php break;
					case 'reviews' : ?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<?php comments_template(); ?>
						</div>
					<?php break;
					case 'booking' :

						$booking_template = apply_filters( 'wp_travel_booking_cart_process_template_path', wp_travel_get_template( 'content-pricing-options.php' ) );

						load_template( $booking_template );
					
					break;
					case 'faq' : ?>
					<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
						<div class="panel-group" id="accordion">
					<?php
						$faqs = wp_travel_get_faqs( $post_id );
						if ( is_array( $faqs ) && count( $faqs ) > 0 ) { ?>
							<div class="wp-collapse-open clearfix">
								<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e('Open All', 'wp-travel'); ?></span></a>
								<a href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e('Close All', 'wp-travel'); ?></span></a>
							</div>
						<?php foreach ( $faqs as $k => $faq ) : ?>
							<div class="panel panel-default">
						    <div class="panel-heading">
						      <h4 class="panel-title">
						        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k +1 ) ?>">
						          <?php echo esc_html( $faq['question'] ) ?>
						          <span class="collapse-icon"></span>
						        </a>
						      </h4>
						    </div>
						    <div id="collapse<?php echo esc_attr( $k +1 ) ?>" class="panel-collapse collapse">
						      <div class="panel-body">
						        <?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
						      </div>
						    </div>
						  </div>
							<?php endforeach;
						}
						else { ?>
							<div class="while-empty">
								<p class="wp-travel-no-detail-found-msg" >
									<?php esc_html_e( 'No Details Found', 'wp-travel' ); ?>
								</p>
							</div>
						<?php } ?>
						</div>
					</div>
					<?php break;
					case 'trip_outline' : ?>
					<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
						<?php echo wp_kses_post( $tab_info['content'] ); ?>

					<?php $itineraries = get_post_meta( $post_id, 'wp_travel_trip_itinerary_data' ); ?>
						<?php if ( isset( $itineraries[0] ) && ! empty( $itineraries[0] ) ) : ?>
					
						<div class="itenary clearfix">
							<div class="timeline-contents clearfix">
								<h2><?php esc_html_e( 'Itineraries', 'wp-travel' ) ?></h2>
									<?php $index = 1; ?>
									<?php foreach ( $itineraries[0] as $key => $itinerary ) : ?>
										<?php if ( $index % 2 === 0 ) : ?>
											<?php
												$first_class  = 'right';
												$second_class = 'left';
												$row_reverse = 'row-reverse';
											?>
										<?php else : ?>
											<?php
												$first_class  = 'left';
												$second_class = 'right';
												$row_reverse = '';
											?>
										<?php endif; ?>
										<?php

										$date_format = get_option('date_format');
										$time_format = get_option('time_format');

										$itinerary_label = '';
										$itinerary_title = '';
										$itinerary_desc  = '';
										$itinerary_date  = '';
										$itinerary_time  = '';
										if ( isset( $itinerary['label'] ) && '' !== $itinerary['label'] ) {
											$itinerary_label = stripslashes( $itinerary['label'] );
										}
										if ( isset( $itinerary['title'] ) && '' !== $itinerary['title'] ) {
											$itinerary_title = stripslashes( $itinerary['title'] );
										}
										if ( isset( $itinerary['desc'] ) && '' !== $itinerary['desc'] ) {
											$itinerary_desc = stripslashes( $itinerary['desc'] );
										}
										if ( isset( $itinerary['date'] ) && '' !== $itinerary['date'] ) {
											$itinerary_date = stripslashes( $itinerary['date'] );
											$itinerary_date = date( $date_format, strtotime( $itinerary_date ) );
											
										}
										if ( isset( $itinerary['time'] ) && '' !== $itinerary['time'] ) {
											$itinerary_time = stripslashes( $itinerary['time'] );
											$itinerary_time = date( $time_format, strtotime( $itinerary_time ) );
										}
										?>
										<div class="col clearfix <?php echo esc_attr( $row_reverse ) ?>">
											<div class="tc-heading <?php echo esc_attr( $first_class ) ?> clearfix">
												<?php if ( '' !== $itinerary_label ) : ?>
												<h4><?php echo esc_html( $itinerary_label ); ?></h4>
												<?php endif; ?>
												<?php if ( $itinerary_date ) : ?>												
													<h3 class="arrival"><?php esc_html_e( 'Date', 'wp-travel' ) ?> : <?php echo esc_html( $itinerary_date ) ?></h3>
												<?php endif; ?>
												<?php if ( $itinerary_time ) : ?>
													<h3><?php esc_html_e( 'Time', 'wp-travel' ) ?> : <?php echo esc_html( $itinerary_time ) ?></h3>
												<?php endif; ?>
											</div><!-- tc-content -->
											<div class="tc-content <?php echo esc_attr( $second_class ) ?> clearfix" >
												<?php if ( '' !== $itinerary_title ) : ?>
												<h3><?php echo esc_html( $itinerary_title ); ?></h3>
												<?php endif; ?>
												<?php echo wp_kses_post( $itinerary_desc ); ?>
												<div class="image"></div>
											</div><!-- tc-content -->
										</div><!-- first-content -->
										<?php $index++ ?>
									<?php endforeach; ?>
								
							</div><!-- timeline-contents -->
						</div><!-- itenary -->

					<?php endif; ?>
					
					</div>
					<?php break;
					 default : ?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
							<?php 
								if ( apply_filters( 'wp_travel_trip_tabs_output_raw', false ) ) {
									
									echo do_shortcode( $tab_info['content'] );

								}else {

									echo apply_filters( 'the_content', $tab_info['content'] ) ; 
								}
								
							?>
						</div>
					<?php break; ?>
				<?php } ?>
			<?php $index++; endforeach; ?>
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
	$settings = wp_travel_get_settings();
	if ( ! isset( $settings['google_map_api_key'] ) || '' === $settings['google_map_api_key'] ) {
		return;
	} ?>
	<div class="wp-travel-map">
		<div id="wp-travel-map" style="width:100%;height:300px"></div>
	</div>
<?php
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
 * @since 1.0.0
 * @return int The total number of trips reviews
 */
function wp_travel_get_review_count() {
	global $wpdb, $post;

	// No meta date? Do the calculation.
	if ( ! metadata_exists( 'post', $post->ID, '_wpt_review_count' ) ) {
		$count = $wpdb->get_var( $wpdb->prepare("
			SELECT COUNT(*) FROM $wpdb->comments
			WHERE comment_parent = 0
			AND comment_post_ID = %d
			AND comment_approved = '1'
		", $post->ID ) );

		update_post_meta( $post->ID, '_wpt_review_count', $count );
	} else {
		$count = get_post_meta( $post->ID, '_wpt_review_count', true );
	}

	return apply_filters( 'wp_travel_review_count', $count, $post );
}

/**
 * Get the average rating of product. This is calculated once and stored in postmeta.
 *
 * @return string
 */
function wp_travel_get_average_rating() {
	global $wpdb, $post;

	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $post->ID, '_wpt_average_rating' ) ) {

		if ( $count = wp_travel_get_rating_count() ) {
			$ratings = $wpdb->get_var( $wpdb->prepare("
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = '_wp_travel_rating'
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
			", $post->ID ) );
			$average = number_format( $ratings / $count, 2, '.', '' );
		} else {
			$average = 0;
		}
		update_post_meta( $post->ID, '_wpt_average_rating', $average );
	} else {

		$average = get_post_meta( $post->ID, '_wpt_average_rating', true );
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
		$raw_counts = $wpdb->get_results( $wpdb->prepare("
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = '_wp_travel_rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		", $post->ID ) );

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
		// 	echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
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
		// 	echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
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
	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );

	if ( ! $enable_sale ) {
		return;
	}

	$trip_price = wp_travel_get_trip_price( $post_id );
	$sale_price = wp_travel_get_trip_sale_price( $post_id );

	if ( $sale_price > $trip_price ) {
		$save = (1 - ( $trip_price / $sale_price )) * 100;
		$save = number_format( $save, 2, '.', ',' ); ?>
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
	if ( isset( $_GET['booked'] ) && 1 == $_GET['booked'] ) : ?>
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>
		<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo apply_filters( 'wp_travel_booked_message', __("We've received your booking details. We'll contact you soon.", "wp-travel" ) ); ?></p>
	
	<?php elseif( isset( $_GET['booked'] ) && 'false' == $_GET['booked']) : ?>
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>

		<?php 

			$err_msg = __( 'Your Item has been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' )

		?>
		
		<p class="col-xs-12 wp-travel-notice-danger wp-travel-notice"><?php echo apply_filters( 'wp_travel_booked_message', $err_msg ); ?></p>
	<?php endif;
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

	if (  $group_size ) {
		return sprintf( apply_filters( 'wp_travel_template_group_size_text', __( '%d pax', 'wp-travel' ) ) , $group_size );
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
	} ?>
	<div class="wp-travel-post-filter clearfix">
		<div class="wp-travel-filter-by-heading">
			<h4><?php esc_html_e( 'Filter By', 'wp-travel' ) ?></h4>
		</div>

		<?php do_action( 'wp_travel_before_post_filter' );  ?>
		<input type="hidden" id="wp-travel-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ) ?>" />
		<?php
			 $price = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
			 $type = ( int ) ( isset( $_GET['type'] ) && '' !== $_GET['type'] ) ? $_GET['type'] : 0;
			 $location = ( int ) ( isset( $_GET['location'] ) && '' !== $_GET['location'] ) ? $_GET['location'] : 0;
		?>

		<?php $enable_filter_price = apply_filters( 'wp_travel_post_filter_by_price', true );  ?>
		<?php if ( $enable_filter_price ) : ?>
			<div class="wp-toolbar-filter-field wt-filter-by-price">
				<p><?php esc_html_e( 'Price', 'wp-travel' ); ?></p>
				<select name="price" class="wp_travel_input_filters price">
					<option value="">--</option>
					<option value="low_high" <?php selected( $price, 'low_high' ) ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ) ?></option>
					<option value="high_low" <?php selected( $price, 'high_low' ) ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ) ?></option>
				</select>
			</div>
		<?php endif; ?>
		<div class="wp-toolbar-filter-field wt-filter-by-itinerary-types">
			<p><?php esc_html_e( 'Trip Type', 'wp-travel' ); ?></p>
			<?php wp_dropdown_categories( array( 'taxonomy' => 'itinerary_types', 'name' => 'type', 'class' => 'wp_travel_input_filters type', 'show_option_none' => '--', 'option_none_value' => '', 'selected' => $type ) ); ?>
		</div>
		<div class="wp-toolbar-filter-field wt-filter-by-travel-locations">
			<p><?php esc_html_e( 'Location', 'wp-travel' ); ?></p>
			<?php wp_dropdown_categories( array( 'taxonomy' => 'travel_locations', 'name' => 'location', 'class' => 'wp_travel_input_filters location', 'show_option_none' => '--', 'option_none_value' => '', 'selected' => $location ) ); ?>
		</div>
		<div class="wp-travel-filter-button">
			<button class="btn-wp-travel-filter"><?php esc_html_e( 'Show', 'wp-travel' ); ?></button>
		</div>
		<?php do_action( 'wp_travel_after_post_filter' );  ?>
	</div>
<?php }

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
	if ( ( is_wp_travel_archive_page() || is_search() ) && ! is_admin() ) : ?>
	<?php if ( is_wp_travel_archive_page() ) : ?>
	<div class="wp-travel-toolbar clearfix">
		<div class="wp-toolbar-content wp-toolbar-left">
			<?php wp_travel_archive_filter_by(); ?>
		</div>
		<div class="wp-toolbar-content wp-toolbar-right">
			<?php
			$current_url = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
			<ul class="wp-travel-view-mode-lists">
				<li class="wp-travel-view-mode <?php echo ( 'grid' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="grid" ><a href="<?php echo esc_url( add_query_arg('view_mode', 'grid', $current_url ) ); ?>"><i class="dashicons dashicons-grid-view"></i></a></li>
				<li class="wp-travel-view-mode <?php echo ( 'list' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="list" ><a href="<?php echo esc_url( add_query_arg('view_mode', 'list', $current_url ) ); ?>"><i class="dashicons dashicons-list-view"></i></a></li>
			</ul>
		</div>
	</div>
	<?php endif; ?>
	<?php 
		
		$archive_sidebar_class = '';
		
		if( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
			$archive_sidebar_class = 'wp-travel-trips-has-sidebar';
		} 
		
	?>
	<div class="wp-travel-archive-content <?php echo esc_attr ( $archive_sidebar_class ); ?>">
		<?php if ( 'grid' === $view_mode ) : ?>
			<?php $col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row' , '3' ); ?>
			<?php 
			if( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
				$col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row' , '2' );
			}  
			?>			
			<div class="wp-travel-itinerary-items">
				<ul class="wp-travel-itinerary-list itinerary-<?php esc_attr_e( $col_per_row, 'wp-travel' ) ?>-per-row">
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
		$view_mode = wp_travel_get_archive_view_mode();	 ?>
		<?php if ( 'grid' === $view_mode ) : ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php
		$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
		$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
		wp_travel_pagination( $pagination_range, $max_num_pages ); ?>
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

function wp_travel_archive_listing_sidebar(){

	if ( is_wp_travel_archive_page() && ! is_admin() && is_active_sidebar( 'wp-travel-archive-sidebar' ) ) : ?>

		<div id="wp-travel-secondary" class="wp-travel-widget-area widget-area" role="complementary">
			<?php dynamic_sidebar('wp-travel-archive-sidebar'); ?>
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

			// // Filter By Dates.
			if ( isset( $_GET['trip_start'] ) || isset( $_GET['trip_end'] ) ) {

				$trip_start = ! empty( $_GET['trip_start'] ) ? $_GET['trip_start'] : 0;

				$trip_end = ! empty( $_GET['trip_end'] ) ? $_GET['trip_end'] : 0;

				if ( $trip_start || $trip_end ) {

					//Convert to timestamp.
					$trip_start = strtotime($trip_start);
					$trip_start = date('Y-m-d',$trip_start);

					//Make date in required format.
					$trip_end = strtotime($trip_end);
					$trip_end = date('Y-m-d',$trip_end);

					$query->set('meta_query', array(
						array(
							'key'     => 'wp_travel_start_date',
							'value'   => array( $trip_start, $trip_end ),
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						),
					)
					);
					
					$query->set('meta_query', array(
						array(
							'key'     => 'wp_travel_start_date',
							'value'   => array( $trip_start, $trip_end ),
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						),
					)
					);

				}

			}

			// Filter By Price.
			if ( isset( $_GET['price'] ) && '' != $_GET['price'] ) {
				$filter_by = $_GET['price'];

				$query->set( 'meta_key', 'wp_travel_trip_price' );
				$query->set( 'orderby', 'meta_value_num' );

				switch ( $filter_by ) {
					case 'low_high' :
						$query->set( 'order', 'asc' );
					break;
					case 'high_low' :
						$query->set( 'order', 'desc' );
					break;
					default :
					break;
				}
			}
			// Trip Cost Range Filter.
			if ( isset( $_GET['max_price'] ) || isset( $_GET['min_price'] ) ) {

				$max_price = ! empty( $_GET['max_price'] ) ? $_GET['max_price'] : 0;

				$min_price = ! empty( $_GET['min_price'] ) ? $_GET['min_price'] : 0;

				if ( $min_price || $max_price ) {

					$query->set( 'meta_key', 'wp_travel_trip_price' );

					$query->set('meta_query', array(
						array(
							'key'     => 'wp_travel_trip_price',
							'value'   => array( $min_price, $max_price ),
							'type'    => 'numeric',
							'compare' => 'BETWEEN',
						),
					)
					);
				}
			}

			// Filter by location,trip type and Keywords.
			if ( isset( $_GET['type'] ) || isset( $_GET['location'] ) ||  isset( $_GET['keyword'] ) ) {

				$type = 0;
				$location = 0;
				$keyword = '';
				$keywords = '';
				if ( isset( $_GET['type'] ) && '' != $_GET['type'] ) {
					$type = $_GET['type'];
				}
				if ( isset( $_GET['location'] ) && '' != $_GET['location'] ) {
					$location = $_GET['location'];
				}
				if ( isset( $_GET['keyword'] ) && '' != $_GET['keyword'] ) {
					$keyword = $_GET['keyword'];
					$keywords = explode( ' ', $keyword );
				}

				if ( $type > 0 && $location > 0 && '' !== $keywords ) {

					$query->set( 'tax_query',  array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'itinerary_types',
								'field' => 'id',
								'terms' => $type,
							),
							array(
								'taxonomy' => 'travel_locations',
								'field' => 'id',
								'terms' => $location,
							),
							array(
								'taxonomy' => 'travel_keywords',
								'field' => 'name',
								'terms' => $keywords,
							),
							
						)
					);

				} elseif ( $type && '' !== $keywords ) {
					$query->set( 'tax_query',  array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'itinerary_types',
								'field' => 'id',
								'terms' => $type,
							),
							array(
								'taxonomy' => 'travel_keywords',
								'field' => 'name',
								'terms' => $keywords,
							),
							
						)
					);

				} elseif ( $location > 0 && '' !== $keywords ) {
					$query->set( 'tax_query',  array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'travel_locations',
								'field' => 'id',
								'terms' => $location,
							),
							array(
								'taxonomy' => 'travel_keywords',
								'field' => 'name',
								'terms' => $keywords,
							),
							
						)
					);

				} elseif ( $type > 0 && $location > 0 ) {
					$query->set( 'tax_query',  array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'itinerary_types',
								'field' => 'id',
								'terms' => $type,
							),
							array(
								'taxonomy' => 'travel_locations',
								'field' => 'id',
								'terms' => $location,
							),
							
						)
					);

				} elseif ( $type > 0 ) {
					$query->set( 'tax_query', array(
						array(
							'taxonomy' => 'itinerary_types',
							'field' => 'id',
							'terms' => $type,
						),
					) );
				} elseif ( $location > 0 ) {
					$query->set( 'tax_query', array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'travel_locations',
							'field' => 'id',
							'terms' => $location,
						),
					) );
				} elseif ( '' !== $keywords ) {

					$query->set( 'tax_query', array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'travel_keywords',
							'field' => 'name',
							'terms' => $keywords,
						),
					) );

				}
			}
			if ( isset( $_GET['fact'] ) ) {

				$fact = $_GET['fact'];

				$query->set('meta_key', 'wp_travel_trip_facts');
				$query->set('meta_query', array(
					array(
						'key' => 'wp_travel_trip_facts',
						'value' => $fact,
						'compare' => 'LIKE',
					),
				));
	
			}
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
	$view_mode = $default_view_mode;
	if ( isset( $_GET['view_mode'] ) && ( 'grid' === $_GET['view_mode'] || 'list' === $_GET['view_mode'] )  ) {
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
	$post_type = get_post_type($post_id);
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
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_trip_facts');
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_contents', 20);
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_trip_map', 20 );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_related_itineraries', 20 );
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
	if ( empty( $post->post_type ) || WP_TRAVEL_POST_TYPE !== $post->post_type )
		return $more;

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
		$query_class = new WP_Travel_Query();
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
