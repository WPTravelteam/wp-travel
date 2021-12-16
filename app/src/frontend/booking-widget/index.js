import { useSelect, dispatch } from '@wordpress/data';
import { forwardRef, useEffect, render, lazy, Suspense } from '@wordpress/element';
import {  applyFilters } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
import ErrorBoundary from './ErrorBoundry';
import {DEFAULT_BOOKING_STATE} from './_Store'; // Note: This store content 2 stores one for trip data and another for selected info for booking.

// Additional lib
const _ = lodash;
import RDP_Locale from './Locale'
import DatePicker, {registerLocale} from "react-datepicker";
registerLocale( "DPLocale", RDP_Locale() );
import moment from 'moment';
import RRule from "rrule";

/**
 * It is important to set global var before any imports.
 * https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
 */
__webpack_public_path__ = _wp_travel.build_path;
const initialState = DEFAULT_BOOKING_STATE(); // just to reset purpose.
const __i18n = {
	..._wp_travel.strings
}

const BookingWidget = lazy(() => import("./BookingWidget"));
import DatesListing from "./sub-components/DatesListing";

// Store Names.
const storeName        = 'WPTravelFrontend/BookingWidget';
const bookingStoreName = 'WPTravel/Booking';

const WPTravelBookingWidget = ( props ) => {
	const bookingState      = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateBooking } = dispatch( bookingStoreName );
	
    const forceCalendarDisplay = 'undefined' !== typeof props.forceCalendarDisplay ? props.forceCalendarDisplay : false;
    const calendarInline = 'undefined' !== typeof props.calendarInline ? props.calendarInline : false;

    const { selectedDate,
		selectedTripDate,
		selectedTime,
		nomineePricings,
		nomineeTimes,
		selectedPricing,
		selectedDateTime,
		excludedDateTimes,
		rruleAll,
		paxCounts,
		tripExtras,
		inventory,
		isLoading,
		pricingUnavailable,
		tempExcludeDate } = bookingState;
    const updateState = data => {
		updateBooking({ ...bookingState, ...data });
    }
	const renderLoader = () => <div className="loader"></div>;
    const allData = useSelect((select) => {
        return select(storeName).getAllStore()
    }, []);

    // Pricing and dates data.
	const pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id)
	const _dates   = 'undefined' !== typeof allData.tripData.dates && allData.tripData.dates.length > 0 ? allData.tripData.dates : [];

	// conditional.
	const isFixedDeparture   = allData.tripData.is_fixed_departure || false
	const isInventoryEnabled = allData.tripData.inventory && allData.tripData.inventory.enable_trip_inventory === 'yes'

	// Additional data.
	const datesById           = _.keyBy(_dates, d => d.id)
	const duration            = allData.tripData.trip_duration.days && parseInt(allData.tripData.trip_duration.days) || 1
	const _excludedDatesTimes = allData.tripData.excluded_dates_times && allData.tripData.excluded_dates_times.length > 0 && allData.tripData.excluded_dates_times || []
	let excludedDates = []
	useEffect(() => {
		// if (!selectedDateTime) {
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
			excludedDates = excludedDates.map(ed => ed.start_date)
				updateState({
					tempExcludeDate:excludedDates
				});
		// }
	}, [selectedDateTime])

	useEffect(() => { // If No Fixed departure set all pricings.
		if (!isFixedDeparture) {
			updateState({ nomineePricings: Object.keys(pricings) })
			isLoading && updateState({ isLoading: false })
		}
	}, [selectedDate])

	useEffect(() => {
		if (!selectedPricing) {
			return
		}
		let _state = {
			pricingUnavailable: false
		}
		let times = getPricingTripTimes(selectedPricing, selectedTripDate)
		if ( isLoading ) { // Prevent looping request.

			if (isInventoryEnabled && isFixedDeparture) {
				setInventoryData(selectedPricing, selectedDate, times)
			} else {
				let pricing = pricings[selectedPricing]
				let categories = pricing.categories
				let _paxCounts = {}
				categories.forEach(c => {
					_paxCounts = { ..._paxCounts, [c.id]: parseInt(c.default_pax) || 0 }
				})
	
				let maxPax = pricing.max_pax || 999
				let _tripExtras = {}
	
				if (pricing.trip_extras.length > 0) {
					pricing.trip_extras.forEach(x => {
						_tripExtras = { ..._tripExtras, [x.id]: x.is_required ? 1 : 0 }
					})
				}
	
				_state = {
					..._state,
					isLoading: false,
					inventory: [{
						'date': moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'),
						'pax_available': maxPax,
						'booked_pax': 0,
						'pax_limit': maxPax,
	
					}],
					nomineeTimes: [],
					tripExtras: _tripExtras,
					paxCounts: _paxCounts
				}
	
				if (times.length > 0) {
					let _times = times
						.map(time => {
							return moment(`${selectedDate.toDateString()} ${time}`)
						})
						.filter(time => {
							if (excludedDateTimes.find(et => moment(et).isSame(time))) {
								return false
							}
							return true
						})
					_state = {
						..._state,
						selectedDateTime: _times[0].toDate(),
						selectedTime: _times[0].format('HH:mm'),
						nomineeTimes: _times,
						inventory: [{
							'date': _times[0].format('YYYY-MM-DD[T]HH:mm'),
							'pax_available': maxPax,
							'booked_pax': 0,
							'pax_limit': maxPax,
						}],
					}
	
				}
			}
		}
		updateState(_state)
	}, [selectedPricing, selectedDateTime])
	useEffect(() => {
		if (nomineePricings.length === 1) {
			handlePricingSelect(nomineePricings[0])
		}
	}, [selectedDate])

	const generateRRule = (data, startDate) => {
		// let _startDate = moment(data.start_date)
		let ruleArgs = {
			freq: RRule.DAILY,
			dtstart: startDate.toDate(),
			until: startDate.toDate(),
		};
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

	// Date param need to have only Y-M-D date without time.
	const isTourDate = date => {
		if (moment(date).isBefore(moment(new Date())))
			return false
		if (!isFixedDeparture)
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

		if (tempExcludeDate.includes(startDate.format('YYYY-MM-DD'))) {
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
				if ( ! applyFilters( 'wpTravelRecurringCutofDateFilter', true, dateRules, allData.tripData, date, data )  ) { // @since 4.3.1
					return
				}
				return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
			}
			if (data.start_date) {
				if ( ! applyFilters( 'wpTravelCutofDateFilter', true, allData.tripData, date, data )  ) { // @since 4.3.1
					return
				}
				return moment(date).isSame(moment(data.start_date))
				
			}
			return false
		})
		return _date && 'undefined' !== typeof _date.id
		// }
	}

	const dayClicked = ( date, date_id ) => {
		if (!isFixedDeparture) {
			updateState({
				pricingUnavailable: false,
				selectedDate: date,
				selectedDateTime: date,
				isLoading: true,
			})
			return
		}

		updateState({
			...initialState,
			excludedDateTimes,
			isLoading: true,
			pricingUnavailable: false,
		})

		let _state = {
			selectedDate: date,
			selectedDateTime: date,
		}

		// @todo use getPricingsByDate function below to get pricing ids/_nomineePricings.
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
		// let startDate = moment(new Date(date));

		const _dateIds = _dates // Trip Date IDs matches to selected date.
			.filter(_date => {
				if (
					_date.is_recurring 
					&& ( ( 'undefined' == typeof date_id && 'dates' === tripDateListing ) || 'calendar' === tripDateListing ) ) { // Temp fixes of going inside all loop. date_id is not available in onclick event of recurring date so checking date id to prevent go inside if clicked non recuring date. 
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
				return moment(_date.start_date).isSame(moment(date))
			}).map(d => d.id)

		let _nomineePricings = _dateIds.map(id => datesById[id].pricing_ids.split(',').map(id => id.trim()))

		_nomineePricings = _.chain(_nomineePricings).flatten().uniq().value().filter(p => p != '' && typeof pricings[p] !== 'undefined')

		if (_nomineePricings.length <= 0) {
			_state = { ..._state, pricingUnavailable: true }
		} else {
			_state = { ..._state, nomineePricings: _nomineePricings }
		}

		_state = { ..._state, selectedTripDate: _dateIds, isLoading: false }

		updateState(_state)
	}

	// Function used to get pricings in Fixed departure listing.
	const getPricingsByDate = ( date, date_id, returnDateIds ) => {
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
		// let startDate = moment(new Date(date));

		const _dateIds = _dates // Trip Date IDs matches to selected date.
			.filter(_date => {
				if (
					_date.is_recurring 
					&& ( ( 'undefined' == typeof date_id && 'dates' === tripDateListing ) || 'calendar' === tripDateListing ) ) { // Temp fixes of going inside all loop. date_id is not available in onclick event of recurring date so checking date id to prevent go inside if clicked non recuring date. 
					if (_date.end_date) {
						if (moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isAfter(moment(_date.end_date))) {
							return false
						}
					}

					let dateRules = generateRRule(_date, startDate);
					return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
				}
				if (_date.start_date) {
					return moment(date).isSame(moment(_date.start_date))
				
				}
				return moment(_date.start_date).isSame(moment(date))
			}).map(d => d.id)

		if ( ! returnDateIds ) { // return pricings
			let _nomineePricings = _dateIds.map(id => datesById[id].pricing_ids.split(',').map(id => id.trim()))
			_nomineePricings = _.chain(_nomineePricings).flatten().uniq().value().filter(p => p != '' && typeof pricings[p] !== 'undefined')
			return _nomineePricings;
		}
		return _dateIds;
	}

	// For fixed departure listing.
	const handleFixedDeparturePricingSelect = ( date, date_id, pricingId ) => {
		if (!isFixedDeparture) {
			updateState({
				pricingUnavailable: false,
				selectedDate: date,
				selectedDateTime: date,
				isLoading: true,
			})
			return
		}
		updateState({
			...initialState,
			excludedDateTimes,
			isLoading: true,
			pricingUnavailable: false,
		})

		let _state = {
			selectedDate: date,
			selectedDateTime: date,
		}

		let _dateIds = getPricingsByDate( date, date_id, true );
		let _nomineePricings = _dateIds.map(id => datesById[id].pricing_ids.split(',').map(id => id.trim()))
			_nomineePricings = _.chain(_nomineePricings).flatten().uniq().value().filter(p => p != '' && typeof pricings[p] !== 'undefined')
		if (_nomineePricings.length <= 0) {
			_state = { ..._state, pricingUnavailable: true }
		} else {
			_state = { ..._state, nomineePricings: _nomineePricings }
		}

		_state = { ..._state, selectedTripDate: _dateIds, isLoading: true, selectedPricing:pricingId }

		updateState(_state)
		// end of date update in state
	}

	const handleTimeClick = time => () => {
		updateState({ selectedDateTime: time.toDate(), selectedTime: time.format('HH:mm') })
	}

	const handlePaxChange = (id, value) => e => {
		let pricing = pricings[selectedPricing]
		let category = pricing.categories.find(c => c.id === id)
		let count = paxCounts[id] + value < 0 ? 0 : paxCounts[id] + value

		let _inventory = inventory.find(i => i.date === moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'))
		let maxPax = _inventory && _inventory.pax_available
		// }
		if (maxPax >= 1) {
			let _totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
			if (_totalPax + value > parseInt(maxPax)) {
				if (e.target.parentElement.querySelector('.error'))
					return
				let em = document.createElement('em')
				em.classList.add('error')
				em.textContent = __i18n.bookings.max_pax_exceeded
				e.target.parentElement.appendChild(em)
				setTimeout(() => {
					em.remove()
				}, 1000)
				return
			} else {
				e.target.parentElement.querySelector('.error') && e.target.parentElement.querySelector('.error').remove()
			}
		}

		updateState({ paxCounts: { ...paxCounts, [id]: count } })
	}

	const getPricingTripTimes = (pricingId, selectedTripdates) => {
		let trip_time = selectedTripdates.map(td => {
			let date = datesById[td]
			if (date.pricing_ids && date.pricing_ids.split(',').includes(pricingId)) {
				let times = date.trip_time && date.trip_time.split(',') || []
				times = applyFilters( 'wpTravelCutofTimeFilter', times, allData.tripData, selectedDateTime )  // @since 4.3.1
				return times;
			}
			return []
		})
		return _.chain(trip_time).flatten().uniq().value()

	}

	const setInventoryData = (pricingId, date, times) => {
		apiFetch({
			url: `${_wp_travel.ajax_url}?action=wp_travel_get_inventory&pricing_id=${pricingId}&trip_id=${allData.tripData.id}&selected_date=${moment(date).format('YYYY-MM-DD')}&times=${times.join()}&_nonce=${_wp_travel._nonce}`
		})
			.then(res => {
				if (res.success && res.data.code === 'WP_TRAVEL_INVENTORY_INFO') {
					if (res.data.inventory.length <= 0) {
						return
					}
					let _times = res.data.inventory.filter(inventory => {
						if (inventory.pax_available > 0) {
							if (excludedDateTimes.find(et => moment(et).isSame(moment(inventory.date)))) {
								return false
							}
							return true
						}
						return false
					}).map(inventory => {
						return moment(inventory.date)
					})
					let _state = {
						isLoading: false
					}

					let _tripExtras = {}
					let pricing = pricings[pricingId]
					if (pricing.trip_extras.length > 0) {
						pricing.trip_extras.forEach(x => {
							_tripExtras = { ..._tripExtras, [x.id]: x.is_required ? 1 : 0 }
						})
						_state = { ..._state, tripExtras: _tripExtras }
					}

					let categories = pricing.categories
					let _paxCounts = {}
					categories.forEach(c => {
						_paxCounts = { ..._paxCounts, [c.id]: parseInt(c.default_pax) || 0 }
					})
					_state = { ..._state, paxCounts: _paxCounts }

					_state = _times.length > 0 && { ..._state, selectedDateTime: _times[0].toDate(), selectedTime: _times[0].format('HH:mm') } || _state
					_state = times.length > 0 && { ..._state, nomineeTimes: _times } || _state
					// if (excludedDateTimes.length > 0 && _times.length <= 0) { // Why??
					// 	_state = { ..._state, pricingUnavailable: true }
					// }
					if (_times.length <= 0) { // Why??
						_state = { ..._state, pricingUnavailable: true }
					}
					_state = res.data.inventory.length > 0 && { ..._state, inventory: res.data.inventory } || { ..._state, pricingUnavailable: true }
					updateState(_state)
				}
			})
	}

	const handlePricingSelect = (id) => {
		let _state = {
			isLoading: true,
			selectedPricing:id
		}
		updateState( _state )
	}

	useEffect(() => {
		jQuery('.wti__selector-item.active').find('.wti__selector-content-wrapper').slideDown();
	}),[];


	// Calendar Data starts.
	const DatePickerBtn = forwardRef(({ value, onClick }, ref) => (
		!_wp_travel.itinerary_v2 ? 
		<button className="wp-travel-date-picker-btn" onClick={onClick}>
			{selectedDate ? !isFixedDeparture && `${moment(selectedDate).format('MMM D, YYYY')} - ${moment(selectedDate).add(duration - 1, 'days').format('MMM D, YYYY')}` || moment(selectedDate).format('MMM D, YYYY') : __i18n.bookings.date_select}
			<span>
				<svg enableBackground="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m446 40h-46v-24c0-8.836-7.163-16-16-16s-16 7.164-16 16v24h-224v-24c0-8.836-7.163-16-16-16s-16 7.164-16 16v24h-46c-36.393 0-66 29.607-66 66v340c0 36.393 29.607 66 66 66h380c36.393 0 66-29.607 66-66v-340c0-36.393-29.607-66-66-66zm-380 32h46v16c0 8.836 7.163 16 16 16s16-7.164 16-16v-16h224v16c0 8.836 7.163 16 16 16s16-7.164 16-16v-16h46c18.748 0 34 15.252 34 34v38h-448v-38c0-18.748 15.252-34 34-34zm380 408h-380c-18.748 0-34-15.252-34-34v-270h448v270c0 18.748-15.252 34-34 34z"></path></g></svg>
			</span>
		</button>
		:
		<button className="wp-travel-date-picker-btn" onClick={onClick}>
			{selectedDate ? !isFixedDeparture && `${moment(selectedDate).format('MMM D, YYYY')} - ${moment(selectedDate).add(duration - 1, 'days').format('MMM D, YYYY')}` || moment(selectedDate).format('MMM D, YYYY') : __i18n.bookings.date_select}
			<span>
				<i className="far fa-calendar-alt"></i>
			</span>
		</button>
	))

	let _mindate = _.chain(_dates).sortBy(d => moment(d.start_date).unix()).value() || []
	_mindate = _mindate.find(md => moment(md.start_date).isAfter(moment(new Date())) || moment(md.start_date).isSame(moment(new Date())))

	let minDate = _mindate && moment(_mindate.start_date).toDate() || new Date();
	let maxDate = new Date( new Date().setFullYear(new Date().getFullYear() + 10 ));
	
	let params = {
		showMonthDropdown: true,
		customInput: <DatePickerBtn isFixedDeparture={isFixedDeparture} duration={duration} selectedDate={selectedDate} />,
		showYearDropdown: true,
		dropdownMode: "select",
		minDate: minDate,
		maxDate: maxDate,
		onChange: dayClicked,
		filterDate: isTourDate,
		locale:"DPLocale"
	}
	if (!isFixedDeparture) {
		delete params.filterDate
		params.minDate = new Date()
		params.startDate = selectedDate
		params.endDate = moment(selectedDate).add(duration - 1, 'days').toDate()
	}
	// calendar Data ends.

	let totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
	const tripDateListing = _wp_travel.trip_date_listing
	// Only used in fixed departure date listing.
	let componentData ={
		pricing: pricings[selectedPricing] || null,
		onPaxChange:handlePaxChange,
		counts:paxCounts,
		inventory:inventory,
		selectedPricingId:selectedPricing, // Additional Data.
		selectedDateIds:selectedTripDate, // Additional Data.
		selectedDateTime:selectedDateTime, // Additional Data.  for pax picker and time picker
		onTimeSelect:handleTimeClick, // For time picker
		nomineeTimes:nomineeTimes, // For time picker
		totalPax:totalPax, // For extras.
		tripExtras:tripExtras, // For extras.
		updateState:updateState, // For extras.
		isLoading:isLoading
	}


    let tripData = allData.tripData;
    const {
        pricing_type,
        custom_booking_type,
        custom_booking_link,
        custom_booking_link_text,
        custom_booking_form,
        custom_booking_link_open_in_new_tab,
        is_fixed_departure
    } = tripData;
    return <>
        <ErrorBoundary>
            {
                tripData && pricing_type === 'custom-booking' && <>
                    {
                        custom_booking_type == 'custom-link' ? <div className="wp-travel-custom-link">
                            <a href={custom_booking_link || '#'} target={custom_booking_link_open_in_new_tab ? 'new' : ''}>{custom_booking_link_text}</a>
                        </div> : custom_booking_type == 'custom-form' && <div className="wp-travel-custom-form" dangerouslySetInnerHTML={{__html: custom_booking_form || ''}}>
                        </div>
                    }
                </> || <ErrorBoundary>
					<Suspense fallback={renderLoader()}>
                        <div className="wp-travel-booking__header">
                            <h3>{__i18n.booking_tab_content_label}</h3>
                            {selectedDate && <button onClick={() => {
                            let _initialState = Object.assign(initialState)
                            if (!isFixedDeparture)
                                delete _initialState.nomineePricings
                            updateState(_initialState)
                        }}>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x={0} y={0} viewBox="0 0 36.9 41.7"><path
                                d="M35.8,17.1l-2.8,1c0.6,1.7,0.9,3.4,0.9,5.2c0,8.5-6.9,15.4-15.4,15.4C9.9,38.7,3,31.8,3,23.3C3,14.8,9.9,7.8,18.4,7.8
                                c1.3,0,2.6,0.2,3.9,0.5l-2.8,3l2.2,2L25,9.9l2.3-2.4L26,4.4L24.2,0l-2.8,1.1l1.8,4.3c-1.5-0.4-3.1-0.6-4.7-0.6
                                C8.3,4.8,0,13.1,0,23.3c0,10.2,8.3,18.4,18.4,18.4c10.2,0,18.4-8.3,18.4-18.4C36.9,21.2,36.5,19.1,35.8,17.1z">
                            </path>
                            </svg>
                            {__i18n.bookings.booking_tab_clear_all}</button>}

                        </div>
                            {
                                isFixedDeparture && 'dates' === tripDateListing && ! forceCalendarDisplay && 
                                    <div className="wp-travel-booking__content-wrapper">
                                        {/* <Suspense fallback={<Loader />}> */}
                                            <DatesListing {...{ dates: datesById, isTourDate, getPricingsByDate, allData, onFixedDeparturePricingSelect:handleFixedDeparturePricingSelect, componentData, getPricingTripTimes:getPricingTripTimes }} />
                                        {/* </Suspense> */}
                                    </div>
                                ||
                                <div className="wp-travel-booking__datepicker-wrapper">
                                    {/* <Suspense fallback={<Loader />}> */}
									{
										calendarInline ? <DatePicker inline {...params} /> : <DatePicker {...params} />
									}
                                        
                                        {!selectedDateTime && <p>{__i18n.bookings.date_select_to_view_options}</p> || null}
                                    {/* </Suspense> */}
                                </div>
                            }
                            <BookingWidget {...bookingState} initialState={initialState} handlePricingSelect={handlePricingSelect} handleTimeClick={handleTimeClick} handlePaxChange={handlePaxChange} updateState={updateState} /> {/* just a calendar */}
					</Suspense>
                </ErrorBoundary>
            }
        </ErrorBoundary>
    </>
}
let bookingWidgetElementId = _wp_travel.itinerary_v2 ? 'wti__booking' : 'booking';
if (document.getElementById(bookingWidgetElementId)) {
    render(<WPTravelBookingWidget />, document.getElementById(bookingWidgetElementId));
}

// For Block.
let blockId = 'wptravel-block-trip-calendar';
if (document.getElementById(blockId)) {
	let elem = document.getElementById(blockId);
	const props = Object.assign( {}, elem.dataset );
	const inline = undefined != typeof props.inline && props.inline;
	render(<WPTravelBookingWidget forceCalendarDisplay={true} calendarInline={inline} />, elem );
}
