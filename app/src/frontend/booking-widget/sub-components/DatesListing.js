import moment from 'moment'
import RRule from "rrule"
import { useMemo, useState, useRef, useEffect } from '@wordpress/element'
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
        // until: new Date(Date.UTC(rruleStartDate.getFullYear(), rruleStartDate.getMonth(), rruleStartDate.getDate(), 0, 0, 0)),
    };

    if ( data.end_date  ) {
        let endDate = new Date(data.end_date)
        ruleArgs.until = endDate
    }
    rruleStartDate = moment( rruleStartDate ).utc();
    let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];
    // console.log( 'type ', typeof rruleStartDate )
    // console.log( 'start', rruleStartDate.year() )
    
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
                <li><button onClick={handleDateClick(_date)}>Book now</button></li>
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
    // const [recurringDates, setRecurringDates] = useState([])
    // console.debug(onChange, dates, filterDate)
    const handleClick = date => () => {
        if (typeof onDateClick === 'function') {
            onDateClick(moment(date).toDate())
        }
    }

    const _dates = Object.values(dates)
    return <>
        {
            _dates.length > 0 ? <>

                <div className="fix-trip-detail">
                    <ol className="listing">
                        
                        <li>
                             <strong>Start</strong>
                        </li>
                        <li>
                             <strong>End</strong>
                        </li>
                        <li>
                             <strong>Action</strong>
                        </li>
        
                        {
                            _dates.map((date, index) => {
                                return <>
                                    {! date.is_recurring && 
                                        <>
                                            <li>{date.start_date}</li>
                                            <li>
                                                {moment(date.end_date).format('YYYY-MM-DD')}
                                            </li>
                                            <li>

                                                <button className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date)}>
                                                    Book now
                                                </button>
                                            </li>
                                        </>
                                    }
                                </>
                            })
                        }
                    </ol>
                    {_dates.map((date, index) => {
                        return <>
                            <ol className="listing">
                                <li>
                                    <strong>Start</strong>
                                </li>
                                <li>
                                    <strong>End</strong>
                                </li>
                                <li>
                                    <strong>Action</strong>
                                </li>
                                { date.is_recurring && <RecurringDates data={date} onDateClick={handleClick} key={index} /> }
                            </ol>
                            </>
                        })
                    }
                               
                </div>

            
            </> : <> Please add date.</>
            
        }
    </>
}

export default DatesListing