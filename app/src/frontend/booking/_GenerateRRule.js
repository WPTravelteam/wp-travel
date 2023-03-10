// Additional lib
const _ = lodash;
import { RRule } from "rrule";

/**]
 * @param data array Date row form date table.
 * @param startDate object Moment start date 
 */
const generateRRule = ( data, startDate ) => {
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
export default generateRRule;