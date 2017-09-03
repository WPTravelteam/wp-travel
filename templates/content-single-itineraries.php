<?php
/**
 * Itinerary single content.
 *
 * @package WP Travel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
do_action( 'wp_travel_before_single_itinerary', get_the_ID() );
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
$enable_sale 	= get_post_meta( get_the_ID(), 'wp_travel_enable_sale', true );
?>

<div id="itinerary-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="content entry-content">
		<div class="wp-travel trip-headline-wrapper">
	         <div class="wp-travel-feature-slide-content featured-side-image left-plot">
	            <div class="banner-image-wrapper" style="background-image: url(<?php echo esc_url( wp_travel_get_post_thumbnail_url( get_the_ID() ) ) ?>)">
						<?php echo wp_kses( wp_travel_get_post_thumbnail( get_the_ID() ) ); ?>
	         	</div>
	         	<?php if ( $enable_sale ) : ?>
	      			<div class="wp-travel-offer">
	      			    <span><?php esc_html_e( 'Offer', 'wp-travel' ) ?></span>
	      			</div>
	      			<?php endif; ?>
	      			<div class="wp-travel-view-gallery">
	      				<a class="top-view-gallery" href=""><?php esc_html_e( 'View Gallery', 'wp-travel' ) ?></a>
	      			</div>
	         </div>
	         <div class="wp-travel-feature-slide-content featured-detail-section right-plot">
	           <div class="right-plot-inner-wrap">
		         	<?php do_action( 'wp_tarvel_before_single_title', get_the_ID() ) ?>
		         	<?php $show_title = apply_filters( 'wp_travel_show_single_page_title', true ); ?>
		         	<?php if ( $show_title ) : ?>
		         	<header class="entry-header">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						</header>
					<?php endif; ?>
					<?php do_action( 'wp_tarvel_after_single_title', get_the_ID() ) ?>
				</div>
	        </div>
	    </div>
	    <?php do_action( 'wp_travel_after_single_itinerary_header', get_the_ID() ); ?>
	</div><!-- .summary -->

</div><!-- #itinerary-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary', get_the_ID() ); ?>
