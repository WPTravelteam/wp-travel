import moment from 'moment'
import { RRule, RRuleSet, rrulestr } from 'rrule'
import { useMemo, useState, useRef, useEffect, lazy, Suspense } from '@wordpress/element'
import { PanelBody, PanelRow, Disabled, RadioControl, CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n'
const _ = lodash

import ErrorBoundry from '../ErrorBoundry';

import PaxSelector from './PaxSelector';
import TripTimesListing from './TripTimesListing';
import TripExtrasListing from './TripExtrasListing';
// const PaxSelector       = lazy(() => import("./PaxSelector"));
// const TripTimesListing  = lazy(() => import("./TripTimesListing"));
// const TripExtrasListing = lazy(() => import("./TripExtrasListing"));

import Loader from '../../../GlobalComponents/Loader'

const __i18n = {
	..._wp_travel.strings
}
const datePerPage = 5
const generateRRule = rruleArgs => {
    const rruleSet = new RRuleSet();
    rruleSet.rrule(
        new RRule(rruleArgs)
    );
    let rule = new RRule(rruleArgs);
    return rule.all()
}

const generateRRUleArgs = data => {
    let startDate = data.start_date && new Date(data.start_date) || new Date();
    let currentDate = new Date();

    let rruleStartDate = currentDate < startDate ? startDate : currentDate;

    let ruleArgs = {
        freq: RRule.DAILY,
        count: datePerPage,
        // dtstart: new Date(Date.UTC(rruleStartDate.getFullYear(), rruleStartDate.getMonth(), rruleStartDate.getDate(), 0, 0, 0)),
        dtstart: new Date(rruleStartDate),
    };
    if ( data.end_date && '0000-00-00' != data.end_date ) { // if has end date.
        let endDate = new Date(data.end_date)
        ruleArgs.until = endDate
    }
    rruleStartDate = moment( rruleStartDate ).utc();
    let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];
    if (selectedYears.length > 0 && !selectedYears.includes(rruleStartDate.year())) {
        return []
    }


    let selectedMonths = data.months ? data.months.split(",").filter(month => month != 'every_month') : [];
    let selectedDates = data.date_days ? data.date_days.split(",").filter(date => date !== 'every_weekdays' && date !== '') : [];
    let selectedDays = data.days ? data.days.split(",").filter(day => day !== 'every_date_days' && day !== '') : [];

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

const RecurringDates = ({ data, isTourDate, getPricingsByDate, onFixedDeparturePricingSelect, allData, componentData }) => {
   
    const [dates, setRecurringDates] = useState([])
    const [activeRecurringDates, setActiveRecurringDates] = useState([])
    const [rruleArgs, setRRuleArgs] = useState(null)
    const [showRecurringDates, showRecurringDatesToggle] = useState(false)
    const [selectedDate, setSelectedDate] = useState(null)
    const [{ activePage, datesPerPage, pagesCount }, setPagination] = useState({
        activePage: 0,
        datesPerPage: datePerPage,
        pagesCount: 0
    })
    useEffect(() => {
        if (!rruleArgs) {
            let aaa = generateRRUleArgs(data);
            if (Object.keys(aaa).length > 0) {
                setRRuleArgs(aaa)
            }
        }
    }, [data])

    useEffect(() => {
        if (rruleArgs) {
            let _dates = generateRRule(rruleArgs)
            setRecurringDates(_dates)
            setActiveRecurringDates(_dates)
            setPagination(state => ({ ...state, activePage: 1, pagesCount: 1 }))
        }
    }, [rruleArgs])
    const nextStartDate = dates.length > 0 && moment(dates[dates.length - 1]).add(1, 'days').toDate()

    let pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id); // All Pricings.
    const handleFixedDeparturePricingClick = ( date, date_id, pricingId ) => e => {
        if (typeof onFixedDeparturePricingSelect === 'function') {
            onFixedDeparturePricingSelect(moment(date).toDate(), date_id, pricingId )()
        }
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
    let _pricingIds = getPricingsByDate(moment(data.start_date).toDate(), data.id);

    if ( ! _pricingIds.length ) {
        return  <tbody><tr><td colSpan="3"><p className="text-center">Date has no pricings</p></td></tr></tbody>;
    }
    return <>
    <tbody className="tbody-table">
        {activeRecurringDates.map( ( date, dateIndex ) => {
            let _date = moment(moment(date).format("YYYY-MM-DD")) // looped date.
            let _selectedDateTime = null;
            if (componentData.selectedDateTime) {
                _selectedDateTime = moment(moment(componentData.selectedDateTime).format("YYYY-MM-DD"));
            }
            let firstPricingId = _pricingIds[0];
            let firstPricing = pricings[firstPricingId]

            let firstCounts = {}
            
            let pricingOptions = []
            if ( 'undefined' != typeof _pricingIds.length && _pricingIds.length ) {
                let firstCategories = firstPricing.categories;
                firstCategories.forEach(c => {
                    firstCounts = { ...firstCounts, [c.id]: parseInt(c.default_pax) || 0 }
                })
                pricingOptions = _pricingIds.map( (pricingId, pricingIndex) => {
                    return { label: pricings[pricingId].title, value: pricingId }
                })
            }
            return <>
                { isTourDate(new Date( _date ) ) ? 
                 
                    <tr key={dateIndex} className={
                        _date.isSame( _selectedDateTime ) ? 'selected': '', 
                        componentData.isLoading && componentData.selectedDateIds.includes(data.id) && componentData.selectedPricingId == componentData.pricing.id && _date.isSame( _selectedDateTime ) && 'wptravel-loading'
                        } >

                        <td data-label={__i18n.bookings.pricings_list_label}>
                            {/* <Loader /> */}
                            {componentData.isLoading && componentData.selectedDateIds.includes(data.id) && componentData.selectedPricingId == componentData.pricing.id && _date.isSame( _selectedDateTime ) && <Loader />}
                            { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                <>
                                    {_pricingIds.map( (pricingId, pricingIndex) => {
                                        return <CheckboxControl
                                            key={pricingIndex}
                                            label={pricings[pricingId].title}
                                            checked={ componentData.selectedPricingId == pricingId && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) }
                                            onChange={ handleFixedDeparturePricingClick(_date, date.id, pricingId ) }
                                        />
                                        
                                    })}
                                </>
                            }
                        </td>
                        <td data-label={__i18n.bookings.person}>
                            <div className ="person-box">
                        {
                        !componentData.pricingUnavailable && componentData.pricing && componentData.inventory.find(i => i.pax_available > 0 && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) ) ? 
                            <>
                            {/* <Suspense fallback={<Loader />}> */}
                                <PaxSelector
                                    pricing={componentData.pricing ? componentData.pricing : firstPricing }
                                    onPaxChange={componentData.onPaxChange}
                                    counts={componentData.counts ? componentData.counts : firstCounts }
                                    inventory={componentData.inventory}
                                />
                            {/* </Suspense> */}
                            {componentData.totalPax > 0 && _.size(componentData.pricing.trip_extras) > 0 && 
                                // <Suspense fallback={<Loader />}>
                                    <ErrorBoundry>
                                        <TripExtrasListing
                                            options={componentData.pricing.trip_extras}
                                            onChange={(id, value) => () => componentData.updateState({ tripExtras: { ...componentData.tripExtras, [id]: parseInt(value) } })}
                                            counts={componentData.tripExtras}
                                        />
                                    </ErrorBoundry>
                                // </Suspense>
                            }
                            </>
                            : <Disabled>
                                {/* Just to display */}
                                {/* <Suspense fallback={<Loader />}> */}
                                    <PaxSelector
                                        pricing={ firstPricing }
                                        onPaxChange={componentData.onPaxChange}
                                        counts={firstCounts }
                                        inventory={componentData.inventory}
                                        />
                                {/* </Suspense> */}
                            </Disabled>

                        }
                        </div>
                        </td>
                        <td data-label={__i18n.bookings.date}>
                            <div className="date-time-wrapper">
                                <span className="start-date"><span>{__i18n.bookings.start_date}: </span>{_date.format(_wp_travel.date_format_moment)}</span>
                            </div>
                            {
                                !componentData.pricingUnavailable && componentData.nomineeTimes.length > 0 && componentData.inventory.find(i => i.pax_available > 0 && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) ) &&
                                // <Suspense fallback={<Loader />}>
                                    <TripTimesListing
                                        selected={componentData.selectedDateTime}
                                        onTimeSelect={componentData.onTimeSelect}
                                        options={componentData.nomineeTimes}
                                    />
                                // </Suspense>
                            }
                            
                       </td>
                    </tr>
                    :
                    <tr>
                        <td>
                            <Disabled>
                                { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                    <>
                                        {_pricingIds.map( (pricingId, pricingIndex) => {
                                            return <CheckboxControl
                                                key={pricingIndex}
                                                label={pricings[pricingId].title}
                                                checked={ componentData.selectedPricingId == pricingId && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) }
                                                onChange={ handleFixedDeparturePricingClick(_date, date.id, pricingId ) }
                                            />
                                        })}
                                    </>
                                }
                            </Disabled>
                        </td>
                        <td>
                            <Disabled>
                                {/* <Suspense fallback={<Loader />}> */}
                                    <PaxSelector
                                        pricing={ firstPricing }
                                        onPaxChange={componentData.onPaxChange}
                                        counts={firstCounts }
                                        inventory={componentData.inventory}
                                    />
                                {/* </Suspense> */}
                            </Disabled>
                        </td>
                        <td data-label={__i18n.bookings.pricings_list_label}><Disabled>
                                <div className="date-time-wrapper">
                                <span className="start-date"><span>{__i18n.bookings.start_date}: </span>{_date.format(_wp_travel.date_format_moment)}</span>
                            </div></Disabled>
                        </td>
                    </tr>
                }
            </>
            
        })}
        </tbody> 
        <tfoot className="wp-travel-recurring-dates-nav-btns">
           <tr> 
               <td colSpan="3">{activePage > 1 && <button onClick={loadMoreDates(-1)} className="prev">{__i18n.previous}</button>}
            {activePage < pagesCount && activePage >= 1 && <button className="next" onClick={loadMoreDates(1)}>{__i18n.next}</button>}
            { ( activePage >= pagesCount && activeRecurringDates.length >= datePerPage ) && <button onClick={loadMoreDates(1)} className="show-more">{__i18n.load_more}</button>}</td>
            </tr>
        </tfoot>
    </>
}

const DatesListing = ({ dates, isTourDate, getPricingsByDate, allData, onFixedDeparturePricingSelect, componentData, getPricingTripTimes }) => {

    const handlePricingClick = ( date, date_id, pricingId ) => () => {

        if (typeof onFixedDeparturePricingSelect === 'function') {
            onFixedDeparturePricingSelect(moment(date).toDate(), date_id, pricingId )
        }
    }

    const _dates = Object.values(dates)
    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' != d.start_date && new Date( d.start_date )  > new Date() } )
    let pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id); // All Pricings.
    let times = getPricingTripTimes(componentData.selectedPricingId, [])
    // console.log( 'componentData', componentData );

    return <>
        {
            _dates.length > 0 ? <>

                <div className="wptravel-recurring-dates">
                    <div className="wptravel-recurring-table-wrapper">
                        {nonRecurringDates.length > 0 &&
                        <>
                            <table className="wptravel-recurring-table">
                                <thead className="thead-table">
                                    <tr>
                                        <th data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</th>
                                        <th data-label={__i18n.bookings.person}>{__i18n.bookings.person}</th>
                                        <th data-label={__i18n.bookings.date}>{__i18n.bookings.date}</th>
                                        {/* <th>{__i18n.bookings.action}</th> */}
                                    </tr>
                                </thead>
                                <tbody className="tbody-table">
                                    {
                                        nonRecurringDates.map((date, index) => {
                                            let firstPricing = {};
                                            let firstCounts = {}

                                            let _pricingIds = getPricingsByDate(moment(date.start_date).toDate(), date.id);
                                            if ( ! _pricingIds.length ) {
                                                return  <tr><td colSpan="3"><p className="text-center">Date has no pricings</p></td></tr>
                                            }
                                            let firstPricingId = _pricingIds[0];
                                            firstPricing = pricings[firstPricingId]
                                            if ( 'undefined' != typeof firstPricing ) {
                                                // console.log('pricings', pricings);
                                                // console.log('firstPricing', firstPricing);
                                                let firstCategories = firstPricing.categories
                                                firstCategories.forEach(c => {
                                                    firstCounts = { ...firstCounts, [c.id]: parseInt(c.default_pax) || 0 }
                                                })
                                            }

                                            let pricingOptions = []
                                            if ( 'undefined' != typeof _pricingIds.length && _pricingIds.length ) {
                                                pricingOptions = _pricingIds.map( (pricingId, pricingIndex) => {
                                                    return { label: pricings[pricingId].title, value: pricingId }
                                                })
                                            }

                                            let _selectedDateTime = null;
                                            if ( 'undefined' != typeof componentData.selectedDateTime) {
                                                _selectedDateTime = moment(moment(componentData.selectedDateTime).format("YYYY-MM-DD"));
                                            }
                                            let _start_date = null;
                                            if ( ! date.is_recurring && date.start_date ) {
                                                _start_date = moment( moment(date.start_date).format("YYYY-MM-DD") )
                                            }
                                            return <>
                                                {! date.is_recurring && isTourDate(new Date( _start_date ) ) && 
                                                    <>
                                                    <tr key={index} className={
                                                        _start_date.isSame( _selectedDateTime ) ? 'selected': '',
                                                        componentData.isLoading && componentData.selectedDateIds.includes(date.id) && 'wptravel-loading'
                                                        }>
                                                        <td data-label={__i18n.bookings.pricings_list_label}>
                                                            {componentData.isLoading && componentData.selectedDateIds.includes(date.id) && <Loader /> }
                                                                {/* <Loader /> */}
                                                            { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                                                <>
                                                                    {_pricingIds.map( (pricingId, pricingIndex) => {
                                                                        return <CheckboxControl
                                                                                key={pricingIndex}
                                                                                label={pricings[pricingId].title}
                                                                                checked={ componentData.selectedPricingId == pricingId && componentData.selectedDateIds.includes(date.id) }
                                                                                onChange={ handlePricingClick(date.start_date, date.id, pricingId ) }
                                                                            />
                                                                    })}
                                                                </>
                                                            }
                                                        </td>
                                                        <td data-label={__i18n.bookings.person}>
                                                            
                                                        <div className ="person-box">
                                                        {
                                                            !componentData.pricingUnavailable && componentData.pricing && componentData.inventory.find(i => i.pax_available > 0 && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(date.id) ) ? 
                                                                <>
                                                                    {/* <Suspense fallback={<Loader />}> */}
                                                                        <PaxSelector
                                                                            pricing={componentData.pricing ? componentData.pricing : firstPricing }
                                                                            onPaxChange={componentData.onPaxChange}
                                                                            counts={componentData.counts ? componentData.counts : firstCounts }
                                                                            inventory={componentData.inventory}
                                                                        />
                                                                    {/* </Suspense> */}
                                                                    {
                                                                        componentData.totalPax > 0 && _.size(componentData.pricing.trip_extras) > 0 && 
                                                                        // <Suspense fallback={<Loader />}>
                                                                            <ErrorBoundry>
                                                                                <TripExtrasListing
                                                                                    options={componentData.pricing.trip_extras}
                                                                                    onChange={(id, value) => () => componentData.updateState({ tripExtras: { ...componentData.tripExtras, [id]: parseInt(value) } })}
                                                                                    counts={componentData.tripExtras}
                                                                                />
                                                                            </ErrorBoundry>
                                                                        // </Suspense>
                                                                    }
                                                                </>
                                                                : <Disabled>
                                                                    {/* Just to display */}
                                                                    {/* <Suspense fallback={<Loader />}> */}
                                                                        <PaxSelector
                                                                            pricing={ firstPricing }
                                                                            onPaxChange={componentData.onPaxChange}
                                                                            counts={firstCounts }
                                                                            inventory={componentData.inventory}
                                                                            />
                                                                    {/* </Suspense> */}
                                                                </Disabled>

                                                        }
                                                        
                                                        </div>
                                                        </td>
                                                        <td data-label={__i18n.bookings.date}>
                                                            <div className = "date-box">
                                                                <div className="date-time-wrapper">
                                                                    <span className="start-date"><span>{__i18n.bookings.start_date}: </span>{moment(date.start_date).format(_wp_travel.date_format_moment)}</span>
                                                                    {date.end_date && '0000-00-00' != date.end_date && <span className="end-date"><span>{__i18n.bookings.end_date}: </span>{moment(date.end_date).format(_wp_travel.date_format_moment)}</span> }
                                                                </div>
                                                                    { !componentData.pricingUnavailable && componentData.nomineeTimes.length > 0 && componentData.selectedPricingId == componentData.pricing.id && componentData.selectedDateIds.includes(date.id) &&
                                                                        <>
                                                                        {/* <Suspense fallback={<Loader />}> */}
                                                                            <TripTimesListing
                                                                                selected={componentData.selectedDateTime}
                                                                                onTimeSelect={componentData.onTimeSelect}
                                                                                options={componentData.nomineeTimes}
                                                                            />
                                                                        {/* </Suspense> */}
                                                                        </>
                                                                    }
                                                            </div>
                                                        </td>
                                                        
                                                    </tr>
                                                    </>
                                                }
                                            </>
                                        })
                                    }
                                </tbody>
                            </table>
                        </>
                        }
                    </div>
                    {/* Recurring */}
                    {_dates.map((date, index) => {
                        return <>
                            { date.is_recurring && 
                                <PanelBody title={__( `${__i18n.bookings.recurring} ${date.title}`, 'wp-travel' )} initialOpen={true} key={index} >
                                    <PanelRow>
                                    <table>
                                        <thead className="thead-table">
                                            <tr>
                                                <th data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</th>
                                                <th data-label={__i18n.bookings.person}>{__i18n.bookings.person}</th>
                                                <th data-label={__i18n.bookings.date}>{__i18n.bookings.date}</th>
                                                {/* <th>{__i18n.bookings.action}</th> */}
                                            </tr>
                                        </thead>
                                        <RecurringDates data={date} onFixedDeparturePricingSelect={handlePricingClick} getPricingsByDate={getPricingsByDate} isTourDate={isTourDate} allData={allData} componentData={componentData} key={index} />
                                        
                                    </table>
                                    </PanelRow>
                                </PanelBody>
                            }
                            </>
                        })
                    }
                    <div id="wp-travel-booking-recurring-dates"></div> {/* <!-- required for scroll pricing --> */}
                </div>
            </> : <> {__i18n.add_date}</>
        }
    </>
}

export default DatesListing