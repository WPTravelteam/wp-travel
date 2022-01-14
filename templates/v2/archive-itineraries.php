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
$sanitized_get               = WP_Travel::get_sanitize_request();
$view_mode                   = wptravel_get_archive_view_mode( $sanitized_get );
?>
	<!-- <div id="btnContainer">
		<button class="btn active" onclick="listView()"><i class="fa fa-bars"></i> <?php esc_html_e( 'List', 'wp-travel' ); ?></button>
		<button class="btn" onclick="gridView()"><i class="fa fa-th-large"></i> <?php esc_html_e( 'Grid', 'wp-travel' ); ?></button>
	</div>
	<br> -->
	<section class="view-3">
		<div class="main-container">
			<div id="wptravel-archive-wrapper" class="<?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?> ">
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
		</div>
	</section>
	<?php

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
