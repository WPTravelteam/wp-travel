<?php
/**
 * Helpers cache.
 *
 * @package core/helpers
 */

defined( 'ABSPATH' ) || exit;
/**
 * WP_Travel_Helpers_Strings class.
 */
class WP_Travel_Helpers_Strings { // @phpcs:ignore

	/**
	 * Set constants to prevent caching by some plugins.
	 *
	 * @return array
	 */
	public static function get() {
		$localized_strings = array(
			'from'                      => __( 'From', 'wp-travel' ),
			'to'                        => __( 'To', 'wp-travel' ),
			'confirm'                   => __( 'Are you sure you want to remove?', 'wp-travel' ),
			'book_now'                  => __( 'Book Now', 'wp-travel' ),
			'book_n_pay'                => __( 'Book and Pay', 'wp-travel' ),
			'select'                    => __( 'Select', 'wp-travel' ),
			'close'                     => __( 'Close', 'wp-travel' ),
			'featured_book_now'         => __( 'Book Now', 'wp-travel' ), // Book Now at the featured section.
			'featured_trip_enquiry'     => __( 'Trip Enquiry', 'wp-travel' ), // Trip Enquiry at the featured section.
			'trip_enquiry'              => __( 'Trip Enquiry', 'wp-travel' ),
			'trip_type'                 => __( 'Trip Type', 'wp-travel' ),
			'activities'                => __( 'Activities', 'wp-travel' ),
			'group_size'                => __( 'Group Size', 'wp-travel' ),
			'reviews'                   => __( 'Reviews', 'wp-travel' ),
			'locations'                 => __( 'Locations', 'wp-travel' ),
			'fixed_departure'           => __( 'Fixed Departure', 'wp-travel' ),
			'trip_duration'             => __( 'Trip Duration', 'wp-travel' ),
			'filter_by'                 => __( 'Filter By', 'wp-travel' ),
			'price'                     => __( 'Price', 'wp-travel' ),
			'location'                  => __( 'Location', 'wp-travel' ),
			'trip_date'                 => __( 'Trip date', 'wp-travel' ),
			'add_date'                  => __( 'Please add date.', 'wp-travel' ),
			'trip_name'                 => __( 'Trip Name', 'wp-travel' ),
			'trip_code'                 => __( 'Trip code', 'wp-travel' ),
			'show'                      => __( 'Show', 'wp-travel' ),
			'booking_tab_content_label' => __( 'Select Date and Pricing Options', 'wp-travel' ),
			'keyword'                   => __( 'Keyword', 'wp-travel' ),
			'fact'                      => __( 'Fact', 'wp-travel' ),
			'price_range'               => __( 'Price Range', 'wp-travel' ),
			'bookings'                  => self::booking_strings(),
			'empty_results'             => array(
				'trip_type'  => __( 'No Trip Type', 'wp-travel' ),
				'activities' => __( 'No Activities', 'wp-travel' ),
				'group_size' => __( 'No size limit', 'wp-travel' ),
	
			),
			'alert'                     => array(
				'required_pax_alert'    => __( 'Pax is required.', 'wp-travel' ),
				'atleast_min_pax_alert' => __( 'Please select at least minimum pax.', 'wp-travel' ),
				'min_pax_alert'         => __( 'Pax should be greater than or equal to {min_pax}.', 'wp-travel' ),
				'max_pax_alert'         => __( 'Pax should be lower than or equal to {max_pax}.', 'wp-travel' ),
				'both_pax_alert'        => __( 'Pax should be between {min_pax} and {max_pax}.', 'wp-travel' ),
			),
			'admin_tabs' => array(
				'itinerary'         => __( 'Itinerary', 'wp-travel' ),
				'price_n_dates'     => __( 'Prices & Dates', 'wp-travel' ),
				'includes_excludes' => __('Includes/Excludes', 'wp-travel'),
				'facts'             => __('Facts', 'wp-travel'),
				'gallery'           => __('Gallery', 'wp-travel'),
				'locations'         => __('Locations', 'wp-travel'),
				'checkout'          => __('Checkout', 'wp-travel'),
				'inventory_options' => __('Inventory Options', 'wp-travel'),
				'faqs'              => __('FAQs', 'wp-travel'),
				'downloads'         => __('Downloads', 'wp-travel'),
				'misc'              => __('Misc', 'wp-travel'),
				'tabs'              => __('Tabs', 'wp-travel'),
			),
			'notices' => array(
				
			),
	
		);
	
		return apply_filters( 'wp_travel_strings', $localized_strings );
		
	}

	public static function booking_strings() {
		return array(
			'pricing_name'                  => __( 'Pricing Name', 'wp-travel' ),
			'start_date'                    => __( 'Start', 'wp-travel' ),
			'end_date'                      => __( 'End', 'wp-travel' ),
			'action'                        => __( 'Action', 'wp-travel' ),
			'recurring'                     => __( 'Recurring:', 'wp-travel' ),
			'group_size'                    => __( 'Group (Min-Max)', 'wp-travel' ),
			'seats_left'                    => __( 'Seats left', 'wp-travel' ),
			'pax'                           => __( 'Pax', 'wp-travel' ),
			'select_pax'                    => __( 'Select Pax', 'wp-travel' ),
			'price'                         => __( 'Price', 'wp-travel' ),
			'arrival_date'                  => __( 'Arrival date', 'wp-travel' ),
			'departure_date'                => __( 'Departure date', 'wp-travel' ),
			'sold_out'                      => __( 'Sold Out', 'wp-travel' ),
			'select'                        => __( 'Select', 'wp-travel' ),
			'close'                         => __( 'Close', 'wp-travel' ),
			'book_now'                      => __( 'Book Now', 'wp-travel' ),
			'combined_pricing'              => __( 'Pricing', 'wp-travel' ), // Added for combined pricing label for categorized pricing @since 3.0.0
			'pricing_not_available'         => __( 'The pricing is not available on the selected Date. Please choose another date or pricing.', 'wp-travel' ),
			'max_pax_exceeded'              => __( 'Max. Pax Exceeded.', 'wp-travel' ),
			'date_select'                   => __( 'Select a Date', 'wp-travel' ),
			'date_select_to_view_options'   => __( 'Select a Date to view available pricings and other options.', 'wp-travel' ),
			'booking_tab_clear_all'         => __( 'Clear All', 'wp-travel' ),
			'booking_tab_cart_total'        => __( 'Total:', 'wp-travel' ),
			'booking_tab_booking_btn_label' => __( 'Book Now', 'wp-travel' ),
			'booking_tab_pax_selector'      => __( 'Pax Selector', 'wp-travel' ),
			'group_discount_tooltip'        => __( 'Group Discounts', 'wp-travel' ),
			'view_group_discount'           => __( 'Discounts', 'wp-travel' ),
			'pricings_list_label'           => __( 'Pricings', 'wp-travel' ),
			'person'                        => __( 'Person', 'wp-travel' ),
			'date'                          => __( 'Date', 'wp-travel' ),
			'trip_extras_list_label'        => __( 'Trip Extras', 'wp-travel' ),
			'trip_extras_link_label'        => __( 'Learn More', 'wp-travel' ),
			'available_trip_times'          => __( 'Available times', 'wp-travel' ),
			'booking_option'                => __( 'Booking Options', 'wp-travel' ),
			'booking_with_payment'          => __( 'Booking with payment', 'wp-travel' ),
			'booking_only'                  => __( 'Booking only', 'wp-travel' ),
		);
	}
}
