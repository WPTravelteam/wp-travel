<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	 do_action( 'wp_travel_before_archive_itinerary', get_the_ID() );
	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
	 $enable_sale 	= get_post_meta( get_the_ID(), 'wp_travel_enable_sale', true );
	 $group_size 	= get_post_meta( get_the_ID(), 'wp_travel_group_size', true );	 
	 $start_date 	= get_post_meta( get_the_ID(), 'wp_travel_start_date', true );
	 $end_date 		= get_post_meta( get_the_ID(), 'wp_travel_end_date', true );
?>



		<article class="wp-travel-default-article">
			<div class="wp-travel-article-image-wrap">
				<a href="<?php the_permalink(); ?>">
					<?php echo wp_travel_get_post_thumbnail( get_the_ID() ); ?>
				</a>
				<?php if ( $enable_sale ) : ?>
				<div class="wp-travel-offer">
      			    <span><?php esc_html_e( 'Offer', 'wp-travel' ) ?></span>
      			</div>
      			<?php endif; ?>
			</div>
			<div class="wp-travel-entry-content-wrapper">
			    <div class="description-left">
				    <header class="entry-header">
						<h2 class="entry-title">
						    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
					</header><!-- .entry-header -->
					<div class="entry-content">
						<?php the_excerpt(); ?>

					</div>
					<div class="wp-travel-average-review" title="Rated 4 out of 5">
					<?php wp_travel_trip_rating( get_the_ID() ); ?>
						<?php $count = (int) wp_travel_get_review_count() ?>
						<a href="">/<?php printf( _n( '%d Review', '%d Reviews', $count, 'wp-travel' ), $count ); ?></a>
					</div>
					<div class="entry-meta">
						<div class="category-list-items">
							<span class="post-category">
								<?php $terms = get_the_terms( get_the_ID(), 'itinerary_types' ); ?>
							    <?php if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
							    	<i class="fa fa-plane" aria-hidden="true"></i>
							    	<?php 
							    	$first_term = array_shift( $terms );
							    	$term_name = $first_term->name;
							    	$term_link = get_term_link( $first_term->term_id, 'itinerary_types' ); ?> 
									<a href="<?php echo esc_url( $term_link, 'wp-travel' ); ?>" rel="tag">									
										<?php esc_html_e( $term_name, 'wp-travel' ); ?>
									</a>
									<div class="caret">
									<?php if ( count( $terms ) > 0 ) : ?>
										<i class="fa fa-caret-down"></i>
									       	 
										<div class="sub-category-menu">
											<?php foreach( $terms as $term ) : ?>
												<?php
													$term_name = $term->name;
							    					$term_link = get_term_link( $term->term_id, 'itinerary_types' ); ?>
												<a href="<?php echo esc_url( $term_link, 'wp-travel' ); ?>">									
													<?php esc_html_e( $term_name, 'wp-travel' ); ?>
												</a>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
									</div>
								<?php endif; ?>
							</span>
						</div>
						<div class="travel-info">
							<i class="fa fa-child" aria-hidden="true"></i>
							<span class="value"><?php printf( '%d pax', $group_size ) ?></span>
						</div>
						<?php if ( $start_date && $end_date ) : ?>
							<div class="travel-info">
							    <i class="fa fa-clock-o"></i>
								<span class="value"><?php printf( '%s to %s', $start_date, $end_date ); ?></span>
							</div>
						<?php endif; ?>
					</div>
			    </div>
			    <div class="description-right">			    	
				    <?php wp_travel_trip_price( get_the_ID() ); ?>
				    <div class="wp-travel-explore">
							<a class="" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Explore', 'wp-travel' ); ?></a>
					</div>
			    </div>
			</div>
		</article>


<?php do_action( 'wp_travel_after_archive_itinerary', get_the_ID() ); ?>
