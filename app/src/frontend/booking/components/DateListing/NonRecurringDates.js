import { CheckboxControl, Disabled } from '@wordpress/components';
import { Fragment, useState, useEffect } from '@wordpress/element';

const __i18n = {
	..._wp_travel.strings
}

/**
 * it fixe olny start date giving
 * 
 * @since 5.3.9
 */
const datePerPage = 1
/**
 * in thise file use cuttOffTime
 * 
 * @since 5.3.9
 */
import { IsTourDates } from '../../_IsTourDate'; // Filter available dates in calendar.
import { RRule, RRuleSet } from "rrule";

// WP Travel Functions.
import { objectSum } from '../../_wptravelFunctions';

// Additional lib
// const _ = lodash;
import _ from 'lodash';

// WP Travel Components.
import Pricings from './SubComponents/Pricings';
import PaxSelector from './SubComponents/PaxSelector';
import TripExtras from './SubComponents/TripExtras';
import TripTimes from './SubComponents/TripTimes';
import InventoryNotice, { Notice } from '../../_InventoryNotice';

/**
 * git trip data as props like date, price id and use IsTourDate component to check cuttOffTime of starting one by one
 * 
 * @param  props like date, price id and trip data etc
 * @returns price list
 * @since 5.3.9
 */
const NonRecorringRepeater = ( props ) => {
    const { date, _nomineePricings } = props;

    const [dates, setRecurringDates] = useState([]); // New dates will push here when clicking load more.
    const [activeRecurringDates, setActiveRecurringDates] = useState([]); // curren page dates.
    const [rruleArgs, setRRuleArgs] = useState(null)

    useEffect( () => {
        if ( ! rruleArgs ) {
            let aaa = generateRRUleArgs(date);
            if (Object.keys(aaa).length > 0) {
                setRRuleArgs(aaa)
			}
        }
    }, [date]);

    useEffect(() => {
        if (rruleArgs) {
            let _dates = generateRRule(rruleArgs);
            setRecurringDates(_dates)
            setActiveRecurringDates(_dates)
        }
    }, [rruleArgs]);
    
    

    const generateRRule = rruleArgs => {
        const rruleSet = new RRuleSet();
        rruleSet.rrule(
            new RRule(rruleArgs)
        );
        return rruleSet.all();
    }

	/**
	 * 
	 * @param {*} date 
	 * @param {*} showAll 
	 * @returns Wed Dec 21 2022 05:45:00 GMT+0545 (Nepal Time) 
	 * @since 5.3.9
	 */
    const generateRRUleArgs = ( date, showAll ) => {
        let startDate = date.start_date && new Date( date.start_date + ' 00:00:00' ) || new Date();
        let nowDate   = new Date();
        nowDate.setHours(0, 0, 0, 0);

        let rruleStartDate = nowDate < startDate ? startDate : nowDate;

        let curretYear = rruleStartDate.getFullYear();
        let currentDate = rruleStartDate.getDate();
        let currentMonth = rruleStartDate.getMonth();
    
        // UTC Offset Fixes.
        let totalOffsetMin = new Date(rruleStartDate).getTimezoneOffset();
        let offsetHour = parseInt(totalOffsetMin/60);
        let offsetMin = parseInt(totalOffsetMin%60);
    
        let currentHours = 0;
        let currentMin = 0;
        if ( offsetHour > 0 ) {
            currentHours = offsetHour;
            currentMin = offsetMin;
        }
        rruleStartDate = moment( new Date( Date.UTC(curretYear, currentMonth, currentDate, currentHours, currentMin, 0 ) ) ).utc();
        
        let ruleArgs = {
            freq: RRule.DAILY,
            count: datePerPage,
            dtstart: new Date(rruleStartDate),
        };
		if ( showAll ) { // This args is only For Pagination.
			// delete ruleArgs.count;
            ruleArgs.count = ( 50 * datePerPage ); // Support Max 50 pages i.e. 500 records.
		}
        if ( date.end_date && '0000-00-00' != date.end_date ) { // if has end date.
            let endDate = new Date(date.end_date)
            ruleArgs.until = endDate
        }
        rruleStartDate    = moment( rruleStartDate );
        let selectedYears = date.years ? date.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];
        if (selectedYears.length > 0 && !selectedYears.includes(rruleStartDate.year())) {
            return []
        }
    
        let selectedMonths = date.months ? date.months.split(",").filter(month => month != 'every_month') : [];
        let selectedDates  = date.date_days ? date.date_days.split(",").filter(date => date !== 'every_weekdays' && date !== '') : [];
        let selectedDays   = date.days ? date.days.split(",").filter(day => day !== 'every_date_days' && day !== '') : [];
    
        if (selectedMonths.length > 0) {
            ruleArgs.bymonth = selectedMonths.map(m => parseInt(m));
        }
        if (selectedDays.length > 0) {
            ruleArgs.byweekday = selectedDays.map(sd => RRule[sd]);
        }
        else if (selectedDates.length > 0) {
            ruleArgs.bymonthday = selectedDates.map(md => parseInt(md));
        }
        return ruleArgs
    }
    return <>
            { activeRecurringDates.map( esx => {

            	return  <div key={12} > {IsTourDates(props)(esx) && <Pricings { ...props } /> || <Disabled><Pricings { ...props } /></Disabled>} </div>; 
                       
            })}

    </>
}

const NonRecurringDates = ( props ) => {
    // Component Props.
	const { tripData, bookingData } = props;

    // Trip Data.
    const {
        dates,
        pricings,
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];

    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' !== d.start_date && new Date( d.start_date )  > new Date() } )
	nonRecurringDates     = _.chain( nonRecurringDates ).sortBy( d => moment( d.start_date).unix() ).value() || []; // Sort by date.

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;
	let enable_time = '';
	_dates.map( ( dateData ) => {
		if( selectedDateIds[0] == dateData.id ) {
			enable_time = dateData.enable_time;
		}
	})
    return <>
    {
		nonRecurringDates.length > 0 && <tbody className="tbody-table">
		{ nonRecurringDates.map( ( date, index ) => {
			let loadingClass = isLoading && selectedDateIds.includes( date.id ) ? 'wptravel-loading' : '';
			let _nomineePricings = date.pricing_ids.split(',').map( id => id.trim() );
			_nomineePricings = _.chain( _nomineePricings ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' );
			if ( ! _nomineePricings.length ) {
				return <Fragment key={index}></Fragment>
			}
			return <tr key={index} className={loadingClass}>
				<td class="tablebody-booking-pricings" data-label={__i18n.bookings.pricings_list_label}>
					{/* _nomineePricings not updated in store/state because there are multiple _nomineePricings as per date so just a variable. */}
					{/* <Pricings { ...{ ...props, _nomineePricings, date } }  /> */}
					{/**
					 * @param props, date , and price id
					 * @return price name
					 * @since 5.3.9
					 */}
					 <NonRecorringRepeater { ...{ ...props, _nomineePricings, date } } />
				</td>
				<td class="tablebody-booking-person" data-label={__i18n.bookings.person}>
					<div className ="person-box">
						
						{ ! isLoading && pricingUnavailable && tripData.inventory && 'yes' === tripData.inventory.enable_trip_inventory && selectedDateIds.includes( date.id ) ? 
							<Notice><InventoryNotice inventory={tripData.inventory} /></Notice> 
							:
							<>
								{ ( ! selectedPricingId || ( enable_time && nomineeTimes.length > 0 && ! selectedTime ) || ! selectedDateIds.includes( date.id ) || isLoading ) && <Disabled><PaxSelector { ...{ ...props, _nomineePricings, date } } /></Disabled> || <PaxSelector { ...{ ...props, _nomineePricings, date } } /> }
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
				<td class="tablebody-booking-dates" data-label={__i18n.bookings.date}>
					<div className = "date-box">
						<div className="date-time-wrapper">
							<span className="start-date"><span>{__i18n.bookings.start_date}: </span>{moment(date.start_date).format(_wp_travel.date_format_moment)}</span>
							{date.end_date && '0000-00-00' != date.end_date && <span className="end-date"><span>{__i18n.bookings.end_date}: </span>{moment(date.end_date).format(_wp_travel.date_format_moment)}</span> }
						</div>
						{selectedDateIds.includes( date.id ) && ! isLoading &&
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