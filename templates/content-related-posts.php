<?php
/**
 * Related Posts Template.
 * @package WP_Travel
 */
$post_id = $args;
$settings = wp_travel_get_settings();
$hide_related_itinerary = ( isset( $settings['hide_related_itinerary'] ) && '' !== $settings['hide_related_itinerary'] ) ? $settings['hide_related_itinerary'] : 'no';

	if ( 'yes' === $hide_related_itinerary ) {
		return;
	}
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	// For use in the loop, list 5 post titles related to first tag on current post.
	$terms = wp_get_object_terms( $post_id, 'itinerary_types' );

	$no_related_post_message = '<p class="wp-travel-no-detail-found-msg">' . esc_html__( 'Related trip not found.', 'wp-travel' ) . '</p>';
	?>
	 <div class="wp-travel-related-posts wp-travel-container-wrap">
		 <h2><?php echo apply_filters( 'wp_travel_related_post_title', esc_html__( 'Related Trips', 'wp-travel' ) ); ?></h2>
		<div class="wp-travel-itinerary-items"> 
			 <?php
		 	if ( ! empty( $terms ) ) {
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				$col_per_row = apply_filters( 'wp_travel_related_itineraries_col_per_row' , '3' );
				$args = array(
					'post_type' => WP_TRAVEL_POST_TYPE,
					'post__not_in' => array( $post_id ),
					'posts_per_page' => $col_per_row,
					'tax_query' => array(
						array(
							'taxonomy' => 'itinerary_types',
							'field' => 'id',
							'terms' => $term_ids,
						),
					),
				);
				$query = new WP_Query( $args );
			if ( $query->have_posts() ) { ?>
				
				<ul style="grid-template-columns:repeat(<?php esc_attr_e( $col_per_row, 'wp-travel') ?>, 1fr)" class="wp-travel-itinerary-list">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item' ); ?>
					<?php endwhile; ?>
				</ul>
			<?php
			} else {
				wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
			}
			wp_reset_query();
	 } else {
		wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
	 }
	 ?>
	 </div>
</div>