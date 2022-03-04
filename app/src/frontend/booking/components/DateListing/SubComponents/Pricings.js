import { CheckboxControl } from '@wordpress/components';

const __i18n = {
	..._wp_travel.strings
}

// Additional lib
const _ = lodash;

const Pricings = ( props ) => {
    // Component Props.
	const { tripData, bookingData, updateBookingData, _nomineePricings, date } = props;
    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration:tripDuration
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById          = _.keyBy(_dates, d => d.id); // object structure along with date id.

    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' !== d.start_date && new Date( d.start_date )  > new Date() } )

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;
    return <>
		{ 'undefined' != typeof _nomineePricings && _nomineePricings.length > 0 && <>
			{ 
				_nomineePricings.map( (pricingId, pricingIndex) => {
					return <CheckboxControl
						key={pricingIndex}
						label={allPricings[pricingId].title}
						checked={ selectedPricingId == pricingId && selectedDateIds.includes( date.id ) }
						onChange={ ( value ) => {
							if ( value ) {
								updateBookingData({
									selectedPricingId:pricingId,
									selectedPricing:allPricings[pricingId].title,
									selectedDate: new Date( date.start_date + ' 00:00:00' ),
									selectedDateIds:[date.id]
								});
							} else {
								updateBookingData({
									selectedPricingId:null,
									selectedPricing:null,
									selectedDate: null,
									selectedDateIds:[],
									// Additional.
									nomineeTimes:[],
									selectedTime:null
								});
							}
						}}
					/>
				})
			}
		</>
		
		}
			
	</>
}
export default Pricings;