<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	 do_action( 'wp_travel_before_single_itinerary', get_the_ID() );
	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="itinerary-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="content entry-content">
		<div class="wp-travel trip-headline-wrapper">
	         <div class="wp-travel-feature-slide-content featured-side-image left-plot">
	         	<?php the_post_thumbnail(); ?>
	         </div>
	         <div class="wp-travel-feature-slide-content featured-detail-section right-plot">
	         	<?php do_action( 'wp_tarvel_before_single_title', get_the_ID() ) ?>
	         	<header class="entry-header">
				<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
				</header>
				<?php do_action( 'wp_tarvel_after_single_title', get_the_ID() ) ?>
	        </div>
	    </div>
	    <?php do_action( 'wp_travel_after_single_itinerary_header', get_the_ID() ); ?>
        <?php //do_action( 'wp_travel_before_main_content' ); ?>
		<?php //echo apply_filters( 'the_content', get_the_content() ); ?>
		<?php //do_action( 'wp_travel_after_main_content' ); ?>
	</div><!-- .summary -->

</div><!-- #itinerary-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary', get_the_ID() ); ?>
