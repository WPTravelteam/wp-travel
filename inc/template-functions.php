<?php
/**
 * Template Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Return template.
 *
 * @param  String $path Path of template.
 * @param  array  $args arguments.
 * @return Mixed
 */
function wp_travel_get_template( $path, $args = array() ) {
	$file = sprintf( '%s/templates/%s', untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ), $path );
	if ( file_exists( $file ) ) {
		return $file;
	}
	return false;
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

	if ( ! is_singular( 'itineraries' ) ) {
		return $content;
	}
	global $post;

	$settings = wp_traval_get_settings();

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
	$settings = wp_traval_get_settings();
	$trip_price 	= wp_travel_get_trip_price( $post_id );

	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	$sale_price 	= wp_travel_get_trip_sale_price( $post_id );
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code ); ?>
    <div class="wp-detail-review-wrap">
    <?php do_action( 'wp_travel_single_before_trip_price', $post_id, $hide_rating ); ?>
	<div class="wp-travel-trip-detail">
		<div class="trip-price" >
		<?php if ( $enable_sale ) : ?>
		    <del>
		<?php else : ?>
			<ins>
		<?php endif; ?>
	    	<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
	    <?php if ( $enable_sale ) : ?>
		    </del>
		    <ins>

		      <span><?php echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price ); ?></span>
		   </ins>
		<?php else : ?>
			</ins>
		<?php endif; ?>

		    <span class="person-count">/<?php esc_html_e( 'person', 'wp-travel') ?></span>
		</div>
	</div>
	<?php do_action( 'wp_travel_single_after_trip_price', $post_id, $hide_rating ); ?>

	</div>

<?php
}

/**
 * Add html of Rating.
 *
 * @param int $post_id ID for current post.
 */
function wp_travel_single_trip_rating( $post_id, $hide_rating = false ) {
	if ( ! is_singular( 'itineraries' ) ) {
		return;
	}
	if ( ! $post_id ) {
		return;
	}
	if ( $hide_rating ) {
		return;
	}
	$average_rating = wp_travel_get_average_rating(); ?>
	<div class="wp-travel-average-review" title="<?php printf( __( 'Rated %s out of 5', 'classified' ), $average_rating ); ?>">
		 <a>
			<span style="width:<?php echo ( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( __( 'out of %s5%s', 'classified' ), '<span itemprop="bestRating">', '</span>' ); ?>
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
	<div class="wp-travel-average-review" title="<?php printf( __( 'Rated %s out of 5', 'classified' ), $average_rating ); ?>">
		 <a>
			<span style="width:<?php echo ( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( __( 'out of %s5%s', 'classified' ), '<span itemprop="bestRating">', '</span>' ); ?>
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
	$wp_travel_itinerary = new WP_Travel_Itinerary();
	?>
	<div class="trip-short-desc">
		<?php the_excerpt(); ?>
	</div>
	  <div class="wp-travel-trip-meta-info">
	  	 <ul>
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
					<strong class="title"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value code-font">
						<?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?>
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
								printf( '%d pax', esc_html( $group_size ) );
						} else {
							echo esc_html( apply_filters( 'wp_travel_default_group_size_text', __( 'No Size Limit', 'wp-travel' ) ) );
						}
						?>
					</span>
				</div>
	  	 	</li>
			<?php if( comments_open() ) : ?>
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
	  	 </ul>
	  </div>

  	<div class="booking-form">
		<div class="wp-travel-booking-wrapper">
			<button class="wp-travel-booknow-btn"><?php esc_html_e( 'Book Now', 'wp-travel' ); ?></button>
		</div>
	</div>
	<?php
}

function wp_travel_frontend_contents( $post_id ) {
	global $wp_travel_itinerary;
	$trip_content	= $wp_travel_itinerary->get_content();
	$trip_outline	= $wp_travel_itinerary->get_outline();
	$trip_include	= $wp_travel_itinerary->get_trip_include();
	$trip_exclude	= $wp_travel_itinerary->get_trip_exclude();
	$gallery_ids 	= $wp_travel_itinerary->get_gallery_ids();
	$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
	?>


	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper">
		<ul class="wp-travel tab-list resp-tabs-list ">
			<li class="tab-link" data-tab="tab-5"><?php esc_html_e( 'Overview', 'wp-travel' ) ?></li>
			<li class="content-li" data-tab="tab-1"><?php esc_html_e( 'Trip Outline', 'wp-travel' ); ?></li>
			<li class="content-li" data-tab="tab-2"><?php esc_html_e( 'Trip Include', 'wp-travel' ) ?></li>
			<li class="content-li" data-tab="tab-3"><?php esc_html_e( 'Trip Exclude', 'wp-travel' ) ?></li>
			<li class="content-li wp-travel-tab-gallery-contnet" data-tab="tab-4"><?php esc_html_e( 'Gallery', 'wp-travel' ) ?></li>
			<?php if ( comments_open() ) : ?>
			<li class="content-li wp-travel-review" data-tab="tab-6"><?php esc_html_e( 'Reviews', 'wp-travel' ) ?></li>
			<?php endif; ?>
			<li class="content-li wp-travel-booking-form" data-tab="tab-7"><?php esc_html_e( 'Booking', 'wp-travel' ) ?></li>
		</ul>
		<div class="resp-tabs-container">
			<div id="tab-5" class="tab-list-content">
				<?php
				if ( false !== $trip_content ) {
					echo wp_kses_post( $trip_content );
				} else {
					echo wp_kses( $no_details_found_message, wp_travel_allowed_html( array( 'p' ) ) );
				}
				?>
			</div>
			<div id="tab-1" class="tab-list-content ">
				<?php
				if ( false !== $trip_outline )
					echo wp_kses_post( $trip_outline );
				else
					echo wp_kses( $no_details_found_message, wp_travel_allowed_html( array( 'p' ) ) );
				?>
			</div>
			<div id="tab-2" class="tab-list-content">
				<?php
				if ( false !== $trip_include ) {
					echo wp_kses_post( $trip_include );
				} else {
					echo wp_kses( $no_details_found_message, wp_travel_allowed_html( array( 'p' ) ) );
				}
				?>
			</div>
			<div id="tab-3" class="tab-list-content">
				<?php
				if ( false !== $trip_exclude ) {
					echo wp_kses_post( $trip_exclude );
				} else {
					echo wp_kses( $no_details_found_message, wp_travel_allowed_html( array( 'p' ) ) );
				}
				?>
			</div>
			<div id="tab-4" class="tab-list-content">
				<?php if ( false !== $gallery_ids ) : ?>
				<div class="wp-travel-gallery wp-travel-container-wrap">
				    <div class="wp-travel-row-wrap">
						<ul>
							<?php foreach ( $gallery_ids as $gallery_id ) : ?>
							<li>
								<?php $gallery_image = wp_get_attachment_image_src( $gallery_id, 'medium' );  ?>
								<a href="<?php echo ( wp_get_attachment_url( $gallery_id ) ); ?>">
								<img src="<?php echo ( $gallery_image[0] ); ?>" />
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
			<?php if ( comments_open() ) : ?>
			<div id="tab-6" class="tab-list-content ">
			    <?php comments_template(); ?>
			</div><!-- Tab 6 ends -->
			<?php endif; ?>
			<div id="tab-7" class="tab-list-content ">
				<?php echo wp_travel_get_booking_form(); ?>
			</div>
		</div>
	</div>
<?php
}

function wp_travel_trip_map( $post_id ) {
	global $wp_travel_itinerary;
	if ( ! $wp_travel_itinerary->get_location() ) {
		return;
	}
	$settings = wp_traval_get_settings();
	if ( ! isset( $settings['google_map_api_key'] ) || '' === $settings['google_map_api_key'] ) {
		return;
	}
	?>
	<div class="wp-travel-map">
		<div id="gmap" style="width:100%;height:300px"></div>
	</div>
	<?php wp_travel_get_related_post( $post_id );
}

function wp_travel_add_comment_rating( $comment_id ) {
	if ( isset( $_POST['wp_travel_rate_val'] ) && 'itineraries' === get_post_type( $_POST['comment_post_ID'] ) ) {
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
	&& 'itineraries' === get_post_type( sanitize_text_field( $_POST['comment_post_ID'] ) )
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
 * @return int The total numver of itineraries reviews
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
	if ( get_post_type() !== 'itineraries' ) {
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

function wp_travel_template_loader( $template ) {

	if ( get_post_type() !== 'itineraries' ) {
		return $template;
	}


	$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
			trailingslashit( get_template_directory() ) . WP_TRAVEL_TEMPLATE_PATH,
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WP_TRAVEL_PLUGIN_PATH ) . 'templates/',
		);
	// Load template for post archive / taxonomy archive.
	if ( is_post_type_archive( 'itineraries' ) || is_tax( 'itinerary_types' ) ) {
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
	if ( get_post_type() !== 'itineraries' ) {
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
	if ( get_post_type() !== 'itineraries' ) {
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
	if ( get_post_type() !== 'itineraries' ) {
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

	if ( is_active_sidebar( 'sidebar-1' ) && is_singular( 'itineraries' ) ) {
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
	if ( ! is_singular( 'itineraries' ) ) {
		return;
	}
	if ( isset( $_POST['wp_travel_book_now'] ) ) : ?>
		<p class="wp-travel-notice-success wp-travel-notice"><?php echo apply_filters( 'wp_travel_booked_message', "We've received your booking details. We'll contact you soon." ); ?></p>
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
		return sprintf( '%d pax', $group_size );
	}

	return apply_filters( 'wp_travel_default_group_size_text', esc_html( 'No Size Limit', 'wp-travel' ) );
}


/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post
 * @return WC_Product
 */
function wp_travel_setup_itinerary_data( $post ) {
	unset( $GLOBALS['wp_travel_itinerary'] );

	if ( is_int( $post ) )
		$post = get_post( $post );

	if ( empty( $post->post_type ) || 'itineraries' !== $post->post_type )
		return;

	$GLOBALS['wp_travel_itinerary'] = new WP_Travel_Itinerary( $post );

	return $GLOBALS['wp_travel_itinerary'];
}

// Hooks.
add_action( 'wp_tarvel_after_single_title', 'wp_travel_trip_price', 1 );
add_action( 'wp_tarvel_after_single_title', 'wp_travel_single_excerpt', 1 );
add_action( 'wp_travel_single_after_trip_price', 'wp_travel_single_trip_rating', 10, 2 );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_contents' );
add_action( 'wp_travel_after_single_itinerary_header', 'wp_travel_trip_map' );
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

add_action( 'wp_travel_before_main_content', 'wp_travel_booking_message' );

add_action( 'the_post', 'wp_travel_setup_itinerary_data' );


function wp_travel_excerpt_more( $more ) {
	global $post;
	if ( empty( $post->post_type ) || 'itineraries' !== $post->post_type )
		return $more;

	return '...';
}
add_filter( 'excerpt_more', 'wp_travel_excerpt_more' );
