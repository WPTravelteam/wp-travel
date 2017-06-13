<?php get_header( 'itinerary' ); ?>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<?php while ( have_posts() ) : the_post(); ?>

	<?php wp_travel_get_template_part( 'content', 'single-itineraries' ); ?>

<?php endwhile; // end of the loop. ?>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<?php get_footer( 'itinerary' ); ?>
