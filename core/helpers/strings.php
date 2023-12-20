<?php
/**
 * Helpers cache.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Strings class.
 *
 * @since 4.6.4
 */
class WpTravel_Helpers_Strings {
 // @phpcs:ignore

	/**
	 * Get all strings used in WP Travel.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
	public static function get() {

		$price_per = array(
			array(
				'label' => __( 'Person', 'wp-travel' ),
				'value' => 'person',
			),
			array(
				'label' => __( 'Group', 'wp-travel' ),
				'value' => 'group',
			)
		);

		$localized_strings = array(
			'activities'                => __( 'Activities', 'wp-travel' ),
			'add_date'                  => __( 'Please add date.', 'wp-travel' ),
			'alert'                     => self::alert_strings(),
			'book_n_pay'                => __( 'Book and Pay', 'wp-travel' ),
			'book_now'                  => __( 'Book Now', 'wp-travel' ),
			'booking_tab_content_label' => __( 'Select Date and Pricing Options for this trip in the Trip Options setting.', 'wp-travel' ),
			'bookings'                  => self::booking_strings(),
			'category'                  => __( 'Category', 'wp-travel' ),
			'close'                     => __( 'Close', 'wp-travel' ),
			'confirm'                   => __( 'Are you sure you want to remove?', 'wp-travel' ),
			'custom_min_payout'         => __( 'Custom Min. Payout %', 'wp-travel' ),
			'custom_partial_payout'     => __( 'Custom Partial Payout', 'wp-travel' ),
			'custom_trip_title'         => __( 'Custom Trip Title', 'wp-travel' ),
			'dates'                     => __( 'Dates', 'wp-travel' ),
			'default_pax'               => __( 'Default Pax', 'wp-travel' ),
			'display'                   => __( 'Display', 'wp-travel' ),
			'empty_results'             => self::empty_results_strings(),
			'enable_sale'               => __( 'Enable Sale', 'wp-travel' ),
			'enter_location'            => __( 'Enter Location', 'wp-travel' ),
			'fact'                      => __( 'Fact', 'wp-travel' ),
			'featured_book_now'         => __( 'Book Now', 'wp-travel' ), // Book Now at the featured section.
			'featured_trip_enquiry'     => __( 'Trip Enquiry', 'wp-travel' ), // Trip Enquiry at the featured section.
			'featured_trip_video'		=> __( 'Featured Video', 'wp-travel' ),	// Featured Video Heading at Trip Options
			'filter_by'                 => __( 'Filter By', 'wp-travel' ),
			'fixed_departure'           => __( 'Fixed Departure', 'wp-travel' ),
			'from'                      => __( 'From', 'wp-travel' ),
			'gallery_images'     		=> __( 'Images', 'wp-travel' ),	// Images Heading at Trip Options
			'global_partial_payout'     => __( 'Global Partial Payout', 'wp-travel' ),
			'global_trip_enquiry'       => __( 'Global Trip Enquiry Option', 'wp-travel' ),
			'global_trip_title'         => __( 'Global Trip Title', 'wp-travel' ),
			'group'                     => __( 'Group', 'wp-travel' ),
			'group_size'                => __( 'Group Size', 'wp-travel' ),
			'keyword'                   => __( 'Keyword', 'wp-travel' ),
			'latitude'                  => __( 'Latitude', 'wp-travel' ),
			'loading'                   => __( 'Loading..', 'wp-travel' ),
			'load_more'                 => __( 'Load more..', 'wp-travel' ),
			'location'                  => __( 'Location', 'wp-travel' ),
			'locations'                 => __( 'Locations', 'wp-travel' ),
			'longitude'                 => __( 'Longitude', 'wp-travel' ),
			'max_pax'                   => __( 'Max Pax.', 'wp-travel' ),
			'min_pax'                   => __( 'Min Pax.', 'wp-travel' ),
			'minimum_payout'            => __( 'Minimum Payout', 'wp-travel' ),
			'next'                      => __( 'Next', 'wp-travel' ),
			'notice_button_text'        => array( 'get_pro' => __( 'Get WP Travel Pro', 'wp-travel' ) ),
			'previous'                  => __( 'Previous', 'wp-travel' ),
			'prices'                    => __( 'Prices', 'wp-travel' ),
			'price_category'            => __( 'Price Category', 'wp-travel' ),
			'price_per'                 => __( 'Price Per', 'wp-travel' ),
			'person'                    => __( 'Person', 'wp-travel' ),
			'price'                     => __( 'Price', 'wp-travel' ),
			'price_range'               => __( 'Price Range', 'wp-travel' ),
			'pricing_name'              => __( 'Pricing Name', 'wp-travel' ),
			'highest_price'             => __( 'Show Highest Price', 'wp-travel' ),
			'highest_price_description' => __( 'This option will display the highest price.', 'wp-travel' ),
			'enable_pax_individual'     => __( 'Enable Pax Individually', 'wp-travel' ),
			'enable_pax_individual_description' => __( 'This option will enable pax limit for individual pricing.', 'wp-travel' ),
			'reviews'                   => __( 'Reviews', 'wp-travel' ),
			'sale_price'                => __( 'Sale Price', 'wp-travel' ),
			'search'                    => __( 'Search', 'wp-travel' ),
			'search_placeholder'        => __( 'Ex: Trekking', 'wp-travel' ),
			'select'                    => __( 'Select', 'wp-travel' ),
			'save'                      => __( 'Save', 'wp-travel' ),
			'off'                       => __( 'Off', 'wp-travel' ),
			'save_settings'             => __( 'Save Settings', 'wp-travel' ),
			'show'                      => __( 'Show', 'wp-travel' ),
			'system_information'        => __( 'System Information', 'wp-travel' ),
			'view_system_information'   => __( 'View system information', 'wp-travel' ),
			'general_setting'           => __( 'General Settings', 'wp-travel' ),
			'to'                        => __( 'To', 'wp-travel' ),
			'video_url'                 => __( 'Video URL', 'wp-travel' ),
			'trip_code'                 => __( 'Trip code', 'wp-travel' ),
			'trip_date'                 => __( 'Trip date', 'wp-travel' ),
			'trip_duration'             => __( 'Trip Duration', 'wp-travel' ),
			'trip_enquiry'              => __( 'Trip Enquiry', 'wp-travel' ),
			'enquiry'					=> __( apply_filters( 'wp_travel_trip_enquiry_label', 'Enquiry' ), 'wp-travel' ),
			'trip_name'                 => __( 'Trip Name', 'wp-travel' ),
			'trip_type'                 => __( 'Trip Type', 'wp-travel' ),
			'unit'                      => __( 'Unit', 'wp-travel' ),
			'use_global_payout'         => __( 'Use Global Payout', 'wp-travel' ),
			'use_global_tabs_layout'    => __( 'Use Global Tabs Layout', 'wp-travel' ),
			//for duration select option translate 
			'duration_select_label'		=> array(
				'hour'			=> __( 'Hour', 'wp-travel' ),
				'day'			=> __( 'Day', 'wp-travel' ),
				'night'			=> __( 'Night', 'wp-travel' ),
				'day_night'		=> __( 'Day and Night', 'wp-travel' ),
				'day_hour'		=> __( 'Day and Hour', 'wp-travel' ),
				'hour_minute'	=> __( 'Hour and Minute', 'wp-travel' ),
			),
			// Admin related data.
			'admin_tabs'                => self::admin_tabs_strings(),
			'notices'                   => self::admin_notices(),
			'messages'                  => array(
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
			'update'                    => __( 'Update', 'wp-travel' ),
			'upload'                    => __( 'Upload', 'wp-travel' ),
			'media_library'             => __( 'Media Library', 'wp-travel' ),
			'save_changes'              => __( 'Save Changes', 'wp-travel' ),
			'add'                       => __( '+ Add', 'wp-travel' ),
			'edit'                      => __( 'Edit', 'wp-travel' ),
			'remove'                    => __( '-Remove', 'wp-travel' ),
			'add_date'                  => __( '+ Add Date', 'wp-travel' ),
			'remove_date'               => __( '-Remove Date', 'wp-travel' ),
			'add_category'              => __( '+ Add Category', 'wp-travel' ),
			'remove_category'           => __( '-Remove Category', 'wp-travel' ),
			'add_extras'                => __( '+ Add Extras', 'wp-travel' ),
			'remove_extras'             => __( '-Remove Extras', 'wp-travel' ),
			'add_fact'                  => __( '+ Add Fact', 'wp-travel' ),
			'remove_fact'               => __( '-Remove Fact', 'wp-travel' ),
			'add_faq'                   => __( '+ Add Faq', 'wp-travel' ),
			'remove_faq'                => __( '-Remove Faq', 'wp-travel' ),
			'add_price'                 => __( '+ Add Price', 'wp-travel' ),
			'remove_price'              => __( '-Remove Price', 'wp-travel' ),
			'add_itinerary'             => __( '+ Add Itinerary', 'wp-travel' ),
			'remove_itinerary'          => __( '-Remove Itinerary', 'wp-travel' ),
			'date_label'                => __( 'Date Label', 'wp-travel' ),
			'select_pricing'            => __( 'Select pricing options', 'wp-travel' ),
			'select_all'                => __( 'Select All', 'wp-travel' ),
			'select_type'               => __( 'Select Type', 'wp-travel' ),
			'start_date'                => __( 'Start Date', 'wp-travel' ),
			'end_date'                  => __( 'End Date', 'wp-travel' ),
			'date_time'                 => __( 'Date & time', 'wp-travel' ),
			'enable_fixed_departure'    => __( 'Enable Fixed Departure', 'wp-travel' ),
			'nights'                    => __( 'Night(s)', 'wp-travel' ),
			'days'                      => __( 'Day(s)', 'wp-travel' ),
			'hour'                     	=> __( 'Hour(s)', 'wp-travel' ),
			'duration_start_date'       => __( 'Duration Start Date', 'wp-travel' ),
			'duration_end_date'         => __( 'Duration End Date', 'wp-travel' ),
			'minutes'                   => __( 'Minute(s)', 'wp-travel' ),
			'value'                     => __( 'Value', 'wp-travel' ),
			'faq_questions'             => __( 'FAQ Questions ?', 'wp-travel' ),
			'enter_question'            => __( 'Enter your question', 'wp-travel' ),
			'faq_answer'                => __( 'Your Answer', 'wp-travel' ),
			'trip_includes'             => __( 'Trip Includes', 'wp-travel' ),
			'trip_excludes'             => __( 'Trip Excludes', 'wp-travel' ),

			'itinerary'                 => __( 'Itinerary', 'wp-travel' ),
			'day_x'                     => __( 'Day X', 'wp-travel' ),
			'your_plan'                 => __( 'Your Plan', 'wp-travel' ),
			'trip_outline'              => __( 'Trip Outline', 'wp-travel' ),
			'overview'                  => __( 'Overview', 'wp-travel' ),
			'itinerary_label'           => __( 'Itinerary Label', 'wp-travel' ),
			'itinerary_title'           => __( 'Itinerary Title', 'wp-travel' ),
			'itinerary_date'            => __( 'Itinerary Date', 'wp-travel' ),
			'itinerary_time'            => __( 'Itinerary Time', 'wp-travel' ),
			'hours'                     => __( 'Hours', 'wp-travel' ),
			'minute'                    => __( 'Minute', 'wp-travel' ),
			'description'               => __( 'Description', 'wp-travel' ),
			'map'                       => __( 'Map', 'wp-travel' ),

			'help_text'                 => array(
				'date_pricing'       => __( 'Type Pricing option and enter', 'wp-travel' ),
				'enable_location'    => __( 'Enable/Disable latitude-longitude option', 'wp-travel' ),
				'use_global_payout'  => __( 'Note: In case of multiple cart items checkout, global payout will be used.', 'wp-travel' ),
				'show_highest_price' => __( 'This option will display the highest price..', 'wp-travel' ),
				'show_highest_price' => __( 'This option will display the highest price..', 'wp-travel' ),
			),
			'full_name'                 => __( 'Full Name', 'wp-travel' ),
			'enter_your_name'           => __( 'Enter your name', 'wp-travel' ),
			'email'                     => __( 'Email', 'wp-travel' ),
			'enter_your_email'          => __( 'Enter your email', 'wp-travel' ),
			'enquiry_message'           => __( apply_filters( 'wp_travel_enquiry_message_label', 'Enquiry Message' ), 'wp-travel' ),
			'enter_your_enquiry'        => __( 'Enter your enquiry...', 'wp-travel' ),
			'arrival_departure'			=> apply_filters( 'wp_travel_trip_duration_arrival_time', false ),
			'arrival_time'				=> __( apply_filters( 'wp_travel_arrival_time', 'Arrival Time' ), 'wp-travel' ),
			'departure_time'			=> __( apply_filters( 'wp_travel_departure_time', 'Departure Time' ), 'wp-travel' ),
			'conditional_payment_text'	=> __( 'Using the Conditional payment module, you can apply for conditional payment on the checkout page according to the billing address or the trip locations.', 'wp-travel' ),
			'single_archive'			=> self::wp_travel_single_archive_strings(),
			'set_cart_error'			=> __( 'You have already applied a coupon.', 'wp-travel' ),
			'set_coupon_empty'			=> __( 'Please enter your coupon code', 'wp-travel' ),
			'set_invalid_coupon_error'	=> __( 'Invalid Coupon code. Please re-enter your coupon code', 'wp-travel' ),
			'set_coupon_apply'			=> __( 'Coupon applied.', 'wp-travel' ),
			'set_enter_coupon_message'	=> __( 'Enter you coupon code', 'wp-travel' ),
			'set_coupon_btn'			=> __( 'Apply Coupon', 'wp-travel' ),
			'set_ideal_bank'			=> __( 'iDEAL Bank', 'wp-travel' ),
			'set_book_now_btn'			=> __( 'Book Now', 'wp-travel' ),
			'set_cart_updated'			=> __( 'Cart updated successfully.', 'wp-travel' ),
			'set_cart_updated_error'	=> __( "Cart failed to update due to server error.", "wp-travel" ),
			'set_cart_updated_server_responce' => __( "Cart failed to update due to server response error.", "wp-travel" ),
			'set_cart_server_error'		=> __( 'Cart failed to update due to some server error.', 'wp-travel' ),
			'set_close_cart'			=> __( 'Close Cart', 'wp-travel' ),
			'set_view_cart'				=> __( 'View Cart', 'wp-travel' ),
			'set_updated_cart_btn'		=> __( 'Update Cart', 'wp-travel' ),
			'set_cart_total_price'		=> __( 'Trip Price', 'wp-travel' ) ,
			'set_cart_discount'			=> __( 'Discount', 'wp-travel' ),
			'set_cart_tax'				=> __( 'Tax', 'wp-travel' ) ,
			'set_payment_price'			=> __( 'Total Trip Price', 'wp-travel' ),
			'set_cart_partial_payment'	=>  __( 'Partial Payment Price', 'wp-travel' ),
			'set_require_message'		=> __( ' is required', 'wp-travel' ),
			'set_require_empty'			=> __( 'Required field is empty', 'wp-travel' ),
			'set_go_back'				=> __( 'Go Back', 'wp-travel' ) ,
			'set_next_btn'				=> __( 'Next', 'wp-travel' ),
			'set_added_cart'			=> __( 'has been added to cart.', 'wp-travel' ),
			'set_gateway_select'		=> __( 'Plese select you payment gateway', 'wp-travel' ),
			'set_book_now'				=> __( "Book Now", 'wp-travel' ),
			'set_invalid_email'			=> __( 'Invalid Email', 'wp-travel' ),
			'set_load_traveler'			=> __( "Lead Traveler", 'wp-travel' ),
			'set_traveler'				=> __( 'Traveler ', 'wp-travel' ),
			'set_time_out'				=> __( '[X] Request Timeout!', 'wp-travel' ),
			'set_traveler_details'		=> __('Traveler Details', 'wp-travel' ),
			'set_booking_details'		=> __('Billing Details', 'wp-travel' ),
			'set_booking_with'			=> __( 'Booking / Payments', 'wp-travel' ),
			'set_booking_only'			=>  __( 'Booking', 'wp-travel' ),
			'set_bank_detail'			=> __( 'Bank Details', 'wp-travel'),
			'set_account_name'			=> __( 'Account Name', 'wp-travel'),
			'set_account_number'		=> __( 'Account Number', 'wp-travel'),
			'set_bank_name'				=> __( 'Bank Name', 'wp-travel'),
			'set_sort_code'				=> __( 'Sort Code', 'wp-travel'),
			'set_ibam'					=> __( 'IBAN', 'wp-travel'),
			'set_swift'					=> __( 'Swift', 'wp-travel'),
			'set_routing_number'		=> __( 'Routing Number', 'wp-travel'),
			'set_add_to_cart'			=> __('Add to Cart', 'wp-travel'),
			'trip_price_per'			=> apply_filters( 'wp_travel_trip_price_per', $price_per )
		);

		$localized_strings['price_per_labels'] = array(
			'group'  => $localized_strings['group'],
			'person' => self::booking_strings()['person'],
		);

		return apply_filters( 'wp_travel_strings', $localized_strings ); // @phpcs:ignore

	}

	/**
	 * Get all booking related strings.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
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
			'price_tax'                     => __( 'Tax', 'wp-travel' ),
			'select_pax'                    => __( 'Select Pax', 'wp-travel' ),
			'price'                         => __( 'Price', 'wp-travel' ),
			'arrival_date'                  => __( 'Arrival date', 'wp-travel' ),
			'departure_date'                => __( 'Departure date', 'wp-travel' ),
			'sold_out'                      => __( 'Sold Out', 'wp-travel' ),
			'select'                        => __( 'Select', 'wp-travel' ),
			'close'                         => __( 'Close', 'wp-travel' ),
			'book_now'                      => __( 'Book Now', 'wp-travel' ),
			'combined_pricing'              => __( 'Pricing', 'wp-travel' ), // Added for combined pricing label for categorized pricing @since 3.0.0.
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
			'payment_price_detail'			=> [
				'payment_detail'		=> __( 'Payment Details', 'wp-travel' ),
				'date'					=> __( 'Date', 'wp-travel' ),
				'payment_id'			=> __( 'Payment ID / Txn ID', 'wp-travel' ),
				'payment_methode'		=> __( 'Payment Method', 'wp-travel' ),
				'payment_amount'		=> __( 'Payment Amount', 'wp-travel' ),
			]
		);
	}

	/**
	 * Get all tabs related strings.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
	public static function admin_tabs_strings() {
		return array(
			'itinerary'         => __( 'Itinerary', 'wp-travel' ),
			'price_n_dates'     => __( 'Prices & Dates', 'wp-travel' ),
			'includes_excludes' => __( 'Includes/Excludes', 'wp-travel' ),
			'facts'             => __( 'Facts', 'wp-travel' ),
			'gallery'           => __( 'Gallery', 'wp-travel' ),
			'locations'         => __( 'Locations', 'wp-travel' ),
			'checkout'          => __( 'Checkout', 'wp-travel' ),
			'inventory_options' => __( 'Inventory Options', 'wp-travel' ),
			'faqs'              => __( 'FAQs', 'wp-travel' ),
			'downloads'         => __( 'Downloads', 'wp-travel' ),
			'misc'              => __( 'Misc', 'wp-travel' ),
			'tabs'              => __( 'Tabs', 'wp-travel' ),
			'guides'            => __( 'Guides', 'wp-travel' ),
			'checkout_field_editor'            => __( 'Checkout Field Editor', 'wp-travel' ),
		);
	}

	/**
	 * Get all alert strings.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
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

	/**
	 * Get all empty results strings.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
	public static function empty_results_strings() {
		return array(
			'activities' => __( 'No Activities', 'wp-travel' ),
			'add_extras' => __( 'Please add extras first', 'wp-travel' ),
			'category'   => __( 'No category found.', 'wp-travel' ),
			'dates'      => __( 'No dates found', 'wp-travel' ),
			'extras'     => __( 'No extras found.', 'wp-travel' ),
			'group_size' => __( 'No size limit', 'wp-travel' ),
			'itinerary'  => __( 'No Itineraries found.', 'wp-travel' ),
			'pricing'    => __( 'No pricing found.', 'wp-travel' ),
			'trip_type'  => __( 'No Trip Type', 'wp-travel' ),
		);
	}

	/**
	 * Get all admin notices strings.
	 *
	 * @since 4.6.4
	 *
	 * @return array
	 */
	public static function admin_notices() {
		return array(
			'checkout_option'    => array(
				'title'       => __( 'Need to add your checkout options?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your checkout options for all of your trips !', 'wp-travel' ),
			),
			'inventory_option'   => array(
				'title'       => __( 'Need to add your inventory options?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your inventory options in all of your trips !', 'wp-travel' ),
			),
			'downloads_option'   => array(
				'title'       => __( 'Need to add your downloads?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
			),
			'guide_option'   => array(
				'title'       => __( 'Need to add trip guides?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can add trip guides in all of your trips !', 'wp-travel' ),
			),
			'need_more_option'   => array(
				'title'       => __( 'Need More Options ?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel' ),
			),
			'need_extras_option' => array(
				'title'       => __( 'Need advance Trip Extras options?', 'wp-travel' ),
				'description' => '',
			),
			'global_faq_option'  => array(
				'title'       => __( 'Tired of updating repitative FAQs ?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can create and use Global FAQs in all of your trips !', 'wp-travel' ),
			),
			'featured_trip_video_option'   => array(
				'description' => __( 'Insert the video link to embed.', 'wp-travel' ),
			),
			'trip_code_option'   => array(
				'description' => __( 'Need Custom Trip Code? Check', 'wp-travel' ),
			),
			'map_option'         => array(
				'title'       => __( 'Need alternative maps ?', 'wp-travel' ),
				'description' => __( 'If you need alternative to current map then you can get free or pro maps for WP Travel.', 'wp-travel' ),
			),
			'map_key_option'     => array(
				'description' => __( "You can add 'Google Map API Key' in the %1\$ssettings%2\$s to use additional features.", 'wp-travel' ),
			),
			'global_tab_option'  => array(
				'title'       => __( 'Need Additional Tabs ?', 'wp-travel' ),
				'description' => __( 'By upgrading to Pro, you can get trip specific custom tabs addition options with customized content and sorting !', 'wp-travel' ),
			),
		);
	}
	/**
	 * Wp trave trip single archive page strings
	 * @since 6.9
	 */
	public static function wp_travel_single_archive_strings() {
		$strings = [
			'offer'		=> __( 'Offer', 'wp-travel' ),
			'view_gallery'					=> __( 'View Gallery', 'wp-travel' ),
			'keywords'						=> __( 'Keywords', 'wp-travel' ),

		];

		return $strings;
	}
}
