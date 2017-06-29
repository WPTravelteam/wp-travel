<?php get_header( 'itinerary' ); ?>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<div class="container">
<?php while ( have_posts() ) : the_post(); ?>

	<?php wp_travel_get_template_part( 'content', 'archive-itineraries' ); ?>

<?php endwhile; // end of the loop. ?></div>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<?php get_footer( 'itinerary' ); ?>
