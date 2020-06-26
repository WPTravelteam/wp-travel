import moment from 'moment'
import RRule from "rrule"

const generateRRule = (data) => {
    // let _startDate = moment(data.start_date)
    let startDate = data.start_date
    let endDate = data.end_date
    let ruleArgs = {
        freq: RRule.DAILY,
        count: 10,
        dtstart: moment(startDate).toDate(),
        until: moment(endDate).toDate(),
    };

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

    let rule = new RRule(ruleArgs);
    return rule.all()
}

const RecurringDates = ({ data, onClick }) => {
    let dates = generateRRule(data)
    console.debug(dates)
    return <select onChange={e => onClick(e.target.value)()}>
        {/* {dates.map(date => <option><button onChange={value => onClick(value)()}>{moment(date).format('MMM DD, YYYY')}</button></li>)} */}
        {dates.map(date => <option>{moment(date).utc().format('MMM DD, YYYY')}</option>)}
    </select>
}

const DatesListing = ({ dates, onChange, filterDate }) => {
    // console.debug(onChange, dates, filterDate)
    const handleClick = date => () => {
        if (typeof onChange === 'function') {
            onChange(moment(date).utc().toDate())
        }
    }
    const _dates = Object.values(dates)
    return <>
        {
            _dates.map((date, index) => {
                return <>
                    {
                        date.is_recurring
                        &&
                        <div className="wp-travel-recurring-dates"><div className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date)}>
                            {moment(date.start_date).format('MMM DD, YYYY')}
                            {date.is_recurring && <button className="btn" title="Recurring Date">
                                <svg viewBox="0 0 384.107 384.107" style={{ enableBackground: 'new 0 0 384.107 384.107' }}><g><g><g><polygon points="170.774,106.707 170.774,213.374 262.081,267.56 277.441,241.747 202.774,197.374 202.774,106.707" /><path d="M384.107,0.04l-58.347,59.947c-74.773-74.133-195.093-74.133-269.867,0c-74.88,74.133-74.347,194.347,0.533,268.48s196.48,74.133,271.36,0c37.547-37.227,56.32-92.053,56.32-134.293h-42.773c0.107,42.24-14.4,75.627-43.413,104.427c-58.24,57.707-152.533,57.707-210.773,0s-58.24-151.147,0-208.853s152.533-55.573,210.773,2.133l-58.56,60.16h144.747V0.04z" /></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                            </button>}
                        </div>
                            {date.is_recurring && <RecurringDates data={date} onClick={handleClick} />}
                        </div>
                        ||
                        <button className="wp-travel-recurring-date-picker-btn" key={index} onClick={handleClick(date.start_date)}>
                            {moment(date.start_date).format('MMM DD, YYYY')}
                        </button>
                    }
                </>
            })
        }
    </>
}

export default DatesListing