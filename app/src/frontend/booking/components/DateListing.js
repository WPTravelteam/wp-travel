import { forwardRef, useEffect, useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { Disabled } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const _ = lodash;
import moment from 'moment';
import RDP_Locale from '../_Locale'
import DatePicker, {registerLocale} from "react-datepicker";
registerLocale( "DPLocale", RDP_Locale() );
import generateRRule from "../_GenerateRRule";

// WP Travel Functions.
import { objectSum } from '../_wptravelFunctions';
import { filteredTripDates } from '../_FilteredDates'; // Filter available dates in calendar.

const DateListing = ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration:tripDuration
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById          = _.keyBy(_dates, d => d.id)
    const duration           = tripDuration.days && parseInt( tripDuration.days ) || 1;
	const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;

	
    return <ErrorBoundary>
            <>
            Date listing
            </>
    </ErrorBoundary>
}
export default DateListing;