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
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_travel_itinerary;
?>

<?php
do_action( 'wp_travel_before_single_itinerary', get_the_ID() );
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

do_action( 'wp_travel_before_content_start');
?>

<div id="itinerary-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="content entry-content">
		<div class="wp-travel trip-headline-wrapper clearfix">
	        <div class="wp-travel-feature-slide-content featured-side-image left-plot">
	            <div class="banner-image-wrapper" style="background-image: url(<?php echo esc_url( wp_travel_get_post_thumbnail_url( get_the_ID(), 'large' ) ) ?>)">
						<?php echo wp_kses( wp_travel_get_post_thumbnail( get_the_ID() ), wp_travel_allowed_html( array( 'img' )  ) ); ?>
	        	</div>
				<?php // if ( $wp_travel_itinerary->is_sale_enabled() ) : ?>
				<?php if ( wp_travel_is_enable_sale_price( get_the_ID() ) ) : ?>

					<div class="wp-travel-offer">
						<span><?php esc_html_e( 'Offer', 'wp-travel' ) ?></span>
					</div>
					<?php endif; ?>
						<?php if ( $wp_travel_itinerary->has_multiple_images() ) : ?>
					<div class="wp-travel-view-gallery">
						<a class="top-view-gallery" href=""><?php esc_html_e( 'View Gallery', 'wp-travel' ) ?></a>
					</div>
				<?php endif; ?>
	        </div>
	        <div class="wp-travel-feature-slide-content featured-detail-section right-plot">
				<div class="right-plot-inner-wrap">
					<?php do_action( 'wp_travel_before_single_title', get_the_ID() ); ?>
					<?php wp_travel_do_deprecated_action( 'wp_tarvel_before_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_single_title' ); ?>
					<?php $show_title = apply_filters( 'wp_travel_show_single_page_title', true ); ?>
					<?php if ( $show_title ) : ?>
						<header class="entry-header">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						</header>
					<?php endif; ?>					
					<?php wp_travel_do_deprecated_action( 'wp_travel_after_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_single_trip_after_title' );  // @since 1.0.4 and deprecated in 2.0.4 ?>
					<?php do_action( 'wp_travel_single_trip_after_title', get_the_ID() ) ?>
					
				</div>
	        </div>
	    </div>
		<?php
			wp_travel_do_deprecated_action( 'wp_travel_after_single_itinerary_header', array( get_the_ID() ), '2.0.4', 'wp_travel_single_trip_after_header' );  // @since 1.0.4 and deprecated in 2.0.4
			do_action( 'wp_travel_single_trip_after_header', get_the_ID() );
		?>
	</div><!-- .summary -->
</div><!-- #itinerary-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary', get_the_ID() ); ?>
