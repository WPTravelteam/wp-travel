import { forwardRef, useEffect } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { Disabled } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
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

// WP Travel Functions.
import { objectSum } from '../_wptravelFunctions';
import { filteredTripDates } from '../_FilteredDates'; // Filter available dates in calendar.

// WP Travel Components.
import Pricings from './CalendarView/Pricings';
import TripTimes from './CalendarView/TripTimes';
import PaxSelector from './CalendarView/PaxSelector';
import InventoryNotice, { Notice } from '../_InventoryNotice';

const CalendarView = ( props ) => {
	// Component Props.
	const { calendarInline, showTooltip, tooltipText, tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration:tripDuration
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById          = _.keyBy(_dates, d => d.id)
    const duration           = tripDuration.days && parseInt( tripDuration.days ) || 1;
	const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    // Booking Data.
    const { selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;

	// Lifecycles. [ This will only trigger if pricing and time is selected or changed ]
    useEffect(() => {
		if ( ! selectedPricingId ) {
			return
		}
		let _bookingData = {
			pricingUnavailable:false
		};

		// after selecting pricing. need to check available time for selected pricing as well.
		let times = getPricingTripTimes( selectedPricingId, selectedDateIds );
		if ( times.length > 0 ) {
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
			_bookingData = {
				..._bookingData,
				nomineeTimes: _times,
			}
			// add selected trip time if nominee times length is one.
			if ( 1 === _times.length ) {
				_bookingData = {
					..._bookingData,
					selectedTime: _times[0].format('HH:mm'),
				}
			}
		} else {
			_bookingData = {
				..._bookingData,
				nomineeTimes: [],
				selectedTime: null,
			}
		}
		
		// Add default selected pax object values as 0 for all categories as per selected pricing. {'2':0,'3':0} where cat id 2, 3 have default 0 selected pax.
		const pricing = allPricings[selectedPricingId];
		let categories = pricing && pricing.categories || []
		let _paxCounts = {}
		categories.forEach(c => {
			_paxCounts = { ..._paxCounts, [c.id]: parseInt(c.default_pax) || 0 }
		})
		_bookingData = { ..._bookingData, paxCounts: _paxCounts }

		let maxPax               = pricing.max_pax || 999
		let tempSelectedDatetime = selectedDate;

		let selectedHour = 0;
		let selectedMin  = 0;
		if ( selectedTime ) { // if time is selected then the selectedDate must have time on it.
			const selectedDateTime = new Date( `${selectedDate.toDateString()} ${selectedTime}` );
			_bookingData = { ..._bookingData, selectedDate: selectedDateTime } // Updating date state with time if time is selected.
			selectedHour = selectedDateTime.getHours(); // Date object
			selectedMin  = selectedDateTime.getMinutes(); // Date object
		}
		tempSelectedDatetime.setHours( selectedHour )
		tempSelectedDatetime.setMinutes( selectedMin )
		
		// Fallback data for inventory.
		_bookingData = {
			..._bookingData,
			inventory: [{
				'date': moment(tempSelectedDatetime).format('YYYY-MM-DD[T]HH:mm'),
				'pax_available': maxPax,
				'booked_pax': 0,
				'pax_limit': maxPax,
			}],
		}
		if ( isInventoryEnabled ) {
			// This will also update above booking Data in store along with inventory data. updateBookingData is already called in setInventoryData function so need to return here.
			setInventoryData( _bookingData );
			return;
		}
		updateBookingData( _bookingData );
	}, [ selectedPricingId, selectedTime ])

	// functions.
	const getPricingTripTimes = ( pricingId, selectedTripdates ) => {
		let trip_time = selectedTripdates.map( td => {
			let date = datesById[td]
			if (date.pricing_ids && date.pricing_ids.split(',').includes(pricingId)) {
				let times = date.trip_time && date.trip_time.split(',') || []
				times = applyFilters( 'wpTravelCutofTimeFilter', times, tripData, selectedDate );
				return times;
			}
			return []
		})
		return _.chain(trip_time).flatten().uniq().value()
	}

	const setInventoryData = ( _bookingData ) => {
		let times = getPricingTripTimes( selectedPricingId, selectedDateIds );
		apiFetch({
			url: `${_wp_travel.ajax_url}?action=wp_travel_get_inventory&pricing_id=${selectedPricingId}&trip_id=${tripData.id}&selected_date=${moment(selectedDate).format('YYYY-MM-DD')}&times=${times.join()}&_nonce=${_wp_travel._nonce}`
		}).then(res => {
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
				});
				
				let _inventory_state = {}
				_inventory_state = times.length > 0 && { ..._inventory_state, nomineeTimes: _times } || { ..._inventory_state, nomineeTimes: [] }
				
				if (_times.length <= 0) {
					_inventory_state = { ..._inventory_state, pricingUnavailable: true }
				}
				_inventory_state = res.data.inventory.length > 0 && { ..._inventory_state, inventory: res.data.inventory } || { ..._inventory_state, pricingUnavailable: true }
				
				_bookingData = {..._bookingData, ..._inventory_state }
				updateBookingData( _bookingData );
			}
		})
	}

    // Just custom botton. There is no custom onclick event here.
    const DatePickerBtn = forwardRef( ( { value, onClick }, ref ) => (
		<button className="wp-travel-date-picker-btn" onClick={ onClick } >
			{ selectedDate ? ! isFixedDeparture && `${moment(selectedDate).format('MMM D, YYYY')} - ${moment(selectedDate).add(duration - 1, 'days').format('MMM D, YYYY')}` || moment(selectedDate).format('MMM D, YYYY') : __i18n.bookings.date_select}
			<span><i className="far fa-calendar-alt"></i></span>
		</button>
	));

    // Update Selected Trip date in store.
    const selectTripDate = ( date ) => {
		// Default or Trip duration.
		let _bookingData = {
			isLoading: true,
			pricingUnavailable: false,
			selectedDate: date,
			selectedPricingId:null,
			selectedPricing:null,
			selectedTime:null,
			nomineeTimes:[]
		}

		// Pricing ids as per selected date for fixed departure and all pricing for trip duration.
		let _nomineePricingIds = []; 

		// Fixed Departure.
		if ( isFixedDeparture ) {
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
			} else if ( _nomineePricingIds.length === 1 ) {
				let tempSelectedPricingId = _nomineePricingIds[0];
				let selectedPricing   = allPricings[ tempSelectedPricingId ].title;
				// if tempSelectedPricingId is equel to previously selected selectedPricingId then this date change will not trigger pricingid change effect. So we may not have time for selected date. so need to reset selected time here to trigger time change lifecycle to update nominee time and selected time here.
				if ( tempSelectedPricingId === selectedPricingId ) {
					_bookingData = { ..._bookingData, selectedTime: '00:00'  } // Quick hack to trigger time change effect.
				}
				_bookingData = { ..._bookingData, selectedPricingId: tempSelectedPricingId, selectedPricing:selectedPricing }
			}
			_bookingData = { ..._bookingData, selectedDateIds: _dateIds }
		} else {
			_nomineePricingIds = pricings && pricings.map( pricing => pricing.id );
		}

		_bookingData = { ..._bookingData, nomineePricingIds: _nomineePricingIds, isLoading: false } // nomineePricingIds
		updateBookingData( _bookingData  );
	}

    // Datepicker Params
    let _mindate = _.chain( _dates ).sortBy( d => moment( d.start_date).unix() ).value() || []; // Sort by date.
	// Finding min date
	_mindate = _mindate.find( md => 
		moment( md.start_date ).isAfter( moment(new Date() ) ) ||
		( moment( md.start_date ).isBefore( moment(new Date() ) ) && md.is_recurring ) || // @todo need to filter end date condition as well in recurring.
		moment( md.start_date ).isSame( moment(new Date() ) ) 
	);

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
    return <ErrorBoundary>
		<div className="wp-travel-booking__datepicker-wrapper">
			{ calendarInline ? <DatePicker inline { ...params }  /> : <DatePicker { ...params }  /> }
			{ ! selectedDate && showTooltip && <p>{ tooltipText } </p> || null }
		</div>
		{/* Pricing and Times are in pricing wrapper */}
		{ selectedDate && <>
			{ ( ! pricingUnavailable || nomineePricingIds.length > 1 ) &&
				<div className="wp-travel-booking__pricing-wrapper">
					<div className="wp-travel-booking__pricing-name"> 
						<Pricings { ...props } />
					</div>
					{ selectedPricingId && <TripTimes { ...props } /> }
				</div>
			}
			<div className="wp-travel-booking__pricing-wrapper wptravel-pax-selector">
				{ ! pricingUnavailable && selectedPricingId && <ErrorBoundary>
					{ nomineeTimes.length > 1 && ! selectedTime && <Disabled><PaxSelector { ...props } /></Disabled> || <PaxSelector { ...props } /> }
					{ _.size(allPricings[ selectedPricingId ].trip_extras) > 0 && objectSum( paxCounts ) > 0 && <ErrorBoundary> extras </ErrorBoundary> }
				</ErrorBoundary> }
				{ pricingUnavailable && 
					<Notice>
						{ tripData.inventory &&  tripData.inventory.enable_trip_inventory == 'yes'
							&& <InventoryNotice inventory={tripData.inventory} />
						}
					</Notice>
				}
			</div>
			</>
		}
    </ErrorBoundary>
}
export default CalendarView;
// @todo Extras component inside pax selector