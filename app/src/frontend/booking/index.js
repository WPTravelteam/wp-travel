/**
 * It is important to set global var before any imports.
 * https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
 */
__webpack_public_path__ = _wp_travel.build_path;
import { useSelect, dispatch } from '@wordpress/data';
import { forwardRef, useEffect, render, lazy, Suspense } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { DEFAULT_BOOKING_STATE } from './_Store'; // Note: This store content 2 stores one for trip data and another for Booking Data.
import apiFetch from '@wordpress/api-fetch';

const __i18n = {
	..._wp_travel.strings
}

// Store Names.
// const tripStoreName    = 'WPTravelFrontend/TripData';
const bookingStoreName = 'WPTravelFrontend/BookingData';

// Additional lib
const _ = lodash;
import RDP_Locale from './_Locale'
import DatePicker, {registerLocale} from "react-datepicker";
registerLocale( "DPLocale", RDP_Locale() );
import moment from 'moment';
import RRule from "rrule";
import ErrorBoundary from './../../ErrorBoundry/ErrorBoundry';

// Custom Components.
import CustomBooking from './CustomBooking';

const WPTravelBooking = ( props ) => {

	// Component Props.
	const {forceCalendarDisplay, calendarInline, showTooltip, tooltipText } = props;

	// All Trip Related Data fetched from store. (Not State)
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
	const {
		pricings,
        pricing_type:pricingType,
        is_fixed_departure:isFixedDeparture
    } = tripData;
	// End of Trip related datas.

	// Booking Data/state.
    const bookingStore  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { selectedDate, selectedTime } = bookingStore;
    const { updateBooking } = dispatch( bookingStoreName );

    return <>
        <ErrorBoundary>
            {
                tripData && 'custom-booking' === pricingType && <CustomBooking {...tripData} /> ||
				<ErrorBoundary>
					<Suspense>
                        <div className="wp-travel-booking__header">
                            {<h3>{__i18n.booking_tab_content_label}</h3> }
                            { selectedDate && 
                                <button onClick={ () => {
                                        let _initialState = Object.assign(initialState)
                                        if ( ! isFixedDeparture )
                                            delete _initialState.nomineePricings
                                        updateState(_initialState)
                                    }}>
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x={0} y={0} viewBox="0 0 36.9 41.7">
                                    <path d="M35.8,17.1l-2.8,1c0.6,1.7,0.9,3.4,0.9,5.2c0,8.5-6.9,15.4-15.4,15.4C9.9,38.7,3,31.8,3,23.3C3,14.8,9.9,7.8,18.4,7.8
                                    c1.3,0,2.6,0.2,3.9,0.5l-2.8,3l2.2,2L25,9.9l2.3-2.4L26,4.4L24.2,0l-2.8,1.1l1.8,4.3c-1.5-0.4-3.1-0.6-4.7-0.6
                                    C8.3,4.8,0,13.1,0,23.3c0,10.2,8.3,18.4,18.4,18.4c10.2,0,18.4-8.3,18.4-18.4C36.9,21.2,36.5,19.1,35.8,17.1z">
                                    </path>
                                </svg>
                                {__i18n.bookings.booking_tab_clear_all}</button>
                            }

                        </div>
                    
                            {
                                isFixedDeparture && 'dates' === tripDateListing && ! forceCalendarDisplay && 
                                    <div className="wp-travel-booking__content-wrapper">
                                        <DatesListing {...{ dates: datesById, isTourDate, getPricingsByDate, allData, onFixedDeparturePricingSelect:handleFixedDeparturePricingSelect, componentData, getPricingTripTimes:getPricingTripTimes }} />
                                    </div>
                                ||
                                <div className="wp-travel-booking__datepicker-wrapper">
									{ calendarInline ? <DatePicker inline  /> : <DatePicker  /> }
                                    {!selectedTime && showTooltip && <p>{tooltipText} </p> || null}
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
