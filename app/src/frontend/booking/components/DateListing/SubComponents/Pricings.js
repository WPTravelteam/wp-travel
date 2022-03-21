import { CheckboxControl } from '@wordpress/components';

const __i18n = {
	..._wp_travel.strings
}

// Additional lib
const _ = lodash;

// WP Travel Functions.
import Loader from '../../../../../GlobalComponents/Loader';

const Pricings = ( props ) => {
    // Component Props.
	const { tripData, bookingData, updateBookingData, _nomineePricings, date, recurrindDate } = props; // where date is row and recurrindDate is the date to select in recurring
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

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;
	let sd = moment(moment(selectedDate).format('YYYY-MM-DD')).unix();
	let rd = moment(moment(recurrindDate).format('YYYY-MM-DD')).unix();
	return <>
		{ isLoading && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) ) && <Loader /> }
		{ 'undefined' != typeof _nomineePricings && _nomineePricings.length > 0 && <>
			{ 
				_nomineePricings.map( (pricingId, pricingIndex) => {
					return <CheckboxControl
						key={pricingIndex}
						label={allPricings[pricingId].title}
						checked={ selectedPricingId == pricingId && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) ) && ! isLoading}
						onChange={ ( value ) => {
							if ( value ) {
								let newSelectedDate = new Date( date.start_date + ' 00:00:00' ); // Non Recurring.
								if ( 'undefined' !== typeof recurrindDate ) {
									newSelectedDate = recurrindDate; // Recurring
								}
								let _selectedDateIds   = [date.id];
								let _nomineePricingIds = _selectedDateIds.map( id => datesById[id].pricing_ids.split(',').map( id => id.trim() ) )
								_nomineePricingIds     = _.chain( _nomineePricingIds ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' )

								updateBookingData({
									selectedPricingId:pricingId,
									selectedPricing:allPricings[pricingId].title,
									selectedDate: newSelectedDate,
									selectedDateIds:_selectedDateIds,
									nomineePricingIds:_nomineePricingIds,
									isLoading:true,
									// pricingUnavailable:false
								});
							} else {
								updateBookingData({
									selectedPricingId:null,
									selectedPricing:null,
									selectedDate: null,
									selectedDateIds:[],
									// Additional.
									nomineeTimes:[],
									selectedTime:null,
									isLoading:true,
									// pricingUnavailable:false
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