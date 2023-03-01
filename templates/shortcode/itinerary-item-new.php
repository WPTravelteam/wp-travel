<?php
/**
 * Itinerary Shortcode Contnet Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/shortcode/itinerary-item.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( post_password_required() ) {
	echo get_the_password_form(); //phpcs:ignore
	return;
}
$wptravel_post_id                          = get_the_ID();
$wptravel_strings                          = WpTravel_Helpers_Strings::get();
$wptravel_args                             = $wptravel_args_regular = array( 'trip_id' => $wptravel_post_id ); // phpcs:ignore
$wptravel_args_regular['is_regular_price'] = true;
$wptravel_trip_price                       = WP_Travel_Helpers_Pricings::get_price( $wptravel_args );
$wptravel_regular_price                    = WP_Travel_Helpers_Pricings::get_price( $wptravel_args_regular );
$wptravel_enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
	array(
		'trip_id'                => $wptravel_post_id,
		'from_price_sale_enable' => true,
	)
);
?>
<article class="wti__trip-list-item">
	<div class="wti__trip-thumbnail">
		<a href="<?php the_permalink(); ?>" class="wti__trip-link"><?php echo wptravel_get_post_thumbnail( $wptravel_post_id, 'full' ); //phpcs:ignore ?></a>
		<?php
		if ( $wptravel_regular_price > $wptravel_trip_price ) {
			$wptravel_save = ( 1 - ( $wptravel_trip_price / $wptravel_regular_price ) ) * 100;
			$wptravel_save = number_format( $wptravel_save, 2, '.', ',' );
			?>
			<div class="wti__savings"><?php printf( 'save <span>%s&#37;</span>', esc_html( $wptravel_save ) ); ?></div>
			<?php
		}
		?>
		<div class="wti__trip-meta">
			<?php if ( $wptravel_enable_sale ) : ?>
				<span class="wti__trip-meta-offer">
					<?php esc_html_e( 'Offer', 'wp-travel' ); ?>
				</span>
			<?php endif; ?>
			<span class="wti__trip-meta-wishlist"></span>
		</div>
	</div>
	<div class="wti__trip-content-wrapper">
		<div class="wti__trip-header">
			<div class="wti__trip-price-area">
				<div class="wti__trip-price-amount">
					<span class="price-from"><?php echo esc_html( $wptravel_strings['from'] ); ?>: </span>
					<strong><span class="currency"><?php echo wptravel_get_formated_price_currency( $wptravel_trip_price ); //phpcs:ignore ?></strong>
					<?php if ( $wptravel_enable_sale ) : ?>
						<span class="trip__price-stikeout">
							<del><span class="currency"><?php echo wptravel_get_formated_price_currency( $wptravel_regular_price, true ); //phpcs:ignore ?></del>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<?php do_action( 'wp_travel_before_item_title', get_the_ID() ); ?>
			<?php wptravel_do_deprecated_action( 'wp_tarvel_before_archive_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_item_title' ); ?>
			<h3 class="trip-travel__trip-title">
				<a href="<?php the_permalink(); ?> " rel="bookmark" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to: ', 'wp-travel' ) ) ); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
			<?php do_action( 'wp_travel_after_archive_title', get_the_ID() ); ?>
			<div class="wti__trip-locations">
			<?php
				$wptravel_index = 0;
				$wptravel_terms = get_the_terms( get_the_id(), 'travel_locations' );
			if ( is_array( $wptravel_terms ) && count( $wptravel_terms ) > 0 ) {
				foreach ( $wptravel_terms as $wptravel_term ) {
					if ( $wptravel_index > 0 ) {
						?>
								,
						<?php
					}
					?>
						<span><a href="<?php echo esc_url( get_term_link( $wptravel_term->term_id ) ); ?>"><?php echo esc_html( $wptravel_term->name ); ?></a></span>
						<?php
						$wptravel_index++;
				}
			}
			?>
			</div>
		</div>
		<div class="wti__trip-review">
			<?php
				$wptravel_average_rating = wptravel_get_average_rating( get_the_id() );
			?>
			<div class="wp-travel-average-review" title="<?php printf( 'Rated %s out of 5', esc_html( $wptravel_average_rating ) ); ?>">
				<a>
					<span style="width:<?php echo esc_attr( ( $wptravel_average_rating / 5 ) * 100 ); ?>%">
						<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $wptravel_average_rating ); ?></strong> <?php printf( 'out of %1$s5%2$s', '<span itemprop="bestRating">', '</span>' ); ?>
					</span>
				</a>

			</div>
			<span class="rating-text">(<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $wptravel_average_rating ); ?></strong> <?php printf( 'out of %1$s5%2$s', '<span itemprop="bestRating">', '</span>' ); ?>)</span>
		</div>
		<div class="wti__trip-content">
			<?php
			$wptravel_content = apply_filters( 'the_content', get_the_content() ); //phpcs:ignore
			$wptravel_excerpt = apply_filters( 'wp_travel_archive_trip_excerpt', wp_trim_words( $wptravel_content, 15 ) );
			if ( ! empty( $wptravel_excerpt ) ) {
				?>
				<p><?php echo esc_html( $wptravel_excerpt ); ?></p>
				<?php
			}
			?>
			<div class="wti__trip-book-button">
				<a href="<?php the_permalink( get_the_ID() ); ?>" class="button wti__trip-explore"><?php echo esc_html( apply_filters( 'wp_travel_archive_explore_btn', 'Explore Now' ) ); ?></a>
			</div>
		</div>
		<?php
		$wptravel_fixed_departure = get_post_meta( $wptravel_post_id, 'wp_travel_fixed_departure', true );
		$wptravel_fixed_departure = ( $wptravel_fixed_departure ) ? $wptravel_fixed_departure : 'yes';
		$wptravel_fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $wptravel_fixed_departure );
		$wptravel_group_size      = wptravel_get_group_size( $wptravel_post_id );
		$wptravel_terms           = get_the_terms( $wptravel_post_id, 'itinerary_types' );
		?>
		<div class="wti__trip-footer">
			<div class="wti__trip-footer-meta">
				<span>
					<img src="<?php echo plugins_url( '/wp-travel/assets/images/flag.svg' ); ?>" alt=""> 
					<?php printf( '%s', esc_html( $wptravel_group_size ) ); ?>
				</span>
				<?php
				if ( 'yes' === $wptravel_fixed_departure ) {
					?>
						<span><img src="<?php echo plugins_url( '/wp-travel/assets/images/hiking.svg' ); ?>" alt="">
						<?php echo wptravel_get_fixed_departure_date( $wptravel_post_id ); //phpcs:ignore ?>
						</span>
						<?php
				} else {
					$wptravel_trip_duration = get_post_meta( $wptravel_post_id, 'wp_travel_trip_duration', true );
					$wptravel_trip_duration = ( $wptravel_trip_duration ) ? $wptravel_trip_duration : 0;
					?>
						<span><img src="<?php echo plugins_url( '/wp-travel/assets/images/hiking.svg' ); ?>" alt="">
						<?php if ( (int) $wptravel_trip_duration > 0 ) : ?>
								<?php echo esc_html( $wptravel_trip_duration . ' ' . isset( $wptravel_strings['days'] ) ? $wptravel_strings['days'] : __( ' Days', 'wp-travel' ) ); ?>
							<?php else : ?>
								<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
							<?php endif; ?>
						</span>
						<?php
				}
				?>
				<span>
					<?php if ( is_array( $wptravel_terms ) && count( $wptravel_terms ) > 0 ) : ?>
						<?php
						$wptravel_first_term = array_shift( $wptravel_terms );
						$wptravel_term_name  = $wptravel_first_term->name;
						$wptravel_term_link  = get_term_link( $wptravel_first_term->term_id, 'itinerary_types' );
						?>
						<a href="<?php echo esc_url( $wptravel_term_link ); ?>"><img src="<?php echo plugins_url( '/wp-travel/assets/images/group.svg' ); ?>" alt=""> 
							<?php echo esc_html( $wptravel_term_name ); ?>
						</a>
						<?php if ( count( $wptravel_terms ) > 0 ) : ?>
						<div class="wp-travel-related-trip-caret">
							<i class="wt-icon wt-icon-caret-down"></i>
							<div class="related-sub-category-menu">
								<?php foreach ( $wptravel_terms as $wptravel_term ) : ?>
									<?php
										$wptravel_term_name = $wptravel_term->name;
										$wptravel_term_link = get_term_link( $wptravel_term->term_id, 'itinerary_types' );
									?>
										<a href="<?php echo esc_url( $wptravel_term_link ); ?>">
											<?php echo esc_html( $wptravel_term_name ); ?>
										</a>
								<?php endforeach; ?>
							</div>
						</div>
						<?php endif; ?>
					<?php endif; ?>
				</span>
			</div>
		</div>
	</div>
</article>

