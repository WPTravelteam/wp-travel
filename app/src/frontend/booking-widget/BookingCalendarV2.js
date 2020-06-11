import DatePicker from "react-datepicker";
import RRule, { RRuleSet } from "rrule";
import { useSelect, dispatch } from '@wordpress/data';
import moment from 'moment';
import { useState, useEffect } from '@wordpress/element'
import { __ } from '@wordpress/i18n'
import apiFetch from '@wordpress/api-fetch'

const _ = lodash

const storeName = 'WPTravelFrontend/BookingWidget';

const BookingCalender = () => {
	const [selectedDate, setSelectedDate] = useState(null)
	const [selectedTripDate, setSelectedTripDate] = useState([])
	const [nomineePricings, setNomineePricings] = useState([])
	const [selectedPricing, setSelectedPricing] = useState(null)
	const [selectedDateTime, setSelectedDateTime] = useState(null)
	const [rruleAll, setrruleAll] = useState({})
	const [paxCounts, setPaxCounts] = useState({})

	const allData = useSelect((select) => {
		return select(storeName).getAllStore()
	}, []);

	let pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id)

	let _dates = 'undefined' !== typeof allData.tripData.dates && allData.tripData.dates.length > 0 ? allData.tripData.dates : [];
	let datesById = _.keyBy(_dates, d => d.id)
	let isFixedDeparture = allData.tripData.is_fixed_departure || false
	let duration = allData.tripData.trip_duration.days && parseInt(allData.tripData.trip_duration.days) || 1

	useEffect(() => { // If No Fixed departure set all pricings.
		if (!isFixedDeparture) {
			if (Object.keys(pricings).length == 1) {
				setSelectedPricing(Object.keys(pricings)[0])
				return
			}
			setNomineePricings(Object.keys(pricings))
		}
	}, [])

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
		if (!isFixedDeparture)
			return true
		let curretYear = date.getFullYear();
		let currentDate = date.getDate();
		let currentMonth = date.getMonth();

		let startDate = moment(new Date(Date.UTC(curretYear, currentMonth, currentDate, 12, 0, 0))).utc();
		let idx = currentMonth.toString() + curretYear.toString() // index to save dates [rruleAll] in the state.

		let excludedDatesTimes = allData.tripData.excluded_dates_times && allData.tripData.excluded_dates_times.length > 0 && allData.tripData.excluded_dates_times || []
		let excludedDates = excludedDatesTimes.map(ed => ed.start_date)
		if (excludedDates.includes(startDate.format('YYYY-MM-DD')))
			return false

		if (rruleAll && rruleAll[idx]) {
			_ruleall = rruleAll[idx]
		} else {
			// let rruleSet = new RRuleSet()
			const _date = _dates.find(data => {
				if (data.is_recurring) {
					let selectedYears = data.years ? data.years.split(",").filter(year => year != 'every_year').map(year => parseInt(year)) : [];

					if (selectedYears.length > 0 && !selectedYears.includes(startDate.year()))
						return false


					let dateRules = generateRRule(data, startDate)
					return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
				}
				return moment(data.start_date).isSame(moment(date))
			})
			return _date && 'undefined' !== typeof _date.id
		}
	}

	const getTime = (date) => {
		return date.trip_time && date.trip_time.split(',') || []
	}

	const dayClicked = date => {
		setSelectedPricing(null)
		// let selectedDate = _dates.find(d => moment(moment(d).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix())
		setSelectedDate(date)
		setSelectedDateTime(date)
		if (!isFixedDeparture) {
			return
		}
		setNomineePricings([])

		let startDate = moment(new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0))).utc();

		const _dateIds = _dates // Trip Date IDs matches to selected date.
			.filter(_date => {
				if (_date.is_recurring) {
					let dateRules = generateRRule(_date, startDate)
					return dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date
				}
				return moment(_date.start_date).isSame(moment(date))
			}).map(d => d.id)
		// setSelectedDateTime(date)

		_dateIds.forEach(id => {
			if (getTime(datesById[id]).length <= 0) {
				setNomineePricings(_.uniq([...nomineePricings, ...datesById[id].pricing_ids.split(',')]))
			}
		})

		setSelectedTripDate(_dateIds)
	}

	const tripTimes = selectedTripDate && selectedTripDate.map(dateId => {
		return getTime(datesById[dateId])
	})


	const handleTimeClick = time => () => {
		setSelectedPricing(null)
		let nomineePricings = _dates.map(_date => {
			let times = _date.trip_time && _date.trip_time.split(',') || []

			let datetimes = times.map(time => {
				return moment(`${selectedDate.toDateString()} ${time}`)
			})

			if (datetimes.find(dt => dt.isSame(time)) instanceof moment) {
				return _date.pricing_ids && _date.pricing_ids.split(',')
			}
			return []

		})

		let _nomineePricings = _.chain(nomineePricings).flatten().uniq().value()
		setSelectedDateTime(time.toDate())
		setNomineePricings(_nomineePricings)
		_nomineePricings.length == 1 && setSelectedPricing(_nomineePricings[0])

		// selectedDateTime(moment(selectedDate))
	}

	const handlePaxChange = (id, value) => () => {
		let count = 'undefined' === typeof paxCounts[id] ? 1 : paxCounts[id] + value
		count = count < 0 ? 0 : count

		setPaxCounts({ ...paxCounts, [id]: count })
	}

	const getCartTotal = () => {
		let total = Object.entries(paxCounts).map(([i, count]) => {
			let categories = _.keyBy(pricings[selectedPricing].categories, c => c.id)
			let category = categories[i]
			let price = category.is_sale ? category.sale_price : category.regular_price
			return parseFloat(price) * count
		}).reduce((acc, curr) => acc + curr)
		return total || 0
	}

	const addToCart = () => {
		let data = {
			trip_id: allData.tripData.id,
			arrival_date: moment(selectedDateTime).format('YYYY-MM-DD'),
			trip_time: moment(selectedDateTime).format('HH:mm'),
			pricing_id: selectedPricing,
			pax: paxCounts,
			category_pax: paxCounts,
			trip_price: getCartTotal()
		}
		apiFetch({
			url: `${wp_travel.ajaxUrl}?action=wp_travel_add_to_cart&_nonce=${_wp_travel._nonce}`,
			method: 'POST',
			data
		})
			.then(res => {
				if (true === res.success && 'WP_TRAVEL_ADDED_TO_CART' === res.data.code) {
					console.debug(res)
				}
			})
	}

	let params = {
		showMonthDropdown: true,
		inline: true,
		showYearDropdown: true,
		dropdownMode: "select",
		minDate: new Date(),
		onChange: dayClicked,
		filterDate: isTourDate
	}
	if (!isFixedDeparture) {
		delete params.filterDate
		// minDate = selectedDate
		// maxDate
		params.startDate = selectedDate
		params.endDate = moment(selectedDate).add(duration - 1, 'days').toDate()
	}

	return <>
		<DatePicker {...params} />
		<p>Selected Date and Time: {selectedDateTime && moment(selectedDateTime).format()}</p>
		{
			_.chain(tripTimes).flatten().uniq().value().map((time, i) => {
				let timeObject = moment(`${selectedDate.toDateString()} ${time}`)
				return <button key={i} onClick={handleTimeClick(timeObject)}>{timeObject.format('h:mm A')}</button>
			})
		}
		{
			nomineePricings.map(id => pricings[id] && <span>{pricings[id].id}</span>)
		}
		{(selectedDateTime && nomineePricings.length > 0) && <>
			<p>Pricings Nominee:</p>
			{
				nomineePricings.map((id, i) => <button key={i} onClick={() => {
					setSelectedPricing(id)
				}}>{pricings[id].title}</button>)
			}
		</>}
		{
			selectedDateTime && selectedPricing && <>
				<p>Selected Pricing: <strong>{selectedPricing}</strong></p>
				<ul>
					{
						pricings[selectedPricing] && pricings[selectedPricing].categories.map((c, i) => {
							let price = c.is_sale ? c.sale_price : c.regular_price
							return <li><strong>{`${c.term_info.title} (${price})/${c.price_per}`}</strong><button onClick={handlePaxChange(c.id, 1)}>+</button>{paxCounts[c.id]}<button onClick={handlePaxChange(c.id, -1)}>-</button> = {parseFloat(price) * paxCounts[c.id]}</li>
						})
					}
				</ul>
				{Object.values(paxCounts).length > 0 && <p><strong>Total:</strong>{getCartTotal()}</p>}
			</>
		}
		{
			selectedPricing && <button className="wp-travel-book" onClick={addToCart}>{__('Book Now')}</button>
		}
	</>;
}

export default BookingCalender;
