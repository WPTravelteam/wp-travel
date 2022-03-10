import { forwardRef, useEffect, useState } from '@wordpress/element';
import { CheckboxControl, Disabled } from '@wordpress/components';

const __i18n = {
	..._wp_travel.strings
}
const datePerPage = 5
// WP Travel Functions.
import { objectSum } from '../../_wptravelFunctions';
import { IsTourDate } from '../../_IsTourDate'; // Filter available dates in calendar.


// Additional lib
const _ = lodash;
import { RRule, RRuleSet } from "rrule";


// WP Travel Components.
import Pricings from './SubComponents/Pricings';
import PaxSelector from './SubComponents/PaxSelector';
import TripExtras from './SubComponents/TripExtras';
import TripTimes from './SubComponents/TripTimes';
// import generateRRule from "../../_GenerateRRule";


const RecurringRepeator = ( props ) =>  {
    const { index, _nomineePricings, date, recurrindDate, tripData, bookingData } = props;

    // Trip Data.
    const { pricings } = tripData;
    const allPricings  = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.

    // Booking Data.
    const { selectedDate, selectedDateIds, selectedPricingId, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;

    return <tr key={index}>
        <td data-label={__i18n.bookings.pricings_list_label}>
            {/* _nomineePricings not updated in store/state because there are multiple _nomineePricings as per date so just a variable. */}
            {IsTourDate(props)(recurrindDate) && <Pricings { ...props } /> || <Disabled><Pricings { ...props } /></Disabled> }
        </td>
        <td data-label={__i18n.bookings.person}>
            <div className ="person-box">
                { ! pricingUnavailable && <>
                    { ( ! selectedPricingId || ( nomineeTimes.length > 1 && ! selectedTime ) || ! selectedDateIds.includes( date.id ) || recurrindDate !== selectedDate ) && <Disabled><PaxSelector { ...{ ...props, _nomineePricings, date, recurrindDate } } /></Disabled> || <PaxSelector { ...{ ...props, _nomineePricings, date, recurrindDate } } /> }
                    { 
                        selectedPricingId && 
                        selectedDateIds.includes( date.id ) && 
                        _.size( allPricings[ selectedPricingId ].trip_extras ) > 0 && 
                        objectSum( paxCounts ) > 0 && 
                        recurrindDate === selectedDate &&
                        ( ! nomineeTimes.length || ( nomineeTimes.length > 0 && selectedTime ) ) &&
                        <> <TripExtras {...props} /> </> 
                    }
                    </> 
                }		
            </div>
        </td>
        <td data-label={__i18n.bookings.date}>
            <div className = "date-box">
                <div className="date-time-wrapper">
                    <span className="start-date"><span>{__i18n.bookings.start_date}: </span>{moment(recurrindDate).format(_wp_travel.date_format_moment)}</span>
                    {date.end_date && '0000-00-00' != date.end_date && <span className="end-date"><span>{__i18n.bookings.end_date}: </span>{moment(date.end_date).format(_wp_travel.date_format_moment)}</span> }
                </div>
                {selectedDateIds.includes( date.id ) &&
                    <TripTimes { ...props }  />
                }
            </div>
        </td>
    </tr>
}

const RecurringDates = ( props ) => {
    // Component Props.
	const { tripData, bookingData, date } = props;

    // Trip Data.
    const {
        pricings,
    } = tripData;

    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.

    // Recurring logic form old components
    const [dates, setRecurringDates] = useState([])
    const [activeRecurringDates, setActiveRecurringDates] = useState([])
    const [rruleArgs, setRRuleArgs] = useState(null)
    const [{ activePage, datesPerPage, pagesCount }, setPagination] = useState({
        activePage: 0,
        datesPerPage: datePerPage,
        pagesCount: 0
    });

    const generateRRule = rruleArgs => {
        const rruleSet = new RRuleSet();
        rruleSet.rrule(
            new RRule(rruleArgs)
        );
        // let exc = new Date( ' 2022-03-15 00:00:00');
        // rruleSet.exdate( exc )
        return rruleSet.all();
    }
    const generateRRUleArgs = date => {
        let startDate = date.start_date && new Date(date.start_date + ' 00:00:00' ) || new Date();
        let currentDate = new Date();
        // currentDate.setHours(0, 0, 0, 0);

        let rruleStartDate = currentDate < startDate ? startDate : currentDate;

        let ruleArgs = {
            freq: RRule.DAILY,
            count: datePerPage,
            dtstart: new Date(rruleStartDate),
        };
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
    useEffect( () => {
        if ( ! rruleArgs ) {
            let aaa = generateRRUleArgs(date);
            if (Object.keys(aaa).length > 0) {
                setRRuleArgs(aaa)
            }
        }
    }, [date])

    useEffect(() => {
        if (rruleArgs) {
            let _dates = generateRRule(rruleArgs)
            setRecurringDates(_dates)
            setActiveRecurringDates(_dates)
            setPagination(state => ({ ...state, activePage: 1, pagesCount: 1 }))
        }
    }, [rruleArgs]);

    const loadMoreDates = page => () => {
        let start = page < 0 ? (activePage - 2) * datesPerPage : activePage * datesPerPage
        let end = start + datesPerPage
        let _dates = []
        let pagination = {}
        if (dates.slice(start, end).length > 0) {
            _dates = dates.slice(start, end)
        } else {
            let _rruleArgs = {
                ...rruleArgs,
                dtstart: nextStartDate
            }
            _dates = generateRRule(_rruleArgs)
            setRecurringDates([...dates, ..._dates])
            pagination.pagesCount = pagesCount + 1
        }
        if (_dates.length > 0) {
            setActiveRecurringDates(_dates)
            pagination.activePage = activePage + page
            setPagination(state => ({ ...state, ...pagination }))
        }
    }
    const nextStartDate = dates.length > 0 && moment( dates[dates.length - 1] ).add(1, 'days').toDate();

    return <>
    {
		activeRecurringDates.length > 0 && <tbody className="tbody-table">
		{ activeRecurringDates.map( ( recurrindDate, index ) => {
            let _nomineePricings = date.pricing_ids.split(',').map( id => id.trim() );
			_nomineePricings = _.chain( _nomineePricings ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' );
			if ( ! _nomineePricings.length ) {
				return <></>
			}
			return <RecurringRepeator { ...{ ...props, _nomineePricings, date, recurrindDate, index, _nomineePricings } } />
        } ) }
		</tbody>
    }
        <tfoot className="wp-travel-recurring-dates-nav-btns">
           <tr> 
               <td colSpan="3">{activePage > 1 && <button onClick={loadMoreDates(-1)} className="prev">{__i18n.previous}</button>}
            {activePage < pagesCount && activePage >= 1 && <button className="next" onClick={loadMoreDates(1)}>{__i18n.next}</button>}
            { ( activePage >= pagesCount && activeRecurringDates.length >= datePerPage ) && <button onClick={loadMoreDates(1)} className="show-more">{__i18n.load_more}</button>}</td>
            </tr>
        </tfoot>
    </>
}
export default RecurringDates;