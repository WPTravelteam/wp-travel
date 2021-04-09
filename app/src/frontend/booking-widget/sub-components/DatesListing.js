import moment from 'moment'
import { RRule, RRuleSet, rrulestr } from 'rrule'
import { useMemo, useState, useRef, useEffect } from '@wordpress/element'
import { PanelBody, PanelRow, Disabled, RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n'
const _ = lodash

import PaxSelector from './PaxSelector';
import TripTimesListing from './TripTimesListing';

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
        dtstart: new Date(Date.UTC(rruleStartDate.getFullYear(), rruleStartDate.getMonth(), rruleStartDate.getDate(), 0, 0, 0)),
    };
    if ( data.end_date && '0000-00-00' != data.end_date ) { // if has end date.
        let endDate = new Date(data.end_date)
        ruleArgs.until = endDate
    }
    rruleStartDate = moment( rruleStartDate ).utc();
    let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];
    
    if (selectedYears.length > 0 && !selectedYears.includes(rruleStartDate.year()))
        return []


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

const RecurringDates = ({ data, onDateClick, isTourDate, getPricingsByDate, onFixedDeparturePricingSelect, allData, paxSelectorData }) => {
   
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
            setRRuleArgs(generateRRUleArgs(data))
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

    const handleDateClick = _date => e => {
        // e.target.disabled = true
        showRecurringDatesToggle(!showRecurringDates)
        setSelectedDate(_date)
        onDateClick(_date)() // onDateClick returns a function.
    }
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
    return <>
    <tbody>
        {activeRecurringDates.map( ( date, dateIndex ) => {
            let _date = moment(moment(date).format("YYYY-MM-DD")) // looped date.
            let _selectedDateTime = null;
            if (paxSelectorData.selectedDateTime) {
                _selectedDateTime = moment(moment(paxSelectorData.selectedDateTime).format("YYYY-MM-DD"));
            }
            let _pricingIds = getPricingsByDate(moment(date.start_date).toDate(), date.id);

            let firstPricingId = _pricingIds[0];
            let firstPricing = pricings[firstPricingId]

            let firstCategories = firstPricing.categories
            let firstCounts = {}
            firstCategories.forEach(c => {
                firstCounts = { ...firstCounts, [c.id]: parseInt(c.default_pax) || 0 }
            })
            let pricingOptions = []
            if ( 'undefined' != typeof _pricingIds.length && _pricingIds.length ) {
                pricingOptions = _pricingIds.map( (pricingId, pricingIndex) => {
                    return { label: pricings[pricingId].title, value: pricingId }
                })
            }
            return <>
                { isTourDate(new Date( _date ) ) ? 
                 
                    <tr key={dateIndex}>
                        <td>
                            { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                <>
                                <RadioControl
                                    // label="User type"
                                    // help="The type of the current user"
                                    selected={paxSelectorData.selectedPricingId}
                                    options={ pricingOptions}
                                    onChange={ ( e ) => { 
                                        handleFixedDeparturePricingClick(_date, date.id, e )()
                                    } } />
                                {/* <ul>
                                    {_pricingIds.map( (pricingId, pricingIndex) => {
                                        return <li key={pricingIndex}>

                                                    {pricings[pricingId].title}
                                                    
                                                    <button 
                                                        disabled={paxSelectorData.selectedPricingId == pricingId && paxSelectorData.selectedPricingId == paxSelectorData.pricing.id && paxSelectorData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) }
                                                        className={paxSelectorData.selectedPricingId == pricingId ? 'active' : '' }
                                                        onClick={handleFixedDeparturePricingClick(_date, date.id, pricingId )} >
                                                    </button>
                                            </li>
                                    })}
                                </ul> */}
                                </>
                            }
                        </td>
                        <td>
                        {
                        !paxSelectorData.pricingUnavailable && paxSelectorData.pricing && paxSelectorData.inventory.find(i => i.pax_available > 0 && paxSelectorData.selectedPricingId == paxSelectorData.pricing.id && paxSelectorData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) ) ? 
                            
                            <PaxSelector
                                pricing={paxSelectorData.pricing ? paxSelectorData.pricing : firstPricing }
                                onPaxChange={paxSelectorData.onPaxChange}
                                counts={paxSelectorData.counts ? paxSelectorData.counts : firstCounts }
                                inventory={paxSelectorData.inventory}
                            />
                            : <Disabled>
                                {/* Just to display */}
                                <PaxSelector
                                    pricing={ firstPricing }
                                    onPaxChange={paxSelectorData.onPaxChange}
                                    counts={firstCounts }
                                    inventory={paxSelectorData.inventory}
                                    />
                            </Disabled>

                        }
                        </td>
                        <td className="row">
                            <div className="date-time-wrapper">
                                {_date.format("YYYY-MM-DD")}
                            </div>
                            {
                                !paxSelectorData.pricingUnavailable && paxSelectorData.nomineeTimes.length > 0 && paxSelectorData.inventory.find(i => i.pax_available > 0 && paxSelectorData.selectedPricingId == paxSelectorData.pricing.id && paxSelectorData.selectedDateIds.includes(data.id) && _date.isSame( _selectedDateTime ) ) &&
                                    <> 
                                    <TripTimesListing
                                        selected={paxSelectorData.selectedDateTime}
                                        onTimeSelect={paxSelectorData.onTimeSelect}
                                        options={paxSelectorData.nomineeTimes}
                                    />
                                    </>
                            }
                            
                       </td>
                        {/* <td><button onClick={handleDateClick(_date)}>{_wp_travel.strings.bookings.book_now}</button></td> */}
                    </tr>
                    :
                    <>
                        <tr>
                            <td>
                                <Disabled>
                                    { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                        <>
                                        <RadioControl
                                            // label="User type"
                                            // help="The type of the current user"
                                            selected={paxSelectorData.selectedPricingId}
                                            options={ pricingOptions}
                                            onChange={ ( e ) => { 
                                                handleFixedDeparturePricingClick(date.start_date, date.id, e )()
                                            } }
                                        />
                                        {/* <ul>
                                            {_pricingIds.map( (pricingId, pricingIndex) => {
                                                return <li key={pricingIndex}>
                                                        <button onClick={handleFixedDeparturePricingClick(_date, date.id, pricingId )} >{pricings[pricingId].title}</button>
                                                    </li>
                                            })}
                                        </ul> */}
                                        </>
                                    }
                                </Disabled>
                            </td>
                            <td>
                                <Disabled>
                                    
                                    <PaxSelector
                                        pricing={ firstPricing }
                                        onPaxChange={paxSelectorData.onPaxChange}
                                        counts={firstCounts }
                                        inventory={paxSelectorData.inventory}
                                    />
                                </Disabled>
                            </td>
                            <td><Disabled>{_date.format("YYYY-MM-DD")}</Disabled></td>
                            <td><Disabled></Disabled></td>
                            {/* <td><Disabled><button onClick={handleDateClick(_date)}>{_wp_travel.strings.bookings.book_now}</button></Disabled></td> */}
                        </tr>
                    
                    </>
               
                }
            </>
            
        })}
        </tbody> 
        <div className="wp-travel-recurring-dates-nav-btns">
            {activePage > 1 && <button onClick={loadMoreDates(-1)} className="prev">{__('Previous')}</button>}
            {activePage < pagesCount && activePage >= 1 && <button className="next" onClick={loadMoreDates(1)}>{__('Next')}</button>}
            { ( activePage >= pagesCount && activeRecurringDates.length >= datePerPage ) && <button onClick={loadMoreDates(1)} className="show-more">{__('Load More...')}</button>}
        </div>
    </>
}

const DatesListing = ({ dates, onDateClick, isTourDate, getPricingsByDate, allData, onFixedDeparturePricingSelect, paxSelectorData, getPricingTripTimes }) => {
    const handleClick = ( date, date_id ) => () => {
        if (typeof onDateClick === 'function') {
            // console.log( 'Book now click step 1' );
            onDateClick(moment(date).toDate(), date_id)

            // Temp fixes[scroll to pricing if book now is clicked]
            var top = jQuery('#wp-travel-booking-recurring-dates').offset().top
            jQuery('html, body').animate({
                scrollTop: ( top - 20 )
            }, 1200);
        }
    }

    const handlePricingClick = ( date, date_id, pricingId ) => () => {

        if (typeof onFixedDeparturePricingSelect === 'function') {
            onFixedDeparturePricingSelect(moment(date).toDate(), date_id, pricingId )
        }
    }

    const _dates = Object.values(dates)
    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' != d.start_date && new Date( d.start_date )  > new Date() } )
    let pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id); // All Pricings.
    let times = getPricingTripTimes(paxSelectorData.selectedPricingId, [])
    return <>
        {
            _dates.length > 0 ? <>

                <div className="wptravel-recurring-dates">
                    <div className="wptravel-recurring-table">
                        {nonRecurringDates.length > 0 &&
                        <table>
                            <thead>
                                <tr>
                                    <th data-label="pricings">Pricings</th>
                                    <th data-label="person">Person</th>
                                    <th data-label="date">Date</th>
                                    {/* <th>{_wp_travel.strings.bookings.action}</th> */}
                                </tr>
                                </thead>
                                <tbody>
                                    {
                                        nonRecurringDates.map((date, index) => {
                                            let _pricingIds = getPricingsByDate(moment(date.start_date).toDate(), date.id);
                                            let firstPricingId = _pricingIds[0];
                                            let firstPricing = pricings[firstPricingId]

                                            let firstCategories = firstPricing.categories
                                            let firstCounts = {}
                                            firstCategories.forEach(c => {
                                                firstCounts = { ...firstCounts, [c.id]: parseInt(c.default_pax) || 0 }
                                            })

                                            let pricingOptions = []
                                            if ( 'undefined' != typeof _pricingIds.length && _pricingIds.length ) {
                                                pricingOptions = _pricingIds.map( (pricingId, pricingIndex) => {
                                                    return { label: pricings[pricingId].title, value: pricingId }
                                                })
                                            }
                
                                            return <>
                                                {! date.is_recurring && 
                                                    <>
                                                    <tr key={index}>
                                                        <td data-label="pricings">
                                                            { 'undefined' != typeof _pricingIds.length && _pricingIds.length > 0 &&
                                                            <>
                                                                <RadioControl
                                                                    className="test"
                                                                    // label="User type"
                                                                    // help="The type of the current user"
                                                                    selected={paxSelectorData.selectedPricingId}
                                                                    options={ pricingOptions}
                                                                    onChange={ ( e ) => { 
                                                                        handlePricingClick(date.start_date, date.id, e )()
                                                                    } }
                                                                />
                                                                {/* <ul>
                                                                    {_pricingIds.map( (pricingId, pricingIndex) => {
                                                                        return <li key={pricingIndex}>
                                                                            <button
                                                                                disabled={paxSelectorData.selectedPricingId == pricingId}
                                                                                className={paxSelectorData.selectedPricingId == pricingId ? 'active' : '' }
                                                                                onClick={ 
                                                                                    handlePricingClick(date.start_date, date.id, pricingId )
                                                                                } >
                                                                                {pricings[pricingId].title}
                                                                            </button>
                                                                        </li>
                                                                    })}
                                                                </ul> */}
                                                            </>
                                                            }
                                                        </td>
                                                        <td data-label="person">
                                                        {
                                                            !paxSelectorData.pricingUnavailable && paxSelectorData.pricing && paxSelectorData.inventory.find(i => i.pax_available > 0 && paxSelectorData.selectedPricingId == paxSelectorData.pricing.id && paxSelectorData.selectedDateIds.includes(date.id) ) ? 
                                                                <PaxSelector
                                                                    pricing={paxSelectorData.pricing ? paxSelectorData.pricing : firstPricing }
                                                                    onPaxChange={paxSelectorData.onPaxChange}
                                                                    counts={paxSelectorData.counts ? paxSelectorData.counts : firstCounts }
                                                                    inventory={paxSelectorData.inventory}
                                                                />
                                                                : <Disabled>
                                                                    {/* Just to display */}
                                                                    <PaxSelector
                                                                        pricing={ firstPricing }
                                                                        onPaxChange={paxSelectorData.onPaxChange}
                                                                        counts={firstCounts }
                                                                        inventory={paxSelectorData.inventory}
                                                                        />
                                                                </Disabled>

                                                        }
                                                        

                                                        </td>
                                                        <td data-label="date">
                                                            <div className="date-time-wrapper">
                                                                {date.start_date}
                                                                -
                                                                {date.end_date && '0000-00-00' != date.end_date ? moment(date.end_date).format('YYYY-MM-DD') : 'N/A' }
                                                            </div>
                                                            { !paxSelectorData.pricingUnavailable && paxSelectorData.nomineeTimes.length > 0 && paxSelectorData.selectedPricingId == paxSelectorData.pricing.id && paxSelectorData.selectedDateIds.includes(date.id) &&
                                                                <> 
                                                                <TripTimesListing
                                                                    selected={paxSelectorData.selectedDateTime}
                                                                    onTimeSelect={paxSelectorData.onTimeSelect}
                                                                    options={paxSelectorData.nomineeTimes}
                                                                />
                                                                </>
                                                            }
                                                        </td>
                                                        {/* <td>
                                                            <button className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date, date.id)}>
                                                            {_wp_travel.strings.bookings.book_now}
                                                            </button>
                                                        </td> */}
                                                    </tr>
                                                    </>
                                                }
                                            </>
                                        })
                                    }
                                
                        </tbody>
                        </table>
                        }
                    </div>
                    {/* Recurring */}
                    {_dates.map((date, index) => {
                        return <>
                            { date.is_recurring && 
                                <PanelBody title={__( `${_wp_travel.strings.bookings.recurring} ${date.title}`, 'wp-travel' )} initialOpen={true} key={index} >
                                    <PanelRow>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Pricings</th>
                                                <th>Person</th>
                                                <th>Date</th>
                                                {/* <th>{_wp_travel.strings.bookings.action}</th> */}
                                            </tr>
                                        </thead>
                                        <RecurringDates data={date} onDateClick={handleClick} onFixedDeparturePricingSelect={handlePricingClick} getPricingsByDate={getPricingsByDate} isTourDate={isTourDate} allData={allData} paxSelectorData={paxSelectorData} key={index} />
                                        
                                    </table>
                                    </PanelRow>
                                </PanelBody>
                            }
                            </>
                        })
                    }
                    <div id="wp-travel-booking-recurring-dates"></div> {/* <!-- required for scroll pricing --> */}
                </div>
            </> : <> Please add date.</>
        }
    </>
}

export default DatesListing