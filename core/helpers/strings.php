<?php
/**
 * Helpers cache.
 *
 * @package core/helpers
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Strings class.
 * 
 * @since WP Travel 4.6.4
 */
class WpTravel_Helpers_Strings { // @phpcs:ignore

	/**
	 * Set constants to prevent caching by some plugins.
	 *
	 * @return array
	 */
	public static function get() {
		$localized_strings = array(
			'alert'                     => self::alert_strings(),
			'bookings'                  => self::booking_strings(),
			'empty_results'             => self::empty_results_strings(),
			'activities'                => __( 'Activities', 'wp-travel' ),
			'book_n_pay'                => __( 'Book and Pay', 'wp-travel' ),
			'book_now'                  => __( 'Book Now', 'wp-travel' ),
			'close'                     => __( 'Close', 'wp-travel' ),
			'confirm'                   => __( 'Are you sure you want to remove?', 'wp-travel' ),
			'custom_min_payout'         => __( 'Custom Min. Payout %', 'wp-travel' ),
			'custom_partial_payout'     => __( 'Custom Partial Payout', 'wp-travel' ),
			'dates'                     => __( 'Dates', 'wp-travel' ),
			'enter_location'            => __( 'Enter Location', 'wp-travel' ),
			'featured_book_now'         => __( 'Book Now', 'wp-travel' ), // Book Now at the featured section.
			'featured_trip_enquiry'     => __( 'Trip Enquiry', 'wp-travel' ), // Trip Enquiry at the featured section.
			'filter_by'                 => __( 'Filter By', 'wp-travel' ),
			'fixed_departure'           => __( 'Fixed Departure', 'wp-travel' ),
			'from'                      => __( 'From', 'wp-travel' ),
			'global_partial_payout'     => __( 'Global Partial Payout', 'wp-travel' ),
			'global_trip_enquiry'       => __( 'Global Trip Enquiry Option', 'wp-travel' ),
			'group_size'                => __( 'Group Size', 'wp-travel' ),
			'latitude'                  => __( 'Latitude', 'wp-travel' ),
			'loading'                   => __( 'Loading..', 'wp-travel' ),
			'minimum_payout'            => __( 'Minimum Payout', 'wp-travel' ),
			'longitude'                 => __( 'Longitude', 'wp-travel' ),
			'notice_button_text'        => array( 'get_pro' => __( 'Get WP Travel Pro', 'wp-travel' ) ),
			'prices'                    => __( 'Prices', 'wp-travel' ),
			'price_category'            => __( 'Price Category', 'wp-travel' ),
			'price_per'                 => __( 'Price Per', 'wp-travel' ),
			'person'                    => __( 'Person', 'wp-travel' ),
			'group'                     => __( 'Group', 'wp-travel' ),
			'category'                  => __( 'Category', 'wp-travel' ),
			'select'                    => __( 'Select', 'wp-travel' ),
			'to'                        => __( 'To', 'wp-travel' ),
			'trip_duration'             => __( 'Trip Duration', 'wp-travel' ),
			'trip_enquiry'              => __( 'Trip Enquiry', 'wp-travel' ),
			'trip_type'                 => __( 'Trip Type', 'wp-travel' ),
			'use_global_payout'         => __( 'Use Global Payout', 'wp-travel' ),
			'reviews'                   => __( 'Reviews', 'wp-travel' ),
			'location'                  => __( 'Location', 'wp-travel' ),
			'locations'                 => __( 'Locations', 'wp-travel' ),
			'price'                     => __( 'Price', 'wp-travel' ),
			'enable_sale'               => __( 'Enable Sale', 'wp-travel' ),
			'sale_price'                => __( 'Sale Price', 'wp-travel' ),
			'default_pax'               => __( 'Default Pax', 'wp-travel' ),
			'trip_date'                 => __( 'Trip date', 'wp-travel' ),
			'add_date'                  => __( 'Please add date.', 'wp-travel' ),
			'trip_name'                 => __( 'Trip Name', 'wp-travel' ),
			'trip_code'                 => __( 'Trip code', 'wp-travel' ),
			'show'                      => __( 'Show', 'wp-travel' ),
			'booking_tab_content_label' => __( 'Select Date and Pricing Options', 'wp-travel' ),
			'keyword'                   => __( 'Keyword', 'wp-travel' ),
			'fact'                      => __( 'Fact', 'wp-travel' ),
			'price_range'               => __( 'Price Range', 'wp-travel' ),
			'pricing_name'              => __( 'Pricing Name', 'wp-travel' ),
			'max_pax'                   => __( 'Max Pax.', 'wp-travel' ),
			'min_pax'                   => __( 'Min Pax.', 'wp-travel' ),
			'global_trip_title'         => __( 'Global Trip Title', 'wp-travel' ),
			'custom_trip_title'         => __( 'Custom Trip Title', 'wp-travel' ),
			'display'                   => __( 'Display', 'wp-travel' ),
			'use_global_tabs_layout'    => __( 'Use Global Tabs Layout', 'wp-travel' ),
			'system_information'        => __( 'System Information', 'wp-travel' ),
			'save_settings'             => __( 'Save Settings', 'wp-travel' ),
			
			// Admin related data.
			'admin_tabs'                => self::admin_tabs_strings(),
			'notices'                   => self::admin_notices(),
			'messages' => array(
				'add_fact'        => __( 'Please add new fact here.', 'wp-travel' ),
				'add_new_fact'    => __( 'Please add fact from the settings', 'wp-travel' ),  // add new fact in settings.
				'add_new_faq'     => __( 'Please add new FAQ here.', 'wp-travel' ),  // add new fact in settings.
				'no_gallery'      => __( 'There are no gallery images.', 'wp-travel' ),
				'pricing_message' => __( 'Before making any changes in date, please make sure pricing is saved.', 'wp-travel' ),
				'save_changes'    => __( '* Please save the changes', 'wp-travel' ),
				'total_payout'    => __( 'Error: Total payout percent is not equals to 100%. Please update the trip once else global partial percent will be used as default.', 'wp-travel' ),
				'trip_saved'      => __( 'Trip Saved!', 'wp-travel' ),
				'upload_desc'     => __( 'Drop files here to upload.', 'wp-travel' ),
			),
			'update'                 => __( 'Update', 'wp-travel' ),
			'upload'                 => __( 'Upload', 'wp-travel' ),
			'media_library'          => __( 'Media Library', 'wp-travel' ),
			'save_changes'           => __( 'Save Changes', 'wp-travel' ),
			'add'                    => __( '+ Add', 'wp-travel' ),
			'remove'                 => __( '-Remove', 'wp-travel' ),
			'add_date'               => __( '+ Add Date', 'wp-travel' ),
			'remove_date'            => __( '-Remove Date', 'wp-travel' ),
			'add_category'               => __( '+ Add Category', 'wp-travel' ),
			'remove_category'            => __( '-Remove Category', 'wp-travel' ),
			'add_extras'               => __( '+ Add Extras', 'wp-travel' ),
			'remove_extras'            => __( '-Remove Extras', 'wp-travel' ),
			'add_fact'               => __( '+ Add Fact', 'wp-travel' ),
			'remove_fact'            => __( '-Remove Fact', 'wp-travel' ),
			'add_faq'                => __( '+ Add Faq', 'wp-travel' ),
			'remove_faq'             => __( '-Remove Faq', 'wp-travel' ),
			'add_price'                => __( '+ Add Price', 'wp-travel' ),
			'remove_price'             => __( '-Remove Price', 'wp-travel' ),
			'add_itinerary'          => __( '+ Add Itinerary', 'wp-travel' ),
			'remove_itinerary'       => __( '-Remove Itinerary', 'wp-travel' ),
			'date_label'             => __( 'Date Label', 'wp-travel' ),
			'select_pricing'         => __( 'Select pricing options', 'wp-travel' ),
			'select_all'             => __( 'Select All', 'wp-travel' ),
			'select_type'            => __( 'Select Type', 'wp-travel' ),
			'start_date'             => __( 'Start Date', 'wp-travel' ),
			'end_date'               => __( 'End Date', 'wp-travel' ),
			'date_time'              => __( 'Date & time', 'wp-travel' ),
			'enable_fixed_departure' => __( 'Enable Fixed Departure', 'wp-travel' ),
			'nights'                 => __( 'Night(s)', 'wp-travel' ),
			'days'                   => __( 'Day(s)', 'wp-travel' ),
			'value'                  => __( 'Value', 'wp-travel' ),
			'faq_questions'          => __( 'FAQ Questions ?', 'wp-travel' ),
			'enter_question'         => __( 'Enter your question', 'wp-travel' ),
			'faq_answer'             => __( 'Your Answer', 'wp-travel' ),
			'trip_includes'          => __( 'Trip Includes', 'wp-travel' ),
			'trip_excludes'          => __( 'Trip Excludes', 'wp-travel' ),
			
			'itinerary'              => __( 'Itinerary', 'wp-travel' ),
			'day_x'                  => __( 'Day X', 'wp-travel' ),
			'your_plan'              => __( 'Your Plan', 'wp-travel' ),
			'trip_outline'           => __( 'Trip Outline', 'wp-travel' ),
			'itinerary_label'        => __( 'Itinerary Label', 'wp-travel' ),
			'itinerary_title'        => __( 'Itinerary Title', 'wp-travel' ),
			'itinerary_date'         => __( 'Itinerary Date', 'wp-travel' ),
			'itinerary_time'         => __( 'Itinerary Time', 'wp-travel' ),
			'hours'                  => __( 'Hours', 'wp-travel' ),
			'minute'                 => __( 'Minute', 'wp-travel' ),
			'description'            => __( 'Description', 'wp-travel' ),
			'map'                    => __( 'Map', 'wp-travel' ),

			'help_text'				 => array(
				'date_pricing'       => __( 'Type Pricing option and enter', 'wp-travel' ),
				'enable_location'    => __( 'Enable/Disable latitude-longitude option', 'wp-travel' ),
				'use_global_payout'  => __( 'Note: In case of multiple cart items checkout, global payout will be used.', 'wp-travel' ),
			),
			'full_name'              => __( 'Full Name', 'wp-travel' ),
			'enter_your_name'        => __( 'Enter your name', 'wp-travel' ),
			'email'                  => __( 'Email', 'wp-travel' ),
			'enter_your_email'       => __( 'Enter your email', 'wp-travel' ),
			'enquiry_message'        => __( 'Enquiry Message', 'wp-travel' ),
			'enter_your_enquiry'     => __( 'Enter your enquiry...', 'wp-travel' ),
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
			'trip_extras'                   => __( 'Trip Extras', 'wp-travel' ),
			'trip_extras_list_label'        => __( 'Trip Extras', 'wp-travel' ),
			'trip_extras_link_label'        => __( 'Learn More', 'wp-travel' ),
			'available_trip_times'          => __( 'Available times', 'wp-travel' ),
			'booking_option'                => __( 'Booking Options', 'wp-travel' ),
			'booking_with_payment'          => __( 'Booking with payment', 'wp-travel' ),
			'booking_only'                  => __( 'Booking only', 'wp-travel' ),
		);
	}

	public static function admin_tabs_strings() {
		return array(
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
		);
	}

	public static function alert_strings() {
		return array(
			'atleast_min_pax_alert' => __( 'Please select at least minimum pax.', 'wp-travel' ),
			'both_pax_alert'        => __( 'Pax should be between {min_pax} and {max_pax}.', 'wp-travel' ),
			'max_pax_alert'         => __( 'Pax should be lower than or equal to {max_pax}.', 'wp-travel' ),
			'min_pax_alert'         => __( 'Pax should be greater than or equal to {min_pax}.', 'wp-travel' ),
			'remove_category'       => __( 'Are you sure to delete category?', 'wp-travel' ), // admin alert.
			'remove_date'           => __( 'Are you sure to delete this date?', 'wp-travel' ), // admin alert.
			'remove_fact'           => __( 'Are you sure to delete remove fact?', 'wp-travel' ), // admin alert.
			'remove_faq'            => __( 'Are you sure to delete FAQ?', 'wp-travel' ), // admin alert.
			'remove_gallery'        => __( 'Are you sure, want to remove the image from Gallery?', 'wp-travel' ), // admin alert.
			'remove_itinerary'      => __( 'Are you sure to delete this itinerary?', 'wp-travel' ), // admin alert.
			'remove_price'          => __( 'Are you sure to delete this price?', 'wp-travel' ), // admin alert.
			'required_pax_alert'    => __( 'Pax is required.', 'wp-travel' ),
		);
	}

	public static function empty_results_strings() {
		return array(
			'trip_type'  => __( 'No Trip Type', 'wp-travel' ),
			'activities' => __( 'No Activities', 'wp-travel' ),
			'group_size' => __( 'No size limit', 'wp-travel' ),
			'dates'      => __( 'No dates found', 'wp-travel' ),
			'itinerary'  => __( 'No Itineraries found.', 'wp-travel' ),
			'add_extras' => __( 'Please add extras first', 'wp-travel' ),
			'extras'     => __( 'No extras found.', 'wp-travel' ),
			'category'   => __( 'No category found.', 'wp-travel' ),
		);
	}

	public static function admin_notices() {
		return array(
			'checkout_option' => array(
				'title'       => __( 'Need to add your checkout options?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your checkout options for all of your trips !', 'wp-travel' ),
			),
			'inventory_option' => array(
				'title'       => __( 'Need to add your inventory options?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your inventory options in all of your trips !', 'wp-travel' ),
			),
			'downloads_option' => array(
				'title'       => __( 'Need to add your downloads?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
			),
			'need_more_option' => array(
				'title'       => __( 'Need More Options ?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel' ),
			),
			'need_extras_option' => array(
				'title'       => __( 'Need advance Trip Extras options?', 'wp-travel' ),
				'description' => '',
			),
			'global_faq_option' => array(
				'title'       => __( 'Tired of updating repitative FAQs ?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can create and use Global FAQs in all of your trips !', 'wp-travel' ),
			),
			'trip_code_option' => array(
				'description' => __( 'Need Custom Trip Code? Check', 'wp-travel' ),
			),
			'map_option' => array(
				'title'       => __( 'Need alternative maps ?', 'wp-travel' ),
				'description' => __( 'If you need alternative to current map then you can get free or pro maps for WP Travel.', 'wp-travel' ),
			),
			'map_key_option' => array(
				'description' => __( "You can add 'Google Map API Key' in the %ssettings%s to use additional features.", 'wp-travel' ),
			),
			'global_tab_option' => array(
				'title'       => __( 'Need Additional Tabs ?', 'wp-travel' ),
				'description' => __( "By upgrading to Pro, you can get trip specific custom tabs addition options with customized content and sorting !", 'wp-travel' ),
			),
		);
	}
}
