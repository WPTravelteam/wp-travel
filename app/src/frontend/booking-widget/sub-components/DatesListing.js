import moment from 'moment'
import RRule from "rrule"
import { useMemo, useState, useRef, useEffect } from '@wordpress/element'
import { PanelBody, PanelRow } from '@wordpress/components';
import { __ } from '@wordpress/i18n'

const generateRRule = rruleArgs => {
    let rule = new RRule(rruleArgs);
    return rule.all()
}

const datePerPage = 5

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

const RecurringDates = ({ data, onDateClick }) => {
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
        {activeRecurringDates.map(date => {
            let _date = moment(moment(date).format("YYYY-MM-DD"))
            return <>
                <li>{_date.format("YYYY-MM-DD")}</li>
                <li></li>
                <li><button onClick={handleDateClick(_date)}>{_wp_travel.strings.bookings.book_now}</button></li>
            </>
        })}
        <div className="wp-travel-recurring-dates-nav-btns">
            {activePage > 1 && <button onClick={loadMoreDates(-1)} className="prev">{__('Previous')}</button>}
            {activePage < pagesCount && activePage >= 1 && <button className="next" onClick={loadMoreDates(1)}>{__('Next')}</button>}
            {activePage >= pagesCount && <button onClick={loadMoreDates(1)} className="show-more">{__('Load More...')}</button>}
        </div>
    </>
}

const DatesListing = ({ dates, onDateClick }) => {
    const handleClick = ( date, date_id ) => () => {
        if (typeof onDateClick === 'function') {
            // console.log( 'Book now click step 1' );
            onDateClick(moment(date).toDate(), date_id)

            // Temp fixes[scroll to pricing if book now is clicked]
            var top = jQuery('#wp-travel-booking-recurring-dates').offset().top
            jQuery('html, body').animate({
                scrollTop: top
            }, 1200);
        }
    }

    const _dates = Object.values(dates)
    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' != d.start_date && new Date( d.start_date )  > new Date() } )
    return <>
        {
            _dates.length > 0 ? <>

                <div className="fix-trip-detail">
                    {nonRecurringDates.length > 0 &&
                        <ol className="listing">
                            <li><strong>{_wp_travel.strings.bookings.start_date}</strong></li>
                            <li><strong>{_wp_travel.strings.bookings.end_date}</strong></li>
                            <li><strong>{_wp_travel.strings.bookings.action}</strong></li>
                            {
                                nonRecurringDates.map((date, index) => {
                                    return <>
                                        {! date.is_recurring && 
                                            <>
                                                <li>{date.start_date}</li>
                                                <li>
                                                    {date.end_date && '0000-00-00' != date.end_date ? moment(date.end_date).format('YYYY-MM-DD') : 'N/A' }
                                                </li>
                                                <li>
                                                    <button className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date, date.id)}>
                                                    {_wp_travel.strings.bookings.book_now}
                                                    </button>
                                                </li>
                                            </>
                                        }
                                    </>
                                })
                            }
                        </ol>
                    }
                    
                    {/* Recurring */}
                    {_dates.map((date, index) => {
                        return <>
                            { date.is_recurring && 
                                <PanelBody title={__( `Recurring: ${date.title}`, 'wp-travel' )} initialOpen={true} >
                                    <PanelRow>
                                    <ol className="listing">
                                        <li><strong>{_wp_travel.strings.bookings.start_date}</strong></li>
                                        <li><strong>{_wp_travel.strings.bookings.end_date}</strong></li>
                                        <li><strong>{_wp_travel.strings.bookings.action}</strong></li>
                                        <RecurringDates data={date} onDateClick={handleClick} key={index} />
                                    </ol>
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