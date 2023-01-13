import { DEFAULT_BOOKING_STATE } from '../store/_Store'; // Note: This store content 2 stores one for trip data and another for Booking Data.
import { forwardRef, useEffect, useState } from '@wordpress/element';
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
import { IsTourDate } from '../_IsTourDate'; // Filter available dates in calendar.

// WP Travel Components.
import Pricings from './CalendarView/Pricings';
import TripTimes from './CalendarView/TripTimes';
import PaxSelector from './CalendarView/PaxSelector';
import TripExtras from './CalendarView/TripExtras';
import InventoryNotice, { Notice } from '../_InventoryNotice';
const initialState = DEFAULT_BOOKING_STATE();

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
    const { isLoading, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, pricingUnavailable, selectedTime, nomineeTimes, paxCounts } = bookingData;

	// Temp/Local state to play with HTTP Request.
	const [_inventoryData, _setInventoryData] = useState([]);
	const [_nomineeTripExtras, _setNomineeTripExtras] = useState([]);

	// Lifecycles. [ This will only trigger if pricing is selected or changed ]
    useEffect(() => { // removed async to compatible with legacy.
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			pricingUnavailable:false,
			nomineeTimes: [],
			selectedTime: null,
		};
		// if ( ! isInventoryEnabled ) {
		// 	_bookingData.isLoading = false; // maybe not reqd.
		// }
		// after selecting pricing. need to check available time for selected pricing as well. Single pricing id case is already checked in date changes lifecycle below.
		bookingWidgetUseEffects( _bookingData, 'pricingChange' );
	}, [ selectedPricingId ]); 

	// Lifecycles. [ This will only trigger if time is selected or changed ]
    useEffect(() => {
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			pricingUnavailable:false,
		};
		// Note: This effect is same as date changes lifecycle effect.
		// after selecting pricing. need to check available time for selected pricing as well. Single pricing id case is already checked in date changes lifecycle below.
		bookingWidgetUseEffects( _bookingData, 'timeChange' );
	}, [ selectedTime ]);
	// Date changes Lifecycle. [Only For fixed Departure which have date id]
	useEffect(() => {
		// If date changes has selectedPricingId, that mean selected date has only one pricing. So nomineeTimes for single date is calculated from here.
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			nomineeTimes: [],
			selectedTime: null,
		};
		bookingWidgetUseEffects( _bookingData, 'dateChange' );
		
	}, [ selectedDateIds ]);

	// Lifecycles. [ This will only trigger if Inventory Data changed ]. Fix real time store update issue with trip time change.
    useEffect(() => {
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			isLoading:false,
			pricingUnavailable:false,
		};

		let times = getPricingTripTimes( selectedPricingId, selectedDateIds );
		let _inventory_state = {}
		if ( _inventoryData.length > 0 ) {
			// Add logic if inventory ajax is complete
			let _times = _inventoryData.filter(inventory => {
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

			_inventory_state = times.length > 0 && { ..._inventory_state, nomineeTimes: _times } || { ..._inventory_state, nomineeTimes: [] }
			if (_times.length <= 0) {
				_inventory_state = { ..._inventory_state, pricingUnavailable: true }
			} else if( 1 === _times.length ) {
				_inventory_state = {
					..._inventory_state,
					selectedTime: _times[0].format('HH:mm'),
				}
			}

			// Quick fix. Pax count data is overriding due to async setInventoryData so need to re assign here.
			// Add default selected pax object values as 0 for all categories as per selected pricing. {'2':0,'3':0} where cat id 2, 3 have default 0 selected pax.
			let pricing    = allPricings[selectedPricingId];
			let categories = pricing && pricing.categories || []
			let _paxCounts = {}
			categories.forEach(c => {
				_paxCounts = { ..._paxCounts, [c.id]: parseInt(c.default_pax) || 0 }
			});
			// End of pax count re assign here.
			_inventory_state = { ..._inventory_state, inventory: _inventoryData, paxCounts: _paxCounts }
		}
		_bookingData = {..._bookingData, ..._inventory_state }
		
		updateBookingData( _bookingData );
	}, [ _inventoryData ]); 

	// Lifecycles. [ This will only trigger if Extras Data changed ]. Fix real time store update issue with trip time change & single Pricing.
    useEffect(() => { // removed async to compatible with legacy.
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			pricingUnavailable:false,
		};
		if ( ! isInventoryEnabled ) {
			_bookingData.isLoading = false;  // IsLoading will be false if inventory is disable othrewise this will false after fetching inventory data. Fixes for trip duration with extras as well.
		}
		if ( _nomineeTripExtras.length > 0 ) {
			// Default extras values.
			let _tripExtras = {}
			_nomineeTripExtras.forEach(x => {
				_tripExtras = { ..._tripExtras, [x.id]: x.is_required ? 1 : 0 }
			})
			_bookingData = {..._bookingData, nomineeTripExtras:_nomineeTripExtras, tripExtras: _tripExtras }
		}
		
		updateBookingData( _bookingData );
	}, [ _nomineeTripExtras ]); 

	const bookingWidgetUseEffects = async ( _bookingData, effectType ) => {
		if ( selectedPricingId ) {
			if ( nomineePricingIds.length > 0 ) {
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
			// extras Calculation.
			if ( ! isInventoryEnabled ) {
				setTripExtrasData(effectType); // quick fix trip not displaying if no inventory. [price change effect is overriding extras Data.]
			} else {
				await setTripExtrasData(effectType);
			}
			
			if ( _nomineeTripExtras.length > 0 ) {
				// Default extras values.
				let _tripExtras = {}
				_nomineeTripExtras.forEach(x => {
					_tripExtras = { ..._tripExtras, [x.id]: x.is_required ? 1 : 0 }
				})
				_bookingData = {..._bookingData, nomineeTripExtras:_nomineeTripExtras, tripExtras: _tripExtras }
			}
			if ( isInventoryEnabled ) {
				let times = getPricingTripTimes( selectedPricingId, selectedDateIds );
				let _inventory_state = {isLoading:false }
				if ( _inventoryData.length > 0 ) {
					// Add logic if inventory ajax is complete
					let _times = _inventoryData.filter(inventory => {
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
					_inventory_state = times.length > 0 && { ..._inventory_state, nomineeTimes: _times } || { ..._inventory_state, nomineeTimes: [] }
					if (_times.length <= 0) {
						_inventory_state = { ..._inventory_state, pricingUnavailable: true }
					} else if( 1 === _times.length ) {
						// _inventory_state = {
						// 	..._inventory_state,
						// 	selectedTime: _times[0].format('HH:mm'),
						// }
					}
					_inventory_state = { ..._inventory_state, inventory: _inventoryData }
				}
				_bookingData = {..._bookingData, ..._inventory_state }
			} else {
				// _bookingData = {..._bookingData, isLoading:false }
			}
			_bookingData = {..._bookingData }
		}
		updateBookingData( _bookingData );
	}

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

	// HTTP Request Calls
	const setInventoryData = async () => {
		let times = getPricingTripTimes( selectedPricingId, selectedDateIds );
		await apiFetch({
			url: `${_wp_travel.ajax_url}?action=wp_travel_get_inventory&pricing_id=${selectedPricingId}&trip_id=${tripData.id}&selected_date=${moment(selectedDate).format('YYYY-MM-DD')}&times=${times.join()}&_nonce=${_wp_travel._nonce}`
		}).then(res => {
			if (res.success && res.data.code === 'WP_TRAVEL_INVENTORY_INFO') {
				if (res.data.inventory.length <= 0) {
					return
				}
				_setInventoryData( res.data.inventory );
			}
		});
		// return true;
	}
	const setTripExtrasData = async ( effectType ) => {
		if ( ( ! selectedPricingId || 'timeChange' === effectType ) ) {
			return;
		}
		let extras = allPricings[ selectedPricingId ].trip_extras;
		if ( extras.length > 0 ) {
			const url = `${wp_travel.ajaxUrl}?action=wp_travel_get_trip_extras&_nonce=${_wp_travel._nonce}`;
			await apiFetch( { url: url, data: {trip_ids:extras}, method:'post' } ).then( ( result ) => {
				if ( result.success ) {
					if (result.data.trip_extras.length <= 0) {
						return
					}
					_setNomineeTripExtras( result.data.trip_extras );
				}
				if ( isInventoryEnabled ) {
					setInventoryData();
				} else {
					updateBookingData( {isLoading:false} );
				}
			} )
		} else {
			_setNomineeTripExtras( [] );
			if ( isInventoryEnabled ) {
				await setInventoryData();
			} else {
				await updateBookingData( {isLoading:false} );
			}
		}
	}

    // Just custom botton. There is no custom onclick event here.
    const DatePickerBtn = forwardRef( ( { value, onClick }, ref ) => (
		<button className="wp-travel-date-picker-btn" onClick={ onClick } >
			{ selectedDate ? ! isFixedDeparture && `${moment(selectedDate).format('MMM D, YYYY')} - ${moment(selectedDate).add(duration - 1, 'days').format('MMM D, YYYY')}` || moment(selectedDate).format('MMM D, YYYY') : __i18n.bookings.date_select}
			<span><i className="far fa-calendar-alt"></i></span>
		</button>
	));

    // Update Selected Trip date in store.
    const selectTripDate = async ( date ) => {
		await updateBookingData( initialState ); // Quick hack to fix loader issue and store update.
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

		// Not required because it is false from extras ajas request.
		// if ( ! isFixedDeparture ) {
		// 	_bookingData.isLoading = false;  // Default false for trip duration only because date changes effect is not triggered in trip ducation.
		// }

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
				_bookingData = { ..._bookingData, selectedPricingId: tempSelectedPricingId, selectedPricing:selectedPricing, isLoading:true }
			}
			_bookingData = { ..._bookingData, selectedDateIds: _dateIds, nomineePricingIds: _nomineePricingIds }
		} else {
			_nomineePricingIds = pricings && pricings.map( pricing => pricing.id );

			if ( _nomineePricingIds.length <= 0 ) {
				_bookingData = { ..._bookingData, pricingUnavailable: true }
			} else if ( _nomineePricingIds.length === 1 ) {
				let tempSelectedPricingId = _nomineePricingIds[0];
				let selectedPricing   = allPricings[ tempSelectedPricingId ].title;
				_bookingData = { ..._bookingData, selectedPricingId: tempSelectedPricingId, selectedPricing:selectedPricing, isLoading:true }
			} else {
				_bookingData = { ..._bookingData, isLoading:false } // For multiple pricing.
			}
			_bookingData = { ..._bookingData, nomineePricingIds: _nomineePricingIds } // nomineePricingIds
		}

		updateBookingData( _bookingData  ); // isLoadting true + quick hack for issue creating from initialState update at the start.
		// bookingWidgetUseEffects( _bookingData, 'dateChange' ); // isloading false [quick fixes for loader displaying issue on date change]
		jQuery( document.body ).trigger( 'selectedTripDate', [ _bookingData ] );
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
		filterDate: IsTourDate( props ),
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
	let enable_time = '';
	_dates.map( ( dateData ) => {
		if( selectedDateIds[0] == dateData.id ) {
			enable_time = dateData.enable_time;
		}
	})
    return <ErrorBoundary>
		<div className="wp-travel-booking__datepicker-wrapper">
			{ calendarInline ? <DatePicker inline { ...params }  /> : <DatePicker { ...params }  /> }
			{ ! selectedDate && showTooltip && <p>{ tooltipText } </p> || null }
		</div>
		{/* Pricing and Times are in pricing wrapper */}
		{ selectedDate && <>
			<div className={isLoading ? 'wp-travel-booking__pricing-wrapper wptravel-loading' : 'wp-travel-booking__pricing-wrapper'}>
				<div className="wp-travel-booking__pricing-name"> 
					<Pricings { ...props } />
				</div>
				{ selectedPricingId && nomineeTimes.length > 0 && <TripTimes { ...props } /> }
			</div>
			{ ! isLoading &&
				<div className="wp-travel-booking__pricing-wrapper wptravel-pax-selector">
					{ ! pricingUnavailable && selectedPricingId && <ErrorBoundary>
						{ enable_time && nomineeTimes.length > 0 && ! selectedTime && <Disabled><PaxSelector { ...props } /></Disabled> || <PaxSelector { ...props } /> }
						{ _.size(allPricings[ selectedPricingId ].trip_extras) > 0 && objectSum( paxCounts ) > 0 && <ErrorBoundary> <TripExtras { ...props } /> </ErrorBoundary> }
					</ErrorBoundary> }
					
					{  pricingUnavailable && tripData.inventory && 'yes' === tripData.inventory.enable_trip_inventory && 
						<Notice><InventoryNotice inventory={tripData.inventory} /></Notice>
					}
				</div>
			}
			</>
		}
    </ErrorBoundary>
}
export default CalendarView;