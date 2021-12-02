<?php
/**
 * Itinerary Archive Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/archive-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     WP_Travel
 * @since       1.0.0
 */

get_header( 'itinerary' );

$template = get_option( 'template' );

if ( 'Divi' === $template ) {
	?>
		<div class="container clearfix">
	<?php
}

$current_theme = wp_get_theme();

if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) ) {
	?>
		<div class="wrap">
	<?php
}

do_action( 'wp_travel_before_main_content' );

$itinerary_layout_v2_enabled = wptravel_use_itinerary_v2_layout();

if ( have_posts() ) :

	while ( have_posts() ) :
		the_post();

		if ( $itinerary_layout_v2_enabled ) {

			wptravel_get_template_part( 'content', 'archive-itineraries-new' );

		} else {

			wptravel_get_template_part( 'content', 'archive-itineraries' );

		}
	endwhile; // end of the loop.
else :
	wptravel_get_template_part( 'content', 'archive-itineraries-none' );
endif;

do_action( 'wp_travel_after_main_content' );

do_action( 'wp_travel_archive_listing_sidebar' );

if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) ) {
	?>
		</div>
	<?php
}

if ( 'Divi' === $template ) {
	?>
		</div>
	<?php
}

get_footer( 'itinerary' );

?>
