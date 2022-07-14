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
$template      = get_option( 'template' );
$current_theme = wp_get_theme();
$sanitized_get = WP_Travel::get_sanitize_request( 'get', true );
$view_mode     = wptravel_get_archive_view_mode( $sanitized_get );
get_header( 'itinerary' );


if ( 'Divi' === $template ) {
	?>
	<div class="container clearfix">
	<?php
}


if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) ) {
	?> <div class="wrap"><?php
}
do_action( 'wp_travel_before_main_content' );
?>
<div id="wptravel-archive-wrapper" class="wptravel-archive-wrapper <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?> ">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
		endwhile; // end of the loop.
	else :
		wptravel_get_template_part( 'v2/content', 'archive-itineraries-none' );
	endif;
	?>
</div>
<?php
do_action( 'wp_travel_after_main_content' );
do_action( 'wp_travel_archive_listing_sidebar' );

if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) || 'Divi' === $template ) {
	?>
	</div>
	<?php
}

get_footer( 'itinerary' );
