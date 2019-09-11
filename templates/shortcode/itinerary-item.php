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
$post_id 		= get_the_ID();
$enable_sale 	= wp_travel_is_enable_sale_price( $post_id );
$trip_price 	= wp_travel_get_trip_price( $post_id );
$sale_price 	= wp_travel_get_trip_sale_price( $post_id ); ?>
<li>
<div class="wp-travel-post-item-wrapper">
    <div class="wp-travel-post-wrap-bg">

		<div class="wp-travel-post-thumbnail">
			<a href="<?php the_permalink() ?>">
				<?php echo wp_travel_get_post_thumbnail( $post_id, 'wp_travel_thumbnail' ); ?>
			</a>
			<?php wp_travel_save_offer( $post_id ); ?>
		</div>
		<div class="wp-travel-post-info clearfix">
			<?php do_action( 'wp_travel_before_item_title', get_the_ID() ); ?>
			<?php wp_travel_do_deprecated_action( 'wp_tarvel_before_archive_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_item_title' ); ?>
			<h4 class="post-title">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute( array( 'before' => __( 'Permalink to: ', 'wp-travel' ) ) ); ?>">
					<?php the_title(); ?>
				</a>
			</h4>
			<?php do_action( 'wp_travel_after_archive_title', get_the_ID() ) ?>
			<div class="recent-post-bottom-meta">
				<?php wp_travel_trip_price( $post_id, true ); ?>
			</div>
		</div>
		<div class="wp-travel-post-content">
			<?php wp_travel_get_trip_duration( $post_id ); ?>
			<span class="post-category">
				<div class="entry-meta">
					<?php if ( wp_travel_tab_show_in_menu( 'reviews' ) ) : ?>
						<?php $average_rating = wp_travel_get_average_rating( $post_id ) ?>
						<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">

							<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
								<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
							</span>
						</div>
						<?php $count = (int) wp_travel_get_review_count() ?>
						<span class="wp-travel-review-text"> (<?php printf( esc_html( _n( '%d Review', '%d Reviews', $count, 'wp-travel' ) ), $count ); ?>)</span>
					<?php endif; ?>
					<?php $terms = get_the_terms( $post_id, 'itinerary_types' ); ?>
					<div class="category-list-items">
						<?php if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
							<i class="wt-icon wt-icon-folder" aria-hidden="true"></i>
							<?php
							$first_term = array_shift( $terms );
							$term_name = $first_term->name;
							$term_link = get_term_link( $first_term->term_id, 'itinerary_types' ); ?>
							<a href="<?php echo esc_url( $term_link ); ?>" rel="tag">
								<?php echo esc_html( $term_name ); ?>
							</a>
							<?php if ( count( $terms ) > 0 ) : ?>
							<div class="wp-travel-caret">
								<i class="wt-icon wt-icon-caret-down"></i>
								<div class="sub-category-menu">
									<?php foreach( $terms as $term ) : ?>
										<?php
											$term_name = $term->name;
											$term_link = get_term_link( $term->term_id, 'itinerary_types' ); ?>
										<a href="<?php echo esc_url( $term_link ); ?>">
											<?php echo esc_html( $term_name ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
							<?php endif; ?>

						<?php endif; ?>
					</div>


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
