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
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.1.5
 */
global $post;
global $wp_travel_itinerary;

if ( ! class_exists( 'WP_Travel_FW_Form' ) ) {
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
}

$trip_id    = $post->ID;
$settings   = wp_travel_get_settings();
$form       = new WP_Travel_FW_Form();
$form_field = new WP_Travel_FW_Field();

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

$enable_pricing_options         = wp_travel_is_enable_pricing_options( $trip_id );
$enable_multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

$enable_checkout = apply_filters( 'wp_travel_enable_checkout', true );
$force_checkout  = apply_filters( 'wp_travel_is_force_checkout_enabled', false );

$pricing_option_type = wp_travel_get_pricing_option_type( $trip_id ); ?>

<div id="<?php echo isset( $tab_key ) ? esc_attr( $tab_key ) : 'booking'; ?>" class="tab-list-content">
	<?php
	if ( ( $enable_checkout ) || $force_checkout ) :
		// Set Default WP Travel options list as it is.
		$default_pricing_options = array( 'single-price', 'multiple-price' );
		if ( in_array( $pricing_option_type, $default_pricing_options ) ) {

			$trip_pricing_options_data = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );
			$trip_multiple_dates_data  = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true );

			if ( $enable_pricing_options && is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) !== 0 ) :

				$list_type = wp_travel_get_pricing_option_listing_type( $settings );

				if ( 'by-pricing-option' === $list_type ) {
					// Default pricing options template.
					do_action( 'wp_travel_booking_princing_options_list', $trip_pricing_options_data ); // Need to deprecate.
					do_action( 'wp_travel_booking_default_princing_list', $trip_id );

				} else {
					if ( 'yes' === $enable_multiple_fixed_departue && 'yes' === $fixed_departure && ( ! empty( $trip_multiple_dates_data ) && is_array( $trip_multiple_dates_data ) ) ) {
						// Date listing template.
						do_action( 'wp_travel_booking_departure_date_list', $trip_multiple_dates_data ); // Need To deprecate.
						do_action( 'wp_travel_booking_fixed_departure_list', $trip_id );
						
					} else {
						do_action( 'wp_travel_booking_princing_options_list', $trip_pricing_options_data ); // Neeed to deprecate.
						do_action( 'wp_travel_booking_default_princing_list', $trip_id );
					}
				}
			else :
				// Default pricing options template with trip id.
				do_action( 'wp_travel_booking_princing_options_list', (int) $trip_id ); // Neeed to deprecate.
				do_action( 'wp_travel_booking_default_princing_list', (int) $trip_id );
				?>
			<?php endif;
		} else {
			do_action( "wp_travel_{$pricing_option_type}_options_list", $trip_id );
		}
		?>
	<?php else : ?>
		<?php echo wp_travel_get_booking_form(); ?>
	<?php endif; ?>
</div>
