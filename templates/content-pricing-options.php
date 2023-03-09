<?php
/**
 * Itinerary Pricing Options Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-pricing-options.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   1.1.5
 * @since   5.2.9 Removed PHP Pricing options for the booking tab.
 */
global $post;
global $wp_travel_itinerary;

$settings        = wptravel_get_settings();
$trip_id         = $post->ID;
$trip_id         = apply_filters( 'wp_travel_booking_tab_custom_trip_id', $trip_id );
$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
$wrapper_id      = isset( $tab_key ) ? $tab_key . '-booking-form' : 'booking-form'; // temp fixes.
if ( wptravel_is_react_version_enabled() ) {
	$wrapper_id = isset( $tab_key ) ? $tab_key : 'booking';
}

$settings_listing = $settings['trip_date_listing'];
$fixed_departure  = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
$wrapper_class    = 'dates' === $settings_listing && 'yes' === $fixed_departure ? 'wp-travel-list-view' : 'wp-travel-calendar-view';

?>

<div  class="tab-list-content">
	<div id="<?php echo esc_attr( $wrapper_id ); ?>" class="<?php echo esc_attr( $wrapper_class ); ?>">
		<?php esc_html_e( 'Please wait...', 'wp-travel' ); ?>
	</div>
</div>
