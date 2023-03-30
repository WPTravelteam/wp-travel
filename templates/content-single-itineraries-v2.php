<?php
/**
 * Itinerary Single Contnet Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-single-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_travel_itinerary;
?>

<?php
do_action( 'wp_travel_before_single_itinerary', get_the_ID() );
if ( post_password_required() ) {
	echo get_the_password_form(); //phpcs:ignore
	return;
}
$wrapper_class = wptravel_get_theme_wrapper_class();
do_action( 'wp_travel_before_content_start' );
?>

<div id="wti_main-<?php the_ID(); ?>" <?php post_class( 'itinerary_v2' ); ?>>
	<div class="wti__wrapper">
		<div class="wti__single-inner">
			<?php
				/**
				 * Hook 'wp_travel_itinerary_v2_hero_section'.
				 *
				 * @hooked 'wp_travel_hero_section'.
				 * @param int get_the_ID().
				 */
				do_action( 'wp_travel_itinerary_v2_hero_section', get_the_ID() );
			?>
			<div class="wti__single-wrapper">
				<?php
				/**
				 * Hook 'wp_travel_single_trip_after_header'.
				 *
				 * @hooked 'wp_travel_single_trip_tabs_and_price'.
				 * @hooked 'wp_travel_single_trip_contents' - 15.
				 * @param int get_the_ID().
				 */
				wptravel_do_deprecated_action( 'wp_travel_after_single_itinerary_header', array( get_the_ID() ), '2.0.4', 'wp_travel_single_trip_after_header' );  // @since 1.0.4 and deprecated in 2.0.4
				do_action( 'wp_travel_single_trip_after_header', get_the_ID() );
				?>
			</div>
		</div><!-- wti__single-inner -->
	</div><!-- wti__wrapper -->
</div><!-- #wti_main-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary', get_the_ID() ); ?>
