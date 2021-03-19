import apiFetch from '@wordpress/api-fetch';
import { useSelect } from '@wordpress/data';
import { applyFilters } from '@wordpress/hooks';
import { forwardRef, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import moment from 'moment';
import DatePicker from "react-datepicker";
import RRule from "rrule";
import ErrorBoundry from './ErrorBoundry';
import { wpTravelFormat, wpTravelTimeout } from "./functions";
// sub-components
import PaxSelector from './sub-components/PaxSelector';
import PricingListing from './sub-components/PricingListing';
import TripExtrasListing from './sub-components/TripExtrasListing';
import TripTimesListing from './sub-components/TripTimesListing';
import DatesListing from './sub-components/DatesListing'
// layout-v2
import PaxSelectorV2 from './sub-components/layout-v2/PaxSelectorV2';
import PricingListingV2 from './sub-components/layout-v2/PricingListingV2';
import TripExtrasListingV2 from './sub-components/layout-v2/TripExtrasListingV2';
import TripTimesListingV2 from './sub-components/layout-v2/TripTimesListingV2';

const _ = lodash

const storeName = 'WPTravelFrontend/BookingWidget';

const { currency_symbol: currencySymbol } = _wp_travel

const Notice = ({ children, className }) => {
	return <div className="wp-travel-booking__notice is-info">
		<svg id="Capa_1" enableBackground="new 0 0 524.235 524.235" height="512" viewBox="0 0 524.235 524.235" width="512"><path d="m262.118 0c-144.53 0-262.118 117.588-262.118 262.118s117.588 262.118 262.118 262.118 262.118-117.588 262.118-262.118-117.589-262.118-262.118-262.118zm17.05 417.639c-12.453 2.076-37.232 7.261-49.815 8.303-10.651.882-20.702-5.215-26.829-13.967-6.143-8.751-7.615-19.95-3.968-29.997l49.547-136.242h-51.515c-.044-28.389 21.25-49.263 48.485-57.274 12.997-3.824 37.212-9.057 49.809-8.255 7.547.48 20.702 5.215 26.829 13.967 6.143 8.751 7.615 19.95 3.968 29.997l-49.547 136.242h51.499c.01 28.356-20.49 52.564-48.463 57.226zm15.714-253.815c-18.096 0-32.765-14.671-32.765-32.765 0-18.096 14.669-32.765 32.765-32.765s32.765 14.669 32.765 32.765c0 18.095-14.668 32.765-32.765 32.765z"></path></svg>
		{children}
	</div>
}

const initialState = {
	selectedDate: null,
	selectedTripDate: [],
	nomineePricings: [],
	nomineeTimes: [],
	selectedPricing: null,
	selectedDateTime: null,
	selectedTime: null,
	rruleAll: {},
	paxCounts: {},
	tripExtras: {},
	inventory: [],
	isLoading: false,
	excludedDateTimes: [],
	pricingUnavailable: false
}
const __i18n = {
	..._wp_travel.strings
}

const InventoryNotice = ({ inventory }) => {
	const {
		wp_travel_inventory_pax_limit_type,
		wp_travel_inventory_sold_out_action,
		wp_travel_inventory_custom_max_pax,
		wp_travel_inventory_sold_out_message,
		wp_travel_inventory_status_message_format
	} = inventory

	if (wp_travel_inventory_sold_out_action == 'show_sold_out_msg_only') {
		return <p className="wp-travel-sold-out">s{wp_travel_inventory_sold_out_message}</p>
	}
	if (wp_travel_inventory_sold_out_action == 'allow_trip_enquiry') {
		return <p className="wp-travel-sold-out">
			<a
				className="wp-travel-send-enquiries"
				data-effect="mfp-move-from-top"
				href="#wp-travel-enquiries"
				onClick={() => {
					let enquiryBtn = document.getElementById('wp-travel-send-enquiries')
					enquiryBtn && enquiryBtn.click()
				}}>
				{__i18n.trip_enquiry}
			</a>
		</p>
	}
	return <p>{__i18n.bookings.pricing_not_available}</p>
}

const BookingCalender = () => {

	const [{ selectedDate,
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
		pricingUnavailable }
		, setState] = useState(initialState)

	const updateState = data => {
		setState(state => ({ ...state, ...data }))
	}

	const allData = useSelect((select) => {
		return select(storeName).getAllStore()
	}, []);

	const pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id)

	const _dates = 'undefined' !== typeof allData.tripData.dates && allData.tripData.dates.length > 0 ? allData.tripData.dates : [];
	const datesById = _.keyBy(_dates, d => d.id)
	const isFixedDeparture = allData.tripData.is_fixed_departure || false
	const duration = allData.tripData.trip_duration.days && parseInt(allData.tripData.trip_duration.days) || 1
	const isTripInventoryEnabled = allData.tripData.inventory && allData.tripData.inventory.enable_trip_inventory === 'yes'
	const _excludedDatesTimes = allData.tripData.excluded_dates_times && allData.tripData.excluded_dates_times.length > 0 && allData.tripData.excluded_dates_times || []
	let excludedDates = []
	useEffect(() => {
		if (!selectedDateTime) {
			excludedDates = _excludedDatesTimes
				.filter(ed => {
					if (ed.trip_time.length > 0) {
						let _times = ed.trip_time.split(',')
						let _datetimes = _times.map(t => moment(`${ed.start_date} ${t}`).toDate())
						updateState({
							excludedDateTimes: _datetimes
						})
						return false
					}
					return true
				})
				.map(ed => ed.start_date)
		}
	}, [_excludedDatesTimes])

	useEffect(() => { // If No Fixed departure set all pricings.
		if (!isFixedDeparture) {
			updateState({ nomineePricings: Object.keys(pricings) })
			isLoading && updateState({ isLoading: false })
		}
	}, [selectedDate])


	useEffect(() => {
		if (nomineePricings.length === 1) {
			handlePricingSelect(nomineePricings[0])()
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
		if (isTripInventoryEnabled && isFixedDeparture) {
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

		updateState(_state)
	}, [selectedPricing])

	useEffect(() => {
		if (!isTripInventoryEnabled && selectedPricing) {
			let pricing = pricings[selectedPricing]
			let maxPax = pricing.max_pax || 999
			updateState({
				inventory: [{
					'date': moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'),
					'pax_available': maxPax,
					'booked_pax': 0,
					'pax_limit': maxPax,
				}]
			})
		}
	}, [selectedDateTime])


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

	const isTourDate = date => {
		if (moment(date).isBefore(moment(new Date())))
			return false
		if (!isFixedDeparture)
			return true
		let curretYear = date.getFullYear();
		let currentDate = date.getDate();
		let currentMonth = date.getMonth();

		let startDate = moment(new Date(Date.UTC(curretYear, currentMonth, currentDate, 0, 0, 0))).utc();

		if (excludedDates.includes(startDate.format('YYYY-MM-DD'))) {
			return false
		}

		// if (rruleAll && rruleAll[idx]) {
		// 	_ruleall = rruleAll[idx]
		// } else {
		const _date = _dates.find(data => {
			if (data.is_recurring) {
				let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];

				if (data.end_date) {
					if (moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isAfter(moment(data.end_date))) {
						return false
					}
				}
				if (selectedYears.length > 0 && !selectedYears.includes(startDate.year()))
					return false

				let dateRules = generateRRule(data, startDate)
				if ( ! applyFilters( 'wpTravelRecurringCutofDateFilter', true, dateRules, allData.tripData, date, data )  ) { // @since WP Travel 4.3.1
					return
				}
				return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
			}
			if (data.start_date) {
				if ( ! applyFilters( 'wpTravelCutofDateFilter', true, allData.tripData, date, data )  ) { // @since WP Travel 4.3.1
					return
				}
				return moment(date).isSame(moment(data.start_date))
				// if (moment(date).isSameOrAfter(moment(data.start_date))) {
				// 	if (data.end_date) {
				// 		if (String(new Date(data.end_date)).toLowerCase() == 'invalid date') {
				// 			return true
				// 		}
				// 		return moment(date).isSameOrBefore(moment(data.end_date))
				// 	}
				// }
				// return moment(date).isSameOrAfter(moment(data.start_date))
			}
			return false
		})
		return _date && 'undefined' !== typeof _date.id
		// }
	}

	const dayClicked = date => {

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

		let startDate = moment(new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0))).utc();

		const _dateIds = _dates // Trip Date IDs matches to selected date.
			.filter(_date => {
				if (_date.is_recurring) {
					if (_date.end_date) {
						if (moment(date).toDate().toString().toLowerCase() != 'invalid date' && moment(date).isAfter(moment(_date.end_date))) {
							return false
						}
					}
					let dateRules = generateRRule(_date, startDate)
					return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
				}
				if (_date.start_date) {
					return moment(date).isSame(moment(_date.start_date))
					// if (moment(date).isSameOrAfter(moment(_date.start_date))) {
					// 	if (_date.end_date && moment(date).isSameOrBefore(moment(_date.end_date))) {
					// 		if (String(new Date(_date.end_date)).toLowerCase() == 'invalid date') {
					// 			return true
					// 		}
					// 		return moment(date).isSameOrBefore(moment(_date.end_date))
					// 	}
					// }
					// return moment(date).isSameOrAfter(moment(_date.start_date))
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

	const handleTimeClick = time => () => {
		updateState({ selectedDateTime: time.toDate(), selectedTime: time.format('HH:mm') })
	}

	const handlePaxChange = (id, value) => e => {
		let pricing = pricings[selectedPricing]
		let category = pricing.categories.find(c => c.id === id)
		let count = paxCounts[id] + value < 0 ? 0 : paxCounts[id] + value
		if (parseInt(category.default_pax) > count)
			count = parseInt(category.default_pax)

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

	const getCartTotal = (withExtras) => {
		let total = 0;
		let txTotal = 0;

		let tpax = 0
		total = _.size(paxCounts) > 0 && Object.entries(paxCounts).map(([i, count]) => {
			tpax += parseInt(count)
			return count > 0 && getCategoryPrice(i, count) || 0 // i is category id here.
			// return parseFloat(price) * count
		}).reduce((acc, curr) => acc + curr) || 0
		if (!withExtras || tpax <= 0) {
			return total
		}
		let _tripExtras = selectedPricing && _.keyBy(pricings[selectedPricing].trip_extras, tx => tx.id)
		// console.debug(_tripExtras)
		txTotal = _.size(tripExtras) > 0 && Object.entries(tripExtras).map(([i, count]) => {
			let tx = _tripExtras[i]
			if (!tx || typeof tx.tour_extras_metas == 'undefined') {
				return 0
			}
			let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
			return parseFloat(price) * count
		}).reduce((acc, curr) => acc + curr) || 0
		return total + txTotal
	}

	const addToCart = () => {
		let data = {
			trip_id: allData.tripData.id,
			arrival_date: moment(selectedDateTime).format('YYYY-MM-DD'),
			pricing_id: selectedPricing,
			pax: paxCounts,
			category_pax: paxCounts,
			trip_price: getCartTotal(),
		}

		if (selectedTime)
			data.trip_time = moment(selectedDateTime).format('HH:mm')

		let _txs = {}
		Object.entries(tripExtras).forEach(([key, value]) => {
			if (value > 0) {
				_txs = { ..._txs, [key]: value }
			}
		})

		if (_.size(_txs) > 0) {
			data.wp_travel_trip_extras = {
				id: Object.keys(_txs),
				qty: Object.values(_txs)
			}
		}
		wpTravelTimeout(
			apiFetch({
				url: `${wp_travel.ajaxUrl}?action=wp_travel_add_to_cart&_nonce=${_wp_travel._nonce}`,
				method: 'POST',
				data
			})
				.then(res => {
					if (true === res.success && 'WP_TRAVEL_ADDED_TO_CART' === res.data.code) {
						if ( wp_travel.isEnabledCartPage ) {
							location.href = wp_travel.cartUrl; // This may include cart or checkout page url.
						} else {
						location.href = wp_travel.checkoutUrl; // [only checkout page url]
						}
					}
				}), 1000)
			.catch(error => {
				alert('[X] Request Timeout!')
			})
	}

	const getCategoryPrice = (categoryId, count) => {
		let pricing = pricings[selectedPricing]
		let category = pricing.categories.find(c => c.id == categoryId)
		if (!category) return
		let price = category && category.is_sale ? category.sale_price : category.regular_price

		if (category.has_group_price && category.group_prices.length > 0) { // If has group price/discount.
			// hasGroupPrice = true
			let groupPrices = _.orderBy(category.group_prices, gp => parseInt(gp.max_pax))
			let group_price = groupPrices.find(gp => parseInt(gp.min_pax) <= count && parseInt(gp.max_pax) >= count)
			if (group_price && group_price.price) {
				price = 'group' === category.price_per ? count > 0 && parseFloat(group_price.price) || 0 : parseFloat(group_price.price) * count
			} else {
				price = 'group' === category.price_per ? count > 0 && parseFloat(price) || 0 : parseFloat(price) * count
			}
		} else {
			price = 'group' === category.price_per ? count > 0 && parseFloat(price) || 0 : parseFloat(price) * count
		}
		return price || 0
	}

	const getPricingTripTimes = (pricingId, selectedTripdates) => {
		let trip_time = selectedTripdates.map(td => {
			let date = datesById[td]
			if (date.pricing_ids && date.pricing_ids.split(',').includes(pricingId)) {
				let times = date.trip_time && date.trip_time.split(',') || []
				times = applyFilters( 'wpTravelCutofTimeFilter', times, allData.tripData, selectedDateTime )  // @since WP Travel 4.3.1
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

	const handlePricingSelect = id => () => {
		// 		updateState({
		// 			paxCounts: initialState.paxCounts,
		// 			tripExtras: initialState.tripExtras,
		// 			nomineeTimes: initialState.nomineeTimes,
		// 			isLoading: true
		// 		})

		let _state = {}
		_state.isLoading = true
		_state.selectedPricing = id

		updateState(_state)
	}

	useEffect(() => {
		jQuery('.wti__selector-item.active').find('.wti__selector-content-wrapper').slideDown();
	}),[];

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
		filterDate: isTourDate
	}
	if (!isFixedDeparture) {
		delete params.filterDate
		params.minDate = new Date()
		params.startDate = selectedDate
		params.endDate = moment(selectedDate).add(duration - 1, 'days').toDate()
	}

	let totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
	let minPaxToBook = selectedPricing && pricings[selectedPricing].min_pax && parseInt(pricings[selectedPricing].min_pax) || 1
	let activeInventory = inventory.find(i => i.date === moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'))
	let maxPaxToBook = activeInventory && parseInt(activeInventory.pax_available)
	const tripDateListing = _wp_travel.trip_date_listing
	return <>
		{
			!_wp_travel.itinerary_v2 ?
			<>
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
			<div className="wp-travel-booking__datepicker-wrapper">
				{<>
					{
						isFixedDeparture && 'dates' === tripDateListing && 
							<DatesListing {...{ dates: datesById, onDateClick: dayClicked }} />
						||
							<>
								<DatePicker {...params} />
								{!selectedDateTime && <p>{__i18n.bookings.date_select_to_view_options}</p> || null}
							</>
					}
				</>}
			</div>
			{
				selectedDateTime && <div className="wp-travel-booking__pricing-wrapper">
					{
						nomineePricings.length > 1 && <ErrorBoundry>
							<PricingListing
								selected={selectedPricing}
								options={nomineePricings}
								onPricingSelect={handlePricingSelect}
							/>
						</ErrorBoundry>
					}
					{
						!pricingUnavailable && nomineeTimes.length > 0 && <ErrorBoundry>
							<TripTimesListing
								selected={selectedDateTime}
								onTimeSelect={handleTimeClick}
								options={nomineeTimes}
							/>
						</ErrorBoundry>
					}
					{
						!pricingUnavailable && selectedPricing && inventory.find(i => i.pax_available > 0) && <ErrorBoundry>
							<PaxSelector
								pricing={pricings[selectedPricing] || null}
								onPaxChange={handlePaxChange}
								counts={paxCounts}
								inventory={inventory}
							/>
						</ErrorBoundry>

					}
					{
						!pricingUnavailable && totalPax > 0 && _.size(pricings[selectedPricing].trip_extras) > 0 && <ErrorBoundry>
							<TripExtrasListing
								options={pricings[selectedPricing].trip_extras}
								onChange={(id, value) => () => updateState({ tripExtras: { ...tripExtras, [id]: parseInt(value) } })}
								counts={tripExtras}
							/>
						</ErrorBoundry>
					}
					{pricingUnavailable && <Notice>
						{
							allData
							&& allData.tripData.inventory
							&& allData.tripData.inventory.enable_trip_inventory == 'yes'
							&& <InventoryNotice inventory={allData.tripData.inventory} />
						}
					</Notice>
					}
					{isLoading && <div id="loader">
						<span></span>
					</div>}
				</div>
			}
			{selectedDate && selectedPricing && <div className="wp-travel-booking__panel-bottom">
				<p>{__i18n.bookings.booking_tab_cart_total}<strong dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCartTotal(true)) }}></strong></p>
				<button disabled={totalPax < minPaxToBook || totalPax > maxPaxToBook} onClick={addToCart} className="wp-travel-book">{__i18n.bookings.booking_tab_booking_btn_label}</button>
			</div>}
			</>
			: // For new layout. @since v4.4.6
			<>
				{/* <h3>{__i18n.booking_tab_content_label}</h3> */}
				{selectedDate && <div className="wti_booking_clear_btn"><button className="wti_clear_all" onClick={() => {
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
				{__i18n.bookings.booking_tab_clear_all}</button></div>}
				<div className="wti__booking-date-picker">
					<DatePicker {...params} />
				</div>
				{
					selectedDateTime && <div className="wti__selectors">
						{
							nomineePricings.length > 1 && <ErrorBoundry>
								<PricingListingV2
									selected={selectedPricing}
									options={nomineePricings}
									onPricingSelect={handlePricingSelect}
								/>
							</ErrorBoundry>
						}
						{
							!pricingUnavailable && nomineeTimes.length > 0 && <ErrorBoundry>
								<TripTimesListingV2
									selected={selectedDateTime}
									onTimeSelect={handleTimeClick}
									options={nomineeTimes}
								/>
							</ErrorBoundry>
						}
						{
							!pricingUnavailable && selectedPricing && inventory.find(i => i.pax_available > 0) && <ErrorBoundry>
								<PaxSelectorV2
									pricing={pricings[selectedPricing] || null}
									onPaxChange={handlePaxChange}
									counts={paxCounts}
								/>
							</ErrorBoundry>

						}
						{
							!pricingUnavailable && totalPax > 0 && _.size(pricings[selectedPricing].trip_extras) > 0 && <ErrorBoundry>
								<TripExtrasListingV2
									options={pricings[selectedPricing].trip_extras}
									onChange={(id, value) => () => updateState({ tripExtras: { ...tripExtras, [id]: parseInt(value) } })}
									counts={tripExtras}
								/>
							</ErrorBoundry>
						}
						{pricingUnavailable && <Notice>
							{
								allData
								&& allData.tripData.inventory
								&& allData.tripData.inventory.enable_trip_inventory == 'yes'
								&& <InventoryNotice inventory={allData.tripData.inventory} />
							}
						</Notice>
						}
						{isLoading && <div id="loader">
							<span></span>
						</div>}
					</div>
			}
			{selectedDate && selectedPricing && <div className="wti__booking-total-amount">
				<h3 className="amount-figure"><span>{__i18n.bookings.booking_tab_cart_total}</span><strong className="total-amount" dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCartTotal(true)) }}></strong></h3>
			</div>
			}
			{selectedDate && selectedPricing &&
				<button disabled={totalPax < minPaxToBook || totalPax > maxPaxToBook} onClick={addToCart} className="wti__book-now-button">{__i18n.bookings.booking_tab_booking_btn_label}</button>
			}
			</>
		}
	</>;
}

export default BookingCalender
