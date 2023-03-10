import { forwardRef, useEffect, useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';

// Additional lib
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const _ = lodash;
import moment from 'moment';

// WP Travel Components.
import DateListingTableHead from './DateListing/DateListingTableHead';
import NonRecurringDates from './DateListing/NonRecurringDates';
import RecurringDates from './DateListing/RecurringDates';

const DateListing = ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration:tripDuration
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById          = _.keyBy(_dates, d => d.id); // object structure along with date id.

    let nonRecurringDates = _dates.filter( d => { return !d.is_recurring && d.start_date && '0000-00-00' !== d.start_date && new Date( d.start_date )  > new Date() } )
    let recurringDates    = _dates.filter( d => { return d.is_recurring && ( '0000-00-00' === d.end_date || ( d.end_date && new Date( d.end_date )  > new Date() ) ) } )

	const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    // Booking Data.
    const { dateListingChangeType, selectedDate, selectedDateIds, nomineePricingIds, selectedPricingId, excludedDateTimes, selectedTime } = bookingData;
	
    // Temp/Local state to play with HTTP Request. [same as Calendar View] @todo need to optimize by using only one.
	const [_inventoryData, _setInventoryData] = useState([]);
	const [_nomineeTripExtras, _setNomineeTripExtras] = useState([]);
	
    // Lifecycles. [ This will only trigger if pricing is selected or changed ]
    useEffect( () => {  // removed async to compatible with legacy.
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			pricingUnavailable:false,
			nomineeTimes: [],
			selectedTime: null,
		};
		
		// after selecting pricing. need to check available time for selected pricing as well. Single pricing id case is already checked in date changes lifecycle below.
		bookingWidgetUseEffects( _bookingData, 'pricingChange' );
	}, [ selectedPricingId ]); 

	// Lifecycles. [ This will only trigger if time is selected or changed ]
    useEffect( () => {  // removed async to compatible with legacy.
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
	useEffect( () => {  // removed async to compatible with legacy.
		// If date changes has selectedPricingId, that mean selected date has only one pricing. So nomineeTimes for single date is calculated from here.
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = {
			// pricingUnavailable:false,
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
			isLoading:false
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
			// Why This time length check. this is creating issue as pricingUnavailable
			if (_times.length <= 0) {
				_inventory_state = { ..._inventory_state, pricingUnavailable: true }
			} else if( 1 === _times.length ) {
				_inventory_state = {
					..._inventory_state,
					selectedTime: _times[0].format('HH:mm'),
					pricingUnavailable: false
				}
			} else {
				_inventory_state = { ..._inventory_state, pricingUnavailable: false }
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
    useEffect(() => {
		if ( ! selectedPricingId ) {
			updateBookingData( { isLoading:false } )
			return
		}
		let _bookingData = { };
		if ( ! isInventoryEnabled ) {
			_bookingData.isLoading = false;
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
		setTripExtrasData(effectType);
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
			let _inventory_state = { }
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
				// Why This time length check. this is creating issue as pricingUnavailable
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
		}
		updateBookingData( _bookingData );
		// console.log(effectType, bookingData, _bookingData );
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
		})
	}
	const setTripExtrasData = async (effectType) => {

		// In date listing pricing and date are selected at once so need to prevent multiple ajax requst of extras and inventory.
		if ( ( ! selectedPricingId || 'timeChange' === effectType ) || (  'dateChange' === effectType && 'dateOnly' !== dateListingChangeType ) ) {
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
				setInventoryData();
			} else {
				await updateBookingData( {isLoading:false} );
			}
		}
	}

    return <ErrorBoundary>
            { _dates.length > 0 &&
                <div className="wptravel-recurring-dates">
                    { nonRecurringDates.length > 0 &&
                        <div className="wptravel-recurring-table-wrapper">
                            <table className="wptravel-recurring-table">
                                <DateListingTableHead />
                                <NonRecurringDates { ...props } />
                            </table>
                        </div>
                    }

                    { recurringDates.length > 0 && <>
						{ recurringDates.map((date, index) => {
							return <div className="wptravel-recurring-table-wrapper" key={index}>
								<table className="wptravel-recurring-table">
									<DateListingTableHead />
									<RecurringDates { ...{ ...props, date, index } } />
								</table>
							</div>
						} ) }
					</>
                    }

                </div>
            }
    </ErrorBoundary>
}
export default DateListing;