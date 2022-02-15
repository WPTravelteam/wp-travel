import { forwardRef } from '@wordpress/element';
import {  applyFilters } from '@wordpress/hooks';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const _ = lodash;
import moment from 'moment';
import RDP_Locale from '../_Locale'
import DatePicker, {registerLocale} from "react-datepicker";
registerLocale( "DPLocale", RDP_Locale() );
import generateRRule from "../_GenerateRRule";


const CalendarView = ( props ) => {
	// Component Props.
	const { calendarInline, showTooltip, tooltipText, tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration
    } = tripData;
    const allPricings = pricings && _.keyBy( pricings, p => p.id )
    const _dates      = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById   = _.keyBy(_dates, d => d.id)
    const duration    = trip_duration.days && parseInt( trip_duration.days ) || 1;

    // Booking Data.
    const { selectedDate } = bookingData;

    // Just custom botton. There is no custom onclick event here.
    const DatePickerBtn = forwardRef( ( { value, onClick }, ref ) => (
		<button className="wp-travel-date-picker-btn" onClick={ onClick } >
			{ selectedDate ? ! isFixedDeparture && `${moment(selectedDate).format('MMM D, YYYY')} - ${moment(selectedDate).add(duration - 1, 'days').format('MMM D, YYYY')}` || moment(selectedDate).format('MMM D, YYYY') : __i18n.bookings.date_select}
			<span>
				<i className="far fa-calendar-alt"></i>
			</span>
		</button>
	));

    // Update Selected Trip date in store.
    const selectTripDate = ( date ) => {
		if ( ! isFixedDeparture ) {
			updateBookingData({
				pricingUnavailable: false,
				selectedDate: date,
				isLoading: true,
			})
			return
		}

        // Fixed Departure.
        let _nomineePricingIds = []; // Pricing ids as per selected date.
		let _bookingData = {
            isLoading: true,
			pricingUnavailable: false,
            selectedDate: date
        }

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
		let startDate = moment(new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), currentHours, currentMin, 0))).utc();

		const _dateIds = _dates // Trip Date IDs matches to selected date.
			.filter(_date => {
				if ( _date.is_recurring ) {
					if (_date.end_date) {
						if (moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isAfter(moment(_date.end_date))) {
							return false
						}
					}
					if (_date.start_date) {
						if (moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isBefore(moment(_date.start_date))) {
							return false
						}
					}
					let dateRules = generateRRule(_date, startDate);
					return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
				}
				if (_date.start_date) {
					return moment(date).isSame(moment(_date.start_date))
				}
				return moment( _date.start_date ).isSame(moment( date ) )
			}).map( d => d.id );

		_nomineePricingIds = _dateIds.map( id => datesById[id].pricing_ids.split(',').map( id => id.trim() ) )
		_nomineePricingIds = _.chain( _nomineePricingIds ).flatten().uniq().value().filter( p => p != '' && typeof allPricings[p] !== 'undefined' )

		if ( _nomineePricingIds.length <= 0 ) {
			_bookingData = { ..._bookingData, pricingUnavailable: true }
		} else {
			_bookingData = { ..._bookingData, nomineePricings: _nomineePricingIds }
		}

		if ( _nomineePricingIds.length === 1 ) {
			_bookingData = { ..._bookingData, selectedPricingId: _nomineePricingIds[0] }
		}
		_bookingData = { ..._bookingData, selectedDateIds: _dateIds, isLoading: false }
		updateBookingData( _bookingData  );
	}
    // Date param need to have only Y-M-D date without time.
	const filteredTripDates = date => {
		if (moment(date).isBefore(moment(new Date())))
			return false
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
		let startDate = moment(new Date(Date.UTC(curretYear, currentMonth, currentDate, currentHours, currentMin, 0))).utc();
		// let startDate = moment(new Date(date));

		// Get all Trip Exclude Date 
		const _excludedDatesTimes = tripData.excluded_dates_times && tripData.excluded_dates_times.length > 0 && tripData.excluded_dates_times || []
		let excludedDates = [];
		excludedDates = _excludedDatesTimes
			.filter(ed => {
				if (ed.trip_time.length > 0) {
					let _times = ed.trip_time.split(',')
					let _datetimes = _times.map(t => moment(`${ed.start_date} ${t}`).toDate())
					if ( !selectedDateTime || _excludedDatesTimes.includes(moment(ed.start_date).format('YYYY-MM-DD')) ) {
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
			}
			if (data.start_date) {
				if ( ! applyFilters( 'wpTravelCutofDateFilter', true, tripData, date, data )  ) { // @since 4.3.1
					return
				}
				return moment(date).isSame(moment(data.start_date))
				
			}
			return false
		})
		return _date && 'undefined' !== typeof _date.id
		// }
	}




    // Datepicker Params
    let _mindate = _.chain( _dates ).sortBy( d => moment( d.start_date).unix() ).value() || [];
	_mindate     = _mindate.find(md => moment(md.start_date).isAfter(moment(new Date())) || moment(md.start_date).isSame(moment(new Date())));

	let minDate = _mindate && moment(_mindate.start_date).toDate() || new Date();
	let maxDate = new Date( new Date().setFullYear(new Date().getFullYear() + 10 ));
    let params = {
		showMonthDropdown: true,
		customInput: <DatePickerBtn />, // Just button with custom html.
		showYearDropdown: true,
		dropdownMode: "select",
		minDate: minDate,
		maxDate: maxDate,
		onChange: selectTripDate,
		filterDate: filteredTripDates,
		locale:"DPLocale",
        startDate:null,
        endDate:null
	}
    if ( ! isFixedDeparture ) {
		delete params.filterDate;
		params.minDate   = new Date();
		params.startDate = selectedDate;
		params.endDate   = moment( selectedDate ).add( duration - 1, 'days' ).toDate();
	}
    // console.log('params', params);
    return <ErrorBoundary>
        { calendarInline ? <DatePicker inline { ...params }  /> : <DatePicker { ...params }  /> }
        { ! selectedDate && showTooltip && <p>{ tooltipText } </p> || null }
    </ErrorBoundary>
}
export default CalendarView;