const changeLog = [
	{
		version: "6.6.0",
		log: <p>
				Release Date: 26th April 2023
				<br/><br/>
				Fixes: 
				<br/>
				* Fixed N/A in payment mode while the partial payment is disabled.
				<br/><br/>
				Enhancement:
				<br/>
				* Sending an email to the client when Payment Info is changed to Paid by the admin manually.
				<br/>
				* Added option to add hourly Trips in case of Trip duration.
				<br/>
				* For more detail, please refer to our [release note]( https://wptravel.io/wp-travel-plugin-version-6-6-0-release-note/ ).
				<br/>
			</p>
	},
	{
		version: "6.5.0",
		log: <p>
				Release Date: 12th April 2023
				<br/><br/>
				Fixes: 
				<br/>
				* Fixed when changing Payment Status to paid, Payment Mode status will also be changed to full.
				<br/>
				* Fixed calendar layout issue in backend trip edit dashboard.
				<br/><br/>
				Enhancement:
				<br/>
				* Added hook `[wp_travel_email_itinerary_pdf_attachment]` to attach itinerary PDF in the booking email.
				<br/>
				* For more detail, please refer to our [release note]( https://wptravel.io/wp-travel-plugin-version-6-5-0-release-note/ ).
				<br/>
			</p>
	},
	{
		version: "6.4.1",
		log: <p>
				Release Date: 3rd April 2023
				<br/><br/>
				Fixes: 
				<br/>
				* Fixed js issue in the gallery backend.
				<br/>
				* Fixed thank you page not set while activate WPML plugin.
				<br/>
				* Fixed custom filter label not translate while translating using WPML.
				<br/><br/>
				others:
				<br/>
				* Sets the WordPress tested up to version to 6.2.
				<br/>
			</p>
	},
	{
		version: "6.4.0",
		log: <p>
				Release Date: 30th March 2023
				<br/><br/>
				Enhancement: 
				<br/>
				* Implemented new design and interface of the Settings page.
				<br/>
				* Added compatibility with WPML plugin for translations.
				<br/>
				* For more detail, please refer to our [release note]( https://wptravel.io/wp-travel-plugin-version-6-4-0-release-note/ ).
				<br/>
			</p>
	},
	{
		version: "6.3.0",
		log: <p>
				Release Date: 1st March 2023
				<br/><br/>
				Tweaks:
				<br/>
				* Added hook 'wp_travel_strings'.
				<br/><br/>
				Fixes:
				<br/>
				* For additional compatible bug fixes with WP Travel Pro please see this [changelog]( https://wptravel.io/changelog-wp-travel-pro/ ).
				<br/>
			</p>
	},
	{
		version: "6.2.0",
		log: <p>
				Release Date: 13th February 2023
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Trip placeholder image being cropped.
				<br/>
				* Fixed filter by option not being clickable in small screen size.
				<br />
				* Fixed shortcode `[WP_TRAVEL_ITINERARIES limit=50 type='itinerary_types' slug="term-slug" ]` not working.
				<br/><br/>
				Enhancement:
				<br/>
				* Added option to disable rating star option for admin.
			</p>
	},
	{
		version: "6.1.1",
		log: <p>
				Release Date:  24th January 2023
				<br/><br/>
				Fixes: 
				<br/>
				* Fixed slow performance in admin Dashboard with latest version of WP Travel.
				<br/>
				* Fixed media upload issue.
			</p>
	},
	{
		version: "6.1.0",
		log: <p>
				Release Date:  19th January 2023
				<br/><br/>
				Tweaks:
				<br/>
				* Hook added to rename Tax.
				<br/><br/>
				Enhancement:
				<br/>
				* Added  WP Travel Initial Setup step to make admin easier to setup site.
			</p>
	},
	{
		version: "6.0.1",
		log: <p>
				Release Date:  5th January 2023
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Redirect on Setup Page while Activate Plugins.
			</p>
	},
	{
		version: "6.0.0",
		log: <p>
				Release Date: 4th January 2023
				<br/><br/>
				Enhancement:
				<br/>
				* Provides compatibility for the Travel Guide feature in WP Travel Pro.<br/>
                * Provides compatibility for showing high prices on trips.improving in TTFB speed.
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Ascending/Descending issue while using oderby trip_date option.<br/>
                * For more detail, please refer to our [release note](https://wptravel.io/wp-travel-plugin-version-6-0-0-release-note/)
			</p>
	},
	{
		version: "5.3.9",
		log: <p>
				Release Date: 26th December 2022
				<br/><br/>
				Enhancement:<br/>
				* Added Routing number field in Bank Deposit payment system.<br/>
                * Added payment Method and Payment status message in Booking success message while using  WP Travel Free only.<br/>
                * Modified the code so that Pax can be renamed through function code.
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Cutoff Time issue in Date Listing View.
			</p>
	},
	{
		version: "5.3.8",
		log: <p>
				Release Date: 5th December 2022
				<br/><br/>
				Enhancement:<br/>
				* Added Shortcode `[WP_TRAVEL_ITINERARY_FILTER]` or `[wp_travel_itinerary_filter]` to display Itinerary Filter.<br/>
                * Added Shortcode `[WP_TRAVEL_TRIP_CATEGORY_ITEMS taxonomy='itinerary_types' child='yes']` to display only the child Trip Type.<br/>
                * Added Shortcode `[WP_TRAVEL_TRIP_CATEGORY_ITEMS taxonomy='itinerary_types' parent='yes']` to display only the parent Trip Type.
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Trip extras not being saved.<br/>
                * Fixed issue in Departure Date while adding multiple date.<br/>
                * Fixed View System Information text made translation ready.
​
			</p>
	},
	{
		version: "5.3.7",
		log: <p>
				Release Date: 9th November 2022
​
				<br/><br/>
				Fixes:
				<br/>
				* Fixed CSS layout issue on the Single Trip page while using a Shortcode.
​
			</p>
	},
	{
		version: "5.3.6",
		log: <p>
				Release Date: 9th November 2022
				<br/><br/>
				Enhancement:
				* Added Shortcode `[WP_TRAVEL_TRIP_CATEGORY_ITEMS child='yes']` to display only the child destination.<br/>
                * Added Shortcode `[WP_TRAVEL_TRIP_CATEGORY_ITEMS parent='yes']` to display only the parent destination.
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Feature image not deleted when deleting the image from the backend.<br/>
                * Fixed Shortcodes not working in Trip Outline Tab.<br/>
                * Fixed double booking created with the same detail while clicking Book and Pay button twice in the checkout page.
			</p>
	},
	{
		version: "5.3.5",
		log: <p>
				Release Date: 18th October 2022
				<br/><br/>
				Enhancement:<br/>
				* Added Shortcode `[WP_TRAVEL_ITINERARIES order="asc"]`to display the Trips in Ascending order.<br/>
                * Added Shortcode `[WP_TRAVEL_ITINERARIES order="desc"]` to display the Trips in Descending order.
				<br/><br/>
				Fixes:
				<br/>
				* Removed Dots (.) displaying in mail Footer section.<br/>
                * Fixed Empty data not saved in Overview , Itinerary (Trip Outline), and Include/Excludes Tabs.<br/>
                * Fixed 'booking_departure_date' email tag not working.<br/>
                * Fixed Trip duration in search filter widget/Shortcode not working.
				<br/><br/>
				Layout Fixes:<br/>
                * Fixed save % tag  issue in responsive.
			</p>
	},
	{
		version: "5.3.4",
		log: <p>
				Release Date: 27th September 2022
				<br/><br/>
				Enhancement:
				<br/>
                * Added Shortcode `[wptravel_trip_type]` to display the Trip Type of trip. <br/>
                * Added Shortcode `[wptravel_activities]` to display the Activity of trip. <br/>
                * Added Shortcode `[wptravel_group_size]` to display the Group Size of trip. <br/>
                * Added Shortcode `[wptravel_reviews]` to display the Review of trip. <br/>
                * Added a hooks for remove `Trip Type` , `Activity` , `Group Size` and `Review` in a single trip page.
			</p>
	},
	{
		version: "5.3.3",
		log: <p>
				Release Date: 27th September 2022 
​
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Price not deleted when deleting pricing category
			</p>
	},
	{
		version: "5.3.2",
		log: <p>
				Release Date: 1st September 2022
				<br/><br/>
				Fixes:
				<br/>
				* Fixed Trip name and trip code not displaying in dashboard while booking directly<br/>
                * Fixed Payment detail not showing in booking while paid through Bank deposit<br/>
                * Fixed save % tag not displayed in List view in v1 layout<br/>
                * Fixed duplicate price issue when publishing the trip directly<br/>
                * Fixed general issue in trip facts<br/>
                * Fixed inventory issue in multiple checkout mode <br/>
                * Fixed magnific popup issue for payment receipt 
			</p>
	},
	{
		version: "5.3.1",
		log: <p>
				Release Date:  9th August 2022
				<br/><br/>
				Enhancement:<br/>
				* Added all trips booking list for admin in WP Travel User Dashboard.<br/>
                * Added all payment information under Payments tabs in WP Travel User Dashboard.
				<br/><br/>
				Fixes:
				<br/>
				* Fixed WPML Compatibility with WP Travel Checkout Page. Now Mini cart section edit, and remove trip are working along with all payment methods.<br/>
                * Fixed Trip Enquiry showing an alert message.<br/>
                * Fixed Trip Enquiry data not showing on admin enquiry detail page.<br/>
                * Fixed WP Travel User Dashboard not showing booking when `enable registration` on booking is `enabled`.<br/>
                * Fixed Voucher Submit from WP Travel User Dashboard not submitting the voucher issue. 
				<br/><br/>
				Tweaks:
               * Hooks added `wptravel_send_booking_email_to_client`.<br/>
               * Hooks modified `wp_travel_payment_email_tags` added new `booking id` param in the hook.
			</p>
	},
	{
		version: "5.3.0",
		log: <p>
				Release Date:  28th July 2022
				<br/><br/>
				Enhancement:<br/>
				* Added Shortcode `WP_TRAVEL_TRIP_CATEGORY_ITEMS` to display the trips under selected terms.<br/><br/>
				Fixes:<br/>
				* Fixed License tab not working in case of multisite network activate.
				<br/><br/>
				Layout Fixes:<br/>
               * Fixed Trip archive page pagination style in grid view.
			</p>
	},
	{
		version: "5.2.9",
		log: <p>
				Release Date: 21st July 2022
				<br/><br/>
				Deprecated:<br/>
				* Functions `wptravel_booking_default_princing_list_content` and `wptravel_booking_fixed_departure_list_content` have been deprecated.<br/><br/>
				Layout Fixes:<br/>
				* Archive and single trip page wishlist icon CSS removed from WP Travel.<br/>
                * Wishlist icon fixed when using shortcodes.<br/>
                * Multiple currency drop-down in navbar layout fixed.
				<br/><br/>
				Tweaks:<br/>
               * Removed Canonical page URL like `view_mode=grid` in trip archive page.<br/>
               * Added DOM event before and after adding the trip in a cart and also removing trip from a  cart.<br/>
               * Code optimized and cleanup.
			</p>
	},
	{
		version: "5.2.8",
		log: <p>
				Release Date: 12th July 2022
				<br/><br/>
				Fixes:<br/>
				* Fixed dropdown date display layout issue in WP Travel pages and sections.
			</p>
	},
	{
		version: "5.2.7",
		log: <p>
				Release Date:  7th July 2022
				<br/><br/>
				Tweaks:<br/>
                * Hide Nights text if there is no nights.<br/>
                * Added trigger event `selectedTripDate` on calendar date click.<br/>
                * Display multiple fixed departure dates.<br/><br/>
				Fixes:
				<br/>
				* Fixed Sort Pricing and display price accordingly.<br/>
                * Fixed 100% coupon code with booking status booked.
			</p>
	},
	{
		version: "5.2.6",
		log: <p>
				Release Date: 28th June 2022
				<br/><br/>
				Fixes:
				<br/>
				* Fixed not displaying menu icon for WP Travel.
				<br/><br/>
			</p>
	},
	{
		version: "5.2.5",
		log: <p>
				Release Date: 21st June 2022
				<br/><br/>
				Fixes:
				<br/>
				* Fixed max pax can be selectable more than max pax in case of inventory disabled.
				<br/><br/>
				Layout Fixes:<br/>
				* Fixed Trip single page mobile tab heading displaying in desktop view.
			</p>
	},
	{
		version: "5.2.4",
		log: <p>
				Release Date: 14th June, 2022
				<br/><br/>
				Tweaks:
				<br/>
				* WP Travel dates insert data for new and update for existing dates on saving instead of removing all and inserting again.<br/>
                * JS script optimization to reduce zip size and WP Travel pages size.
				<br/><br/>
				Fixes:<br/>
				* WP Travel review schema fixes to support it with rich result test.<br/>
                * Fixed WP Travel trip date showing same date even after update while using new filter hook.
				<br/><br/>
				Layout Fixes:
				<br/>
                * Fixed Archive page wishlist icon in old and new layout.<br/>
                * Fixed Single Trip Page book now button with custom link layout.<br/>
                * Fixed Single Trip page booking tab select time layout.<br/>
                * Fixed Single Trip page booking tab calendar disabled date layout.
​
			</p>
	},
	{
		version: "5.2.3",
		log: <p>
				Release Date: 25th May, 2022
				<br/><br/>
				Tweaks:
				<br/>
				* Changed Gallery image size to `wp_travel_thumbnail` from `thumbnail`.<br/>
                * Updated Required WordPress version to `5.9` from `5.4.1` in respective file.<br/>
                * Sorted trip types options as per trip title in WP Travel Search Form.<br/>
                * Added filter `wptravel_trip_dates` to modify trip dates.<br/>
                * Added Option to rename Days and Nights in single trip page.
				<br/><br/>
				Fixes:<br/>
				* Undefined wp_travel on the front page in case of WP Travel Pro is activated.<br/>
                * Fixed Displaying itinerary date under trip outline, even date is deleted.<br/>
                * Fixed past date also been able to select when selecting future date for date field type in the field editor option.<br/>
                * Fixed unable to select a trip time in case of no trip extras.<br/>
                * Fixed appearance of same Trip code when cloning trip.<br/>
                * Fixed issue regarding restoration of previous FAQs in case of deletion of FAQ and addition of same question as deleted FAQ.<br/>
                * Fixed issue regarding booking when pax is zero while editing through minicart. 
				<br/><br/>
				Layout Fixes:
				<br/>
                * Fixed issue related to Wishlist icon on Archive Page in list view, grid view and sidebar widget section.<br/>
                * Fixed gallery section layout in Single Trip page.<br/>
                * For more detail, please refer to our [release note](https://wptravel.io/wp-travel-plugin--version-5-2-3-release-note/)
			</p>
	},
	{
		version: "5.2.2",
		log: <p>
				Release Date: 27th April, 2022
				<br/><br/>
				Tweaks:
				* Renamed `Addons settings` to `Modules settings` and added all modules enable/disable options.
				<br/><br/>
				Fixes:<br/>
				* Fixed conflict with wp rocket on first setup.<br/>
                * Fixed Loading issue on trip duration while selecting date 2nd time.<br/>
                * Fixed Trip extras not being displayed in case of calendar view with trip duration.<br/>
                * Fixed Invalid post type in enquiry shortcode form.<br/>
                * Fixed Warning: array_unique() expects parameter 1 to be array in dashboard and booking  Page of user dashboard.<br/>
                * Fixed Inventory not working for booking only trips in case of all payment addons disabled.<br/>
                * Fixed issue related to recurring feature being available if pro is not activated.<br/>
                * Fixed issue related to displaying booking details section in dashboard page when logging in using customer login credentials.
				<br/><br/>
				Layout Fixes:
				<br/>
                * Fixed Single Trip page trip tabs section gallery issue in mobile screen.<br/>
                * Fixed Trip Search responsive in Elementor.<br/>
                * Fixed Single Trip Page enquiry popup form checkbox unclickable issue.<br/>
                * For more detail, please refer to our [release note](https://wptravel.io/wp-travel-plugin-version-5-2-2-release-note/)
			</p>
	},
	{
		version: "5.2.1",
		log: <p>
				Release Date: 30th March, 2022
​
				<br/><br/>
				Fixes:
				<br/>
				* Fixed pricing loading issue in case of trip duration.<br/>
                * Fixed search filter not working while enabling load combined scripts.
				<br/><br/>
				Layout Fixes:<br/>
				* Fixed minor layout issue with trip time in calendar view.<br/>
                * Fixed Pax selector not visible in mobile version.<br/>
                * Fixed loader layout in mobile version.
			</p>
	},
	{
		version: "5.2.0",
		log: <p>
				Release Date: 29th March, 2022
				<br/><br/>
				Fixes:
				<br/>
				* Display sold out notice in calendar if one date has been sold out and other dates has been selected.<br/>
                * Recurring dates not displaying if trip has multiple dates one with recurring and another with non recurring.<br/>
                * Amount conversion not working in checkout page while editing cart with multiple currency.<br/>
                * Fixed trip time displaying even when pro is disabled.<br/>
                * Fixed total pricing in mini cart when updating trip.<br/>
                * Fixed sidebar not displaying in the wp travel search result page.<br/>
                * Fixed showing multiple booking with same booking id while booking being logged in  through dashboard.
				<br/><br/>
				Enhancement:<br/>
				* Resolved site load speed issue with multiple currency. Reduced server response time by improving in TTFB speed.
			</p>
	}
];

export default changeLog;