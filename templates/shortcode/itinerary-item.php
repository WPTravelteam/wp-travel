<?php
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
		<div class="wp-travel-post-content">

			<h4 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			<span class="post-category">


				<div class="entry-meta">
					<div class="category-list-items">
						<span class="post-category">
							<?php $terms = get_the_terms( get_the_ID(), 'itinerary_types' ); ?>
						    <?php if ( is_array( $terms ) && count( $terms ) > 0 ) : ?>
						    	<i class="fa fa-folder-o" aria-hidden="true"></i>
						    	<?php
						    	$first_term = array_shift( $terms );
						    	$term_name = $first_term->name;
						    	$term_link = get_term_link( $first_term->term_id, 'itinerary_types' ); ?>
								<a href="<?php echo esc_url( $term_link, 'wp-travel' ); ?>" rel="tag">
									<?php esc_html_e( $term_name, 'wp-travel' ); ?>
								</a>
								<div class="wp-travel-caret">
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
					<div class="wp-travel-average-review">
					    <span><?php printf( '%s reviews',wp_travel_get_rating_count() ); ?></span>
					</div>
				</div>
			</span>
		</div>
		<div class="wp-travel-post-thumbnail">
		 	<a href="<?php the_permalink() ?>">
			<?php echo wp_travel_get_post_thumbnail( get_the_ID(), 'wp_travel_thumbnail' ); ?>
		   	</a>
		   	<?php wp_travel_save_offer( get_the_ID() ); ?>
		</div>
		<div class="recent-post-bottom-meta">
			<?php wp_travel_trip_price( get_the_ID(), true ); ?>
		</div>
		<?php if ( $enable_sale ) : ?>
  			<div class="wp-travel-offer">
  			    <span><?php esc_html_e( 'Offer', 'wp-travel' ); ?></span>
  			</div>
  			<?php endif; ?>

	</div>
</div>
</li>
