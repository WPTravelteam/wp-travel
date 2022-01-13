<?php
/**
 * Itinerary Archive Contnet Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-archive-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     WP_Travel
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'wp_travel_before_archive_itinerary', get_the_ID() );
if ( post_password_required() ) {
	echo get_the_password_form(); //phpcs:ignore
	return;
}
	$enable_sale   = WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => get_the_ID() ) );
	$group_size    = wptravel_get_group_size( get_the_ID() );
	$start_date    = get_post_meta( get_the_ID(), 'wp_travel_start_date', true );
	$end_date      = get_post_meta( get_the_ID(), 'wp_travel_end_date', true );
	$sanitized_get = WP_Travel::get_sanitize_request();
	$view_mode     = wptravel_get_archive_view_mode( $sanitized_get );
	global $wp_travel_itinerary;
?>
	<!-- Contents Here -->
	<div class="view-box">
		<div class="view-image">
			<a href="<?php the_permalink(); ?>" class="image-thumb">
				<div class="image-overlay"></div>
				<?php echo wptravel_get_post_thumbnail( get_the_ID() ); ?>
			</a>
			<div class="offer">
				<span>#<?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?></span>
			</div>
		</div>

		<div class="view-content">
			<div class="left-content">
				<header>
					<?php do_action( 'wp_travel_before_archive_content_title', get_the_ID() ); ?>
					<h2 class="entry-title">
						<a class="heading-link" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to: ', 'wp-travel' ) ) ); ?>">
							<?php the_title(); ?>
						</a>
					</h2>
					
					<?php do_action( 'wp_travel_after_archive_title', get_the_ID() ); ?>
					<div class="favourite">
						<a href="javascript:void(0);" data-id="5812" data-event="add" title="Add to wishlists" class="wp-travel-add-to-wishlists">
							<i class="fas fa-bookmark"></i>
						</a>
					</div>
				</header>
				<div class="trip-icons">
					<div class="trip-time">
						<i class="far fa-clock"></i>
						<span>5 Hours</span>
					</div>
					<div class="trip-calendar">
						<i class="far fa-calendar-alt"></i>
						<span>Feb 7 - Feb 14</span>
					</div>
					<div class="trip-location">
						<i class="fas fa-map-marker-alt"></i>
						<span>Africa</span>
					</div>
					<div class="group-size">
						<i class="fas fa-users"></i>
						<span>5 Pax</span>
					</div>
					
				</div>
				<div class="trip-desc">
					<?php the_excerpt(); ?>
				</div>
			</div>
			<div class="right-content">
				<div class="footer-wrapper">
					<div class="trip-price">
						<span class="discount">
							50% Off
						</span>
						<span class="price-here">
							$5,000
						</span>
						<del>$2,500</del>
					</div>
					<div class="trip-rating">
						<h4>Rating</h4>

					</div>
				</div>

				<a class="wp-block-button__link explore-btn" href="<?php the_permalink(); ?>"><span><?php esc_html_e( 'Explore', 'wp-travel' ); ?></span></a>
			</div>
		</div>
	</div>

				

				
<?php do_action( 'wp_travel_after_archive_itinerary', get_the_ID() ); ?>
