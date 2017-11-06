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
 * @see 	    http://docs.wensolutions.com/document/template-structure/
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

$enable_sale 	= get_post_meta( get_the_ID(), 'wp_travel_enable_sale', true );
$trip_price 	= wp_travel_get_trip_price( get_the_ID() );
$sale_price 	= wp_travel_get_trip_sale_price( get_the_ID() ); ?>
<li>
<div class="wp-travel-post-item-wrapper">
    <div class="wp-travel-post-wrap-bg">
		
		<div class="wp-travel-post-thumbnail">
		 	<a href="<?php the_permalink() ?>">
			<?php echo wp_travel_get_post_thumbnail( get_the_ID(), 'wp_travel_thumbnail' ); ?>
		   	</a>
		   	<?php wp_travel_save_offer( get_the_ID() ); ?>
		</div>
		<div class="wp-travel-post-info">
			<h4 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			<div class="recent-post-bottom-meta">
				<?php wp_travel_trip_price( get_the_ID(), true ); ?>
			</div>
		</div>
		<div class="wp-travel-post-content">
			
			<?php
				$fixed_departure = get_post_meta( get_the_ID(), 'wp_travel_fixed_departure', true );
				$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
				$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
			?>
			<?php if ( 'yes' === $fixed_departure ) : ?>
				<?php
					$start_date	= get_post_meta( get_the_ID(), 'wp_travel_start_date', true );
					$end_date 	= get_post_meta( get_the_ID(), 'wp_travel_end_date', true );
				?>
				<?php if ( $start_date && $end_date ) : ?>
					<div class="wp-travel-trip-time trip-fixed-departure">
						<i class="fa fa-calendar"></i>
						<span class="wp-travel-trip-duration">
							<?php printf( '%s to %s', $start_date, $end_date ); ?> 
						</span>
						
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php
				$trip_duration = get_post_meta( get_the_ID(), 'wp_travel_trip_duration', true );
				$trip_duration = ( $trip_duration ) ? $trip_duration : 0; ?>
				<?php if ( ( int ) $trip_duration > 0 ) : ?>
					<div class="wp-travel-trip-time trip-duration">
						<i class="fa fa-clock-o"></i>
											
						<span class="wp-travel-trip-duration">
							<?php echo esc_html( $trip_duration . ' Days' ); ?>
						</span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<span class="post-category">
				<div class="entry-meta">
					<?php //wp_travel_single_trip_rating( get_the_ID() ) ?>
					<?php $average_rating = wp_travel_get_average_rating( ) ?>				
					<div class="wp-travel-average-review" title="<?php printf( __( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">
						
							<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
								<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( __( 'out of %s5%s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
							</span>
						
					</div> <span class="review-count">( <?php printf( '%s reviews', wp_travel_get_rating_count() ); ?> )</span>
				</div>
			</span>
		</div>
		
		<?php if ( $enable_sale ) : ?>
  			<div class="wp-travel-offer">
  			    <span><?php esc_html_e( 'Offer', 'wp-travel' ); ?></span>
  			</div>
  		<?php endif; ?>

	</div>
</div>
</li>
