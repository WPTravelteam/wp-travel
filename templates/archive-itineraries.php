<?php get_header( 'itinerary' ); ?>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<?php while ( have_posts() ) : the_post(); ?>

	<?php wp_travel_get_template_part( 'content', 'archive-itineraries' ); ?>

<?php endwhile; // end of the loop. ?>	
<?php do_action( 'wp_travel_after_main_content' ); ?>
<?php
$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
?>
<?php wp_travel_pagination( $pagination_range, $max_num_pages ); ?>
<?php get_footer( 'itinerary' ); ?>
