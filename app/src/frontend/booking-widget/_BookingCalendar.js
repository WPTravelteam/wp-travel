import DatePicker from "react-datepicker";
import RRule, { RRuleSet } from "rrule";
import { useSelect, dispatch } from '@wordpress/data';
import moment from 'moment';
import { useState, useEffect } from '@wordpress/element'

const storeName = 'WpTravelFrontend/BooingWidget';
const BookingCalender = () => {
	const [selectedDate, setSelectedDate] = useState(null)
	const [rruleAll, setrruleAll] = useState({})


	const allData = useSelect((select) => {
		return select(storeName).getAllStore()
	}, []);
	let _dates = 'undefined' !== typeof allData.tripData.dates && allData.tripData.dates.length > 0 ? allData.tripData.dates : {};

	console.debug(rruleAll)
	const isTourDate = date => {
		let curretYear = date.getFullYear();
		let currentDate = date.getDate();
		let currentMonth = date.getMonth();

		let startDate = moment(new Date(Date.UTC(curretYear, currentMonth, currentDate, 12, 0, 0))).utc();

		let dtstart = startDate;


		let idx = currentMonth.toString() + curretYear.toString() // index to save dates [rruleAll] in the state.

		let _ruleall = []

		if (rruleAll && rruleAll[idx]) {
			_ruleall = rruleAll[idx]
		} else {
			let rruleSet = new RRuleSet()
			_dates.map(data => {
				let ruleArgs = {
					freq: RRule.DAILY,
					dtstart: moment(new Date(Date.UTC(curretYear, currentMonth, 1, 12, 0, 0))).subtract(7, 'days').toDate(),
					until: moment(new Date(Date.UTC(curretYear, currentMonth, 1, 12, 0, 0))).add(35, 'days').utc().toDate(),
				};
				let until = moment(dtstart).add(1, 'months').toDate()
				let selectedMonths = data.months ? data.months.split(",").filter(month => month != 'every_month') : [];
				let selectedDates = data.date_days ? data.date_days.split(",").filter(date => date !== 'every_weekdays' && date !== '') : [];
				let selectedDays = data.days ? data.days.split(",").filter(day => day !== 'every_date_days' && date !== '') : [];

				if (selectedMonths.length > 0) {
					ruleArgs.bymonth = selectedMonths;
				}
				if (selectedDays.length > 0) {
					ruleArgs.byweekday = selectedDays.map(sd => RRule[sd]);
				} else if (selectedDates.length > 0) {
					ruleArgs.bymonthday = selectedDates.map(md => parseInt(md));
				}

				// console.debug(ruleArgs, selectedDates)

				let rule = new RRule(ruleArgs);
				rruleSet.rrule(rule)
				rruleSet.exdate(new Date(Date.UTC(2020, 8, 15, 10, 30)))
				// rruleSet.exdate(new Date(Date.UTC(2020, 9, 15, 10, 30)))
			})
			_ruleall = rruleSet.all()
			setrruleAll({ ...rruleAll, [idx]: _ruleall })
		}

		return _ruleall.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date;
	}

	const dayClicked = date => {
		let selectedDate = _dates.find(d => moment(moment(d).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix())
		setSelectedDate(selectedDate)
	}

	return <>
		<DatePicker
			showMonthDropdown
			showYearDropdown
			dropdownMode="select"
			inline
			minDate={new Date()}
			onChange={dayClicked}
			filterDate={isTourDate}
		/>
		<p>Selected Date: {moment(selectedDate).format()}</p>
		<button></button>
	</>;
}

export default BookingCalender;