/**
 * It is important to set global var before any imports.
 * https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
 */
__webpack_public_path__ = _wp_travel.build_path;
import { useSelect, dispatch } from '@wordpress/data';
import { forwardRef, useEffect, render, lazy, Suspense } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { DEFAULT_BOOKING_STATE } from './store/_Store'; // Note: This store content 2 stores one for trip data and another for Booking Data.
import apiFetch from '@wordpress/api-fetch';
const __i18n = {
	..._wp_travel.strings
}

// Store Names.
const bookingStoreName = 'WPTravelFrontend/BookingData';

// Additional lib @todo need to implement path lib.
const _ = lodash;
import ErrorBoundary from './../../ErrorBoundry/ErrorBoundry';

// WP Travel Components.
import CustomBooking from './components/CustomBooking';
import ResetBooking from './components/ResetBooking';
import CalendarView from './components/CalendarView';
import DateListing from './components/DateListing';

const WPTravelBooking = ( props ) => {
	// Component Props.
	const {forceCalendarDisplay } = props;

	// All Trip Related Data fetched from store. (Not State)
    const tripListingType = 'undefined' !== typeof _wp_travel.trip_date_listing ? _wp_travel.trip_date_listing : 'calendar'; // dates | calendar
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
	const {
        pricing_type:pricingType,
        is_fixed_departure:isFixedDeparture
    } = tripData;
	// End of Trip related datas.

	// Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    console.log(bookingData);
    const { selectedDate } = bookingData;
    const { updateStore } = dispatch( bookingStoreName );
    const updateBookingData = ( data ) => {
		updateStore({ ...bookingData, ...data });
    }
    return <>
        <ErrorBoundary>
            {
                tripData && 'custom-booking' === pricingType && <CustomBooking {...tripData} /> ||
				<ErrorBoundary>
					<Suspense>
                        { ( ! forceCalendarDisplay || selectedDate ) &&
                            <div className="wp-travel-booking__header">
                                {<h3>{__i18n.booking_tab_content_label}</h3> }
                                { selectedDate &&  <ResetBooking { ...{ ...props, bookingData, updateBookingData, tripData } } /> } 
                            </div>
                        }
                        { isFixedDeparture && 'dates' === tripListingType && ! forceCalendarDisplay && 
                            <div className="wp-travel-booking__content-wrapper">
                                <DateListing  { ...{ ...props, bookingData, updateBookingData, tripData } } />
                            </div>
                            ||
                            <div className="wp-travel-booking__datepicker-wrapper">
                                <CalendarView { ...{ ...props, bookingData, updateBookingData, tripData } } />
                            </div>
                        }
					</Suspense>
                </ErrorBoundary>
            }
        </ErrorBoundary>
    </>
}
let bookingWidgetElementId = _wp_travel.itinerary_v2 ? 'wti__booking' : 'booking';
if (document.getElementById(bookingWidgetElementId)) {
    const tooltipText = __i18n.bookings.date_select_to_view_options;
    render(<WPTravelBooking forceCalendarDisplay={false} calendarInline={false} showTooltip={true} tooltipText={tooltipText} />, document.getElementById(bookingWidgetElementId));
}

// For Frontend Block.
let blockId = 'wptravel-block-trip-calendar';
if (document.getElementById(blockId)) {
	let elem = document.getElementById(blockId);
	const props = Object.assign( {}, elem.dataset );
	const inline = undefined != typeof props.inline && props.inline;
	const tooltip = undefined != typeof props.tooltip && props.tooltip;
	const tooltipText = undefined != typeof props.tooltip_text && props.tooltip_text;
	render(<WPTravelBooking forceCalendarDisplay={true} calendarInline={inline} showTooltip={tooltip} tooltipText={tooltipText} />, elem );
}
// @todo calendar button. in trip duration