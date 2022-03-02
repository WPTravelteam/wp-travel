import { useSelect } from '@wordpress/data';
import { applyFilters } from '@wordpress/hooks';

// Store Names.
const bookingStoreName = 'WPTravelFrontend/BookingData';

// Additional lib
import generateRRule from "./_GenerateRRule";


// Date param need to have only Y-M-D date without time.
const filteredTripDates = ( props ) => date => {
    const {tripData,bookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates
    } = tripData;
    const _dates = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];

    // Booking Data/state.
    const { selectedDate } = bookingData;

    if (moment(date).isBefore(moment(new Date())))
        return false
    // if ( moment( date ).isSame(moment( selectedDate ) ) ) {
    // 	return;
    // }
    if ( ! isFixedDeparture )
        return true
    let curretYear = date.getFullYear();
    let currentDate = date.getDate();
    let currentMonth = date.getMonth();

    // UTC Offset Fixes.
    let totalOffsetMin = new Date(date).getTimezoneOffset();
    let offsetHour = parseInt(totalOffsetMin/60);
    let offsetMin = parseInt(totalOffsetMin%60);

    let currentHours = 0;
    let currentMin = 0;
    if ( offsetHour > 0 ) {
        currentHours = offsetHour;
        currentMin = offsetMin;
    }
    let startDate = moment( new Date( Date.UTC(curretYear, currentMonth, currentDate, currentHours, currentMin, 0 ) ) ).utc();

    // Get all Trip Exclude Date 
    const _excludedDatesTimes = tripData.excluded_dates_times && tripData.excluded_dates_times.length > 0 && tripData.excluded_dates_times || []
    let excludedDates = [];
    excludedDates = _excludedDatesTimes
        .filter(ed => {
            if (ed.trip_time.length > 0) {
                let _times = ed.trip_time.split(',')
                let _datetimes = _times.map(t => moment(`${ed.start_date} ${t}`).toDate())
                if ( ! selectedDate || _excludedDatesTimes.includes(moment(ed.start_date).format('YYYY-MM-DD')) ) {
                    
                    excludedDateTimes.push(_datetimes[0]); // Temp fixes Pushing into direct state is not good.
                }
                return false
            }
            return true
        });
    
    // Seperated exclude date.
    excludedDates = excludedDates.map(ed => ed.start_date);
    // End of Get all Trip Exclude Date.

    if ( excludedDates.length > 0 && excludedDates.includes(startDate.format('YYYY-MM-DD'))) {
        return false
    }

    const _date = _dates.find(data => {
        if (data.is_recurring) {
            let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];

            if (data.end_date && moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isAfter(moment(data.end_date))) {
                return false
            }
            if (data.start_date && moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isBefore(moment(data.start_date))) {
                return false
            }
            if (selectedYears.length > 0 && !selectedYears.includes(startDate.year()))
                return false

            let dateRules = generateRRule(data, startDate)
            if ( ! applyFilters( 'wpTravelRecurringCutofDateFilter', true, dateRules, tripData, date, data )  ) { // @since 4.3.1
                return
            }
            return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
        } else if( data.start_date ) {
            if ( ! applyFilters( 'wpTravelCutofDateFilter', true, tripData, date, data )  ) { // @since 4.3.1
                return
            }
            return moment(date).isSame(moment(data.start_date))
        }
        return false
    })
    return _date && 'undefined' !== typeof _date.id
}
export { filteredTripDates }
