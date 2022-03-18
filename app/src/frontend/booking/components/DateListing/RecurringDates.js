import { forwardRef, useEffect, useState, Fragment } from '@wordpress/element';
import { CheckboxControl, Disabled } from '@wordpress/components';

const __i18n = {
	..._wp_travel.strings
}
const datePerPage = 5
// WP Travel Functions.
import { objectSum } from '../../_wptravelFunctions';
import { IsTourDate } from '../../_IsTourDate'; // Filter available dates in calendar.
import Loader from '../../../../GlobalComponents/Loader';


// Additional lib
const _ = lodash;
import { RRule, RRuleSet } from "rrule";


// WP Travel Components.
import Pricings from './SubComponents/Pricings';
import PaxSelector from './SubComponents/PaxSelector';
import TripExtras from './SubComponents/TripExtras';
import TripTimes from './SubComponents/TripTimes';
// import generateRRule from "../../_GenerateRRule";
import InventoryNotice, { Notice } from '../../_InventoryNotice';


const RecurringRepeator = ( props ) =>  {
    const { innerIndex:index, _nomineePricings, date, recurrindDate, tripData, bookingData } = props;

    // Trip Data.
    const { pricings } = tripData;
    const allPricings  = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.

    // Booking Data.
    const { isLoading, selectedDate, selectedDateIds, selectedPricingId, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;
    let sd = moment(moment(selectedDate).format('YYYY-MM-DD')).unix();
    let rd = moment(moment(recurrindDate).format('YYYY-MM-DD')).unix();

    let loadingClass = isLoading && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) ) ? 'wptravel-loading' : '';
    // { pricingUnavailable && tripData.inventory && 'yes' === tripData.inventory.enable_trip_inventory && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) && ( ! nomineeTimes.length || ( nomineeTimes.length && selectedTime ) )  ) 
    console.log( 'recurrindDate', recurrindDate );
    console.log( 'pricingUnavailable', pricingUnavailable );
    console.log( 'tripData.inventory.enable_trip_inventory', tripData.inventory.enable_trip_inventory );
    console.log( 'selectedDateIds.includes( date.id )', selectedDateIds.includes( date.id ) );
    console.log( 'recurrindDate && sd == rd ', recurrindDate && sd == rd  );
    console.log( '! nomineeTimes.length || ( nomineeTimes.length && selectedTime )', ! nomineeTimes.length || ( nomineeTimes.length && selectedTime ) );
    console.log( '---------------' );
    return <tr key={index} className={loadingClass}>
        <td data-label={__i18n.bookings.pricings_list_label}>
            {/* _nomineePricings not updated in store/state because there are multiple _nomineePricings as per date so just a variable. */}
            {IsTourDate(props)(recurrindDate) && <Pricings { ...props } /> || <Disabled><Pricings { ...props } /></Disabled> }
        </td>
        <td data-label={__i18n.bookings.person}>
            <div className ="person-box">
                <>
                    { ( 
                       ! pricingUnavailable && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) ) && ( ! nomineeTimes.length || ( nomineeTimes.length && selectedTime ) )  ) && 
                        <PaxSelector { ...{ ...props, _nomineePricings, date, recurrindDate } } /> || 
                        <Disabled><PaxSelector { ...{ ...props, _nomineePricings, date, recurrindDate } } /></Disabled> }
                    { 
                        selectedPricingId && 
                        selectedDateIds.includes( date.id ) && 
                        _.size( allPricings[ selectedPricingId ].trip_extras ) > 0 && 
                        objectSum( paxCounts ) > 0 && 
                        ( ! recurrindDate || ( recurrindDate && sd == rd ) ) &&
                        ( ! nomineeTimes.length || ( nomineeTimes.length > 0 && selectedTime ) ) &&
                        <> <TripExtras {...props} /> </> 
                    }
                </> 
                { ! isLoading && pricingUnavailable && tripData.inventory && 'yes' === tripData.inventory.enable_trip_inventory && selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) && ( ! nomineeTimes.length || ( nomineeTimes.length && selectedTime ) )  ) &&
                    <Notice><InventoryNotice inventory={tripData.inventory} /></Notice>
                }		
            </div>
        </td>
        <td data-label={__i18n.bookings.date}>
            <div className = "date-box">
                <div className="date-time-wrapper">
                    <span className="start-date"><span>{__i18n.bookings.start_date}: </span>{moment(recurrindDate).format(_wp_travel.date_format_moment)}</span>
                    {date.end_date && '0000-00-00' != date.end_date && ! recurrindDate && <span className="end-date"><span>{__i18n.bookings.end_date}: </span>{moment(date.end_date).format(_wp_travel.date_format_moment)}</span> }
                </div>
                {selectedDateIds.includes( date.id ) && ( ! recurrindDate || ( recurrindDate && sd == rd ) ) &&
                    <TripTimes { ...props }  />
                }
            </div>
        </td>
    </tr>
}

const RecurringDates = ( props ) => {
    // Component Props.
	const { tripData, bookingData, date, index } = props;

    // Trip Data.
    const {
        pricings,
    } = tripData;

    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.

    // Recurring logic form old components
    const [dates, setRecurringDates] = useState([]); // New dates will push here when clicking load more.
    const [activeRecurringDates, setActiveRecurringDates] = useState([]); // curren page dates.
    const [rruleArgs, setRRuleArgs] = useState(null)
    const [{ activePage, datesPerPage, pagesCount, totalPages }, setPagination] = useState({
        activePage: 0, // Current Page
        datesPerPage: datePerPage,
        pagesCount: 0, // Load more click count [How many times load more clicked]
        totalPages: 1
    });

    // Date changes step 1 to add rrule args.
    useEffect( () => {
        if ( ! rruleArgs ) {
            let aaa = generateRRUleArgs(date);
            if (Object.keys(aaa).length > 0) {
                setRRuleArgs(aaa)
            }

            // Total Number of page calculation. [ support Upto 50 pages ] Fetching all Data in case of no end date will cause site slow issue.
			let alldateRruleArgs = generateRRUleArgs( date, true );
            let _tempDates  = generateRRule( alldateRruleArgs );
            let tp = _tempDates.length > 0 ? _tempDates.length / datePerPage : 1; // Total Page
                tp =  Math.ceil(tp);
            setPagination(state => ({ ...state, totalPages: tp }))

        }
    }, [date])

    // Set dates if rrule args is set.
    useEffect(() => {
        if (rruleArgs) {
            let _dates = generateRRule(rruleArgs);
            setRecurringDates(_dates)
            setActiveRecurringDates(_dates)
            setPagination(state => ({ ...state, activePage: 1, pagesCount: 1 }))
        }
    }, [rruleArgs]);


    const generateRRule = rruleArgs => {
        const rruleSet = new RRuleSet();
        rruleSet.rrule(
            new RRule(rruleArgs)
        );
        // let exc = new Date( ' 2022-03-15 00:00:00');
        // rruleSet.exdate( exc )
        return rruleSet.all();
    }
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

    return <Fragment key={index}>
    {
		activeRecurringDates.length > 0 && <tbody className="tbody-table">
		{ activeRecurringDates.map( ( recurrindDate, innerIndex ) => {
            let _nomineePricings = date.pricing_ids.split(',').map( id => id.trim() );
			_nomineePricings = _.chain( _nomineePricings ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' );
			if ( ! _nomineePricings.length ) {
				return <></>
			}
			return <RecurringRepeator { ...{ ...props, _nomineePricings, date, recurrindDate, innerIndex, _nomineePricings } } />
        } ) }
		</tbody>
    }
		<tfoot className="wp-travel-recurring-dates-nav-btns">
			<tr> 
				<td colSpan="3">
					{activePage > 1 && <button onClick={loadMoreDates(-1)} className="prev">{__i18n.previous}</button>}
					{activePage < pagesCount && activePage >= 1 && <button className="next" onClick={loadMoreDates(1)}>{__i18n.next}</button>}
					{ ( activePage >= pagesCount && activeRecurringDates.length >= datePerPage && activePage < totalPages ) && <button onClick={loadMoreDates(1)} className="show-more">{__i18n.load_more}</button>}</td>
            </tr>
        </tfoot>
    </Fragment>
}
export default RecurringDates;