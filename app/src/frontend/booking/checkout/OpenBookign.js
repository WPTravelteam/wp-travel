const __i18n = {
	..._wp_travel.strings
}
__webpack_public_path__ = _wp_travel.build_path;
import { useSelect, dispatch } from '@wordpress/data';
import ErrorBoundary from "../../../ErrorBoundry/ErrorBoundry";
import {  Suspense } from '@wordpress/element';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import ResetBooking from "../components/ResetBooking";
import DateListing from "../components/DateListing";
import CalendarView from "../components/CalendarView";
// import WpTravelBookNow from '../components/WpTravelBookNow';
import CustomBooking from '../components/CustomBooking';
import NextTravelerBtn from './NextTravelerBtn'
// import ProgressBary from './ProgressBary';
export default ( props ) => {
	// Component Props.
	const { forceCalendarDisplay } = props;
    console.log( 'sdfdf', _wp_travel)
	// All Trip Related Data(Not State)
    const tripListingType = 'undefined' !== typeof _wp_travel.trip_date_listing ? _wp_travel.trip_date_listing : 'calendar'; // dates | calendar
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
	const {
        pricing_type:pricingType,
        is_fixed_departure:isFixedDeparture
    } = tripData;
	// End of Trip related datas.

	// Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    console.log( 'tttt', bookingData );
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

                        {/* progress bar */}
                        {/* <ProgressBary statusText={`Progress: this is the starting`} value={5} max={100} /> */}

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
                            <CalendarView { ...{ ...props, bookingData, updateBookingData, tripData } } />
                        }
                        {/* Book Now Button at bottom */}
                        
                        <NextTravelerBtn { ...{ ...props, bookingData, updateBookingData, tripData } } />
					</Suspense>
                </ErrorBoundary>
            }
        </ErrorBoundary>
    </>
}