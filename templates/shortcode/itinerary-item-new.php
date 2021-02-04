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
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
$post_id = get_the_ID();
// $enable_sale 	= WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $post_id ) );
$strings                          = wp_travel_get_strings();
$args                             = $args_regular = array( 'trip_id' => $post_id );
$args_regular['is_regular_price'] = true;
$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
$enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
	array(
		'trip_id'                => $post_id,
		'from_price_sale_enable' => true,
	)
);
?>
<article class="wti__trip-list-item">
	<div class="wti__trip-thumbnail">
		<a href="<?php the_permalink(); ?>" class="wti__trip-link"><?php echo wp_travel_get_post_thumbnail( $post_id, 'wp_travel_thumbnail' ); ?></a>
		<div class="wti__trip-meta">
			<?php if ( $enable_sale ) : ?>
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
					<span class="price-from"><?php echo esc_html( $strings['from'] ); ?>: </span>
					<strong><span class="currency"><?php echo wp_travel_get_formated_price_currency( $trip_price ); ?></strong>
					<?php if ( $enable_sale ) : ?>
						<span class="trip__price-stikeout">
							<del><span class="currency"><?php echo wp_travel_get_formated_price_currency( $regular_price, true ); ?></del>
						</span>
					<?php endif; ?>
					<!-- / <span>1 night(s)</span> -->
				</div>
			</div>
			<?php do_action( 'wp_travel_before_item_title', get_the_ID() ); ?>
			<?php wp_travel_do_deprecated_action( 'wp_tarvel_before_archive_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_item_title' ); ?>
			<h3 class="trip-travel__trip-title">
				<a href="<?php the_permalink(); ?> " rel="bookmark" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to: ', 'wp-travel' ) ) ); ?>">
					<?php the_title(); ?>
				</a>
			</h3>
			<?php do_action( 'wp_travel_after_archive_title', get_the_ID() ); ?>
			<div class="wti__trip-locations">
			<?php
				$i     = 0;
				$terms = get_the_terms( get_the_id(), 'travel_locations' );
			if ( is_array( $terms ) && count( $terms ) > 0 ) {
				foreach ( $terms as $term ) {
					if ( $i > 0 ) {
						?>
								,
						<?php
					}
					?>
						<span><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></span>
						<?php
						$i++;
				}
			}
			?>
			</div>
		</div>
		<div class="wti__trip-review">
			<?php
				$average_rating = wp_travel_get_average_rating( get_the_id() );
			?>
			<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">
				<a>
					<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
						<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
					</span>
				</a>

			</div>
			<span class="rating-text">(<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>)</span>
		</div>
		<div class="wti__trip-content">
			<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Placeat eum hic ipsam illum in blanditiis, quidem possimus minus libero facere.</p>
			<div class="wti__trip-book-button">
				<a href="#" class="button wti__trip-explore">Book Now</a>
			</div>
		</div>
		<?php
		$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
		$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
		$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
		$group_size      = wp_travel_get_group_size( $post_id );
		$terms           = get_the_terms( $post_id, 'itinerary_types' );
		?>
		<div class="wti__trip-footer">
			<div class="wti__trip-footer-meta">
				<span>
					<img src="<?php echo plugins_url( '/wp-travel/assets/images/flag.svg' ); ?>" alt=""> 
					<?php printf( '%s', $group_size ); ?>
				</span>
				<?php
				if ( 'yes' === $fixed_departure ) {
					?>
						<span><img src="<?php echo plugins_url( '/wp-travel/assets/images/hiking.svg' ); ?>" alt="">
						<?php echo wp_travel_get_fixed_departure_date( $post_id ); ?>
						</span>
						<?php
				} else {
					$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
					$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
					?>
						<span><img src="<?php echo plugins_url( '/wp-travel/assets/images/hiking.svg' ); ?>" alt="">
						<?php if ( (int) $trip_duration > 0 ) : ?>
								<?php echo esc_html( $trip_duration . __( ' Days', 'wp-travel' ) ); ?>
							<?php else : ?>
								<?php esc_html_e( 'N/A', 'wp-travel' ); ?>
							<?php endif; ?>
						</span>
						<?php
				}
				?>
				<span>
					<?php if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
						<?php
						$first_term = array_shift( $terms );
						$term_name  = $first_term->name;
						$term_link  = get_term_link( $first_term->term_id, 'itinerary_types' );
						?>
						<a href="<?php echo esc_url( $term_link ); ?>"><img src="<?php echo plugins_url( '/wp-travel/assets/images/group.svg' ); ?>" alt=""> 
							<?php echo esc_html( $term_name ); ?>
						</a>
						<?php if ( count( $terms ) > 0 ) : ?>
						<div class="wp-travel-related-trip-caret">
							<i class="wt-icon wt-icon-caret-down"></i>
							<div class="related-sub-category-menu">
								<?php foreach ( $terms as $term ) : ?>
									<?php
										$term_name = $term->name;
										$term_link = get_term_link( $term->term_id, 'itinerary_types' );
									?>
										<a href="<?php echo esc_url( $term_link ); ?>">
											<?php echo esc_html( $term_name ); ?>
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

