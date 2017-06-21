<?php
function wp_travel_get_template( $path, $args = array() ) {
	$file =  sprintf( '%s/templates/%s', untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ), $path );
	if ( file_exists( $file ) ) {
		return $file;
	}
	return false;
}

function wp_travel_get_template_part( $slug, $name = '' ) {
	$template = '';
	$file_name = ( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
	if( $name ) {
		$template = wp_travel_get_template( $file_name );
	}
	if ( $template ) {
    load_template( $template, false );
  }
}

function wp_travel_load_template( $path, $args = array() ) {
	$template = wp_travel_get_template( $path, $args );
	if( $template ){
		include $template;
	}
}

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

add_filter( 'the_content', 'wp_travel_content_filter' );

/**
 * Wrapper Start
 */
add_action ( 'wp_travel_before_single_itinerary', 'wp_travel_wrapper_start' );

function wp_travel_wrapper_start() {
	if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

	switch( $template ) {
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
			echo '<div id="container"><div id="content" class="wp-travel-content" role="main">';
			break;
	}
}

add_action ( 'wp_travel_after_single_itinerary', 'wp_travel_wrapper_end' );

function wp_travel_wrapper_end() {
	$template = get_option( 'template' );

	switch( $template ) {
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
			echo '</div></div>';
			break;
	}
}





// Hooks.
add_action( 'wp_tarvel_after_single_title', 'wp_travel_trip_price', 1 );
add_action( 'wp_tarvel_after_single_title', 'wp_travel_single_excerpt', 1 );

add_action ( 'wp_travel_after_single_itinerary_header', 'wp_travel_frontend_contents' );

add_action ( 'wp_travel_after_single_itinerary_header', 'wp_travel_sintle_trip_map' );


/**
 * Add html of trip price.
 *
 * @param int $post_id ID for current post.
 */
function wp_travel_trip_price( $post_id ) {
	$settings = wp_traval_get_settings();
	$trip_price 	= get_post_meta( $post_id, 'wp_travel_price', true );
	$enable_sale 	= get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	$sale_price 	= get_post_meta( $post_id, 'wp_travel_sale_price', true );
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code ); ?>

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
	<?php  $average_rating = wp_travel_get_average_rating(); ?>
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
	$group_size = get_post_meta( $post_id, 'wp_travel_group_size', true );

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
						echo get_the_term_list( $post_id, 'itinerary_types', '', ', ', '' ); ?>
					</span>
				</div>
	  	 	</li>
	  	 	<li>
	  	 		<div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value code-font"><?php esc_html_e( wp_traval_get_trip_code( $post_id ), 'wp-travel' ); ?></span>
				</div>
	  	 	</li>
	  	 	<li>
	  	 		<div class="travel-info">
					<strong class="title"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value"><?php printf( '%d pax', $group_size ) ?></span>
				</div>
	  	 	</li>
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
	$trip_outline	= get_post_meta( $post_id, 'wp_travel_outline', true );
	$trip_include	= get_post_meta( $post_id, 'wp_travel_trip_include', true );
	$trip_exclude	= get_post_meta( $post_id, 'wp_travel_trip_exclude', true );
	$gallery_ids 	= get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	?>	
	<?php if ( '' != $trip_outline ) : ?>

	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper">
		<ul class="wp-travel tab-list resp-tabs-list ">
			<li class="tab-link" data-tab="tab-5"><?php esc_html_e( 'Overview', 'wp-travel' ) ?></li>
			<li class="content-li" data-tab="tab-1"><?php esc_html_e( 'Trip Outline', 'wp-travel' ); ?></li>
			<li class="content-li" data-tab="tab-2"><?php esc_html_e( 'Trip Include', 'wp-travel' ) ?></li>
			<li class="content-li" data-tab="tab-3"><?php esc_html_e( 'Trip Exclude', 'wp-travel' ) ?></li>
			<li class="content-li" data-tab="tab-4"><?php esc_html_e( 'Gallery', 'wp-travel' ) ?></li>
			<li class="content-li wp-travel-review" data-tab="tab-6"><?php esc_html_e( 'Reviews', 'wp-travel' ) ?></li>
			<li class="content-li wp-travel-booking-form" data-tab="tab-7"><?php esc_html_e( 'Booking', 'wp-travel' ) ?></li>
		</ul>
		<div class="resp-tabs-container">
			<div id="tab-5" class="tab-list-content">
				<?php echo apply_filters( 'the_content', get_the_content() ); ?>
			</div>
			<div id="tab-1" class="tab-list-content ">
				<?php _e( wpautop( $trip_outline ), 'wp-travel' ); ?>
			</div>
			<div id="tab-2" class="tab-list-content">
				<?php _e( wpautop( $trip_include ), 'wp-travel' ); ?>
			</div>
			<div id="tab-3" class="tab-list-content">
				<?php _e( wpautop( $trip_exclude ), 'wp-travel' ); ?>
			</div>
			<div id="tab-4" class="tab-list-content">
				<?php if ( count( $gallery_ids ) > 0 ) : ?>
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
				<?php endif; ?>
			</div>
			
			<div id="tab-6" class="tab-list-content">
				<?php
				if ( ! comments_open() ) {
					return;
				}
				?>

			    <div class="review-main-wrapper">
			    	<?php 
			    	$args = array( 'post_id' => $post_id, 'comment_approved' => '1' );
			    	$comments = get_comments( $args ); ?>
			    	<div class="wp-travel-review-count">
				    	<?php
						$count = wp_travel_get_review_count();
						printf( _n( '<h3>%s review for %s', '%s reviews for %s </h3>', $count, 'wp-travel' ), $count, get_the_title() );
						?>
					</div>
					<?php foreach ( $comments as $comment ) : ?>
						<?php $rating = get_comment_meta( $comment->comment_ID, '_wp_travel_rating', true ); ?>
						<div class="wp-tab-review-inner-wrapper">
							<div class="author-img-wrapper">

								<img src="<?php _e( get_avatar_url( $comment->comment_author_email ) ); ?>">
							</div>
							<div class="review-content-wrapper">
								<div class="wp-travel-star-rating">
									<?php for( $i=1; $i <= 5; $i++ ) : ?>
										<?php $class_name = ( $rating >= $i ) ? 'dashicons-star-filled' : 'dashicons-star-empty' ?>
										<a class="dashicons <?php esc_html_e( $class_name ) ?>"></a>
									<?php endfor; ?>								
								</div>
								<p class="meta-items">
					                <strong class="author-name"><?php echo wpautop( $comment->comment_author ) ?></strong> 
					                <time class="wp-travel-review-published-date" datetime="">
					                <?php echo date( get_option('date_format'), strtotime( $comment->comment_date ) ) ?>				                	
					                </time>
				                </p>
				                <div class="description-review">
				                	<?php echo wpautop( $comment->comment_content ) ?>
				                </div>
							</div>
						</div>
					 <?php endforeach;  ?>

					<div class="wp-travel-form-review">
						<?php
						$commenter = wp_get_current_commenter();

						$comment_form = array(
							'title_reply'          => $comments ? __( 'Add a review', 'wp_travel' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'wp_travel' ), get_the_title() ),
							'title_reply_to'       => __( 'Leave a Reply to %s', 'wp_travel' ),
							'comment_notes_before' => '',
							'comment_notes_after'  => '',
							'fields'               => array(
								'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp_travel' ) . ' <span class="required">*</span></label> ' .
								            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
								'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp_travel' ) . ' <span class="required">*</span></label> ' .
								            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
							),
							'label_submit'  => __( 'Submit', 'wp_travel' ),
							'logged_in_as'  => '',
							'comment_field' => ''
						);

						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . __( 'Your Rating', 'wp_travel' ) .'</label><div id="wp_travel_rate" class="clearfix">
	                            <a href="#" class="rate_label dashicons dashicons-star-empty" data-id="1"></a>
	                            <a href="#" class="rate_label dashicons dashicons-star-empty" data-id="2"></a>
	                            <a href="#" class="rate_label dashicons dashicons-star-empty" data-id="3"></a>
	                            <a href="#" class="rate_label dashicons dashicons-star-empty" data-id="4"></a>
	                            <a href="#" class="rate_label dashicons dashicons-star-empty" data-id="5"></a>
	                        </div>
	                        <input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'wp_travel' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

						comment_form( apply_filters( 'wp_travel_product_review_comment_form_args', $comment_form ) ); ?>					
					</div>
				</div>
			</div><!-- Tab 6 ends -->
			<div id="tab-7" class="tab-list-content ">
				<?php echo wp_travel_get_booking_form(); ?>
			</div>
		</div>
	</div>
		
	<?php endif; ?>
	
<?php
}

function wp_travel_sintle_trip_map( $post_id ) {
	$settings = wp_traval_get_settings();
	if ( ! isset( $settings['google_map_api_key'] ) || '' == $settings['google_map_api_key'] ) {
		return;
	} ?>

	<div class="wp-travel-map">
		<div id="gmap" style="width:100%;height:300px"></div>	
	</div>
	<?php 
	wp_travel_get_related_post( $post_id );	
}

function wp_travel_add_comment_rating( $comment_id ) {
	if ( isset( $_POST['wp_travel_rate_val'] ) && 'itineraries' === get_post_type( $_POST['comment_post_ID'] ) ) {
		if ( ! $_POST['wp_travel_rate_val'] || $_POST['wp_travel_rate_val'] > 5 || $_POST['wp_travel_rate_val'] < 0 ) {
			return;
		}
		add_comment_meta( $comment_id, '_wp_travel_rating', (int) esc_attr( $_POST['wp_travel_rate_val'] ), true );
	}
}

add_action( 'comment_post', 'wp_travel_add_comment_rating' );
add_filter( 'preprocess_comment', 'wp_travel_verify_comment_meta_data' );

// Clear transients.
add_action( 'wp_update_comment_count', 'wp_travel_clear_transients' );


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

	// No meta date? Do the calculation
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
 * @return string
 */
function wp_travel_get_average_rating() {
	global $wpdb, $post;

	// No meta data? Do the calculation
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
 * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
 * @return int
 */
function wp_travel_get_rating_count( $value = null ) {
	global $wpdb, $post;

	// No meta data? Do the calculation
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
