import { CheckboxControl, Disabled } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

const __i18n = {
	..._wp_travel.strings
}
// WP Travel Functions.
import { objectSum } from '../../_wptravelFunctions';

// Additional lib
const _ = lodash;

// WP Travel Components.
import Pricings from './SubComponents/Pricings';
import PaxSelector from './SubComponents/PaxSelector';
import TripExtras from './SubComponents/TripExtras';
import TripTimes from './SubComponents/TripTimes';

const NonRecurringDates = ( props ) => {
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
    const datesById          = _.keyBy(_dates, d => d.id); // object structure along with date id.

    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' !== d.start_date && new Date( d.start_date )  > new Date() } )
	nonRecurringDates     = _.chain( nonRecurringDates ).sortBy( d => moment( d.start_date).unix() ).value() || []; // Sort by date.

    const duration           = tripDuration.days && parseInt( tripDuration.days ) || 1;
	const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;

    return <>
    {
		nonRecurringDates.length > 0 && <tbody className="tbody-table">
		{ nonRecurringDates.map( ( date, index ) => {
			let _nomineePricings = date.pricing_ids.split(',').map( id => id.trim() );
			_nomineePricings = _.chain( _nomineePricings ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' );
			if ( ! _nomineePricings.length ) {
				return <Fragment key={index}></Fragment>
			}
			return <tr key={index}>
				<td data-label={__i18n.bookings.pricings_list_label}>
					{/* _nomineePricings not updated in store/state because there are multiple _nomineePricings as per date so just a variable. */}
					<Pricings { ...{ ...props, _nomineePricings, date } }  />
				</td>
				<td data-label={__i18n.bookings.person}>
					<div className ="person-box">
						{ ! pricingUnavailable && <>
							{ ( ! selectedPricingId || ( nomineeTimes.length > 1 && ! selectedTime ) || ! selectedDateIds.includes( date.id ) ) && <Disabled><PaxSelector { ...{ ...props, _nomineePricings, date } } /></Disabled> || <PaxSelector { ...{ ...props, _nomineePricings, date } } /> }
							{ 
								selectedPricingId && 
								selectedDateIds.includes( date.id ) && 
								_.size( allPricings[ selectedPricingId ].trip_extras ) > 0 && 
								objectSum( paxCounts ) > 0 && 
								( ! nomineeTimes.length || ( nomineeTimes.length > 0 && selectedTime ) ) &&
								<> <TripExtras { ...{ ...props, _nomineePricings, date } } /> </> 
							}
							</> 
						}			
					</div>
				</td>
				<td data-label={__i18n.bookings.date}>
					<div className = "date-box">
						<div className="date-time-wrapper">
							<span className="start-date"><span>{__i18n.bookings.start_date}: </span>{moment(date.start_date).format(_wp_travel.date_format_moment)}</span>
							{date.end_date && '0000-00-00' != date.end_date && <span className="end-date"><span>{__i18n.bookings.end_date}: </span>{moment(date.end_date).format(_wp_travel.date_format_moment)}</span> }
						</div>
						{selectedDateIds.includes( date.id ) &&
							<TripTimes { ...{ ...props, _nomineePricings, date } }  />
						}
					</div>
				</td>
			</tr>
        } ) }
		</tbody>
    }
    </>
}
export default NonRecurringDates;