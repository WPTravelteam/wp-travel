import moment from 'moment'
import RRule from "rrule"
import { useMemo, useState, useRef, useEffect } from '@wordpress/element'

const generateRRule = rruleArgs => {
    let rule = new RRule(rruleArgs);
    return rule.all()
}

const generateRRUleArgs = data => {
    // let _startDate = moment(data.start_date)
    let startDate = data.start_date && new Date(data.start_date) || new Date()
    let ruleArgs = {
        freq: RRule.DAILY,
        count: 10,
        dtstart: new Date(Date.UTC(startDate.getFullYear(), startDate.getMonth(), startDate.getDate(), 0
            , 0, 0)),
    };
    if (!!data.end_date) {
        let endDate = new Date(data.end_date)
        ruleArgs.until = new Date(Date.UTC(endDate.getFullYear(), endDate.getMonth(), endDate.getDate(), 0
            , 0, 0))
    }

    // let rule = new RRule(ruleArgs);
    // return new RRule(ruleArgs).all();
    let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];

    if (selectedYears.length > 0 && !selectedYears.includes(startDate.year()))
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
    const [{ activePage, datesPerPage, pagesCount }, setPagination] = useState({
        activePage: 0,
        datesPerPage: 10,
        pagesCount: 0
    })
    useEffect(() => {
        if (!rruleArgs) {
            setRRuleArgs(generateRRUleArgs(data))
        }
    }, [data])

    useEffect(() => {
        if (!!rruleArgs) {
            let _dates = generateRRule(rruleArgs)
            setRecurringDates(_dates)
            setActiveRecurringDates(_dates)
            setPagination(state => ({ ...state, activePage: 1, pagesCount: 1 }))
        }
    }, [rruleArgs])
    // const dates = generateRecurringDates(data)
    console.debug(dates)
    const nextStartDate = dates.length > 0 && moment(dates[dates.length - 1]).add(1, 'days').toDate()

    const LoadMoreDates = page => () => {
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
        <div className="wp-travel-recurring-dates">
            <div className="wp-travel-recurring-date-picker-btn">
                {moment(dates[0]).format('MMM DD, YYYY')}
                <button className="btn" title={!!rruleArgs && new RRule(rruleArgs).toText() || 'Recurring Dates'} onClick={() => { showRecurringDatesToggle(!showRecurringDates) }}>
                    <svg viewBox="0 0 384.107 384.107" style={{ enableBackground: 'new 0 0 384.107 384.107' }}><g><g><g><polygon points="170.774,106.707 170.774,213.374 262.081,267.56 277.441,241.747 202.774,197.374 202.774,106.707" /><path d="M384.107,0.04l-58.347,59.947c-74.773-74.133-195.093-74.133-269.867,0c-74.88,74.133-74.347,194.347,0.533,268.48s196.48,74.133,271.36,0c37.547-37.227,56.32-92.053,56.32-134.293h-42.773c0.107,42.24-14.4,75.627-43.413,104.427c-58.24,57.707-152.533,57.707-210.773,0s-58.24-151.147,0-208.853s152.533-55.573,210.773,2.133l-58.56,60.16h144.747V0.04z" /></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                </button>
            </div>
            {
                showRecurringDates && <>
                    <div className="wp-travel-recurring-dates-wrapper">
                        <ul>
                            {activeRecurringDates.map(date => {
                                let _date = moment(moment(date).format("YYYY-MM-DD")).toDate()
                                return <li><button onClick={onDateClick(_date)}>{moment(_date).format()}</button></li>
                            })}
                        </ul>
                        {activePage > 1 && <button onClick={LoadMoreDates(-1)}>Previous</button>}
                        {activePage < pagesCount && activePage >= 1 && <button onClick={LoadMoreDates(1)}>Next</button>}
                        {activePage >= pagesCount && <button onClick={LoadMoreDates(1)}>Load More...</button>}
                    </div>
                </>
            }
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
            _dates.map((date, index) => {
                return <>
                    {date.is_recurring && <RecurringDates data={date} onDateClick={handleClick} key={index} />
                        ||
                        <button className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date)}>
                            {moment(date.start_date).format()}
                        </button>
                    }
                </>
            })
        }
    </>
}

export default DatesListing