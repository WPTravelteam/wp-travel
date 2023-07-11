import { DEFAULT_BOOKING_STATE } from '../store/_Store'; // Note: This store content 2 stores one for trip data and another for Booking Data.

// Additional lib
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel.strings
}
const initialState = DEFAULT_BOOKING_STATE();

const ResetBooking = ( props ) => {
	// Component Props.
	const { tripData, updateBookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
    } = tripData;
    return <ErrorBoundary>
            <button className='wp-travel-reset-btn' onClick={ () => {
                updateBookingData( initialState );
                }}>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x={0} y={0} viewBox="0 0 36.9 41.7">
                    <path d="M35.8,17.1l-2.8,1c0.6,1.7,0.9,3.4,0.9,5.2c0,8.5-6.9,15.4-15.4,15.4C9.9,38.7,3,31.8,3,23.3C3,14.8,9.9,7.8,18.4,7.8
                    c1.3,0,2.6,0.2,3.9,0.5l-2.8,3l2.2,2L25,9.9l2.3-2.4L26,4.4L24.2,0l-2.8,1.1l1.8,4.3c-1.5-0.4-3.1-0.6-4.7-0.6
                    C8.3,4.8,0,13.1,0,23.3c0,10.2,8.3,18.4,18.4,18.4c10.2,0,18.4-8.3,18.4-18.4C36.9,21.2,36.5,19.1,35.8,17.1z">
                    </path>
                </svg>
                {__i18n.bookings.booking_tab_clear_all}
            </button>
    </ErrorBoundary>
}
export default ResetBooking;